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

$disputes = [
['id' => 'DS-00063', 'customer' => 'Jane Doe', 'provider' => 'Pawfect Salon', 'provider_type' => 'groomer', 'booking' => 'GS-0563-B12', 'reason' => 'Full Groom - Late arrival + incomplete service', 'status' => 'unassigned', 'assigned' => '—', 'days' => 22, 'value' => '£55.00', 'due' => '8 days'],
['id' => 'DS-00062', 'customer' => 'Tom Harris', 'provider' => 'Furs & Co. Studio', 'provider_type' => 'space', 'booking' => 'SS-0412-B08', 'reason' => 'Day Care - Pet returned with minor injury', 'status' => 'under_review', 'assigned' => 'Michelle M', 'days' => 18, 'value' => '£80.00', 'due' => '5 days'],
['id' => 'DS-00061', 'customer' => 'Sarah Chen', 'provider' => 'Pawfect Salon', 'provider_type' => 'groomer', 'booking' => 'GS-0499-B03', 'reason' => 'Nail Trim - Overcharged for add-on', 'status' => 'unassigned', 'assigned' => '—', 'days' => 16, 'value' => '£25.00', 'due' => '6 days'],
['id' => 'DS-00060', 'customer' => 'Alex Rivera', 'provider' => 'Furs & Co. Studio', 'provider_type' => 'space', 'booking' => 'SS-0388-B21', 'reason' => 'Overnight Stay - Cancellation dispute', 'status' => 'under_review', 'assigned' => 'Ben M', 'days' => 14, 'value' => '£120.00', 'due' => '10 days'],
['id' => 'DS-00059', 'customer' => 'Priya Kapoor', 'provider' => 'Pawfect Salon', 'provider_type' => 'groomer', 'booking' => 'GS-0521-B17', 'reason' => 'Full Groom - Coat damage after service', 'status' => 'under_review', 'assigned' => 'Michelle M', 'days' => 12, 'value' => '£65.00', 'due' => '9 days'],
['id' => 'DS-00058', 'customer' => 'Nicole S', 'provider' => 'Furs & Co. Studio', 'provider_type' => 'space', 'booking' => 'SS-0401-B05', 'reason' => 'Half Day - Facility access issue', 'status' => 'unassigned', 'assigned' => '—', 'days' => 9, 'value' => '£40.00', 'due' => '12 days'],
['id' => 'DS-00057', 'customer' => 'James Turner', 'provider' => 'Pawfect Salon', 'provider_type' => 'groomer', 'booking' => 'GS-0555-B09', 'reason' => 'Puppy Intro - No-show by provider', 'status' => 'under_review', 'assigned' => 'Ben M', 'days' => 7, 'value' => '£45.00', 'due' => '14 days'],
['id' => 'DS-00056', 'customer' => 'Emily Watson', 'provider' => 'Furs & Co. Studio', 'provider_type' => 'space', 'booking' => 'SS-0375-B14', 'reason' => 'Full Day - Double booking conflict', 'status' => 'unassigned', 'assigned' => '—', 'days' => 4, 'value' => '£90.00', 'due' => '15 days'],
];

$disputeStatusLabels = [
'unassigned' => 'Unassigned',
'under_review' => 'Under review',
'assigned_to_me' => 'Assigned to me',
];

$tickets = [
['id' => 'SPRT-00412', 'customer' => 'Jane Doe', 'user_id' => 'USR-01452', 'subject' => 'Unable to reschedule upcoming booking for Luna…', 'status' => 'open', 'assigned' => '—', 'category' => 'Booking', 'opened' => '22 days ago', 'opened_days' => 22, 'due' => '8 days'],
['id' => 'SPRT-00411', 'customer' => 'Tom Harris', 'user_id' => 'USR-01488', 'subject' => 'Payment failed but booking still shows as confirmed…', 'status' => 'in_progress', 'assigned' => 'Michelle M', 'category' => 'Payment', 'opened' => '18 days ago', 'opened_days' => 18, 'due' => '5 days'],
['id' => 'SPRT-00410', 'customer' => 'Sarah Chen', 'user_id' => 'USR-01502', 'subject' => 'Need help verifying my account after email change…', 'status' => 'reopened', 'assigned' => 'Ben M', 'category' => 'Account', 'opened' => '16 days ago', 'opened_days' => 16, 'due' => '3 days'],
['id' => 'SPRT-00409', 'customer' => 'Alex Rivera', 'user_id' => 'USR-01519', 'subject' => 'Refund not received after cancelled space booking…', 'status' => 'resolved', 'assigned' => 'Michelle M', 'category' => 'Payment', 'opened' => '12 days ago', 'opened_days' => 12, 'due' => '0 days'],
['id' => 'SPRT-00408', 'customer' => 'Priya Kapoor', 'user_id' => 'USR-01544', 'subject' => 'Cannot upload pet vaccination documents…', 'status' => 'in_progress', 'assigned' => 'Ben M', 'category' => 'Account', 'opened' => '9 days ago', 'opened_days' => 9, 'due' => '6 days'],
['id' => 'SPRT-00407', 'customer' => 'Nicole S', 'user_id' => 'USR-01561', 'subject' => 'Wrong service listed on my booking confirmation…', 'status' => 'open', 'assigned' => '—', 'category' => 'Booking', 'opened' => '7 days ago', 'opened_days' => 7, 'due' => '10 days'],
['id' => 'SPRT-00406', 'customer' => 'James Turner', 'user_id' => 'USR-01577', 'subject' => 'App keeps logging me out on mobile…', 'status' => 'closed', 'assigned' => 'Michelle M', 'category' => 'Account', 'opened' => '5 days ago', 'opened_days' => 5, 'due' => '0 days'],
['id' => 'SPRT-00405', 'customer' => 'Emily Watson', 'user_id' => 'USR-01590', 'subject' => 'Promo code not applying at checkout…', 'status' => 'resolved', 'assigned' => 'Ben M', 'category' => 'Payment', 'opened' => '3 days ago', 'opened_days' => 3, 'due' => '0 days'],
['id' => 'SPRT-00404', 'customer' => 'Chris Nolan', 'user_id' => 'USR-01605', 'subject' => 'Provider cancelled last minute with no notice…', 'status' => 'in_progress', 'assigned' => 'Michelle M', 'category' => 'Booking', 'opened' => '2 days ago', 'opened_days' => 2, 'due' => '12 days'],
['id' => 'SPRT-00403', 'customer' => 'Mia Brooks', 'user_id' => 'USR-01622', 'subject' => 'Request to update billing address on file…', 'status' => 'open', 'assigned' => '—', 'category' => 'Account', 'opened' => '1 day ago', 'opened_days' => 1, 'due' => '14 days'],
];

