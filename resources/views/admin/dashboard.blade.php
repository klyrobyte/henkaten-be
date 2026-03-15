@extends('layouts.admin')
@section('title', 'Dashboard')

@push('styles')
    <style>
        /* ══ MACHINE SECTION ══════════════════════════════════════════════ */
        .machines-wrap {
            padding: 0 0 110px;
        }

        .machine-group-section {
            margin-bottom: 20px;
        }

        .machine-group-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2E7D32;
            color: #fff;
            padding: 6px 16px 6px 12px;
            border-radius: 0 20px 20px 0;
            font-family: 'Orbitron', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .8px;
            margin-bottom: 12px;
            margin-left: -12px;
            box-shadow: 2px 2px 8px rgba(31, 60, 136, .25);
        }

        .mg-badge {
            background: rgba(255, 255, 255, .2);
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .mg-badge.warn {
            background: rgba(231, 76, 60, .75);
        }

        /* CHANGE 1: Kartu mesin lebih lebar */
        .machine-cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        /* ══ MACHINE CARD ════════════════════════════════════════════════ */
        .mc-card {
            background: #fff;
            border-radius: 14px;
            border: 2px solid #e0ecd4;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: box-shadow .2s, border-color .2s, transform .15s;
            position: relative;
        }

        .mc-card:active {
            transform: scale(.975);
        }

        .mc-card.mc-status-man {
            border-color: #e74c3c;
            box-shadow: 0 3px 14px rgba(231, 76, 60, .22);
        }

        .mc-card.mc-status-machine {
            border-color: #1f3c88;
            box-shadow: 0 3px 14px rgba(31, 60, 136, .22);
        }

        .mc-card.mc-status-material {
            border-color: #f39c12;
            box-shadow: 0 3px 14px rgba(243, 156, 18, .22);
        }

        .mc-card.mc-status-method {
            border-color: #2e7d32;
            box-shadow: 0 3px 14px rgba(46, 125, 50, .22);
        }

        .mc-card.mc-has-absen {
            border-color: #e74c3c;
            box-shadow: 0 3px 18px rgba(231, 76, 60, .3);
        }

        .mc-photo-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, #eef2e8, #dde8c8);
            overflow: hidden;
            border-bottom: 1px solid #e0ecd4;
        }

        .mc-card.mc-has-absen .mc-photo-wrap,
        .mc-card.mc-status-man .mc-photo-wrap {
            background: linear-gradient(135deg, #fdeaea, #fad0d0);
            border-bottom-color: #f4a8a8;
        }

        .mc-photo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .3s;
        }

        .mc-card:hover .mc-photo-wrap img {
            transform: scale(1.04);
        }

        .mc-photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .mc-photo-placeholder .ph-ico {
            font-size: 28px;
            opacity: .25;
        }

        .mc-photo-placeholder .ph-txt {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #aaa;
        }

        .mc-photo-upload-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .0);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            cursor: pointer;
            transition: background .2s;
            z-index: 2;
            pointer-events: none;
        }

        .mc-photo-wrap:hover .mc-photo-upload-overlay {
            background: rgba(0, 0, 0, .45);
            pointer-events: all;
        }

        .mc-photo-upload-overlay .upload-ico {
            font-size: 20px;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity .2s, transform .2s;
            pointer-events: none;
        }

        .mc-photo-upload-overlay .upload-txt {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .5px;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity .2s .05s, transform .2s .05s;
            pointer-events: none;
        }

        .mc-photo-wrap:hover .upload-ico,
        .mc-photo-wrap:hover .upload-txt {
            opacity: 1;
            transform: translateY(0);
        }

        .mc-photo-wrap.uploading .mc-photo-upload-overlay {
            background: rgba(0, 0, 0, .55);
            pointer-events: none;
        }

        .mc-photo-wrap.uploading .upload-ico,
        .mc-photo-wrap.uploading .upload-txt {
            opacity: 1;
            transform: none;
        }

        .mc-name-badge {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, .6) 0%, transparent 100%);
            padding: 20px 8px 6px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            z-index: 3;
            pointer-events: none;
        }

        .mc-name-badge .mc-dot,
        .mc-name-badge .mc-4m-row {
            pointer-events: all;
        }

        .mc-name-txt {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .4px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, .5);
            line-height: 1;
        }

        .mc-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, .6);
            display: none;
            flex-shrink: 0;
            transition: transform .15s;
        }

        .mc-dot.d-visible {
            display: block;
        }

        .mc-dot.d-absen {
            display: block;
            background: #e74c3c;
            cursor: pointer;
            pointer-events: all;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, .4);
            animation: abpulse 1.3s ease-in-out infinite;
        }

        .mc-dot.d-absen:hover {
            transform: scale(1.8);
        }

        @keyframes abpulse {

            0%,
            100% {
                box-shadow: 0 0 0 2px rgba(231, 76, 60, .5);
            }

            50% {
                box-shadow: 0 0 0 7px rgba(231, 76, 60, .0);
            }
        }

        .mc-4m-row {
            display: flex;
            gap: 3px;
            align-items: center;
        }

        .mc-4m-pip {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .3);
        }

        .mc-card-body {
            padding: 8px 8px 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .mc-members-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
            min-height: 64px;
        }

        .mc-member-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            min-width: 56px;
        }

        /* CHANGE 2: Avatar lebih besar */
        .mc-av {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 900;
            color: #fff;
            overflow: hidden;
            border: 2.5px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
            font-family: 'Roboto Condensed', sans-serif;
            flex-shrink: 0;
        }

        .mc-av img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .av-ok {
            background: linear-gradient(135deg, #8bc34a, var(--green));
        }

        .av-absen {
            background: linear-gradient(135deg, #ef9a9a, #c0392b);
            filter: grayscale(.3);
        }

        /* CHANGE 3: Nama member lebih besar, tidak disingkat */
        .mc-member-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-align: center;
            color: #555;
            line-height: 1.2;
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
            max-width: 64px;
            width: 100%;
            word-break: break-word;
        }

        .mi-absen .mc-member-name {
            color: #c0392b;
        }

        .mc-member-tag {
            font-size: 7px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 800;
            padding: 1px 4px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .tag-hadir {
            background: var(--green);
            color: #fff;
        }

        .tag-absen {
            background: #e74c3c;
            color: #fff;
        }

        .tag-dipinjam {
            background: #607d8b;
            color: #fff;
        }

        .mi-dipinjam {
            opacity: .72;
        }

        .mi-dipinjam .mc-av {
            filter: grayscale(.55) brightness(.75);
            border-color: #b0bec5;
            box-shadow: none;
        }

        .mi-dipinjam .mc-member-name {
            color: #78909c;
        }

        .mi-dipinjam-dest {
            font-size: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            color: #546e7a;
            text-align: center;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 64px;
        }

        .mc-empty-slot {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #d0ddc0;
            border-radius: 10px;
            min-height: 64px;
            color: #ccc;
            font-size: 22px;
            width: 100%;
        }

        /* ── STATUS PILLS ─────────────────────────────────────────────── */
        .mc-status-row {
            display: flex;
            gap: 3px;
            flex-wrap: wrap;
            justify-content: center;
            padding-top: 5px;
            border-top: 1px solid #eef2e8;
        }

        .mc-status-pill {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 4px 7px;
            border-radius: 20px;
            border: 1.5px solid #e4e4e4;
            background: #f7f7f7;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #aaa;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            line-height: 1;
        }

        .pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .mc-status-pill.p-normal.active {
            background: #e8f5e9;
            border-color: var(--green);
            color: var(--green);
        }

        .mc-status-pill.p-man.active {
            background: #fdeaea;
            border-color: #e74c3c;
            color: #e74c3c;
        }

        .mc-status-pill.p-material.active {
            background: #fff8e1;
            border-color: #f39c12;
            color: #c67c00;
        }

        .mc-status-pill.p-machine.active {
            background: #e8eefa;
            border-color: #1f3c88;
            color: #1f3c88;
        }

        .mc-status-pill.p-method.active {
            background: #e8f5e9;
            border-color: #2e7d32;
            color: #2e7d32;
        }

        .pill-dot.dn {
            background: #4caf50;
        }

        .pill-dot.dm {
            background: #e74c3c;
        }

        .pill-dot.dt {
            background: #f39c12;
        }

        .pill-dot.dc {
            background: #1f3c88;
        }

        .pill-dot.dme {
            background: #2e7d32;
        }

        .mc-addlog-bar {
            display: flex;
            justify-content: center;
            padding: 4px 0 2px;
        }

        .mc-addlog-btn {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 3px 12px;
            border-radius: 20px;
            border: 1.5px dashed #ccc;
            background: transparent;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #bbb;
            cursor: pointer;
            transition: all .15s;
        }

        .mc-addlog-btn:hover {
            border-color: var(--orange, #e65100);
            color: var(--orange, #e65100);
            background: rgba(230, 81, 0, .06);
            transform: scale(1.05);
        }

        /* Quick Log modal */
        .ql-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px 12px;
            border-bottom: 1.5px solid #f0f0f0;
        }

        .ql-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ql-header-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--orange, #e65100), #ff7043);
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(230, 81, 0, .3);
        }

        .ql-header-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #222;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .ql-header-sub {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--navy, #1f3c88);
            background: #eef1fa;
            padding: 2px 8px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 3px;
        }

        /* Finder modal */
        .finder-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            z-index: 1100;
            display: none;
            align-items: flex-end;
            justify-content: center;
        }

        .finder-overlay.show {
            display: flex;
        }

        .finder-sheet {
            background: #fff;
            border-radius: 20px 20px 0 0;
            width: 100%;
            max-width: 560px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 -4px 30px rgba(0, 0, 0, .2);
            animation: slideUp .25s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(60px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .finder-handle {
            width: 44px;
            height: 5px;
            background: #ddd;
            border-radius: 3px;
            margin: 12px auto 0;
            flex-shrink: 0;
        }

        .finder-ph {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            padding: 14px 18px 16px;
            color: #fff;
            flex-shrink: 0;
            position: relative;
        }

        .finder-ph h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 13px;
            font-weight: 900;
            margin: 0 0 2px;
        }

        .finder-ph p {
            font-size: 11px;
            opacity: .85;
            margin: 0;
        }

        .finder-ph-machine {
            background: rgba(255, 255, 255, .2);
            border-radius: 6px;
            padding: 3px 10px;
            margin-top: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }

        .finder-close-btn {
            position: absolute;
            top: 14px;
            right: 16px;
            background: rgba(255, 255, 255, .25);
            border: none;
            color: #fff;
            font-size: 18px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .finder-search-wrap {
            padding: 12px 14px;
            border-bottom: 1px solid #eee;
            flex-shrink: 0;
        }

        .finder-search-wrap input {
            width: 100%;
            padding: 9px 12px;
            border: 2px solid var(--green-light);
            border-radius: 10px;
            font-size: 13px;
            box-sizing: border-box;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .finder-search-wrap input:focus {
            outline: none;
            border-color: var(--green);
        }

        .finder-cand-list {
            overflow-y: auto;
            padding: 10px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            flex: 1;
        }

        .cand-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 10px 6px 8px;
            border-radius: 10px;
            border: 2px solid var(--green-light);
            background: #f8faf5;
            cursor: pointer;
            transition: all .15s;
            text-align: center;
        }

        .cand-card:active {
            border-color: var(--orange);
            transform: scale(.96);
        }

        .cand-av {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
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

        .cav-n {
            background: linear-gradient(135deg, var(--green-light), var(--green));
        }

        .cav-w {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            border-color: #f39c12;
        }

        .cand-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.2;
        }

        .cand-tag {
            font-size: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 3px;
            border: 1px solid #f39c12;
            color: #f39c12;
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
            font-size: 10px;
            cursor: pointer;
        }

        .cand-empty {
            text-align: center;
            padding: 24px 10px;
            color: #aaa;
            grid-column: 1/-1;
            font-size: 12px;
        }

        #qlLogList {
            max-height: 230px;
            overflow-y: auto;
            margin-bottom: 4px;
        }

        .ql-log-item {
            border-radius: 10px;
            border: 1.5px solid #eee;
            padding: 8px 10px;
            margin-bottom: 6px;
            background: #fafafa;
        }

        .ql-log-open {
            border-left: 3px solid #e74c3c;
        }

        .ql-log-closed {
            border-left: 3px solid #4caf50;
            opacity: .75;
        }

        .ql-log-top {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }

        .ql-log-badge {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 9px;
            font-weight: 900;
            padding: 2px 7px;
            border-radius: 5px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .ql-log-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dot-open {
            background: #e74c3c;
            animation: pulse-ql 1.5s infinite;
        }

        .dot-closed {
            background: #4caf50;
        }

        @keyframes pulse-ql {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, .4);
            }

            50% {
                box-shadow: 0 0 0 4px rgba(231, 76, 60, 0);
            }
        }

        .ql-log-time {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            color: #888;
            font-weight: 600;
        }

        .ql-log-dur {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            color: #aaa;
            margin-left: auto;
        }

        .ql-log-dur.blink {
            color: #e74c3c;
            font-weight: 800;
            animation: blink-ql .9s step-end infinite;
        }

        @keyframes blink-ql {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .3
            }
        }

        .ql-log-desc {
            font-size: 11px;
            color: #555;
            line-height: 1.4;
            margin-bottom: 5px;
        }

        .ql-log-meta {
            font-size: 10px;
            color: #888;
            margin-bottom: 2px;
        }

        .ql-log-actions {
            display: flex;
            gap: 5px;
        }

        .ql-btn {
            padding: 4px 10px;
            border-radius: 7px;
            border: 1.5px solid #ddd;
            background: #f5f5f5;
            font-size: 10px;
            font-weight: 800;
            font-family: 'Roboto Condensed', sans-serif;
            cursor: pointer;
            transition: all .15s;
        }

        .ql-btn-done {
            border-color: #a5d6a7;
            color: #2e7d32;
            background: #f1f8e9;
        }

        .ql-btn-done:hover {
            background: #c8e6c9;
        }

        .ql-btn-reopen {
            border-color: #90caf9;
            color: #1565c0;
            background: #e3f2fd;
        }

        .ql-btn-reopen:hover {
            background: #bbdefb;
        }

        .ql-btn-del {
            border-color: #ffcdd2;
            color: #c62828;
            background: #fff5f5;
            margin-left: auto;
        }

        .ql-btn-del:hover {
            background: #ffcdd2;
        }

        .jenis-selector {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .jenis-btn {
            flex: 1;
            padding: 12px 8px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            background: #f9f9f9;
            cursor: pointer;
            text-align: center;
            transition: all .18s;
            font-family: 'Roboto Condensed', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        .jenis-btn .jb-icon {
            font-size: 22px;
            display: block;
            margin-bottom: 4px;
        }

        .jenis-btn .jb-lbl {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #888;
        }

        .jenis-btn.sel-machine {
            border-color: #1f3c88;
            background: #eef1fa;
        }

        .jenis-btn.sel-machine .jb-lbl {
            color: #1f3c88;
        }

        .jenis-btn.sel-material {
            border-color: #f39c12;
            background: #fff8ec;
        }

        .jenis-btn.sel-material .jb-lbl {
            color: #f39c12;
        }

        .jenis-btn.sel-method {
            border-color: #2e7d32;
            background: #f1f8e9;
        }

        .jenis-btn.sel-method .jb-lbl {
            color: #2e7d32;
        }

        .ql-form-section {
            display: none;
            animation: qlFadeIn .2s;
        }

        .ql-form-section.visible {
            display: block;
        }

        @keyframes qlFadeIn {
            from {
                opacity: 0;
                transform: translateY(4px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .ql-shb {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 12px;
        }

        .ql-shb-machine {
            background: #eef1fa;
            color: #1f3c88;
            border: 1.5px solid #c5cae9;
        }

        .ql-shb-material {
            background: #fff8ec;
            color: #e67e22;
            border: 1.5px solid #ffe0b2;
        }

        .ql-shb-method {
            background: #f1f8e9;
            color: #2e7d32;
            border: 1.5px solid #c8e6c9;
        }

        .ql-form-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 12px 0 10px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #bbb;
        }

        .ql-form-divider::before,
        .ql-form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #eee;
        }

        #qlSaveBtnWrap {
            display: none;
        }

        .save-btn-big {
            width: 100%;
            padding: 13px 16px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--orange, #e65100), #ff7043);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(230, 81, 0, .35);
            transition: opacity .15s, transform .15s;
        }

        .save-btn-big:active {
            opacity: .85;
            transform: scale(.98);
        }

        .save-btn-big:disabled {
            opacity: .55;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')

    <div class="date-bar"
        style="display:flex;align-items:center;background:#fff;border-radius:50px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,0.08);gap:12px;">

        {{-- Icon kalender — klik ini untuk buka date picker --}}
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

        {{-- Input date tersembunyi — hanya trigger via icon --}}
        <input type="date" id="tanggalHari" value="{{ $tanggal }}" onchange="onDateChange(this.value)"
            style="position:absolute;opacity:0;pointer-events:none;width:0;height:0;">

        {{-- Factory badge --}}
        <button onclick="showFactoryPicker()" style="background:#2E7D32;border:none;border-radius:20px;padding:7px 18px;color:#fff;
                                               font-family:'Orbitron', sans-serif;font-weight:700;font-size:12px;
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
            FACTORY {{ session('factory', 'Factory 2') === 'Factory 2' ? '2' : '3&4' }}
        </button>
    </div>

    <div class="shift-toggle-bar">
        @php $userShift = auth()->user()->shift; $isAdmin = auth()->user()->role === 'admin'; @endphp
        @if($isAdmin || !$userShift || $userShift === 'A')
        <button class="shift-toggle-btn {{ $shift === 'A' ? 'active' : '' }}" onclick="switchShift('A')">SHIFT A</button>
        @endif
        @if($isAdmin || !$userShift || $userShift === 'B')
        <button class="shift-toggle-btn {{ $shift === 'B' ? 'active' : '' }}" onclick="switchShift('B')">SHIFT B</button>
        @endif
    </div>

    <div class="legend-4m" style="display:inline-flex;
                                        align-items:center;
                                        gap:16px;
                                        background:#fff;
                                        border-radius:50px;
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

    <div class="status-panel">
        <div class="status-panel-title">STATUS KESELURUHAN</div>
        <div class="auto-refresh-bar">
            <div class="ar-left">
                <div class="ar-dot" id="arDot"></div>
                <span id="arLabel">Auto Refresh</span>
            </div>
            <span class="ar-countdown" id="arCountdown">15s</span>
            <button class="ar-toggle" id="arToggleBtn" onclick="toggleAutoRefresh()">⏸ Pause</button>
        </div>
        <div class="ar-last-updated" id="arLastUpdated">Belum diperbarui</div>
        <div class="status-emot-row">
            <div class="emot-card {{ $statusLevel === 0 ? 'active-green' : '' }}" id="ec-green">
                <span class="emot-icon"></span>
                <div class="emot-label">AMAN</div>
            </div>
            <div class="emot-card {{ $statusLevel === 1 ? 'active-yellowgreen' : '' }}" id="ec-yg">
                <span class="emot-icon"></span>
                <div class="emot-label">RINGAN</div>
            </div>
            <div class="emot-card {{ $statusLevel === 2 ? 'active-yellow' : '' }}" id="ec-yellow">
                <span class="emot-icon"></span>
                <div class="emot-label">KHUSUS</div>
            </div>
            <div class="emot-card {{ $statusLevel === 3 ? 'active-red' : '' }}" id="ec-red">
                <span class="emot-icon"></span>
                <div class="emot-label">BAHAYA</div>
            </div>
        </div>
        @php
            $chipMap = ['chip-green', 'chip-yellowgreen', 'chip-yellow', 'chip-red'];
            $chipTxt = [
                'AMAN — Semua Normal',
                'PERHATIAN RINGAN — Absen 1',
                'PERHATIAN KHUSUS — Absen 2–3 / Ada Problem',
                'BAHAYA — Absen ≥4 / MC Problem ≥2',
            ];
        @endphp
        <div class="status-chip {{ $chipMap[$statusLevel] }}" id="statusChip">{{ $chipTxt[$statusLevel] }}</div>
        <div class="status-live-row">
            <div class="live-item">
                <div class="live-value" id="liveAbsen">{{ $absenceSummary?->total_absen ?? 0 }}</div>
                <div class="live-label">Absen MP</div>
            </div>
            <div class="live-item">
                <div class="live-value" id="liveMCProblem">{{ $machineSummary['problem'] }}</div>
                <div class="live-label">MC Problem</div>
            </div>
            <div class="live-item">
                <div class="live-value" id="liveLogOpen">{{ $openLogsCount }}</div>
                <div class="live-label">Log Open</div>
            </div>
        </div>
    </div>

    <div class="section-title">Report Summary</div>
    <div class="summary-grid">
        <div class="summary-card">
            <div class="s-val" id="sTotalMC">{{ $machineSummary['total'] }}</div>
            <div class="s-lbl">Total MC</div>
        </div>
        <div class="summary-card">
            <div class="s-val" id="sTotalMP">{{ $totalMP }}</div>
            <div class="s-lbl">Total MP</div>
        </div>
        <div class="summary-card" style="border-bottom-color:var(--red)">
            <div class="s-val" id="sMan" style="color:var(--red)">{{ $machineSummary['man'] }}</div>
            <div class="s-lbl">Man</div>
        </div>
        <div class="summary-card" style="border-bottom-color:var(--navy)">
            <div class="s-val" id="sMachine" style="color:var(--navy)">{{ $machineSummary['machine'] }}</div>
            <div class="s-lbl">Machine</div>
        </div>
        <div class="summary-card" style="border-bottom-color:var(--yellow)">
            <div class="s-val" id="sMaterial" style="color:var(--yellow)">{{ $machineSummary['material'] }}</div>
            <div class="s-lbl">Material</div>
        </div>
        <div class="summary-card" style="border-bottom-color:var(--green-light)">
            <div class="s-val" id="sMethod" style="color:var(--green-light)">{{ $machineSummary['method'] }}</div>
            <div class="s-lbl">Method</div>
        </div>
    </div>

    <div class="section-title">Diagram Kehadiran</div>
    <div class="attendance-chart-wrap">
        <div class="chart-container">
            <canvas id="myChart"></canvas>
            <div class="chart-center">
                <div class="cv" id="centerValue">
                    {{ $absenceSummary ? $absenceSummary->mp_hadir . '/' . $absenceSummary->total_member : '0/0' }}
                </div>
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
            <div class="absen-bar">
                @php
                    $pct = ($absenceSummary && $absenceSummary->total_member > 0)
                        ? round($absenceSummary->mp_hadir / $absenceSummary->total_member * 100, 1)
                        : 0;
                @endphp
                <div class="absen-fill" id="absenFill" style="width:{{ $pct }}%">{{ $pct }}%</div>
            </div>
        </div>
    </div>

    <div class="section-title" style="margin-top:16px">Status Mesin</div>
    <div class="legend-4m" style="margin-bottom:10px">
        <div class="legend-4m-item">
            <div class="l4m-dot" style="background:#e74c3c"></div>Man
        </div>
        <div class="legend-4m-item">
            <div class="l4m-dot" style="background:#1f3c88"></div>Machine
        </div>
        <div class="legend-4m-item">
            <div class="l4m-dot" style="background:#f39c12"></div>Material
        </div>
        <div class="legend-4m-item">
            <div class="l4m-dot" style="background:var(--green)"></div>Method
        </div>
        <div class="legend-4m-item" style="color:#e74c3c;font-weight:700;font-size:11px">● = ada absen (klik)</div>
        <div class="legend-4m-item" style="color:#607d8b;font-weight:700;font-size:11px">▨ = tugas di mesin lain</div>
    </div>

    <div class="machines-wrap">
        @php
            $absenRecords = \App\Models\AbsenceRecord::where([
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
                'status' => 'absen',
            ])->get()->keyBy('member_id');
            $absenIds = $absenRecords->keys()->toArray();
            $absenReasons = $absenRecords->map(fn($r) => $r->reason)->toArray();

            try {
                $replacements = \App\Models\AssignmentReplacement::where([
                    'tanggal' => $tanggal,
                    'factory' => $factory,
                    'shift' => $shift,
                ])->get()->keyBy('member_id');
            } catch (\Throwable $e) {
                $replacements = collect();
            }

            $replacedMachines = \App\Models\AssignmentReplacement::where([
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
            ])->pluck('target_machine')->toArray();

            $pipColors = [
                'man' => '#e74c3c',
                'machine' => '#1f3c88',
                'material' => '#f39c12',
                'method' => '#2e7d32',
            ];

            $pillDefs = [
                'normal' => ['dot' => null, 'label' => 'Normal'],
                'man' => ['dot' => 'dm', 'label' => 'Man'],
                'material' => ['dot' => 'dt', 'label' => 'Matl'],
                'machine' => ['dot' => 'dc', 'label' => 'Mchn'],
                'method' => ['dot' => 'dme', 'label' => 'Mthd'],
            ];
        @endphp

        @foreach($groups as $group)
            @php
                $absenMesinCount = 0;
                foreach ($group['machines'] as $mac) {
                    $assigned = $members->filter(fn($m) => $m->mesin === $mac);
                    $macHasAbsen = $assigned->whereIn('id', $absenIds)->isNotEmpty();
                    $macHasReplacement = in_array($mac, $replacedMachines);
                    if ($macHasAbsen && !$macHasReplacement)
                        $absenMesinCount++;
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
                            $st = $statuses[$machine] ?? null;
                            $stVal = $st?->status ?? 'normal';
                            $stAll = $st?->statuses ?? [];

                            $assigned = $members->filter(fn($m) => $m->mesin === $machine);
                            $absenMemberIds = $assigned->whereIn('id', $absenIds)->pluck('id');
                            $machineHasRepl = in_array($machine, $replacedMachines);
                            $hasAbsen = $absenMemberIds->isNotEmpty();
                            $needsFinder = $hasAbsen && !$machineHasRepl;

                            $cardCls = $needsFinder
                                ? 'mc-has-absen'
                                : (!$hasAbsen && $stVal !== 'normal' ? 'mc-status-' . $stVal : '');

                            $machineRecord = $machinePhotos[$machine] ?? null;
                            $machinePhoto = $machineRecord?->photo_url ?? null;
                            $machineSlug = Str::slug($machine);

                            $initPips = [];
                            if ($hasAbsen)
                                $initPips[] = 'man';
                            foreach ($stAll as $s) {
                                if ($s !== 'man' && !in_array($s, $initPips))
                                    $initPips[] = $s;
                            }

                            $dotInitClass = $hasAbsen ? 'd-absen' : ($stVal !== 'normal' ? 'd-visible' : '');
                            $dotInitBg = (!$hasAbsen && $stVal !== 'normal') ? ($pipColors[$stVal] ?? '') : '';

                            $activePills = [];
                            if ($hasAbsen)
                                $activePills[] = 'man';
                            foreach ($stAll as $s) {
                                if (!in_array($s, $activePills))
                                    $activePills[] = $s;
                            }
                            if (empty($activePills))
                                $activePills[] = 'normal';
                        @endphp

                        <div class="mc-card {{ $cardCls }}" data-machine="{{ $machine }}" data-status="{{ $stVal }}"
                            onclick="handleCardClick(event,'{{ addslashes($machine) }}','{{ $stVal }}')">

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

                                <div class="mc-photo-upload-overlay"
                                    onclick="event.stopPropagation(); triggerPhotoUpload('{{ addslashes($machine) }}','{{ $machineSlug }}')"
                                    title="Ganti foto mesin">
                                    <span class="upload-ico">📷</span>
                                    <span class="upload-txt">{{ $machinePhoto ? 'Ganti Foto' : 'Upload Foto' }}</span>
                                </div>

                                <input type="file" accept="image/*" id="file-{{ $machineSlug }}" style="display:none"
                                    onchange="uploadMachinePhoto(event,'{{ addslashes($machine) }}','{{ $machineSlug }}')">

                                <div class="mc-name-badge">
                                    <span class="mc-name-txt">{{ $machine }}</span>
                                    <div style="display:flex;align-items:center;gap:4px">
                                        <div class="mc-4m-row" id="lights-{{ $machineSlug }}">
                                            @foreach($initPips as $pip)
                                                <div class="mc-4m-pip" style="background:{{ $pipColors[$pip] ?? '#ccc' }}"
                                                    title="{{ $pip }}"></div>
                                            @endforeach
                                        </div>
                                        <div class="mc-dot {{ $dotInitClass }}" id="dot-{{ $machineSlug }}" @if($dotInitBg)
                                        style="background:{{ $dotInitBg }}" @endif @if($hasAbsen && $needsFinder)
                                                onclick="event.stopPropagation();openFinderModal('{{ addslashes($machine) }}')"
                                            @elseif(!$hasAbsen && $stVal !== 'normal')
                                                onclick="event.stopPropagation();openMachineDetail('{{ addslashes($machine) }}','{{ $stVal }}')"
                                            @endif></div>
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
                                                $isAbsen = in_array($m->id, $absenIds);
                                                $isDipinjam = !$isAbsen && isset($replacements[$m->id]);
                                                $destMachine = $isDipinjam ? ($replacements[$m->id]->target_machine ?? '?') : null;
                                                $itemCls = $isAbsen ? 'mi-absen' : ($isDipinjam ? 'mi-dipinjam' : '');
                                                $avCls = $isAbsen ? 'av-absen' : 'av-ok';
                                            @endphp
                                            <div class="mc-member-item {{ $itemCls }}" data-member-id="{{ $m->id }}">
                                                <div class="mc-av {{ $avCls }}">
                                                    @if($m->photo_url)
                                                        <img src="{{ $m->photo_url }}" alt="{{ $m->nama }}" @if($isAbsen)
                                                        style="filter:grayscale(.5) brightness(.8)" @elseif($isDipinjam)
                                                            style="filter:grayscale(.55) brightness(.72)" @endif>
                                                    @else
                                                        {{ mb_strtoupper(mb_substr($m->nama, 0, 1)) . (str_contains($m->nama, ' ') ? mb_strtoupper(mb_substr(explode(' ', $m->nama)[1], 0, 1)) : '') }}
                                                    @endif
                                                </div>
                                                {{-- CHANGE: Nama lengkap penuh tanpa singkatan --}}
                                                <div class="mc-member-name" title="{{ $m->nama }}">
                                                    {{ $m->nama }}
                                                </div>
                                                @if($isAbsen)
                                                    @php
                                                        $absenReason = strtolower($absenReasons[$m->id] ?? '');
                                                        $absenLabel = match (true) {
                                                            str_contains($absenReason, 'sakit') => 'SAKIT',
                                                            str_contains($absenReason, 'izin') || str_contains($absenReason, 'ijin') => 'IZIN',
                                                            str_contains($absenReason, 'cuti') => 'CUTI',
                                                            default => 'Absen',
                                                        };
                                                    @endphp
                                                    <span class="mc-member-tag tag-absen">{{ $absenLabel }}</span>
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

                                <div class="mc-status-row">
                                    @foreach($pillDefs as $pKey => $pDef)
                                        <span class="mc-status-pill p-{{ $pKey }} {{ in_array($pKey, $activePills) ? 'active' : '' }}"
                                            data-status="{{ $pKey }}">
                                            @if($pDef['dot'])
                                                <span class="pill-dot {{ $pDef['dot'] }}"></span>
                                            @endif
                                            {{ $pDef['label'] }}
                                        </span>
                                    @endforeach
                                </div>

                                <div class="mc-addlog-bar" onclick="event.stopPropagation()">
                                    <button class="mc-addlog-btn" onclick="openQuickLog('{{ addslashes($machine) }}')">
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

    {{-- Quick Add Log modal --}}
    <div class="modal-overlay" id="quickLogOverlay">
        <div class="modal-sheet" style="max-height:94vh;display:flex;flex-direction:column;">
            <div class="modal-sheet-handle" style="flex-shrink:0"></div>
            <div class="ql-header" style="flex-shrink:0">
                <div class="ql-header-left">
                    <div class="ql-header-icon">＋</div>
                    <div>
                        <div class="ql-header-title">Tambah Problem Log</div>
                        <div class="ql-header-sub" id="qlMesinLabel">—</div>
                    </div>
                </div>
                <button class="modal-sheet-close" onclick="closeQuickLog()">✕</button>
                <input type="hidden" id="qlLokasi">
            </div>
            <div class="modal-sheet-body" style="padding-top:6px;overflow-y:auto;flex:1;min-height:0;">
                <div id="qlLogList" style="display:none"></div>
                <div class="ql-form-divider">Pilih Tipe Masalah</div>
                <div class="jenis-selector">
                    <button type="button" class="jenis-btn" id="ql-btn-machine" onclick="qlSelectJenis('Machine')">
                        <span class="jb-icon">⚙️</span><span class="jb-lbl">Machine</span>
                    </button>
                    <button type="button" class="jenis-btn" id="ql-btn-material" onclick="qlSelectJenis('Material')">
                        <span class="jb-icon">📦</span><span class="jb-lbl">Material</span>
                    </button>
                    <button type="button" class="jenis-btn" id="ql-btn-method" onclick="qlSelectJenis('Method')">
                        <span class="jb-icon">📋</span><span class="jb-lbl">Method</span>
                    </button>
                </div>

                <div class="ql-form-section" id="ql-section-machine">
                    <div class="ql-shb ql-shb-machine">⚙️ Machine Problem</div>
                    <div class="form-row">
                        <div class="field-group"><label>Waktu Mulai *</label><input type="time" id="qlMulai"></div>
                        <div class="field-group">
                            <label>Status</label>
                            <select id="ql-m-status" onchange="qlToggleSelesai('m',this.value)">
                                <option value="open">Open — belum selesai</option>
                                <option value="closed">Closed — sudah selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group" id="ql-m-selesai-wrap" style="display:none">
                        <label>Waktu Selesai</label><input type="time" id="ql-m-selesai">
                    </div>
                    <div class="field-group">
                        <label>Deskripsi Kerusakan *</label>
                        <textarea id="qlDeskripsi" rows="2" placeholder="Contoh: MC mati mendadak, bunyi abnormal…"
                            style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                            onfocus="this.style.borderColor='#1f3c88'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>Root Cause</label><input type="text" id="qlCause"
                                placeholder="Contoh: bearing aus…"></div>
                        <div class="field-group"><label>Teknisi / PIC</label><input type="text" id="qlPIC"
                                placeholder="Nama teknisi"></div>
                    </div>
                </div>

                <div class="ql-form-section" id="ql-section-material">
                    <div class="ql-shb ql-shb-material">📦 Material Problem</div>
                    <div class="form-row">
                        <div class="field-group"><label>Waktu Mulai *</label><input type="time" id="ql-mat-mulai"></div>
                        <div class="field-group">
                            <label>Status</label>
                            <select id="ql-mat-status" onchange="qlToggleSelesai('mat',this.value)">
                                <option value="open">Open — belum selesai</option>
                                <option value="closed">Closed — sudah selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group" id="ql-mat-selesai-wrap" style="display:none">
                        <label>Waktu Selesai</label><input type="time" id="ql-mat-selesai">
                    </div>
                    <div class="field-group">
                        <label>Deskripsi Masalah Material *</label>
                        <textarea id="ql-mat-deskripsi" rows="2"
                            placeholder="Contoh: material short shot, warna tidak sesuai…"
                            style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                            onfocus="this.style.borderColor='#f39c12'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>No. Lot / Batch</label><input type="text" id="ql-mat-cause"
                                placeholder="No. lot material bermasalah"></div>
                        <div class="field-group"><label>PIC</label><input type="text" id="ql-mat-pic"
                                placeholder="Nama penanggung jawab"></div>
                    </div>
                </div>

                <div class="ql-form-section" id="ql-section-method">
                    <div class="ql-shb ql-shb-method">📋 Method Problem</div>
                    <div class="form-row">
                        <div class="field-group"><label>Waktu Mulai *</label><input type="time" id="ql-met-mulai"></div>
                        <div class="field-group">
                            <label>Status</label>
                            <select id="ql-met-status" onchange="qlToggleSelesai('met',this.value)">
                                <option value="open">Open — belum selesai</option>
                                <option value="closed">Closed — sudah selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group" id="ql-met-selesai-wrap" style="display:none">
                        <label>Waktu Selesai</label><input type="time" id="ql-met-selesai">
                    </div>
                    <div class="field-group">
                        <label>Deskripsi Penyimpangan *</label>
                        <textarea id="ql-met-deskripsi" rows="2"
                            placeholder="Contoh: setting tidak sesuai standar, tidak mengikuti SOP…"
                            style="width:100%;padding:10px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box"
                            onfocus="this.style.borderColor='#2e7d32'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>Standar yang Dilanggar</label><input type="text" id="ql-met-cause"
                                placeholder="Contoh: suhu resin, cycle time SOP…"></div>
                        <div class="field-group"><label>PIC</label><input type="text" id="ql-met-pic"
                                placeholder="Nama penanggung jawab"></div>
                    </div>
                </div>
            </div>

            <div style="flex-shrink:0;padding:10px 16px 16px;border-top:1px solid #f0f0f0;background:#fff;"
                id="qlSaveBtnWrap">
                <button class="save-btn-big" id="qlSubmitBtn" onclick="submitQuickLog()">💾 Simpan Log</button>
            </div>
        </div>
    </div>

    {{-- Finder pengganti --}}
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
        Chart.register(ChartDataLabels);

        const initAbsence = @json($absenceSummary);
        const TANGGAL = '{{ $tanggal }}';
        const FACTORY = CURRENT_FACTORY;
        const SHIFT = CURRENT_SHIFT;
        const CSRF = '{{ csrf_token() }}';

        let dashChart = null;
        let _qlMachine = null;

        function esc(s) { return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
        function initials(n) { if (!n) return '?'; const p = n.trim().split(' '); return p.length >= 2 ? (p[0][0] + p[1][0]).toUpperCase() : n.slice(0, 2).toUpperCase(); }
        const $id = id => document.getElementById(id);

        const colorMap = { man: '#e74c3c', machine: '#1f3c88', material: '#f39c12', method: '#2e7d32' };
        const borderCls = { man: 'mc-status-man', machine: 'mc-status-machine', material: 'mc-status-material', method: 'mc-status-method' };

        /* ── 1. SYNC SUMMARY ── */
        async function syncSummary() {
            try {
                const d = $id('tanggalHari').value;
                const url = `{{ route('admin.status') }}?tanggal=${d}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`;
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();

                if ($id('liveAbsen')) $id('liveAbsen').textContent = data.total_absen ?? 0;
                if ($id('liveMCProblem')) $id('liveMCProblem').textContent = data.problem_mc ?? 0;
                if ($id('liveLogOpen')) $id('liveLogOpen').textContent = data.open_logs ?? 0;
                if ($id('sTotalMC')) $id('sTotalMC').textContent = data.summary?.total ?? 0;
                if ($id('sTotalMP')) $id('sTotalMP').textContent = data.total_mp ?? 0;
                if ($id('sMan')) $id('sMan').textContent = data.summary?.man ?? 0;
                if ($id('sMachine')) $id('sMachine').textContent = data.summary?.machine ?? 0;
                if ($id('sMaterial')) $id('sMaterial').textContent = data.summary?.material ?? 0;
                if ($id('sMethod')) $id('sMethod').textContent = data.summary?.method ?? 0;

                const lv = data.status_level ?? 0;
                const ids = ['ec-green', 'ec-yg', 'ec-yellow', 'ec-red'];
                const cls = ['active-green', 'active-yellowgreen', 'active-yellow', 'active-red'];
                const lbls = [
                    '🟢 AMAN — Semua Normal',
                    '🟡 PERHATIAN RINGAN — Absen 1',
                    '⚠️ PERHATIAN KHUSUS — Absen 2–3 / Ada Problem',
                    '🔴 BAHAYA — Absen ≥4 / MC Problem ≥2',
                ];
                ids.forEach((id, i) => { const e = $id(id); if (!e) return; e.classList.remove(...cls); if (i === lv) e.classList.add(cls[i]); });
                const chip = $id('statusChip');
                if (chip) { chip.className = `status-chip ${chips[lv]}`; chip.textContent = lbls[lv]; }
                if (data.absence) renderChart(data.absence);
                const ts = $id('arLastUpdated');
                if (ts) ts.textContent = `Diperbarui: ${data.updated_at}`;
            } catch (e) { console.error('syncSummary', e); }
        }

        /* ── 2. SYNC MACHINE CARDS ── */
        let _syncing = false;
        async function syncCards() {
            if (_syncing) return;
            _syncing = true;
            try {
                const qs = `tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`;
                const [absRes, replRes, logRes] = await Promise.all([
                    fetch(`/admin/absence/data?${qs}`, { headers: { Accept: 'application/json' } }),
                    fetch(`/admin/replacements?${qs}`, { headers: { Accept: 'application/json' } }),
                    fetch(`/admin/logs/list?${qs}`, { headers: { Accept: 'application/json' } }),
                ]);
                const absData = absRes.ok ? await absRes.json() : {};
                const replData = replRes.ok ? await replRes.json() : [];
                const logData = logRes.ok ? await logRes.json() : [];

                const openLogMap = {};
                logData.filter(l => l.status === 'open').forEach(l => {
                    if (!openLogMap[l.lokasi]) openLogMap[l.lokasi] = [];
                    const t = (l.jenis || '').toLowerCase();
                    if (t && !openLogMap[l.lokasi].includes(t)) openLogMap[l.lokasi].push(t);
                });

                const activReplIds = new Set(replData.map(r => r.member_id));
                const replacedMachines = new Set(replData.map(r => r.target_machine));
                const absenIds = new Set(
                    Object.entries(absData).filter(([, v]) => v.status === 'absen').map(([id]) => parseInt(id))
                );

                document.querySelectorAll('.mc-card').forEach(card => {
                    const machine = card.dataset.machine;
                    if (!machine) return;

                    const ownItems = [...card.querySelectorAll('.mc-member-item:not([data-replacement="1"])')];
                    const absenItems = ownItems.filter(el => absenIds.has(parseInt(el.dataset.memberId)));
                    const cardHasAbsen = absenItems.length > 0;
                    const hasReplacement = card.querySelectorAll('.mc-member-item[data-replacement="1"]').length > 0
                        || replacedMachines.has(machine);
                    const cardNeedsFinder = cardHasAbsen && !hasReplacement;

                    if (!cardHasAbsen) card.querySelectorAll('.mc-member-item[data-replacement="1"]').forEach(el => el.remove());

                    // ── Inject pengganti baru yang belum ada di DOM ──
                    if (cardHasAbsen) {
                        const row = card.querySelector('.mc-members-row');
                        const repl = replData.filter(r => r.target_machine === machine);
                        repl.forEach(r => {
                            if (!row) return;
                            const exists = row.querySelector(`.mc-member-item[data-member-id="${r.member_id}"][data-replacement="1"]`);
                            if (!exists) {
                                row.querySelector('.mc-empty-slot')?.remove();
                                const div = document.createElement('div');
                                div.className = 'mc-member-item';
                                div.dataset.memberId = r.member_id;
                                div.dataset.replacement = '1';
                                const avContent = r.member_photo
                                    ? `<img src="${esc(r.member_photo)}" alt="${esc(r.member_name)}">`
                                    : `<span>${initials(r.member_name)}</span>`;
                                div.innerHTML = `<div class="mc-av av-ok">${avContent}</div>`
                                    + `<div class="mc-member-name">${esc(r.member_name || '')}</div>`
                                    + `<span class="mc-member-tag" style="background:#e65100;color:#fff;font-size:7px;`
                                    + `padding:1px 4px;border-radius:3px;text-transform:uppercase;font-weight:800">Pengganti</span>`;
                                row.appendChild(div);
                            }
                        });
                    }

                    ownItems.forEach(item => {
                        const mid = parseInt(item.dataset.memberId); if (!mid) return;
                        const isAbsen = absenIds.has(mid);
                        const isDipinjam = !isAbsen && activReplIds.has(mid);
                        const av = item.querySelector('.mc-av');
                        const img = item.querySelector('.mc-av img');
                        const tag = item.querySelector('.mc-member-tag');

                        if (isAbsen) {
                            item.classList.add('mi-absen'); item.classList.remove('mi-dipinjam');
                            av?.classList.replace('av-ok', 'av-absen');
                            if (img) img.style.filter = 'grayscale(.5) brightness(.8)';
                            if (tag) {
                                tag.className = 'mc-member-tag tag-absen';
                                const r = (absData[mid]?.reason || '').toLowerCase();
                                tag.textContent = r.includes('sakit') ? 'SAKIT' : r.includes('izin') || r.includes('ijin') ? 'IZIN' : r.includes('cuti') ? 'CUTI' : 'Absen';
                            }
                            item.querySelector('.mi-dipinjam-dest')?.remove();
                        } else if (isDipinjam) {
                            const dest = replData.find(r => r.member_id === mid)?.target_machine || '';
                            item.classList.add('mi-dipinjam'); item.classList.remove('mi-absen');
                            av?.classList.replace('av-absen', 'av-ok');
                            if (img) img.style.filter = 'grayscale(.55) brightness(.72)';
                            if (tag) { tag.className = 'mc-member-tag tag-dipinjam'; tag.textContent = 'Tugas Lain'; }
                            let d = item.querySelector('.mi-dipinjam-dest');
                            if (!d) { d = document.createElement('div'); d.className = 'mi-dipinjam-dest'; item.appendChild(d); }
                            if (dest) { d.textContent = `↗ ${dest}`; d.title = `Bertugas di: ${dest}`; }
                        } else {
                            item.classList.remove('mi-absen', 'mi-dipinjam');
                            av?.classList.replace('av-absen', 'av-ok');
                            if (img) img.style.filter = '';
                            item.querySelector('.mi-dipinjam-dest')?.remove();
                            if (tag && ['Absen', 'Tugas Lain'].includes(tag.textContent)) { tag.className = 'mc-member-tag tag-hadir'; tag.textContent = 'Hadir'; }
                        }
                    });

                    const logTypes = openLogMap[machine] || [];
                    const pipTypes = [];
                    if (cardHasAbsen) pipTypes.push('man');
                    logTypes.forEach(t => { if (!pipTypes.includes(t)) pipTypes.push(t); });
                    const lightEl = card.querySelector('.mc-4m-row');
                    if (lightEl) lightEl.innerHTML = pipTypes.map(t =>
                        `<div class="mc-4m-pip" style="background:${colorMap[t] || '#ccc'}" title="${t}"></div>`
                    ).join('');

                    let primaryStatus = 'normal';
                    if (cardHasAbsen && !hasReplacement) primaryStatus = 'man';
                    else if (logTypes.includes('machine')) primaryStatus = 'machine';
                    else if (logTypes.includes('material')) primaryStatus = 'material';
                    else if (logTypes.includes('method')) primaryStatus = 'method';

                    card.classList.remove('mc-has-absen', ...Object.values(borderCls));
                    if (cardNeedsFinder) card.classList.add('mc-has-absen');
                    else if (!cardHasAbsen && primaryStatus !== 'normal' && borderCls[primaryStatus]) card.classList.add(borderCls[primaryStatus]);

                    const dot = card.querySelector('.mc-dot'); if (!dot) return;
                    if (cardHasAbsen && cardNeedsFinder) {
                        // Ada absen, belum ada pengganti → dot merah blink, bisa klik cari pengganti
                        dot.className = 'mc-dot d-absen'; dot.style.background = '';
                        dot.onclick = e => { e.stopPropagation(); openFinderModal(machine); };
                    } else if (cardHasAbsen && !cardNeedsFinder) {
                        // Ada absen, sudah ada pengganti → dot hilang
                        dot.className = 'mc-dot'; dot.style.background = ''; dot.onclick = null;
                    } else if (primaryStatus !== 'normal' && colorMap[primaryStatus]) {
                        dot.className = 'mc-dot d-visible'; dot.style.background = colorMap[primaryStatus];
                        dot.onclick = e => { e.stopPropagation(); openMachineDetail(machine, primaryStatus); };
                    } else {
                        dot.className = 'mc-dot'; dot.style.background = ''; dot.onclick = null;
                    }

                    const activePillCls = [];
                    if (cardHasAbsen) activePillCls.push('p-man');
                    if (logTypes.includes('machine')) activePillCls.push('p-machine');
                    if (logTypes.includes('material')) activePillCls.push('p-material');
                    if (logTypes.includes('method')) activePillCls.push('p-method');
                    if (activePillCls.length === 0) activePillCls.push('p-normal');

                    card.querySelectorAll('.mc-status-pill').forEach(p => p.classList.remove('active'));
                    activePillCls.forEach(cls => card.querySelector(`.mc-status-pill.${cls}`)?.classList.add('active'));
                });
            } catch (e) { console.error('syncCards', e); }
            finally { _syncing = false; }
        }

        async function syncAll() {
            await syncCards();
            await syncSummary();
        }

        /* ── 3. QUICK LOG MODAL ── */
        let _qlActiveJenis = null;
        const _qlPrefix = { Machine: 'm', Material: 'mat', Method: 'met' };

        function openQuickLog(machine) {
            _qlMachine = machine; _qlActiveJenis = null;
            $id('qlLokasi').value = machine;
            $id('qlMesinLabel').textContent = machine;
            ['machine', 'material', 'method'].forEach(j => { $id(`ql-btn-${j}`).className = 'jenis-btn'; });
            document.querySelectorAll('.ql-form-section').forEach(s => s.classList.remove('visible'));
            $id('qlSaveBtnWrap').style.display = 'none';
            const now = new Date().toTimeString().slice(0, 5);
            ['qlMulai', 'ql-mat-mulai', 'ql-met-mulai'].forEach(id => { const el = $id(id); if (el) el.value = now; });
            ['qlDeskripsi', 'qlCause', 'qlPIC', 'ql-mat-deskripsi', 'ql-mat-cause', 'ql-mat-pic', 'ql-met-deskripsi', 'ql-met-cause', 'ql-met-pic'].forEach(id => { const el = $id(id); if (el) el.value = ''; });
            ['ql-m-status', 'ql-mat-status', 'ql-met-status'].forEach(id => { const el = $id(id); if (el) el.value = 'open'; });
            ['m', 'mat', 'met'].forEach(p => { const w = $id(`ql-${p}-selesai-wrap`); if (w) w.style.display = 'none'; });
            renderLogList(machine);
            openSheet('quickLogOverlay');
        }

        function closeQuickLog() { closeSheet('quickLogOverlay'); _qlMachine = null; _qlActiveJenis = null; }

        function qlSelectJenis(jenis) {
            _qlActiveJenis = jenis;
            ['Machine', 'Material', 'Method'].forEach(j => { $id(`ql-btn-${j.toLowerCase()}`).className = 'jenis-btn'; });
            $id(`ql-btn-${jenis.toLowerCase()}`).className = `jenis-btn sel-${jenis.toLowerCase()}`;
            document.querySelectorAll('.ql-form-section').forEach(s => s.classList.remove('visible'));
            $id(`ql-section-${jenis.toLowerCase()}`).classList.add('visible');
            const now = new Date().toTimeString().slice(0, 5);
            const p = _qlPrefix[jenis];
            const mulaiId = jenis === 'Machine' ? 'qlMulai' : `ql-${p}-mulai`;
            const el = $id(mulaiId); if (el && !el.value) el.value = now;
            $id('qlSaveBtnWrap').style.display = 'block';
        }

        function qlToggleSelesai(prefix, val) {
            const wrap = $id(`ql-${prefix}-selesai-wrap`); if (!wrap) return;
            wrap.style.display = val === 'closed' ? '' : 'none';
            if (val === 'closed') { const el = $id(`ql-${prefix}-selesai`); if (el) el.value = new Date().toTimeString().slice(0, 5); }
        }

        async function renderLogList(machine) {
            const wrap = $id('qlLogList'); if (!wrap) return;
            wrap.innerHTML = '<div style="text-align:center;padding:10px;color:#aaa;font-size:11px">⏳ Memuat…</div>';
            wrap.style.display = 'block';
            try {
                const qs = `tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`;
                const res = await fetch(`/admin/logs/list?${qs}`, { headers: { Accept: 'application/json' } });
                const all = res.ok ? await res.json() : [];
                const logs = all.filter(l => l.lokasi === machine);
                if (!logs.length) { wrap.innerHTML = '<div style="text-align:center;padding:12px;color:#ccc;font-size:11px">Belum ada log untuk mesin ini hari ini.</div>'; return; }
                const jc = { machine: '#1f3c88', material: '#f39c12', method: '#2e7d32' };
                wrap.innerHTML = logs.map(l => {
                    const isOpen = l.status === 'open', jClr = jc[(l.jenis || '').toLowerCase()] || '#888';
                    const mulaiStr = (l.waktu_mulai || '').slice(0, 5), selStr = (l.waktu_selesai || '').slice(0, 5);
                    const timeStr = mulaiStr + (selStr ? ` – ${selStr}` : ' – …');
                    return `<div class="ql-log-item ${isOpen ? 'ql-log-open' : 'ql-log-closed'}" id="qli-${l.id}">
                                            <div class="ql-log-top">
                                                <span class="ql-log-badge" style="background:${jClr}">${esc(l.jenis)}</span>
                                                <span class="ql-log-dot ${isOpen ? 'dot-open' : 'dot-closed'}"></span>
                                                <span class="ql-log-time" id="qltime-${l.id}">${timeStr}</span>
                                                ${l.durasi ? `<span class="ql-log-dur" id="qldur-${l.id}">(${esc(l.durasi)})</span>` : (isOpen ? `<span class="ql-log-dur blink" id="qldur-${l.id}">ON GOING</span>` : '')}
                                            </div>
                                            <div class="ql-log-desc">${esc(l.deskripsi)}</div>
                                            ${l.cause ? `<div class="ql-log-meta">🔍 ${esc(l.cause)}</div>` : ''}
                                            ${l.pic ? `<div class="ql-log-meta">👤 ${esc(l.pic)}</div>` : ''}
                                            <div class="ql-log-actions">
                                                ${isOpen ? `<button class="ql-btn ql-btn-done" onclick="qlCloseLog(${l.id},this)">✅ Selesai</button>` : `<button class="ql-btn ql-btn-reopen" onclick="qlReopenLog(${l.id},this)">🔄 Buka Ulang</button>`}
                                                <button class="ql-btn ql-btn-del" onclick="qlDeleteLog(${l.id},this)">🗑️</button>
                                        </div>
                                    </div>`;
                }).join('');
            } catch (e) { wrap.innerHTML = '<div style="text-align:center;padding:10px    ;color:#f99;font-size:11px">Gagal memuat log.</div>'; }
        }

        async function submitQuickLog() {
            if (!_qlActiveJenis) { showToast('Pilih tipe masalah dulu', 'error'); return; }
            const lokasi = _qlMachine, jenis = _qlActiveJenis, p = _qlPrefix[jenis];
            const mulaiId = jenis === 'Machine' ? 'qlMulai' : `ql-${p}-mulai`;
            const deskId = jenis === 'Machine' ? 'qlDeskripsi' : `ql-${p}-deskripsi`;
            const causeId = jenis === 'Machine' ? 'qlCause' : `ql-${p}-cause`;
            const picId = jenis === 'Machine' ? 'qlPIC' : `ql-${p}-pic`;
            const mulai = $id(mulaiId)?.value;
            const desk = $id(deskId)?.value?.trim();
            const status = $id(`ql-${p}-status`)?.value || 'open';
            const selesaiR = $id(`ql-${p}-selesai`)?.value;
            const waktuSel = (status === 'closed' && selesaiR) ? selesaiR : null;
            if (!mulai) { showToast('Isi waktu mulai', 'error'); return; }
            if (!desk) { showToast('Isi deskripsi masalah', 'error'); return; }
            const btn = $id('qlSubmitBtn'); if (btn) { btn.disabled = true; btn.textContent = '⏳ Menyimpan…'; }
            try {
                const res = await fetch('/admin/logs', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ tanggal: TANGGAL, factory: FACTORY, shift: SHIFT, jenis, lokasi, waktu_mulai: mulai, waktu_selesai: waktuSel, status, deskripsi: desk, cause: $id(causeId)?.value || null, countermeasure: null, pic: $id(picId)?.value || null }),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) { showToast('Gagal: ' + (data?.message?.slice(0, 60) ?? 'error'), 'error'); return; }
                showToast(`✅ Log ${jenis} ditambah — ${lokasi}`, 'success');
                [$id(deskId), $id(causeId), $id(picId)].forEach(el => { if (el) el.value = ''; });
                renderLogList(lokasi); syncAll();
            } catch (e) { showToast('Gagal: ' + e.message, 'error'); }
            finally { if (btn) { btn.disabled = false; btn.textContent = '💾 Simpan Log'; } }
        }

        async function qlCloseLog(id, btn) {
            if (btn) { btn.disabled = true; btn.textContent = '⏳…'; }
            try {
                const res = await fetch(`/admin/logs/${id}/close`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' } });
                const data = await res.json();
                if (!res.ok) { showToast('Gagal menutup log', 'error'); if (btn) { btn.disabled = false; btn.textContent = '✅ Selesai'; } return; }
                const item = $id(`qli-${id}`), timeEl = $id(`qltime-${id}`), durEl = $id(`qldur-${id}`), dotEl = item?.querySelector('.ql-log-dot');
                if (item) item.classList.replace('ql-log-open', 'ql-log-closed');
                if (dotEl) dotEl.className = 'ql-log-dot dot-closed';
                if (timeEl && data.waktu_selesai) { const m = timeEl.textContent.split('–')[0].trim(); timeEl.textContent = `${m} – ${data.waktu_selesai}`; }
                if (durEl && data.durasi) { durEl.className = 'ql-log-dur'; durEl.textContent = `(${data.durasi})`; }
                if (btn) { btn.className = 'ql-btn ql-btn-reopen'; btn.disabled = false; btn.textContent = '🔄 Buka Ulang'; btn.onclick = () => qlReopenLog(id, btn); }
                showToast(`✅ Selesai — ${data.durasi ?? ''}`, 'success'); syncAll();
            } catch (e) { showToast('Gagal', 'error'); if (btn) { btn.disabled = false; btn.textContent = '✅ Selesai'; } }
        }

        async function qlReopenLog(id, btn) {
            if (btn) { btn.disabled = true; btn.textContent = '⏳…'; }
            try {
                const res = await fetch(`/admin/logs/${id}/reopen`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' } });
                if (!res.ok) { showToast('Gagal membuka ulang', 'error'); if (btn) { btn.disabled = false; btn.textContent = '🔄 Buka Ulang'; } return; }
                if (_qlMachine) renderLogList(_qlMachine);
                showToast('🔄 Log dibuka ulang', 'info'); syncAll();
            } catch (e) { showToast('Gagal', 'error'); if (btn) btn.disabled = false; }
        }

        async function qlDeleteLog(id, btn) {
            if (!confirm('Hapus log ini?')) return;
            if (btn) btn.disabled = true;
            try {
                const res = await fetch(`/admin/logs/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' } });
                if (!res.ok) { showToast('Gagal menghapus', 'error'); if (btn) btn.disabled = false; return; }
                $id(`qli-${id}`)?.remove();
                showToast('🗑️ Log dihapus', 'info'); syncAll();
            } catch (e) { showToast('Gagal', 'error'); if (btn) btn.disabled = false; }
        }

        /* ── 4. MACHINE DETAIL SHEET ── */
        function handleCardClick(e, machine, status) {
            if (e.target.classList.contains('d-absen') || e.target.closest('.mc-addlog-bar')) return;
            openMachineDetail(machine, status);
        }
        function openMachineDetail(machine, currentStatus) {
            openSheet('machineSheet');
            $id('machineSheetTitle').textContent = machine;
            const body = $id('machineSheetBody');
            if (body) body.innerHTML = '<div style="text-align:center;padding:24px;color:#aaa">Memuat...</div>';
            fetch(`/admin/logs/list?tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}`)
                .then(r => r.json())
                .then(logs => {
                    const ml = logs.filter(l => l.lokasi === machine && l.status === 'open');
                    const logHtml = ml.length
                        ? `<div style="margin-bottom:14px"><div class="section-title" style="margin-bottom:6px">⚠️ Open Logs (${ml.length})</div>${ml.map(l => `<div style="background:#fff8f0;border:1px solid #fcd8a0;border-radius:8px;padding:8px 10px;margin-bottom:6px;font-size:11px"><span style="font-weight:700;color:var(--orange)">[${esc(l.jenis)}]</span> ${esc(l.deskripsi)}<span style="color:#aaa;margin-left:4px">${(l.waktu_mulai || '').slice(0, 5)}</span></div>`).join('')}</div>`
                        : '<p style="font-size:12px;color:#aaa;text-align:center;padding:8px 0">Tidak ada open log saat ini.</p>';
                    if (body) body.innerHTML = `<p style="font-size:12px;color:#888;margin-bottom:12px">${esc(FACTORY)} | Shift ${SHIFT} | ${TANGGAL}</p>${logHtml}<div style="text-align:center;margin-top:8px"><a href="/admin/reports?tanggal=${TANGGAL}&factory=${encodeURIComponent(FACTORY)}&shift=${SHIFT}" style="font-size:12px;padding:8px 16px;text-decoration:none;border-radius:8px;background:var(--navy);color:#fff;display:inline-block">📝 Lihat Semua Problem Log</a></div>`;
                })
                .catch(() => { if (body) body.innerHTML = '<p style="text-align:center;color:#aaa;padding:20px">Gagal memuat data</p>'; });
        }

        /* ── 5. FINDER PENGGANTI ── */
        let activeMachine = null;
        function openFinderModal(machine) {
            activeMachine = machine;
            $id('finderMachineName').textContent = machine;
            $id('finderSearch').value = '';
            $id('finderOverlay').classList.add('show');
            document.body.style.overflow = 'hidden';
            renderFinderCandidates();
            setTimeout(() => $id('finderSearch')?.focus(), 200);
        }
        function closeFinderModal() { $id('finderOverlay').classList.remove('show'); document.body.style.overflow = ''; activeMachine = null; }
        function handleFinderOverlayClick(e) { if (e.target === $id('finderOverlay')) closeFinderModal(); }

        async function renderFinderCandidates() {
            const list = $id('finderCandList'), q = $id('finderSearch')?.value?.trim() || '';
            list.innerHTML = '<div class="cand-empty">⏳ Memuat…</div>';
            try {
                const params = new URLSearchParams({ tanggal: TANGGAL, factory: FACTORY, shift: SHIFT, q });
                const res = await fetch(`/admin/assignment/candidates?${params}`, { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF } });
                if (!res.ok) { list.innerHTML = `<div class="cand-empty">⚠️ Error ${res.status}</div>`; return; }
                const data = await res.json();
                if (!Array.isArray(data) || !data.length) { list.innerHTML = '<div class="cand-empty">🔍 Tidak ada member tersedia.</div>'; return; }
                list.innerHTML = data.map(m => {
                    const av = m.photo ? `<img src="${esc(m.photo)}" alt="">` : `<span>${initials(m.name)}</span>`;
                    return `<div class="cand-card">
                                            <div class="cand-av ${m.isWorking ? 'cav-w' : 'cav-n'}">${av}</div>
                                            <div class="cand-name">${esc(m.name)}</div>
                                            ${m.jabatan ? `<div style="font-size:9px;color:#aaa;font-family:'Roboto Condensed',sans-serif">${esc(m.jabatan)}</div>` : ''}
                                            ${m.mesin ? `<div style="font-size:9px;color:#aaa;font-family:'Roboto Condensed',sans-serif">${esc(m.mesin)}</div>` : ''}
                                            ${m.isWorking ? '<div class="cand-tag">Sdh Bertugas</div>' : ''}
                                            <button class="cand-btn" onclick="pickCandidate(${m.id},'${esc(m.name)}','${esc(m.photo || '')}','${esc(m.mesin || '')}')">✓ Pilih</button>
                                        </div>`;
                }).join('');
            } catch (e) { list.innerHTML = '<div class="cand-empty">Gagal memuat kandidat.</div>'; }
        }

        async function pickCandidate(memberId, name, photo, sourceMachine) {
            if (!activeMachine) return;
            const targetMachine = activeMachine; // simpan sebelum closeFinderModal() reset
            event?.target?.setAttribute('disabled', true);
            try {
                const res = await fetch('/admin/replacements', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify({ tanggal: TANGGAL, factory: FACTORY, shift: SHIFT, target_machine: targetMachine, member_id: memberId }) });
                if (!res.ok) { const err = await res.json().catch(() => ({})); showToast(err.message || 'Gagal menyimpan pengganti', 'error'); event?.target?.removeAttribute('disabled'); return; }

                showToast(`✅ ${name} → ${targetMachine}`, 'success');
                closeFinderModal();

                // ── 1. Inject card pengganti langsung ke DOM (tanpa tunggu full reload) ──
                const tc = document.querySelector(`.mc-card[data-machine="${CSS.escape(targetMachine)}"]`);
                if (tc) {
                    const row = tc.querySelector('.mc-members-row');
                    row?.querySelector('.mc-empty-slot')?.remove();
                    if (row && !row.querySelector(`.mc-member-item[data-member-id="${memberId}"][data-replacement="1"]`)) {
                        const div = document.createElement('div');
                        div.className = 'mc-member-item';
                        div.dataset.memberId = memberId;
                        div.dataset.replacement = '1';
                        const avContent = photo
                            ? `<img src="${esc(photo)}" alt="${esc(name)}">`
                            : `<span>${initials(name)}</span>`;
                        div.innerHTML = `<div class="mc-av av-ok">${avContent}</div>`
                            + `<div class="mc-member-name">${esc(name)}</div>`
                            + `<span class="mc-member-tag" style="background:#e65100;color:#fff;font-size:7px;`
                            + `padding:1px 4px;border-radius:3px;text-transform:uppercase;font-weight:800">Pengganti</span>`;
                        row.appendChild(div);
                    }
                    // Hilangkan border + dot merah — sudah ada pengganti
                    tc.classList.remove('mc-has-absen');
                    const dot = tc.querySelector('.mc-dot');
                    if (dot) { dot.className = 'mc-dot d-absen'; dot.style.background = ''; dot.onclick = null; }
                }

                // ── 2. Update source card (member yg "dipinjam") ──
                if (sourceMachine) {
                    const sc = document.querySelector(`.mc-card[data-machine="${CSS.escape(sourceMachine)}"]`);
                    if (sc) {
                        const mi = sc.querySelector(`.mc-member-item[data-member-id="${memberId}"]`);
                        if (mi && !mi.classList.contains('mi-dipinjam')) {
                            mi.classList.add('mi-dipinjam');
                            const img = mi.querySelector('.mc-av img');
                            const tag = mi.querySelector('.mc-member-tag');
                            if (img) img.style.filter = 'grayscale(.55) brightness(.72)';
                            if (tag) { tag.className = 'mc-member-tag tag-dipinjam'; tag.textContent = 'Tugas Lain'; }
                            let d = mi.querySelector('.mi-dipinjam-dest');
                            if (!d) { d = document.createElement('div'); d.className = 'mi-dipinjam-dest'; mi.appendChild(d); }
                            d.textContent = `↗ ${targetMachine}`; d.title = `Bertugas di: ${targetMachine}`;
                        }
                    }
                }

                // ── 3. Sync penuh: reset guard + delay agar server commit dulu ──
                _syncing = false;
                await new Promise(r => setTimeout(r, 350));
                await syncCards();
                await syncSummary();

            } catch (e) { showToast('Gagal menyimpan pengganti', 'error'); event?.target?.removeAttribute('disabled'); }
        }

        async function loadReplacements() {
            try {
                const params = new URLSearchParams({ tanggal: TANGGAL, factory: FACTORY, shift: SHIFT });
                const res = await fetch(`/admin/replacements?${params}`, { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF } });
                if (!res.ok) return;
                const data = await res.json();
                data.forEach(r => {
                    const tc = document.querySelector(`.mc-card[data-machine="${CSS.escape(r.target_machine)}"]`);
                    if (tc) {
                        const row = tc.querySelector('.mc-members-row');
                        const exists = row?.querySelector(`.mc-member-item[data-member-id="${r.member_id}"][data-replacement="1"]`);
                        if (row && !exists) {
                            row.querySelector('.mc-empty-slot')?.remove();
                            const div = document.createElement('div'); div.className = 'mc-member-item'; div.dataset.memberId = r.member_id; div.dataset.replacement = '1';
                            div.innerHTML = `<div class="mc-av av-ok">${r.member_photo ? `<img src="${esc(r.member_photo)}" alt="${esc(r.member_name)}">` : `<span>${initials(r.member_name)}</span>`}</div><div class="mc-member-name">${esc(r.member_name || '')}</div><span class="mc-member-tag" style="background:#e65100;color:#fff;font-size:7px;padding:1px 4px;border-radius:3px;text-transform:uppercase;font-weight:800">Pengganti</span>`;
                            row.appendChild(div);
                        }
                        tc.classList.remove('mc-has-absen');
                        const dot = tc.querySelector('.mc-dot'); if (dot) { dot.className = 'mc-dot d-absen'; dot.style.background = ''; dot.onclick = null; }
                    }
                    if (r.source_machine) {
                        const sc = document.querySelector(`.mc-card[data-machine="${CSS.escape(r.source_machine)}"]`);
                        if (sc) {
                            const mi = sc.querySelector(`.mc-member-item[data-member-id="${r.member_id}"]`);
                            if (mi && !mi.classList.contains('mi-dipinjam')) {
                                mi.classList.add('mi-dipinjam');
                                const img = mi.querySelector('.mc-av img'), tag = mi.querySelector('.mc-member-tag');
                                if (img) img.style.filter = 'grayscale(.55) brightness(.72)';
                                if (tag) { tag.className = 'mc-member-tag tag-dipinjam'; tag.textContent = 'Tugas Lain'; }
                                let d = mi.querySelector('.mi-dipinjam-dest');
                                if (!d) { d = document.createElement('div'); d.className = 'mi-dipinjam-dest'; mi.appendChild(d); }
                                d.textContent = `↗ ${r.target_machine}`; d.title = `Bertugas di: ${r.target_machine}`;
                            }
                        }
                    }
                });
            } catch (e) { console.error('loadReplacements', e); }
        }

        /* ── 6. CHART KEHADIRAN ── */
        function renderChart(data) {
            if (!data) return;
            const values = [data.mp_hadir ?? 0, data.op_cuti ?? 0, data.op_sakit ?? 0, data.op_ijin ?? 0, data.spv_cuti ?? 0, data.spv_sakit ?? 0, data.spv_ijin ?? 0];
            const total = data.total_member ?? values.reduce((a, b) => a + b, 0); if (!total) return;
            const hadir = data.mp_hadir ?? 0, pct = ((hadir / total) * 100).toFixed(1);
            const cv = $id('centerValue'), af = $id('absenFill');
            if (cv) cv.textContent = `${hadir}/${total}`;
            if (af) { af.style.width = pct + '%'; af.textContent = pct + '%'; }
            if (dashChart) dashChart.destroy();
            dashChart = new Chart($id('myChart'), {
                type: 'doughnut',
                data: { labels: ['MP Hadir', 'OP Cuti', 'OP Sakit', 'OP Ijin', 'Pengawas Cuti', 'Pengawas Sakit', 'Pengawas Ijin'], datasets: [{ data: values, backgroundColor: ['#729E3F', '#ff69b4', '#8e44ad', '#f1c40f', '#1F3C88', '#5dade2', '#FF8F1F'], borderWidth: 0 }] },
                options: { cutout: '75%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { display: false }, tooltip: { callbacks: { label: ctx => { const v = ctx.parsed; return v ? `${ctx.label}: ${v} (${((v / total) * 100).toFixed(1)}%)` : null; } } } } },
            });
        }

        /* ── 7. AUTO REFRESH — CHANGE: interval 15 detik ── */
        let _arInterval = null, _arCountdown = 15, _arPaused = false;
        function startAutoRefresh() {
            if (_arInterval) clearInterval(_arInterval);
            _arCountdown = 15;
            _arInterval = setInterval(() => {
                if (_arPaused) return;
                _arCountdown--;
                const cd = $id('arCountdown'); if (cd) cd.textContent = _arCountdown + 's';
                if (_arCountdown <= 0) { _arCountdown = 15; syncAll(); }
            }, 1000);
        }
        function toggleAutoRefresh() {
            _arPaused = !_arPaused;
            const btn = $id('arToggleBtn'), dot = $id('arDot');
            if (_arPaused) { dot?.classList.add('paused'); if (btn) btn.textContent = '▶ Resume'; const cd = $id('arCountdown'); if (cd) cd.textContent = '--'; }
            else { _arCountdown = 15; dot?.classList.remove('paused'); if (btn) btn.textContent = '⏸ Pause'; }
        }
        function onDateChange(val) { const url = new URL(window.location); url.searchParams.set('tanggal', val); window.location = url; }

        /* ── 8. FOTO MESIN ── */
        function triggerPhotoUpload(machine, slug) { document.getElementById('file-' + slug)?.click(); }
        async function uploadMachinePhoto(event, machine, slug) {
            const file = event.target.files?.[0]; if (!file) return;
            const wrap = document.getElementById('photo-wrap-' + slug);
            if (file.size > 3 * 1024 * 1024) { showToast('Foto terlalu besar (maks 3MB)', 'error'); event.target.value = ''; return; }
            wrap?.classList.add('uploading');
            const ot = wrap?.querySelector('.upload-txt'); if (ot) ot.textContent = '⏳ Mengupload...';
            try {
                const fd = new FormData(); fd.append('factory', FACTORY); fd.append('machine_name', machine); fd.append('photo', file); fd.append('_token', CSRF);
                const res = await fetch('/admin/machines/photo', { method: 'POST', body: fd }); const data = await res.json();
                if (!res.ok || !data.ok) { showToast(data.message || 'Gagal upload foto', 'error'); return; }
                const imgEl = document.getElementById('photo-img-' + slug);
                if (imgEl) {
                    if (imgEl.tagName === 'IMG') { imgEl.src = data.photo_url + '?t=' + Date.now(); }
                    else { const ni = document.createElement('img'); ni.id = `photo-img-${slug}`; ni.src = data.photo_url; ni.alt = machine; ni.loading = 'lazy'; ni.style.cssText = 'width:100%;height:100%;object-fit:cover;transition:transform .3s'; imgEl.replaceWith(ni); }
                }
                if (ot) ot.textContent = 'Ganti Foto';
                showToast(`✅ Foto ${machine} berhasil diupload`, 'success');
            } catch (e) { showToast('Gagal upload foto', 'error'); if (ot) ot.textContent = 'Upload Foto'; }
            finally { wrap?.classList.remove('uploading'); event.target.value = ''; }
        }

        /* ── 9. INIT ── */
        document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeFinderModal(); closeQuickLog(); } });
        document.addEventListener('DOMContentLoaded', () => {
            if (initAbsence) renderChart(initAbsence);
            loadReplacements();
            syncAll();
            startAutoRefresh();
            document.addEventListener('visibilitychange', () => { if (!document.hidden) syncAll(); });
        });
    </script>
@endpush