@extends('layouts.admin')
@section('title', 'Laporan')

@push('styles')
    <style>
        /*   Section card                      ─ */
        .rpt-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 14px;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .rpt-section-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #aaa;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rpt-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f0f0;
        }

        /*   4M stat boxes                        */
        .m4-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .m4-box {
            text-align: center;
            padding: 12px 6px;
            border-radius: 12px;
            border: 1.5px solid transparent;
        }

        .m4-box .m4-val {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
        }

        .m4-box .m4-lbl {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #aaa;
            margin-top: 4px;
        }

        .m4-man {
            background: rgba(231, 76, 60, .07);
            border-color: rgba(231, 76, 60, .2);
        }

        .m4-man .m4-val {
            color: #e74c3c;
        }

        .m4-machine {
            background: rgba(31, 60, 136, .07);
            border-color: rgba(31, 60, 136, .2);
        }

        .m4-machine .m4-val {
            color: #1f3c88;
        }

        .m4-material {
            background: rgba(243, 156, 18, .07);
            border-color: rgba(243, 156, 18, .2);
        }

        .m4-material .m4-val {
            color: #f39c12;
        }

        .m4-method {
            background: rgba(46, 125, 50, .07);
            border-color: rgba(46, 125, 50, .2);
        }

        .m4-method .m4-val {
            color: #2e7d32;
        }

        .m4-open {
            background: rgba(231, 76, 60, .05);
            border-color: rgba(231, 76, 60, .2);
        }

        .m4-open .m4-val {
            color: #e74c3c;
        }

        .m4-closed {
            background: rgba(46, 125, 50, .05);
            border-color: rgba(46, 125, 50, .2);
        }

        .m4-closed .m4-val {
            color: #2e7d32;
        }

        /*   Absen member list                      */
        .absen-member-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f8f8f8;
        }

        .absen-member-row:last-child {
            border-bottom: none;
        }

        .absen-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--navy);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .absen-info {
            flex: 1;
            min-width: 0;
        }

        .absen-nama {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 800;
            color: var(--navy);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .absen-meta {
            font-size: 11px;
            color: #aaa;
            margin-top: 1px;
        }

        .absen-mesin-tag {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .tag-diganti {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1.5px solid #a5d6a7;
        }

        .tag-belum {
            background: #fff3e0;
            color: #e65100;
            border: 1.5px solid #ffcc80;
        }

        .tag-no-mesin {
            background: #f5f5f5;
            color: #aaa;
            border: 1.5px solid #e0e0e0;
        }

        /*   Replacement list                     ─ */
        .repl-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid #f8f8f8;
        }

        .repl-row:last-child {
            border-bottom: none;
        }

        .repl-arrow {
            font-size: 14px;
            color: #aaa;
        }

        .repl-info {
            flex: 1;
            font-size: 12px;
            color: #444;
        }

        .repl-mesin {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            color: var(--navy);
        }

        .repl-nama {
            color: #888;
        }

        /*   Warning banner                      ─ */
        .warning-banner {
            background: #fff8e1;
            border: 1.5px solid #ffe082;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 12px;
            color: #e65100;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        /*   Log cards                         ─ */
        .log-summary-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .lsb-item {
            flex: 1;
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid #eee;
            padding: 10px 8px;
            text-align: center;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
        }

        .lsb-val {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
            color: var(--navy);
        }

        .lsb-lbl {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #aaa;
            margin-top: 3px;
        }

        .log-card {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #eee;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
            margin-bottom: 10px;
            overflow: hidden;
            transition: box-shadow .2s, border-color .2s;
        }

        .log-card.open {
            border-left: 4px solid #e74c3c;
        }

        .log-card.closed {
            border-left: 4px solid #4caf50;
            opacity: .85;
        }

        .log-card-top {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px 6px;
            flex-wrap: wrap;
        }

        .log-badge {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 900;
            padding: 3px 9px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #fff;
            flex-shrink: 0;
        }

        .log-machine {
            background: #1f3c88;
        }

        .log-material {
            background: #f39c12;
        }

        .log-method {
            background: #2e7d32;
        }

        .log-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dot-open {
            background: #e74c3c;
            animation: pulse-red 1.5s infinite;
        }

        .dot-closed {
            background: #4caf50;
        }

        @keyframes pulse-red {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, .5);
            }

            50% {
                box-shadow: 0 0 0 5px rgba(231, 76, 60, 0);
            }
        }

        .log-time {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            color: #888;
            font-weight: 600;
        }

        .log-durasi {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            color: #aaa;
            margin-left: auto;
        }

        .log-durasi.ongoing {
            color: #e74c3c;
            font-weight: 800;
            animation: blink .9s step-end infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .3
            }
        }

        .log-card-body {
            padding: 4px 12px 8px;
        }

        .log-lokasi {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 3px;
        }

        .log-desc {
            font-size: 12px;
            color: #444;
            line-height: 1.5;
        }

        .log-meta {
            font-size: 11px;
            color: #777;
            margin-top: 3px;
            line-height: 1.4;
        }

        .log-card-actions {
            display: flex;
            gap: 6px;
            padding: 8px 12px 10px;
            border-top: 1px solid #f5f5f5;
        }

        .log-btn {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1.5px solid #e0e0e0;
            background: #f7f7f7;
            font-size: 11px;
            font-weight: 700;
            font-family: 'Roboto Condensed', sans-serif;
            cursor: pointer;
            transition: all .15s;
            color: #555;
        }

        .log-btn:hover {
            border-color: #bbb;
            background: #eee;
        }

        .log-btn-close {
            border-color: #a5d6a7;
            color: #2e7d32;
            background: #f1f8e9;
        }

        .log-btn-close:hover {
            background: #c8e6c9;
        }

        .log-btn-reopen {
            border-color: #90caf9;
            color: #1565c0;
            background: #e3f2fd;
        }

        .log-btn-reopen:hover {
            background: #bbdefb;
        }

        .log-btn-del {
            border-color: #ffcdd2;
            color: #c62828;
            background: #fff5f5;
            margin-left: auto;
        }

        .log-btn-del:hover {
            background: #ffcdd2;
        }

        /*   Date Mode Bar                       ─ */
        .date-mode-bar {
            display: flex;
            align-items: stretch;
            gap: 0;
            background: #fff;
            border-radius: 50px;
            padding: 6px 10px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 6px;
        }

        .dmb-mode-group {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #f5f5f5;
            border-radius: 30px;
            padding: 3px;
        }

        .dmb-btn {
            padding: 6px 14px;
            border-radius: 30px;
            border: none;
            background: transparent;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #999;
            cursor: pointer;
            transition: all .18s;
            white-space: nowrap;
        }

        .dmb-btn.active {
            background: #ffffffff;
            color: #fff;
            box-shadow: 0 2px 6px rgba(46, 125, 50, .3);
        }

        .dmb-inputs {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 0;
        }

        .dmb-input {
            padding: 7px 12px;
            border: 1.5px solid #e0e0e0;
            border-radius: 20px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            background: #fafafa;
            cursor: pointer;
            outline: none;
            transition: border-color .15s;
        }

        .dmb-input:focus {
            border-color: #2E7D32;
            background: #fff;
        }

        .dmb-arrow {
            font-size: 14px;
            color: #bbb;
            flex-shrink: 0;
        }

        .dmb-apply {
            padding: 7px 16px;
            border-radius: 20px;
            border: none;
            background: #2E7D32;
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            transition: filter .15s;
            flex-shrink: 0;
        }

        .dmb-apply:hover {
            filter: brightness(1.12);
        }

        /*   Jenis Selector                      ─ */
        .jenis-selector {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .jenis-btn {
            flex: 1;
            padding: 12px 8px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            background: #f9f9f9;
            cursor: pointer;
            text-align: center;
            transition: all .18s;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .jenis-btn .jb-icon {
            font-size: 22px;
            display: block;
            margin-bottom: 4px;
        }

        .jenis-btn .jb-lbl {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #888;
        }

        .jenis-btn.sel-machine {
            border-color: #1f3c88;
            background: #eef1fa;
        }

        .jenis-btn.sel-machine .jb-lbl {
            color: #1f3c88;
        }

        .jenis-btn.sel-material {
            border-color: #f39c12;
            background: #fff8ec;
        }

        .jenis-btn.sel-material .jb-lbl {
            color: #f39c12;
        }

        .jenis-btn.sel-method {
            border-color: #2e7d32;
            background: #f1f8e9;
        }

        .jenis-btn.sel-method .jb-lbl {
            color: #2e7d32;
        }

        .form-section {
            display: none;
            animation: fadeIn .2s;
        }

        .form-section.visible {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .section-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 12px;
        }

        .shb-machine {
            background: #eef1fa;
            color: #1f3c88;
            border: 1.5px solid #c5cae9;
        }

        .shb-material {
            background: #fff8ec;
            color: #e67e22;
            border: 1.5px solid #ffe0b2;
        }

        .shb-method {
            background: #f1f8e9;
            color: #2e7d32;
            border: 1.5px solid #c8e6c9;
        }

        .ql-header,
        .et-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px 12px;
            border-bottom: 1.5px solid #f0f0f0;
        }

        .ql-header-left,
        .et-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ql-header-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--orange, #e65100), #ff7043);
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(230, 81, 0, .3);
        }

        .ql-header-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #222;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .ql-header-sub {
            font-size: 11px;
            color: #aaa;
            margin-top: 1px;
        }

        .et-header-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #1f3c88, #2c4a9e);
            color: #fff;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(31, 60, 136, .3);
        }

        .et-header-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #222;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .form-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 14px 0 10px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #bbb;
        }

        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #eee;
        }
    </style>
