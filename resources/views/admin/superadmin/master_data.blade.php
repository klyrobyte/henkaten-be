@extends('layouts.admin')
@section('title', 'Master Data Management')

@push('styles')
    <style>
        /*   Tier-1 Modern Variables & Styling               */
        :root {
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
            --text-dark: #1a1f36;
            --text-gray: #4f566b;
            --text-light: #8792a1;
            --bg-hover: rgba(var(--brand-primary-rgb), 0.03);
            --border-radius-premium: 16px;
        }

        /* Smooth scrollbars for scrollable tables */
        .md-table-wrapper::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .md-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .md-table-wrapper::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .md-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        /*   Container Layout   */
        .premium-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 12px;
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /*   Glass Cards & Overlays   */
        .premium-card {
            background: #fff;
            border-radius: var(--border-radius-premium);
            border: 1px solid #f0f0f5;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .premium-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            border-color: rgba(var(--brand-primary-rgb), 0.15);
        }

        .premium-section-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .premium-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, #eaeaea, transparent);
        }

        /*   4M Summary Grid   */
        .m4-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }
        .m4-box {
            position: relative;
            text-align: left;
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #f0f0f5;
            background: #fafafa;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .m4-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }
        .m4-box .m4-val {
            font-family: 'Rajdhani', sans-serif;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 4px;
        }
        .m4-box .m4-lbl {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Color variations with subtle glass gradient effects */
        .m4-man {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.03), rgba(231, 76, 60, 0.06));
            border-color: rgba(231, 76, 60, 0.15);
        }
        .m4-man .m4-val { color: #e74c3c; text-shadow: 0 0 15px rgba(231, 76, 60, 0.1); }

        .m4-machine {
            background: linear-gradient(135deg, rgba(31, 60, 136, 0.03), rgba(31, 60, 136, 0.06));
            border-color: rgba(31, 60, 136, 0.15);
        }
        .m4-machine .m4-val { color: #1f3c88; text-shadow: 0 0 15px rgba(31, 60, 136, 0.1); }

        .m4-material {
            background: linear-gradient(135deg, rgba(243, 156, 18, 0.03), rgba(243, 156, 18, 0.06));
            border-color: rgba(243, 156, 18, 0.15);
        }
        .m4-material .m4-val { color: #f39c12; text-shadow: 0 0 15px rgba(243, 156, 18, 0.1); }

        .m4-method {
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.03), rgba(46, 125, 50, 0.06));
            border-color: rgba(46, 125, 50, 0.15);
        }
        .m4-method .m4-val { color: #2e7d32; text-shadow: 0 0 15px rgba(46, 125, 50, 0.1); }

        .m4-repl {
            background: linear-gradient(135deg, rgba(142, 68, 173, 0.03), rgba(142, 68, 173, 0.06));
            border-color: rgba(142, 68, 173, 0.15);
        }
        .m4-repl .m4-val { color: #8e44ad; text-shadow: 0 0 15px rgba(142, 68, 173, 0.1); }

        /*   Date Mode Filtering Bar   */
        .date-mode-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #fff;
            border-radius: 12px;
            padding: 12px 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .dmb-label-group {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .dmb-divider {
            width: 1px;
            height: 24px;
            background: #e5e7eb;
        }
        .dmb-mode-group {
            display: flex;
            align-items: center;
            gap: 2px;
            background: #f3f4f6;
            border-radius: 8px;
            padding: 2px;
        }
        .dmb-btn {
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            background: transparent;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-gray);
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .dmb-btn.active {
            background: #fff;
            color:  #fff !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dmb-btn:hover:not(.active) {
            color: var(--text-dark);
        }

        .dmb-inputs {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dmb-input {
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            background: #fff;
            cursor: pointer;
            outline: none;
            transition: all 0.15s ease;
            height: 32px;
            box-sizing: border-box;
        }
        .dmb-input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 2px rgba(var(--brand-primary-rgb), 0.1);
        }
        .dmb-apply {
            padding: 6px 16px;
            border-radius: 8px;
            border: none;
            background: var(--warna-header);
            color: #fff !important;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.15s ease;
            height: 32px;
        }
        .dmb-apply:hover {
            filter: brightness(1.05);
        }
        .dmb-context-section {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /*   Delete All Button Style   */
        .btn-delete-all {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #fee2e2;
            background: #fef2f2;
            color: #ef4444;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            height: 36px;
            box-sizing: border-box;
        }
        .btn-delete-all:hover {
            background: #ef4444;
            color: #fff !important;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
            transform: translateY(-1px);
        }

        /*   Elegant Modern Tabs   */
        .md-tabs {
            display: flex;
            gap: 6px;
            background: #f3f3f6;
            padding: 4px;
            border-radius: 14px;
            width: fit-content;
        }
        .md-tab {
            padding: 10px 24px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-gray);
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .md-tab:hover:not(.active) {
            color: var(--text-dark);
            background: rgba(255, 255, 255, 0.4);
        }
        .md-tab.active {
            background: #fff;
            color: var(--brand-primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        /*   Premium High-Tech Table   */
        .md-table-card {
            background: #fff;
            border-radius: var(--border-radius-premium);
            border: 1px solid #f0f0f5;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            overflow: hidden;
            transition: border-color 0.3s;
        }
        .md-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }
        .md-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .md-table th {
            background: #fafafa;
            padding: 16px 20px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid #f0f0f5;
        }
        .md-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f8f8fa;
            font-size: 13.5px;
            color: var(--text-dark);
            vertical-align: middle;
            transition: background-color 0.15s ease;
        }
        .md-table tr {
            transition: transform 0.2s ease;
        }
        .md-table tr:hover td {
            background-color: var(--bg-hover);
        }

        /* Glowing Custom Badges */
        .md-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 30px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .md-act-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid #eef0f6;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-gray);
        }
        .md-act-btn:hover {
            border-color: #d1d5db;
            background: #fafbfc;
            color: var(--brand-primary);
            transform: translateY(-1px);
        }
        .md-act-btn.del:hover {
            border-color: #fca5a5;
            background: #fef2f2;
            color: #ef4444;
        }

        .pagination-container {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #f0f0f5;
            flex-wrap: wrap;
            gap: 12px;
        }
        .per-page-select {
            padding: 6px 12px;
            border-radius: 20px;
            border: 1.5px solid #eaeaea;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-gray);
            outline: none;
            background: #fff;
            cursor: pointer;
        }

        /*   Premium Modern Modals   */
        .modal-overlay {
            backdrop-filter: blur(8px) saturate(180%);
            -webkit-backdrop-filter: blur(8px) saturate(180%);
            background-color: rgba(26, 31, 54, 0.3);
        }
        .modal-sheet {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .modal-sheet {
            transform: scale(1);
            opacity: 1;
        }
        .modal-body-scroll {
            padding: 24px;
            max-height: calc(85vh - 120px);
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1.5px solid #eaeaea;
            font-size: 14px;
            color: var(--text-dark);
            background: #fcfcfd;
            box-sizing: border-box;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--brand-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(var(--brand-primary-rgb), 0.1);
        }
        .form-control[readonly] {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #f3f3f6;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #fafafa;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }
        .btn-cancel {
            padding: 10px 24px;
            border-radius: 30px;
            border: 1px solid #eaeaea;
            background: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            color: var(--text-gray);
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background: #fafafa;
            color: var(--text-dark);
        }
        .btn-save {
            padding: 10px 28px;
            border-radius: 30px;
            border: none;
            background: var(--warna-header);
            color: #fff !important;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(var(--brand-primary-rgb), 0.2);
            transition: all 0.2s;
        }
        .btn-save:hover {
            box-shadow: 0 6px 16px rgba(var(--brand-primary-rgb), 0.3);
            filter: brightness(1.05);
        }

        /*   Responsive Styling   */
        @media (max-width: 1024px) {
            .m4-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .premium-container {
                padding: 8px;
            }
            .m4-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .date-mode-bar {
                border-radius: 20px;
                padding: 16px;
            }
            .date-mode-bar > div {
                width: 100%;
            }
            .date-mode-bar > div:last-child {
                justify-content: flex-start;
                margin-left: 0 !important;
                margin-top: 10px;
            }
            .dmb-inputs {
                width: 100%;
                flex-wrap: wrap;
            }
            .dmb-input {
                flex: 1;
                min-width: 110px;
            }
            .md-tabs {
                width: 100%;
            }
            .md-tab {
                flex: 1;
                text-align: center;
                padding: 10px 12px;
            }
        }
        @media (max-width: 480px) {
            .m4-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="premium-container">

        {{-- ══ DATE & SHIFT SELECTORS  =>═══════════════ --}}
        <div class="date-mode-bar">
            <div class="dmb-label-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                <span>Filter</span>
            </div>
            <div class="dmb-divider"></div>

            <div class="dmb-mode-group">
                <button class="dmb-btn {{ $mode === 'hari' ? 'active' : '' }}" onclick="setDateMode('hari')">Hari</button>
                <button class="dmb-btn {{ $mode === 'bulan' ? 'active' : '' }}" onclick="setDateMode('bulan')">Bulan</button>
                <button class="dmb-btn {{ $mode === 'rentang' ? 'active' : '' }}" onclick="setDateMode('rentang')">Rentang</button>
            </div>

            <div class="dmb-inputs" id="dmb-hari" style="{{ $mode === 'hari' ? '' : 'display:none' }}">
                <input type="date" id="inputHari" value="{{ $tanggal }}" class="dmb-input" onchange="applyHari()">
            </div>

            <div class="dmb-inputs" id="dmb-bulan" style="{{ $mode === 'bulan' ? '' : 'display:none' }}">
                @php
                    $selYear = explode('-', $bulan)[0];
                    $selMonth = explode('-', $bulan)[1];
                    $monthsMap = ['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'];
                @endphp
                <select id="inputBulanBulan" class="dmb-input" onchange="applyBulan()">
                    @foreach($monthsMap as $num => $name)
                        <option value="{{ $num }}" {{ $selMonth === $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select id="inputBulanTahun" class="dmb-input" onchange="applyBulan()">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $selYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="dmb-inputs" id="dmb-rentang" style="{{ $mode === 'rentang' ? '' : 'display:none' }}">
                <input type="date" id="inputDari" value="{{ $dari }}" class="dmb-input">
                <span style="color:#9ca3af; font-weight: 700; font-size: 12px;">to</span>
                <input type="date" id="inputSampai" value="{{ $sampai }}" class="dmb-input">
                <button class="dmb-apply" onclick="applyRentang()">Apply</button>
            </div>

            <div class="dmb-context-section">
                <select onchange="switchFactory(this.value)" class="dmb-input" style="font-weight:700;">
                    @foreach($factories as $f)
                        <option value="{{ $f->name }}" {{ $factory === $f->name ? 'selected' : '' }}>{{ strtoupper($f->name) }}</option>
                    @endforeach
                </select>
                <div class="dmb-mode-group">
                    <button class="dmb-btn {{ $shift === 'A' ? 'active' : '' }}" onclick="switchShift('A')">A</button>
                    <button class="dmb-btn {{ $shift === 'B' ? 'active' : '' }}" onclick="switchShift('B')">B</button>
                </div>
            </div>
        </div>

        {{-- ══ 4M SUMMARY =>══════════ --}}
        <div class="premium-card">
            <div class="premium-section-title">📊 4M Summary Status</div>
            <div class="m4-grid">
                <div class="m4-box m4-man">
                    <div class="m4-val">{{ $manCount }}</div>
                    <div class="m4-lbl">👤 Man (Absen)</div>
                </div>
                @foreach($jenisList as $jenis)
                    @php 
                                        $p = strtolower($jenis);
                        $cls = in_array($p, ['machine', 'material', 'method']) ? 'm4-' . $p : '';
                    @endphp
                    <div class="m4-box {{ $cls }}" style="{{ !$cls ? 'background:#fafafa; border-color:#f0f0f5' : '' }}">
                        <div class="m4-val">{{ $summaryLogs->where('jenis', $jenis)->count() }}</div>
                        <div class="m4-lbl">
                            @if($p === 'machine') ⚙️ @elseif($p === 'material') 📦 @else 📝 @endif
                            {{ strtoupper($jenis) }}
                        </div>
                    </div>
                @endforeach
                <div class="m4-box m4-repl">
                    <div class="m4-val">{{ $replacementsCount }}</div>
                    <div class="m4-lbl">🔄 Replacements</div>
                </div>
            </div>
        </div>

        {{-- ══ HISTORY TABLE =>════════ --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
            <div class="md-tabs" style="overflow-x: auto; max-width: 100%; white-space: nowrap;">
                <a href="{{ request()->fullUrlWithQuery(['tab' => '3m']) }}" class="md-tab {{ $tab === '3m' ? 'active' : '' }}">3M Logs</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'absence']) }}" class="md-tab {{ $tab === 'absence' ? 'active' : '' }}">Absence</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'abs-sum']) }}" class="md-tab {{ $tab === 'abs-sum' ? 'active' : '' }}">Summary</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'abs-reason']) }}" class="md-tab {{ $tab === 'abs-reason' ? 'active' : '' }}">Reasons</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'replacements']) }}" class="md-tab {{ $tab === 'replacements' ? 'active' : '' }}">Replacements</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'assignments']) }}" class="md-tab {{ $tab === 'assignments' ? 'active' : '' }}">Assignments</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'mc-status']) }}" class="md-tab {{ $tab === 'mc-status' ? 'active' : '' }}">MC Status</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'global-logs']) }}" class="md-tab {{ $tab === 'global-logs' ? 'active' : '' }}">Global Logs</a>
            </div>
            <button class="btn-delete-all" onclick="confirmDeleteAll()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Delete All {{ strtoupper($tab) }}
            </button>
        </div>

        <div class="md-table-card">
            <div class="md-table-wrapper">
                <table class="md-table">
                    <thead>
                        @if($tab === '3m')
                            <tr>
                                <th>Date / Duration</th>
                                <th>Type</th>
                                <th>Location / Machine</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        @elseif($tab === 'absence')
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>NIK / Position</th>
                                <th>Reason</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        @elseif($tab === 'abs-sum')
                            <tr>
                                <th>Date</th>
                                <th>MP Present</th>
                                <th>Absences</th>
                                <th>Breakdown (OP)</th>
                                <th>Breakdown (SPV)</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        @elseif($tab === 'abs-reason')
                            <tr>
                                <th>Name</th>
                                <th>Color</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        @elseif($tab === 'replacements')
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Source</th>
                                <th>Target</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        @elseif($tab === 'assignments')
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Group / Machine</th>
                                <th>Status</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        @elseif($tab === 'mc-status')
                            <tr>
                                <th>Date</th>
                                <th>Machine</th>
                                <th>Status</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        @elseif($tab === 'global-logs')
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Target</th>
                                <th>Details</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                            <tr id="row-{{ $item->id }}">
                            @if($tab === '3m')
                                <td style="white-space:nowrap">
                                    <strong style="color:var(--text-dark)">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong><br>
                                    <span style="font-size:11px; color:var(--text-light)">
                                        ⏱️ {{ substr($item->waktu_mulai, 0, 5) }} – {{ $item->waktu_selesai ? substr($item->waktu_selesai, 0, 5) : '…' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $p = strtolower($item->jenis);
                                        $color = match ($p) { 'machine' => '#1f3c88', 'material' => '#f39c12', 'method' => '#2e7d32', default => '#aaa'};
                                    @endphp
                                    <span class="md-badge" style="background:{{ $color }}">{{ $item->jenis }}</span>
                                </td>
                                <td><strong style="color:var(--text-dark)">{{ $item->lokasi }}</strong></td>
                                <td>
                                    <div style="max-width:320px; font-size:12.5px; line-height:1.4; color:var(--text-gray)">
                                        {{ \Illuminate\Support\Str::limit($item->deskripsi, 90) }}
                                        @if($item->pic) <br><span style="color:var(--text-light); font-size:11px">👤 PIC: {{ $item->pic }}</span> @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusColor = $item->status === 'open' ? '#ef4444' : '#10b981';
                                    @endphp
                                    <span class="md-badge" style="background:{{ $statusColor }}; box-shadow: 0 2px 6px {{ $statusColor }}30">
                                        {{ strtoupper($item->status) }}
                                    </span>
                                </td>
                            @elseif($tab === 'absence')
                                <td style="white-space:nowrap"><strong style="color:var(--text-dark)">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong></td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px">
                                        <div style="width:32px; height:32px; border-radius:50%; background:#f3f3f6; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:10.5px; color:var(--text-gray); border:1px solid #e5e7eb">
                                            {{ strtoupper(substr($item->member?->nama ?? '?', 0, 2)) }}
                                        </div>
                                        <strong style="color:var(--text-dark)">{{ $item->member?->nama ?? 'Unknown' }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span style="color:var(--text-gray); font-weight:600">{{ $item->member?->nik ?? '-' }}</span><br>
                                    <span style="color:var(--text-light); font-size:11px">{{ $item->member?->jabatan ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="md-badge" style="background:#ef4444; box-shadow:0 2px 6px rgba(239, 68, 68, 0.2)">
                                        ⚠️ {{ $item->reason }}
                                    </span>
                                </td>
                            @elseif($tab === 'abs-sum')
                                <td><strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong></td>
                                <td><span style="font-weight:700; color:#10b981">{{ $item->mp_hadir }}</span> / {{ $item->total_member }}</td>
                                <td><span style="font-weight:700; color:#ef4444">{{ $item->total_absen }}</span></td>
                                <td style="font-size:11px; color:var(--text-gray)">
                                    C:{{ $item->op_cuti }} S:{{ $item->op_sakit }} I:{{ $item->op_ijin }} A:{{ $item->op_Alpha }}
                                </td>
                                <td style="font-size:11px; color:var(--text-gray)">
                                    C:{{ $item->spv_cuti }} S:{{ $item->spv_sakit }} I:{{ $item->spv_ijin }} A:{{ $item->spv_Alpha }}
                                </td>
                            @elseif($tab === 'abs-reason')
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px">
                                        <div style="width:16px; height:16px; border-radius:4px; background:{{ $item->color }}"></div>
                                        <code>{{ $item->color }}</code>
                                    </div>
                                </td>
                            @elseif($tab === 'replacements')
                                <td><strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong></td>
                                <td><strong>{{ $item->member?->nama ?? '?' }}</strong></td>
                                <td><span class="md-badge" style="background:#1f3c88">{{ $item->source_machine }}</span></td>
                                <td><span class="md-badge" style="background:#2e7d32">➜ {{ $item->target_machine }}</span></td>
                            @elseif($tab === 'assignments')
                                <td><strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong></td>
                                <td><strong>{{ $item->member_name ?? $item->member?->nama }}</strong></td>
                                <td>
                                    <span style="font-size:11px; color:var(--text-light)">{{ $item->group_title }}</span><br>
                                    <strong style="color:var(--text-dark)">{{ $item->machine_name }}</strong>
                                </td>
                                <td>
                                    @php $sColor = $item->status === 'absen' ? '#ef4444' : '#10b981'; @endphp
                                    <span class="md-badge" style="background:{{ $sColor }}">{{ strtoupper($item->status) }}</span>
                                    @if($item->is_substitute) <br><span style="font-size:10px; color:#8e44ad">REPLACEMENT</span> @endif
                                </td>
                            @elseif($tab === 'mc-status')
                                <td><strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong></td>
                                <td><strong>{{ $item->machine_name }}</strong></td>
                                <td>
                                    @php $sColor = $item->status === 'normal' ? '#10b981' : '#ef4444'; @endphp
                                    <span class="md-badge" style="background:{{ $sColor }}">{{ strtoupper($item->status) }}</span>
                                </td>
                            @elseif($tab === 'global-logs')
                                <td style="white-space:nowrap; font-size:12px">{{ $item->created_at->format('d/m/y H:i') }}</td>
                                <td>
                                    <strong style="color:var(--text-dark)">{{ $item->username_dec }}</strong><br>
                                    <span style="font-size:10px; color:var(--text-light)">{{ $item->role }} • {{ $item->ip_dec }}</span>
                                </td>
                                <td><span style="font-weight:800; color:var(--brand-primary)">{{ $item->action }}</span></td>
                                <td><code style="font-size:11px">{{ $item->target }}</code></td>
                                <td><div style="font-size:11px; color:var(--text-gray); max-width:200px; overflow:hidden; text-overflow:ellipsis">{{ $item->detail }}</div></td>
                            @endif

                            @if($tab !== 'global-logs')
                                <td style="text-align:right; white-space:nowrap">
                                    <button class="md-act-btn" onclick="editRecord({{ $item->id }}, '{{ $tab }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button class="md-act-btn del" onclick="deleteRecord({{ $item->id }}, '{{ $tab }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:60px 20px; color:var(--text-light)">
                                    <div style="font-size:32px; margin-bottom:12px; filter: grayscale(1)">📂</div>
                                    <div style="font-weight:600; font-size:15px; color:var(--text-gray)">No records found</div>
                                    <div style="font-size:12px; margin-top:4px">Try adjusting your filters or selected shift.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <div>
                    <span style="font-size:12px; color:var(--text-light); font-weight:700">Rows per page:</span>
                    <select class="per-page-select" onchange="changePerPage(this.value)">
                        @foreach([15, 30, 50, 100] as $v)
                            <option value="{{ $v }}" {{ $perPage == $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    {{ $history->links() }}
                </div>
            </div>
        </div>

        {{-- ══ MODALS =>═══════════════ --}}

        {{-- Edit 3M Modal --}}
        <div class="modal-overlay" id="modalEdit3M">
            <div class="modal-sheet" style="max-width:500px">
                <div class="modal-sheet-handle"></div>
                <div style="padding:20px 24px; border-bottom:1px solid #f3f3f6; display:flex; justify-content:space-between; align-items:center">
                    <h3 style="margin:0; font-family:'Roboto Condensed',sans-serif; text-transform:uppercase; color:var(--text-dark); font-size:16px; font-weight:800">Edit Problem Log</h3>
                    <button onclick="closeSheet('modalEdit3M')" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-light); hover:color:var(--text-dark)">✕</button>
                </div>
                <form id="formEdit3M" onsubmit="save3M(event)">
                    <input type="hidden" name="id" id="edit-3m-id">
                    <div class="modal-body-scroll">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" id="edit-3m-tanggal" class="form-control" required>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px">
                            <div class="form-group">
                                <label>Factory</label>
                                <input type="text" name="factory" id="edit-3m-factory" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Shift</label>
                                <select name="shift" id="edit-3m-shift" class="form-control">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tipe (Jenis)</label>
                            <select name="jenis" id="edit-3m-jenis" class="form-control">
                                @foreach($jenisList as $j) <option value="{{ $j }}">{{ $j }}</option> @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lokasi / Mesin</label>
                            <input type="text" name="lokasi" id="edit-3m-lokasi" class="form-control" required>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px">
                            <div class="form-group">
                                <label>Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" id="edit-3m-mulai" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" id="edit-3m-selesai" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Masalah</label>
                            <textarea name="deskripsi" id="edit-3m-deskripsi" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Cause (Penyebab)</label>
                            <input type="text" name="cause" id="edit-3m-cause" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Countermeasure</label>
                            <input type="text" name="countermeasure" id="edit-3m-cm" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>PIC</label>
                            <input type="text" name="pic" id="edit-3m-pic" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="edit-3m-status" class="form-control">
                                <option value="open">OPEN</option>
                                <option value="closed">CLOSED</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeSheet('modalEdit3M')" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Absence Modal --}}
        <div class="modal-overlay" id="modalEditAbsence">
            <div class="modal-sheet" style="max-width:420px">
                <div class="modal-sheet-handle"></div>
                <div style="padding:20px 24px; border-bottom:1px solid #f3f3f6; display:flex; justify-content:space-between; align-items:center">
                    <h3 style="margin:0; font-family:'Roboto Condensed',sans-serif; text-transform:uppercase; color:var(--text-dark); font-size:16px; font-weight:800">Edit Absence</h3>
                    <button onclick="closeSheet('modalEditAbsence')" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-light); hover:color:var(--text-dark)">✕</button>
                </div>
                <form id="formEditAbsence" onsubmit="saveAbsence(event)">
                    <input type="hidden" name="id" id="edit-abs-id">
                    <div class="modal-body-scroll">
                        <div class="form-group">
                            <label>Member</label>
                            <input type="text" id="edit-abs-name" class="form-control" readonly style="background:#f3f4f6">
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" id="edit-abs-tanggal" class="form-control" required>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px">
                            <div class="form-group">
                                <label>Factory</label>
                                <input type="text" name="factory" id="edit-abs-factory" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Shift</label>
                                <select name="shift" id="edit-abs-shift" class="form-control">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" name="reason" id="edit-abs-reason" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeSheet('modalEditAbsence')" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const CSRF = '{{ csrf_token() }}';

        //   Filtering Logic  
        function setDateMode(mode) {
            const u = new URL(window.location);
            u.searchParams.set('mode', mode);
            window.location = u.toString();
        }

        function applyHari() {
            const val = document.getElementById('inputHari').value;
            if (!val) return;
            const u = new URL(window.location);
            u.searchParams.set('mode', 'hari');
            u.searchParams.set('tanggal', val);
            u.searchParams.delete('dari');
            u.searchParams.delete('sampai');
            u.searchParams.delete('bulan');
            u.searchParams.delete('page');
            window.location = u.toString();
        }

        function applyBulan() {
            const bln = document.getElementById('inputBulanBulan').value;
            const thn = document.getElementById('inputBulanTahun').value;
            const u = new URL(window.location);
            u.searchParams.set('mode', 'bulan');
            u.searchParams.set('bulan', `${thn}-${bln}`);
            u.searchParams.delete('tanggal');
            u.searchParams.delete('dari');
            u.searchParams.delete('sampai');
            u.searchParams.delete('page');
            window.location = u.toString();
        }

        function applyRentang() {
            const dari = document.getElementById('inputDari').value;
            const sampai = document.getElementById('inputSampai').value;
            if (!dari || !sampai) return;
            const u = new URL(window.location);
            u.searchParams.set('mode', 'rentang');
            u.searchParams.set('dari', dari);
            u.searchParams.set('sampai', sampai);
            u.searchParams.delete('tanggal');
            u.searchParams.delete('bulan');
            u.searchParams.delete('page');
            window.location = u.toString();
        }

        function switchFactory(f) {
            const u = new URL(window.location);
            u.searchParams.set('factory', f);
            window.location = u.toString();
        }

        function switchShift(s) {
            const u = new URL(window.location);
            u.searchParams.set('shift', s);
            window.location = u.toString();
        }

        function changePerPage(v) {
            const u = new URL(window.location);
            u.searchParams.set('per_page', v);
            window.location = u.toString();
        }

        async function confirmDeleteAll() {
            const tabName = '{{ $tab === "3m" ? "Problem Logs (3M)" : "Absence Records" }}';

            const { value: result } = await Swal.fire({
                title: 'Confirm Bulk Deletion',
                html: `
                    <div style="text-align:left; font-size:14px; color:#4b5563; line-height:1.6;">
                        You are about to delete records from <strong>${tabName}</strong>.
                        <div style="background:#f8fafc; padding:15px; border-radius:12px; margin:15px 0; border:1px solid #e2e8f0;">
                            <div style="margin-bottom:8px;">• Factory: <strong style="color:var(--brand-primary)">{{ strtoupper($factory) }}</strong></div>
                            <div style="margin-bottom:8px;">• Shift: <strong style="color:var(--brand-primary)">SHIFT {{ $shift }}</strong></div>
                            <div id="swal-date-scope">• Current View: <strong>{{ $dari }} to {{ $sampai }}</strong></div>
                        </div>

                        <div style="margin:20px 0; padding:12px; background:#fff1f2; border-radius:10px; border:1px solid #fecaca; display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" id="swal-delete-all-time" style="width:18px; height:18px; cursor:pointer;" onchange="toggleSwalScope(this)">
                            <label for="swal-delete-all-time" style="cursor:pointer; font-weight:700; color:#e11d48; font-size:13px;">
                                DELETE ALL TIME (Ignore date filter)
                            </label>
                        </div>

                        <p style="margin-bottom:10px;">To confirm, please type exactly:</p>
                        <code style="display:block; padding:8px; background:#f1f5f9; border-radius:6px; text-align:center; font-weight:800; color:#0f172a; margin-bottom:15px;">yes i responsible for this</code>
                    </div>
                `,
                input: 'text',
                inputPlaceholder: 'Type confirmation here...',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Permanently Delete',
                didOpen: () => {
                    window.toggleSwalScope = (el) => {
                        const scopeDiv = document.getElementById('swal-date-scope');
                        if (el.checked) {
                            scopeDiv.innerHTML = '• Deletion Scope: <strong style="color:#ef4444">ALL DATA (NO DATE LIMIT)</strong>';
                        } else {
                            scopeDiv.innerHTML = '• Current View: <strong>{{ $dari }} to {{ $sampai }}</strong>';
                        }
                    }
                },
                inputValidator: (value) => {
                    if (value !== 'yes i responsible for this') {
                        return 'Confirmation text does not match!';
                    }
                }
            });

            if (result === 'yes i responsible for this') {
                const deleteAllTime = document.getElementById('swal-delete-all-time').checked;

                showLoading('Deleting records...');
                try {
                    const u = new URL(window.location);
                    const params = new URLSearchParams(deleteAllTime ? '' : u.search);

                    params.set('tab', '{{ $tab }}');
                    params.set('factory', '{{ $factory }}');
                    params.set('shift', '{{ $shift }}');

                    if (!deleteAllTime) {
                        // Ensure current filters are sent if not already in URL
                        if (!params.has('mode')) params.set('mode', '{{ $mode }}');
                        if (!params.has('tanggal')) params.set('tanggal', '{{ $tanggal }}');
                        if (!params.has('dari')) params.set('dari', '{{ $dari }}');
                        if (!params.has('sampai')) params.set('sampai', '{{ $sampai }}');
                    }

                    const res = await fetch(`/admin/master-data/delete-all?${params.toString()}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    hideLoading();

                    if (data.ok) {
                        await Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: 'var(--brand-primary)'
                        });
                        window.location.reload();
                    } else {
                        showToast(data.message || 'Error occurred during deletion', 'error');
                    }
                } catch (e) {
                    hideLoading();
                    showToast('Error deleting records', 'error');
                }
            }
        }

        //   Actions  
        async function deleteRecord(id, type) {
            if (!confirm(`Delete this record?`)) return;
            try {
                const res = await fetch(`/admin/master-data/${id}?type=${type}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                if ((await res.json()).ok) {
                    const row = document.getElementById(`row-${id}`);
                    if (row) {
                        row.style.transform = 'scale(0.95)';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 250);
                    }
                    showToast('Deleted successfully', 'success');
                }
            } catch (e) { showToast('Error deleting record', 'error'); }
        }

        async function editRecord(id, type) {
            try {
                const res = await fetch(`/admin/master-data/${id}?type=${type}`);
                const data = await res.json();

                if (type === '3m') {
                    document.getElementById('edit-3m-id').value = data.id;
                    document.getElementById('edit-3m-tanggal').value = data.tanggal;
                    document.getElementById('edit-3m-factory').value = data.factory;
                    document.getElementById('edit-3m-shift').value = data.shift;
                    document.getElementById('edit-3m-jenis').value = data.jenis;
                    document.getElementById('edit-3m-lokasi').value = data.lokasi;
                    document.getElementById('edit-3m-mulai').value = data.waktu_mulai.substring(0, 5);
                    document.getElementById('edit-3m-selesai').value = data.waktu_selesai ? data.waktu_selesai.substring(0, 5) : '';
                    document.getElementById('edit-3m-deskripsi').value = data.deskripsi;
                    document.getElementById('edit-3m-cause').value = data.cause || '';
                    document.getElementById('edit-3m-cm').value = data.countermeasure || '';
                    document.getElementById('edit-3m-pic').value = data.pic || '';
                    document.getElementById('edit-3m-status').value = data.status;
                    openSheet('modalEdit3M');
                } else if (type === 'absence') {
                    document.getElementById('edit-abs-id').value = data.id;
                    document.getElementById('edit-abs-name').value = data.member ? data.member.nama : 'Unknown';
                    document.getElementById('edit-abs-tanggal').value = data.tanggal;
                    document.getElementById('edit-abs-factory').value = data.factory;
                    document.getElementById('edit-abs-shift').value = data.shift;
                    document.getElementById('edit-abs-reason').value = data.reason;
                    openSheet('modalEditAbsence');
                } else {
                    showToast('Edit for this type is not implemented yet in this modal.', 'info');
                }
            } catch (e) { showToast('Error loading record data', 'error'); }
        }

        async function save3M(e) {
            e.preventDefault();
            const id = document.getElementById('edit-3m-id').value;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch(`/admin/master-data/${id}?type=3m`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if ((await res.json()).ok) {
                    showToast('Updated successfully', 'success');
                    closeSheet('modalEdit3M');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch (e) { showToast('Error updating record', 'error'); }
        }

        async function saveAbsence(e) {
            e.preventDefault();
            const id = document.getElementById('edit-abs-id').value;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch(`/admin/master-data/${id}?type=absence`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if ((await res.json()).ok) {
                    showToast('Updated successfully', 'success');
                    closeSheet('modalEditAbsence');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch (e) { showToast('Error updating record', 'error'); }
        }
    </script>
@endpush
