@extends('layouts.app')

@section('page-title', 'Create New Document')

@section('content')
<div class="card animate-fade-in" style="max-width: 600px; margin: 0 auto;">
    <div class="card-title">
        <span>Document Specifications</span>
    </div>

    <form action="{{ route('documents.store') }}" method="POST" class="form-light" onsubmit="showLoader()">
        @csrf
        
        <div class="form-group">
            <label for="title">Document Title</label>
            <input type="text" name="title" id="title" class="form-input" placeholder="e.g. Official Notice Regarding Public Holidays" required autofocus>
            @error('title')
                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="font_gujarati">Default Gujarati Font</label>
            <select name="font_gujarati" id="font_gujarati" class="form-input">
                <option value="Noto Sans Gujarati">Noto Sans Gujarati (Recommended)</option>
                <option value="Lohit Gujarati">Lohit Gujarati</option>
                <option value="Noto Serif Gujarati">Noto Serif Gujarati</option>
            </select>
            @error('font_gujarati')
                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="font_english">Default English Font</label>
            <select name="font_english" id="font_english" class="form-input">
                <option value="Times New Roman">Times New Roman (Standard)</option>
                <option value="Arial">Arial</option>
                <option value="Calibri">Calibri</option>
            </select>
            @error('font_english')
                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Initialize Editor</button>
        </div>
    </form>
</div>
@endsection
