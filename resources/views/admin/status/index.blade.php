@extends('layouts.admin')
@section('title', 'Status Management')

@push('styles')
<style>
    .stm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
        margin-top: 18px;
    }
    .stm-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
        overflow: hidden;
        transition: transform .15s, box-shadow .15s;
        display: flex;
        flex-direction: column;
    }
    .stm-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.13); }
    .stm-card-header {
        padding: 14px 16px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .stm-card-icon {
        font-size: 24px; line-height: 1;
    }
    .stm-card-info { flex: 1; }
    .stm-card-label {
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 15px; font-weight: 900; color: #222;
    }
    .stm-card-key {
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 10px; font-weight: 700; color: #888;
        margin-top: 2px;
    }
    .stm-color-dot {
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 2px solid rgba(0,0,0,.1);
        flex-shrink: 0;
    }
    .stm-card-body {
        padding: 8px 16px 10px;
        flex: 1;
    }
    .stm-usage {
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 11px; color: #888; font-weight: 600;
    }
    .stm-card-actions {
        display: flex; gap: 6px;
        padding: 0 16px 12px;
    }
    .stm-act-btn {
        flex: 1; padding: 7px 0;
        border-radius: 8px;
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 11px; font-weight: 800;
        cursor: pointer; border: 1.5px solid;
        transition: all .12s; text-align: center;
    }
    .stm-act-edit { border-color: #1f3c88; color: #1f3c88; background: #eef1fa; }
    .stm-act-edit:hover { background: #dde4f5; }
    .stm-act-del  { border-color: #e74c3c; color: #e74c3c; background: #fdeaea; }
    .stm-act-del:hover  { background: #fbd0d0; }
    .stm-add-btn {
        display: flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px; border: none;
        background: linear-gradient(135deg, #1f3c88, #2e57d4); color: #fff;
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(31,60,136,.35);
        transition: opacity .15s, transform .1s; white-space: nowrap;
    }
    .stm-add-btn:hover { opacity: .9; transform: translateY(-1px); }
    .stm-field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
    .stm-field label { font-family: 'Roboto Condensed', sans-serif; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; color: #666; }
    .stm-field input {
        padding: 10px 12px; border: 1.5px solid #e0e0e0; border-radius: 10px;
        font-size: 13px; font-family: inherit; transition: border-color .15s;
        box-sizing: border-box; width: 100%; background: #fff;
    }
    .stm-field input:focus { outline: none; border-color: #1f3c88; }
    .stm-color-row { display: flex; gap: 8px; align-items: center; }
    .stm-color-row input[type=text] { flex: 1; }
    .stm-color-row input[type=color] {
        width: 42px; height: 42px; padding: 2px; border: 1.5px solid #e0e0e0;
        border-radius: 10px; cursor: pointer; background: #fff;
    }
    .stm-save-btn {
        width: 100%; padding: 13px; border-radius: 12px; border: none;
        background: linear-gradient(135deg, #1f3c88, #2e57d4); color: #fff;
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .5px;
        cursor: pointer; margin-top: 6px; margin-bottom: 18px;
        box-shadow: 0 4px 14px rgba(31,60,136,.35); transition: opacity .15s;
    }
    .stm-save-btn:hover { opacity: .92; }
    .stm-save-btn:disabled { opacity: .55; cursor: not-allowed; }
    .stm-empty { text-align: center; padding: 60px 20px; color: #aaa; font-family: 'Roboto Condensed', sans-serif; font-size: 14px; display: none; }
    .stm-empty-icon { font-size: 40px; margin-bottom: 10px; }
</style>
@endpush

@section('content')
<div style="padding: 6px 0 110px;">

    {{-- Page Header --}}
    <div style="margin-bottom:18px;">
        <div class="section-title" style="margin-bottom:4px;">🏷️ Status Management</div>
        <div class="mm-page-sub">Kelola Status mesin: tambah, edit, dan hapus status.</div>
    </div>

    {{-- Toolbar --}}
    <div class="mm-toolbar">
        <div style="font-family:'Roboto Condensed',sans-serif;font-size:13px;color:#666;">
            <strong style="color:#1f3c88;">{{ $statuses->count() }}</strong> status terdaftar
        </div>
        <button class="stm-add-btn" onclick="openStatusModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Status
        </button>
    </div>

    {{-- Card Grid --}}
    <div class="stm-grid" id="stmGrid">
        @forelse($statuses as $st)
            <div class="stm-card" id="stm-card-{{ $st->id }}">
                <div class="stm-card-header">
                    <div class="stm-card-icon">{{ $st->icon }}</div>
                    <div class="stm-card-info">
                        <div class="stm-card-label">{{ $st->label }}</div>
                        <div class="stm-card-key">key: {{ $st->key }}</div>
                    </div>
                    <div class="stm-color-dot" style="background: {{ $st->color }};"></div>
                </div>
                <div class="stm-card-body">
                    <div class="stm-usage">🎨 {{ $st->color }}</div>
                </div>
                <div class="stm-card-actions">
                    <button class="stm-act-btn stm-act-edit"
                        onclick="openStatusModal({{ $st->id }}, '{{ addslashes($st->key) }}', '{{ addslashes($st->label) }}', '{{ addslashes($st->icon) }}', '{{ addslashes($st->color) }}')">
                        ✏️ Edit
                    </button>
                    <button class="stm-act-btn stm-act-del" onclick="deleteStatus({{ $st->id }}, '{{ addslashes($st->label) }}')">
                        🗑️
                    </button>
                </div>
            </div>
        @empty
            <div class="stm-empty" style="display:block;grid-column:1/-1;">
                <div class="stm-empty-icon">🏷️</div>
                <div>Belum ada status. Klik <strong>Tambah Status</strong> untuk mulai.</div>
            </div>
        @endforelse
    </div>

</div>

{{-- Add/Edit Modal --}}
<div class="modal-overlay" id="statusModal">
    <div class="modal-sheet" style="max-height:92vh;overflow-y:auto">
        <div class="modal-sheet-handle"></div>
        <div class="modal-sheet-header">
            <h3 id="statusModalTitle">Tambah Status</h3>
            <button class="modal-sheet-close" onclick="closeSheet('statusModal')">✕</button>
        </div>
        <div class="mm-modal-body">
            <input type="hidden" id="stmStatusId">

            <div class="stm-field">
                <label>Key (unik) *</label>
                <input type="text" id="stmKey" placeholder="Contoh: mesin" autocomplete="off">
                <small style="color:#888;font-size:10px;font-family:'Roboto Condensed',sans-serif;">Lowercase, tanpa spasi. Harus sama dengan nilai kolom <strong>status</strong> di tabel mesin.</small>
            </div>

            <div class="stm-field">
                <label>Label *</label>
                <input type="text" id="stmLabel" placeholder="Contoh: Mesin" autocomplete="off">
            </div>

            <div class="stm-field">
                <label>Icon (emoji)</label>
                <input type="text" id="stmIcon" placeholder="Contoh: ⚙️" autocomplete="off" maxlength="5">
            </div>

            <div class="stm-field">
                <label>Warna</label>
                <div class="stm-color-row">
                    <input type="text" id="stmColor" placeholder="#1f3c88" autocomplete="off" oninput="syncColorPicker(this.value)">
                    <input type="color" id="stmColorPicker" value="#1f3c88" oninput="syncColorText(this.value)">
                </div>
            </div>

            <button class="stm-save-btn" id="stmSaveBtn" onclick="saveStatus()">💾 Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    let _stmEditId = null;

    function syncColorPicker(val) {
        if (/^#[0-9a-f]{3,6}$/i.test(val)) {
            document.getElementById('stmColorPicker').value = val;
        }
    }
    function syncColorText(val) {
        document.getElementById('stmColor').value = val;
    }

    function openStatusModal(id = null, key = '', label = '', icon = '', color = '') {
        _stmEditId = id;
        const isEdit = !!id;
        document.getElementById('statusModalTitle').textContent = isEdit ? '✏️ Edit Status' : '➕ Tambah Status';
        document.getElementById('stmStatusId').value  = id ?? '';
        document.getElementById('stmKey').value       = key;
        document.getElementById('stmLabel').value     = label;
        document.getElementById('stmIcon').value      = icon;
        document.getElementById('stmColor').value     = color || '#1f3c88';
        document.getElementById('stmColorPicker').value = /^#[0-9a-f]{3,6}$/i.test(color) ? color : '#1f3c88';
        // Lock key field when editing (key is the DB identifier)
        document.getElementById('stmKey').disabled = isEdit;
        openSheet('statusModal');
        setTimeout(() => document.getElementById(isEdit ? 'stmLabel' : 'stmKey').focus(), 200);
    }

    async function saveStatus() {
        const id    = _stmEditId;
        const key   = document.getElementById('stmKey').value.trim().toLowerCase();
        const label = document.getElementById('stmLabel').value.trim();
        const icon  = document.getElementById('stmIcon').value.trim();
        const color = document.getElementById('stmColor').value.trim();

        if (!id && !key)  { showToast('Key tidak boleh kosong.', 'error'); return; }
        if (!label) { showToast('Label tidak boleh kosong.', 'error'); return; }

        const btn = document.getElementById('stmSaveBtn');
        btn.disabled = true; btn.textContent = '⏳ Menyimpan…';

        try {
            const url    = id ? `/admin/api/statuses/${id}` : '/admin/api/statuses';
            const method = id ? 'PUT' : 'POST';
            const body   = { label, icon, color };
            if (!id) body.key = key;
            else     body.key = key || document.getElementById('stmKey').dataset.key;

            const res  = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await res.json();

            if (!res.ok) {
                const msg = data.errors ? Object.values(data.errors)[0][0] : (data.message ?? 'Gagal menyimpan.');
                showToast(msg, 'error'); return;
            }

            showToast('✅ Status berhasil disimpan!', 'success');
            closeSheet('statusModal');
            setTimeout(() => window.location.reload(), 600);
        } catch(e) {
            showToast('Gagal: ' + e.message, 'error');
        } finally {
            btn.disabled = false; btn.textContent = '💾 Simpan';
        }
    }

    async function deleteStatus(id, label) {
        if (!confirm(`Hapus status "${label}"?`)) return;

        try {
            const res  = await fetch(`/api/statuses/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            });
            const data = await res.json();

            if (data.confirm) {
                if (!confirm(data.message)) return;
                const res2  = await fetch(`/api/statuses/${id}?force=1`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                });
                const data2 = await res2.json();
                if (!data2.ok) { showToast(data2.message ?? 'Gagal menghapus.', 'error'); return; }
            } else if (!data.ok) {
                showToast(data.message ?? 'Gagal menghapus.', 'error'); return;
            }

            showToast('🗑️ Status dihapus.', 'success');
            const card = document.getElementById(`stm-card-${id}`);
            if (card) { card.style.transition = 'opacity .3s'; card.style.opacity = '0'; setTimeout(() => card.remove(), 300); }
        } catch(e) {
            showToast('Gagal: ' + e.message, 'error');
        }
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet('statusModal'); });
</script>
@endpush
