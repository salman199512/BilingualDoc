@extends('layouts.app')

@section('content')
<div class="auth-page">
    <div class="auth-card animate-fade-in">
        <div class="auth-header">
            <h2>BilingualDoc Login</h2>
            <p>Enter details to access document platform</p>
        </div>

        <form action="{{ route('login') }}" method="POST" onsubmit="showLoader()">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="admin@bilingual.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span style="color: #f87171; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
                @error('password')
                    <span style="color: #f87171; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group flex-space-between" style="margin-top: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; text-transform: none; font-size: 0.9rem; color: #cbd5e1; cursor: pointer;">
                    <input type="checkbox" name="remember" style="accent-color: var(--accent-color);"> Remember Me
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">
                Login to Account
            </button>
        </form>

        <div class="auth-footer">
            <p>New operator? <a href="{{ route('register') }}">Register here</a></p>
        </div>
    </div>
</div>
@endsection
