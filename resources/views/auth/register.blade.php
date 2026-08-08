@extends('layouts.app')

@section('content')
<div class="auth-page">
    <div class="auth-card animate-fade-in">
        <div class="auth-header">
            <h2>BilingualDoc Register</h2>
            <p>Create a new operator account</p>
        </div>

        <form action="{{ route('register') }}" method="POST" onsubmit="showLoader()">
            @csrf
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-input" placeholder="Operator Name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span style="color: #f87171; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="operator@bilingual.com" value="{{ old('email') }}" required>
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

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            <p>Already registered? <a href="{{ route('login') }}">Login here</a></p>
        </div>
    </div>
</div>
@endsection
