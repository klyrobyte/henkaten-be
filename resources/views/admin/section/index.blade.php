@extends('layouts.admin')
@section('title', 'Section Management')

@push('styles')
    <style>
        .sm-factory-select-wrap {
            background: #fff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sm-factory-select-wrap label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #666;
            white-space: nowrap;
        }

        .sm-factory-select-wrap select {
            flex: 1;
            min-width: 160px;
            padding: 9px 12px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            transition: border-color .15s;
            background: #fff;
        }

        .sm-factory-select-wrap select:focus {
            outline: none;
            border-color: #1f3c88;
        }

        .sm-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 4px;
        }

        .sm-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            transition: box-shadow .15s, transform .15s;
            cursor: grab;
        }

        .sm-item:active {
            cursor: grabbing;
        }

        .sm-item.dragging {
            opacity: .5;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
            transform: scale(1.02);
        }

        .sm-item.drag-over {
            border: 2px dashed #1f3c88;
        }

        .sm-drag-handle {
            color: #ccc;
            cursor: grab;
            font-size: 18px;
            flex-shrink: 0;
            user-select: none;
            padding: 0 4px;
        }

        .sm-item-order {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #eef1fa;
            color: #1f3c88;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .sm-item-info {
            flex: 1;
            min-width: 0;
        }

        .sm-item-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 900;
            color: #222;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sm-item-code {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 700;
            color: #888;
            margin-top: 2px;
        }

        .sm-item-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 10px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #fff;
            background: #2e7d32;
            margin-top: 3px;
        }

        .sm-item-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .sm-act-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            border: 1.5px solid;
            transition: all .12s;
        }

        .sm-act-edit {
            border-color: #1f3c88;
            color: #1f3c88;
            background: #eef1fa;
        }

        .sm-act-edit:hover {
            background: #dde4f5;
        }

        .sm-act-del {
            border-color: #e74c3c;
            color: #e74c3c;
            background: #fdeaea;
        }

        .sm-act-del:hover {
            background: #fbd0d0;
        }

        .sm-add-btn {
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

        .sm-add-btn:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        .sm-add-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
        }

        .sm-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 12px;
        }

        .sm-field label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #666;
        }

        .sm-field input,
        .sm-field select {
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

        .sm-field input:focus,
        .sm-field select:focus {
            outline: none;
            border-color: #1f3c88;
        }

        .sm-save-btn {
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

        .sm-save-btn:hover {
            opacity: .92;
        }

        .sm-save-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .sm-empty {
            text-align: center;
            padding: 40px 20px;
            color: #aaa;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
        }

        .sm-checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: border-color .15s;
        }

        .sm-checkbox-wrap:hover {
            border-color: #1f3c88;
        }

        .sm-checkbox-wrap input[type=checkbox] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .sm-reorder-hint {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            color: #aaa;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
@endpush

@section('content')
    <div style="padding: 6px 0 110px;">

        {{-- Page Header --}}
        <div style="margin-bottom:18px;">
            <div class="section-title" style="margin-bottom:4px;">📋 Section Management</div>
            <div class="mm-page-sub">Kelola Section (#Section): tambah, edit, hapus, dan urutkan secara drag-and-drop.</div>
        </div>

        {{-- Factory Selector --}}
        <div class="sm-factory-select-wrap">
            <label>Pilih Factory:</label>
            <select id="smFactorySelect" onchange="loadSections()" {{ count($factories) === 1 ? 'disabled' : '' }}>
                @if(count($factories) !== 1)
                    <option value=""> - Pilih Factory -</option>
                @endif
                @foreach($factories as $f)
                    <option value="{{ $f->id }}" data-name="{{ $f->name }}" {{ count($factories) === 1 ? 'selected' : '' }}>
                        {{ $f->name }}</option>
                @endforeach
            </select>
            <div class="sm-reorder-hint">☰ Drag untuk ubah urutan</div>
        </div>

        {{-- Toolbar --}}
        <div class="mm-toolbar" id="smToolbar" style="display:none;">
            <div style="font-family:'Roboto Condensed',sans-serif;font-size:13px;color:#666;">
                <strong style="color:#1f3c88;" id="smCount">0</strong> section
            </div>
            <button class="sm-add-btn" id="smAddBtn" onclick="openSectionModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Section
            </button>
        </div>

        {{-- Section List --}}
        <div id="smList" class="sm-list"></div>
        <div id="smEmpty" class="sm-empty" style="display:none;">
            <div style="font-size:36px;margin-bottom:8px;">📋</div>
            <div>Pilih factory di atas, atau tambah section baru.</div>
        </div>

    </div>

    {{-- Add/Edit Section Modal --}}
    <div class="modal-overlay" id="sectionModal">
        <div class="modal-sheet" style="max-height:92vh;overflow-y:auto">
            <div class="modal-sheet-handle"></div>
            <div class="modal-sheet-header">
                <h3 id="sectionModalTitle">Tambah Section</h3>
                <button class="modal-sheet-close" onclick="closeSheet('sectionModal')">✕</button>
            </div>
            <div class="mm-modal-body">
                <input type="hidden" id="smSectionId">

                <div class="sm-field">
                    <label>Nama Section *</label>
                    <input type="text" id="smName" placeholder="Contoh: Resin Injection" autocomplete="off">
                </div>

                <div class="sm-field">
                    <label>Code (unik per factory) *</label>
                    <input type="text" id="smCode" placeholder="Contoh: f2-resin" autocomplete="off">
                    <small style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif;">
                        Code ini harus sama dengan nilai yang disimpan di kolom <strong>section</strong> pada tabel mesin.
                    </small>
                </div>

                <div class="sm-field">
                    <label>Tipe Section</label>
                    <select id="smType">
                        <option value=""> - Pilih Tipe Section -</option>
                        <option value="persons">👥 Key Persons section</option>
                        <option value="mesin">⚙️ Mesin</option>
                        <option value="robot">🤖 Robot</option>
                        <option value="line">🔗 Line</option>
                        <option value="pos">📍 Pos</option>
                        <option value="lainya">📦 Lainya</option>
                        <option value="mc_vibration">🔴 MC Vibration</option>
                    </select>
                    <small style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif;">
                        Digunakan untuk memfilter Section di form mesin berdasarkan Status mesin.
                    </small>
                </div>

                <button class="sm-save-btn" id="smSaveBtn" onclick="saveSection()">💾 Simpan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let _smEditId = null;
        let _smSections = [];
        let _dragSrc = null;

        async function loadSections() {
            const factoryId = document.getElementById('smFactorySelect').value;
            const toolbar = document.getElementById('smToolbar');
            const list = document.getElementById('smList');
            const empty = document.getElementById('smEmpty');

            list.innerHTML = '';
            if (!factoryId) {
                toolbar.style.display = 'none';
                empty.style.display = 'none';
                return;
            }

            toolbar.style.display = 'flex';

            try {
                const res = await fetch(`/api/sections?factory_id=${factoryId}`);
                const data = await res.json();
                _smSections = data.sections || [];
                renderSections(_smSections);
            } catch (e) {
                showToast('Gagal memuat section.', 'error');
            }
        }

        function renderSections(sections) {
            const list = document.getElementById('smList');
            const empty = document.getElementById('smEmpty');
            const count = document.getElementById('smCount');

            count.textContent = sections.length;
            list.innerHTML = '';

            if (sections.length === 0) {
                empty.style.display = 'block';
                return;
            }
            empty.style.display = 'none';

            sections.forEach((s, idx) => {
                const item = document.createElement('div');
                item.className = 'sm-item';
                item.dataset.id = s.id;
                item.dataset.order = s.order_index;
                item.draggable = true;

                item.innerHTML = `
                    <div class="sm-drag-handle">⠿</div>
                    <div class="sm-item-order">${idx + 1}</div>
                    <div class="sm-item-info">
                        <div class="sm-item-name">${s.name}</div>
                        <div class="sm-item-code">code: ${s.code}</div>
                        ${s.type ? `<span class="sm-item-badge" style="background:#555;margin-top:4px;">Type: ${s.type}</span>` : ''}
                    </div>
                    <div class="sm-item-actions">
                        <button class="sm-act-btn sm-act-edit" onclick="openSectionModal(${s.id},'${escQ(s.name)}','${escQ(s.code)}','${s.type || ''}')">✏️ Edit</button>
                        <button class="sm-act-btn sm-act-del"  onclick="deleteSection(${s.id},'${escQ(s.name)}')">🗑️</button>
                    </div>
                `;

                // Drag events
                item.addEventListener('dragstart', e => {
                    _dragSrc = item;
                    e.dataTransfer.effectAllowed = 'move';
                    setTimeout(() => item.classList.add('dragging'), 0);
                });
                item.addEventListener('dragend', () => {
                    item.classList.remove('dragging');
                    document.querySelectorAll('.sm-item').forEach(i => i.classList.remove('drag-over'));
                });
                item.addEventListener('dragover', e => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    if (_dragSrc !== item) item.classList.add('drag-over');
                });
                item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
                item.addEventListener('drop', e => {
                    e.preventDefault();
                    item.classList.remove('drag-over');
                    if (_dragSrc === item) return;
                    // Reorder in DOM
                    const items = [...list.querySelectorAll('.sm-item')];
                    const srcIdx = items.indexOf(_dragSrc);
                    const dstIdx = items.indexOf(item);
                    if (srcIdx < dstIdx) list.insertBefore(_dragSrc, item.nextSibling);
                    else list.insertBefore(_dragSrc, item);
                    // Persist new order
                    persistOrder();
                    // Update visual numbers
                    [...list.querySelectorAll('.sm-item')].forEach((el, i) => {
                        el.querySelector('.sm-item-order').textContent = i + 1;
                    });
                });

                list.appendChild(item);
            });
        }

        async function persistOrder() {
            const items = [...document.querySelectorAll('.sm-item')];
            const order = items.map((el, idx) => ({ id: parseInt(el.dataset.id), order_index: idx + 1 }));

            try {
                await fetch('/api/sections/reorder', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ order }),
                });
            } catch (e) { /* silent */ }
        }

        function escQ(s) { return String(s).replace(/'/g, "\\'"); }

        function openSectionModal(id = null, name = '', code = '', type = '') {
            _smEditId = id;
            const isEdit = !!id;
            document.getElementById('sectionModalTitle').textContent = isEdit ? '✏️ Edit Section' : '➕ Tambah Section';
            document.getElementById('smSectionId').value = id ?? '';
            document.getElementById('smName').value = name;
            document.getElementById('smCode').value = code;
            document.getElementById('smType').value = type;
            openSheet('sectionModal');
            setTimeout(() => document.getElementById('smName').focus(), 200);
        }

        async function saveSection() {
            const id = _smEditId;
            const factoryId = document.getElementById('smFactorySelect').value;
            const name = document.getElementById('smName').value.trim();
            const code = document.getElementById('smCode').value.trim();
            const type = document.getElementById('smType').value;

            if (!factoryId) { showToast('Pilih factory terlebih dahulu.', 'error'); return; }
            if (!name) { showToast('Nama section tidak boleh kosong.', 'error'); return; }
            if (!code) { showToast('Code section tidak boleh kosong.', 'error'); return; }

            const btn = document.getElementById('smSaveBtn');
            btn.disabled = true; btn.textContent = '⏳ Menyimpan…';

            try {
                const url = id ? `/admin/api/sections/${id}` : '/admin/api/sections';
                const method = id ? 'PUT' : 'POST';
                const body = { name, code, type };
                if (!id) body.factory_id = parseInt(factoryId);

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

                showToast('✅ Section berhasil disimpan!', 'success');
                closeSheet('sectionModal');
                loadSections();
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            } finally {
                btn.disabled = false; btn.textContent = '💾 Simpan';
            }
        }

        async function deleteSection(id, name) {
            if (!confirm(`Hapus section "${name}"?`)) return;

            try {
                const res = await fetch(`/api/sections/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                });
                const data = await res.json();

                if (data.confirm) {
                    if (!confirm(data.message)) return;
                    const res2 = await fetch(`/api/sections/${id}?force=1`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    });
                    const data2 = await res2.json();
                    if (!data2.ok) { showToast(data2.message ?? 'Gagal menghapus.', 'error'); return; }
                } else if (!data.ok) {
                    showToast(data.message ?? 'Gagal menghapus.', 'error'); return;
                }

                showToast('🗑️ Section dihapus.', 'success');
                loadSections();
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            }
        }

        // ── Init: auto-load if factory selected ───────────
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('smFactorySelect');
            if (select && select.value) {
                loadSections();
            }
        });

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet('sectionModal'); });
    </script>
@endpush