<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Template;
use App\Models\TemplateField;
use App\Models\TemplateSubmission;
use App\Services\BilingualFormatterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Template::where('user_id', Auth::id())->select('templates.*');
            return DataTables::of($query)
                ->addColumn('action', function ($tpl) {
                    return '
                        <div class="action-btn-group">
                            <a href="'.route('templates.fill', $tpl->id).'" class="action-btn action-btn-fill" data-tooltip="Fill & Generate Document" data-tooltip-pos="bottom">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            </a>
                            <a href="'.route('templates.edit', $tpl->id).'" class="action-btn action-btn-edit" data-tooltip="Edit Template" data-tooltip-pos="bottom">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </a>
                            <form action="'.route('templates.destroy', $tpl->id).'" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm(\'Delete this template?\');">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="action-btn action-btn-delete" data-tooltip="Delete Template" data-tooltip-pos="bottom">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->editColumn('updated_at', function ($tpl) {
                    return $tpl->updated_at->format('M d, Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('templates.index');
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'fields' => 'nullable|array',
            'fields.*.key' => 'required_with:fields|string|alpha_dash',
            'fields.*.label' => 'required_with:fields|string|max:255',
            'fields.*.type' => 'required_with:fields|string|in:text,date,textarea,number',
            'fields.*.default_value' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $template = Template::create([
                'title' => $request->title,
                'description' => $request->description,
                'html_content' => $request->html_content,
                'user_id' => Auth::id(),
            ]);

            if ($request->has('fields')) {
                foreach ($request->fields as $field) {
                    TemplateField::create([
                        'template_id' => $template->id,
                        'field_key' => $field['key'],
                        'field_label' => $field['label'],
                        'field_type' => $field['type'],
                        'default_value' => $field['default_value'],
                    ]);
                }
            }
        });

        return redirect()->route('templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $template->load('fields');
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'fields' => 'nullable|array',
            'fields.*.key' => 'required_with:fields|string|alpha_dash',
            'fields.*.label' => 'required_with:fields|string|max:255',
            'fields.*.type' => 'required_with:fields|string|in:text,date,textarea,number',
            'fields.*.default_value' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $template) {
            $template->update([
                'title' => $request->title,
                'description' => $request->description,
                'html_content' => $request->html_content,
            ]);

            // Sync fields (delete old ones and recreate)
            $template->fields()->delete();

            if ($request->has('fields')) {
                foreach ($request->fields as $field) {
                    TemplateField::create([
                        'template_id' => $template->id,
                        'field_key' => $field['key'],
                        'field_label' => $field['label'],
                        'field_type' => $field['type'],
                        'default_value' => $field['default_value'],
                    ]);
                }
            }
        });

        return redirect()->route('templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted successfully.');
    }

    public function fillForm(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $template->load('fields');
        return view('templates.fill', compact('template'));
    }

    public function merge(Request $request, Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $template->load('fields');
        
        $rules = [];
        foreach ($template->fields as $field) {
            $rules[$field->field_key] = $field->field_type === 'number' ? 'nullable|numeric' : 'nullable|string';
        }
        $request->validate($rules);

        $fieldValues = $request->only($template->fields->pluck('field_key')->toArray());

        // Perform merge replacement
        $mergedHtml = $template->html_content;
        foreach ($fieldValues as $key => $value) {
            $value = $value ?? '';
            // If the value contains Gujarati / English, format it using the helper or just output it directly in a styled way.
            // Let's replace placeholders like [[key]] with styled bilingual formatting.
            $styledVal = BilingualFormatterService::formatHtml($value);
            
            // Extract the inner HTML content of the paragraphs so we don't nest <p> tags
            $styledVal = preg_replace('/^<p[^>]*>/i', '', $styledVal);
            $styledVal = preg_replace('/<\/p>$/i', '', $styledVal);
            
            $mergedHtml = str_replace("[[{$key}]]", $styledVal, $mergedHtml);
            $mergedHtml = str_replace("{{{$key}}}", $styledVal, $mergedHtml);
        }

        // Create new Document from merged content
        $document = DB::transaction(function () use ($template, $mergedHtml, $fieldValues) {
            $doc = Document::create([
                'title' => 'Document from - ' . $template->title,
                'html_content' => $mergedHtml,
                'page_size' => 'A4',
                'orientation' => 'portrait',
                'font_gujarati' => 'Noto Sans Gujarati',
                'font_english' => 'Times New Roman',
                'margin_left' => 40,
                'margin_right' => 40,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'status' => 'draft',
                'user_id' => Auth::id(),
            ]);

            TemplateSubmission::create([
                'template_id' => $template->id,
                'document_id' => $doc->id,
                'user_id' => Auth::id(),
                'field_values' => $fieldValues,
            ]);

            return $doc;
        });

        return redirect()->route('documents.edit', $document->id)->with('success', 'Values merged successfully. New document generated!');
    }
}
