@props(['profile'])

@php
$bookingsTab = $profile['bookings_tab'] ?? [];
$metrics = $bookingsTab['metrics'] ?? [
    ['title' => 'Confirmed', 'value' => '50', 'note' => 'Upcoming'],
    ['title' => 'Completed', 'value' => '142', 'note' => 'All time'],
    ['title' => 'Pending', 'value' => '9', 'note' => 'Awaiting response'],
    ['title' => 'Cancelled', 'value' => '3', 'note' => 'All time'],
];
$filters = $bookingsTab['filters'] ?? [
    'all' => 14,
    'completed' => 9,
    'confirmed' => 2,
    'disputed' => 1,
    'cancelled' => 1,
    'refunded' => 1,
];
$rows = $bookingsTab['rows'] ?? [];
$totalCount = $bookingsTab['total'] ?? ($filters['all'] ?? count($rows));
$spaceBookingsTab = $profile['space_bookings_tab'] ?? $bookingsTab;
$spaceRows = $spaceBookingsTab['rows'] ?? [];
$spaceTotalCount = $spaceBookingsTab['total'] ?? (($spaceBookingsTab['filters']['all'] ?? null) ?? count($spaceRows));
$subTabs = [
    'bookings' => 'Bookings',
    'availability' => 'Availability',
    'clients' => 'Clients',
];
$isSpace = ($profile['type'] ?? '') === 'space';
$providerName = $profile['name'] ?? 'Pawfect Grooming';
$spaceProviderName = $profile['space']['name'] ?? ($isSpace ? $providerName : 'The Garden Grooming Spot');
$spaceAvailabilityProfile = array_merge($profile, [
    'availability' => $profile['space_availability'] ?? ($profile['availability'] ?? []),
]);
$spaceClientsProfile = array_merge($profile, [
    'clients_tab' => $profile['space_clients_tab'] ?? ($profile['clients_tab'] ?? []),
]);
@endphp

