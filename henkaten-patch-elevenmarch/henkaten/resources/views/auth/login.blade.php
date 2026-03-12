<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Login — HENKATEN BOARD</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/henkaten.css') }}">
<style>
body { margin: 0; min-height: 100vh; background: linear-gradient(135deg, #2E7D32, #1F3C88); display: flex; align-items: center; justify-content: center; font-family: 'Roboto', sans-serif; }
.login-card { background: #fff; border-radius: 20px; padding: 36px 28px; width: 90%; max-width: 380px; box-shadow: 0 8px 32px rgba(0,0,0,.18); animation: slideUp .4s ease; }
@keyframes slideUp { from { transform: translateY(40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.login-logo { text-align: center; margin-bottom: 24px; }
.login-logo .brand { font-family: 'Orbitron', sans-serif; font-size: 24px; color: #1F3C88; }
.login-logo .brand span { color: #ff8000; }
.login-logo .sub { font-size: 13px; color: #888; margin-top: 4px; }
.login-field { margin-bottom: 16px; }
.login-field label { display: block; font-size: 12px; font-weight: 600; color: #666; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
.login-field input { width: 100%; padding: 14px 16px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 16px; box-sizing: border-box; background: #fafafa; color: #2E2E2E; transition: border-color .2s; }
.login-field input:focus { outline: none; border-color: #729E3F; background: #fff; }
.login-error { color: #e74c3c; font-size: 13px; text-align: center; margin-bottom: 12px; }
.login-btn { width: 100%; padding: 16px; border: none; border-radius: 12px; background: linear-gradient(135deg, #729E3F, #2E7D32); color: #fff; font-size: 16px; font-weight: 700; cursor: pointer; font-family: 'Roboto Condensed', sans-serif; letter-spacing: 1px; }
.login-btn:hover { opacity: .9; }
</style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <div class="brand">HENKATEN <span>BOARD</span></div>
        <div class="sub">Admin Management System</div>
    </div>

    @if ($errors->hasAny(['username','login']))
        <div class="login-error">{{ $errors->first('username') ?: $errors->first('login') }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="login-field">
            <label>Username</label>
            <input type="text" name="username" value="{{ old('username') }}"
                   placeholder="Enter username" autocomplete="username" required autofocus>
        </div>
        <div class="login-field">
            <label>Password</label>
            <input type="password" name="password"
                   placeholder="Enter password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="login-btn">LOGIN</button>
    </form>
</div>

</body>
</html>
