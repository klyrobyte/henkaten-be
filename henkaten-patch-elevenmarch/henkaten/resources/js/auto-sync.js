/**
 * auto-sync.js
 * ─────────────────────────────────────────────────────────────────────────
 * Taruh file ini di public/js/auto-sync.js
 * Lalu include di layouts/admin.blade.php sebelum @stack('scripts'):
 *
 *   <script src="{{ asset('js/auto-sync.js') }}"></script>
 *
 * Cara kerja:
 *  1. Setiap halaman yang membutuhkan sync mendaftarkan handler via
 *     window.HKSync.onUpdate(callback)
 *  2. Module ini polling ke /admin/absence/data setiap POLL_MS milidetik
 *  3. Jika hash data berubah → semua handler dipanggil dengan data terbaru
 *  4. Page absen auto-save tiap kali user klik Hadir/Absen (debounced 800ms)
 * ─────────────────────────────────────────────────────────────────────────
 */

(function () {
    'use strict';

    const POLL_MS      = 10_000; // polling interval: 10 detik
    const SAVE_DEBOUNCE = 800;   // debounce auto-save absen: 0.8 detik

    // ── Ambil context dari meta tag yang di-inject layout ────────────
    // Di layouts/admin.blade.php tambahkan:
    //   <meta name="hk-factory" content="{{ session('factory','Factory 2') }}">
    //   <meta name="hk-shift"   content="{{ session('shift','A') }}">
    //   <meta name="hk-tanggal" content="{{ request('tanggal', date('Y-m-d')) }}">
    //   <meta name="csrf-token" content="{{ csrf_token() }}">
    function getMeta(name) {
        return document.querySelector(`meta[name="${name}"]`)?.content ?? '';
    }

    const ctx = {
        get factory() { return getMeta('hk-factory') || 'Factory 2'; },
        get shift()   { return getMeta('hk-shift')   || 'A'; },
        get tanggal() { return getMeta('hk-tanggal') || new Date().toISOString().slice(0,10); },
        get csrf()    { return getMeta('csrf-token')  || ''; },
    };

    // ── Internal state ───────────────────────────────────────────────
    let _lastHash     = null;
    let _handlers     = [];       // callback terdaftar
    let _pollTimer    = null;
    let _saveTimer    = null;
    let _indicator    = null;     // elemen DOM kecil di pojok

    // ── Public API ───────────────────────────────────────────────────
    window.HKSync = {
        /** Daftarkan callback yang dipanggil saat data absen berubah */
        onUpdate(fn) { _handlers.push(fn); },

        /** Paksa poll sekarang (misal: setelah save) */
        poll: pollNow,

        /** Trigger auto-save absen (debounced)  - dipanggil dari absen.blade */
        triggerSave: triggerSave,

        /** Tampilkan indikator sync di pojok kanan atas */
        showIndicator: showSyncIndicator,
    };

    // ── Indicator UI ─────────────────────────────────────────────────
    function createIndicator() {
        if (_indicator) return;
        _indicator = document.createElement('div');
        _indicator.id = 'hk-sync-indicator';
        _indicator.style.cssText = `
            position:fixed; bottom:74px; right:10px; z-index:9999;
            display:flex; align-items:center; gap:5px;
            background:rgba(0,0,0,.65); color:#fff;
            font-family:'Roboto Condensed',sans-serif; font-size:10px;
            font-weight:700; padding:4px 9px; border-radius:20px;
            opacity:0; transition:opacity .4s; pointer-events:none;
            backdrop-filter:blur(4px);
        `;
        document.body.appendChild(_indicator);
    }

    function showSyncIndicator(text, type = 'sync') {
        createIndicator();
        const colors = { sync: '#64b5f6', ok: '#81c784', error: '#e57373', save: '#ffb74d' };
        const icons  = { sync: '⟳', ok: '✓', error: '!', save: '💾' };
        _indicator.style.borderLeft = `3px solid ${colors[type] || colors.sync}`;
        _indicator.textContent = `${icons[type] || '⟳'} ${text}`;
        _indicator.style.opacity = '1';
        clearTimeout(_indicator._hideTimer);
        _indicator._hideTimer = setTimeout(() => { _indicator.style.opacity = '0'; }, 2500);
    }

    // ── Hash helper ──────────────────────────────────────────────────
    async function quickHash(data) {
        const str = JSON.stringify(data);
        const buf = await crypto.subtle.digest('SHA-1', new TextEncoder().encode(str));
        return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('');
    }

    // ── Core poll ────────────────────────────────────────────────────
    async function pollNow(silent = false) {
        try {
            const params = new URLSearchParams({
                tanggal: ctx.tanggal,
                factory: ctx.factory,
                shift:   ctx.shift,
            });

            // Poll 2 endpoint paralel
            const [absenceRes, statusRes] = await Promise.all([
                fetch(`/admin/absence/data?${params}`,       { headers: { 'Accept': 'application/json' } }),
                fetch(`/admin/dashboard/status?${params}`,   { headers: { 'Accept': 'application/json' } }),
            ]);

            if (!absenceRes.ok || !statusRes.ok) return;

            const absenceData = await absenceRes.json();
            const statusData  = await statusRes.json();

            const combined = { absenceData, statusData };
            const hash = await quickHash(combined);

            if (hash !== _lastHash) {
                _lastHash = hash;
                if (!silent) showSyncIndicator('Data diperbarui', 'ok');

                // Panggil semua handler terdaftar
                _handlers.forEach(fn => {
                    try { fn({ absenceData, statusData, ctx }); }
                    catch (e) { console.warn('[HKSync] handler error', e); }
                });
            }
        } catch (e) {
            console.warn('[HKSync] poll error', e);
        }
    }

    // ── Auto-save absen (debounced) ───────────────────────────────────
    function triggerSave(absenStateObj) {
        clearTimeout(_saveTimer);
        showSyncIndicator('Menyimpan...', 'save');
        _saveTimer = setTimeout(async () => {
            try {
                await fetch('/admin/absence/save', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': ctx.csrf,
                    },
                    body: JSON.stringify({
                        tanggal: ctx.tanggal,
                        factory: ctx.factory,
                        shift:   ctx.shift,
                        records: absenStateObj,
                    }),
                });
                showSyncIndicator('Tersimpan & sync', 'ok');
                // Langsung poll semua halaman setelah save
                pollNow(true);
            } catch (e) {
                showSyncIndicator('Gagal simpan!', 'error');
            }
        }, SAVE_DEBOUNCE);
    }

    // ── Start polling ────────────────────────────────────────────────
    function startPolling() {
        pollNow(true); // poll sekali langsung saat load
        _pollTimer = setInterval(() => pollNow(), POLL_MS);

        // Pause saat tab tidak aktif, resume saat aktif kembali
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(_pollTimer);
                _pollTimer = null;
            } else {
                pollNow(); // langsung poll saat tab aktif lagi
                _pollTimer = setInterval(() => pollNow(), POLL_MS);
            }
        });
    }

    // ── Boot ─────────────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startPolling);
    } else {
        startPolling();
    }

})();