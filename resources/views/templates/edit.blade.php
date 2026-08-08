@extends('layouts.app')

@section('page-title', 'Edit Template')

@section('content')
<div class="card animate-fade-in">
    <div class="card-title">
        <span>Template Constructor</span>
    </div>

    <form action="{{ route('templates.update', $template->id) }}" method="POST" class="form-light" onsubmit="prepareTplSubmit(); showLoader();">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <div class="form-group">
                    <label for="title">Template Name</label>
                    <input type="text" name="title" id="title" class="form-input" placeholder="e.g. Standard Bilingual Circular" value="{{ old('title', $template->title) }}" required>
                    @error('title')
                        <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-input" placeholder="What is this template used for?" rows="3">{{ old('description', $template->description) }}</textarea>
                </div>
            </div>
            
            <div style="background-color: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
                <h4 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.95rem;">💡 Template Guidelines</h4>
                <ul style="font-size: 0.85rem; color: var(--text-muted); padding-left: 1.25rem; line-height: 1.6;">
                    <li>Define variables in your content using double brackets: <code>[[field_key]]</code>.</li>
                    <li>Add the corresponding fields below with matching keys.</li>
                    <li>When operators fill this template, those placeholders will be replaced with entered values, split/wrapped in bilingual fonts, and converted to an editable document.</li>
                </ul>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <label style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0;">Template Document Body</label>
                <button type="button" class="btn btn-sm btn-secondary" onclick="toggleCodeView()" id="btn-toggle-code" data-tooltip="Switch between Visual Editor and HTML Code" data-tooltip-pos="left" style="font-size: 0.78rem; padding: 4px 12px;">
                    &lt;/&gt; Code View
                </button>
            </div>

            <!-- Hidden input to submit HTML with the form -->
            <input type="hidden" name="html_content" id="html_content" value="{{ old('html_content', $template->html_content) }}">

            <!-- Rich Text Visual Editor Wrapper -->
            <div class="template-editor-wrapper" style="border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden; background: #ffffff; box-shadow: var(--shadow-sm);">
                <!-- Visual Toolbar -->
                <div class="template-editor-toolbar" style="background: #f8fafc; border-bottom: 1px solid var(--border-color); padding: 0.5rem 0.75rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center;">
                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('undo')" data-tooltip="Undo (Ctrl+Z)">↩</button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('redo')" data-tooltip="Redo (Ctrl+Y)">↪</button>
                    </div>
                    
                    <div class="toolbar-group">
                        <select class="toolbar-select" onchange="execTplFormatBlock(this.value)" data-tooltip="Paragraph / Heading Style">
                            <option value="p">Paragraph</option>
                            <option value="h1">Heading 1</option>
                            <option value="h2">Heading 2</option>
                            <option value="h3">Heading 3</option>
                            <option value="blockquote">Quote</option>
                        </select>
                        <select class="toolbar-select" onchange="execTplFontSize(this.value)" data-tooltip="Font Size">
                            <option value="12pt">12 pt</option>
                            <option value="13pt" selected>13 pt (Standard)</option>
                            <option value="14pt">14 pt</option>
                            <option value="16pt">16 pt</option>
                            <option value="18pt">18 pt</option>
                            <option value="22pt">22 pt</option>
                        </select>
                    </div>

                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('bold')" data-tooltip="Bold (Ctrl+B)"><b>B</b></button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('italic')" data-tooltip="Italic (Ctrl+I)"><i>I</i></button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('underline')" data-tooltip="Underline (Ctrl+U)"><u>U</u></button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('strikeThrough')" data-tooltip="Strikethrough"><s>S</s></button>
                    </div>

                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('justifyLeft')" data-tooltip="Align Left">⇤</button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('justifyCenter')" data-tooltip="Align Center">≡</button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('justifyRight')" data-tooltip="Align Right">⇥</button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('justifyFull')" data-tooltip="Justify">↔</button>
                    </div>

                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('insertUnorderedList')" data-tooltip="Bullet List">•≡</button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('insertOrderedList')" data-tooltip="Numbered List">1.≡</button>
                        <button type="button" class="toolbar-btn" onclick="execTplCmd('insertHorizontalRule')" data-tooltip="Horizontal Line">—</button>
                        <button type="button" class="toolbar-btn" onclick="insertTplTable()" data-tooltip="Insert 2-Column Split Box">▦ Table</button>
                    </div>

                    <div class="toolbar-group" style="border-right: none; margin-left: auto;">
                        <button type="button" class="btn btn-sm btn-primary" onclick="runTplBilingualFormat()" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" data-tooltip="Auto-detect & format Gujarati and English fonts" data-tooltip-pos="left">
                            ✨ Auto-Format Scripts
                        </button>
                    </div>
                </div>

                <!-- Visual Contenteditable Area -->
                <div id="tpl-visual-editor" contenteditable="true" style="min-height: 320px; max-height: 550px; overflow-y: auto; padding: 1.75rem 2rem; font-size: 13pt; line-height: 1.6; color: #000000; outline: none; background: #ffffff;" oninput="syncTplContent()">
                    {!! old('html_content', $template->html_content) !!}
                </div>

                <!-- Code View Textarea (Hidden by default) -->
                <textarea id="tpl-code-editor" style="display: none; width: 100%; min-height: 320px; padding: 1.25rem; font-family: monospace; font-size: 0.88rem; border: none; outline: none; background: #0f172a; color: #e2e8f0; resize: vertical;" oninput="syncTplCode()"></textarea>
            </div>
            @error('html_content')
                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="card-title" style="margin-top: 3rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <span>Dynamic Input Fields</span>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addFieldRow()">+ Add Field</button>
        </div>

        <div id="fields-container" style="margin-top: 1.5rem; margin-bottom: 2rem;">
            @php $index = 0; @endphp
            @foreach($template->fields as $field)
                <div class="field-row" id="field-row-{{ $index }}">
                    <div class="form-group">
                        <label>Field Key (alphanumeric)</label>
                        <input type="text" name="fields[{{ $index }}][key]" class="form-input" placeholder="e.g. name" value="{{ $field->field_key }}" required pattern="[a-zA-Z0-9_]+">
                    </div>
                    <div class="form-group">
                        <label>Field Label</label>
                        <input type="text" name="fields[{{ $index }}][label]" class="form-input" placeholder="e.g. Full Name" value="{{ $field->field_label }}" required>
                    </div>
                    <div class="form-group">
                        <label>Input Type</label>
                        <select name="fields[{{ $index }}][type]" class="form-input">
                            <option value="text" {{ $field->field_type === 'text' ? 'selected' : '' }}>Text Input</option>
                            <option value="date" {{ $field->field_type === 'date' ? 'selected' : '' }}>Date Picker</option>
                            <option value="textarea" {{ $field->field_type === 'textarea' ? 'selected' : '' }}>Text Area</option>
                            <option value="number" {{ $field->field_type === 'number' ? 'selected' : '' }}>Numeric Input</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Default Value</label>
                        <input type="text" name="fields[{{ $index }}][default_value]" class="form-input" placeholder="Optional" value="{{ $field->default_value }}">
                    </div>
                    <div style="padding-bottom: 1.25rem;">
                        <button type="button" class="action-btn action-btn-delete" data-tooltip="Remove Field" data-tooltip-pos="left" onclick="removeFieldRow({{ $index }})">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </div>
                </div>
                @php $index++; @endphp
            @endforeach
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 3rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <a href="{{ route('templates.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Template</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let fieldIndex = {{ $template->fields->count() }};
    let isCodeView = false;

    function addFieldRow() {
        const container = document.getElementById('fields-container');
        const html = `
            <div class="field-row animate-fade-in" id="field-row-${fieldIndex}">
                <div class="form-group">
                    <label>Field Key (alphanumeric)</label>
                    <input type="text" name="fields[${fieldIndex}][key]" class="form-input" placeholder="e.g. circular_no" required pattern="[a-zA-Z0-9_]+">
                </div>
                <div class="form-group">
                    <label>Field Label</label>
                    <input type="text" name="fields[${fieldIndex}][label]" class="form-input" placeholder="e.g. Circular Number" required>
                </div>
                <div class="form-group">
                    <label>Input Type</label>
                    <select name="fields[${fieldIndex}][type]" class="form-input">
                        <option value="text">Text Input</option>
                        <option value="date">Date Picker</option>
                        <option value="textarea">Text Area</option>
                        <option value="number">Numeric Input</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Default Value</label>
                    <input type="text" name="fields[${fieldIndex}][default_value]" class="form-input" placeholder="Optional">
                </div>
                <div style="padding-bottom: 1.25rem;">
                    <button type="button" class="action-btn action-btn-delete" data-tooltip="Remove Field" data-tooltip-pos="left" onclick="removeFieldRow(${fieldIndex})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        fieldIndex++;
    }

    function removeFieldRow(idx) {
        document.getElementById(`field-row-${idx}`).remove();
    }

    // Rich Text Editor Commands
    function execTplCmd(cmd, value = null) {
        document.getElementById('tpl-visual-editor').focus();
        document.execCommand(cmd, false, value);
        syncTplContent();
    }

    function execTplFormatBlock(tag) {
        execTplCmd('formatBlock', tag);
    }

    function execTplFontSize(size) {
        document.getElementById('tpl-visual-editor').focus();
        const selection = window.getSelection();
        if (!selection.rangeCount) return;
        const range = selection.getRangeAt(0);
        const span = document.createElement('span');
        span.style.fontSize = size;
        try {
            span.appendChild(range.extractContents());
            range.insertNode(span);
        } catch (e) {
            execTplCmd('fontSize', '4');
        }
        syncTplContent();
    }

    function insertTplTable() {
        const tableHtml = `<div style="display: flex; justify-content: space-between; margin: 15px 0;">
            <div style="width: 48%;">Left Column / ડાબી બાજુ</div>
            <div style="width: 48%; text-align: right;">Right Column / જમણી બાજુ</div>
        </div><p>&nbsp;</p>`;
        execTplCmd('insertHTML', tableHtml);
    }

    function syncTplContent() {
        const visual = document.getElementById('tpl-visual-editor');
        const hidden = document.getElementById('html_content');
        const code = document.getElementById('tpl-code-editor');
        hidden.value = visual.innerHTML;
        code.value = visual.innerHTML;
    }

    function syncTplCode() {
        const visual = document.getElementById('tpl-visual-editor');
        const hidden = document.getElementById('html_content');
        const code = document.getElementById('tpl-code-editor');
        hidden.value = code.value;
        visual.innerHTML = code.value;
    }

    function toggleCodeView() {
        const visual = document.getElementById('tpl-visual-editor');
        const code = document.getElementById('tpl-code-editor');
        const btn = document.getElementById('btn-toggle-code');
        isCodeView = !isCodeView;

        if (isCodeView) {
            code.value = visual.innerHTML;
            visual.style.display = 'none';
            code.style.display = 'block';
            btn.innerHTML = '👁 Visual View';
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-primary');
        } else {
            visual.innerHTML = code.value;
            code.style.display = 'none';
            visual.style.display = 'block';
            btn.innerHTML = '&lt;/&gt; Code View';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
        }
        syncTplContent();
    }

    async function runTplBilingualFormat() {
        const visual = document.getElementById('tpl-visual-editor');
        try {
            const res = await fetch('{{ route("documents.api-format") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    html: visual.innerHTML,
                    font_gujarati: 'Noto Sans Gujarati',
                    font_english: 'Times New Roman'
                })
            });
            const data = await res.json();
            if (data.html) {
                visual.innerHTML = data.html;
                syncTplContent();
                showToast('Scripts auto-formatted with Noto Sans Gujarati & Times New Roman!', 'success');
            }
        } catch (e) {
            console.error(e);
        }
    }

    function prepareTplSubmit() {
        if (isCodeView) {
            syncTplCode();
        } else {
            syncTplContent();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        syncTplContent();
    });
</script>
@endsection
