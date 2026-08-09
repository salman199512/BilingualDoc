@extends('errors.layout')

@section('theme-class', 'theme-blue')
@section('title', '404 - Document or Page Not Found')
@section('code', '404')
@section('badge-text', 'Record Not Found')

@section('icon')
<svg width="46" height="46" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6"/>
</svg>
@endsection

@section('heading', 'Document or Page Not Found')
@section('subtitle-gu', 'દસ્તાવેજ અથવા પાનું ઉપલબ્ધ નથી')

@section('message')
The legal document, template, or case route you requested cannot be located. It may have been archived, renamed, deleted, or the URL might be mistyped.
@endsection

@section('custom-actions')
    @auth
        <a href="{{ route('dashboard') }}" class="btn-action btn-primary-action">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('documents.index') }}" class="btn-action btn-secondary-action">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            My Documents
        </a>
    @else
        <a href="{{ route('login') }}" class="btn-action btn-primary-action">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Operator Login
        </a>
    @endauth
    <button type="button" onclick="history.back()" class="btn-action btn-secondary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Go Back
    </button>
@endsection
