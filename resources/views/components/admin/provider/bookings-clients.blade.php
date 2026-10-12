@props(['profile', 'mode' => 'any'])

@php
$clientsTab = $profile['clients_tab'] ?? [];
$clientAvatar = asset('images/profile_image.png');
$fallbackAvatar = $clientAvatar;
$clientDetailDemo = [
'name' => 'Jane Doe',
'location_id' => 'London, Uk - USER-01452',
'since' => 'Client since 02 Feb 2024',
'avatar' => $clientAvatar,
'stats' => [
['value' => '2', 'label' => 'Upcoming'],
['value' => '15', 'label' => 'Completed'],
['value' => '£252', 'label' => 'Total paid'],
['value' => '4.8', 'label' => 'Avg. rating', 'star' => true],
],
'recent_bookings' => [
['id' => 'FG-0563-B12', 'service' => 'Full Groom', 'date' => '10/06/2025', 'status' => 'Completed', 'amount' => '£55.00'],
['id' => 'FG-0563-B12', 'service' => 'Full Groom', 'date' => '10/06/2025', 'status' => 'Completed', 'amount' => '£55.00'],
],
'review' => [
        'author_name' => 'Jane Doe',
        'author_id' => 'USER-01452',
'stars' => 5,
'ago' => '2 wk ago',
'body' => "I booked this groomer through FursGo for my anxious little cockapoo and honestly couldn't be happier....",
        'footer_service' => 'Full Groom',
        'footer_booking' => 'FG-0563-B12',
    ],
];
$rows = $clientsTab['rows'] ?? [
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'avatar' => $fallbackAvatar, 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'avatar' => $fallbackAvatar, 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'initials' => 'JD', 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'avatar' => $fallbackAvatar, 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'avatar' => $fallbackAvatar, 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'avatar' => $fallbackAvatar, 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'initials' => 'JD', 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'avatar' => $fallbackAvatar, 'detail' => $clientDetailDemo],
['name' => 'Sam P', 'meta' => '+2 pets • Since 02 Feb 2022', 'bookings' => '2', 'paid' => '£50.00', 'last_booked' => '18/12/2023', 'avatar' => $fallbackAvatar, 'detail' => $clientDetailDemo],
];
$totalCount = $clientsTab['total'] ?? 14;
$showingCount = $clientsTab['showing'] ?? count($rows);
$modalViewCondition = $mode === 'space'
    ? "(dual ? viewAs === 'space' : true)"
    : ($mode === 'groomer' ? "(dual ? viewAs !== 'space' : true)" : 'true');
$starPath = 'M4.48558 0.495351C4.69568 -0.165118 5.63765 -0.165117 5.84775 0.495352L6.56678 2.75566C6.66058 3.05053 6.93624 3.25102 7.24787 3.25102H9.61774C10.3046 3.25102 10.5955 4.11899 10.0455 4.52717L8.09296 5.97613C7.84986 6.15653 7.74826 6.4697 7.83963 6.75694L8.57697 9.07482C8.78582 9.73134 8.02376 10.2679 7.46813 9.85562L5.59443 8.46516C5.3408 8.27694 4.99253 8.27694 4.73891 8.46515L2.86521 9.85562C2.30957 10.2679 1.54751 9.73134 1.75636 9.07481L2.4937 6.75694C2.58507 6.4697 2.48347 6.15653 2.24037 5.97613L0.287834 4.52717C-0.262197 4.11899 0.0287438 3.25102 0.715593 3.25102H3.08546C3.39709 3.25102 3.67275 3.05053 3.76655 2.75566L4.48558 0.495351Z';
@endphp

