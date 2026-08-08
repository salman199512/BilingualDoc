<?php

namespace App\Services;

class BilingualFormatterService
{
    /**
     * Parses plain text, detects Gujarati and English scripts,
     * and returns structured HTML with explicit styling.
     * Gujarati Unicode range: \u0A80 to \u0AFF.
     * Both scripts set to font size 13pt.
     *
     * @param string $text Raw text
     * @param string $gujaratiFont Font for Gujarati text
     * @param string $englishFont Font for English text
     * @return string Styled HTML
     */
    public static function formatHtml(string $text, string $gujaratiFont = 'Noto Sans Gujarati', string $englishFont = 'Times New Roman'): string
    {
        if (empty($text)) {
            return '';
        }

        // Split text by paragraphs
        $paragraphs = explode("\n", $text);
        $formattedParagraphs = [];

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === '') {
                $formattedParagraphs[] = '<p>&nbsp;</p>';
                continue;
            }

            // Split paragraph text by Gujarati script blocks
            $parts = preg_split('/([\x{0A80}-\x{0AFF}]+)/u', $para, -1, PREG_SPLIT_DELIM_CAPTURE);
            $paraHtml = '';

            foreach ($parts as $part) {
                if ($part === '') continue;

                // Check if part contains Gujarati script characters
                if (preg_match('/[\x{0A80}-\x{0AFF}]/u', $part)) {
                    $paraHtml .= '<span class="lang-gu" style="font-family: \'' . $gujaratiFont . '\'; font-size: 13pt;">' . e($part) . '</span>';
                } else {
                    $paraHtml .= '<span class="lang-en" style="font-family: \'' . $englishFont . '\'; font-size: 13pt;">' . e($part) . '</span>';
                }
            }

            $formattedParagraphs[] = '<p style="line-height: 1.6; margin-bottom: 10px;">' . $paraHtml . '</p>';
        }

        return implode("\n", $formattedParagraphs);
    }

    /**
     * Parses an existing HTML structure, walks through all text nodes,
     * splits mixed-script text runs, and wraps them in appropriately
     * styled span elements.
     *
     * @param string $html Input HTML content
     * @param string $gujaratiFont Font for Gujarati text
     * @param string $englishFont Font for English text
     * @return string Styled HTML content
     */
    public static function formatHtmlDocument(string $html, string $gujaratiFont = 'Noto Sans Gujarati', string $englishFont = 'Times New Roman'): string
    {
        if (empty($html)) {
            return '';
        }

        $dom = new \DOMDocument();
        // Load HTML and preserve UTF-8 structure
        libxml_use_internal_errors(true);
        // We use XML load format to avoid adding head/body wrappers automatically
        $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        if (!$loaded) {
            return $html;
        }

        $xpath = new \DOMXPath($dom);
        // Select all text nodes that are not children of script or style elements
        $textNodes = $xpath->query('//text()[not(parent::script or parent::style)]');

        foreach ($textNodes as $node) {
            $text = $node->nodeValue;
            if (!trim($text)) continue;

            // Split by Gujarati blocks
            $parts = preg_split('/([\x{0A80}-\x{0AFF}]+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
            
            if (count($parts) > 1) {
                $fragment = $dom->createDocumentFragment();
                foreach ($parts as $part) {
                    if ($part === '') continue;

                    $isGuj = preg_match('/[\x{0A80}-\x{0AFF}]/u', $part);
                    $fontFamily = $isGuj ? $gujaratiFont : $englishFont;
                    $className = $isGuj ? 'lang-gu' : 'lang-en';

                    $span = $dom->createElement('span');
                    $span->setAttribute('class', $className);
                    $span->setAttribute('style', "font-family: '{$fontFamily}'; font-size: 13pt;");
                    $span->textContent = $part;
                    $fragment->appendChild($span);
                }
                $node->parentNode->replaceChild($fragment, $node);
            } else {
                // Check if the current node needs styling because parent does not have it
                $parent = $node->parentNode;
                $isGuj = preg_match('/[\x{0A80}-\x{0AFF}]/u', $text);
                $targetFont = $isGuj ? $gujaratiFont : $englishFont;
                $className = $isGuj ? 'lang-gu' : 'lang-en';

                // Wrap if parent is not a span, or if parent span style does not contain target font
                $isSpan = ($parent && strtolower($parent->tagName) === 'span');
                $hasStyle = ($isSpan && $parent->hasAttribute('style') && str_contains($parent->getAttribute('style'), $targetFont));

                if (!$hasStyle) {
                    $span = $dom->createElement('span');
                    $span->setAttribute('class', $className);
                    $span->setAttribute('style', "font-family: '{$targetFont}'; font-size: 13pt;");
                    $span->textContent = $text;
                    $node->parentNode->replaceChild($span, $node);
                } else if ($isSpan) {
                    // Update class names if not present
                    $parent->setAttribute('class', $className);
                }
            }
        }

        // Export and clean the XML header
        $htmlOutput = $dom->saveHTML();
        // Remove the xml encoding header
        $htmlOutput = preg_replace('/<\?xml[^>]*\?>/i', '', $htmlOutput);
        
        // Convert all HTML entities back to raw UTF-8 characters so Dompdf matches text runs directly
        $htmlOutput = html_entity_decode($htmlOutput, ENT_QUOTES | ENT_SUBSTITUTE | ENT_XHTML, 'UTF-8');
        
        return trim($htmlOutput);
    }
}
