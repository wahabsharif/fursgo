@props(['profile', 'variant' => null])

@php
$isSpace = $variant === 'space' || ($variant === null && ($profile['type'] ?? '') === 'space');
$roleProfile = $isSpace ? ($profile['space'] ?? []) : ($profile['groomer'] ?? []);
$payouts = $roleProfile['payouts'] ?? ($profile['payouts'] ?? []);
$tab = $payouts['payouts_tab'] ?? [];
$roleTab = $roleProfile['payouts']['payouts_tab'] ?? [];
$hasConfiguredDetails = array_key_exists('details', $isSpace ? $roleTab : $tab);
$hasConfiguredRows = array_key_exists('rows', $isSpace ? $roleTab : $tab);
$businessName = $roleProfile['name'] ?? ($profile['name'] ?? ($isSpace ? 'The Garden Grooming Spot' : 'Pawfect Grooming'));
$adminNotes = $profile['notes'] ?? [
    [
        'text' => 'Customer payment reviewed. No further action required at this time.',
        'author' => 'Michelle M',
        'time' => '18 Apr 2025',
        'tag' => 'Internal only',
    ],
];

$summary = $tab['summary'] ?? [
    ['title' => 'Pending payout', 'value' => '£157.00', 'note' => 'Awaiting processing'],
    ['title' => 'Total paid out', 'value' => '£591.00', 'note' => 'All time'],
    ['title' => 'Next payout', 'value' => '26 Mar', 'note' => 'Payout cycle: Weekly'],
];
$details = $tab['details'] ?? [
    ['label' => 'Account holder', 'value' => $businessName],
    ['label' => 'Bank', 'value' => 'Barclays'],
    ['label' => 'Account number', 'value' => '•••• 4821'],
    ['label' => 'Sort code', 'value' => '40 - 00 - 42'],
    ['label' => 'Payout frequency', 'value' => 'Weekly · every Tuesday'],
    ['label' => 'Next payout', 'value' => '26 Mar 2025 · £1,054 pending'],
    ['label' => 'Bank verified', 'value' => 'Verified', 'verified' => true],
];

if ($isSpace && ! $hasConfiguredDetails) {
    $details = [
        ['label' => 'Bank', 'value' => 'Barclays'],
        ['label' => 'Account number', 'value' => '**** **** **** 4821'],
        ['label' => 'Sort code', 'value' => '40 - 00 - 42'],
        ['label' => 'Account holder', 'value' => $businessName],
    ];
}

