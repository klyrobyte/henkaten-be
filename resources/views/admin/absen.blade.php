@extends('layouts.admin')
@section('title', 'Input Absen')

@section('content')

{{-- ── Filter bar ── --}}
<form method="GET" action="{{ route('admin.absence.index') }}" id="filterForm">
    <div class="card" style="padding:12px 16px;margin-bottom:10px">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <div class="field-group" style="flex:1;min-width:120px">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                       onchange="document.getElementById('filterForm').submit()">
            </div>
            <div class="field-group" style="flex:1;min-width:100px">
                <label>Factory</label>
                <select name="factory" onchange="document.getElementById('filterForm').submit()">
                    <option value="Factory 2"         {{ $factory === 'Factory 2'     ? 'selected' : '' }}>Factory 2</option>
                    <option value="Factory 3 &amp; 4" {{ $factory === 'Factory 3 & 4' ? 'selected' : '' }}>Factory 3 &amp; 4</option>
                </select>
            </div>
            <div class="field-group" style="flex:1;min-width:90px">
                <label>Shift</label>
                <select name="shift" onchange="document.getElementById('filterForm').submit()">
                    <option value="A" {{ $shift === 'A' ? 'selected' : '' }}>Shift A</option>
                    <option value="B" {{ $shift === 'B' ? 'selected' : '' }}>Shift B</option>
                </select>
            </div>
        </div>
    </div>
</form>

{{-- ── Summary counts ── --}}
<div class="absen-summary-grid">
    <div class="absen-stat">
        <div class="as-val" id="asTotal" style="color:var(--navy)">{{ $members->count() }}</div>
        <div class="as-lbl">Total</div>
    </div>
    <div class="absen-stat">
        <div class="as-val" id="asHadir" style="color:var(--green)">{{ $hadir }}</div>
        <div class="as-lbl">Hadir</div>
    </div>
    <div class="absen-stat">
        <div class="as-val" id="asAbsen" style="color:var(--red)">{{ $absen }}</div>
        <div class="as-lbl">Tidak Hadir</div>
    </div>
    <div class="absen-stat">
        <div class="as-val" id="asButuh" style="color:var(--orange, #ff8c00)">{{ $absen }}</div>
        <div class="as-lbl">Butuh Pengganti</div>
    </div>
</div>

{{-- ── Action buttons ── --}}
<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
    <button class="btn btn-sm btn-primary" onclick="saveAbsenData()" style="flex:1">
        💾 Simpan Absen
    </button>
    <button class="btn btn-sm btn-navy" onclick="saveAbsenData()">📡 Sync ke Board</button>
</div>

{{-- ── Alert: posisi kosong ── --}}
<div class="alert-bar" id="alertBar" style="display:none;background:rgba(255,140,0,.15);border:1px solid var(--orange,#ff8c00);border-radius:8px;padding:10px 14px;margin-bottom:12px;font-size:13px;color:var(--orange,#ff8c00);font-weight:600">
    <span id="alertText"></span>
</div>

@if($members->isEmpty())
    <div class="empty-state">
        <div class="ei">👥</div>
        <div class="et">Tidak ada member untuk factory/shift ini.<br>
            <a href="{{ route('admin.members.index') }}" style="color:var(--green)">Import data dulu</a>
        </div>
    </div>
@else

