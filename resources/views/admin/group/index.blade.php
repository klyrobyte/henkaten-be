@extends('layouts.admin')
@section('title', 'Group Management')

@push('styles')
    <style>
        .gm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
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

        .gm-empty {
            text-align: center;
            padding: 60px 20px;
            color: #aaa;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
        }

        .gm-empty-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        /* ── Color Chip Selector ─────────────────────────────── */
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
    </style>
@endpush

@section('content')
    <div style="padding: 6px 0 110px;">

        {{-- Page Header --}}
        <div style="margin-bottom:18px;">
            <div class="section-title" style="margin-bottom:4px;">🏭 Group Management</div>
            <div class="mm-page-sub">
                Kelola Factory (#Head): tambah, edit, hapus, dan urutkan factory.
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="mm-toolbar">
            <div style="font-family:'Roboto Condensed',sans-serif;font-size:13px;color:#666;">
                <strong style="color:#1f3c88;">{{ $factories->count() }}</strong> factory terdaftar
            </div>
            <button class="gm-add-btn" id="gmAddBtn" onclick="openGroupModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Factory
            </button>
        </div>

        {{-- Card Grid --}}
        <div class="gm-grid" id="gmGrid">
            @forelse($factories as $f)
                <div class="gm-card" id="gm-card-{{ $f->id }}">
                    <div class="gm-card-header" style="background: {{ $f->gradient }};">
                        <div class="gm-card-order">{{ $f->order_index }}</div>
                        <div class="gm-card-name">{{ $f->name }}</div>
                        <span
                            style="font-family:'Roboto Condensed',sans-serif;font-size:11px;font-weight:900;background:rgba(255,255,255,.25);padding:2px 8px;border-radius:10px;">
                            {{ $f->short_label }}
                        </span>
                    </div>
                    <div class="gm-card-body">
                        <div class="gm-card-meta">🔗 Slug: {{ $f->slug }}</div>
                        <div class="gm-card-meta">📋 Sections: <strong id="gm-sec-count-{{ $f->id }}"> -</strong></div>
                    </div>
                    <div class="gm-card-actions">
                        <button class="gm-act-btn gm-act-edit"
                            onclick="openGroupModal({{ $f->id }}, '{{ addslashes($f->name) }}', '{{ addslashes($f->short_label) }}', '{{ addslashes($f->gradient) }}', '{{ addslashes($f->warna_header) }}')">
                            ✏️ Edit
                        </button>
                        <button class="gm-act-btn gm-act-del"
                            onclick="deleteFactory({{ $f->id }}, '{{ addslashes($f->name) }}')">
                            🗑️
                        </button>
                    </div>
                </div>
            @empty
                <div class="gm-empty" style="grid-column:1/-1;">
                    <div class="gm-empty-icon">🏭</div>
                    <div>Belum ada factory. Klik <strong>Tambah Factory</strong> untuk mulai.</div>
                </div>
            @endforelse
        </div>

    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal-overlay" id="groupModal">
        <div class="modal-sheet" style="max-height:92vh;overflow-y:auto">
            <div class="modal-sheet-handle"></div>
            <div class="modal-sheet-header">
                <h3 id="groupModalTitle">Tambah Factory</h3>
                <button class="modal-sheet-close" onclick="closeSheet('groupModal')">✕</button>
            </div>
            <div class="mm-modal-body">
                <input type="hidden" id="gmFactoryId">

                <div class="gm-field">
                    <label>Nama Factory *</label>
                    <input type="text" id="gmName" placeholder="Contoh: Factory 5" autocomplete="off">
                </div>

                <div class="gm-field">
                    <label>Label Singkat (opsional)</label>
                    <input type="text" id="gmShortLabel" placeholder="Contoh: F5" maxlength="10" autocomplete="off">
                    <small style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif;">Kosongkan untuk
                        auto-generate dari nama.</small>
                </div>

                <div class="gm-field">
                    <label>Background Gradient</label>
                    <input type="hidden" id="gmGradient">
                    <div class="gm-chip-grid" id="gmChipGrid"></div>
                    <div class="gm-custom-picker-wrap" id="gmCustomPickerWrap">
                        <div class="gm-custom-row">
                            <label>Warna 1</label>
                            <input type="color" id="gmColor1" value="#1f3c88" oninput="gmBuildCustomGradient()">
                            <label style="width:auto">→ Warna 2</label>
                            <input type="color" id="gmColor2" value="#2e57d4" oninput="gmBuildCustomGradient()">
                            <div class="gm-gradient-preview" id="gmGradPreview"></div>
                        </div>
                        <small style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif;">Pilih dua warna
                            untuk membuat gradient custom.</small>
                    </div>
                    <small
                        style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif;margin-top:4px;display:block">Pilih
                        warna untuk background card factory.</small>
                </div>

                <div class="gm-field">
                    <label>Warna Header (Solid)</label>
                    <input type="color" id="gmWarnaHeader" value="#1f3c88" style="width: 44px; height: 34px; border-radius: 8px; border: 1.5px solid #ddd; padding: 2px; cursor: pointer; background: #fff;">
                    <small style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif;margin-top:4px;display:block">Warna utama (hex) untuk elemen UI Modal & Badge.</small>
                </div>

                <button class="gm-save-btn" id="gmSaveBtn" onclick="saveFactory()">💾 Simpan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let _gmEditId = null;

        // ── Color chip presets ───────────────────────────────────────────────────
        const GM_PRESETS = [
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

        function gmBuildChips() {
            const grid = document.getElementById('gmChipGrid');
            if (!grid) return;
            let html = GM_PRESETS.map((p, i) =>
                `<div class="gm-chip" data-idx="${i}" style="background:${p.value}" title="${p.label}" onclick="gmSelectChip(${i})">
                    <span class="gm-chip-check">✓</span>
                 </div>`
            ).join('');
            html += `<div class="gm-chip gm-chip-custom" data-idx="custom" onclick="gmSelectChip('custom')">
                        <span style="font-size:18px">🎨</span>
                        <span style="font-size:9px;font-family:'Roboto Condensed',sans-serif;font-weight:800">Custom</span>
                        <span class="gm-chip-check" style="display:none">✓</span>
                     </div>`;
            grid.innerHTML = html;
        }

        function gmSelectChip(idx) {
            // Deactivate all
            document.querySelectorAll('#gmChipGrid .gm-chip').forEach(c => c.classList.remove('active'));
            const wrap = document.getElementById('gmCustomPickerWrap');

            if (idx === 'custom') {
                document.querySelector('#gmChipGrid .gm-chip-custom')?.classList.add('active');
                wrap?.classList.add('show');
                gmBuildCustomGradient();
            } else {
                wrap?.classList.remove('show');
                document.querySelector(`#gmChipGrid .gm-chip[data-idx="${idx}"]`)?.classList.add('active');
                const preset = GM_PRESETS[idx];
                if (preset) document.getElementById('gmGradient').value = preset.value;
            }
        }

        function gmBuildCustomGradient() {
            const c1 = document.getElementById('gmColor1')?.value || '#1f3c88';
            const c2 = document.getElementById('gmColor2')?.value || '#2e57d4';
            const grad = `linear-gradient(135deg,${c1},${c2})`;
            document.getElementById('gmGradient').value = grad;
            const preview = document.getElementById('gmGradPreview');
            if (preview) preview.style.background = grad;
        }

        function gmSyncChipSelection(gradient) {
            // Auto-select chip matching the current gradient value
            const idx = GM_PRESETS.findIndex(p => p.value === gradient);
            if (idx >= 0) {
                gmSelectChip(idx);
            } else if (gradient) {
                // Try to parse custom colors from gradient string
                const match = gradient.match(/#[0-9a-fA-F]{3,6}/g);
                if (match && match.length >= 2) {
                    const c1el = document.getElementById('gmColor1');
                    const c2el = document.getElementById('gmColor2');
                    if (c1el) c1el.value = match[0];
                    if (c2el) c2el.value = match[1];
                }
                gmSelectChip('custom');
            }
            // else: nothing selected (default)
        }

        // Load section counts
        document.addEventListener('DOMContentLoaded', async () => {
            gmBuildChips();
            try {
                const res = await fetch('/api/sections');
                const data = await res.json();
                if (!data.ok) return;
                const counts = {};
                data.sections.forEach(s => {
                    counts[s.factory_id] = (counts[s.factory_id] || 0) + 1;
                });
                Object.entries(counts).forEach(([fid, cnt]) => {
                    const el = document.getElementById(`gm-sec-count-${fid}`);
                    if (el) el.textContent = cnt;
                });
                // Set 0 for factories with no sections
                document.querySelectorAll('[id^="gm-sec-count-"]').forEach(el => {
                    if (el.textContent === ' -') el.textContent = '0';
                });
            } catch (e) { /* silent */ }
        });

        function openGroupModal(id = null, name = '', shortLabel = '', gradient = '', warnaHeader = '') {
            _gmEditId = id;
            const isEdit = !!id;
            document.getElementById('groupModalTitle').textContent = isEdit ? '✏️ Edit Factory' : '➕ Tambah Factory';
            document.getElementById('gmFactoryId').value = id ?? '';
            document.getElementById('gmName').value = name;
            document.getElementById('gmShortLabel').value = shortLabel;
            document.getElementById('gmGradient').value = gradient;
            document.getElementById('gmWarnaHeader').value = warnaHeader || '#1f3c88';
            // Reset chip selection, then auto-select matching chip
            document.querySelectorAll('#gmChipGrid .gm-chip').forEach(c => c.classList.remove('active'));
            document.getElementById('gmCustomPickerWrap')?.classList.remove('show');
            if (gradient) gmSyncChipSelection(gradient);
            openSheet('groupModal');
            setTimeout(() => document.getElementById('gmName').focus(), 200);
        }

        async function saveFactory() {
            const id = _gmEditId;
            const name = document.getElementById('gmName').value.trim();
            const short = document.getElementById('gmShortLabel').value.trim();
            const grad = document.getElementById('gmGradient').value.trim();
            const warnaHeader = document.getElementById('gmWarnaHeader').value.trim();

            if (!name) { showToast('Nama factory tidak boleh kosong.', 'error'); return; }

            const btn = document.getElementById('gmSaveBtn');
            btn.disabled = true; btn.textContent = '⏳ Menyimpan…';

            try {
                const url = id ? `/admin/api/factories/${id}` : '/admin/api/factories';
                const method = id ? 'PUT' : 'POST';
                const body = { name };
                if (short) body.short_label = short;
                if (grad) body.gradient = grad;
                if (warnaHeader) body.warna_header = warnaHeader;

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

                showToast('✅ Factory berhasil disimpan!', 'success');
                closeSheet('groupModal');
                setTimeout(() => window.location.reload(), 600);
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            } finally {
                btn.disabled = false; btn.textContent = '💾 Simpan';
            }
        }

        async function deleteFactory(id, name) {
            if (!confirm(`Hapus factory "${name}"?\n\nSemua section di dalamnya juga akan dihapus.`)) return;

            try {
                const res = await fetch(`/api/factories/${id}?force=1`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                });
                const data = await res.json();

                if (data.confirm) {
                    if (!confirm(data.message)) return;
                    // Retry with force
                    const res2 = await fetch(`/api/factories/${id}?force=1`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    });
                    const data2 = await res2.json();
                    if (!data2.ok) { showToast(data2.message ?? 'Gagal menghapus.', 'error'); return; }
                } else if (!data.ok) {
                    showToast(data.message ?? 'Gagal menghapus.', 'error'); return;
                }

                showToast('🗑️ Factory dihapus.', 'success');
                const card = document.getElementById(`gm-card-${id}`);
                if (card) { card.style.transition = 'opacity .3s'; card.style.opacity = '0'; setTimeout(() => { card.remove(); }, 300); }
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            }
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet('groupModal'); });
    </script>
@endpush