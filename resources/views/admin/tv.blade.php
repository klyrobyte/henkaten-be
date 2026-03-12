{{--
    resources/views/admin/tv.blade.php
    TV MODE — UI/UX identik persis dengan dashboard.blade.php
    Header = app-header hijau dari admin.blade, semua komponen konten = copy 1:1 dashboard
--}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>📺 TV MODE — HENKATEN BOARD</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Roboto+Condensed:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

{{-- CSS utama (berisi semua --green, --navy, --red, dan semua shared class dashboard) --}}
<link rel="stylesheet" href="{{ asset('assets/css/henkaten.css') }}">

<style>
/* ════════════════════════════════════════════════════════════════
   TV MODE SHELL — layout chrome saja.
   Semua class komponen (mc-card, pills, avatar, legend, dll.)
   dimuat dari henkaten.css yang sama dengan dashboard.
   ════════════════════════════════════════════════════════════════ */

html, body { margin:0; padding:0; overflow:hidden; height:100vh; width:100vw; background:#f4f7f0; }

/* Scroll container — tepat di bawah .app-header (52px) dan di atas ticker (36px) */
.tv-body {
    position: fixed;
    top: 52px; left: 0; right: 0; bottom: 36px;
    overflow: hidden;
}
.tv-body-inner { will-change: transform; }

/* Ticker identik warna app-header */
.tv-ticker {
    position: fixed; bottom: 0; left: 0; right: 0; height: 36px; z-index: 600;
    background: linear-gradient(135deg, #1a5c1a, #2d7a2d);
    border-top: 2px solid rgba(255,255,255,.15);
    display: flex; align-items: center; overflow: hidden;
}
.tv-ticker-lbl {
    flex-shrink: 0; height: 100%; padding: 0 14px;
    background: rgba(0,0,0,.2); border-right: 1px solid rgba(255,255,255,.15);
    display: flex; align-items: center;
    font-family: 'Orbitron', sans-serif; font-size: 13px; font-weight: 900;
    color: #f5a623; letter-spacing: 1.5px; white-space: nowrap;
}
.tv-ticker-track { flex:1; overflow:hidden; height:100%; }
.tv-ticker-inner {
    display: inline-flex; align-items: center; height: 100%;
    white-space: nowrap; gap: 48px; padding-left: 20px;
    animation: tv-roll 40s linear infinite;
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 16px; font-weight: 700; color: rgba(255,255,255,.9);
}
@keyframes tv-roll { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
.tick-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; display:inline-block; margin-right:5px; vertical-align:middle; }

/* TV MODE badge melayang */
.tv-badge {
    position:fixed; top:58px; left:10px; z-index:500; pointer-events:none;
    background:linear-gradient(135deg,#e65100,#ff7043); color:#fff;
    font-family:'Orbitron',sans-serif; font-size:12px; font-weight:900;
    letter-spacing:1.5px; padding:5px 14px; border-radius:20px;
    box-shadow:0 2px 10px rgba(230,81,0,.45);
    animation:tvbadge 2.4s ease-in-out infinite;
}
@keyframes tvbadge { 0%,100%{box-shadow:0 2px 10px rgba(230,81,0,.45)} 50%{box-shadow:0 2px 22px rgba(230,81,0,.85)} }

/* Paused pill */
.tv-paused {
    position:fixed; top:58px; right:10px; z-index:500; display:none; pointer-events:none;
    background:rgba(230,81,0,.92); color:#fff;
    font-family:'Orbitron',sans-serif; font-size:13px; font-weight:900;
    letter-spacing:1.2px; padding:6px 16px; border-radius:20px;
}
.tv-paused.show { display:block; }

/* Fullscreen button */
.tv-fs {
    position:fixed; bottom:44px; right:12px; z-index:500;
    background:rgba(26,92,26,.8); color:rgba(255,255,255,.7);
    border:1px solid rgba(255,255,255,.2); border-radius:8px;
    padding:5px 12px; font-family:'Roboto Condensed',sans-serif;
    font-size:13px; font-weight:700; cursor:pointer; transition:all .2s;
}
.tv-fs:hover { color:#fff; background:rgba(26,92,26,1); }

/* ── Overrides read-only (sembunyikan elemen interaktif) ─────── */
.mc-photo-upload-overlay { display:none !important; }
.mc-addlog-bar           { display:none !important; }
.mc-dot                  { cursor:default !important; pointer-events:none !important; }
.shift-toggle-btn        { cursor:default !important; }
.mc-card                 { cursor:default !important; }

/* ── Tambahan class yang tidak ada di henkaten.css ────────────── */
.tag-repl { background:#e65100; color:#fff; }
.av-repl  { background:linear-gradient(135deg,#e65100,#ff7043); }

/* ── machine-group/cards: identik dashboard ──────────────────── */
.machines-wrap { padding:0 0 20px; }
.machine-group-section { margin-bottom:20px; }
.machine-group-title {
    display:inline-flex; align-items:center; gap:8px;
    background:linear-gradient(135deg,var(--navy),#2c4a9e);
    color:#fff; padding:6px 16px 6px 12px; border-radius:0 20px 20px 0;
    font-family:'Orbitron',sans-serif; font-size:14px; font-weight:700;
    letter-spacing:.8px; margin-bottom:14px; margin-left:-12px;
    box-shadow:2px 2px 8px rgba(31,60,136,.25);
}
.mg-badge { background:rgba(255,255,255,.2); padding:3px 10px; border-radius:8px; font-size:13px; font-weight:600; font-family:'Roboto Condensed',sans-serif; }
.mg-badge.warn { background:rgba(231,76,60,.75); }
.machine-cards-row { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:14px; }

/* ── mc-card: copy persis dashboard ─────────────────────────── */
.mc-card {
    background:#fff; border-radius:14px; border:2px solid #e0ecd4;
    box-shadow:0 2px 10px rgba(0,0,0,.07);
    overflow:hidden; display:flex; flex-direction:column; position:relative;
    transition:box-shadow .2s,border-color .2s;
}
.mc-card.mc-status-man      { border-color:#e74c3c; box-shadow:0 3px 14px rgba(231,76,60,.22); }
.mc-card.mc-status-machine  { border-color:#1f3c88; box-shadow:0 3px 14px rgba(31,60,136,.22); }
.mc-card.mc-status-material { border-color:#f39c12; box-shadow:0 3px 14px rgba(243,156,18,.22); }
.mc-card.mc-status-method   { border-color:#2e7d32; box-shadow:0 3px 14px rgba(46,125,50,.22); }
.mc-card.mc-has-absen { border-color:#e74c3c; box-shadow:0 3px 18px rgba(231,76,60,.3); animation:absenglow 1.5s ease-in-out infinite; }
@keyframes absenglow { 0%,100%{box-shadow:0 3px 14px rgba(231,76,60,.3)} 50%{box-shadow:0 3px 28px rgba(231,76,60,.65)} }

.mc-photo-wrap { position:relative; width:100%; aspect-ratio:16/9; background:linear-gradient(135deg,#eef2e8,#dde8c8); overflow:hidden; border-bottom:1px solid #e0ecd4; }
.mc-card.mc-has-absen .mc-photo-wrap,
.mc-card.mc-status-man .mc-photo-wrap { background:linear-gradient(135deg,#fdeaea,#fad0d0); border-bottom-color:#f4a8a8; }
.mc-photo-wrap img { width:100%; height:100%; object-fit:cover; }
.mc-photo-placeholder { width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; }
.mc-photo-placeholder .ph-ico { font-size:28px; opacity:.25; }
.mc-photo-placeholder .ph-txt { font-family:'Roboto Condensed',sans-serif; font-size:12px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#aaa; }

.mc-name-badge { position:absolute; bottom:0; left:0; right:0; background:linear-gradient(to top,rgba(0,0,0,.6) 0%,transparent 100%); padding:20px 8px 6px; display:flex; align-items:flex-end; justify-content:space-between; z-index:3; pointer-events:none; }
.mc-name-txt { font-family:'Roboto Condensed',sans-serif; font-size:14px; font-weight:800; color:#fff; text-transform:uppercase; letter-spacing:.4px; text-shadow:0 1px 3px rgba(0,0,0,.5); line-height:1; }
.mc-4m-row { display:flex; gap:3px; align-items:center; }
.mc-4m-pip { width:7px; height:7px; border-radius:50%; border:1px solid rgba(255,255,255,.3); }
.mc-dot { width:10px; height:10px; border-radius:50%; border:1.5px solid rgba(255,255,255,.6); display:none; flex-shrink:0; }
.mc-dot.d-visible { display:block; }
.mc-dot.d-absen { display:block; background:#e74c3c; box-shadow:0 0 0 3px rgba(231,76,60,.4); animation:abpulse 1.3s ease-in-out infinite; }
@keyframes abpulse { 0%,100%{box-shadow:0 0 0 2px rgba(231,76,60,.5)} 50%{box-shadow:0 0 0 7px rgba(231,76,60,0)} }

.mc-card-body { padding:8px 8px 10px; display:flex; flex-direction:column; gap:6px; }
.mc-members-row { display:flex; flex-wrap:wrap; gap:8px; justify-content:center; min-height:68px; }
.mc-member-item { display:flex; flex-direction:column; align-items:center; gap:4px; min-width:56px; }
.mc-av { width:58px; height:58px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:900; color:#fff; overflow:hidden; border:2.5px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,.15); font-family:'Roboto Condensed',sans-serif; flex-shrink:0; }
.mc-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.av-ok    { background:linear-gradient(135deg,#8bc34a,var(--green)); }
.av-absen { background:linear-gradient(135deg,#ef9a9a,#c0392b); filter:grayscale(.3); }
.mc-member-name { font-family:'Roboto Condensed',sans-serif; font-size:12px; font-weight:800; text-align:center; color:#555; line-height:1.2; word-break:break-word; overflow:visible; max-width:72px; width:100%; }
.mi-absen .mc-member-name { color:#c0392b; }
.mc-member-tag { font-size:10px; font-family:'Roboto Condensed',sans-serif; font-weight:800; padding:2px 5px; border-radius:3px; text-transform:uppercase; }
.tag-hadir    { background:var(--green); color:#fff; }
.tag-absen    { background:#e74c3c; color:#fff; animation:tagblink .8s step-end infinite; }
.tag-dipinjam { background:#607d8b; color:#fff; }
@keyframes tagblink { 0%,100%{opacity:1} 50%{opacity:.45} }
.mi-dipinjam { opacity:.72; }
.mi-dipinjam .mc-av { filter:grayscale(.55) brightness(.75); border-color:#b0bec5; box-shadow:none; }
.mi-dipinjam .mc-member-name { color:#78909c; }
.mi-dipinjam-dest { font-size:11px; font-family:'Roboto Condensed',sans-serif; font-weight:700; color:#546e7a; text-align:center; line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:64px; }
.mc-empty-slot { display:flex; align-items:center; justify-content:center; border:2px dashed #d0ddc0; border-radius:10px; min-height:54px; color:#ccc; font-size:22px; width:100%; }

.mc-status-row { display:flex; gap:3px; flex-wrap:wrap; justify-content:center; padding-top:5px; border-top:1px solid #eef2e8; }
.mc-status-pill { display:flex; align-items:center; gap:3px; padding:5px 9px; border-radius:20px; border:1.5px solid #e4e4e4; background:#f7f7f7; font-family:'Roboto Condensed',sans-serif; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.3px; color:#aaa; user-select:none; white-space:nowrap; line-height:1; }
.pill-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.mc-status-pill.p-normal.active   { background:#e8f5e9; border-color:var(--green);  color:var(--green); }
.mc-status-pill.p-man.active      { background:#fdeaea; border-color:#e74c3c;       color:#e74c3c; }
.mc-status-pill.p-material.active { background:#fff8e1; border-color:#f39c12;       color:#c67c00; }
.mc-status-pill.p-machine.active  { background:#e8eefa; border-color:#1f3c88;       color:#1f3c88; }
.mc-status-pill.p-method.active   { background:#e8f5e9; border-color:#2e7d32;       color:#2e7d32; }
.pill-dot.dn  { background:#4caf50; }
.pill-dot.dm  { background:#e74c3c; }
.pill-dot.dt  { background:#f39c12; }
.pill-dot.dc  { background:#1f3c88; }
.pill-dot.dme { background:#2e7d32; }
</style>
</head>
<body>

{{-- ════════════════════════════════════════════════════════════════
     HEADER — identik persis app-header dari admin.blade.php
     ════════════════════════════════════════════════════════════════ --}}
<div class="app-header" style="padding:0 12px;gap:10px;position:fixed;top:0;left:0;right:0;z-index:400;">

    <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
        <img src="{{ asset('sugity.png') }}" alt="Sugity Creatives"
             style="height:36px;width:auto;object-fit:contain;flex-shrink:0;"
             onerror="this.style.display='none'">
        <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
            <span style="font-family:'Orbitron',sans-serif;font-weight:900;font-size:22px;letter-spacing:2px;color:#f5a623;text-shadow:0 0 14px rgba(245,166,35,.5);white-space:nowrap;">HENKATEN BOARD</span>
            <span style="font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:14px;letter-spacing:1.2px;color:rgba(255,255,255,.85);text-transform:uppercase;white-space:nowrap;">{{ $factory }}</span>
        </div>
    </div>

    <span style="background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.3);border-radius:20px;padding:6px 16px;font-family:'Roboto Condensed',sans-serif;font-size:14px;font-weight:900;color:#fff;letter-spacing:.8px;flex-shrink:0;">SHIFT {{ $shift }}</span>

    <div class="header-clock" id="tvClock">00:00:00</div>

    <a href="{{ route('admin.dashboard') }}"
       style="background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);border-radius:8px;padding:6px 14px;color:rgba(255,255,255,.75);font-family:'Roboto Condensed',sans-serif;font-size:14px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:5px;flex-shrink:0;"
       onmouseover="this.style.background='rgba(255,255,255,.2)';this.style.color='#fff'"
       onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.75)'">
        ✕ Exit TV
    </a>
</div>

<div class="tv-badge">📺 TV MODE</div>
<div class="tv-paused" id="tvPaused">⏸ PAUSED</div>

{{-- ════════════════════════════════════════════════════════════════
     BODY SCROLL — semua konten identik persis @section('content') dashboard
     ════════════════════════════════════════════════════════════════ --}}
<div class="tv-body" id="tvBody">
<div class="tv-body-inner" id="tvInner">

    {{-- ── date-bar (read-only) ─────────────────────────────────── --}}
    <div class="date-bar">
        <div class="date-label">📅</div>
        <span style="font-family:'Roboto Condensed',sans-serif;font-size:16px;font-weight:700;color:#444;flex:1;">
            {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM YYYY') }}
        </span>
        <span style="background:var(--green-light,#8bc34a);color:#fff;padding:5px 14px;border-radius:20px;font-family:'Roboto Condensed',sans-serif;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;">
            🏭 {{ $factory === 'Factory 2' ? 'F2' : 'F3&4' }}
        </span>
    </div>

    {{-- ── shift-toggle-bar (identik, read-only) ───────────────── --}}
    <div class="shift-toggle-bar">
        <button class="shift-toggle-btn {{ $shift === 'A' ? 'active' : '' }}">SHIFT A</button>
        <button class="shift-toggle-btn {{ $shift === 'B' ? 'active' : '' }}">SHIFT B</button>
    </div>

    {{-- ── legend-4m (identik) ──────────────────────────────────── --}}
    <div class="legend-4m">
        <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--red)"></div>Man (Absen)</div>
        <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--navy)"></div>Machine</div>
        <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--yellow)"></div>Material</div>
        <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--green-light)"></div>Method</div>
    </div>

    {{-- ── status-panel (identik, tanpa ar-toggle) ─────────────── --}}
    @php
        $chipMap = ['chip-green','chip-yellowgreen','chip-yellow','chip-red'];
        $chipTxt = [
            '🟢 AMAN — Semua Normal',
            '🟡 PERHATIAN RINGAN — Absen 1',
            '⚠️ PERHATIAN KHUSUS — Absen 2–3 / Ada Problem',
            '🔴 BAHAYA — Absen ≥4 / MC Problem ≥2',
        ];
    @endphp
    <div class="status-panel">
        <div class="status-panel-title">📊 Status Keseluruhan</div>
        <div class="auto-refresh-bar">
            <div class="ar-left">
                <div class="ar-dot" id="arDot"></div>
                <span id="arLabel">Auto Refresh</span>
            </div>
            <span class="ar-countdown" id="arCountdown">30s</span>
        </div>
        <div class="ar-last-updated" id="arLastUpdated">Belum diperbarui</div>
        <div class="status-emot-row">
            <div class="emot-card {{ $statusLevel===0?'active-green':'' }}"      id="ec-green"> <span class="emot-icon">🟢</span><div class="emot-label">AMAN</div></div>
            <div class="emot-card {{ $statusLevel===1?'active-yellowgreen':'' }}" id="ec-yg">   <span class="emot-icon">🟡</span><div class="emot-label">RINGAN</div></div>
            <div class="emot-card {{ $statusLevel===2?'active-yellow':'' }}"     id="ec-yellow"><span class="emot-icon">⚠️</span><div class="emot-label">KHUSUS</div></div>
            <div class="emot-card {{ $statusLevel===3?'active-red':'' }}"        id="ec-red">   <span class="emot-icon">🔴</span><div class="emot-label">BAHAYA</div></div>
        </div>
        <div class="status-chip {{ $chipMap[$statusLevel] }}" id="statusChip">{{ $chipTxt[$statusLevel] }}</div>
        <div class="status-live-row">
            <div class="live-item"><div class="live-value" id="liveAbsen">{{ $absenceSummary?->total_absen ?? 0 }}</div><div class="live-label">Absen MP</div></div>
            <div class="live-item"><div class="live-value" id="liveMCProblem">{{ $machineSummary['problem'] }}</div><div class="live-label">MC Problem</div></div>
            <div class="live-item"><div class="live-value" id="liveLogOpen">{{ $openLogsCount }}</div><div class="live-label">Log Open</div></div>
        </div>
    </div>

    {{-- ── Report Summary (identik) ─────────────────────────────── --}}
    <div class="section-title">Report Summary</div>
    <div class="summary-grid">
        <div class="summary-card"><div class="s-val" id="sTotalMC">{{ $machineSummary['total'] }}</div><div class="s-lbl">Total MC</div></div>
        <div class="summary-card"><div class="s-val" id="sTotalMP">{{ $absenceSummary?->total_member ?? 0 }}</div><div class="s-lbl">Total MP</div></div>
        <div class="summary-card" style="border-bottom-color:var(--red)"><div class="s-val" id="sMan" style="color:var(--red)">{{ $machineSummary['man'] }}</div><div class="s-lbl">Man</div></div>
        <div class="summary-card" style="border-bottom-color:var(--navy)"><div class="s-val" id="sMachine" style="color:var(--navy)">{{ $machineSummary['machine'] }}</div><div class="s-lbl">Machine</div></div>
        <div class="summary-card" style="border-bottom-color:var(--yellow)"><div class="s-val" id="sMaterial" style="color:var(--yellow)">{{ $machineSummary['material'] }}</div><div class="s-lbl">Material</div></div>
        <div class="summary-card" style="border-bottom-color:var(--green-light)"><div class="s-val" id="sMethod" style="color:var(--green-light)">{{ $machineSummary['method'] }}</div><div class="s-lbl">Method</div></div>
    </div>

    {{-- ── Diagram Kehadiran (identik) ──────────────────────────── --}}
    <div class="section-title">Diagram Kehadiran</div>
    <div class="attendance-chart-wrap">
        <div class="chart-container">
            <canvas id="myChart"></canvas>
            <div class="chart-center">
                <div class="cv" id="centerValue">{{ $absenceSummary ? $absenceSummary->mp_hadir.'/'.$absenceSummary->total_member : '0/0' }}</div>
                <div class="cl">MP</div>
            </div>
        </div>
        <div class="legend-grid">
            <div class="legend-item"><span class="leg-dot" style="background:#729E3F"></span>MP Hadir</div>
            <div class="legend-item"><span class="leg-dot" style="background:#ff69b4"></span>OP Cuti</div>
            <div class="legend-item"><span class="leg-dot" style="background:#8e44ad"></span>OP Sakit</div>
            <div class="legend-item"><span class="leg-dot" style="background:#f1c40f"></span>OP Ijin</div>
            <div class="legend-item"><span class="leg-dot" style="background:#1F3C88"></span>Pengawas Cuti</div>
            <div class="legend-item"><span class="leg-dot" style="background:#5dade2"></span>Pengawas Sakit</div>
            <div class="legend-item"><span class="leg-dot" style="background:#FF8F1F"></span>Pengawas Ijin</div>
        </div>
        <div style="width:100%">
            @php $pct = ($absenceSummary && $absenceSummary->total_member > 0) ? round($absenceSummary->mp_hadir / $absenceSummary->total_member * 100, 1) : 0; @endphp
            <div class="absen-bar">
                <div class="absen-fill" id="absenFill" style="width:{{ $pct }}%">{{ $pct }}%</div>
            </div>
        </div>
    </div>

    {{-- ── Status Mesin legend + machines (identik persis dashboard) ── --}}
    <div class="section-title" style="margin-top:16px">Status Mesin</div>
    <div class="legend-4m" style="margin-bottom:10px">
        <div class="legend-4m-item"><div class="l4m-dot" style="background:#e74c3c"></div>Man</div>
        <div class="legend-4m-item"><div class="l4m-dot" style="background:#1f3c88"></div>Machine</div>
        <div class="legend-4m-item"><div class="l4m-dot" style="background:#f39c12"></div>Material</div>
        <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--green)"></div>Method</div>
        <div class="legend-4m-item" style="color:#607d8b;font-weight:700;font-size:14px">▨ = tugas di mesin lain</div>
    </div>

    <div class="machines-wrap">
        @php
            $absenIds = \App\Models\AbsenceRecord::where([
                'tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift,'status'=>'absen',
            ])->pluck('member_id')->toArray();

            try {
                $replacements = \App\Models\AssignmentReplacement::where([
                    'tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift,
                ])->get()->keyBy('member_id');
            } catch(\Throwable $e) { $replacements = collect(); }

            $replacedMachines = \App\Models\AssignmentReplacement::where([
                'tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift,
            ])->pluck('target_machine')->toArray();

            try {
                $replacementsAll = \App\Models\AssignmentReplacement::where([
                    'tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift,
                ])->get();
            } catch(\Throwable $e) { $replacementsAll = collect(); }

            $pipColors = ['man'=>'#e74c3c','machine'=>'#1f3c88','material'=>'#f39c12','method'=>'#2e7d32'];
            $pillDefs  = [
                'normal'   => ['dot'=>null,  'label'=>'Normal'],
                'man'      => ['dot'=>'dm',  'label'=>'Man'],
                'material' => ['dot'=>'dt',  'label'=>'Matl'],
                'machine'  => ['dot'=>'dc',  'label'=>'Mchn'],
                'method'   => ['dot'=>'dme', 'label'=>'Mthd'],
            ];
        @endphp

        @foreach($groups as $group)
        @php
            $absenMesinCount = 0;
            foreach($group['machines'] as $mac) {
                $asgn = $members->filter(fn($m)=>$m->mesin===$mac);
                if($asgn->whereIn('id',$absenIds)->isNotEmpty() && !in_array($mac,$replacedMachines)) $absenMesinCount++;
            }
        @endphp

        <div class="machine-group-section">
            <div class="machine-group-title">
                🔧 {{ $group['title'] }}
                @if($absenMesinCount > 0)
                    <span class="mg-badge warn">⚠ {{ $absenMesinCount }} absen</span>
                @else
                    <span class="mg-badge">{{ count($group['machines']) }} mesin</span>
                @endif
            </div>

            <div class="machine-cards-row">
                @foreach($group['machines'] as $machine)
                @php
                    $st    = $statuses[$machine] ?? null;
                    $stVal = $st?->status   ?? 'normal';
                    $stAll = $st?->statuses ?? [];

                    $assigned       = $members->filter(fn($m)=>$m->mesin===$machine);
                    $absenMemberIds = $assigned->whereIn('id',$absenIds)->pluck('id');
                    $machineHasRepl = in_array($machine,$replacedMachines);
                    $hasAbsen       = $absenMemberIds->isNotEmpty();
                    $needsFinder    = $hasAbsen && !$machineHasRepl;

                    $cardCls = $needsFinder
                        ? 'mc-has-absen'
                        : (!$hasAbsen && $stVal!=='normal' ? 'mc-status-'.$stVal : '');

                    $machineRecord = $machinePhotos[$machine] ?? null;
                    $machinePhoto  = $machineRecord?->photo_url ?? null;
                    $machineSlug   = Str::slug($machine);

                    $initPips = [];
                    if($hasAbsen) $initPips[] = 'man';
                    foreach($stAll as $s){ if($s!=='man' && !in_array($s,$initPips)) $initPips[]=$s; }

                    $dotInitClass = $hasAbsen ? 'd-absen' : ($stVal!=='normal' ? 'd-visible' : '');
                    $dotInitBg    = (!$hasAbsen && $stVal!=='normal') ? ($pipColors[$stVal]??'') : '';

                    $activePills = [];
                    if($hasAbsen) $activePills[] = 'man';
                    foreach($stAll as $s){ if(!in_array($s,$activePills)) $activePills[]=$s; }
                    if(empty($activePills)) $activePills[] = 'normal';
                @endphp

                <div class="mc-card {{ $cardCls }}" data-machine="{{ $machine }}" data-status="{{ $stVal }}">

                    <div class="mc-photo-wrap" id="photo-wrap-{{ $machineSlug }}">
                        @if($machinePhoto)
                            <img src="{{ $machinePhoto }}" alt="{{ $machine }}" loading="lazy" id="photo-img-{{ $machineSlug }}">
                        @else
                            <div class="mc-photo-placeholder" id="photo-img-{{ $machineSlug }}">
                                <div class="ph-ico">📷</div>
                                <div class="ph-txt">Foto Mesin</div>
                            </div>
                        @endif
                        <div class="mc-name-badge">
                            <span class="mc-name-txt">{{ $machine }}</span>
                            <div style="display:flex;align-items:center;gap:4px">
                                <div class="mc-4m-row" id="lights-{{ $machineSlug }}">
                                    @foreach($initPips as $pip)
                                        <div class="mc-4m-pip" style="background:{{ $pipColors[$pip]??'#ccc' }}" title="{{ $pip }}"></div>
                                    @endforeach
                                </div>
                                <div class="mc-dot {{ $dotInitClass }}" id="dot-{{ $machineSlug }}"
                                     @if($dotInitBg) style="background:{{ $dotInitBg }}" @endif></div>
                            </div>
                        </div>
                    </div>

                    <div class="mc-card-body">
                        <div class="mc-members-row">
                            @if($assigned->isEmpty())
                                <div class="mc-empty-slot">+</div>
                            @else
                                @foreach($assigned as $m)
                                @php
                                    $isAbsen     = in_array($m->id,$absenIds);
                                    $isDipinjam  = !$isAbsen && isset($replacements[$m->id]);
                                    $destMachine = $isDipinjam ? ($replacements[$m->id]->target_machine??'?') : null;
                                    $itemCls     = $isAbsen ? 'mi-absen' : ($isDipinjam ? 'mi-dipinjam' : '');
                                    $avCls       = $isAbsen ? 'av-absen' : 'av-ok';
                                @endphp
                                <div class="mc-member-item {{ $itemCls }}" data-member-id="{{ $m->id }}">
                                    <div class="mc-av {{ $avCls }}">
                                        @if($m->photo_url)
                                            <img src="{{ $m->photo_url }}" alt="{{ $m->nama }}"
                                                 @if($isAbsen) style="filter:grayscale(.5) brightness(.8)"
                                                 @elseif($isDipinjam) style="filter:grayscale(.55) brightness(.72)"
                                                 @endif>
                                        @else
                                            {{ mb_strtoupper(mb_substr($m->nama,0,1)).(str_contains($m->nama,' ')?mb_strtoupper(mb_substr(explode(' ',$m->nama)[1],0,1)):'') }}
                                        @endif
                                    </div>
                                    <div class="mc-member-name" title="{{ $m->nama }}">
                                        {{ $m->nama }}
                                    </div>
                                    @if($isAbsen)
                                        <span class="mc-member-tag tag-absen">Absen</span>
                                    @elseif($isDipinjam)
                                        <span class="mc-member-tag tag-dipinjam">Tugas Lain</span>
                                        <div class="mi-dipinjam-dest" title="Bertugas di: {{ $destMachine }}">↗ {{ Str::limit($destMachine,8) }}</div>
                                    @else
                                        <span class="mc-member-tag tag-hadir">Hadir</span>
                                    @endif
                                </div>
                                @endforeach

                                {{-- Pengganti yang ditugaskan ke mesin ini --}}
                                @foreach($replacementsAll->where('target_machine',$machine) as $repl)
                                    @php $replM = $members->firstWhere('id',$repl->member_id); @endphp
                                    @if($replM)
                                    @php $rpP = explode(' ',$replM->nama); @endphp
                                    <div class="mc-member-item" data-member-id="{{ $replM->id }}" data-replacement="1">
                                        <div class="mc-av av-repl">
                                            @if($replM->photo_url)<img src="{{ $replM->photo_url }}" alt="{{ $replM->nama }}">
                                            @else{{ mb_strtoupper(mb_substr($rpP[0],0,1)).(isset($rpP[1])?mb_strtoupper(mb_substr($rpP[1],0,1)):'') }}@endif
                                        </div>
                                        <div class="mc-member-name">{{ implode(' ', $rpP) }}</div>
                                        <span class="mc-member-tag tag-repl">Pengganti</span>
                                    </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        {{-- Pills: identik dashboard --}}
                        <div class="mc-status-row">
                            @foreach($pillDefs as $pKey=>$pDef)
                                <span class="mc-status-pill p-{{ $pKey }} {{ in_array($pKey,$activePills)?'active':'' }}" data-status="{{ $pKey }}">
                                    @if($pDef['dot'])<span class="pill-dot {{ $pDef['dot'] }}"></span>@endif
                                    {{ $pDef['label'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div style="height:16px"></div>
</div>{{-- /tv-body-inner --}}
</div>{{-- /tv-body --}}

{{-- ════════════════════════════════════════════════════════════════
     TICKER BAWAH
     ════════════════════════════════════════════════════════════════ --}}
<div class="tv-ticker">
    <div class="tv-ticker-lbl">📢 INFO</div>
    <div class="tv-ticker-track">
        <div class="tv-ticker-inner" id="tvTickerInner"></div>
    </div>
</div>

<button class="tv-fs" onclick="tvFS()">⛶ Fullscreen</button>

{{-- ════════════════════════════════════════════════════════════════
     JAVASCRIPT
     ════════════════════════════════════════════════════════════════ --}}
<script>
Chart.register(ChartDataLabels);

const TV_F   = @json($factory);
const TV_S   = @json($shift);
const TV_T   = @json($tanggal);
const initAb = @json($absenceSummary);

const LVL_CHIP = ['chip-green','chip-yellowgreen','chip-yellow','chip-red'];
const LVL_TXT  = ['🟢 AMAN — Semua Normal','🟡 PERHATIAN RINGAN — Absen 1','⚠️ PERHATIAN KHUSUS — Absen 2–3 / Ada Problem','🔴 BAHAYA — Absen ≥4 / MC Problem ≥2'];
const LVL_CLS  = ['active-green','active-yellowgreen','active-yellow','active-red'];

let tvChart = null;
const $g = id => document.getElementById(id);

/* ── JAM ────────────────────────────────────────────────────────── */
(()=>{
    const el = $g('tvClock'); const p = x=>String(x).padStart(2,'0');
    const t = ()=>{ const n=new Date(); if(el) el.textContent=p(n.getHours())+':'+p(n.getMinutes())+':'+p(n.getSeconds()); };
    t(); setInterval(t,1000);
})();

/* ── CHART — identik renderChart() dashboard ───────────────────── */
function renderChart(d){
    if(!d) return;
    const v=[d.mp_hadir??0,d.op_cuti??0,d.op_sakit??0,d.op_ijin??0,d.spv_cuti??0,d.spv_sakit??0,d.spv_ijin??0];
    const tot=d.total_member??v.reduce((a,b)=>a+b,0); if(!tot) return;
    const h=d.mp_hadir??0, pct=((h/tot)*100).toFixed(1);
    const cv=$g('centerValue'),af=$g('absenFill');
    if(cv) cv.textContent=`${h}/${tot}`;
    if(af){ af.style.width=pct+'%'; af.textContent=pct+'%'; }
    if(tvChart) tvChart.destroy();
    tvChart=new Chart($g('myChart'),{
        type:'doughnut',
        data:{labels:['MP Hadir','OP Cuti','OP Sakit','OP Ijin','Pengawas Cuti','Pengawas Sakit','Pengawas Ijin'],
              datasets:[{data:v,backgroundColor:['#729E3F','#ff69b4','#8e44ad','#f1c40f','#1F3C88','#5dade2','#FF8F1F'],borderWidth:0}]},
        options:{cutout:'75%',responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false},datalabels:{display:false},
                tooltip:{callbacks:{label:c=>{const vv=c.parsed;return vv?`${c.label}: ${vv} (${((vv/tot)*100).toFixed(1)}%)`:null;}}}}}
    });
}

/* ── AUTO SCROLL ───────────────────────────────────────────────── */
const tvBody  = $g('tvBody');
const tvInner = $g('tvInner');
const tvPBadge= $g('tvPaused');
let posY=0, paused=false, ptmr=null;
const SPD=0.55;

function raf(){
    if(!paused){
        const mx=tvInner.scrollHeight-tvBody.clientHeight;
        if(mx>0){
            if(posY>=mx){ paused=true; clearTimeout(ptmr); ptmr=setTimeout(()=>{posY=0;paused=false;},4500); }
            else posY=Math.min(posY+SPD,mx);
        }
        tvInner.style.transform=`translateY(-${posY}px)`;
    }
    requestAnimationFrame(raf);
}
raf();

tvBody.addEventListener('mouseenter',()=>{ paused=true;  tvPBadge.classList.add('show'); });
tvBody.addEventListener('mouseleave',()=>{ paused=false; tvPBadge.classList.remove('show'); });
tvBody.addEventListener('touchstart',()=>{ paused=true;  tvPBadge.classList.add('show'); },{passive:true});
tvBody.addEventListener('touchend',  ()=>{ tvPBadge.classList.remove('show'); clearTimeout(ptmr); ptmr=setTimeout(()=>{ paused=false; },2500); },{passive:true});
document.addEventListener('keydown',e=>{
    if(e.key===' '){ e.preventDefault(); paused=!paused; tvPBadge.classList.toggle('show',paused); }
    if(e.key==='f'||e.key==='F'||e.key==='F11'){ e.preventDefault(); tvFS(); }
});

/* ── FULLSCREEN ─────────────────────────────────────────────────── */
function tvFS(){
    if(!document.fullscreenElement)(document.documentElement.requestFullscreen||document.documentElement.webkitRequestFullscreen)?.call(document.documentElement);
    else(document.exitFullscreen||document.webkitExitFullscreen)?.call(document);
}

/* ── SYNC — identik syncSummary() dashboard ────────────────────── */
let _cd=30, _cdI=null;
function startCD(){
    _cd=30; clearInterval(_cdI);
    _cdI=setInterval(()=>{ _cd--; if($g('arCountdown'))$g('arCountdown').textContent=_cd+'s'; if(_cd<=0){_cd=30;syncTV();} },1000);
}

async function syncTV(){
    const dot=$g('arDot'); if(dot) dot.style.opacity='.5';
    try{
        const r=await fetch(`/admin/status?tanggal=${TV_T}&factory=${encodeURIComponent(TV_F)}&shift=${TV_S}`,{headers:{Accept:'application/json'}});
        if(!r.ok) return; const d=await r.json();

        // Live counters — identik dashboard syncSummary()
        if($g('liveAbsen'))     $g('liveAbsen').textContent     = d.total_absen??0;
        if($g('liveMCProblem')) $g('liveMCProblem').textContent = d.problem_mc??0;
        if($g('liveLogOpen'))   $g('liveLogOpen').textContent   = d.open_logs??0;
        if($g('sTotalMC'))      $g('sTotalMC').textContent      = d.summary?.total??0;
        if($g('sTotalMP'))      $g('sTotalMP').textContent      = d.total_mp??0;
        if($g('sMan'))          $g('sMan').textContent          = d.summary?.man??0;
        if($g('sMachine'))      $g('sMachine').textContent      = d.summary?.machine??0;
        if($g('sMaterial'))     $g('sMaterial').textContent     = d.summary?.material??0;
        if($g('sMethod'))       $g('sMethod').textContent       = d.summary?.method??0;

        // Status level
        const lv=d.status_level??0;
        ['ec-green','ec-yg','ec-yellow','ec-red'].forEach((id,i)=>{
            const e=$g(id); if(!e) return; e.classList.remove(...LVL_CLS); if(i===lv) e.classList.add(LVL_CLS[i]);
        });
        const chip=$g('statusChip');
        if(chip){ chip.className=`status-chip ${LVL_CHIP[lv]}`; chip.textContent=LVL_TXT[lv]; }

        if(d.absence) renderChart(d.absence);
        const ts=$g('arLastUpdated'); if(ts) ts.textContent=`Diperbarui: ${d.updated_at}`;
        buildTicker(d); _cd=30;
        // Sync cards setelah summary selesai
        syncCards();
    }catch(e){console.error('[TV]',e);}
    finally{ if(dot) dot.style.opacity='1'; }
}

/* ── SYNC MACHINE CARDS ─────────────────────────────────────────── */
const colorMap  = { man:'#e74c3c', machine:'#1f3c88', material:'#f39c12', method:'#2e7d32' };
const borderCls = { man:'mc-status-man', machine:'mc-status-machine', material:'mc-status-material', method:'mc-status-method' };

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
function initials(n){ if(!n)return'?'; const p=n.trim().split(' '); return p.length>=2?(p[0][0]+p[1][0]).toUpperCase():n.slice(0,2).toUpperCase(); }

let _tvSyncing = false;
async function syncCards() {
    if (_tvSyncing) return;
    _tvSyncing = true;
    try {
        const qs = `tanggal=${TV_T}&factory=${encodeURIComponent(TV_F)}&shift=${TV_S}`;
        const [absRes, replRes, logRes] = await Promise.all([
            fetch(`/admin/absence/data?${qs}`,  { headers:{ Accept:'application/json' } }),
            fetch(`/admin/replacements?${qs}`,  { headers:{ Accept:'application/json' } }),
            fetch(`/admin/logs/list?${qs}`,     { headers:{ Accept:'application/json' } }),
        ]);
        const absData  = absRes.ok  ? await absRes.json()  : {};
        const replData = replRes.ok ? await replRes.json() : [];
        const logData  = logRes.ok  ? await logRes.json()  : [];

        // Build lookup maps
        const absenIds         = new Set(Object.entries(absData).filter(([,v])=>v.status==='absen').map(([id])=>parseInt(id)));
        const activReplIds     = new Set(replData.map(r=>r.member_id));
        const replacedMachines = new Set(replData.map(r=>r.target_machine));
        const openLogMap = {};
        logData.filter(l=>l.status==='open').forEach(l=>{
            if(!openLogMap[l.lokasi]) openLogMap[l.lokasi]=[];
            const t=(l.jenis||'').toLowerCase();
            if(t && !openLogMap[l.lokasi].includes(t)) openLogMap[l.lokasi].push(t);
        });

        document.querySelectorAll('.mc-card').forEach(card => {
            const machine = card.dataset.machine; if(!machine) return;

            const ownItems   = [...card.querySelectorAll('.mc-member-item:not([data-replacement="1"])')];
            const absenItems = ownItems.filter(el => absenIds.has(parseInt(el.dataset.memberId)));
            const cardHasAbsen   = absenItems.length > 0;
            const hasReplacement = card.querySelectorAll('.mc-member-item[data-replacement="1"]').length > 0
                                   || replacedMachines.has(machine);
            const cardNeedsFinder = cardHasAbsen && !hasReplacement;

            // Remove stale replacement cards if no longer absent
            if (!cardHasAbsen) card.querySelectorAll('.mc-member-item[data-replacement="1"]').forEach(el=>el.remove());

            // Inject new replacement cards
            if (cardHasAbsen) {
                const row  = card.querySelector('.mc-members-row');
                const repl = replData.filter(r=>r.target_machine===machine);
                repl.forEach(r => {
                    if (!row) return;
                    if (row.querySelector(`.mc-member-item[data-member-id="${r.member_id}"][data-replacement="1"]`)) return;
                    row.querySelector('.mc-empty-slot')?.remove();
                    const div = document.createElement('div');
                    div.className='mc-member-item'; div.dataset.memberId=r.member_id; div.dataset.replacement='1';
                    const av = r.member_photo ? `<img src="${esc(r.member_photo)}" alt="${esc(r.member_name)}">` : `<span>${initials(r.member_name)}</span>`;
                    div.innerHTML = `<div class="mc-av av-repl">${av}</div>`
                        + `<div class="mc-member-name">${esc(r.member_name||'')}</div>`
                        + `<span class="mc-member-tag tag-repl">Pengganti</span>`;
                    row.appendChild(div);
                });
            }

            // Update member status tags
            ownItems.forEach(item => {
                const mid = parseInt(item.dataset.memberId); if(!mid) return;
                const isAbsen   = absenIds.has(mid);
                const isDipinjam= !isAbsen && activReplIds.has(mid);
                const av  = item.querySelector('.mc-av');
                const img = item.querySelector('.mc-av img');
                const tag = item.querySelector('.mc-member-tag');
                if (isAbsen) {
                    item.classList.add('mi-absen'); item.classList.remove('mi-dipinjam');
                    av?.classList.replace('av-ok','av-absen');
                    if(img) img.style.filter='grayscale(.5) brightness(.8)';
                    if(tag){ tag.className='mc-member-tag tag-absen'; tag.textContent='Absen'; }
                    item.querySelector('.mi-dipinjam-dest')?.remove();
                } else if (isDipinjam) {
                    const dest = replData.find(r=>r.member_id===mid)?.target_machine||'';
                    item.classList.add('mi-dipinjam'); item.classList.remove('mi-absen');
                    av?.classList.replace('av-absen','av-ok');
                    if(img) img.style.filter='grayscale(.55) brightness(.72)';
                    if(tag){ tag.className='mc-member-tag tag-dipinjam'; tag.textContent='Tugas Lain'; }
                    let d=item.querySelector('.mi-dipinjam-dest');
                    if(!d){ d=document.createElement('div'); d.className='mi-dipinjam-dest'; item.appendChild(d); }
                    if(dest){ d.textContent=`↗ ${dest}`; d.title=`Bertugas di: ${dest}`; }
                } else {
                    item.classList.remove('mi-absen','mi-dipinjam');
                    av?.classList.replace('av-absen','av-ok');
                    if(img) img.style.filter='';
                    item.querySelector('.mi-dipinjam-dest')?.remove();
                    if(tag&&['Absen','Tugas Lain'].includes(tag.textContent)){ tag.className='mc-member-tag tag-hadir'; tag.textContent='Hadir'; }
                }
            });

            // Update card border & dot
            const logTypes = openLogMap[machine] || [];
            card.classList.remove('mc-has-absen', ...Object.values(borderCls));
            if (cardNeedsFinder) card.classList.add('mc-has-absen');
            else if (!cardHasAbsen && logTypes.length) {
                const p = logTypes[0];
                if(borderCls[p]) card.classList.add(borderCls[p]);
            }

            const dot = card.querySelector('.mc-dot'); if(!dot) return;
            if (cardHasAbsen && cardNeedsFinder) {
                dot.className='mc-dot d-absen'; dot.style.background=''; dot.onclick=null;
            } else if (cardHasAbsen && !cardNeedsFinder) {
                dot.className='mc-dot'; dot.style.background=''; dot.onclick=null;
            } else if (logTypes.length && colorMap[logTypes[0]]) {
                dot.className='mc-dot d-visible'; dot.style.background=colorMap[logTypes[0]];
            } else {
                dot.className='mc-dot'; dot.style.background='';
            }

            // Update status pills
            const activePillCls = [];
            if(cardHasAbsen)                   activePillCls.push('p-man');
            if(logTypes.includes('machine'))   activePillCls.push('p-machine');
            if(logTypes.includes('material'))  activePillCls.push('p-material');
            if(logTypes.includes('method'))    activePillCls.push('p-method');
            if(!activePillCls.length)          activePillCls.push('p-normal');
            card.querySelectorAll('.mc-status-pill').forEach(p=>p.classList.remove('active'));
            activePillCls.forEach(cls=>card.querySelector(`.mc-status-pill.${cls}`)?.classList.add('active'));

            // Update group badge warning count
            const section = card.closest('.machine-group-section');
            if (section) {
                const badge = section.querySelector('.mg-badge');
                if (badge) {
                    const allCards = [...section.querySelectorAll('.mc-card')];
                    const warnCount = allCards.filter(c=>c.classList.contains('mc-has-absen')).length;
                    if(warnCount > 0){
                        badge.className='mg-badge warn'; badge.textContent=`⚠ ${warnCount} absen`;
                    } else {
                        badge.className='mg-badge'; badge.textContent=`${allCards.length} mesin`;
                    }
                }
            }
        });
    } catch(e){ console.error('[TV syncCards]', e); }
    finally { _tvSyncing = false; }
}

/* ── TICKER ─────────────────────────────────────────────────────── */
function buildTicker(d){
    const items=[]; const ab=d.absence??{}; const s=d.summary??{};
    if((ab.total_absen??0)>0) items.push({dot:'#e74c3c',txt:`⚠ ABSEN: ${ab.total_absen} orang`});
    if((ab.op_cuti??0)>0)     items.push({dot:'#ff69b4',txt:`Cuti: ${ab.op_cuti}`});
    if((ab.op_sakit??0)>0)    items.push({dot:'#8e44ad',txt:`Sakit: ${ab.op_sakit}`});
    if((ab.op_ijin??0)>0)     items.push({dot:'#f1c40f',txt:`Ijin: ${ab.op_ijin}`});
    if((s.man??0)>0)          items.push({dot:'#e74c3c',txt:`MC Absen: ${s.man} mesin`});
    if((s.machine??0)>0)      items.push({dot:'#1f3c88',txt:`Machine Problem: ${s.machine}`});
    if((s.material??0)>0)     items.push({dot:'#f39c12',txt:`Material Problem: ${s.material}`});
    if((s.method??0)>0)       items.push({dot:'#2e7d32',txt:`Method Problem: ${s.method}`});
    if((d.open_logs??0)>0)    items.push({dot:'#5dade2',txt:`Log Open: ${d.open_logs}`});
    if(!items.length)          items.push({dot:'#4caf50',txt:'✅ Semua kondisi NORMAL — Tidak ada masalah hari ini'});

    const sep=`<span style="color:rgba(255,255,255,.3);margin:0 16px">◆</span>`;
    const html=items.map(i=>`<span><span class="tick-dot" style="background:${i.dot}"></span>${i.txt}</span>${sep}`).join('');
    const el=$g('tvTickerInner'); if(!el) return;
    el.innerHTML=html+html;
    el.style.animationDuration=Math.max(20,(el.scrollWidth/2)/70)+'s';
}

/* ── INIT ───────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded',()=>{
    if(initAb) renderChart(initAb);
    buildTicker({absence:initAb,summary:@json($machineSummary),open_logs:{{ $openLogsCount }},status_level:{{ $statusLevel }}});
    syncTV(); startCD();
    syncCards(); // initial card sync
    document.addEventListener('visibilitychange',()=>{ if(!document.hidden){ syncTV(); syncCards(); } });
});
</script>
</body>
</html>