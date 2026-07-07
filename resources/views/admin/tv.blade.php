{{--
resources/views/admin/tv.blade.php
TV MODE - UI/UX identik persis dengan dashboard.blade.php
Header = app-header hijau dari admin.blade, semua komponen konten = copy 1:1 dashboard
--}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>📺 TV MODE - HENKATEN BOARD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Roboto+Condensed:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    {{-- CSS utama (berisi semua --green, --navy, --red, dan semua shared class dashboard) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/henkaten.css') }}">
    {{-- Floor plan CSS for TV mode --}}
    <link rel="stylesheet" href="{{ asset('css/floor-plan.css') }}">

    {{-- Dynamic Theme Styles --}}
    @php
        $siteConfigs = \App\Models\SiteConfig::all()->pluck('value', 'key');
        $navbarBg = $siteConfigs['navbar_color'] ?? '#2E7D32';
        $primaryColor = $siteConfigs['primary_color'] ?? '#2E7D32';
        $secondaryColor = $siteConfigs['secondary_color'] ?? '#729E3F';
        $themeEffect = $siteConfigs['theme_effect'] ?? 'normal';
    @endphp
    <style>
        :root {
            --navbar-bg:
                {{ $navbarBg }}
            ;
            --brand-primary:
                {{ $primaryColor }}
            ;
            --brand-secondary:
                {{ $secondaryColor }}
            ;
        }

        /* Navbar dynamic colors */
        .app-header {
            background: var(--navbar-bg) !important;
        }

        .tv-footer {
            background: var(--navbar-bg) !important;
        }

        /* Brand primary overrides */
        .section-title-box,
        .stat-card,
        .machine-group-title {
            border-color: var(--brand-primary) !important;
        }

        .machine-group-title {
            background: var(--brand-primary) !important;
        }

        /* 1. Dynamic Primary Theme Mapping (TV Mode) */
        .statusChip {
            background: var(--brand-primary) !important;
            color: #fff !important;
        }

        .leg-dot[style*="background: #729E3F"],
        .leg-dot[style*="background:#729E3F"] {
            background: var(--brand-primary) !important;
        }

        .tv-ticker {
            background: var(--brand-primary) !important;
            color: #fff !important;
        }

        .tv-fs {
            background: var(--brand-primary) !important;
            color: #fff !important;
        }

        .absen-bar,
        .absenFill {
            background: var(--brand-primary) !important;
        }

        /* Glossy Effect */
        @if($themeEffect === 'glossy')
            .app-header,
            .tv-footer,
            .machine-group-title {
                background-image: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 50%, rgba(0, 0, 0, 0.05) 100%) !important;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
            }

        @endif
    </style>

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            background: #f4f7f0;
        }

        /* ── Layout utama ── */
        .tv-body {
            position: fixed;
            top: 95px;
            left: 0;
            right: 0;
            bottom: 36px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 38% 62%;
        }

        .tv-col-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 6px 8px 6px 12px;
            box-sizing: border-box;
            overflow: hidden;
            height: 100%;
        }

        .tv-col-right {
            display: flex;
            flex-direction: column;
            padding: 6px 12px 6px 8px;
            box-sizing: border-box;
            overflow: hidden;
            border-left: 1px solid #dde8c8;
            height: 100%;
            gap: 8px;
        }


        .tv-problem-table {
            flex-shrink: 0;
            background: white;
            border: 1px solid #dde8c8;
            border-radius: 6px;
            overflow-x: auto;
        }

        .tv-problem-table table {
            font-size: 11px;
            width: 100%;
            border-collapse: collapse;
        }

        .tv-problem-table th {
            background: #f4f7f0;
            padding: 6px 8px;
            text-align: left;
            font-weight: 700;
            border-bottom: 1px solid #dde8c8;
        }

        .tv-problem-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .tv-problem-table tbody tr:hover {
            background: #f9faf8;
        }

        .tv-bottom-section {
            flex: 1 1 0;
            display: flex;
            gap: 8px;
            min-height: 0;
            overflow: hidden;
        }

        .tv-floor-plan-col {
            flex: 0 0 40%;
            display: flex;
            flex-direction: column;
        }

        .tv-machine-cards-col {
            flex: 1 1 0;
            overflow: auto;
        }

        /* ── Problems Section (normal TV mode) ── */
        .tv-problems-wrap {
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        .tv-problems-header {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 10px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s;
        }

        .tv-problems-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 900;
            color: #64748b;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.3s;
        }

        .tv-problems-badge {
            background: #94a3b8;
            color: #fff;
            border-radius: 10px;
            padding: 1px 8px;
            font-size: 10px;
            font-weight: 900;
            min-width: 18px;
            text-align: center;
            transition: background 0.3s;
        }

        .tv-problems-grid {
            flex: 1 1 0;
            min-height: 0;
            overflow: auto;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 6px;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 6px;
            transition: all 0.3s;
        }

        /* ── Active Problem Variants (Red Accent) ── */
        .tv-problems-wrap.has-prob .tv-problems-header {
            background: linear-gradient(135deg, #fff0f0 0%, #ffe8e8 100%);
            border-color: #f5c6c6;
        }

        .tv-problems-wrap.has-prob .tv-problems-title {
            color: #c0392b;
        }

        .tv-problems-wrap.has-prob .tv-problems-badge {
            background: #e74c3c;
            animation: probBlink 0.8s step-end infinite;
        }

        .tv-problems-wrap.has-prob .tv-problems-grid {
            background: #fff8f8;
            border-color: #f5c6c6;
        }

        /* Make cloned problem cards compact */
        .tv-problems-grid .mc-card {
            width: 136px;
            flex-shrink: 0;
        }

        /* ── Glowing Problem Cards Animations ── */
        @keyframes glowMan {

            0%,
            100% {
                box-shadow: 0 0 8px rgba(231, 76, 60, 0.4), inset 0 0 4px rgba(231, 76, 60, 0.1);
                border-color: #e74c3c;
            }

            50% {
                box-shadow: 0 0 20px rgba(231, 76, 60, 0.9), inset 0 0 10px rgba(231, 76, 60, 0.25);
                border-color: #e74c3c;
            }
        }

        @keyframes glowMachine {

            0%,
            100% {
                box-shadow: 0 0 8px rgba(31, 60, 136, 0.4), inset 0 0 4px rgba(31, 60, 136, 0.1);
                border-color: #1f3c88;
            }

            50% {
                box-shadow: 0 0 20px rgba(31, 60, 136, 0.9), inset 0 0 10px rgba(31, 60, 136, 0.25);
                border-color: #1f3c88;
            }
        }

        @keyframes glowMaterial {

            0%,
            100% {
                box-shadow: 0 0 8px rgba(243, 156, 18, 0.4), inset 0 0 4px rgba(243, 156, 18, 0.1);
                border-color: #f39c12;
            }

            50% {
                box-shadow: 0 0 20px rgba(243, 156, 18, 0.9), inset 0 0 10px rgba(243, 156, 18, 0.25);
                border-color: #f39c12;
            }
        }

        @keyframes glowMethod {

            0%,
            100% {
                box-shadow: 0 0 8px rgba(46, 125, 50, 0.4), inset 0 0 4px rgba(46, 125, 50, 0.1);
                border-color: #2e7d32;
            }

            50% {
                box-shadow: 0 0 20px rgba(46, 125, 50, 0.9), inset 0 0 10px rgba(46, 125, 50, 0.25);
                border-color: #2e7d32;
            }
        }

        .tv-problems-grid .mc-card-clone.mc-has-absen {
            animation: glowMan 1.5s infinite;
        }

        .tv-problems-grid .mc-card-clone.mc-status-machine {
            animation: glowMachine 1.5s infinite;
        }

        .tv-problems-grid .mc-card-clone.mc-status-material {
            animation: glowMaterial 1.5s infinite;
        }

        .tv-problems-grid .mc-card-clone.mc-status-method {
            animation: glowMethod 1.5s infinite;
        }

        .tv-prob-all-ok {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 6px;
            color: #2e7d32;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 700;
            width: 100%;
            height: 100%;
        }

        .tv-detail-btn {
            font-size: 10px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 5px;
            background: #1f3c88;
            color: #fff;
            border: none;
            cursor: pointer;
            font-family: 'Roboto Condensed', sans-serif;
            letter-spacing: 0.3px;
            transition: background 0.15s;
        }

        .tv-detail-btn:hover {
            background: #2e54b8;
        }

        /* Detail mode header bar */
        .tv-detail-mode-bar {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 12px;
            background: #e8f5e9;
            border: 1.5px solid #c8e6c9;
            border-radius: 8px 8px 0 0;
            font-size: 11px;
            font-weight: 700;
            color: #2e7d32;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .tv-detail-cards-col {
            flex: 1 1 0;
            min-height: 0;
            overflow: auto;
            border: 1.5px solid #c8e6c9;
            border-top: none;
            border-radius: 0 0 8px 8px;
            background: #f9fdf9;
        }

        .tv-col-left .date-bar {
            flex-shrink: 0;
        }

        .tv-col-left .shift-toggle-bar {
            flex-shrink: 0;
        }

        .tv-col-left .legend-4m {
            flex-shrink: 0;
        }

        .tv-col-left .status-panel {
            flex-shrink: 0;
        }

        .tv-col-left .tv-chart-section {
            flex: 1 1 0;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        /* ── Problem Panel Realtime ── */
        .tv-problem-detail {
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 0;
            overflow: hidden;
            border-radius: 8px;
        }

        .tv-prob-panel-header {
            background: #f4f7f0;
            padding: 8px 12px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #444;
            border-bottom: 2px solid #dde8c8;
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tv-prob-badge {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 900;
            min-width: 20px;
            text-align: center;
        }

        .tv-prob-badge.blink {
            animation: probBlink 0.8s step-end infinite;
        }

        @keyframes probBlink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .35
            }
        }

        .tv-prob-panel {
            background: white;
            border: 1.5px solid #dde8c8;
            border-radius: 6px;
            flex: 1 1 0;
            min-height: 0;
            overflow-y: auto;
            overflow-x: auto;
            display: flex;
            flex-direction: column;
        }

        .tv-prob-panel table {
            font-size: 13px;
            width: 100%;
            border-collapse: collapse;
            flex: 1;
        }

        .tv-prob-panel th {
            background: #f4f7f0;
            padding: 8px 10px;
            text-align: left;
            font-weight: 700;
            border: 1px solid #c8e6c9;
            font-family: 'Roboto Condensed', sans-serif;
            white-space: nowrap;
        }

        .tv-prob-panel td {
            padding: 8px 10px;
            border: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .tv-prob-panel tbody tr:hover {
            background: #fafafa;
        }

        .tv-prob-row-open {
            background: #fff5f5;
        }

        .tv-prob-row-open td {
            border-color: #fddede;
        }

        .tv-prob-row-closed {
            background: #f6fff6;
        }

        .tv-jenis-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            white-space: nowrap;
        }

        .tv-jenis-machine {
            background: #1f3c88;
        }

        .tv-jenis-material {
            background: #f39c12;
        }

        .tv-jenis-method {
            background: #2e7d32;
        }

        .tv-jenis-man {
            background: #e74c3c;
        }

        .tv-prob-status-open {
            color: #e74c3c;
            font-weight: 800;
            font-size: 11px;
        }

        .tv-prob-status-closed {
            color: #2e7d32;
            font-weight: 800;
            font-size: 11px;
        }

        .tv-prob-empty {
            text-align: center;
            color: #999;
            padding: 16px 8px;
            font-size: 13px;
        }

        /* ════════════════════════════════════════════
       Semua override di-scope .tv-body
       agar TIDAK bocor ke dashboard.blade.php
    ════════════════════════════════════════════ */

        /* ── date-bar ── */
        .tv-body .date-bar {
            padding: 8px 14px !important;
            border-radius: 10px !important;
            flex-shrink: 0 !important;
        }

        /* ── shift toggle ── */
        .tv-body .shift-toggle-bar {
            padding: 5px !important;
            border-radius: 10px !important;
            gap: 5px !important;
            flex-shrink: 0 !important;
        }

        .tv-body .shift-toggle-btn {
            padding: 8px 14px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
            letter-spacing: 0.5px !important;
            cursor: default !important;
        }

        /* ── legend 4M ── */
        .tv-body .legend-4m {
            padding: 7px 14px !important;
            border-radius: 10px !important;
            gap: 14px !important;
            flex-shrink: 0 !important;
            flex-wrap: wrap !important;
        }

        .tv-body .legend-4m-item {
            font-size: 12px !important;
            gap: 6px !important;
        }

        .tv-body .l4m-dot {
            width: 10px !important;
            height: 10px !important;
        }

        /* ── Status panel ── */
        .tv-body .status-panel {
            padding: 14px 12px !important;
            border-radius: 12px !important;
            overflow: visible !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 0 !important;
            margin-bottom: 0 !important;
        }

        .tv-body .status-panel-title {
            font-size: 15px !important;
            font-weight: 900 !important;
            padding-bottom: 10px !important;
            margin: 0 0 2px !important;
        }

        .tv-body .auto-refresh-bar {
            display: none !important;
        }

        .tv-body .ar-last-updated {
            font-size: 9px !important;
            text-align: right !important;
            margin: 2px 0 0 !important;
            opacity: .6 !important;
            flex-shrink: 0 !important;
        }

        .tv-body .status-emot-row {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 6px !important;
            margin: 8px 0 0 !important;
            flex-shrink: 0 !important;
        }

        .tv-body .emot-card {
            padding: 14px 4px !important;
            border-radius: 10px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            min-width: 0 !important;
        }

        .tv-body .emot-icon {
            font-size: 28px !important;
            line-height: 1 !important;
        }

        .tv-body .emot-label {
            font-size: 12px !important;
            font-weight: 900 !important;
            white-space: nowrap !important;
        }

        .tv-body .status-chip {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            width: 100% !important;
            box-sizing: border-box !important;
            text-align: center !important;
            font-size: 14px !important;
            font-weight: 900 !important;
            padding: 12px 8px !important;
            border-radius: 20px !important;
            margin: 10px 0 0 !important;
            flex-shrink: 0 !important;
        }

        .tv-body .status-live-row {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 3px !important;
            margin: 5px 0 0 !important;
            flex-shrink: 0 !important;
        }

        .tv-body .live-item {
            flex: 1 1 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 10px 2px !important;
            border-radius: 10px !important;
            box-sizing: border-box !important;
            min-width: 0 !important;
        }

        .tv-body .live-value {
            font-size: clamp(22px, 2.5vw, 32px) !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            display: block !important;
        }

        .tv-body .live-label {
            font-size: 11px !important;
            font-weight: 800 !important;
            text-align: center !important;
            letter-spacing: 0.8px !important;
            margin-top: 4px !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
            display: block !important;
        }

        /* ── Chart section ── */
        .tv-body .tv-chart-section .section-title {
            flex-shrink: 0 !important;
            margin-bottom: 4px !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap {
            flex: 1 1 0 !important;
            min-height: 0 !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 16px !important;
            padding: 16px 14px !important;
            background: #fff !important;
            border: 1px solid #dde8c8 !important;
            border-radius: 10px !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .chart-legend-wrap {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 24px !important;
            width: 100% !important;
            flex: 1 1 0 !important;
            min-height: 0 !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .legend-content {
            display: flex !important;
            flex-direction: column !important;
            gap: 5px !important;
            align-items: flex-start !important;
            justify-content: center !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .chart-container {
            position: relative !important;
            width: clamp(130px, 14vw, 190px) !important;
            height: clamp(130px, 14vw, 190px) !important;
            flex-shrink: 0 !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .chart-center {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            text-align: center !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .cv {
            font-size: clamp(14px, 1.6vw, 20px) !important;
            font-weight: 900 !important;
            color: #2e7d32 !important;
            line-height: 1.1 !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .cl {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #888 !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .legend-item {
            font-size: clamp(10px, 1vw, 13px) !important;
            font-weight: 700 !important;
            color: #444 !important;
            white-space: nowrap !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .leg-dot {
            width: 9px !important;
            height: 9px !important;
            border-radius: 50% !important;
            flex-shrink: 0 !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .absen-bar-wrap {
            width: 100% !important;
            flex-shrink: 0 !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .absen-bar {
            background: #e8f5e9 !important;
            border-radius: 20px !important;
            height: 20px !important;
            overflow: hidden !important;
            width: 100% !important;
        }

        .tv-body .tv-chart-section .attendance-chart-wrap .absen-fill {
            background: var(--green-light) !important;
            height: 100% !important;
            border-radius: 20px !important;
            display: flex !important;
            align-items: center !important;
            padding-left: 8px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            color: #fff !important;
            min-width: 36px !important;
            transition: width .5s ease !important;
            box-sizing: border-box !important;
        }

        /* ── Right column scroll ── */
        .tv-body .status-mesin-scroll {
            flex: 1 1 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
            scrollbar-width: thin;
            scrollbar-color: #2e7d32 #eef2e8;
        }

        .tv-body .status-mesin-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .tv-body .status-mesin-scroll::-webkit-scrollbar-track {
            background: #eef2e8;
            border-radius: 3px;
        }

        .tv-body .status-mesin-scroll::-webkit-scrollbar-thumb {
            background: #2e7d32;
            border-radius: 3px;
        }

        .tv-body .tv-mesin-header {
            flex-shrink: 0;
        }

        /* ── Machine cards (scoped) ── */
        .tv-body .mc-photo-upload-overlay {
            display: none !important;
        }

        .tv-body .mc-addlog-bar {
            display: none !important;
        }

        .tv-body .mc-dot {
            cursor: default !important;
            pointer-events: none !important;
        }

        .tv-body .mc-card {
            cursor: default !important;
        }

        .tv-body .tag-repl {
            background: #e65100;
            color: #fff;
        }

        .tv-body .av-repl {
            background: linear-gradient(135deg, #e65100, #ff7043);
        }

        .tv-body .machines-wrap {
            padding: 0 0 20px;
        }

        .tv-body .machine-group-section {
            margin-bottom: 20px;
        }

        .tv-body .machine-group-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2E7D32;
            color: #fff;
            padding: 6px 16px 6px 12px;
            border-radius: 0 20px 20px 0;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .8px;
            margin-bottom: 12px;
            margin-left: -12px;
            box-shadow: 2px 2px 8px rgba(31, 60, 136, .25);
        }

        .tv-body .mg-badge {
            background: var(--brand-secondary);
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .tv-body .mg-badge.warn {
            background: rgba(231, 76, 60, .75);
        }

        .tv-body .machine-cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .tv-body .mc-card {
            background: #fff;
            border-radius: 14px;
            border: 2px solid #e0ecd4;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: box-shadow .2s, border-color .2s;
        }

        .tv-body .mc-card.mc-status-man {
            border-color: #e74c3c;
            box-shadow: 0 3px 14px rgba(231, 76, 60, .22);
        }

        .tv-body .mc-card.mc-status-machine {
            border-color: #1f3c88;
        }

        .tv-body .mc-card.mc-status-material {
            border-color: #f39c12;
        }

        .tv-body .mc-card.mc-status-method {
            border-color: #2e7d32;
        }

        .tv-body .mc-card.mc-has-absen {
            border-color: #e74c3c;
            animation: absenglow 1.5s ease-in-out infinite;
        }

        @keyframes absenglow {

            0%,
            100% {
                box-shadow: 0 3px 14px rgba(231, 76, 60, .3)
            }

            50% {
                box-shadow: 0 3px 28px rgba(231, 76, 60, .65)
            }
        }

        .tv-body .mc-photo-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, #eef2e8, #dde8c8);
            overflow: hidden;
            border-bottom: 1px solid #e0ecd4;
        }

        .tv-body .mc-card.mc-has-absen .mc-photo-wrap,
        .tv-body .mc-card.mc-status-man .mc-photo-wrap {
            background: linear-gradient(135deg, #fdeaea, #fad0d0);
            border-bottom-color: #f4a8a8;
        }

        .tv-body .mc-photo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tv-body .mc-photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .tv-body .mc-photo-placeholder .ph-ico {
            font-size: 28px;
            opacity: .25;
        }

        .tv-body .mc-photo-placeholder .ph-txt {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #aaa;
        }

        .tv-body .mc-name-badge {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, .6) 0%, transparent 100%);
            padding: 20px 8px 6px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            z-index: 3;
            pointer-events: none;
        }

        .tv-body .mc-name-txt {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
            text-shadow: 0 1px 3px rgba(0, 0, 0, .5);
            line-height: 1;
        }

        .tv-body .mc-4m-row {
            display: flex;
            gap: 3px;
            align-items: center;
        }

        .tv-body .mc-4m-pip {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .3);
        }

        .tv-body .mc-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, .6);
            display: none;
            flex-shrink: 0;
        }

        .tv-body .mc-dot.d-visible {
            display: block;
        }

        .tv-body .mc-dot.d-absen {
            display: block;
            background: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, .4);
            animation: abpulse 1.3s ease-in-out infinite;
        }

        @keyframes abpulse {

            0%,
            100% {
                box-shadow: 0 0 0 2px rgba(231, 76, 60, .5)
            }

            50% {
                box-shadow: 0 0 0 7px rgba(231, 76, 60, 0)
            }
        }

        .tv-body .mc-card-body {
            padding: 8px 8px 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .tv-body .mc-members-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
            min-height: 64px;
        }

        .tv-body .mc-member-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            min-width: 52px;
        }

        .tv-body .mc-av {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            color: #fff;
            overflow: hidden;
            border: 2.5px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
            font-family: 'Roboto Condensed', sans-serif;
            flex-shrink: 0;
        }

        .tv-body .mc-av img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .tv-body .av-ok {
            background: linear-gradient(135deg, #8bc34a, var(--green, #2e7d32));
        }

        .tv-body .av-absen {
            background: linear-gradient(135deg, #ef9a9a, #c0392b);
            filter: grayscale(.3);
        }

        .tv-body .mc-member-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-align: center;
            color: #555;
            line-height: 1.2;
            word-break: break-word;
            max-width: 64px;
        }

        .tv-body .mi-absen .mc-member-name {
            color: #c0392b;
        }

        .tv-body .mc-member-tag {
            font-size: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 800;
            padding: 1px 4px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .tv-body .tag-hadir {
            background: var(--green, #2e7d32);
            color: #fff;
        }

        .tv-body .tag-absen {
            background: #e74c3c;
            color: #fff;
            animation: tagblink .8s step-end infinite;
        }

        .tv-body .tag-dipinjam {
            background: #607d8b;
            color: #fff;
        }

        @keyframes tagblink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .45
            }
        }

        .tv-body .mi-dipinjam {
            opacity: .72;
        }

        .tv-body .mi-dipinjam .mc-av {
            filter: grayscale(.55) brightness(.75);
            border-color: #b0bec5;
        }

        .tv-body .mi-dipinjam .mc-member-name {
            color: #78909c;
        }

        .tv-body .mi-dipinjam-dest {
            font-size: 9px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            color: #546e7a;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 64px;
        }

        .tv-body .mc-empty-slot {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #d0ddc0;
            border-radius: 10px;
            min-height: 54px;
            color: #ccc;
            font-size: 22px;
            width: 100%;
        }

        .tv-body .mc-status-row {
            display: flex;
            gap: 3px;
            flex-wrap: wrap;
            justify-content: center;
            padding-top: 5px;
            border-top: 1px solid #eef2e8;
        }

        .tv-body .mc-status-pill {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 4px 7px;
            border-radius: 20px;
            border: 1.5px solid #e4e4e4;
            background: #f7f7f7;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #aaa;
            white-space: nowrap;
            line-height: 1;
        }

        .tv-body .pill-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .tv-body .mc-status-pill.p-normal.active {
            background: #e8f5e9;
            border-color: var(--green, #2e7d32);
            color: var(--green, #2e7d32);
        }

        .tv-body .mc-status-pill.p-man.active {
            background: #fdeaea;
            border-color: #e74c3c;
            color: #e74c3c;
        }

        .tv-body .mc-status-pill.p-material.active {
            background: #fff8e1;
            border-color: #f39c12;
            color: #c67c00;
        }

        .tv-body .mc-status-pill.p-machine.active {
            background: #e8eefa;
            border-color: #1f3c88;
            color: #1f3c88;
        }

        .tv-body .mc-status-pill.p-method.active {
            background: #eeff82 !important;
            border-color: #afcb1f !important;
            color: #afcb1f !important;
        }

        .tv-body .mc-card.mc-status-method:hover {
            border-color: #afcb1f !important;
        }

        .tv-body .pill-dot.dn {
            background: #4caf50;
        }

        .tv-body .pill-dot.dm {
            background: #e74c3c;
        }

        .tv-body .pill-dot.dt {
            background: #f39c12;
        }

        .tv-body .pill-dot.dc {
            background: #1f3c88;
        }

        .tv-body .pill-dot.dme {
            background: #2e7d32;
        }

        /* ── TV fixed elements (standalone page, tidak perlu scope) ── */
        .tv-badge {
            position: fixed;
            top: 64px;
            left: 10px;
            z-index: 500;
            pointer-events: none;
            background: linear-gradient(135deg, #e65100, #ff7043);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1.5px;
            padding: 4px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(230, 81, 0, .45);
            animation: tvbadge 2.4s ease-in-out infinite;
        }

        @keyframes tvbadge {

            0%,
            100% {
                box-shadow: 0 2px 10px rgba(230, 81, 0, .45)
            }

            50% {
                box-shadow: 0 2px 22px rgba(230, 81, 0, .85)
            }
        }

        .tv-paused {
            position: fixed;
            top: 58px;
            right: 10px;
            z-index: 500;
            display: none;
            pointer-events: none;
            background: rgba(230, 81, 0, .92);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 1.2px;
            padding: 5px 14px;
            border-radius: 20px;
        }

        .tv-paused.show {
            display: block;
        }

        .tv-fs {
            position: fixed;
            bottom: 44px;
            right: 12px;
            z-index: 500;
            background: rgba(26, 92, 26, .8);
            color: rgba(255, 255, 255, .7);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 8px;
            padding: 5px 12px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
        }

        .tv-fs:hover {
            color: #fff;
            background: rgba(26, 92, 26, 1);
        }

        .tv-ticker {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 36px;
            z-index: 600;
            background: linear-gradient(135deg, #1a5c1a, #2d7a2d);
            border-top: 2px solid rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .tv-ticker-lbl {
            flex-shrink: 0;
            height: 100%;
            padding: 0 14px;
            background: rgba(0, 0, 0, .2);
            border-right: 1px solid rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 900;
            color: #f5a623;
            letter-spacing: 1.5px;
            white-space: nowrap;
        }

        .tv-ticker-track {
            flex: 1;
            overflow: hidden;
            height: 100%;
        }

        .tv-ticker-inner {
            display: inline-flex;
            align-items: center;
            height: 100%;
            white-space: nowrap;
            gap: 48px;
            padding-left: 20px;
            animation: tv-roll 40s linear infinite;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: rgba(255, 255, 255, .9);
        }

        @keyframes tv-roll {
            0% {
                transform: translateX(0)
            }

            100% {
                transform: translateX(-50%)
            }
        }

        .tick-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
            display: inline-block;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        /* ── Slide Wrapper ── */
        .tv-master-slide {
            position: absolute; top: 0; left: 0; width: 100vw; height: 100vh;
            opacity: 0; visibility: hidden; pointer-events: none; transition: opacity 0.5s ease;
            z-index: 1; background: transparent; overflow: hidden;
        }
        .tv-master-slide.active { opacity: 1; visibility: visible; pointer-events: auto; z-index: 2; }

        /* ── Navigation Dots ── */
        .tv-nav-dots { position: absolute; bottom: 45px; left: 50%; transform: translateX(-50%); z-index: 500; display: flex; gap: 12px; }
        .tv-nav-dot { width: 45px; height: 6px; border-radius: 4px; background: rgba(255,255,255,0.25); cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); }
        .tv-nav-dot.active { background: #fff; box-shadow: 0 0 10px rgba(255,255,255,0.8); }
        .tv-nav-dot:hover { background: rgba(255,255,255,0.6); }

        /* ── Slide 2 (Floor Plan + Matrix) CSS ── */
        #tvSlide2 { background: #E5E7EB; padding: 75px 16px 75px 16px; font-family: 'Roboto', sans-serif; display: flex; flex-direction: column; }
        #tvSlide2 .main-layout { display: flex; gap: 16px; flex: 1; min-height: 0; }
        #tvSlide2 .left-panel { flex: 0 0 38%; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; flex-direction: column; overflow: hidden; position: relative; padding: 16px; }
        #tvSlide2 .tv-img-wrap { position: relative; display: inline-block; line-height: 0; max-width: 100%; max-height: 100%; }
        #tvSlide2 .tv-img-wrap img { display: block; max-width: 100%; max-height: calc(100vh - 150px); object-fit: contain; }
        #tvSlide2 .tv-pin { position: absolute; width: 24px; height: 24px; border-radius: 50%; transform: translate(-50%, -50%); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.3); border: 2px solid #fff; }
        #tvSlide2 .tv-pin-label { position: absolute; right: 100%; margin-right: 8px; background: transparent; color: #000; font-weight: 900; font-size: 14px; white-space: nowrap; font-family: 'Roboto Condensed', sans-serif; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff; }
        #tvSlide2 .pin-ok      { background: #2E7D32; }
        #tvSlide2 .pin-warn    { background: #F39C12; }
        #tvSlide2 .pin-problem { background: #E74C3C; }
        #tvSlide2 .pin-off     { background: #9E9E9E; }
        #tvSlide2 .fp-empty { color: #777; text-align: center; font-size: 14px; line-height: 1.6; }
        
        #tvSlide2 .right-panel { flex: 1; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; flex-direction: column; overflow: hidden; padding: 16px; }
        #tvSlide2 .matrix-header { background: #185E35; color: #fff; padding: 10px 16px; border-radius: 6px; font-family: 'Roboto Condensed', sans-serif; font-size: 14px; font-weight: 700; display: flex; justify-content: space-between; margin-bottom: 12px; }
        #tvSlide2 .summary-stats { display: flex; justify-content: space-around; padding: 10px 0 20px 0; border-bottom: 1px solid #eee; margin-bottom: 16px; }
        #tvSlide2 .stat-item { text-align: center; }
        #tvSlide2 .stat-val { font-size: 24px; font-weight: 900; color: #333; font-family: 'Roboto Condensed', sans-serif; }
        #tvSlide2 .stat-lbl { font-size: 11px; color: #666; margin-top: 4px; }
        #tvSlide2 .matrix-content { flex: 1; overflow: auto; }
        
        #tvSlide2 .m-table { width: 100%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-size: 11px; }
        #tvSlide2 .m-table th { background: var(--brand-primary); color: #fff; padding: 8px; font-weight: 600; text-align: center; border: 1px solid rgba(0,0,0,.15); white-space: nowrap; position: sticky; top: 0; z-index: 10; }
        #tvSlide2 .m-table thead tr:nth-child(2) th { top: 31px; z-index: 9; }
        #tvSlide2 .m-table thead tr:nth-child(3) th { top: 62px; z-index: 8; }
        
        /* ponytail: No column — narrow, sticky left-0 */
        #tvSlide2 .m-table th.col-no, #tvSlide2 .m-table td.col-no { width: 34px; min-width: 34px; max-width: 34px; left: 0; position: sticky; font-weight: 900; color: var(--brand-primary); text-align: center; }
        #tvSlide2 .m-table th.col-no { color: #fff; }

        #tvSlide2 .m-table th.col-name, #tvSlide2 .m-table td.col-name { width: 140px; min-width: 140px; max-width: 140px; left: 34px; position: sticky; text-align: left; }
        #tvSlide2 .m-table th.col-shift, #tvSlide2 .m-table td.col-shift { width: 40px; min-width: 40px; max-width: 40px; left: 174px; position: sticky; }
        
        #tvSlide2 .m-table th.col-no, #tvSlide2 .m-table th.col-name, #tvSlide2 .m-table th.col-shift { z-index: 12 !important; background: var(--brand-primary); }
        #tvSlide2 .m-table td.col-no, #tvSlide2 .m-table td.col-name, #tvSlide2 .m-table td.col-shift { z-index: 11; background: #fff; box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1); font-weight: 600; }
        
        #tvSlide2 .m-table td { padding: 6px 8px; border: 1px solid #eee; text-align: center; color: #333; background: #fff; }
        #tvSlide2 .m-table tr:nth-child(even) td:not(.col-no):not(.col-name):not(.col-shift) { background: #f9f9f9; }
        
        #tvSlide2 .m-table thead tr:first-child th:first-child { border-top-left-radius: 11px; }
        #tvSlide2 .m-table thead tr:first-child th:last-child { border-top-right-radius: 11px; }
        #tvSlide2 .m-table tbody tr:last-child td:first-child { border-bottom-left-radius: 11px; }
        #tvSlide2 .m-table tbody tr:last-child td:last-child { border-bottom-right-radius: 11px; }
        
        #tvSlide2 .skill-circle { display: inline-block; width: 14px; height: 14px; border-radius: 50%; border: 1px solid #333; position: relative; background: #fff; vertical-align: middle; }
        #tvSlide2 .skill-100 { background: #333; }
        #tvSlide2 .skill-75 { background: conic-gradient(#333 0deg 270deg, #fff 270deg 360deg); }
        #tvSlide2 .skill-50 { background: conic-gradient(#333 0deg 180deg, #fff 180deg 360deg); }
        #tvSlide2 .skill-25 { background: conic-gradient(#333 0deg 90deg, #fff 90deg 360deg); }
        #tvSlide2 .skill-0 { background: #fff; border-color: #ccc; }
        
        #tvSlide2 .chip-level { font-size: 10px; font-weight: 700; color: #2E7D32; }
        #tvSlide2 .chip-level.training { color: #E74C3C; }
    </style>
</head>

<body>

    <!-- SLIDE 1: Dashboard -->
    <div class="tv-master-slide active" id="tvSlide1">

    {{-- ════════════════════════════════════════════════════════════════
    HEADER - identik persis app-header dari admin.blade.php
    ════════════════════════════════════════════════════════════════ --}}
    <div class="app-header" style="padding:0 12px;gap:10px;position:absolute;top:0;left:0;right:0;z-index:400;">

        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
            <img src="{{ asset('/images/sugity.png') }}" alt="Sugity Creatives"
                style="height:36px;width:auto;object-fit:contain;flex-shrink:0;" onerror="this.style.display='none'">
            <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
                <span
                    style="font-family:'Roboto Condensed',sans-serif;font-weight:900;font-size:25px;letter-spacing:2px;color:#fff;text-shadow:0 0 14px rgba(245,166,35,.5);white-space:nowrap;-webkit-text-stroke: 1px rgba(255, 255, 255, 0.6);">HENKATEN
                    BOARD</span>
                <span
                    style="font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:14px;letter-spacing:1.2px;color:rgba(255,255,255,.85);text-transform:uppercase;white-space:nowrap;">
                    {{ $factory }} @if(!empty($factoryDetails)) - {{ $factoryDetails }} @endif
                </span>
            </div>
        </div>

        <span
            style="background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.3);border-radius:20px;padding:6px 16px;font-family:'Roboto Condensed',sans-serif;font-size:14px;font-weight:900;color:#fff;letter-spacing:.8px;flex-shrink:0;">SHIFT
            {{ $shift }}</span>

        <div class="header-clock" id="tvClock">00:00:00</div>

        <a href="{{ route('admin.dashboard') }}"
            style="background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);border-radius:8px;padding:6px 14px;color:rgba(255,255,255,.75);font-family:'Roboto Condensed',sans-serif;font-size:14px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:5px;flex-shrink:0;"
            onmouseover="this.style.background='rgba(255,255,255,.2)';this.style.color='#fff'"
            onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.75)'">
            ✕ Exit TV
        </a>
    </div>

    <div class="tv-badge">📺 TV MODE</div>
    <div class="tv-paused" id="tvPaused">⏸ PAUSED</div>

    {{-- ════════════════════════════════════════════════════════════════
    BODY
    ════════════════════════════════════════════════════════════════ --}}
    <div class="tv-body" id="tvBody">

        {{-- ════ LEFT COLUMN ════ --}}
        <div class="tv-col-left">

            {{-- ── date-bar ── --}}
            <div class="date-bar">
                <div class="date-label">📅</div>
                <span style="flex:1;font-family:'Roboto Condensed',sans-serif;font-weight:600;font-size:16px;color:#222;letter-spacing:.5px;">
                    {{ \Carbon\Carbon::parse($tanggal)->format('d / m / Y') }}
                </span>
            </div>

            {{-- ── shift-toggle-bar ── --}}
            <div class="shift-toggle-bar">
                <button class="shift-toggle-btn {{ $shift === 'A' ? 'active' : '' }}">SHIFT A</button>
                <button class="shift-toggle-btn {{ $shift === 'B' ? 'active' : '' }}">SHIFT B</button>
            </div>

            {{-- ── legend-4m ── --}}
            <div class="legend-4m">
                <div class="legend-4m-item">
                    <div class="l4m-dot" style="background:var(--red)"></div>Man (Absen)
                </div>
                <div class="legend-4m-item">
                    <div class="l4m-dot" style="background:var(--navy)"></div>Machine
                </div>
                <div class="legend-4m-item">
                    <div class="l4m-dot" style="background:var(--yellow)"></div>Material
                </div>
                <div class="legend-4m-item">
                    <div class="l4m-dot" style="background:var(--green-light)"></div>Method
                </div>
            </div>

            {{-- ── status-panel ── --}}
            @php
                $chipMap = ['chip-green', 'chip-yellowgreen', 'chip-yellow', 'chip-red'];
                $chipTxt = [
                    'OKE  - Semua Normal',
                    'LOW RISK  - Absen 1',
                    'MID RISK  - Absen 2–3 / Ada Problem',
                    'HIGH RISK  - Absen ≥4 / MC Problem ≥2',
                ];
            @endphp
            <div class="status-panel">
                <div class="status-panel-title">📊 Overall Status</div>
                <div class="auto-refresh-bar">
                    <div class="ar-left">
                        <div class="ar-dot" id="arDot" style="display: none; background: #ffffff;"></div>
                        <span id="arLabel">Auto Refresh</span>
                    </div>
                    <span class="ar-countdown" id="arCountdown">30s</span>
                </div>
                <div class="ar-last-updated" id="arLastUpdated">Belum diperbarui</div>
                <div class="status-emot-row">
                    <div class="emot-card {{ $statusLevel === 0 ? 'active-green' : '' }}" id="ec-green">
                        <span class="emot-icon"></span>
                        <div class="emot-label">SAFE</div>
                    </div>
                    <div class="emot-card {{ $statusLevel === 1 ? 'active-yellowgreen' : '' }}" id="ec-yg">
                        <span class="emot-icon"></span>
                        <div class="emot-label">LOW RISK</div>
                    </div>
                    <div class="emot-card {{ $statusLevel === 2 ? 'active-yellow' : '' }}" id="ec-yellow">
                        <span class="emot-icon"></span>
                        <div class="emot-label">MID RISK</div>
                    </div>
                    <div class="emot-card {{ $statusLevel === 3 ? 'active-red' : '' }}" id="ec-red">
                        <span class="emot-icon"></span>
                        <div class="emot-label">HIGH RISK</div>
                    </div>
                </div>
                <div class="status-chip {{ $chipMap[$statusLevel] }}" id="statusChip">{{ $chipTxt[$statusLevel] }}</div>
                <div class="status-live-row">
                    <div class="live-item">
                        <div class="live-value" id="sMan" style="color:var(--red)">{{ $machineSummary['man'] }}</div>
                        <div class="live-label">Man</div>
                    </div>
                    <div class="live-item">
                        <div class="live-value" id="sMachine" style="color:var(--navy)">{{ $machineSummary['machine'] }}
                        </div>
                        <div class="live-label">Machine</div>
                    </div>
                    <div class="live-item">
                        <div class="live-value" id="sMaterial" style="color:var(--yellow)">
                            {{ $machineSummary['material'] }}
                        </div>
                        <div class="live-label">Material</div>
                    </div>
                    <div class="live-item">
                        <div class="live-value" id="sMethod" style="color:var(--green-light)">
                            {{ $machineSummary['method'] }}
                        </div>
                        <div class="live-label">Method</div>
                    </div>
                </div>
            </div>

            {{-- ── Diagram Attendance ── --}}
            <div class="tv-chart-section">
                <div class="section-title">Attendance</div>
                <div class="attendance-chart-wrap">
                    <div class="chart-legend-wrap">
                        {{-- Chart Kiri --}}
                        <div class="chart-container">
                            <canvas id="myChart"></canvas>
                            <div class="chart-center">
                                <div class="cv" id="centerValue">
                                    {{ $absenceSummary ? $absenceSummary->mp_hadir . '/' . $absenceSummary->total_member : '0/0' }}
                                </div>
                                <div class="cl">MP</div>
                            </div>
                        </div>

                        {{-- Legend Kanan --}}
                        <div class="legend-content">
                            <div class="legend-item"><span class="leg-dot" style="background:#729E3F"></span>MP</div>
                            <div class="legend-item"><span class="leg-dot" style="background:#5dade2"></span>PENGAWAS
                                SAKIT
                            </div>
                            <div class="legend-item"><span class="leg-dot" style="background:#FF8F1F"></span>PENGAWAS
                                IZIN
                            </div>
                            <div class="legend-item"><span class="leg-dot" style="background:#1F3C88"></span>PENGAWAS
                                CUTI
                            </div>
                            <div class="legend-item"><span class="leg-dot" style="background:#8e44ad"></span>OPERATOR
                                SAKIT
                            </div>
                            <div class="legend-item"><span class="leg-dot" style="background:#f1c40f"></span>OPERATOR
                                IZIN
                            </div>
                            <div class="legend-item"><span class="leg-dot" style="background:#ff69b4"></span>OPERATOR
                                CUTI
                            </div>
                        </div>
                    </div>
                    {{-- Progress bar --}}
                    <div class="absen-bar-wrap">
                        @php
                            $pct = ($absenceSummary && $absenceSummary->total_member > 0)
                                ? round($absenceSummary->mp_hadir / $absenceSummary->total_member * 100, 1)
                                : 0;
                        @endphp
                        <div class="absen-bar">
                            <div class="absen-fill" id="absenFill" style="width:{{ $pct }}%">{{ $pct }}%</div>
                        </div>
                    </div>{{-- /absen-bar-wrap --}}

                </div>{{-- /attendance-chart-wrap --}}
            </div>{{-- /tv-chart-section --}}

        </div>{{-- /tv-col-left --}}

        {{-- ════ RIGHT COLUMN ════ --}}
        <div class="tv-col-right">

            @php
                use App\Models\Machine;
                use App\Models\Member;

                $scId = \App\Services\ScContext::id();

                if ($factory === 'Factory 2') {
                    $totMcF2 = Machine::where('sc_id', $scId)->where('factory', 'Factory 2')->where('status', 'mesin')->count();
                    $totRobotF2 = Machine::where('sc_id', $scId)->where('factory', 'Factory 2')->where('status', 'line')->count();
                } else {
                    $totMcF3 = Machine::where('sc_id', $scId)->where('factory', 'Factory 3 & 4')->where('section', 'f3-resin')->where('status', 'mesin')->count();
                    $totMcF4 = Machine::where('sc_id', $scId)->where('factory', 'Factory 3 & 4')->where('section', 'f4-resin')->where('status', 'mesin')->count();
                    $totRobotF34 = Machine::where('sc_id', $scId)->where('factory', 'Factory 3 & 4')->where('status', 'robot')->count();
                    $totVibF34 = Machine::where('sc_id', $scId)->where('factory', 'Factory 3 & 4')->where('status', 'mc_vibration')->count();
                }
            @endphp

            <style>
                .tv-report-summary {
                    display: flex;
                    gap: 8px;
                    margin-bottom: 14px;
                    width: 100%;
                }

                .tv-rs-card {
                    flex: 1 1 0;
                    background: #fff;
                    border: 2px solid #e0ecd4;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 10px 4px;
                    min-width: 0;
                }

                .tv-rs-val {
                    font-family: 'Roboto Condensed', sans-serif;
                    font-size: 20px;
                    font-weight: 900;
                    color: #222;
                    line-height: 1.1;
                    margin-bottom: 3px;
                }

                .tv-rs-lbl {
                    font-family: 'Roboto Condensed', sans-serif;
                    font-size: 9px;
                    font-weight: 700;
                    color: #555;
                    text-transform: uppercase;
                    text-align: center;
                    letter-spacing: 0.5px;
                    line-height: 1.2;
                    white-space: pre-wrap;
                }
            </style>

            <div class="section-title">FACTORY OVERVIEW</div>
            <div class="tv-report-summary">
                @if($factory === 'Factory 2')
                    <div class="tv-rs-card">
                        <div class="tv-rs-val" id="rsMpF2">{{ $total_mp }}</div>
                        <div class="tv-rs-lbl">TOTAL MP</div>
                    </div>
                    <div class="tv-rs-card">
                        <div class="tv-rs-val">{{ $totMcF2 }}</div>
                        <div class="tv-rs-lbl">TOTAL MC</div>
                    </div>
                    <div class="tv-rs-card">
                        <div class="tv-rs-val">{{ $totRobotF2 }}</div>
                        <div class="tv-rs-lbl">TOTAL ROBOT</div>
                    </div>
                @else
                    <div class="tv-rs-card">
                        <div class="tv-rs-val" id="rsMpF34">{{ $total_mp }}</div>
                        <div class="tv-rs-lbl">TOTAL MP<br>FAC 3&4</div>
                    </div>
                    <div class="tv-rs-card">
                        <div class="tv-rs-val">{{ $totMcF3 }}</div>
                        <div class="tv-rs-lbl">TOTAL MC<br>FAC 3</div>
                    </div>
                    <div class="tv-rs-card">
                        <div class="tv-rs-val">{{ $totRobotF34 }}</div>
                        <div class="tv-rs-lbl">TOTAL ROBOT</div>
                    </div>
                    <div class="tv-rs-card">
                        <div class="tv-rs-val">{{ $totVibF34 }}</div>
                        <div class="tv-rs-lbl">TOTAL<br>MC VIBRATION</div>
                    </div>
                    <div class="tv-rs-card">
                        <div class="tv-rs-val">{{ $totMcF4 }}</div>
                        <div class="tv-rs-lbl">TOTAL MC<br>FAC 4</div>
                    </div>
                @endif
            </div>

            <style>
                .tv-announcement-box {
                    background: #fff8e1;
                    border: 1.5px solid #f39c12;
                    border-radius: 8px;
                    padding: 8px 12px;
                    margin-bottom: 15px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(243, 156, 18, .15);
                }

                .tv-announce-badge {
                    background: #f39c12;
                    color: #fff;
                    font-family: 'Roboto Condensed', sans-serif;
                    font-size: 11px;
                    font-weight: 900;
                    padding: 4px 10px;
                    border-radius: 20px;
                    flex-shrink: 0;
                    letter-spacing: 0.5px;
                    box-shadow: 0 2px 4px rgba(243, 156, 18, .4);
                }

                .tv-announce-badge.ok {
                    background: var(--green);
                    border-color: var(--green);
                    box-shadow: 0 2px 4px rgba(46, 125, 50, .4);
                }

                .tv-announce-track {
                    flex: 1;
                    overflow: hidden;
                    position: relative;
                    height: 18px;
                    display: flex;
                    align-items: center;
                }

                .tv-announce-marquee {
                    display: flex;
                    white-space: nowrap;
                    font-family: 'Roboto Condensed', sans-serif;
                    font-size: 13px;
                    font-weight: 600;
                    color: #444;
                    position: absolute;
                    animation: marqueeScroll 25s linear infinite;
                }

                .tv-announce-marquee:hover {
                    animation-play-state: paused;
                }

                .tv-announce-item {
                    display: flex;
                    align-items: center;
                    margin-right: 40px;
                }

                .tv-announce-item b {
                    color: #c0392b;
                    margin: 0 4px;
                    font-weight: 800;
                }

                @keyframes marqueeScroll {
                    0% {
                        transform: translateX(100%);
                    }

                    100% {
                        transform: translateX(-100%);
                    }
                }

                .tv-announce-safe {
                    font-family: 'Roboto Condensed', sans-serif;
                    font-size: 13px;
                    font-weight: 700;
                    color: var(--green);
                }
            </style>

            @php
                $absenRecords = \App\Models\AbsenceRecord::where([
                    'tanggal' => $tanggal,
                    'factory' => $factory,
                    'shift' => $shift,
                    'status' => 'absen',
                ])->get()->keyBy('member_id');
                $absenIds = $absenRecords->keys()->toArray();
                $absenReasons = $absenRecords->map(fn($r) => $r->reason ?? '')->toArray();

                $openLogsData = \App\Models\ProblemLog::where([
                    'tanggal' => $tanggal,
                    'factory' => $factory,
                    'shift' => $shift,
                    'status' => 'open',
                ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->get();

                $announcements = [];
                foreach ($absenRecords as $memberId => $record) {
                    $memberObj = \App\Models\Member::find($memberId);
                    if ($memberObj) {
                        $memberMesin = $memberObj->mesin ?? 'Tidak diketahui';
                        $reason = strtolower($record->reason ?? '');
                        $absenLabel = 'Absen';
                        if (str_contains($reason, 'sakit'))
                            $absenLabel = 'SAKIT';
                        elseif (str_contains($reason, 'izin') || str_contains($reason, 'ijin'))
                            $absenLabel = 'IZIN';
                        elseif (str_contains($reason, 'cuti'))
                            $absenLabel = 'CUTI';
                        $announcements[] = "👷 <b>{$memberObj->nama}</b> ({$memberMesin}) tidak masuk karena <b>{$absenLabel}</b>";
                    }
                }
                $openLogsByJenis = $openLogsData->groupBy('jenis');
                foreach ($openLogsByJenis as $jenis => $logs) {
                    $jenisUpper = strtoupper($jenis);
                    $lokasiList = $logs->pluck('lokasi')->filter()->implode(', ');
                    $announcements[] = "⚠️ Open Logs ({$logs->count()}) <b>{$jenisUpper}</b>" . ($lokasiList ? "  - {$lokasiList}" : '');
                }
            @endphp

            {{-- PROBLEM DETAIL TABLE - Stacked --}}
            <div class="tv-problem-detail" id="tvProblemDetail">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                    <div class="section-title" style="margin:0;">📋 4M Henkaten</div>
                    <span id="tvProbLastSync"
                        style="font-size:18px;color:#aaa;font-family:'Roboto Condensed',sans-serif;"> -</span>
                </div>

                {{-- Active Table (Top) --}}
                <div class="tv-prob-panel">
                    <div class="tv-prob-panel-header"
                        style="background: #ffe8e8; border-color: #f5c6c6; color: #c0392b;">
                        <div>🔴 DETAIL PROBLEM</div>
                        <span class="tv-prob-badge blink" id="tvProbBadgeActive"
                            style="background:#e74c3c; color:#fff;">0</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:3%; text-align:center;">NO</th>
                                <th style="width:6%">4M</th>
                                <th style="width:12%">Area Problem</th>
                                <th style="width:25%">Problem</th>
                                <th style="width:22%">Countermeasure</th>
                                <th style="width:8%">PIC</th>
                                <th style="width:6%; text-align:center;">Start</th>
                                <th style="width:6%; text-align:center;">Finish</th>
                                <th style="width:6%; text-align:center;">Durasi</th>
                                <th style="width:6%; text-align:center;">Progres</th>
                            </tr>
                        </thead>
                        <tbody id="tvProbTbodyActive">
                            <tr>
                                <td colspan="10" class="tv-prob-empty">Memuat data…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- History Table (Bottom) --}}
                <div class="tv-prob-panel">
                    <div class="tv-prob-panel-header"
                        style="background: #e8f5e9; border-color: #c8e6c9; color: #2e7d32;">
                        <div>📋 HISTORY</div>
                        <span class="tv-prob-badge" id="tvProbBadgeHistory"
                            style="background:#2e7d32; color:#fff;">0</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:3%; text-align:center;">No</th>
                                <th style="width:8%">Tanggal</th>
                                <th style="width:6%">4M</th>
                                <th style="width:12%">Area Problem</th>
                                <th style="width:20%">Problem</th>
                                <th style="width:18%">Countermeasure</th>
                                <th style="width:7%">PIC</th>
                                <th style="width:6%; text-align:center;">Start</th>
                                <th style="width:6%; text-align:center;">Finish</th>
                                <th style="width:6%; text-align:center;">Durasi</th>
                                <th style="width:8%; text-align:center;">Progres</th>
                            </tr>
                        </thead>
                        <tbody id="tvProbTbodyHistory">
                            <tr>
                                <td colspan="11" class="tv-prob-empty">Memuat data…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- BOTTOM SECTION --}}
            @php $tvDetailMode = request()->get('mode') === 'detail'; @endphp
            <div class="tv-bottom-section" @if(!$tvDetailMode) style="display:none;" @endif>
                @if($tvDetailMode)
                    {{-- DETAIL MODE: full card list --}}
                    <div style="flex:1 1 0;display:flex;flex-direction:column;min-height:0;overflow:hidden;">
                        <div class="tv-detail-mode-bar">
                            <span>📋 Detail Semua Mesin - {{ $factory }} Shift {{ $shift }}</span>
                            <button class="tv-detail-btn" style="background:#e74c3c" onclick="window.close()">✕
                                Tutup</button>
                        </div>
                        <div
                            style="flex:1 1 0;min-height:0;overflow:auto;border:1.5px solid #c8e6c9;border-top:none;border-radius:0 0 8px 8px;background:#f9fdf9;">
                @endif

                        {{-- Machine Cards (hidden on TV / visible on detail) --}}
                        <div class="tv-machine-cards-col" id="tvMachineCardsCol" @if(!$tvDetailMode)
                        style="display:none" @endif>
                            <div class="status-mesin-scroll" style="height: 100%; overflow: auto;">
                                <div class="machines-wrap">
                                    @php
                                        try {
                                            $replacements = \App\Models\AssignmentReplacement::where([
                                                'tanggal' => $tanggal,
                                                'factory' => $factory,
                                                'shift' => $shift,
                                            ])->get()->keyBy('member_id');
                                        } catch (\Throwable $e) {
                                            $replacements = collect();
                                        }

                                        $replacedMachines = \App\Models\AssignmentReplacement::where([
                                            'tanggal' => $tanggal,
                                            'factory' => $factory,
                                            'shift' => $shift,
                                        ])->pluck('target_machine')->toArray();

                                        try {
                                            $replacementsAll = \App\Models\AssignmentReplacement::where([
                                                'tanggal' => $tanggal,
                                                'factory' => $factory,
                                                'shift' => $shift,
                                            ])->get();
                                        } catch (\Throwable $e) {
                                            $replacementsAll = collect();
                                        }

                                        $pipColors = ['man' => '#e74c3c', 'machine' => '#1f3c88', 'material' => '#f39c12', 'method' => '#2e7d32'];
                                        $pillDefs = [
                                            'normal' => ['dot' => null, 'label' => 'Normal'],
                                            'man' => ['dot' => 'dm', 'label' => 'Man'],
                                            'material' => ['dot' => 'dt', 'label' => 'Matl'],
                                            'machine' => ['dot' => 'dc', 'label' => 'Mc'],
                                            'method' => ['dot' => 'dme', 'label' => 'Method'],
                                        ];
                                    @endphp

                                    @foreach($groups as $group)
                                        @php
                                            $absenMesinCount = 0;
                                            foreach ($group['machines'] as $mac) {
                                                $asgn = $members->filter(fn($m) => $m->mesin === $mac || $m->mesin_secondary === $mac);
                                                if ($asgn->whereIn('id', $absenIds)->isNotEmpty() && !in_array($mac, $replacedMachines))
                                                    $absenMesinCount++;
                                            }
                                        @endphp

                                        <div class="machine-group-section">
                                            <div class="machine-group-title">
                                                @php
                                                    $sectionKey = $group['section_key'] ?? '';
                                                    $isKeyPersons = $group['is_key_persons'] ?? false;

                                                    $statusCount = collect($group['machines'])->filter(function ($machine) {
                                                        $machineObj = \App\Models\Machine::where('name', $machine)->first();
                                                        if (!$machineObj)
                                                            return false;
                                                        if ($machineObj->status === 'lainya' || $machineObj->status === 'mc_vibration')
                                                            return false;
                                                        return true;
                                                    })->count();

                                                    $gType = $group['type'] ?? 'mesin';
                                                    $unitLabel = match ($gType) {
                                                        'persons' => 'person',
                                                        'robot' => 'robot',
                                                        'line' => 'line',
                                                        'pos' => 'pos',
                                                        'lainya' => 'support',
                                                        'mc_vibration' => 'vibration',
                                                        default => 'machine',
                                                    };
                                                @endphp
                                                🔧 {{ $group['title'] }}
                                                @if($absenMesinCount > 0)
                                                    <span class="mg-badge warn">⚠ {{ $absenMesinCount }} absen</span>
                                                @else
                                                    <span class="mg-badge"
                                                        data-default="{{ $statusCount }} {{ $unitLabel }}">{{ $statusCount }}
                                                        {{ $unitLabel }}</span>
                                                @endif
                                            </div>

                                            <div class="machine-cards-row">
                                                @foreach($group['machines'] as $machine)
                                                    @php
                                                        $st = $statuses[$machine] ?? null;
                                                        $stVal = $st?->status ?? 'normal';
                                                        $stAll = $st?->statuses ?? [];

                                                        $assigned = $members->filter(fn($m) => $m->mesin === $machine || $m->mesin_secondary === $machine);
                                                        $absenMemberIds = $assigned->whereIn('id', $absenIds)->pluck('id');
                                                        $machineHasRepl = in_array($machine, $replacedMachines);
                                                        $hasAbsen = $absenMemberIds->isNotEmpty();
                                                        $needsFinder = $hasAbsen && !$machineHasRepl;

                                                        $cardCls = $needsFinder
                                                            ? 'mc-has-absen'
                                                            : (!$hasAbsen && $stVal !== 'normal' ? 'mc-status-' . $stVal : '');

                                                        $machineRecord = $machinePhotos[$machine] ?? null;
                                                        $machinePhoto = $machineRecord?->photo_url ?? null;
                                                        $factorySlug = Str::slug($factory);
                                                        $machineSlug = $factorySlug . '-' . Str::slug($machine);

                                                        $initPips = [];
                                                        if ($hasAbsen)
                                                            $initPips[] = 'man';
                                                        foreach ($stAll as $s) {
                                                            if ($s !== 'man' && !in_array($s, $initPips))
                                                                $initPips[] = $s;
                                                        }

                                                        $dotInitClass = $hasAbsen ? 'd-absen' : ($stVal !== 'normal' ? 'd-visible' : '');
                                                        $dotInitBg = (!$hasAbsen && $stVal !== 'normal') ? ($pipColors[$stVal] ?? '') : '';

                                                        $activePills = [];
                                                        if ($hasAbsen)
                                                            $activePills[] = 'man';
                                                        foreach ($stAll as $s) {
                                                            if (!in_array($s, $activePills))
                                                                $activePills[] = $s;
                                                        }
                                                        if (empty($activePills))
                                                            $activePills[] = 'normal';
                                                    @endphp

                                                    <div class="mc-card {{ $cardCls }}" data-machine="{{ $machine }}"
                                                        data-status="{{ $stVal }}">

                                                        <div class="mc-photo-wrap" id="photo-wrap-{{ $machineSlug }}">
                                                            @if($machinePhoto)
                                                                <img src="{{ $machinePhoto }}" alt="{{ $machine }}" loading="lazy"
                                                                    id="photo-img-{{ $machineSlug }}">
                                                            @else
                                                                <div class="mc-photo-placeholder" id="photo-img-{{ $machineSlug }}">
                                                                    <div class="ph-ico">📷</div>
                                                                    <div class="ph-txt">Foto Mesin</div>
                                                                </div>
                                                            @endif
                                                            <div class="mc-name-badge">
                                                                <span class="mc-name-txt">{{ $machine }}</span>
                                                                <div style="display:flex;align-items:center;gap:4px">
                                                                    <div class="mc-4m-row" id="lights-{{ $machineSlug }}">
                                                                        @foreach($initPips as $pip)
                                                                            <div class="mc-4m-pip"
                                                                                style="background:{{ $pipColors[$pip] ?? '#ccc' }}"
                                                                                title="{{ $pip }}"></div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mc-dot {{ $dotInitClass }}"
                                                                        id="dot-{{ $machineSlug }}" @if($dotInitBg)
                                                                        style="background:{{ $dotInitBg }}" @endif></div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mc-card-body">
                                                            <div class="mc-members-row">
                                                                @if($assigned->isEmpty())
                                                                    <div class="mc-empty-slot">+</div>
                                                                @else
                                                                    @foreach($assigned as $m)
                                                                        @php
                                                                            $isAbsen = in_array($m->id, $absenIds);
                                                                            $isDipinjam = !$isAbsen && isset($replacements[$m->id]);
                                                                            $destMachine = $isDipinjam ? ($replacements[$m->id]->target_machine ?? '?') : null;
                                                                            $itemCls = $isAbsen ? 'mi-absen' : ($isDipinjam ? 'mi-dipinjam' : '');
                                                                            $avCls = $isAbsen ? 'av-absen' : 'av-ok';
                                                                        @endphp
                                                                        <div class="mc-member-item {{ $itemCls }}"
                                                                            data-member-id="{{ $m->id }}">
                                                                            <div class="mc-av {{ $avCls }}">
                                                                                @if($m->photo_url)
                                                                                    <img src="{{ $m->photo_url }}" alt="{{ $m->nama }}"
                                                                                        @if($isAbsen)
                                                                                            style="filter:grayscale(.5) brightness(.8)"
                                                                                        @elseif($isDipinjam)
                                                                                            style="filter:grayscale(.55) brightness(.72)"
                                                                                        @endif>
                                                                                @else
                                                                                    {{ mb_strtoupper(mb_substr($m->nama, 0, 1)) . (str_contains($m->nama, ' ') ? mb_strtoupper(mb_substr(explode(' ', $m->nama)[1], 0, 1)) : '') }}
                                                                                @endif
                                                                            </div>
                                                                            <div class="mc-member-name" title="{{ $m->nama }}">
                                                                                {{ $m->nama }}
                                                                            </div>
                                                                            @if($isAbsen)
                                                                                @php
                                                                                    $absenReason = strtolower($absenReasons[$m->id] ?? '');
                                                                                    $absenLabel = match (true) {
                                                                                        str_contains($absenReason, 'sakit') => 'SAKIT',
                                                                                        str_contains($absenReason, 'izin') || str_contains($absenReason, 'ijin') => 'IZIN',
                                                                                        str_contains($absenReason, 'cuti') => 'CUTI',
                                                                                        default => 'Absen',
                                                                                    };
                                                                                @endphp
                                                                                <span
                                                                                    class="mc-member-tag tag-absen">{{ $absenLabel }}</span>
                                                                            @elseif($isDipinjam)
                                                                                <span class="mc-member-tag tag-dipinjam">Backup to</span>
                                                                                <div class="mi-dipinjam-dest"
                                                                                    title="Bertugas di: {{ $destMachine }}">↗
                                                                                    {{ Str::limit($destMachine, 8) }}
                                                                                </div>
                                                                            @else
                                                                                <span class="mc-member-tag tag-hadir">Hadir</span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach

                                                                    @foreach($replacementsAll->where('target_machine', $machine) as $repl)
                                                                        @php $replM = $members->firstWhere('id', $repl->member_id); @endphp
                                                                        @if($replM)
                                                                            @php $rpP = explode(' ', $replM->nama); @endphp
                                                                            <div class="mc-member-item" data-member-id="{{ $replM->id }}"
                                                                                data-replacement="1">
                                                                                <div class="mc-av av-repl">
                                                                                    @if($replM->photo_url)
                                                                                        <img src="{{ $replM->photo_url }}"
                                                                                            alt="{{ $replM->nama }}">
                                                                                    @else
                                                                                        {{ mb_strtoupper(mb_substr($rpP[0], 0, 1)) . (isset($rpP[1]) ? mb_strtoupper(mb_substr($rpP[1], 0, 1)) : '') }}
                                                                                    @endif
                                                                                </div>
                                                                                <div class="mc-member-name">{{ implode(' ', $rpP) }}</div>
                                                                                <span class="mc-member-tag tag-repl">Backup</span>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </div>

                                                            <div class="mc-status-row">
                                                                @foreach($pillDefs as $pKey => $pDef)
                                                                    <span
                                                                        class="mc-status-pill p-{{ $pKey }} {{ in_array($pKey, $activePills) ? 'active' : '' }}"
                                                                        data-status="{{ $pKey }}">
                                                                        @if($pDef['dot'])<span
                                                                        class="pill-dot {{ $pDef['dot'] }}"></span>@endif
                                                                        {{ $pDef['label'] }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>{{-- /machines-wrap --}}
                            </div>{{-- /status-mesin-scroll --}}
                        </div>{{-- /tv-machine-cards-col --}}

                        @if($tvDetailMode)
                                </div>{{-- /inner scroll --}}
                            </div>{{-- /detail col --}}
                        @endif

            </div>{{-- /tv-bottom-section --}}

        </div>{{-- /tv-col-right --}}

    </div>{{-- /tv-body --}}

    </div><!-- /tvSlide1 -->

    <!-- SLIDE 2: Floor Plan & Skill Matrix (Native) -->
    <div class="tv-master-slide" id="tvSlide2">
        <div class="app-header" style="padding:0 12px;gap:10px;position:absolute;top:0;left:0;right:0;z-index:400;">
            <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                <img src="{{ asset('/images/sugity.png') }}" alt="Sugity Creatives" style="height:36px;width:auto;object-fit:contain;flex-shrink:0;" onerror="this.style.display='none'">
                <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
                    <span style="font-family:'Roboto Condensed',sans-serif;font-weight:900;font-size:25px;letter-spacing:2px;color:#fff;text-shadow:0 0 14px rgba(245,166,35,.5);white-space:nowrap;-webkit-text-stroke: 1px rgba(255, 255, 255, 0.6);">HENKATEN BOARD</span>
                    <span style="font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:14px;letter-spacing:1.2px;color:rgba(255,255,255,.85);text-transform:uppercase;white-space:nowrap;">{{ $factory }}</span>
                </div>
            </div>
            <a href="{{ route('admin.dashboard') }}" style="background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);border-radius:8px;padding:6px 14px;color:rgba(255,255,255,.75);font-family:'Roboto Condensed',sans-serif;font-size:14px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:5px;flex-shrink:0;">✕ Exit TV</a>
        </div>
        
        <div class="main-layout">
            <!-- LEFT: Floor Plan -->
            <div class="left-panel">
                <div class="section-title" style="margin-bottom:16px;">LAYOUT FACTORY</div>
                <div style="flex:1; display:flex; align-items:center; justify-content:center; width:100%;">
                    @php $layout = \App\Models\FactoryLayout::where('factory', $factory)->where('sc_id', session('current_sc_id', 1))->first(); @endphp
                    @if($layout && $layout->image_url)
                        <div class="tv-img-wrap">
                            <img src="{{ $layout->image_url }}" alt="Layout">
                            <div id="tvPinsLayer"></div>
                        </div>
                    @else
                        <div class="fp-empty">📷 Layout belum dikonfigurasi.<br>Gunakan <strong>Floor Plan Manager</strong>.</div>
                    @endif
                </div>
            </div>

            <!-- RIGHT: Skill Matrix -->
            <div class="right-panel">
                <div class="matrix-header">
                    <span>Man Power Skill Map — {{ $factory }}</span>
                    <span id="headerClock">Loading...</span>
                </div>
                
                <div class="summary-stats">
                    <div class="stat-item"><div class="stat-val" id="statTotal">0</div><div class="stat-lbl">Total operator</div></div>
                    <div class="stat-item"><div class="stat-val" id="statMulti">0</div><div class="stat-lbl">Multi-skill ≥75%</div></div>
                    <div class="stat-item"><div class="stat-val" id="statPengembangan">0</div><div class="stat-lbl">Pada pengembangan</div></div>
                    <div class="stat-item"><div class="stat-val" id="statBaru">0</div><div class="stat-lbl">Operator baru (&lt;40%)</div></div>
                </div>

                <div class="matrix-content" id="matrixBox">
                    <div style="display:flex;height:100%;align-items:center;justify-content:center;color:#777;">⏳ Memuat data skill...</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- NAVIGATION DOTS (PPT Style) -->
    <div class="tv-nav-dots">
        <div class="tv-nav-dot active" onclick="manualSlide(0)" title="Slide 1: Dashboard"></div>
        <div class="tv-nav-dot" onclick="manualSlide(1)" title="Slide 2: Floor Plan & Skill"></div>
    </div>

    {{-- ════ TICKER & FULLSCREEN (GLOBAL) ════ --}}
    <div class="tv-ticker" style="z-index: 600;">
        <div class="tv-ticker-lbl">📢 INFO</div>
        <div class="tv-ticker-track">
            <div class="tv-ticker-inner" id="tvTickerInner"></div>
        </div>
    </div>

    <button class="tv-fs" onclick="tvFS()" style="z-index: 600;">⛶ Fullscreen</button>

    <script>
        /* ── Master Slide Rotation (PPT Style) ── */
        let tvStep = 0;
        let slideInterval;
        const SLIDE_MS = 10000; // 10 detik

        function goSlide(n) {
            tvStep = n;
            document.getElementById('tvSlide1').classList.toggle('active', tvStep === 0);
            document.getElementById('tvSlide2').classList.toggle('active', tvStep === 1);
            
            const dots = document.querySelectorAll('.tv-nav-dot');
            dots.forEach((d, i) => d.classList.toggle('active', i === tvStep));
            
            if (tvStep === 1) {
                fetchSlide2Data();
            }
        }

        function startSlideTimer() {
            clearInterval(slideInterval);
            slideInterval = setInterval(() => {
                goSlide((tvStep + 1) % 2);
            }, SLIDE_MS);
        }

        function manualSlide(n) {
            goSlide(n);
            startSlideTimer(); // reset timer on manual click
        }
        
        startSlideTimer();

        /* ── API Data for Slide 2 ── */
        async function fetchSlide2Data() {
            try {
                const f = encodeURIComponent(TV_F);
                const s = encodeURIComponent(TV_S);
                const [resM, resS] = await Promise.all([
                    fetch(`/api/floor-plan-manager/machines?factory=${f}`),
                    fetch(`/api/skills?factory=${f}&shift=${s}`)
                ]);
                const machines = await resM.json();
                const skillData = await resS.json();
                renderPins(machines);
                renderMatrix(skillData.members || [], skillData.skills || {}, skillData.processes || {}, machines);
            } catch(e) { console.error('Error fetching slide 2 data', e); }
        }

        function renderPins(machines) {
            const layer = document.getElementById('tvPinsLayer');
            if (!layer) return;
            layer.innerHTML = machines.filter(m => m.floor_cx !== null && m.floor_cy !== null).map(m => {
                const s = (m.status || '').toLowerCase();
                let cls = 'pin-ok';
                if (s.includes('stop') || s.includes('rusak') || s.includes('problem')) cls = 'pin-problem';
                else if (s.includes('slow') || s.includes('masalah') || s.includes('warn')) cls = 'pin-warn';
                else if (s.includes('off') || s.includes('mati')) cls = 'pin-off';
                return `<div class="tv-pin ${cls}" style="left:${m.floor_cx}%;top:${m.floor_cy}%;"><div class="tv-pin-label">${m.name}</div></div>`;
            }).join('');
        }

        function renderMatrix(members, skills, processes, machinesData) {
            const box = document.getElementById('matrixBox');
            if (!members.length) { box.innerHTML = '<div style="text-align:center;padding:40px;color:#777;">Tidak ada data member aktif.</div>'; return; }

            const machineNames = (machinesData || []).map(m => m.name).sort().slice(0, 12);
            if (!machineNames.length) { box.innerHTML = '<div style="text-align:center;padding:40px;color:#777;">Belum ada data mesin di factory ini.</div>'; return; }

            // ponytail: count ALL members for stats; displayMembers only limits table rows
            const displayMembers = members.slice(0, 20);
            let totalOp = members.length, multiSkill = 0, pengembang = 0, opBaru = 0;

            // Aggregate skill tiers across all members (not just displayed 20)
            members.forEach(m => {
                let avgScore = 0, count = 0;
                machineNames.forEach(mn => {
                    const procs = processes[mn] || [];
                    if (procs.length === 0) {
                        const pct = skills[m.id]?.[mn]?.['-']?.skill_pct ?? null;
                        if (pct !== null) { avgScore += pct; count++; }
                    } else {
                        procs.forEach(p => {
                            const pct = skills[m.id]?.[mn]?.[p]?.skill_pct ?? null;
                            if (pct !== null) { avgScore += pct; count++; }
                        });
                    }
                });
                const finalAvg = count > 0 ? Math.round(avgScore / count) : 0;
                if (finalAvg >= 75) multiSkill++;
                else if (finalAvg >= 40) pengembang++;
                else opBaru++;
            });

            let totalCols = 0;
            machineNames.forEach(m => {
                 totalCols += Math.max(1, (processes[m] || []).length);
            });

            let html = `<table class="m-table"><thead><tr>
                <th class="col-no" rowspan="3">No</th><th class="col-name" rowspan="3">Nama operator</th><th class="col-shift" rowspan="3">Shift</th>
                <th colspan="${totalCols}">Mesin / Proses</th>
                </tr><tr>`;
                
            machineNames.forEach(m => {
                 const procs = processes[m] || [];
                 const colspan = Math.max(1, procs.length);
                 html += `<th colspan="${colspan}">${esc(m)}</th>`;
            });
            html += `</tr><tr>`;
            
            machineNames.forEach(m => {
                 const procs = processes[m] || [];
                 if (procs.length === 0) {
                     html += `<th style="color:#cfdfd4;font-size:9px;">ALL</th>`;
                 } else {
                     procs.forEach(p => {
                         html += `<th style="font-size:9px;font-weight:normal;">${esc(p)}</th>`;
                     });
                 }
            });
            html += `</tr></thead><tbody>`;

            displayMembers.forEach((m, index) => {
                let avgScore = 0, count = 0;
                let rowHtml = `<tr><td class="col-no">${index + 1}.</td><td class="col-name">${esc(m.nama)}</td><td class="col-shift" style="color:var(--brand-primary);font-weight:700;">${m.shift}</td>`;
                
                machineNames.forEach(mn => {
                    const procs = processes[mn] || [];
                    if (procs.length === 0) {
                        const pct = skills[m.id]?.[mn]?.['-']?.skill_pct ?? null;
                        let circleClass = 'skill-0';
                        if (pct !== null) {
                            avgScore += pct; count++;
                            if (pct >= 100) circleClass = 'skill-100'; else if (pct >= 75) circleClass = 'skill-75';
                            else if (pct >= 50) circleClass = 'skill-50'; else if (pct > 0) circleClass = 'skill-25';
                        }
                        rowHtml += `<td><div class="skill-circle ${circleClass}"></div></td>`;
                    } else {
                        procs.forEach(p => {
                            const pct = skills[m.id]?.[mn]?.[p]?.skill_pct ?? null;
                            let circleClass = 'skill-0';
                            if (pct !== null) {
                                avgScore += pct; count++;
                                if (pct >= 100) circleClass = 'skill-100'; else if (pct >= 75) circleClass = 'skill-75';
                                else if (pct >= 50) circleClass = 'skill-50'; else if (pct > 0) circleClass = 'skill-25';
                            }
                            rowHtml += `<td><div class="skill-circle ${circleClass}"></div></td>`;
                        });
                    }
                });

                rowHtml += `</tr>`;
                html += rowHtml;
            });
            html += `</tbody></table>`;
            box.innerHTML = html;
            
            document.getElementById('statTotal').textContent = totalOp;
            document.getElementById('statMulti').textContent = multiSkill;
            document.getElementById('statPengembangan').textContent = pengembang;
            document.getElementById('statBaru').textContent = opBaru;
        }

        function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
        
        setInterval(() => {
            const d = new Date();
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
            const hc = document.getElementById('headerClock');
            if(hc) hc.textContent = `Bulan ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} | PT. Sugity Creatives`;
        }, 1000);
        
    </script>

    <script>
        Chart.register(ChartDataLabels);

        const TV_F = @json($factory);
        const TV_S = @json($shift);
        const TV_T = @json($tanggal);
        const initAb = @json($absenceSummary);

        const LVL_CHIP = ['chip-green', 'chip-yellowgreen', 'chip-yellow', 'chip-red'];
        const LVL_TXT = ['OKE  - Semua Normal', 'LOW RISK  - Absen 1', 'MID RISK  - Absen 2–3 / Ada Problem', 'HIGH RISK  - Absen ≥4 / MC Problem ≥2'];
        const LVL_CLS = ['active-green', 'active-yellowgreen', 'active-yellow', 'active-red'];

        let tvChart = null;
        const $g = id => document.getElementById(id);

        /* ── JAM ── */
        (() => {
            const el = $g('tvClock');
            const p = x => String(x).padStart(2, '0');
            const t = () => { const n = new Date(); if (el) el.textContent = p(n.getHours()) + ':' + p(n.getMinutes()) + ':' + p(n.getSeconds()); };
            t(); setInterval(t, 1000);
        })();

        /* ── CHART (Task 4: includes red Absen indicator) ── */
        function renderChart(d) {
            if (!d) return;
            // Task 4: compute absen count
            const absenCount = Math.max(0, (d.total_member ?? 0) - (d.mp_hadir ?? 0) - (d.op_cuti ?? 0) - (d.op_sakit ?? 0) - (d.op_ijin ?? 0) - (d.spv_cuti ?? 0) - (d.spv_sakit ?? 0) - (d.spv_ijin ?? 0));
            const showAbsen = absenCount > 0;
            const v = [
                d.mp_hadir ?? 0,
                d.spv_sakit ?? 0,
                d.spv_ijin ?? 0,
                d.spv_cuti ?? 0,
                d.op_sakit ?? 0,
                d.op_ijin ?? 0,
                d.op_cuti ?? 0,
                showAbsen ? absenCount : 0,
            ];
            const labels = ['MP', 'PENGAWAS SAKIT', 'PENGAWAS IZIN', 'PENGAWAS CUTI', 'OPERATOR SAKIT', 'OPERATOR IZIN', 'OPERATOR CUTI', 'ABSEN'];
            const pColor = getComputedStyle(document.documentElement).getPropertyValue('--brand-primary').trim() || '#2E7D32';
            const colors = [pColor, '#5dade2', '#FF8F1F', '#1F3C88', '#8e44ad', '#f1c40f', '#ff69b4', '#EF4444'];
            const tot = d.total_member ?? v.reduce((a, b) => a + b, 0);
            if (!tot) return;
            const h = d.mp_hadir ?? 0;
            const pct = ((h / tot) * 100).toFixed(1);
            const cv = $g('centerValue'), af = $g('absenFill');
            if (cv) cv.textContent = `${h}/${tot}`;
            if (af) { af.style.width = pct + '%'; af.textContent = pct + '%'; }
            if (tvChart) tvChart.destroy();
            tvChart = new Chart($g('myChart'), {
                type: 'doughnut',
                data: { labels, datasets: [{ data: v, backgroundColor: colors, borderWidth: 0 }] },
                options: {
                    cutout: '72%', responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, datalabels: { display: false },
                        tooltip: { callbacks: { label: c => { const vv = c.parsed; return vv ? `${c.label}: ${vv} (${((vv / tot) * 100).toFixed(1)}%)` : null; } } }
                    }
                }
            });
        }

        /* ── AUTO-SCROLL MESIN ── */
        const mesinScroll = $g('statusMesinScroll');
        let mesinPaused = false;
        let mesinScrollPos = 0;
        let mesinWrap = mesinScroll ? mesinScroll.querySelector('.machines-wrap') : null;

        if (mesinScroll && mesinWrap) {
            const clone = mesinWrap.cloneNode(true);
            mesinScroll.appendChild(clone);
        }

        function mesinAutoScroll() {
            if (!mesinScroll || !mesinWrap) return;
            if (!mesinPaused) {
                if (mesinScroll.scrollTop >= mesinWrap.offsetHeight) {
                    mesinScroll.scrollTop = 0; mesinScrollPos = 0;
                } else {
                    mesinScrollPos += 0.5;
                    mesinScroll.scrollTop = mesinScrollPos;
                }
            }
            requestAnimationFrame(mesinAutoScroll);
        }

        if (mesinScroll && mesinWrap) {
            mesinScrollPos = mesinScroll.scrollTop;
            requestAnimationFrame(mesinAutoScroll);
            mesinScroll.addEventListener('mouseenter', () => { mesinPaused = true; });
            mesinScroll.addEventListener('mouseleave', () => { setTimeout(() => { mesinScrollPos = mesinScroll.scrollTop; mesinPaused = false; }, 1000); });
            mesinScroll.addEventListener('touchstart', () => { mesinPaused = true; }, { passive: true });
            mesinScroll.addEventListener('touchend', () => { setTimeout(() => { mesinScrollPos = mesinScroll.scrollTop; mesinPaused = false; }, 2000); }, { passive: true });
            mesinScroll.addEventListener('scroll', () => { if (mesinPaused) mesinScrollPos = mesinScroll.scrollTop; }, { passive: true });
        }

        document.addEventListener('keydown', e => {
            if (e.key === ' ') { e.preventDefault(); mesinPaused = !mesinPaused; if (!mesinPaused) mesinScrollPos = mesinScroll.scrollTop; }
            if (e.key === 'f' || e.key === 'F' || e.key === 'F11') { e.preventDefault(); tvFS(); }
        });

        /* ── FULLSCREEN ── */
        function tvFS() {
            if (!document.fullscreenElement)
                (document.documentElement.requestFullscreen || document.documentElement.webkitRequestFullscreen)?.call(document.documentElement);
            else
                (document.exitFullscreen || document.webkitExitFullscreen)?.call(document);
        }

        /* ── SYNC ── */
        let _cd = 30, _cdI = null;
        function startCD() {
            _cd = 30; clearInterval(_cdI);
            _cdI = setInterval(() => {
                _cd--;
                if ($g('arCountdown')) $g('arCountdown').textContent = _cd + 's';
                if (_cd <= 0) { _cd = 30; syncTV(); }
            }, 1000);
        }

        async function syncTV() {
            const dot = $g('arDot'); if (dot) dot.style.opacity = '.5';
            try {
                const qs = `tanggal=${TV_T}&factory=${encodeURIComponent(TV_F)}&shift=${TV_S}`;
                const [r, replR] = await Promise.all([
                    fetch(`/api/status?${qs}`, { headers: { Accept: 'application/json' } }),
                    fetch(`/api/replacements?${qs}`, { headers: { Accept: 'application/json' } })
                ]);
                if (!r.ok) return;
                const d = await r.json();
                const replData = replR.ok ? await replR.json() : [];
                // Build a Map<machine, string[]> so multiple pengganti per machine are all captured
                const replacedMachines = new Map();
                replData.forEach(x => {
                    const key = x.target_machine;
                    const name = x.member_name || 'Pengganti';
                    if (!replacedMachines.has(key)) replacedMachines.set(key, []);
                    if (!replacedMachines.get(key).includes(name)) replacedMachines.get(key).push(name);
                });

                if ($g('rsMpF2')) $g('rsMpF2').textContent = d.total_mp ?? 0;
                if ($g('rsMpF34')) $g('rsMpF34').textContent = d.total_mp ?? 0;
                if ($g('sMan')) $g('sMan').textContent = d.summary?.man ?? 0;
                if ($g('sMachine')) $g('sMachine').textContent = d.summary?.machine ?? 0;
                if ($g('sMaterial')) $g('sMaterial').textContent = d.summary?.material ?? 0;
                if ($g('sMethod')) $g('sMethod').textContent = d.summary?.method ?? 0;

                const lv = d.status_level ?? 0;
                ['ec-green', 'ec-yg', 'ec-yellow', 'ec-red'].forEach((id, i) => {
                    const e = $g(id); if (!e) return;
                    e.classList.remove(...LVL_CLS);
                    if (i === lv) e.classList.add(LVL_CLS[i]);
                });
                const chip = $g('statusChip');
                if (chip) { chip.className = `status-chip ${LVL_CHIP[lv]}`; chip.textContent = LVL_TXT[lv]; }

                if (d.absence) renderChart(d.absence);
                const ts = $g('arLastUpdated'); if (ts) ts.textContent = `Diperbarui: ${d.updated_at}`;
                buildTicker(d); _cd = 30;

                const annBox = $g('tvAnnouncementBox');
                if (annBox && d.announcements) {
                    if (d.announcements.length > 0) {
                        let html = '<div class="tv-announce-badge">ANNOUNCEMENT</div><div class="tv-announce-track"><div class="tv-announce-marquee" id="tvAnnounceMarquee">';
                        d.announcements.forEach(a => html += `<div class="tv-announce-item">${a} •</div>`);
                        html += '</div></div>';
                        annBox.innerHTML = html;
                    } else {
                        annBox.innerHTML = '<div class="tv-announce-badge ok">ALL CLEAR</div><div class="tv-announce-safe">✅ Semua aman, tidak ada problem mesin / absen tercatat.</div>';
                    }
                }

                const activeProblems = d.active_problems || [];
                activeProblems.forEach(p => {
                    if (p.jenis === 'Man' && p.lokasi && replacedMachines.has(p.lokasi)) {
                        const names = replacedMachines.get(p.lokasi); // array of all pengganti names
                        p._has_replacement = true;
                        p.countermeasure = 'Backup: ' + names.join(' + ');
                    }
                });
                syncActiveProblemPanel(activeProblems);

                syncCards();

                // Overdue overlay is now driven by DETAIL PROBLEM (syncActiveProblemPanel)
                // — no separate overdue_problems server call needed.
            } catch (e) { console.error('[TV]', e); }
            finally { if (dot) dot.style.opacity = '1'; }
        }

        /* ── SYNC CARDS ── */
        const colorMap = { man: '#e74c3c', machine: '#1f3c88', material: '#f39c12', method: '#2e7d32' };
        const borderCls = { man: 'mc-status-man', machine: 'mc-status-machine', material: 'mc-status-material', method: 'mc-status-method' };

        function esc(s) { return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
        function initials(n) { if (!n) return '?'; const p = n.trim().split(' '); return p.length >= 2 ? (p[0][0] + p[1][0]).toUpperCase() : n.slice(0, 2).toUpperCase(); }

        let _tvSyncing = false;
        async function syncCards() {
            if (_tvSyncing) return; _tvSyncing = true;
            try {
                const qs = `tanggal=${TV_T}&factory=${encodeURIComponent(TV_F)}&shift=${TV_S}`;
                const [absRes, replRes, logRes] = await Promise.all([
                    fetch(`/api/absence/data?${qs}`, { headers: { Accept: 'application/json' } }),
                    fetch(`/api/replacements?${qs}`, { headers: { Accept: 'application/json' } }),
                    fetch(`/api/logs/list?${qs}&history=3months`, { headers: { Accept: 'application/json' } }),
                ]);
                const absData = absRes.ok ? await absRes.json() : {};
                const replData = replRes.ok ? await replRes.json() : [];
                const logData = logRes.ok ? await logRes.json() : [];

                const absenIds = new Set(Object.entries(absData).filter(([, v]) => v.status === 'absen').map(([id]) => parseInt(id)));
                const activReplIds = new Set(replData.map(r => r.member_id));
                const replacedMachines = new Set(replData.map(r => r.target_machine));
                const openLogMap = {};
                logData.filter(l => l.status === 'open').forEach(l => {
                    if (!openLogMap[l.lokasi]) openLogMap[l.lokasi] = [];
                    const t = (l.jenis || '').toLowerCase();
                    if (t && !openLogMap[l.lokasi].includes(t)) openLogMap[l.lokasi].push(t);
                });

                document.querySelectorAll('.mc-card').forEach(card => {
                    const machine = card.dataset.machine; if (!machine) return;
                    const ownItems = [...card.querySelectorAll('.mc-member-item:not([data-replacement="1"])')];
                    const absenItems = ownItems.filter(el => absenIds.has(parseInt(el.dataset.memberId)));
                    const cardHasAbsen = absenItems.length > 0;
                    const hasReplacement = card.querySelectorAll('.mc-member-item[data-replacement="1"]').length > 0 || replacedMachines.has(machine);
                    const cardNeedsFinder = cardHasAbsen && !hasReplacement;

                    if (!cardHasAbsen) card.querySelectorAll('.mc-member-item[data-replacement="1"]').forEach(el => el.remove());

                    if (cardHasAbsen) {
                        const row = card.querySelector('.mc-members-row');
                        const repl = replData.filter(r => r.target_machine === machine);
                        repl.forEach(r => {
                            if (!row) return;
                            if (row.querySelector(`.mc-member-item[data-member-id="${r.member_id}"][data-replacement="1"]`)) return;
                            row.querySelector('.mc-empty-slot')?.remove();
                            const div = document.createElement('div');
                            div.className = 'mc-member-item'; div.dataset.memberId = r.member_id; div.dataset.replacement = '1';
                            const av = r.member_photo ? `<img src="${esc(r.member_photo)}" alt="${esc(r.member_name)}">` : `<span>${initials(r.member_name)}</span>`;
                            div.innerHTML = `<div class="mc-av av-repl">${av}</div><div class="mc-member-name">${esc(r.member_name || '')}</div><span class="mc-member-tag tag-repl">Backup</span>`;
                            row.appendChild(div);
                        });
                    }

                    ownItems.forEach(item => {
                        const mid = parseInt(item.dataset.memberId); if (!mid) return;
                        const isAbsen = absenIds.has(mid);
                        const isDipinjam = !isAbsen && activReplIds.has(mid);
                        const av = item.querySelector('.mc-av');
                        const img = item.querySelector('.mc-av img');
                        const tag = item.querySelector('.mc-member-tag');
                        if (isAbsen) {
                            item.classList.add('mi-absen'); item.classList.remove('mi-dipinjam');
                            av?.classList.replace('av-ok', 'av-absen');
                            if (img) img.style.filter = 'grayscale(.5) brightness(.8)';
                            if (tag) { tag.className = 'mc-member-tag tag-absen'; const r = (absData[mid]?.reason || '').toLowerCase(); tag.textContent = r.includes('sakit') ? 'SAKIT' : r.includes('izin') || r.includes('ijin') ? 'IZIN' : r.includes('cuti') ? 'CUTI' : 'Absen'; }
                            item.querySelector('.mi-dipinjam-dest')?.remove();
                        } else if (isDipinjam) {
                            const dest = replData.find(r => r.member_id === mid)?.target_machine || '';
                            item.classList.add('mi-dipinjam'); item.classList.remove('mi-absen');
                            av?.classList.replace('av-absen', 'av-ok');
                            if (img) img.style.filter = 'grayscale(.55) brightness(.72)';
                            if (tag) { tag.className = 'mc-member-tag tag-dipinjam'; tag.textContent = 'Backup to'; }
                            let dd = item.querySelector('.mi-dipinjam-dest');
                            if (!dd) { dd = document.createElement('div'); dd.className = 'mi-dipinjam-dest'; item.appendChild(dd); }
                            if (dest) { dd.textContent = `↗ ${dest}`; dd.title = `Bertugas di: ${dest}`; }
                        } else {
                            item.classList.remove('mi-absen', 'mi-dipinjam');
                            av?.classList.replace('av-absen', 'av-ok');
                            if (img) img.style.filter = '';
                            item.querySelector('.mi-dipinjam-dest')?.remove();
                            if (tag && ['Absen', 'Backup to'].includes(tag.textContent)) { tag.className = 'mc-member-tag tag-hadir'; tag.textContent = 'Hadir'; }
                        }
                    });

                    const logTypes = openLogMap[machine] || [];
                    card.classList.remove('mc-has-absen', ...Object.values(borderCls));
                    if (cardNeedsFinder) card.classList.add('mc-has-absen');
                    else if (!cardHasAbsen && logTypes.length && borderCls[logTypes[0]]) card.classList.add(borderCls[logTypes[0]]);

                    const dot = card.querySelector('.mc-dot'); if (!dot) return;
                    if (cardHasAbsen && cardNeedsFinder) { dot.className = 'mc-dot d-absen'; dot.style.background = ''; }
                    else if (cardHasAbsen && !cardNeedsFinder) { dot.className = 'mc-dot'; dot.style.background = ''; }
                    else if (logTypes.length && colorMap[logTypes[0]]) { dot.className = 'mc-dot d-visible'; dot.style.background = colorMap[logTypes[0]]; }
                    else { dot.className = 'mc-dot'; dot.style.background = ''; }

                    const activePillCls = [];
                    if (cardHasAbsen) activePillCls.push('p-man');
                    if (logTypes.includes('machine')) activePillCls.push('p-machine');
                    if (logTypes.includes('material')) activePillCls.push('p-material');
                    if (logTypes.includes('method')) activePillCls.push('p-method');
                    if (!activePillCls.length) activePillCls.push('p-normal');
                    card.querySelectorAll('.mc-status-pill').forEach(p => p.classList.remove('active'));
                    activePillCls.forEach(cls => card.querySelector(`.mc-status-pill.${cls}`)?.classList.add('active'));

                    const section = card.closest('.machine-group-section');
                    if (section) {
                        const badge = section.querySelector('.mg-badge');
                        if (badge) {
                            const allCards = [...section.querySelectorAll('.mc-card')];
                            const warnCount = allCards.filter(c => c.classList.contains('mc-has-absen')).length;
                            if (warnCount > 0) { badge.className = 'mg-badge warn'; badge.textContent = `⚠ ${warnCount} absen`; }
                            else { badge.className = 'mg-badge'; badge.textContent = badge.dataset.default ?? `${allCards.length} mesin`; }
                        }
                    }
                });

            } catch (e) { console.error('[TV syncCards]', e); }
            finally { _tvSyncing = false; }
        }

        /* ── TICKER ── */
        function buildTicker(d) {
            const items = []; const ab = d.absence ?? {}; const s = d.summary ?? {};
            if ((ab.op_cuti ?? 0) > 0) items.push({ dot: '#ff69b4', txt: `Cuti: ${ab.op_cuti}` });
            if ((ab.op_sakit ?? 0) > 0) items.push({ dot: '#8e44ad', txt: `Sakit: ${ab.op_sakit}` });
            if ((ab.op_ijin ?? 0) > 0) items.push({ dot: '#f1c40f', txt: `Izin: ${ab.op_ijin}` });
            if ((s.machine ?? 0) > 0) items.push({ dot: '#1f3c88', txt: `Machine Problem: ${s.machine}` });
            if ((s.material ?? 0) > 0) items.push({ dot: '#f39c12', txt: `Material Problem: ${s.material}` });
            if ((s.method ?? 0) > 0) items.push({ dot: '#2e7d32', txt: `Method Problem: ${s.method}` });
            if ((d.open_logs ?? 0) > 0) items.push({ dot: '#5dade2', txt: `Log Open: ${d.open_logs}` });
            if (!items.length) items.push({ dot: '#4caf50', txt: '✅ Semua kondisi NORMAL  - Tidak ada masalah hari ini' });

            const sep = `<span style="color:rgba(255,255,255,.3);margin:0 16px">◆</span>`;
            const html = items.map(i => `<span><span class="tick-dot" style="background:${i.dot}"></span>${i.txt}</span>${sep}`).join('');
            const el = $g('tvTickerInner'); if (!el) return;
            el.innerHTML = html + html;
            el.style.animationDuration = Math.max(20, (el.scrollWidth / 2) / 70) + 's';
        }

        /* ── PROBLEM PANEL HTML RENDERER ── */
        const renderProbRows = (tbody, dataset, emptyMsg) => {
            if (!tbody) return;
            const isHistory = tbody.id === 'tvProbTbodyHistory';
            const colSpan = isHistory ? 11 : 10;
            if (dataset.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="tv-prob-empty">${emptyMsg}</td></tr>`;
                return;
            }

            const kMap = { 'Machine': 'machine', 'Material': 'material', 'Method': 'method', 'Man': 'man' };
            const html = dataset.map((r, rowIdx) => {
                const rowClass = r.status === 'open' ? 'tv-prob-row-open' : 'tv-prob-row-closed';
                const jk = kMap[r.jenis] || 'machine';
                const jpill = `<span class="tv-jenis-pill tv-jenis-${jk}">${esc(r.jenis).toUpperCase()}</span>`;
                const statClass = r.status === 'open' ? 'tv-prob-status-open' : 'tv-prob-status-closed';
                const sm = r.waktu_mulai ? r.waktu_mulai.substring(0, 5) : '-';
                const ss = r.waktu_selesai ? r.waktu_selesai.substring(0, 5) : '-';
                const sd = r.durasi || '-';
                const cau = r.cause ? `<br><small style="color:#666">Sebab: ${esc(r.cause)}</small>` : '';
                const srcMark = r._source === 'absen' ? '<span style="color:#e74c3c;font-weight:900" title="Dari Absensi Member">🧍</span> ' : '';
                const rDesc = `<b>${srcMark}${esc(r.deskripsi)}</b>` + cau;
                let rCount = esc(r.countermeasure || '');
                if (!rCount && r.status === 'open') rCount = '<i style="color:#aaa">Belum ada CM</i>';

                let statText = r.status === 'open' ? 'OPEN' : 'CLOSE';
                if (r.status === 'open') {
                    if (r.jenis === 'Man') {
                        statText = r._has_replacement ? 'PROGRES (BACKUP)' : 'NO BACKUP';
                    } else {
                        statText = 'ON GOING';
                    }
                } else {
                    statText = 'CLOSE';
                }

                let fmtTanggal = '-';
                if (r.tanggal) {
                    try {
                        const d = new Date(r.tanggal);
                        if (!isNaN(d.getTime())) {
                            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            fmtTanggal = `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
                        } else { fmtTanggal = r.tanggal; }
                    } catch (e) { fmtTanggal = r.tanggal; }
                }

                const tglCell = isHistory ? `<td style="font-weight:700;white-space:nowrap;">${esc(fmtTanggal)}</td>` : '';

                return `
                <tr class="${rowClass}">
                    <td style="color:#888;font-weight:800;font-size:10px;text-align:center;">${rowIdx + 1}</td>
                    ${tglCell}
                    <td>${jpill}</td>
                    <td style="font-weight:800;color:#222">${esc(r.lokasi)}</td>
                    <td>${rDesc}</td>
                    <td style="font-size:11px">${rCount}</td>
                    <td style="font-size:11px;font-weight:700">${esc(r.pic)}</td>
                    <td style="font-weight:800;text-align:center;">${sm}</td>
                    <td style="font-weight:800;text-align:center;">${ss}</td>
                    <td style="font-weight:800;color:#e67e22;text-align:center;">${sd}</td>
                    <td class="${statClass}" style="white-space:nowrap;font-size:10px;text-align:center;">${statText}</td>
                </tr>`;
            }).join('');

            tbody.innerHTML = html;
        };

        /* ── ACTIVE PROBLEM PANEL (DETAIL PROBLEM) ──
         *  — also drives the "MESIN BERMASALAH > 4 JAM" overlay.
         *  The overlay ONLY fires when:
         *    • _source === 'log'  (real MC/MM/MT log entry, NOT Man/absen)
         *    • jenis is Machine, Material, or Method
         *    • opened_at is present and elapsed >= 4 hours
         *  Factory+shift scope is already correct because active_problems
         *  comes from statusApi() which is scoped to TV_F / TV_S. */
        function syncActiveProblemPanel(activeProblems) {
            const tbodyActive = $g('tvProbTbodyActive');
            const badgeA = $g('tvProbBadgeActive');

            if (badgeA) {
                badgeA.textContent = activeProblems.length;
                badgeA.classList.toggle('blink', activeProblems.length > 0);
            }
            renderProbRows(tbodyActive, activeProblems, '✅ Tidak ada problem aktif  - Semua kondisi normal');

            // ── Overdue watcher: MC/MM/MT problems open > 4h ──
            const FOUR_H_MS = 4 * 60 * 60 * 1000;
            const now = Date.now();
            const overdueJenis = new Set(['machine', 'material', 'method']);

            const overdueProblems = activeProblems
                .filter(p => {
                    // Must be a real log entry (not a Man/absen synthetic entry)
                    if ((p._source || '') !== 'log') return false;
                    if (!overdueJenis.has((p.jenis || '').toLowerCase())) return false;
                    if (!p.opened_at) return false;
                    const openedMs = new Date(p.opened_at).getTime();
                    if (isNaN(openedMs)) return false;
                    return (now - openedMs) >= FOUR_H_MS;
                })
                .map(p => {
                    const elapsed = now - new Date(p.opened_at).getTime();
                    const totalMins = Math.floor(elapsed / 60000);
                    return {
                        ...p,
                        duration_hours: Math.floor(totalMins / 60),
                        duration_minutes: totalMins % 60,
                    };
                });

            updateOverdueOverlay(overdueProblems);
        }

        /* ── HISTORY PROBLEM PANEL ── */
        async function fetchAndSyncHistoryPanel() {
            try {
                const qs = `tanggal=${TV_T}&factory=${encodeURIComponent(TV_F)}&shift=${TV_S}`;
                const res = await fetch(`/api/logs/combined?${qs}&history=3months`, { headers: { Accept: 'application/json' } });
                if (res.ok) {
                    const rows = await res.json();
                    const closedRows = rows.filter(r => r.status === 'closed');
                    const tbodyHistory = $g('tvProbTbodyHistory');
                    const badgeH = $g('tvProbBadgeHistory');
                    if (badgeH) badgeH.textContent = closedRows.length;

                    const syncLbl = $g('tvProbLastSync');
                    if (syncLbl) {
                        const now = new Date();
                        const pad = x => String(x).padStart(2, '0');
                        syncLbl.textContent = `Update: ${pad(now.getHours())}:${pad(now.getMinutes())}`;
                    }

                    renderProbRows(tbodyHistory, closedRows, ' - Belum ada history problem bulan ini  -');
                }
            } catch (e) { console.warn('[TV hist]', e); }
        }

        /* ── PROBLEMS SECTION ── */
        function syncProblemsSection() {
            const grid = $g('tvProblemsGrid');
            const wrap = $g('tvProblemsWrap');
            const badge = $g('tvProblemsBadge');
            const allOk = $g('tvProbAllOk');
            if (!grid) return; // not rendered in detail mode

            // Remove previous clones
            grid.querySelectorAll('.mc-card-clone').forEach(el => el.remove());

            // A card is a problem if it has any active problem class
            const problemCards = [...document.querySelectorAll('#tvMachineCardsCol .mc-card')].filter(c =>
                c.classList.contains('mc-has-absen') ||
                c.classList.contains('mc-status-machine') ||
                c.classList.contains('mc-status-material') ||
                c.classList.contains('mc-status-method')
            );

            if (badge) badge.textContent = problemCards.length;
            if (wrap) wrap.classList.toggle('has-prob', problemCards.length > 0);

            if (problemCards.length === 0) {
                if (allOk) allOk.style.display = 'flex';
                return;
            }
            if (allOk) allOk.style.display = 'none';

            problemCards.forEach(card => {
                const clone = card.cloneNode(true);
                clone.classList.add('mc-card-clone');
                clone.dataset.cloneOf = card.dataset.machine || '';
                // remove listeners, clone is display-only
                grid.appendChild(clone);
            });
        }

        function tvOpenDetail() {
            const qs = `factory=${encodeURIComponent(TV_F)}&shift=${encodeURIComponent(TV_S)}&tanggal=${TV_T}&mode=detail`;
            window.open(`/admin/tv?${qs}`, '_blank');
        }

        /* ── Overdue MC Overlay watcher (>4h) ──
         *  Receives pre-filtered overdue list from syncActiveProblemPanel.
         *  duration_hours / duration_minutes are already computed client-side. */
        function updateOverdueOverlay(problems) {
            const overlay = $g('tvOverdueOverlay');
            if (!overlay) return;

            if (!problems || problems.length === 0) {
                // Only hide if user has not manually dismissed; once dismissed we
                // respect that until the next polling cycle clears all problems.
                if (!overlay.dataset.userDismissed) {
                    overlay.classList.remove('visible');
                }
                return;
            }

            // New overdue list — reset dismiss flag and show
            overlay.dataset.userDismissed = '';

            const cards = problems.map(p => `
                <div class="ov-card">
                    <div class="ov-card-header">
                        <span class="ov-jenis">${esc(p.jenis || 'MC')}</span>
                        <span class="ov-machine">${esc(p.lokasi)}</span>
                        <span class="ov-duration">⏱ ${p.duration_hours}j ${p.duration_minutes}m</span>
                    </div>
                    <div class="ov-desc">${esc(p.deskripsi)}</div>
                </div>
            `).join('');
            $g('tvOverdueCards').innerHTML = cards;
            overlay.classList.add('visible');
        }

        /* ── INIT ── */
        document.addEventListener('DOMContentLoaded', () => {
            if (initAb) renderChart(initAb);
            buildTicker({ absence: initAb, summary: @json($machineSummary), open_logs: {{ $openLogsCount }}, status_level: {{ $statusLevel }} });
            syncTV(); startCD();
            // syncCards already calls syncProblemsSection inside, but wait for it
            syncCards().then(() => syncProblemsSection()).catch(() => { });
            // History panel independent refresh every 60s
            fetchAndSyncHistoryPanel();
            setInterval(fetchAndSyncHistoryPanel, 60000);
            // Also re-sync problems section every 30s in case syncCards misses
            setInterval(syncProblemsSection, 30000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    syncTV(); syncCards(); fetchAndSyncHistoryPanel();
                    setTimeout(syncProblemsSection, 1500); // after syncCards settles
                }
            });
        });
    </script>

    <style>
        /* ── Task 7: Overdue MC Overlay Styles ── */
        #tvOverdueOverlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 10, 10, 0.82);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
            padding: 24px;
            backdrop-filter: blur(4px);
            animation: ovFadeIn .4s ease;
        }

        #tvOverdueOverlay.visible {
            display: flex;
        }

        @keyframes ovFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .ov-header {
            text-align: center;
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .ov-title {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #EF4444;
            text-shadow: 0 0 20px rgba(239, 68, 68, .6);
            animation: ovPulse 1.8s ease-in-out infinite;
        }

        @keyframes ovPulse {

            0%,
            100% {
                opacity: 1;
                text-shadow: 0 0 20px rgba(239, 68, 68, .6);
            }

            50% {
                opacity: .75;
                text-shadow: 0 0 40px rgba(239, 68, 68, .9);
            }
        }

        .ov-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, .7);
            margin-top: 4px;
            letter-spacing: .5px;
        }

        .ov-cards-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            max-width: 1000px;
            width: 100%;
        }

        .ov-card {
            background: linear-gradient(135deg, #1a0a0a, #2d1010);
            border: 2px solid #EF4444;
            border-radius: 14px;
            padding: 14px 18px;
            min-width: 240px;
            max-width: 320px;
            box-shadow: 0 4px 20px rgba(239, 68, 68, .3);
            animation: ovCardBlink 2s ease-in-out infinite;
        }

        @keyframes ovCardBlink {

            0%,
            100% {
                border-color: #EF4444;
                box-shadow: 0 4px 20px rgba(239, 68, 68, .3);
            }

            50% {
                border-color: #f87171;
                box-shadow: 0 4px 30px rgba(239, 68, 68, .6);
            }
        }

        .ov-card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .ov-jenis {
            background: #EF4444;
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .ov-machine {
            font-size: 14px;
            font-weight: 900;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .5px;
            flex: 1;
        }

        .ov-duration {
            font-size: 11px;
            font-weight: 800;
            color: #f87171;
            white-space: nowrap;
        }

        .ov-desc {
            font-size: 12px;
            color: rgba(255, 255, 255, .8);
            font-family: 'Roboto Condensed', sans-serif;
            line-height: 1.4;
        }

        .ov-dismiss {
            margin-top: 8px;
            background: rgba(255, 255, 255, .1);
            border: 1.5px solid rgba(255, 255, 255, .3);
            color: rgba(255, 255, 255, .8);
            padding: 8px 24px;
            border-radius: 20px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
        }

        .ov-dismiss:hover {
            background: rgba(255, 255, 255, .2);
            color: #fff;
        }
    </style>

    {{-- Task 7: Overlay HTML (hidden until overdue problems detected) --}}
    <div id="tvOverdueOverlay" role="alertdialog" aria-modal="true" aria-label="Peringatan Mesin Bermasalah">
        <div class="ov-header">
            <div class="ov-title">⚠ MESIN BERMASALAH &gt; 4 JAM</div>
            <div class="ov-sub">Problem mesin berikut belum diselesaikan lebih dari 4 jam</div>
        </div>
        <div class="ov-cards-wrap" id="tvOverdueCards"></div>
        <button class="ov-dismiss" id="tvOverdueDismissBtn" onclick="
            const ov = document.getElementById('tvOverdueOverlay');
            ov.classList.remove('visible');
            ov.dataset.userDismissed = '1'; // prevent re-show until next poll cycle resolves
        ">✕ Tutup (akan muncul lagi pada refresh berikutnya jika masih terbuka)</button>
    </div>

</body>

</html>