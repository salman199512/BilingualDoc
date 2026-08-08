<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@hasSection('tab-title')@yield('tab-title') - @elseif(trim($__env->yieldContent('page-title'))){{ trim(strip_tags($__env->yieldContent('page-title'))) }} - @endif{{ config('app.name', 'BilingualDoc') }} | Court &amp; Legal Automation</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Fonts: Poppins for UI, Noto Sans Gujarati & Times New Roman for Documents -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Noto+Sans+Gujarati:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Global Page Loader -->
    <div id="global-loader" class="loader-overlay">
        <div class="loader-spinner"></div>
        <div class="loader-text">Loading Legal Automation Platform...</div>
    </div>

    @auth
    <!-- Horizontal Top Navigation Navbar -->
    <header class="app-navbar">
        <div class="navbar-limit">
            <div class="navbar-brand">
                <span class="brand-logo-icon">⚖️</span>
                <div>
                    <span class="brand-text">BilingualDoc</span>
                    <span class="brand-subtext">Court & Legal Automation</span>
                </div>
            </div>
            
            <nav class="navbar-menu">
                <a href="{{ route('dashboard') }}" class="navbar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('documents.index') }}" class="navbar-item {{ request()->routeIs('documents.index') || request()->routeIs('documents.edit') || request()->routeIs('documents.create') ? 'active' : '' }}">
                    My Documents
                </a>
                <a href="{{ route('templates.index') }}" class="navbar-item {{ request()->routeIs('templates.index') || request()->routeIs('templates.edit') || request()->routeIs('templates.create') || request()->routeIs('templates.fill') ? 'active' : '' }}">
                    Templates
                </a>
                <a href="{{ route('upload-legacy.show') }}" class="navbar-item {{ request()->routeIs('upload-legacy.show') ? 'active' : '' }}">
                    Legacy Upload
                </a>
            </nav>
            
            <div class="navbar-user">
                <div class="user-avatar" onclick="window.location.href='{{ route('profile.edit') }}'" style="cursor: pointer;" data-tooltip="Manage Profile & Password" data-tooltip-pos="bottom">
                    @php
                        $words = explode(' ', Auth::user()->name);
                        $initials = '';
                        foreach ($words as $w) {
                            $initials .= strtoupper(substr($w, 0, 1));
                        }
                        $initials = substr($initials, 0, 2);
                    @endphp
                    {{ $initials }}
                </div>
                <div class="user-details" onclick="window.location.href='{{ route('profile.edit') }}'" style="cursor: pointer;" data-tooltip="Manage Profile & Password" data-tooltip-pos="bottom">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">{{ Auth::user()->office_name ?? 'Court Operator' }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: inline;">
                    @csrf
                    <button type="submit" class="navbar-logout-btn" onclick="showLoader()" data-tooltip="Sign out of your account" data-tooltip-pos="bottom">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endauth

    <div class="app-container">
        <!-- Main Content Area -->
        <main class="app-content {{ !auth()->check() ? 'full-width' : '' }}">
            <div class="container-limit">
                @auth
                <!-- Header Bar -->
                <header class="content-header animate-fade-in">
                    <div class="header-title">
                        <h1>@yield('page-title', 'Overview')</h1>
                    </div>
                    <div class="header-actions">
                        @yield('header-actions')
                    </div>
                </header>
                @endauth

                <!-- Flash Notifications (Toast triggers) -->
                @if(session('success'))
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        showToast("{{ session('success') }}", 'success');
                    });
                </script>
                @endif

                @if(session('error'))
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        showToast("{{ session('error') }}", 'error');
                    });
                </script>
                @endif

                @if($errors->any())
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        showToast("{{ $errors->first() }}", 'error');
                    });
                </script>
                @endif

                <!-- Page Body -->
                <div class="content-body">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- JS Utility scripts -->
    <script>
        // Page loader show/hide
        window.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('global-loader');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('hide');
                }, 400); // smooth hide
            }
        });

        function showLoader() {
            const loader = document.getElementById('global-loader');
            if (loader) {
                loader.classList.remove('hide');
                loader.querySelector('.loader-text').innerText = 'Processing request...';
            }
        }

        // Global Toast Notification Helper
        function showToast(message, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            let icon = 'ℹ️';
            if (type === 'success') icon = '✅';
            if (type === 'error') icon = '❌';
            if (type === 'warning') icon = '⚠️';
            
            toast.innerHTML = `<span class="toast-icon">${icon}</span><span class="toast-message">${message}</span>`;
            container.appendChild(toast);
            
            // Slide in
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 50);

            // Auto-remove after 4 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        }

        // Global Pitch-Black Tooltip Engine
        document.addEventListener('DOMContentLoaded', () => {
            let tooltipEl = document.getElementById('app-tooltip');
            if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.id = 'app-tooltip';
                document.body.appendChild(tooltipEl);
            }

            document.addEventListener('mouseover', (e) => {
                const target = e.target.closest('[data-tooltip]');
                if (!target) return;

                const text = target.getAttribute('data-tooltip');
                if (!text) return;

                tooltipEl.textContent = text;
                tooltipEl.className = '';

                const rect = target.getBoundingClientRect();
                const pos = target.getAttribute('data-tooltip-pos') || 'top';

                let top, left;
                if (pos === 'bottom') {
                    top = rect.bottom + 8;
                    left = rect.left + (rect.width / 2);
                    tooltipEl.classList.add('tooltip-bottom');
                } else {
                    top = rect.top - 8;
                    left = rect.left + (rect.width / 2);
                    tooltipEl.classList.add('tooltip-top');
                }

                tooltipEl.style.top = `${top}px`;
                tooltipEl.style.left = `${left}px`;

                // Calculate tooltip dimensions for screen clamping
                const tooltipRect = tooltipEl.getBoundingClientRect();
                if (pos === 'top') {
                    tooltipEl.style.top = `${rect.top - tooltipRect.height - 8}px`;
                }

                let translateX = -50;
                const halfWidth = tooltipRect.width / 2;
                if (left - halfWidth < 12) {
                    translateX = 0;
                    tooltipEl.style.left = '12px';
                } else if (left + halfWidth > window.innerWidth - 12) {
                    translateX = -100;
                    tooltipEl.style.left = `${window.innerWidth - 12}px`;
                }

                tooltipEl.style.transform = `translateX(${translateX}%) translateY(0)`;
                tooltipEl.classList.add('show');
            });

            document.addEventListener('mouseout', (e) => {
                const target = e.target.closest('[data-tooltip]');
                if (target) {
                    tooltipEl.classList.remove('show');
                }
            });

            document.addEventListener('click', () => {
                tooltipEl.classList.remove('show');
            });

            window.addEventListener('scroll', () => {
                tooltipEl.classList.remove('show');
            }, true);
        });
    </script>
    @yield('scripts')
</body>
</html>
