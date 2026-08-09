@extends('errors.layout')

@section('theme-class', 'theme-teal')
@section('title', '503 - Service Under Maintenance')
@section('code', '503')
@section('badge-text', 'Scheduled Maintenance')

@section('icon')
<svg width="46" height="46" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
</svg>
@endsection

@section('heading', 'Service Under Maintenance')
@section('subtitle-gu', 'પ્લેટફોર્મ અપગ્રેડ અને મેન્ટેનન્સ ચાલુ છે')

@section('message')
{{ $exception && $exception->getMessage() ? $exception->getMessage() : 'BilingualDoc is currently undergoing scheduled judicial system upgrades and database optimizations. We will be back online shortly.' }}
@endsection

@section('custom-actions')
    <button type="button" onclick="window.location.reload()" class="btn-action btn-primary-action">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Check System Status
    </button>
@endsection
