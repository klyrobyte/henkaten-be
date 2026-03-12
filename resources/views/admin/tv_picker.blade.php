<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Mode — Pilih Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Roboto+Condensed:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d1117 0%, #1a237e 50%, #0d47a1 100%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            font-family: 'Roboto Condensed', sans-serif;
            padding: 24px 16px;
        }

        /* Header */
        .tv-header {
            text-align: center; margin-bottom: 36px;
        }
        .tv-logo {
            font-size: 52px; line-height: 1; margin-bottom: 10px;
            filter: drop-shadow(0 4px 16px rgba(255,255,255,.3));
        }
        .tv-title {
            font-family: 'Orbitron', sans-serif; font-weight: 900;
            font-size: clamp(20px, 5vw, 32px); letter-spacing: 3px;
            color: #fff; text-transform: uppercase;
            text-shadow: 0 2px 16px rgba(100,150,255,.6);
        }
        .tv-subtitle {
            font-size: 12px; letter-spacing: 2px; text-transform: uppercase;
            color: rgba(255,255,255,.5); margin-top: 6px;
        }

        /* Cards grid */
        .picker-grid {
            display: flex; flex-direction: column; gap: 16px;
            width: 100%; max-width: 420px;
        }

        .factory-card {
            border-radius: 16px; overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
            transition: transform .15s, box-shadow .15s;
        }
        .factory-card:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,.5); }

        .factory-header {
            padding: 12px 18px; display: flex; align-items: center; gap: 10px;
        }
        .factory-header span.icon { font-size: 20px; }
        .factory-header span.name {
            font-family: 'Orbitron', sans-serif; font-weight: 900;
            font-size: 13px; letter-spacing: 2px; color: #fff; text-transform: uppercase;
        }

        .factory-2 .factory-header  { background: linear-gradient(135deg, #1b5e20, #2e7d32); }
        .factory-34 .factory-header { background: linear-gradient(135deg, #0d47a1, #1565c0); }

        .shift-row {
            display: flex; background: #fff;
        }
        .shift-btn {
            flex: 1; padding: 16px 8px; border: none; cursor: pointer;
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            background: transparent; transition: background .15s;
            text-decoration: none;
        }
        .shift-btn:not(:last-child) { border-right: 1px solid #e8e8e8; }

        .factory-2  .shift-btn:hover { background: #e8f5e9; }
        .factory-34 .shift-btn:hover { background: #e3f2fd; }

        .shift-factory-code {
            font-family: 'Orbitron', sans-serif; font-weight: 900;
            font-size: 18px; color: #1a1a1a; letter-spacing: 1px;
        }
        .shift-badge {
            font-family: 'Roboto Condensed', sans-serif; font-weight: 800;
            font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase;
            color: #fff; padding: 3px 12px; border-radius: 20px;
        }
        .badge-a { background: #f5a623; }
        .badge-b { background: #ef5350; }

        /* User info + logout */
        .user-bar {
            margin-top: 28px; text-align: center;
        }
        .user-bar span {
            font-size: 11px; color: rgba(255,255,255,.45); letter-spacing: .5px;
        }
        .logout-btn {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 10px; padding: 7px 18px; border-radius: 20px;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.7); font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px; font-weight: 700; letter-spacing: .5px;
            text-decoration: none; cursor: pointer; transition: background .15s;
        }
        .logout-btn:hover { background: rgba(255,255,255,.2); color: #fff; }
    </style>
</head>
<body>

    <div class="tv-header">
        <div class="tv-logo">📺</div>
        <div class="tv-title">Henkaten Board</div>
        <div class="tv-subtitle">Pilih Factory &amp; Shift</div>
    </div>

    <div class="picker-grid">

        {{-- Factory 2 --}}
        <div class="factory-card factory-2">
            <div class="factory-header">
                <span class="icon">🏭</span>
                <span class="name">Factory 2</span>
            </div>
            <div class="shift-row">
                <a class="shift-btn"
                   href="{{ route('admin.tv') }}?factory={{ urlencode('Factory 2') }}&shift=A">
                    <span class="shift-factory-code">F2</span>
                    <span class="shift-badge badge-a">Shift A</span>
                </a>
                <a class="shift-btn"
                   href="{{ route('admin.tv') }}?factory={{ urlencode('Factory 2') }}&shift=B">
                    <span class="shift-factory-code">F2</span>
                    <span class="shift-badge badge-b">Shift B</span>
                </a>
            </div>
        </div>

        {{-- Factory 3 & 4 --}}
        <div class="factory-card factory-34">
            <div class="factory-header">
                <span class="icon">🏭</span>
                <span class="name">Factory 3 &amp; 4</span>
            </div>
            <div class="shift-row">
                <a class="shift-btn"
                   href="{{ route('admin.tv') }}?factory={{ urlencode('Factory 3 & 4') }}&shift=A">
                    <span class="shift-factory-code">F3&4</span>
                    <span class="shift-badge badge-a">Shift A</span>
                </a>
                <a class="shift-btn"
                   href="{{ route('admin.tv') }}?factory={{ urlencode('Factory 3 & 4') }}&shift=B">
                    <span class="shift-factory-code">F3&4</span>
                    <span class="shift-badge badge-b">Shift B</span>
                </a>
            </div>
        </div>

    </div>

    <div class="user-bar">
        <span>Login sebagai: <strong style="color:rgba(255,255,255,.7)">{{ auth()->user()->name }}</strong>
            &nbsp;·&nbsp;
            <span style="background:#6a1b9a;color:#fff;font-size:9px;font-weight:800;
                         letter-spacing:1px;padding:2px 7px;border-radius:10px;text-transform:uppercase;">
                TV Only
            </span>
        </span><br>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">🚪 Logout</button>
        </form>
    </div>

</body>
</html>