@endpush

@section('content')

    {{-- ══ DATE MODE BAR =>═════════ --}}
    <div class="date-mode-bar">

        {{-- Mode toggle buttons --}}
        <div class="dmb-mode-group">
            <button class="dmb-btn {{ $mode === 'hari' ? 'active' : '' }}" onclick="setDateMode('hari')">📅 Per
                Hari</button>
            <button class="dmb-btn {{ $mode === 'bulan' ? 'active' : '' }}" onclick="setDateMode('bulan')">📆 Per
                Bulan</button>
            <button class="dmb-btn {{ $mode === 'rentang' ? 'active' : '' }}" onclick="setDateMode('rentang')">📊
                Rentang</button>
        </div>

        {{-- Mode: Per Hari --}}
        <div class="dmb-inputs" id="dmb-hari" style="{{ $mode === 'hari' ? '' : 'display:none' }}">
            <input type="date" id="inputHari" value="{{ $tanggal }}" class="dmb-input" onchange="applyHari()">
            <button class="dmb-apply" onclick="applyHari()">Tampilkan</button>
        </div>

        {{-- Mode: Per Bulan --}}
        <div class="dmb-inputs" id="dmb-bulan" style="{{ $mode === 'bulan' ? '' : 'display:none' }}">
            @php
                $selYear = explode('-', $bulan)[0];
                $selMonth = explode('-', $bulan)[1];
                $monthsMap = [
                    '01' => 'Jan',
                    '02' => 'Feb',
                    '03' => 'Mar',
                    '04' => 'Apr',
                    '05' => 'Mei',
                    '06' => 'Jun',
                    '07' => 'Jul',
                    '08' => 'Agu',
                    '09' => 'Sep',
                    '10' => 'Okt',
                    '11' => 'Nov',
                    '12' => 'Des'
                ];
            @endphp
            <select id="inputBulanBulan" class="dmb-input" style="width:auto; cursor:pointer; padding-right:24px;"
                onchange="applyBulan()">
                @foreach($monthsMap as $num => $name)
                    <option value="{{ $num }}" {{ $selMonth === $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select id="inputBulanTahun" class="dmb-input" style="width:auto; cursor:pointer; padding-right:24px;"
                onchange="applyBulan()">
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $selYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button class="dmb-apply" onclick="applyBulan()">Tampilkan</button>
        </div>

        {{-- Mode: Rentang --}}
        <div class="dmb-inputs" id="dmb-rentang" style="{{ $mode === 'rentang' ? '' : 'display:none' }}">
            <input type="date" id="inputDari" value="{{ $dari }}" class="dmb-input">
            <span class="dmb-arrow">→</span>
            <input type="date" id="inputSampai" value="{{ $sampai }}" class="dmb-input">
            <button class="dmb-apply" onclick="applyRentang()">Tampilkan</button>
        </div>

        {{-- Factory badge always visible --}}
        <button onclick="showFactoryPicker()" {{ count($factories) === 1 ? 'disabled' : '' }} style="background:#2E7D32;border:none;border-radius:20px;padding:7px 18px;color:#fff;
                           font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:12px;
                           letter-spacing:1px;cursor:{{ count($factories) === 1 ? 'default' : 'pointer' }};white-space:nowrap;display:flex;align-items:center;gap:6px;
                           transition:all .2s ease;margin-left:auto;{{ count($factories) === 1 ? 'opacity:0.9' : '' }}" 
            @if(count($factories) !== 1)
            onmouseover="this.style.filter='brightness(1.15)'"
            onmouseout="this.style.filter='brightness(1)'"
            @endif>
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff"
                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="15" rx="1" />
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                <line x1="12" y1="12" x2="12" y2="12" />
                <line x1="8" y1="12" x2="8" y2="12" />
                <line x1="16" y1="12" x2="16" y2="16" />
                <line x1="8" y1="16" x2="8" y2="16" />
                <line x1="16" y1="16" x2="16" y2="16" />
                <line x1="12" y1="16" x2="12" y2="16" />
            </svg>
            FACTORY {{ $currentFactory ? ($currentFactory->short_label ?? str_replace('Factory ', '', $currentFactory->name)) : session('factory', '2') }}
        </button>
    </div>
    <div class="shift-toggle-bar">
        @php $userShift = auth()->user()->shift;
        $isAdmin = auth()->user()->role === 'admin'; @endphp
        @if($isAdmin || !$userShift || $userShift === 'A')
            <button class="shift-toggle-btn {{ $shift === 'A' ? 'active' : '' }}" onclick="switchShift('A')">SHIFT A</button>
        @endif
        @if($isAdmin || !$userShift || $userShift === 'B')
            <button class="shift-toggle-btn {{ $shift === 'B' ? 'active' : '' }}" onclick="switchShift('B')">SHIFT B</button>
        @endif
    </div>

    {{-- Header + export --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div style="font-family:'Roboto Condensed',sans-serif;font-size:13px;font-weight:700;color:var(--navy)">
            @if($mode === 'bulan') LAPORAN BULANAN
            @elseif($mode === 'rentang') LAPORAN RENTANG
            @else LAPORAN HARIAN
            @endif
        </div>
        @php
            $exportParams = array_merge(request()->query(), ['factory' => $factory, 'shift' => $shift, 'tanggal' => $tanggal]);
        @endphp
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.reports.export', $exportParams) }}" style="padding:8px 14px;border-radius:8px;background:var(--green);color:#fff;
                      text-decoration:none;font-size:12px;font-weight:700;font-family:'Roboto Condensed',sans-serif">
                📤 CSV
            </a>
            <a href="{{ route('admin.reports.backup', $exportParams) }}" style="padding:8px 14px;border-radius:8px;background:var(--navy);color:#fff;
                      text-decoration:none;font-size:12px;font-weight:700;font-family:'Roboto Condensed',sans-serif">
                💾 JSON
            </a>
        </div>
    </div>

    {{-- Info card --}}
    <div class="rpt-card" style="padding:12px 14px">
        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:#555">
            <div><span style="color:#aaa">Factory:</span> <strong>{{ $factory }}</strong></div>
            <div><span style="color:#aaa">Shift:</span> <strong>{{ $shift }}</strong></div>
            @if($mode === 'hari')
                <div><span style="color:#aaa">Tanggal:</span>
                    <strong>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}</strong>
                </div>
            @elseif($mode === 'bulan')
                <div><span style="color:#aaa">Bulan:</span>
                    <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->locale('id')->isoFormat('MMMM YYYY') }}</strong>
                </div>
            @else
                <div><span style="color:#aaa">Rentang:</span>
                    <strong>{{ \Carbon\Carbon::parse($dari)->locale('id')->isoFormat('D MMM') }}
                        → {{ \Carbon\Carbon::parse($sampai)->locale('id')->isoFormat('D MMM YYYY') }}</strong>
                </div>
            @endif
            @if($isRange)
                <div style="background:#e8f5e9;color:#2e7d32;padding:2px 9px;border-radius:8px;font-weight:700;
                            font-family:'Roboto Condensed',sans-serif;font-size:11px;border:1px solid #a5d6a7">
                    📊 {{ $logs->count() }} log ·
                    {{ $dari === $sampai ? 1 : \Carbon\Carbon::parse($dari)->diffInDays(\Carbon\Carbon::parse($sampai)) + 1 }}
                    hari
                </div>
            @endif
        </div>
    </div>

    {{-- ══ SECTION 1: 4M Summary =>══ --}}
    @php
        $manCount = $absenMembers->count();
        $openCount = $logs->where('status', 'open')->count();
        $closedCount = $logs->where('status', 'closed')->count();
        $totalLogs = $logs->count();
    @endphp

    <div class="rpt-card">
        <div class="rpt-section-title">📊 4M Summary</div>

        <div class="m4-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:8px">
            <div class="m4-box m4-man">
                <div class="m4-val">{{ $manCount }}</div>
                <div class="m4-lbl">👤 Man (Absen)</div>
            </div>
            <div class="m4-box" style="background:rgba(46,125,50,.05);border-color:rgba(46,125,50,.2)">
                <div class="m4-val" style="color:#2e7d32">{{ $replacements->count() }}</div>
                <div class="m4-lbl">🔄 Pengganti</div>
            </div>
        </div>

        <div class="m4-grid" style="grid-template-columns:repeat({{ count($jenisList) ?: 1 }}, 1fr)">
            @foreach($jenisList as $jenis)
            @php $cssClass = 'm4-' . strtolower($jenis); @endphp
            <div class="m4-box {{ in_array(strtolower($jenis), ['machine','material','method']) ? $cssClass : '' }}" style="{{ !in_array(strtolower($jenis), ['machine','material','method']) ? 'border: 1.5px solid rgba(0,0,0,0.1); background: #f9f9f9; color: var(--navy)' : '' }}">
                <div class="m4-val">{{ $logs->where('jenis', $jenis)->count() }}</div>
                <div class="m4-lbl">{{ strtoupper($jenis) }}</div>
            </div>
            @endforeach
        </div>

        @if($totalLogs > 0)
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px">
                <div class="m4-box m4-open">
                    <div class="m4-val">{{ $openCount }}</div>
                    <div class="m4-lbl">⚠️ Log Open</div>
                </div>
                <div class="m4-box m4-closed">
                    <div class="m4-val">{{ $closedCount }}</div>
                    <div class="m4-lbl">✅ Log Closed</div>
                </div>
            </div>
        @endif
    </div>

    {{-- ══ SECTION 2: Man  - Absensi => --}}
    <div class="rpt-card">
        <div class="rpt-section-title">👤 Man  - Absensi MP ({{ $manCount }})</div>

        @if(count($absenTanpaRepl) > 0)
            <div class="warning-banner">
                ⚠️ {{ count($absenTanpaRepl) }} mesin belum ada pengganti:
                {{ implode(', ', $absenTanpaRepl) }}
            </div>
        @endif

        @forelse($absenMembers as $member)
            @php
                $sudahDiganti = $replacements->where('target_machine', $member->mesin)->isNotEmpty();
            @endphp
            <div class="absen-member-row">
                <div class="absen-avatar">
                    {{ strtoupper(substr($member->nama, 0, 2)) }}
                </div>
                <div class="absen-info">
                    <div class="absen-nama">{{ $member->nama }}</div>
                    <div class="absen-meta">{{ $member->nik }} · {{ $member->jabatan }}</div>
                </div>
                @if($member->mesin)
                    <span class="absen-mesin-tag {{ $sudahDiganti ? 'tag-diganti' : 'tag-belum' }}">
                        {{ $member->mesin }}<br>
                        <span style="font-weight:400">{{ $sudahDiganti ? '✅ diganti' : '⚠️ belum' }}</span>
                    </span>
                @else
                    <span class="absen-mesin-tag tag-no-mesin">–</span>
                @endif
            </div>
        @empty
            <div style="text-align:center;padding:16px;color:#aaa;font-size:12px">
                ✅ Tidak ada absensi MP hari ini
            </div>
        @endforelse

        @if($replacements->isNotEmpty())
            <div style="margin-top:12px">
                <div class="rpt-section-title" style="font-size:10px;color:#ccc">Pengganti yang Assign</div>
                @foreach($replacements as $repl)
                    <div class="repl-row">
                        <span class="repl-mesin">{{ $repl->target_machine }}</span>
                        <span class="repl-arrow">←</span>
                        <div class="repl-info">
                            <span class="repl-nama">{{ $repl->member?->nama ?? 'ID ' . $repl->member_id }}</span>
                            @if($repl->catatan)
                                <span style="color:#bbb"> · {{ $repl->catatan }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══ SECTION 3: Problem Log (Full Interactive)--}}
    <div style="margin-bottom:80px">

        {{-- Sub-header + tombol tambah --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div class="rpt-section-title" style="margin-bottom:0;flex:1">🔧 Problem Log 3M</div>
        </div>

        {{-- Summary bar --}}
        <div class="log-summary-bar" style="overflow-x:auto; padding-bottom:4px;">
            <div class="lsb-item">
                <div class="lsb-val" id="cntAll">{{ $logs->count() }}</div>
                <div class="lsb-lbl">Total</div>
            </div>
            <div class="lsb-item">
                <div class="lsb-val" style="color:#e74c3c" id="cntOpen">{{ $logs->where('status', 'open')->count() }}</div>
                <div class="lsb-lbl">Open</div>
            </div>
            @foreach($jenisList as $jenis)
            @php 
                $color = match(strtolower($jenis)) {
                    'machine' => '#1f3c88',
                    'material' => '#f39c12',
                    'method' => '#2e7d32',
                    default => 'var(--navy)'
                };
            @endphp
            <div class="lsb-item">
                <div class="lsb-val" style="color:{{ $color }}" id="cnt{{ $jenis }}">{{ $logs->where('jenis', $jenis)->count() }}</div>
                <div class="lsb-lbl">{{ $jenis }}</div>
            </div>
            @endforeach
        </div>

        {{-- Log list --}}
        <div id="logList">
            @forelse($logs as $log)
                <div class="log-card {{ $log->status }}" id="logcard-{{ $log->id }}">
                    <div class="log-card-top">
                        <span class="log-badge log-{{ strtolower($log->jenis) }}">{{ $log->jenis }}</span>
                        <span class="log-status-dot {{ $log->status === 'open' ? 'dot-open' : 'dot-closed' }}"
                            id="dot-{{ $log->id }}"></span>
                        <span class="log-time" id="time-{{ $log->id }}">
                            @if($isRange)
                                <span style="font-size:10px;background:#f0f4ff;color:#1f3c88;padding:1px 6px;border-radius:6px;
                                             font-family:'Roboto Condensed',sans-serif;font-weight:800;margin-right:4px;">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->format('d/m') }}
                                </span>
                            @endif
                            {{ substr($log->waktu_mulai, 0, 5) }}{{ $log->waktu_selesai ? ' – ' . substr($log->waktu_selesai, 0, 5) : ' – …' }}
                        </span>
                        @if($log->durasi)
                            <span class="log-durasi" id="dur-{{ $log->id }}">({{ $log->durasi }})</span>
                        @elseif($log->status === 'open')
                            <span class="log-durasi ongoing" id="dur-{{ $log->id }}">ON GOING</span>
                        @endif

                    </div>
                    <div class="log-card-body">
                        <div class="log-lokasi">📍 {{ $log->lokasi }}</div>
                        <div class="log-desc">{{ $log->deskripsi }}</div>
                        @if($log->cause)
                            <div class="log-meta">🔍 <strong>Cause:</strong> {{ $log->cause }}</div>
                        @endif
                        @if($log->countermeasure)
                            <div class="log-meta">🔧 <strong>CM:</strong> {{ $log->countermeasure }}</div>
                        @endif
                        @if($log->pic)
                            <div class="log-meta">👤 <strong>PIC:</strong> {{ $log->pic }}</div>
                        @endif
                    </div>
                    <div class="log-card-actions">
                        @if($log->status === 'open')
                            <button class="log-btn log-btn-close" id="btn-close-{{ $log->id }}" onclick="closeLog({{ $log->id }})">✅
                                Selesai</button>
                        @else
                            <button class="log-btn log-btn-reopen" onclick="reopenLog({{ $log->id }})">🔄 Buka Ulang</button>
                        @endif
                        <button class="log-btn log-btn-edit"
                            onclick="editLog({{ $log->id }},'{{ substr($log->waktu_mulai, 0, 5) }}','{{ $log->waktu_selesai ? substr($log->waktu_selesai, 0, 5) : '' }}')">
                            ✏️ Edit Waktu
                        </button>
                        <button class="log-btn log-btn-del" onclick="deleteLog({{ $log->id }})">🗑️</button>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="ei">📝</div>
                    <div class="et">Belum ada problem log hari ini.<br>Tap <strong>➕ Tambah Log</strong> untuk mulai.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ══ MODAL: Tambah Log =>══════ --}}
    <div class="modal-overlay" id="addLogSheet">
        <div class="modal-sheet">
            <div class="modal-sheet-handle"></div>
            <div class="ql-header">
                <div class="ql-header-left">
                    <div class="ql-header-icon">＋</div>
                    <div>
                        <div class="ql-header-title">Tambah Problem Log</div>
                        <div class="ql-header-sub">{{ $factory }} · Shift {{ $shift }} · {{ $tanggal }}</div>
                    </div>
                </div>
                <button class="modal-sheet-close" onclick="closeSheet('addLogSheet')">✕</button>
            </div>
            <div class="modal-sheet-body">
                <div class="form-divider">Pilih Tipe Masalah</div>
                <div class="jenis-selector" style="flex-wrap: wrap;">
                    @foreach($jenisList as $jenis)
                    @php 
                        $icon = match(strtolower($jenis)) {
                            'machine' => '⚙️',
                            'material' => '📦',
                            'method' => '📋',
                            default => '⚡'
                        };
                    @endphp
                    <button type="button" class="jenis-btn" id="btn-{{ strtolower($jenis) }}" onclick="selectJenis('{{ $jenis }}')">
                        <span class="jb-icon">{{ $icon }}</span><span class="jb-lbl">{{ $jenis }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- FORM: DYNAMIC --}}
                @foreach($jenisList as $jenis)
                @php
                    $p = strtolower($jenis);
                    $icon = match($p) { 'machine' => '⚙️', 'material' => '📦', 'method' => '📋', default => '⚡' };
                    $badgeClass = in_array($p, ['machine','material','method']) ? 'shb-'.$p : '';
                    $badgeStyle = $badgeClass ? '' : 'background:#eef1fa; color:#1f3c88; border:1.5px solid #c5cae9;';
                    $borderColor = match($p) { 'machine' => '#1f3c88', 'material' => '#f39c12', 'method' => '#2e7d32', default => '#1f3c88' };
                @endphp
                <div class="form-section" id="section-{{ $p }}">
                    <div class="section-header-badge {{ $badgeClass }}" style="{{ $badgeStyle }}">{{ $icon }} {{ $jenis }} Problem</div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Mesin / Lokasi *</label>
                            <select id="{{ $p }}-lokasi">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesinList as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Waktu Mulai *</label>
                            <input type="time" id="{{ $p }}-mulai">
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Deskripsi Masalah / Kerusakan *</label>
                        <textarea id="{{ $p }}-deskripsi" rows="2" placeholder="Contoh: Deskripsi masalah..."
                            style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                            onfocus="this.style.borderColor='{{ $borderColor }}'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>Penyebab</label><input type="text" id="{{ $p }}-cause"
                                placeholder="Contoh: detail penyebab..."></div>
                        <div class="field-group"><label>Countermeasure</label><input type="text" id="{{ $p }}-cm"
                                placeholder="Contoh: tindakan yang diambil..."></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>Teknisi / PIC</label><input type="text" id="{{ $p }}-pic"
                                placeholder="Nama yang handle"></div>
                        <div class="field-group">
                            <label>Status</label>
                            <select id="{{ $p }}-status" onchange="toggleSelesai('{{ $p }}', this.value)">
                                <option value="open">Open  - belum selesai</option>
                                <option value="closed">Closed  - sudah selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group" id="{{ $p }}-selesai-wrap" style="display:none">
                        <label>Waktu Selesai</label>
                        <input type="time" id="{{ $p }}-selesai">
                    </div>
                </div>
                @endforeach

                <div class="save-bar" id="saveBtnWrap" style="display:none">
                    <button class="save-btn-big" id="saveBtnMain" onclick="submitLog()">💾 Simpan Log</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: Edit Waktu =>══════ --}}
    <div class="modal-overlay" id="editTimeSheet">
        <div class="modal-sheet" style="max-height:320px">
            <div class="modal-sheet-handle"></div>
            <div class="et-header">
                <div class="et-header-left">
                    <div class="et-header-icon">✏️</div>
                    <div class="et-header-title">Edit Waktu Log</div>
                </div>
                <button class="modal-sheet-close" onclick="closeSheet('editTimeSheet')">✕</button>
            </div>
            <div class="modal-sheet-body">
                <input type="hidden" id="editLogId">
                <div class="form-row">
                    <div class="field-group">
                        <label>Waktu Mulai</label>
                        <input type="time" id="editMulai">
                    </div>
                    <div class="field-group">
                        <label>Waktu Selesai <span style="font-weight:400;color:#bbb">(opsional)</span></label>
                        <input type="time" id="editSelesai">
                    </div>
                </div>
                <div id="durasiPreview" style="text-align:center;font-family:'Roboto Condensed',sans-serif;
                     font-size:13px;color:#888;margin-bottom:8px;min-height:20px"></div>
                <div class="save-bar" style="margin-top:4px">
                    <button class="save-btn-big" onclick="saveEditTime()">💾 Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: Close Log (Wajib Countermeasure)  => --}}
    <div class="modal-overlay" id="closeLogSheet">
        <div class="modal-sheet" style="max-height:380px">
            <div class="modal-sheet-handle"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:14px 16px 12px;border-bottom:1.5px solid #f0f0f0">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:36px;height:36px;border-radius:10px;
                                background:linear-gradient(135deg,#2e7d32,#43a047);
                                color:#fff;font-size:18px;display:flex;align-items:center;
                                justify-content:center;box-shadow:0 3px 8px rgba(46,125,50,.3);
                                flex-shrink:0">✅</div>
                    <div>
                        <div style="font-family:'Roboto Condensed',sans-serif;font-size:14px;
                                    font-weight:900;color:#222;text-transform:uppercase;
                                    letter-spacing:.5px">Selesaikan Log</div>
                        <div id="closeLogSubtitle" style="font-size:11px;color:#aaa;margin-top:1px"></div>
                    </div>
                </div>
                <button class="modal-sheet-close" onclick="closeSheet('closeLogSheet')">✕</button>
            </div>
            <div class="modal-sheet-body">
                <input type="hidden" id="closeLogId">

                {{-- Countermeasure  - WAJIB --}}
                <div style="margin-bottom:14px">
                    <label style="font-family:'Roboto Condensed',sans-serif;font-size:11px;
                                  font-weight:900;text-transform:uppercase;letter-spacing:.5px;
                                  color:#2e7d32;display:flex;align-items:center;
                                  gap:6px;margin-bottom:7px">
                        🔧 Penyebab
                        <span style="background:#e74c3c;color:#fff;font-size:9px;
                                     padding:2px 7px;border-radius:4px;font-weight:900">WAJIB</span>
                    </label>
                    <textarea id="closeCM" rows="4" placeholder="Tindakan yang dilakukan untuk menyelesaikan masalah..."
                        style="width:100%;padding:11px 12px;border:2px solid #e0e0e0;
                                     border-radius:10px;font-family:inherit;font-size:13px;
                                     resize:none;box-sizing:border-box;outline:none;
                                     transition:border-color .2s,box-shadow .2s;line-height:1.5" oninput="onCMInput()"
                        onfocus="this.style.borderColor='#2e7d32';this.style.boxShadow='0 0 0 3px rgba(46,125,50,.12)'"
                        onblur="this.style.boxShadow='none';this.style.borderColor=this.value.trim()?'#a5d6a7':'#e0e0e0'">
                    </textarea>
                    <div id="cmError" style="display:none;color:#e74c3c;font-size:11px;font-weight:700;
                                font-family:'Roboto Condensed',sans-serif;margin-top:5px">
                        ⚠️ Penyebab wajib diisi
                    </div>
                </div>

                {{-- Waktu selesai opsional --}}
                <div style="margin-bottom:16px">
                    <label style="font-family:'Roboto Condensed',sans-serif;font-size:11px;
                                  font-weight:700;text-transform:uppercase;letter-spacing:.4px;
                                  color:#aaa;display:flex;align-items:center;
                                  gap:5px;margin-bottom:6px">
                        ⏰ Waktu Selesai
                        <span style="font-weight:400;color:#ccc">(opsional)</span>
                    </label>
                    <input type="time" id="closeWaktuSelesai" style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;
                                  border-radius:10px;font-family:inherit;font-size:13px;
                                  box-sizing:border-box;outline:none;transition:border-color .2s"
                        onfocus="this.style.borderColor='#888'" onblur="this.style.borderColor='#e0e0e0'">
                </div>

                <div class="save-bar">
                    <button id="btnConfirmClose" onclick="confirmCloseLog()" style="width:100%;padding:14px;border-radius:12px;border:none;
                                   background:#ccc;color:#fff;cursor:not-allowed;
                                   font-family:'Roboto Condensed',sans-serif;
                                   font-size:14px;font-weight:900;letter-spacing:.5px;
                                   text-transform:uppercase;transition:all .2s;opacity:.6" disabled>
                        ✅ Konfirmasi Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        //   Date Mode Switcher                     
        const DATE_MODE_PANELS = ['hari', 'bulan', 'rentang'];

        function setDateMode(mode) {
            DATE_MODE_PANELS.forEach(m => {
                const el = document.getElementById('dmb-' + m);
                if (el) el.style.display = (m === mode) ? '' : 'none';
            });
            document.querySelectorAll('.dmb-btn').forEach((btn, i) => {
                btn.classList.toggle('active', DATE_MODE_PANELS[i] === mode);
            });
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
            window.location = u.toString();
        }

        function applyBulan() {
            const bln = document.getElementById('inputBulanBulan').value;
            const thn = document.getElementById('inputBulanTahun').value;
            const val = thn + '-' + bln;
            if (!bln || !thn) return;
            const u = new URL(window.location);
            u.searchParams.set('mode', 'bulan');
            u.searchParams.set('bulan', val);
            u.searchParams.delete('tanggal');
            u.searchParams.delete('dari');
            u.searchParams.delete('sampai');
            window.location = u.toString();
        }

        function applyRentang() {
            const dari = document.getElementById('inputDari').value;
            const sampai = document.getElementById('inputSampai').value;
            if (!dari || !sampai) { alert('Pilih tanggal awal dan akhir terlebih dahulu.'); return; }
            if (dari > sampai) { alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir.'); return; }
            const u = new URL(window.location);
            u.searchParams.set('mode', 'rentang');
            u.searchParams.set('dari', dari);
            u.searchParams.set('sampai', sampai);
            u.searchParams.delete('tanggal');
            u.searchParams.delete('bulan');
            window.location = u.toString();
        }

        // Legacy alias  - keeps existing code working if called elsewhere
        function onDateChange(val) {
            if (!val) return;
            const u = new URL(window.location);
            u.searchParams.set('mode', 'hari');
            u.searchParams.set('tanggal', val);
            window.location = u.toString();
        }

        //   Problem Log JS (identik logika dari log.blade.php)     
        const LOG_CSRF = '{{ csrf_token() }}';
        const LOG_TANGGAL = '{{ $tanggal }}';
        const LOG_FACTORY = @json($factory);
        const LOG_SHIFT = '{{ $shift }}';

        let activeJenis = null;
        const jenisOptions = @json($jenisList);

        //   Pilih jenis                        
        function selectJenis(jenis) {
            activeJenis = jenis;
            jenisOptions.forEach(j => {
                const btn = document.getElementById(`btn-${j.toLowerCase()}`);
                if (btn) btn.className = 'jenis-btn';
            });
            const activeBtn = document.getElementById(`btn-${jenis.toLowerCase()}`);
            if (activeBtn) {
               activeBtn.className = `jenis-btn sel-${jenis.toLowerCase()}`;
               if (!['machine','material','method'].includes(jenis.toLowerCase())) {
                   activeBtn.style.borderColor = '#1f3c88';
                   activeBtn.style.background = '#eef1fa';
               }
            }
            document.querySelectorAll('.form-section').forEach(s => s.classList.remove('visible'));
            document.getElementById(`section-${jenis.toLowerCase()}`).classList.add('visible');

            const p = jenis.toLowerCase();
            const now = new Date().toTimeString().slice(0, 5);
            document.getElementById(`${p}-mulai`).value = now;
            document.getElementById('saveBtnWrap').style.display = '';
        }

        function toggleSelesai(prefix, val) {
            const wrap = document.getElementById(`${prefix}-selesai-wrap`);
            wrap.style.display = val === 'closed' ? '' : 'none';
            if (val === 'closed') {
                document.getElementById(`${prefix}-selesai`).value = new Date().toTimeString().slice(0, 5);
            }
        }

        //   Submit log baru                      
        async function submitLog() {
            if (!activeJenis) { showToast('Pilih tipe masalah dulu', 'error'); return; }

            const p = activeJenis.toLowerCase();
            const lokasi = document.getElementById(`${p}-lokasi`).value;
            const mulai = document.getElementById(`${p}-mulai`).value;
            const desk = document.getElementById(`${p}-deskripsi`).value.trim();
            const status = document.getElementById(`${p}-status`).value;

            if (!lokasi) { showToast('Pilih lokasi mesin', 'error'); return; }
            if (!mulai) { showToast('Isi waktu mulai', 'error'); return; }
            if (!desk) { showToast('Isi deskripsi masalah', 'error'); return; }

            const selesaiRaw = document.getElementById(`${p}-selesai`)?.value;
            const waktuSelesai = (status === 'closed' && selesaiRaw) ? selesaiRaw : null;

            const btn = document.getElementById('saveBtnMain');
            btn.disabled = true; btn.textContent = '⏳ Menyimpan...';

            try {
                const res = await fetch('/api/logs', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        tanggal: LOG_TANGGAL, factory: LOG_FACTORY, shift: LOG_SHIFT,
                        jenis: activeJenis, lokasi,
                        waktu_mulai: mulai, waktu_selesai: waktuSelesai, status,
                        deskripsi: desk,
                        cause: document.getElementById(`${p}-cause`)?.value || null,
                        countermeasure: document.getElementById(`${p}-cm`)?.value || null,
                        pic: document.getElementById(`${p}-pic`)?.value || null,
                    }),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) { showToast('Gagal simpan: ' + (data?.message?.slice(0, 80) ?? 'error'), 'error'); return; }
                showToast('✅ Log berhasil ditambah!', 'success');
                closeSheet('addLogSheet');
                setTimeout(() => window.location.reload(), 700);
            } catch (e) {
                showToast('Gagal kirim: ' + e.message, 'error');
            } finally {
                btn.disabled = false; btn.textContent = '💾 Simpan Log';
            }
        }

        //   Close log                         
        function closeLog(id) {
            document.getElementById('closeLogId').value = id;
            document.getElementById('closeCM').value = '';
            document.getElementById('closeWaktuSelesai').value = '';
            document.getElementById('cmError').style.display = 'none';

            const btn = document.getElementById('btnConfirmClose');
            btn.disabled = true;
            btn.style.background = '#ccc';
            btn.style.cursor = 'not-allowed';

            const subtitle = document.getElementById('closeLogSubtitle');
            if (subtitle) subtitle.textContent = 'ID Log: ' + id;

            openSheet('closeLogSheet');
        }

        function onCMInput() {
            const cm = document.getElementById('closeCM').value.trim();
            const btn = document.getElementById('btnConfirmClose');
            const err = document.getElementById('cmError');
            if (cm) {
                btn.disabled = false;
                btn.style.background = 'linear-gradient(135deg, #2e7d32, #43a047)';
                btn.style.cursor = 'pointer';
                err.style.display = 'none';
            } else {
                btn.disabled = true;
                btn.style.background = '#ccc';
                btn.style.cursor = 'not-allowed';
            }
        }

        async function confirmCloseLog() {
            const id = document.getElementById('closeLogId').value;
            const cm = document.getElementById('closeCM').value.trim();
            const waktu = document.getElementById('closeWaktuSelesai').value;
            const btn = document.getElementById('btnConfirmClose');

            if (!cm) {
                document.getElementById('cmError').style.display = 'block';
                return;
            }

            btn.disabled = true;
            btn.textContent = '⏳ Menyimpan...';

            try {
                const payload = { countermeasure: cm };
                if (waktu) payload.waktu_selesai = waktu;

                const res = await fetch(`/api/logs/${id}/close`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (res.ok) {
                    showToast('✅ Log diselesaikan', 'success');
                    closeSheet('closeLogSheet');

                    const card = document.getElementById(`logcard-${id}`);
                    const timeEl = document.getElementById(`time-${id}`);
                    const durEl = document.getElementById(`dur-${id}`);
                    const dotEl = document.getElementById(`dot-${id}`);
                    const closeBtn = document.getElementById(`btn-close-${id}`);

                    if (card) card.classList.replace('open', 'closed');
                    if (dotEl) dotEl.className = 'log-status-dot dot-closed';
                    if (timeEl && data.waktu_selesai) {
                        const mulai = timeEl.textContent.split('–')[0].trim();
                        timeEl.textContent = `${mulai} – ${data.waktu_selesai}`;
                    }
                    if (durEl && data.durasi) {
                        durEl.className = 'log-durasi';
                        durEl.textContent = `(${data.durasi})`;
                    }
                    if (closeBtn) {
                        closeBtn.className = 'log-btn log-btn-reopen';
                        closeBtn.id = '';
                        closeBtn.textContent = '🔄 Buka Ulang';
                        closeBtn.onclick = () => reopenLog(id);
                    }
                } else {
                    showToast('Gagal: ' + (data.message || 'error'), 'error');
                }
            } catch (e) {
                showToast('Error: ' + e.message, 'error');
            } finally {
                btn.textContent = '✅ Konfirmasi Selesai';
                btn.disabled = false;
            }
        }

        //   Reopen log                        ─
        async function reopenLog(id) {
            try {
                await fetch(`/api/logs/${id}/reopen`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                });
                showToast('🔄 Log dibuka ulang', 'info');
                setTimeout(() => window.location.reload(), 600);
            } catch (e) { showToast('Gagal', 'error'); }
        }

        //   Edit waktu                        ─
        function editLog(id, mulai, selesai) {
            document.getElementById('editLogId').value = id;
            document.getElementById('editMulai').value = mulai;
            document.getElementById('editSelesai').value = selesai || new Date().toTimeString().slice(0, 5);
            updateDurasiPreview();
            openSheet('editTimeSheet');
        }

        function updateDurasiPreview() {
            const mulai = document.getElementById('editMulai').value;
            const selesai = document.getElementById('editSelesai').value;
            const preview = document.getElementById('durasiPreview');
            if (!mulai || !selesai) { preview.textContent = ''; return; }

            const s = mulai.split(':').map(Number);
            const e = selesai.split(':').map(Number);
            let diff = (e[0] * 60 + e[1]) - (s[0] * 60 + s[1]);
            if (diff < 0) diff += 1440;

            const h = Math.floor(diff / 60);
            const m = diff % 60;
            const dur = h > 0 ? `${h}j ${m}m` : `${m}m`;
            preview.innerHTML = `⏱ Durasi perbaikan: <strong style="color:var(--navy)">${dur}</strong>`;
        }

        async function saveEditTime() {
            const id = document.getElementById('editLogId').value;
            const mulai = document.getElementById('editMulai').value;
            const selesai = document.getElementById('editSelesai').value;
            const btn = document.querySelector('#editTimeSheet .save-btn-big');
            if (btn) { btn.disabled = true; btn.textContent = '⏳ Menyimpan...'; }
            try {
                const res = await fetch(`/api/logs/${id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ waktu_mulai: mulai, waktu_selesai: selesai || null }),
                });
                const data = await res.json();
                closeSheet('editTimeSheet');
                showToast('✅ Waktu diupdate  - ' + (data.durasi ?? ''), 'success');
                setTimeout(() => window.location.reload(), 600);
            } catch (e) {
                showToast('Gagal', 'error');
            } finally {
                if (btn) { btn.disabled = false; btn.textContent = '💾 Simpan Perubahan'; }
            }
        }

        //   Delete log                        ─
        async function deleteLog(id) {
            if (!confirm('Hapus log ini? Tindakan tidak bisa dibatalkan.')) return;
            try {
                await fetch(`/api/logs/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                });
                document.getElementById(`logcard-${id}`)?.remove();
                showToast('🗑️ Log dihapus', 'info');
            } catch (e) { showToast('Gagal', 'error'); }
        }

        //   Live preview durasi saat edit waktu           ─
        document.getElementById('editMulai').addEventListener('input', updateDurasiPreview);
        document.getElementById('editSelesai').addEventListener('input', updateDurasiPreview);

        //   Reset form saat sheet ditutup               
        const origClose = window.closeSheet;
        window.closeSheet = function (id) {
            if (id === 'addLogSheet') {
                activeJenis = null;
                document.querySelectorAll('.jenis-btn').forEach(b => b.className = 'jenis-btn');
                document.querySelectorAll('.form-section').forEach(s => s.classList.remove('visible'));
                document.getElementById('saveBtnWrap').style.display = 'none';
            }
            if (origClose) origClose(id);
        };
    </script>
@endpush