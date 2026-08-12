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
     * Convert legacy Gujarati (Gopika, Saral, Krishna, TeraFont, Guj-Bhavna, LMG-Arun)
     * text to modern standard Unicode Gujarati.
     */
    public static function convert(string $text): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        $lines = explode("\n", $text);
        $convertedLines = [];

        foreach ($lines as $line) {
            $convertedLines[] = self::convertLine($line);
        }

        return implode("\n", $convertedLines);
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
