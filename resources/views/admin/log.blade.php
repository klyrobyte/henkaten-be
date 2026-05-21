@extends('layouts.admin')
@section('title', 'Problem Log')

@push('styles')
    <style>
        /* ── Summary bar ─────────────────────────────────────────────── */
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

        /* ── Log cards ───────────────────────────────────────────────── */
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

        /* ── Jenis Selector ─────────────────────────────────────────── */
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

    <div class="date-bar"
        style="display:flex;align-items:center;background:#fff;border-radius:50px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,0.08);gap:12px;">

        {{-- Icon kalender - klik ini untuk buka date picker --}}
        <div class="date-label" onclick="document.getElementById('tanggalHari').showPicker()"
            style="width:36px;height:36px;background:#2E7D32;border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
        </div>

        {{-- Tanggal display --}}
        <span
            style="flex:1;font-family:'Roboto Condensed',sans-serif;font-weight:600;font-size:18px;color:#222;letter-spacing:.5px;">
            {{ \Carbon\Carbon::parse($tanggal)->format('d / m / Y') }}
        </span>

        {{-- Input date tersembunyi - hanya trigger via icon --}}
        <input type="date" id="tanggalHari" value="{{ $tanggal }}" onchange="onDateChange(this.value)"
            style="position:absolute;opacity:0;pointer-events:none;width:0;height:0;">

        {{-- Factory badge --}}
        <button onclick="showFactoryPicker()" style="background:#2E7D32;border:none;border-radius:20px;padding:7px 18px;color:#fff;
                           font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:12px;
                           letter-spacing:1px;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;
                           transition:all .2s ease;" onmouseover="this.style.filter='brightness(1.15)'"
            onmouseout="this.style.filter='brightness(1)'">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff"
                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="15" rx="1" />
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                <line x1="12" y1="12" x2="12" y2="12" />
                <line x1="8" y1="12" x2="8" y2="12" />
                <line x1="16" y1="12" x2="16" y2="12" />
                <line x1="8" y1="16" x2="8" y2="16" />
                <line x1="16" y1="16" x2="16" y2="16" />
                <line x1="12" y1="16" x2="12" y2="16" />
            </svg>
            {{ strtoupper(\App\Models\Factory::where('name', session('factory', 'Factory 2'))->value('short_label') ?? session('factory', 'Factory 2')) }}
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

    {{-- Summary bar --}}
    <div class="log-summary-bar">
        <div class="lsb-item">
            <div class="lsb-val" id="cntAll">{{ $logs->count() }}</div>
            <div class="lsb-lbl">Total</div>
        </div>
        <div class="lsb-item">
            <div class="lsb-val" style="color:#e74c3c" id="cntOpen">{{ $logs->where('status', 'open')->count() }}</div>
            <div class="lsb-lbl">Open</div>
        </div>
        <div class="lsb-item">
            <div class="lsb-val" style="color:#1f3c88" id="cntMachine">{{ $logs->where('jenis', 'Machine')->count() }}</div>
            <div class="lsb-lbl">Machine</div>
        </div>
        <div class="lsb-item">
            <div class="lsb-val" style="color:#f39c12" id="cntMaterial">{{ $logs->where('jenis', 'Material')->count() }}
            </div>
            <div class="lsb-lbl">Material</div>
        </div>
        <div class="lsb-item">
            <div class="lsb-val" style="color:#2e7d32" id="cntMethod">{{ $logs->where('jenis', 'Method')->count() }}</div>
            <div class="lsb-lbl">Method</div>
        </div>
    </div>

    {{-- Action bar --}}
    <div style="display:flex;justify-content:flex-end;margin-bottom:14px;gap:8px">
        <a href="{{ route('admin.reports.export', ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift]) }}"
            style="display:flex;align-items:center;gap:5px;font-size:12px;padding:8px 14px;border-radius:8px;
                      background:#f5f5f5;color:#666;text-decoration:none;border:1.5px solid #ddd;
                      font-family:'Roboto Condensed',sans-serif;font-weight:700">
            📤 Export CSV
        </a>
        <button onclick="openSheet('addLogSheet')" style="display:flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;
                           background:var(--orange);color:#fff;border:none;font-weight:800;cursor:pointer;
                           font-size:13px;font-family:'Roboto Condensed',sans-serif;letter-spacing:.3px;
                           box-shadow:0 3px 10px rgba(230,81,0,.3)">
            ➕ Tambah Log
        </button>
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
                    @if($log->departemen_perbaikan)
                        <div class="log-meta">🏢 <strong>Dept. Perbaikan:</strong> {{ $log->departemen_perbaikan }}</div>
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

    {{-- ══ MODAL: Tambah Log ══════════════════════════════════════════ --}}
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
                <div class="jenis-selector">
                    <button type="button" class="jenis-btn" id="btn-machine" onclick="selectJenis('Machine')">
                        <span class="jb-icon">⚙️</span><span class="jb-lbl">Machine</span>
                    </button>
                    <button type="button" class="jenis-btn" id="btn-material" onclick="selectJenis('Material')">
                        <span class="jb-icon">📦</span><span class="jb-lbl">Material</span>
                    </button>
                    <button type="button" class="jenis-btn" id="btn-method" onclick="selectJenis('Method')">
                        <span class="jb-icon">📋</span><span class="jb-lbl">Method</span>
                    </button>
                </div>

                {{-- FORM: MACHINE --}}
                <div class="form-section" id="section-machine">
                    <div class="section-header-badge shb-machine">⚙️ Machine Problem</div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Mesin / Lokasi *</label>
                            <select id="m-lokasi">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesinList as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Waktu Mulai *</label>
                            <input type="time" id="m-mulai">
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Deskripsi Kerusakan *</label>
                        <textarea id="m-deskripsi" rows="2" placeholder="Contoh: MC mati mendadak, bunyi abnormal..."
                            style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                            onfocus="this.style.borderColor='#1f3c88'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>Root Cause</label><input type="text" id="m-cause"
                                placeholder="Contoh: bearing aus, sensor error..."></div>
                        <div class="field-group"><label>Countermeasure</label><input type="text" id="m-cm"
                                placeholder="Contoh: ganti bearing, reset PLC..."></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Departemen Perbaikan</label>
                            <select id="m-dept">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($repairDepartments as $rd)
                                    <option value="{{ $rd->name }}">{{ $rd->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group"><label>Teknisi / PIC</label><input type="text" id="m-pic"
                                placeholder="Nama teknisi yang handle"></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Status</label>
                            <select id="m-status" onchange="toggleSelesai('m', this.value)">
                                <option value="open">Open - belum selesai</option>
                                <option value="closed">Closed - sudah selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group" id="m-selesai-wrap" style="display:none">
                        <label>Waktu Selesai</label>
                        <input type="time" id="m-selesai">
                    </div>
                </div>

                {{-- FORM: MATERIAL --}}
                <div class="form-section" id="section-material">
                    <div class="section-header-badge shb-material">📦 Material Problem</div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Mesin / Lokasi *</label>
                            <select id="mat-lokasi">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesinList as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Waktu Mulai *</label>
                            <input type="time" id="mat-mulai">
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Deskripsi Masalah Material *</label>
                        <textarea id="mat-deskripsi" rows="2"
                            placeholder="Contoh: material short shot, warna tidak sesuai..."
                            style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                            onfocus="this.style.borderColor='#f39c12'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>No. Lot / Batch</label><input type="text" id="mat-cause"
                                placeholder="No. lot material bermasalah"></div>
                        <div class="field-group"><label>Countermeasure</label><input type="text" id="mat-cm"
                                placeholder="Contoh: ganti lot, kembalikan ke gudang..."></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Departemen Perbaikan</label>
                            <select id="mat-dept">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($repairDepartments as $rd)
                                    <option value="{{ $rd->name }}">{{ $rd->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group"><label>PIC</label><input type="text" id="mat-pic"
                                placeholder="Nama penanggung jawab"></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Status</label>
                            <select id="mat-status" onchange="toggleSelesai('mat', this.value)">
                                <option value="open">Open - belum selesai</option>
                                <option value="closed">Closed - sudah selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group" id="mat-selesai-wrap" style="display:none">
                        <label>Waktu Selesai</label>
                        <input type="time" id="mat-selesai">
                    </div>
                </div>

                {{-- FORM: METHOD --}}
                <div class="form-section" id="section-method">
                    <div class="section-header-badge shb-method">📋 Method Problem</div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Mesin / Lokasi *</label>
                            <select id="met-lokasi">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesinList as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Waktu Mulai *</label>
                            <input type="time" id="met-mulai">
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Deskripsi Penyimpangan *</label>
                        <textarea id="met-deskripsi" rows="2"
                            placeholder="Contoh: setting tidak sesuai standar, proses tidak mengikuti SOP..."
                            style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                            onfocus="this.style.borderColor='#2e7d32'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>Standar yang Dilanggar</label><input type="text" id="met-cause"
                                placeholder="Contoh: suhu resin, cycle time SOP..."></div>
                        <div class="field-group"><label>Tindakan Koreksi</label><input type="text" id="met-cm"
                                placeholder="Contoh: re-training, update SOP..."></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Departemen Perbaikan</label>
                            <select id="met-dept">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($repairDepartments as $rd)
                                    <option value="{{ $rd->name }}">{{ $rd->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group"><label>PIC</label><input type="text" id="met-pic"
                                placeholder="Nama penanggung jawab"></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group">
                            <label>Status</label>
                            <select id="met-status" onchange="toggleSelesai('met', this.value)">
                                <option value="open">Open - belum selesai</option>
                                <option value="closed">Closed - sudah selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group" id="met-selesai-wrap" style="display:none">
                        <label>Waktu Selesai</label>
                        <input type="time" id="met-selesai">
                    </div>
                </div>

                <div class="save-bar" id="saveBtnWrap" style="display:none">
                    <button class="save-btn-big" id="saveBtnMain" onclick="submitLog()">💾 Simpan Log</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: Edit Waktu ══════════════════════════════════════════ --}}
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

    {{-- ══ MODAL: Close Log (Wajib Countermeasure) ════════════════════ --}}
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

                {{-- Countermeasure - WAJIB --}}
                <div style="margin-bottom:14px">
                    <label style="font-family:'Roboto Condensed',sans-serif;font-size:11px;
                                      font-weight:900;text-transform:uppercase;letter-spacing:.5px;
                                      color:#2e7d32;display:flex;align-items:center;
                                      gap:6px;margin-bottom:7px">
                        🔧 Countermeasure
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
                        ⚠️ Countermeasure wajib diisi
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
        const TANGGAL = '{{ $tanggal }}';
        const FACTORY = @json($factory);
        const SHIFT = '{{ $shift }}';
        const CSRF = '{{ csrf_token() }}';

        let activeJenis = null;
        const PREFIX = { Machine: 'm', Material: 'mat', Method: 'met' };

        function updateQS(key, val) {
            const u = new URL(window.location);
            u.searchParams.set(key, val);
            return u.toString();
        }

        // ── Pilih jenis ──────────────────────────────────────────────
        function selectJenis(jenis) {
            activeJenis = jenis;
            ['Machine', 'Material', 'Method'].forEach(j => {
                document.getElementById(`btn-${j.toLowerCase()}`).className = 'jenis-btn';
            });
            document.getElementById(`btn-${jenis.toLowerCase()}`).className = `jenis-btn sel-${jenis.toLowerCase()}`;
            document.querySelectorAll('.form-section').forEach(s => s.classList.remove('visible'));
            document.getElementById(`section-${jenis.toLowerCase()}`).classList.add('visible');

            const now = new Date().toTimeString().slice(0, 5);
            document.getElementById(`${PREFIX[jenis]}-mulai`).value = now;
            document.getElementById('saveBtnWrap').style.display = '';
        }

        function toggleSelesai(prefix, val) {
            const wrap = document.getElementById(`${prefix}-selesai-wrap`);
            wrap.style.display = val === 'closed' ? '' : 'none';
            if (val === 'closed') {
                // Auto-isi waktu selesai dengan jam sekarang
                document.getElementById(`${prefix}-selesai`).value = new Date().toTimeString().slice(0, 5);
            }
        }

        // ── Submit log baru ──────────────────────────────────────────
        async function submitLog() {
            if (!activeJenis) { showToast('Pilih tipe masalah dulu', 'error'); return; }

            const p = PREFIX[activeJenis];
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
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        tanggal: TANGGAL, factory: FACTORY, shift: SHIFT,
                        jenis: activeJenis, lokasi,
                        waktu_mulai: mulai, waktu_selesai: waktuSelesai, status,
                        deskripsi: desk,
                        cause: document.getElementById(`${p}-cause`)?.value || null,
                        countermeasure: document.getElementById(`${p}-cm`)?.value || null,
                        pic: document.getElementById(`${p}-pic`)?.value || null,
                        departemen_perbaikan: document.getElementById(`${p}-dept`)?.value || null,
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

        // ── Close log: tampilkan jam selesai + durasi langsung di card ──
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
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
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

                    if (card) { card.classList.replace('open', 'closed'); }
                    if (dotEl) { dotEl.className = 'log-status-dot dot-closed'; }
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

        async function reopenLog(id) {
            try {
                await fetch(`/api/logs/${id}/reopen`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                });
                showToast('🔄 Log dibuka ulang', 'info');
                setTimeout(() => window.location.reload(), 600);
            } catch (e) { showToast('Gagal', 'error'); }
        }

        // ── Edit waktu: auto-isi waktu selesai sekarang jika kosong ──
        function editLog(id, mulai, selesai) {
            document.getElementById('editLogId').value = id;
            document.getElementById('editMulai').value = mulai;
            // Jika belum ada waktu selesai, isi dengan jam sekarang sebagai saran
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
            if (diff < 0) diff += 1440; // lintas tengah malam

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
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
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

        async function deleteLog(id) {
            if (!confirm('Hapus log ini? Tindakan tidak bisa dibatalkan.')) return;
            try {
                await fetch(`/api/logs/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                });
                document.getElementById(`logcard-${id}`)?.remove();
                showToast('🗑️ Log dihapus', 'info');
            } catch (e) { showToast('Gagal', 'error'); }
        }

        // ── Live preview durasi saat edit waktu ─────────────────────
        document.getElementById('editMulai').addEventListener('input', updateDurasiPreview);
        document.getElementById('editSelesai').addEventListener('input', updateDurasiPreview);

        // ── Reset form saat sheet ditutup ────────────────────────────
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