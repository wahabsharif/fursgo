<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNo }} — Fursgo</title>
    <style>
        {!! $invoiceEmbeddedFontFacesCss !!}

        /*
         * Full-bleed cream background: @page margin must be 0 or Dompdf shows white
         * "paper" around the body. Content inset uses padding on .page instead.
         * Do not set @page size — Dompdf uses setPaper(); a fixed size here overrides
         * dynamic height from BookingInvoicePdfController.
         */
        @page {
            margin: 0;
            background: #FDFCF8;
        }

        * {
            box-sizing: border-box;
        }

        /* Dompdf / legacy UA default border on <img> (esp. data-URI SVG) */
        img {
            border: 0;
            outline: none;
        }

        html {
            background: #FDFCF8;
            height: auto;
        }

        body {
            font-family: Lato;
            font-size: 9.5pt;
            line-height: 1.45;
            color: #3b3731;
            margin: 0;
            padding: 0;
            background: #FDFCF8;
            height: auto;
        }

        /*
         * Dompdf: avoid min-height:100% / full-page min-height on the root — extra blank pages.
         * Bottom-left corner uses position:fixed so it sticks to the sheet (not end of content).
         */
        .page {
            position: relative;
            height: auto;
            min-height: 0;
            /* Even inset from the sheet on all sides */
            padding: 5rem 4rem;
            /* Corner art uses negative offsets; must not clip bottom-left */
            overflow: visible;
            background: #FDFCF8;
        }

        /* Shrink-wrap helper for Dompdf: must not stretch to the measurement canvas height */
        .page-content {
            min-height: 0;
        }

        /*
         * Stacking (back → front): #FDFCF8 on @page/html/body/.page → corner PNGs → invoice content.
         */
        .invoice-corner-tr {
            position: absolute;
            top: -68px;
            right: -80px;
            width: 440px;
            height: 455px;
            z-index: 1;
            pointer-events: none;
        }

        .invoice-corner-tr__img {
            display: block;
            width: 440px;
            height: 455px;
            opacity: 1;
        }

        .invoice-corner-bl {
            position: fixed;
            left: -82px;
            bottom: -88px;
            width: 452px;
            height: 438px;
            z-index: 1;
            pointer-events: none;
        }

        .invoice-corner-bl__img {
            display: block;
            width: 452px;
            height: 438px;
            opacity: 1;
        }

        .z1 {
            position: relative;
            z-index: 2;
            overflow: visible;
        }

        .serif-italic {
            font-family: "Playfair Display";
            font-style: italic;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .header-table td {
            vertical-align: top;
            background: #FDFCF8;
        }

        .header-table td.inv-meta-wrap {
            background: transparent;
        }

        .header-table td.header-brand-cell {
            background: transparent;
            text-align: left;
            width: 58%;
        }

        .invoice-logo {
            width: 195px;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 0 8px 0;
            background: transparent;
        }

        .brand-title {
            font-family: Lato;
            font-size: 26pt;
            font-weight: 700;
            color: #f5b086;
            letter-spacing: 0.02em;
            margin: 0 0 4px;
        }

        .invoice-word {
            font-family: "Playfair Display";
            font-size: 32px;
            font-style: italic;
            font-weight: 400;
            line-height: 20px;
            color: #3B3731;
            margin: 0;
        }

        .inv-meta-wrap {
            text-align: right;
            background: transparent;
        }

        .inv-meta-inner {
            display: inline-block;
            text-align: right;
            padding-right: 12px;
            border-right: 3px solid #f5b086;
        }


        .inv-meta-value {
            color: #3B3731;
            text-align: right;
            font-family: Lato;
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 157.5%;
            /* 15.75px */
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5rem;
        }

        .content-table td {
            vertical-align: top;
            background: transparent;
        }

        .content-left {
            width: 36%;
            /* Align left bill blocks with items tbody (below the header row) */
            padding: 45px 42px 0 0;
        }

        .content-right {
            width: 64%;
        }

        .bill-block {
            border-left: 3px solid #F5B086;
            padding-left: 20px;
            margin-bottom: 78px;
        }

        .bill-block:last-child {
            margin-bottom: 0;
        }

        .section-label {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 14px;
            font-style: italic;
            font-weight: 400;
            line-height: normal;
            margin: 0;
        }

        .block-text {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 8px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .bill-to-address {
            white-space: pre-line;
            color: #3B3731;
            font-family: Lato;
            font-size: 8px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .muted {
            color: #3B3731;
            font-size: 10px;
        }

        .verified-row {
            margin-top: 10px;
            color: #3B3731;
            font-family: Lato;
            font-size: 7px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
        }

        .verified-icon {
            display: inline-block;
            width: 6px;
            height: 6px;
            margin-right: 4px;
            vertical-align: -1px;
        }

        .items-wrap {
            margin-bottom: 4px;
        }

        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        tr.items-head th {
            background: #F5B086;
            color: #fff;
            font-family: Lato;
            font-size: 10px;
            font-weight: 700;
            line-height: 12px;
            padding: 13px 14px;
            text-align: left;
            text-transform: uppercase;
        }

        tr.items-head th:first-child {
            border-radius: 10px 0 0 3px;
        }

        tr.items-head th:last-child {
            border-radius: 0 10px 3px 0;
        }

        .items-table tbody tr td {
            padding: 16px 14px;
            vertical-align: top;
        }

        .items-table tbody tr td:first-child {
            border-radius: 4px 0 0 4px;
        }

        .items-table tbody tr td:last-child {
            border-radius: 0 4px 4px 0;
        }

        .row-alt td {
            background: #FFF1D8;
        }

        .row-plain td {
            background: #FDFCF8;
        }

        .row-plain td {
            border-top: 1px solid #F5B086;
            border-bottom: 1px solid #F5B086;
        }

        .row-plain td:first-child {
            border-left: 1px solid #F5B086;
        }

        .row-plain td:last-child {
            border-right: 1px solid #F5B086;
        }

        .col-num {
            width: 8%;
            text-align: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 10px;
            line-height: 14px;
        }

        .desc-main {
            color: #3B3731;
            font-family: Lato;
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .desc-sub {
            color: #3B3731;
            font-family: Lato;
            font-size: 8px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .col-pet {
            width: 24%;
            color: #3B3731;
            font-family: Lato;
            font-size: 10px;
            font-weight: 400;
            line-height: 14px;
        }

        .col-price {
            width: 20%;
            text-align: right;
            color: #3B3731;
            font-family: Lato;
            font-size: 10px;
            font-weight: 400;
            line-height: 14px;
            white-space: nowrap;
        }

        .totals {
            width: 100%;
            margin-top: 6px;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px 0;
            background: #FDFCF8;
        }

        .totals .label {
            color: #3B3731;
            font-family: Lato;
            font-size: 10px;
            font-weight: 400;
            line-height: 14px;
            text-align: left;
            text-transform: uppercase;
        }

        .totals .amt {
            text-align: right;
            width: 96px;
            color: #3B3731;
            font-family: Lato;
            font-size: 10px;
            font-weight: 400;
            line-height: 14px;
        }

        .totals .grand td {
            padding-top: 8px;
            font-size: 20px;
            line-height: 24px;
            text-transform: none;
        }

        .totals .grand .label {
            font-size: 20px;
            line-height: 24px;
            text-transform: none;
        }

        .totals .grand .amt {
            font-size: 20px;
            line-height: 24px;
        }

        .footer-section .section-label {
            font-family: "Playfair Display";
            font-size: 11pt;
            line-height: 1.2;
            color: #4a4540;
            margin: 0 0 6px;
        }

        .footer-section {
            width: 100%;
            margin-top: 28px;
            padding-top: 8px;
            padding-bottom: 2rem;
            border-bottom: 2px solid #FFD177;
            box-sizing: border-box;
        }

        /*
         * Real <table> row: Dompdf does not lay out flex rows — Terms/Note were stacking.
         * Peach rule = border-right on Terms cell (full row height, fixed 2px).
         */
        .footer-main {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .footer-main__terms {
            width: 50%;
            vertical-align: top;
            padding-right: 3rem;
            border-right: 2px solid #FBC0A0;
            box-sizing: border-box;
        }

        .footer-main__note {
            width: 50%;
            vertical-align: top;
            padding-left: 3rem;
            box-sizing: border-box;
        }

        .footer-note-accent {
            padding-left: 0;
        }

        .footer-brand {
            width: 100%;
            margin-top: 4rem;
            font-size: 8pt;
            color: #6f6a64;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .footer-brand__tagline {
            width: 40%;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 8px;
            font-style: italic;
            font-weight: 400;
            line-height: normal;
        }

        .footer-brand__tagline-line2 {
            display: block;
            color: #3B3731;
            font-family: Lato;
            font-size: 8px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .footer-brand__icons {
            text-align: center;
            width: 30%;
            padding: 0 10px;
            box-sizing: border-box;
        }

        .footer-brand__icons-inner {
            display: inline-block;
            line-height: 1;
            white-space: nowrap;
        }

        .footer-brand__icon-img {
            display: inline-block;
            vertical-align: middle;
            margin: 0 6px;
            border: none;
        }

        .footer-brand__wm {
            vertical-align: bottom;
            text-align: right;
            line-height: 1;
            width: 30%;
            white-space: nowrap;
        }

        /* Footer mark: keep visible in Dompdf (0.2–0.25 was nearly invisible on cream) */
        .wm__img {
            display: inline-block;
            vertical-align: bottom;
            border: none;
            box-shadow: none;
        }

        .wm--text {
            font-size: 42pt;
            font-weight: 700;
            color: rgba(245, 176, 134, 0.2);
        }

        .status-pill {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            background: #ebebeb;
            color: #4a4540;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="invoice-corner-tr" aria-hidden="true">
            <img src="{{ $invoiceCornerTopRightDataUri }}" alt="" class="invoice-corner-tr__img" width="440"
                height="455">
        </div>
        <div class="page-content z1">
            <table class="header-table">
                <tbody>
                    <tr>
                        <td class="header-brand-cell">
                            @if ($logoDataUri !== '')
                                <img src="{{ $logoDataUri }}" alt="Fursgo" class="invoice-logo">
                            @else
                                <p class="brand-title" style="margin-bottom:2px;">fursgo</p>
                            @endif
                            <p class="invoice-word serif-italic">Invoice</p>
                            @if ($bookingStatus === 'cancelled')
                                <span class="status-pill">Cancelled booking</span>
                            @endif
                        </td>
                        <td class="inv-meta-wrap">
                            <div class="inv-meta-inner">
                                <div class="inv-meta-value">No. {{ $invoiceNo }}</div>
                                <div class="inv-meta-value">{{ $invoiceDate }}</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="content-table">
                <tbody>
                    <tr>
                        <td class="content-left">
                            <div class="bill-block">
                                <p class="section-label">Bill To</p>
                                <p class="block-text">{{ $billToName }}</p>
                                @if ($billToAddress !== '')
                                    <p class="block-text bill-to-address">{{ $billToAddress }}</p>
                                @endif
                                <p class="block-text">{{ $billToEmail }}</p>
                            </div>

                            <div class="bill-block">
                                <p class="section-label">Bill From</p>
                                <p class="block-text">{{ $issuerName }}</p>
                                @if ($issuerPhone !== '')
                                    <p class="block-text">{{ $issuerPhone }}</p>
                                @endif
                                @if ($issuerEmail !== '')
                                    <p class="block-text muted">{{ $issuerEmail }}</p>
                                @endif
                                <p class="verified-row">
                                    <img src="{{ $verifiedBadgeDataUri }}" alt=""
                                        class="verified-icon">Verified by FursGo
                                </p>
                            </div>
                        </td>

                        <td class="content-right">
                            <div class="items-wrap">
                                <table class="items-table" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr class="items-head">
                                            <th class="col-num" scope="col"></th>
                                            <th scope="col" style="padding: 16px 0;">DESCRIPTION</th>
                                            <th class="col-pet" scope="col">PET NAME</th>
                                            <th class="col-price" scope="col">PRICE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lines as $idx => $line)
                                            <tr class="{{ $idx % 2 === 0 ? 'row-alt' : 'row-plain' }}">
                                                <td class="col-num">{{ $idx + 1 }}</td>
                                                <td style="padding: 16px 0;">
                                                    <div class="desc-main">{{ $line['description'] }}</div>
                                                    @if (($line['subtitle'] ?? '') !== '')
                                                        <div class="desc-sub">{{ $line['subtitle'] }}</div>
                                                    @endif
                                                </td>
                                                <td class="col-pet">{{ $line['pet_name'] }}</td>
                                                <td class="col-price">£{{ number_format((float) $line['amount'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <table class="totals" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td class="label">SUBTOTAL</td>
                                        <td class="amt">£{{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">PLATFORM FEE
                                            ({{ rtrim(rtrim(number_format($platformPercent, 2), '0'), '.') }}%)</td>
                                        <td class="amt">£{{ number_format($platformFee, 2) }}</td>
                                    </tr>
                                    <tr class="grand">
                                        <td class="label">Total Due</td>
                                        <td class="amt">£{{ number_format($totalDue, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="footer-section">
                <table class="footer-main" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td class="footer-main__terms">
                                <p class="section-label serif-italic" style="margin-top:0;">Terms &amp; Conditions</p>
                                <p class="block-text muted" style="font-size:8.5pt;">
                                    Lorem ipsum dolor sit amet, excepturi quasi quaerat sapiente. Dolorem cupiditate
                                    rerum
                                    perspiciatis qui quism cupiditate rerum.
                                </p>
                            </td>
                            <td class="footer-main__note">
                                <div class="footer-note-accent">
                                    <p class="section-label serif-italic" style="margin-top:0;">Note</p>
                                    <p class="block-text" style="font-size:8.5pt;">
                                        Thank you for choosing {{ $issuerName }}! If you enjoyed your experience,
                                        please
                                        leave a
                                        review
                                        on Fursgo — it really helps small businesses grow.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <table class="footer-brand" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td class="footer-brand__tagline">
                            Connecting pets &amp; Groomers.
                            <span class="footer-brand__tagline-line2">www.fursgo.co.uk &nbsp;|&nbsp;
                                hello@@fursgo.com &nbsp;|&nbsp; @@fursgo</span>
                        </td>
                        <td class="footer-brand__icons">
                            <div class="footer-brand__icons-inner" aria-hidden="true">
                                @foreach ($footerBrandSocialIcons as $icon)
                                    @if (($icon['uri'] ?? '') !== '')
                                        <img src="{{ $icon['uri'] }}" alt="" class="footer-brand__icon-img"
                                            width="{{ $icon['w'] }}" height="{{ $icon['h'] }}">
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="footer-brand__wm">
                            @if ($footerWatermarkIconDataUri !== '')
                                <img src="{{ $footerWatermarkIconDataUri }}" alt="" class="wm__img"
                                    width="91" height="92">
                            @else
                                <span class="wm--text">fg</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="invoice-corner-bl" aria-hidden="true">
            <img src="{{ $invoiceCornerBottomLeftDataUri }}" alt="" class="invoice-corner-bl__img"
                width="452" height="438">
        </div>
    </div>
</body>

</html>
