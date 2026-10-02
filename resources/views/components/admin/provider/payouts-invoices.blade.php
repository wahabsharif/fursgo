@props(['profile', 'variant' => null])

@php
$isSpace = $variant === 'space' || ($variant === null && ($profile['type'] ?? '') === 'space');
$roleProfile = $isSpace ? ($profile['space'] ?? []) : ($profile['groomer'] ?? []);
$payouts = $roleProfile['payouts'] ?? ($profile['payouts'] ?? []);
$tab = $payouts['invoices'] ?? [];
$roleTab = $roleProfile['payouts']['invoices'] ?? [];
$hasConfiguredRows = array_key_exists('rows', $isSpace ? $roleTab : $tab);
$businessName = $roleProfile['name'] ?? ($profile['name'] ?? ($isSpace ? 'The Garden Grooming Spot' : 'Pawfect Grooming'));
$adminNotes = [
    [
        'text' => 'Atque suscipit ut et. Modi modi dolorem repudiandae eum ut. Nesciunt mollitia dicta necessitatibus praesentium libero maxime tempora reprehenderit. Et neque. Commodi voluptas ex omnis architecto non. Ad impedit aut eius....',
        'author' => 'Michelle M',
        'time' => '18 Apr 2025',
        'tag' => 'Internal only',
    ],
];

$filters = $tab['filters'] ?? [
    'all' => 15,
    'paid' => 12,
    'disputed' => 1,
    'refunded' => 2,
];

