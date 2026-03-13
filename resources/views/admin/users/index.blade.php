@extends('layouts.admin')
@section('title', 'User Management')

@push('styles')
<style>
.um-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px; padding: 0 2px;
}
.um-header h2 {
    font-family: 'Orbitron', sans-serif; font-size: 14px;
    font-weight: 900; color: var(--navy); letter-spacing: .8px; margin: 0;
}
.um-add-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 9px 16px; border-radius: 10px; border: none;
    background: linear-gradient(135deg, var(--navy), #2c4a9e);
    color: #fff; font-family: 'Roboto Condensed', sans-serif;
    font-size: 12px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .5px; cursor: pointer;
    box-shadow: 0 3px 10px rgba(31,60,136,.3); transition: opacity .15s;
}
.um-add-btn:active { opacity: .8; }

/* ── User Table ── */
.um-table-wrap { overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
.um-table {
    width: 100%; border-collapse: collapse;
    background: #fff; border-radius: 12px; overflow: hidden;
}
.um-table thead tr { background: linear-gradient(135deg, var(--navy), #2c4a9e); }
.um-table thead th {
    padding: 11px 14px; text-align: left; font-family: 'Roboto Condensed', sans-serif;
    font-size: 10px; font-weight: 800; color: #fff;
    text-transform: uppercase; letter-spacing: .6px; white-space: nowrap;
}
.um-table tbody tr { border-bottom: 1px solid #f0f4f8; transition: background .12s; }
.um-table tbody tr:last-child { border-bottom: none; }
.um-table tbody tr:hover { background: #f8fafb; }
.um-table tbody td { padding: 11px 14px; font-size: 13px; color: #444; vertical-align: middle; }

.um-av {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 13px; font-weight: 900; color: #fff;
    flex-shrink: 0;
}
.um-name-col { display: flex; align-items: center; gap: 10px; }
.um-name-txt { font-weight: 700; font-size: 13px; color: #222; }
.um-username-txt { font-size: 11px; color: #888; font-family: 'Roboto Condensed', sans-serif; }

.role-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 20px;
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 10px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .4px; color: #fff;
}
.role-admin    { background: #e74c3c; }
.role-tl       { background: #1f3c88; }
.role-gl       { background: #2e7d32; }
.role-pengawas { background: #f39c12; }
        .role-tv       { background: #6a1b9a; }

.um-action-btn {
    padding: 5px 11px; border-radius: 7px;
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 10px; font-weight: 800; cursor: pointer;
    border: 1.5px solid; transition: all .12s;
}
.um-btn-edit { border-color: #1f3c88; color: #1f3c88; background: #eef1fa; }
.um-btn-edit:hover { background: #dde4f5; }
.um-btn-del  { border-color: #e74c3c; color: #e74c3c; background: #fdeaea; margin-left: 4px; }
.um-btn-del:hover  { background: #fbd0d0; }
.um-btn-self { border-color: #ccc; color: #aaa; background: #f5f5f5; cursor: default; margin-left: 4px; }

/* ── Modal Form ── */
.um-modal-body { padding: 16px; }
.um-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 480px) { .um-form-grid { grid-template-columns: 1fr; } }
.um-field { display: flex; flex-direction: column; gap: 5px; }
.um-field label {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .4px; color: #666;
}
.um-field input, .um-field select {
    padding: 10px 12px; border: 1.5px solid #e0e0e0;
    border-radius: 10px; font-size: 13px; font-family: inherit;
    transition: border-color .15s; box-sizing: border-box; width: 100%;
}
.um-field input:focus, .um-field select:focus {
    outline: none; border-color: var(--navy);
}
.um-field-full { grid-column: 1 / -1; }
.um-pw-hint {
    font-size: 10px; color: #aaa;
    font-family: 'Roboto Condensed', sans-serif; margin-top: 2px;
}

.um-save-btn {
    width: 100%; padding: 13px; border-radius: 12px; border: none;
    background: linear-gradient(135deg, var(--navy), #2c4a9e);
    color: #fff; font-family: 'Roboto Condensed', sans-serif;
    font-size: 14px; font-weight: 900; text-transform: uppercase;
    letter-spacing: .5px; cursor: pointer; margin-top: 16px;
    box-shadow: 0 4px 14px rgba(31,60,136,.3);
    transition: opacity .15s;
}
.um-save-btn:active { opacity: .85; }
.um-save-btn:disabled { opacity: .55; cursor: not-allowed; }

/* Role description info box */
.role-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
@media (max-width: 480px) { .role-info-grid { grid-template-columns: 1fr; } }
.role-info-card {
    border-radius: 10px; padding: 10px 12px;
    border: 1.5px solid; display: flex; align-items: flex-start; gap: 8px;
}
.ri-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-top: 2px; }
.ri-name { font-family: 'Roboto Condensed', sans-serif; font-size: 11px; font-weight: 800; text-transform: uppercase; }
.ri-desc { font-size: 10px; color: #777; margin-top: 2px; line-height: 1.4; }
</style>
@endpush

@section('content')

<div style="padding: 4px 0 110px;">

    {{-- Role Info Cards --}}
    <div class="section-title" style="margin-bottom:10px">👥 User Management</div>

    <div class="role-info-grid" style="margin-bottom:16px">
        <div class="role-info-card" style="border-color:#fcd0d0;background:#fff5f5">
            <div class="ri-dot" style="background:#e74c3c"></div>
            <div>
                <div class="ri-name" style="color:#e74c3c">Admin</div>
                <div class="ri-desc">Full access + kelola user. Akses ke semua factory &amp; shift.</div>
            </div>
        </div>
        <div class="role-info-card" style="border-color:#c5cae9;background:#eef1fa">
            <div class="ri-dot" style="background:#1f3c88"></div>
            <div>
                <div class="ri-name" style="color:#1f3c88">Team Leader (TL)</div>
                <div class="ri-desc">Akses hanya ke factory &amp; shift yang di-assign.</div>
            </div>
        </div>
        <div class="role-info-card" style="border-color:#c8e6c9;background:#f1f8e9">
            <div class="ri-dot" style="background:#2e7d32"></div>
            <div>
                <div class="ri-name" style="color:#2e7d32">Group Leader (GL)</div>
                <div class="ri-desc">Akses hanya ke factory &amp; shift yang di-assign.</div>
            </div>
        </div>
        <div class="role-info-card" style="border-color:#ffe0b2;background:#fff8ec">
            <div class="ri-dot" style="background:#f39c12"></div>
            <div>
                <div class="ri-name" style="color:#f39c12">Pengawas</div>
                <div class="ri-desc">Akses hanya ke factory &amp; shift yang di-assign.</div>
            </div>
        </div>
    </div>

    {{-- Header + Add Button --}}
    <div class="um-header">
        <div style="font-family:'Roboto Condensed',sans-serif;font-size:12px;color:#888;font-weight:600">
            Total: <strong style="color:#222">{{ $users->count() }}</strong> akun
        </div>
        <button class="um-add-btn" onclick="openUserModal()">
            ➕ Tambah User
        </button>
    </div>

    {{-- User Table --}}
    <div class="um-table-wrap">
        <table class="um-table">
            <thead>
                <tr>
                    <th>Nama / Username</th>
                    <th>Role</th>
                    <th>Factory / Shift</th>
                    <th>Dibuat</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody id="umTableBody">
                @forelse($users as $u)
                @php
                    $roleColors = ['admin'=>'#e74c3c','tl'=>'#1f3c88','gl'=>'#2e7d32','pengawas'=>'#f39c12','tv'=>'#6a1b9a'];
                    $roleLabels = ['admin'=>'Admin','tl'=>'Team Leader','gl'=>'Group Leader','pengawas'=>'Pengawas','tv'=>'TV Only'];
                    $color      = $roleColors[$u->role] ?? '#888';
                    $isSelf     = $u->id === auth()->id();
                @endphp
                <tr id="um-row-{{ $u->id }}">
                    <td>
                        <div class="um-name-col">
                            <div class="um-av" style="background:{{ $color }}">
                                {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="um-name-txt">
                                    {{ $u->name }}
                                    @if($isSelf)
                                        <span style="font-size:9px;background:#eee;color:#888;padding:1px 5px;border-radius:4px;font-weight:700">KAMU</span>
                                    @endif
                                </div>
                                <div class="um-username-txt">{{ $u->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-badge role-{{ $u->role }}">
                            {{ $roleLabels[$u->role] ?? $u->role }}
                        </span>
                    </td>
                    <td style="font-size:11px;color:#555;font-family:'Roboto Condensed',sans-serif">
                        @if($u->factory && $u->shift)
                            <strong>{{ $u->factory }}</strong><br>
                            <span style="color:#888">Shift {{ $u->shift }}</span>
                        @else
                            <span style="color:#ccc">—</span>
                        @endif
                    </td>
                    <td style="font-size:11px;color:#aaa;font-family:'Roboto Condensed',sans-serif">
                        {{ $u->created_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td style="text-align:right;white-space:nowrap">
                        <button class="um-action-btn um-btn-edit"
                                onclick="openUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->username }}', '{{ $u->role }}', '{{ $u->factory ?? '' }}', '{{ $u->shift ?? '' }}')"
                        >
                            ✏️ Edit
                        </button>
                        @if($isSelf)
                            <button class="um-action-btn um-btn-self" disabled title="Tidak bisa hapus akun sendiri">🚫</button>
                        @else
                            <button class="um-action-btn um-btn-del"
                                    onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                            >
                                🗑️
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:24px;color:#aaa;font-size:12px">
                        Belum ada user.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ── Add/Edit User Modal ── --}}
<div class="modal-overlay" id="userModal">
    <div class="modal-sheet" style="max-height:90vh;overflow-y:auto">
        <div class="modal-sheet-handle"></div>
        <div class="modal-sheet-header">
            <h3 id="userModalTitle">Tambah User</h3>
            <button class="modal-sheet-close" onclick="closeSheet('userModal')">✕</button>
        </div>
        <div class="um-modal-body">
            <input type="hidden" id="umUserId">
            <div class="um-form-grid">
                <div class="um-field um-field-full">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="umName" placeholder="Contoh: Ahmad Santoso" autocomplete="off">
                </div>
                <div class="um-field">
                    <label>Username *</label>
                    <input type="text" id="umUsername" placeholder="Contoh: ahmad.s" autocomplete="off">
                </div>
                <div class="um-field">
                    <label>Role *</label>
                    <select id="umRole">
                        <option value="">— Pilih Role —</option>
                        <option value="admin">🔴 Admin (Full + User Mgmt)</option>
                        <option value="tl">🔵 Team Leader (TL)</option>
                        <option value="gl">🟢 Group Leader (GL)</option>
                        <option value="pengawas">🟡 Pengawas</option>
                        <option value="tv">📺 TV Only</option>
                    </select>
                </div>
                <div class="um-field">
                    <label>Factory</label>
                    <select id="umFactory">
                        <option value="">— Pilih Factory —</option>
                        <option value="Factory 2">Factory 2</option>
                        <option value="Factory 3 &amp; 4">Factory 3 &amp; 4</option>
                    </select>
                </div>
                <div class="um-field">
                    <label>Shift</label>
                    <select id="umShift">
                        <option value="">— Pilih Shift —</option>
                        <option value="A">Shift A</option>
                        <option value="B">Shift B</option>
                    </select>
                </div>
                <div class="um-field">
                    <label>Password *</label>
                    <input type="password" id="umPassword" placeholder="Min. 6 karakter" autocomplete="new-password">
                    <div class="um-pw-hint" id="umPwHint">Minimal 6 karakter.</div>
                </div>
                <div class="um-field">
                    <label>Konfirmasi Password *</label>
                    <input type="password" id="umPasswordConfirm" placeholder="Ulangi password" autocomplete="new-password">
                </div>
            </div>
            <button class="um-save-btn" id="umSaveBtn" onclick="saveUser()">💾 Simpan</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let _umEditId = null;

const RESTRICTED_ROLES = ['tl', 'gl', 'pengawas'];

function openUserModal(id = null, name = '', username = '', role = '', factory = '', shift = '') {
    _umEditId = id;
    const isEdit = !!id;

    document.getElementById('userModalTitle').textContent = isEdit ? '✏️ Edit User' : '➕ Tambah User';
    document.getElementById('umUserId').value          = id ?? '';
    document.getElementById('umName').value            = name;
    document.getElementById('umUsername').value        = username;
    document.getElementById('umRole').value            = role;
    document.getElementById('umFactory').value         = factory;
    document.getElementById('umShift').value           = shift;
    document.getElementById('umPassword').value        = '';
    document.getElementById('umPasswordConfirm').value = '';
    document.getElementById('umPwHint').textContent    = isEdit
        ? 'Kosongkan jika tidak ingin mengubah password.'
        : 'Minimal 6 karakter.';

    openSheet('userModal');
    setTimeout(() => document.getElementById('umName').focus(), 200);
}

async function saveUser() {
    const id       = _umEditId;
    const name     = document.getElementById('umName').value.trim();
    const username = document.getElementById('umUsername').value.trim();
    const role     = document.getElementById('umRole').value;
    const factory  = document.getElementById('umFactory').value;
    const shift    = document.getElementById('umShift').value;
    const password = document.getElementById('umPassword').value;
    const confirm  = document.getElementById('umPasswordConfirm').value;

    if (!name)     { showToast('Nama tidak boleh kosong.', 'error'); return; }
    if (!username) { showToast('Username tidak boleh kosong.', 'error'); return; }
    if (!role)     { showToast('Pilih role terlebih dahulu.', 'error'); return; }
    if (RESTRICTED_ROLES.includes(role) && !factory) { showToast('Pilih factory untuk role ini.', 'error'); return; }
    if (RESTRICTED_ROLES.includes(role) && !shift)   { showToast('Pilih shift untuk role ini.', 'error'); return; }
    if (!id && !password)         { showToast('Password wajib diisi.', 'error'); return; }
    if (password && password.length < 6) { showToast('Password minimal 6 karakter.', 'error'); return; }
    if (password && password !== confirm) { showToast('Konfirmasi password tidak cocok.', 'error'); return; }

    const btn = document.getElementById('umSaveBtn');
    btn.disabled = true; btn.textContent = '⏳ Menyimpan…';

    try {
        const url    = id ? `/admin/users/${id}` : '/admin/users';
        const method = id ? 'PUT' : 'POST';
    const body   = { name, username, role, factory, shift };
        if (password) { body.password = password; body.password_confirmation = confirm; }

        const res  = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();

        if (!res.ok) {
            const firstErr = data.errors ? Object.values(data.errors)[0][0] : (data.message ?? 'Gagal menyimpan.');
            showToast(firstErr, 'error');
            return;
        }

        showToast(data.message ?? '✅ Berhasil!', 'success');
        closeSheet('userModal');
        // Reload halaman untuk refresh tabel
        setTimeout(() => window.location.reload(), 600);
    } catch (e) {
        showToast('Gagal: ' + e.message, 'error');
    } finally {
        btn.disabled = false; btn.textContent = '💾 Simpan';
    }
}

async function deleteUser(id, name) {
    if (!confirm(`Hapus user "${name}"?\n\nAksi ini tidak bisa dibatalkan.`)) return;

    try {
        const res  = await fetch(`/admin/users/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();

        if (!res.ok) { showToast(data.message ?? 'Gagal menghapus.', 'error'); return; }

        showToast(data.message ?? '🗑️ User dihapus.', 'success');
        document.getElementById(`um-row-${id}`)?.remove();
    } catch (e) {
        showToast('Gagal: ' + e.message, 'error');
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeSheet('userModal');
});
</script>
@endpush
