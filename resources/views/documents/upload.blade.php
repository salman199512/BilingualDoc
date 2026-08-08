@extends('layouts.app')

@section('page-title', 'Document Upload (Word / PDF)')

@section('content')
<div class="card animate-fade-in" style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.15rem; font-weight: 600; color: #0f172a; margin-bottom: 0.5rem;">Document Import & Reformatting</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
            Upload an existing Word document (<code>.docx</code>, <code>.doc</code>) or <strong>PDF file (<code>.pdf</code>)</strong>. The system will automatically parse the file, adjust pages to <strong>A4 Portrait</strong>, set margins to <strong>4cm left/right</strong> and <strong>2cm top/bottom</strong>, and apply correct bilingual fonts (Times New Roman / selected Gujarati font) at <strong>13pt</strong>.
        </p>
    </div>

    <form action="{{ route('upload-legacy.store') }}" method="POST" enctype="multipart/form-data" class="form-light" onsubmit="showLoader()">
        @csrf
        
        <div class="form-group">
            <label for="title">New Document Title</label>
            <input type="text" name="title" id="title" class="form-input" placeholder="e.g. Imported Government Notice" required>
            @error('title')
                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="font_gujarati">Default Gujarati Font</label>
                <select name="font_gujarati" id="font_gujarati" class="form-input">
                    <option value="Noto Sans Gujarati">Noto Sans Gujarati</option>
                    <option value="Lohit Gujarati">Lohit Gujarati</option>
                    <option value="Noto Serif Gujarati">Noto Serif Gujarati</option>
                </select>
            </div>

            <div class="form-group">
                <label for="font_english">Default English Font</label>
                <select name="font_english" id="font_english" class="form-input">
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Arial">Arial</option>
                    <option value="Calibri">Calibri</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Select Document File (.docx, .doc, .pdf)</label>
            <div class="upload-dropzone" onclick="document.getElementById('document-file-input').click()">
                <span class="upload-icon" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;">📂</span>
                <span class="upload-text" id="dropzone-text" style="font-weight: 600; font-size: 0.95rem; display: block;">Click to choose file or drag & drop</span>
                <span class="upload-subtext" style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Microsoft Word (.docx, .doc) or PDF (.pdf) files up to 15MB</span>
                <input type="file" name="document_file" id="document-file-input" style="display: none;" accept=".docx,.doc,.pdf" onchange="updateFileName(this)" required>
            </div>
            @error('document_file')
                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Upload and Reformat</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function updateFileName(input) {
        const text = document.getElementById('dropzone-text');
        if (input.files && input.files[0]) {
            text.innerText = 'Selected file: ' + input.files[0].name;
            text.style.color = 'var(--primary-blue-hover)';
        } else {
            text.innerText = 'Click to choose file or drag & drop';
            text.style.color = 'var(--text-primary)';
        }
    }
</script>
@endsection