$buildInvoicePayload = function (array $row) use ($businessName, $adminNotes, $isSpace): array {
    $amount = $row['total'] ?? $row['gross'] ?? '£55.00';
    $gross = $row['gross'] ?? $amount;
    $reference = $row['invoice_id'] ?? 'BG-000001';
    $invoiceDetailReference = $reference;
    $status = $row['status'] ?? 'paid';
    $customerName = $row['customer'] ?? 'Jane Doe';
    $customerId = $row['customer_id'] ?? 'USR-01452';
    $method = $row['method'] ?? 'Visa **** 4525';
    $settlementMethod = 'Visa **** 4562';
    $bookingId = $row['booking_id'] ?? 'BK-000001';
    $displayDate = '10 Jun 2025';
    $isDisputed = $status === 'disputed';
    $isRefunded = $status === 'refunded';

    if ($isSpace) {
        $amount = $isRefunded ? '- £155.00' : '£155.00';
        $gross = '£155.00';
        $reference = 'BSP-000001';
        $displayDate = '10 Jun 2025';
    }

    $displayBusinessName = $isSpace ? $businessName : 'Pawfect Salon';
    $activityAmount = '£55.00';

    $detailVariant = $isRefunded ? 'refunded' : 'processed';
    $statusLabel = $row['status_label'] ?? ucfirst($status);

    $detail = [
        'variant' => $detailVariant,
        'charge_label' => 'Charge · ' . $method,
        'summary_date' => $displayDate,
        'amount_label' => $isRefunded ? 'Amount refunded' : 'Amount charged',
        'hero_amount' => $amount,
        'amount_tone' => $isRefunded ? 'refund' : 'default',
        'amount_note' => ($isSpace ? 'Full-Day' : 'Full groom') . ' · ' . $displayBusinessName,
        'status_side_label' => 'Payment status',
        'status_side_value' => $isDisputed ? 'Disputed' : ($isRefunded ? 'Refunded' : 'Processed'),
        'status_key' => $isDisputed ? 'open_refund' : ($isRefunded ? 'refunded' : 'processed'),
        'status_side_note' => $displayDate . ' · 10:14 AM',
        'payout_label' => $isSpace ? 'Space Host payout' : 'Groomer payout',
        'payout_value' => $isDisputed ? 'On hold' : ($isRefunded ? 'Reversed' : 'Paid out'),
        'payout_key' => $isDisputed || $isRefunded ? 'open_refund' : 'processed',
        'payout_note' => $isDisputed ? 'Held pending dispute' : ($isRefunded ? 'Refund settled' : 'Processed 17 Jun 2025'),
        'hero_sub' => $settlementMethod . ' · ' . $displayDate . ' · 10:14 AM',
        'rows' => [
            ['label' => 'Invoice ID', 'value' => $invoiceDetailReference],
            ['label' => 'Transaction type', 'value' => $isRefunded ? 'Refund' : 'Charge'],
            ['label' => 'Linked booking', 'value' => $bookingId],
            ['label' => 'Description', 'value' => $isSpace ? 'Full-Day (+ add-ons)' : 'Full groom (+ add-ons)'],
            ['label' => 'Business name', 'value' => $displayBusinessName . ' SE13'],
            ['label' => 'Date & time', 'value' => $displayDate . ' · 10:14 AM'],
            ['label' => 'Payment method', 'value' => $settlementMethod],
            ['label' => $isSpace ? 'Settled to Space Host' : 'Settled to groomer', 'value' => $isDisputed ? 'Pending' : '17 Jun 2025'],
            ['label' => 'Processor reference', 'value' => 'pi_3P8xJK2eZvKYlo4A1'],
        ],
        'price_lines' => $isSpace
            ? [
                ['label' => 'Full-Day (service)', 'value' => '£140.00'],
                ['label' => 'Deep Clean (add-on)', 'value' => '£10.00'],
                ['label' => 'Locker Storage (add-on)', 'value' => '£10.00'],
                ['label' => 'Promo code (NWYR26)', 'value' => '-£5.00', 'tone' => 'discount'],
            ]
            : [
                ['label' => 'Full groom (service)', 'value' => '£40.00'],
                ['label' => 'Ear cleaning (add-on)', 'value' => '£10.00'],
                ['label' => 'De-shed treatment (add-on)', 'value' => '£10.00'],
                ['label' => 'Promo code (NWYR26)', 'value' => '-£5.00', 'tone' => 'discount'],
            ],
        'total_label' => $isRefunded ? 'Total refunded to customer' : 'Total charged to customer',
        'total' => $isRefunded ? ltrim($amount, '- ') : $gross,
        'aside_payout_label' => $isSpace ? 'Space Host pay-out' : 'Groomer pay-out',
        'aside_payout_value' => $isDisputed
            ? $gross . ' · on hold'
            : ($isRefunded ? $gross . ' · reversed' : $gross . ' · paid 17 Jun'),
        'callout_title' => $isSpace ? 'Space Host payout' : 'Groomer payout',
        'callout' => $isDisputed
            ? [
                'title' => 'Payout on hold',
                'body' => $gross . ' is held pending dispute resolution for ' . $displayBusinessName . '.',
            ]
            : ($isRefunded
                ? [
                    'title' => 'Refund completed',
                    'body' => $amount . ' was refunded to the customer. ' . ($isSpace ? 'Space Host' : 'Groomer') . ' payout was reversed.',
                ]
                : [
                    'title' => 'Payout completed',
                    'body' => $activityAmount . ' was successfully paid to Pawfect Salon on 17 Jun 2025 via the weekly Tuesday payout cycle.',
                ]),
        'callout_rows' => [
            ['label' => 'Payout amount', 'value' => $gross],
            ['label' => 'Paid to', 'value' => $displayBusinessName],
            ['label' => 'Payout date', 'value' => $isDisputed ? 'Pending' : '17 Jun 2025'],
            ['label' => 'Payout method', 'value' => 'Bank transfer'],
            ['label' => 'Payout reference', 'value' => 'PO-2025-0563-B12'],
        ],
        'notes' => $adminNotes,
        'timeline' => [
            [
                'tone' => $isDisputed ? 'orange' : ($isRefunded ? 'orange' : 'green'),
                'title' => $isDisputed
                    ? ($isSpace ? 'Space Host' : 'Groomer') . ' payout of ' . $activityAmount . ' placed on hold'
                    : ($isRefunded
                        ? 'Refund of ' . ltrim($amount, '- ') . ' issued to customer'
                        : ($isSpace ? 'Space Host' : 'Groomer') . ' payout of ' . $activityAmount . ' processed to ' . $displayBusinessName),
                'time' => '17 Jun 2025 · 09:00 · weekly Tuesday payout cycle',
            ],
            ['tone' => 'green', 'title' => 'Payment of ' . $activityAmount . ' settled and cleared', 'time' => '12 Jun 2025 · 08:30'],
            ['tone' => 'green', 'title' => 'Payment of ' . $activityAmount . ' processed successfully · Visa **** 4563', 'time' => '10 Jun 2025 · 21:50'],
            ['tone' => 'blue', 'title' => 'Payment authorised by card issuer', 'time' => '10 Jun 2024 · 21:48'],
            ['tone' => 'gray', 'title' => 'Payment initiated by customer at checkout', 'time' => '10 Jun 2025 · 21:45'],
        ],
    ];

    return [
        'id' => $row['id'] ?? ('inv-' . $reference),
        'invoice_id' => $reference,
        'heading_prefix' => 'Invoice',
        'booking_id' => $bookingId,
        'customer_name' => $customerName,
        'customer_id' => $customerId,
        'date' => $displayDate,
        'amount' => $amount,
        'status' => $isRefunded ? 'refunded' : ($isDisputed ? 'open_refund' : 'processed'),
        'status_label' => $isRefunded ? 'Refunded' : ($isDisputed ? 'Disputed' : 'Paid'),
        'method' => $method,
        'detail' => $detail,
    ];
};

