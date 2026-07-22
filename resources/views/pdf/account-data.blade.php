<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Account Data — Fursgo</title>
    <style>
        /*
         * Dompdf: @page left/right margins + width:100% tables overflow the right edge.
         * Do not wrap multipage content in an outer table — that creates blank pages.
         * Vertical @page margins apply on every page; equal side inset uses .page padding.
         */
        @page {
            margin-top: 64px;
            margin-right: 0;
            margin-bottom: 48px;
            margin-left: 0;
            background: #FDFCF8;
        }

        * {
            box-sizing: border-box;
        }

        img {
            border: 0;
            outline: none;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #FDFCF8;
            color: #3B3731;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        .page {
            padding: 0 48px;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
            table-layout: fixed;
        }

        .header td {
            vertical-align: middle;
            border: 0;
            padding: 0;
        }

        .logo {
            height: 42px;
            width: auto;
            max-width: 160px;
            display: block;
        }

        .header-meta {
            text-align: right;
        }

        .doc-kicker {
            margin: 0;
            color: #9D9B98;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 9pt;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .doc-date {
            margin: 4px 0 0;
            color: #3B3731;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 10pt;
        }

        .accent-bar {
            height: 4px;
            width: 100%;
            background: #FFC97A;
            border-radius: 2px;
            margin: 0 0 28px;
        }

        .hero-title {
            margin: 0 0 8px;
            color: #3B3731;
            font-family: "Playfair Display", DejaVu Serif, serif;
            font-size: 26pt;
            font-weight: 600;
            line-height: 1.2;
        }

        .hero-copy {
            margin: 0 0 8px;
            color: #3B3731;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 11pt;
        }

        .hero-sub {
            margin: 0 0 28px;
            color: #9D9B98;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 10pt;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            background: #F8F8F8;
            color: #3B3731;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 9pt;
            font-weight: bold;
        }

        .section {
            margin: 0 0 22px;
        }

        .section-title {
            margin: 18px 0 12px;
            color: #3B3731;
            font-family: "Playfair Display", DejaVu Serif, serif;
            font-size: 16pt;
            font-weight: 600;
            line-height: 1.2;
            page-break-after: avoid;
        }

        .section:first-of-type .section-title {
            margin-top: 0;
        }

        .section-rule {
            border: 0;
            border-top: 1px solid #D4D4D4;
            margin: 0 0 14px;
        }

        .card {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #D4D4D4;
            border-radius: 10px;
            background: #FFFFFF;
        }

        .card th,
        .card td {
            text-align: left;
            vertical-align: top;
            padding: 12px 16px;
            border-bottom: 1px solid #E8E4DE;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 10.5pt;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .card tr:last-child th,
        .card tr:last-child td {
            border-bottom: 0;
        }

        .card th {
            width: 38%;
            color: #3B3731;
            font-weight: bold;
            background: #F8F8F8;
        }

        .card td {
            color: #3B3731;
            font-weight: normal;
        }

        .status-enabled {
            color: #3B3731;
            font-weight: bold;
        }

        .status-disabled {
            color: #9D9B98;
        }

        .footer {
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px solid #D4D4D4;
            page-break-inside: avoid;
        }

        .footer-title {
            margin: 0 0 4px;
            color: #3B3731;
            font-family: "Playfair Display", DejaVu Serif, serif;
            font-size: 12pt;
            font-weight: 600;
        }

        .footer-copy {
            margin: 0;
            color: #9D9B98;
            font-family: Lato, DejaVu Sans, sans-serif;
            font-size: 9pt;
        }
    </style>
</head>

<body>
    <div class="page">
        <table class="header">
            <tr>
                <td style="width: 55%;">
                    @if ($logoDataUri !== '')
                        <img class="logo" src="{{ $logoDataUri }}" alt="Fursgo">
                    @else
                        <div class="hero-title" style="font-size: 20pt; margin: 0;">Fursgo</div>
                    @endif
                </td>
                <td class="header-meta">
                    <p class="doc-kicker">Account data export</p>
                    <p class="doc-date">{{ $exportedAt }}</p>
                </td>
            </tr>
        </table>

        <div class="accent-bar"></div>

        <h1 class="hero-title">Your account data</h1>
        <p class="hero-copy">Prepared for {{ $ownerName }}</p>
        <p class="hero-sub">
            <span class="badge">{{ $ownerTypeLabel }}</span>
            &nbsp; A copy of your profile details and saved account preferences.
        </p>

        <div class="section">
            <h2 class="section-title">Profile</h2>
            <hr class="section-rule">
            <table class="card">
                @foreach ($profileRows as $row)
                    <tr>
                        <th>{{ $row['label'] }}</th>
                        <td>{{ $row['value'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="section">
            <h2 class="section-title">General preferences</h2>
            <hr class="section-rule">
            <table class="card">
                @foreach ($generalSettings as $row)
                    <tr>
                        <th>{{ $row['label'] }}</th>
                        <td>{{ $row['value'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="section">
            <h2 class="section-title">Notifications</h2>
            <hr class="section-rule">
            <table class="card">
                @foreach ($notificationSettings as $row)
                    <tr>
                        <th>{{ $row['label'] }}</th>
                        <td
                            class="{{ $row['value'] === 'Enabled' ? 'status-enabled' : ($row['value'] === 'Disabled' ? 'status-disabled' : '') }}">
                            {{ $row['value'] }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="section">
            <h2 class="section-title">Privacy & security</h2>
            <hr class="section-rule">
            <table class="card">
                @foreach ($privacySettings as $row)
                    <tr>
                        <th>{{ $row['label'] }}</th>
                        <td
                            class="{{ $row['value'] === 'Enabled' ? 'status-enabled' : ($row['value'] === 'Disabled' ? 'status-disabled' : '') }}">
                            {{ $row['value'] }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="footer">
            <p class="footer-title">Fursgo</p>
            <p class="footer-copy">
                This document was generated from your Account Settings. It is intended for your personal records.
                If you did not request this export, please contact Fursgo support.
            </p>
        </div>
    </div>
</body>

</html>