<div class="admin-bp-clients">
    <section class="admin-bp-clients-table-card">
        <div class="admin-co-pay-table-wrap">
            <table class="admin-po-table admin-bp-clients-table">
                <thead>
                    <tr>
                        @foreach (['Clients', 'Total bookings', 'Total paid', 'Last booked'] as $th)
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
                    @foreach ($rows as $index => $row)
                    @php $detail = $row['detail'] ?? $clientDetailDemo; @endphp
                    <tr>
                        <td>
                            <div class="admin-bp-clients-person">
                                @if (! empty($row['initials']))
                                <span class="admin-bp-clients-initials" aria-hidden="true">{{ $row['initials'] }}</span>
                                @else
                                <img
                                    src="{{ $row['avatar'] ?? $fallbackAvatar }}"
                                    alt=""
                                    class="admin-bp-clients-avatar"
                                    width="40"
                                    height="40">
                                @endif
                                <div class="admin-bp-clients-copy">
                                    <p class="admin-bp-clients-name">{{ $row['name'] }}</p>
                                    <p class="admin-bp-clients-meta">{{ $row['meta'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ $row['bookings'] }}</td>
                        <td>{{ $row['paid'] }}</td>
                        <td>{{ $row['last_booked'] }}</td>
                        <td>
                            <button
                                type="button"
                                class="admin-bp-tx-view-btn"
                                aria-label="View client {{ $row['name'] }}"
                                @click.stop="openClient(@js($detail))">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                    <path d="M1.5 9C2.7 5.9 5.4 3.75 9 3.75C12.6 3.75 15.3 5.9 16.5 9C15.3 12.1 12.6 14.25 9 14.25C5.4 14.25 2.7 12.1 1.5 9Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                    <circle cx="9" cy="9" r="2.25" stroke="#3B3731" stroke-width="1.2" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="admin-po-table-footer admin-bp-clients-footer">
        <p class="admin-table-count">SHOWING 1–{{ $showingCount }} OF {{ $totalCount }} CLIENTS</p>
        <nav class="admin-po-pagination" aria-label="Client pagination">
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
                    <g filter="url(#filter0_d_bp_clients_page_next)">
                        <circle cx="20" cy="16" r="16" fill="white" />
                    </g>
                    <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_bp_clients_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bp_clients_page_next" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bp_clients_page_next" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
        </nav>
    </div>
</div>

<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': !!selectedClient && {{ $modalViewCondition }} }"
        @click.self="closeClient()">
        <div
            class="admin-co-modal admin-bp-client-modal"
            role="dialog"
            aria-modal="true"
            aria-label="Client profile"
            @click.stop>
            <button type="button" class="admin-co-modal-close admin-bp-client-modal-close" @click="closeClient()" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                    <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>

            <div class="admin-bp-client-modal-head">
                <img
                    :src="selectedClient?.avatar || @js($fallbackAvatar)"
                    alt=""
                    class="admin-bp-client-modal-avatar"
                    width="48"
                    height="48">
                <div class="admin-bp-client-modal-identity">
                    <p class="admin-bp-client-modal-name" x-text="selectedClient?.name"></p>
                    <p class="admin-bp-client-modal-meta" x-text="selectedClient?.location_id"></p>
                    <p class="admin-bp-client-modal-meta" x-text="selectedClient?.since"></p>
                </div>
            </div>

            <button type="button" class="admin-bp-client-modal-profile-link">
                <span class="admin-bp-client-modal-profile-link-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
                        <path d="M0.625 15.625V14.7917C0.625 12.0334 2.86667 9.79184 5.625 9.79184H8.95833C11.7167 9.79184 13.9583 12.0334 13.9583 14.7917V15.625" stroke="#649FC9" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7.29183 7.29147C5.45016 7.29147 3.9585 5.79985 3.9585 3.95824C3.9585 2.11662 5.45016 0.625 7.29183 0.625C9.13349 0.625 10.6252 2.11662 10.6252 3.95824C10.6252 5.79985 9.13349 7.29147 7.29183 7.29147Z" stroke="#649FC9" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>View full customer profile</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                    <path d="M5.25 3.5L8.75 7L5.25 10.5" stroke="#659FC9" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <hr class="admin-bp-client-modal-sep" />

            <section class="admin-bp-client-modal-section">
                <h3 class="admin-bp-client-modal-section-title">Client bookings with provider</h3>
                <div class="admin-bp-client-modal-stats">
                    <template x-for="(stat, statIndex) in (selectedClient?.stats || [])" :key="'client-stat-' + statIndex">
                        <div class="admin-bp-client-modal-stat">
                            <p class="admin-bp-client-modal-stat-value">
                                <span class="value" x-text="stat.value"></span>
                                <template x-if="stat.star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none" aria-hidden="true">
                                        <path d="{{ $starPath }}" fill="#FFC97A" />
                                    </svg>
                                </template>
                            </p>
                            <p class="admin-bp-client-modal-stat-label" x-text="stat.label"></p>
                        </div>
                    </template>
                </div>
            </section>

            <hr class="admin-bp-client-modal-sep" />

            <section class="admin-bp-client-modal-section">
                <h3 class="admin-bp-client-modal-section-title">Recent bookings with provider</h3>
                <ul class="admin-bp-client-modal-bookings">
                    <template x-for="(booking, bookingIndex) in (selectedClient?.recent_bookings || [])" :key="'client-bk-' + bookingIndex">
                        <li class="admin-bp-client-modal-booking">
                            <button type="button" class="admin-bp-client-modal-booking-id" x-text="booking.id"></button>
                            <span class="admin-bp-client-modal-booking-service" x-text="booking.service"></span>
                            <span class="admin-bp-client-modal-booking-date" x-text="booking.date"></span>
                            <span class="admin-bp-client-modal-booking-status" x-text="booking.status"></span>
                            <span class="admin-bp-client-modal-booking-amount" x-text="booking.amount"></span>
                        </li>
                    </template>
                </ul>
            </section>

            <hr class="admin-bp-client-modal-sep" />

            <section class="admin-bp-client-modal-section">
                <h3 class="admin-bp-client-modal-section-title">Recent review for this provider</h3>
                <div class="admin-bp-client-modal-review" x-show="selectedClient?.review">
                    <div class="admin-bp-client-modal-review-top">
                        <div class="admin-bp-client-modal-review-author-row">
                            <p class="admin-bp-client-modal-review-author">
                                <span class="admin-bp-client-modal-review-author-name" x-text="selectedClient?.review?.author_name"></span>
                                <span class="admin-bp-client-modal-review-author-id" x-text="selectedClient?.review?.author_id ? (' · ' + selectedClient.review.author_id) : ''"></span>
                            </p>
                            <div class="admin-bp-client-modal-review-stars" aria-hidden="true">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="'client-star-' + star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                                        <path d="{{ $starPath }}" :fill="star <= (selectedClient?.review?.stars || 0) ? '#FFC97A' : '#E8E6E3'" />
                                    </svg>
                                </template>
                            </div>
                        </div>
                        <span class="admin-bp-client-modal-review-ago" x-text="selectedClient?.review?.ago"></span>
                    </div>
                    <p class="admin-bp-client-modal-review-body" x-text="selectedClient?.review?.body"></p>
                    <p class="admin-bp-client-modal-review-footer">
                        <span class="admin-bp-client-modal-review-footer-service" x-text="selectedClient?.review?.footer_service"></span>
                        <span class="admin-bp-client-modal-review-footer-booking" x-text="selectedClient?.review?.footer_booking ? (' · ' + selectedClient.review.footer_booking) : ''"></span>
                    </p>
                </div>
            </section>
        </div>
    </div>
</template>
