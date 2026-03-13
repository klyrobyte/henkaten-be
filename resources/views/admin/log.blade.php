@extends('layouts.admin')
@section('title', 'Problem Log')

@push('styles')
<style>
/* ── Log cards ───────────────────────────────────────────────── */
.log-card {
    background:#fff; border-radius:14px; border:1.5px solid #eee;
    box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:10px; overflow:hidden;
    transition:box-shadow .2s, border-color .2s;
}
.log-card.open   { border-left:4px solid #e74c3c; }
.log-card.closed { border-left:4px solid #4caf50; opacity:.85; }
.log-card-top {
    display:flex; align-items:center; gap:8px;
    padding:10px 12px 6px; flex-wrap:wrap;
}
.log-badge {
    font-family:'Roboto Condensed',sans-serif; font-size:10px; font-weight:900;
    padding:3px 9px; border-radius:6px; text-transform:uppercase;
    letter-spacing:.5px; color:#fff; flex-shrink:0;
}
.log-machine  { background:#1f3c88; }
.log-material { background:#f39c12; }
.log-method   { background:#2e7d32; }
.log-status-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.dot-open   { background:#e74c3c; animation:pulse-red 1.5s infinite; }
.dot-closed { background:#4caf50; }
@keyframes pulse-red {
    0%,100% { box-shadow:0 0 0 0 rgba(231,76,60,.5); }
    50%      { box-shadow:0 0 0 5px rgba(231,76,60,0); }
}
.log-time {
    font-family:'Roboto Condensed',sans-serif; font-size:11px; color:#888; font-weight:600;
}
.log-durasi { font-family:'Roboto Condensed',sans-serif; font-size:10px; color:#aaa; margin-left:auto; }
.log-durasi.ongoing { color:#e74c3c; font-weight:800; animation:blink .9s step-end infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
.log-card-body { padding:4px 12px 8px; }
.log-lokasi {
    font-family:'Roboto Condensed',sans-serif; font-size:11px;
    font-weight:800; color:var(--navy); margin-bottom:3px;
}
.log-desc { font-size:12px; color:#444; line-height:1.5; }
.log-meta  { font-size:11px; color:#777; margin-top:3px; line-height:1.4; }
.log-card-actions {
    display:flex; gap:6px; padding:8px 12px 10px; border-top:1px solid #f5f5f5; flex-wrap:wrap;
}
.log-btn {
    padding:6px 12px; border-radius:8px; border:1.5px solid #e0e0e0;
    background:#f7f7f7; font-size:11px; font-weight:700;
    font-family:'Roboto Condensed',sans-serif; cursor:pointer;
    transition:all .15s; color:#555;
}
.log-btn:hover { border-color:#bbb; background:#eee; }
.log-btn-close  { border-color:#a5d6a7; color:#2e7d32; background:#f1f8e9; }
.log-btn-close:hover  { background:#c8e6c9; }
.log-btn-reopen { border-color:#90caf9; color:#1565c0; background:#e3f2fd; }
.log-btn-reopen:hover { background:#bbdefb; }
.log-btn-del { border-color:#ffcdd2; color:#c62828; background:#fff5f5; margin-left:auto; }
.log-btn-del:hover { background:#ffcdd2; }

/* ── Summary bar ────────────────────────────────────────────── */
.log-summary-bar { display:flex; gap:8px; margin-bottom:14px; }
.lsb-item {
    flex:1; background:#fff; border-radius:12px; border:1.5px solid #eee;
    padding:10px 8px; text-align:center; box-shadow:0 1px 6px rgba(0,0,0,.05);
}
.lsb-val {
    font-family:'Orbitron',sans-serif; font-size:20px; font-weight:700;
    line-height:1; color:var(--navy);
}
.lsb-lbl {
    font-family:'Roboto Condensed',sans-serif; font-size:10px; font-weight:700;
    text-transform:uppercase; letter-spacing:.5px; color:#aaa; margin-top:3px;
}

/* ── Jenis Selector ─────────────────────────────────────────── */
.jenis-selector { display:flex; gap:8px; margin-bottom:16px; }
.jenis-btn {
    flex:1; padding:12px 8px; border-radius:12px; border:2px solid #e0e0e0;
    background:#f9f9f9; cursor:pointer; text-align:center;
    transition:all .18s; font-family:'Roboto Condensed',sans-serif;
}
.jenis-btn .jb-icon { font-size:22px; display:block; margin-bottom:4px; }
.jenis-btn .jb-lbl  { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.4px; color:#888; }
.jenis-btn.sel-machine  { border-color:#1f3c88; background:#eef1fa; }
.jenis-btn.sel-machine  .jb-lbl { color:#1f3c88; }
.jenis-btn.sel-material { border-color:#f39c12; background:#fff8ec; }
.jenis-btn.sel-material .jb-lbl { color:#f39c12; }
.jenis-btn.sel-method   { border-color:#2e7d32; background:#f1f8e9; }
.jenis-btn.sel-method   .jb-lbl { color:#2e7d32; }
.form-section { display:none; animation:fadeIn .2s; }
.form-section.visible { display:block; }
@keyframes fadeIn { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:none} }
.section-header-badge {
    display:inline-flex; align-items:center; gap:6px; padding:5px 12px;
    border-radius:8px; font-family:'Roboto Condensed',sans-serif;
    font-size:11px; font-weight:900; text-transform:uppercase;
    letter-spacing:.5px; margin-bottom:12px;
}
.shb-machine  { background:#eef1fa; color:#1f3c88; border:1.5px solid #c5cae9; }
.shb-material { background:#fff8ec; color:#e67e22; border:1.5px solid #ffe0b2; }
.shb-method   { background:#f1f8e9; color:#2e7d32; border:1.5px solid #c8e6c9; }
.form-divider {
    display:flex; align-items:center; gap:10px; margin:14px 0 10px;
    font-family:'Roboto Condensed',sans-serif; font-size:10px;
    font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#bbb;
}
.form-divider::before, .form-divider::after { content:''; flex:1; height:1px; background:#eee; }
.ql-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 16px 12px; border-bottom:1.5px solid #f0f0f0;
}
.ql-header-left { display:flex; align-items:center; gap:10px; }
.ql-header-icon {
    width:34px; height:34px; border-radius:10px;
    background:linear-gradient(135deg,#e65100,#ff7043);
    color:#fff; font-size:20px; font-weight:900;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; box-shadow:0 3px 8px rgba(230,81,0,.3);
}
.ql-header-title {
    font-family:'Roboto Condensed',sans-serif; font-size:14px;
    font-weight:800; color:#222; text-transform:uppercase; letter-spacing:.4px;
}
.ql-header-sub { font-size:11px; color:#aaa; margin-top:1px; }
</style>
@endpush

@section('content')

<div class="date-bar">
    <div class="date-label">📅</div>
    <input type="date" id="tanggalHari" value="{{ $tanggal }}"
           onchange="window.location.href=updateQS('tanggal',this.value)">
    <button class="factory-select-btn" onclick="showFactoryPicker()">🏭</button>
</div>
<div class="shift-toggle-bar">
    <button class="shift-toggle-btn {{ $shift==='A'?'active':'' }}" onclick="switchShift('A')">SHIFT A</button>
    <button class="shift-toggle-btn {{ $shift==='B'?'active':'' }}" onclick="switchShift('B')">SHIFT B</button>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <div style="font-family:'Orbitron',sans-serif;font-size:13px;font-weight:700;color:var(--navy)">
        PROBLEM LOG 3M
    </div>
    <button class="save-btn-big"
            style="padding:8px 16px;font-size:12px;border-radius:10px"
            onclick="openSheet('addLogSheet')">
        ➕ Tambah Log
    </button>
</div>

{{-- Summary bar --}}
<div class="log-summary-bar">
    <div class="lsb-item">
        <div class="lsb-val" id="cntAll">{{ $logs->count() }}</div>
        <div class="lsb-lbl">Total</div>
    </div>
    <div class="lsb-item">
        <div class="lsb-val" style="color:#e74c3c" id="cntOpen">{{ $logs->where('status','open')->count() }}</div>
        <div class="lsb-lbl">Open</div>
    </div>
    <div class="lsb-item">
        <div class="lsb-val" style="color:#1f3c88" id="cntMachine">{{ $logs->where('jenis','Machine')->count() }}</div>
        <div class="lsb-lbl">Machine</div>
    </div>
    <div class="lsb-item">
        <div class="lsb-val" style="color:#f39c12" id="cntMaterial">{{ $logs->where('jenis','Material')->count() }}</div>
        <div class="lsb-lbl">Material</div>
    </div>
    <div class="lsb-item">
        <div class="lsb-val" style="color:#2e7d32" id="cntMethod">{{ $logs->where('jenis','Method')->count() }}</div>
        <div class="lsb-lbl">Method</div>
    </div>
</div>

{{-- Log list --}}
<div id="logList" style="margin-bottom:80px">
    @forelse($logs as $log)
        <div class="log-card {{ $log->status }}" id="logcard-{{ $log->id }}">
            <div class="log-card-top">
                <span class="log-badge log-{{ strtolower($log->jenis) }}">{{ $log->jenis }}</span>
                <span class="log-status-dot {{ $log->status==='open'?'dot-open':'dot-closed' }}"
                      id="dot-{{ $log->id }}"></span>
                <span class="log-time" id="time-{{ $log->id }}">
                    {{ substr($log->waktu_mulai,0,5) }}
                    {{ $log->waktu_selesai ? ' – '.substr($log->waktu_selesai,0,5) : ' – …' }}
                </span>
                @if($log->durasi)
                    <span class="log-durasi" id="dur-{{ $log->id }}">({{ $log->durasi }})</span>
                @elseif($log->status==='open')
                    <span class="log-durasi ongoing" id="dur-{{ $log->id }}">ON GOING</span>
                @else
                    <span class="log-durasi" id="dur-{{ $log->id }}"></span>
                @endif
            </div>
            <div class="log-card-body">
                <div class="log-lokasi">📍 {{ $log->lokasi }}</div>
                <div class="log-desc">{{ $log->deskripsi }}</div>
                @if($log->cause)
                    <div class="log-meta">🔍 <strong>Cause:</strong> {{ $log->cause }}</div>
                @endif
                @if($log->countermeasure)
                    <div class="log-meta" data-cm="1">🔧 <strong>CM:</strong> {{ $log->countermeasure }}</div>
                @endif
                @if($log->pic)
                    <div class="log-meta">👤 <strong>PIC:</strong> {{ $log->pic }}</div>
                @endif
            </div>
            <div class="log-card-actions">
                @if($log->status==='open')
                    {{-- Tombol Selesai: buka modal CM wajib --}}
                    <button class="log-btn log-btn-close" id="btn-close-{{ $log->id }}"
                            onclick="closeLog({{ $log->id }})">✅ Selesai</button>
                @else
                    {{-- Tombol Buka Ulang: langsung reopen tanpa modal --}}
                    <button class="log-btn log-btn-reopen" id="btn-reopen-{{ $log->id }}"
                            onclick="reopenLog({{ $log->id }})">🔄 Buka Ulang</button>
                @endif
                <button class="log-btn log-btn-del" onclick="deleteLog({{ $log->id }})">🗑️ Hapus</button>
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

            {{-- MACHINE --}}
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
                        style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;
                               font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                        onfocus="this.style.borderColor='#1f3c88'"
                        onblur="this.style.borderColor='#e0e0e0'"></textarea>
                </div>
                <div class="form-row">
                    <div class="field-group">
                        <label>Root Cause</label>
                        <input type="text" id="m-cause" placeholder="Contoh: bearing aus, sensor error...">
                    </div>
                    <div class="field-group">
                        <label id="lbl-m-cm">Countermeasure / Solusi</label>
                        <input type="text" id="m-cm" placeholder="Contoh: ganti bearing, reset PLC...">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field-group">
                        <label>Teknisi / PIC</label>
                        <input type="text" id="m-pic" placeholder="Nama teknisi yang handle">
                    </div>
                    <div class="field-group">
                        <label>Status</label>
                        <select id="m-status" onchange="toggleCMRequired('m', this.value)">
                            <option value="open">Open — belum selesai</option>
                            <option value="closed">Closed — sudah selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- MATERIAL --}}
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
                    <textarea id="mat-deskripsi" rows="2" placeholder="Contoh: material short shot, warna tidak sesuai..."
                        style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;
                               font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                        onfocus="this.style.borderColor='#f39c12'"
                        onblur="this.style.borderColor='#e0e0e0'"></textarea>
                </div>
                <div class="form-row">
                    <div class="field-group">
                        <label>No. Lot / Batch</label>
                        <input type="text" id="mat-cause" placeholder="No. lot material bermasalah">
                    </div>
                    <div class="field-group">
                        <label id="lbl-mat-cm">Countermeasure / Solusi</label>
                        <input type="text" id="mat-cm" placeholder="Contoh: ganti lot, kembalikan ke gudang...">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field-group">
                        <label>PIC</label>
                        <input type="text" id="mat-pic" placeholder="Nama penanggung jawab">
                    </div>
                    <div class="field-group">
                        <label>Status</label>
                        <select id="mat-status" onchange="toggleCMRequired('mat', this.value)">
                            <option value="open">Open — belum selesai</option>
                            <option value="closed">Closed — sudah selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- METHOD --}}
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
                    <textarea id="met-deskripsi" rows="2" placeholder="Contoh: setting tidak sesuai standar..."
                        style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;
                               font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                        onfocus="this.style.borderColor='#2e7d32'"
                        onblur="this.style.borderColor='#e0e0e0'"></textarea>
                </div>
                <div class="form-row">
                    <div class="field-group">
                        <label>Standar yang Dilanggar</label>
                        <input type="text" id="met-cause" placeholder="Contoh: suhu resin, cycle time SOP...">
                    </div>
                    <div class="field-group">
                        <label id="lbl-met-cm">Countermeasure / Solusi</label>
                        <input type="text" id="met-cm" placeholder="Contoh: re-training, update SOP...">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field-group">
                        <label>PIC</label>
                        <input type="text" id="met-pic" placeholder="Nama penanggung jawab">
                    </div>
                    <div class="field-group">
                        <label>Status</label>
                        <select id="met-status" onchange="toggleCMRequired('met', this.value)">
                            <option value="open">Open — belum selesai</option>
                            <option value="closed">Closed — sudah selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="save-bar" id="saveBtnWrap" style="display:none">
                <button class="save-btn-big" id="saveBtnMain" onclick="submitLog()">💾 Simpan Log</button>
            </div>
        </div>
    </div>
</div>

{{-- ══ MODAL: Selesai / Close Log — Solusi WAJIB ═════════════════ --}}
<div class="modal-overlay" id="closeLogSheet">
    <div class="modal-sheet" style="max-height:400px">
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
                                font-weight:900;color:#222;text-transform:uppercase;letter-spacing:.5px">
                        Selesaikan Log
                    </div>
                    <div id="closeLogSubtitle" style="font-size:11px;color:#aaa;margin-top:1px"></div>
                </div>
            </div>
            <button class="modal-sheet-close" onclick="closeSheet('closeLogSheet')">✕</button>
        </div>
        <div class="modal-sheet-body">
            <input type="hidden" id="closeLogId">

            {{-- Countermeasure / Solusi — WAJIB --}}
            <div style="margin-bottom:14px">
                <label style="font-family:'Roboto Condensed',sans-serif;font-size:11px;
                              font-weight:900;text-transform:uppercase;letter-spacing:.5px;
                              color:#2e7d32;display:flex;align-items:center;gap:6px;margin-bottom:7px">
                    🔧 Solusi / Countermeasure
                    <span style="background:#e74c3c;color:#fff;font-size:9px;
                                 padding:2px 7px;border-radius:4px;font-weight:900">WAJIB</span>
                </label>
                <textarea id="closeCM" rows="4"
                          placeholder="Tindakan yang dilakukan untuk menyelesaikan masalah ini..."
                          style="width:100%;padding:11px 12px;border:2px solid #e0e0e0;
                                 border-radius:10px;font-family:inherit;font-size:13px;
                                 resize:none;box-sizing:border-box;outline:none;
                                 transition:border-color .2s,box-shadow .2s;line-height:1.5"
                          oninput="onCMInput()"
                          onfocus="this.style.borderColor='#2e7d32';this.style.boxShadow='0 0 0 3px rgba(46,125,50,.12)'"
                          onblur="this.style.boxShadow='none';this.style.borderColor=this.value.trim()?'#a5d6a7':'#e0e0e0'">
                </textarea>
                <div id="cmError"
                     style="display:none;color:#e74c3c;font-size:11px;font-weight:700;
                            font-family:'Roboto Condensed',sans-serif;margin-top:5px">
                    ⚠️ Solusi / Countermeasure wajib diisi sebelum menutup log
                </div>
            </div>

            {{-- Waktu selesai opsional --}}
            <div style="margin-bottom:16px">
                <label style="font-family:'Roboto Condensed',sans-serif;font-size:11px;
                              font-weight:700;text-transform:uppercase;letter-spacing:.4px;
                              color:#aaa;display:flex;align-items:center;gap:5px;margin-bottom:6px">
                    ⏰ Waktu Selesai
                    <span style="font-weight:400;color:#ccc">(opsional — default: sekarang)</span>
                </label>
                <input type="time" id="closeWaktuSelesai"
                       style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;
                              border-radius:10px;font-family:inherit;font-size:13px;
                              box-sizing:border-box;outline:none;transition:border-color .2s"
                       onfocus="this.style.borderColor='#888'"
                       onblur="this.style.borderColor='#e0e0e0'">
            </div>

            <div class="save-bar">
                <button id="btnConfirmClose"
                        onclick="confirmCloseLog()"
                        style="width:100%;padding:14px;border-radius:12px;border:none;
                               background:#ccc;color:#fff;cursor:not-allowed;
                               font-family:'Roboto Condensed',sans-serif;font-size:14px;
                               font-weight:900;letter-spacing:.5px;text-transform:uppercase;
                               transition:all .2s;opacity:.6"
                        disabled>
                    ✅ Konfirmasi Selesai
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateQS(key, val) {
    const u = new URL(window.location);
    u.searchParams.set(key, val);
    return u.toString();
}

const LOG_CSRF    = '{{ csrf_token() }}';
const LOG_TANGGAL = '{{ $tanggal }}';
const LOG_FACTORY = @json($factory);
const LOG_SHIFT   = '{{ $shift }}';

let activeJenis = null;
const PREFIX    = { Machine: 'm', Material: 'mat', Method: 'met' };

// ── Pilih jenis ──────────────────────────────────────────────
function selectJenis(jenis) {
    activeJenis = jenis;
    ['Machine','Material','Method'].forEach(j => {
        document.getElementById(`btn-${j.toLowerCase()}`).className = 'jenis-btn';
    });
    document.getElementById(`btn-${jenis.toLowerCase()}`).className = `jenis-btn sel-${jenis.toLowerCase()}`;
    document.querySelectorAll('.form-section').forEach(s => s.classList.remove('visible'));
    document.getElementById(`section-${jenis.toLowerCase()}`).classList.add('visible');
    document.getElementById(`${PREFIX[jenis]}-mulai`).value = new Date().toTimeString().slice(0,5);
    document.getElementById('saveBtnWrap').style.display = '';
}

// Highlight label CM merah kalau status dipilih closed dari form tambah
function toggleCMRequired(prefix, val) {
    const input = document.getElementById(`${prefix}-cm`);
    const label = document.getElementById(`lbl-${prefix}-cm`);
    if (!input) return;
    if (val === 'closed') {
        input.style.borderColor = '#f39c12';
        input.style.background  = '#fffde7';
        if (label) { label.style.color = '#e65100'; label.textContent = 'Solusi / Countermeasure ⚠️ WAJIB'; }
    } else {
        input.style.borderColor = '';
        input.style.background  = '';
        if (label) { label.style.color = ''; label.textContent = 'Countermeasure / Solusi'; }
    }
}

// ── Submit log baru ──────────────────────────────────────────
async function submitLog() {
    if (!activeJenis) { showToast('Pilih tipe masalah dulu', 'error'); return; }

    const p      = PREFIX[activeJenis];
    const lokasi = document.getElementById(`${p}-lokasi`).value;
    const mulai  = document.getElementById(`${p}-mulai`).value;
    const desk   = document.getElementById(`${p}-deskripsi`).value.trim();
    const status = document.getElementById(`${p}-status`).value;
    const cm     = document.getElementById(`${p}-cm`)?.value.trim() ?? '';

    if (!lokasi) { showToast('Pilih lokasi mesin', 'error'); return; }
    if (!mulai)  { showToast('Isi waktu mulai', 'error');    return; }
    if (!desk)   { showToast('Isi deskripsi masalah', 'error'); return; }

    // Jika status closed langsung dari form tambah → CM wajib
    if (status === 'closed' && !cm) {
        showToast('⚠️ Solusi/Countermeasure wajib diisi untuk status Closed!', 'error');
        document.getElementById(`${p}-cm`)?.focus();
        return;
    }

    const btn = document.getElementById('saveBtnMain');
    btn.disabled = true; btn.textContent = '⏳ Menyimpan...';

    try {
        const res = await fetch('/admin/logs', {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':LOG_CSRF, 'Accept':'application/json' },
            body   : JSON.stringify({
                tanggal: LOG_TANGGAL, factory: LOG_FACTORY, shift: LOG_SHIFT,
                jenis: activeJenis, lokasi, waktu_mulai: mulai, status,
                deskripsi: desk,
                cause:          document.getElementById(`${p}-cause`)?.value || null,
                countermeasure: cm || null,
                pic:            document.getElementById(`${p}-pic`)?.value   || null,
            }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) { showToast('Gagal simpan: ' + (data?.message?.slice(0,80) ?? 'error'), 'error'); return; }
        showToast('✅ Log berhasil ditambah!', 'success');
        closeSheet('addLogSheet');
        setTimeout(() => window.location.reload(), 700);
    } catch (e) {
        showToast('Gagal kirim: ' + e.message, 'error');
    } finally {
        btn.disabled = false; btn.textContent = '💾 Simpan Log';
    }
}

// ══════════════════════════════════════════════════════════════
// CLOSE LOG — buka modal, wajib isi solusi dulu
// ══════════════════════════════════════════════════════════════
let _closeId = null;

function closeLog(id) {
    _closeId = id;

    // Ambil info dari card untuk subtitle modal
    const card   = document.getElementById(`logcard-${id}`);
    const jenis  = card?.querySelector('.log-badge')?.textContent?.trim() ?? '';
    const lokasi = card?.querySelector('.log-lokasi')?.textContent?.replace('📍','').trim() ?? '';

    document.getElementById('closeLogId').value             = id;
    document.getElementById('closeLogSubtitle').textContent = `${jenis} · ${lokasi}`;
    document.getElementById('closeWaktuSelesai').value      = new Date().toTimeString().slice(0,5);

    // Reset field CM
    const cm = document.getElementById('closeCM');
    cm.value             = '';
    cm.style.borderColor = '#e0e0e0';
    cm.style.boxShadow   = 'none';
    document.getElementById('cmError').style.display = 'none';
    _setCloseBtn(false);

    openSheet('closeLogSheet');
    setTimeout(() => cm.focus(), 350);
}

// Live enable/disable tombol konfirmasi
function onCMInput() {
    const val = document.getElementById('closeCM').value.trim();
    document.getElementById('cmError').style.display = 'none';
    _setCloseBtn(!!val);
}

function _setCloseBtn(enabled) {
    const btn = document.getElementById('btnConfirmClose');
    if (!btn) return;
    btn.disabled         = !enabled;
    btn.style.cursor     = enabled ? 'pointer'    : 'not-allowed';
    btn.style.opacity    = enabled ? '1'          : '.6';
    btn.style.background = enabled
        ? 'linear-gradient(135deg,#2e7d32,#43a047)'
        : '#ccc';
    btn.style.boxShadow  = enabled ? '0 4px 12px rgba(46,125,50,.4)' : 'none';
}

async function confirmCloseLog() {
    const cm = document.getElementById('closeCM').value.trim();
    if (!cm) {
        document.getElementById('cmError').style.display = '';
        document.getElementById('closeCM').style.borderColor = '#e74c3c';
        showToast('⚠️ Solusi wajib diisi sebelum menutup log!', 'error');
        return;
    }

    const id           = _closeId;
    const waktuSelesai = document.getElementById('closeWaktuSelesai').value || null;
    const btn          = document.getElementById('btnConfirmClose');

    btn.disabled    = true;
    btn.textContent = '⏳ Menyimpan...';

    try {
        const res = await fetch(`/admin/logs/${id}/close`, {
            method : 'PATCH',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':LOG_CSRF, 'Accept':'application/json' },
            body   : JSON.stringify({ countermeasure: cm, waktu_selesai: waktuSelesai }),
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok) { showToast('Gagal: ' + (data?.message ?? 'error'), 'error'); return; }

        // ── Update DOM card langsung ─────────────────────────
        const card   = document.getElementById(`logcard-${id}`);
        const timeEl = document.getElementById(`time-${id}`);
        const durEl  = document.getElementById(`dur-${id}`);
        const dotEl  = document.getElementById(`dot-${id}`);

        card?.classList.replace('open','closed');

        if (dotEl) dotEl.className = 'log-status-dot dot-closed';

        if (timeEl) {
            const ws    = data.waktu_selesai || waktuSelesai;
            const mulai = timeEl.textContent.split('–')[0].trim();
            timeEl.textContent = ws ? `${mulai} – ${ws}` : mulai;
        }
        if (durEl) {
            durEl.className   = 'log-durasi';
            durEl.textContent = data.durasi ? `(${data.durasi})` : '';
        }

        // Ganti tombol ✅ Selesai → 🔄 Buka Ulang
        const closeBtn = document.getElementById(`btn-close-${id}`);
        if (closeBtn) {
            closeBtn.className   = 'log-btn log-btn-reopen';
            closeBtn.id          = `btn-reopen-${id}`;
            closeBtn.textContent = '🔄 Buka Ulang';
            closeBtn.onclick     = () => reopenLog(id);
        }

        // Tampilkan CM di card body
        const body = card?.querySelector('.log-card-body');
        if (body) {
            let cmEl = body.querySelector('[data-cm]');
            if (!cmEl) {
                cmEl = document.createElement('div');
                cmEl.className = 'log-meta';
                cmEl.setAttribute('data-cm','1');
                body.appendChild(cmEl);
            }
            cmEl.innerHTML = `🔧 <strong>CM:</strong> ${cm}`;
        }

        // Update counter summary bar
        const cntOpen = document.getElementById('cntOpen');
        if (cntOpen) cntOpen.textContent = Math.max(0, parseInt(cntOpen.textContent) - 1);

        closeSheet('closeLogSheet');
        showToast('✅ Log ditutup — ' + (data.durasi ?? ''), 'success');

    } catch (e) {
        showToast('Gagal: ' + e.message, 'error');
    } finally {
        btn.disabled    = false;
        btn.textContent = '✅ Konfirmasi Selesai';
        _setCloseBtn(true);
    }
}

// ══════════════════════════════════════════════════════════════
// REOPEN LOG — update DOM langsung tanpa reload
// ══════════════════════════════════════════════════════════════
async function reopenLog(id) {
    const btn = document.getElementById(`btn-reopen-${id}`);
    if (btn) { btn.disabled = true; btn.textContent = '⏳...'; }

    try {
        const res = await fetch(`/admin/logs/${id}/reopen`, {
            method : 'PATCH',
            headers: { 'X-CSRF-TOKEN':LOG_CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        });

        if (!res.ok) { showToast('Gagal buka ulang', 'error'); return; }

        // ── Update DOM card langsung ─────────────────────────
        const card   = document.getElementById(`logcard-${id}`);
        const timeEl = document.getElementById(`time-${id}`);
        const durEl  = document.getElementById(`dur-${id}`);
        const dotEl  = document.getElementById(`dot-${id}`);

        card?.classList.replace('closed','open');

        if (dotEl) dotEl.className = 'log-status-dot dot-open';

        if (timeEl) {
            const mulai = timeEl.textContent.split('–')[0].trim();
            timeEl.textContent = `${mulai} – …`;
        }
        if (durEl) {
            durEl.className   = 'log-durasi ongoing';
            durEl.textContent = 'ON GOING';
        }

        // Ganti tombol 🔄 Buka Ulang → ✅ Selesai
        if (btn) {
            btn.className   = 'log-btn log-btn-close';
            btn.id          = `btn-close-${id}`;
            btn.textContent = '✅ Selesai';
            btn.disabled    = false;
            btn.onclick     = () => closeLog(id);
        }

        // Update counter summary bar
        const cntOpen = document.getElementById('cntOpen');
        if (cntOpen) cntOpen.textContent = parseInt(cntOpen.textContent) + 1;

        showToast('🔄 Log dibuka ulang', 'info');

    } catch (e) {
        showToast('Gagal: ' + e.message, 'error');
        if (btn) { btn.disabled = false; btn.textContent = '🔄 Buka Ulang'; }
    }
}

// ── Delete log ───────────────────────────────────────────────
async function deleteLog(id) {
    if (!confirm('Hapus log ini? Tindakan tidak bisa dibatalkan.')) return;
    try {
        const res = await fetch(`/admin/logs/${id}`, {
            method : 'DELETE',
            headers: { 'X-CSRF-TOKEN':LOG_CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        });
        if (!res.ok) { showToast('Gagal hapus', 'error'); return; }
        document.getElementById(`logcard-${id}`)?.remove();

        // Update counter all + open kalau log yg dihapus open
        const cntAll  = document.getElementById('cntAll');
        if (cntAll) cntAll.textContent = Math.max(0, parseInt(cntAll.textContent) - 1);

        showToast('🗑️ Log dihapus', 'info');
    } catch (e) { showToast('Gagal', 'error'); }
}

// ── Reset form saat sheet ditutup ────────────────────────────
const origClose = window.closeSheet;
window.closeSheet = function(id) {
    if (id === 'addLogSheet') {
        activeJenis = null;
        document.querySelectorAll('.jenis-btn').forEach(b => b.className = 'jenis-btn');
        document.querySelectorAll('.form-section').forEach(s => s.classList.remove('visible'));
        document.getElementById('saveBtnWrap').style.display = 'none';
        // Reset label CM
        ['m','mat','met'].forEach(p => {
            const lbl = document.getElementById(`lbl-${p}-cm`);
            const inp = document.getElementById(`${p}-cm`);
            if (lbl) { lbl.style.color = ''; lbl.textContent = 'Countermeasure / Solusi'; }
            if (inp) { inp.style.borderColor = ''; inp.style.background = ''; }
        });
    }
    if (id === 'closeLogSheet') { _closeId = null; }
    if (origClose) origClose(id);
};
</script>
@endpush