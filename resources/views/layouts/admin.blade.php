<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>@yield('title', 'HENKATEN BOARD') — Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Fonts --}}
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Roboto+Condensed:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

{{-- xlsx untuk export Excel di browser --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="{{ asset('js/auto-sync.js') }}"></script>

{{-- CSS utama --}}
<link rel="stylesheet" href="{{ asset('assets/css/henkaten.css') }}">

@stack('styles')
</head>
<body>

{{-- ── LOADING OVERLAY ── --}}
<div class="loading-overlay" id="loadingEl">
    <div class="loading-spinner"></div>
    <div class="loading-text" id="loadingText">Loading...</div>
</div>

{{-- ── TOAST ── --}}
<div class="toast" id="toastEl"></div>

{{-- ── DRAWER OVERLAY ── --}}
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

{{-- ── DRAWER MENU ── --}}
<div class="drawer-menu" id="drawerMenu">
    <div class="drawer-header">
        <div class="dh-logo" style="font-family:'Orbitron',sans-serif;font-weight:900;font-size:16px;letter-spacing:1.5px;color:#f5a623;text-shadow:0 0 12px rgba(245,166,35,.4);">HENKATEN BOARD</div>
        <div class="dh-user">{{ auth()->user()->name }} ({{ auth()->user()->role }})</div>
    </div>
    <div class="drawer-nav">
        <button class="drawer-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.dashboard') }}'">
            <span class="di-icon">🏠</span>Dashboard
        </button>

        {{-- TV Mode Dropdown --}}
        <button onclick="toggleTvDropdown()" id="tvDropdownBtn"
                style="display:flex;align-items:center;justify-content:space-between;
                       width:calc(100% - 20px);margin:4px 10px;padding:9px 14px;
                       border:none;cursor:pointer;border-radius:10px;
                       background:linear-gradient(135deg,#1a237e,#283593);
                       box-shadow:0 2px 8px rgba(26,35,126,.4);transition:filter .15s;"
                onmouseover="this.style.filter='brightness(1.15)'"
                onmouseout="this.style.filter='brightness(1)'">
            <span style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:16px;line-height:1;">📺</span>
                <span style="font-family:'Roboto Condensed',sans-serif;font-weight:700;
                             font-size:13px;letter-spacing:.5px;color:#fff;">TV Mode</span>
            </span>
            <span id="tvChevron" style="font-size:9px;transition:transform .25s;color:rgba(255,255,255,.7);">▼</span>
        </button>

        <div id="tvDropdown" style="display:none;padding:5px 10px 8px;">
            @php
                $tvFactories = [
                    ['key'=>'Factory 2',     'label'=>'Factory 2',  'short'=>'F2',
                     'grad'=>'linear-gradient(135deg,#2e7d32,#43a047)'],
                    ['key'=>'Factory 3 & 4', 'label'=>'Factory 3&4','short'=>'F3&4',
                     'grad'=>'linear-gradient(135deg,#1565c0,#1e88e5)'],
                ];
            @endphp
            @foreach($tvFactories as $fac)
            <div style="margin-bottom:6px;border-radius:9px;overflow:hidden;
                        box-shadow:0 1px 6px rgba(0,0,0,.25);">
                {{-- Factory header --}}
                <div style="background:{{ $fac['grad'] }};padding:5px 10px;
                            display:flex;align-items:center;gap:5px;">
                    <span style="font-size:11px;">🏭</span>
                    <span style="font-family:'Orbitron',sans-serif;font-weight:700;
                                 font-size:8px;letter-spacing:1.2px;color:#fff;
                                 text-transform:uppercase;">{{ $fac['label'] }}</span>
                </div>
                {{-- Shift buttons --}}
                <div style="display:flex;background:#fff;">
                    <button onclick="openTvMode('{{ $fac['key'] }}','A')"
                            style="flex:1;padding:8px 4px;border:none;
                                   border-right:1px solid #e0e0e0;
                                   background:transparent;cursor:pointer;
                                   display:flex;flex-direction:column;align-items:center;gap:3px;
                                   transition:background .12s;"
                            onmouseover="this.style.background='#e8f5e9'"
                            onmouseout="this.style.background='transparent'">
                        <span style="font-family:'Roboto Condensed',sans-serif;font-weight:900;
                                     font-size:13px;color:#222;">{{ $fac['short'] }}</span>
                        <span style="font-family:'Roboto Condensed',sans-serif;font-weight:800;
                                     font-size:8px;letter-spacing:.8px;text-transform:uppercase;
                                     color:#fff;background:#f5a623;padding:1px 7px;border-radius:20px;">
                            SHIFT A
                        </span>
                    </button>
                    <button onclick="openTvMode('{{ $fac['key'] }}','B')"
                            style="flex:1;padding:8px 4px;border:none;
                                   background:transparent;cursor:pointer;
                                   display:flex;flex-direction:column;align-items:center;gap:3px;
                                   transition:background .12s;"
                            onmouseover="this.style.background='#ffebee'"
                            onmouseout="this.style.background='transparent'">
                        <span style="font-family:'Roboto Condensed',sans-serif;font-weight:900;
                                     font-size:13px;color:#222;">{{ $fac['short'] }}</span>
                        <span style="font-family:'Roboto Condensed',sans-serif;font-weight:800;
                                     font-size:8px;letter-spacing:.8px;text-transform:uppercase;
                                     color:#fff;background:#ef5350;padding:1px 7px;border-radius:20px;">
                            SHIFT B
                        </span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        <div class="drawer-divider"></div>
        <div style="padding:10px 20px 4px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#bbb;font-family:'Roboto Condensed',sans-serif;">Halaman</div>

        <button class="drawer-item {{ request()->routeIs('admin.members.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.members.index') }}'">
            <span class="di-icon">👥</span>Member Management
        </button>

        <button class="drawer-item {{ request()->routeIs('admin.absence.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.absence.index', ['factory' => session('factory','Factory 2'), 'shift' => session('shift','A')]) }}'">
            <span class="di-icon">✅</span>Input Absen
        </button>

        <button class="drawer-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.reports.index') }}'">
            <span class="di-icon">📊</span>Laporan
        </button>

        {{-- User Management — hanya untuk admin --}}
        @if(auth()->user()->role === 'admin')
        <div class="drawer-divider"></div>
        <div style="padding:10px 20px 4px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#bbb;font-family:'Roboto Condensed',sans-serif;">Admin</div>
        <button class="drawer-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.users.index') }}'">
            <span class="di-icon">🔐</span>User Management
        </button>
        @endif

        <div class="drawer-divider"></div>

        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="drawer-item" style="color:var(--red);width:100%">
                <span class="di-icon">🚪</span>Logout
            </button>
        </form>
    </div>
</div>

{{-- ── HEADER ── --}}
<div class="app-header" style="padding:0 12px;gap:10px;">
    <button class="header-menu-btn" onclick="openDrawer()">☰</button>

    {{-- Logo Sugity + Nama --}}
    <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
        <img src="{{ asset('images/sugity.png') }}"
             alt="Sugity Creatives"
             style="height:36px;width:auto;object-fit:contain;flex-shrink:0;"
             onerror="this.style.display='none'">
        <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
            <span style="font-family:'Orbitron',sans-serif;font-weight:900;font-size:16px;letter-spacing:2px;color:#f5a623;text-shadow:0 0 14px rgba(245,166,35,.5);white-space:nowrap;">HENKATEN BOARD</span>
            <span style="font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:11px;letter-spacing:1.2px;color:rgba(255,255,255,.85);text-transform:uppercase;white-space:nowrap;">
                {!! session('factory','Factory 2') === 'Factory 2' ? 'Factory 2' : 'Factory 3 &amp; 4' !!}
            </span>
        </div>
    </div>

    <div class="header-clock" id="headerClock">00:00:00</div>

    {{-- Factory switcher — tombol pill --}}
    <button onclick="showFactoryPicker()"
            style="background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.25);border-radius:20px;padding:5px 12px;color:#fff;font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:11px;letter-spacing:.8px;cursor:pointer;display:flex;align-items:center;gap:5px;white-space:nowrap;transition:background .15s;"
            onmouseover="this.style.background='rgba(255,255,255,.2)'"
            onmouseout="this.style.background='rgba(255,255,255,.12)'"
            id="headerFactory">
        🏭 {{ session('factory', 'Factory 2') === 'Factory 2' ? 'F2' : 'F3&4' }}
    </button>
</div>

{{-- ── MAIN CONTENT ── --}}
<div class="main-content">
    @yield('content')
</div>

{{-- ── BOTTOM NAV ── --}}
@php
    $currentFactory = session('factory', 'Factory 2');
    $currentShift   = session('shift', 'A');
    $absenUrl       = route('admin.absence.index', ['factory' => $currentFactory, 'shift' => $currentShift]);
@endphp

<nav class="bottom-nav">
    <button class="nav-btn {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
            onclick="window.location='{{ route('admin.dashboard') }}'">
        <span class="nav-icon">🏠</span>Dashboard
    </button>
    <button class="nav-btn {{ request()->routeIs('admin.absence.*','admin.members.*','admin.attendance.*') ? 'active' : '' }}"
            onclick="window.location='{{ $absenUrl }}'">
        <span class="nav-icon">✅</span>Absen
    </button>
    <button class="nav-btn {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
            onclick="window.location='{{ route('admin.reports.index') }}'">
        <span class="nav-icon">📊</span>Laporan
    </button>
</nav>

{{-- ── FACTORY PICKER MODAL ── --}}
<div class="modal-overlay" id="factorySheet">
    <div class="modal-sheet">
        <div class="modal-sheet-handle"></div>
        <div class="modal-sheet-header">
            <h3>Pilih Factory</h3>
            <button class="modal-sheet-close" onclick="closeSheet('factorySheet')">✕</button>
        </div>
        <div class="modal-sheet-body">
            <div style="display:flex;flex-direction:column;gap:12px">
                <button class="btn-primary" onclick="setFactory('Factory 2')">🏭 Factory 2</button>
                <button class="btn-primary" style="background:linear-gradient(135deg,var(--navy),var(--blue))"
                        onclick="setFactory('Factory 3 &amp; 4')">🏭 Factory 3 &amp; 4</button>
            </div>
        </div>
    </div>
</div>

{{-- ── TV PICKER SHEET (dari bottom nav) ── --}}
<div class="modal-overlay" id="tvPickerSheet">
    <div class="modal-sheet">
        <div class="modal-sheet-handle"></div>
        <div class="modal-sheet-header">
            <h3>📺 Buka TV Mode</h3>
            <button class="modal-sheet-close" onclick="closeSheet('tvPickerSheet')">✕</button>
        </div>
        <div class="modal-sheet-body" style="padding:8px 14px 20px;">
            @php
                $tvFacsSheet = [
                    ['key'=>'Factory 2',     'label'=>'Factory 2',     'short'=>'F2',
                     'grad'=>'linear-gradient(135deg,#2e7d32,#43a047)',
                     'icon'=>'🏭'],
                    ['key'=>'Factory 3 & 4', 'label'=>'Factory 3 & 4', 'short'=>'F3&4',
                     'grad'=>'linear-gradient(135deg,#1565c0,#1e88e5)',
                     'icon'=>'🏭'],
                ];
            @endphp
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($tvFacsSheet as $fac)
                <div style="border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.12);">
                    {{-- Header factory --}}
                    <div style="background:{{ $fac['grad'] }};padding:10px 16px;
                                display:flex;align-items:center;gap:8px;">
                        <span style="font-size:18px;">{{ $fac['icon'] }}</span>
                        <span style="font-family:'Orbitron',sans-serif;font-weight:900;
                                     font-size:12px;letter-spacing:1.5px;color:#fff;
                                     text-transform:uppercase;">{{ $fac['label'] }}</span>
                    </div>
                    {{-- Shift buttons --}}
                    <div style="display:flex;background:#f8f9fa;border:1px solid #e0e0e0;
                                border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
                        <button onclick="openTvMode('{{ $fac['key'] }}','A');closeSheet('tvPickerSheet')"
                                style="flex:1;padding:14px 8px;border:none;border-right:1px solid #e0e0e0;
                                       background:transparent;cursor:pointer;transition:background .15s;
                                       display:flex;flex-direction:column;align-items:center;gap:4px;"
                                onmouseover="this.style.background='#e8f5e9'"
                                onmouseout="this.style.background='transparent'">
                            <span style="font-family:'Roboto Condensed',sans-serif;font-weight:900;
                                         font-size:16px;color:#333;">{{ $fac['short'] }}</span>
                            <span style="font-family:'Roboto Condensed',sans-serif;font-weight:800;
                                         font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                         color:#fff;background:#f5a623;padding:2px 10px;border-radius:20px;">
                                SHIFT A
                            </span>
                        </button>
                        <button onclick="openTvMode('{{ $fac['key'] }}','B');closeSheet('tvPickerSheet')"
                                style="flex:1;padding:14px 8px;border:none;
                                       background:transparent;cursor:pointer;transition:background .15s;
                                       display:flex;flex-direction:column;align-items:center;gap:4px;"
                                onmouseover="this.style.background='#ffebee'"
                                onmouseout="this.style.background='transparent'">
                            <span style="font-family:'Roboto Condensed',sans-serif;font-weight:900;
                                         font-size:16px;color:#333;">{{ $fac['short'] }}</span>
                            <span style="font-family:'Roboto Condensed',sans-serif;font-weight:800;
                                         font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                         color:#fff;background:#ef5350;padding:2px 10px;border-radius:20px;">
                                SHIFT B
                            </span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── SHARED JS ── --}}
<script>
const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]').content;
const CURRENT_FACTORY = @json(session('factory', 'Factory 2'));
const CURRENT_SHIFT   = @json(session('shift', 'A'));