<div
    class="admin-bp-bookings"
    :class="{ 'is-space-view': dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }} }"
    x-data="{
        bookingFilter: 'all',
        bookingSearch: '',
        selectedBooking: null,
        selectedClient: null,
        matchesBooking(status, id, owner, pet, service, location) {
            const statusOk = this.bookingFilter === 'all' || this.bookingFilter === status;
            if (!statusOk) return false;
            const q = (this.bookingSearch || '').trim().toLowerCase();
            if (!q) return true;
            return [id, owner, pet, service, location, status].some((v) => String(v || '').toLowerCase().includes(q));
        },
        petParen(pet) {
            if (!pet) return '';
            return String(pet).replace(/\s*·\s*/, ' (') + (String(pet).includes('·') ? ')' : '');
        },
        normalizeBooking(booking) {
            const isSpaceBooking = booking.provider_kind === 'space' || (dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }});
            if (booking.detail && booking.meta_line) return { ...booking, is_space: isSpaceBooking };
            const petParen = this.petParen(booking.pet);
            const metaLine = [booking.owner, petParen, booking.date_long || booking.date, booking.time].filter(Boolean).join(' · ');
            const status = booking.status || 'completed';
            const charge = booking.charge_status || (status === 'refunded' || status === 'cancelled' ? 'Refunded' : 'Paid');
            const chargeTone = booking.charge_tone || (charge === 'Paid' ? 'paid' : (charge === 'Refunded' ? 'refunded' : 'paid'));
            const payout = booking.payout_status || (
                status === 'completed' ? 'Paid'
                : status === 'confirmed' ? 'Pending'
                : status === 'disputed' ? 'On hold'
                : status === 'refunded' ? 'Refunded'
                : '—'
            );
            const payoutTone = booking.payout_tone || (
                payout === 'Paid' ? 'paid'
                : payout === 'On hold' || payout === 'Pending' ? 'refunded'
                : payout === 'Refunded' ? 'refunded'
                : 'paid'
            );
            return {
                ...booking,
                is_space: isSpaceBooking,
                meta_line: booking.meta_line || metaLine,
                summary_service: booking.summary_service || booking.service,
                summary_location: booking.summary_location || booking.location,
                payment_method_short: booking.payment_method_short || 'Debit/Credit Card',
                charge_status: charge,
                charge_tone: chargeTone,
                detail: booking.detail || {
                    service: booking.service,
                    addons: booking.addons || '—',
                    pet: petParen || booking.pet || '—',
                    groomer: @js($providerName),
                    space: isSpaceBooking ? booking.location : null,
                    location_name: isSpaceBooking ? @js($spaceProviderName) : booking.location,
                    location_address: booking.location_address || '',
                    datetime: [booking.date, booking.time].filter(Boolean).join(' · '),
                    duration: booking.duration || '60 Minutes',
                    notes: booking.notes || '',
                    price_lines: booking.price_lines || [
                        { label: booking.service, value: booking.amount },
                    ],
                    total_label: 'Total charged to customer',
                    total: booking.amount,
                    payment_method: booking.payment_method || 'Visa ···· 4529',
                    payment_status: charge,
                    payment_tone: chargeTone,
                    provider_payout: payout,
                    payout_tone: payoutTone,
                    timeline: booking.timeline || [
                        { tone: 'pass', title: 'Booking marked ' + (booking.status_label || status).toLowerCase() + ' by ' + (isSpaceBooking ? 'space host' : 'groomer'), time: booking.date + (booking.time ? ' · ' + booking.time : '') },
                        { tone: 'flag', title: 'Service booked by customer', time: booking.date || '' },
                    ],
                },
            };
        },
        openBooking(booking) {
            this.selectedBooking = this.normalizeBooking(booking);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        closeBooking() {
            this.selectedBooking = null;
        },
        openClient(client) {
            this.selectedClient = client;
        },
        closeClient() {
            this.selectedClient = null;
        },
    }"
    @keydown.escape.window="if (selectedClient) closeClient(); else if (selectedBooking) closeBooking()">

    {{-- List chrome --}}
    <div class="admin-bp-bookings-chrome" x-show="!selectedBooking" x-cloak>
        <div class="admin-co-overview-head">
            <h2
                class="admin-page-title mb-0"
                x-text="({
                    bookings: 'Bookings',
                    availability: 'Availability',
                    clients: 'Clients',
                })[bookingsSubTab] || 'Bookings'"
            ></h2>
            <div class="admin-co-pay-head-actions" x-show="bookingsSubTab === 'bookings' || bookingsSubTab === 'availability' || bookingsSubTab === 'clients'" x-cloak>
                <button type="button" class="admin-btn-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                    </svg>
                    Export Data
                </button>
            </div>
        </div>

        <nav class="admin-bp-profile-subnav admin-bp-bookings-subnav" aria-label="Bookings sections">
            @foreach ($subTabs as $subKey => $subLabel)
            <button
                type="button"
                class="admin-bp-profile-subnav-btn"
                :class="{ 'is-active': bookingsSubTab === '{{ $subKey }}' }"
                @click="bookingsSubTab = '{{ $subKey }}'; selectedBooking = null; selectedClient = null;">
                {{ $subLabel }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- Bookings list --}}
    <div class="admin-bp-bookings-panel" x-show="!selectedBooking && bookingsSubTab === 'bookings'" x-cloak>
        <div class="admin-bp-bookings-metrics">
            @foreach ($metrics as $metric)
            <div class="admin-bp-bookings-metric-card">
                <p class="admin-bp-bookings-metric-title">{{ $metric['title'] }}</p>
                <p class="admin-bp-bookings-metric-value">{{ $metric['value'] }}</p>
                <p class="admin-bp-bookings-metric-note">{{ $metric['note'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="admin-po-toolbar admin-co-bk-toolbar admin-bp-bookings-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Booking status filters">
                <button type="button" class="admin-po-filter is-all" :class="{ 'is-active': bookingFilter === 'all' }" @click="bookingFilter = 'all'">
                    All ({{ $filters['all'] }})
                </button>
                <button type="button" class="admin-po-filter is-completed" :class="{ 'is-active': bookingFilter === 'completed' }" @click="bookingFilter = 'completed'">
                    Completed ({{ $filters['completed'] }})
                </button>
                <button type="button" class="admin-po-filter is-confirmed" :class="{ 'is-active': bookingFilter === 'confirmed' }" @click="bookingFilter = 'confirmed'">
                    Confirmed ({{ $filters['confirmed'] }})
                </button>
                <button type="button" class="admin-po-filter is-disputed" :class="{ 'is-active': bookingFilter === 'disputed' }" @click="bookingFilter = 'disputed'">
                    Disputed ({{ $filters['disputed'] }})
                </button>
                <button type="button" class="admin-po-filter is-cancelled" :class="{ 'is-active': bookingFilter === 'cancelled' }" @click="bookingFilter = 'cancelled'">
                    Cancelled ({{ $filters['cancelled'] }})
                </button>
                <button type="button" class="admin-po-filter is-refunded" :class="{ 'is-active': bookingFilter === 'refunded' }" @click="bookingFilter = 'refunded'">
                    Refunded ({{ $filters['refunded'] }})
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                        <circle cx="6.5" cy="6.5" r="4.75" stroke="#9C9A97" stroke-width="1.2" />
                        <path d="M10 10L12.5 12.5" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round" />
                    </svg>
                    <input type="search" placeholder="Search..." x-model="bookingSearch" aria-label="Search bookings">
                </label>
                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button type="button" class="admin-po-tool-btn filter-btn" aria-label="Filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M9.5 4.99974H3.48946M1.36789 4.99974H0.5M1.36789 4.99974C1.36789 4.70367 1.47963 4.41972 1.67852 4.21036C1.87741 4.001 2.14716 3.88339 2.42843 3.88339C2.70971 3.88339 2.97946 4.001 3.17835 4.21036C3.37724 4.41972 3.48897 4.70367 3.48897 4.99974C3.48897 5.29582 3.37724 5.57977 3.17835 5.78913C2.97946 5.99849 2.70971 6.1161 2.42843 6.1161C2.14716 6.1161 1.87741 5.99849 1.67852 5.78913C1.47963 5.57977 1.36789 5.29582 1.36789 4.99974ZM9.5 8.38313H6.70368M6.70368 8.38313C6.70368 8.67927 6.59167 8.96355 6.39274 9.17295C6.1938 9.38236 5.92399 9.5 5.64265 9.5C5.36138 9.5 5.09162 9.38187 4.89273 9.17251C4.69384 8.96316 4.58211 8.67921 4.58211 8.38313M6.70368 8.38313C6.70368 8.08698 6.59167 7.80323 6.39274 7.59382C6.1938 7.38441 5.92399 7.26677 5.64265 7.26677C5.36138 7.26677 5.09162 7.38439 4.89273 7.59375C4.69384 7.8031 4.58211 8.08705 4.58211 8.38313M4.58211 8.38313H0.5M9.5 1.61636H7.98946M5.86789 1.61636H0.5M5.86789 1.61636C5.86789 1.32028 5.97963 1.03633 6.17852 0.826974C6.37741 0.617616 6.64716 0.5 6.92843 0.5C7.0677 0.5 7.20561 0.528875 7.33428 0.584978C7.46295 0.64108 7.57987 0.72331 7.67835 0.826974C7.77683 0.930637 7.85495 1.0537 7.90824 1.18915C7.96154 1.32459 7.98897 1.46976 7.98897 1.61636C7.98897 1.76296 7.96154 1.90813 7.90824 2.04357C7.85495 2.17901 7.77683 2.30208 7.67835 2.40574C7.57987 2.50941 7.46295 2.59164 7.33428 2.64774C7.20561 2.70384 7.0677 2.73272 6.92843 2.73272C6.64716 2.73272 6.37741 2.6151 6.17852 2.40574C5.97963 2.19639 5.86789 1.91244 5.86789 1.61636Z" stroke="#3B3731" stroke-miterlimit="10" stroke-linecap="round" />
                    </svg>
                    <span>Filter</span>
                </button>
            </div>
        </div>

        <section class="admin-bp-bookings-table-card">
            <div class="admin-co-pay-table-wrap">
                <table class="admin-po-table admin-bp-bookings-table">
                    <thead>
                        <tr>
                            @foreach (['Booking ID', null, 'Service · Location', 'Date · Time', 'Status', 'Amount'] as $thIndex => $th)
                            <th>
                                <span class="admin-co-ref-th">
                                    @if ($thIndex === 1)
                                    <span x-text="(dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }}) ? 'Owner' : 'Owner · Pet'"></span>
                                    @else
                                    {{ $th }}
                                    @endif
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
                        @foreach ([['kind' => 'groomer', 'rows' => $rows], ['kind' => 'space', 'rows' => $spaceRows]] as $bookingSet)
                        @continue(($bookingSet['kind'] === 'space' && ! $isSpace && empty($profile['dual'])) || ($bookingSet['kind'] === 'groomer' && $isSpace))
                        @foreach ($bookingSet['rows'] as $row)
                        @php $bookingPayload = array_merge($row, ['provider_kind' => $bookingSet['kind']]); @endphp
                        <tr
                            class="admin-bp-bookings-row is-clickable"
                            role="button"
                            tabindex="0"
                            @click="openBooking(@js($bookingPayload))"
                            @keydown.enter.prevent="openBooking(@js($bookingPayload))"
                            @keydown.space.prevent="openBooking(@js($bookingPayload))"
                            x-show="({{ $bookingSet['kind'] === 'space' ? "(dual ? viewAs === 'space' : true)" : "(dual ? viewAs !== 'space' : true)" }}) && matchesBooking(
                                @js($row['status']),
                                @js($row['id']),
                                @js($row['owner']),
                                @js($row['pet'] ?? ''),
                                @js($row['service']),
                                @js($row['location'])
                            )"
                            x-cloak>
                            <td>
                                <span class="admin-bp-bookings-id">{{ $row['id'] }}</span>
                            </td>
                            <td>
                                <div class="admin-bp-bookings-stack">
                                    <span class="admin-bp-bookings-primary">{{ $row['owner'] }}</span>
                                    @if (! empty($row['pet']))
                                    <span class="admin-bp-bookings-secondary">{{ $row['pet'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="admin-bp-bookings-stack">
                                    <span class="admin-bp-bookings-primary">{{ $row['service'] }}</span>
                                    <span class="admin-bp-bookings-secondary">{{ $row['location'] }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="admin-bp-bookings-stack">
                                    <span class="admin-bp-bookings-primary">{{ $row['date'] }}</span>
                                    <span class="admin-bp-bookings-secondary">{{ $row['time'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="admin-co-booking-status is-pill is-{{ $row['status'] }}">
                                    {{ $row['status_label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-bp-bookings-amount">{{ $row['amount'] }}</span>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="admin-bp-tx-view-btn"
                                    aria-label="View booking {{ $row['id'] }}"
                                    @click.stop="openBooking(@js($bookingPayload))">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                        <path d="M1.5 9C2.7 5.9 5.4 3.75 9 3.75C12.6 3.75 15.3 5.9 16.5 9C15.3 12.1 12.6 14.25 9 14.25C5.4 14.25 2.7 12.1 1.5 9Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                        <circle cx="9" cy="9" r="2.25" stroke="#3B3731" stroke-width="1.2" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="admin-po-table-footer admin-bp-bookings-footer">
            <p
                class="admin-table-count"
                x-text="(dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }})
                    ? 'SHOWING 1–{{ max(count($spaceRows), 1) }} OF {{ $spaceTotalCount }} BOOKINGS'
                    : 'SHOWING 1–{{ max(count($rows), 1) }} OF {{ $totalCount }} BOOKINGS'">
            </p>
            <nav class="admin-po-pagination" aria-label="Booking pagination">
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
                        <g filter="url(#filter0_d_bp_bk_page_next)">
                            <circle cx="20" cy="16" r="16" fill="white" />
                        </g>
                        <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        <defs>
                            <filter id="filter0_d_bp_bk_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="4" />
                                <feGaussianBlur stdDeviation="2" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bp_bk_page_next" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bp_bk_page_next" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </button>
            </nav>
        </div>
    </div>

    @if (! $isSpace)
    <div x-show="!selectedBooking && bookingsSubTab === 'availability' && (dual ? viewAs !== 'space' : true)" x-cloak>
        <x-admin.provider.bookings-availability :profile="$profile" />
    </div>
    @endif

    @if ($isSpace || ! empty($profile['dual']))
    <div x-show="!selectedBooking && bookingsSubTab === 'availability' && (dual ? viewAs === 'space' : true)" x-cloak>
        <x-admin.provider.bookings-availability :profile="$spaceAvailabilityProfile" />
    </div>
    @endif

    @if (! $isSpace)
    <div x-show="!selectedBooking && bookingsSubTab === 'clients' && (dual ? viewAs !== 'space' : true)" x-cloak>
        <x-admin.provider.bookings-clients :profile="$profile" mode="groomer" />
    </div>
    @endif

    @if ($isSpace || ! empty($profile['dual']))
    <div x-show="!selectedBooking && bookingsSubTab === 'clients' && (dual ? viewAs === 'space' : true)" x-cloak>
        <x-admin.provider.bookings-clients :profile="$spaceClientsProfile" mode="space" />
    </div>
    @endif

    {{-- Booking detail --}}
    <div class="admin-bp-bk-detail" x-show="selectedBooking" x-cloak>
        <div class="admin-co-overview-head">
            <div class="admin-co-bk-detail-title-row">
                <button type="button" class="admin-co-bk-detail-back" @click="closeBooking()" aria-label="Back to bookings">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                        <g filter="url(#filter0_d_bp_bk_detail_back)">
                            <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
                        </g>
                        <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        <defs>
                            <filter id="filter0_d_bp_bk_detail_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="4" />
                                <feGaussianBlur stdDeviation="2" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bp_bk_detail_back" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bp_bk_detail_back" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </button>
                <h2 class="admin-page-title mb-0">
                    Booking ID <span x-text="selectedBooking?.id"></span>
                </h2>
            </div>
            <button type="button" class="admin-btn-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                </svg>
                Export Data
            </button>
        </div>

        <template x-if="selectedBooking">
            <div class="admin-bp-bk-detail-body">
                <article class="admin-bp-bk-detail-summary">
                    <div class="admin-bp-bk-detail-summary-top">
                        <div class="admin-bp-bk-detail-summary-main">
                            <p class="admin-bp-bk-detail-id" x-text="selectedBooking.id"></p>
                            <p class="admin-bp-bk-detail-meta" x-text="selectedBooking.meta_line"></p>
                        </div>
                        <span
                            class="admin-co-booking-status is-pill"
                            :class="'is-' + selectedBooking.status"
                            x-text="selectedBooking.status_label"></span>
                    </div>
                    <div class="admin-bp-bk-detail-summary-sep" aria-hidden="true"></div>
                    <div class="admin-bp-bk-detail-summary-grid">
                        <div class="admin-bp-bk-detail-summary-cell">
                            <p class="admin-bp-bk-detail-summary-label">Service</p>
                            <p class="admin-bp-bk-detail-summary-value" x-text="selectedBooking.summary_service"></p>
                        </div>
                        <div class="admin-bp-bk-detail-summary-cell">
                            <p class="admin-bp-bk-detail-summary-label">Location type</p>
                            <p class="admin-bp-bk-detail-summary-value" x-text="selectedBooking.summary_location"></p>
                        </div>
                        <div class="admin-bp-bk-detail-summary-cell">
                            <p class="admin-bp-bk-detail-summary-label">Payment method</p>
                            <p class="admin-bp-bk-detail-summary-value" x-text="selectedBooking.payment_method_short"></p>
                        </div>
                        <div class="admin-bp-bk-detail-summary-cell">
                            <p class="admin-bp-bk-detail-summary-label">Charge status</p>
                            <p class="admin-bp-bk-detail-summary-value is-status" :class="'is-' + selectedBooking.charge_tone">
                                <span class="admin-bp-tx-dot" aria-hidden="true"></span>
                                <span x-text="selectedBooking.charge_status"></span>
                            </p>
                        </div>
                    </div>
                </article>

                <section class="admin-card admin-co-panel">
                    <x-admin.customer.section-header title="Booking details">
                        <button type="button" class="admin-co-link-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                            </svg>
                            View Customer Booking
                        </button>
                    </x-admin.customer.section-header>
                    <dl class="admin-co-details">
                        <div class="admin-co-details-row">
                            <dt>Service</dt>
                            <dd x-text="selectedBooking.detail.service"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Add-ons</dt>
                            <dd x-text="selectedBooking.detail.addons"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Pet</dt>
                            <dd x-text="selectedBooking.detail.pet"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt x-text="selectedBooking.is_space ? 'Space' : 'Groomer'"></dt>
                            <dd x-text="selectedBooking.is_space ? selectedBooking.detail.space : selectedBooking.detail.groomer"></dd>
                        </div>
                        <div class="admin-co-details-row admin-co-bk-detail-location">
                            <dt>Location</dt>
                            <dd>
                                <span class="admin-co-bk-detail-loc-name" x-text="selectedBooking.detail.location_name"></span>
                                <span
                                    class="admin-co-bk-detail-loc-addr"
                                    x-show="selectedBooking.detail.location_address"
                                    x-text="selectedBooking.detail.location_address"></span>
                            </dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Date &amp; Time</dt>
                            <dd x-text="selectedBooking.detail.datetime"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Duration</dt>
                            <dd x-text="selectedBooking.detail.duration"></dd>
                        </div>
                        <div class="admin-co-details-row" x-show="selectedBooking.detail.notes">
                            <dt>Customer Notes</dt>
                            <dd class="admin-co-bk-detail-notes" x-text="selectedBooking.detail.notes"></dd>
                        </div>
                    </dl>
                </section>

                <section class="admin-card admin-co-panel admin-bp-bk-price-panel">
                    <x-admin.customer.section-header title="Full price breakdown" />
                    <dl class="admin-co-details admin-bp-bk-price-details">
                        <template x-for="(line, idx) in (selectedBooking.detail.price_lines || [])" :key="idx">
                            <div
                                class="admin-co-details-row"
                                :class="{
                                    'is-muted-line': !!line.muted || String(line.label || '').toLowerCase().includes('discount') || String(line.label || '').toLowerCase().includes('add-on'),
                                    'is-discount': String(line.label || '').toLowerCase().includes('discount') || String(line.value || '').trim().startsWith('-'),
                                }">
                                <dt x-text="line.label"></dt>
                                <dd x-text="line.value"></dd>
                            </div>
                        </template>
                        <div class="admin-co-details-row admin-bp-bk-price-total">
                            <dt x-text="selectedBooking.detail.total_label || 'Total charged to customer'"></dt>
                            <dd x-text="selectedBooking.detail.total || selectedBooking.amount"></dd>
                        </div>
                        <div class="admin-co-details-row admin-bp-bk-price-pay">
                            <dt>Payment method</dt>
                            <dd class="admin-bp-bk-price-method" x-text="selectedBooking.detail.payment_method"></dd>
                        </div>
                        <div class="admin-co-details-row admin-bp-bk-price-pay">
                            <dt>Payment status</dt>
                            <dd class="admin-bp-bk-detail-status" :class="'is-' + (selectedBooking.detail.payment_tone || selectedBooking.charge_tone)">
                                <span class="admin-bp-tx-dot" aria-hidden="true"></span>
                                <span x-text="selectedBooking.detail.payment_status"></span>
                            </dd>
                        </div>
                        <div class="admin-co-details-row admin-bp-bk-price-pay">
                            <dt x-text="selectedBooking.is_space ? 'Space Host payout' : 'Groomer payout'"></dt>
                            <dd class="admin-bp-bk-detail-status" :class="'is-' + (selectedBooking.detail.payout_tone || 'paid')">
                                <span class="admin-bp-tx-dot" aria-hidden="true"></span>
                                <span x-text="selectedBooking.detail.provider_payout || selectedBooking.detail.groomer_payout"></span>
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="admin-card admin-co-panel admin-co-activity-panel">
                    <x-admin.customer.section-header title="Activity timeline">
                        <button type="button" class="admin-co-link-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                            </svg>
                            Full Log
                        </button>
                    </x-admin.customer.section-header>
                    <ul class="admin-co-activity">
                        <template x-for="(item, idx) in (selectedBooking.detail.timeline || [])" :key="idx">
                            <li class="admin-co-activity-item">
                                <span class="admin-co-activity-dot" :class="'is-' + (item.tone || 'flag')" aria-hidden="true"></span>
                                <div>
                                    <p class="admin-co-activity-title" x-text="item.title"></p>
                                    <span class="admin-co-activity-time" x-text="item.time"></span>
                                </div>
                            </li>
                        </template>
                    </ul>
                </section>
            </div>
        </template>
    </div>
</div>
