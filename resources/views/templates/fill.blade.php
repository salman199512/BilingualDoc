@extends('layouts.app')

@section('page-title')
    Generate Document: <span style="font-weight: 500;">{{ $template->title }}</span>
@endsection

@section('content')
<div class="card animate-fade-in" style="max-width: 700px; margin: 0 auto;">
    <div class="card-title">
        <span>Template Field Input</span>
    </div>
    
    <div style="margin-bottom: 2rem;">
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            {{ $template->description ?? 'Fill in the fields below to merge variables into the template and create a new editable document.' }}
        </p>
    </div>

    <form action="{{ route('templates.merge', $template->id) }}" method="POST" class="form-light" onsubmit="showLoader()">
        @csrf
        
        @if($template->fields->count() > 0)
            @foreach($template->fields as $field)
                <div class="form-group">
                    <label for="field-{{ $field->field_key }}">{{ $field->field_label }}</label>
                    
                    @if($field->field_type === 'textarea')
                        <textarea name="{{ $field->field_key }}" id="field-{{ $field->field_key }}" class="form-input" placeholder="Enter content..." rows="4">{{ old($field->field_key, $field->default_value) }}</textarea>
                    @elseif($field->field_type === 'date')
                        <input type="date" name="{{ $field->field_key }}" id="field-{{ $field->field_key }}" class="form-input" value="{{ old($field->field_key, $field->default_value) }}">
                    @elseif($field->field_type === 'number')
                        <input type="number" step="any" name="{{ $field->field_key }}" id="field-{{ $field->field_key }}" class="form-input" placeholder="0" value="{{ old($field->field_key, $field->default_value) }}">
                    @else
                        <input type="text" name="{{ $field->field_key }}" id="field-{{ $field->field_key }}" class="form-input" placeholder="Enter text..." value="{{ old($field->field_key, $field->default_value) }}">
                    @endif
                    
                    @error($field->field_key)
                        <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach
        @else
            <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No custom input fields defined for this template. You can merge directly to create the document.</p>
        @endif

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <a href="{{ route('templates.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Merge & Edit Document</button>
        </div>
    </form>
</div>
@endsection