$buildInvoicePayload = function (array $row) use ($businessName, $adminNotes, $isSpace): array {
    $amount = $row['amount'] ?? '£55.00';
    $reference = $row['reference'] ?? 'BG-000001';
    $date = $row['date'] ?? '10/06/25';
    $isHold = ($row['status'] ?? '') === 'on_hold';
    $customerName = $row['customer_name'] ?? 'Jane Doe';
    $customerId = $row['customer_id'] ?? 'USR-01452';
    $method = $row['method'] ?? 'Visa **** 4525';
    $bookingId = $row['booking_id'] ?? $reference;
    $displayDate = $row['display_date'] ?? '10 Jun 2025';

    $detail = [
        'variant' => 'processed',
        'charge_label' => 'Charge · ' . $method,
        'summary_date' => $displayDate,
        'amount_label' => 'Amount charged',
        'hero_amount' => $amount,
        'amount_tone' => 'default',
        'amount_note' => ($isSpace ? 'Full-Day space hire' : 'Full groom') . ' · ' . $businessName,
        'status_side_label' => 'Payment status',
        'status_side_value' => 'Processed',
        'status_key' => 'processed',
        'status_side_note' => $displayDate . ' · 10:14 AM',
        'payout_label' => $isSpace ? 'Space Host payout' : 'Groomer payout',
        'payout_value' => $isHold ? 'On hold' : 'Paid out',
        'payout_key' => $isHold ? 'open_refund' : 'processed',
        'payout_note' => $isHold ? 'Awaiting weekly payout cycle' : '17 Jun 2025',
        'hero_sub' => $method . ' · ' . $displayDate . ' · 10:14 AM',
        'rows' => [
            ['label' => 'Invoice ID', 'value' => $reference],
            ['label' => 'Transaction type', 'value' => 'Charge'],
            ['label' => 'Linked booking', 'value' => $bookingId],
            ['label' => 'Description', 'value' => $isSpace ? 'Full-Day space hire' : 'Full groom (+ add-ons)'],
            ['label' => 'Business name', 'value' => $businessName . ($isSpace ? '' : ' SE13')],
            ['label' => 'Date & time', 'value' => $displayDate . ' · 10:14 AM'],
            ['label' => 'Payment method', 'value' => $method],
            ['label' => 'Settlement date', 'value' => $isHold ? 'Pending' : '17 Jun 2025'],
            ['label' => 'Processor reference', 'value' => 'pi_3OxK2mR2eZv0Y'],
        ],
        'price_lines' => $isSpace
            ? [
                ['label' => 'Full-Day', 'value' => $amount],
            ]
            : [
                ['label' => 'Full groom', 'value' => '£40.00'],
                ['label' => 'Ear cleaning', 'value' => '£10.00'],
                ['label' => 'De-shed treatment', 'value' => '£10.00'],
                ['label' => 'Promo code (NWYR26)', 'value' => '-£5.00', 'tone' => 'discount'],
            ],
        'total_label' => 'Total charged to customer',
        'total' => $amount,
        'aside_payout_label' => $isSpace ? 'Space Host pay-out' : 'Groomer pay-out',
        'aside_payout_value' => $isHold ? $amount . ' · on hold' : $amount . ' · paid 17 Jun',
        'callout_title' => $isSpace ? 'Space Host payout' : 'Groomer payout',
        'callout' => $isHold
            ? [
                'title' => 'Payout on hold',
                'body' => $amount . ' is held pending the next weekly Tuesday payout cycle for ' . $businessName . '.',
            ]
            : [
                'title' => 'Payout completed',
                'body' => $amount . ' was successfully paid to ' . $businessName . ' on 17 Jun 2025 via the weekly Tuesday payout cycle.',
            ],
        'callout_rows' => [
            ['label' => 'Payout amount', 'value' => $amount],
            ['label' => 'Paid to', 'value' => $businessName],
            ['label' => 'Payout date', 'value' => $isHold ? 'Pending' : '17 Jun 2025'],
            ['label' => 'Payout method', 'value' => 'Bank transfer'],
            ['label' => 'Payout reference', 'value' => 'po_7Kx9mR2eZv'],
        ],
        'notes' => $adminNotes,
        'timeline' => [
            [
                'tone' => $isHold ? 'orange' : 'green',
                'title' => $isHold
                    ? ($isSpace ? 'Space Host' : 'Groomer') . ' payout of ' . $amount . ' placed on hold'
                    : ($isSpace ? 'Space Host' : 'Groomer') . ' payout of ' . $amount . ' processed to ' . $businessName,
                'time' => ($isHold ? $displayDate : '17 Jun 2025') . ' · 09:00 · weekly Tuesday payout cycle',
            ],
            ['tone' => 'green', 'title' => 'Payment of ' . $amount . ' settled and cleared', 'time' => '12 Jun 2025 · 08:30'],
            ['tone' => 'green', 'title' => 'Payment of ' . $amount . ' processed successfully · ' . $method, 'time' => $displayDate . ' · 10:14'],
            ['tone' => 'blue', 'title' => 'Payment authorised by card issuer', 'time' => $displayDate . ' · 10:12'],
            ['tone' => 'gray', 'title' => 'Payment initiated by customer at checkout', 'time' => $displayDate . ' · 10:10'],
        ],
    ];

    return [
        'id' => $row['id'] ?? ('po-' . $reference . '-' . $date),
        'invoice_id' => $reference,
        'heading_prefix' => 'Invoice',
        'booking_id' => $bookingId,
        'customer_name' => $customerName,
        'customer_id' => $customerId,
        'date' => $displayDate,
        'amount' => $amount,
        'status' => 'processed',
        'status_label' => 'Paid',
        'method' => $method,
        'detail' => $detail,
    ];
};