$ticketStatusLabels = [
'open' => 'Open',
'in_progress' => 'In progress',
'resolved' => 'Resolved',
'closed' => 'Closed',
'reopened' => 'Re-opened',
];
@endphp

<div class="admin-pet-owners" x-data="{
    view: 'list',
    selectedCustomerId: null,
    detailTab: 'overview',
    previousTab: null,
    selectedPetId: null,
    selectedBookingId: null,
    // Remember scroll position per tab (first visit = top)
    tabScroll: {},
    detailTabLabels: {
        overview: 'Overview',
        pets: 'Pets',
        bookings: 'Bookings',
        payments: 'Payments',
        support: 'Support',
        referrals: 'Referrals',
        activity: 'Activity',
    },
    section: 'customers',
    statusFilter: 'all',
    search: '',
    disputeFilter: 'all',
    disputeSearch: '',
    ticketFilter: 'all',
    ticketSearch: '',

    // Mass-select for export
    selectedIds: [],
    allIds: @js(collect($customers)->pluck('id')->values()),

    openCustomer(id) {
        this.selectedCustomerId = id;
        this.detailTab = 'overview';
        this.previousTab = null;
        this.selectedPetId = null;
        this.selectedBookingId = null;
        this.tabScroll = {};
        this.view = 'detail';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    closeCustomer() {
        this.view = 'list';
        this.selectedCustomerId = null;
        this.detailTab = 'overview';
        this.previousTab = null;
        this.selectedPetId = null;
        this.selectedBookingId = null;
        this.tabScroll = {};
    },
    switchDetailTab(tab) {
        if (tab === this.detailTab) return;
        if (this.selectedBookingId) {
            this.selectedBookingId = null;
            this.$dispatch('admin-booking-close-request');
        }
        // Save where we were on the current tab
        this.tabScroll[this.detailTab] = window.scrollY;
        this.previousTab = this.detailTab;
        this.detailTab = tab;
        // Restore saved position, or start at top if first visit
        const y = this.tabScroll[tab] ?? 0;
        this.$nextTick(() => {
            window.scrollTo({ top: y, behavior: 'auto' });
        });
    },
    goBack() {
        if (this.selectedBookingId) {
            this.selectedBookingId = null;
            this.$dispatch('admin-booking-close-request');
            return;
        }
        if (this.previousTab) {
            this.tabScroll[this.detailTab] = window.scrollY;
            const target = this.previousTab;
            this.previousTab = null;
            this.detailTab = target;
            const y = this.tabScroll[target] ?? 0;
            this.$nextTick(() => {
                window.scrollTo({ top: y, behavior: 'auto' });
            });
            return;
        }
        this.closeCustomer();
    },
    get backLabel() {
        if (this.selectedBookingId) return 'BOOKINGS';
        if (this.previousTab && this.detailTabLabels[this.previousTab]) {
            return this.detailTabLabels[this.previousTab].toUpperCase();
        }
        return 'ALL CUSTOMERS';
    },

    isSelected(id) {
        return this.selectedIds.includes(id);
    },
    toggleOne(id) {
        if (this.isSelected(id)) {
            this.selectedIds = this.selectedIds.filter(x => x !== id);
        } else {
            this.selectedIds.push(id);
        }
    },
    get allSelected() {
        return this.selectedIds.length === this.allIds.length;
    },
    toggleAll() {
        this.selectedIds = this.allSelected ? [] : [...this.allIds];
    },
}"
@admin-pet-selected.window="selectedPetId = $event.detail.id"
@admin-booking-selected.window="selectedBookingId = $event.detail.booking?.id || null"
@admin-booking-closed.window="selectedBookingId = null">
    @include('admin.tabs.customer-detail')

    <div class="admin-po-list" x-show="view === 'list'">
    {{-- Section tabs: All customers / Disputes / Support tickets --}}
    <div class="admin-po-sections-bar">
        <nav class="admin-po-sections" aria-label="Pet owners sections">
            <button type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === 'customers' }"
                @click="section = 'customers'">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                    <path d="M0.5 10.5V9.94445C0.5 8.10556 1.99444 6.61111 3.83333 6.61111H6.05555C7.89444 6.61111 9.38889 8.10556 9.38889 9.94445V10.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4.94439 4.94444C3.71661 4.94444 2.72217 3.95 2.72217 2.72222C2.72217 1.49444 3.71661 0.5 4.94439 0.5C6.17217 0.5 7.16661 1.49444 7.16661 2.72222C7.16661 3.95 6.17217 4.94444 4.94439 4.94444Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>All customers</span>
                <span class="admin-po-section-count">2,847</span>
            </button>

            <button type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === 'disputes' }"
                @click="section = 'disputes'">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                    <path d="M8.46284 4.45985C8.68634 4.23631 9.04922 4.23631 9.27273 4.45985L9.6074 4.79406C9.83076 5.01757 9.83075 5.38061 9.6074 5.60413L7.55641 7.65714L7.51292 7.69639C7.30309 7.86759 6.99991 7.86748 6.79001 7.69639L6.74652 7.65714L6.41185 7.32293V7.3208C6.18846 7.09725 6.18838 6.73424 6.41185 6.51074L8.46284 4.45985ZM8.88873 4.84393C8.87732 4.83251 8.85825 4.83251 8.84683 4.84393L6.79585 6.89482C6.78449 6.90618 6.78456 6.92532 6.79585 6.93673L6.95496 7.09587V7.09747L7.13052 7.27306C7.14188 7.2843 7.16049 7.28424 7.17189 7.27306L9.22287 5.22005C9.23413 5.20867 9.23467 5.18951 9.2234 5.17814L8.88873 4.84393ZM4.17152 0.167621C4.39501 -0.0558764 4.75791 -0.0558708 4.98141 0.167621L5.31608 0.503954C5.53938 0.727479 5.53944 1.09053 5.31608 1.31402L3.26509 3.36544L3.2216 3.4047C3.01172 3.57584 2.70803 3.57587 2.49816 3.4047L2.45467 3.36544L2.12053 3.0307C1.89707 2.80716 1.89705 2.44416 2.12053 2.22063L4.17152 0.167621ZM4.56506 0.545333L4.55551 0.551699L2.50453 2.60471C2.49314 2.6161 2.49316 2.63522 2.50453 2.64662L2.8392 2.98083C2.85061 2.99223 2.86969 2.99224 2.8811 2.98083L4.93155 0.929941C4.94295 0.918545 4.94295 0.899427 4.93155 0.888032L4.93102 0.886971L4.59741 0.551699C4.58881 0.543136 4.5758 0.540971 4.56506 0.545333Z" fill="currentColor" />
                    <path d="M5.37011 5.41983L1.22195 9.56878L1.16785 9.61758C0.905988 9.83135 0.527903 9.8312 0.264595 9.61758L0.209965 9.56878C-0.0531755 9.30728 -0.0682516 8.89332 0.160639 8.61125L0.209965 8.55661L4.35813 4.40766L5.37011 5.41983ZM0.593966 8.94068C0.52456 9.01176 0.528234 9.11911 0.592375 9.18311C0.661971 9.25227 0.771776 9.25088 0.837945 9.1847L4.60211 5.41983L4.35813 5.17581L0.593966 8.94068Z" fill="currentColor" />
                    <path d="M5.8215 5.90107L5.43744 6.28517L3.50892 4.35641L3.89297 3.97231L5.8215 5.90107ZM7.8134 3.90892L5.88488 1.98016L3.50892 4.35641L3.47141 4.31515C3.30914 4.11606 3.30915 3.82856 3.47141 3.62947L3.50892 3.58821L5.50083 1.59605C5.71293 1.38392 6.05683 1.38392 6.26894 1.59605L8.19746 3.52482C8.40957 3.73695 8.40957 4.08089 8.19746 4.29302L6.20555 6.28517L6.1643 6.32268C5.96517 6.485 5.67776 6.48511 5.4787 6.32268L5.43744 6.28517L7.8134 3.90892Z" fill="currentColor" />
                    <path d="M9.72844 9.45678C9.87831 9.4569 10 9.57846 10 9.72839C10 9.87832 9.87831 9.99988 9.72844 10H5.15013C5.00015 10 4.87857 9.8784 4.87857 9.72839C4.87857 9.57838 5.00015 9.45678 5.15013 9.45678H9.72844Z" fill="currentColor" />
                </svg>
                <span>Disputes</span>
                <span class="admin-po-section-count">25</span>
            </button>

            <button type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === 'tickets' }"
                @click="section = 'tickets'">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="9" viewBox="0 0 13 9" fill="none" aria-hidden="true">
                    <path d="M7.1 0.5V1.64286M7.1 7.35714V8.5M7.1 3.92857V5.07143M0.5 2.78571C0.97739 2.78571 1.43523 2.96633 1.77279 3.28782C2.11036 3.60931 2.3 4.04534 2.3 4.5C2.3 4.95466 2.11036 5.39069 1.77279 5.71218C1.43523 6.03367 0.97739 6.21429 0.5 6.21429V7.35714C0.5 7.66025 0.626428 7.95094 0.851472 8.16527C1.07652 8.37959 1.38174 8.5 1.7 8.5H11.3C11.6183 8.5 11.9235 8.37959 12.1485 8.16527C12.3736 7.95094 12.5 7.66025 12.5 7.35714V6.21429C12.0226 6.21429 11.5648 6.03367 11.2272 5.71218C10.8896 5.39069 10.7 4.95466 10.7 4.5C10.7 4.04534 10.8896 3.60931 11.2272 3.28782C11.5648 2.96633 12.0226 2.78571 12.5 2.78571V1.64286C12.5 1.33975 12.3736 1.04906 12.1485 0.834735C11.9235 0.620408 11.6183 0.5 11.3 0.5H1.7C1.38174 0.5 1.07652 0.620408 0.851472 0.834735C0.626428 1.04906 0.5 1.33975 0.5 1.64286V2.78571Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Support tickets</span>
                <span class="admin-po-section-count">9</span>
            </button>
        </nav>

        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

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
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M11.0303 0.00602895L11.0243 6.98789L10.1131 6.96979C9.92802 6.96979 9.79727 6.91548 9.72084 6.80686C9.6444 6.69019 9.60417 6.54537 9.60015 6.37238L9.61222 3.42757C9.61222 3.21837 9.61825 3.02326 9.63032 2.84222C9.63837 2.65717 9.65245 2.48619 9.67256 2.32929C9.46337 2.59481 9.23808 2.86837 8.9967 3.14998C8.75532 3.42354 8.50188 3.69308 8.23636 3.9586L1.42044 10.7745C1.0955 11.0995 0.568666 11.0995 0.243724 10.7745C-0.0812176 10.4496 -0.0812177 9.92274 0.243724 9.5978L7.05964 2.78188C7.32516 2.51636 7.59872 2.26292 7.88033 2.02154C8.15791 1.77614 8.43147 1.55085 8.70101 1.34568C8.5401 1.36982 8.36912 1.38792 8.18809 1.39999C8.00303 1.40803 7.8059 1.41206 7.59671 1.41206L4.62776 1.42413C4.45879 1.42413 4.31598 1.38591 4.19931 1.30947C4.08667 1.22901 4.03035 1.09625 4.03035 0.911198L4.01224 -5.168e-06L11.0303 0.00602895Z" fill="#A7C569" />
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="11" viewBox="0 0 8 11" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.387097 0C0.489761 0 0.588221 0.0404276 0.660816 0.112389C0.73341 0.184351 0.774194 0.281952 0.774194 0.383721V0.938837L1.58658 0.777674C2.51216 0.594395 3.47153 0.681852 4.34787 1.0294L4.45265 1.07084C5.18284 1.36031 5.98647 1.41437 6.74942 1.22535C6.89781 1.18857 7.05271 1.1858 7.20234 1.21726C7.35197 1.24871 7.49241 1.31357 7.61297 1.40688C7.73353 1.5002 7.83104 1.61953 7.89811 1.75581C7.96517 1.89209 8.00002 2.04172 8 2.19335V5.96251C8 6.468 7.65265 6.90902 7.15768 7.03182L7.04722 7.05893C6.05487 7.30475 5.0096 7.23439 4.05987 6.85786C3.32305 6.56583 2.51647 6.49241 1.73832 6.64656L0.774194 6.83791V10.6163C0.774194 10.718 0.73341 10.8156 0.660816 10.8876C0.588221 10.9596 0.489761 11 0.387097 11C0.284432 11 0.185973 10.9596 0.113378 10.8876C0.0407832 10.8156 0 10.718 0 10.6163V0.383721C0 0.281952 0.0407832 0.184351 0.113378 0.112389C0.185973 0.0404276 0.284432 0 0.387097 0Z" fill="#FF7F3C" />
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                        <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731" />
                    </svg>
                    <input type="search"
                        placeholder="Search name, email, ID ..."
                        x-model="search"
                        aria-label="Search customers">
                </label>

                <button type="button" class="admin-po-tool-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                        <path d="M0.5 4.88457C0.5 3.14411 0.5 2.27365 1.04092 1.73318C1.58185 1.19272 2.45185 1.19226 4.19231 1.19226H6.03846C7.77892 1.19226 8.64938 1.19226 9.18985 1.73318C9.73031 2.27411 9.73077 3.14411 9.73077 4.88457V5.80765C9.73077 7.54812 9.73077 8.41858 9.18985 8.95904C8.64892 9.4995 7.77892 9.49996 6.03846 9.49996H4.19231C2.45185 9.49996 1.58138 9.49996 1.04092 8.95904C0.500462 8.41812 0.5 7.54812 0.5 5.80765V4.88457Z" stroke="#3B3731" />
                        <path d="M2.80739 1.19231V0.5M7.42278 1.19231V0.5M0.730469 3.5H9.4997" stroke="#3B3731" stroke-linecap="round" />
                        <path d="M7.88465 7.19234C7.88465 7.31475 7.83603 7.43214 7.74947 7.5187C7.66292 7.60525 7.54552 7.65388 7.42311 7.65388C7.30071 7.65388 7.18331 7.60525 7.09676 7.5187C7.0102 7.43214 6.96158 7.31475 6.96158 7.19234C6.96158 7.06993 7.0102 6.95254 7.09676 6.86598C7.18331 6.77943 7.30071 6.7308 7.42311 6.7308C7.54552 6.7308 7.66292 6.77943 7.74947 6.86598C7.83603 6.95254 7.88465 7.06993 7.88465 7.19234ZM7.88465 5.34618C7.88465 5.46859 7.83603 5.58598 7.74947 5.67254C7.66292 5.7591 7.54552 5.80772 7.42311 5.80772C7.30071 5.80772 7.18331 5.7591 7.09676 5.67254C7.0102 5.58598 6.96158 5.46859 6.96158 5.34618C6.96158 5.22377 7.0102 5.10638 7.09676 5.01983C7.18331 4.93327 7.30071 4.88464 7.42311 4.88464C7.54552 4.88464 7.66292 4.93327 7.74947 5.01983C7.83603 5.10638 7.88465 5.22377 7.88465 5.34618ZM5.57696 7.19234C5.57696 7.31475 5.52833 7.43214 5.44178 7.5187C5.35522 7.60525 5.23783 7.65388 5.11542 7.65388C4.99301 7.65388 4.87562 7.60525 4.78907 7.5187C4.70251 7.43214 4.65388 7.31475 4.65388 7.19234C4.65388 7.06993 4.70251 6.95254 4.78907 6.86598C4.87562 6.77943 4.99301 6.7308 5.11542 6.7308C5.23783 6.7308 5.35522 6.77943 5.44178 6.86598C5.52833 6.95254 5.57696 7.06993 5.57696 7.19234ZM5.57696 5.34618C5.57696 5.46859 5.52833 5.58598 5.44178 5.67254C5.35522 5.7591 5.23783 5.80772 5.11542 5.80772C4.99301 5.80772 4.87562 5.7591 4.78907 5.67254C4.70251 5.58598 4.65388 5.46859 4.65388 5.34618C4.65388 5.22377 4.70251 5.10638 4.78907 5.01983C4.87562 4.93327 4.99301 4.88464 5.11542 4.88464C5.23783 4.88464 5.35522 4.93327 5.44178 5.01983C5.52833 5.10638 5.57696 5.22377 5.57696 5.34618ZM3.26927 7.19234C3.26927 7.31475 3.22064 7.43214 3.13409 7.5187C3.04753 7.60525 2.93014 7.65388 2.80773 7.65388C2.68532 7.65388 2.56793 7.60525 2.48137 7.5187C2.39482 7.43214 2.34619 7.31475 2.34619 7.19234C2.34619 7.06993 2.39482 6.95254 2.48137 6.86598C2.56793 6.77943 2.68532 6.7308 2.80773 6.7308C2.93014 6.7308 3.04753 6.77943 3.13409 6.86598C3.22064 6.95254 3.26927 7.06993 3.26927 7.19234ZM3.26927 5.34618C3.26927 5.46859 3.22064 5.58598 3.13409 5.67254C3.04753 5.7591 2.93014 5.80772 2.80773 5.80772C2.68532 5.80772 2.56793 5.7591 2.48137 5.67254C2.39482 5.58598 2.34619 5.46859 2.34619 5.34618C2.34619 5.22377 2.39482 5.10638 2.48137 5.01983C2.56793 4.93327 2.68532 4.88464 2.80773 4.88464C2.93014 4.88464 3.04753 4.93327 3.13409 5.01983C3.22064 5.10638 3.26927 5.22377 3.26927 5.34618Z" fill="#3B3731" />
                    </svg>
                    <span>Date range</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <div class="admin-card admin-po-table-card">
                <div class="admin-table-wrap">
                    <table class="admin-live-table admin-po-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input type="checkbox"
                                        class="admin-po-check"
                                        aria-label="Select all customers"
                                        :checked="allSelected"
                                        @change="toggleAll()"
                                        @click.stop>
                                </th>
                                <th>Name · User ID <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Email Address <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Region <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Date Joined <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Total Bookings <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Total Spend <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Verified <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Total Pets <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Last Active <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                            <tr
                                class="admin-po-row-clickable"
                                role="button"
                                tabindex="0"
                                @click="openCustomer('{{ $customer['id'] }}')"
                                @keydown.enter.prevent="openCustomer('{{ $customer['id'] }}')"
                                @keydown.space.prevent="openCustomer('{{ $customer['id'] }}')"
                                x-show="(statusFilter === 'all' || statusFilter === '{{ $customer['status'] }}') &&
                                    (search === '' ||
                                     '{{ strtolower($customer['name']) }}'.includes(search.toLowerCase()) ||
                                     '{{ strtolower($customer['email']) }}'.includes(search.toLowerCase()) ||
                                     '{{ strtolower($customer['id']) }}'.includes(search.toLowerCase()))">
                                <td class="admin-po-check-col" @click.stop>
                                    <input type="checkbox"
                                        class="admin-po-check"
                                        aria-label="Select {{ $customer['name'] }}"
                                        :checked="isSelected('{{ $customer['id'] }}')"
                                        @change="toggleOne('{{ $customer['id'] }}')">
                                </td>
                                <td>
                                    <span class="admin-po-name-cell">
                                        @if ($customer['status'] === 'flagged')
                                        <span class="admin-po-flag" aria-label="Flagged" title="Flagged">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 12" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.435484 0C0.550981 0 0.661748 0.0441029 0.743418 0.122606C0.825087 0.20111 0.870968 0.307584 0.870968 0.418605V1.02419L1.7849 0.848372C2.82618 0.648431 3.90548 0.743839 4.89135 1.12298L5.00923 1.16819C5.83069 1.48397 6.73478 1.54295 7.5931 1.33674C7.76004 1.29662 7.9343 1.2936 8.10263 1.32792C8.27097 1.36223 8.42896 1.43298 8.56459 1.53478C8.70022 1.63658 8.80992 1.76676 8.88537 1.91543C8.96082 2.06409 9.00002 2.22733 9 2.39274V6.50456C9 7.056 8.60923 7.53712 8.05239 7.67107L7.92813 7.70065C6.81172 7.96882 5.6358 7.89207 4.56735 7.4813C3.73843 7.16272 2.83103 7.08263 1.95561 7.25079L0.870968 7.45954V11.5814C0.870968 11.6924 0.825087 11.7989 0.743418 11.8774C0.661748 11.9559 0.550981 12 0.435484 12C0.319986 12 0.209219 11.9559 0.12755 11.8774C0.0458812 11.7989 0 11.6924 0 11.5814V0.418605C0 0.307584 0.0458812 0.20111 0.12755 0.122606C0.209219 0.0441029 0.319986 0 0.435484 0Z" fill="#FF7F3C" />
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                                            <path d="M5.59509 12L0 6.31185L1.39877 4.88981L5.59509 9.15592L14.6012 0L16 1.42204L5.59509 12Z" fill="#A7C569" />
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
            </div>
            <div class="admin-po-table-footer">
                <p class="admin-table-count">Showing 1–25 of 4,218 customers</p>
                <nav class="admin-po-pagination" aria-label="Customer pagination">
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
                            <g filter="url(#filter0_d_1_244)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_1_244" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1_244" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1_244" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    {{-- All disputes content --}}
    <div class="admin-po-disputes" x-show="section === 'disputes'" x-cloak>
        <div class="admin-po-head">
            <h1 class="admin-page-title mb-0">All Disputes</h1>
            <p class="admin-section-label mb-0">25 disputes · oldest open 22 days</p>
        </div>

        <div class="admin-po-metric-row">
            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Open disputes</p>
                <p class="admin-card-value">10</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-warn"></span>5 under review
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Unassigned disputes</p>
                <p class="admin-card-value">4</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge is-danger">28 days</span>
                    <span class="admin-metric-note">Avg. response SLA</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Total value at stake</p>
                <p class="admin-card-value">£520</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge is-info">£50.25</span>
                    <span class="admin-metric-note">Avg. dispute value</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Resolved this month</p>
                    <span class="admin-po-metric-icon is-up" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                            <path d="M5.59509 12L0 6.31185L1.39877 4.88981L5.59509 9.15592L14.6012 0L16 1.42204L5.59509 12Z" fill="#A7C569" />
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">15</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">+5</span>
                    <span class="admin-metric-note">vs last month</span>
                </div>
            </div>
        </div>

        <div class="admin-po-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Dispute status filters">
                <button type="button"
                    class="admin-po-filter is-all"
                    :class="{ 'is-active': disputeFilter === 'all' }"
                    @click="disputeFilter = 'all'">
                    All (25)
                </button>
                <button type="button"
                    class="admin-po-filter is-unassigned"
                    :class="{ 'is-active': disputeFilter === 'unassigned' }"
                    @click="disputeFilter = 'unassigned'">
                    Unassigned (4)
                </button>
                <button type="button"
                    class="admin-po-filter is-under-review"
                    :class="{ 'is-active': disputeFilter === 'under_review' }"
                    @click="disputeFilter = 'under_review'">
                    Under review (10)
                </button>
                <button type="button"
                    class="admin-po-filter is-assigned-me"
                    :class="{ 'is-active': disputeFilter === 'assigned_to_me' }"
                    @click="disputeFilter = 'assigned_to_me'">
                    Assigned to me
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731" />
                    </svg>
                    <input type="search"
                        placeholder="Search name, email, ID ..."
                        x-model="disputeSearch"
                        aria-label="Search disputes">
                </label>

                <button type="button" class="admin-po-tool-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                        <path d="M0.5 10.5V9.94445C0.5 8.10556 1.99444 6.61111 3.83333 6.61111H6.05555C7.89444 6.61111 9.38889 8.10556 9.38889 9.94445V10.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.94439 4.94444C3.71661 4.94444 2.72217 3.95 2.72217 2.72222C2.72217 1.49444 3.71661 0.5 4.94439 0.5C6.17217 0.5 7.16661 1.49444 7.16661 2.72222C7.16661 3.95 6.17217 4.94444 4.94439 4.94444Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Admin</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <div class="admin-card admin-po-table-card">
                <div class="admin-table-wrap">
                    <table class="admin-live-table admin-po-table admin-po-disputes-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select all disputes">
                                </th>
                                <th>Dispute ID <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Customer · Provider <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Booking ID · Reason <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Assigned to <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Days open <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Value <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Due by <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($disputes as $dispute)
                            <tr
                                x-show="(disputeFilter === 'all'
                                    || (disputeFilter === 'assigned_to_me' && '{{ $dispute['assigned'] }}' !== '—')
                                    || disputeFilter === '{{ $dispute['status'] }}') &&
                                    (disputeSearch === '' ||
                                     '{{ strtolower($dispute['id']) }}'.includes(disputeSearch.toLowerCase()) ||
                                     '{{ strtolower($dispute['customer']) }}'.includes(disputeSearch.toLowerCase()) ||
                                     '{{ strtolower($dispute['provider']) }}'.includes(disputeSearch.toLowerCase()) ||
                                     '{{ strtolower($dispute['booking']) }}'.includes(disputeSearch.toLowerCase()))">
                                <td class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select {{ $dispute['id'] }}">
                                </td>
                                <td>
                                    <span class="admin-po-dispute-id">{{ $dispute['id'] }}</span>
                                </td>
                                <td>
                                    <div class="admin-po-stack">
                                        <span class="admin-po-stack-primary">{{ $dispute['customer'] }}</span>
                                        <span class="admin-po-stack-secondary">
                                            @if ($dispute['provider_type'] === 'groomer')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none" aria-hidden="true">
                                                <path d="M5.25833 4.875L2.59722 2.988M10.375 1.2462L2.59722 6.762M3.15278 7.9242C3.15278 7.1226 2.53056 6.4722 1.76389 6.4722C0.997222 6.4722 0.375 7.122 0.375 7.9236C0.375 8.7252 0.997222 9.375 1.76389 9.375C2.53056 9.375 3.15278 8.7252 3.15278 7.923M10.375 8.5038L7.005 6.114M3.15278 1.827C3.15278 2.6292 2.53056 3.279 1.76389 3.279C0.997222 3.279 0.375 2.6286 0.375 1.8264C0.375 1.0242 0.997222 0.375 1.76389 0.375C2.53056 0.375 3.15278 1.0248 3.15278 1.827Z" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                                                <path d="M8.7396 8.07756V2.55606C8.7396 2.54281 8.7407 2.52983 8.74274 2.51712L7.24984 1.24408C6.93237 0.973758 6.71303 0.787442 6.52696 0.665974C6.34729 0.548723 6.22654 0.511213 6.11104 0.511213C5.99564 0.511213 5.87565 0.54886 5.69617 0.665974C5.51007 0.787458 5.29015 0.973606 4.97225 1.24408L3.4783 2.51712C3.48035 2.52988 3.48249 2.54276 3.48249 2.55606V8.07756C3.48226 8.21854 3.36227 8.33317 3.21429 8.33317C3.0664 8.33306 2.94632 8.21848 2.94609 8.07756V2.97043L2.66951 3.20706C2.55909 3.30115 2.38899 3.29133 2.29026 3.18609C2.19192 3.08092 2.20113 2.91967 2.31121 2.82565L4.61395 0.86367H4.615C4.92227 0.602235 5.1711 0.389111 5.39236 0.244623C5.62029 0.0958328 5.84658 1.49993e-07 6.11104 0C6.37548 0 6.60172 0.0958269 6.82973 0.244623C7.05113 0.389146 7.30099 0.602138 7.60814 0.86367L9.91088 2.82565C10.021 2.91968 10.0302 3.08092 9.93183 3.18609C9.8331 3.29133 9.663 3.30115 9.55258 3.20706L9.276 2.97043V8.07756C9.27577 8.21848 9.15569 8.33306 9.0078 8.33317C8.85982 8.33317 8.73983 8.21854 8.7396 8.07756Z" fill="#3B3731"/>
                                                <path d="M1.21612 4.44359C1.21612 4.25079 1.16128 4.08536 1.08324 3.97368C1.00518 3.86207 0.914487 3.81515 0.833331 3.81515C0.75222 3.81522 0.661428 3.86215 0.58342 3.97368C0.50545 4.08536 0.450544 4.25089 0.450544 4.44359C0.450616 4.63636 0.505354 4.80189 0.58342 4.9135C0.661414 5.02497 0.752241 5.07102 0.833331 5.07109C0.914422 5.07109 1.00522 5.02492 1.08324 4.9135C1.16131 4.80189 1.21605 4.63636 1.21612 4.44359ZM1.66666 4.44359C1.66659 4.73079 1.58597 5.00023 1.44403 5.20319C1.30196 5.40632 1.08809 5.55421 0.833331 5.55421C0.578745 5.55414 0.365553 5.40615 0.223512 5.20319C0.0815533 5.00022 7.10594e-05 4.7308 0 4.44359C0 4.15624 0.0814911 3.8861 0.223512 3.68305C0.365553 3.48015 0.578796 3.3321 0.833331 3.33203C1.08806 3.33203 1.30196 3.47997 1.44403 3.68305C1.58605 3.8861 1.66666 4.15624 1.66666 4.44359Z" fill="#3B3731"/>
                                                <path d="M0.555664 8.07189V5.25942C0.555664 5.11561 0.680029 4.99902 0.833441 4.99902C0.986853 4.99902 1.11122 5.11561 1.11122 5.25942V8.07189C1.1111 8.21561 0.986781 8.33229 0.833441 8.33229C0.680101 8.33229 0.555781 8.21561 0.555664 8.07189Z" fill="#3B3731"/>
                                                <path d="M7.10524 6.20674C7.10524 5.97991 7.10425 5.8359 7.08982 5.73036C7.07645 5.63252 7.05597 5.60706 7.04356 5.59483C7.03118 5.58264 7.00548 5.5615 6.90582 5.5483C6.79848 5.5341 6.65141 5.53414 6.42062 5.53414H5.94776C5.71697 5.53414 5.5699 5.5341 5.46257 5.5483C5.3629 5.5615 5.33721 5.58264 5.32482 5.59483C5.31242 5.60706 5.29194 5.63252 5.27856 5.73036C5.26413 5.8359 5.26314 5.97991 5.26314 6.20674V7.81287H7.10524V6.20674ZM6.65808 3.61751C6.80319 3.61762 6.92102 3.73368 6.92124 3.87643C6.92124 4.01936 6.80332 4.13524 6.65808 4.13535H5.7103C5.56506 4.13524 5.44715 4.01936 5.44715 3.87643C5.44737 3.73368 5.5652 3.61762 5.7103 3.61751H6.65808ZM6.65808 2.21973L6.71051 2.22478C6.83064 2.24876 6.92124 2.35338 6.92124 2.47865C6.92124 2.60392 6.83064 2.70853 6.71051 2.73252L6.65808 2.73757H5.7103C5.56506 2.73746 5.44715 2.62158 5.44715 2.47865C5.44715 2.33572 5.56506 2.21984 5.7103 2.21973H6.65808ZM7.63156 7.81287H9.73681C9.88215 7.81287 9.99997 7.92879 9.99997 8.07179C9.99975 8.2146 9.88201 8.33071 9.73681 8.33071H0.263157C0.117956 8.33071 0.000221645 8.2146 0 8.07179C0 7.92879 0.117819 7.81287 0.263157 7.81287H4.73683V6.20674C4.73683 5.99466 4.73617 5.80959 4.75636 5.66158C4.77764 5.50597 4.82638 5.35304 4.9527 5.2287C5.0791 5.10433 5.23446 5.05645 5.39266 5.03551C5.54323 5.0156 5.73193 5.0163 5.94776 5.0163H6.42062C6.63645 5.0163 6.82516 5.0156 6.97572 5.03551C7.13392 5.05645 7.28928 5.10433 7.41568 5.2287C7.54201 5.35304 7.59075 5.50597 7.61202 5.66158C7.63222 5.80959 7.63156 5.99466 7.63156 6.20674V7.81287Z" fill="#3B3731"/>
                                            </svg>
                                            @endif
                                            {{ $dispute['provider'] }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-po-stack">
                                        <span class="admin-po-stack-primary">{{ $dispute['booking'] }}</span>
                                        <span class="admin-po-stack-muted">{{ $dispute['reason'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-po-status is-{{ $dispute['status'] }}">
                                        {{ $disputeStatusLabels[$dispute['status']] }}
                                    </span>
                                </td>
                                <td>{{ $dispute['assigned'] }}</td>
                                <td>
                                    <span @class(['admin-po-days-warn' => $dispute['days'] >= 15])>
                                        {{ $dispute['days'] }}
                                    </span>
                                </td>
                                <td>{{ $dispute['value'] }}</td>
                                <td>{{ $dispute['due'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-po-table-footer">
                <p class="admin-table-count">Showing 1–8 of 10 disputes</p>
                <nav class="admin-po-pagination" aria-label="Dispute pagination">
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
                            <g filter="url(#filter0_d_disputes_page)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_disputes_page" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_disputes_page" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_disputes_page" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    {{-- All support tickets content --}}
    <div class="admin-po-tickets" x-show="section === 'tickets'" x-cloak>
        <div class="admin-po-head">
            <h1 class="admin-page-title mb-0">All Support tickets</h1>
            <p class="admin-section-label mb-0">47 tickets · 14 open</p>
        </div>

        <div class="admin-po-metric-row">
            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Open support tickets</p>
                <p class="admin-card-value">20</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge warn">14 days</span>
                    <span class="admin-metric-note">Oldest open ticket</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Waiting reply from admin</p>
                <p class="admin-card-value">6</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-danger"></span>4 unassigned
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">In progress</p>
                <p class="admin-card-value">9</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge is-info">6.2 days</span>
                    <span class="admin-metric-note">Avg. open duration</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Resolved this week</p>
                    <span class="admin-po-metric-icon is-up" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M11.0303 0.00602895L11.0243 6.98789L10.1131 6.96979C9.92802 6.96979 9.79727 6.91548 9.72084 6.80686C9.6444 6.69019 9.60417 6.54537 9.60015 6.37238L9.61222 3.42757C9.61222 3.21837 9.61825 3.02326 9.63032 2.84222C9.63837 2.65717 9.65245 2.48619 9.67256 2.32929C9.46337 2.59481 9.23808 2.86837 8.9967 3.14998C8.75532 3.42354 8.50188 3.69308 8.23636 3.9586L1.42044 10.7745C1.0955 11.0995 0.568666 11.0995 0.243724 10.7745C-0.0812176 10.4496 -0.0812177 9.92274 0.243724 9.5978L7.05964 2.78188C7.32516 2.51636 7.59872 2.26292 7.88033 2.02154C8.15791 1.77614 8.43147 1.55085 8.70101 1.34568C8.5401 1.36982 8.36912 1.38792 8.18809 1.39999C8.00303 1.40803 7.8059 1.41206 7.59671 1.41206L4.62776 1.42413C4.45879 1.42413 4.31598 1.38591 4.19931 1.30947C4.08667 1.22901 4.03035 1.09625 4.03035 0.911198L4.01224 -5.168e-06L11.0303 0.00602895Z" fill="#A7C569" />
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">15</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">+5</span>
                    <span class="admin-metric-note">vs last week</span>
                </div>
            </div>
        </div>

        <div class="admin-po-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Ticket status filters">
                <button type="button"
                    class="admin-po-filter is-all"
                    :class="{ 'is-active': ticketFilter === 'all' }"
                    @click="ticketFilter = 'all'">
                    All (14)
                </button>
                <button type="button"
                    class="admin-po-filter is-ticket-open"
                    :class="{ 'is-active': ticketFilter === 'open' }"
                    @click="ticketFilter = 'open'">
                    Open (1)
                </button>
                <button type="button"
                    class="admin-po-filter is-in-progress"
                    :class="{ 'is-active': ticketFilter === 'in_progress' }"
                    @click="ticketFilter = 'in_progress'">
                    In progress (3)
                </button>
                <button type="button"
                    class="admin-po-filter is-resolved"
                    :class="{ 'is-active': ticketFilter === 'resolved' }"
                    @click="ticketFilter = 'resolved'">
                    Resolved (4)
                </button>
                <button type="button"
                    class="admin-po-filter is-closed"
                    :class="{ 'is-active': ticketFilter === 'closed' }"
                    @click="ticketFilter = 'closed'">
                    Closed (1)
                </button>
                <button type="button"
                    class="admin-po-filter is-reopened"
                    :class="{ 'is-active': ticketFilter === 'reopened' }"
                    @click="ticketFilter = 'reopened'">
                    Re-opened (1)
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731" />
                    </svg>
                    <input type="search"
                        placeholder="Search name, email, ID ..."
                        x-model="ticketSearch"
                        aria-label="Search support tickets">
                </label>

                <button type="button" class="admin-po-tool-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                        <path d="M0.5 10.5V9.94445C0.5 8.10556 1.99444 6.61111 3.83333 6.61111H6.05555C7.89444 6.61111 9.38889 8.10556 9.38889 9.94445V10.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.94439 4.94444C3.71661 4.94444 2.72217 3.95 2.72217 2.72222C2.72217 1.49444 3.71661 0.5 4.94439 0.5C6.17217 0.5 7.16661 1.49444 7.16661 2.72222C7.16661 3.95 6.17217 4.94444 4.94439 4.94444Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Admin</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <div class="admin-card admin-po-table-card">
                <div class="admin-table-wrap">
                    <table class="admin-live-table admin-po-table admin-po-tickets-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select all tickets">
                                </th>
                                <th>Ticket ID <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Customer <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Subject <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Assigned to <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Category <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Opened <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                                <th>Due in <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none"><path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" /></svg></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                            <tr
                                x-show="(ticketFilter === 'all' || ticketFilter === '{{ $ticket['status'] }}') &&
                                    (ticketSearch === '' ||
                                     '{{ strtolower($ticket['id']) }}'.includes(ticketSearch.toLowerCase()) ||
                                     '{{ strtolower($ticket['customer']) }}'.includes(ticketSearch.toLowerCase()) ||
                                     '{{ strtolower($ticket['user_id']) }}'.includes(ticketSearch.toLowerCase()) ||
                                     '{{ strtolower($ticket['subject']) }}'.includes(ticketSearch.toLowerCase()))">
                                <td class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select {{ $ticket['id'] }}">
                                </td>
                                <td>
                                    <span class="admin-po-ticket-id">{{ $ticket['id'] }}</span>
                                </td>
                                <td>
                                    <span class="admin-po-name-cell">
                                        <span class="admin-po-name">{{ $ticket['customer'] }}</span>
                                        <span class="admin-po-name-sep">·</span>
                                        <span class="admin-po-user-id">{{ $ticket['user_id'] }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-po-ticket-subject" title="{{ $ticket['subject'] }}">{{ $ticket['subject'] }}</span>
                                </td>
                                <td>
                                    <span class="admin-po-status is-{{ $ticket['status'] }}">
                                        {{ $ticketStatusLabels[$ticket['status']] }}
                                    </span>
                                </td>
                                <td>{{ $ticket['assigned'] }}</td>
                                <td>{{ $ticket['category'] }}</td>
                                <td>
                                    <span @class(['admin-po-days-warn' => $ticket['opened_days'] >= 15])>
                                        {{ $ticket['opened'] }}
                                    </span>
                                </td>
                                <td>{{ $ticket['due'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-po-table-footer">
                <p class="admin-table-count">Showing 1–25 of 47 support tickets</p>
                <nav class="admin-po-pagination" aria-label="Ticket pagination">
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
                            <g filter="url(#filter0_d_tickets_page)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_tickets_page" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_tickets_page" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_tickets_page" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
    </div>{{-- /list view --}}
</div>