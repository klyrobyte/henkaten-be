@extends('layouts.admin')
@section('title', 'Mesin Management')

@push('styles')
    <style>
        /*   Page Header   */
        .mm-page-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 18px;
            font-weight: 900;
            color: var(--navy, #1f3c88);
            letter-spacing: .6px;
            margin: 0 0 4px;
        }

        .mm-page-sub {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            color: #888;
            font-weight: 600;
        }

        /*   Stats Bar   */
        .mm-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .mm-stat-card {
            flex: 1 1 100px;
            min-width: 90px;
            background: #fff;
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .mm-stat-val {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 24px;
            font-weight: 900;
            line-height: 1;
        }

        .mm-stat-lbl {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #888;
        }

        /*   Toolbar   */
        .mm-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .mm-search {
            flex: 1;
            min-width: 160px;
            padding: 9px 14px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            transition: border-color .15s;
        }

        .mm-search:focus {
            outline: none;
            border-color: #1f3c88;
        }

        .mm-add-btn {
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

        .mm-add-btn:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        .mm-add-btn:active {
            opacity: .8;
            transform: none;
        }

        /*   Card Grid   */
        .mm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }

        @media (max-width: 480px) {
            .mm-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 10px;
            }
        }

        .mm-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            overflow: hidden;
            transition: transform .15s, box-shadow .15s;
            display: flex;
            flex-direction: column;
        }

        .mm-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .13);
        }

        /* Photo area */
        .mm-card-photo {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 36px;
            flex-shrink: 0;
        }

        .mm-card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mm-card-photo.no-photo {
            background: linear-gradient(135deg, #eef1fa, #f5f7ff);
        }

        .mm-card-body {
            padding: 12px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .mm-card-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 900;
            color: #222;
            line-height: 1.3;
            word-break: break-word;
        }

        /* Status badge */
        .mm-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #fff;
            align-self: flex-start;
        }

        .st-mesin {
            background: #1f3c88;
        }

        .st-persons {
            background: #2e7d32;
        }

        .st-robot {
            background: #6a1b9a;
        }

        .st-line {
            background: #e67e22;
        }

        .st-pos {
            background: #6a1b9a;
        }

        .st-lainya {
            background: #546e7a;
        }

        .st-mc_vibration {
            background: #c0392b;
        }

        /* Card actions */
        .mm-card-actions {
            display: flex;
            gap: 6px;
            padding: 0 14px 12px;
        }

        .mm-act-btn {
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

        .mm-act-edit {
            border-color: #1f3c88;
            color: #1f3c88;
            background: #eef1fa;
        }

        .mm-act-edit:hover {
            background: #dde4f5;
        }

        .mm-act-del {
            border-color: #e74c3c;
            color: #e74c3c;
            background: #fdeaea;
        }

        .mm-act-del:hover {
            background: #fbd0d0;
        }

        /* Empty state */
        .mm-empty {
            text-align: center;
            padding: 60px 20px;
            color: #aaa;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            display: none;
        }

        .mm-empty-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        /*   Modal Form   */
        .mm-modal-body {
            padding: 18px 18px 4px;
        }

        /* Photo uploader */
        .mm-photo-uploader {
            width: 100%;
            aspect-ratio: 16/9;
            border: 2px dashed #cedcf8;
            border-radius: 12px;
            background: #f5f8ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            position: relative;
            transition: border-color .15s, background .15s;
            margin-bottom: 14px;
        }

        .mm-photo-uploader:hover {
            border-color: #1f3c88;
            background: #eef1fa;
        }

        .mm-photo-uploader.has-photo {
            border-style: solid;
            border-color: #1f3c88;
        }

        .mm-photo-preview {
            position: absolute;
            inset: 0;
            object-fit: cover;
            width: 100%;
            height: 100%;
            display: none;
        }

        .mm-photo-uploader.has-photo .mm-photo-preview {
            display: block;
        }

        .mm-photo-uploader.has-photo .mm-photo-placeholder {
            display: none;
        }

        .mm-photo-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: #aaa;
            pointer-events: none;
        }

        .mm-photo-placeholder span {
            font-size: 32px;
        }

        .mm-photo-placeholder p {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #bbb;
            margin: 0;
        }

        .mm-photo-overlay-btn {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(31, 60, 136, .85);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 4px 10px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            display: none;
        }

        .mm-photo-uploader.has-photo .mm-photo-overlay-btn {
            display: block;
        }

        .mm-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 12px;
        }

        .mm-field label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #666;
        }

        .mm-field input,
        .mm-field select {
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

        .mm-field input:focus,
        .mm-field select:focus {
            outline: none;
            border-color: #1f3c88;
        }

        .mm-save-btn {
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

        .mm-save-btn:hover {
            opacity: .92;
        }

        .mm-save-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    <div style="padding: 6px 0 110px;">

        {{-- Page Header --}}
        <div style="margin-bottom:18px;">
            <div class="section-title" style="margin-bottom:4px">⚙️ Mesin Management</div>
            <div class="mm-page-sub">
                Kelola data mesin: tambah, edit, dan hapus mesin.
                &nbsp;|&nbsp;
                <span style="font-weight:800;color:#1f3c88">🏭 {{ $currentFactory }}</span>
                @if($userRole === 'admin')
                    <span style="font-size:10px;color:#aaa;margin-left:4px">(sesuai factory aktif)</span>
                @else
                    <span style="font-size:10px;color:#aaa;margin-left:4px">(factory penempatan Anda)</span>
                @endif
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="mm-stats" id="mmStatsBar">
            <div class="mm-stat-card">
                <div class="mm-stat-val" style="color:#1f3c88">{{ $stats['total'] }}</div>
                <div class="mm-stat-lbl">Total</div>
            </div>
            <div class="mm-stat-card">
                <div class="mm-stat-val" style="color:#1f3c88">{{ $stats['mesin'] }}</div>
                <div class="mm-stat-lbl">Mesin</div>
            </div>
            <div class="mm-stat-card">
                <div class="mm-stat-val" style="color:#2e7d32">{{ $stats['persons'] }}</div>
                <div class="mm-stat-lbl">Persons</div>
            </div>
            <div class="mm-stat-card">
                <div class="mm-stat-val" style="color:#e67e22">{{ $stats['line'] }}</div>
                <div class="mm-stat-lbl">Line</div>
            </div>
            <div class="mm-stat-card">
                <div class="mm-stat-val" style="color:#6a1b9a">{{ $stats['pos'] }}</div>
                <div class="mm-stat-lbl">Pos</div>
            </div>
            <div class="mm-stat-card">
                <div class="mm-stat-val" style="color:#546e7a">{{ $stats['lainya'] }}</div>
                <div class="mm-stat-lbl">Lainya</div>
            </div>
            <div class="mm-stat-card">
                <div class="mm-stat-val" style="color:#c0392b">{{ $stats['mc_vibration'] ?? 0 }}</div>
                <div class="mm-stat-lbl">MC Vibration</div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="mm-toolbar">
            <input class="mm-search" type="text" id="mmSearch" placeholder="🔍 Cari nama prosess..." oninput="filterCards()">
            <button class="mm-add-btn" id="mmAddBtn" onclick="openMesinModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Mesin
            </button>
        </div>

        {{-- Card Grid --}}
        <div class="mm-grid" id="mmGrid">
            @forelse($machines as $m)
                @php
                    $stClass = 'st-' . $m->status;
                    $stLabel = ucfirst($m->status);
                @endphp
                <div class="mm-card" id="mm-card-{{ $m->id }}" data-name="{{ strtolower($m->name) }}">
                    <div class="mm-card-photo {{ $m->photo ? '' : 'no-photo' }}">
                        @if($m->photo)
                            <img src="{{ $m->photo_url }}" alt="{{ $m->name }}" loading="lazy"
                                onerror="this.parentElement.classList.add('no-photo');this.style.display='none';this.parentElement.innerHTML='⚙️';">
                        @else
                            ⚙️
                        @endif
                    </div>
                    <div class="mm-card-body">
                        <div class="mm-card-name">{{ $m->name }}</div>
                        <div class="mm-card-name">{{ $m->factory }}</div>
                        <span class="mm-status-badge {{ $stClass }}">{{ $stLabel }}</span>
                        @if($m->section)
                            @php
                                $__sectionLabel = $sections->firstWhere('code', $m->section)?->name ?? $m->section;
                            @endphp
                            <span class="mm-status-badge" style="background:#546e7a;font-size:9px;margin-top:2px">
                                {{ $__sectionLabel }}
                            </span>
                        @endif
                    </div>
                    <div class="mm-card-actions">
                        <button class="mm-act-btn mm-act-edit"
                            onclick="openMesinModal({{ $m->id }}, '{{ addslashes($m->name) }}', '{{ $m->status }}', '{{ $m->photo_url ?? '' }}', '{{ $m->factory }}', '{{ $m->section ?? '' }}')">
                            ✏️ Edit
                        </button>
                        <button class="mm-act-btn mm-act-del" onclick="deleteMesin({{ $m->id }}, '{{ addslashes($m->name) }}')">
                            🗑️
                        </button>
                    </div>
                </div>
            @empty
            @endforelse
        </div>

        {{-- Empty state --}}
        <div class="mm-empty" id="mmEmpty">
            <div class="mm-empty-icon">⚙️</div>
            <div>Belum ada mesin. Klik <strong>Tambah Mesin</strong> untuk mulai.</div>
        </div>

    </div>

    {{--   Add/Edit Mesin Modal   --}}
    <div class="modal-overlay" id="mesinModal">
        <div class="modal-sheet" style="max-height:92vh;overflow-y:auto">
            <div class="modal-sheet-handle"></div>
            <div class="modal-sheet-header">
                <h3 id="mesinModalTitle">Tambah Mesin</h3>
                <button class="modal-sheet-close" onclick="closeSheet('mesinModal')">✕</button>
            </div>
            <div class="mm-modal-body">
                <input type="hidden" id="mmMesinId">

                {{-- Photo Uploader --}}
                <div class="mm-photo-uploader" id="mmPhotoBox" onclick="triggerPhotoInput()">
                    <img class="mm-photo-preview" id="mmPhotoPreview" src="" alt="Preview">
                    <div class="mm-photo-placeholder">
                        <span>📷</span>
                        <p>Klik untuk upload foto mesin</p>
                    </div>
                    <button class="mm-photo-overlay-btn" id="mmPhotoChangeBtn"
                        onclick="event.stopPropagation();triggerPhotoInput()">Ganti Foto</button>
                </div>
                <input type="file" id="mmPhotoInput" accept="image/*" style="display:none"
                    onchange="handlePhotoSelect(this)">
                <input type="hidden" id="mmPhotoBase64">

                {{-- Nama Mesin --}}
                <div class="mm-field">
                    <label>Nama Prosess *</label>
                    <input type="text" id="mmName" placeholder="Contoh: #01-2500T" autocomplete="off">
                </div>

                {{-- Factory --}}
                @if($userRole === 'admin')
                    <div class="mm-field">
                        <label>Factory *</label>
                        {{-- Options di-inject otomatis oleh JS dari FACTORY_SECTIONS --}}
                        <select id="mmFactory" onchange="updateSectionVisibility()" {{ count($factories) === 1 ? 'disabled' : '' }}>
                            <option value=""> - Pilih Factory  -</option>
                        </select>
                    </div>
                @else
                    {{-- GL: factory dikunci, tidak perlu dipilih --}}
                    <input type="hidden" id="mmFactory" value="{{ $currentFactory }}">
                    <div class="mm-field">
                        <label>Factory</label>
                        <input type="text" value="{{ $currentFactory }}" disabled
                            style="background:#f5f5f5;color:#888;cursor:not-allowed;">
                    </div>
                @endif

                {{-- Status --}}
                <div class="mm-field">
                    <label>Status *</label>
                    <select id="mmStatus" onchange="updateSectionVisibility()">
                        <option value=""> - Pilih Status  -</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->key }}">{{ $st->icon }} {{ $st->label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Section (target group di dashboard) --}}
                <div class="mm-field" id="mmSectionWrap">
                    <label>Section Dashboard *</label>
                    <select id="mmSection">
                        <option value=""> - Pilih dulu Factory & Status  -</option>
                    </select>
                    <small style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif">Pilih di mana mesin
                        ini akan tampil di dashboard.</small>
                </div>

                <button class="mm-save-btn" id="mmSaveBtn" onclick="saveMesin()">💾 Simpan</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const MM_FACTORY = {!! json_encode($currentFactory) !!};
        let _mmEditId = null;

        //   Init: check empty               ─
        (function checkEmpty() {
            const cards = document.querySelectorAll('.mm-card');
            document.getElementById('mmEmpty').style.display = cards.length === 0 ? 'block' : 'none';
        })();

        //   Search/filter                 ─
        function filterCards() {
            const q = document.getElementById('mmSearch').value.toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.mm-card').forEach(card => {
                const match = !q || card.dataset.name.includes(q);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('mmEmpty').style.display = visible === 0 ? 'block' : 'none';
        }

        //   FACTORY_SECTIONS  - built from DB (injected by PHP)       
        // { factoryName: [ {value, label, type}, ... ] }
        @php
            $__fsMap = [];
            foreach($sections as $__sec) {
                $__fname = $__sec->factory?->name ?? '';
                if (!$__fname) continue;
                $__fsMap[$__fname][] = [
                    'value' => $__sec->code,
                    'label' => $__sec->name,
                    'type'  => $__sec->type,
                ];
            }
        @endphp
        const FACTORY_SECTIONS = {!! json_encode($__fsMap, JSON_UNESCAPED_UNICODE) !!};
        const ALL_FACTORIES = {!! json_encode($factories->pluck('name')->toArray(), JSON_UNESCAPED_UNICODE) !!};

        function populateFactoryDropdown(selectedFactory = '') {
            const factoryEl = document.getElementById('mmFactory');
            if (!factoryEl || factoryEl.tagName !== 'SELECT') return;

            factoryEl.innerHTML = '<option value=""> - Pilih Factory  -</option>';

            ALL_FACTORIES.forEach(factory => {
                const opt = document.createElement('option');
                opt.value = factory;
                opt.textContent = factory;
                if (factory === selectedFactory || ALL_FACTORIES.length === 1) opt.selected = true;
                factoryEl.appendChild(opt);
            });
        }

        //   Open modal                  ─
        function openMesinModal(id = null, name = '', status = '', photoUrl = '', factory = '', section = '') {
            _mmEditId = id;
            const isEdit = !!id;

            document.getElementById('mesinModalTitle').textContent = isEdit ? '✏️ Edit Mesin' : '➕ Tambah Mesin';
            document.getElementById('mmMesinId').value = id ?? '';
            document.getElementById('mmName').value = name;
            document.getElementById('mmStatus').value = status;
            document.getElementById('mmPhotoBase64').value = '';

            // Populate & set factory dropdown (hanya admin punya SELECT)
            const factoryEl = document.getElementById('mmFactory');
            if (factoryEl && factoryEl.tagName === 'SELECT') {
                populateFactoryDropdown(factory || MM_FACTORY);
            }

            // Isi section dropdown sesuai factory + status yang sudah di-set
            updateSectionVisibility(section);

            const box = document.getElementById('mmPhotoBox');
            const preview = document.getElementById('mmPhotoPreview');
            if (photoUrl) {
                preview.src = photoUrl;
                box.classList.add('has-photo');
            } else {
                preview.src = '';
                box.classList.remove('has-photo');
            }

            openSheet('mesinModal');
            setTimeout(() => document.getElementById('mmName').focus(), 200);
        }

        //   Photo handling                 
        function triggerPhotoInput() {
            document.getElementById('mmPhotoInput').click();
        }

        function handlePhotoSelect(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                showToast('Foto terlalu besar (max 5MB).', 'error');
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                const dataUrl = e.target.result;
                document.getElementById('mmPhotoPreview').src = dataUrl;
                document.getElementById('mmPhotoBox').classList.add('has-photo');
                document.getElementById('mmPhotoBase64').value = dataUrl;
            };
            reader.readAsDataURL(file);
            input.value = ''; // reset agar file yang sama bisa dipilih ulang
        }

        //   Populate section dropdown           ─
        // Membaca factory + status yang sedang aktif di modal,
        // lalu mengisi #mmSection dari FACTORY_SECTIONS.
        // currentSection = value (misal 'f3', 'f4', 'f2-resin', dst)
        function updateSectionVisibility(currentSection = '') {
            const factoryEl = document.getElementById('mmFactory');
            const factory = factoryEl
                ? (factoryEl.value || MM_FACTORY)
                : MM_FACTORY;
            const status = document.getElementById('mmStatus').value;
            const sectionEl = document.getElementById('mmSection');
            
            let allSections = FACTORY_SECTIONS[factory] || [];
            
            // Smart filter: only show sections that match the selected status type
            let sectionDefs = allSections.filter(s => s.type === status);

            // Reset
            sectionEl.innerHTML = '<option value=""> - Pilih Section Dashboard  -</option>';

            if (!factory || !status) return; // belum pilih factory/status

            if (sectionDefs.length === 0) {
                 sectionEl.innerHTML = '<option value=""> - Tidak ada Tipe Section yg sesuai  -</option>';
            } else {
                sectionDefs.forEach(def => {
                    const opt = document.createElement('option');
                    opt.value = def.value;
                    // Bug Fix: Display code instead of name as requested
                    opt.textContent = def.value;
                    if (def.value === currentSection) opt.selected = true;
                    sectionEl.appendChild(opt);
                });

                // Auto-select jika hanya ada 1 pilihan dan belum ada nilai sebelumnya
                if (sectionDefs.length === 1 && !currentSection) sectionEl.value = sectionDefs[0].value;
            }

            // Pastikan nilai yang sudah diset tidak hilang jika belum ada di options (fallback)
            if (currentSection) {
                const found = sectionDefs.find(s => s.value === currentSection);
                if (!found && !Array.from(sectionEl.options).find(o => o.value === currentSection)) {
                    // It exists in old data but doesn't match the smart filter. Add it anyway visibly.
                    const fallbackDef = allSections.find(s => s.value === currentSection);
                    const opt = document.createElement('option');
                    opt.value = currentSection;
                    opt.textContent = fallbackDef ? (fallbackDef.value + " (Beda Status)") : (currentSection);
                    opt.selected = true;
                    sectionEl.appendChild(opt);
                }
            }
        }

        // Trigger update section saat factory berubah (admin)
        const _factEl = document.getElementById('mmFactory');
        if (_factEl && _factEl.tagName === 'SELECT') {
            _factEl.addEventListener('change', () => updateSectionVisibility());
        }

        //   Save (Add/Edit)                
        async function saveMesin() {
            const id = _mmEditId;
            const name = document.getElementById('mmName').value.trim();
            const status = document.getElementById('mmStatus').value;
            const factory = document.getElementById('mmFactory').value;
            const section = document.getElementById('mmSection').value;
            const photoBase64 = document.getElementById('mmPhotoBase64').value;

            if (!name) { showToast('Nama prosess tidak boleh kosong.', 'error'); return; }
            if (!status) { showToast('Pilih status mesin.', 'error'); return; }
            if (factory && status && !section) {
                showToast('Pilih section untuk mesin ini.', 'error'); return;
            }

            const btn = document.getElementById('mmSaveBtn');
            btn.disabled = true; btn.textContent = '⏳ Menyimpan…';

            try {
                const url = id ? `/admin/mesinmg/${id}` : '/admin/mesinmg';
                const method = id ? 'PUT' : 'POST';
                const body = { name, status, factory };
                if (section) body.section = section;
                if (photoBase64) body.photo_base64 = photoBase64;

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();

                if (!res.ok) {
                    const msg = data.errors
                        ? Object.values(data.errors)[0][0]
                        : (data.message ?? 'Gagal menyimpan.');
                    showToast(msg, 'error');
                    return;
                }

                showToast('✅ Berhasil disimpan!', 'success');
                closeSheet('mesinModal');
                setTimeout(() => window.location.reload(), 600);

            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            } finally {
                btn.disabled = false; btn.textContent = '💾 Simpan';
            }
        }

        //   Delete                    ─
        async function deleteMesin(id, name) {
            if (!confirm(`Hapus mesin "${name}"?\n\nAksi ini tidak bisa dibatalkan.`)) return;

            try {
                const res = await fetch(`/admin/mesinmg/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });
                const data = await res.json();

                if (!res.ok) { showToast(data.message ?? 'Gagal menghapus.', 'error'); return; }

                showToast('🗑️ Mesin dihapus.', 'success');
                const card = document.getElementById(`mm-card-${id}`);
                if (card) {
                    card.style.transition = 'opacity .3s';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        filterCards(); // re-check empty state
                    }, 300);
                }
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            }
        }

        //   Keyboard shortcut               
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeSheet('mesinModal');
        });
    </script>
@endpush