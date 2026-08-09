@extends('errors.layout')

@section('theme-class', 'theme-indigo')
@section('title', '403 - Access Forbidden')
@section('code', '403')
@section('badge-text', 'Access Denied')

@section('icon')
<svg width="46" height="46" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
</svg>
@endsection

@section('heading', 'Access Forbidden / Restricted')
@section('subtitle-gu', 'પ્રવેશ પ્રતિબંધિત - અધિકાર નથી')

@section('message')
{{ $exception && $exception->getMessage() ? $exception->getMessage() : 'You do not have the required judicial clearance or permissions to view or edit this document record.' }}
@endsection

@section('custom-actions')
    @auth
        <a href="{{ route('dashboard') }}" class="btn-action btn-primary-action">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Return to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn-action btn-primary-action">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Authenticate with Authorized Account
        </a>
    @endauth
    <button type="button" onclick="history.back()" class="btn-action btn-secondary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Go Back
    </button>
@endsection
