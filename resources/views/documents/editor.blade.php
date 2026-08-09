@extends('layouts.app')

@section('tab-title', $document->title)

@section('page-title')
    <div style="display: flex; align-items: center; gap: 0.5rem; max-width: 480px;">
        <span style="color: var(--text-muted); font-size: 0.95rem; font-weight: 500;">Editing:</span>
        <span style="font-weight: 600; font-size: 1.05rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $document->title }}">{{ $document->title }}</span>
    </div>
@endsection

@section('header-actions')
    <div style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
        <button type="button" class="btn btn-sm btn-primary" onclick="saveDocument()" data-tooltip="Save document changes (Ctrl+S)" data-tooltip-pos="bottom">
            💾 Save
        </button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="triggerImportDocx()" data-tooltip="Upload & import Word document (.docx/.doc)" data-tooltip-pos="bottom">
            📂 Import DOCX
        </button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="triggerImportPageMaker()" data-tooltip="Upload & import Adobe PageMaker (.pmd/.p65/.pm6/.ptd)" data-tooltip-pos="bottom">
            📑 Import PageMaker
        </button>
        {{-- Temporarily hidden: Import PDF button
        <button type="button" class="btn btn-sm btn-secondary" onclick="triggerImportPdf()" data-tooltip="Upload & extract text from PDF document" data-tooltip-pos="bottom">
            📥 Import PDF
        </button>
        --}}
        <a href="{{ route('documents.export-docx', $document->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Export as formatted Word DOCX" data-tooltip-pos="bottom">
            🗂️ DOCX
        </a>
        <button type="button" class="btn btn-sm btn-secondary" onclick="downloadPerfectPdf()" data-tooltip="Export as bilingual Court PDF" data-tooltip-pos="bottom">
            📄 PDF
        </button>
        <a href="{{ route('documents.index') }}" class="btn btn-sm btn-danger" onclick="showLoader()" data-tooltip="Close editor & return to documents" data-tooltip-pos="bottom">
            ✕ Close
        </a>
    </div>
@endsection

@section('content')
<!-- Hidden File Inputs for DOCX, PDF and PageMaker Imports -->
<input type="file" id="editor-docx-file" accept=".docx,.doc" style="display: none;" onchange="handleDocxImport(this)">
<input type="file" id="editor-pdf-file" accept=".pdf" style="display: none;" onchange="handlePdfImport(this)">
<input type="file" id="editor-pagemaker-file" accept=".pmd,.p65,.pm6,.pm5,.ptd,.txt" style="display: none;" onchange="handlePageMakerImport(this)">

