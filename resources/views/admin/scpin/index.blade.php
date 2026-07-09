@extends('layouts.admin')
@section('title', 'SC PIN Protection')

@section('content')
<style>
    .scpin-card {
        background: var(--card-bg, #fff);
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,.07);
        padding: 20px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .scpin-info { flex: 1; }
    .scpin-name { font-size: 15px; font-weight: 600; color: var(--text-primary, #1a1a2e); }
    .scpin-sub  { font-size: 12px; color: #888; margin-top: 2px; }
    .scpin-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
    .pin-status { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
    .pin-on  { background: #d4edda; color: #155724; }
    .pin-off { background: #f0f0f0; color: #888; }
    .pin-form { display: flex; gap: 8px; align-items: center; }
    .pin-input { border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 6px 10px; font-size: 13px; width: 120px; outline: none; }
    .pin-input:focus { border-color: var(--brand-primary, #1f3c88); }
    .btn-pin-toggle { padding: 6px 14px; border-radius: 8px; border: none; cursor: pointer; font-size: 12px; font-weight: 600; }
    .btn-enable  { background: var(--brand-primary, #1f3c88); color: #fff; }
    .btn-disable { background: #e74c3c; color: #fff; }
    .page-header { margin-bottom: 20px; }
    .page-header h2 { font-size: 20px; font-weight: 700; color: var(--text-primary, #1a1a2e); margin: 0 0 4px; }
    .page-header p  { font-size: 12px; color: #888; margin: 0; }
</style>

<div class="main">
    <div class="page-header">
        <h2>🔐 SC PIN Protection</h2>
        <p>Konfigurasi PIN per Sub-Center. Jika aktif, pengguna dari SC lain harus memasukkan PIN sebelum dapat berpindah ke SC ini.</p>
    </div>

    @foreach($scs as $sc)
        <div class="scpin-card" id="scpin-card-{{ $sc->id }}">
            <div class="scpin-info">
                <div class="scpin-name">{{ $sc->name }}</div>
                <div class="scpin-sub">{{ $sc->short_label }}</div>
            </div>
            <div class="scpin-actions">
                <span class="pin-status {{ $sc->require_pin ? 'pin-on' : 'pin-off' }}" id="status-{{ $sc->id }}">
                    {{ $sc->require_pin ? '🔒 PIN Aktif' : '🔓 Tidak Terlindungi' }}
                </span>
                <div class="pin-form">
                    @if(!$sc->require_pin)
                        <input type="password" class="pin-input" id="pin-{{ $sc->id }}"
                               placeholder="Buat PIN (min 4 karakter)" maxlength="12">
                        <button class="btn-pin-toggle btn-enable" onclick="enablePin({{ $sc->id }})">
                            ✅ Aktifkan
                        </button>
                    @else
                        <input type="password" class="pin-input" id="pin-{{ $sc->id }}"
                               placeholder="PIN baru (opsional)">
                        <button class="btn-pin-toggle btn-disable" onclick="disablePin({{ $sc->id }})">
                            🔓 Nonaktifkan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';

    async function enablePin(scId) {
        const pin = document.getElementById('pin-' + scId)?.value?.trim();
        if (!pin || pin.length < 4) { showToast('PIN minimal 4 karakter', 'error'); return; }
        const res = await fetch(`/admin/scpin/${scId}/toggle`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ require_pin: true, pin })
        });
        const data = await res.json();
        if (data.ok) { showToast('🔒 PIN aktif untuk SC ini', 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message || 'Gagal', 'error');
    }

    async function disablePin(scId) {
        if (!confirm('Nonaktifkan proteksi PIN untuk SC ini?')) return;
        const pin = document.getElementById('pin-' + scId)?.value?.trim() || null;
        const res = await fetch(`/admin/scpin/${scId}/toggle`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ require_pin: false, pin })
        });
        const data = await res.json();
        if (data.ok) { showToast('🔓 Proteksi PIN dinonaktifkan', 'info'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message || 'Gagal', 'error');
    }
</script>
@endpush
