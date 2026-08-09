@extends('errors.layout')

@section('theme-class', 'theme-indigo')
@section('title', '401 - Authentication Required')
@section('code', '401')
@section('badge-text', 'Authentication Required')

@section('icon')
<svg width="46" height="46" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
</svg>
@endsection

@section('heading', 'Authentication Required')
@section('subtitle-gu', 'પ્રવેશ માટે લોગિન કરવું અનિવાર્ય છે')

@section('message')
{{ $exception && $exception->getMessage() ? $exception->getMessage() : 'Your session could not be verified. Please log in with your valid operator credentials to access court documents and templates.' }}
@endsection

@section('custom-actions')
    <a href="{{ route('login') }}" class="btn-action btn-primary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Log In to Platform
    </a>
    <button type="button" onclick="history.back()" class="btn-action btn-secondary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Go Back
    </button>
@endsection
