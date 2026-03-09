@extends('layouts.admin')
@section('title', 'Member Management')

@section('content')

{{-- ──────────────────────────────────────────────────────────── --}}
{{-- HEADER BAR                                                   --}}
{{-- ──────────────────────────────────────────────────────────── --}}
<div class="app-header">
    <a href="{{ route('admin.dashboard') }}" class="h-back">←</a>
    <div class="h-title">MEMBER MANAGEMENT</div>
    <div class="h-badge">{{ $stats['f2'] > 0 ? 'F2+'.$stats['f34'] : 'MM' }}</div>
</div>

{{-- ──────────────────────────────────────────────────────────── --}}
{{-- BOTTOM NAV (4 tab: Member · Absen · Rekap · Import)          --}}
{{-- menggantikan: bottom-nav + goPage() JS                       --}}
{{-- ──────────────────────────────────────────────────────────── --}}
<nav class="bottom-nav">
    <button class="nav-btn active" id="nav-members" onclick="goPage('members')">
        <span class="ni">👥</span>Member
    </button>
    <button class="nav-btn" id="nav-absen" onclick="goPage('absen')">
        <span class="ni">✅</span>Absen
        @if($stats['absen_today'] > 0)
            <span class="nav-badge show" id="absenBadge">{{ $stats['absen_today'] }}</span>
        @else
            <span class="nav-badge" id="absenBadge">0</span>
        @endif
    </button>
    <button class="nav-btn" id="nav-report" onclick="goPage('report')">
        <span class="ni">📊</span>Rekap
    </button>
    <button class="nav-btn" id="nav-import" onclick="goPage('import')">
        <span class="ni">📥</span>Import
    </button>
</nav>

