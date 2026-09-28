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
$teamMembers = [
['id' => 'michelle', 'name' => 'Michelle M', 'initials' => 'MM', 'tone' => 'green', 'role' => 'Super admin', 'disputes' => 2],
['id' => 'ben', 'name' => 'Ben M', 'initials' => 'BM', 'tone' => 'blue', 'role' => 'Super admin', 'disputes' => 1],
];
@endphp

<div
    class="admin-co-bookings-tab"
    x-data="{
        bookingFilter: 'all',
        bookingSearch: '',
        selectedBooking: null,
        disputeOpen: false,
        resolveOutcome: 'customer',
        resolveRefund: '',
        resolvePayout: '',
        resolveNotes: '',
        resolveInternal: '',
        resolveNotify: 'both',
        openResolveRefund: false,
        openResolvePayout: false,
        openResolveNotify: false,
        assignOpen: false,
        assignNote: '',
        assignMemberId: 'michelle',
        cancelOpen: false,
        cancelReason: '',
        cancelRefund: '',
        cancelPayout: '',
        cancelNotes: '',
        openCancelReason: false,
        openCancelRefund: false,
        openCancelPayout: false,
        activeBooking: null,
        teamMembers: @js($teamMembers),
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
        petNames(booking) {
            return (booking.pets || []).map((p) => p.name).filter(Boolean).join(' + ');
        },
        petLabel(booking) {
            const pets = booking.pets || [];
            if (!pets.length) return '';
            return pets.map((p) => {
                if (p.breed) return p.name + ' · ' + p.breed;
                return p.name;
            }).filter(Boolean).join(' + ');
        },
        bookingMetaLine(booking) {
            const pets = this.petNames(booking);
            const provider = booking.provider_short || booking.provider || '';
            const date = booking.date_label || booking.date || '';
            return [booking.service, pets, provider, date].filter(Boolean).join(' · ');
        },
        bookingTitleLine(booking) {
            const parts = [booking.id];
            if (booking.dispute_id) parts.push(booking.dispute_id);
            if (booking.status_label) parts.push(booking.status_label);
            return parts.join(' · ');
        },
        normalizeBooking(booking) {
            if (booking.detail) return booking;
            const pets = booking.pets || [];
            const petLine = pets.map((p) => p.breed ? (p.name + ' (' + p.breed + ')') : p.name).filter(Boolean).join(', ');
            return {
                ...booking,
                detail: {
                    service: booking.service,
                    addons: '—',
                    pet: petLine || '—',
                    groomer: booking.provider_short || booking.provider || '—',
                    location_name: booking.provider_short || booking.provider || '—',
                    location_address: '',
                    datetime: booking.date_label || booking.date || '—',
                    duration: '—',
                    notes: '',
                    price_lines: [
                        { label: booking.service, value: booking.amount },
                    ],
                    total_label: 'Total charged to customer',
                    total: booking.amount,
                    payment_method: '—',
                    payment_status: booking.status_label || '—',
                    groomer_payout: '—',
                    timeline: [
                        { tone: 'flag', title: 'Service booked by customer', time: booking.date_label || booking.date || '' },
                    ],
                    dispute: null,
                },
            };
        },
        openBooking(booking) {
            this.selectedBooking = this.normalizeBooking(booking);
            this.disputeOpen = false;
            this.$dispatch('admin-booking-selected', { booking: this.selectedBooking });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        closeBooking() {
            this.disputeOpen = false;
            this.selectedBooking = null;
            this.$dispatch('admin-dispute-closed');
            this.$dispatch('admin-booking-closed');
        },
        openDispute() {
            if (!this.selectedBooking) return;
            this.disputeOpen = true;
            this.selectResolveOutcome('customer');
            this.resolveNotes = '';
            this.resolveInternal = '';
            this.resolveNotify = 'both';
            this.openResolveNotify = false;
            this.$dispatch('admin-dispute-opened');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        closeDispute() {
            this.disputeOpen = false;
            this.$dispatch('admin-dispute-closed');
        },
        selectResolveOutcome(outcome) {
            this.resolveOutcome = outcome;
            const amount = this.selectedBooking?.amount || '';
            if (outcome === 'customer') {
                this.resolveRefund = amount ? ('Full refund - ' + amount) : 'Full refund';
                this.resolvePayout = 'Withhold full payout';
            } else if (outcome === 'groomer') {
                this.resolveRefund = 'No refund';
                this.resolvePayout = amount ? ('Release full payout - ' + amount) : 'Release full payout';
            } else {
                this.resolveRefund = 'Partial refund - enter amount';
                this.resolvePayout = 'Release partial payout - enter amount';
            }
            this.openResolveRefund = false;
            this.openResolvePayout = false;
        },
        bookingAmountLabel() {
            return this.selectedBooking?.detail?.total || this.selectedBooking?.amount || '';
        },
        resolveMoneyFromLabel(label, zeroLabels = []) {
            if (!label) return null;
            const text = String(label);
            if (zeroLabels.some((z) => text.toLowerCase().includes(z))) return '£0.00';
            const match = text.match(/£[\d,.]+/);
            if (match) return match[0];
            if (/full refund/i.test(text) || /release full payout/i.test(text)) {
                return this.bookingAmountLabel() || null;
            }
            return null;
        },
        resolveRefundPreview() {
            const amount = this.resolveMoneyFromLabel(this.resolveRefund, ['no refund']);
            if (!amount) return '—';
            if (amount === '£0.00') return '£0.00';
            return amount.startsWith('-') ? amount : ('-' + amount);
        },
        resolvePayoutPreview() {
            return this.resolveMoneyFromLabel(this.resolvePayout, ['withhold full']) || '—';
        },
        resolveReady() {
            return !!this.resolveOutcome && !!this.resolveRefund && !!this.resolvePayout && !!this.resolveNotes.trim();
        },
        openAssign(booking) {
            this.activeBooking = booking || this.selectedBooking;
            this.assignNote = '';
            this.assignMemberId = 'michelle';
            this.assignOpen = true;
        },
        openCancel(booking) {
            const b = booking || this.selectedBooking;
            this.activeBooking = b;
            this.cancelReason = '';
            this.cancelRefund = b?.amount ? ('Full refund - ' + b.amount) : 'Full refund';
            this.cancelPayout = b?.status === 'disputed' ? 'Hold payout - dispute open' : 'Release payout to groomer';
            this.cancelNotes = '';
            this.openCancelReason = false;
            this.openCancelRefund = false;
            this.openCancelPayout = false;
            this.cancelOpen = true;
        },
    }"
    @keydown.escape.window="
        if (assignOpen) assignOpen = false;
        else if (cancelOpen) cancelOpen = false;
    "
    @admin-booking-close-request.window="closeBooking()"
    @admin-dispute-close-request.window="closeDispute()"
    @admin-view-dispute.window="selectedBooking && openDispute()"
    @admin-open-assign-booking.window="selectedBooking && openAssign(selectedBooking)"
    @admin-open-cancel-booking.window="selectedBooking && openCancel(selectedBooking)">

    {{-- List view --}}
    <div x-show="!selectedBooking" x-cloak>
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

        <div class="admin-co-bk-sep" aria-hidden="true"></div>

        <div class="admin-co-bk-list">
            @foreach ($bookings as $booking)
            @php
            $pets = $booking['pets'] ?? [[
            'name' => $booking['pet_name'] ?? '',
            'image' => $booking['pet_image'] ?? '',
            'type' => $booking['pet_type'] ?? 'dog',
            ]];
            $petLabel = collect($pets)->pluck('name')->filter()->implode(' + ');
            $primaryType = $pets[0]['type'] ?? 'dog';
            $bookingPayload = array_merge($booking, ['pets' => $pets]);
            @endphp
            <article
                class="admin-co-bk-card is-clickable {{ $booking['status'] === 'disputed' ? 'is-disputed' : '' }}"
                role="button"
                tabindex="0"
                @click="openBooking(@js($bookingPayload))"
                @keydown.enter.prevent="openBooking(@js($bookingPayload))"
                @keydown.space.prevent="openBooking(@js($bookingPayload))"
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none">
                                <path d="M9.53101 0.535238C9.11998 -0.178413 8.01236 -0.178413 7.60133 0.535238L0.129134 13.5183L0.0672303 13.6453C-0.173102 14.2451 0.256901 14.9057 0.943487 14.9911L1.09504 15H16.0373C16.8614 15 17.3895 14.1901 17.0032 13.5183L9.53101 0.535238Z" fill="#FFC97A" />
                                <path d="M8.40443 5.47978L8.60442 9.73045L8.80406 5.48152C8.8053 5.45436 8.80098 5.42723 8.79137 5.40179C8.78176 5.37635 8.76707 5.35314 8.74819 5.33358C8.72931 5.31401 8.70664 5.2985 8.68156 5.28799C8.65648 5.27749 8.62952 5.27221 8.60233 5.27247C8.57562 5.27273 8.54923 5.27834 8.52472 5.28897C8.50021 5.2996 8.47808 5.31503 8.45963 5.33436C8.44118 5.35368 8.42679 5.3765 8.41731 5.40148C8.40783 5.42646 8.40345 5.45308 8.40443 5.47978Z" fill="#3B3731" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.4707 11.1465C8.60269 11.1202 8.7399 11.1341 8.86426 11.1855C8.98873 11.2371 9.09605 11.3245 9.1709 11.4365C9.24567 11.5485 9.28516 11.6808 9.28516 11.8154C9.28504 11.9959 9.21359 12.1692 9.08594 12.2969C8.95829 12.4245 8.785 12.496 8.60449 12.4961C8.46984 12.4961 8.33757 12.4566 8.22559 12.3818C8.11356 12.307 8.02617 12.1997 7.97461 12.0752C7.9232 11.9508 7.90929 11.8136 7.93555 11.6816C7.96188 11.5496 8.02689 11.4282 8.12207 11.333C8.21725 11.2378 8.3387 11.1728 8.4707 11.1465Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.03125" />
                            </svg>
                        </span>
                        @endif
                        <span class="admin-co-bk-meta-label">Booking ID</span>
                        <span class="admin-co-bk-meta-value">{{ $booking['id'] }}</span>
                    </p>
                    <div class="admin-co-bk-card-head-right">
                        <p class="admin-co-bk-meta">
                            <span class="admin-co-bk-meta-label">Date</span>
                            <span class="admin-co-bk-meta-value">{{ $booking['date'] }}</span>
                        </p>
                    </div>
                </div>

                <div class="admin-co-bk-card-body">
                    <div class="admin-co-bk-col admin-co-bk-col--pet">
                        <div class="admin-co-bk-pet">
                            <div class="admin-co-bk-pet-stack" aria-hidden="true">
                                @foreach ($pets as $pet)
                                <img src="{{ $pet['image'] }}" alt="" class="admin-co-bk-pet-avatar" width="36" height="36">
                                @endforeach
                            </div>
                            <div class="admin-co-bk-field">
                                <span class="admin-co-bk-field-label">
                                    <span class="admin-co-pet-type-icon" aria-hidden="true">
                                        @if ($primaryType === 'cat')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 16 22" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.0661 3.58301C7.82655 3.48924 8.87949 3.51286 9.88642 3.85352C10.9998 4.23021 12.075 5.00401 12.6013 6.43945L14.7712 7.47949L14.8181 7.64941C15.1058 8.68692 15.2764 10.2987 14.8308 11.7656C14.6062 12.5047 14.2222 13.2153 13.6101 13.791C12.9963 14.3682 12.1714 14.7931 11.0934 14.9854C7.21531 15.6771 5.01491 18.9931 4.4079 20.5596C4.15869 21.2733 2.6782 21.455 2.32978 20.7842C-2.87181 10.7633 1.80671 2.85095 5.03583 0L7.0661 3.58301ZM9.46845 7.20898C8.8993 7.20899 8.29571 7.49133 8.2956 8.62109C8.2956 9.40106 9.28944 8.62143 9.9372 8.62109C10.585 8.62109 10.6413 9.40123 10.6413 8.62109C10.6412 7.84111 10.1161 7.20898 9.46845 7.20898Z" fill="#FFC97A" />
                                        </svg>
                                        @elseif ($primaryType === 'other')
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
                                <span class="admin-co-bk-field-value">{{ $petLabel }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-bk-col">
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none" aria-hidden="true">
                                    <path d="M4.65756 10.8634C5.86245 12.0683 8.79289 11.0917 11.2027 8.68156C13.6128 6.27179 14.5894 3.34135 13.3845 2.13647M8.20279 1.31804L8.74815 1.86379M6.29403 3.22719L6.83939 3.77255M4.65718 5.40901L5.20254 5.95437M4.11182 8.1362L4.65718 8.68156M11.2027 0.5L11.748 1.04536M10.6573 3.77293L11.748 4.86365M8.74854 5.68208L9.83926 6.7728M6.56671 7.31816L7.65743 8.40888" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4.65703 12.5004C5.10889 12.0485 5.10889 11.3159 4.65703 10.8641C4.20517 10.4122 3.47256 10.4122 3.0207 10.8641L0.838933 13.0458C0.387073 13.4977 0.387073 14.2303 0.838933 14.6822C1.29079 15.134 2.0234 15.134 2.47526 14.6822L4.65703 12.5004Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Service
                            </span>
                            <span class="admin-co-bk-field-value">{{ $booking['service'] }}</span>
                        </div>
                    </div>

                    <div class="admin-co-bk-col admin-co-bk-col--provider">
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="15" viewBox="0 0 11 15" fill="none" aria-hidden="true">
                                    <path d="M5.5 0.5C6.83339 0.5 8.10786 1.00588 9.04395 1.89941C9.9792 2.79219 10.5 3.99796 10.5 5.25C10.5 6.6294 9.73861 8.338 8.73145 9.9707C7.73727 11.5823 6.5574 13.0362 5.82422 13.8867C5.6489 14.0901 5.3511 14.0901 5.17578 13.8867C4.4426 13.0362 3.26273 11.5823 2.26855 9.9707C1.26139 8.338 0.5 6.6294 0.5 5.25C0.5 3.99796 1.0208 2.79219 1.95605 1.89941C2.89214 1.00588 4.16661 0.5 5.5 0.5ZM5.5 2.875C4.85374 2.875 4.22936 3.11984 3.76562 3.5625C3.30115 4.00591 3.03613 4.61245 3.03613 5.25C3.03613 5.88755 3.30115 6.49409 3.76562 6.9375C4.22936 7.38016 4.85374 7.625 5.5 7.625C5.82047 7.625 6.13831 7.56479 6.43555 7.44727C6.73282 7.32973 7.00457 7.15686 7.23438 6.9375C7.46409 6.7182 7.64771 6.45659 7.77344 6.16699C7.89921 5.87715 7.96387 5.5652 7.96387 5.25C7.96387 4.61245 7.69885 4.00591 7.23438 3.5625C6.77064 3.11984 6.14626 2.875 5.5 2.875Z" stroke="#3B3731" />
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                        <g filter="url(#filter0_d_bk_page_next)">
                            <circle cx="20" cy="16" r="16" fill="white" />
                        </g>
                        <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        <defs>
                            <filter id="filter0_d_bk_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="4" />
                                <feGaussianBlur stdDeviation="2" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bk_page_next" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bk_page_next" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </button>
            </nav>
        </div>
    </div>{{-- /list view --}}

    {{-- Booking detail view --}}
    <div class="admin-co-bk-detail" x-show="selectedBooking && !disputeOpen" x-cloak>
        <div class="admin-co-overview-head">
            <div class="admin-co-bk-detail-title-row">
                <button type="button" class="admin-co-bk-detail-back" @click="closeBooking()" aria-label="Back to bookings">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                        <g filter="url(#filter0_d_bk_detail_back)">
                            <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
                        </g>
                        <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        <defs>
                            <filter id="filter0_d_bk_detail_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="4" />
                                <feGaussianBlur stdDeviation="2" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bk_detail_back" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bk_detail_back" result="shape" />
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
            <article
                class="admin-co-bk-card admin-co-bk-detail-summary"
                :class="{ 'is-disputed': selectedBooking.status === 'disputed' }">
                <div class="admin-co-bk-card-head">
                    <p class="admin-co-bk-meta">
                        <span class="admin-co-bk-warn" x-show="selectedBooking.status === 'disputed'" aria-label="Disputed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none">
                                <path d="M9.53101 0.535238C9.11998 -0.178413 8.01236 -0.178413 7.60133 0.535238L0.129134 13.5183L0.0672303 13.6453C-0.173102 14.2451 0.256901 14.9057 0.943487 14.9911L1.09504 15H16.0373C16.8614 15 17.3895 14.1901 17.0032 13.5183L9.53101 0.535238Z" fill="#FFC97A" />
                                <path d="M8.40443 5.47978L8.60442 9.73045L8.80406 5.48152C8.8053 5.45436 8.80098 5.42723 8.79137 5.40179C8.78176 5.37635 8.76707 5.35314 8.74819 5.33358C8.72931 5.31401 8.70664 5.2985 8.68156 5.28799C8.65648 5.27749 8.62952 5.27221 8.60233 5.27247C8.57562 5.27273 8.54923 5.27834 8.52472 5.28897C8.50021 5.2996 8.47808 5.31503 8.45963 5.33436C8.44118 5.35368 8.42679 5.3765 8.41731 5.40148C8.40783 5.42646 8.40345 5.45308 8.40443 5.47978Z" fill="#3B3731" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.4707 11.1465C8.60269 11.1202 8.7399 11.1341 8.86426 11.1855C8.98873 11.2371 9.09605 11.3245 9.1709 11.4365C9.24567 11.5485 9.28516 11.6808 9.28516 11.8154C9.28504 11.9959 9.21359 12.1692 9.08594 12.2969C8.95829 12.4245 8.785 12.496 8.60449 12.4961C8.46984 12.4961 8.33757 12.4566 8.22559 12.3818C8.11356 12.307 8.02617 12.1997 7.97461 12.0752C7.9232 11.9508 7.90929 11.8136 7.93555 11.6816C7.96188 11.5496 8.02689 11.4282 8.12207 11.333C8.21725 11.2378 8.3387 11.1728 8.4707 11.1465Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.03125" />
                            </svg>
                        </span>
                        <span class="admin-co-bk-meta-label">Booking ID</span>
                        <span class="admin-co-bk-meta-value" x-text="selectedBooking.id"></span>
                    </p>
                    <div class="admin-co-bk-card-head-right">
                        <p class="admin-co-bk-meta">
                            <span class="admin-co-bk-meta-label">Date</span>
                            <span class="admin-co-bk-meta-value" x-text="selectedBooking.date"></span>
                        </p>
                    </div>
                </div>

                <div class="admin-co-bk-card-body">
                    <div class="admin-co-bk-col admin-co-bk-col--pet">
                        <div class="admin-co-bk-pet">
                            <div class="admin-co-bk-pet-stack" aria-hidden="true">
                                <template x-for="(pet, idx) in (selectedBooking.pets || [])" :key="idx">
                                    <img :src="pet.image" alt="" class="admin-co-bk-pet-avatar" width="36" height="36">
                                </template>
                            </div>
                            <div class="admin-co-bk-field">
                                <span class="admin-co-bk-field-label">Pet</span>
                                <span class="admin-co-bk-field-value" x-text="petLabel(selectedBooking)"></span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-bk-col">
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label">Service</span>
                            <span class="admin-co-bk-field-value" x-text="selectedBooking.service"></span>
                        </div>
                    </div>

                    <div class="admin-co-bk-col admin-co-bk-col--provider">
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label" x-text="selectedBooking.provider_role"></span>
                            <span class="admin-co-bk-field-value" x-text="selectedBooking.provider"></span>
                        </div>
                    </div>

                    <div class="admin-co-bk-col admin-co-bk-col--rating">
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label">Rating</span>
                            <span class="admin-co-bk-field-value is-muted" x-show="selectedBooking.rating === null">—</span>
                            <span class="admin-co-pet-stars" x-show="selectedBooking.rating !== null" :aria-label="(selectedBooking.rating || 0) + ' out of 5 stars'">
                                <template x-for="i in 5" :key="i">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                        <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" :fill="i <= (selectedBooking.rating || 0) ? '#FFC97A' : '#D4D2CF'" />
                                    </svg>
                                </template>
                            </span>
                        </div>
                    </div>

                    <div class="admin-co-bk-col admin-co-bk-col--total">
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label">Total</span>
                            <span class="admin-co-bk-total" x-text="selectedBooking.amount"></span>
                        </div>
                        <span
                            class="admin-co-booking-status is-pill"
                            :class="'is-' + selectedBooking.status"
                            x-text="selectedBooking.status_label"></span>
                    </div>
                </div>
            </article>
        </template>

        <div class="admin-co-bk-sep" aria-hidden="true"></div>

        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Booking details">
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                    </svg>
                    View Groomer Booking
                </button>
            </x-admin.customer.section-header>
            <dl class="admin-co-details">
                <div class="admin-co-details-row">
                    <dt>Service</dt>
                    <dd x-text="selectedBooking?.detail?.service"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Add-ons</dt>
                    <dd x-text="selectedBooking?.detail?.addons"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Pet</dt>
                    <dd x-text="selectedBooking?.detail?.pet"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Groomer</dt>
                    <dd x-text="selectedBooking?.detail?.groomer"></dd>
                </div>
                <div class="admin-co-details-row admin-co-bk-detail-location">
                    <dt>Location</dt>
                    <dd>
                        <span class="admin-co-bk-detail-loc-name" x-text="selectedBooking?.detail?.location_name"></span>
                        <span
                            class="admin-co-bk-detail-loc-addr"
                            x-show="selectedBooking?.detail?.location_address"
                            x-text="selectedBooking?.detail?.location_address"></span>
                    </dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Date &amp; Time</dt>
                    <dd x-text="selectedBooking?.detail?.datetime"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Duration</dt>
                    <dd x-text="selectedBooking?.detail?.duration"></dd>
                </div>
                <div class="admin-co-details-row" x-show="selectedBooking?.detail?.notes">
                    <dt>Customer Notes</dt>
                    <dd class="admin-co-bk-detail-notes" x-text="selectedBooking?.detail?.notes"></dd>
                </div>
            </dl>
        </section>

        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Full price breakdown" />
            <dl class="admin-co-details">
                <template x-for="(line, idx) in (selectedBooking?.detail?.price_lines || [])" :key="idx">
                    <div class="admin-co-details-row">
                        <dt x-text="line.label"></dt>
                        <dd x-text="line.value"></dd>
                    </div>
                </template>
                <div class="admin-co-details-row admin-co-bk-detail-total-row">
                    <dt x-text="selectedBooking?.detail?.total_label || 'Total charged to customer'"></dt>
                    <dd x-text="selectedBooking?.detail?.total || selectedBooking?.amount"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Payment method</dt>
                    <dd x-text="selectedBooking?.detail?.payment_method"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Payment status</dt>
                    <dd class="admin-co-bk-detail-warn" x-text="selectedBooking?.detail?.payment_status"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Groomer payout</dt>
                    <dd class="admin-co-bk-detail-warn" x-text="selectedBooking?.detail?.groomer_payout"></dd>
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
                <template x-for="(item, idx) in (selectedBooking?.detail?.timeline || [])" :key="idx">
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
    </div>{{-- /booking detail --}}

    @include('components.admin.customer.dispute-detail')

    {{-- Assign to team member --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': assignOpen }"
            @click.self="assignOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal admin-co-bk-assign-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-archive" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none">
                                    <path d="M4.67871 0.5C5.89398 0.5 6.87966 1.48503 6.87988 2.7002C6.87988 3.91554 5.89412 4.90137 4.67871 4.90137C3.46352 4.90111 2.47852 3.91539 2.47852 2.7002C2.47874 1.48519 3.46366 0.500256 4.67871 0.5Z" stroke="#649FC9" />
                                    <path d="M7.6257 6.21383C6.70333 5.22558 5.46817 5.1557 4.67978 5.1557C1.14436 5.1557 0.424177 8.18365 0.506016 9.82038" stroke="#649FC9" stroke-linecap="round" />
                                    <path d="M9.40502 7.64673L7.34273 9.92572C7.29778 9.97526 7.24533 10 7.18539 10C7.12545 10 7.073 9.97526 7.02805 9.92572L6.06152 8.86054" stroke="#649FC9" stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Assign to team member</h3>
                                <p class="admin-co-modal-sub">
                                    Booking <span x-text="activeBooking?.id"></span>
                                    <template x-if="activeBooking?.status_label">
                                        <span> · <span x-text="activeBooking.status_label"></span></span>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="assignOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert is-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#659FC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#659FC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>Select a team member to take ownership of this booking and its open dispute. They will be notified by email.</p>
                    </div>

                    <template x-if="activeBooking">
                        <div class="admin-co-bk-summary-card">
                            <span class="admin-co-bk-summary-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                                    <path d="M4.65756 10.8634C5.86245 12.0683 8.79289 11.0917 11.2027 8.68156C13.6128 6.27179 14.5894 3.34135 13.3845 2.13647M8.20279 1.31804L8.74815 1.86379M6.29403 3.22719L6.83939 3.77255M4.65718 5.40901L5.20254 5.95437M4.11182 8.1362L4.65718 8.68156M11.2027 0.5L11.748 1.04536M10.6573 3.77293L11.748 4.86365M8.74854 5.68208L9.83926 6.7728M6.56671 7.31816L7.65743 8.40888" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4.65703 12.5004C5.10889 12.0485 5.10889 11.3159 4.65703 10.8641C4.20517 10.4122 3.47256 10.4122 3.0207 10.8641L0.838933 13.0458C0.387073 13.4977 0.387073 14.2303 0.838933 14.6822C1.29079 15.134 2.0234 15.134 2.47526 14.6822L4.65703 12.5004Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <p class="admin-co-bk-summary-title" x-text="bookingTitleLine(activeBooking)"></p>
                                <p class="admin-co-bk-summary-meta" x-text="bookingMetaLine(activeBooking)"></p>
                            </div>
                        </div>
                    </template>

                    <div class="admin-co-transfer-account-list admin-co-bk-assign-list">
                        <template x-for="member in teamMembers" :key="member.id">
                            <button
                                type="button"
                                class="admin-co-transfer-account"
                                :class="{ 'is-selected': assignMemberId === member.id }"
                                @click="assignMemberId = member.id">
                                <span class="admin-co-transfer-radio" aria-hidden="true"></span>
                                <span
                                    class="admin-co-bk-assign-avatar"
                                    :class="'is-' + member.tone"
                                    x-text="member.initials"></span>
                                <span class="admin-co-transfer-account-body">
                                    <span class="admin-co-transfer-account-name" x-text="member.name"></span>
                                    <span class="admin-co-transfer-account-email" x-text="member.role + ' · ' + member.disputes + ' open disputes'"></span>
                                </span>
                            </button>
                        </template>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">Note to assignee (optional)</label>
                        <textarea
                            class="admin-co-suspend-notes"
                            rows="3"
                            placeholder="e.g. Please review the groomer's photo evidence before resolving ..."
                            x-model="assignNote"></textarea>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="assignOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        :disabled="!assignMemberId"
                        @click="assignMemberId && (assignOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M8 8C9.65685 8 11 6.65685 11 5C11 3.34315 9.65685 2 8 2C6.34315 2 5 3.34315 5 5C5 6.65685 6.34315 8 8 8Z" stroke="currentColor" stroke-width="1.25" />
                            <path d="M2.5 13.5C2.5 11.2909 4.79086 9.5 8 9.5C11.2091 9.5 13.5 11.2909 13.5 13.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" />
                        </svg>
                        Assign booking
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Cancel booking --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': cancelOpen }"
            @click.self="cancelOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal admin-co-bk-cancel-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-delete" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                                    <path d="M6.5 12.5C9.81371 12.5 12.5 9.81371 12.5 6.5C12.5 3.18629 9.81371 0.5 6.5 0.5C3.18629 0.5 0.5 3.18629 0.5 6.5C0.5 9.81371 3.18629 12.5 6.5 12.5Z" stroke="#FE6F56" />
                                    <path d="M10.5 10.5L2.5 2.5" stroke="#FE6F56" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Cancel booking</h3>
                                <p class="admin-co-modal-sub">
                                    <span x-text="activeBooking?.id"></span>
                                    <template x-if="activeBooking?.amount">
                                        <span> · <span x-text="activeBooking.amount"></span></span>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="cancelOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert is-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>Cancelling this booking cannot be undone. Both the customer and groomer will be notified immediately.</p>
                    </div>

                    <template x-if="activeBooking">
                        <div class="admin-co-bk-summary-card">
                            <span class="admin-co-bk-summary-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                                    <path d="M4.65756 10.8634C5.86245 12.0683 8.79289 11.0917 11.2027 8.68156C13.6128 6.27179 14.5894 3.34135 13.3845 2.13647M8.20279 1.31804L8.74815 1.86379M6.29403 3.22719L6.83939 3.77255M4.65718 5.40901L5.20254 5.95437M4.11182 8.1362L4.65718 8.68156M11.2027 0.5L11.748 1.04536M10.6573 3.77293L11.748 4.86365M8.74854 5.68208L9.83926 6.7728M6.56671 7.31816L7.65743 8.40888" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4.65703 12.5004C5.10889 12.0485 5.10889 11.3159 4.65703 10.8641C4.20517 10.4122 3.47256 10.4122 3.0207 10.8641L0.838933 13.0458C0.387073 13.4977 0.387073 14.2303 0.838933 14.6822C1.29079 15.134 2.0234 15.134 2.47526 14.6822L4.65703 12.5004Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <p class="admin-co-bk-summary-title">
                                    <span x-text="activeBooking.id"></span> · <span x-text="activeBooking.service"></span>
                                </p>
                                <p class="admin-co-bk-summary-meta">
                                    <span x-text="petNames(activeBooking)"></span>
                                    · <span x-text="activeBooking.provider_short || activeBooking.provider"></span>
                                    · <span x-text="activeBooking.date_label || activeBooking.date"></span>
                                    · <span x-text="activeBooking.amount"></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Reason for cancellation <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openCancelReason = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openCancelReason = !openCancelReason; openCancelRefund = false; openCancelPayout = false">
                                <span
                                    class="admin-co-dd-value"
                                    :class="{ 'is-placeholder': !cancelReason }"
                                    x-text="cancelReason || 'Select a reason ...'"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openCancelReason" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelReason === 'Admin decision - dispute unresolved' }" @click="cancelReason = 'Admin decision - dispute unresolved'; openCancelReason = false">Admin decision - dispute unresolved</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelReason === 'Customer requested cancellation' }" @click="cancelReason = 'Customer requested cancellation'; openCancelReason = false">Customer requested cancellation</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelReason === 'Groomer unavailable' }" @click="cancelReason = 'Groomer unavailable'; openCancelReason = false">Groomer unavailable</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelReason === 'Fraudulent booking suspected' }" @click="cancelReason = 'Fraudulent booking suspected'; openCancelReason = false">Fraudulent booking suspected</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelReason === 'Customer account suspended' }" @click="cancelReason = 'Customer account suspended'; openCancelReason = false">Customer account suspended</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelReason === 'Other' }" @click="cancelReason = 'Other'; openCancelReason = false">Other</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Refund to customer <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openCancelRefund = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openCancelRefund = !openCancelRefund; openCancelReason = false; openCancelPayout = false">
                                <span class="admin-co-dd-value" x-text="cancelRefund"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openCancelRefund" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelRefund === ('Full refund - ' + (activeBooking?.amount || '')) }" @click="cancelRefund = 'Full refund - ' + (activeBooking?.amount || ''); openCancelRefund = false">
                                    Full refund - <span x-text="activeBooking?.amount"></span>
                                </button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelRefund === 'Partial refund - specify amount' }" @click="cancelRefund = 'Partial refund - specify amount'; openCancelRefund = false">Partial refund - specify amount</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelRefund === 'No refund' }" @click="cancelRefund = 'No refund'; openCancelRefund = false">No refund</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Groomer payout <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openCancelPayout = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openCancelPayout = !openCancelPayout; openCancelReason = false; openCancelRefund = false">
                                <span class="admin-co-dd-value" x-text="cancelPayout"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openCancelPayout" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelPayout === 'Hold payout - dispute open' }" @click="cancelPayout = 'Hold payout - dispute open'; openCancelPayout = false">Hold payout - dispute open</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelPayout === 'Release payout to groomer' }" @click="cancelPayout = 'Release payout to groomer'; openCancelPayout = false">Release payout to groomer</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': cancelPayout === 'No payout' }" @click="cancelPayout = 'No payout'; openCancelPayout = false">No payout</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">Additional notes (optional)</label>
                        <textarea
                            class="admin-co-suspend-notes"
                            rows="3"
                            placeholder="Any additional context for the archive log ..."
                            x-model="cancelNotes"></textarea>
                    </div>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What happens when cancelled</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li>Booking status changes to Cancelled immediately</li>
                            <li>Customer and groomer both receive a cancellation email</li>
                            <li>Refund is processed according to the option selected above</li>
                            <li>Logged in activity log with your name and timestamp</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="cancelOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        :disabled="!cancelReason || !cancelRefund || !cancelPayout"
                        @click="cancelReason && cancelRefund && cancelPayout && (cancelOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <circle cx="7" cy="7" r="5.75" stroke="currentColor" stroke-width="1.25" />
                            <path d="M4.5 4.5L9.5 9.5M9.5 4.5L4.5 9.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" />
                        </svg>
                        Cancel booking
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>