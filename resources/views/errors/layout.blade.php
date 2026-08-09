<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') - {{ config('app.name', 'BilingualDoc') }} | Court & Legal Automation</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Noto+Sans+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Error Page Styles -->
    <style>
        :root {
            --primary-blue: #0ea5e9;
            --primary-blue-hover: #0284c7;
            --accent-blue-light: #f0f9ff;
            --accent-blue-border: #e0f2fe;
            --navbar-gradient: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            --danger-gradient: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
            --warning-gradient: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow-premium: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Background Orbs */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .orb-1 {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, rgba(2, 132, 199, 0.02) 70%, transparent 100%);
            filter: blur(50px);
            animation: floatSlow 12s ease-in-out infinite alternate;
        }

        .orb-2 {
            position: absolute;
            bottom: -10%;
            left: -5%;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.1) 0%, rgba(224, 242, 254, 0.03) 70%, transparent 100%);
            filter: blur(60px);
            animation: floatSlow 15s ease-in-out infinite alternate-reverse;
        }

        @keyframes floatSlow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(25px, 20px) scale(1.05); }
        }

        /* Minimal Header */
        .error-navbar {
            background: var(--navbar-gradient);
            height: 68px;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 10;
            box-shadow: var(--shadow-md);
        }

        .navbar-limit {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .brand-logo-icon {
            font-size: 1.6rem;
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .brand-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .brand-subtext {
            font-size: 0.65rem;
            font-weight: 600;
            color: #e0f2fe;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            display: block;
        }

        .nav-quick-link {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.45rem 0.9rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .nav-quick-link:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        /* Error Container */
        .error-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            position: relative;
            z-index: 1;
        }

        .error-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            width: 100%;
            max-width: 620px;
            padding: 2.75rem 2.25rem;
            box-shadow: var(--shadow-premium);
            text-align: center;
            position: relative;
            animation: cardAppear 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Icon Container with glowing ring */
        .error-icon-wrapper {
            width: 88px;
            height: 88px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .error-icon-wrapper::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            opacity: 0.5;
            animation: pulseRing 2.5s infinite;
        }

        @keyframes pulseRing {
            0% { transform: scale(0.95); opacity: 0.5; }
            50% { transform: scale(1.08); opacity: 0.2; }
            100% { transform: scale(0.95); opacity: 0.5; }
        }

        /* Code & Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: currentColor;
            display: inline-block;
            box-shadow: 0 0 8px currentColor;
        }

        .error-code {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
            margin-bottom: 0.5rem;
            display: inline-block;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.35rem;
            letter-spacing: -0.3px;
        }

        .error-subtitle-gu {
            font-family: 'Noto Sans Gujarati', sans-serif;
            font-size: 0.95rem;
            color: #0284c7;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .error-description {
            font-size: 0.92rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Action Buttons */
        .error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.4rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-action:active {
            transform: scale(0.98);
        }

        .btn-primary-action {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }

        .btn-primary-action:hover {
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.35);
            transform: translateY(-1px);
        }

        .btn-secondary-action {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #334155;
        }

        .btn-secondary-action:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
            transform: translateY(-1px);
        }

        /* Diagnostics Footnote */
        .diagnostics-box {
            border-top: 1px solid #f1f5f9;
            padding-top: 1.25rem;
            margin-top: 0.5rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .diagnostics-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* Theme Variations */
        /* 404 & Info: Sky Blue */
        .theme-blue .error-icon-wrapper {
            background: #f0f9ff;
            color: #0284c7;
            border: 2px solid #bae6fd;
        }
        .theme-blue .error-icon-wrapper::before {
            background: #0ea5e9;
        }
        .theme-blue .status-badge {
            background: #e0f2fe;
            color: #0369a1;
        }
        .theme-blue .error-code {
            background-image: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
        }

        /* 403 & 401: Violet / Indigo */
        .theme-indigo .error-icon-wrapper {
            background: #f5f3ff;
            color: #7c3aed;
            border: 2px solid #ddd6fe;
        }
        .theme-indigo .error-icon-wrapper::before {
            background: #8b5cf6;
        }
        .theme-indigo .status-badge {
            background: #ede9fe;
            color: #6d28d9;
        }
        .theme-indigo .error-code {
            background-image: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%);
        }

        /* 419 & 429: Amber / Orange */
        .theme-amber .error-icon-wrapper {
            background: #fffbeb;
            color: #d97706;
            border: 2px solid #fde68a;
        }
        .theme-amber .error-icon-wrapper::before {
            background: #f59e0b;
        }
        .theme-amber .status-badge {
            background: #fef3c7;
            color: #b45309;
        }
        .theme-amber .error-code {
            background-image: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        }

        /* 500 & 400: Rose / Red */
        .theme-red .error-icon-wrapper {
            background: #fef2f2;
            color: #dc2626;
            border: 2px solid #fecaca;
        }
        .theme-red .error-icon-wrapper::before {
            background: #ef4444;
        }
        .theme-red .status-badge {
            background: #fee2e2;
            color: #b91c1c;
        }
        .theme-red .error-code {
            background-image: linear-gradient(135deg, #dc2626 0%, #f87171 100%);
        }

        /* 503: Cyan / Teal Maintenance */
        .theme-teal .error-icon-wrapper {
            background: #f0fdfa;
            color: #0d9488;
            border: 2px solid #99f6e4;
        }
        .theme-teal .error-icon-wrapper::before {
            background: #14b8a6;
        }
        .theme-teal .status-badge {
            background: #ccfbf1;
            color: #0f766e;
        }
        .theme-teal .error-code {
            background-image: linear-gradient(135deg, #0d9488 0%, #2dd4bf 100%);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .error-card {
                padding: 2rem 1.25rem;
            }
            .error-code {
                font-size: 3.5rem;
            }
            .error-title {
                font-size: 1.25rem;
            }
            .error-actions {
                flex-direction: column;
                width: 100%;
            }
            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>
<body class="@yield('theme-class', 'theme-blue')">
    <div class="ambient-bg">
        <div class="orb-1"></div>
        <div class="orb-2"></div>
    </div>

    <!-- Navigation Bar -->
    <header class="error-navbar">
        <div class="navbar-limit">
            <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="navbar-brand">
                <span class="brand-logo-icon">⚖️</span>
                <div>
                    <span class="brand-text">{{ config('app.name', 'BilingualDoc') }}</span>
                    <span class="brand-subtext">Court &amp; Legal Automation</span>
                </div>
            </a>
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-quick-link">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-quick-link">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Error Main Content -->
    <main class="error-wrapper">
        <div class="error-card">
            <div class="error-icon-wrapper">
                @yield('icon')
            </div>

            <div class="status-badge">
                <span class="status-dot"></span>
                @yield('badge-text', 'System Notice')
            </div>

            <div>
                <div class="error-code">@yield('code', '404')</div>
                <h1 class="error-title">@yield('heading', 'Page Not Found')</h1>
                <div class="error-subtitle-gu">@yield('subtitle-gu', 'વિનંતી કરેલ પાનું મળ્યું નથી')</div>
                <p class="error-description">
                    @yield('message', 'The legal document, case file, or page you are looking for is unavailable or does not exist.')
                </p>
            </div>

            <div class="error-actions">
                @hasSection('custom-actions')
                    @yield('custom-actions')
                @else
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-action btn-primary-action">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Back to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-action btn-primary-action">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Return to Login
                        </a>
                    @endauth
                    
                    <button type="button" onclick="history.back()" class="btn-action btn-secondary-action">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Previous Page
                    </button>
                @endif
            </div>

            <div class="diagnostics-box">
                <div class="diagnostics-item">
                    <span>Platform:</span>
                    <strong>BilingualDoc Automation</strong>
                </div>
                <div class="diagnostics-item">
                    <span>Status Code:</span>
                    <strong>@yield('code', '404')</strong>
                </div>
                <div class="diagnostics-item">
                    <span>Reference:</span>
                    <strong>{{ strtoupper(substr(md5(request()->path() . now()->timestamp), 0, 8)) }}</strong>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
