@props(['profile'])

@php
$summary = $profile['booking_summary'] ?? [
    'count' => 0,
    'spend' => '£0',
    'filters' => [
        'all' => 0,
        'completed' => 0,
        'confirmed' => 0,
        'disputed' => 0,
        'cancelled' => 0,
        'refunded' => 0,
    ],
];
$bookings = $profile['booking_list'] ?? [];
$filters = $summary['filters'];
@endphp

<div
    class="admin-co-bookings-tab"
    x-data="{
        bookingFilter: 'all',
        bookingSearch: '',
        matchesBooking(status, id, pet, service, provider) {
            const q = (this.bookingSearch || '').trim().toLowerCase();
            const statusOk = this.bookingFilter === 'all' || this.bookingFilter === status;
            if (!statusOk) return false;
            if (!q) return true;
            return id.toLowerCase().includes(q)
                || pet.toLowerCase().includes(q)
                || service.toLowerCase().includes(q)
                || provider.toLowerCase().includes(q);
        },
    }">
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">
            Bookings
            <span class="page-title-count">({{ $summary['count'] }} bookings · {{ $summary['spend'] }} total spend)</span>
        </h2>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    <div class="admin-po-toolbar admin-co-bk-toolbar">
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
            <button type="button" class="admin-po-tool-btn" aria-label="Filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none" aria-hidden="true">
                    <path d="M1 2H13M3 6H11M5 10H9" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                </svg>
                <span>Filter</span>
            </button>
        </div>
    </div>

    <div class="admin-co-bk-list">
        @foreach ($bookings as $booking)
        @php
            $petLabel = $booking['pet_name'] . ' · ' . $booking['pet_breed'];
        @endphp
        <article
            class="admin-co-bk-card {{ $booking['status'] === 'disputed' ? 'is-disputed' : '' }}"
            x-show="matchesBooking(
                @js($booking['status']),
                @js($booking['id']),
                @js($petLabel),
                @js($booking['service']),
                @js($booking['provider'])
            )"
            x-cloak>
            <div class="admin-co-bk-card-head">
                <p class="admin-co-bk-meta">
                    @if ($booking['status'] === 'disputed')
                    <span class="admin-co-bk-warn" aria-label="Disputed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                            <path d="M5.485 0.308055C5.24845 -0.102644 4.61103 -0.102644 4.37448 0.308055L0.0743152 7.77965L0.0386903 7.85279C-0.0996183 8.19795 0.147844 8.57813 0.542967 8.62728L0.630186 8.63238H9.2293C9.70357 8.63238 10.0075 8.1663 9.78517 7.77965L5.485 0.308055Z" fill="#FFC97A" />
                            <path d="M4.8365 3.15331L4.9516 5.59952L5.06649 3.15431C5.0672 3.13868 5.06471 3.12306 5.05918 3.10842C5.05365 3.09379 5.0452 3.08043 5.03433 3.06917C5.02347 3.05791 5.01042 3.04898 4.99599 3.04294C4.98155 3.03689 4.96604 3.03385 4.95039 3.034C4.93502 3.03415 4.91983 3.03738 4.90573 3.0435C4.89162 3.04962 4.87888 3.0585 4.86827 3.06962C4.85765 3.08074 4.84937 3.09387 4.84392 3.10825C4.83846 3.12262 4.83594 3.13794 4.8365 3.15331Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5.00879 6.66028C5.03629 6.67172 5.05962 6.69118 5.07617 6.71594C5.09273 6.74076 5.10156 6.77009 5.10156 6.79993C5.10147 6.83985 5.08586 6.87813 5.05762 6.90637C5.02938 6.93461 4.9911 6.95023 4.95117 6.95032C4.92134 6.95032 4.892 6.94149 4.86719 6.92493C4.84242 6.90838 4.82297 6.88504 4.81152 6.85754C4.80009 6.82995 4.79691 6.79895 4.80273 6.76965C4.80862 6.74053 4.82274 6.71352 4.84375 6.6925C4.86476 6.67149 4.89178 6.65737 4.9209 6.65149C4.95019 6.64566 4.98119 6.64885 5.00879 6.66028Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" />
                        </svg>
                    </span>
                    @endif
                    <span class="admin-co-bk-meta-label">Booking ID</span>
                    <span class="admin-co-bk-meta-value">{{ $booking['id'] }}</span>
                </p>
                <p class="admin-co-bk-meta">
                    <span class="admin-co-bk-meta-label">Date</span>
                    <span class="admin-co-bk-meta-value">{{ $booking['date'] }}</span>
                </p>
            </div>

            <div class="admin-co-bk-card-body">
                <div class="admin-co-bk-col admin-co-bk-col--pet">
                    <div class="admin-co-bk-pet">
                        <img src="{{ $booking['pet_image'] }}" alt="" class="admin-co-bk-pet-avatar" width="40" height="40">
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label">
                                <span class="admin-co-pet-type-icon" aria-hidden="true">
                                    @if (($booking['pet_type'] ?? '') === 'cat')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 16 22" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.0661 3.58301C7.82655 3.48924 8.87949 3.51286 9.88642 3.85352C10.9998 4.23021 12.075 5.00401 12.6013 6.43945L14.7712 7.47949L14.8181 7.64941C15.1058 8.68692 15.2764 10.2987 14.8308 11.7656C14.6062 12.5047 14.2222 13.2153 13.6101 13.791C12.9963 14.3682 12.1714 14.7931 11.0934 14.9854C7.21531 15.6771 5.01491 18.9931 4.4079 20.5596C4.15869 21.2733 2.6782 21.455 2.32978 20.7842C-2.87181 10.7633 1.80671 2.85095 5.03583 0L7.0661 3.58301ZM9.46845 7.20898C8.8993 7.20899 8.29571 7.49133 8.2956 8.62109C8.2956 9.40106 9.28944 8.62143 9.9372 8.62109C10.585 8.62109 10.6413 9.40123 10.6413 8.62109C10.6412 7.84111 10.1161 7.20898 9.46845 7.20898Z" fill="#FFC97A" />
                                    </svg>
                                    @elseif (($booking['pet_type'] ?? '') === 'other')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="11" viewBox="0 0 22 20" fill="none">
                                        <path d="M11 7.89474C7.68219 7.89474 4.87876 10.8058 3.97362 14.5447C3.57552 16.1889 4.17581 17.9342 5.64929 18.7542C6.81738 19.4042 8.55486 20 11 20C13.4451 20 15.1831 19.4042 16.3512 18.7542C17.8247 17.9342 18.4245 16.1889 18.0264 14.5447C17.1212 10.8053 14.3178 7.89474 11 7.89474ZM0 7.07579C0 8.52947 0.937619 10 2.09524 10C3.25286 10 4.19048 8.52947 4.19048 7.07579C4.19048 5.62211 3.25286 4.73684 2.09524 4.73684C0.937619 4.73684 0 5.62263 0 7.07579ZM22 7.07579C22 8.52947 21.0624 10 19.9048 10C18.7471 10 17.8095 8.52947 17.8095 7.07579C17.8095 5.62211 18.7471 4.73684 19.9048 4.73684C21.0624 4.73684 22 5.62263 22 7.07579ZM5.5 2.33895C5.5 3.79263 6.43762 5.26316 7.59524 5.26316C8.75286 5.26316 9.69048 3.79263 9.69048 2.33895C9.69048 0.885263 8.75286 0 7.59524 0C6.43762 0 5.5 0.88579 5.5 2.33895ZM16.5 2.33895C16.5 3.79263 15.5624 5.26316 14.4048 5.26316C13.2471 5.26316 12.3095 3.79263 12.3095 2.33895C12.3095 0.885263 13.2471 0 14.4048 0C15.5624 0 16.5 0.88579 16.5 2.33895Z" fill="#FFC97A" />
                                    </svg>
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 22 21" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 8.68947e-10C12.0763 -1.81862e-05 12.6594 0.285457 13.0383 0.772461L16.2532 4.90625C16.4122 5.11071 16.6452 5.2455 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01399 20.759 6.35742C21.0768 7.32445 21.6377 9.33341 21.2551 10.5C20.8003 11.8863 20.0704 12.5797 18.7551 12.9189C16.5021 13.4997 14.639 12.8357 12.4377 14.5137C11.758 15.0319 11.2942 15.7103 10.9895 16.4678C9.95215 19.0461 6.72476 21.706 4.32933 20.2969L1.40648 18.5781L2.88597 12.9932C3.03732 12.9827 3.18549 12.9709 3.3264 12.9531C3.72901 12.9023 4.11587 12.8149 4.36937 12.6543C4.57272 12.5254 4.78068 12.3019 4.97777 12.0498C5.17869 11.7928 5.38391 11.4855 5.57835 11.168C5.96743 10.5326 6.32411 9.84073 6.53441 9.39648C6.59343 9.27173 6.53998 9.12257 6.41527 9.06348C6.29079 9.00495 6.1424 9.05746 6.08324 9.18164C5.87884 9.61348 5.5304 10.2892 5.15257 10.9062C4.96359 11.2149 4.7693 11.5055 4.58421 11.7422C4.39522 11.9839 4.2301 12.1511 4.10179 12.2324C3.94887 12.3293 3.65899 12.4072 3.2639 12.457C2.87954 12.5055 2.43079 12.5234 1.98949 12.5225C1.58427 12.5216 1.18933 12.5013 0.862533 12.4795C0.853021 12.4768 0.842688 12.4744 0.833236 12.4717C0.25988 12.3087 -0.117659 11.685 0.0334314 11.1084C1.50838 5.48351 2.3485 2.92214 3.76585 1.50488C5.2619 0.00933487 8.24416 5.75404e-05 8.28148 8.68947e-10H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47081 11.6506 6.59876 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72589 12.5754 5.0178 11.8508 5.01758Z" fill="#FFC97A" />
                                    </svg>
                                    @endif
                                </span>
                                Pet
                            </span>
                            <span class="admin-co-bk-field-value">{{ $booking['pet_name'] }} · {{ $booking['pet_breed'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-co-bk-col">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M2.5 9.5L4 4L7.5 7.5L9.5 2.5" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Service
                        </span>
                        <span class="admin-co-bk-field-value">{{ $booking['service'] }}</span>
                    </div>
                </div>

                <div class="admin-co-bk-col admin-co-bk-col--provider">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="12" viewBox="0 0 10 12" fill="none" aria-hidden="true">
                                <path d="M5 6.25C5.82843 6.25 6.5 5.57843 6.5 4.75C6.5 3.92157 5.82843 3.25 5 3.25C4.17157 3.25 3.5 3.92157 3.5 4.75C3.5 5.57843 4.17157 6.25 5 6.25Z" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M5 11C7 8.5 9 6.76142 9 4.75C9 2.67893 7.20914 1 5 1C2.79086 1 1 2.67893 1 4.75C1 6.76142 3 8.5 5 11Z" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ $booking['provider_role'] }}
                        </span>
                        <span class="admin-co-bk-field-value">{{ $booking['provider'] }}</span>
                    </div>
                </div>

                <div class="admin-co-bk-col admin-co-bk-col--rating">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label">Rating</span>
                        @if ($booking['rating'] === null)
                        <span class="admin-co-bk-field-value is-muted">—</span>
                        @else
                        <span class="admin-co-pet-stars" aria-label="{{ $booking['rating'] }} out of 5 stars">
                            @for ($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="{{ $i <= $booking['rating'] ? '#FFC97A' : '#D4D2CF' }}" />
                            </svg>
                            @endfor
                        </span>
                        @endif
                    </div>
                </div>

                <div class="admin-co-bk-col admin-co-bk-col--total">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label">Total</span>
                        <span class="admin-co-bk-total">{{ $booking['amount'] }}</span>
                    </div>
                </div>

                <div class="admin-co-bk-col admin-co-bk-col--status">
                    <span class="admin-co-booking-status is-{{ $booking['status'] }} is-pill">
                        {{ $booking['status_label'] }}
                    </span>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    <div class="admin-po-table-footer">
        <p class="admin-table-count">Showing 1–{{ count($bookings) }} of {{ $summary['count'] }} bookings</p>
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
            <span class="admin-po-page-ellipsis" aria-hidden="true">…</span>
            <button type="button" class="admin-po-page">100</button>
            <button type="button" class="admin-po-page-arrow" aria-label="Next page">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                    <circle cx="16" cy="16" r="16" fill="#F3F3F3" />
                    <path d="M14 21L19.0343 15.9657L14.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </nav>
    </div>
</div>
