@php
    $customers = [
        ['name' => 'Jane Doe', 'id' => 'USR-01452', 'email' => 'janed@gmail.com', 'region' => 'Manchester', 'status' => 'active', 'joined' => '12-03-2024', 'bookings' => 18, 'spend' => '£1,240.00', 'verified' => true, 'pets' => 2, 'last' => 'Today'],
        ['name' => 'Tom Harris', 'id' => 'USR-01488', 'email' => 'tom.h@outlook.com', 'region' => 'Bristol', 'status' => 'active', 'joined' => '04-01-2024', 'bookings' => 7, 'spend' => '£420.50', 'verified' => true, 'pets' => 1, 'last' => 'Yesterday'],
        ['name' => 'Sarah Bell', 'id' => 'USR-01502', 'email' => 's.chen@icloud.com', 'region' => 'London', 'status' => 'suspended', 'joined' => '22-11-2023', 'bookings' => 3, 'spend' => '£95.00', 'verified' => false, 'pets' => 1, 'last' => '12-08-2025'],
        ['name' => 'Alex Rivera', 'id' => 'USR-01519', 'email' => 'alex.r@gmail.com', 'region' => 'Leeds', 'status' => 'active', 'joined' => '09-06-2024', 'bookings' => 24, 'spend' => '£2,180.75', 'verified' => true, 'pets' => 3, 'last' => 'Today'],
        ['name' => 'Priya Kapoor', 'id' => 'USR-01544', 'email' => 'priya.k@yahoo.com', 'region' => 'Birmingham', 'status' => 'deactivated', 'joined' => '18-02-2023', 'bookings' => 11, 'spend' => '£680.20', 'verified' => true, 'pets' => 2, 'last' => '03-01-2025'],
        ['name' => 'Nicole S', 'id' => 'USR-01561', 'email' => 'nicole.s@gmail.com', 'region' => 'Edinburgh', 'status' => 'flagged', 'joined' => '30-07-2024', 'bookings' => 5, 'spend' => '£250.48', 'verified' => true, 'pets' => 1, 'last' => '01-02-2025'],
        ['name' => 'James Turner', 'id' => 'USR-01577', 'email' => 'j.turner@mail.com', 'region' => 'Liverpool', 'status' => 'active', 'joined' => '14-09-2023', 'bookings' => 15, 'spend' => '£990.00', 'verified' => true, 'pets' => 2, 'last' => 'Today'],
        ['name' => 'Emily Watson', 'id' => 'USR-01590', 'email' => 'emily.w@gmail.com', 'region' => 'Cardiff', 'status' => 'unverified', 'joined' => '02-08-2025', 'bookings' => 0, 'spend' => '£0.00', 'verified' => false, 'pets' => 1, 'last' => '02-08-2025'],
        ['name' => 'Chris Nolan', 'id' => 'USR-01605', 'email' => 'chris.n@proton.me', 'region' => 'Glasgow', 'status' => 'active', 'joined' => '21-05-2024', 'bookings' => 9, 'spend' => '£510.30', 'verified' => true, 'pets' => 1, 'last' => 'Yesterday'],
        ['name' => 'Mia Brooks', 'id' => 'USR-01622', 'email' => 'mia.brooks@gmail.com', 'region' => 'Oxford', 'status' => 'active', 'joined' => '11-12-2023', 'bookings' => 21, 'spend' => '£1,560.90', 'verified' => true, 'pets' => 4, 'last' => 'Today'],
    ];

    $statusLabels = [
        'active' => 'Active',
        'suspended' => 'Suspended',
        'flagged' => 'Flagged',
        'deactivated' => 'Deactivated',
        'unverified' => 'Unverified',
    ];
@endphp

