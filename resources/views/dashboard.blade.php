<section class="dashboard-content-wrapper">
    <div class="active-section-header" x-data="{ tabsLoading: false, navLoading: false, activeBookingFilter: '' }" x-on:bookings-tabs-loading-start.window="tabsLoading = true"
        x-on:bookings-tabs-loading-end.window="tabsLoading = false"
        x-on:booking-status-changed.window="tabsLoading = false; activeBookingFilter = $event.detail.status || ''"
        x-on:nav-list-loading-start.window="navLoading = true; setTimeout(() => navLoading = false, 350)">
        <template x-if="activeSection === 'bookings'">
            <div class="active-section-header-bookings">
                <h2
                    x-text="activeBookingFilter === 'pending' ? 'Pending Bookings' : (activeBookingFilter === 'confirmed' ? 'Confirmed Bookings' : (activeBookingFilter === 'completed' ? 'Completed Bookings' : (activeBookingFilter === 'cancelled' ? 'Cancelled Bookings' : 'All Bookings')))">
                </h2>
                <p>Manage your bookings</p>
            </div>
        </template>

        <template x-if="activeSection !== 'bookings'">
            <div x-text="activeSection.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())"></div>
        </template>

        {{-- Progress bar for bookings filtering + sidebar nav switching --}}
        <div class="active-section-loading-bar" x-cloak x-show="tabsLoading || navLoading" aria-hidden="true">
            <span class="active-section-loading-bar__sweep"></span>
        </div>
    </div>

    <div class="section-container">
        <div class="section-panel" :class="{ 'section-active': activeSection === 'business-hub' }">
            <x-dashboard.business-hub />
        </div>
        <div class="section-panel" :class="{ 'section-active': activeSection === 'bookings' }">
            <x-dashboard.bookings />
        </div>
    </div>
</section>

<style>
    [x-cloak] {
        display: none !important;
    }

    .dashboard-content-wrapper {
        display: flex;
        flex-direction: column;
    }

    .active-section-header {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
        position: relative;
    }

    .active-section-loading-bar {
        position: absolute;
        left: 0;
        right: 0;
        bottom: -6px;
        height: 4px;
        overflow: hidden;
        z-index: 10;
        pointer-events: none;
        background: rgba(232, 228, 222, 0.85);
        border-radius: 2px;
    }

    .active-section-loading-bar__sweep {
        position: absolute;
        top: 0;
        left: -42%;
        height: 100%;
        width: 42%;
        border-radius: 2px;
        background: linear-gradient(90deg, #FFC97A 0%, #f6a623 45%, #FFC97A 100%);
        box-shadow: 0 0 12px rgba(246, 166, 35, 0.45);
        will-change: left;
        animation: active-section-load-sweep 1.1s linear infinite;
    }

    @keyframes active-section-load-sweep {
        0% {
            left: -42%;
        }

        100% {
            left: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .active-section-loading-bar__sweep {
            animation: none;
            left: 0;
            width: 100%;
            opacity: 0.85;
        }
    }

    .active-section-header-bookings h2 {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .active-section-header-bookings p {
        margin: 0.2rem 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .section-container {
        position: relative;
    }

    .section-panel {
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        pointer-events: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }

    .section-panel.section-active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        position: relative;
    }
</style>
