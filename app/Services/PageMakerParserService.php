<?php

namespace App\Services;

class PageMakerParserService
{
    /**
     * Parse an Adobe PageMaker file (.pmd, .p65, .pm6, .pm5, .ptd, .txt)
     * and convert its content into clean, standard bilingual HTML.
     *
     * @param string $filePath Absolute path to the PageMaker file
     * @param string $gujaratiFont Target Gujarati font
     * @param string $englishFont Target English font
     * @return string Formatted HTML string
     */
    public static function parseToHtml(string $filePath, string $gujaratiFont = 'Noto Sans Gujarati', string $englishFont = 'Times New Roman'): string
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception("File not found or unreadable: {$filePath}");
        }

        $rawContent = file_get_contents($filePath);
        if (empty($rawContent)) {
            throw new \Exception("The PageMaker file is empty.");
        }

        // 1. Check if it is a PageMaker Tagged Text file
        if (self::isTaggedText($rawContent)) {
            $extractedText = self::parseTaggedText($rawContent);
        } else {
            // 2. Parse binary PageMaker (.pmd / .p65 / .pm6 / .pm5)
            $extractedText = self::parseBinaryPageMaker($rawContent);
        }

        if (empty(trim($extractedText))) {
            throw new \Exception("No readable text stories found in the PageMaker file.");
        }

        // 3. Format extracted text into bilingual HTML
        return BilingualFormatterService::formatHtml($extractedText, $gujaratiFont, $englishFont);
    }

    /**
     * Check if content is PageMaker Tagged Text
     */
    private static function isTaggedText(string $content): bool
    {
        return (bool) preg_match('/<PMTaggedText|<v\d+\.\d+>|<ParaStyle:|<Font:/i', substr($content, 0, 500));
    }

    /**
     * Parse PageMaker Tagged Text (.ptd / .txt / exported stories)
     */
    private static function parseTaggedText(string $content): string
    {
        // Strip PM Tagged Text header lines if present
        $content = preg_replace('/^<PMTaggedText[^>]*>/i', '', $content);
        $content = preg_replace('/<v\d+\.\d+>[^\r\n]*/i', '', $content);

        // Replace paragraph and soft break tags
        $content = preg_replace('/<\\/?ParaStyle:[^>]*>/i', "\n", $content);
        $content = preg_replace('/<\\/?CharStyle:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?Font:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?FontSize:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?Color:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?Leading:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?Tracking:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?HorizScale:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?Kern:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?BaselineShift:[^>]*>/i', '', $content);
        $content = preg_replace('/<\\/?Rule[^>]*>/i', '', $content);
        $content = preg_replace('/<[A-Za-z]+:[^>]*>/', '', $content); // generic PM tag

        // Replace special escape codes
        $content = str_replace(['\\<', '\\>'], ['<', '>'], $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        return self::cleanExtractedText($content);
    }

    /**
     * Parse binary Adobe PageMaker (.pmd / .p65 / .pm6 / .pm5) files
     * by scanning for text story streams, string blocks, and UTF-8 / extended ANSI sequences.
     */
    private static function parseBinaryPageMaker(string $binary): string
    {
        $length = strlen($binary);
        $textBlocks = [];
        $currentBlock = '';
        $inTextBlock = false;

        // Common PostScript / PageMaker internal system font names and metadata to filter out
        $metadataBlacklist = [
            'Adobe', 'PageMaker', 'Aldus', 'Times-Roman', 'Times-Bold', 'Times-Italic',
            'Helvetica', 'Helvetica-Bold', 'Helvetica-Oblique', 'Courier', 'Courier-Bold',
            'Symbol', 'ZapfDingbats', 'AvantGarde', 'Bookman', 'NewCenturySchlbk', 'Palatino',
            'PostScript', 'DisplayPostScript', 'WinAnsiEncoding', 'MacRomanEncoding',
            'StandardEncoding', 'ISOLatin1Encoding', 'FontDirectory', 'FontName',
            'DefaultFont', 'CreationDate', 'ModDate', 'Producer', 'Creator', 'Title'
        ];

        // Scan the binary stream for printable character runs (ASCII 32-126, tabs, newlines, and UTF-8 / extended characters 128-255)
        for ($i = 0; $i < $length; $i++) {
            $byte = ord($binary[$i]);

            // Printable ASCII or Tab (9), LF (10), CR (13), or non-breaking space (160), or multibyte/extended chars
            $isPrintable = ($byte >= 32 && $byte <= 126) || $byte === 9 || $byte === 10 || $byte === 13 || ($byte >= 160 && $byte <= 255);

            if ($isPrintable) {
                // Map carriage return or form feed to standard newline
                if ($byte === 13 || $byte === 12 || $byte === 11) {
                    $currentBlock .= "\n";
                } elseif ($byte === 9) {
                    $currentBlock .= "\t";
                } elseif ($byte === 10) {
                    $currentBlock .= "\n";
                } else {
                    $currentBlock .= $binary[$i];
                }
                $inTextBlock = true;
            } else {
                if ($inTextBlock) {
                    $trimmed = trim($currentBlock);
                    // Filter out short noise (< 3 chars) or pure punctuation/binary noise
                    if (strlen($trimmed) >= 3 && preg_match('/[a-zA-Z0-9\x{0A80}-\x{0AFF}\x80-\xFF]/u', $trimmed)) {
                        $isMetadata = false;
                        foreach ($metadataBlacklist as $meta) {
                            if (strcasecmp($trimmed, $meta) === 0) {
                                $isMetadata = true;
                                break;
                            }
                        }
                        if (!$isMetadata) {
                            $textBlocks[] = $trimmed;
                        }
                    }
                    $currentBlock = '';
                    $inTextBlock = false;
                }
            }
        }

        // Add remaining block
        if ($inTextBlock && !empty(trim($currentBlock))) {
            $trimmed = trim($currentBlock);
            if (strlen($trimmed) >= 3) {
                $textBlocks[] = $trimmed;
            }
        }

        // Join text blocks and clean up
        $fullText = implode("\n\n", $textBlocks);

        return self::cleanExtractedText($fullText);
    }

    /**
     * Clean and normalize extracted text
     */
    private static function cleanExtractedText(string $text): string
    {
        // Fix line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Remove control characters except tab and newline
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Remove repetitive non-text sequences
        $text = preg_replace('/[_\-=*~]{8,}/', '------------------------', $text);

        // Remove excessive empty lines
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        // Ensure valid UTF-8
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1, Windows-1252, UTF-8');
        }

        return trim($text);
    }
}
