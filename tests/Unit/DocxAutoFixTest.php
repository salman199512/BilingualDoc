<?php

namespace Tests\Unit;

use App\Services\DocxAutoFixService;
use PHPUnit\Framework\TestCase;
use ZipArchive;
use DOMDocument;

class DocxAutoFixTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'docx_test_') . '.docx';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    /**
     * Test that legacy Gujarati fonts (TitleLight, TitleLightTwo, TitleTwo, AbhayTwo)
     * are detected, text is converted, font size is set to 13pt (26) and font family
     * is changed to target Gujarati font.
     */
    public function test_legacy_gujarati_font_conversion_and_sizing(): void
    {
        // 1. Create a mock DOCX file structure (Zip containing document.xml and settings.xml)
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($this->tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE));

        $documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:body>
        <w:p>
            <w:r>
                <w:rPr>
                    <w:rFonts w:ascii="TitleLight" w:hAnsi="TitleLight" w:cs="TitleLight"/>
                </w:rPr>
                <w:t>íkkhe¾ MÚ¤ Mkne</w:t>
            </w:r>
        </w:p>
        <w:sectPr>
            <w:pgSz w:w="12240" w:h="15840"/>
            <w:pgMar w:top="1440" w:bottom="1440" w:left="1440" w:right="1440"/>
        </w:sectPr>
    </w:body>
