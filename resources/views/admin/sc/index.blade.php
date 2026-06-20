@extends('layouts.admin')
@section('title', 'SC Management')

@push('styles')
    <style>
        .gm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .gm-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            overflow: hidden;
            transition: transform .15s, box-shadow .15s;
            display: flex;
            flex-direction: column;
        }

        .gm-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .13);
        }

        .gm-card-header {
            padding: 14px 16px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
        }

        .gm-card-order {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .gm-card-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: .5px;
            flex: 1;
        }

        .gm-card-body {
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .gm-card-meta {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            color: #888;
            font-weight: 600;
        }

        .sc-admin-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            margin-top: 4px;
            border: 1px dashed #ddd;
        }
        .sc-admin-label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 9px;
            font-weight: 800;
            color: #1f3c88;
            text-transform: uppercase;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .gm-card-actions {
            display: flex;
            gap: 6px;
            padding: 0 16px 12px;
        }

        .gm-act-btn {
            flex: 1;
            padding: 7px 0;
            border-radius: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            border: 1.5px solid;
            transition: all .12s;
            text-align: center;
        }

        .gm-act-edit {
            border-color: #1f3c88;
            color: #1f3c88;
            background: #eef1fa;
        }

        .gm-act-edit:hover {
            background: #dde4f5;
        }

        .gm-act-del {
            border-color: #e74c3c;
            color: #e74c3c;
            background: #fdeaea;
        }

        .gm-act-del:hover {
            background: #fbd0d0;
        }

        .gm-add-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #1f3c88, #2e57d4);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            box-shadow: 0 3px 12px rgba(31, 60, 136, .35);
            transition: opacity .15s, transform .1s;
            white-space: nowrap;
        }

        .gm-add-btn:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        .gm-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 12px;
        }

        .gm-field label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #666;
        }

        .gm-field input {
            padding: 10px 12px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            transition: border-color .15s;
            box-sizing: border-box;
            width: 100%;
            background: #fff;
        }

        .gm-field input:focus {
            outline: none;
            border-color: #1f3c88;
        }

        .gm-save-btn {
            width: 100%;
            padding: 13px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #1f3c88, #2e57d4);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            margin-top: 6px;
            margin-bottom: 18px;
            box-shadow: 0 4px 14px rgba(31, 60, 136, .35);
            transition: opacity .15s;
        }

        .gm-save-btn:hover {
            opacity: .92;
        }

        .gm-save-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        /* ── Color Chip Selector ── */
        .gm-chip-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-top: 6px;
        }

        .gm-chip {
            height: 38px;
            border-radius: 10px;
            border: 2.5px solid transparent;
            cursor: pointer;
            transition: transform .12s, box-shadow .12s, border-color .12s;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gm-chip:hover {
            transform: scale(1.06);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .22);
        }

        .gm-chip.active {
            border-color: #fff;
            box-shadow: 0 0 0 3px rgba(31, 60, 136, .55);
            transform: scale(1.08);
        }

        .gm-chip-check {
            color: #fff;
            font-size: 16px;
            font-weight: 900;
            text-shadow: 0 1px 3px rgba(0, 0, 0, .5);
            display: none;
        }

        .gm-chip.active .gm-chip-check {
            display: block;
        }

        .gm-chip-custom {
            background: #f0f0f0;
            border: 2px dashed #ccc;
            color: #888;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            flex-direction: column;
            gap: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gm-chip-custom.active {
            border-color: #1f3c88;
            color: #1f3c88;
            background: #eef1fa;
            box-shadow: 0 0 0 3px rgba(31, 60, 136, .35);
        }

        .gm-custom-picker-wrap {
            display: none;
            margin-top: 10px;
            background: #f8f9fc;
            border-radius: 10px;
            border: 1.5px solid #e0e0e0;
            padding: 12px;
            gap: 10px;
            flex-direction: column;
        }

        .gm-custom-picker-wrap.show {
            display: flex;
        }

        .gm-custom-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .gm-custom-row label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color: #666;
            text-transform: uppercase;
            width: 60px;
            flex-shrink: 0;
        }

        .gm-custom-row input[type="color"] {
            width: 44px;
            height: 34px;
            border-radius: 8px;
            border: 1.5px solid #ddd;
            padding: 2px;
            cursor: pointer;
            background: #fff;
        }

        .gm-gradient-preview {
            height: 34px;
            border-radius: 8px;
            flex: 1;
            border: 1.5px solid #e0e0e0;
        }

        .admin-setup-section {
            background: #f0f7ff;
            border-radius: 12px;
            padding: 16px;
            margin-top: 10px;
            border: 1.5px solid #cce3ff;
        }
        .admin-setup-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            color: #0056b3;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