$rawRows = collect($tab['rows'] ?? [
    ['id' => 'inv-1', 'date' => '07 Apr 2025', 'month' => 'April', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£55.00', 'total' => '£55.00', 'status' => 'paid', 'status_label' => 'Paid'],
    ['id' => 'inv-2', 'date' => '07 Apr 2025', 'month' => 'April', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£55.00', 'total' => '£55.00', 'status' => 'paid', 'status_label' => 'Paid'],
    ['id' => 'inv-3', 'date' => '07 Apr 2025', 'month' => 'April', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£55.00', 'total' => '£55.00', 'status' => 'disputed', 'status_label' => 'Disputed'],
    ['id' => 'inv-4', 'date' => '07 Apr 2025', 'month' => 'April', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£55.00', 'total' => '£55.00', 'status' => 'paid', 'status_label' => 'Paid'],
    ['id' => 'inv-5', 'date' => '07 Mar 2025', 'month' => 'March', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£20.00', 'total' => '- £20.00', 'status' => 'refunded', 'status_label' => 'Refunded'],
    ['id' => 'inv-6', 'date' => '07 Mar 2025', 'month' => 'March', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£55.00', 'total' => '£55.00', 'status' => 'paid', 'status_label' => 'Paid'],
    ['id' => 'inv-7', 'date' => '07 Mar 2025', 'month' => 'March', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£55.00', 'total' => '£55.00', 'status' => 'paid', 'status_label' => 'Paid'],
    ['id' => 'inv-8', 'date' => '07 Feb 2025', 'month' => 'February', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£55.00', 'total' => '£55.00', 'status' => 'paid', 'status_label' => 'Paid'],
    ['id' => 'inv-9', 'date' => '07 Feb 2025', 'month' => 'February', 'invoice_id' => 'BG-000001', 'customer' => 'Jane Doe', 'booking_id' => 'BK-000001', 'gross' => '£20.00', 'total' => '- £20.00', 'status' => 'refunded', 'status_label' => 'Refunded'],
]);

if ($isSpace && ! $hasConfiguredRows) {
    $spaceInvoiceStates = [
        ['gross' => '£55.00', 'total' => '£55.00', 'status' => 'paid', 'status_label' => 'Paid'],
        ['gross' => '£20.00', 'total' => '£20.00', 'status' => 'disputed', 'status_label' => 'Disputed'],
        ['gross' => '£20.00', 'total' => '- £20.00', 'status' => 'refunded', 'status_label' => 'Refunded'],
        ['gross' => '£20.00', 'total' => '£20.00', 'status' => 'paid', 'status_label' => 'Paid'],
    ];

    $rawRows = $rawRows->values()->map(function (array $row, int $index) use ($spaceInvoiceStates): array {
        $state = $spaceInvoiceStates[$index] ?? $spaceInvoiceStates[3];

        return array_merge($row, $state, [
            'date' => '07 Apr 2025',
        ]);
    });
}

$rows = $rawRows->map(function ($row) use ($buildInvoicePayload) {
    return array_merge($row, ['payload' => $buildInvoicePayload($row)]);
});
$groupedRows = $rows->groupBy(fn ($r) => $r['month'] ?? 'Other');
$totalCount = $filters['all'] ?? $rows->count();
@endphp

<div
    class="admin-bp-inv"
    x-data="{
        invFilter: 'all',
        invSearch: '',
        selectedIds: [],
        allIds: @js($rows->pluck('id')->values()),
        matchesInv(status, invoiceId, customer, bookingId) {
            const statusOk = this.invFilter === 'all' || this.invFilter === status;
            if (!statusOk) return false;
            const q = (this.invSearch || '').trim().toLowerCase();
            if (!q) return true;
            return [invoiceId, customer, bookingId, status].some((v) => String(v || '').toLowerCase().includes(q));
        },
        isSelected(id) {
            return this.selectedIds.includes(id);
        },
        toggleOne(id) {
            this.selectedIds = this.isSelected(id)
                ? this.selectedIds.filter((x) => x !== id)
                : [...this.selectedIds, id];
        },
        get allSelected() {
            return this.allIds.length > 0 && this.selectedIds.length === this.allIds.length;
        },
        toggleAll() {
            this.selectedIds = this.allSelected ? [] : [...this.allIds];
        },
        monthVisible(statuses, haystacks) {
            if (this.invFilter !== 'all' && !statuses.includes(this.invFilter)) return false;
            const q = (this.invSearch || '').trim().toLowerCase();
            if (!q) return true;
            return haystacks.some((text) => (text || '').toLowerCase().includes(q));
        },
        openInvoice(row) {
            this.$dispatch('admin-bp-open-payment', row);
        },
    }">
    <div class="admin-po-toolbar admin-co-pay-toolbar admin-bp-inv-toolbar">
        <div class="admin-po-filters" role="tablist" aria-label="Invoice status filters">
            <button type="button" class="admin-po-filter is-all" :class="{ 'is-active': invFilter === 'all' }" @click="invFilter = 'all'">
                All ({{ $filters['all'] }})
            </button>
            <button type="button" class="admin-po-filter is-paid" :class="{ 'is-active': invFilter === 'paid' }" @click="invFilter = 'paid'">
                Paid ({{ $filters['paid'] }})
            </button>
            <button type="button" class="admin-po-filter is-disputed" :class="{ 'is-active': invFilter === 'disputed' }" @click="invFilter = 'disputed'">
                Disputed ({{ $filters['disputed'] }})
            </button>
            <button type="button" class="admin-po-filter is-inv-refunded" :class="{ 'is-active': invFilter === 'refunded' }" @click="invFilter = 'refunded'">
                Refunded ({{ $filters['refunded'] }})
            </button>
        </div>

        <div class="admin-po-tools">
            <label class="admin-po-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                    <circle cx="6.2" cy="6.2" r="4.7" stroke="#3B3731" stroke-width="1.2" />
                    <path d="M9.6 9.6L12.5 12.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                </svg>
                <input type="search" placeholder="Search..." x-model="invSearch" aria-label="Search invoices" />
            </label>
            <button type="button" class="admin-po-tool-btn">
                Sort by
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none" aria-hidden="true">
                    <path d="M1 1L5 5L9 1" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button type="button" class="admin-po-tool-btn filter-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                    <path d="M9.5 4.99974H3.48946M1.36789 4.99974H0.5M1.36789 4.99974C1.36789 4.70367 1.47963 4.41972 1.67852 4.21036C1.87741 4.001 2.14716 3.88339 2.42843 3.88339C2.70971 3.88339 2.97946 4.001 3.17835 4.21036C3.37724 4.41972 3.48897 4.70367 3.48897 4.99974C3.48897 5.29582 3.37724 5.57977 3.17835 5.78913C2.97946 5.99849 2.70971 6.1161 2.42843 6.1161C2.14716 6.1161 1.87741 5.99849 1.67852 5.78913C1.47963 5.57977 1.36789 5.29582 1.36789 4.99974ZM9.5 8.38313H6.70368M6.70368 8.38313C6.70368 8.67927 6.59167 8.96355 6.39274 9.17295C6.1938 9.38236 5.92399 9.5 5.64265 9.5C5.36138 9.5 5.09162 9.38187 4.89273 9.17251C4.69384 8.96316 4.58211 8.67921 4.58211 8.38313M6.70368 8.38313C6.70368 8.08698 6.59167 7.80323 6.39274 7.59382C6.1938 7.38441 5.92399 7.26677 5.64265 7.26677C5.36138 7.26677 5.09162 7.38439 4.89273 7.59375C4.69384 7.8031 4.58211 8.08705 4.58211 8.38313M4.58211 8.38313H0.5M9.5 1.61636H7.98946M5.86789 1.61636H0.5M5.86789 1.61636C5.86789 1.32028 5.97963 1.03633 6.17852 0.826974C6.37741 0.617616 6.64716 0.5 6.92843 0.5C7.0677 0.5 7.20561 0.528875 7.33428 0.584978C7.46295 0.64108 7.57987 0.72331 7.67835 0.826974C7.77683 0.930637 7.85495 1.0537 7.90824 1.18915C7.96154 1.32459 7.98897 1.46976 7.98897 1.61636C7.98897 1.76296 7.96154 1.90813 7.90824 2.04357C7.85495 2.17901 7.77683 2.30208 7.67835 2.40574C7.57987 2.50941 7.46295 2.59164 7.33428 2.64774C7.20561 2.70384 7.0677 2.73272 6.92843 2.73272C6.64716 2.73272 6.37741 2.6151 6.17852 2.40574C5.97963 2.19639 5.86789 1.91244 5.86789 1.61636Z" stroke="#3B3731" stroke-miterlimit="10" stroke-linecap="round" />
                </svg>
                Filter
            </button>
        </div>
    </div>

    <section class="admin-bp-inv-table-card">
        <div class="admin-co-pay-table-wrap">
            <table class="admin-po-table admin-bp-inv-table">
                <thead>
                    <tr>
                        <th class="admin-po-check-col">
                            <input
                                type="checkbox"
                                class="admin-po-check"
                                :checked="allSelected"
                                @change="toggleAll()"
                                aria-label="Select all invoices" />
                        </th>
                        @foreach (['Date', 'Invoice ID', 'Customer', 'Booking ID', 'Gross', 'Total', 'Status'] as $th)
                        <th>
                            <span class="admin-co-ref-th">
                                {{ $th }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                    <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedRows as $month => $monthRows)
                    @php
                        $monthStatuses = $monthRows->pluck('status')->unique()->values()->all();
                        $monthHaystacks = $monthRows->map(fn ($r) => implode(' ', [
                            $r['invoice_id'] ?? '',
                            $r['customer'] ?? '',
                            $r['booking_id'] ?? '',
                            $r['status'] ?? '',
                        ]))->values()->all();
                    @endphp
                    <tr
                        class="admin-co-pay-month-row"
                        x-show="monthVisible(@js($monthStatuses), @js($monthHaystacks))"
                        x-cloak>
                        <td colspan="8">
                            <span class="admin-co-pay-month">{{ strtoupper($month) }}</span>
                        </td>
                    </tr>
                    @foreach ($monthRows as $row)
                    <tr
                        class="admin-co-pay-row admin-bp-inv-row"
                        role="button"
                        tabindex="0"
                        :class="{ 'is-selected': isSelected(@js($row['id'])) }"
                        x-show="matchesInv(
                            @js($row['status']),
                            @js($row['invoice_id']),
                            @js($row['customer']),
                            @js($row['booking_id'])
                        )"
                        @click="openInvoice(@js($row['payload']))"
                        @keydown.enter.prevent="openInvoice(@js($row['payload']))"
                        @keydown.space.prevent="openInvoice(@js($row['payload']))"
                        x-cloak>
                        <td class="admin-po-check-col" @click.stop>
                            <input
                                type="checkbox"
                                class="admin-po-check"
                                :checked="isSelected(@js($row['id']))"
                                @change="toggleOne(@js($row['id']))"
                                aria-label="Select {{ $row['invoice_id'] }}" />
                        </td>
                        <td>{{ $row['date'] }}</td>
                        <td>
                            <span class="admin-co-pay-invoice">{{ $row['invoice_id'] }}</span>
                        </td>
                        <td>{{ $row['customer'] }}</td>
                        <td>{{ $row['booking_id'] }}</td>
                        <td>{{ $row['gross'] }}</td>
                        <td class="{{ ($row['status'] ?? '') === 'refunded' ? 'is-refund-total' : '' }}">{{ $row['total'] }}</td>
                        <td>
                            <span class="admin-bp-inv-status is-{{ $row['status'] }}">{{ $row['status_label'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="admin-po-table-footer admin-co-pay-footer admin-bp-inv-footer">
        <p class="admin-table-count">SHOWING 1–{{ min(9, $rows->count()) }} OF {{ $totalCount }} INVOICES</p>
        <nav class="admin-po-pagination" aria-label="Invoices pagination">
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
                    <g filter="url(#filter0_d_provider_inv_page_next)">
                        <circle cx="20" cy="16" r="16" fill="white" />
                    </g>
                    <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_provider_inv_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_provider_inv_page_next" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_provider_inv_page_next" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
        </nav>
    </div>
</div>
