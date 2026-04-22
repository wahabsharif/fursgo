@props(['variant' => null])

@php
    $activeBgColor = '#FFC97A';
    if (auth()->check()) {
        $userType = auth()->user()->user_type;
        if ($userType === 'space') {
            $activeBgColor = '#FFA899';
        }
    }

    $spacerId = auth('groomer_spacer')->id();
    $bookingCounts = \App\Models\Booking::query()
        ->when($spacerId, fn($query) => $query->where('goormer_spacer_id', $spacerId))
        ->selectRaw('booking_status, COUNT(*) as total')
        ->groupBy('booking_status')
        ->pluck('total', 'booking_status');

    $pendingCount = (int) ($bookingCounts['pending'] ?? 0);
    $confirmedCount = (int) ($bookingCounts['confirmed'] ?? 0);
    $completedCount = (int) ($bookingCounts['completed'] ?? 0);
    $cancelledCount = (int) ($bookingCounts['cancelled'] ?? 0);
@endphp

<div x-data="{
    mobileOpen: false,
    bookingsOpen: false,
    availabilityOpen: false,
    activeBookingStatus: '',
    bookingCounts: {
        pending: {{ $pendingCount }},
        confirmed: {{ $confirmedCount }},
        completed: {{ $completedCount }},
        cancelled: {{ $cancelledCount }},
    }
}"
    @booking-status-changed.window="
        activeBookingStatus = $event.detail.status ?? '';
        if (activeSection === 'bookings') {
            bookingsOpen = true;
        }
    "
    @booking-counts-updated.window="
        bookingCounts = {
            pending: Number($event.detail?.counts?.pending ?? bookingCounts.pending),
            confirmed: Number($event.detail?.counts?.confirmed ?? bookingCounts.confirmed),
            completed: Number($event.detail?.counts?.completed ?? bookingCounts.completed),
            cancelled: Number($event.detail?.counts?.cancelled ?? bookingCounts.cancelled),
        }
    "
    style="{{ $variant === 'dashboard' ? 'max-width: 16rem; margin: 0; padding: 0; width: 100%; position: relative;' : 'position: relative;' }}">
    <style>
        :root {
            --sidebar-active-bg: {{ $activeBgColor }};
        }
    </style>

    <!-- Mobile Toggle Button -->
    <button @click="mobileOpen = !mobileOpen" class="mobile-toggle" aria-label="Toggle Sidebar">
        <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Horizontal Navigation (above content) -->
    <aside class="aside">
        <ul class="nav-list">
            <!-- Business Hub -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'business-hub'; bookingsOpen = false; availabilityOpen = false"
                    :class="{ 'active': activeSection === 'business-hub' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="nav-icon">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                    </svg>
                    <span class="nav-text">Business Hub</span>
                </a>
            </li>

            <!-- Bookings -->
            <li class="nav-item">
                <a href="#"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'bookings'; bookingsOpen = true; availabilityOpen = false; activeBookingStatus = ''; window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); window.Livewire?.dispatch('booking-filter-reset')"
                    :class="{ 'active': activeSection === 'bookings' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14"
                        fill="none">
                        <path
                            d="M4.29615 6.9274C4.38027 6.94943 4.46964 6.96044 4.56076 6.96044C5.17935 6.96044 5.68228 6.43367 5.68228 5.78576V4.61108H6.45683C6.66887 4.61108 6.86339 4.73589 6.95801 4.93596L7.08418 5.19842H8.20571C8.35992 5.19842 8.48609 5.33057 8.48609 5.49209V6.07943C8.48609 6.89069 7.85874 7.54778 7.08418 7.54778H6.24304V8.47834C6.24304 8.61233 6.13965 8.72246 6.00998 8.72246C5.97843 8.72246 5.94689 8.71511 5.91885 8.70227L4.18926 7.92588C4.0736 7.87449 4 7.75518 4 7.62487C4 7.57347 4.01051 7.52392 4.0333 7.47803L4.29615 6.9274ZM4.28038 4.61108H5.12152V5.78576C5.12152 6.11063 4.87093 6.3731 4.56076 6.3731C4.25059 6.3731 4 6.11063 4 5.78576V4.90475C4 4.74324 4.12617 4.61108 4.28038 4.61108Z"
                            fill="#3B3731" />
                        <path
                            d="M6 0.625C6.02228 0.625 6.04363 0.629375 6.06738 0.640625L6.07422 0.643555L6.08105 0.647461L10.7891 2.73926C11.1208 2.88613 11.3764 3.23484 11.375 3.66309C11.3632 6.0941 10.4324 10.3399 6.74512 12.4229L6.37988 12.6172C6.13876 12.7382 5.86124 12.7382 5.62012 12.6172C1.87814 10.7404 0.7732 6.71839 0.639648 4.15527L0.625 3.66309C0.623815 3.28842 0.818847 2.97465 1.08984 2.80371L1.21094 2.73926L5.91895 0.647461L5.92578 0.643555L5.93262 0.640625C5.95637 0.629375 5.97772 0.625 6 0.625Z"
                            stroke="#3B3731" stroke-width="1.25" />
                    </svg>
                    <span class="nav-text">Bookings</span>
                </a>
                <ul x-cloak x-show="bookingsOpen" x-transition:enter="bookings-transition-enter"
                    x-transition:enter-start="bookings-transition-enter-start"
                    x-transition:enter-end="bookings-transition-enter-end"
                    x-transition:leave="bookings-transition-leave"
                    x-transition:leave-start="bookings-transition-leave-start"
                    x-transition:leave-end="bookings-transition-leave-end" class="booking-status-list">
                    <li class="booking-status-item">
                        <span class="booking-status-dot pending"></span>
                        <button type="button" class="booking-status-trigger pending"
                            :class="{ 'is-active': activeBookingStatus === 'pending' }"
                            @click="activeSection = 'bookings'; bookingsOpen = true; activeBookingStatus = 'pending'; window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); window.Livewire?.dispatch('booking-status-selected', { status: 'pending' })">
                            Pending Requests <span class="booking-status-count"
                                x-text="`(${bookingCounts.pending})`">({{ $pendingCount }})</span>
                        </button>
                    </li>
                    <li class="booking-status-item">
                        <span class="booking-status-dot confirmed"></span>
                        <button type="button" class="booking-status-trigger confirmed"
                            :class="{ 'is-active': activeBookingStatus === 'confirmed' }"
                            @click="activeSection = 'bookings'; bookingsOpen = true; activeBookingStatus = 'confirmed'; window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); window.Livewire?.dispatch('booking-status-selected', { status: 'confirmed' })">
                            Confirmed Bookings <span class="booking-status-count"
                                x-text="`(${bookingCounts.confirmed})`">({{ $confirmedCount }})</span>
                        </button>
                    </li>
                    <li class="booking-status-item">
                        <span class="booking-status-dot completed"></span>
                        <button type="button" class="booking-status-trigger completed"
                            :class="{ 'is-active': activeBookingStatus === 'completed' }"
                            @click="activeSection = 'bookings'; bookingsOpen = true; activeBookingStatus = 'completed'; window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); window.Livewire?.dispatch('booking-status-selected', { status: 'completed' })">
                            Completed Bookings <span class="booking-status-count"
                                x-text="`(${bookingCounts.completed})`">({{ $completedCount }})</span>
                        </button>
                    </li>
                    <li class="booking-status-item">
                        <span class="booking-status-dot cancelled"></span>
                        <button type="button" class="booking-status-trigger cancelled"
                            :class="{ 'is-active': activeBookingStatus === 'cancelled' }"
                            @click="activeSection = 'bookings'; bookingsOpen = true; activeBookingStatus = 'cancelled'; window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); window.Livewire?.dispatch('booking-status-selected', { status: 'cancelled' })">
                            Cancelled Bookings <span class="booking-status-count"
                                x-text="`(${bookingCounts.cancelled})`">({{ $cancelledCount }})</span>
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Availability -->
            <li class="nav-item">
                <a href="#"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'availability'; bookingsOpen = false; availabilityOpen = !availabilityOpen"
                    :class="{ 'active': activeSection === 'availability' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13"
                        fill="none">
                        <path
                            d="M0.625 6.08193C0.625 3.91602 0.625 2.83278 1.3282 2.16021C2.0314 1.48763 3.1624 1.48706 5.425 1.48706H7.825C10.0876 1.48706 11.2192 1.48706 11.9218 2.16021C12.6244 2.83336 12.625 3.91602 12.625 6.08193V7.23064C12.625 9.39655 12.625 10.4798 11.9218 11.1524C11.2186 11.8249 10.0876 11.8255 7.825 11.8255H5.425C3.1624 11.8255 2.0308 11.8255 1.3282 11.1524C0.6256 10.4792 0.625 9.39655 0.625 7.23064V6.08193Z"
                            stroke="#3B3731" stroke-width="1.25" />
                        <path d="M3.62505 1.48654V0.625M9.62505 1.48654V0.625M0.925049 4.35833H12.325" stroke="#3B3731"
                            stroke-width="1.25" stroke-linecap="round" />
                        <path
                            d="M10.2251 8.95333C10.2251 9.10566 10.1619 9.25175 10.0494 9.35947C9.93689 9.46718 9.78428 9.52769 9.62515 9.52769C9.46602 9.52769 9.3134 9.46718 9.20088 9.35947C9.08836 9.25175 9.02515 9.10566 9.02515 8.95333C9.02515 8.801 9.08836 8.65491 9.20088 8.5472C9.3134 8.43949 9.46602 8.37898 9.62515 8.37898C9.78428 8.37898 9.93689 8.43949 10.0494 8.5472C10.1619 8.65491 10.2251 8.801 10.2251 8.95333ZM10.2251 6.6559C10.2251 6.80823 10.1619 6.95432 10.0494 7.06203C9.93689 7.16975 9.78428 7.23026 9.62515 7.23026C9.46602 7.23026 9.3134 7.16975 9.20088 7.06203C9.08836 6.95432 9.02515 6.80823 9.02515 6.6559C9.02515 6.50357 9.08836 6.35748 9.20088 6.24977C9.3134 6.14206 9.46602 6.08154 9.62515 6.08154C9.78428 6.08154 9.93689 6.14206 10.0494 6.24977C10.1619 6.35748 10.2251 6.50357 10.2251 6.6559ZM7.22515 8.95333C7.22515 9.10566 7.16193 9.25175 7.04941 9.35947C6.93689 9.46718 6.78428 9.52769 6.62515 9.52769C6.46602 9.52769 6.3134 9.46718 6.20088 9.35947C6.08836 9.25175 6.02515 9.10566 6.02515 8.95333C6.02515 8.801 6.08836 8.65491 6.20088 8.5472C6.3134 8.43949 6.46602 8.37898 6.62515 8.37898C6.78428 8.37898 6.93689 8.43949 7.04941 8.5472C7.16193 8.65491 7.22515 8.801 7.22515 8.95333ZM7.22515 6.6559C7.22515 6.80823 7.16193 6.95432 7.04941 7.06203C6.93689 7.16975 6.78428 7.23026 6.62515 7.23026C6.46602 7.23026 6.3134 7.16975 6.20088 7.06203C6.08836 6.95432 6.02515 6.80823 6.02515 6.6559C6.02515 6.50357 6.08836 6.35748 6.20088 6.24977C6.3134 6.14206 6.46602 6.08154 6.62515 6.08154C6.78428 6.08154 6.93689 6.14206 7.04941 6.24977C7.16193 6.35748 7.22515 6.50357 7.22515 6.6559ZM4.22515 8.95333C4.22515 9.10566 4.16193 9.25175 4.04941 9.35947C3.93689 9.46718 3.78428 9.52769 3.62515 9.52769C3.46602 9.52769 3.3134 9.46718 3.20088 9.35947C3.08836 9.25175 3.02515 9.10566 3.02515 8.95333C3.02515 8.801 3.08836 8.65491 3.20088 8.5472C3.3134 8.43949 3.46602 8.37898 3.62515 8.37898C3.78428 8.37898 3.93689 8.43949 4.04941 8.5472C4.16193 8.65491 4.22515 8.801 4.22515 8.95333ZM4.22515 6.6559C4.22515 6.80823 4.16193 6.95432 4.04941 7.06203C3.93689 7.16975 3.78428 7.23026 3.62515 7.23026C3.46602 7.23026 3.3134 7.16975 3.20088 7.06203C3.08836 6.95432 3.02515 6.80823 3.02515 6.6559C3.02515 6.50357 3.08836 6.35748 3.20088 6.24977C3.3134 6.14206 3.46602 6.08154 3.62515 6.08154C3.78428 6.08154 3.93689 6.14206 4.04941 6.24977C4.16193 6.35748 4.22515 6.50357 4.22515 6.6559Z"
                            fill="#3B3731" />
                    </svg>
                    <span class="nav-text">Availability</span>
                </a>
                <ul x-cloak x-show="availabilityOpen" x-transition:enter="bookings-transition-enter"
                    x-transition:enter-start="bookings-transition-enter-start"
                    x-transition:enter-end="bookings-transition-enter-end"
                    x-transition:leave="bookings-transition-leave"
                    x-transition:leave-start="bookings-transition-leave-start"
                    x-transition:leave-end="bookings-transition-leave-end" class="booking-status-list">
                    <li class="booking-status-item">
                        <span class="availability-status-dot"></span>
                        <button type="button" class="booking-status-trigger"
                            :class="{ 'is-active': activeSection === 'availability' }"
                            @click="activeSection = 'availability'; availabilityOpen = true;">
                            Manage Availability
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Services -->
            <li class="nav-item">
                <a href="#"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'services'; bookingsOpen = false; availabilityOpen = false"
                    :class="{ 'active': activeSection === 'services' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14"
                        fill="none">
                        <path
                            d="M4.21939 9.58489C5.2611 10.6266 7.79466 9.7823 9.87807 7.69855C11.9618 5.61513 12.8061 3.08155 11.7644 2.03984M7.28448 1.33225L7.75598 1.80409M5.63423 2.98285L6.10573 3.45435M4.21906 4.8692L4.69056 5.3407M3.74756 7.22704L4.21906 7.69855M9.87807 0.625L10.3496 1.0965M9.40657 3.45469L10.3496 4.39769M7.75631 5.10528L8.69932 6.04829M5.86998 6.51979L6.81298 7.4628"
                            stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M4.21895 10.9995C4.60962 10.6089 4.60962 9.97547 4.21895 9.5848C3.82829 9.19414 3.1949 9.19414 2.80424 9.5848L0.917951 11.4711C0.527288 11.8618 0.527288 12.4952 0.917951 12.8858C1.30861 13.2765 1.942 13.2765 2.33267 12.8858L4.21895 10.9995Z"
                            stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Services</span>
                </a>
            </li>

            <!-- Clients -->
            <li class="nav-item">
                <a href="#"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'clients'; bookingsOpen = false; availabilityOpen = false"
                    :class="{ 'active': activeSection === 'clients' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="10" viewBox="0 0 14 10"
                        fill="none">
                        <path
                            d="M10.5336 2.55354C10.4551 3.64289 9.64702 4.48207 8.76578 4.48207C7.88455 4.48207 7.0751 3.64316 6.99796 2.55354C6.9176 1.42025 7.70428 0.625 8.76578 0.625C9.82728 0.625 10.614 1.44088 10.5336 2.55354Z"
                            stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M8.76578 6.19629C7.02019 6.19629 5.34156 7.06333 4.92103 8.75187C4.86532 8.97526 5.0054 9.19624 5.23495 9.19624H12.2969C12.5264 9.19624 12.6657 8.97526 12.6108 8.75187C12.1903 7.03627 10.5116 6.19629 8.76578 6.19629Z"
                            stroke="#3B3731" stroke-width="1.25" stroke-miterlimit="10" />
                        <path
                            d="M5.12309 3.03411C5.06042 3.9041 4.40739 4.58926 3.70348 4.58926C2.99956 4.58926 2.34547 3.90437 2.28386 3.03411C2.21984 2.12904 2.85546 1.48218 3.70348 1.48218C4.5515 1.48218 5.18711 2.14565 5.12309 3.03411Z"
                            stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M5.2837 6.24981C4.80022 6.02829 4.26773 5.94312 3.70337 5.94312C2.31053 5.94312 0.968594 6.63551 0.63244 7.98415C0.588244 8.16254 0.700206 8.33905 0.883417 8.33905H3.89086"
                            stroke="#3B3731" stroke-width="1.25" stroke-miterlimit="10" stroke-linecap="round" />
                    </svg>
                    <span class="nav-text">Clients</span>
                </a>
            </li>

            <!-- Earnings -->
            <li class="nav-item">
                <a href="#"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'earnings'; bookingsOpen = false; availabilityOpen = false"
                    :class="{ 'active': activeSection === 'earnings' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12"
                        fill="none">
                        <path
                            d="M0.625 3.29167V1.95833C0.625 1.60471 0.765476 1.26557 1.01552 1.01552C1.26557 0.765476 1.60471 0.625 1.95833 0.625H3.29167M0.625 3.29167C1.51367 3.29167 3.29167 2.75833 3.29167 0.625M0.625 3.29167V5.95833M3.29167 0.625H9.95833M0.625 5.95833V7.29167C0.625 7.64529 0.765476 7.98443 1.01552 8.23448C1.26557 8.48452 1.60471 8.625 1.95833 8.625H3.29167M0.625 5.95833C1.51367 5.95833 3.29167 6.49167 3.29167 8.625M12.625 3.29167V1.95833C12.625 1.60471 12.4845 1.26557 12.2345 1.01552C11.9844 0.765476 11.6453 0.625 11.2917 0.625H9.95833M12.625 3.29167C11.7363 3.29167 9.95833 2.75833 9.95833 0.625M12.625 3.29167V4.625M3.29167 8.625H5.95833"
                            stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M6.62496 5.95841C7.36134 5.95841 7.95829 5.36146 7.95829 4.62508C7.95829 3.8887 7.36134 3.29175 6.62496 3.29175C5.88858 3.29175 5.29163 3.8887 5.29163 4.62508C5.29163 5.36146 5.88858 5.95841 6.62496 5.95841Z"
                            stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10.625 6.625V8.625M10.625 8.625V10.625M10.625 8.625H8.625M10.625 8.625H12.625"
                            stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Earnings</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a href="#"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'settings'; bookingsOpen = false; availabilityOpen = false"
                    :class="{ 'active': activeSection === 'settings' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"
                        fill="none">
                        <path
                            d="M6.62511 8.89793C7.67952 8.89793 8.53428 8.04316 8.53428 6.98876C8.53428 5.93435 7.67952 5.07959 6.62511 5.07959C5.57071 5.07959 4.71594 5.93435 4.71594 6.98876C4.71594 8.04316 5.57071 8.89793 6.62511 8.89793Z"
                            stroke="#3B3731" stroke-width="1.25" />
                        <path
                            d="M7.74823 0.721731C7.51467 0.625 7.21812 0.625 6.625 0.625C6.03188 0.625 5.73533 0.625 5.50177 0.721731C5.34724 0.785697 5.20684 0.879489 5.08858 0.997748C4.97032 1.11601 4.87653 1.25641 4.81256 1.41094C4.75401 1.55286 4.73047 1.71895 4.72156 1.96015C4.71741 2.13447 4.66912 2.3049 4.5812 2.45548C4.49327 2.60607 4.36859 2.73189 4.21881 2.82118C4.06659 2.90631 3.89526 2.95143 3.72086 2.95232C3.54645 2.95321 3.37467 2.90984 3.22159 2.82627C3.00776 2.71299 2.85312 2.65063 2.69975 2.63026C2.36521 2.58627 2.02689 2.67692 1.75916 2.88227C1.55934 3.03692 1.41042 3.29338 1.11386 3.80695C0.817307 4.32051 0.668392 4.57698 0.635936 4.82835C0.614067 4.9941 0.625067 5.16254 0.668307 5.32403C0.711548 5.48553 0.786182 5.63693 0.887947 5.76957C0.982132 5.89176 1.11387 5.99422 1.31815 6.12277C1.61916 6.31178 1.81262 6.63379 1.81262 6.9889C1.81262 7.344 1.61916 7.66602 1.31815 7.85439C1.11387 7.98357 0.981496 8.08603 0.887947 8.20822C0.786182 8.34087 0.711548 8.49226 0.668307 8.65376C0.625067 8.81526 0.614067 8.98369 0.635936 9.14944C0.669029 9.40018 0.817307 9.65728 1.11323 10.1708C1.41042 10.6844 1.5587 10.9409 1.75916 11.0955C1.89181 11.1973 2.04321 11.2719 2.2047 11.3152C2.3662 11.3584 2.53464 11.3694 2.70038 11.3475C2.85312 11.3272 3.00776 11.2648 3.22159 11.1515C3.37467 11.068 3.54645 11.0246 3.72086 11.0255C3.89526 11.0264 4.06659 11.0715 4.21881 11.1566C4.52619 11.3348 4.70883 11.6625 4.72156 12.0176C4.73047 12.2595 4.75338 12.4249 4.81256 12.5669C4.87653 12.7214 4.97032 12.8618 5.08858 12.98C5.20684 13.0983 5.34724 13.1921 5.50177 13.2561C5.73533 13.3528 6.03188 13.3528 6.625 13.3528C7.21812 13.3528 7.51467 13.3528 7.74823 13.2561C7.90276 13.1921 8.04316 13.0983 8.16142 12.98C8.27968 12.8618 8.37347 12.7214 8.43744 12.5669C8.49599 12.4249 8.51953 12.2595 8.52844 12.0176C8.54117 11.6625 8.72381 11.3342 9.03119 11.1566C9.18341 11.0715 9.35474 11.0264 9.52914 11.0255C9.70355 11.0246 9.87533 11.068 10.0284 11.1515C10.2422 11.2648 10.3969 11.3272 10.5496 11.3475C10.7154 11.3694 10.8838 11.3584 11.0453 11.3152C11.2068 11.2719 11.3582 11.1973 11.4908 11.0955C11.6913 10.9415 11.8396 10.6844 12.1361 10.1708C12.4327 9.65728 12.5816 9.40081 12.6141 9.14944C12.6359 8.98369 12.6249 8.81526 12.5817 8.65376C12.5385 8.49226 12.4638 8.34087 12.3621 8.20822C12.2679 8.08603 12.1361 7.98357 11.9319 7.85502C11.7829 7.76427 11.6594 7.63721 11.5729 7.48573C11.4864 7.33425 11.4398 7.16331 11.4374 6.9889C11.4374 6.63379 11.6308 6.31178 11.9319 6.12341C12.1361 5.99422 12.2685 5.89176 12.3621 5.76957C12.4638 5.63693 12.5385 5.48553 12.5817 5.32403C12.6249 5.16254 12.6359 4.9941 12.6141 4.82835C12.581 4.57762 12.4327 4.32051 12.1368 3.80695C11.8396 3.29338 11.6913 3.03692 11.4908 2.88227C11.3582 2.78051 11.2068 2.70588 11.0453 2.66263C10.8838 2.61939 10.7154 2.60839 10.5496 2.63026C10.3969 2.65063 10.2422 2.71299 10.0278 2.82627C9.87477 2.90973 9.70311 2.95304 9.52883 2.95215C9.35454 2.95126 9.18333 2.9062 9.03119 2.82118C8.88141 2.73189 8.75673 2.60607 8.6688 2.45548C8.58088 2.3049 8.53259 2.13447 8.52844 1.96015C8.51953 1.71832 8.49662 1.55286 8.43744 1.41094C8.37347 1.25641 8.27968 1.11601 8.16142 0.997748C8.04316 0.879489 7.90276 0.785697 7.74823 0.721731Z"
                            stroke="#3B3731" stroke-width="1.25" />
                    </svg>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" @click="mobileOpen = false" class="mobile-overlay" style="display: none;"
        x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Dashboard Layout */
        .dashboard-wrapper {
            display: flex;
            gap: 4rem;
            padding-top: 4rem;
            max-width: 110rem;
            margin: 0 auto;
            width: 100%;
        }

        .dashboard-main {
            flex: 1;
            min-width: 0;
        }

        .aside {
            flex-shrink: 0;
            position: sticky;
            top: 2rem;
            align-self: flex-start;
            height: fit-content;
        }

        /* Mobile Toggle Button */
        .mobile-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 50;
            padding: 0.5rem;
            background: var(--sidebar-active-bg);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            display: none;
            color: #5a3d2b;
        }


        .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-width: 240px;
        }

        .nav-item {
            position: relative;
        }

        .booking-status-list {
            list-style: none;
            margin: 1rem 0.5rem;
            padding: 0 0 0 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .bookings-transition-enter {
            transition: opacity 0.3s ease, transform 0.3s ease;
            transform-origin: top;
        }

        .bookings-transition-enter-start {
            opacity: 0;
            transform: translateY(-4px) scaleY(0.95);
        }

        .bookings-transition-enter-end {
            opacity: 1;
            transform: translateY(0) scaleY(1);
        }

        .bookings-transition-leave {
            transition: opacity 0.2s ease, transform 0.2s ease;
            transform-origin: top;
        }

        .bookings-transition-leave-start {
            opacity: 1;
            transform: translateY(0) scaleY(1);
        }

        .bookings-transition-leave-end {
            opacity: 0;
            transform: translateY(-4px) scaleY(0.95);
        }

        .booking-status-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .booking-status-dot {
            width: 12px;
            height: 12px;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .booking-status-dot.pending {
            background-color: #FFC97A;
        }

        .booking-status-dot.confirmed {
            background: #D8E8B7;
        }

        .booking-status-dot.completed {
            background: #CBDCE8;
        }

        .booking-status-dot.cancelled {
            background: #FFA899;
        }

        .availability-status-dot {
            width: 10px;
            height: 10px;
            aspect-ratio: 1/1;
            border-radius: 100px;
            background: #FFA899;
            flex-shrink: 0;
        }

        .booking-status-text {
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .booking-status-trigger {
            background: transparent;
            border: 0;
            padding: 0;
            margin: 0;
            text-align: left;
            cursor: pointer;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            color: #9D9B98;
        }

        .booking-status-trigger.is-active {
            color: #000;
        }

        .booking-status-count {
            color: #FF6E6E;
        }

        .nav-link {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 9999px;
            transition: all 0.2s ease;
        }

        .nav-link svg {
            width: 20px;
            height: 20px;
        }

        .nav-link:hover {
            background: var(--sidebar-active-bg);
            color: #FFF;
        }

        .nav-link:hover svg path,
        .nav-link:hover svg circle {
            stroke: #FFF;
            fill: none;
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: #FFF;
        }

        .nav-link.active svg path,
        .nav-link.active svg rect,
        .nav-link.active svg circle {
            stroke: #FFF;
            fill: none;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .nav-text {
            font-weight: 500;
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 35;
        }


        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(245, 198, 147, 0.5);
            border-radius: 2px;
        }
    </style>
</div>
