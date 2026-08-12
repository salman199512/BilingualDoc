<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\BilingualFormatterService;
use App\Services\DocxAutoFixService;
use App\Services\PageMakerParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Document::where('user_id', Auth::id())->select('documents.*');
            return DataTables::of($query)
                ->addColumn('action', function ($doc) {
                    return '
                        <div class="action-btn-group">
                            <a href="'.route('documents.edit', $doc->id).'" class="action-btn action-btn-edit" data-tooltip="Edit Document" data-tooltip-pos="bottom">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </a>
                            <a href="'.route('documents.export-pdf', $doc->id).'" class="action-btn action-btn-pdf" data-tooltip="Download PDF" data-tooltip-pos="bottom">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="12" y2="18"></line><line x1="15" y1="15" x2="12" y2="18"></line></svg>
                            </a>
                            <a href="'.route('documents.export-docx', $doc->id).'" class="action-btn action-btn-docx" data-tooltip="Download Word DOCX" data-tooltip-pos="bottom">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M8 13h2"></path><path d="M8 17h8"></path><path d="M14 13h2"></path></svg>
                            </a>
                            <form action="'.route('documents.destroy', $doc->id).'" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm(\'Delete this document?\');">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="action-btn action-btn-delete" data-tooltip="Delete Document" data-tooltip-pos="bottom">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->editColumn('status', function ($doc) {
                    $class = $doc->status === 'final' ? 'badge-final' : 'badge-draft';
                    return '<span class="badge '.$class.'">'.ucfirst($doc->status).'</span>';
                })
                ->editColumn('updated_at', function ($doc) {
                    return $doc->updated_at->format('M d, Y h:i A');
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('documents.index');
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'font_gujarati' => 'required|string',
            'font_english' => 'required|string',
        ]);

        $document = Document::create([
            'title' => $request->title,
            'html_content' => '<p style="line-height: 1.6;"><span class="lang-en" style="font-family: \'' . $request->font_english . '\'; font-size: 13pt;">Start typing...</span></p>',
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'font_gujarati' => $request->font_gujarati,
            'font_english' => $request->font_english,
            'margin_left' => 40,
            'margin_right' => 40,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'status' => 'draft',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('documents.edit', $document->id)->with('success', 'Document created successfully.');
    }

    public function edit(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $versions = $document->versions()->with('user')->get();
        return view('documents.editor', compact('document', 'versions'));
    }

    public function update(Request $request, Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'html_content' => 'nullable|string',
            'font_gujarati' => 'required|string',
            'font_english' => 'required|string',
            'margin_left' => 'required|integer|min:0',
            'margin_right' => 'required|integer|min:0',
            'margin_top' => 'required|integer|min:0',
            'margin_bottom' => 'required|integer|min:0',
            'status' => 'required|in:draft,final',
        ]);

        // 1. Create version backup of current state
        $latestVersion = DocumentVersion::where('document_id', $document->id)
            ->max('version_number') ?? 0;

        DocumentVersion::create([
            'document_id' => $document->id,
            'html_content' => $document->html_content,
            'version_number' => $latestVersion + 1,
            'user_id' => Auth::id(),
        ]);

        // 2. Update the document
        $document->update([
            'title' => $request->title,
            'html_content' => $request->html_content,
            'font_gujarati' => $request->font_gujarati,
            'font_english' => $request->font_english,
            'margin_left' => $request->margin_left,
            'margin_right' => $request->margin_right,
            'margin_top' => $request->margin_top,
            'margin_bottom' => $request->margin_bottom,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document saved successfully and backup version created.',
            'version' => $latestVersion + 1
        ]);
    }

    public function destroy(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }

    public function restoreVersion(Request $request, Document $document, $versionId)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $version = DocumentVersion::where('document_id', $document->id)->findOrFail($versionId);

        // Backup current content before restoring
        $latestVersion = DocumentVersion::where('document_id', $document->id)->max('version_number') ?? 0;
        DocumentVersion::create([
            'document_id' => $document->id,
            'html_content' => $document->html_content,
            'version_number' => $latestVersion + 1,
            'user_id' => Auth::id(),
        ]);

        $document->update([
            'html_content' => $version->html_content
        ]);

        return redirect()->route('documents.edit', $document->id)->with('success', 'Document restored to version ' . $version->version_number . '.');
    }

    public function importDocx(Request $request, Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'docx_file' => 'required|file|max:20480', // max 20MB
        ]);

        if ($request->file('docx_file')->isValid()) {
            $file = $request->file('docx_file');
            
            // Store file temporarily
            $tempPath = $file->storeAs('temp', uniqid() . '.docx');
            $absolutePath = storage_path('app/private/' . $tempPath);
            if (!file_exists($absolutePath)) {
                $absolutePath = storage_path('app/' . $tempPath);
            }

            // Run the DocxAutoFixService to re-align margins, size, and fonts in the XML!
            try {
                DocxAutoFixService::autoFixDocx($absolutePath, $document->font_gujarati, $document->font_english);
            } catch (\Exception $e) {
                // Continue if auto-fix encounters non-standard XML
            }

            // Load the DOCX and convert to HTML
            $htmlContent = '';
            try {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($absolutePath);
                $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
                
                ob_start();
                $htmlWriter->save('php://output');
                $rawHtml = ob_get_clean();

                if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $rawHtml, $matches)) {
                    $htmlContent = $matches[1];
                } else {
                    $htmlContent = $rawHtml;
                }

                if (!empty(trim($htmlContent))) {
                    $htmlContent = BilingualFormatterService::formatHtmlDocument(
                        $htmlContent,
                        $document->font_gujarati,
                        $document->font_english
                    );
                }

            } catch (\Exception $e) {
                // Try fallback ZipArchive direct XML extraction
                try {
                    $zip = new \ZipArchive();
                    if ($zip->open($absolutePath) === true) {
                        $xmlContent = $zip->getFromName('word/document.xml');
                        if ($xmlContent) {
                            $xmlDom = new \DOMDocument();
                            @$xmlDom->loadXML($xmlContent);
                            $pList = $xmlDom->getElementsByTagName('p');
                            $extractedHtml = '';
                            foreach ($pList as $pNode) {
                                $tNodes = $pNode->getElementsByTagName('t');
                                $pStr = '';
                                foreach ($tNodes as $tNode) {
                                    $pStr .= $tNode->nodeValue;
                                }
                                if (trim($pStr) !== '') {
                                    $extractedHtml .= '<p>' . htmlspecialchars($pStr) . '</p>';
                                }
                            }
                            if (!empty(trim($extractedHtml))) {
                                $htmlContent = BilingualFormatterService::formatHtmlDocument(
                                    $extractedHtml,
                                    $document->font_gujarati,
                                    $document->font_english
                                );
                            }
                        }
                        $zip->close();
                    }
                } catch (\Exception $xmlEx) {
                    // Fallback to client-side
                }
            } finally {
                if (file_exists($absolutePath)) {
                    unlink($absolutePath);
                }
            }

            if (empty(trim($htmlContent))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backend DOCX parsing yielded empty content.'
                ], 200); // 200 so Mammoth client fallback can process
            }

            return response()->json([
                'success' => true,
                'html' => $htmlContent
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Upload failed.'
        ], 422);
    }

    public function importPdf(Request $request, Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'pdf_file' => 'required|file|max:20480', // max 20MB
        ]);

        if ($request->file('pdf_file')->isValid()) {
            $file = $request->file('pdf_file');
            
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($file->getPathname());
                
                // Get all text across all pages
                $pages = $pdf->getPages();
                $allText = '';
                
                if (!empty($pages)) {
                    foreach ($pages as $page) {
                        $pageText = $page->getText();
                        if (!empty(trim($pageText))) {
                            $allText .= trim($pageText) . "\n\n";
                        }
                    }
                }
                
                if (empty(trim($allText))) {
                    $allText = $pdf->getText();
                }

                if (empty(trim($allText))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The PDF appears to be empty or contains scanned images without selectable text.'
                    ], 200); // return 200 with success: false so client-side PDF.js can try
                }

                // Format extracted text with BilingualFormatterService
                $htmlContent = BilingualFormatterService::formatHtml(
                    trim($allText),
                    $document->font_gujarati,
                    $document->font_english
                );

                return response()->json([
                    'success' => true,
                    'html' => $htmlContent
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF text parse error: ' . $e->getMessage()
                ], 200); // return 200 with success: false so client-side PDF.js can try
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Upload failed.'
        ], 422);
    }

    public function importPageMaker(Request $request, Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'pagemaker_file' => 'required|file|max:30720', // max 30MB
        ]);

        if ($request->file('pagemaker_file')->isValid()) {
            $file = $request->file('pagemaker_file');
            
            try {
                $htmlContent = PageMakerParserService::parseToHtml(
                    $file->getPathname(),
                    $document->font_gujarati,
                    $document->font_english
                );

                return response()->json([
                    'success' => true,
                    'html' => $htmlContent
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'PageMaker import notice: ' . $e->getMessage()
                ], 200); // 200 so client-side reader can attempt local fallback extraction
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Upload failed or invalid PageMaker file.'
        ], 200);
    }

    public function apiFormat(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'font_gujarati' => 'nullable|string',
            'font_english' => 'nullable|string',
        ]);

        $fontGujarati = $request->input('font_gujarati', 'Noto Sans Gujarati');
        $fontEnglish = $request->input('font_english', 'Times New Roman');

        $formatted = BilingualFormatterService::formatHtml($request->text, $fontGujarati, $fontEnglish);

        return response()->json([
            'html' => $formatted
        ]);
    }
}
