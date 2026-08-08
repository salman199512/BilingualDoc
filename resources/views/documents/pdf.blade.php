<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $document->title }}</title>
    <style>
        /* Noto Sans Gujarati Font Variants for Dompdf */
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: normal;
            font-weight: bold;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: normal;
            font-weight: 500;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: normal;
            font-weight: 600;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: normal;
            font-weight: 700;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: normal;
            font-weight: 800;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: italic;
            font-weight: normal;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Gujarati';
            font-style: italic;
            font-weight: bold;
            src: url('{{ public_path("fonts/NotoSansGujarati.ttf") }}') format('truetype');
        }

        /* Lohit Gujarati Font Variants for Dompdf */
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: normal;
            font-weight: bold;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: normal;
            font-weight: 500;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: normal;
            font-weight: 600;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: normal;
            font-weight: 700;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: normal;
            font-weight: 800;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: italic;
            font-weight: normal;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Lohit Gujarati';
            font-style: italic;
            font-weight: bold;
            src: url('{{ public_path("fonts/LohitGujarati.ttf") }}') format('truetype');
        }
        
        @page {
            size: A4 portrait;
            margin-top: {{ $document->margin_top / 10 }}cm;
            margin-bottom: {{ $document->margin_bottom / 10 }}cm;
            margin-left: {{ $document->margin_left / 10 }}cm;
            margin-right: {{ $document->margin_right / 10 }}cm;
        }
        
        body {
            font-family: '{{ $document->font_english }}', 'Times New Roman', serif;
            font-size: 13pt;
            line-height: 1.6;
            color: #000000;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        
        /* Class-based styles for high-fidelity PDF rendering */
        .lang-gu,
        .lang-gu * {
            font-family: '{{ $document->font_gujarati }}', sans-serif !important;
        }
        .lang-en,
        .lang-en * {
            font-family: '{{ $document->font_english }}', 'Times New Roman', serif !important;
        }
        
        /* Map custom inline font styles as fallback overrides */
        span[style*="Noto Sans Gujarati"],
        p[style*="Noto Sans Gujarati"],
        div[style*="Noto Sans Gujarati"] {
            font-family: 'Noto Sans Gujarati', sans-serif !important;
        }
        span[style*="Lohit Gujarati"],
        p[style*="Lohit Gujarati"],
        div[style*="Lohit Gujarati"] {
            font-family: 'Lohit Gujarati', sans-serif !important;
        }
        span[style*="Noto Serif Gujarati"],
        p[style*="Noto Serif Gujarati"],
        div[style*="Noto Serif Gujarati"] {
            font-family: 'Noto Sans Gujarati', sans-serif !important;
        }
        span[style*="Times New Roman"],
        p[style*="Times New Roman"],
        div[style*="Times New Roman"] {
            font-family: 'Times New Roman', serif !important;
        }
        span[style*="Arial"],
        p[style*="Arial"],
        div[style*="Arial"] {
            font-family: 'Arial', sans-serif !important;
        }
        span[style*="Calibri"],
        p[style*="Calibri"],
        div[style*="Calibri"] {
            font-family: 'Calibri', sans-serif !important;
        }

        p {
            margin: 0 0 12px 0;
            font-size: 13pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        table.bordered td, table.bordered th {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 13pt;
        }

        table[border="0"] td, 
        table[border="0"] th, 
        table[style*="border: none"] td, 
        table[style*="border:none"] td, 
        table[style*="border: 0"] td {
            border: none !important;
            padding: 2px 0;
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        {!! $document->html_content !!}
    </div>
</body>
</html>