{{-- ── LAYOUT: daftar member + panel pengganti ── --}}
<div style="display:flex;gap:14px;align-items:flex-start">

    {{-- Kiri: daftar member --}}
    <div style="flex:1;min-width:0">
        <div class="absen-member-list" id="absenList">
            @foreach($members as $m)
                @php
                    $rec     = $records[$m->id] ?? null;
                    $isAbsen = $rec && $rec->status === 'absen';
                    $reason  = $rec?->reason ?? '';
                @endphp
                <div class="absen-member-row" id="arow-{{ $m->id }}" data-member-id="{{ $m->id }}" data-member-name="{{ $m->nama }}">
                    <div class="amr-photo">
                        @if($m->photo_url)
                            <img src="{{ $m->photo_url }}" alt="{{ $m->nama }}">
                        @else
                            👤
                        @endif
                    </div>
                    <div class="amr-info">
                        <div class="amr-name">{{ $m->nama }}</div>
                        <div class="amr-role">
                            {{ $m->jabatan }}{{ $m->mesin ? ' — '.$m->mesin : '' }}
                        </div>
                        {{-- Tag pengganti (muncul setelah absen & pilih pengganti) --}}
                        <div class="sub-tag" id="subtag-{{ $m->id }}" style="display:none;margin-top:3px;font-size:11px;color:var(--orange,#ff8c00);font-weight:600"></div>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;flex-wrap:wrap">
                        <button class="absen-btn hadir {{ !$isAbsen ? 'active' : '' }}"
                            onclick="setAbsenState({{ $m->id }}, 'hadir', '')">✓ Hadir</button>
                        <button class="absen-btn absen {{ $isAbsen ? 'active' : '' }}"
                            onclick="setAbsenState({{ $m->id }}, 'absen', document.getElementById('reason-{{ $m->id }}').value)">
                            ✗ Absen
                        </button>
                        {{-- Tombol cari pengganti, muncul saat absen --}}
                        <button class="absen-btn" id="findbtn-{{ $m->id }}"
                                style="{{ $isAbsen ? '' : 'display:none' }};background:var(--orange,#ff8c00);color:#fff;font-size:11px"
                                onclick="openFinder({{ $m->id }}, '{{ addslashes($m->nama) }}')">
                            🔍
                        </button>
                        <select class="reason-sel {{ $isAbsen ? 'show' : '' }}"
                                id="reason-{{ $m->id }}"
                                onchange="if(absenState[{{ $m->id }}]?.status==='absen') setAbsenState({{ $m->id }},'absen',this.value)">
                            <option value="Cuti"    {{ $reason === 'Cuti'    ? 'selected' : '' }}>Cuti</option>
                            <option value="Sakit"   {{ $reason === 'Sakit'   ? 'selected' : '' }}>Sakit</option>
                            <option value="Ijin"    {{ $reason === 'Ijin'    ? 'selected' : '' }}>Ijin</option>
                            <option value="Mangkir" {{ $reason === 'Mangkir' ? 'selected' : '' }}>Mangkir</option>
                        </select>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Kanan: Panel Pengganti (sticky) ── algoritma dari dailyassignment.html ── --}}
    <div class="sub-pane" id="subPane"
         style="width:260px;flex-shrink:0;position:sticky;top:10px;background:var(--card-bg,#1e2130);border-radius:12px;border:1px solid rgba(255,255,255,.08);overflow:hidden">

        {{-- Header panel --}}
        <div style="padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.07);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#aaa">
            🔍 Cari Pengganti
        </div>

        {{-- Idle state --}}
        <div id="subIdle" style="padding:20px 14px;text-align:center;color:#666;font-size:12px">
            <div style="font-size:32px;margin-bottom:8px">👆</div>
            <p style="margin:0">Klik tombol <strong style="color:var(--orange,#ff8c00)">🔍</strong> pada member yang absen untuk mencari pengganti.</p>
        </div>

        {{-- Active state --}}
        <div id="subActive" style="display:none">
            <div id="subCtx" style="margin:10px 14px 0;padding:8px 10px;border-radius:8px;background:rgba(255,140,0,.12);border:1px solid rgba(255,140,0,.3);font-size:11px;color:var(--orange,#ff8c00);font-weight:600;display:none"></div>
            <div style="padding:10px 14px 6px">
                <input type="text" id="subSearch"
                       placeholder="🔍 Cari nama..."
                       oninput="renderCandidates()"
                       style="width:100%;box-sizing:border-box;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:8px 10px;color:#fff;font-size:13px;outline:none">
            </div>
            <div id="candList" style="max-height:340px;overflow-y:auto;padding:0 10px 10px"></div>
        </div>
    </div>

</div>
@endif