<div class="main">

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- PAGE: MEMBERS                                                  --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="page active" id="page-members">

    {{-- Filter chips — menggantikan filterFactory() + filterShift() JS --}}
    <div class="filter-bar">
        <div class="filter-chip {{ $factory==='all'        ? 'active' : '' }}"
             onclick="applyFilter('all','all')">Semua</div>
        <div class="filter-chip orange {{ $factory==='Factory 2'     ? 'active' : '' }}"
             onclick="applyFilter('Factory 2', currentShift)">Factory 2</div>
        <div class="filter-chip navy {{ $factory==='Factory 3 & 4' ? 'active' : '' }}"
             onclick="applyFilter('Factory 3 & 4', currentShift)">Factory 3&4</div>
        <div class="filter-chip {{ $shift==='A' ? 'active' : '' }}"
             onclick="applyFilter(currentFactory, currentShift==='A'?'all':'A')">Shift A</div>
        <div class="filter-chip {{ $shift==='B' ? 'active' : '' }}"
             onclick="applyFilter(currentFactory, currentShift==='B'?'all':'B')">Shift B</div>
    </div>

    {{-- Stats — menggantikan updateStats() JS --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="sv" id="statTotal">{{ $stats['total'] }}</div>
            <div class="sl">Total</div>
        </div>
        <div class="stat-card">
            <div class="sv" id="statF2">{{ $stats['f2'] }}</div>
            <div class="sl">Factory 2</div>
        </div>
        <div class="stat-card">
            <div class="sv" id="statF34">{{ $stats['f34'] }}</div>
            <div class="sl">F 3&amp;4</div>
        </div>
        <div class="stat-card">
            <div class="sv" id="statToday" style="color:var(--red)">{{ $stats['absen_today'] }}</div>
            <div class="sl">Absen Hr Ini</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="search-bar">
        <span class="si">🔍</span>
        <input type="text" id="searchInput" placeholder="Cari nama member..."
               oninput="filterGrid()">
    </div>

    <div style="display:flex;justify-content:flex-end;margin-bottom:10px">
        <button class="btn btn-sm btn-orange" onclick="openAddMember()">➕ Tambah Member</button>
    </div>

    {{-- Member grid — menggantikan renderMembers() JS --}}
    <div class="member-grid" id="memberGrid">
        @forelse($members as $i => $m)
            @php
                $rec = \App\Models\AbsenceRecord::where([
                    'tanggal'   => today()->toDateString(),
                    'factory'   => $m->factory,
                    'shift'     => $m->shift,
                    'member_id' => $m->id,
                ])->first();
                $isAbsent = $rec && $rec->status === 'absen';
            @endphp
            <div class="member-card {{ $isAbsent ? 'absent-today' : '' }}"
                 data-name="{{ strtolower($m->nama) }}"
                 data-factory="{{ $m->factory }}"
                 data-shift="{{ $m->shift }}"
                 onclick="openMemberDetail({{ $m->id }})">
                <span class="mc-number">#{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
                @if($isAbsent)
                    <span class="absent-badge">{{ $rec->reason ?? 'Absen' }}</span>
                @endif
                <div class="mc-photo">
                    @if($m->photo_url)
                        <img src="{{ $m->photo_url }}" alt="{{ $m->nama }}">
                    @else
                        👤
                    @endif
                </div>
                <div class="mc-name">{{ $m->nama }}</div>
                <div class="mc-role">
                    {{ $m->factory }} | Shift {{ $m->shift }}
                    @if($m->mesin) | {{ $m->mesin }} @endif
                </div>
                <span class="mc-badge {{ $m->role_badge_class }}">{{ $m->jabatan }}</span>
            </div>
        @empty
            <div class="empty-state" style="grid-column:1/-1">
                <div class="ei">👥</div>
                <div class="et">Belum ada data member.<br>Import dari Excel atau tambah manual.</div>
            </div>
        @endforelse
    </div>
</div>{{-- /page-members --}}

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- PAGE: ABSEN                                                    --}}
{{-- menggantikan: page-absen (seluruhnya dirender via AJAX)       --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="page" id="page-absen">
    <div class="card" style="padding:12px 16px">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <div class="field-group" style="flex:1;min-width:120px">
                <label>Tanggal</label>
                <input type="date" id="absenDate" onchange="loadAbsenPage()">
            </div>
            <div class="field-group" style="flex:1;min-width:100px">
                <label>Factory</label>
                <select id="absenFactory" onchange="loadAbsenPage()">
                    <option value="Factory 2">Factory 2</option>
                    <option value="Factory 3 &amp; 4">Factory 3 &amp; 4</option>
                </select>
            </div>
            <div class="field-group" style="flex:1;min-width:90px">
                <label>Shift</label>
                <select id="absenShift" onchange="loadAbsenPage()">
                    <option value="A">Shift A</option>
                    <option value="B">Shift B</option>
                </select>
            </div>
        </div>
    </div>

    <div class="absen-summary-grid">
        <div class="absen-stat">
            <div class="as-val" id="asTotal" style="color:var(--navy)">0</div>
            <div class="as-lbl">Total</div>
        </div>
        <div class="absen-stat">
            <div class="as-val" id="asHadir" style="color:var(--green)">0</div>
            <div class="as-lbl">Hadir</div>
        </div>
        <div class="absen-stat">
            <div class="as-val" id="asAbsen" style="color:var(--red)">0</div>
            <div class="as-lbl">Tidak Hadir</div>
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
        <button class="btn btn-sm btn-primary" onclick="saveAbsenData()" style="flex:1">
            💾 Simpan Absen
        </button>
        <button class="btn btn-sm btn-navy" onclick="broadcastAbsen()">📡 Sync ke Board</button>
    </div>

    <div class="absen-member-list" id="absenList">
        <div class="empty-state">
            <div class="ei">👥</div>
            <div class="et">Pilih factory dan shift</div>
        </div>
    </div>
</div>{{-- /page-absen --}}

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- PAGE: REPORT                                                   --}}
{{-- menggantikan: page-report (render via AJAX)                   --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="page" id="page-report">
    <div class="report-date-nav">
        <button class="rdn-btn" onclick="changeReportDate(-1)">←</button>
        <div class="rdn-date" id="reportDateLabel">—</div>
        <button class="rdn-btn" onclick="changeReportDate(1)">→</button>
    </div>
    <div style="display:flex;gap:8px;margin-bottom:14px">
        <button class="btn btn-sm btn-primary" onclick="generateReport()" style="flex:1">🔄 Refresh</button>
        <a id="exportReportBtn" href="{{ route('admin.absence.export') }}" class="btn btn-sm btn-navy">
            📤 Export CSV
        </a>
    </div>
    <div id="reportContent">
        <div class="empty-state">
            <div class="ei">📊</div>
            <div class="et">Klik Refresh untuk muat laporan</div>
        </div>
    </div>
</div>{{-- /page-report --}}

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- PAGE: IMPORT                                                   --}}
{{-- menggantikan: page-import + processExcelFile() + confirmImport() --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="page" id="page-import">

    <div class="card">
        <h3>📥 Import dari Excel</h3>
        <div style="background:#fff8e1;border-radius:10px;padding:12px;margin-bottom:14px;font-size:12px;color:#795548;border:1px solid #ffe0b2">
            <strong>Format kolom yang dibutuhkan:</strong><br>
            <code>Nama | Factory | Shift | Jabatan | NIK (opsional) | Mesin (opsional)</code><br><br>
            <strong>Nilai yang valid:</strong><br>
            • Factory: <code>Factory 2</code> atau <code>Factory 3 &amp; 4</code><br>
            • Shift: <code>A</code> atau <code>B</code><br>
            • Jabatan: <code>Operator</code>, <code>SPV</code>, <code>TL</code>, <code>GL</code>, <code>KY</code>
        </div>

        {{-- Drop zone — JS tetap parse Excel di browser, lalu POST JSON ke server --}}
        <div class="import-box" id="importBox"
             onclick="document.getElementById('excelInput').click()"
             ondragover="event.preventDefault();this.classList.add('drag')"
             ondragleave="this.classList.remove('drag')"
             ondrop="handleDrop(event)">
            <div class="ib-icon">📊</div>
            <div class="ib-title">Tap atau Drag &amp; Drop File Excel</div>
            <div class="ib-sub">.xlsx, .xls, atau .csv</div>
        </div>
        <input type="file" id="excelInput" accept=".xlsx,.xls,.csv" style="display:none"
               onchange="handleExcelImport(this)">

        <div id="importPreviewArea" style="display:none">
            <div class="import-preview" id="importPreview"></div>
            <div style="display:flex;gap:8px;margin-top:12px">
                <button class="btn btn-primary" onclick="confirmImport()" style="flex:1">
                    ✅ Import <span id="importCount">0</span> Member
                </button>
                <button class="btn btn-secondary btn-sm" onclick="clearImport()">❌ Batal</button>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>📋 Download Template</h3>
        <p style="font-size:13px;color:#888;margin:0 0 12px">
            Download template CSV yang sudah sesuai format import.
        </p>
        <a href="{{ route('admin.members.template') }}" class="btn btn-orange">
            ⬇️ Download Template CSV
        </a>
    </div>

    <div class="card">
        <h3>🗑️ Kelola Data</h3>
        <div style="display:flex;flex-direction:column;gap:10px">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0">
                <div>
                    <div style="font-size:14px;font-weight:500">Export Data Member</div>
                    <div style="font-size:11px;color:#888">Simpan semua member ke CSV</div>
                </div>
                <a href="{{ route('admin.members.export') }}" class="btn btn-sm btn-navy">Export</a>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0">
                <div>
                    <div style="font-size:14px;font-weight:500;color:var(--red)">Hapus Semua Member</div>
                    <div style="font-size:11px;color:#888">Hati-hati! Tidak bisa dibatalkan</div>
                </div>
                <button class="btn btn-sm btn-danger" onclick="clearAllMembers()">Hapus</button>
            </div>
        </div>
    </div>

</div>{{-- /page-import --}}

</div>{{-- /main --}}

{{-- ──────────────────────────────────────────────────────────── --}}
{{-- MODAL: MEMBER DETAIL SHEET                                   --}}
{{-- menggantikan: #memberSheet + openMemberDetail() JS          --}}
{{-- ──────────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="memberSheet">
    <div class="modal-sheet">
        <div class="modal-handle"></div>
        <div class="modal-header">
            <h3 id="memberSheetTitle">Detail Member</h3>
            <button class="modal-close" onclick="closeSheet('memberSheet')">✕</button>
        </div>
        <div class="modal-body" id="memberSheetBody">
            <div style="text-align:center;padding:24px;color:#aaa">Memuat...</div>
        </div>
    </div>
</div>

{{-- ──────────────────────────────────────────────────────────── --}}
{{-- MODAL: ADD / EDIT MEMBER SHEET                               --}}
{{-- menggantikan: #editSheet + openAddMember() + editMember() JS --}}
{{-- ──────────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="editSheet">
    <div class="modal-sheet">
        <div class="modal-handle"></div>
        <div class="modal-header">
            <h3 id="editSheetTitle">Tambah Member</h3>
            <button class="modal-close" onclick="closeSheet('editSheet')">✕</button>
        </div>
        <div class="modal-body">
            {{-- Foto --}}
            <div style="text-align:center;margin-bottom:16px">
                <div class="photo-upload-area" id="editPhotoPreview"
                     onclick="document.getElementById('editPhotoInput').click()">📷</div>
                <input type="file" id="editPhotoInput" accept="image/*" style="display:none"
                       onchange="handleEditPhoto(this)">
                <div style="font-size:11px;color:#aaa">Tap untuk upload foto</div>
            </div>

            {{-- Row 1: Nama + NIK --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="editName" placeholder="Nama member">
                </div>
                <div class="field-group">
                    <label>NIK / ID</label>
                    <input type="text" id="editNIK" placeholder="Opsional">
                </div>
            </div>

            {{-- Row 2: Jabatan + Factory --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Jabatan *</label>
                    <select id="editRole">
                        <option value="Operator">Operator</option>
                        <option value="SPV">SPV</option>
                        <option value="TL">TL</option>
                        <option value="GL">GL</option>
                        <option value="KY">KY</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Factory *</label>
                    <select id="editFactory" onchange="updateMachineSelector()">
                        <option value="Factory 2">Factory 2</option>
                        <option value="Factory 3 &amp; 4">Factory 3 &amp; 4</option>
                    </select>
                </div>
            </div>

            {{-- Row 3: Shift + Mesin --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Shift *</label>
                    <select id="editShift">
                        <option value="A">Shift A</option>
                        <option value="B">Shift B</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Mesin</label>
                    <select id="editMesin">
                        <option value="">-- Pilih Mesin --</option>
                    </select>
                </div>
            </div>

            {{-- Row 4: Status --}}
            <div class="form-row single">
                <div class="field-group">
                    <label>Status</label>
                    <select id="editStatus">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <input type="hidden" id="editMemberId">
            <div class="save-bar">
                <button class="save-btn-big" onclick="saveMember()">💾 Simpan Member</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// ══════════════════════════════════════════════════════════════════════
// CONFIG dari server — menggantikan const factoryMachines JS
// ══════════════════════════════════════════════════════════════════════
const FACTORY_MACHINES = @json($mesinList);
const CSRF = '{{ csrf_token() }}';

// ══════════════════════════════════════════════════════════════════════
// STATE — menggantikan let members, filterFact, filterShiftVal JS
// Data member ada di DOM (PHP render), filter dilakukan di browser
// ══════════════════════════════════════════════════════════════════════
let currentFactory = '{{ $factory }}';
let currentShift   = '{{ $shift }}';
let editPhotoData  = null;
let pendingImport  = [];
let absenState     = {};      // { memberId: { status, reason } }
let currentReportDate = new Date().toISOString().split('T')[0];

// ──────────────────────────────────────────────────────────────────────
// NAVIGASI TAB — menggantikan goPage() JS
// ──────────────────────────────────────────────────────────────────────
function goPage(page) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('page-' + page)?.classList.add('active');
    document.getElementById('nav-' + page)?.classList.add('active');
    if (page === 'absen')  loadAbsenPage();
    if (page === 'report') { renderReportDateLabel(); generateReport(); }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ──────────────────────────────────────────────────────────────────────
// FILTER (client-side) — menggantikan filterFactory() + filterShift() JS
// ──────────────────────────────────────────────────────────────────────
function applyFilter(factory, shift) {
    currentFactory = factory;
    currentShift   = shift;

    // Update chip active states
    document.querySelectorAll('.filter-chip').forEach((c, i) => {
        c.classList.remove('active');
    });
    const chips = document.querySelectorAll('.filter-chip');
    if (factory === 'all')          chips[0]?.classList.add('active');
    if (factory === 'Factory 2')    chips[1]?.classList.add('active');
    if (factory === 'Factory 3 & 4') chips[2]?.classList.add('active');
    if (shift === 'A')              chips[3]?.classList.add('active');
    if (shift === 'B')              chips[4]?.classList.add('active');

    filterGrid();
}

function filterGrid() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('#memberGrid .member-card');
    let visible = 0;

    cards.forEach(card => {
        const name    = card.dataset.name    || '';
        const factory = card.dataset.factory || '';
        const shift   = card.dataset.shift   || '';

        const matchF = currentFactory === 'all' || factory === currentFactory;
        const matchS = currentShift   === 'all' || shift   === currentShift;
        const matchQ = !q || name.includes(q);

        const show = matchF && matchS && matchQ;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // Tampilkan empty state jika tidak ada
    let empty = document.getElementById('emptyState');
    if (!visible) {
        if (!empty) {
            empty = document.createElement('div');
            empty.id = 'emptyState';
            empty.className = 'empty-state';
            empty.style.cssText = 'grid-column:1/-1';
            empty.innerHTML = '<div class="ei">👥</div><div class="et">Tidak ada member ditemukan</div>';
            document.getElementById('memberGrid').appendChild(empty);
        }
    } else {
        empty?.remove();
    }
}

// ──────────────────────────────────────────────────────────────────────
// MEMBER DETAIL SHEET — menggantikan openMemberDetail() JS
// Fetch JSON dari server, tampilkan di sheet
// ──────────────────────────────────────────────────────────────────────
async function openMemberDetail(id) {
    openSheet('memberSheet');
    document.getElementById('memberSheetTitle').textContent = 'Detail Member';
    document.getElementById('memberSheetBody').innerHTML =
        '<div style="text-align:center;padding:24px;color:#aaa">⏳ Memuat...</div>';

    try {
        const res  = await fetch(`/admin/members/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        const data = await res.json();
        const m    = data.member;
        const hist = data.history;

        document.getElementById('memberSheetTitle').textContent = m.nama;

        const photoHTML = m.photo_url
            ? `<img src="${m.photo_url}" style="width:100%;height:100%;object-fit:cover">`
            : '👤';

        const badgeClass = { Operator:'op', SPV:'spv', TL:'tl', GL:'gl', KY:'ky' }[m.jabatan] ?? 'op';

        const histHTML = hist.length
            ? hist.map(h => {
                const statusClass = { hadir:'hi-hadir', Cuti:'hi-cuti', Sakit:'hi-sakit', Ijin:'hi-ijin', Mangkir:'hi-ijin' }[h.status === 'hadir' ? 'hadir' : (h.reason || 'hadir')] ?? 'hi-hadir';
                const label = h.status === 'hadir' ? 'Hadir' : (h.reason || 'Absen');
                const tgl   = new Date(h.tanggal + 'T00:00:00').toLocaleDateString('id-ID', { weekday:'short', day:'numeric', month:'short' });
                return `<div class="history-item">
                    <span class="hi-date">${tgl}</span>
                    <span class="hi-badge ${statusClass}">${label}</span>
                </div>`;
            }).join('')
            : '<div style="text-align:center;padding:16px;color:#bbb;font-size:12px">Belum ada riwayat absen</div>';

        document.getElementById('memberSheetBody').innerHTML = `
            <div class="member-detail-hero">
                <div class="member-detail-photo">${photoHTML}</div>
                <div class="member-detail-info">
                    <h4>${m.nama}</h4>
                    <div style="font-size:12px;color:#888;margin-bottom:6px">
                        ${m.factory} | Shift ${m.shift}${m.mesin ? ' | ' + m.mesin : ''}
                    </div>
                    <span class="mc-badge ${badgeClass}">${m.jabatan}</span>
                    ${m.nik ? `<span style="font-size:11px;color:#aaa;margin-left:8px">NIK: ${m.nik}</span>` : ''}
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-bottom:16px">
                <button class="btn btn-sm btn-orange" style="flex:1"
                    onclick="closeSheet('memberSheet');loadEditMember(${m.id})">✏️ Edit</button>
                <button class="btn btn-sm btn-danger"
                    onclick="deleteMember(${m.id})">🗑️</button>
            </div>
            <div class="section-title">Riwayat Absen (10 Terakhir)</div>
            <div class="history-list">${histHTML}</div>`;
    } catch (e) {
        document.getElementById('memberSheetBody').innerHTML =
            `<div style="text-align:center;padding:24px;color:var(--red)">Gagal memuat: ${e.message}</div>`;
    }
}

// ──────────────────────────────────────────────────────────────────────
// TAMBAH MEMBER — menggantikan openAddMember() JS
// ──────────────────────────────────────────────────────────────────────
function openAddMember() {
    editPhotoData = null;
    document.getElementById('editSheetTitle').textContent = 'Tambah Member';
    document.getElementById('editName').value    = '';
    document.getElementById('editNIK').value     = '';
    document.getElementById('editRole').value    = 'Operator';
    document.getElementById('editFactory').value = 'Factory 2';
    document.getElementById('editShift').value   = 'A';
    document.getElementById('editStatus').value  = 'active';
    document.getElementById('editMemberId').value = '';
    document.getElementById('editPhotoPreview').innerHTML = '📷';
    updateMachineSelector();
    openSheet('editSheet');
}

// ──────────────────────────────────────────────────────────────────────
// EDIT MEMBER — menggantikan editMember(id) JS
// Fetch data dari server, isi form
// ──────────────────────────────────────────────────────────────────────
async function loadEditMember(id) {
    try {
        const res  = await fetch(`/admin/members/${id}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const m    = data.member;

        editPhotoData = null;
        document.getElementById('editSheetTitle').textContent = 'Edit Member';
        document.getElementById('editName').value     = m.nama    || '';
        document.getElementById('editNIK').value      = m.nik     || '';
        document.getElementById('editRole').value     = m.jabatan || 'Operator';
        document.getElementById('editFactory').value  = m.factory || 'Factory 2';
        document.getElementById('editShift').value    = m.shift   || 'A';
        document.getElementById('editStatus').value   = m.status  || 'active';
        document.getElementById('editMemberId').value = m.id;

        updateMachineSelector();
        // Timing fix: set mesin setelah dropdown diisi
        setTimeout(() => {
            const sel = document.getElementById('editMesin');
            sel.value = m.mesin || '';
            if (m.mesin && sel.value !== m.mesin) {
                const opt = document.createElement('option');
                opt.value = m.mesin; opt.textContent = m.mesin;
                sel.insertBefore(opt, sel.options[1]);
                sel.value = m.mesin;
            }
        }, 10);

        const pp = document.getElementById('editPhotoPreview');
        pp.innerHTML = m.photo_url
            ? `<img src="${m.photo_url}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`
            : '📷';

        openSheet('editSheet');
    } catch (e) { showToast('Gagal memuat data member', 'error'); }
}

// Foto upload handler — menggantikan handleEditPhoto() JS
function handleEditPhoto(input) {
    const f = input.files[0]; if (!f) return;
    const reader = new FileReader();
    reader.onload = e => {
        editPhotoData = e.target.result;
        document.getElementById('editPhotoPreview').innerHTML =
            `<img src="${editPhotoData}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
    };
    reader.readAsDataURL(f);
    input.value = '';
}

// Machine dropdown — menggantikan updateMachineSelector() JS
function updateMachineSelector() {
    const factory  = document.getElementById('editFactory').value;
    const sel      = document.getElementById('editMesin');
    const machines = FACTORY_MACHINES[factory] || [];
    sel.innerHTML  = '<option value="">-- Pilih Mesin --</option>';
    machines.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m; opt.textContent = m;
        sel.appendChild(opt);
    });
}

// ──────────────────────────────────────────────────────────────────────
// SIMPAN MEMBER (POST/PUT ke server) — menggantikan saveMember() JS
// ──────────────────────────────────────────────────────────────────────
async function saveMember() {
    const name = document.getElementById('editName').value.trim();
    if (!name) { showToast('Nama tidak boleh kosong', 'error'); return; }

    const id = document.getElementById('editMemberId').value;
    const payload = {
        nama:         name,
        nik:          document.getElementById('editNIK').value.trim(),
        jabatan:      document.getElementById('editRole').value,
        factory:      document.getElementById('editFactory').value,
        shift:        document.getElementById('editShift').value,
        mesin:        document.getElementById('editMesin').value,
        status:       document.getElementById('editStatus').value,
        photo_base64: editPhotoData || '',
    };

    showLoading('Menyimpan...');
    try {
        const url    = id ? `/admin/members/${id}` : '/admin/members';
        const method = id ? 'PUT' : 'POST';
        const res    = await apiCall(url, method, payload);
        if (res.ok) {
            closeSheet('editSheet');
            showToast(id ? '✅ Member diupdate' : '✅ Member ditambah', 'success');
            setTimeout(() => window.location.reload(), 700);
        }
    } catch (e) {
        showToast('Gagal menyimpan: ' + e.message, 'error');
    } finally { hideLoading(); }
}

// ──────────────────────────────────────────────────────────────────────
// HAPUS MEMBER — menggantikan deleteMember() JS
// ──────────────────────────────────────────────────────────────────────
async function deleteMember(id) {
    if (!confirm('Hapus member ini?')) return;
    try {
        await apiCall(`/admin/members/${id}`, 'DELETE');
        closeSheet('memberSheet');
        showToast('Member dihapus', 'info');
        setTimeout(() => window.location.reload(), 600);
    } catch (e) { showToast('Gagal hapus: ' + e.message, 'error'); }
}

async function clearAllMembers() {
    if (!confirm('Hapus SEMUA member?\nData absen tidak ikut terhapus.')) return;
    try {
        await apiCall('/admin/members/clear-all', 'DELETE');
        showToast('Semua member dihapus', 'info');
        setTimeout(() => window.location.reload(), 700);
    } catch (e) { showToast('Gagal: ' + e.message, 'error'); }
}

// ══════════════════════════════════════════════════════════════════════
// ABSEN PAGE — menggantikan loadAbsenPage() + renderAbsenList() JS
// Fetch member list dari server, rekaman absen juga dari server
// ══════════════════════════════════════════════════════════════════════
async function loadAbsenPage() {
    const tanggal = document.getElementById('absenDate').value;
    const factory = document.getElementById('absenFactory').value;
    const shift   = document.getElementById('absenShift').value;

    document.getElementById('absenList').innerHTML =
        '<div style="text-align:center;padding:24px;color:#aaa">⏳ Memuat...</div>';

    try {
        // Ambil member list dari server (menggantikan filter dari array lokal)
        const [mRes, aRes] = await Promise.all([
            fetch(`/admin/members/list?factory=${encodeURIComponent(factory)}&shift=${shift}`, {
                headers: { 'Accept': 'application/json' }
            }),
            // Absen records hari ini (menggantikan getAbsenData() dari IDB)
            fetch(`/admin/absence/data?tanggal=${tanggal}&factory=${encodeURIComponent(factory)}&shift=${shift}`, {
                headers: { 'Accept': 'application/json' }
            })
        ]);

        const members    = await mRes.json();
        const savedAbsen = await aRes.json(); // { memberId: { status, reason } }

        if (!members.length) {
            document.getElementById('absenList').innerHTML =
                '<div class="empty-state"><div class="ei">👥</div><div class="et">Tidak ada member untuk factory/shift ini.<br>Import data dulu di tab Import.</div></div>';
            ['asTotal','asHadir','asAbsen'].forEach(id => document.getElementById(id).textContent = 0);
            return;
        }

        // Bangun state lokal dari data tersimpan (menggantikan absenState JS)
        absenState = {};
        members.forEach(m => {
            const saved = savedAbsen[m.id];
            absenState[m.id] = saved
                ? { status: saved.status || 'hadir', reason: saved.reason || '' }
                : { status: 'hadir', reason: '' };
        });

        renderAbsenList(members);
    } catch (e) {
        showToast('Gagal memuat absen: ' + e.message, 'error');
    }
}

function renderAbsenList(members) {
    const container = document.getElementById('absenList');
    container.innerHTML = '';

    members.forEach(m => {
        const rec     = absenState[m.id] || { status: 'hadir', reason: '' };
        const isAbsen = rec.status === 'absen';
        const photoHTML = m.photo
            ? `<img src="${m.photo.startsWith('data:') ? m.photo : '/storage/' + m.photo}">`
            : '👤';

        const row = document.createElement('div');
        row.className = 'absen-member-row';
        row.id = 'arow-' + m.id;
        row.innerHTML = `
            <div class="amr-photo">${photoHTML}</div>
            <div class="amr-info">
                <div class="amr-name">${m.nama}</div>
                <div class="amr-role">${m.jabatan}${m.mesin ? ' — ' + m.mesin : ''}</div>
            </div>
            <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;flex-wrap:wrap">
                <button class="absen-btn hadir ${!isAbsen ? 'active' : ''}"
                    onclick="setAbsenState(${m.id}, 'hadir', '')">✓ Hadir</button>
                <button class="absen-btn absen ${isAbsen ? 'active' : ''}"
                    onclick="setAbsenState(${m.id}, 'absen', document.getElementById('reason-${m.id}').value)">
                    ✗ Absen
                </button>
                <select class="reason-sel ${isAbsen ? 'show' : ''}" id="reason-${m.id}"
                    onchange="if(absenState[${m.id}]?.status==='absen') setAbsenState(${m.id},'absen',this.value)">
                    <option value="Cuti"    ${rec.reason==='Cuti'    ? 'selected':''}>Cuti</option>
                    <option value="Sakit"   ${rec.reason==='Sakit'   ? 'selected':''}>Sakit</option>
                    <option value="Ijin"    ${rec.reason==='Ijin'    ? 'selected':''}>Ijin</option>
                    <option value="Mangkir" ${rec.reason==='Mangkir' ? 'selected':''}>Mangkir</option>
                </select>
            </div>`;
        container.appendChild(row);
    });

    updateAbsenCounts();
}

// Menggantikan setAbsenState() JS — update state + UI tombol langsung
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

// ──────────────────────────────────────────────────────────────────────
// SIMPAN ABSEN — menggantikan saveAbsenData() JS
// POST JSON ke server (bukan localStorage)
// ──────────────────────────────────────────────────────────────────────
async function saveAbsenData() {
    if (!Object.keys(absenState).length) {
        showToast('Buka tab Absen dulu sebelum menyimpan', 'error');
        return;
    }
    const tanggal = document.getElementById('absenDate').value;
    const factory = document.getElementById('absenFactory').value;
    const shift   = document.getElementById('absenShift').value;

    showLoading('Menyimpan data absen...');
    try {
        const res = await apiCall('/admin/absence/save', 'POST', {
            tanggal, factory, shift, records: absenState
        });
        hideLoading();
        const absenCount = Object.values(absenState).filter(v => v.status === 'absen').length;
        const hadirCount = Object.values(absenState).length - absenCount;
        showToast(`✅ Absen disimpan! Hadir: ${hadirCount} | Absen: ${absenCount}`, 'success');
        // Update badge nav
        document.getElementById('absenBadge').textContent = absenCount;
        document.getElementById('absenBadge').classList.toggle('show', absenCount > 0);
        document.getElementById('statToday').textContent = absenCount;
    } catch (e) {
        hideLoading();
        showToast('Gagal menyimpan: ' + e.message, 'error');
    }
}

// Menggantikan broadcastAbsen() JS — di Laravel cukup simpan ulang
async function broadcastAbsen() {
    await saveAbsenData();
    showToast('📡 Data disinkronkan ke Board!', 'success');
}

// ══════════════════════════════════════════════════════════════════════
// REPORT PAGE — menggantikan generateReport() + generateReportDate JS
// Fetch data dari server via AJAX
// ══════════════════════════════════════════════════════════════════════
function changeReportDate(d) {
    const dt = new Date(currentReportDate + 'T00:00:00');
    dt.setDate(dt.getDate() + d);
    currentReportDate = dt.toISOString().split('T')[0];
    renderReportDateLabel();
    generateReport();
}

function renderReportDateLabel() {
    const dt  = new Date(currentReportDate + 'T00:00:00');
    const lbl = dt.toLocaleDateString('id-ID', { weekday:'short', day:'numeric', month:'short', year:'numeric' });
    document.getElementById('reportDateLabel').textContent = lbl;
    // Update link export
    document.getElementById('exportReportBtn').href =
        `/admin/absence/export?tanggal=${currentReportDate}`;
}

async function generateReport() {
    document.getElementById('reportContent').innerHTML =
        '<div style="text-align:center;padding:24px;color:#aaa">⏳ Memuat laporan...</div>';

    try {
        const res  = await fetch(`/admin/absence/report?tanggal=${currentReportDate}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Server mengembalikan view HTML, tapi kita butuh data JSON
        // Jadi kita fetch page lalu parse, atau gunakan endpoint khusus
        // Saat ini: redirect ke halaman rekap yang sudah ada
        window.location.href = `/admin/absence/report?tanggal=${currentReportDate}`;
    } catch (e) {
        document.getElementById('reportContent').innerHTML =
            `<div class="empty-state"><div class="ei">⚠️</div><div class="et">${e.message}</div></div>`;
    }
}

// ══════════════════════════════════════════════════════════════════════
// IMPORT EXCEL — menggantikan processExcelFile() + confirmImport() JS
// JS parse Excel di browser (SheetJS), lalu POST JSON ke server
// ══════════════════════════════════════════════════════════════════════
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('importBox').classList.remove('drag');
    const f = e.dataTransfer.files[0];
    if (f) processExcelFile(f);
}

function handleExcelImport(input) {
    const f = input.files[0]; if (!f) return;
    processExcelFile(f); input.value = '';
}

function processExcelFile(file) {
    showLoading('Membaca file Excel...');
    const reader = new FileReader();
    reader.onload = e => {
        try {
            const wb   = XLSX.read(e.target.result, { type: 'array' });
            const ws   = wb.Sheets[wb.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });
            if (rows.length < 2) { showToast('File kosong atau format salah', 'error'); hideLoading(); return; }

            const hdr     = rows[0].map(h => String(h).toLowerCase().trim());
            const nameIdx = hdr.findIndex(h => h.includes('nama'));
            if (nameIdx === -1) { showToast('Kolom "Nama" tidak ditemukan!', 'error'); hideLoading(); return; }

            const factoryIdx = hdr.findIndex(h => h.includes('factory') || h.includes('pabrik'));
            const shiftIdx   = hdr.findIndex(h => h.includes('shift'));
            const roleIdx    = hdr.findIndex(h => h.includes('jabatan') || h.includes('role'));
            const nikIdx     = hdr.findIndex(h => h.includes('nik') || h.includes('id'));
            const mesinIdx   = hdr.findIndex(h => h.includes('mesin') || h.includes('machine'));

            pendingImport = [];
            for (let i = 1; i < rows.length; i++) {
                const row  = rows[i];
                const name = String(row[nameIdx] ?? '').trim();
                if (!name) continue;
                pendingImport.push({
                    name,
                    factory: factoryIdx > -1 ? normFactory(String(row[factoryIdx])) : 'Factory 2',
                    shift:   shiftIdx   > -1 ? normShift(String(row[shiftIdx]))     : 'A',
                    role:    roleIdx    > -1 ? normRole(String(row[roleIdx]))        : 'Operator',
                    nik:     nikIdx     > -1 ? String(row[nikIdx] ?? '').trim()     : '',
                    mesin:   mesinIdx   > -1 ? String(row[mesinIdx] ?? '').trim()   : '',
                });
            }
            renderImportPreview();
            hideLoading();
        } catch (ex) { showToast('Error: ' + ex.message, 'error'); hideLoading(); }
    };
    reader.readAsArrayBuffer(file);
}

// Normalize helpers — menggantikan normalizeFactory/Shift/Role() JS
function normFactory(v) {
    const l = v.toLowerCase();
    return (l.includes('3') || l.includes('4')) ? 'Factory 3 & 4' : 'Factory 2';
}
function normShift(v) { return v.trim().toUpperCase() === 'B' ? 'B' : 'A'; }
function normRole(v) {
    const l = v.toLowerCase();
    if (l.includes('spv') || l.includes('supervisor')) return 'SPV';
    if (l.includes('gl') || l.includes('group'))       return 'GL';
    if (l.includes('tl') || l.includes('team'))        return 'TL';
    if (l.includes('ky'))                              return 'KY';
    return 'Operator';
}

function renderImportPreview() {
    if (!pendingImport.length) { document.getElementById('importPreviewArea').style.display = 'none'; return; }
    document.getElementById('importCount').textContent = pendingImport.length;
    document.getElementById('importPreviewArea').style.display = 'block';
    document.getElementById('importPreview').innerHTML = `
        <table>
            <thead><tr><th>#</th><th>Nama</th><th>Factory</th><th>Shift</th><th>Jabatan</th><th>Mesin</th><th>NIK</th></tr></thead>
            <tbody>
                ${pendingImport.slice(0, 50).map((m, i) =>
                    `<tr><td>${i+1}</td><td>${m.name}</td><td>${m.factory}</td><td>${m.shift}</td><td>${m.role}</td><td>${m.mesin||'-'}</td><td>${m.nik||'-'}</td></tr>`
                ).join('')}
                ${pendingImport.length > 50
                    ? `<tr><td colspan="7" style="text-align:center;color:#888">... dan ${pendingImport.length-50} member lainnya</td></tr>`
                    : ''}
            </tbody>
        </table>`;
    showToast(`${pendingImport.length} member siap diimport`, 'info');
}

async function confirmImport() {
    if (!pendingImport.length) return;
    // OK=tambahkan, Cancel=ganti semua (sama dengan logika JS asli)
    const addMode = confirm(`Import ${pendingImport.length} member?\n\nOK = Tambahkan ke data yang ada\nCancel = Ganti semua data`);
    showLoading(`Mengimport ${pendingImport.length} member...`);
    try {
        const res = await apiCall('/admin/members/import', 'POST', {
            members: pendingImport,
            replace: !addMode,   // replace=true → hapus semua dulu
        });
        hideLoading();
        clearImport();
        showToast(`${res.added} member diimport${res.skipped ? ', ' + res.skipped + ' dilewati' : ''}!`, 'success');
        setTimeout(() => window.location.reload(), 800);
    } catch (e) { hideLoading(); showToast('Gagal import: ' + e.message, 'error'); }
}

function clearImport() {
    pendingImport = [];
    document.getElementById('importPreviewArea').style.display = 'none';
    document.getElementById('importPreview').innerHTML = '';
}

// ══════════════════════════════════════════════════════════════════════
// UTILITY
// ══════════════════════════════════════════════════════════════════════
function openSheet(id)  { document.getElementById(id).classList.add('show'); }
function closeSheet(id) { document.getElementById(id).classList.remove('show'); }
function showLoading(msg) {
    document.getElementById('loadingEl').classList.add('show');
    document.getElementById('loadingText').textContent = msg || 'Loading...';
}
function hideLoading() { document.getElementById('loadingEl').classList.remove('show'); }
function showToast(msg, type = 'info') {
    const t = document.getElementById('toastEl');
    t.textContent = msg; t.className = `toast ${type} show`;
    clearTimeout(t._t); t._t = setTimeout(() => t.classList.remove('show'), 2800);
}

// apiCall — wrapper fetch JSON dengan CSRF (menggantikan api() di layout)
async function apiCall(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    };
    if (body) opts.body = JSON.stringify(body);
    const res  = await fetch(url, opts);
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Request gagal');
    return data;
}

// ══════════════════════════════════════════════════════════════════════
// INIT
// ══════════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('absenDate').value = new Date().toISOString().split('T')[0];
    renderReportDateLabel();
});
</script>
@endpush
