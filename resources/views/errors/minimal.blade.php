@extends('errors.layout')

@php
    $errorCode = $code ?? ($exception ? $exception->getStatusCode() : 500);
    $themeClass = 'theme-blue';
    if (in_array($errorCode, [401, 403])) {
        $themeClass = 'theme-indigo';
    } elseif (in_array($errorCode, [419, 429])) {
        $themeClass = 'theme-amber';
    } elseif (in_array($errorCode, [500, 400])) {
        $themeClass = 'theme-red';
    } elseif ($errorCode == 503) {
        $themeClass = 'theme-teal';
    }
@endphp

@section('theme-class', $themeClass)
@section('title', trim($__env->yieldContent('title')) ? trim($__env->yieldContent('title')) : 'Error ' . $errorCode)
@section('code', $errorCode)

@section('icon')
    @if(in_array($errorCode, [401, 403]))
        <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    @elseif($errorCode == 419)
        <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    @elseif($errorCode == 429)
        <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    @elseif($errorCode == 503)
        <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    @elseif($errorCode == 500)
        <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    @else
        <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    @endif
@endsection

@section('badge-text', 'HTTP ' . $errorCode)
@section('heading', trim($__env->yieldContent('message')) ? trim($__env->yieldContent('message')) : 'An Unexpected Error Occurred')
@section('subtitle-gu', 'વિનંતી પૂર્ણ કરવામાં સમસ્યા આવી છે')
@section('message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'An error occurred while processing your request. Please try again or return to the dashboard.' }}
@endsection
