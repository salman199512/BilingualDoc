@extends('errors.layout')

@section('theme-class', 'theme-amber')
@section('title', '419 - Session Expired')
@section('code', '419')
@section('badge-text', 'Security Timeout')

@section('icon')
<svg width="46" height="46" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('heading', 'Page Session Expired')
@section('subtitle-gu', 'સુરક્ષા સત્ર સમાપ્ત થયું છે')

@section('message')
Due to inactivity or an expired CSRF security token, your session has timed out to protect confidential court documents. Please refresh the page or log in again to continue your work.
@endsection

@section('custom-actions')
    <button type="button" onclick="window.location.reload()" class="btn-action btn-primary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Refresh &amp; Retry
    </button>
    <a href="{{ route('login') }}" class="btn-action btn-secondary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Re-Login
    </a>
@endsection