<div class="admin-pet-owners" x-data="{
    section: 'customers',
    statusFilter: 'all',
    search: '',
}">
    {{-- Section tabs: All customers / Disputes / Support tickets --}}
    <nav class="admin-po-sections" aria-label="Pet owners sections">
        <button type="button"
            class="admin-po-section"
            :class="{ 'is-active': section === 'customers' }"
            @click="section = 'customers'">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                <path d="M0.5 10.5V9.94445C0.5 8.10556 1.99444 6.61111 3.83333 6.61111H6.05555C7.89444 6.61111 9.38889 8.10556 9.38889 9.94445V10.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4.94439 4.94444C3.71661 4.94444 2.72217 3.95 2.72217 2.72222C2.72217 1.49444 3.71661 0.5 4.94439 0.5C6.17217 0.5 7.16661 1.49444 7.16661 2.72222C7.16661 3.95 6.17217 4.94444 4.94439 4.94444Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>All customers</span>
            <span class="admin-po-section-count">2,847</span>
        </button>

        <button type="button"
            class="admin-po-section"
            :class="{ 'is-active': section === 'disputes' }"
            @click="section = 'disputes'">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                <path d="M8.46284 4.45985C8.68634 4.23631 9.04922 4.23631 9.27273 4.45985L9.6074 4.79406C9.83076 5.01757 9.83075 5.38061 9.6074 5.60413L7.55641 7.65714L7.51292 7.69639C7.30309 7.86759 6.99991 7.86748 6.79001 7.69639L6.74652 7.65714L6.41185 7.32293V7.3208C6.18846 7.09725 6.18838 6.73424 6.41185 6.51074L8.46284 4.45985ZM8.88873 4.84393C8.87732 4.83251 8.85825 4.83251 8.84683 4.84393L6.79585 6.89482C6.78449 6.90618 6.78456 6.92532 6.79585 6.93673L6.95496 7.09587V7.09747L7.13052 7.27306C7.14188 7.2843 7.16049 7.28424 7.17189 7.27306L9.22287 5.22005C9.23413 5.20867 9.23467 5.18951 9.2234 5.17814L8.88873 4.84393ZM4.17152 0.167621C4.39501 -0.0558764 4.75791 -0.0558708 4.98141 0.167621L5.31608 0.503954C5.53938 0.727479 5.53944 1.09053 5.31608 1.31402L3.26509 3.36544L3.2216 3.4047C3.01172 3.57584 2.70803 3.57587 2.49816 3.4047L2.45467 3.36544L2.12053 3.0307C1.89707 2.80716 1.89705 2.44416 2.12053 2.22063L4.17152 0.167621ZM4.56506 0.545333L4.55551 0.551699L2.50453 2.60471C2.49314 2.6161 2.49316 2.63522 2.50453 2.64662L2.8392 2.98083C2.85061 2.99223 2.86969 2.99224 2.8811 2.98083L4.93155 0.929941C4.94295 0.918545 4.94295 0.899427 4.93155 0.888032L4.93102 0.886971L4.59741 0.551699C4.58881 0.543136 4.5758 0.540971 4.56506 0.545333Z" fill="currentColor"/>
                <path d="M5.37011 5.41983L1.22195 9.56878L1.16785 9.61758C0.905988 9.83135 0.527903 9.8312 0.264595 9.61758L0.209965 9.56878C-0.0531755 9.30728 -0.0682516 8.89332 0.160639 8.61125L0.209965 8.55661L4.35813 4.40766L5.37011 5.41983ZM0.593966 8.94068C0.52456 9.01176 0.528234 9.11911 0.592375 9.18311C0.661971 9.25227 0.771776 9.25088 0.837945 9.1847L4.60211 5.41983L4.35813 5.17581L0.593966 8.94068Z" fill="currentColor"/>
                <path d="M5.8215 5.90107L5.43744 6.28517L3.50892 4.35641L3.89297 3.97231L5.8215 5.90107ZM7.8134 3.90892L5.88488 1.98016L3.50892 4.35641L3.47141 4.31515C3.30914 4.11606 3.30915 3.82856 3.47141 3.62947L3.50892 3.58821L5.50083 1.59605C5.71293 1.38392 6.05683 1.38392 6.26894 1.59605L8.19746 3.52482C8.40957 3.73695 8.40957 4.08089 8.19746 4.29302L6.20555 6.28517L6.1643 6.32268C5.96517 6.485 5.67776 6.48511 5.4787 6.32268L5.43744 6.28517L7.8134 3.90892Z" fill="currentColor"/>
                <path d="M9.72844 9.45678C9.87831 9.4569 10 9.57846 10 9.72839C10 9.87832 9.87831 9.99988 9.72844 10H5.15013C5.00015 10 4.87857 9.8784 4.87857 9.72839C4.87857 9.57838 5.00015 9.45678 5.15013 9.45678H9.72844Z" fill="currentColor"/>
            </svg>
            <span>Disputes</span>
            <span class="admin-po-section-count">25</span>
        </button>

        <button type="button"
            class="admin-po-section"
            :class="{ 'is-active': section === 'tickets' }"
            @click="section = 'tickets'">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="9" viewBox="0 0 13 9" fill="none" aria-hidden="true">
                <path d="M7.1 0.5V1.64286M7.1 7.35714V8.5M7.1 3.92857V5.07143M0.5 2.78571C0.97739 2.78571 1.43523 2.96633 1.77279 3.28782C2.11036 3.60931 2.3 4.04534 2.3 4.5C2.3 4.95466 2.11036 5.39069 1.77279 5.71218C1.43523 6.03367 0.97739 6.21429 0.5 6.21429V7.35714C0.5 7.66025 0.626428 7.95094 0.851472 8.16527C1.07652 8.37959 1.38174 8.5 1.7 8.5H11.3C11.6183 8.5 11.9235 8.37959 12.1485 8.16527C12.3736 7.95094 12.5 7.66025 12.5 7.35714V6.21429C12.0226 6.21429 11.5648 6.03367 11.2272 5.71218C10.8896 5.39069 10.7 4.95466 10.7 4.5C10.7 4.04534 10.8896 3.60931 11.2272 3.28782C11.5648 2.96633 12.0226 2.78571 12.5 2.78571V1.64286C12.5 1.33975 12.3736 1.04906 12.1485 0.834735C11.9235 0.620408 11.6183 0.5 11.3 0.5H1.7C1.38174 0.5 1.07652 0.620408 0.851472 0.834735C0.626428 1.04906 0.5 1.33975 0.5 1.64286V2.78571Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Support tickets</span>
            <span class="admin-po-section-count">9</span>
        </button>
    </nav>

    {{-- All customers content --}}
    <div class="admin-po-customers" x-show="section === 'customers'" x-cloak>
        <div class="admin-po-head">
            <h1 class="admin-page-title mb-0">All Customers</h1>
            <p class="admin-section-label mb-0">2,847 users — 12 new today</p>
        </div>

        <div class="admin-po-metric-row">
            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Total customers</p>
                    <span class="admin-po-metric-icon is-up" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M4 10L10 4M10 4H5.5M10 4V8.5" stroke="#A7C569" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">2,847</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">+47</span>
                    <span class="admin-metric-note">this week</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Daily active users</p>
                <p class="admin-card-value">284</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot"></span>updated now - 15:30
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Suspended or flagged</p>
                    <span class="admin-po-metric-icon is-flag" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none">
                            <path d="M1.5 12.5V1.5H10.5L8.5 5L10.5 8.5H1.5" stroke="#FBAC83" stroke-width="1.2" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">52</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-warn"></span>38 suspended · 14 flagged
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Verified users</p>
                <p class="admin-card-value">2,245</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">80.1%</span>
                    <span class="admin-metric-note">verification rate</span>
                </div>
            </div>
        </div>

        <div class="admin-po-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Customer status filters">
                <button type="button"
                    class="admin-po-filter is-all"
                    :class="{ 'is-active': statusFilter === 'all' }"
                    @click="statusFilter = 'all'">
                    All (2,847)
                </button>
                <button type="button"
                    class="admin-po-filter is-active-status"
                    :class="{ 'is-active': statusFilter === 'active' }"
                    @click="statusFilter = 'active'">
                    Active
                </button>
                <button type="button"
                    class="admin-po-filter is-suspended"
                    :class="{ 'is-active': statusFilter === 'suspended' }"
                    @click="statusFilter = 'suspended'">
                    Suspended (38)
                </button>
                <button type="button"
                    class="admin-po-filter is-flagged"
                    :class="{ 'is-active': statusFilter === 'flagged' }"
                    @click="statusFilter = 'flagged'">
                    Flagged (14)
                </button>
                <button type="button"
                    class="admin-po-filter is-deactivated"
                    :class="{ 'is-active': statusFilter === 'deactivated' }"
                    @click="statusFilter = 'deactivated'">
                    Deactivated (325)
                </button>
                <button type="button"
                    class="admin-po-filter is-unverified"
                    :class="{ 'is-active': statusFilter === 'unverified' }"
                    @click="statusFilter = 'unverified'">
                    Unverified (5)
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                        <circle cx="6" cy="6" r="4.25" stroke="#9C9A97" stroke-width="1.2"/>
                        <path d="M9.25 9.25L12 12" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                    <input type="search"
                        placeholder="Search name, email, ID ..."
                        x-model="search"
                        aria-label="Search customers">
                </label>

                <button type="button" class="admin-po-tool-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                        <rect x="2.5" y="2.5" width="9" height="9" rx="1.5" stroke="#3B3731" stroke-width="1.1"/>
                        <path d="M2.5 5.5H11.5" stroke="#3B3731" stroke-width="1.1"/>
                        <path d="M5.5 2.5V5.5" stroke="#3B3731" stroke-width="1.1"/>
                    </svg>
                    <span>Date range</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="admin-card admin-po-table-card">
            <div class="admin-table-wrap">
                <table class="admin-live-table admin-po-table">
                    <thead>
                        <tr>
                            <th class="admin-po-check-col">
                                <input type="checkbox" class="admin-po-check" aria-label="Select all customers">
                            </th>
                            <th>Name · User ID <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Email Address <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Region <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Date Joined <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Total Bookings <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Total Spend <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Verified <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Total Pets <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                            <th>Last Active <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round"/></svg></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr
                                x-show="(statusFilter === 'all' || statusFilter === '{{ $customer['status'] }}') &&
                                    (search === '' ||
                                     '{{ strtolower($customer['name']) }}'.includes(search.toLowerCase()) ||
                                     '{{ strtolower($customer['email']) }}'.includes(search.toLowerCase()) ||
                                     '{{ strtolower($customer['id']) }}'.includes(search.toLowerCase()))"
                            >
                                <td class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select {{ $customer['name'] }}">
                                </td>
                                <td>
                                    <span class="admin-po-name-cell">
                                        @if ($customer['status'] === 'flagged')
                                            <span class="admin-po-flag" aria-label="Flagged" title="Flagged">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="12" viewBox="0 0 12 14" fill="none" aria-hidden="true">
                                                    <path d="M1.5 12.5V1.5H10.5L8.5 5L10.5 8.5H1.5" stroke="#FBAC83" stroke-width="1.2" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                        @endif
                                        <span class="admin-po-name">{{ $customer['name'] }}</span>
                                        <span class="admin-po-name-sep">·</span>
                                        <span class="admin-po-user-id">{{ $customer['id'] }}</span>
                                    </span>
                                </td>
                                <td>{{ $customer['email'] }}</td>
                                <td>{{ $customer['region'] }}</td>
                                <td>
                                    <span class="admin-po-status is-{{ $customer['status'] }}">
                                        {{ $statusLabels[$customer['status']] }}
                                    </span>
                                </td>
                                <td>{{ $customer['joined'] }}</td>
                                <td>{{ $customer['bookings'] }}</td>
                                <td>{{ $customer['spend'] }}</td>
                                <td>
                                    @if ($customer['verified'])
                                        <span class="admin-po-verified" aria-label="Verified">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                <path d="M3 7.2L5.8 10L11 4" stroke="#A7C569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="admin-po-unverified">—</span>
                                    @endif
                                </td>
                                <td>{{ $customer['pets'] }}</td>
                                <td>{{ $customer['last'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-po-table-footer">
                <p class="admin-table-count">Showing 1–25 of 4,218 customers</p>
                <nav class="admin-po-pagination" aria-label="Customer pagination">
                    <button type="button" class="admin-po-page-arrow" aria-label="Previous page" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none">
                            <path d="M6.5 1L1.5 6L6.5 11" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none">
                            <path d="M1.5 1L6.5 6L1.5 11" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <div x-show="section === 'disputes'" x-cloak class="admin-po-placeholder">
        <h1 class="admin-page-title">Disputes</h1>
        <p>Dispute management will be available here.</p>
    </div>

    <div x-show="section === 'tickets'" x-cloak class="admin-po-placeholder">
        <h1 class="admin-page-title">Support tickets</h1>
        <p>Support ticket management will be available here.</p>
    </div>
</div>
