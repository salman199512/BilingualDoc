@extends('layouts.app')

@section('tab-title', 'Register Operator')

@section('content')
<div class="auth-page">
    <!-- Top Guest Navigation Bar -->
    <header class="auth-navbar">
        <a href="{{ url('/') }}" class="auth-brand">
            <span class="auth-brand-icon">⚖️</span>
            <div>
                <span class="auth-brand-text">{{ config('app.name', 'BilingualDoc') }}</span>
                <span class="auth-brand-subtext">Court &amp; Legal Automation</span>
            </div>
        </a>

        <div class="auth-nav-status">
            <span class="auth-status-dot"></span>
            <span>Court Platform Online</span>
        </div>
    </header>

    <!-- Main Split Grid -->
    <div class="auth-main-wrapper">
        <div class="auth-grid">
            <!-- Left Hero Showcase -->
            <div class="auth-showcase animate-fade-in">
                <div class="auth-hero-pill">
                    <span>🚀 New Operator Onboarding</span>
                    <span>•</span>
                    <span>Legal SaaS</span>
                </div>

                <h1 class="auth-hero-title">
                    Join India's Fastest <span>Bilingual Court</span> Drafting Platform
                </h1>

                <p class="auth-hero-desc">
                    Empower your legal firm or court registry with instant Gujarati Unicode conversion, automated bail petitions, and real-time version-controlled legal drafts.
                </p>

                <!-- Metrics Grid -->
                <div class="auth-metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-blue">
                            📚
                        </div>
                        <div>
                            <div class="metric-value">120+</div>
                            <div class="metric-label">Court Templates</div>
                            <div class="metric-sub">Ready to customize &amp; fill</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-emerald">
                            ⚡
                        </div>
                        <div>
                            <div class="metric-value">0-Sec</div>
                            <div class="metric-label">Legacy Conversion</div>
                            <div class="metric-sub">PageMaker &amp; PDF Auto-clean</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-violet">
                            🔤
                        </div>
                        <div>
                            <div class="metric-value">100%</div>
                            <div class="metric-label">Unicode Gujarati</div>
                            <div class="metric-sub">Zero font missing issues</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-amber">
                            🛡️
                        </div>
                        <div>
                            <div class="metric-value">100%</div>
                            <div class="metric-label">Private &amp; Secure</div>
                            <div class="metric-sub">Encrypted client records</div>
                        </div>
                    </div>
                </div>

                <!-- Feature Tags -->
                <div class="auth-feature-tags">
                    <span class="feature-tag">⚖️ High Court Compliant</span>
                    <span class="feature-tag">📑 1-Click DOCX &amp; PDF</span>
                    <span class="feature-tag">🔄 Auto Version History</span>
                    <span class="feature-tag">🔒 256-bit Encrypted</span>
                </div>
            </div>

            <!-- Right Registration Form -->
            <div class="auth-form-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <span class="auth-header-badge">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            New Operator Registration
                        </span>
                        <h2>Create Account</h2>
                        <p>Set up your credentials to start drafting legal files</p>
                    </div>

                    <form action="{{ route('register') }}" method="POST" onsubmit="showLoader()">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Full Name / Office Title</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon-prefix">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                                <input type="text" name="name" id="name" class="form-input" placeholder="Adv. Rajesh Patel" value="{{ old('name') }}" required autofocus>
                            </div>
                            @error('name')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 500;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Official Email</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon-prefix">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </span>
                                <input type="email" name="email" id="email" class="form-input" placeholder="operator@bilingual.com" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 500;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Security Password</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon-prefix">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input type="password" name="password" id="password" class="form-input" placeholder="Min. 8 characters" required>
                                <button type="button" class="input-toggle-suffix" onclick="toggleRegPassword('password', 'eye-icon-1')" title="Toggle password visibility">
                                    <svg id="eye-icon-1" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 500;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon-prefix">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" placeholder="Repeat password" required>
                                <button type="button" class="input-toggle-suffix" onclick="toggleRegPassword('password_confirmation', 'eye-icon-2')" title="Toggle password visibility">
                                    <svg id="eye-icon-2" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="auth-action-btn">
                            <span>Register &amp; Open Workspace</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>

                    <div class="auth-footer">
                        <p>Already registered? <a href="{{ route('login') }}">Sign in here</a></p>
                    </div>

                    <div class="auth-security-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Protected by Judicial Access Control Protocols</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRegPassword(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            `;
        }
    }
</script>
@endsection
