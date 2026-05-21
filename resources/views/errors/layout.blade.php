<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sugity Henkaten</title>

    <!-- Fallback font from Google Fonts, will gracefully degrade to system-ui -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #1F3C88;
            --navy-dark: #152960;
            --red: #EE3124;
            --gray-100: #F3F4F6;
            --gray-600: #4B5563;
            --gray-800: #1F2937;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #E0E7FF 0%, #F3F4F6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-800);
            overflow: hidden;
        }

        /* Decorative background elements */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.6;
            animation: float 10s infinite ease-in-out alternate;
        }

        .bg-shape-1 {
            width: 400px;
            height: 400px;
            background: rgba(31, 136, 41, 0.2);
            /* Navy */
            top: -100px;
            left: -100px;
        }

        .bg-shape-2 {
            width: 300px;
            height: 300px;
            background: rgba(238, 49, 36, 0.15);
            /* Red */
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        @keyframes float {
            0% {
                transform: translateY(0px) scale(1);
            }

            100% {
                transform: translateY(30px) scale(1.1);
            }
        }

        .glass-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 32px;
            padding: 3rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(31, 60, 136, 0.08),
                inset 0 0 0 1px rgba(255, 255, 255, 0.5);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transform: translateY(20px);
            opacity: 0;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            margin: 0;
            background: linear-gradient(135deg, var(--navy), var(--navy-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0px 4px 0px #4abc50ff;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 1rem 0;
            color: var(--gray-800);
        }

        .error-message {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--gray-600);
            margin-bottom: 2.5rem;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: var(--navy);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(31, 60, 136, 0.25);
            border: 2px solid transparent;
        }

        .btn-home:hover {
            background: var(--navy-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 60, 136, 0.35);
        }

        .btn-home:focus {
            outline: none;
            border-color: rgba(31, 60, 136, 0.4);
            box-shadow: 0 0 0 4px rgba(31, 60, 136, 0.2);
        }

        .btn-home svg {
            width: 1.25rem;
            height: 1.25rem;
            transition: transform 0.3s ease;
        }

        .btn-home:hover svg {
            transform: translateX(-4px);
        }

        .logo {
            margin-bottom: 2rem;
        }

        .logo img {
            height: 40px;
            width: auto;
            opacity: 0.8;
        }

        @media (max-width: 640px) {
            .glass-container {
                padding: 2rem 1.5rem;
                border-radius: 24px;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <!-- Background Decor -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <div class="glass-container">
        <!-- Optional Logo Placeholder -->
        <div class="logo" style="display: none;">
            <!-- SVG Icon representing the app -->
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
        </div>

        <h1 class="error-code">@yield('code')</h1>
        <h2 class="error-title">@yield('title')</h2>

        <p class="error-message">
            @yield('message')
        </p>

        <a href="{{ url('/admin') }}" class="btn-home">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</body>

</html>