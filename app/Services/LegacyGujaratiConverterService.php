<?php

namespace App\Services;

class LegacyGujaratiConverterService
{
    /**
     * Detect if a text string contains legacy non-Unicode Gujarati characters
     * (Gopika, Krishna, Saral, TeraFont, Guj-Bhavna, LMG-Arun, Shree layouts).
     */
    public static function isLegacyGujarati(string $text): bool
    {
        $patterns = [
            '/,the¾/u',           // તારીખ
            '/MÚt¤/u',           // સ્થળ
            '/Ëne/u',             // સહી
            '/ftÞ{e/u',           // કાયમી
            '/Ëh™t{wk/u',         // સરનામું
            '/fhðt/u',            // કરવા
            '/ytÃ/u',             // આપ
            '/y{u/u',             // અમે
            '/¼h...tE/u',         // ભરપાઈ
            '/ftfe/u',            // બાકી
            '/Awk/u',             // છું
            '/„wsht,/u',          // ગુજરાત
            '/òý/u',              // જાણ
            '/Mkne/u',            // સહી (LMG)
            '/Mne/u',             // સહી (LMG)
            '/yLku/u',            // અને (LMG)
            '/fhLkkh/u',          // કરનાર (LMG)
            '/Mkkuøkt/u',         // સોગંદ (LMG)
            '/fhðk/u',            // કરવા (LMG)
            '/Awt/u',             // છું (LMG)
            '/[f¾øGsÍxXzZý,ÚË™ŒÃç¼{Þh÷ðþ»Ën¤û¿„Ä][tewquok¢]/u',
        ];

        foreach ($patterns as $pattern) {
            if (@preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect if a text string contains LMG-Arun/TitleTwo layout characters.
     */
    public static function isLMG(string $text): bool
    {
        $patterns = [
            '/Mkne/u',
            '/Mne/u',
            '/yLku/u',
            '/fhLkkh/u',
            '/Mkkuøkt/u',
            '/fhðk/u',
            '/Awt/u',
        ];

        foreach ($patterns as $pattern) {
            if (@preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert legacy Gujarati (Gopika, Saral, Krishna, TeraFont, Guj-Bhavna, LMG-Arun)
     * text to modern standard Unicode Gujarati.
     */
    public static function convert(string $text): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        if (self::isLMG($text)) {
            return self::convertLMG($text);
        }

        $lines = explode("\n", $text);
        $convertedLines = [];

        foreach ($lines as $line) {
            $convertedLines[] = self::convertLine($line);
        }

        return implode("\n", $convertedLines);
    }

    /**
     * Convert LMG-Arun / TitleTwo encoded text to Unicode Gujarati.
     */
    public static function convertLMG(string $text): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        $lines = explode("\n", $text);
        $convertedLines = [];

        foreach ($lines as $line) {
            $convertedLines[] = self::convertLMGLine($line);
        }

        return implode("\n", $convertedLines);
    }

    /**
     * Convert a single line of LMG-Arun / TitleTwo text.
     */
    private static function convertLMGLine(string $text): string
    {
        // Consonant list for LMG/TitleTwo
        $consonants = 'f¾gøG\\[sÍxXzZý,íÚËŒL™ÃVç¼{Þh÷ðþ»Mn¤û¿º©«SAxWg';

        // 1. Swap 'k' + 'ú' (vowel + reph/conjunct) to 'ú' + 'k' (e.g. kú -> úk)
        $text = str_replace('kú', 'úk', $text);

        // 2. Swap hrasva 'r' with the following consonant + 'k' if present
        $text = preg_replace('/r([' . $consonants . '])k/u', '$1kિ', $text);
        // Fallback for hrasva 'r' without trailing 'k'
        $text = preg_replace('/r([' . $consonants . '])/u', '$1િ', $text);

        // 3. Handle reph 'eo' or 'o' after consonant (e.g. {o -> ર્મ, ðeo -> ર્વી)
        $text = preg_replace('/([' . $consonants . '])eo/u', 'ર્$1ી', $text);
        $text = preg_replace('/([' . $consonants . '])o/u', 'ર્$1', $text);

        // 4. Known phrases and combined characters
        $phrases = [
            // Standard typewriter vowel mappings (order is important!)
            'økw' => 'ગુ',
            'gkw' => 'ગુ',
            
            'Lkku' => 'નો',
            'Lku' => 'ને',
            'çkku' => 'બો',
            'çku' => 'બે',
            'íkku' => 'તો',
            'íku' => 'તે',
            'Mkku' => 'સો',
            'Mku' => 'સે',
            'Ãkku' => 'પો',
            'Ãku' => 'પે',
            'økku' => 'ગો',
            'øku' => 'ગે',
            'gkku' => 'ગો',
            'gku' => 'ગો',
            'ge' => 'ગે',
            'gu' => 'ગે',
            'fku' => 'કો',
            'fu' => 'કે',
            'hku' => 'રો',
            'hu' => 'રે',
            'ðku' => 'વો',
            'ðu' => 'વે',
            'nku' => 'હો',
            'nu' => 'હે',
            '÷ku' => 'લો',
            '÷u' => 'લે',
            
            'Mð' => 'સ્વ',
            'íÞ' => 'ત્ય',
            'ke' => 'ી',
            'wt' => 'ું',
            'Awt' => 'છું',
            '©e' => 'શ્રી',
            '©' => 'શ્ર',
            '«' => 'પ્ર',
            'S' => 'જી',
            'çk' => 'બ',
            'Ãk' => 'પ',
            'íkk' => 'તા',
            'ík' => 'ત',
            'Ãkk' => 'પા',
            'Lkk' => 'ના',
            'Lk' => 'ન',
            'fkt' => 'કાં',
            'Xk' => 'ઠા',
            'ðk' => 'વા',
            '¤k' => 'ળા',
            'Lke' => 'ની',
            '[u' => 'ચે',
            'Mne' => 'સહી',
            'Mkne' => 'સહી',
            'fhLkkh' => 'કરનાર',
            'Ãkwºk' => 'પુત્ર',
            'ò' => 'જા',
            'fY' => 'કરું',
            'fí' => 'ક્ત',
            'LM' => 'ન્સ',
            'økt' => 'ગં',
            'Mkk' => 'સા',
            'Mk' => 'સ',
        ];

        foreach ($phrases as $k => $v) {
            $text = str_replace($k, $v, $text);
        }

        // 5. Single character mappings
        $chars = [
            'f' => 'ક',
            '¾' => 'ખ',
            'g' => 'ગ',
            'ø' => 'ગ',
            'G' => 'ઘ',
            '[' => 'ચ',
            's' => 'જ',
            'Í' => 'ઝ',
            'x' => 'ટ',
            'X' => 'ઠ',
            'z' => 'ડ',
            'Z' => 'ઢ',
            'ý' => 'ણ',
            'í' => 'ત',
            'Ú' => 'થ',
            'Ë' => 'દ',
            'Œ' => 'દ',
            'Ä' => 'ધ',
            'L' => 'ન',
            '™' => 'ન',
            'Ã' => 'પ્',
            'V' => 'ફ',
            'ç' => 'બ્',
            '¼' => 'ભ',
            '{' => 'મ',
            'Þ' => 'ય',
            'h' => 'ર',
            '÷' => 'લ',
            'ð' => 'વ',
            'þ' => 'શ',
            '»' => 'ષ',
            'M' => 'સ્',
            'n' => 'હ',
            '¤' => 'ળ',
            'û' => 'ક્ષ',
            '¿' => 'જ્ઞ',
            'º' => 'ત્ર',
            'y' => 'અ',
            'k' => 'ા',
            'e' => 'ી',
            'w' => 'ુ',
            'q' => 'ૂ',
            'u' => 'ે',
            't' => 'ં',
            'W' => 'ઉ',
            'Y' => 'રું',
            'A' => 'છ',
            'Ù' => '્ર',
            'ú' => '્ર',
            'E' => 'ઈ',
        ];

        foreach ($chars as $k => $v) {
            $text = str_replace($k, $v, $text);
        }

        // Clean up double matras and formatting issues
        $text = str_replace('અા', 'આ', $text);
        $text = str_replace('ાા', 'ા', $text);
        $text = str_replace('ાી', 'ી', $text);
        $text = str_replace('ાે', 'ો', $text);
        $text = str_replace('ાૈ', 'ૌ', $text);
        $text = str_replace('્ા', 'ા', $text);
        $text = str_replace('ત્ા', 'તા', $text);
        $text = str_replace('પ્ા', 'પા', $text);
        $text = str_replace('્ા', 'ા', $text);
        $text = str_replace('પુત્રા', 'પુત્ર', $text);

        return $text;
    }

    /**
     * Convert a single line of text
     */
    private static function convertLine(string $text): string
    {
        // 1. Multi-character legal phrases & words
        $phrases = [
            ',the¾' => 'તારીખ',
            ',the:' => 'તારીખ:',
            ',the'  => 'તારીખ',
            'MÚt¤'  => 'સ્થળ',
            'MÚt:'  => 'સ્થળ:',
            'Ëne'   => 'સહી',
            'Ëne.'  => 'સહી.',
            'Ëh™t{wk' => 'સરનામું',
            'ftÞ{e' => 'કાયમી',
            'rðøkík' => 'વિગત',
            'r{Õf,{tkÚte' => 'મિલકતમાંથી',
            'r{Õfík' => 'મિલકત',
            'r{÷fík' => 'મિલકત',
            'ytÄth' => 'આધાર',
            'YkrÃÞt' => 'રૂપિયા',
            'YkrÃÞt.' => 'રૂપિયા.',
            'Yt.'   => 'રૂ.',
            'Y.'    => 'રૂ.',
            '…tðíte' => 'પાવતી',
            'Ëtuøkkt' => 'સોગંદ',
            '™t{u'  => 'નામે',
            '™t{wk' => 'નામું',
            'r™Þ{'  => 'નિયમ',
            'f÷{'   => 'કલમ',
            'ytÃ™th' => 'આપનાર',
            '÷u™th' => 'લેનાર',
            'ƒtf'   => 'ઉક્ત',
            '¾tºte' => 'ખાતરી',
            'Awk.'  => 'છું.',
            'Awk'   => 'છું',
            'Aum'   => 'છે',
            'Au'    => 'છે',
            'r™»V¤' => 'નિષ્ફળ',
            'ò{e™„ehe' => 'જામીનગીરી',
            '„wsht,' => 'ગુજરાત',
            'yrÄfth' => 'અધિકાર',
            'ËkÃkqýo' => 'સંપૂર્ણ',
            'rðftË' => 'વિકાસ',
            'r™„{'  => 'નિગમ',
            '„tkÄe™„h™u' => 'ગાંધીનગરને',
            'Ëtu÷kfe' => 'સોલંકી',
            'rŒ™uþ¼tE' => 'દિનેશભાઈ',
            'y{ht¼tE' => 'અમરાભાઈ',
            'ytu®¾' => 'ઓળખ',
            '¼h...tE' => 'ભરપાઈ',
            'yt...wk' => 'આપું',
            '...t÷™...wh' => 'પાલનપુર',
            '...wY' => 'પૂરું',
            '...ý'  => 'પણ',
            'yt...™th™e' => 'આપનારની',
            'òý'    => 'જાણ',
            ',™e'   => 'તેની',
            'Út,tk' => 'થતાં',
            'ftfe'  => 'બાકી',
            '[z,'   => 'ચડત',
            '[z'    => 'ચડત',
            'hf{'   => 'રકમ',
            ',tíftr÷f' => 'તાત્કાલિક',
            'fhðt™e' => 'કરવાની',
            'fhðt{tk' => 'કરવામાં',
            'nwk'   => 'હું',
            'òu'    => 'જો',
            'sWk'   => 'જઉં',
            ',tu'   => 'તો',
            '{u'    => 'મેં',
            ',hefu' => 'તરીકે',
            ',hsw'  => 'રજૂ',
            'fhu÷'  => 'કરેલ',
            'ytÃt™e' => 'આપની',
            'yÚtðt' => 'અથવા',
            '{tht'  => 'મારા',
            'ðËw÷'  => 'વસૂલ',
            'y™w.'  => 'અનુ.',
            'stre'  => 'જાતિ',
            ',Útt'  => 'તથા',
            'rð.st.' => 'વિ.જા.',
            'r÷.'   => 'લિ.',
        ];

        foreach ($phrases as $k => $v) {
            $text = str_replace($k, $v, $text);
        }

        // 2. Inverted hrasva 'i' matra replacements
        $text = preg_replace('/r([f¾øGsÍxXzZý,ÚË™ŒÃç¼{Þh÷ðþ»Ën¤û¿„Ä])/u', '$1િ', $text);
        $text = preg_replace('/\[z([f¾øGsÍxXzZý,ÚË™ŒÃç¼{Þh÷ðþ»Ën¤û¿„Ä])/u', '$1િ', $text);
        $text = preg_replace('/í([f¾øGsÍxXzZý,ÚË™ŒÃç¼{Þh÷ðþ»Ën¤û¿„Ä])/u', '$1િ', $text);

        // 3. Single-character and glyph mappings
        $chars = [
            '„' => 'ગ',
            'Ä' => 'ધ',
            'Œ' => 'દ',
            '¼' => 'ભ',
            '¾' => 'ખ',
            'ø' => 'ગ',
            'G' => 'ઘ',
            's' => 'જ',
            'Í' => 'ઝ',
            'x' => 'ટ',
            'X' => 'ઠ',
            'z' => 'ડ',
            'Z' => 'ઢ',
            'ý' => 'ણ',
            ',' => 'ત',
            'Ú' => 'થ',
            'Ë' => 'સ',
            '™' => 'ન',
            'V' => 'ફ',
            '{' => 'મ',
            'Þ' => 'ય',
            'h' => 'ર',
            '÷' => 'લ',
            'ð' => 'વ',
            'þ' => 'શ',
            '»' => 'ષ',
            'n' => 'હ',
            '¤' => 'ળ',
            'f' => 'ક',
            'û' => 'ક્ષ',
            '¿' => 'જ્ઞ',
            'ºt' => 'ત્રા',
            'º' => 'ત્ર',
            '©' => 'શ્ર',
            'ô' => 'શ્',
            'î' => 'દ્વ',
            'æ' => 'ધ્',
            'í' => 'ત્',
            'Õ' => 'ખ્',
            'õ' => 'ક્',
            'Ãk' => 'પ',
            'Ã' => 'પ્',
            'çk' => 'બ',
            'ç' => 'બ્',
            'Ç' => 'ભ્ય',
            'M' => 'સ્',
            'Ü' => 'ષ્ટ',
            'Ý' => 'ષ્ઠ',
            'ß' => 'દ્દ',
            '¢' => 'ૃ',
            'Ù' => '્ર',
            '§' => '્ર',
            'ytu' => 'ઓ',
            'yt' => 'આ',
            'yut' => 'ઐ',
            'yu' => 'એ',
            'yk' => 'અં',
            'y:' => 'અઃ',
            'y' => 'અ',
            'EÃ' => 'ઈ',
            'E' => 'ઈ',
            'WÃ' => 'ઊ',
            'W' => 'ઉ',
            'É' => 'ઋ',
            '¥' => 'રૂ',
            'ò' => 'જો',
            'ó' => 'ઝો',
            't' => 'ા',
            'e' => 'ી',
            'w' => 'ુ',
            'q' => 'ૂ',
            'u' => 'ે',
            'o' => 'ો',
            'k' => 'ં',
            '®' => 'ં',
            'õ' => 'કો',
            'ö' => 'ડો',
        ];

        foreach ($chars as $k => $v) {
            $text = str_replace($k, $v, $text);
        }

        // Clean double matras
        $text = str_replace('ાી', 'ી', $text);
        $text = str_replace('ાે', 'ો', $text);
        $text = str_replace('ાૈ', 'ૌ', $text);
        $text = str_replace('જોે', 'જો', $text);

        return $text;
    }
}
