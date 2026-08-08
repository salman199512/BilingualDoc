<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocxAutoFixService;
use App\Services\BilingualFormatterService;
use App\Services\PageMakerParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class UploadController extends Controller
{
    public function showUpload()
    {
        return view('documents.upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document_file' => 'required|file|max:30720', // max 30MB
            'title' => 'required|string|max:255',
            'font_gujarati' => 'required|string',
            'font_english' => 'required|string',
        ]);

        if ($request->file('document_file')->isValid()) {
            $file = $request->file('document_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $htmlContent = '';

            $allowedExtensions = ['docx', 'doc', 'pdf', 'pmd', 'p65', 'pm6', 'pm5', 'ptd', 'txt'];
            if (!in_array($extension, $allowedExtensions)) {
                return back()->withErrors(['document_file' => 'Invalid file format. Supported formats: DOCX, DOC, PDF, PageMaker (.pmd, .p65, .pm6, .pm5, .ptd).']);
            }

            if (in_array($extension, ['pmd', 'p65', 'pm6', 'pm5', 'ptd', 'txt'])) {
                try {
                    $htmlContent = PageMakerParserService::parseToHtml(
                        $file->getPathname(),
                        $request->font_gujarati,
                        $request->font_english
                    );
                } catch (\Exception $e) {
                    return back()->withErrors(['document_file' => 'Could not extract text from PageMaker file: ' . $e->getMessage()]);
                }
            } elseif ($extension === 'pdf') {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($file->getPathname());
                    $text = $pdf->getText();

                    if (empty(trim($text))) {
                        return back()->withErrors(['document_file' => 'The PDF appears to be empty or contains scanned images without selectable text.']);
                    }

                    $htmlContent = BilingualFormatterService::formatHtml(
                        $text,
                        $request->font_gujarati,
                        $request->font_english
                    );
                } catch (\Exception $e) {
                    return back()->withErrors(['document_file' => 'Could not extract text from PDF: ' . $e->getMessage()]);
                }
            } else {
                // DOCX or DOC
                $tempPath = $file->storeAs('temp', uniqid() . '.docx');
                $absolutePath = storage_path('app/private/' . $tempPath);
                if (!file_exists($absolutePath)) {
                    $absolutePath = storage_path('app/' . $tempPath);
                }

                // 1. Run the DocxAutoFixService to re-align margins, size, and fonts in the XML!
                $fixed = DocxAutoFixService::autoFixDocx($absolutePath, $request->font_gujarati, $request->font_english);

                // 2. Load the auto-fixed DOCX file and convert to HTML for our web editor
                try {
                    $phpWord = IOFactory::load($absolutePath);
                    foreach ($phpWord->getSections() as $section) {
                        $section->getStyle()->setMarginLeft(2268);
                        $section->getStyle()->setMarginRight(2268);
                        $section->getStyle()->setMarginTop(1134);
                        $section->getStyle()->setMarginBottom(1134);
                        $section->getStyle()->setPageSizeW(11906);
                        $section->getStyle()->setPageSizeH(16838);
                    }

                    $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
                    ob_start();
                    $htmlWriter->save('php://output');
                    $rawHtml = ob_get_clean();

                    if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $rawHtml, $matches)) {
                        $htmlContent = $matches[1];
                    } else {
                        $htmlContent = $rawHtml;
                    }

                    $htmlContent = BilingualFormatterService::formatHtmlDocument(
                        $htmlContent,
                        $request->font_gujarati,
                        $request->font_english
                    );
                } catch (\Exception $e) {
                    $htmlContent = '<p>Could not convert legacy DOCX structure to HTML. Please edit manually.</p>';
                } finally {
                    if (file_exists($absolutePath)) {
                        unlink($absolutePath);
                    }
                }
            }

            // Save as a new Document in database
            $document = Document::create([
                'title' => $request->title,
                'html_content' => $htmlContent,
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

            return redirect()->route('documents.edit', $document->id)->with('success', 'Document uploaded and reformatted to standard. Feel free to edit in the editor!');
        }

        return back()->withErrors(['document_file' => 'File upload failed.']);
    }
}
