<?php

namespace App\Services;

class PageMakerParserService
{
    /**
     * Parse an Adobe / Aldus PageMaker file (.pmd, .p65, .pm6, .pm5, .ptd, .txt)
     * and convert its stories and text blocks into clean, standard bilingual HTML.
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
            // 2. Parse binary PageMaker (.pm5 / .pm6 / .p65 / .pmd)
            $extractedText = self::parseBinaryPageMaker($rawContent);
        }

        if (empty(trim($extractedText))) {
            throw new \Exception("No readable text stories found in the PageMaker file.");
        }

        // 3. Convert legacy non-Unicode Gujarati (Gopika/Saral/Krishna/TeraFont) to standard Unicode
        if (LegacyGujaratiConverterService::isLegacyGujarati($extractedText)) {
            $extractedText = LegacyGujaratiConverterService::convert($extractedText);
        }

        // 4. Format extracted text into bilingual HTML
        return BilingualFormatterService::formatHtml($extractedText, $gujaratiFont, $englishFont);
    }

    /**
     * Check if content is PageMaker Tagged Text
     */
    private static function isTaggedText(string $content): bool
    {
        return (bool) preg_match('/<PMTaggedText|<v\d+\.\d+>|<ParaStyle:|<Font:/i', substr($content, 0, 1024));
    }

