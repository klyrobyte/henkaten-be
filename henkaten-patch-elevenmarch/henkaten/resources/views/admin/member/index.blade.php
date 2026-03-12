@extends('layouts.admin')
@section('title', 'Member Management')

@section('content')

    <style>
        .page {
            display: none !important;
        }

        .page.active {
            display: block !important;
        }

        body>nav.bottom-nav {
            display: none !important;
        }
    </style>

    <div class="main">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- PAGE: MEMBERS --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="page active" id="page-members">

            <div class="filter-bar">
                <div class="filter-chip {{ $factory === 'all' ? 'active' : '' }}" onclick="applyFilter('all','all')">
                    Semua</div>
                <div class="filter-chip orange {{ $factory === 'Factory 2' ? 'active' : '' }}"
                    onclick="applyFilter('Factory 2', currentShift)">Factory 2</div>
                <div class="filter-chip navy {{ $factory === 'Factory 3 & 4' ? 'active' : '' }}"
                    onclick="applyFilter('Factory 3 & 4', currentShift)">Factory 3&4</div>
                <div class="filter-chip {{ $shift === 'A' ? 'active' : '' }}"
                    onclick="applyFilter(currentFactory, currentShift==='A'?'all':'A')">Shift A</div>
                <div class="filter-chip {{ $shift === 'B' ? 'active' : '' }}"
                    onclick="applyFilter(currentFactory, currentShift==='B'?'all':'B')">Shift B</div>
            </div>

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
                    <div class="sl">F 3&4</div>
                </div>
                <div class="stat-card">
                    <div class="sv" id="statToday" style="color:var(--red)">{{ $stats['absen_today'] }}</div>
                    <div class="sl">Absen Hr Ini</div>
                </div>
            </div>

            <div class="search-bar">
                <span class="si">🔍</span>
                <input type="text" id="searchInput" placeholder="Cari nama member..." oninput="filterGrid()">
            </div>

            <div style="display:flex;justify-content:flex-end;margin-bottom:10px">
                <button class="btn btn-sm btn-orange" onclick="openAddMember()">➕ Tambah Member</button>
            </div>

            <div class="member-grid" id="memberGrid">
                @forelse($members as $i => $m)
                    @php
                        $rec = \App\Models\AbsenceRecord::where([
                            'tanggal' => today()->toDateString(),
                            'factory' => $m->factory,
                            'shift' => $m->shift,
                            'member_id' => $m->id,
                        ])->first();
                        $isAbsent = $rec && $rec->status === 'absen';
                    @endphp
                    <div class="member-card {{ $isAbsent ? 'absent-today' : '' }}" data-name="{{ strtolower($m->nama) }}"
                        data-factory="{{ $m->factory }}" data-shift="{{ $m->shift }}" onclick="openMemberDetail({{ $m->id }})">
                        <span class="mc-number">#{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
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

            {{-- Kelola Data — tetap di page members, bawah grid --}}
            <div class="card" style="margin-top:20px">
                <h3>🗑️ Kelola Data</h3>
                <div style="display:flex;flex-direction:column;gap:10px">
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0">
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

        </div>{{-- /page-members --}}

    </div>{{-- /main --}}

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- MODAL: MEMBER DETAIL SHEET --}}
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
    {{-- MODAL: ADD / EDIT MEMBER SHEET --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
    <div class="modal-overlay" id="editSheet">
        <div class="modal-sheet">
            <div class="modal-handle"></div>
            <div class="modal-header">
                <h3 id="editSheetTitle">Tambah Member</h3>
                <button class="modal-close" onclick="closeSheet('editSheet')">✕</button>
            </div>
            <div class="modal-body">
                <div style="text-align:center;margin-bottom:16px">
                    <div class="photo-upload-area" id="editPhotoPreview"
                        onclick="document.getElementById('editPhotoInput').click()">📷</div>
                    <input type="file" id="editPhotoInput" accept="image/*" style="display:none"
                        onchange="handleEditPhoto(this)">
                    <div style="font-size:11px;color:#aaa">Tap untuk upload foto</div>
                </div>

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
    <script>
        const FACTORY_MACHINES = @json($mesinList);
        const CSRF = '{{ csrf_token() }}';

        let currentFactory = '{{ $factory }}';
        let currentShift = '{{ $shift }}';
        let editPhotoData = null;

        // ── Navigasi tab ──────────────────────────────────────────────
        function goPage(page) {
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('page-' + page)?.classList.add('active');
            document.getElementById('nav-' + page)?.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ── Filter chips ──────────────────────────────────────────────
        function applyFilter(factory, shift) {
            currentFactory = factory;
            currentShift = shift;

            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            const chips = document.querySelectorAll('.filter-chip');
            if (factory === 'all') chips[0]?.classList.add('active');
            if (factory === 'Factory 2') chips[1]?.classList.add('active');
            if (factory === 'Factory 3 & 4') chips[2]?.classList.add('active');
            if (shift === 'A') chips[3]?.classList.add('active');
            if (shift === 'B') chips[4]?.classList.add('active');

            filterGrid();
        }

        function filterGrid() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('#memberGrid .member-card');
            let visible = 0;

            cards.forEach(card => {
                const matchF = currentFactory === 'all' || card.dataset.factory === currentFactory;
                const matchS = currentShift === 'all' || card.dataset.shift === currentShift;
                const matchQ = !q || (card.dataset.name || '').includes(q);
                const show = matchF && matchS && matchQ;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

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

        // ── Member detail sheet ───────────────────────────────────────
        async function openMemberDetail(id) {
            openSheet('memberSheet');
            document.getElementById('memberSheetTitle').textContent = 'Detail Member';
            document.getElementById('memberSheetBody').innerHTML =
                '<div style="text-align:center;padding:24px;color:#aaa">⏳ Memuat...</div>';

            try {
                const res = await fetch(`/admin/members/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
                const data = await res.json();
                const m = data.member;
                const hist = data.history;

                document.getElementById('memberSheetTitle').textContent = m.nama;

                const photoHTML = m.photo_url ? `<img src="${m.photo_url}" style="width:100%;height:100%;object-fit:cover">` : '👤';
                const badgeClass = { Operator: 'op', SPV: 'spv', TL: 'tl', GL: 'gl', KY: 'ky' }[m.jabatan] ?? 'op';
                const histHTML = hist.length
                    ? hist.map(h => {
                        const sc = { hadir: 'hi-hadir', Cuti: 'hi-cuti', Sakit: 'hi-sakit', Ijin: 'hi-ijin', Mangkir: 'hi-ijin' }[h.status === 'hadir' ? 'hadir' : (h.reason || 'hadir')] ?? 'hi-hadir';
                        const lbl = h.status === 'hadir' ? 'Hadir' : (h.reason || 'Absen');
                        const tgl = new Date(h.tanggal + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
                        return `<div class="history-item"><span class="hi-date">${tgl}</span><span class="hi-badge ${sc}">${lbl}</span></div>`;
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
                    <button class="btn btn-sm btn-danger" onclick="deleteMember(${m.id})">🗑️</button>
                </div>
                <div class="section-title">Riwayat Absen (10 Terakhir)</div>
                <div class="history-list">${histHTML}</div>`;
            } catch (e) {
                document.getElementById('memberSheetBody').innerHTML =
                    `<div style="text-align:center;padding:24px;color:var(--red)">Gagal memuat: ${e.message}</div>`;
            }
        }

        // ── Tambah / Edit member ──────────────────────────────────────
        function openAddMember() {
            editPhotoData = null;
            document.getElementById('editSheetTitle').textContent = 'Tambah Member';
            document.getElementById('editName').value = '';
            document.getElementById('editNIK').value = '';
            document.getElementById('editRole').value = 'Operator';
            document.getElementById('editFactory').value = 'Factory 2';
            document.getElementById('editShift').value = 'A';
            document.getElementById('editStatus').value = 'active';
            document.getElementById('editMemberId').value = '';
            document.getElementById('editPhotoPreview').innerHTML = '📷';
            updateMachineSelector();
            openSheet('editSheet');
        }

        async function loadEditMember(id) {
            try {
                const res = await fetch(`/admin/members/${id}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const m = data.member;

                editPhotoData = null;
                document.getElementById('editSheetTitle').textContent = 'Edit Member';
                document.getElementById('editName').value = m.nama || '';
                document.getElementById('editNIK').value = m.nik || '';
                document.getElementById('editRole').value = m.jabatan || 'Operator';
                document.getElementById('editFactory').value = m.factory || 'Factory 2';
                document.getElementById('editShift').value = m.shift || 'A';
                document.getElementById('editStatus').value = m.status || 'active';
                document.getElementById('editMemberId').value = m.id;

                updateMachineSelector();
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

                document.getElementById('editPhotoPreview').innerHTML = m.photo_url
                    ? `<img src="${m.photo_url}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`
                    : '📷';

                openSheet('editSheet');
            } catch (e) { showToast('Gagal memuat data member', 'error'); }
        }

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

        function updateMachineSelector() {
            const factory = document.getElementById('editFactory').value;
            const sel = document.getElementById('editMesin');
            const machines = FACTORY_MACHINES[factory] || [];
            sel.innerHTML = '<option value="">-- Pilih Mesin --</option>';
            machines.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m; opt.textContent = m;
                sel.appendChild(opt);
            });
        }

        async function saveMember() {
            const name = document.getElementById('editName').value.trim();
            if (!name) { showToast('Nama tidak boleh kosong', 'error'); return; }

            const id = document.getElementById('editMemberId').value;
            const payload = {
                nama: name,
                nik: document.getElementById('editNIK').value.trim(),
                jabatan: document.getElementById('editRole').value,
                factory: document.getElementById('editFactory').value,
                shift: document.getElementById('editShift').value,
                mesin: document.getElementById('editMesin').value,
                status: document.getElementById('editStatus').value,
                photo_base64: editPhotoData || '',
            };

            showLoading('Menyimpan...');
            try {
                // Use fetch directly so we control the full response
                const res = await fetch(id ? `/admin/members/${id}` : '/admin/members', {
                    method: id ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok) {
                    // Laravel validation errors come in data.errors
                    const msg = data.message || data.errors
                        ? Object.values(data.errors || {}).flat().join(', ')
                        : 'Terjadi kesalahan';
                    throw new Error(msg);
                }

                closeSheet('editSheet');
                showToast(id ? '✅ Member diupdate' : '✅ Member ditambah', 'success');
                setTimeout(() => window.location.reload(), 700);

            } catch (e) {
                showToast('Gagal menyimpan: ' + e.message, 'error');
            } finally {
                hideLoading();
            }
        }
        // ── Hapus member ──────────────────────────────────────────────
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

        // ── Utility ───────────────────────────────────────────────────
        function apiCall(url, method = 'GET', body = null) {
            return api(url, method, body);
        }
    </script>
@endpush