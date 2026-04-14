<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Legal Agreements — Fursgo</title>
    <style>
        @page {
            margin: 24px 32px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #3B3731;
            margin: 0;
        }

        .pdf-header {
            text-align: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #E5E5E5;
        }

        .pdf-header img {
            height: 40px;
            width: auto;
            display: inline-block;
        }

        .legal-agreements-section>h2 {
            color: #3B3731;
            font-size: 18pt;
            font-weight: 600;
            margin: 1.25rem 0 1rem;
        }

        .legal-agreements-section>h2:first-child {
            margin-top: 0;
        }

        .legal-agreements-section-title {
            margin: 1rem 0 0.5rem;
            color: #3B3731;
            font-size: 12pt;
            font-weight: 600;
            padding-bottom: 0.35rem;
            border-bottom: 1px solid #D4D4D4;
        }

        .legal-agreements-body p {
            margin: 0 0 0.65rem;
            color: #3B3731;
            font-size: 10pt;
        }
    </style>
</head>

<body>
    @if ($logoDataUri !== '')
        <div class="pdf-header">
            <img src="{{ $logoDataUri }}" alt="Fursgo">
        </div>
    @endif

    @include('partials.legal-agreements-document')
</body>

</html>
