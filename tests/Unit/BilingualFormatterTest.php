<?php

namespace Tests\Unit;

use App\Services\BilingualFormatterService;
use PHPUnit\Framework\TestCase;

class BilingualFormatterTest extends TestCase
{
    /**
     * Test that empty input returns empty string.
     */
    public function test_empty_input_returns_empty_string(): void
    {
        $this->assertEquals('', BilingualFormatterService::formatHtml(''));
    }

    /**
     * Test that pure English text is wrapped in English font.
     */
    public function test_pure_english_text_formatting(): void
    {
        $input = "Hello World";
        $expected = '<p style="line-height: 1.6; margin-bottom: 10px;"><span class="lang-en" style="font-family: \'Times New Roman\'; font-size: 13pt;">Hello World</span></p>';
        $this->assertEquals($expected, BilingualFormatterService::formatHtml($input));
    }

    /**
     * Test that pure Gujarati text is wrapped in Gujarati font.
     */
    public function test_pure_gujarati_text_formatting(): void
    {
        $input = "નમસ્તે ગુજરાત";
        $expected = '<p style="line-height: 1.6; margin-bottom: 10px;"><span class="lang-gu" style="font-family: \'Noto Sans Gujarati\'; font-size: 13pt;">નમસ્તે</span><span class="lang-en" style="font-family: \'Times New Roman\'; font-size: 13pt;"> </span><span class="lang-gu" style="font-family: \'Noto Sans Gujarati\'; font-size: 13pt;">ગુજરાત</span></p>';
        $this->assertEquals($expected, BilingualFormatterService::formatHtml($input));
    }

    /**
     * Test that mixed English and Gujarati text is split and wrapped correctly.
     */
    public function test_mixed_scripts_formatting(): void
    {
        $input = "Welcome નમસ્તે";
        
        $html = BilingualFormatterService::formatHtml($input);
        
        // Assert that the output contains Times New Roman for English and Noto Sans Gujarati for Gujarati
        $this->assertStringContainsString('Times New Roman', $html);
        $this->assertStringContainsString('Noto Sans Gujarati', $html);
        $this->assertStringContainsString('Welcome', $html);
        $this->assertStringContainsString('નમસ્તે', $html);
    }
}
