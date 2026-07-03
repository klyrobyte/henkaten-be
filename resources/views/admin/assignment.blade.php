@extends('layouts.admin')
@section('title', 'Penugasan Harian')

@push('styles')
    <style>
        /* ══ WRAP ══════════════════════════════════════════════════════ */
        .assign-wrap {
            padding: 10px 12px 110px;
        }

        /* ── Summary bar ─────────────────────────────────────────────── */
        .sum-bar {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .sum-card {
            background: #fff;
            border-radius: 10px;
            padding: 12px 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            border-top: 3px solid var(--green-light);
        }

        .sum-num {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 3px;
        }

        .sum-lbl {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #888;
            font-weight: 700;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .sc-hadir .sum-num {
            color: var(--green);
        }

        .sc-hadir {
            border-top-color: var(--green);
        }

        .sc-absen .sum-num {
            color: var(--red);
        }

        .sc-absen {
            border-top-color: var(--red);
        }

        .sc-butuh .sum-num {
            color: var(--yellow);
        }

        .sc-butuh {
            border-top-color: var(--yellow);
        }

        .sc-terisi .sum-num {
            color: var(--navy);
        }

        .sc-terisi {
            border-top-color: var(--navy);
        }

        .sc-kosong .sum-num {
            color: var(--orange);
        }

        .sc-kosong {
            border-top-color: var(--orange);
        }

        /* ── Alert ───────────────────────────────────────────────────── */
        .alert-strip {
            background: linear-gradient(90deg, var(--red), #c0392b);
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            margin-bottom: 12px;
            display: none;
            align-items: center;
            gap: 10px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 700;
        }

        .alert-strip.show {
            display: flex;
        }

        .alert-strip .ai {
            font-size: 18px;
            animation: apulse 1.5s infinite;
        }

        @keyframes apulse {

            0%,
            100% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.2)
            }
        }

        /* ── Group block ─────────────────────────────────────────────── */
        .group-block {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
            border: 2px solid var(--green-light);
            margin-bottom: 14px;
            overflow: hidden;
        }

        .group-hdr {
            background: var(--green);
            color: #fff;
            padding: 10px 14px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .8px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .group-hdr.kp {
            background: linear-gradient(135deg, var(--navy), var(--blue));
        }

        .g-badge {
            background: rgba(255, 255, 255, .2);
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
        }

        .g-badge.warn {
            background: var(--orange);
        }

        /* ══ ID CARD grid ════════════════════════════════════════════════
                           Setiap mesin = satu kartu ID card:
                           ┌──────────────────┐
                           │ ● NAMA MESIN     │  ← header, dot bisa diklik jika merah
                           ├──────────────────┤
                           │    [FOTO]        │  ← avatar member
                           │   Nama Member    │
                           │   [tag Hadir]    │
                           │  [btn Absen/🔍]  │
                           └──────────────────┘
                           ════════════════════════════════════════════════════════════════ */
        .machine-grid {
            padding: 12px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(128px, 1fr));
            gap: 10px;
        }

        .mc-card {
            border-radius: 12px;
            border: 2px solid #dde8c8;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            overflow: hidden;
            transition: box-shadow .2s, border-color .2s;
            display: flex;
            flex-direction: column;
        }

        .mc-card.card-ok {
            border-color: #729E3F;
        }

        .mc-card.card-partial {
            border-color: var(--yellow);
            box-shadow: 0 2px 12px rgba(243, 156, 18, .2);
        }

        .mc-card.card-problem {
            border-color: var(--red);
            box-shadow: 0 2px 16px rgba(231, 76, 60, .25);
        }

        /* Card header */
        .mc-card-hdr {
            padding: 7px 9px 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #f0f5e8, #e5efd6);
            border-bottom: 1px solid #d8e8c0;
        }

        .mc-card.card-partial .mc-card-hdr {
            background: linear-gradient(135deg, #fffbe6, #fef3c0);
            border-bottom-color: #f0d060;
        }

        .mc-card.card-problem .mc-card-hdr {
            background: linear-gradient(135deg, #fdeaea, #fad0d0);
            border-bottom-color: #f4a8a8;
        }

        /* ── Status dot  - DOT MERAH BISA DIKLIK ──────────────────────── */
        .mc-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
            border: 1.5px solid rgba(0, 0, 0, .1);
            transition: transform .15s;
        }

        .dot-ok {
            background: var(--green);
        }

        .dot-warn {
            background: var(--yellow);
            cursor: pointer;
            pointer-events: all;
            box-shadow: 0 0 0 3px rgba(243, 156, 18, .25);
            animation: warnpulse 1.8s ease-in-out infinite;
        }

        .dot-ab {
            background: var(--red);
            cursor: pointer;
            pointer-events: all;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, .3);
            animation: abpulse 1.3s ease-in-out infinite;
        }

        .dot-warn:hover,
        .dot-ab:hover {
            transform: scale(1.6);
        }

        .dot-warn:active,
        .dot-ab:active {
            transform: scale(.9);
        }

        @keyframes abpulse {

            0%,
            100% {
                box-shadow: 0 0 0 2px rgba(231, 76, 60, .4)
            }

            50% {
                box-shadow: 0 0 0 7px rgba(231, 76, 60, .0)
            }
        }

        @keyframes warnpulse {

            0%,
            100% {
                box-shadow: 0 0 0 2px rgba(243, 156, 18, .4)
            }

            50% {
                box-shadow: 0 0 0 6px rgba(243, 156, 18, .0)
            }
        }

        .mc-card-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color: #3a4a20;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .mc-card.card-problem .mc-card-title {
            color: #c0392b;
        }

        .mc-card.card-partial .mc-card-title {
            color: #8a6200;
        }

        /* Card body */
        .mc-card-body {
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        /* Empty slot */
        .mc-slot-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 10px 4px;
            color: #bbb;
            font-size: 11px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            min-height: 60px;
        }

        .mc-slot-empty:hover {
            border-color: var(--orange);
            color: var(--orange);
            background: rgba(255, 128, 0, .04);
        }

        /* Member slot */
        .mc-member {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            border-radius: 10px;
            padding: 8px 6px 7px;
            position: relative;
            border: 1.5px solid #dde8c8;
            background: #f8faf5;
            transition: all .15s;
        }

        .ms-present {
            border-color: #b4d87a;
            background: #f4f9ec;
        }

        .ms-absent {
            border-color: #f5aaaa;
            background: #fdf0f0;
        }

        .ms-sub {
            border-color: var(--orange);
            background: #fff7ee;
            border-style: dashed;
        }

        /* Avatar */
        .mc-av {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 900;
            color: #fff;
            overflow: hidden;
            flex-shrink: 0;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
            font-family: 'Roboto Condensed', sans-serif;
        }

        .mc-av img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .av-present {
            background: linear-gradient(135deg, #8bc34a, var(--green));
        }

        .av-absent {
            background: linear-gradient(135deg, #ef9a9a, #c0392b);
            filter: grayscale(.3);
        }

        .av-sub {
            background: linear-gradient(135deg, #ffb74d, var(--orange));
        }

        /* Name */
        .mc-member-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-align: center;
            line-height: 1.2;
            color: var(--gray);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 112px;
            width: 100%;
        }

        .ms-absent .mc-member-name {
            color: #c0392b;
        }

        .ms-sub .mc-member-name {
            color: #c05800;
        }

        /* Tag */
        .mc-tag {
            font-size: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: .3px;
        }

        .tag-hadir {
            background: var(--green);
            color: #fff;
        }

        .tag-absen {
            background: var(--red);
            color: #fff;
        }

        .tag-sub {
            background: var(--orange);
            color: #fff;
        }

        .tag-sync {
            background: var(--navy);
            color: #fff;
        }

        /* Action buttons */
        .mc-actions {
            display: flex;
            gap: 3px;
            margin-top: 2px;
            width: 100%;
        }

        .mc-btn {
            padding: 4px 6px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 9px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 800;
            transition: all .15s;
            flex: 1;
            text-align: center;
            white-space: nowrap;
        }

        .mc-btn:active {
            transform: scale(.9);
        }

        .btn-ab {
            background: rgba(231, 76, 60, .1);
            color: var(--red);
        }

        .btn-pr {
            background: rgba(46, 125, 50, .1);
            color: var(--green);
        }

        .btn-find {
            background: var(--orange);
            color: #fff;
            padding: 4px 8px;
            font-size: 12px;
            flex: 0 0 auto;
        }

        .btn-rm {
            background: rgba(231, 76, 60, .1);
            color: var(--red);
        }

        /* ── Two-pane + Sub panel ────────────────────────────────────── */
        .assign-two {
            display: grid;
            grid-template-columns: 1fr 290px;
            gap: 14px;
            align-items: start;
        }

        @media(max-width:1100px) {
            .assign-two {
                grid-template-columns: 1fr;
            }

            .sub-panel {
                position: static;
                max-height: none;
            }
        }

        @media(max-width:600px) {
            .sum-bar {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .sub-panel {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            border: 2px solid var(--green-light);
            overflow: hidden;
            position: sticky;
            top: 80px;
            max-height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
        }

        .sub-ph {
            background: linear-gradient(135deg, var(--orange), #d46200);
            padding: 14px 16px;
            color: #fff;
        }

        .sub-ph h2 {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 900;
            margin: 0 0 2px;
        }

        .sub-ph p {
            font-size: 11px;
            opacity: .85;
            margin: 0;
        }

        .sub-ctx {
            background: rgba(0, 0, 0, .2);
            border-radius: 6px;
            padding: 5px 8px;
            margin-top: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 700;
            display: none;
        }

        .sub-ctx.show {
            display: block;
        }

        .sub-idle {
            padding: 28px 14px;
            text-align: center;
            color: #bbb;
        }

        .idle-ico {
            font-size: 40px;
            margin-bottom: 8px;
        }

        .sub-idle h3 {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            color: #ccc;
            margin-bottom: 6px;
        }

        .sub-idle p {
            font-size: 11px;
            line-height: 1.7;
            color: #aaa;
        }

        .sub-search-wrap {
            padding: 10px 12px;
            border-bottom: 1px solid #e0ecd4;
        }

        .sub-search-wrap input {
            width: 100%;
            padding: 8px 10px;
            border: 2px solid var(--green-light);
            border-radius: 8px;
            font-size: 13px;
            box-sizing: border-box;
        }

        .sub-search-wrap input:focus {
            outline: none;
            border-color: var(--green);
        }

        .cand-list {
            overflow-y: auto;
            padding: 8px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
            flex: 1;
        }

        .cand-list::-webkit-scrollbar {
            width: 4px;
        }

        .cand-list::-webkit-scrollbar-thumb {
            background: var(--green);
            border-radius: 2px;
        }

        .cand-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 10px 5px 8px;
            border-radius: 10px;
            border: 2px solid var(--green-light);
            background: #f8faf5;
            cursor: pointer;
            transition: all .18s;
            text-align: center;
        }

        .cand-card:active {
            border-color: var(--orange);
            background: rgba(255, 128, 0, .05);
            transform: scale(.96);
        }

        .cand-av {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            color: #fff;
            overflow: hidden;
            border: 2px solid var(--green);
            font-family: 'Roboto Condensed', sans-serif;
        }

        .cand-av img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .av-n {
            background: linear-gradient(135deg, var(--green-light), var(--green));
        }

        .av-w {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            border-color: var(--yellow);
        }

        .cand-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.2;
        }

        .cand-tag {
            font-size: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid var(--yellow);
            color: var(--yellow);
            background: rgba(243, 156, 18, .1);
        }

        .cand-btn {
            width: 100%;
            padding: 5px 0;
            border-radius: 6px;
            border: none;
            background: var(--orange);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 900;
            font-size: 11px;
            cursor: pointer;
        }

        .cand-empty {
            text-align: center;
            padding: 20px 10px;
            color: #aaa;
            grid-column: 1/-1;
            font-size: 12px;
        }

        /* ── Sync strip ──────────────────────────────────────────────── */
        .sync-strip {
            background: linear-gradient(90deg, #1565C0, var(--navy));
            color: #fff;
            padding: 7px 14px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: none;
            align-items: center;
            gap: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 600;
        }

        .sync-strip.show {
            display: flex;
        }

        /* ── Save bar ────────────────────────────────────────────────── */
        .assign-save-bar {
            position: fixed;
            bottom: 56px;
            left: 0;
            right: 0;
            background: var(--navy);
            padding: 10px 18px;
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 200;
            box-shadow: 0 -4px 18px rgba(0, 0, 0, .2);
            border-top: 3px solid var(--orange);
        }

        .assign-save-bar.show {
            display: flex;
        }

        .asb-msg {
            color: rgba(255, 255, 255, .85);
            font-size: 13px;
            font-weight: 600;
            flex: 1;
        }

        .asb-msg strong {
            color: var(--orange);
        }

        .btn-asb-save {
            padding: 8px 20px;
            border-radius: 8px;
            border: none;
            background: var(--green);
            color: #fff;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-asb-discard {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1.5px solid rgba(255, 255, 255, .3);
            background: transparent;
            color: rgba(255, 255, 255, .7);
            font-size: 12px;
            cursor: pointer;
        }

        /* ── Modal alasan absen ─────────────────────────────────────── */
        .modal-bg2 {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            z-index: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s;
        }

        .modal-bg2.open {
            opacity: 1;
            pointer-events: all;
        }

        .modal-box2 {
            background: #fff;
            border-radius: 14px;
            width: 100%;
            max-width: 360px;
            transform: scale(.93) translateY(18px);
            transition: transform .22s;
            overflow: hidden;
        }

        .modal-bg2.open .modal-box2 {
            transform: scale(1) translateY(0);
        }

        .modal-hdr2 {
            background: linear-gradient(135deg, var(--green), var(--navy));
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-hdr2 h3 {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 900;
            color: #fff;
            margin: 0;
        }

        .modal-hdr2 button {
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, .35);
            color: #fff;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
        }

        .reason-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 16px 18px;
        }

        .reason-btn {
            padding: 11px 8px;
            border-radius: 8px;
            border: 2px solid var(--green-light);
            background: #f8faf5;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
        }

        .reason-btn.sel {
            background: var(--red);
            color: #fff;
            border-color: var(--red);
        }

        .modal-foot2 {
            padding: 0 18px 16px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn-cancel {
            padding: 9px 18px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            background: #f5f5f5;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-confirm {
            padding: 9px 18px;
            border-radius: 8px;
            border: none;
            background: var(--red);
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')

    {{-- Date + Shift --}}
    <div class="date-bar"
        style="display:flex;align-items:center;background:#fff;border-radius:50px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,0.08);gap:12px;">

        {{-- Icon kalender  - klik ini untuk buka date picker --}}
        <div class="date-label" onclick="document.getElementById('tanggalHari').showPicker()"
            style="width:36px;height:36px;background:#2E7D32;border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
        </div>

        {{-- Tanggal display --}}
        <span
            style="flex:1;font-family:'Roboto Condensed',sans-serif;font-weight:600;font-size:18px;color:#222;letter-spacing:.5px;">
            {{ \Carbon\Carbon::parse($tanggal)->format('d / m / Y') }}
        </span>

        {{-- Input date tersembunyi  - hanya trigger via icon --}}
        <input type="date" id="tanggalHari" value="{{ $tanggal }}" onchange="onDateChange(this.value)"
            style="position:absolute;opacity:0;pointer-events:none;width:0;height:0;">

        {{-- Factory badge --}}
        <button onclick="showFactoryPicker()" style="background:#2E7D32;border:none;border-radius:20px;padding:7px 18px;color:#fff;
                                           font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:12px;
                                           letter-spacing:1px;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;
                                           transition:all .2s ease;" onmouseover="this.style.filter='brightness(1.15)'"
            onmouseout="this.style.filter='brightness(1)'">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff"
                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="15" rx="1" />
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                <line x1="12" y1="12" x2="12" y2="12" />
                <line x1="8" y1="12" x2="8" y2="12" />
                <line x1="16" y1="12" x2="16" y2="12" />
                <line x1="8" y1="16" x2="8" y2="16" />
                <line x1="16" y1="16" x2="16" y2="16" />
                <line x1="12" y1="16" x2="12" y2="16" />
            </svg>
            {{ strtoupper(\App\Models\Factory::where('name', session('factory', 'Factory 2'))->value('short_label') ?? session('factory', 'Factory 2')) }}
        </button>
    </div>

    <div class="shift-toggle-bar">
        @php $userShift = auth()->user()->shift;
        $isAdmin = auth()->user()->role === 'admin'; @endphp
        @if($isAdmin || !$userShift || $userShift === 'A')
            <button class="shift-toggle-btn {{ $shift === 'A' ? 'active' : '' }}" onclick="switchShiftAssign('A')">SHIFT
                A</button>
        @endif
        @if($isAdmin || !$userShift || $userShift === 'B')
            <button class="shift-toggle-btn {{ $shift === 'B' ? 'active' : '' }}" onclick="switchShiftAssign('B')">SHIFT
                B</button>
        @endif
    </div>

    {{-- Legenda --}}
    <div class="legend-4m" style="display:inline-flex;
                    align-items:center;
                    gap:16px;
                    background:#fff;
                    border-radius:15px;
                    border:1.5px solid #e0e0e0;
                    padding:10px 22px;
                    box-shadow:0 1px 3px rgba(0,0,0,0.05);
                    transition:box-shadow .2s ease, border-color .2s ease;"
        onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.10)';this.style.borderColor='#bdbdbd';"
        onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)';this.style.borderColor='#e0e0e0';">

        <div style="display:flex;align-items:center;gap:7px;">
            <span
                style="width:14px;height:14px;border-radius:50%;background:var(--red);flex-shrink:0;display:inline-block;"></span>
            <span
                style="font-size:13px;font-weight:700;color:#1a1a1a;white-space:nowrap;font-family:'Roboto Condensed',sans-serif;">Man
                (Absen)</span>
        </div>

        <div style="display:flex;align-items:center;gap:7px;">
            <span
                style="width:14px;height:14px;border-radius:50%;background:var(--navy);flex-shrink:0;display:inline-block;"></span>
            <span
                style="font-size:13px;font-weight:700;color:#1a1a1a;white-space:nowrap;font-family:'Roboto Condensed',sans-serif;">Machine</span>
        </div>

        <div style="display:flex;align-items:center;gap:7px;">
            <span
                style="width:14px;height:14px;border-radius:50%;background:var(--yellow);flex-shrink:0;display:inline-block;"></span>
            <span
                style="font-size:13px;font-weight:700;color:#1a1a1a;white-space:nowrap;font-family:'Roboto Condensed',sans-serif;">Material</span>
        </div>

        <div style="display:flex;align-items:center;gap:7px;">
            <span
                style="width:14px;height:14px;border-radius:50%;background:var(--green);flex-shrink:0;display:inline-block;"></span>
            <span
                style="font-size:13px;font-weight:700;color:#1a1a1a;white-space:nowrap;font-family:'Roboto Condensed',sans-serif;">Method</span>
        </div>

    </div>



    <div class="assign-wrap">

        <div class="sync-strip" id="syncStrip"><span>📡</span><span id="syncTxt"></span></div>
        <div class="alert-strip" id="alertStrip"><span class="ai">🚨</span><span id="alertTxt"></span></div>

        {{-- Summary --}}
        <div class="sum-bar">
            <div class="sum-card sc-hadir">
                <div class="sum-num" id="sumHadir">{{ $summary['hadir'] }}</div>
                <div class="sum-lbl">Hadir</div>
            </div>
            <div class="sum-card sc-absen">
                <div class="sum-num" id="sumAbsen">{{ $summary['absen'] }}</div>
                <div class="sum-lbl">Absen</div>
            </div>
            <div class="sum-card sc-butuh">
                <div class="sum-num" id="sumButuh">{{ $summary['butuh'] }}</div>
                <div class="sum-lbl">Butuh Pengganti</div>
            </div>
            <div class="sum-card sc-terisi">
                <div class="sum-num" id="sumTerisi">{{ $summary['terisi'] }}</div>
                <div class="sum-lbl">Terisi</div>
            </div>
            <div class="sum-card sc-kosong">
                <div class="sum-num" id="sumKosong">{{ $summary['kosong'] }}</div>
                <div class="sum-lbl">Kosong</div>
            </div>
        </div>

        <div class="assign-two">

            {{-- LEFT: Papan penugasan  - dirender penuh oleh JS --}}
            <div id="boardRoot"></div>

            {{-- RIGHT: Panel cari pengganti --}}
            <div>
                <div class="sub-panel" id="subPanel">
                    <div class="sub-ph">
                        <h2>🔍 Cari Pengganti</h2>
                        <p>Klik dot merah atau tombol 🔍 pada member absen</p>
                        <div class="sub-ctx" id="subCtx"></div>
                    </div>
                    <div class="sub-idle" id="subIdle">
                        <div class="idle-ico">👆</div>
                        <h3>Belum Ada Pilihan</h3>
                        <p>Tandai member absen,<br>lalu klik <strong style="color:var(--red)">dot merah</strong><br>atau tap
                            <strong style="color:var(--orange)">🔍</strong>.
                        </p>
                    </div>
                    <div id="subActive" style="display:none;flex-direction:column;flex:1;overflow:hidden">
                        <div class="sub-search-wrap">
                            <input type="text" id="subSearch" placeholder="🔍 Cari nama..." oninput="renderCandidates()">
                        </div>
                        <div class="cand-list" id="candList"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Save bar --}}
    <div class="assign-save-bar" id="assignSaveBar">
        <div class="asb-msg">Ada <strong id="changeCount">0</strong> perubahan belum disimpan</div>
        <button class="btn-asb-discard" onclick="discardChanges()">↩ Batalkan</button>
        <button class="btn-asb-save" onclick="saveAssignments()">💾 Simpan</button>
    </div>

    {{-- Modal alasan absen --}}
    <div class="modal-bg2" id="absenModal" onclick="if(event.target===this)closeAbsenModal()">
        <div class="modal-box2">
            <div class="modal-hdr2">
                <h3 id="absenModalTitle">Tandai Tidak Masuk</h3>
                <button onclick="closeAbsenModal()">×</button>
            </div>
            <div class="reason-grid">
                <button class="reason-btn" data-reason="Sakit" onclick="selectReason(this)">🤒 Sakit</button>
                <button class="reason-btn" data-reason="Cuti" onclick="selectReason(this)">📅 Cuti</button>
                <button class="reason-btn" data-reason="Ijin" onclick="selectReason(this)">📝 Ijin</button>
                <button class="reason-btn" data-reason="Alpha" onclick="selectReason(this)">❓ Tidak Diketahui</button>
            </div>
            <div class="modal-foot2">
                <button class="btn-cancel" onclick="closeAbsenModal()">Batal</button>
                <button class="btn-confirm" onclick="confirmAbsent()">✅ Konfirmasi</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ══ CONFIG ════════════════════════════════════════════════════════════════════
        const TANGGAL = '{{ $tanggal }}';
        const FACTORY = @json($factory);
        const SHIFT = '{{ $shift }}';
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        const FACTORY_CONFIG = @json($groups);
        const MEMBER_LIST = @json($members->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->nama,
            'photo' => $m->photo_url,
            'jabatan' => $m->jabatan,
        ]));

        // ══ STATE ═════════════════════════════════════════════════════════════════════
        let assignments = @json($assignments);
        let savedAssignments = JSON.parse(JSON.stringify(assignments));
        let unsaved = false;
        let pendingAbsent = null;
        let selectedReason = '';
        let activeFinder = null;

        // ══ HELPERS ═══════════════════════════════════════════════════════════════════
        function initials(name) {
            if (!name) return '?';
            const p = name.trim().split(' ');
            return p.length >= 2 ? (p[0][0] + p[1][0]).toUpperCase() : name.slice(0, 2).toUpperCase();
        }
        function esc(s) {
            return String(s || '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        function getPhoto(name) { return MEMBER_LIST.find(x => x.name === name)?.photo || null; }

        // ══ RENDER BOARD ══════════════════════════════════════════════════════════════
        function renderBoard() {
            const root = document.getElementById('boardRoot');
            let html = '';

            FACTORY_CONFIG.forEach(group => {
                const isKP = group.title.includes('Key Persons');
                let absenCount = 0;

                group.machines.forEach(mac => {
                    const slots = assignments[`${group.title}::${mac}`] || [];
                    if (slots.some(s => s.status === 'absent' && !s.isSubstitute)) absenCount++;
                });

                const badge = absenCount > 0
                    ? `<span class="g-badge warn">⚠ ${absenCount} absen</span>`
                    : `<span class="g-badge">${group.machines.length} mesin</span>`;

                html += `<div class="group-block">
                                    <div class="group-hdr ${isKP ? 'kp' : ''}">
                                        ${isKP ? '👑' : '🔧'} ${esc(group.title)} ${badge}
                                    </div>
                                    <div class="machine-grid">`;

                group.machines.forEach(mac => {
                    html += renderMachineCard(`${group.title}::${mac}`, mac);
                });

                html += `</div></div>`;
            });

            root.innerHTML = html;
        }

        // ── ID Card untuk satu mesin ──────────────────────────────────────────────
        function renderMachineCard(key, macName) {
            const slots = assignments[key] || [];
            const hasAb = slots.some(s => s.status === 'absent' && !s.isSubstitute);
            const allAb = slots.length > 0 && slots.filter(s => !s.isSubstitute).every(s => s.status === 'absent');
            const cardCls = allAb ? 'card-problem' : hasAb ? 'card-partial' : 'card-ok';
            const dotCls = allAb ? 'dot-ab' : hasAb ? 'dot-warn' : 'dot-ok';

            // ── dot onclick: langsung tempel di atribut HTML, BUKAN addEventListener
            const dotOnClick = (dotCls !== 'dot-ok')
                ? `onclick="openFinderForMachine(this)" data-key="${esc(key)}"`
                : '';

            let slotsHtml = '';
            if (!slots.length) {
                slotsHtml = `<div class="mc-slot-empty" onclick="openFinderEmpty('${esc(key)}',0)">
                                    <span style="font-size:16px">+</span><span>Tambah</span></div>`;
            } else {
                slots.forEach((slot, idx) => { slotsHtml += renderMemberSlot(key, slot, idx); });
            }

            return `<div class="mc-card ${cardCls}">
                                <div class="mc-card-hdr">
                                    <div class="mc-dot ${dotCls}" ${dotOnClick}></div>
                                    <div class="mc-card-title" title="${esc(macName)}">${esc(macName)}</div>
                                </div>
                                <div class="mc-card-body">${slotsHtml}</div>
                            </div>`;
        }

        // ── Member slot dalam card ────────────────────────────────────────────────
        function renderMemberSlot(key, slot, idx) {
            if (!slot.memberName) {
                return `<div class="mc-slot-empty" onclick="openFinderEmpty('${esc(key)}',${idx})">
                                    <span style="font-size:16px">+</span><span>Tambah</span></div>`;
            }

            const photo = slot.foto || getPhoto(slot.memberName);
            const isAb = slot.status === 'absent';
            const isSub = !!slot.isSubstitute;

            const avCls = isSub ? 'av-sub' : isAb ? 'av-absent' : 'av-present';
            const memberCls = isSub ? 'ms-sub' : isAb ? 'ms-absent' : 'ms-present';
            const imgStyle = isAb && !isSub ? 'style="filter:grayscale(.4) brightness(.85)"' : '';
            const avContent = photo
                ? `<img src="${esc(photo)}" alt="" ${imgStyle}>`
                : `<span>${initials(slot.memberName)}</span>`;

            const tagHtml = isSub
                ? `<span class="mc-tag tag-sub">Pengganti</span>`
                : isAb
                    ? (slot.syncedFromMM
                        ? `<span class="mc-tag tag-sync">Absen(MM)</span>`
                        : `<span class="mc-tag tag-absen">${esc(slot.absentReason || 'Absen')}</span>`)
                    : `<span class="mc-tag tag-hadir">Hadir</span>`;

            const btnsHtml = isSub
                ? `<div class="mc-actions"><button class="mc-btn btn-rm" onclick="removeSub('${esc(key)}',${idx})">✕ Hapus</button></div>`
                : isAb
                    ? `<div class="mc-actions">
                                        <button class="mc-btn btn-pr"   onclick="markPresent('${esc(key)}',${idx})">✔ Hadir</button>
                                        <button class="mc-btn btn-find" onclick="openFinder('${esc(key)}',${idx})">🔍</button>
                                       </div>`
                    : `<div class="mc-actions"><button class="mc-btn btn-ab" onclick="openAbsenModal('${esc(key)}',${idx},'${esc(slot.memberName)}')">✕ Absen</button></div>`;

            return `<div class="mc-member ${memberCls}">
                                <div class="mc-av ${avCls}">${avContent}</div>
                                <div class="mc-member-name" title="${esc(slot.memberName)}">${esc(slot.memberName)}</div>
                                ${tagHtml}
                                ${btnsHtml}
                            </div>`;
        }

        // ══ SUMMARY ═══════════════════════════════════════════════════════════════════
        function updateSummary() {
            let hadir = 0, absen = 0, butuh = 0, terisi = 0;
            Object.values(assignments).forEach(slots => {
                slots.forEach((s, i) => {
                    if (!s.memberName || s.isSubstitute) return;
                    if (s.status === 'absent') {
                        absen++; butuh++;
                        if (slots.some(x => x.isSubstitute && x.substituteFor === i)) terisi++;
                    } else { hadir++; }
                });
            });
            const kosong = Math.max(0, butuh - terisi);
            ['hadir', 'absen', 'butuh', 'terisi', 'kosong'].forEach(k => {
                document.getElementById('sum' + k.charAt(0).toUpperCase() + k.slice(1)).textContent =
                    { hadir, absen, butuh, terisi, kosong }[k];
            });
            const strip = document.getElementById('alertStrip');
            if (kosong > 0) {
                document.getElementById('alertTxt').textContent = `⚠ Ada ${kosong} posisi kosong belum ada penggantinya!`;
                strip.classList.add('show');
            } else { strip.classList.remove('show'); }
        }

        function render() { renderBoard(); updateSummary(); renderCandidates(); updateSaveBar(); }

        // ══ ABSEN / HADIR ═════════════════════════════════════════════════════════════
        function openAbsenModal(key, idx, memberName) {
            pendingAbsent = { key, idx, memberName };
            selectedReason = '';
            document.getElementById('absenModalTitle').textContent = `Tandai Tidak Masuk: ${memberName}`;
            document.querySelectorAll('.reason-btn').forEach(b => b.classList.remove('sel'));
            document.getElementById('absenModal').classList.add('open');
        }
        function closeAbsenModal() { document.getElementById('absenModal').classList.remove('open'); }
        function selectReason(btn) {
            document.querySelectorAll('.reason-btn').forEach(b => b.classList.remove('sel'));
            btn.classList.add('sel'); selectedReason = btn.dataset.reason;
        }
        function confirmAbsent() {
            if (!pendingAbsent) return;
            const { key, idx } = pendingAbsent;
            if (!assignments[key]?.[idx]) return;
            assignments[key][idx].status = 'absent';
            assignments[key][idx].absentReason = selectedReason || 'Absen';
            assignments[key][idx].syncedFromMM = false;
            markUnsaved(); closeAbsenModal(); render();
            showToast(`🔴 ${pendingAbsent.memberName} tidak masuk (${selectedReason || 'Absen'})`, 'warn');
            pendingAbsent = null;
        }
        function markPresent(key, idx) {
            if (!assignments[key]?.[idx]) return;
            const name = assignments[key][idx].memberName;
            assignments[key][idx].status = 'present'; assignments[key][idx].absentReason = ''; assignments[key][idx].syncedFromMM = false;
            const si = assignments[key].findIndex(s => s.isSubstitute && s.substituteFor === idx);
            if (si !== -1) assignments[key].splice(si, 1);
            markUnsaved(); render(); showToast(`✅ ${name} ditandai hadir`, 'success');
        }

        // ══ FINDER ════════════════════════════════════════════════════════════════════
        // Dipanggil oleh onclick="openFinderForMachine(this)" pada DOT elemen
        // Menggunakan data-key dari elemen DOM  - tidak ada isu string escaping
        function openFinderForMachine(dotEl) {
            const key = dotEl.getAttribute('data-key');
            if (!key) return;
            const slots = assignments[key];
            if (!slots) return;
            // Cari slot absen yang belum punya pengganti
            let idx = slots.findIndex((s, i) =>
                s.status === 'absent' && !s.isSubstitute &&
                !slots.some(x => x.isSubstitute && x.substituteFor === i)
            );
            if (idx === -1) idx = slots.findIndex(s => s.status === 'absent' && !s.isSubstitute);
            if (idx !== -1) openFinder(key, idx);
        }

        function openFinder(key, idx) {
            activeFinder = { key, idx };
            const slot = assignments[key]?.[idx];
            document.getElementById('subIdle').style.display = 'none';
            document.getElementById('subActive').style.display = 'flex';
            document.getElementById('subSearch').value = '';
            const ctx = document.getElementById('subCtx');
            ctx.className = 'sub-ctx show';
            ctx.textContent = `Pengganti untuk: ${slot?.memberName || '?'} → ${key.split('::')[1]}`;
            renderCandidates();
            if (window.innerWidth <= 1100) document.getElementById('subPanel').scrollIntoView({ behavior: 'smooth' });
        }
        function openFinderEmpty(key, idx) {
            activeFinder = { key, idx, isEmpty: true };
            document.getElementById('subIdle').style.display = 'none';
            document.getElementById('subActive').style.display = 'flex';
            document.getElementById('subSearch').value = '';
            const ctx = document.getElementById('subCtx');
            ctx.className = 'sub-ctx show';
            ctx.textContent = `Tambah member ke: ${key.split('::')[1]}`;
            renderCandidates();
        }
        function closeFinder() {
            activeFinder = null;
            document.getElementById('subIdle').style.display = 'block';
            document.getElementById('subActive').style.display = 'none';
            document.getElementById('subCtx').className = 'sub-ctx';
        }

        async function renderCandidates() {
            if (!activeFinder) return;
            const machineName = activeFinder.key.split('::')[1];
            const list = document.getElementById('candList');
            const q = document.getElementById('subSearch')?.value?.toLowerCase().trim() || '';
            
            let absentName = '';
            if (!activeFinder.isEmpty) {
                const slot = assignments[activeFinder.key]?.[activeFinder.idx];
                if (slot && slot.status === 'absent') {
                    absentName = slot.memberName;
                }
            }

            list.innerHTML = '<div class="cand-empty">⏳ Memuat...</div>';
            try {
                const url = `/admin/assignment/candidates?tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}&machine=${encodeURIComponent(machineName)}&q=${encodeURIComponent(q)}&absentName=${encodeURIComponent(absentName)}`;
                const data = await fetch(url, { headers: { 'Accept': 'application/json' } }).then(r => r.json());
                if (!data.length) { list.innerHTML = '<div class="cand-empty">🔍 Tidak ada member pengganti yg sesuai kriteria skill.</div>'; return; }
                list.innerHTML = data.map(m => {
                    const av = m.photo ? `<img src="${esc(m.photo)}" alt="">` : `<span>${initials(m.name)}</span>`;
                    return `<div class="cand-card" onclick="assignSub('${esc(m.name)}','${esc(m.photo || '')}')">
                                        <div class="cand-av ${m.isWorking ? 'av-w' : 'av-n'}">${av}</div>
                                        <div class="cand-name">${esc(m.name)}</div>
                                        ${m.isWorking ? '<div class="cand-tag">Sdh Bertugas</div>' : ''}
                                        ${m.skill_pct !== null ? `<div class="cand-tag" style="background:${m.skill_pct >= 75 ? 'var(--green)' : 'var(--orange)'};color:#fff;">Skill ${m.skill_pct}%</div>` : ''}
                                        ${m.eligible ? `<button class="cand-btn" onclick="event.stopPropagation();assignSub('${esc(m.name)}','${esc(m.photo || '')}')">✓ Pilih</button>` : `<button class="cand-btn" style="background:#aaa;cursor:not-allowed;" disabled>Skill < 75%</button>`}
                                    </div>`;
                }).join('');
            } catch (e) { list.innerHTML = '<div class="cand-empty">Gagal memuat kandidat.</div>'; }
        }

        function assignSub(memberName, photoData) {
            if (!activeFinder) return;
            const { key, idx, isEmpty } = activeFinder;
            if (!assignments[key]) return;
            const photo = photoData || getPhoto(memberName) || null;
            if (isEmpty) {
                assignments[key][idx] = { memberName, foto: photo, status: 'present', absentReason: '', isSubstitute: false, substituteFor: null, syncedFromMM: false };
                showToast(`✅ ${memberName} ditambahkan`, 'success');
            } else {
                const rec = { memberName, foto: photo, status: 'present', absentReason: '', isSubstitute: true, substituteFor: idx, syncedFromMM: false };
                const ex = assignments[key].findIndex(s => s.isSubstitute && s.substituteFor === idx);
                ex !== -1 ? assignments[key][ex] = rec : assignments[key].push(rec);
                showToast(`✅ ${memberName} sebagai pengganti`, 'success');
            }
            closeFinder(); markUnsaved(); render();
        }
        function removeSub(key, idx) {
            if (!assignments[key]?.[idx]) return;
            const name = assignments[key][idx].memberName;
            assignments[key].splice(idx, 1);
            markUnsaved(); render(); showToast(`↩ Pengganti ${name} dihapus`, 'warn');
        }

        // ══ SAVE / DISCARD ════════════════════════════════════════════════════════════
        function markUnsaved() { unsaved = true; updateSaveBar(); }
        function updateSaveBar() {
            const bar = document.getElementById('assignSaveBar');
            const n = Object.entries(assignments).filter(([k, v]) => JSON.stringify(v) !== JSON.stringify(savedAssignments[k] || [])).length;
            if (unsaved) { bar.classList.add('show'); document.getElementById('changeCount').textContent = n; }
            else bar.classList.remove('show');
        }
        async function saveAssignments() {
            showLoading();
            try {
                const res = await fetch('/api/assignment/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ tanggal: TANGGAL, factory: FACTORY, shift: SHIFT, assignments }),
                });
                const data = await res.json();
                hideLoading();
                if (data.ok) {
                    savedAssignments = JSON.parse(JSON.stringify(assignments));
                    unsaved = false; updateSaveBar();
                    if (data.summary) ['hadir', 'absen', 'butuh', 'terisi', 'kosong'].forEach(k => {
                        const el = document.getElementById('sum' + k.charAt(0).toUpperCase() + k.slice(1));
                        if (el) el.textContent = data.summary[k];
                    });
                    showToast('💾 Penugasan berhasil disimpan!', 'success');
                } else showToast('Gagal menyimpan', 'error');
            } catch (e) { hideLoading(); showToast('Error: ' + e.message, 'error'); }
        }
        function discardChanges() {
            if (!confirm('Batalkan semua perubahan?')) return;
            assignments = JSON.parse(JSON.stringify(savedAssignments));
            unsaved = false; activeFinder = null; closeFinder(); render();
            showToast('↩ Perubahan dibatalkan', 'warn');
        }

        // ══ DATE / SHIFT ══════════════════════════════════════════════════════════════
        async function onDateChange(val) {
            if (unsaved && !confirm('Ada perubahan belum disimpan. Ganti tanggal?')) {
                document.getElementById('tanggalHari').value = TANGGAL; return;
            }
            const u = new URL(window.location); u.searchParams.set('tanggal', val); window.location.href = u.toString();
        }
        async function switchShiftAssign(s) {
            if (unsaved && !confirm('Ada perubahan belum disimpan. Pindah shift?')) return;
            await api('/admin/context', 'POST', { factory: FACTORY, shift: s });
            window.location.reload();
        }

        // ══ SYNC ABSEN ════════════════════════════════════════════════════════════════
        async function syncAbsenFromServer() {
            try {
                const res = await fetch(`/api/assignment/sync-absen?tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`,
                    { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' } });
                const data = await res.json();
                if (data.synced > 0) {
                    const r2 = await fetch(`/api/assignment/data?tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`);
                    const d2 = await r2.json();
                    assignments = d2.assignments; savedAssignments = JSON.parse(JSON.stringify(assignments));
                    render(); showSync(`📡 ${data.synced} member tersinkronisasi dari Absen`);
                }
            } catch (e) { console.warn('syncAbsen', e); }
        }
        function showSync(msg) {
            const el = document.getElementById('syncStrip');
            document.getElementById('syncTxt').textContent = msg;
            el.classList.add('show'); setTimeout(() => el.classList.remove('show'), 5000);
        }

        // ══ TOAST / LOADING ═══════════════════════════════════════════════════════════
        function showToast(msg, type = 'info') {
            const t = document.getElementById('toastEl'); if (!t) return;
            t.textContent = msg; t.className = `toast ${type} show`;
            clearTimeout(t._timer); t._timer = setTimeout(() => t.classList.remove('show'), 2800);
        }
        function showLoading() { document.getElementById('loadingEl')?.classList.add('show'); }
        function hideLoading() { document.getElementById('loadingEl')?.classList.remove('show'); }

        // ══ KEYBOARD ══════════════════════════════════════════════════════════════════
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeAbsenModal();
            if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); if (unsaved) saveAssignments(); }
        });
        window.addEventListener('beforeunload', e => { if (unsaved) { e.preventDefault(); e.returnValue = ''; } });

        // ══ INIT ══════════════════════════════════════════════════════════════════════
        document.addEventListener('DOMContentLoaded', () => {
            render();
            updateSaveBar();
            syncAbsenFromServer();
            setInterval(syncAbsenFromServer, 60000);
        });
    </script>
@endpush