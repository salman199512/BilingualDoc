<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <div id="global-loader" class="loader-overlay hide" style="display: none;">
        <div class="loader-spinner"></div>
        <div class="loader-text">Loading Legal Automation Platform...</div>
    </div>
    <script>
        (function() {
            var loader = document.getElementById('global-loader');
            if (loader) {
                setTimeout(function() {
                    loader.classList.add('hide');
                    loader.style.display = 'none';
                }, 300);
            }
        })();
    </script>

    @auth
    <!-- Horizontal Top Navigation Navbar -->
    <header class="app-navbar">
        <div class="navbar-limit">
            <div class="navbar-brand" onclick="window.location.href='{{ route('dashboard') }}'">
                <span class="brand-logo-icon">⚖️</span>
                <div>
                    <span class="brand-text">BilingualDoc</span>
                    <span class="brand-subtext">Court &amp; Legal Automation</span>
                </div>
            </div>
            
            <!-- Desktop Navbar Menu -->
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
                <div class="user-avatar" onclick="window.location.href='{{ route('profile.edit') }}'" style="cursor: pointer;" data-tooltip="Manage Profile &amp; Password" data-tooltip-pos="bottom">
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
                <div class="user-details" onclick="window.location.href='{{ route('profile.edit') }}'" style="cursor: pointer;" data-tooltip="Manage Profile &amp; Password" data-tooltip-pos="bottom">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">{{ Auth::user()->office_name ?? 'Court Operator' }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: inline;">
                    @csrf
                    <button type="submit" class="navbar-logout-btn" onclick="showLoader()" data-tooltip="Sign out of your account" data-tooltip-pos="bottom">
                        Logout
                    </button>
                </form>

                <!-- Mobile Hamburger Toggle Button -->
                <button type="button" class="navbar-hamburger" id="nav-toggle-btn" aria-label="Toggle Navigation Menu">
                    <svg id="hamburger-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation Overlay -->
        <div class="navbar-mobile-drawer" id="mobile-drawer">
            <div class="mobile-drawer-header">
                <div class="user-info-mobile" onclick="window.location.href='{{ route('profile.edit') }}'">
                    <div class="user-avatar-mobile">{{ $initials }}</div>
                    <div>
                        <div class="mobile-user-name">{{ Auth::user()->name }}</div>
                        <div class="mobile-user-role">{{ Auth::user()->office_name ?? 'Court Operator' }}</div>
                    </div>
                </div>
            </div>
            
            <nav class="mobile-menu-links">
                <a href="{{ route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="mobile-nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('documents.index') }}" class="mobile-nav-link {{ request()->routeIs('documents.index') || request()->routeIs('documents.edit') || request()->routeIs('documents.create') ? 'active' : '' }}">
                    <span class="mobile-nav-icon">📁</span>
                    <span>My Documents</span>
                </a>
                <a href="{{ route('templates.index') }}" class="mobile-nav-link {{ request()->routeIs('templates.index') || request()->routeIs('templates.edit') || request()->routeIs('templates.create') || request()->routeIs('templates.fill') ? 'active' : '' }}">
                    <span class="mobile-nav-icon">📑</span>
                    <span>Templates</span>
                </a>
                <a href="{{ route('upload-legacy.show') }}" class="mobile-nav-link {{ request()->routeIs('upload-legacy.show') ? 'active' : '' }}">
                    <span class="mobile-nav-icon">⚡</span>
                    <span>Legacy Upload</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="mobile-nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <span class="mobile-nav-icon">⚙️</span>
                    <span>My Profile &amp; Settings</span>
                </a>
            </nav>

            <div class="mobile-drawer-footer">
                <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                    @csrf
                    <button type="submit" class="btn btn-danger" style="width: 100%;" onclick="showLoader()">
                        🚪 Sign Out
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
        // Fail-safe Page Loader hiding
        function hideLoader() {
            const loader = document.getElementById('global-loader');
            if (loader) {
                loader.classList.add('hide');
                setTimeout(() => {
                    if (loader.classList.contains('hide')) {
                        loader.style.display = 'none';
                    }
                }, 350);
            }
        }

        function showLoader() {
            const loader = document.getElementById('global-loader');
            if (loader) {
                loader.style.display = 'flex';
                loader.classList.remove('hide');
                const textEl = loader.querySelector('.loader-text');
                if (textEl) textEl.innerText = 'Processing request...';
            }
        }

        // Hide loader on DOM ready, window load, and absolute timeout
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(hideLoader, 100);
        });
        window.addEventListener('load', hideLoader);
        setTimeout(hideLoader, 1000); // Safety fallback

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

        // Global Mobile Navigation Drawer Engine
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('nav-toggle-btn');
            const drawer = document.getElementById('mobile-drawer');
            const hamburgerIcon = document.getElementById('hamburger-icon');

            if (toggleBtn && drawer) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = drawer.classList.toggle('show');
                    if (isOpen) {
                        hamburgerIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>`;
                        document.body.style.overflow = 'hidden';
                    } else {
                        hamburgerIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>`;
                        document.body.style.overflow = '';
                    }
                });

                document.addEventListener('click', (e) => {
                    if (drawer.classList.contains('show') && !drawer.contains(e.target) && !toggleBtn.contains(e.target)) {
                        drawer.classList.remove('show');
                        hamburgerIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>`;
                        document.body.style.overflow = '';
                    }
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
