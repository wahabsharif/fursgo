@props(['profile', 'variant' => null])

@php
$isSpace = $variant === 'space' || ($variant === null && ($profile['type'] ?? '') === 'space');
$roleProfile = $isSpace ? ($profile['space'] ?? []) : ($profile['groomer'] ?? []);
$payouts = $roleProfile['payouts'] ?? ($profile['payouts'] ?? []);
$tx = $payouts['transactions'] ?? [];
$hasConfiguredRows = array_key_exists('rows', $tx);
$filters = $tx['filters'] ?? [
    'all' => 14,
    'paid' => 9,
    'refunded' => 1,
    'failed' => 1,
];
$rows = collect($tx['rows'] ?? [
    [
        'id' => 'tx-1',
        'booking_id' => 'BK-000001',
        'date' => '05/02/26',
        'time' => '16:30',
        'client' => 'Jane Doe',
        'pet' => 'Bella · Rabbit',
        'pet_short' => 'Bella (Rabbit)',
        'service' => 'Full Groom',
        'amount' => '£50',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'PayPal',
        'location' => 'Home visits',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Jane Doe · Bella (Rabbit) · 05 Feb 2026 · 16:30',
        'lines' => [
            ['label' => 'Full Groom', 'value' => '£40.00'],
            ['label' => 'Add-ons', 'value' => '£38.00', 'note' => 'Ear-cleaning £8 · Hypoallergenic shampoo £5 · Anti-itch treatment £10'],
            ['label' => 'Promo code', 'label_suffix' => '(NWYR26)', 'value' => '- £25.00'],
        ],
        'total_label' => 'Total charged',
        'total' => '£50.00',
    ],
    [
        'id' => 'tx-2',
        'booking_id' => 'BK-000002',
        'date' => '04/02/26',
        'time' => '11:15',
        'client' => 'Tom Harris',
        'pet' => 'Spike · Cat',
        'pet_short' => 'Spike (Cat)',
        'service' => 'Nail Trim',
        'amount' => '£25',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'Debit Card',
        'location' => 'Salon',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Tom Harris · Spike (Cat) · 04 Feb 2026 · 11:15',
        'lines' => [
            ['label' => 'Nail Trim', 'value' => '£25.00'],
        ],
        'total_label' => 'Total charged',
        'total' => '£25.00',
    ],
    [
        'id' => 'tx-3',
        'booking_id' => 'BK-000003',
        'date' => '03/02/26',
        'time' => '09:40',
        'client' => 'Alex Rivera',
        'pet' => 'Milo · Dog',
        'pet_short' => 'Milo (Dog)',
        'service' => 'Bath & Brush',
        'amount' => '£50',
        'status' => 'failed',
        'status_label' => 'Failed',
        'payment' => 'Debit Card',
        'location' => 'Home visits',
        'charge_status' => 'Card declined',
        'charge_tone' => 'failed',
        'meta_line' => 'Alex Rivera · Milo (Dog) · 03 Feb 2026 · 09:40',
        'lines' => [
            ['label' => 'Bath & Brush', 'value' => '£45.00'],
            ['label' => 'Add-ons', 'value' => '£5.00', 'note' => 'Ear-cleaning £5'],
        ],
        'total_label' => 'Total attempted',
        'total' => '£50.00',
        'total_note' => '£50.00 — not charged',
        'total_note_tone' => 'failed',
    ],
    [
        'id' => 'tx-4',
        'booking_id' => 'BK-000004',
        'date' => '02/02/26',
        'time' => '14:05',
        'client' => 'Mia Brooks',
        'pet' => 'Luna · Dog',
        'pet_short' => 'Luna (Dog)',
        'service' => 'Full Groom',
        'amount' => '£40',
        'status' => 'refunded',
        'status_label' => 'Refunded',
        'payment' => 'PayPal',
        'location' => 'Salon',
        'charge_status' => 'Partial Refund',
        'charge_tone' => 'refunded',
        'refund_status' => 'Partial Refund',
        'meta_line' => 'Mia Brooks · Luna (Dog) · 02 Feb 2026 · 14:05',
        'support_ticket' => 'SPRT-00412',
        'lines' => [
            ['label' => 'Full Groom', 'value' => '£40.00'],
            ['label' => 'Add-ons', 'value' => '£25.00', 'note' => 'Hypoallergenic shampoo £15 · Nail paint £10'],
        ],
        'total_label' => 'Total charged',
        'total' => '£65.00',
        'refund_rows' => [
            ['label' => 'Original charge', 'value' => '+ £40.00', 'tone' => 'credit'],
            ['label' => 'Refund issued', 'value' => '- £25.00', 'tone' => 'debit'],
            ['label' => 'Net received', 'value' => '£15.00', 'tone' => 'net'],
        ],
    ],
    [
        'id' => 'tx-5',
        'booking_id' => 'BK-000005',
        'date' => '01/02/26',
        'time' => '10:20',
        'client' => 'Jane Doe',
        'pet' => 'Spike · Cat',
        'pet_short' => 'Spike (Cat)',
        'service' => 'Nail Trim',
        'amount' => '£50',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'PayPal',
        'location' => 'Home visits',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Jane Doe · Spike (Cat) · 01 Feb 2026 · 10:20',
        'lines' => [
            ['label' => 'Nail Trim', 'value' => '£20.00'],
            ['label' => 'Add-ons', 'value' => '£30.00', 'note' => 'Dental clean £20 · Cologne £10'],
        ],
        'total_label' => 'Total charged',
        'total' => '£50.00',
    ],
    [
        'id' => 'tx-6',
        'booking_id' => 'BK-000006',
        'date' => '31/01/26',
        'time' => '15:45',
        'client' => 'Sam Patel',
        'pet' => 'Coco · Dog',
        'pet_short' => 'Coco (Dog)',
        'service' => 'Hair Trim',
        'amount' => '£45',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'Apple Pay',
        'location' => 'Salon',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Sam Patel · Coco (Dog) · 31 Jan 2026 · 15:45',
        'lines' => [
            ['label' => 'Hair Trim', 'value' => '£45.00'],
        ],
        'total_label' => 'Total charged',
        'total' => '£45.00',
    ],
    [
        'id' => 'tx-7',
        'booking_id' => 'BK-000007',
        'date' => '30/01/26',
        'time' => '12:10',
        'client' => 'Olivia Chen',
        'pet' => 'Mochi · Cat',
        'pet_short' => 'Mochi (Cat)',
        'service' => 'Full Groom',
        'amount' => '£65',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'Debit Card',
        'location' => 'Home visits',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Olivia Chen · Mochi (Cat) · 30 Jan 2026 · 12:10',
        'lines' => [
            ['label' => 'Full Groom', 'value' => '£55.00'],
            ['label' => 'Add-ons', 'value' => '£10.00', 'note' => 'Nail paint £10'],
        ],
        'total_label' => 'Total charged',
        'total' => '£65.00',
    ],
    [
        'id' => 'tx-8',
        'booking_id' => 'BK-000008',
        'date' => '29/01/26',
        'time' => '09:00',
        'client' => 'Ben Walker',
        'pet' => 'Rex · Dog',
        'pet_short' => 'Rex (Dog)',
        'service' => 'Bath & Brush',
        'amount' => '£35',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'Google Pay',
        'location' => 'Salon',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Ben Walker · Rex (Dog) · 29 Jan 2026 · 09:00',
        'lines' => [
            ['label' => 'Bath & Brush', 'value' => '£35.00'],
        ],
        'total_label' => 'Total charged',
        'total' => '£35.00',
    ],
    [
        'id' => 'tx-9',
        'booking_id' => 'BK-000009',
        'date' => '28/01/26',
        'time' => '17:20',
        'client' => 'Priya Shah',
        'pet' => 'Nala · Dog',
        'pet_short' => 'Nala (Dog)',
        'service' => 'Nail Trim',
        'amount' => '£20',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'PayPal',
        'location' => 'Home visits',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Priya Shah · Nala (Dog) · 28 Jan 2026 · 17:20',
        'lines' => [
            ['label' => 'Nail Trim', 'value' => '£20.00'],
        ],
        'total_label' => 'Total charged',
        'total' => '£20.00',
    ],
    [
        'id' => 'tx-10',
        'booking_id' => 'BK-000010',
        'date' => '27/01/26',
        'time' => '13:30',
        'client' => 'Chris Evans',
        'pet' => 'Buddy · Dog',
        'pet_short' => 'Buddy (Dog)',
        'service' => 'Full Groom',
        'amount' => '£55',
        'status' => 'paid',
        'status_label' => 'Paid',
        'payment' => 'Debit Card',
        'location' => 'Salon',
        'charge_status' => 'Paid',
        'charge_tone' => 'paid',
        'meta_line' => 'Chris Evans · Buddy (Dog) · 27 Jan 2026 · 13:30',
        'lines' => [
            ['label' => 'Full Groom', 'value' => '£55.00'],
        ],
        'total_label' => 'Total charged',
        'total' => '£55.00',
    ],
]);

