@extends('layouts.admin')
@section('title', 'Manajemen Departemen Perbaikan')

@push('styles')
    <style>
        .rd-card-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 14px;
        }

        .rd-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            transition: all .2s ease;
        }

        .rd-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
        }

        .rd-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rd-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #eef1fa;
            color: #1f3c88;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .rd-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: #222;
        }

        .rd-actions {
            display: flex;
            gap: 8px;
        }

        .rd-btn {
            padding: 8px 14px;
            border-radius: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            border: 1.5px solid;
            transition: all .12s;
        }

        .rd-btn-edit {
            border-color: #1f3c88;
            color: #1f3c88;
            background: #eef1fa;
        }

        .rd-btn-edit:hover {
            background: #dde4f5;
        }

        .rd-btn-del {
            border-color: #e74c3c;
            color: #e74c3c;
            background: #fdeaea;
        }

        .rd-btn-del:hover {
            background: #fbd0d0;
        }

        .rd-add-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
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
            transition: all .2s ease;
            white-space: nowrap;
        }

        .rd-add-btn:hover {
            opacity: .95;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(31, 60, 136, .45);
        }

        .rd-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .rd-field label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #666;
        }

        .rd-field input {
            padding: 11px 14px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color .15s;
            box-sizing: border-box;
            width: 100%;
            background: #fff;
        }

        .rd-field input:focus {
            outline: none;
            border-color: #1f3c88;
            box-shadow: 0 0 0 3px rgba(31, 60, 136, .08);
        }

        .rd-save-btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #1f3c88, #2e57d4);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            margin-top: 6px;
            margin-bottom: 10px;
            box-shadow: 0 4px 14px rgba(31, 60, 136, .35);
            transition: all .2s;
        }

        .rd-save-btn:hover {
            opacity: .95;
            box-shadow: 0 5px 16px rgba(31, 60, 136, .45);
        }

        .rd-save-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .rd-empty {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            color: #aaa;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div style="padding: 6px 0 110px;">

        {{-- Page Header --}}
        <div style="margin-bottom: 22px;">
            <div class="section-title" style="margin-bottom: 4px;">🏢 Manajemen Departemen Perbaikan</div>
            <div class="mm-page-sub">Kelola pilihan "Departemen Perbaikan" yang akan muncul pada form Tambah Problem Log.</div>
        </div>

        {{-- Toolbar --}}
        <div class="mm-toolbar" style="margin-bottom: 16px; background:#fff; padding: 14px 20px; border-radius:12px; box-shadow: 0 2px 8px rgba(0, 0, 0, .07); display:flex; align-items:center; justify-content:space-between;">
            <div style="font-family:'Roboto Condensed',sans-serif; font-size:13px; color:#666;">
                Total: <strong style="color:#1f3c88;" id="rdCount">{{ $departments->count() }}</strong> departemen
            </div>
            <button class="rd-add-btn" onclick="openRdModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Departemen
            </button>
        </div>

        {{-- Card List --}}
        <div id="rdList" class="rd-card-list">
            @forelse($departments as $dept)
                <div class="rd-card" id="rd-item-{{ $dept->id }}" data-name="{{ $dept->name }}">
                    <div class="rd-info">
                        <div class="rd-icon-wrap">🏢</div>
                        <div class="rd-name">{{ $dept->name }}</div>
                    </div>
                    <div class="rd-actions">
                        <button class="rd-btn rd-btn-edit" onclick="openRdModal({{ $dept->id }}, '{{ addslashes($dept->name) }}')">✏️ Edit</button>
                        <button class="rd-btn rd-btn-del" onclick="deleteRd({{ $dept->id }}, '{{ addslashes($dept->name) }}')">🗑️</button>
                    </div>
                </div>
            @empty
                <div id="rdEmpty" class="rd-empty">
                    <div style="font-size:42px; margin-bottom:10px;">🏢</div>
                    <div>Belum ada data departemen perbaikan. Klik <strong>Tambah Departemen</strong> untuk menambahkan.</div>
                </div>
            @endforelse
        </div>

    </div>

    {{-- Bottom Sheet Modal overlay --}}
    <div class="modal-overlay" id="rdModal">
        <div class="modal-sheet" style="max-height:85vh; overflow-y:auto;">
            <div class="modal-sheet-handle"></div>
            <div class="modal-sheet-header" style="display:flex; justify-content:space-between; align-items:center; padding: 14px 16px 12px; border-bottom: 1.5px solid #f0f0f0;">
                <h3 id="rdModalTitle" style="font-family:'Roboto Condensed',sans-serif; font-size:15px; font-weight:900; color:#222; text-transform:uppercase; margin:0;">Tambah Departemen</h3>
                <button class="modal-sheet-close" onclick="closeSheet('rdModal')" style="background:none; border:none; font-size:18px; cursor:pointer; color:#aaa;">✕</button>
            </div>
            <div class="mm-modal-body" style="padding: 16px;">
                <input type="hidden" id="rdId">

                <div class="rd-field">
                    <label>Nama Departemen *</label>
                    <input type="text" id="rdName" placeholder="Contoh: Maintenance / Production" autocomplete="off">
                </div>

                <button class="rd-save-btn" id="rdSaveBtn" onclick="saveRd()">💾 Simpan Departemen</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let _rdEditId = null;

        function openRdModal(id = null, name = '') {
            _rdEditId = id;
            const isEdit = !!id;
            document.getElementById('rdModalTitle').textContent = isEdit ? '✏️ Edit Departemen' : '➕ Tambah Departemen';
            document.getElementById('rdId').value = id ?? '';
            document.getElementById('rdName').value = name;
            openSheet('rdModal');
            setTimeout(() => document.getElementById('rdName').focus(), 250);
        }

        async function saveRd() {
            const id = _rdEditId;
            const name = document.getElementById('rdName').value.trim();

            if (!name) {
                showToast('Nama departemen tidak boleh kosong.', 'error');
                return;
            }

            const btn = document.getElementById('rdSaveBtn');
            btn.disabled = true;
            btn.textContent = '⏳ Menyimpan…';

            try {
                const url = id ? `/admin/repair-departments/${id}` : '/admin/repair-departments';
                const method = id ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': CSRF, 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ name }),
                });
                const data = await res.json();

                if (!res.ok) {
                    const msg = data.errors ? Object.values(data.errors)[0][0] : (data.message ?? 'Gagal menyimpan.');
                    showToast(msg, 'error');
                    return;
                }

                showToast('✅ Departemen berhasil disimpan!', 'success');
                closeSheet('rdModal');
                
                // Real-time DOM manipulation
                setTimeout(() => window.location.reload(), 600);
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '💾 Simpan Departemen';
            }
        }

        async function deleteRd(id, name) {
            if (!confirm(`Apakah Anda yakin ingin menghapus departemen "${name}"?`)) return;

            try {
                const res = await fetch(`/admin/repair-departments/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF, 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' 
                    },
                });
                const data = await res.json();

                if (!res.ok) {
                    showToast(data.message ?? 'Gagal menghapus.', 'error');
                    return;
                }

                showToast('🗑️ Departemen berhasil dihapus.', 'success');
                const card = document.getElementById(`rd-item-${id}`);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        // Update count
                        const countEl = document.getElementById('rdCount');
                        if (countEl) {
                            const newCount = Math.max(0, parseInt(countEl.textContent) - 1);
                            countEl.textContent = newCount;
                            if (newCount === 0) {
                                window.location.reload();
                            }
                        }
                    }, 250);
                }
            } catch (e) {
                showToast('Gagal: ' + e.message, 'error');
            }
        }

        document.addEventListener('keydown', e => { 
            if (e.key === 'Escape') closeSheet('rdModal'); 
        });
    </script>
@endpush