    /**
     * Parse PageMaker Tagged Text (.ptd / .txt / exported stories)
     */
    private static function parseTaggedText(string $content): string
    {
        // Strip PM Tagged Text header lines if present
        $content = preg_replace('/^<PMTaggedText[^>]*>/i', '', $content);
        $content = preg_replace('/<v\d+\.\d+>[^\r\n]*/i', '', $content);

        // Replace paragraph and style tags
        $content = preg_replace('/<\\/?ParaStyle:[^>]*>/i', "\n\n", $content);
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
        $content = preg_replace('/<[A-Za-z]+:[^>]*>/', '', $content);

        // Replace special escape codes
        $content = str_replace(['\\<', '\\>'], ['<', '>'], $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        return self::cleanExtractedText($content);
    }

    /**
     * Parse binary Adobe/Aldus PageMaker (.pm5 / .pm6 / .p65 / .pmd) files
     * using multi-pass extraction for 8-bit text streams and UTF-16 wide streams.
     */
    public static function parseBinaryPageMaker(string $binary): string
    {
        $textBlocks = [];

        // Common PostScript / Aldus / PageMaker internal system metadata to ignore
        $metadataBlacklist = [
            'Adobe', 'PageMaker', 'Aldus', 'Times-Roman', 'Times-Bold', 'Times-Italic',
            'Helvetica', 'Helvetica-Bold', 'Helvetica-Oblique', 'Courier', 'Courier-Bold',
            'Symbol', 'ZapfDingbats', 'AvantGarde', 'Bookman', 'NewCenturySchlbk', 'Palatino',
            'PostScript', 'DisplayPostScript', 'WinAnsiEncoding', 'MacRomanEncoding',
            'StandardEncoding', 'ISOLatin1Encoding', 'FontDirectory', 'FontName',
            'DefaultFont', 'CreationDate', 'ModDate', 'Producer', 'Creator', 'Title',
            'PShop', 'TIFF', 'EPSF', 'ColorSpace', 'DeviceRGB', 'DeviceCMYK', 'ArialMT',
            'HelveticaLT'
        ];

        // Pass 1: Extract 8-bit text runs
        $pass1Blocks = self::extract8BitRuns($binary, $metadataBlacklist);
        if (!empty($pass1Blocks)) {
            $textBlocks = array_merge($textBlocks, $pass1Blocks);
        }

        // Pass 2: Extract 16-bit UTF-16LE / UTF-16BE text runs (common in PM6.5 and PMD 7)
        if (empty($textBlocks)) {
            $pass2Blocks = self::extract16BitRuns($binary, $metadataBlacklist);
            if (!empty($pass2Blocks)) {
                $textBlocks = array_merge($textBlocks, $pass2Blocks);
            }
        }

        // Deduplicate and filter out non-story noise
        $uniqueBlocks = [];
        $seen = [];
        foreach ($textBlocks as $block) {
            $normalized = trim($block);
            
            // Check if block is a valid story sentence/paragraph
            if (!self::isRealStoryBlock($normalized)) {
                continue;
            }

            $hash = md5($normalized);
            if (!isset($seen[$hash])) {
                $seen[$hash] = true;
                $uniqueBlocks[] = $normalized;
            }
        }

        $fullText = implode("\n\n", $uniqueBlocks);
        return self::cleanExtractedText($fullText);
    }

    /**
     * Scan binary stream for consecutive 8-bit printable runs
     */
    private static function extract8BitRuns(string $binary, array $blacklist): array
    {
        $length = strlen($binary);
        $blocks = [];
        $current = '';
        $inRun = false;

        for ($i = 0; $i < $length; $i++) {
            $byte = ord($binary[$i]);

            // Printable ASCII (32-126), Tab (9), LF (10), CR (13), or extended ANSI/Gujarati/Indian font bytes (128-254 except 0xFF)
            $isPrintable = ($byte >= 32 && $byte <= 126) || $byte === 9 || $byte === 10 || $byte === 13 || ($byte >= 128 && $byte <= 254);

            if ($isPrintable) {
                if ($byte === 13 || $byte === 12 || $byte === 11) {
                    $current .= "\n";
                } elseif ($byte === 9) {
                    $current .= "\t";
                } elseif ($byte === 10) {
                    $current .= "\n";
                } else {
                    $current .= $binary[$i];
                }
                $inRun = true;
            } else {
                if ($inRun) {
                    $cleaned = self::normalizeAndValidateRun($current, $blacklist);
                    if ($cleaned !== null) {
                        $blocks[] = $cleaned;
                    }
                    $current = '';
                    $inRun = false;
                }
            }
        }

        if ($inRun && !empty($current)) {
            $cleaned = self::normalizeAndValidateRun($current, $blacklist);
            if ($cleaned !== null) {
                $blocks[] = $cleaned;
            }
        }

        return $blocks;
    }

    /**
     * Scan binary stream for 16-bit UTF-16LE / UTF-16BE text runs
     */
    private static function extract16BitRuns(string $binary, array $blacklist): array
    {
        $length = strlen($binary);
        $blocks = [];
        
        $currentLe = '';
        for ($i = 0; $i < $length - 1; $i += 2) {
            $b1 = ord($binary[$i]);
            $b2 = ord($binary[$i + 1]);

            if ($b2 === 0 && (($b1 >= 32 && $b1 <= 126) || $b1 === 9 || $b1 === 10 || $b1 === 13)) {
                $currentLe .= chr($b1);
            } else {
                if (strlen($currentLe) >= 6) {
                    $cleaned = self::normalizeAndValidateRun($currentLe, $blacklist);
                    if ($cleaned !== null) {
                        $blocks[] = $cleaned;
                    }
                }
                $currentLe = '';
            }
        }

        if (strlen($currentLe) >= 6) {
            $cleaned = self::normalizeAndValidateRun($currentLe, $blacklist);
            if ($cleaned !== null) {
                $blocks[] = $cleaned;
            }
        }

        return $blocks;
    }

    /**
     * Validate if a block is real story content
     */
    public static function isRealStoryBlock(string $block): bool
    {
        $trimmed = trim($block);
        $len = mb_strlen($trimmed);

        if ($len < 4) return false;

        // Discard pure binary coordinate noise (e.g. ÿ, À, ê, €, $, /, etc.)
        if (preg_match('/^[ÿ\x{00FF}\x{00C0}-\x{00DF}$€\/\\\\\s\d.,;:\-—]+$/u', $trimmed)) {
            // Only keep if it looks like a valid date e.g. "27-06-2014"
            if (!preg_match('/\d{1,4}[\/\-.]\d{1,2}[\/\-.]\d{1,4}/', $trimmed)) {
                return false;
            }
        }

        // Recognized legal/court keywords or patterns are always kept
        if (preg_match('/(,the¾|MÚt¤|Ëne|ftÞ{e|Ëh™t{wk|™t{|Yt\.|YkrÃÞt|Awk|fhðt|ytÃ|તારીખ|સ્થળ|સહી|નામ|રૂપિયા|રૂ\.|કાયમી|સરનામું|Agreement|Notice|Loan|Government|Circular)/u', $trimmed)) {
            return true;
        }

        // Meaningful story sentences
        if ($len >= 12 && preg_match('/\s+/', $trimmed)) {
            $letterCount = preg_match_all('/[a-zA-Z0-9\x{0A80}-\x{0AFF}]/u', $trimmed);
            if ($letterCount / $len >= 0.35) {
                return true;
            }
        }

        // Short labeled lines e.g. "Name: ...", "Place: ..."
        if (preg_match('/^[a-zA-Z0-9\x{0A80}-\x{0AFF}\s,.:\-\/()_]{6,}$/u', $trimmed) && preg_match('/[\s:\-]/', $trimmed)) {
            return true;
        }

        return false;
    }

    /**
     * Normalize run encoding to UTF-8 and validate against blacklist
     */
    private static function normalizeAndValidateRun(string $rawRun, array $blacklist): ?string
    {
        // Convert to valid UTF-8
        $converted = $rawRun;
        if (!mb_check_encoding($converted, 'UTF-8')) {
            $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $rawRun);
            if ($converted === false || empty($converted)) {
                $converted = mb_convert_encoding($rawRun, 'UTF-8', 'ISO-8859-1, Windows-1252, ASCII');
            }
        }

        $trimmed = trim($converted);
        
        if (strlen($trimmed) < 4) {
            return null;
        }

        // Check against system metadata blacklist
        foreach ($blacklist as $meta) {
            if (strcasecmp($trimmed, $meta) === 0) {
                return null;
            }
        }

        return $trimmed;
    }

    /**
     * Clean and normalize extracted text
     */
    private static function cleanExtractedText(string $text): string
    {
        // Standardize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Remove control characters except tab and newline
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Remove long non-text divider sequences
        $text = preg_replace('/[_\-=*~]{8,}/', '------------------------', $text);

        // Remove excessive empty lines
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        // Ensure valid UTF-8
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = @iconv('Windows-1252', 'UTF-8//IGNORE', $text) ?: mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1, Windows-1252, UTF-8');
        }

        return trim($text);
    }
}