async function api(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

function updateClock() {
    document.getElementById('headerClock').textContent =
        new Date().toLocaleTimeString('en-GB', { hour12: false });
}
setInterval(updateClock, 1000);
updateClock();

function showToast(msg, type = 'info') {
    const t = document.getElementById('toastEl');
    t.textContent = msg;
    t.className = `toast ${type} show`;
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 2800);
}

function showLoading(msg) {
    document.getElementById('loadingEl').classList.add('show');
    document.getElementById('loadingText').textContent = msg || 'Loading...';
}
function hideLoading() { document.getElementById('loadingEl').classList.remove('show'); }

function openDrawer()  { document.getElementById('drawerMenu').classList.add('open'); document.getElementById('drawerOverlay').classList.add('show'); }
function closeDrawer() { document.getElementById('drawerMenu').classList.remove('open'); document.getElementById('drawerOverlay').classList.remove('show'); }

function openSheet(id)  { document.getElementById(id).classList.add('show'); }
function closeSheet(id) { document.getElementById(id).classList.remove('show'); }
function showFactoryPicker() { openSheet('factorySheet'); }

async function setFactory(factory) {
    await api('{{ route('admin.context') }}', 'POST', { factory, shift: CURRENT_SHIFT });
    const cur = window.location.pathname;
    if (cur.includes('/absence')) {
        const params = new URLSearchParams(window.location.search);
        params.set('factory', factory);
        params.set('shift', CURRENT_SHIFT);
        window.location.href = '{{ route('admin.absence.index') }}?' + params.toString();
    } else {
        window.location.reload();
    }
}