{{-- ── Style tambahan untuk panel pengganti ── --}}
@push('styles')
<style>
.cand-card{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;cursor:pointer;border:1px solid rgba(255,255,255,.07);margin-bottom:6px;transition:background .15s}
.cand-card:hover{background:rgba(255,255,255,.07)}
.cand-av{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;overflow:hidden}
.cand-av img{width:100%;height:100%;object-fit:cover}
.cand-av.n{background:rgba(114,158,63,.25);color:#729e3f;border:1.5px solid #729e3f}
.cand-av.w{background:rgba(255,140,0,.15);color:#ff8c00;border:1.5px dashed #ff8c00}
.cand-name{font-size:12px;font-weight:600;color:#e0e0e0;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cand-tag{font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700;margin-top:2px;display:inline-block}
.ct-w{background:rgba(255,140,0,.2);color:#ff8c00}
.cand-btn{flex-shrink:0;background:var(--green,#729e3f);color:#fff;border:none;border-radius:6px;padding:5px 9px;font-size:11px;font-weight:700;cursor:pointer}
.sub-empty{text-align:center;color:#555;padding:20px 10px;font-size:12px}
@media(max-width:700px){
    #subPane{display:none!important}
    /* Mobile: panel muncul sebagai modal */
    #subPaneMobile{display:block}
}
</style>
@endpush

@endsection

@push('scripts')
<script>
const CSRF    = '{{ csrf_token() }}';
const TANGGAL = '{{ $tanggal }}';
const FACTORY = '{{ $factory }}';
const SHIFT   = '{{ $shift }}';

// ── Route untuk candidates ───────────────────────────────────────────────
const CANDIDATES_URL = '{{ route('admin.absence.candidates') }}';

// ── State absen (init dari server) ────────────────────────────────────────
let absenState = {
    @foreach($members as $m)
        @php $rec = $records[$m->id] ?? null; @endphp
        {{ $m->id }}: {
            status: '{{ $rec ? $rec->status : 'hadir' }}',
            reason: '{{ $rec?->reason ?? '' }}',
            substitute: null, // { name, photo }
        },
    @endforeach
};

// ── Finder state ─────────────────────────────────────────────────────────
let activeFinder = null; // { memberId, memberName }

// ── setAbsenState ─────────────────────────────────────────────────────────
function setAbsenState(memberId, status, reason) {
    absenState[memberId] = {
        ...absenState[memberId],
        status,
        reason: status === 'absen' ? (reason || 'Cuti') : '',
        substitute: status === 'hadir' ? null : absenState[memberId]?.substitute,
    };

    const hadirBtn = document.querySelector(`#arow-${memberId} .absen-btn.hadir`);
    const absenBtn = document.querySelector(`#arow-${memberId} .absen-btn.absen`);
    const sel      = document.getElementById('reason-' + memberId);
    const findBtn  = document.getElementById('findbtn-' + memberId);

    if (status === 'hadir') {
        hadirBtn?.classList.add('active');
        absenBtn?.classList.remove('active');
        sel?.classList.remove('show');
        if (findBtn) findBtn.style.display = 'none';
        // Reset pengganti jika kembali hadir
        clearSubstitute(memberId);
    } else {
        absenBtn?.classList.add('active');
        hadirBtn?.classList.remove('active');
        sel?.classList.add('show');
        if (findBtn) findBtn.style.display = '';
        if (sel && reason) sel.value = reason;
    }
    updateAbsenCounts();
    updateAlert();
}

// ── Counts ────────────────────────────────────────────────────────────────
function updateAbsenCounts() {
    const vals  = Object.values(absenState);
    const hadir = vals.filter(v => v.status === 'hadir').length;
    const absen = vals.filter(v => v.status === 'absen').length;
    const terisi = vals.filter(v => v.status === 'absen' && v.substitute).length;
    const butuh = absen - terisi;
    document.getElementById('asTotal').textContent = vals.length;
    document.getElementById('asHadir').textContent = hadir;
    document.getElementById('asAbsen').textContent = absen;
    document.getElementById('asButuh').textContent = Math.max(0, butuh);
}

// ── Alert ─────────────────────────────────────────────────────────────────
function updateAlert() {
    const absen  = Object.values(absenState).filter(v => v.status === 'absen').length;
    const terisi = Object.values(absenState).filter(v => v.status === 'absen' && v.substitute).length;
    const kosong = absen - terisi;
    const bar = document.getElementById('alertBar');
    if (kosong > 0) {
        document.getElementById('alertText').textContent = `⚠ Ada ${kosong} posisi belum ada penggantinya!`;
        bar.style.display = '';
    } else {
        bar.style.display = 'none';
    }
}

// ── Finder ────────────────────────────────────────────────────────────────
function openFinder(memberId, memberName) {
    activeFinder = { memberId, memberName };
    document.getElementById('subIdle').style.display = 'none';
    document.getElementById('subActive').style.display = 'block';
    document.getElementById('subSearch').value = '';
    const ctx = document.getElementById('subCtx');
    ctx.style.display = 'block';
    ctx.textContent = `Pengganti untuk: ${memberName}`;
    renderCandidates();
    // Mobile: scroll ke panel
    if (window.innerWidth <= 700) {
        document.getElementById('subPane').style.display = 'block';
        document.getElementById('subPane').scrollIntoView({ behavior: 'smooth' });
    }
}

// ── renderCandidates ─────────────────────────────────────────────────────
// Algoritma dari dailyassignment.html: filter absen, sort sdh-bertugas terakhir
async function renderCandidates() {
    if (!activeFinder) return;
    const list = document.getElementById('candList');
    list.innerHTML = '<div class="sub-empty">⏳ Memuat...</div>';

    const q = (document.getElementById('subSearch')?.value || '').trim();

    try {
        const params = new URLSearchParams({
            tanggal: TANGGAL,
            factory: FACTORY,
            shift:   SHIFT,
            q,
            // kirim nama member yang sedang absen agar difilter server
            for_member: activeFinder.memberName,
        });
        const cands = await fetch(`${CANDIDATES_URL}?${params}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        }).then(r => r.json());

        if (!cands.length) {
            list.innerHTML = '<div class="sub-empty"><div style="font-size:36px;margin-bottom:8px">🔍</div><p>Tidak ada member tersedia.<br>Coba hapus filter.</p></div>';
            return;
        }

        list.innerHTML = cands.map(m => {
            const avContent = m.photo
                ? `<img src="${m.photo}">`
                : `<span>${initials(m.name)}</span>`;
            const avCls = m.isWorking ? 'w' : 'n';
            return `<div class="cand-card" onclick="assignSub(${JSON.stringify(m.name)},${JSON.stringify(m.photo||'')})">
                <div class="cand-av ${avCls}">${avContent}</div>
                <div style="flex:1;min-width:0">
                    <div class="cand-name">${esc(m.name)}</div>
                    ${m.jabatan ? `<div style="font-size:10px;color:#888">${esc(m.jabatan)}</div>` : ''}
                    ${m.isWorking ? `<span class="cand-tag ct-w">Sdh Bertugas</span>` : ''}
                </div>
                <button class="cand-btn" onclick="event.stopPropagation();assignSub(${JSON.stringify(m.name)},${JSON.stringify(m.photo||'')})">✓ Pilih</button>
            </div>`;
        }).join('');
    } catch(e) {
        list.innerHTML = '<div class="sub-empty">❌ Gagal memuat kandidat</div>';
    }
}

// ── assignSub ─────────────────────────────────────────────────────────────
function assignSub(name, photo) {
    if (!activeFinder) return;
    const { memberId, memberName } = activeFinder;

    absenState[memberId].substitute = { name, photo };

    // Update UI: tampilkan tag pengganti di baris member
    const tag = document.getElementById('subtag-' + memberId);
    if (tag) {
        tag.style.display = '';
        tag.innerHTML = `🔄 Pengganti: <strong>${esc(name)}</strong>
            <button onclick="clearSubstitute(${memberId})" style="background:none;border:none;color:var(--red);cursor:pointer;font-size:11px;margin-left:4px">✕</button>`;
    }

    // Tutup panel
    activeFinder = null;
    document.getElementById('subIdle').style.display = 'block';
    document.getElementById('subActive').style.display = 'none';
    document.getElementById('subCtx').style.display = 'none';

    updateAbsenCounts();
    updateAlert();
    showToast(`✅ ${name} ditunjuk sebagai pengganti ${memberName}`, 'success');
}

function clearSubstitute(memberId) {
    if (absenState[memberId]) absenState[memberId].substitute = null;
    const tag = document.getElementById('subtag-' + memberId);
    if (tag) tag.style.display = 'none';
    updateAbsenCounts();
    updateAlert();
}

// ── Save ──────────────────────────────────────────────────────────────────
async function saveAbsenData() {
    if (!Object.keys(absenState).length) {
        showToast('Tidak ada data absen', 'error'); return;
    }
    showLoading('Menyimpan data absen...');
    try {
        const res = await fetch('/admin/absence/save', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({
                tanggal: TANGGAL,
                factory: FACTORY,
                shift:   SHIFT,
                records: absenState,
            }),
        });
        const data = await res.json();
        hideLoading();
        if (data.ok) {
            const absenCount = Object.values(absenState).filter(v => v.status === 'absen').length;
            const hadirCount = Object.values(absenState).length - absenCount;
            showToast(`✅ Absen disimpan! Hadir: ${hadirCount} | Absen: ${absenCount}`, 'success');
        }
    } catch (e) {
        hideLoading();
        showToast('Gagal menyimpan: ' + e.message, 'error');
    }
}

// ── Helpers ───────────────────────────────────────────────────────────────
function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function initials(name) {
    return (name || '?').split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
}

// ── Init ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    updateAbsenCounts();
    updateAlert();
});
</script>
@endpush