<div class="editor-workspace animate-fade-in">
    <!-- Main Editor -->
    <div class="editor-main">
        <!-- Formatting Toolbar -->
        <div class="editor-toolbar">
            <!-- History / Undo Redo -->
            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" onclick="execCmd('undo')" data-tooltip="Undo (Ctrl+Z)">↩</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('redo')" data-tooltip="Redo (Ctrl+Y)">↪</button>
            </div>

            <!-- Font Size Selector -->
            <div class="toolbar-group">
                <select class="toolbar-select" onchange="applyFontSize(this.value)" data-tooltip="Font Size">
                    <option value="10pt">10 pt</option>
                    <option value="11pt">11 pt</option>
                    <option value="12pt">12 pt</option>
                    <option value="13pt" selected>13 pt (Standard)</option>
                    <option value="14pt">14 pt</option>
                    <option value="16pt">16 pt</option>
                    <option value="18pt">18 pt</option>
                    <option value="24pt">24 pt</option>
                </select>
            </div>

            <!-- Text Styles -->
            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" onclick="execCmd('bold')" data-tooltip="Bold (Ctrl+B)"><strong>B</strong></button>
                <button type="button" class="toolbar-btn" onclick="execCmd('italic')" data-tooltip="Italic (Ctrl+I)"><em>I</em></button>
                <button type="button" class="toolbar-btn" onclick="execCmd('underline')" data-tooltip="Underline (Ctrl+U)"><u>U</u></button>
                <button type="button" class="toolbar-btn" onclick="execCmd('strikeThrough')" data-tooltip="Strikethrough"><s>S</s></button>
                <button type="button" class="toolbar-btn" onclick="execCmd('subscript')" data-tooltip="Subscript">X₂</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('superscript')" data-tooltip="Superscript">X²</button>
            </div>

            <!-- Colors -->
            <div class="toolbar-group" style="gap: 4px;">
                <label data-tooltip="Text color" style="display: flex; align-items: center; gap: 2px; cursor: pointer; font-size: 0.8rem;">
                    🎨 <input type="color" class="toolbar-color-picker" id="text-color" value="#000000" onchange="execCmd('foreColor', this.value)">
                </label>
                <label data-tooltip="Highlight color" style="display: flex; align-items: center; gap: 2px; cursor: pointer; font-size: 0.8rem;">
                    🖌️ <input type="color" class="toolbar-color-picker" id="bg-color" value="#ffff00" onchange="execCmd('hiliteColor', this.value)">
                </label>
            </div>
            
            <!-- Alignment Actions -->
            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" onclick="execCmd('justifyLeft')" data-tooltip="Align Left">⬅</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('justifyCenter')" data-tooltip="Align Center">📭</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('justifyRight')" data-tooltip="Align Right">➡</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('justifyFull')" data-tooltip="Justify text">↔</button>
            </div>
            
            <!-- Lists & Indents -->
            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" onclick="execCmd('insertUnorderedList')" data-tooltip="Bulleted list">•≡</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('insertOrderedList')" data-tooltip="Numbered list">1≡</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('outdent')" data-tooltip="Decrease indent">⇤</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('indent')" data-tooltip="Increase indent">⇥</button>
            </div>

            <!-- Insert Options -->
            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" onclick="openTableModal()" data-tooltip="Insert table">📊 Table</button>
                <button type="button" class="toolbar-btn" onclick="openShapeModal()" data-tooltip="Insert shape / box">⬡ Shape</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('insertHorizontalRule')" data-tooltip="Insert horizontal line">―</button>
                <button type="button" class="toolbar-btn" onclick="insertLinkPrompt()" data-tooltip="Insert hyperlink">🔗</button>
                <button type="button" class="toolbar-btn" onclick="insertCurrentDate()" data-tooltip="Insert today's date">📅</button>
                <button type="button" class="toolbar-btn" onclick="execCmd('removeFormat')" data-tooltip="Clear formatting">⌫</button>
            </div>

            <!-- Font / Formatting Helper -->
            <div class="toolbar-group">
                <button type="button" class="btn btn-sm btn-secondary" onclick="runBilingualFormatter()" data-tooltip="Auto-detect & format Gujarati & English scripts" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                    ✨ Auto-Format Scripts
                </button>
            </div>

            <!-- Dynamic Save Indicator -->
            <div style="margin-left: auto; font-size: 0.82rem; font-weight: 500; color: var(--text-muted);" id="save-indicator">
                All changes saved.
            </div>
        </div>

        <!-- Virtual Page Canvas -->
        <div class="editor-canvas">
            <div id="document-body" class="document-page" contenteditable="true" oninput="markUnsaved()">
                {!! $document->html_content !!}
            </div>
        </div>
    </div>

    <!-- Sidebar Info & Options -->
    <div class="editor-sidebar">
        <!-- Page Layout Specs -->
        <div class="sidebar-box">
            <h3 class="sidebar-box-title">Document Settings</h3>
            <div class="form-light">
                <div class="form-group">
                    <label>Page Size</label>
                    <input type="text" class="form-input" value="A4 Portrait (21cm x 29.7cm)" readonly disabled>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 1rem;">
                    <div class="form-group">
                        <label>Left Margin (cm)</label>
                        <input type="number" id="margin-left" class="form-input" value="{{ $document->margin_left / 10 }}" oninput="updateMarginStyle()">
                    </div>
                    <div class="form-group">
                        <label>Right Margin (cm)</label>
                        <input type="number" id="margin-right" class="form-input" value="{{ $document->margin_right / 10 }}" oninput="updateMarginStyle()">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                    <div class="form-group">
                        <label>Top Margin (cm)</label>
                        <input type="number" id="margin-top" class="form-input" value="{{ $document->margin_top / 10 }}" oninput="updateMarginStyle()">
                    </div>
                    <div class="form-group">
                        <label>Bottom Margin (cm)</label>
                        <input type="number" id="margin-bottom" class="form-input" value="{{ $document->margin_bottom / 10 }}" oninput="updateMarginStyle()">
                    </div>
                </div>

                <div class="form-group">
                    <label>Gujarati Font</label>
                    <select id="font-gujarati" class="form-input" onchange="runBilingualFormatter()">
                        <option value="Noto Sans Gujarati" {{ $document->font_gujarati === 'Noto Sans Gujarati' ? 'selected' : '' }}>Noto Sans Gujarati</option>
                        <option value="Lohit Gujarati" {{ $document->font_gujarati === 'Lohit Gujarati' ? 'selected' : '' }}>Lohit Gujarati</option>
                        <option value="Noto Serif Gujarati" {{ $document->font_gujarati === 'Noto Serif Gujarati' ? 'selected' : '' }}>Noto Serif Gujarati</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>English Font</label>
                    <select id="font-english" class="form-input" onchange="runBilingualFormatter()">
                        <option value="Times New Roman" {{ $document->font_english === 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                        <option value="Arial" {{ $document->font_english === 'Arial' ? 'selected' : '' }}>Arial</option>
                        <option value="Calibri" {{ $document->font_english === 'Calibri' ? 'selected' : '' }}>Calibri</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Backups & Versions -->
        <div class="sidebar-box">
            <h3 class="sidebar-box-title">Audit History</h3>
            <div class="version-list">
                @if($versions->count() > 0)
                    @foreach($versions as $ver)
                        <div class="version-item">
                            <div class="version-info">
                                <span class="version-number">Version #{{ $ver->version_number }}</span>
                                <span class="version-time">{{ $ver->created_at->format('M d, H:i A') }}</span>
                            </div>
                            <form action="{{ route('documents.restore-version', [$document->id, $ver->id]) }}" method="POST" onsubmit="return confirm('Restore document to Version #{{ $ver->version_number }}? Your current changes will be saved to history.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Restore</button>
                            </form>
                        </div>
                    @endforeach
                @else
                    <p style="font-size: 0.85rem; color: var(--text-muted); text-align: center;">No history backups found yet. Saves generate versions.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Insert Table -->
<div id="table-modal" class="editor-modal">
    <div class="modal-content">
        <h3 class="modal-header">Insert Table</h3>
        <div class="form-light">
            <div class="form-group">
                <label for="table-rows">Number of Rows</label>
                <input type="number" id="table-rows" class="form-input" value="3" min="1" max="15">
            </div>
            <div class="form-group">
                <label for="table-cols">Number of Columns</label>
                <input type="number" id="table-cols" class="form-input" value="3" min="1" max="8">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeTableModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="confirmInsertTable()">Insert Table</button>
        </div>
    </div>
</div>

<!-- Modal: Insert Shape -->
<div id="shape-modal" class="editor-modal">
    <div class="modal-content">
        <h3 class="modal-header">Insert Shape / Box</h3>
        <div class="form-light">
            <div class="form-group">
                <label for="shape-type">Shape Style</label>
                <select id="shape-type" class="form-input">
                    <option value="textbox">Floating Text Box</option>
                    <option value="rect">Rectangle Block</option>
                    <option value="circle">Oval / Circle Block</option>
                    <option value="line">Horizontal Line</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeShapeModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="confirmInsertShape()">Insert Shape</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- PDF.js CDN for reliable client-side PDF text extraction -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    let isUnsaved = false;

    // Execute editor commands
    function execCmd(command, val = null) {
        document.execCommand(command, false, val);
        markUnsaved();
    }

    function markUnsaved() {
        isUnsaved = true;
        const indicator = document.getElementById('save-indicator');
        indicator.innerText = 'Unsaved changes...';
        indicator.style.color = '#ef4444';
    }

    // Apply custom font size
    function applyFontSize(size) {
        const selection = window.getSelection();
        if (!selection.rangeCount) return;
        
        const span = document.createElement('span');
        span.style.fontSize = size;
        
        const range = selection.getRangeAt(0);
        if (range.collapsed) {
            execCmd('fontSize', '3');
        } else {
            span.appendChild(range.extractContents());
            range.insertNode(span);
        }
        markUnsaved();
    }

    // Apply custom font family
    function applyFontFamily(font) {
        execCmd('fontName', font);
    }

    // Insert Link Prompt
    function insertLinkPrompt() {
        const url = prompt('Enter URL link (e.g. https://court.gov.in):');
        if (url) {
            execCmd('createLink', url);
        }
    }

    // Insert Current Date
    function insertCurrentDate() {
        const now = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const dateStr = now.toLocaleDateString('en-US', options);
        execCmd('insertText', dateStr);
    }

    // Live margin adjustments
    function updateMarginStyle() {
        const left = document.getElementById('margin-left').value || 4;
        const right = document.getElementById('margin-right').value || 4;
        const top = document.getElementById('margin-top').value || 2;
        const bottom = document.getElementById('margin-bottom').value || 2;

        const page = document.getElementById('document-body');
        page.style.paddingLeft = left + 'cm';
        page.style.paddingRight = right + 'cm';
        page.style.paddingTop = top + 'cm';
        page.style.paddingBottom = bottom + 'cm';
        markUnsaved();
    }

    // Live bilingual format split
    function runBilingualFormatter() {
        const guFont = document.getElementById('font-gujarati').value;
        const enFont = document.getElementById('font-english').value;
        const container = document.getElementById('document-body');

        // Capture caret position if focused
        const selection = window.getSelection();
        let savedRange = null;
        if (selection.rangeCount > 0 && container.contains(selection.getRangeAt(0).commonAncestorContainer)) {
            savedRange = selection.getRangeAt(0).cloneRange();
        }

        applyBilingualFormatting(container, guFont, enFont);

        if (savedRange) {
            selection.removeAllRanges();
            selection.addRange(savedRange);
        }

        markUnsaved();
        showToast('Bilingual Gujarati & English scripts formatted!', 'success');
    }

    function applyBilingualFormatting(element, guFont, enFont) {
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    if (node.parentElement && (node.parentElement.closest('style') || node.parentElement.closest('script'))) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    if (node.nodeValue.trim() === '') {
                        return NodeFilter.FILTER_SKIP;
                    }
                    return NodeFilter.FILTER_ACCEPT;
                }
            },
            false
        );

        const nodesToProcess = [];
        let currentNode;
        while (currentNode = walker.nextNode()) {
            nodesToProcess.push(currentNode);
        }

        const gujaratiRegex = /[\u0A80-\u0AFF]/;

        nodesToProcess.forEach(textNode => {
            const text = textNode.nodeValue;
            if (!text) return;

            let currentScript = null;
            let currentChunk = '';
            const chunks = [];

            for (let i = 0; i < text.length; i++) {
                const char = text[i];
                if (char === ' ' || char === '\n' || char === '\t' || /[0-9\s.,!?:;'"()\/-]/.test(char)) {
                    currentChunk += char;
                } else if (gujaratiRegex.test(char)) {
                    if (currentScript !== 'gu' && currentChunk !== '') {
                        chunks.push({ text: currentChunk, script: currentScript });
                        currentChunk = '';
                    }
                    currentScript = 'gu';
                    currentChunk += char;
                } else {
                    if (currentScript !== 'en' && currentChunk !== '') {
                        chunks.push({ text: currentChunk, script: currentScript });
                        currentChunk = '';
                    }
                    currentScript = 'en';
                    currentChunk += char;
                }
            }

            if (currentChunk !== '') {
                chunks.push({ text: currentChunk, script: currentScript || 'en' });
            }

            if (chunks.length > 1 || (chunks.length === 1 && chunks[0].script)) {
                const fragment = document.createDocumentFragment();
                chunks.forEach(c => {
                    const span = document.createElement('span');
                    if (c.script === 'gu') {
                        span.className = 'lang-gu';
                        span.style.fontFamily = `'${guFont}', sans-serif`;
                    } else {
                        span.className = 'lang-en';
                        span.style.fontFamily = `'${enFont}', serif`;
                    }
                    span.textContent = c.text;
                    fragment.appendChild(span);
                });

                textNode.parentNode.replaceChild(fragment, textNode);
            }
        });
    }

    // Modal controls
    function openTableModal() {
        document.getElementById('table-modal').classList.add('show');
    }
    function closeTableModal() {
        document.getElementById('table-modal').classList.remove('show');
    }

    function confirmInsertTable() {
        const rows = parseInt(document.getElementById('table-rows').value) || 3;
        const cols = parseInt(document.getElementById('table-cols').value) || 3;
        
        let tableHtml = '<table style="width: 100%; border-collapse: collapse; margin: 15px 0; border: 1px solid #94a3b8;"><tbody>';
        for (let r = 0; r < rows; r++) {
            tableHtml += '<tr>';
            for (let c = 0; c < cols; c++) {
                tableHtml += `<td style="border: 1px solid #cbd5e1; padding: 8px 12px; min-width: 50px;">Cell ${r+1},${c+1}</td>`;
            }
            tableHtml += '</tr>';
        }
        tableHtml += '</tbody></table><p><br></p>';

        execCmd('insertHTML', tableHtml);
        closeTableModal();
    }

    function openShapeModal() {
        document.getElementById('shape-modal').classList.add('show');
    }
    function closeShapeModal() {
        document.getElementById('shape-modal').classList.remove('show');
    }

    function confirmInsertShape() {
        const type = document.getElementById('shape-type').value;
        let html = '';
        if (type === 'textbox') {
            html = '<div style="border: 1px solid #0284c7; padding: 10px; width: 240px; min-height: 70px; background-color: #f8fafc; resize: both; overflow: auto; display: inline-block; margin: 5px;" contenteditable="true"><p>Text box content...</p></div>';
        } else if (type === 'rect') {
            html = '<div style="border: 2px solid #334155; width: 150px; height: 80px; background-color: #f1f5f9; display: inline-block; resize: both; overflow: auto; margin: 5px;">&nbsp;</div>';
        } else if (type === 'circle') {
            html = '<div style="border: 2px solid #334155; width: 100px; height: 100px; background-color: #f1f5f9; border-radius: 50%; display: inline-block; resize: both; overflow: auto; margin: 5px;">&nbsp;</div>';
        } else if (type === 'line') {
            html = '<hr style="border: 0; border-top: 2px solid #334155; margin: 15px 0;" />';
        }
        
        execCmd('insertHTML', html);
        closeShapeModal();
    }

    // AJAX save
    function saveDocument() {
        const title = "{{ $document->title }}";
        const content = document.getElementById('document-body').innerHTML;
        const fontGu = document.getElementById('font-gujarati').value;
        const fontEn = document.getElementById('font-english').value;
        const left = parseFloat(document.getElementById('margin-left').value) * 10;
        const right = parseFloat(document.getElementById('margin-right').value) * 10;
        const top = parseFloat(document.getElementById('margin-top').value) * 10;
        const bottom = parseFloat(document.getElementById('margin-bottom').value) * 10;

        const indicator = document.getElementById('save-indicator');
        indicator.innerText = 'Saving changes...';
        indicator.style.color = 'var(--text-muted)';

        fetch("{{ route('documents.update', $document->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                title: title,
                html_content: content,
                font_gujarati: fontGu,
                font_english: fontEn,
                margin_left: parseInt(left),
                margin_right: parseInt(right),
                margin_top: parseInt(top),
                margin_bottom: parseInt(bottom),
                status: 'draft'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                isUnsaved = false;
                indicator.innerText = 'All changes saved (Version #' + data.version + ').';
                indicator.style.color = '#10b981';
                showToast('Document saved successfully!', 'success');
            } else {
                indicator.innerText = 'Error saving changes.';
                indicator.style.color = '#ef4444';
                showToast('Error saving: ' + data.message, 'error');
            }
        })
        .catch(err => {
            indicator.innerText = 'Network error saving changes.';
            indicator.style.color = '#ef4444';
            showToast('Network error saving changes.', 'error');
        });
    }

    // Ctrl+S Keyboard Shortcut
    window.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveDocument();
        }
    });

    // Prompt user on unload if unsaved changes
    window.addEventListener('beforeunload', (e) => {
        if (isUnsaved) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    // Auto-align margins on load
    document.addEventListener('DOMContentLoaded', () => {
        updateMarginStyle();
    });

    // Inline DOCX Import with Dual Server & Mammoth.js Fallback
    function triggerImportDocx() {
        document.getElementById('editor-docx-file').click();
    }

    async function handleDocxImport(input) {
        if (!input.files || input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('docx_file', file);

        const indicator = document.getElementById('save-indicator');
        indicator.innerText = 'Importing DOCX content...';
        indicator.style.color = 'var(--text-muted)';
        
        document.querySelector('.loader-overlay').classList.remove('hide');

        try {
            let serverSuccess = false;
            try {
                const res = await fetch("{{ route('documents.import-docx', $document->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success && data.html && data.html.trim().length > 0) {
                    document.getElementById('document-body').innerHTML = data.html;
                    serverSuccess = true;
                }
            } catch (serverErr) {
                console.log('Server DOCX import failed, falling back to browser Mammoth parser...');
            }

            if (!serverSuccess) {
                indicator.innerText = 'Converting Word document in browser engine...';
                const arrayBuffer = await file.arrayBuffer();
                
                if (typeof mammoth !== 'undefined') {
                    const result = await mammoth.convertToHtml({ arrayBuffer: arrayBuffer });
                    let html = result.value;
                    if (html && html.trim().length > 0) {
                        const container = document.getElementById('document-body');
                        container.innerHTML = html;
                        runBilingualFormatter();
                    } else {
                        throw new Error('Word document contains no readable text or content.');
                    }
                } else {
                    throw new Error('Could not convert Word document.');
                }
            }

            document.querySelector('.loader-overlay').classList.add('hide');
            markUnsaved();
            indicator.innerText = 'DOCX content imported successfully.';
            indicator.style.color = '#10b981';
            showToast('DOCX content imported successfully! Please save changes to persist.', 'success');

        } catch (err) {
            document.querySelector('.loader-overlay').classList.add('hide');
            indicator.innerText = 'Error during DOCX import.';
            indicator.style.color = '#ef4444';
            showToast(err.message || 'Failed to import DOCX file.', 'error');
        } finally {
            input.value = '';
        }
    }

    // Inline Adobe PageMaker Import (.pmd, .p65, .pm6, .ptd, .txt)
    function triggerImportPageMaker() {
        document.getElementById('editor-pagemaker-file').click();
    }

    async function handlePageMakerImport(input) {
        if (!input.files || input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('pagemaker_file', file);

        const indicator = document.getElementById('save-indicator');
        indicator.innerText = 'Importing PageMaker stories...';
        indicator.style.color = 'var(--text-muted)';
        
        document.querySelector('.loader-overlay').classList.remove('hide');

        try {
            const res = await fetch("{{ route('documents.import-pagemaker', $document->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await res.json();
            if (data.success && data.html && data.html.trim().length > 0) {
                document.getElementById('document-body').innerHTML = data.html;
                runBilingualFormatter();
                markUnsaved();
                indicator.innerText = 'PageMaker stories imported successfully.';
                indicator.style.color = '#10b981';
                showToast('PageMaker file imported & placed in editor canvas! Please save to persist.', 'success');
            } else {
                throw new Error(data.message || 'PageMaker import failed. Could not find readable text stories in the file.');
            }
        } catch (err) {
            indicator.innerText = 'Error importing PageMaker file.';
            indicator.style.color = '#ef4444';
            showToast(err.message || 'Failed to import PageMaker file.', 'error');
        } finally {
            document.querySelector('.loader-overlay').classList.add('hide');
            input.value = '';
        }
    }

    // Inline PDF Import with Dual Text & Scanned Image Rendering Engine
    function triggerImportPdf() {
        document.getElementById('editor-pdf-file').click();
    }

    async function parsePdfClientSide(file) {
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
        let fullText = '';
        let pageImages = [];

        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            const page = await pdf.getPage(pageNum);
            
            // Extract text
            const textContent = await page.getTextContent();
            let lastY, pageText = '';

            for (let item of textContent.items) {
                if (lastY !== undefined && Math.abs(item.transform[5] - lastY) > 5) {
                    pageText += '\n';
                } else if (pageText.length > 0 && !pageText.endsWith(' ') && !pageText.endsWith('\n')) {
                    pageText += ' ';
                }
                pageText += item.str;
                lastY = item.transform[5];
            }
            if (pageText.trim()) {
                fullText += pageText.trim() + '\n\n';
            }

            // Render high-res canvas image for scanned documents
            try {
                const viewport = page.getViewport({ scale: 1.5 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({ canvasContext: context, viewport: viewport }).promise;
                const imgData = canvas.toDataURL('image/jpeg', 0.88);
                pageImages.push(imgData);
            } catch (canvasErr) {
                console.log('Page canvas render skip:', canvasErr);
            }
        }
        return { text: fullText.trim(), images: pageImages };
    }

    async function handlePdfImport(input) {
        if (!input.files || input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('pdf_file', file);

        const indicator = document.getElementById('save-indicator');
        indicator.innerText = 'Importing PDF content...';
        indicator.style.color = 'var(--text-muted)';
        
        document.querySelector('.loader-overlay').classList.remove('hide');

        try {
            let serverSuccess = false;
            try {
                const res = await fetch("{{ route('documents.import-pdf', $document->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success && data.html && data.html.trim().length > 0) {
                    document.getElementById('document-body').innerHTML = data.html;
                    serverSuccess = true;
                }
            } catch (serverErr) {
                console.log('Server PDF extraction failed, trying client extraction...');
            }

            // Client-side fallback with PDF.js
            if (!serverSuccess) {
                indicator.innerText = 'Processing PDF via browser engine...';
                const result = await parsePdfClientSide(file);

                // If selectable text is found, format and insert text
                if (result.text && result.text.length > 20) {
                    const formatRes = await fetch("{{ route('documents.api-format') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            text: result.text,
                            font_gujarati: document.getElementById('font-gujarati').value,
                            font_english: document.getElementById('font-english').value
                        })
                    });

                    const formatData = await formatRes.json();
                    if (formatData.html) {
                        document.getElementById('document-body').innerHTML = formatData.html;
                    } else {
                        document.getElementById('document-body').innerText = result.text;
                    }
                } else if (result.images && result.images.length > 0) {
                    // Scanned / Photocopy document -> embed high resolution page images
                    let imageHtml = '';
                    result.images.forEach((imgSrc, idx) => {
                        imageHtml += `<div style="text-align: center; margin-bottom: 25px;">
                            <img src="${imgSrc}" alt="Scanned Page ${idx+1}" style="max-width: 100%; height: auto; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.06);" />
                        </div>`;
                    });
                    document.getElementById('document-body').innerHTML = imageHtml;
                } else {
                    throw new Error('PDF appears to be empty or unreadable.');
                }
            }

            document.querySelector('.loader-overlay').classList.add('hide');
            markUnsaved();
            indicator.innerText = 'PDF content imported successfully.';
            indicator.style.color = '#10b981';
            showToast('PDF imported & placed in editor canvas! Please save to persist.', 'success');

        } catch (err) {
            document.querySelector('.loader-overlay').classList.add('hide');
            indicator.innerText = 'Error importing PDF.';
            indicator.style.color = '#ef4444';
            showToast(err.message || 'Failed to import PDF file.', 'error');
        } finally {
            input.value = '';
        }
    }

    /**
     * Generate pixel-perfect bilingual PDF with native browser HarfBuzz font shaping,
     * zero text overlaps, and strict single/clean A4 page layout (no blank trailing pages).
     */
    async function downloadPerfectPdf() {
        showToast('Generating PDF...', 'info');
        const element = document.getElementById('document-body');
        const titleVal = (document.getElementById('document-title')?.value || 'Document')
            .replace(/[^\w\s-]/g, '')
            .trim()
            .replace(/\s+/g, '_');
        
        if (document.activeElement) document.activeElement.blur();
        
        try {
            // Render canvas without individual letter isolation to preserve Indic ligatures
            const canvas = await html2canvas(element, { 
                scale: 2, 
                useCORS: true, 
                logging: false,
                letterRendering: false, // Prevents Gujarati conjunct overlap
                allowTaint: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0
            });

            const imgData = canvas.toDataURL('image/jpeg', 0.98);
            
            // Access jsPDF from bundle
            const jsPDFClass = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF || (typeof html2pdf !== 'undefined' && html2pdf().jsPDF ? html2pdf().jsPDF : null));
            
            if (jsPDFClass) {
                const pdf = new jsPDFClass('p', 'mm', 'a4');
                const pdfWidth = 210;
                const pdfHeight = 297;
                
                // Calculate proportional height in mm
                const imgHeight = (canvas.height * pdfWidth) / canvas.width;
                
                if (imgHeight <= pdfHeight) {
                    // Fits precisely on 1 clean A4 page
                    pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, imgHeight);
                } else {
                    let heightLeft = imgHeight;
                    let position = 0;
                    
                    pdf.addImage(imgData, 'JPEG', 0, position, pdfWidth, imgHeight);
                    heightLeft -= pdfHeight;
                    
                    // Only add page if meaningful content remains (> 10mm)
                    while (heightLeft > 10) {
                        position = position - pdfHeight;
                        pdf.addPage();
                        pdf.addImage(imgData, 'JPEG', 0, position, pdfWidth, imgHeight);
                        heightLeft -= pdfHeight;
                    }
                }
                
                pdf.save(`${titleVal}.pdf`);
                showToast('PDF downloaded successfully!', 'success');
            } else {
                // Fallback to html2pdf helper
                const opt = {
                    margin: 0,
                    filename: `${titleVal}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, letterRendering: false, scrollX: 0, scrollY: 0 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };
                await html2pdf().set(opt).from(element).save();
                showToast('PDF downloaded successfully!', 'success');
            }
        } catch (err) {
            console.error('PDF export error:', err);
            window.location.href = "{{ route('documents.export-pdf', $document->id) }}";
        }
    }
</script>
<!-- html2pdf.js bundle with html2canvas and jsPDF for 100% pixel-perfect PDF rendering -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- Mammoth.js for client-side Word DOCX extraction -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
@endsection
