@extends('layouts.admin')
@section('title', 'Problem Log')

@push('styles')
<style>
/* ── Summary bar ─────────────────────────────────────────────── */
.log-summary-bar { display:flex; gap:8px; margin-bottom:14px; }
.lsb-item {
    flex:1; background:#fff; border-radius:12px; border:1.5px solid #eee;
    padding:10px 8px; text-align:center; box-shadow:0 1px 6px rgba(0,0,0,.05);
}
.lsb-val {
    font-family:'Orbitron',sans-serif; font-size:22px; font-weight:700;
    line-height:1; color:var(--navy);
}
.lsb-lbl {
    font-family:'Roboto Condensed',sans-serif; font-size:10px; font-weight:700;
    text-transform:uppercase; letter-spacing:.5px; color:#aaa; margin-top:3px;
}

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
    display:flex; gap:6px; padding:8px 12px 10px; border-top:1px solid #f5f5f5;
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

.ql-header, .et-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 16px 12px; border-bottom:1.5px solid #f0f0f0;
}
.ql-header-left, .et-header-left { display:flex; align-items:center; gap:10px; }
.ql-header-icon {
    width:34px; height:34px; border-radius:10px;
    background:linear-gradient(135deg, var(--orange,#e65100), #ff7043);
    color:#fff; font-size:20px; font-weight:900;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; box-shadow:0 3px 8px rgba(230,81,0,.3);
}
.ql-header-title {
    font-family:'Roboto Condensed',sans-serif; font-size:14px;
    font-weight:800; color:#222; text-transform:uppercase; letter-spacing:.4px;
}
.ql-header-sub { font-size:11px; color:#aaa; margin-top:1px; }
.et-header-icon {
    width:34px; height:34px; border-radius:10px;
    background:linear-gradient(135deg, #1f3c88, #2c4a9e); color:#fff; font-size:16px;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; box-shadow:0 3px 8px rgba(31,60,136,.3);
}
.et-header-title {
    font-family:'Roboto Condensed',sans-serif; font-size:14px;
    font-weight:800; color:#222; text-transform:uppercase; letter-spacing:.4px;
}

.form-divider {
    display:flex; align-items:center; gap:10px; margin:14px 0 10px;
    font-family:'Roboto Condensed',sans-serif; font-size:10px;
    font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#bbb;
}
.form-divider::before, .form-divider::after { content:''; flex:1; height:1px; background:#eee; }
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
    @php $userShift = auth()->user()->shift; @endphp
    @if(!$userShift || $userShift === 'A')
    <button class="shift-toggle-btn {{ $shift==='A'?'active':'' }}" onclick="switchShift('A')">SHIFT A</button>
    @endif
    @if(!$userShift || $userShift === 'B')
    <button class="shift-toggle-btn {{ $shift==='B'?'active':'' }}" onclick="switchShift('B')">SHIFT B</button>
    @endif
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

{{-- Action bar --}}
<div style="display:flex;justify-content:flex-end;margin-bottom:14px;gap:8px">
    <a href="{{ route('admin.reports.export', ['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift]) }}"
       style="display:flex;align-items:center;gap:5px;font-size:12px;padding:8px 14px;border-radius:8px;
              background:#f5f5f5;color:#666;text-decoration:none;border:1.5px solid #ddd;
              font-family:'Roboto Condensed',sans-serif;font-weight:700">
        📤 Export CSV
    </a>
    <button onclick="openSheet('addLogSheet')"
            style="display:flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;
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
                <span class="log-status-dot {{ $log->status==='open'?'dot-open':'dot-closed' }}" id="dot-{{ $log->id }}"></span>
                <span class="log-time" id="time-{{ $log->id }}">
                    {{ substr($log->waktu_mulai,0,5) }}{{ $log->waktu_selesai ? ' – '.substr($log->waktu_selesai,0,5) : ' – …' }}
                </span>
                @if($log->durasi)
                    <span class="log-durasi" id="dur-{{ $log->id }}">({{ $log->durasi }})</span>
                @elseif($log->status==='open')
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
                @if($log->status==='open')
                    <button class="log-btn log-btn-close" id="btn-close-{{ $log->id }}"
                            onclick="closeLog({{ $log->id }})">✅ Selesai</button>
                @else
                    <button class="log-btn log-btn-reopen" onclick="reopenLog({{ $log->id }})">🔄 Buka Ulang</button>
                @endif

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
                    <div class="field-group"><label>Root Cause</label><input type="text" id="m-cause" placeholder="Contoh: bearing aus, sensor error..."></div>
                    <div class="field-group"><label>Countermeasure</label><input type="text" id="m-cm" placeholder="Contoh: ganti bearing, reset PLC..."></div>
                </div>
                <div class="form-row">
                    <div class="field-group"><label>Teknisi / PIC</label><input type="text" id="m-pic" placeholder="Nama teknisi yang handle"></div>
                    <div class="field-group">
                        <label>Status</label>
                        <select id="m-status" onchange="toggleSelesai('m', this.value)">
                            <option value="open">Open — belum selesai</option>
                            <option value="closed">Closed — sudah selesai</option>
                        </select>
                    </div>
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
                    <textarea id="mat-deskripsi" rows="2" placeholder="Contoh: material short shot, warna tidak sesuai..."
                              style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                              onfocus="this.style.borderColor='#f39c12'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                </div>
                <div class="form-row">
                    <div class="field-group"><label>No. Lot / Batch</label><input type="text" id="mat-cause" placeholder="No. lot material bermasalah"></div>
                    <div class="field-group"><label>Countermeasure</label><input type="text" id="mat-cm" placeholder="Contoh: ganti lot, kembalikan ke gudang..."></div>
                </div>
                <div class="form-row">
                    <div class="field-group"><label>PIC</label><input type="text" id="mat-pic" placeholder="Nama penanggung jawab"></div>
                    <div class="field-group">
                        <label>Status</label>
                        <select id="mat-status" onchange="toggleSelesai('mat', this.value)">
                            <option value="open">Open — belum selesai</option>
                            <option value="closed">Closed — sudah selesai</option>
                        </select>
                    </div>
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
                    <textarea id="met-deskripsi" rows="2" placeholder="Contoh: setting tidak sesuai standar, proses tidak mengikuti SOP..."
                              style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                              onfocus="this.style.borderColor='#2e7d32'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                </div>
                <div class="form-row">
                    <div class="field-group"><label>Standar yang Dilanggar</label><input type="text" id="met-cause" placeholder="Contoh: suhu resin, cycle time SOP..."></div>
                    <div class="field-group"><label>Tindakan Koreksi</label><input type="text" id="met-cm" placeholder="Contoh: re-training, update SOP..."></div>
                </div>
                <div class="form-row">
                    <div class="field-group"><label>PIC</label><input type="text" id="met-pic" placeholder="Nama penanggung jawab"></div>
                    <div class="field-group">
                        <label>Status</label>
                        <select id="met-status" onchange="toggleSelesai('met', this.value)">
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



@endsection

@push('scripts')
<script>
const TANGGAL = '{{ $tanggal }}';
const FACTORY = @json($factory);
const SHIFT   = '{{ $shift }}';
const CSRF    = '{{ csrf_token() }}';

let activeJenis = null;
const PREFIX    = { Machine: 'm', Material: 'mat', Method: 'met' };

function updateQS(key, val) {
    const u = new URL(window.location);
    u.searchParams.set(key, val);
    return u.toString();
}

// ── Pilih jenis ──────────────────────────────────────────────
function selectJenis(jenis) {
    activeJenis = jenis;
    ['Machine','Material','Method'].forEach(j => {
        document.getElementById(`btn-${j.toLowerCase()}`).className = 'jenis-btn';
    });
    document.getElementById(`btn-${jenis.toLowerCase()}`).className = `jenis-btn sel-${jenis.toLowerCase()}`;
    document.querySelectorAll('.form-section').forEach(s => s.classList.remove('visible'));
    document.getElementById(`section-${jenis.toLowerCase()}`).classList.add('visible');

    const now = new Date().toTimeString().slice(0, 5);
    document.getElementById(`${PREFIX[jenis]}-mulai`).value = now;
    document.getElementById('saveBtnWrap').style.display = '';
}



// ── Submit log baru ──────────────────────────────────────────
async function submitLog() {
    if (!activeJenis) { showToast('Pilih tipe masalah dulu', 'error'); return; }

    const p      = PREFIX[activeJenis];
    const lokasi = document.getElementById(`${p}-lokasi`).value;
    const mulai  = document.getElementById(`${p}-mulai`).value;
    const desk   = document.getElementById(`${p}-deskripsi`).value.trim();
    const status = document.getElementById(`${p}-status`).value;

    if (!lokasi) { showToast('Pilih lokasi mesin', 'error'); return; }
    if (!mulai)  { showToast('Isi waktu mulai', 'error');    return; }
    if (!desk)   { showToast('Isi deskripsi masalah', 'error'); return; }

    const btn = document.getElementById('saveBtnMain');
    btn.disabled = true; btn.textContent = '⏳ Menyimpan...';

    try {
        const res = await fetch('/admin/logs', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({
                tanggal: TANGGAL, factory: FACTORY, shift: SHIFT,
                jenis: activeJenis, lokasi,
                waktu_mulai: mulai, status,
                deskripsi: desk,
                cause:          document.getElementById(`${p}-cause`)?.value || null,
                countermeasure: document.getElementById(`${p}-cm`)?.value    || null,
                pic:            document.getElementById(`${p}-pic`)?.value   || null,
            }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) { showToast('Gagal simpan: ' + (data?.message?.slice(0,80) ?? 'error'), 'error'); return; }
        showToast('✅ Log berhasil ditambah!', 'success');
        closeSheet('addLogSheet');
}

// ── Close log: tampilkan jam selesai + durasi langsung di card ──
async function closeLog(id) {
    const btn = document.getElementById(`btn-close-${id}`);
    if (btn) { btn.disabled = true; btn.textContent = '⏳...'; }
    try {
        const res  = await fetch(`/admin/logs/${id}/close`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        });
        const data = await res.json();

        // Update tampilan card langsung tanpa reload
        const card     = document.getElementById(`logcard-${id}`);
        const timeEl   = document.getElementById(`time-${id}`);
        const durEl    = document.getElementById(`dur-${id}`);
        const dotEl    = document.getElementById(`dot-${id}`);

        if (card)   { card.classList.replace('open', 'closed'); }
        if (dotEl)  { dotEl.className = 'log-status-dot dot-closed'; }
        if (timeEl && data.waktu_selesai) {
            const mulai = timeEl.textContent.split('–')[0].trim();
            timeEl.textContent = `${mulai} – ${data.waktu_selesai}`;
        }
        if (durEl && data.durasi) {
            durEl.className   = 'log-durasi';
            durEl.textContent = `(${data.durasi})`;
        }

        // Ganti tombol Selesai → Buka Ulang
        if (btn) {
            btn.className   = 'log-btn log-btn-reopen';
            btn.id          = '';
            btn.textContent = '🔄 Buka Ulang';
            btn.onclick     = () => reopenLog(id);
        }

        showToast('✅ Log ditutup — ' + (data.durasi ?? ''), 'success');
    } catch (e) {
        showToast('Gagal', 'error');
        if (btn) { btn.disabled = false; btn.textContent = '✅ Selesai'; }
    }
}

async function reopenLog(id) {
    try {
        await fetch(`/admin/logs/${id}/reopen`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        });
        showToast('🔄 Log dibuka ulang', 'info');
        setTimeout(() => window.location.reload(), 600);
    } catch (e) { showToast('Gagal', 'error'); }
}

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
    }
    if (origClose) origClose(id);
};
</script>
@endpush