</w:document>';

        $this->assertTrue($zip->addFromString('word/document.xml', $documentXml));
        $this->assertTrue($zip->addFromString('word/settings.xml', '<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"></w:settings>'));
        $zip->close();

        // 2. Run the Auto Fix Service
        $success = DocxAutoFixService::autoFixDocx($this->tempFile, 'Noto Sans Gujarati', 'Times New Roman');
        $this->assertTrue($success);

        // 3. Inspect modified ZIP contents
        $zipRead = new ZipArchive();
        $this->assertTrue($zipRead->open($this->tempFile));
        $modifiedXml = $zipRead->getFromName('word/document.xml');
        $zipRead->close();

        $this->assertNotEmpty($modifiedXml);

        // 4. Parse XML and assert correctness
        $dom = new DOMDocument();
        $this->assertTrue($dom->loadXML($modifiedXml));

        $runs = $dom->getElementsByTagName('r');
        $this->assertGreaterThan(0, $runs->length);

        $run = $runs->item(0);
        $rPr = $run->getElementsByTagName('rPr')->item(0);
        $this->assertNotNull($rPr);

        // Assert fonts are replaced
        $rFonts = $rPr->getElementsByTagName('rFonts')->item(0);
        $this->assertNotNull($rFonts);
        $this->assertEquals('Noto Sans Gujarati', $rFonts->getAttribute('w:ascii'));
        $this->assertEquals('Noto Sans Gujarati', $rFonts->getAttribute('w:hAnsi'));

        // Assert text is converted and split correctly
        $this->assertEquals(5, $runs->length);

        // Run 0: "તારીખ" (Gujarati)
        $run0 = $runs->item(0);
        $this->assertEquals('તારીખ', $run0->getElementsByTagName('t')->item(0)->textContent);
        $rFonts0 = $run0->getElementsByTagName('rPr')->item(0)->getElementsByTagName('rFonts')->item(0);
        $this->assertEquals('Noto Sans Gujarati', $rFonts0->getAttribute('w:ascii'));
        $this->assertEquals('26', $run0->getElementsByTagName('rPr')->item(0)->getElementsByTagName('sz')->item(0)->getAttribute('w:val'));

        // Run 1: " " (Space - English/Standard font)
        $run1 = $runs->item(1);
        $this->assertEquals(' ', $run1->getElementsByTagName('t')->item(0)->textContent);
        $rFonts1 = $run1->getElementsByTagName('rPr')->item(0)->getElementsByTagName('rFonts')->item(0);
        $this->assertEquals('Times New Roman', $rFonts1->getAttribute('w:ascii'));
        $this->assertEquals('26', $run1->getElementsByTagName('rPr')->item(0)->getElementsByTagName('sz')->item(0)->getAttribute('w:val'));

        // Run 2: "સ્થળ" (Gujarati)
        $run2 = $runs->item(2);
        $this->assertEquals('સ્થળ', $run2->getElementsByTagName('t')->item(0)->textContent);
        $rFonts2 = $run2->getElementsByTagName('rPr')->item(0)->getElementsByTagName('rFonts')->item(0);
        $this->assertEquals('Noto Sans Gujarati', $rFonts2->getAttribute('w:ascii'));

        // Run 3: " " (Space)
        $run3 = $runs->item(3);
        $this->assertEquals(' ', $run3->getElementsByTagName('t')->item(0)->textContent);
        $rFonts3 = $run3->getElementsByTagName('rPr')->item(0)->getElementsByTagName('rFonts')->item(0);
        $this->assertEquals('Times New Roman', $rFonts3->getAttribute('w:ascii'));

        // Run 4: "સહી" (Gujarati)
        $run4 = $runs->item(4);
        $this->assertEquals('સહી', $run4->getElementsByTagName('t')->item(0)->textContent);
        $rFonts4 = $run4->getElementsByTagName('rPr')->item(0)->getElementsByTagName('rFonts')->item(0);
        $this->assertEquals('Noto Sans Gujarati', $rFonts4->getAttribute('w:ascii'));

        // Assert page size A4 (w = 11906, h = 16838)
        $sectPr = $dom->getElementsByTagName('sectPr')->item(0);
        $this->assertNotNull($sectPr);
        $pgSz = $sectPr->getElementsByTagName('pgSz')->item(0);
        $this->assertEquals('11906', $pgSz->getAttribute('w:w'));
        $this->assertEquals('16838', $pgSz->getAttribute('w:h'));

        // Assert margins (left/right = 2268, top/bottom = 1134)
        $pgMar = $sectPr->getElementsByTagName('pgMar')->item(0);
        $this->assertEquals('2268', $pgMar->getAttribute('w:left'));
        $this->assertEquals('2268', $pgMar->getAttribute('w:right'));
        $this->assertEquals('1134', $pgMar->getAttribute('w:top'));
        $this->assertEquals('1134', $pgMar->getAttribute('w:bottom'));
    }

    /**
     * Test that TitleTwo hybrid layout conversion converts real-world legacy text correctly.
     */
    public function test_titletwo_hybrid_layout_conversion(): void
    {
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($this->tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE));

        $documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:body>
        <w:p>
            <w:r>
                <w:rPr>
                    <w:rFonts w:ascii="TitleTwo" w:hAnsi="TitleTwo" w:cs="TitleTwo"/>
                </w:rPr>
                <w:t>:: MkkuøktËLkk{wt ::ykÚke nwt Lke[u Mkne fhLkkh ©e Ãkhuþfw{kh hðkS Mkku÷tfe, W.ð.34 yk., hnu.{w.søkkýk, íkk.Ãkk÷LkÃkwh, S.çkLkkMkfktXk ðk¤k {khk MkíÞÄ{oLke «rík¿kkLkk MkkuøktË WÃkh ònuh fY Awt fu,nwt WÃkhkufík çkíkkðu÷ Xufkýu hnw Awt.</w:t>
            </w:r>
        </w:p>
    </w:body>
</w:document>';

        $this->assertTrue($zip->addFromString('word/document.xml', $documentXml));
        $this->assertTrue($zip->addFromString('word/settings.xml', '<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"></w:settings>'));
        $zip->close();

        $success = DocxAutoFixService::autoFixDocx($this->tempFile, 'Noto Sans Gujarati', 'Times New Roman');
        $this->assertTrue($success);

        $zipRead = new ZipArchive();
        $this->assertTrue($zipRead->open($this->tempFile));
        $modifiedXml = $zipRead->getFromName('word/document.xml');
        $zipRead->close();

        $dom = new DOMDocument();
        $this->assertTrue($dom->loadXML($modifiedXml));

        // Gather all run text contents to verify the combined sentence
        $runs = $dom->getElementsByTagName('r');
        $fullText = '';
        foreach ($runs as $run) {
            $t = $run->getElementsByTagName('t')->item(0);
            if ($t) {
                $fullText .= $t->textContent;
            }
        }

        $expected = ':: સોગંદનામું ::આથી હું નીચે સહી કરનાર શ્રી પરેશકુમાર રવાજી સોલંકી, ઉ.વ.34 આ., રહે.મુ.જગાણા, તા.પાલનપુર, જી.બનાસકાંઠા વાળા મારા સત્યધર્મની પ્રતિજ્ઞાના સોગંદ ઉપર જાહેર કરું છું કે,હું ઉપરોકત બતાવેલ ઠેકાણે રહુ છું.';
        $this->assertEquals($expected, $fullText);
    }
}
