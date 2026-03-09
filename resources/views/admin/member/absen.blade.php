@extends('layouts.admin')
@section('title', 'Input Absen - Member Management')

@section('content')

<div class="app-header">
    <a href="{{ route('admin.members.index') }}" class="h-back">←</a>
    <div class="h-title">INPUT ABSEN</div>
    <div class="h-badge">{{ $factory === 'Factory 2' ? 'F2' : 'F3&4' }}</div>
</div>

<nav class="bottom-nav">
    <button class="nav-btn" onclick="window.location='{{ route('admin.members.index') }}'">
        <span class="ni">👥</span>Member
    </button>
    <button class="nav-btn active">
        <span class="ni">✅</span>Absen
    </button>
    <button class="nav-btn" onclick="window.location='{{ route('admin.absence.report') }}'">
        <span class="ni">📊</span>Rekap
    </button>
    <button class="nav-btn" onclick="window.location='{{ route('admin.members.index') }}?tab=import'">
        <span class="ni">📥</span>Import
    </button>
</nav>

<div class="main">

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.absence.index') }}" id="filterForm">
        <div class="card" style="padding:12px 16px">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <div class="field-group" style="flex:1;min-width:120px">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                           onchange="document.getElementById('filterForm').submit()">
                </div>
                <div class="field-group" style="flex:1;min-width:100px">
                    <label>Factory</label>
                    <select name="factory" onchange="document.getElementById('filterForm').submit()">
                        <option value="Factory 2"     {{ $factory === 'Factory 2'     ? 'selected' : '' }}>Factory 2</option>
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

    {{-- Summary counts — menggantikan updateAbsenCounts() JS --}}
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
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
        <button class="btn btn-sm btn-primary" onclick="saveAbsenData()" style="flex:1">
            💾 Simpan Absen
        </button>
        <button class="btn btn-sm btn-navy" onclick="saveAbsenData()">📡 Sync ke Board</button>
    </div>

    {{-- Member list dengan toggle hadir/absen --}}
    @if($members->isEmpty())
        <div class="empty-state">
            <div class="ei">👥</div>
            <div class="et">Tidak ada member untuk factory/shift ini.<br>
                <a href="{{ route('admin.members.index') }}" style="color:var(--green)">Import data dulu</a>
            </div>
        </div>
    @else
        <div class="absen-member-list" id="absenList">
            @foreach($members as $m)
                @php
                    $rec     = $records[$m->id] ?? null;
                    $isAbsen = $rec && $rec->status === 'absen';
                    $reason  = $rec?->reason ?? '';
                @endphp
                <div class="absen-member-row" id="arow-{{ $m->id }}">
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
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;flex-wrap:wrap">
                        <button class="absen-btn hadir {{ !$isAbsen ? 'active' : '' }}"
                            onclick="setAbsenState({{ $m->id }}, 'hadir', '')">✓ Hadir</button>
                        <button class="absen-btn absen {{ $isAbsen ? 'active' : '' }}"
                            onclick="setAbsenState({{ $m->id }}, 'absen', document.getElementById('reason-{{ $m->id }}').value)">
                            ✗ Absen
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
    @endif

</div>{{-- /main --}}
@endsection

@push('scripts')
<script>
const CSRF    = '{{ csrf_token() }}';
const TANGGAL = '{{ $tanggal }}';
const FACTORY = '{{ $factory }}';
const SHIFT   = '{{ $shift }}';

// Init state dari data server (menggantikan loadAbsenPage() yang baca IDB)
let absenState = {
    @foreach($members as $m)
        @php $rec = $records[$m->id] ?? null; @endphp
        {{ $m->id }}: {
            status: '{{ $rec ? $rec->status : 'hadir' }}',
            reason: '{{ $rec?->reason ?? '' }}'
        },
    @endforeach
};

// Menggantikan setAbsenState() JS — identik logikanya
function setAbsenState(memberId, status, reason) {
    absenState[memberId] = { status, reason: status === 'absen' ? (reason || 'Cuti') : '' };

    const hadirBtn = document.querySelector(`#arow-${memberId} .absen-btn.hadir`);
    const absenBtn = document.querySelector(`#arow-${memberId} .absen-btn.absen`);
    const sel      = document.getElementById('reason-' + memberId);

    if (status === 'hadir') {
        hadirBtn?.classList.add('active');
        absenBtn?.classList.remove('active');
        sel?.classList.remove('show');
    } else {
        absenBtn?.classList.add('active');
        hadirBtn?.classList.remove('active');
        sel?.classList.add('show');
        if (sel && reason) sel.value = reason;
    }
    updateAbsenCounts();
}

function updateAbsenCounts() {
    const vals  = Object.values(absenState);
    const hadir = vals.filter(v => v.status === 'hadir').length;
    const absen = vals.filter(v => v.status === 'absen').length;
    document.getElementById('asTotal').textContent = vals.length;
    document.getElementById('asHadir').textContent = hadir;
    document.getElementById('asAbsen').textContent = absen;
}

// Menggantikan saveAbsenData() JS — POST ke server (bukan localStorage)
async function saveAbsenData() {
    if (!Object.keys(absenState).length) {
        showToast('Tidak ada data absen', 'error'); return;
    }
    showLoading('Menyimpan data absen...');
    try {
        const res = await fetch('/admin/absence/save', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ tanggal: TANGGAL, factory: FACTORY, shift: SHIFT, records: absenState }),
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

function showToast(msg, type = 'info') {
    const t = document.getElementById('toastEl');
    t.textContent = msg; t.className = `toast ${type} show`;
    clearTimeout(t._t); t._t = setTimeout(() => t.classList.remove('show'), 2800);
}
function showLoading(msg) {
    document.getElementById('loadingEl').classList.add('show');
    document.getElementById('loadingText').textContent = msg || 'Loading...';
}
function hideLoading() { document.getElementById('loadingEl').classList.remove('show'); }
</script>
@endpush
