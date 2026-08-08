<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\BilingualFormatterService;
use App\Services\DocxAutoFixService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class ExportController extends Controller
{
    /**
     * Export a document as DOCX.
     */
    public function exportDocx(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $phpWord = new PhpWord();

        // Configure default font and size
        $phpWord->setDefaultFontName($document->font_english);
        $phpWord->setDefaultFontSize(13);

        // Section styles: A4 Portrait with 4cm left/right, 2cm top/bottom margins
        $sectionStyle = [
            'pageSizeW' => 11906, // A4 Width
            'pageSizeH' => 16838, // A4 Height
            'marginLeft' => 2268,
            'marginRight' => 2268,
            'marginTop' => 1134,
            'marginBottom' => 1134,
            'headerHeight' => 720,
            'footerHeight' => 720,
        ];

        $section = $phpWord->addSection($sectionStyle);

        // Clean and prepare HTML to prevent XML corruption and preserve layout in PHPWord
        try {
            $formattedHtml = BilingualFormatterService::formatHtmlDocument(
                $document->html_content,
                $document->font_gujarati,
                $document->font_english
            );

            // Clean basic glitches
            $formattedHtml = str_replace('&nbsp;', ' ', $formattedHtml);
            $formattedHtml = preg_replace('/contenteditable="[^"]*"/i', '', $formattedHtml);

            // Load into DOM to transform flex layouts and alignments for Word OpenXML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><div id="wrapper">' . $formattedHtml . '</div></body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);

            // 1. Convert flex containers with 2 child divs to 2-column borderless tables
            $flexDivs = $xpath->query('//div[contains(@style, "display: flex") or contains(@style, "display:flex")]');
            foreach ($flexDivs as $flex) {
                $childDivs = [];
                foreach ($flex->childNodes as $child) {
                    if ($child instanceof \DOMElement) {
                        $childDivs[] = $child;
                    }
                }

                if (count($childDivs) === 2) {
                    $table = $dom->createElement('table');
                    $table->setAttribute('style', 'width: 100%; border: none;');
                    $table->setAttribute('border', '0');

                    $tr = $dom->createElement('tr');
                    
                    // Left Cell
                    $tdLeft = $dom->createElement('td');
                    $tdLeft->setAttribute('style', 'width: 50%; vertical-align: top; border: none;');
                    $tdLeft->setAttribute('align', 'left');
                    while ($childDivs[0]->hasChildNodes()) {
                        $tdLeft->appendChild($childDivs[0]->firstChild);
                    }
                    $tr->appendChild($tdLeft);

                    // Right Cell
                    $tdRight = $dom->createElement('td');
                    $tdRight->setAttribute('style', 'width: 50%; vertical-align: top; border: none; text-align: right;');
                    $tdRight->setAttribute('align', 'right');
                    while ($childDivs[1]->hasChildNodes()) {
                        $tdRight->appendChild($childDivs[1]->firstChild);
                    }
                    $tr->appendChild($tdRight);

                    $table->appendChild($tr);
                    $flex->parentNode->replaceChild($table, $flex);
                }
            }

            // 2. Propagate text-align: center and text-align: right to align attributes
            $centerNodes = $xpath->query('//*[contains(@style, "text-align: center") or contains(@style, "text-align:center")]');
            foreach ($centerNodes as $node) {
                $node->setAttribute('align', 'center');
                $descPs = $node->getElementsByTagName('p');
                foreach ($descPs as $dp) {
                    $dp->setAttribute('align', 'center');
                }
            }

            $rightNodes = $xpath->query('//*[contains(@style, "text-align: right") or contains(@style, "text-align:right")]');
            foreach ($rightNodes as $node) {
                $node->setAttribute('align', 'right');
                $descPs = $node->getElementsByTagName('p');
                foreach ($descPs as $dp) {
                    $dp->setAttribute('align', 'right');
                }
            }

            // 3. Fix any nested style quotes that break XML attribute parsing
            $spans = $xpath->query('//span');
            foreach ($spans as $span) {
                if ($span->hasAttribute('style')) {
                    $style = $span->getAttribute('style');
                    $style = str_replace(['"', '&quot;'], "'", $style);
                    $span->setAttribute('style', $style);
                }
            }

            // 4. Save as strict valid XML body
            $wrapper = $dom->getElementById('wrapper');
            $bodyXml = '<body>';
            if ($wrapper) {
                foreach ($wrapper->childNodes as $child) {
                    $bodyXml .= $dom->saveXML($child);
                }
            }
            $bodyXml .= '</body>';

            Html::addHtml($section, $bodyXml, false, false);
        } catch (\Exception $e) {
            // Fallback: Re-create document with plain text lines if XML fails
            $plainLines = explode("\n", strip_tags($document->html_content));
            foreach ($plainLines as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $section->addText($line);
                }
            }
        }

        // Save and download file
        $fileName = str_replace(' ', '_', $document->title) . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'docx');
        
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        // Run the XML-based auto-fix on the exported temporary file
        // to split Gujarati and English runs, apply correct fonts, page margins, and Print Layout view!
        DocxAutoFixService::autoFixDocx($tempFile, $document->font_gujarati, $document->font_english);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Export a document as PDF.
     */
    public function exportPdf(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Format HTML and convert flexbox layouts to 2-column borderless tables for PDF compatibility
        $formattedHtml = BilingualFormatterService::formatHtmlDocument(
            $document->html_content,
            $document->font_gujarati,
            $document->font_english
        );

        $formattedHtml = str_replace('&nbsp;', ' ', $formattedHtml);
        $formattedHtml = preg_replace('/contenteditable="[^"]*"/i', '', $formattedHtml);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><div id="wrapper">' . $formattedHtml . '</div></body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Convert flex containers with 2 child divs to 2-column borderless tables so left and right columns stay on the same line
        $flexDivs = $xpath->query('//div[contains(@style, "display: flex") or contains(@style, "display:flex")]');
        foreach ($flexDivs as $flex) {
            $childDivs = [];
            foreach ($flex->childNodes as $child) {
                if ($child instanceof \DOMElement) {
                    $childDivs[] = $child;
                }
            }

            if (count($childDivs) === 2) {
                $hasBorderBottom = (strpos($flex->getAttribute('style'), 'border-bottom') !== false);

                $table = $dom->createElement('table');
                $table->setAttribute('border', '0');
                $table->setAttribute('cellspacing', '0');
                $table->setAttribute('cellpadding', '0');
                $tableStyle = 'width: 100%; border-collapse: collapse; margin-bottom: 12px;';
                if ($hasBorderBottom) {
                    $tableStyle .= ' border-bottom: 2px solid #000000; padding-bottom: 4px;';
                }
                $table->setAttribute('style', $tableStyle);

                $tr = $dom->createElement('tr');
                
                // Left Cell
                $tdLeft = $dom->createElement('td');
                $tdLeft->setAttribute('align', 'left');
                $tdLeft->setAttribute('style', 'width: 50%; vertical-align: top; border: none; padding: 0;');
                while ($childDivs[0]->hasChildNodes()) {
                    $tdLeft->appendChild($childDivs[0]->firstChild);
                }
                $tr->appendChild($tdLeft);

                // Right Cell
                $tdRight = $dom->createElement('td');
                $tdRight->setAttribute('align', 'right');
                $tdRight->setAttribute('style', 'width: 50%; vertical-align: top; border: none; text-align: right; padding: 0;');
                while ($childDivs[1]->hasChildNodes()) {
                    $tdRight->appendChild($childDivs[1]->firstChild);
                }
                $tr->appendChild($tdRight);

                $table->appendChild($tr);
                $flex->parentNode->replaceChild($table, $flex);
            }
        }

        $wrapper = $dom->getElementById('wrapper');
        $bodyHtml = '';
        if ($wrapper) {
            foreach ($wrapper->childNodes as $child) {
                $bodyHtml .= $dom->saveHTML($child);
            }
        }

        $document->html_content = html_entity_decode($bodyHtml, ENT_QUOTES | ENT_SUBSTITUTE | ENT_XHTML, 'UTF-8');

        // Load Dompdf options
        $pdf = Pdf::loadView('documents.pdf', compact('document'));
        
        // Configure PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true); // Allow loading remote fonts/images
        $pdf->setOption('defaultFont', 'Times New Roman');

        $fileName = str_replace(' ', '_', $document->title) . '.pdf';
        return $pdf->download($fileName);
    }
}
