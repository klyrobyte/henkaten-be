@extends('layouts.admin')
@section('title', 'Mesin')

@push('styles')
<style>
/* ══ MACHINE PAGE ════════════════════════════════════════════════ */
.machines-wrap { padding: 10px 12px 110px; }

/* ── Group section ───────────────────────────────────────────── */
.machine-group-section { margin-bottom: 20px; }

.machine-group-title {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, var(--navy), #2c4a9e);
    color: #fff; padding: 6px 16px 6px 12px;
    border-radius: 0 20px 20px 0;
    font-family: 'Orbitron', sans-serif; font-size: 11px;
    font-weight: 700; letter-spacing: .8px;
    margin-bottom: 12px; margin-left: -12px;
    box-shadow: 2px 2px 8px rgba(31,60,136,.25);
}
.mg-badge {
    background: rgba(255,255,255,.2); padding: 2px 8px;
    border-radius: 8px; font-size: 10px; font-weight: 600;
    font-family: 'Roboto Condensed', sans-serif;
}
.mg-badge.warn { background: rgba(231,76,60,.75); }

/* ── Card grid ───────────────────────────────────────────────── */
.machine-cards-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(168px, 1fr));
    gap: 12px;
}

/* ══ MACHINE CARD ════════════════════════════════════════════════ */
.mc-card {
    background: #fff;
    border-radius: 14px;
    border: 2px solid #e0ecd4;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    overflow: hidden;
    display: flex; flex-direction: column;
    cursor: pointer;
    transition: box-shadow .2s, border-color .2s, transform .15s;
    position: relative;
}
.mc-card:active { transform: scale(.975); }