$rawRows = $tab['rows'] ?? [
    ['id' => 'po-1', 'date' => '05/02/26', 'display_date' => '05 Feb 2026', 'amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
    ['id' => 'po-2', 'date' => '04/02/26', 'display_date' => '04 Feb 2026', 'amount' => '£125.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
    ['id' => 'po-3', 'date' => '03/02/26', 'display_date' => '03 Feb 2026', 'amount' => '£50.00', 'status' => 'on_hold', 'status_label' => 'On hold', 'reference' => 'BG-000001'],
    ['id' => 'po-4', 'date' => '02/02/26', 'display_date' => '02 Feb 2026', 'amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
    ['id' => 'po-5', 'date' => '01/02/26', 'display_date' => '01 Feb 2026', 'amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
    ['id' => 'po-6', 'date' => '31/01/26', 'display_date' => '31 Jan 2026', 'amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
    ['id' => 'po-7', 'date' => '30/01/26', 'display_date' => '30 Jan 2026', 'amount' => '£75.00', 'status' => 'on_hold', 'status_label' => 'On hold', 'reference' => 'BG-000001'],
    ['id' => 'po-8', 'date' => '29/01/26', 'display_date' => '29 Jan 2026', 'amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
    ['id' => 'po-9', 'date' => '28/01/26', 'display_date' => '28 Jan 2026', 'amount' => '£90.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
    ['id' => 'po-10', 'date' => '27/01/26', 'display_date' => '27 Jan 2026', 'amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed', 'reference' => 'BG-000001'],
];

if ($isSpace && ! $hasConfiguredRows) {
    $spaceRowStates = [
        ['amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed'],
        ['amount' => '£125.00', 'status' => 'on_hold', 'status_label' => 'On hold'],
        ['amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed'],
        ['amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed'],
        ['amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed'],
        ['amount' => '£50.00', 'status' => 'completed', 'status_label' => 'Completed'],
    ];

    $rawRows = collect($rawRows)->values()->map(function (array $row, int $index) use ($spaceRowStates): array {
        $state = $spaceRowStates[$index % count($spaceRowStates)];

        return array_merge($row, $state, [
            'date' => '05/02/26',
            'display_date' => '05 Feb 2026',
        ]);
    })->all();
}

$rows = collect($rawRows)->map(function ($row) use ($buildInvoicePayload) {
    return array_merge($row, ['invoice_payload' => $buildInvoicePayload($row)]);
});
$totalCount = $tab['total'] ?? 14;
@endphp

<div class="admin-bp-po">
    <div class="admin-bp-po-metrics">
        @foreach ($summary as $metric)
        <div class="admin-bp-payouts-metric-card">
            <p class="admin-bp-payouts-metric-title">{{ $metric['title'] }}</p>
            <p class="admin-bp-payouts-metric-value">{{ $metric['value'] }}</p>
            <p class="admin-bp-payouts-metric-note">{{ $metric['note'] }}</p>
        </div>
        @endforeach
    </div>

    <section class="admin-card admin-co-panel admin-bp-po-details-panel">
        <div class="admin-co-section-head">
            <h3 class="admin-co-section-title">Payout Details</h3>
            <div class="admin-co-pay-head-actions">
                @if ($isSpace)
                <span class="admin-bp-po-verified">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                        <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                    </svg>
                    Verified
                </span>
                @endif
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                        <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Edit
                </button>
            </div>
        </div>

        <dl class="admin-co-details admin-bp-po-details">
            @foreach ($details as $row)
            <div class="admin-co-details-row">
                <dt>{{ $row['label'] }}</dt>
                <dd class="{{ ! empty($row['verified']) ? 'is-verified' : '' }}">
                    @if (! empty($row['verified']))
                    <span class="admin-bp-po-verified">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                        </svg>
                        {{ $row['value'] }}
                    </span>
                    @else
                    {{ $row['value'] }}
                    @endif
                </dd>
            </div>
            @endforeach
        </dl>
    </section>

    <section class="admin-bp-po-table-card">
        <div class="admin-co-pay-table-wrap">
            <table class="admin-po-table admin-bp-po-table">
                <thead>
                    <tr>
                        @foreach (['Date', 'Amount', 'Status', 'Reference'] as $th)
                        <th>
                            <span class="admin-co-ref-th">
                                {{ $th }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                    <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </th>
                        @endforeach
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['amount'] }}</td>
                        <td>
                            <span class="admin-bp-po-status is-{{ str_replace('_', '-', $row['status']) }}">{{ $row['status_label'] }}</span>
                        </td>
                        <td>{{ $row['reference'] }}</td>
                        <td>
                            <button
                                type="button"
                                class="admin-bp-po-invoice-btn"
                                aria-label="View invoice {{ $row['reference'] }}"
                                @click="openPayment(@js($row['invoice_payload']))">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#9C9A97" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="admin-po-table-footer admin-co-pay-footer admin-bp-po-footer">
        <p class="admin-table-count">SHOWING 1–{{ min(10, $rows->count()) }} OF {{ $totalCount }} PAYOUTS</p>
        <nav class="admin-po-pagination" aria-label="Payouts pagination">
            <button type="button" class="admin-po-page-arrow" aria-label="Previous page" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                    <circle cx="16" cy="16" r="16" transform="matrix(-1 0 0 1 32 0)" fill="#F3F3F3" />
                    <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button type="button" class="admin-po-page is-current">1</button>
            <button type="button" class="admin-po-page">2</button>
            <button type="button" class="admin-po-page">3</button>
            <button type="button" class="admin-po-page">4</button>
            <button type="button" class="admin-po-page">5</button>
            <span class="admin-po-page-ellipsis" aria-hidden="true">…</span>
            <button type="button" class="admin-po-page">100</button>
            <button type="button" class="admin-po-page-arrow" aria-label="Next page">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                    <g filter="url(#filter0_d_provider_po_page_next)">
                        <circle cx="20" cy="16" r="16" fill="white" />
                    </g>
                    <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_provider_po_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_provider_po_page_next" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_provider_po_page_next" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
        </nav>
    </div>
</div>
