@extends('layouts.app')

@section('tab-title', 'Sign In')

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
            <!-- Left Hero Showcase & Social Proof Metrics -->
            <div class="auth-showcase animate-fade-in">
                <div class="auth-hero-pill">
                    <span>⚡ Judicial Suite 2.0</span>
                    <span>•</span>
                    <span>Gujarati &amp; English Engine</span>
                </div>

                <h1 class="auth-hero-title">
                    High-Precision <span>Bilingual Legal</span> Automation &amp; Drafting
                </h1>

                <p class="auth-hero-desc">
                    Streamline courtroom pleadings, bilingual bail petitions, and legal notices. Convert legacy PageMaker files and auto-align Gujarati &amp; English fonts in seconds.
                </p>

                <!-- Live Metrics Counts Grid -->
                <div class="auth-metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-blue">
                            📄
                        </div>
                        <div>
                            <div class="metric-value">50,000+</div>
                            <div class="metric-label">Documents Drafted</div>
                            <div class="metric-sub">High Court &amp; District files</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-emerald">
                            🎯
                        </div>
                        <div>
                            <div class="metric-value">99.9%</div>
                            <div class="metric-label">Font &amp; Margin Precision</div>
                            <div class="metric-sub">Noto Gujarati + Times Roman</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-amber">
                            🏛️
                        </div>
                        <div>
                            <div class="metric-value">120+</div>
                            <div class="metric-label">Court Templates</div>
                            <div class="metric-sub">Bail, Writs, Plaints &amp; Affidavits</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon-box metric-icon-violet">
                            ⚡
                        </div>
                        <div>
                            <div class="metric-value">10x</div>
                            <div class="metric-label">Drafting Velocity</div>
                            <div class="metric-sub">Instant PMD &amp; PDF Converter</div>
                        </div>
                    </div>
                </div>

                <!-- Floating Live Document Preview Mockup -->
                <div class="auth-preview-mockup">
                    <div class="mockup-header">
                        <span class="mockup-tag">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Live Legal Drafting Preview
                        </span>
                        <span class="mockup-badge">✓ Judicial A4 Formatted</span>
                    </div>
                    <div class="mockup-content">
                        <div class="mockup-gujarati">
                            ન્યાયાલય સમક્ષ: નામદાર પ્રિન્સિપાલ ડિસ્ટ્રિક્ટ એન્ડ સેશન્સ જજ સાહેબની કોર્ટમાં, અમદાવાદ
                        </div>
                        <div class="mockup-english">
                            IN THE COURT OF PRINCIPAL DISTRICT &amp; SESSIONS JUDGE, AT AHMEDABAD
                        </div>
                    </div>
                </div>

                <!-- Feature Tags -->
                <div class="auth-feature-tags">
                    <span class="feature-tag">⚖️ High Court Compliant</span>
                    <span class="feature-tag">🔄 PageMaker (.pmd) Importer</span>
                    <span class="feature-tag">📑 1-Click DOCX &amp; PDF</span>
                    <span class="feature-tag">🔒 256-bit Encrypted</span>
                </div>
            </div>

            <!-- Right Authentication Form -->
            <div class="auth-form-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <span class="auth-header-badge">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Authorized Operator Access
                        </span>
                        <h2>Sign In</h2>
                        <p>Enter your judicial credentials to access documents</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST" onsubmit="showLoader()">
                        @csrf
                        
                        <div class="form-group">
                            <label for="email">Operator Email</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon-prefix">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </span>
                                <input type="email" name="email" id="email" class="form-input" placeholder="operator@court.gov.in" value="{{ old('email') }}" required autofocus>
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
                                <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
                                <button type="button" class="input-toggle-suffix" onclick="togglePasswordVisibility()" title="Toggle password visibility">
                                    <svg id="eye-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 500;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group flex-space-between" style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; text-transform: none; font-size: 0.85rem; color: #475569; cursor: pointer; margin-bottom: 0;">
                                <input type="checkbox" name="remember" style="accent-color: var(--primary-blue); width: 16px; height: 16px; border-radius: 4px; cursor: pointer;"> 
                                Remember Session
                            </label>
                            <span style="font-size: 0.78rem; color: #64748b;">Court Terminal</span>
                        </div>

                        <button type="submit" class="auth-action-btn">
                            <span>Sign In to Workspace</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>

                    <div class="auth-footer">
                        <p>New operator or advocate? <a href="{{ route('register') }}">Create an account</a></p>
                    </div>

                    <div class="auth-security-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>256-bit Judicial Bank-Grade Encryption</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
            `;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            `;
        }
    }
</script>
@endsection
