@extends('errors.layout')

@section('theme-class', 'theme-amber')
@section('title', '429 - Too Many Requests')
@section('code', '429')
@section('badge-text', 'Rate Limit Exceeded')

@section('icon')
<svg width="46" height="46" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>
</svg>
@endsection

@section('heading', 'Too Many Requests')
@section('subtitle-gu', 'બહુ વધુ વિનંતીઓ - થોડો સમય રાહ જુઓ')

@section('message')
Our court security system detected an unusually high volume of actions in a short period. Please wait a brief moment before attempting your request again.
@endsection

@section('custom-actions')
    <button type="button" onclick="setTimeout(() => window.location.reload(), 1000)" class="btn-action btn-primary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Retry Request
    </button>
    @auth
        <a href="{{ route('dashboard') }}" class="btn-action btn-secondary-action">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn-action btn-secondary-action">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Return to Login
        </a>
    @endauth
@endsection
