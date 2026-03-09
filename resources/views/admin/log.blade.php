@extends('layouts.admin')
@section('title', 'Problem Log')

@push('styles')
<style>
/* ── Log summary bar ─────────────────────────────────────────── */
.log-summary-bar {
    display: flex; gap: 8px; margin-bottom: 14px;
}
.lsb-item {
    flex: 1; background: #fff; border-radius: 12px;
    border: 1.5px solid #eee;
    padding: 10px 8px; text-align: center;
    box-shadow: 0 1px 6px rgba(0,0,0,.05);
}
.lsb-val {
    font-family: 'Orbitron', sans-serif;
    font-size: 22px; font-weight: 700; line-height: 1;
    color: var(--navy);
}
.lsb-lbl {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #aaa; margin-top: 3px;
}

/* ── Log cards ───────────────────────────────────────────────── */
.log-card {
    background: #fff; border-radius: 14px;
    border: 1.5px solid #eee;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    margin-bottom: 10px; overflow: hidden;
    transition: box-shadow .2s, border-color .2s;
}
.log-card.open   { border-left: 4px solid #e74c3c; }
.log-card.closed { border-left: 4px solid #4caf50; opacity: .85; }

.log-card-top {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px 6px; flex-wrap: wrap;
}
.log-badge {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 10px; font-weight: 900; padding: 3px 9px;
    border-radius: 6px; text-transform: uppercase; letter-spacing: .5px;
    color: #fff; flex-shrink: 0;
}
.log-man      { background: #e74c3c; }
.log-machine  { background: #1f3c88; }
.log-material { background: #f39c12; }
.log-method   { background: #2e7d32; }

.log-status-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.dot-open   { background: #e74c3c; animation: pulse-red 1.5s infinite; }
.dot-closed { background: #4caf50; }
@keyframes pulse-red {
    0%,100% { box-shadow: 0 0 0 0 rgba(231,76,60,.5); }
    50%      { box-shadow: 0 0 0 5px rgba(231,76,60,0); }
}

.log-time {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 11px; color: #888; font-weight: 600;
}
.log-durasi {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 10px; color: #aaa; margin-left: auto;
}
.log-durasi.ongoing {
    color: #e74c3c; font-weight: 800;
    animation: blink .9s step-end infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

.log-card-body { padding: 4px 12px 8px; }
.log-lokasi {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 11px; font-weight: 800; color: var(--navy);
    margin-bottom: 3px;
}
.log-desc { font-size: 12px; color: #444; line-height: 1.5; }
.log-meta {
    font-size: 11px; color: #777; margin-top: 3px;
    line-height: 1.4;
}

.log-card-actions {
    display: flex; gap: 6px; padding: 8px 12px 10px;
    border-top: 1px solid #f5f5f5;
}
.log-btn {
    padding: 6px 12px; border-radius: 8px; border: 1.5px solid #e0e0e0;
    background: #f7f7f7; font-size: 11px; font-weight: 700;
    font-family: 'Roboto Condensed', sans-serif; cursor: pointer;
    transition: all .15s; color: #555;
}
.log-btn:hover { border-color: #bbb; background: #eee; }
.log-btn-close  { border-color: #a5d6a7; color: #2e7d32; background: #f1f8e9; }
.log-btn-close:hover  { background: #c8e6c9; }
.log-btn-reopen { border-color: #90caf9; color: #1565c0; background: #e3f2fd; }
.log-btn-reopen:hover { background: #bbdefb; }
.log-btn-del    { border-color: #ffcdd2; color: #c62828; background: #fff5f5; margin-left: auto; }
.log-btn-del:hover    { background: #ffcdd2; }

/* ── Add Log modal header ────────────────────────────────────── */
.ql-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px 12px;
    border-bottom: 1.5px solid #f0f0f0;
}
.ql-header-left { display: flex; align-items: center; gap: 10px; }
.ql-header-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: linear-gradient(135deg, var(--orange,#e65100), #ff7043);
    color: #fff; font-size: 20px; font-weight: 900;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; box-shadow: 0 3px 8px rgba(230,81,0,.3);
}
.ql-header-title {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 14px; font-weight: 800; color: #222;
    text-transform: uppercase; letter-spacing: .4px;
}
.ql-header-sub {
    font-size: 11px; color: #aaa; margin-top: 1px;
}

/* ── Edit Time modal header ──────────────────────────────────── */
.et-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px 12px; border-bottom: 1.5px solid #f0f0f0;
}
.et-header-left { display: flex; align-items: center; gap: 10px; }
.et-header-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: linear-gradient(135deg, #1f3c88, #2c4a9e);
    color: #fff; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; box-shadow: 0 3px 8px rgba(31,60,136,.3);
}
.et-header-title {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 14px; font-weight: 800; color: #222;
    text-transform: uppercase; letter-spacing: .4px;
}
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
        <div class="lsb-val" style="color:#4caf50" id="cntClosed">{{ $logs->where('status','closed')->count() }}</div>
        <div class="lsb-lbl">Closed</div>
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
                <span class="log-status-dot {{ $log->status==='open'?'dot-open':'dot-closed' }}"></span>
                <span class="log-time">
                    {{ $log->waktu_mulai }}{{ $log->waktu_selesai ? ' – '.$log->waktu_selesai : ' – …' }}
                </span>
                @if($log->durasi)
                    <span class="log-durasi">({{ $log->durasi }})</span>
                @elseif($log->status==='open')
                    <span class="log-durasi ongoing" id="ongoing-{{ $log->id }}">ON GOING</span>
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
                    <button class="log-btn log-btn-close" onclick="closeLog({{ $log->id }})">✅ Selesai</button>
                @else
                    <button class="log-btn log-btn-reopen" onclick="reopenLog({{ $log->id }})">🔄 Buka Ulang</button>
                @endif
                <button class="log-btn log-btn-edit"
                        onclick="editLog({{ $log->id }},'{{ $log->waktu_mulai }}','{{ $log->waktu_selesai??'' }}')">
                    ✏️ Edit
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

        {{-- Header --}}
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

            {{-- Row: Jenis + Lokasi --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Tipe Masalah *</label>
                    <select id="logJenis">
                        <option value="Man">Man</option>
                        <option value="Machine">Machine</option>
                        <option value="Material">Material</option>
                        <option value="Method">Method</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Lokasi Mesin *</label>
                    <select id="logLokasi">
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($mesinList as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row: Mulai + Status --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Waktu Mulai *</label>
                    <input type="time" id="logMulai">
                </div>
                <div class="field-group">
                    <label>Status</label>
                    <select id="logStatus" onchange="toggleSelesaiField(this.value)">
                        <option value="open">Open — belum selesai</option>
                        <option value="closed">Closed — sudah selesai</option>
                    </select>
                </div>
            </div>

            {{-- Waktu selesai (muncul saat closed) --}}
            <div class="field-group" id="fieldSelesaiWrap" style="display:none">
                <label>Waktu Selesai</label>
                <input type="time" id="logSelesai">
            </div>

            {{-- Deskripsi --}}
            <div class="field-group">
                <label>Deskripsi *</label>
                <textarea id="logDeskripsi" rows="3"
                          placeholder="Jelaskan masalah secara singkat..."
                          style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;
                                 font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box;
                                 transition:border-color .2s"
                          onfocus="this.style.borderColor='var(--orange)'"
                          onblur="this.style.borderColor='#e0e0e0'"></textarea>
            </div>

            {{-- Row: Cause + Countermeasure --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Cause <span style="font-weight:400;color:#bbb">(opsional)</span></label>
                    <input type="text" id="logCause" placeholder="Penyebab root...">
                </div>
                <div class="field-group">
                    <label>Countermeasure <span style="font-weight:400;color:#bbb">(opsional)</span></label>
                    <input type="text" id="logCM" placeholder="Solusi...">
                </div>
            </div>

            {{-- PIC --}}
            <div class="field-group">
                <label>PIC <span style="font-weight:400;color:#bbb">(opsional)</span></label>
                <input type="text" id="logPIC" placeholder="Nama penanggung jawab">
            </div>

            <div class="save-bar">
                <button class="save-btn-big" onclick="submitLog()">💾 Simpan Log</button>
            </div>
        </div>
    </div>
</div>

{{-- ══ MODAL: Edit Waktu ══════════════════════════════════════════ --}}
<div class="modal-overlay" id="editTimeSheet">
    <div class="modal-sheet" style="max-height:300px">
        <div class="modal-sheet-handle"></div>

        {{-- Header --}}
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
            <div class="save-bar" style="margin-top:4px">
                <button class="save-btn-big" onclick="saveEditTime()">💾 Simpan Perubahan</button>
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

function updateQS(key, val) {
    const u = new URL(window.location);
    u.searchParams.set(key, val);
    return u.toString();
}

function toggleSelesaiField(val) {
    document.getElementById('fieldSelesaiWrap').style.display = val === 'closed' ? '' : 'none';
}

// Set default mulai ke waktu sekarang
document.getElementById('logMulai').value = new Date().toTimeString().slice(0,5);

async function submitLog() {
    const lokasi = document.getElementById('logLokasi').value;
    const desk   = document.getElementById('logDeskripsi').value.trim();
    const mulai  = document.getElementById('logMulai').value;
    const status = document.getElementById('logStatus').value;

    if (!lokasi) { showToast('Pilih lokasi mesin', 'error'); return; }
    if (!desk)   { showToast('Isi deskripsi masalah', 'error'); return; }
    if (!mulai)  { showToast('Isi waktu mulai', 'error'); return; }

    const selesaiRaw   = document.getElementById('logSelesai').value;
    const waktuSelesai = (status === 'closed' && selesaiRaw) ? selesaiRaw : null;

    const btn = document.querySelector('#addLogSheet .save-btn-big');
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Menyimpan...'; }

    try {
        const res = await fetch('/admin/logs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                tanggal:        TANGGAL,
                factory:        FACTORY,
                shift:          SHIFT,
                jenis:          document.getElementById('logJenis').value,
                lokasi,
                waktu_mulai:    mulai,
                waktu_selesai:  waktuSelesai,
                status,
                deskripsi:      desk,
                cause:          document.getElementById('logCause').value || null,
                countermeasure: document.getElementById('logCM').value    || null,
                pic:            document.getElementById('logPIC').value   || null,
            }),
        });

        let data = null;
        try { data = await res.json(); } catch (_) {}

        if (!res.ok || data?.error) {
            showToast('Gagal simpan: ' + (data?.message?.slice(0,80) ?? 'unknown'), 'error');
            return;
        }

        showToast('✅ Log berhasil ditambah!', 'success');
        closeSheet('addLogSheet');
        setTimeout(() => window.location.reload(), 700);

    } catch(e) {
        showToast('Gagal kirim: ' + e.message, 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.textContent = '💾 Simpan Log'; }
    }
}

async function closeLog(id) {
    try {
        await fetch(`/admin/logs/${id}/close`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        });
        showToast('✅ Log ditutup', 'success');
        setTimeout(() => window.location.reload(), 600);
    } catch(e) { showToast('Gagal', 'error'); }
}

async function reopenLog(id) {
    try {
        await fetch(`/admin/logs/${id}/reopen`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        });
        showToast('🔄 Log dibuka ulang', 'info');
        setTimeout(() => window.location.reload(), 600);
    } catch(e) { showToast('Gagal', 'error'); }
}

function editLog(id, mulai, selesai) {
    document.getElementById('editLogId').value   = id;
    document.getElementById('editMulai').value   = mulai;
    document.getElementById('editSelesai').value = selesai;
    openSheet('editTimeSheet');
}

async function saveEditTime() {
    const id = document.getElementById('editLogId').value;
    const btn = document.querySelector('#editTimeSheet .save-btn-big');
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Menyimpan...'; }
    try {
        await fetch(`/admin/logs/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({
                waktu_mulai:   document.getElementById('editMulai').value,
                waktu_selesai: document.getElementById('editSelesai').value || null,
            }),
        });
        closeSheet('editTimeSheet');
        showToast('✅ Waktu diupdate', 'success');
        setTimeout(() => window.location.reload(), 600);
    } catch(e) {
        showToast('Gagal', 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.textContent = '💾 Simpan Perubahan'; }
    }
}

async function deleteLog(id) {
    if (!confirm('Hapus log ini? Tindakan tidak bisa dibatalkan.')) return;
    try {
        await fetch(`/admin/logs/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        });
        showToast('🗑️ Log dihapus', 'info');
        setTimeout(() => window.location.reload(), 600);
    } catch(e) { showToast('Gagal', 'error'); }
}
</script>
@endpush