@endpush

@section('content')
    <div style="padding: 6px 0 110px;">

        {{-- Page Header --}}
        <div style="margin-bottom:18px;">
            <div class="section-title" style="margin-bottom:4px;">🏢 SC Management</div>
            <div class="mm-page-sub">
                Kelola Service Center: buat unit baru dengan template otomatis dan admin terisolasi.
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="mm-toolbar">
            <div style="font-family:'Roboto Condensed',sans-serif;font-size:13px;color:#666;">
                <strong style="color:#1f3c88;">{{ $scs->count() }}</strong> SC terdaftar
            </div>
            <button class="gm-add-btn" id="gmAddBtn" onclick="openScModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah SC Baru
            </button>
        </div>

        {{-- Card Grid --}}
        <div class="gm-grid" id="gmGrid">
            @forelse($scs as $sc)
                <div class="gm-card" id="sc-card-{{ $sc->id }}">
                    <div class="gm-card-header" style="background: {{ $sc->gradient }};">
                        <div class="gm-card-order">{{ $sc->order_index }}</div>
                        <div class="gm-card-name">{{ $sc->name }}</div>
                        <span style="font-family:'Roboto Condensed',sans-serif;font-size:11px;font-weight:900;background:rgba(255,255,255,.25);padding:2px 8px;border-radius:10px;">
                            {{ $sc->short_label ?? 'SC' }}
                        </span>
                    </div>
                    <div class="gm-card-body">
                        <div class="gm-card-meta">🔗 Slug: {{ $sc->slug }}</div>
                        <div class="gm-card-meta">🏭 Groups: <strong>{{ $sc->factories_count }}</strong></div>
                        @if($sc->detail_departemen)
                            <div class="gm-card-meta">🏢 Detail: <strong>{{ $sc->detail_departemen }}</strong></div>
                        @endif

                        <div class="sc-admin-info">
                            <div class="sc-admin-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                SC Administrator
                            </div>
                            <div style="font-family:'Roboto Condensed',sans-serif;font-size:12px;font-weight:700;color:#333;">
                                {{ $sc->admin_user->name ?? 'Belum ditentukan' }}
                            </div>
                            <div style="font-family:'Roboto Condensed',sans-serif;font-size:10px;color:#888;">
                                @ {{ $sc->admin_user->username ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="gm-card-actions">
                        <button class="gm-act-btn gm-act-edit"
                            onclick="openScModal({{ $sc->id }}, '{{ addslashes($sc->name) }}', '{{ addslashes($sc->short_label) }}', '{{ addslashes($sc->gradient) }}', '{{ addslashes($sc->detail_departemen) }}', '{{ addslashes($sc->slug) }}')">
                            ✏️ Edit
                        </button>
                        @if($sc->id !== 1)
                            <button class="gm-act-btn gm-act-del"
                                onclick="deleteSc({{ $sc->id }}, '{{ addslashes($sc->name) }}')">
                                🗑️
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="gm-empty" style="grid-column:1/-1;">
                    <div class="gm-empty-icon">🏢</div>
                    <div>Belum ada Service Center tambahan. Klik <strong>Tambah SC Baru</strong>.</div>
                </div>
            @endforelse
        </div>

    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal-overlay" id="scModal">
        <div class="modal-sheet" style="max-height:92vh;overflow-y:auto">
            <div class="modal-sheet-handle"></div>
            <div class="modal-sheet-header">
                <h3 id="scModalTitle">Tambah SC Baru</h3>
                <button class="modal-sheet-close" onclick="closeSheet('scModal')">✕</button>
            </div>
            <div class="mm-modal-body">
                <input type="hidden" id="scId">

                <div class="gm-field">
                    <label>Nama Service Center *</label>
                    <input type="text" id="scName" placeholder="Contoh: Service Center 2" autocomplete="off">
                </div>

                <div class="gm-field">
                    <label>Slug (Unique ID) *</label>
                    <input type="text" id="scSlug" placeholder="contoh: sc-2" autocomplete="off">
                </div>

                <div class="gm-field">
                    <label>Short Label</label>
                    <input type="text" id="scShortLabel" placeholder="e.g., SC2" maxlength="10" autocomplete="off">
                </div>

                <div class="gm-field">
                    <label>Detail / Lokasi</label>
                    <input type="text" id="scDetail" placeholder="e.g., Plant Cikarang Barat" autocomplete="off">
                </div>

                <div class="gm-field">
                    <label>Warna Tema SC</label>
                    <input type="hidden" id="scGradient">
                    <div class="gm-chip-grid" id="scChipGrid"></div>
                    <div class="gm-custom-picker-wrap" id="scCustomPickerWrap">
                        <div class="gm-custom-row">
                            <label>Warna 1</label>
                            <input type="color" id="scColor1" value="#1f3c88" oninput="scBuildCustomGradient()">
                            <label style="width:auto">→ Warna 2</label>
                            <input type="color" id="scColor2" value="#2e57d4" oninput="scBuildCustomGradient()">
                            <div class="gm-gradient-preview" id="scGradPreview"></div>
                        </div>
                    </div>
                </div>

                {{-- Admin Setup Section - Only for NEW SC --}}
                <div id="adminSetupSection" class="admin-setup-section">
                    <div class="admin-setup-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                        Setup Admin Akun
                    </div>
                    <div class="gm-field">
                        <label>Nama Admin *</label>
                        <input type="text" id="adminName" placeholder="Nama lengkap admin" autocomplete="off">
                    </div>
                    <div style="display:flex;gap:10px">
                        <div class="gm-field" style="flex:1">
                            <label>Username *</label>
                            <input type="text" id="adminUsername" placeholder="Username login" autocomplete="off">
                        </div>
                        <div class="gm-field" style="flex:1">
                            <label>Password *</label>
                            <input type="password" id="adminPassword" placeholder="Min. 6 karakter">
                        </div>
                    </div>
                    <small style="color:#666;font-size:10px;font-family:'Roboto Condensed',sans-serif;line-height:1.4;display:block">
                        ⚠️ Akun ini akan otomatis memiliki role <b>Administrator</b> untuk SC baru ini dan memiliki akses penuh ke template yang dikloning.
                    </small>
                </div>

                <button class="gm-save-btn" id="scSaveBtn" onclick="saveSc()">🚀 Deploy Service Center</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let _scEditId = null;

        const SC_PRESETS = [
            { label: 'Navy', value: 'linear-gradient(135deg,#1f3c88,#2e57d4)' },
            { label: 'Green', value: 'linear-gradient(135deg,#2e7d32,#56ab2f)' },
            { label: 'Red', value: 'linear-gradient(135deg,#c0392b,#e74c3c)' },
            { label: 'Orange', value: 'linear-gradient(135deg,#e65100,#ff7043)' },
            { label: 'Purple', value: 'linear-gradient(135deg,#6a1b9a,#ab47bc)' },
            { label: 'Teal', value: 'linear-gradient(135deg,#00695c,#26a69a)' },
            { label: 'Pink', value: 'linear-gradient(135deg,#ad1457,#e91e63)' },
            { label: 'Amber', value: 'linear-gradient(135deg,#e65100,#fbc02d)' },
            { label: 'Slate', value: 'linear-gradient(135deg,#37474f,#607d8b)' },
            { label: 'Brown', value: 'linear-gradient(135deg,#4e342e,#8d6e63)' },
        ];

        function scBuildChips() {
            const grid = document.getElementById('scChipGrid');
            if (!grid) return;
            let html = SC_PRESETS.map((p, i) =>
                `<div class="gm-chip" data-idx="${i}" style="background:${p.value}" title="${p.label}" onclick="scSelectChip(${i})">
                    <span class="gm-chip-check">✓</span>
                 </div>`
            ).join('');
            html += `<div class="gm-chip gm-chip-custom" data-idx="custom" onclick="scSelectChip('custom')">
                        <span style="font-size:18px">🎨</span>
                        <span style="font-size:9px;font-family:'Roboto Condensed',sans-serif;font-weight:800">Custom</span>
                        <span class="gm-chip-check" style="display:none">✓</span>
                     </div>`;
            grid.innerHTML = html;
        }

        function scSelectChip(idx) {
            document.querySelectorAll('#scChipGrid .gm-chip').forEach(c => c.classList.remove('active'));
            const wrap = document.getElementById('scCustomPickerWrap');

            if (idx === 'custom') {
                document.querySelector('#scChipGrid .gm-chip-custom')?.classList.add('active');
                wrap?.classList.add('show');
                scBuildCustomGradient();
            } else {
                wrap?.classList.remove('show');
                document.querySelector(`#scChipGrid .gm-chip[data-idx="${idx}"]`)?.classList.add('active');
                const preset = SC_PRESETS[idx];
                if (preset) document.getElementById('scGradient').value = preset.value;
            }
        }

        function scBuildCustomGradient() {
            const c1 = document.getElementById('scColor1')?.value || '#1f3c88';
            const c2 = document.getElementById('scColor2')?.value || '#2e57d4';
            const grad = `linear-gradient(135deg,${c1},${c2})`;
            document.getElementById('scGradient').value = grad;
            const preview = document.getElementById('scGradPreview');
            if (preview) preview.style.background = grad;
        }

        function scSyncChipSelection(gradient) {
            const idx = SC_PRESETS.findIndex(p => p.value === gradient);
            if (idx >= 0) {
                scSelectChip(idx);
            } else if (gradient) {
                const match = gradient.match(/#[0-9a-fA-F]{3,6}/g);
                if (match && match.length >= 2) {
                    document.getElementById('scColor1').value = match[0];
                    document.getElementById('scColor2').value = match[1];
                }
                scSelectChip('custom');
            }
        }

        document.addEventListener('DOMContentLoaded', () => scBuildChips());

        function openScModal(id = null, name = '', shortLabel = '', gradient = '', detail = '', slug = '') {
            _scEditId = id;
            const isEdit = !!id;
            document.getElementById('scModalTitle').textContent = isEdit ? '✏️ Edit SC' : '🚀 Tambah SC Baru';
            document.getElementById('scId').value = id ?? '';
            document.getElementById('scName').value = name;
            document.getElementById('scShortLabel').value = shortLabel;
            document.getElementById('scGradient').value = gradient;
            document.getElementById('scDetail').value = detail;
            document.getElementById('scSlug').value = slug;
            
            document.getElementById('scSaveBtn').textContent = isEdit ? '💾 Simpan Perubahan' : '🚀 Deploy Service Center';
            
            // Hide admin setup on edit
            document.getElementById('adminSetupSection').style.display = isEdit ? 'none' : 'block';

            document.querySelectorAll('#scChipGrid .gm-chip').forEach(c => c.classList.remove('active'));
            document.getElementById('scCustomPickerWrap')?.classList.remove('show');
            if (gradient) scSyncChipSelection(gradient);
            
            openSheet('scModal');
            setTimeout(() => document.getElementById('scName').focus(), 200);
        }

        async function saveSc() {
            const id = _scEditId;
            const name = document.getElementById('scName').value.trim();
            const slug = document.getElementById('scSlug').value.trim();
            const short = document.getElementById('scShortLabel').value.trim();
            const grad = document.getElementById('scGradient').value.trim();
            const detail = document.getElementById('scDetail').value.trim();
            
            if (!name || !slug) { showToast('Nama and Slug tidak boleh kosong.', 'error'); return; }

            const body = { name, slug, short_label: short, gradient: grad, detail_departemen: detail };

            if (!id) {
                const adminName = document.getElementById('adminName').value.trim();
                const adminUser = document.getElementById('adminUsername').value.trim();
                const adminPass = document.getElementById('adminPassword').value.trim();
                if (!adminName || !adminUser || !adminPass) {
                    showToast('Admin setup harus diisi untuk SC baru.', 'error'); return;
                }
                body.admin_name = adminName;
                body.admin_username = adminUser;
                body.admin_password = adminPass;
            }

            const btn = document.getElementById('scSaveBtn');
            btn.disabled = true; btn.textContent = '⏳ Processing…';

            try {
                const url = id ? `/admin/sc-management/${id}` : '/admin/sc-management';
                const method = id ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify(body),
                });
                const data = await res.json();

                if (!res.ok) {
                    const msg = data.errors ? Object.values(data.errors)[0][0] : (data.message ?? 'Gagal menyimpan.');
                    showToast(msg, 'error'); return;
                }

                showToast(id ? '✅ SC berhasil diperbarui!' : '🚀 SC berhasil di-deploy!', 'success');
                closeSheet('scModal');
                setTimeout(() => window.location.reload(), 800);
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            } finally {
                btn.disabled = false; btn.textContent = id ? '💾 Simpan Perubahan' : '🚀 Deploy Service Center';
            }
        }

        async function deleteSc(id, name) {
            if (!confirm(`Hapus Service Center "${name}"?\n\nSEMUA data terkait (Factories, Sections, Statuses, Configs, Users) akan dihapus secara permanen!`)) return;

            try {
                const res = await fetch(`/admin/sc-management/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (!data.ok) { showToast(data.message ?? 'Gagal menghapus.', 'error'); return; }

                showToast('🗑️ SC dihapus.', 'success');
                const card = document.getElementById(`sc-card-${id}`);
                if (card) { card.style.transition = 'opacity .3s'; card.style.opacity = '0'; setTimeout(() => { card.remove(); }, 300); }
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            }
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet('scModal'); });
    </script>
@endpush