.mc-card.mc-status-man      { border-color: #e74c3c; box-shadow: 0 3px 14px rgba(231,76,60,.22); }
.mc-card.mc-status-machine  { border-color: #1f3c88; box-shadow: 0 3px 14px rgba(31,60,136,.22); }
.mc-card.mc-status-material { border-color: #f39c12; box-shadow: 0 3px 14px rgba(243,156,18,.22); }
.mc-card.mc-status-method   { border-color: #2e7d32; box-shadow: 0 3px 14px rgba(46,125,50,.22); }
.mc-card.mc-has-absen       { border-color: #e74c3c; box-shadow: 0 3px 18px rgba(231,76,60,.3); }

/* ── Machine photo area ──────────────────────────────────────── */
.mc-photo-wrap {
    position: relative;
    width: 100%; aspect-ratio: 16/9;
    background: linear-gradient(135deg, #eef2e8, #dde8c8);
    overflow: hidden;
    border-bottom: 1px solid #e0ecd4;
}
.mc-card.mc-has-absen  .mc-photo-wrap,
.mc-card.mc-status-man .mc-photo-wrap {
    background: linear-gradient(135deg,#fdeaea,#fad0d0);
    border-bottom-color: #f4a8a8;
}
.mc-photo-wrap img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .3s;
}
.mc-card:hover .mc-photo-wrap img { transform: scale(1.04); }

.mc-photo-placeholder {
    width: 100%; height: 100%;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 4px;
}
.mc-photo-placeholder .ph-ico { font-size: 28px; opacity: .25; }
.mc-photo-placeholder .ph-txt {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 9px; font-weight: 700; letter-spacing: .5px;
    text-transform: uppercase; color: #aaa;
}

/* ── Upload overlay (hover) ──────────────────────────────────── */
.mc-photo-upload-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.0);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 3px;
    cursor: pointer; transition: background .2s;
    z-index: 2;
    /* Biarkan klik menembus ke elemen di bawah saat tidak hover */
    pointer-events: none;
}
/* Aktifkan pointer-events hanya saat hover wrap */
.mc-photo-wrap:hover .mc-photo-upload-overlay {
    background: rgba(0,0,0,.45);
    pointer-events: all;
}
.mc-photo-upload-overlay .upload-ico {
    font-size: 20px; opacity: 0; transform: translateY(4px);
    transition: opacity .2s, transform .2s;
    pointer-events: none;
}
.mc-photo-upload-overlay .upload-txt {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 10px; font-weight: 800; color: #fff;
    text-transform: uppercase; letter-spacing: .5px;
    opacity: 0; transform: translateY(4px);
    transition: opacity .2s .05s, transform .2s .05s;
    pointer-events: none;
}
.mc-photo-wrap:hover .upload-ico,
.mc-photo-wrap:hover .upload-txt {
    opacity: 1; transform: translateY(0);
}
/* Loading state */
.mc-photo-wrap.uploading .mc-photo-upload-overlay {
    background: rgba(0,0,0,.55);
    pointer-events: none;
}
.mc-photo-wrap.uploading .upload-ico  { opacity: 1; transform: none; }
.mc-photo-wrap.uploading .upload-txt  { opacity: 1; transform: none; }

/* Dot harus selalu di atas overlay */

/* Name + status overlay on photo */
.mc-name-badge {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: linear-gradient(to top, rgba(0,0,0,.6) 0%, transparent 100%);
    padding: 20px 8px 6px;
    display: flex; align-items: flex-end; justify-content: space-between;
    z-index: 3;           /* di atas upload overlay (z-index:2) */
    pointer-events: none; /* klik tembus kecuali pada elemen interaktif */
}
.mc-name-badge .mc-dot,
.mc-name-badge .mc-4m-row {
    pointer-events: all;  /* dot & pips tetap klikable */
}
.mc-name-txt {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 11px; font-weight: 800; color: #fff;
    text-transform: uppercase; letter-spacing: .4px;
    text-shadow: 0 1px 3px rgba(0,0,0,.5);
    line-height: 1;
}

/* Status dot — hidden by default */
.mc-dot {
    width: 10px; height: 10px; border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,.6);
    display: none; flex-shrink: 0;
    transition: transform .15s;
}
.mc-dot.d-visible { display: block; }
.mc-dot.d-absen {
    display: block; background: #e74c3c;
    cursor: pointer; pointer-events: all;
    box-shadow: 0 0 0 3px rgba(231,76,60,.4);
    animation: abpulse 1.3s ease-in-out infinite;
}
.mc-dot.d-absen:hover  { transform: scale(1.8); }
@keyframes abpulse {
    0%,100% { box-shadow: 0 0 0 2px rgba(231,76,60,.5); }
    50%      { box-shadow: 0 0 0 7px rgba(231,76,60,.0); }
}

/* 4M pips in overlay */
.mc-4m-row { display: flex; gap: 3px; align-items: center; }
.mc-4m-pip { width: 7px; height: 7px; border-radius: 50%; border: 1px solid rgba(255,255,255,.3); }

/* ── Card body ───────────────────────────────────────────────── */
.mc-card-body { padding: 8px 8px 10px; display: flex; flex-direction: column; gap: 6px; }

/* ── Member row ──────────────────────────────────────────────── */
.mc-members-row {
    display: flex; flex-wrap: wrap; gap: 6px;
    justify-content: center; min-height: 54px;
}
.mc-member-item {
    display: flex; flex-direction: column; align-items: center; gap: 3px;
    min-width: 46px;
}
.mc-av {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 900; color: #fff;
    overflow: hidden; border: 2.5px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
    font-family: 'Roboto Condensed', sans-serif; flex-shrink: 0;
}
.mc-av img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.av-ok    { background: linear-gradient(135deg, #8bc34a, var(--green)); }
.av-absen { background: linear-gradient(135deg, #ef9a9a, #c0392b); filter: grayscale(.3); }
.mc-member-name {
    font-family: 'Roboto Condensed', sans-serif; font-size: 9px; font-weight: 800;
    text-align: center; color: #555; line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 52px; width: 100%;
}
.mi-absen .mc-member-name { color: #c0392b; }
.mc-member-tag {
    font-size: 7px; font-family: 'Roboto Condensed', sans-serif; font-weight: 800;
    padding: 1px 4px; border-radius: 3px; text-transform: uppercase;
}
.tag-hadir    { background: var(--green); color: #fff; }
.tag-absen    { background: #e74c3c;    color: #fff; }
.tag-dipinjam { background: #607d8b;    color: #fff; }

/* ── Member sedang bertugas di mesin LAIN ────────────────────── */
.mi-dipinjam { opacity: .72; }

.mi-dipinjam .mc-av {
    filter: grayscale(.55) brightness(.75);
    border-color: #b0bec5;
    box-shadow: none;
    position: relative;
}

/* hatching overlay via pseudo-element on the item */
.mi-dipinjam {
    position: relative;
    border-color: #b0c4ce !important;
    background: repeating-linear-gradient(
        -45deg,
        transparent,
        transparent 5px,
        rgba(96,125,139,.08) 5px,
        rgba(96,125,139,.08) 6px
    ) !important;
}

.mi-dipinjam .mc-member-name { color: #78909c; }

/* Arrow icon badge — shows which machine they went to */
.mi-dipinjam-dest {
    font-size: 8px; font-family: 'Roboto Condensed', sans-serif; font-weight: 700;
    color: #546e7a; text-align: center; line-height: 1.3;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 52px;
}

.mc-empty-slot {
    display: flex; align-items: center; justify-content: center;
    border: 2px dashed #d0ddc0; border-radius: 10px;
    min-height: 54px; color: #ccc; font-size: 22px; width: 100%;
}

/* ── STATUS PILLS ────────────────────────────────────────────── */
.mc-status-row {
    display: flex; gap: 3px; flex-wrap: wrap;
    justify-content: center; padding-top: 5px;
    border-top: 1px solid #eef2e8;
}
.mc-status-pill {
    display: flex; align-items: center; gap: 3px;
    padding: 4px 7px; border-radius: 20px;
    border: 1.5px solid #e4e4e4; background: #f7f7f7;
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 9px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .3px; color: #aaa; cursor: pointer;
    transition: all .15s; white-space: nowrap; line-height: 1;
}
.mc-status-pill:hover { border-color: #bbb; color: #777; transform: scale(1.06); }
.pill-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

/* Active pill states */
.mc-status-pill.p-normal.active   { background:#e8f5e9; border-color:var(--green); color:var(--green); }
.mc-status-pill.p-man.active      { background:#fdeaea; border-color:#e74c3c;      color:#e74c3c; }
.mc-status-pill.p-material.active { background:#fff8e1; border-color:#f39c12;      color:#c67c00; }
.mc-status-pill.p-machine.active  { background:#e8eefa; border-color:#1f3c88;      color:#1f3c88; }
.mc-status-pill.p-method.active   { background:#e8f5e9; border-color:#2e7d32;      color:#2e7d32; }

.pill-dot.dn  { background: #4caf50; }
.pill-dot.dm  { background: #e74c3c; }
.pill-dot.dt  { background: #f39c12; }
.pill-dot.dc  { background: #1f3c88; }
.pill-dot.dme { background: #2e7d32; }

/* ── Add Log bar ─────────────────────────────────────────────── */
.mc-addlog-bar {
    display: flex; justify-content: center;
    padding: 4px 0 2px;
}
.mc-addlog-btn {
    display: flex; align-items: center; gap: 3px;
    padding: 3px 12px; border-radius: 20px;
    border: 1.5px dashed #ccc; background: transparent;
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 9px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .4px; color: #bbb; cursor: pointer;
    transition: all .15s;
}
.mc-addlog-btn:hover {
    border-color: var(--orange, #e65100);
    color: var(--orange, #e65100);
    background: rgba(230, 81, 0, .06);
    transform: scale(1.05);
}

/* ── Quick Log modal header ──────────────────────────────────── */
.ql-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px 12px;
    border-bottom: 1.5px solid #f0f0f0;
}
.ql-header-left {
    display: flex; align-items: center; gap: 10px;
}
.ql-header-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: linear-gradient(135deg, var(--orange,#e65100), #ff7043);
    color: #fff; font-size: 20px; font-weight: 900;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; box-shadow: 0 3px 8px rgba(230,81,0,.3);
}
.ql-header-title {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 14px; font-weight: 800; color: #222;
    text-transform: uppercase; letter-spacing: .4px;
}
.ql-header-sub {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 11px; font-weight: 700; color: var(--navy,#1f3c88);
    background: #eef1fa; padding: 2px 8px; border-radius: 6px;
    display: inline-block; margin-top: 3px;
}

/* ══ FINDER POPUP MODAL ═════════════════════════════════════════ */
.finder-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.55);
    z-index: 1100; display: none; align-items: flex-end;
    justify-content: center;
}
.finder-overlay.show { display: flex; }

.finder-sheet {
    background: #fff; border-radius: 20px 20px 0 0;
    width: 100%; max-width: 560px; max-height: 88vh;
    display: flex; flex-direction: column;
    box-shadow: 0 -4px 30px rgba(0,0,0,.2);
    animation: slideUp .25s ease-out;
}
@keyframes slideUp {
    from { transform: translateY(60px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
.finder-handle {
    width: 44px; height: 5px; background: #ddd;
    border-radius: 3px; margin: 12px auto 0; flex-shrink: 0;
}
.finder-ph {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    padding: 14px 18px 16px; color: #fff; flex-shrink: 0; position: relative;
}
.finder-ph h2 {
    font-family: 'Orbitron', sans-serif; font-size: 13px;
    font-weight: 900; margin: 0 0 2px;
}
.finder-ph p { font-size: 11px; opacity: .85; margin: 0; }
.finder-ph-machine {
    background: rgba(255,255,255,.2); border-radius: 6px;
    padding: 3px 10px; margin-top: 8px;
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 12px; font-weight: 700; display: inline-block;
}
.finder-close-btn {
    position: absolute; top: 14px; right: 16px;
    background: rgba(255,255,255,.25); border: none; color: #fff;
    font-size: 18px; width: 32px; height: 32px; border-radius: 50%;
    cursor: pointer; display: flex; align-items: center;
    justify-content: center; font-weight: 700;
}
.finder-search-wrap {
    padding: 12px 14px; border-bottom: 1px solid #eee; flex-shrink: 0;
}
.finder-search-wrap input {
    width: 100%; padding: 9px 12px; border: 2px solid var(--green-light);
    border-radius: 10px; font-size: 13px; box-sizing: border-box;
    font-family: 'Roboto Condensed', sans-serif;
}
.finder-search-wrap input:focus { outline: none; border-color: var(--green); }
.finder-cand-list {
    overflow-y: auto; padding: 10px;
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 8px; flex: 1;
}
.finder-cand-list::-webkit-scrollbar { width: 4px; }
.finder-cand-list::-webkit-scrollbar-thumb { background: var(--green); border-radius: 2px; }
.cand-card {
    display: flex; flex-direction: column; align-items: center; gap: 4px;
    padding: 10px 6px 8px; border-radius: 10px;
    border: 2px solid var(--green-light); background: #f8faf5;
    cursor: pointer; transition: all .15s; text-align: center;
}
.cand-card:active { border-color: var(--orange); transform: scale(.96); }
.cand-av {
    width: 42px; height: 42px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 900; color: #fff; overflow: hidden;
    border: 2px solid var(--green); font-family: 'Roboto Condensed', sans-serif;
}
.cand-av img { width: 100%; height: 100%; object-fit: cover; }
.cav-n { background: linear-gradient(135deg, var(--green-light), var(--green)); }
.cav-w { background: linear-gradient(135deg, #f39c12, #e67e22); border-color: #f39c12; }
.cand-name { font-family: 'Roboto Condensed', sans-serif; font-size: 10px; font-weight: 800; line-height: 1.2; }
.cand-tag {
    font-size: 8px; font-family: 'Roboto Condensed', sans-serif; font-weight: 700;
    padding: 1px 4px; border-radius: 3px; border: 1px solid #f39c12;
    color: #f39c12; background: rgba(243,156,18,.1);
}
.cand-btn {
    width: 100%; padding: 5px 0; border-radius: 6px; border: none;
    background: var(--orange); color: #fff;
    font-family: 'Roboto Condensed', sans-serif;
    font-weight: 900; font-size: 10px; cursor: pointer;
}
.cand-empty {
    text-align: center; padding: 24px 10px;
    color: #aaa; grid-column: 1/-1; font-size: 12px;
}
</style>
@endpush

@section('content')

<div class="date-bar">
    <div class="date-label">📅</div>
    <input type="date" id="tanggalHari" value="{{ $tanggal }}"
           onchange="window.location.href=updateQS('tanggal',this.value)">
    <button class="factory-select-btn" onclick="showFactoryPicker()">🏭</button>
</div>
<div class="shift-toggle-bar">
    <button class="shift-toggle-btn {{ $shift==='A'?'active':'' }}" onclick="switchShift('A')">SHIFT A</button>
    <button class="shift-toggle-btn {{ $shift==='B'?'active':'' }}" onclick="switchShift('B')">SHIFT B</button>
</div>

<div class="legend-4m" style="margin-bottom:10px">
    <div class="legend-4m-item"><div class="l4m-dot" style="background:#e74c3c"></div>Man</div>
    <div class="legend-4m-item"><div class="l4m-dot" style="background:#1f3c88"></div>Machine</div>
    <div class="legend-4m-item"><div class="l4m-dot" style="background:#f39c12"></div>Material</div>
    <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--green)"></div>Method</div>
    <div class="legend-4m-item" style="color:#e74c3c;font-weight:700;font-size:11px">● = ada absen (klik)</div>
    <div class="legend-4m-item" style="color:#607d8b;font-weight:700;font-size:11px">▨ = tugas di mesin lain</div>
</div>

<div class="machines-wrap">
    @php
        // Query sekali untuk semua grup — lebih efisien
        $absenIds = \App\Models\AbsenceRecord::where([
            'tanggal' => $tanggal, 'factory' => $factory,
            'shift'   => $shift,   'status'  => 'absen',
        ])->pluck('member_id')->toArray();

        // Member yang sedang jadi pengganti di mesin lain hari ini
        // Model: AssignmentReplacement { member_id, target_machine, tanggal, factory, shift }
        // Sesuaikan nama model/field dengan implementasi Anda
        try {
            $replacements = \App\Models\AssignmentReplacement::where([
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift'   => $shift,
            ])->get()->keyBy('member_id');
        } catch (\Throwable $e) {
            $replacements = collect(); // fallback: kosong jika model/tabel belum ada
        }
    @endphp

    @foreach($groups as $group)
    @php
        $absenMesinCount = 0;
        foreach($group['machines'] as $mac) {
            $assigned = $members->filter(fn($m) => $m->mesin === $mac);
            if ($assigned->whereIn('id', $absenIds)->isNotEmpty()) $absenMesinCount++;
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
                    $st         = $statuses[$machine] ?? null;
                    $stVal      = $st?->status ?? 'normal';
                    $assigned   = $members->filter(fn($m) => $m->mesin === $machine);
                    $hasAbsen   = $assigned->whereIn('id', $absenIds)->isNotEmpty();
                    $cardCls    = $hasAbsen ? 'mc-has-absen' : ($stVal !== 'normal' ? 'mc-status-'.$stVal : '');
                    // Foto dari tabel machines (permanen, tidak tergantung shift/tanggal)
                    $machineRecord = $machinePhotos[$machine] ?? null;
                    $machinePhoto  = $machineRecord?->photo_url ?? null;
                    $machineSlug   = Str::slug($machine);
                @endphp

                <div class="mc-card {{ $cardCls }}"
                     data-machine="{{ $machine }}"
                     data-status="{{ $stVal }}"
                     onclick="handleCardClick(event,'{{ addslashes($machine) }}','{{ $stVal }}')">

                    {{-- ── Photo area ── --}}
                    <div class="mc-photo-wrap" id="photo-wrap-{{ $machineSlug }}">
                        @if($machinePhoto)
                            <img src="{{ $machinePhoto }}" alt="{{ $machine }}" loading="lazy"
                                 id="photo-img-{{ $machineSlug }}">
                        @else
                            <div class="mc-photo-placeholder" id="photo-img-{{ $machineSlug }}">
                                <div class="ph-ico">📷</div>
                                <div class="ph-txt">Foto Mesin</div>
                            </div>
                        @endif

                        {{-- Upload overlay — muncul saat hover foto --}}
                        <div class="mc-photo-upload-overlay"
                             onclick="event.stopPropagation(); triggerPhotoUpload('{{ addslashes($machine) }}','{{ $machineSlug }}')"
                             title="Ganti foto mesin">
                            <span class="upload-ico">📷</span>
                            <span class="upload-txt">{{ $machinePhoto ? 'Ganti Foto' : 'Upload Foto' }}</span>
                        </div>

                        {{-- Hidden file input per mesin --}}
                        <input type="file" accept="image/*"
                               id="file-{{ $machineSlug }}"
                               style="display:none"
                               onchange="uploadMachinePhoto(event,'{{ addslashes($machine) }}','{{ $machineSlug }}')">

                        <div class="mc-name-badge">
                            <span class="mc-name-txt">{{ $machine }}</span>
                            <div style="display:flex;align-items:center;gap:4px">
                                <div class="mc-4m-row" id="lights-{{ $machineSlug }}"></div>
                                <div class="mc-dot {{ $hasAbsen ? 'd-absen' : '' }}"
                                     id="dot-{{ $machineSlug }}"
                                     @if($hasAbsen)
                                         onclick="event.stopPropagation();openFinderModal('{{ addslashes($machine) }}')"
                                         title="Klik untuk cari pengganti"
                                     @endif></div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Body ── --}}
                    <div class="mc-card-body">

                        {{-- Members --}}
                        <div class="mc-members-row">
                            @if($assigned->isEmpty())
                                <div class="mc-empty-slot">+</div>
                            @else
                                @foreach($assigned as $m)
                                    @php
                                        $isAbsen     = in_array($m->id, $absenIds);
                                        $isDipinjam  = !$isAbsen && isset($replacements[$m->id]);
                                        $destMachine = $isDipinjam ? ($replacements[$m->id]->target_machine ?? '?') : null;
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
                                                {{ mb_strtoupper(mb_substr($m->nama,0,1)).(str_contains($m->nama,' ') ? mb_strtoupper(mb_substr(explode(' ',$m->nama)[1],0,1)) : '') }}
                                            @endif
                                        </div>
                                        <div class="mc-member-name" title="{{ $m->nama }}">
                                            {{ Str::limit(explode(' ',$m->nama)[0],7) }}{{ isset(explode(' ',$m->nama)[1]) ? ' '.mb_strtoupper(mb_substr(explode(' ',$m->nama)[1],0,1)).'.' : '' }}
                                        </div>
                                        @if($isAbsen)
                                            <span class="mc-member-tag tag-absen">Absen</span>
                                        @elseif($isDipinjam)
                                            <span class="mc-member-tag tag-dipinjam">Tugas Lain</span>
                                            <div class="mi-dipinjam-dest" title="Bertugas di: {{ $destMachine }}">
                                                ↗ {{ Str::limit($destMachine, 8) }}
                                            </div>
                                        @else
                                            <span class="mc-member-tag tag-hadir">Hadir</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        {{-- Status pills --}}
                        @php
                            $pillDefs = [
                                'normal'   => ['dot'=>null,   'label'=>'Normal'],
                                'man'      => ['dot'=>'dm',   'label'=>'Man'],
                                'material' => ['dot'=>'dt',   'label'=>'Matl'],
                                'machine'  => ['dot'=>'dc',   'label'=>'Mchn'],
                                'method'   => ['dot'=>'dme',  'label'=>'Mthd'],
                            ];
                        @endphp
                        <div class="mc-status-row" onclick="event.stopPropagation()" title="Status otomatis dari problem log & absensi">
                            @foreach($pillDefs as $pKey => $pDef)
                                <button class="mc-status-pill p-{{ $pKey }} {{ $stVal===$pKey?'active':'' }}"
                                        data-status="{{ $pKey }}"
                                        onclick="setPillStatus(this,'{{ addslashes($machine) }}','{{ $pKey }}')"
                                        title="{{ ucfirst($pKey) }} — klik untuk override manual">
                                    @if($pDef['dot'])
                                        <span class="pill-dot {{ $pDef['dot'] }}"></span>
                                    @endif
                                    {{ $pDef['label'] }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Tombol tambah problem log --}}
                        <div class="mc-addlog-bar" onclick="event.stopPropagation()">
                            <button class="mc-addlog-btn"
                                    onclick="openQuickLog('{{ addslashes($machine) }}')"
                                    title="Tambah problem log untuk {{ $machine }}">
                                + Log
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- Machine detail modal --}}
<div class="modal-overlay" id="machineSheet">
    <div class="modal-sheet">
        <div class="modal-sheet-handle"></div>
        <div class="modal-sheet-header">
            <h3 id="machineSheetTitle">Detail Mesin</h3>
            <button class="modal-sheet-close" onclick="closeSheet('machineSheet')">✕</button>
        </div>
        <div class="modal-sheet-body" id="machineSheetBody">
            <div style="text-align:center;padding:24px;color:#aaa">Memuat...</div>
        </div>
    </div>
</div>

{{-- Quick Add Log modal ─ pakai modal-overlay/modal-sheet sama seperti log.blade --}}
<div class="modal-overlay" id="quickLogOverlay">
    <div class="modal-sheet" style="max-height:90vh;overflow-y:auto">
        <div class="modal-sheet-handle"></div>

        {{-- Header dengan aksen warna & badge mesin --}}
        <div class="ql-header">
            <div class="ql-header-left">
                <span class="ql-header-icon">＋</span>
                <div>
                    <div class="ql-header-title">Tambah Problem Log</div>
                    <div class="ql-header-sub" id="qlMesinLabel">—</div>
                </div>
            </div>
            <button class="modal-sheet-close" onclick="closeQuickLog()">✕</button>
            <input type="hidden" id="qlLokasi">
        </div>

        <div class="modal-sheet-body" style="padding-top:4px">

            {{-- Row 1: Jenis + Waktu Mulai --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Tipe Masalah *</label>
                    <select id="qlJenis">
                        <option value="Man">Man</option>
                        <option value="Machine">Machine</option>
                        <option value="Material">Material</option>
                        <option value="Method">Method</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Waktu Mulai *</label>
                    <input type="time" id="qlMulai">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="field-group">
                <label>Deskripsi *</label>
                <textarea id="qlDeskripsi" rows="3"
                          placeholder="Jelaskan masalah secara singkat..."
                          style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                          onfocus="this.style.borderColor='var(--orange)'"
                          onblur="this.style.borderColor='#e0e0e0'"></textarea>
            </div>

            {{-- Row 2: Cause + PIC --}}
            <div class="form-row">
                <div class="field-group">
                    <label>Cause <span style="font-weight:400;color:#bbb">(opsional)</span></label>
                    <input type="text" id="qlCause" placeholder="Penyebab...">
                </div>
                <div class="field-group">
                    <label>PIC <span style="font-weight:400;color:#bbb">(opsional)</span></label>
                    <input type="text" id="qlPIC" placeholder="Nama penanggung jawab">
                </div>
            </div>

            {{-- Save button --}}
            <div class="save-bar">
                <button class="save-btn-big" id="qlSubmitBtn" onclick="submitQuickLog()">
                    💾 Simpan Log
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Finder pengganti popup --}}
<div class="finder-overlay" id="finderOverlay" onclick="handleFinderOverlayClick(event)">
    <div class="finder-sheet">
        <div class="finder-handle"></div>
        <div class="finder-ph">
            <button class="finder-close-btn" onclick="closeFinderModal()">✕</button>
            <h2>🔍 Cari Pengganti</h2>
            <p>Pilih member untuk menggantikan di mesin:</p>
            <div class="finder-ph-machine" id="finderMachineName">—</div>
        </div>
        <div class="finder-search-wrap">
            <input type="text" id="finderSearch" placeholder="🔍 Cari nama member…" oninput="renderFinderCandidates()">
        </div>
        <div class="finder-cand-list" id="finderCandList">
            <div class="cand-empty">⏳ Memuat…</div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const TANGGAL = '{{ $tanggal }}';
const FACTORY = @json($factory);
const SHIFT   = '{{ $shift }}'.replace(/^shift\s*/i,'').trim().toUpperCase();
const CSRF    = '{{ csrf_token() }}';

function updateQS(key,val){const u=new URL(window.location);u.searchParams.set(key,val);return u.toString();}
function initials(n){if(!n)return'?';const p=n.trim().split(' ');return p.length>=2?(p[0][0]+p[1][0]).toUpperCase():n.slice(0,2).toUpperCase();}
function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');}

const colorMap={man:'#e74c3c',machine:'#1f3c88',material:'#f39c12',method:'#2e7d32'};
const borderCls={man:'mc-status-man',machine:'mc-status-machine',material:'mc-status-material',method:'mc-status-method'};

// ══ SINGLE REFRESH — fetch absen + lights sekaligus, apply sekali ══
// Tidak ada dua fungsi terpisah yang saling override lagi.
let _refreshing = false;

async function refreshAll() {
    if (_refreshing) return;
    _refreshing = true;

    try {
        const qs = `tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`;

        const [absRes, lightRes, replRes] = await Promise.all([
            fetch(`/admin/absence/data?${qs}`,    { headers:{'Accept':'application/json'} }),
            fetch(`/admin/machines/lights?${qs}`, { headers:{'Accept':'application/json'} }),
            fetch(`/admin/replacements?${qs}`,    { headers:{'Accept':'application/json'} }),
        ]);

        const absData   = absRes.ok   ? await absRes.json()   : {};
        const lightData = lightRes.ok ? await lightRes.json() : {};
        const replData  = replRes.ok  ? await replRes.json()  : [];

        // Set member_id yang sedang jadi pengganti (dari DB)
        const activeReplacementMemberIds = new Set(replData.map(r => r.member_id));
        // Map: target_machine → [member_id pengganti]
        const replByTarget = {};
        replData.forEach(r => {
            if (!replByTarget[r.target_machine]) replByTarget[r.target_machine] = [];
            replByTarget[r.target_machine].push(r.member_id);
        });

        // Kumpulkan ID yang absen
        const absenIds = new Set(
            Object.entries(absData)
                .filter(([,v]) => v.status === 'absen')
                .map(([id]) => parseInt(id))
        );

        // ── Apply ke setiap kartu ──────────────────────────────────
        document.querySelectorAll('.mc-card').forEach(card => {
            const machine = card.dataset.machine;
            if (!machine) return;

            // ── A. Sync pengganti di DOM dengan DB ─────────────────
            // Cek apakah masih ada member asli yang absen di kartu ini
            let anyAbsen = false;
            card.querySelectorAll('.mc-member-item[data-member-id]').forEach(item => {
                if (item.dataset.replacement === '1') return;
                if (item.classList.contains('mi-dipinjam')) return;
                const id = parseInt(item.dataset.memberId);
                if (id && absenIds.has(id)) anyAbsen = true;
            });

            // Jika tidak ada yang absen lagi → hapus semua div pengganti dari DOM
            if (!anyAbsen) {
                card.querySelectorAll('.mc-member-item[data-replacement="1"]').forEach(el => el.remove());

                // Juga kembalikan member yang "dipinjam" di kartu lain ke status normal
                // (ditangani di loop kartu sumber di bawah)
            }

            const hasReplacement = !!card.querySelector('.mc-member-item[data-replacement="1"]');

            // ── B. Update status tiap member di kartu ini ──────────
            let cardHasAbsen = false;
            card.querySelectorAll('.mc-member-item[data-member-id]').forEach(item => {
                if (item.dataset.replacement === '1') return;

                const memberId = parseInt(item.dataset.memberId);
                if (!memberId) return;

                const isAbsen    = absenIds.has(memberId);
                const isDipinjam = activeReplacementMemberIds.has(memberId);
                const av  = item.querySelector('.mc-av');
                const img = item.querySelector('.mc-av img');
                const tag = item.querySelector('.mc-member-tag');

                if (isAbsen) {
                    if (!hasReplacement) cardHasAbsen = true;
                    item.classList.add('mi-absen');
                    item.classList.remove('mi-dipinjam');
                    av?.classList.replace('av-ok','av-absen');
                    if (img) img.style.filter = 'grayscale(.5) brightness(.8)';
                    if (tag) { tag.className = 'mc-member-tag tag-absen'; tag.textContent = 'Absen'; }
                    item.querySelector('.mi-dipinjam-dest')?.remove();
                } else if (isDipinjam) {
                    // Cari mesin tujuannya
                    const dest = replData.find(r => r.member_id === memberId)?.target_machine || '';
                    item.classList.add('mi-dipinjam');
                    item.classList.remove('mi-absen');
                    av?.classList.replace('av-absen','av-ok');
                    if (img) img.style.filter = 'grayscale(.55) brightness(.72)';
                    if (tag) { tag.className = 'mc-member-tag tag-dipinjam'; tag.textContent = 'Tugas Lain'; }
                    let destEl = item.querySelector('.mi-dipinjam-dest');
                    if (!destEl) { destEl = document.createElement('div'); destEl.className = 'mi-dipinjam-dest'; item.appendChild(destEl); }
                    if (dest) { destEl.textContent = `↗ ${dest}`; destEl.title = `Bertugas di: ${dest}`; }
                } else {
                    // Hadir normal
                    item.classList.remove('mi-absen','mi-dipinjam');
                    av?.classList.replace('av-absen','av-ok');
                    if (img) img.style.filter = '';
                    item.querySelector('.mi-dipinjam-dest')?.remove();
                    if (tag && ['Absen','Tugas Lain'].includes(tag.textContent)) {
                        tag.className = 'mc-member-tag tag-hadir'; tag.textContent = 'Hadir';
                    }
                }
            });

            // ── C. Lights & pill status ────────────────────────────
            const types = lightData[machine] || [];
            const ft    = types[0];

            // Update 4M pips
            const lightEl = card.querySelector('.mc-4m-row');
            if (lightEl) {
                lightEl.innerHTML = types.map(t =>
                    `<div class="mc-4m-pip" style="background:${colorMap[t]||'#ccc'}" title="${t}"></div>`
                ).join('');
            }

            // Sync pill status aktif dengan data server
            const pillMap = { man:'p-man', machine:'p-machine', material:'p-material', method:'p-method' };
            const targetPill = ft ? pillMap[ft] : 'p-normal';
            if (targetPill) {
                card.querySelectorAll('.mc-status-pill').forEach(p => p.classList.remove('active'));
                card.querySelector(`.mc-status-pill.${targetPill}`)?.classList.add('active');
            }

            // ── D. Border & dot ────────────────────────────────────
            card.classList.remove(...Object.values(borderCls));
            if (cardHasAbsen) {
                card.classList.add('mc-has-absen');
            } else {
                card.classList.remove('mc-has-absen');
                if (ft && borderCls[ft]) card.classList.add(borderCls[ft]);
            }

            const dot = card.querySelector('.mc-dot');
            if (!dot) return;

            if (cardHasAbsen) {
                dot.className        = 'mc-dot d-absen';
                dot.style.background = '';
                dot.onclick          = e => { e.stopPropagation(); openFinderModal(machine); };
                dot.title            = 'Klik untuk cari pengganti';
            } else if (ft && colorMap[ft]) {
                dot.className        = 'mc-dot d-visible';
                dot.style.background = colorMap[ft];
                dot.onclick          = e => { e.stopPropagation(); openMachineDetail(machine, card.dataset.status); };
                dot.title            = ft.toUpperCase();
            } else {
                dot.className        = 'mc-dot';
                dot.style.background = '';
                dot.onclick          = null;
                dot.title            = '';
            }
        });

    } catch(e) { console.error('refreshAll error:', e); }
    finally    { _refreshing = false; }
}

// ── Card + pill click handlers ─────────────────────────────────
function handleCardClick(e,machine,status){
    if(e.target.classList.contains('d-absen')||e.target.closest('.mc-status-row')||e.target.closest('.mc-addlog-bar'))return;
    openMachineDetail(machine,status);
}

function openMachineDetail(machine,currentStatus){
    openSheet('machineSheet');
    document.getElementById('machineSheetTitle').textContent=machine;
    fetch(`/admin/logs/list?tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`)
        .then(r=>r.json())
        .then(logs=>{
            const ml=logs.filter(l=>l.lokasi===machine&&l.status==='open');
            const logHtml=ml.length
                ?`<div style="margin-bottom:14px"><div class="section-title" style="margin-bottom:6px">⚠️ Open Logs (${ml.length})</div>
                  ${ml.map(l=>`<div style="background:#fff8f0;border:1px solid #fcd8a0;border-radius:8px;padding:8px 10px;margin-bottom:6px;font-size:11px">
                  <span style="font-weight:700;color:var(--orange)">[${esc(l.jenis)}]</span> ${esc(l.deskripsi)}
                  <span style="color:#aaa;margin-left:4px">${esc(l.waktu_mulai)}</span></div>`).join('')}</div>`
                :'<p style="font-size:12px;color:#aaa;text-align:center;padding:8px 0">Tidak ada open log.</p>';
            document.getElementById('machineSheetBody').innerHTML=`
                <p style="font-size:12px;color:#888;margin-bottom:12px">${esc(FACTORY)} | Shift ${SHIFT} | ${TANGGAL}</p>
                ${logHtml}
                <div style="text-align:center;margin-top:8px">
                    <a href="/admin/logs?tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}"
                       style="font-size:12px;padding:8px 16px;text-decoration:none;border-radius:8px;background:var(--navy);color:#fff;display:inline-block">
                       📝 Lihat Semua Problem Log</a></div>`;
        })
        .catch(()=>{document.getElementById('machineSheetBody').innerHTML='<p style="text-align:center;color:#aaa;padding:20px">Gagal memuat data</p>';});
}

async function setPillStatus(btn,machine,status){
    btn.closest('.mc-status-row').querySelectorAll('.mc-status-pill').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    try{
        await fetch('/admin/machines/status',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body:JSON.stringify({tanggal:TANGGAL,factory:FACTORY,shift:SHIFT,machine_name:machine,status}),
        });
        const card=document.querySelector(`.mc-card[data-machine="${CSS.escape(machine)}"]`);
        if(card) card.dataset.status=status;
        showToast(`${machine} → ${status}`,'success');
        setTimeout(refreshAll, 300);
    }catch(e){showToast('Gagal update status','error');}
}

// ── Quick Add Log (dari tombol + Log di kartu) ─────────────────
function openQuickLog(machine) {
    document.getElementById('qlLokasi').value           = machine;
    document.getElementById('qlMesinLabel').textContent = machine;
    document.getElementById('qlMulai').value            = new Date().toTimeString().slice(0,5);
    document.getElementById('qlDeskripsi').value        = '';
    document.getElementById('qlCause').value            = '';
    document.getElementById('qlPIC').value              = '';
    document.getElementById('qlJenis').value            = 'Machine';
    openSheet('quickLogOverlay');
    setTimeout(() => document.getElementById('qlDeskripsi').focus(), 300);
}

function closeQuickLog() {
    closeSheet('quickLogOverlay');
}

async function submitQuickLog() {
    const lokasi = document.getElementById('qlLokasi').value;
    const jenis  = document.getElementById('qlJenis').value;
    const mulai  = document.getElementById('qlMulai').value;
    const desk   = document.getElementById('qlDeskripsi').value.trim();

    if (!desk)  { showToast('Isi deskripsi masalah', 'error'); return; }
    if (!mulai) { showToast('Isi waktu mulai', 'error'); return; }

    const btn = document.getElementById('qlSubmitBtn');
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Menyimpan...'; }

    try {
        const res = await fetch('/admin/logs', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({
                tanggal:TANGGAL, factory:FACTORY, shift:SHIFT, jenis, lokasi,
                waktu_mulai:    mulai,
                waktu_selesai:  null,
                status:         'open',
                deskripsi:      desk,
                cause:          document.getElementById('qlCause').value || null,
                countermeasure: null,
                pic:            document.getElementById('qlPIC').value || null,
            }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data?.error) { showToast('Gagal: ' + (data?.message?.slice(0,60) ?? 'error'), 'error'); return; }
        showToast(`✅ Log ${jenis} ditambah — ${lokasi}`, 'success');
        closeQuickLog();
        setTimeout(refreshAll, 400);
    } catch(e) {
        showToast('Gagal: ' + e.message, 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.textContent = '💾 Simpan Log'; }
    }
}

// ── Finder modal ───────────────────────────────────────────────
let activeMachine=null;
function openFinderModal(machine){
    activeMachine=machine;
    document.getElementById('finderMachineName').textContent=machine;
    document.getElementById('finderSearch').value='';
    document.getElementById('finderOverlay').classList.add('show');
    document.body.style.overflow='hidden';
    renderFinderCandidates();
    setTimeout(()=>document.getElementById('finderSearch').focus(),200);
}
function closeFinderModal(){
    document.getElementById('finderOverlay').classList.remove('show');
    document.body.style.overflow='';activeMachine=null;
}
function handleFinderOverlayClick(e){if(e.target===document.getElementById('finderOverlay'))closeFinderModal();}

async function renderFinderCandidates(){
    const list=document.getElementById('finderCandList');
    const q=document.getElementById('finderSearch')?.value?.trim()||'';
    list.innerHTML='<div class="cand-empty">⏳ Memuat…</div>';
    try{
        const params=new URLSearchParams({tanggal:TANGGAL,factory:FACTORY,shift:SHIFT,q});
        const res=await fetch(`/admin/assignment/candidates?${params}`,{headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});
        if(!res.ok){list.innerHTML=`<div class="cand-empty">⚠️ Error ${res.status}</div>`;return;}
        const data=await res.json();
        if(!Array.isArray(data)||!data.length){list.innerHTML='<div class="cand-empty">🔍 Tidak ada member tersedia.</div>';return;}
        list.innerHTML=data.map(m=>{
            const av=m.photo?`<img src="${esc(m.photo)}" alt="">`:`<span>${initials(m.name)}</span>`;
            return`<div class="cand-card">
                <div class="cand-av ${m.isWorking?'cav-w':'cav-n'}">${av}</div>
                <div class="cand-name">${esc(m.name)}</div>
                ${m.jabatan?`<div style="font-size:9px;color:#aaa;font-family:'Roboto Condensed',sans-serif">${esc(m.jabatan)}</div>`:''}
                ${m.mesin?`<div style="font-size:9px;color:#aaa;font-family:'Roboto Condensed',sans-serif">${esc(m.mesin)}</div>`:''}
                ${m.isWorking?'<div class="cand-tag">Sdh Bertugas</div>':''}
                <button class="cand-btn" onclick="pickCandidate(${m.id},'${esc(m.name)}','${esc(m.photo||'')}','${esc(m.mesin||'')}')">✓ Pilih</button>
            </div>`;
        }).join('');
    }catch(e){list.innerHTML='<div class="cand-empty">Gagal memuat kandidat.</div>';}
}

async function pickCandidate(memberId, name, photo, sourceMachine) {
    if (!activeMachine) return;

    // Disable tombol cegah double-click
    event?.target?.setAttribute('disabled', true);

    try {
        // ── 1. Simpan ke DB ──────────────────────────────────────
        const res = await fetch('/admin/replacements', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                tanggal:        TANGGAL,
                factory:        FACTORY,
                shift:          SHIFT,
                target_machine: activeMachine,
                member_id:      memberId,
            }),
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            showToast(err.message || 'Gagal menyimpan pengganti', 'error');
            event?.target?.removeAttribute('disabled');
            return;
        }

        // ── 2. Update kartu TARGET (mesin yang ada absen) ─────────
        const targetCard = document.querySelector(`.mc-card[data-machine="${CSS.escape(activeMachine)}"]`);
        if (targetCard) {
            const row = targetCard.querySelector('.mc-members-row');
            if (row) {
                // Hapus empty slot jika ada
                row.querySelector('.mc-empty-slot')?.remove();

                const div = document.createElement('div');
                div.className = 'mc-member-item';
                div.dataset.memberId    = memberId;
                div.dataset.replacement = '1'; // tandai sebagai pengganti
                div.innerHTML = `
                    <div class="mc-av av-ok">
                        ${photo ? `<img src="${esc(photo)}" alt="${esc(name)}">` : `<span>${initials(name)}</span>`}
                    </div>
                    <div class="mc-member-name">${esc(name)}</div>
                    <span class="mc-member-tag" style="background:#e65100;color:#fff;font-size:7px;padding:1px 4px;border-radius:3px;text-transform:uppercase;font-weight:800">Pengganti</span>`;
                row.appendChild(div);
            }

            // Hapus mc-has-absen & dot merah — pengganti sudah ada
            targetCard.classList.remove('mc-has-absen');
            const dot = targetCard.querySelector('.mc-dot');
            if (dot) { dot.className = 'mc-dot'; dot.style.background = ''; dot.onclick = null; dot.title = ''; }

            // Reset pill status ke Normal (visual)
            targetCard.querySelectorAll('.mc-status-pill').forEach(p => p.classList.remove('active'));
            targetCard.querySelector('.mc-status-pill.p-normal')?.classList.add('active');
        }

        // ── 3. Update kartu SOURCE (mesin asal pengganti) ─────────
        // Tampilkan member sebagai "Tugas Lain" di kartu aslinya
        if (sourceMachine) {
            const sourceCard = document.querySelector(`.mc-card[data-machine="${CSS.escape(sourceMachine)}"]`);
            if (sourceCard) {
                const memberItem = sourceCard.querySelector(`.mc-member-item[data-member-id="${memberId}"]`);
                if (memberItem) {
                    memberItem.classList.add('mi-dipinjam');
                    memberItem.classList.remove('mi-absen');

                    const av  = memberItem.querySelector('.mc-av');
                    const img = memberItem.querySelector('.mc-av img');
                    const tag = memberItem.querySelector('.mc-member-tag');

                    av?.classList.replace('av-absen', 'av-ok');
                    if (img) img.style.filter = 'grayscale(.55) brightness(.72)';
                    if (tag) { tag.className = 'mc-member-tag tag-dipinjam'; tag.textContent = 'Tugas Lain'; }

                    // Tambah/update baris tujuan
                    let destEl = memberItem.querySelector('.mi-dipinjam-dest');
                    if (!destEl) {
                        destEl = document.createElement('div');
                        destEl.className = 'mi-dipinjam-dest';
                        memberItem.appendChild(destEl);
                    }
                    destEl.textContent = `↗ ${activeMachine}`;
                    destEl.title = `Bertugas di: ${activeMachine}`;
                }
            }
        }

        showToast(`✅ ${name} → ${activeMachine}`, 'success');
        closeFinderModal();

    } catch(e) {
        console.error('pickCandidate error:', e);
        showToast('Gagal menyimpan pengganti', 'error');
        event?.target?.removeAttribute('disabled');
    }
}

// ── Load replacements dari DB saat halaman buka ───────────────
async function loadReplacements() {
    try {
        const params = new URLSearchParams({ tanggal: TANGGAL, factory: FACTORY, shift: SHIFT });
        const res    = await fetch(`/admin/replacements?${params}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        if (!res.ok) return;
        const data = await res.json();

        data.forEach(r => {
            // Tampilkan pengganti di kartu target
            const targetCard = document.querySelector(`.mc-card[data-machine="${CSS.escape(r.target_machine)}"]`);
            if (targetCard) {
                const row = targetCard.querySelector('.mc-members-row');
                // Cek apakah sudah ada di DOM (hindari duplikat)
                const alreadyExists = row?.querySelector(`.mc-member-item[data-member-id="${r.member_id}"][data-replacement="1"]`);
                if (row && !alreadyExists) {
                    row.querySelector('.mc-empty-slot')?.remove();
                    const div = document.createElement('div');
                    div.className = 'mc-member-item';
                    div.dataset.memberId    = r.member_id;
                    div.dataset.replacement = '1';
                    div.innerHTML = `
                        <div class="mc-av av-ok">
                            ${r.member_photo ? `<img src="${esc(r.member_photo)}" alt="${esc(r.member_name)}">` : `<span>${initials(r.member_name)}</span>`}
                        </div>
                        <div class="mc-member-name">${esc(r.member_name||'')}</div>
                        <span class="mc-member-tag" style="background:#e65100;color:#fff;font-size:7px;padding:1px 4px;border-radius:3px;text-transform:uppercase;font-weight:800">Pengganti</span>`;
                    row.appendChild(div);
                }

                // Hapus mc-has-absen, dot merah, reset pill ke Normal
                targetCard.classList.remove('mc-has-absen');
                const dot = targetCard.querySelector('.mc-dot');
                if (dot) { dot.className = 'mc-dot'; dot.style.background = ''; dot.onclick = null; dot.title = ''; }
                targetCard.querySelectorAll('.mc-status-pill').forEach(p => p.classList.remove('active'));
                targetCard.querySelector('.mc-status-pill.p-normal')?.classList.add('active');
            }

            // Tampilkan "Tugas Lain" di kartu source
            if (r.source_machine) {
                const sourceCard = document.querySelector(`.mc-card[data-machine="${CSS.escape(r.source_machine)}"]`);
                if (sourceCard) {
                    const memberItem = sourceCard.querySelector(`.mc-member-item[data-member-id="${r.member_id}"]`);
                    if (memberItem && !memberItem.classList.contains('mi-dipinjam')) {
                        memberItem.classList.add('mi-dipinjam');
                        const img = memberItem.querySelector('.mc-av img');
                        const tag = memberItem.querySelector('.mc-member-tag');
                        if (img) img.style.filter = 'grayscale(.55) brightness(.72)';
                        if (tag) { tag.className = 'mc-member-tag tag-dipinjam'; tag.textContent = 'Tugas Lain'; }
                        let destEl = memberItem.querySelector('.mi-dipinjam-dest');
                        if (!destEl) {
                            destEl = document.createElement('div');
                            destEl.className = 'mi-dipinjam-dest';
                            memberItem.appendChild(destEl);
                        }
                        destEl.textContent = `↗ ${r.target_machine}`;
                        destEl.title = `Bertugas di: ${r.target_machine}`;
                    }
                }
            }
        });
    } catch(e) { console.error('loadReplacements error:', e); }
}

document.addEventListener('keydown',e=>{if(e.key==='Escape')closeFinderModal();});

// ══ FOTO MESIN UPLOAD ═════════════════════════════════════════

function triggerPhotoUpload(machine, slug) {
    document.getElementById('file-' + slug)?.click();
}

async function uploadMachinePhoto(event, machine, slug) {
    const file = event.target.files?.[0];
    if (!file) return;

    const wrap = document.getElementById('photo-wrap-' + slug);

    // Validasi ukuran (max 3MB)
    if (file.size > 3 * 1024 * 1024) {
        showToast('Foto terlalu besar (maks 3MB)', 'error');
        event.target.value = '';
        return;
    }

    // Loading state
    wrap?.classList.add('uploading');
    const overlayTxt = wrap?.querySelector('.upload-txt');
    if (overlayTxt) overlayTxt.textContent = '⏳ Mengupload...';

    try {
        const fd = new FormData();
        fd.append('factory',      FACTORY);
        fd.append('machine_name', machine);
        fd.append('photo',        file);
        fd.append('_token',       CSRF);

        const res  = await fetch('/admin/machines/photo', { method: 'POST', body: fd });
        const data = await res.json();

        if (!res.ok || !data.ok) {
            showToast(data.message || 'Gagal upload foto', 'error');
            return;
        }

        // Ganti placeholder/img lama dengan img baru
        const imgEl = document.getElementById('photo-img-' + slug);
        if (imgEl) {
            if (imgEl.tagName === 'IMG') {
                imgEl.src = data.photo_url + '?t=' + Date.now();
            } else {
                // Ganti placeholder div → img
                const newImg = document.createElement('img');
                newImg.id        = 'photo-img-' + slug;
                newImg.src       = data.photo_url;
                newImg.alt       = machine;
                newImg.loading   = 'lazy';
                newImg.style.cssText = 'width:100%;height:100%;object-fit:cover;transition:transform .3s';
                imgEl.replaceWith(newImg);
            }
        }

        // Update overlay text
        if (overlayTxt) overlayTxt.textContent = 'Ganti Foto';
        showToast(`✅ Foto ${machine} berhasil diupload`, 'success');

    } catch(e) {
        console.error('upload error:', e);
        showToast('Gagal upload foto', 'error');
        if (overlayTxt) overlayTxt.textContent = 'Upload Foto';
    } finally {
        wrap?.classList.remove('uploading');
        event.target.value = ''; // reset input agar bisa upload ulang
    }
}

// ─────────────────────────────────────────────────────────────
// INIT
// ─────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    refreshAll();          // langsung load saat buka
    loadReplacements();    // load pengganti tersimpan dari DB

    setInterval(refreshAll, 10_000); // satu interval, satu fungsi

    // Resume saat tab aktif kembali
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) refreshAll();
    });
});
</script>
@endpush