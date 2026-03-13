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
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Roboto+Condensed:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">

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

    {{-- ── DRAWER MENU ── --}}
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer-menu" id="drawerMenu">

        {{-- Header --}}
        <div class="drawer-header">
            <div class="dh-logo">HENKATEN BOARD</div>
            <div class="dh-user">{{ auth()->user()->name }} ({{ auth()->user()->role }})</div>
            <button class="drawer-close-btn" onclick="closeDrawer()">
                {{-- Lucide X --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <div class="drawer-nav">

            {{-- Dashboard --}}
            <button class="drawer-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.dashboard') }}'">
                <span class="di-icon">
                    {{-- Lucide LayoutDashboard --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                </span>
                Dashboard
            </button>

            {{-- TV Mode --}}
            <button class="tv-mode-btn" onclick="toggleTvDropdown()" id="tvDropdownBtn">
                <span class="tv-mode-btn-left">
                    <span class="tv-mode-icon">
                        {{-- Lucide Monitor --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                        </svg>
                    </span>
                    <span class="tv-mode-label">Mode TV</span>
                </span>
                <span id="tvChevron">
                    {{-- Lucide ChevronDown --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </span>
            </button>

            {{-- TV Dropdown --}}
            <div id="tvDropdown" style="display:none;">
                @php
                    $tvFactories = [
                        ['key' => 'Factory 2', 'label' => 'Factory 2', 'short' => 'F2', 'grad' => 'linear-gradient(135deg,#2e7d32,#43a047)'],
                        ['key' => 'Factory 3 & 4', 'label' => 'Factory 3&4', 'short' => 'F3&4', 'grad' => 'linear-gradient(135deg,#1565c0,#1e88e5)'],
                    ];
                @endphp
                @foreach($tvFactories as $fac)
                    <div class="tv-factory-block">
                        <div class="tv-factory-header" style="background:{{ $fac['grad'] }};">
                            {{-- Lucide Factory --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 20V9l7-4v4l7-4v4l4-2v13H2z" />
                            </svg>
                            <span>{{ $fac['label'] }}</span>
                        </div>
                        <div class="tv-shift-row">
                            <button class="tv-shift-btn" onclick="openTvMode('{{ $fac['key'] }}','A')"
                                onmouseover="this.style.background='#e8f5e9'"
                                onmouseout="this.style.background='transparent'">
                                <span class="tv-shift-factory-label">{{ $fac['short'] }}</span>
                                <span class="tv-shift-badge badge-a">SHIFT A</span>
                            </button>
                            <button class="tv-shift-btn" onclick="openTvMode('{{ $fac['key'] }}','B')"
                                onmouseover="this.style.background='#ffebee'"
                                onmouseout="this.style.background='transparent'">
                                <span class="tv-shift-factory-label">{{ $fac['short'] }}</span>
                                <span class="tv-shift-badge badge-b">SHIFT B</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="drawer-divider"></div>
            <div class="drawer-section-label">Halaman</div>

            {{-- Member Management --}}
            <button class="drawer-item {{ request()->routeIs('admin.members.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.members.index') }}'">
                <span class="di-icon">
                    {{-- Lucide Users --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </span>
                Member Management
            </button>

            {{-- Input Absen --}}
            <button class="drawer-item {{ request()->routeIs('admin.absence.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.absence.index', ['factory' => session('factory', 'Factory 2'), 'shift' => session('shift', 'A')]) }}'">
                <span class="di-icon">
                    {{-- Lucide CheckSquare --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                </span>
                Input Absen
            </button>

            {{-- Laporan --}}
            <button class="drawer-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.reports.index') }}'">
                <span class="di-icon">
                    {{-- Lucide BarChart2 --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                </span>
                Laporan
            </button>

            @if(auth()->user()->role === 'admin')
                <div class="drawer-divider"></div>
                <div class="drawer-section-label">Admin</div>

                {{-- User Management --}}
                <button class="drawer-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    onclick="window.location='{{ route('admin.users.index') }}'">
                    <span class="di-icon">
                        {{-- Lucide ShieldCheck --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <polyline points="9 12 11 14 15 10" />
                        </svg>
                    </span>
                    User Management
                </button>
            @endif

            <div class="drawer-divider"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="drawer-item logout-item" style="width:calc(100% - 24px)">
                    <span class="di-icon">
                        {{-- Lucide LogOut --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </span>
                    Keluar dari aplikasi
                </button>
            </form>

        </div>
    </div>

    {{-- ── HEADER ── --}}
    <div class="app-header" style="padding:0 12px;gap:10px;">
        <button class="header-menu-btn" onclick="openDrawer()">☰</button>

        {{-- Logo Sugity + Nama --}}
        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
            <img src="{{ asset('images/sugity.png') }}" alt="Sugity Creatives"
                style="height:36px;width:auto;object-fit:contain;flex-shrink:0;" onerror="this.style.display='none'">
            <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
                <span
                    style="font-family:'Orbitron',sans-serif;font-weight:900;font-size:16px;letter-spacing:2px;color:#f5a623;text-shadow:0 0 14px rgba(245,166,35,.5);white-space:nowrap;">HENKATEN
                    BOARD</span>
                <span
                    style="font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:11px;letter-spacing:1.2px;color:rgba(255,255,255,.85);text-transform:uppercase;white-space:nowrap;">
                    {!! session('factory', 'Factory 2') === 'Factory 2' ? 'Factory 2' : 'Factory 3 &amp; 4' !!}
                </span>
            </div>
        </div>

        <div class="header-clock" id="headerClock">00:00:00</div>

        {{-- Factory switcher — tombol pill keren --}}
        <button onclick="showFactoryPicker()" id="headerFactory" style="background:rgba(255,255,255,.12);
                   border:1.5px solid rgba(255,255,255,.25);
                   border-radius:20px;
                   padding:5px 14px;
                   color:#fff;
                   font-family:'Roboto Condensed',sans-serif;
                   font-weight:700;
                   font-size:11px;
                   letter-spacing:.8px;
                   cursor:pointer;
                   display:flex;
                   align-items:center;
                   gap:5px;
                   white-space:nowrap;
                   transition:all .2s ease;" onmouseover="
                this.style.background='linear-gradient(135deg,rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.10) 50%,rgba(255,255,255,0.20) 100%)';
                this.style.backdropFilter='blur(12px) saturate(1.8)';
                this.style.webkitBackdropFilter='blur(12px) saturate(1.8)';
                this.style.boxShadow='0 4px 16px rgba(0,0,0,0.18),inset 0 1px 0 rgba(255,255,255,0.5)';
                this.style.borderColor='rgba(255,255,255,0.45)';" onmouseout="
                this.style.background='rgba(255,255,255,.12)';
                this.style.backdropFilter='none';
                this.style.webkitBackdropFilter='none';
                this.style.boxShadow='none';
                this.style.borderColor='rgba(255,255,255,.25)';">
            {{ session('factory', 'Factory 2') === 'Factory 2' ? 'F2' : 'F3&4' }}
        </button>

        {{-- User info --}}
        <div style="display:flex;align-items:center;gap:10px;margin-left:8px;">
            <div style="text-align:right;line-height:1.2;">
                <div
                    style="color:#fff;font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:13px;letter-spacing:.4px;">
                    {{ Auth::user()->name ?? 'Admin User' }}
                </div>
                <div style="color:rgba(255,255,255,.65);font-size:10px;letter-spacing:.5px;">
                    {{ Auth::user()->role_label ?? Auth::user()->role ?? 'Administrator' }}
                </div>
            </div>
            <div
                style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#f5a623,#e67e22);border:2px solid rgba(255,255,255,.35);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-family:'Roboto Condensed',sans-serif;font-weight:900;font-size:14px;color:#fff;letter-spacing:.5px;">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
        </div>
    </div>


    {{-- ── MAIN CONTENT ── --}}
    <div class="main-content">
        @yield('content')
    </div>

    {{-- ── BOTTOM NAV ── --}}
    @php
        $currentFactory = session('factory', 'Factory 2');
        $currentShift = session('shift', 'A');
        $absenUrl = route('admin.absence.index', ['factory' => $currentFactory, 'shift' => $currentShift]);
    @endphp

    <nav class="bottom-nav">

        {{-- Dashboard --}}
        <button class="nav-btn {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.dashboard') }}'">
            <span class="nav-icon">
                {{-- Lucide House --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9.5z"/>
                    <polyline points="9 21 9 12 15 12 15 21"/>
                </svg>
            </span>
            Dashboard
        </button>

        {{-- Absen --}}
        <button class="nav-btn {{ request()->routeIs('admin.absence.*','admin.members.*','admin.attendance.*') ? 'active' : '' }}"
                onclick="window.location='{{ $absenUrl }}'">
            <span class="nav-icon">
                {{-- Lucide CheckSquare --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
            </span>
            Absen
        </button>

        {{-- Laporan --}}
        <button class="nav-btn {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.reports.index') }}'">
            <span class="nav-icon">
                {{-- Lucide BarChart2 --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </span>
            Laporan
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
                    <button class="btn-primary" style="background:#e16013;" onclick="setFactory('Factory 3 &amp; 4')">🏭
                        Factory 3 &amp; 4</button>
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
                        [
                            'key' => 'Factory 2',
                            'label' => 'Factory 2',
                            'short' => 'F2',
                            'grad' => 'linear-gradient(135deg,#2e7d32,#43a047)',
                            'icon' => '🏭'
                        ],
                        [
                            'key' => 'Factory 3 & 4',
                            'label' => 'Factory 3 & 4',
                            'short' => 'F3&4',
                            'grad' => 'linear-gradient(135deg,#1565c0,#1e88e5)',
                            'icon' => '🏭'
                        ],
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
                                <button onclick="openTvMode('{{ $fac['key'] }}','A');closeSheet('tvPickerSheet')" style="flex:1;padding:14px 8px;border:none;border-right:1px solid #e0e0e0;
                                                       background:transparent;cursor:pointer;transition:background .15s;
                                                       display:flex;flex-direction:column;align-items:center;gap:4px;"
                                    onmouseover="this.style.background='#e8f5e9'"
                                    onmouseout="this.style.background='transparent'">
                                    <span style="font-family:'Roboto Condensed',sans-serif;font-weight:900;
                                                         font-size:16px;color:#333;">{{ $fac['short'] }}</span>
                                    <span
                                        style="font-family:'Roboto Condensed',sans-serif;font-weight:800;
                                                         font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                                         color:#fff;background:#f5a623;padding:2px 10px;border-radius:20px;">
                                        SHIFT A
                                    </span>
                                </button>
                                <button onclick="openTvMode('{{ $fac['key'] }}','B');closeSheet('tvPickerSheet')" style="flex:1;padding:14px 8px;border:none;
                                                       background:transparent;cursor:pointer;transition:background .15s;
                                                       display:flex;flex-direction:column;align-items:center;gap:4px;"
                                    onmouseover="this.style.background='#ffebee'"
                                    onmouseout="this.style.background='transparent'">
                                    <span style="font-family:'Roboto Condensed',sans-serif;font-weight:900;
                                                         font-size:16px;color:#333;">{{ $fac['short'] }}</span>
                                    <span
                                        style="font-family:'Roboto Condensed',sans-serif;font-weight:800;
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
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        const CURRENT_FACTORY = @json(session('factory', 'Factory 2'));
        const CURRENT_SHIFT = @json(session('shift', 'A'));

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

        function openDrawer() { document.getElementById('drawerMenu').classList.add('open'); document.getElementById('drawerOverlay').classList.add('show'); }
        function closeDrawer() { document.getElementById('drawerMenu').classList.remove('open'); document.getElementById('drawerOverlay').classList.remove('show'); }

        function openSheet(id) { document.getElementById(id).classList.add('show'); }
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
            const dd = document.getElementById('tvDropdown');
            const chv = document.getElementById('tvChevron');
            const open = dd.style.display === 'none';
            dd.style.display = open ? 'block' : 'none';
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