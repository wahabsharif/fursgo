<section class="dashboard-content-wrapper">
    <div class="active-section-header" x-data="{ tabsLoading: false, navLoading: false, activeBookingFilter: '' }" x-on:bookings-tabs-loading-start.window="tabsLoading = true"
        x-on:bookings-tabs-loading-end.window="tabsLoading = false"
        x-on:booking-status-changed.window="tabsLoading = false; activeBookingFilter = $event.detail.status || ''"
        x-on:nav-list-loading-start.window="navLoading = true; setTimeout(() => navLoading = false, 350)">
        <template x-if="activeSection === 'bookings'">
            <div class="active-section-header">
                <h2
                    x-text="activeBookingFilter === 'pending' ? 'Pending Bookings' : (activeBookingFilter === 'confirmed' ? 'Confirmed Bookings' : (activeBookingFilter === 'completed' ? 'Completed Bookings' : (activeBookingFilter === 'cancelled' ? 'Cancelled Bookings' : 'All Bookings')))">
                </h2>
                <p>Manage your bookings</p>
            </div>
        </template>

        <template x-if="activeSection === 'availability'">
            <div class="active-section-header active-section-header-availability">
                <button type="button" class="availability-header-pill"
                    @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'manage-availability'">
                    Manage Availability
                </button>
                <div>
                    <h2>Availability</h2>
                    <p>View your schedule and manage when you’re available for bookings.</p>
                </div>
            </div>
        </template>

        <template x-if="activeSection === 'manage-availability'">
            <div class="active-section-header active-section-header-manage-availability">
                <button type="button" class="manage-availability-back-btn"
                    @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'availability'"
                    aria-label="Back to availability">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11"
                        fill="none">
                        <path
                            d="M0 5.202L5.211 0L5.877 0.684C6.015 0.828 6.069 0.972 6.039 1.116C6.015 1.254 5.94 1.386 5.814 1.512L3.609 3.708C3.297 4.02 3.012 4.278 2.754 4.482C3.102 4.434 3.468 4.398 3.852 4.374C4.242 4.344 4.635 4.329 5.031 4.329H16.074V6.084H5.031C4.629 6.084 4.233 6.072 3.843 6.048C3.459 6.024 3.093 5.988 2.745 5.94C2.877 6.042 3.012 6.156 3.15 6.282C3.294 6.408 3.447 6.549 3.609 6.705L5.832 8.919C5.958 9.045 6.033 9.18 6.057 9.324C6.087 9.462 6.033 9.6 5.895 9.738L5.229 10.431L0 5.202Z"
                            fill="black" />
                    </svg>
                    Availability
                </button>
                <div>
                    <h2>Manage Availability</h2>
                    <p>Set your working hours, days off, and staff schedules.</p>
                </div>
            </div>
        </template>

        <template x-if="activeSection === 'services'">
            <div class="active-section-header">
                <h2>Services</h2>
                <p>Manage your services, pricing, and add-ons.</p>
            </div>
        </template>

        <template
            x-if="activeSection !== 'bookings' && activeSection !== 'availability' && activeSection !== 'manage-availability' && activeSection !== 'services'">
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
        <div class="section-panel" :class="{ 'section-active': activeSection === 'availability' }">
            <x-dashboard.availability />
        </div>
        <div class="section-panel" :class="{ 'section-active': activeSection === 'manage-availability' }">
            <x-dashboard.availability.manage-availability />
        </div>
        <div class="section-panel" :class="{ 'section-active': activeSection === 'services' }">
            <x-dashboard.services />
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
        position: relative;
    }

    .active-section-header {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
        position: relative;
    }

    .active-section-header h2,
    .active-section-header>div>h2 {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
    }

    .active-section-header p,
    .active-section-header>div>p {
        margin: 0.2rem 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
        text-transform: none;
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


    .availability-header-pill {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 178px;
        height: 42px;
        border-radius: 100px;
        background: #C9DDA0;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border: 0;
        cursor: pointer;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }

    .availability-header-pill:hover {
        opacity: 0.92;
    }

    .availability-header-pill:active {
        transform: translateY(1px);
    }

    .active-section-header-availability {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .active-section-header-manage-availability {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .manage-availability-back-btn {
        border: 0;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        transition: background-color 0.15s ease;
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