// The admin demo data currently only supplies groomer transactions. Keep that
// fallback useful for space profiles without leaking grooming services or pets
// into the Space Host view. Explicit space transaction rows are left untouched.
if ($isSpace && ! $hasConfiguredRows) {
    $spaceServices = ['Full-Day', 'Half-Day', 'Hourly'];

    $rows = $rows->values()->map(function (array $row, int $index) use ($spaceServices): array {
        $service = $spaceServices[$index % count($spaceServices)];
        $amount = (string) ($row['amount'] ?? '£0');
        $lineAmount = preg_match('/\.\d{2}$/', $amount) ? $amount : $amount . '.00';

        $row['pet'] = '';
        $row['pet_short'] = '';
        $row['service'] = $service;
        $row['location'] = 'Garden / Shed';
        $row['meta_line'] = implode(' · ', array_filter([
            $row['client'] ?? null,
            $row['date'] ?? null,
            $row['time'] ?? null,
        ]));
        $row['lines'] = [
            ['label' => $service, 'value' => $lineAmount],
        ];
        $row['total'] = $lineAmount;

        return $row;
    });
}
@endphp

<div
    class="admin-bp-tx"
    x-data="{
        txFilter: 'all',
        txSearch: '',
        openTxId: null,
        matchesTx(status, client, pet, service, bookingId, payment) {
            const statusOk = this.txFilter === 'all' || this.txFilter === status;
            if (!statusOk) return false;
            const q = (this.txSearch || '').trim().toLowerCase();
            if (!q) return true;
            return [client, pet, service, bookingId, payment, status].some((v) => String(v || '').toLowerCase().includes(q));
        },
        toggleTx(id) {
            this.openTxId = this.openTxId === id ? null : id;
        },
    }">
    <div class="admin-po-toolbar admin-co-support-toolbar admin-bp-tx-toolbar">
        <div class="admin-po-filters" role="tablist" aria-label="Transaction status filters">
            <button type="button" class="admin-po-filter is-all" :class="{ 'is-active': txFilter === 'all' }" @click="txFilter = 'all'">
                All ({{ $filters['all'] }})
            </button>
            <button type="button" class="admin-po-filter is-paid" :class="{ 'is-active': txFilter === 'paid' }" @click="txFilter = 'paid'">
                Paid ({{ $filters['paid'] }})
            </button>
            <button type="button" class="admin-po-filter is-tx-refunded" :class="{ 'is-active': txFilter === 'refunded' }" @click="txFilter = 'refunded'">
                Refunded ({{ $filters['refunded'] }})
            </button>
            <button type="button" class="admin-po-filter is-failed" :class="{ 'is-active': txFilter === 'failed' }" @click="txFilter = 'failed'">
                Failed ({{ $filters['failed'] }})
            </button>
        </div>

        <div class="admin-po-tools">
            <label class="admin-po-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                    <circle cx="6.2" cy="6.2" r="4.7" stroke="#3B3731" stroke-width="1.2" />
                    <path d="M9.6 9.6L12.5 12.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                </svg>
                <input type="search" placeholder="Search..." x-model="txSearch" aria-label="Search transactions" />
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

    <section class="admin-bp-tx-table-card">
        <div class="admin-co-pay-table-wrap">
            <table class="admin-po-table admin-bp-tx-table">
                <thead>
                    <tr>
                        @foreach (['Date / time', $isSpace ? 'Client' : 'Client / Pet', 'Service', 'Amount', 'Status', 'Payment'] as $th)
                        <th>
                            <span class="admin-co-ref-th">
                                {{ $th }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                    <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </th>
                        @endforeach
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr
                        class="admin-bp-tx-row"
                        :class="{ 'is-open': openTxId === @js($row['id']) }"
                        x-show="matchesTx(
                            @js($row['status']),
                            @js($row['client']),
                            @js($row['pet']),
                            @js($row['service']),
                            @js($row['booking_id']),
                            @js($row['payment'])
                        )"
                        x-cloak>
                        <td>{{ $row['date'] }} · {{ $row['time'] }}</td>
                        <td>
                            <span class="admin-bp-tx-client">{{ $row['client'] }}</span>
                            @if (! $isSpace && ! empty($row['pet']))
                            <span class="admin-bp-tx-pet"> / {{ $row['pet'] }}</span>
                            @endif
                        </td>
                        <td>{{ $row['service'] }}</td>
                        <td>{{ $row['amount'] }}</td>
                        <td>
                            <span class="admin-bp-tx-status is-{{ $row['status'] }}">{{ $row['status_label'] }}</span>
                        </td>
                        <td>{{ $row['payment'] }}</td>
                        <td>
                            <button
                                type="button"
                                class="admin-bp-tx-view-btn"
                                :aria-expanded="openTxId === @js($row['id']) ? 'true' : 'false'"
                                :aria-label="openTxId === @js($row['id']) ? 'Close transaction details' : 'View transaction details'"
                                @click="toggleTx(@js($row['id']))">
                                <svg x-show="openTxId !== @js($row['id'])" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                    <path d="M1.5 9C2.7 5.9 5.4 3.75 9 3.75C12.6 3.75 15.3 5.9 16.5 9C15.3 12.1 12.6 14.25 9 14.25C5.4 14.25 2.7 12.1 1.5 9Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                    <circle cx="9" cy="9" r="2.25" stroke="#3B3731" stroke-width="1.2" />
                                </svg>
                                <svg x-show="openTxId === @js($row['id'])" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                    <path d="M3 3L11 11M11 3L3 11" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr
                        class="admin-bp-tx-detail-row"
                        x-show="openTxId === @js($row['id']) && matchesTx(
                            @js($row['status']),
                            @js($row['client']),
                            @js($row['pet']),
                            @js($row['service']),
                            @js($row['booking_id']),
                            @js($row['payment'])
                        )"
                        x-cloak>
                        <td colspan="7">
                            <article class="admin-bp-tx-detail-card is-{{ $row['status'] }}">
                                <div class="admin-bp-tx-detail-top">
                                    <div class="admin-bp-tx-detail-top-main">
                                        <div class="admin-bp-tx-detail-id-row">
                                            <span class="admin-bp-tx-booking-id">{{ $row['booking_id'] }}</span>
                                            <span class="admin-bp-tx-status is-{{ $row['status'] }}">{{ $row['status_label'] }}</span>
                                        </div>
                                        <p class="admin-bp-tx-detail-meta">{{ $row['meta_line'] }}</p>
                                    </div>
                                    <div class="admin-bp-tx-detail-actions">
                                        @if (! empty($row['support_ticket']))
                                        <button type="button" class="admin-co-link-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                                <path d="M10 1L4 7M10 1H6M10 1V5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            View Support Ticket
                                        </button>
                                        @endif
                                        <button type="button" class="admin-btn-dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                                                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>

                                <div class="admin-bp-tx-detail-grid">
                                    <div class="admin-bp-tx-detail-field">
                                        <p class="admin-bp-tx-detail-label">Service</p>
                                        <p class="admin-bp-tx-detail-value">{{ $row['service'] }}</p>
                                    </div>
                                    <div class="admin-bp-tx-detail-field">
                                        <p class="admin-bp-tx-detail-label">Location Type</p>
                                        <p class="admin-bp-tx-detail-value">{{ $row['location'] }}</p>
                                    </div>
                                    <div class="admin-bp-tx-detail-field">
                                        <p class="admin-bp-tx-detail-label">{{ ($row['status'] ?? '') === 'refunded' ? 'Refund Status' : 'Charge Status' }}</p>
                                        <p class="admin-bp-tx-detail-value is-{{ $row['charge_tone'] ?? $row['status'] }}">
                                            <span class="admin-bp-tx-dot" aria-hidden="true"></span>
                                            {{ $row['charge_status'] ?? $row['status_label'] }}
                                        </p>
                                    </div>
                                    <div class="admin-bp-tx-detail-field">
                                        <p class="admin-bp-tx-detail-label">Payment Method</p>
                                        <p class="admin-bp-tx-detail-value">{{ $row['payment'] }}</p>
                                    </div>
                                </div>

                                <div class="admin-bp-tx-breakdown">
                                    @foreach ($row['lines'] ?? [] as $line)
                                    <div class="admin-bp-tx-breakdown-row">
                                        <div class="admin-bp-tx-breakdown-copy">
                                            <p class="admin-bp-tx-breakdown-label">
                                                {{ $line['label'] }}
                                                @if (! empty($line['label_suffix']))
                                                <span class="admin-bp-tx-breakdown-label-suffix">{{ $line['label_suffix'] }}</span>
                                                @endif
                                            </p>
                                            @if (! empty($line['note']))
                                            <p class="admin-bp-tx-breakdown-note">{{ $line['note'] }}</p>
                                            @endif
                                        </div>
                                        <p class="admin-bp-tx-breakdown-value">{{ $line['value'] }}</p>
                                    </div>
                                    @endforeach
                                    <div class="admin-bp-tx-breakdown-total">
                                        <p class="admin-bp-tx-breakdown-total-label">{{ $row['total_label'] ?? 'Total charged' }}</p>
                                        <div class="admin-bp-tx-breakdown-total-side">
                                            <p class="admin-bp-tx-breakdown-total-value">{{ $row['total'] ?? $row['amount'] }}</p>
                                            @if (! empty($row['total_note']))
                                            <p class="admin-bp-tx-breakdown-total-note is-{{ $row['total_note_tone'] ?? 'failed' }}">{{ $row['total_note'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if (! empty($row['refund_rows']))
                                <div class="admin-bp-tx-refund-summary">
                                    @foreach ($row['refund_rows'] as $rRow)
                                    <div class="admin-bp-tx-refund-row">
                                        <span class="admin-bp-tx-refund-label">{{ $rRow['label'] }}</span>
                                        <span class="admin-bp-tx-refund-value is-{{ $rRow['tone'] ?? 'net' }}">{{ $rRow['value'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </article>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="admin-po-table-footer admin-co-pay-footer admin-bp-tx-footer">
        <p class="admin-table-count">SHOWING 1–{{ min(10, $rows->count()) }} OF {{ $filters['all'] }} TRANSACTIONS</p>
        <nav class="admin-po-pagination" aria-label="Transactions pagination">
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
                    <g filter="url(#filter0_d_provider_tx_page_next)">
                        <circle cx="20" cy="16" r="16" fill="white" />
                    </g>
                    <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_provider_tx_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_provider_tx_page_next" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_provider_tx_page_next" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
        </nav>
    </div>
</div>