async function switchShift(shift) {
    await api('{{ route('admin.context') }}', 'POST', { factory: CURRENT_FACTORY, shift });
    const cur = window.location.pathname;
    if (cur.includes('/absence')) {
        const params = new URLSearchParams(window.location.search);
        params.set('factory', CURRENT_FACTORY);
        params.set('shift', shift);
        window.location.href = '{{ route('admin.absence.index') }}?' + params.toString();
    } else {
        window.location.reload();
    }
}

function toggleTvBottomSheet() { openSheet('tvPickerSheet'); }

function toggleTvDropdown() {
    const dd  = document.getElementById('tvDropdown');
    const chv = document.getElementById('tvChevron');
    const open = dd.style.display === 'none';
    dd.style.display  = open ? 'block' : 'none';
    chv.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
}

function openTvMode(factory, shift) {
    // Buka TV Mode langsung dengan factory & shift di query string
    // Tidak mengubah session global — TV page baca dari ?factory=...&shift=...
    const url = '{{ route('admin.tv') }}?factory=' + encodeURIComponent(factory) + '&shift=' + encodeURIComponent(shift);
    window.open(url, '_blank');
}

let touchStartX = 0, touchStartY = 0;
document.addEventListener('touchstart', e => {
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
}, { passive: true });
document.addEventListener('touchend', e => {
    const dx = e.changedTouches[0].clientX - touchStartX;
    const dy = e.changedTouches[0].clientY - touchStartY;
    if (Math.abs(dx) > 60 && Math.abs(dy) < 40) {
        if (dx > 0 && touchStartX < 40) openDrawer();
        else if (dx < 0) closeDrawer();
    }
}, { passive: true });
</script>

@stack('scripts')
</body>
</html>