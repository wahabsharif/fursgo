@php
    use App\Models\GroomerSpacerProfile;
    use App\Support\BusinessHubNav;
    use Illuminate\Support\Facades\Auth;

    $dashboardNav = BusinessHubNav::fromSession();
    $dashboardActiveSection = $dashboardNav['active_section'];

    $gsp = Auth::guard('groomer_spacer')->user();
    $welcomeFirstName = 'there';
    if ($gsp instanceof GroomerSpacerProfile) {
        $nameParts = preg_split('/\s+/', trim((string) ($gsp->full_name ?: '')), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $welcomeFirstName = $nameParts[0] ?? (trim((string) $gsp->full_name) !== '' ? trim((string) $gsp->full_name) : 'there');
    }
    $welcomeBackTitle = 'Welcome back, ' . $welcomeFirstName . ' 👋';
    $servicesUserType = strtolower((string) ($gsp->user_type ?? data_get(auth()->user(), 'user_type', '')));
    $addServiceTitle = $servicesUserType === 'space' ? 'Hourly' : 'Full Groom';
@endphp

<section class="dashboard-content-wrapper"
    x-data="{ tabsLoading: false, navLoading: false, navLoadingTimeout: null, activeBookingFilter: @js($dashboardNav['active_booking_status']), serviceFormOpen: false, isEditingService: false, serviceFormTitle: @js($addServiceTitle), activeServiceMenu: @js($dashboardNav['active_service_menu']), activeEarningsMenu: @js($dashboardNav['active_earnings_menu']), clientProfileOpen: false, showWelcome: @js($dashboardActiveSection === 'business-hub') }"
    x-effect="if (activeSection !== 'business-hub') showWelcome = false"
    x-on:bookings-tabs-loading-start.window="tabsLoading = true"
    x-on:bookings-tabs-loading-end.window="tabsLoading = false"
    x-on:booking-status-changed.window="tabsLoading = false; activeBookingFilter = $event.detail.status || ''"
    x-on:nav-list-loading-start.window="navLoading = true; if (navLoadingTimeout) { clearTimeout(navLoadingTimeout); navLoadingTimeout = null; } if (!$event.detail?.persistent) { navLoadingTimeout = setTimeout(() => { navLoading = false; navLoadingTimeout = null; }, 350); }"
    x-on:nav-list-loading-end.window="navLoading = false; if (navLoadingTimeout) { clearTimeout(navLoadingTimeout); navLoadingTimeout = null; }"
    x-on:client-profile-visible.window="clientProfileOpen = !!$event.detail?.visible"
    x-on:service-form-opened.window="serviceFormOpen = true; isEditingService = !!$event.detail?.editing; if ($event.detail?.title) serviceFormTitle = $event.detail.title; if (!isEditingService && $event.detail?.menu === 'add-ons') serviceFormTitle = 'Add-ons'; if (!isEditingService && $event.detail?.menu === 'services') serviceFormTitle = @js($addServiceTitle)"
    x-on:service-form-title-changed.window="const next = ($event.detail?.title || '').trim(); const fallback = activeServiceMenu === 'add-ons' ? 'Add-ons' : (activeServiceMenu === 'service-area' ? 'Add Service Area' : @js($addServiceTitle)); serviceFormTitle = next || fallback"
    x-on:service-form-cancel.window="serviceFormOpen = false; isEditingService = false"
    x-on:service-form-closed.window="serviceFormOpen = false; isEditingService = false"
    x-on:services-menu-selected.window="activeServiceMenu = $event.detail?.menu || 'services'; serviceFormOpen = false; isEditingService = false"
    x-on:earnings-menu-selected.window="activeEarningsMenu = $event.detail?.menu || 'overview'">
    <template x-teleport=".dashboard-wrapper > main">
        <div class="active-section-loading-bar" x-cloak x-show="tabsLoading || navLoading" aria-hidden="true">
            <span class="active-section-loading-bar__sweep"></span>
        </div>
    </template>

    <div class="active-section-header" x-show="activeSection !== 'clients' || !clientProfileOpen" x-cloak>
        <div class="active-section-header-stack">
            <div class="active-section-header-pane" x-cloak x-show="activeSection === 'business-hub'"
                x-transition.opacity.duration.280ms>
                <h2 class="active-section-header-welcome"
                    x-text="showWelcome ? @js($welcomeBackTitle) : 'Business Hub'"></h2>
            </div>

            <div class="active-section-header-pane" x-cloak x-show="activeSection === 'bookings'"
                x-transition.opacity.duration.280ms>
                <h2
                    x-text="activeBookingFilter === 'pending' ? 'Pending Bookings' : (activeBookingFilter === 'confirmed' ? 'Confirmed Bookings' : (activeBookingFilter === 'completed' ? 'Completed Bookings' : (activeBookingFilter === 'cancelled' ? 'Cancelled Bookings' : 'All Bookings')))">
                </h2>
                <p>Manage your bookings.</p>
            </div>

            <div class="active-section-header-pane active-section-header-availability" x-cloak
                x-show="activeSection === 'availability'" x-transition.opacity.duration.280ms>
                <div>
                    <h2>Availability</h2>
                    <p>View your schedule and manage when you’re available for bookings.</p>
                </div>
                <button type="button" class="availability-header-pill"
                    @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'manage-availability'">
                    <img src="{{ asset('images/business-hub/icon-manage-availability.svg') }}" alt="" />
                    Manage Availability
                </button>
            </div>

            <div class="active-section-header-pane active-section-header-manage-availability" x-cloak
                x-show="activeSection === 'manage-availability'" x-transition.opacity.duration.280ms>
                <button type="button" class="manage-availability-back-btn"
                    @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'availability'"
                    aria-label="Back to availability">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11" fill="none">
                        <path
                            d="M0 5.202L5.211 0L5.877 0.684C6.015 0.828 6.069 0.972 6.039 1.116C6.015 1.254 5.94 1.386 5.814 1.512L3.609 3.708C3.297 4.02 3.012 4.278 2.754 4.482C3.102 4.434 3.468 4.398 3.852 4.374C4.242 4.344 4.635 4.329 5.031 4.329H16.074V6.084H5.031C4.629 6.084 4.233 6.072 3.843 6.048C3.459 6.024 3.093 5.988 2.745 5.94C2.877 6.042 3.012 6.156 3.15 6.282C3.294 6.408 3.447 6.549 3.609 6.705L5.832 8.919C5.958 9.045 6.033 9.18 6.057 9.324C6.087 9.462 6.033 9.6 5.895 9.738L5.229 10.431L0 5.202Z"
                            fill="#3B3731" />
                    </svg>
                    Availability
                </button>
                <div>
                    <h2>Manage Availability</h2>
                    <p>Set your working hours, days off, and staff schedules.</p>
                </div>
            </div>

            <div class="active-section-header-pane active-section-header-clients" x-cloak
                x-show="activeSection === 'clients'" x-transition.opacity.duration.280ms>
                <div>
                    <h2>Clients</h2>
                    <p style="font-weight: 400;">Manage your clients and their pets.</p>
                </div>
            </div>

            <div class="active-section-header-pane" x-cloak x-show="activeSection === 'earnings'"
                x-transition.opacity.duration.280ms>
                <div>
                    <h2
                        x-text="activeEarningsMenu === 'transactions' ? 'Transactions' : (activeEarningsMenu === 'pay-outs' ? 'Pay-outs' : (activeEarningsMenu === 'invoices' ? 'Invoices' : 'Earnings Overview'))">
                    </h2>
                    <p
                        x-text="activeEarningsMenu === 'transactions' ? 'View all your payment and pay-out transactions.' : (activeEarningsMenu === 'pay-outs' ? 'Payouts are processed 2–3 business days after release' : (activeEarningsMenu === 'invoices' ? 'View and download invoices generated for completed bookings.' : 'View your earnings, transactions, pay-outs and statement reports.'))">
                    </p>
                </div>
            </div>

            <div class="active-section-header-pane" x-cloak x-show="activeSection === 'settings'"
                x-transition.opacity.duration.280ms>
                <div>
                    <h2>Settings</h2>
                    <p>Manage account controls.</p>
                </div>
            </div>

            <div class="active-section-header-pane active-section-header-services" x-cloak
                x-show="activeSection === 'services'" x-transition.opacity.duration.280ms>
                <div class="services-header-copy">
                    <button type="button" class="service-list-header-btn" x-cloak x-show="serviceFormOpen"
                        @click="window.dispatchEvent(new CustomEvent('service-form-cancel'))">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11" fill="none">
                            <path
                                d="M0 5.202L5.211 0L5.877 0.684C6.015 0.828 6.069 0.972 6.039 1.116C6.015 1.254 5.94 1.386 5.814 1.512L3.609 3.708C3.297 4.02 3.012 4.278 2.754 4.482C3.102 4.434 3.468 4.398 3.852 4.374C4.242 4.344 4.635 4.329 5.031 4.329H16.074V6.084H5.031C4.629 6.084 4.233 6.072 3.843 6.048C3.459 6.024 3.093 5.988 2.745 5.94C2.877 6.042 3.012 6.156 3.15 6.282C3.294 6.408 3.447 6.549 3.609 6.705L5.832 8.919C5.958 9.045 6.033 9.18 6.057 9.324C6.087 9.462 6.033 9.6 5.895 9.738L5.229 10.431L0 5.202Z"
                                fill="#3B3731" />
                        </svg>
                        <span
                            x-text="activeServiceMenu === 'add-ons' ? 'Add-ons List' : (activeServiceMenu === 'service-area' ? 'Service Area' : 'Service List')"></span>
                    </button>
                    <div class="services-header-title-row">
                        <div class="services-header-titles">
                            <h2
                                x-text="serviceFormOpen ? (activeServiceMenu === 'service-area' ? (serviceFormTitle || 'Add Service Area') : serviceFormTitle) : (activeServiceMenu === 'add-ons' ? 'Add-ons' : (activeServiceMenu === 'pet-preferences' ? 'Pet Preferences' : (activeServiceMenu === 'service-area' ? 'Service Area' : 'Services')))">
                            </h2>
                            <p
                                x-text="serviceFormOpen && activeServiceMenu === 'services' ? 'Manage what this service includes, who it’s for, how long it takes and what it costs.' : (serviceFormOpen && activeServiceMenu === 'add-ons' ? 'Manage what this add-ons includes, who it’s for, how long it takes and what it costs.' : (serviceFormOpen && activeServiceMenu === 'service-area' ? 'Set the centre point and how far you’ll travel from it.' : (activeServiceMenu === 'service-area' ? 'Manage your service area, add-ons, pet preferences and service area.' : 'Manage your services, add-ons, pet preferences and service area.')))"
                                style="font-weight: 400;">
                            </p>
                        </div>
                        <button type="button" class="services-header-pill services-header-pill-delete" x-cloak
                            x-show="serviceFormOpen && isEditingService && (activeServiceMenu === 'services' || activeServiceMenu === 'add-ons' || activeServiceMenu === 'service-area')"
                            @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } })); window.dispatchEvent(new CustomEvent(activeServiceMenu === 'service-area' ? 'service-area-form-delete' : 'service-form-delete'))">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none"
                                aria-hidden="true">
                                <path
                                    d="M4.286 1.5h3.428M1.714 3.23h8.572M9.857 3.23v7.77c0 .466-.377.844-.843.844H2.986a.844.844 0 0 1-.843-.844V3.23m2.286 1.73v4.5m2.743-4.5v4.5"
                                    stroke="#FF6E6E" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span
                                x-text="activeServiceMenu === 'add-ons' ? 'Delete Add-on' : (activeServiceMenu === 'service-area' ? 'Delete area' : 'Delete Service')"></span>
                        </button>
                    </div>
                </div>
                <button type="button" class="services-header-pill" x-cloak
                    x-show="!serviceFormOpen && activeServiceMenu !== 'pet-preferences'"
                    @click="window.dispatchEvent(new CustomEvent('service-add-requested', { detail: { menu: activeServiceMenu } }))">
                    <span
                        x-text="activeServiceMenu === 'add-ons' ? '+ Add Add-ons' : (activeServiceMenu === 'service-area' ? '+ Add Service Area' : '+ Add Service')"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="section-container" x-data="{ mountedSections: [@js($dashboardActiveSection)] }" x-init="const restoreServicesMenu = () => {
        if (activeSection === 'services') {
            window.dispatchEvent(new CustomEvent('services-menu-selected', { detail: { menu: @js($dashboardNav['active_service_menu']) } }));
        }
    };
    const restoreEarningsMenu = () => {
        if (activeSection === 'earnings') {
            window.dispatchEvent(new CustomEvent('earnings-menu-selected', { detail: { menu: @js($dashboardNav['active_earnings_menu']) } }));
        }
    };
    const easeSectionHeight = () => {
        const el = $el;
        const from = el.offsetHeight;
        el.style.minHeight = from + 'px';
        requestAnimationFrame(() => {
            const active = el.querySelector('.section-panel.section-active');
            const to = active ? active.offsetHeight : from;
            el.style.transition = 'min-height 0.38s cubic-bezier(0.22, 1, 0.36, 1)';
            el.style.minHeight = to + 'px';
            const clear = () => {
                el.style.minHeight = '';
                el.style.transition = '';
            };
            el.addEventListener('transitionend', clear, { once: true });
            setTimeout(clear, 450);
        });
    };
    restoreServicesMenu();
    restoreEarningsMenu();
    $watch('activeSection', (section) => {
        if (!mountedSections.includes(section)) {
            mountedSections.push(section);
        }
        if (section === 'services') {
            restoreServicesMenu();
        }
        if (section === 'earnings') {
            restoreEarningsMenu();
        }
        if (section === 'business-hub') {
            requestAnimationFrame(() => {
                window.dispatchEvent(new CustomEvent('business-hub-mounted'));
                window.scheduleWeeklyRevenueChartInit?.();
            });
        }
        if (section === 'earnings') {
            requestAnimationFrame(() => {
                window.dispatchEvent(new CustomEvent('earnings-mounted'));
                window.scheduleEarningsChartsInit?.();
            });
        }
        $nextTick(() => {
            requestAnimationFrame(() => requestAnimationFrame(easeSectionHeight));
        });
    });">
        <template x-if="mountedSections.includes('business-hub')">
            <div class="section-panel" :class="{ 'section-active': activeSection === 'business-hub' }" x-init="$nextTick(() => {
                requestAnimationFrame(() => {
                    window.dispatchEvent(new CustomEvent('business-hub-mounted'));
                    window.scheduleWeeklyRevenueChartInit?.();
                });
            })">
                <x-business-hub.business-hub />
            </div>
        </template>
        <template x-if="mountedSections.includes('bookings')">
            <div class="section-panel" :class="{ 'section-active': activeSection === 'bookings' }">
                <x-business-hub.bookings />
            </div>
        </template>
        <template x-if="mountedSections.includes('availability')">
            <div class="section-panel" :class="{ 'section-active': activeSection === 'availability' }">
                <x-business-hub.availability />
            </div>
        </template>
        <template x-if="mountedSections.includes('manage-availability')">
            <div class="section-panel" :class="{ 'section-active': activeSection === 'manage-availability' }"
                x-init="$nextTick(() => window.dispatchEvent(new CustomEvent('manage-availability-mounted')))">
                <x-business-hub.availability.manage-availability />
            </div>
        </template>
        <template x-if="mountedSections.includes('services')">
            <div class="section-panel" :class="{ 'section-active': activeSection === 'services' }">
                <x-business-hub.services />
            </div>
        </template>
        <div @class([
            'section-panel',
            'section-active' => $dashboardActiveSection === 'clients',
        ])
            :class="{ 'section-active': activeSection === 'clients' }">
            <x-business-hub.clients />
        </div>
        <template x-if="mountedSections.includes('earnings')">
            <div class="section-panel" :class="{ 'section-active': activeSection === 'earnings' }" x-init="$nextTick(() => {
                requestAnimationFrame(() => {
                    window.dispatchEvent(new CustomEvent('earnings-mounted'));
                    window.scheduleEarningsChartsInit?.();
                });
            })">
                <x-business-hub.earnings />
            </div>
        </template>
        <template x-if="mountedSections.includes('settings')">
            <div class="section-panel" :class="{ 'section-active': activeSection === 'settings' }">
                <x-business-hub.settings />
            </div>
        </template>
    </div>
</section>

<style>
    [x-cloak] {
        display: none !important;
    }

    .dashboard-wrapper>main {
        position: relative;
    }

    .dashboard-content-wrapper {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .active-section-header {
        color: #3B3731;
        text-align: left;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
        position: relative;
    }

    .active-section-header h2,
    .active-section-header>div>h2 {
        color: #3B3731;
        text-align: left;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
    }

    .active-section-header h2.active-section-header-welcome {
        text-transform: none;
    }

    .active-section-header p,
    .active-section-header>div>p {
        margin: 0.2rem 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: 20px;
        text-transform: none;
    }

    .active-section-loading-bar {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        overflow: hidden;
        z-index: 20;
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
        gap: 5px;
        width: 201px;
        height: 48px;
        overflow: hidden;
        border-radius: 100px;
        background: #3B3731;
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        border: 0;
        cursor: pointer;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }

    .availability-header-pill img {
        display: block;
        flex-shrink: 0;
        width: 12px;
        height: 12px;
        object-fit: contain;
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
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .active-section-header-manage-availability .manage-availability-back-btn {
        margin-bottom: 0;
    }

    .active-section-header-services {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .services-header-copy {
        min-width: 0;
        flex: 1;
        text-align: left;
    }

    .services-header-copy h2 {
        text-transform: none !important;
    }

    .services-header-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .services-header-titles {
        min-width: 0;
    }

    .services-header-title-row .services-header-pill-delete {
        margin-left: 0;
        align-self: center;
    }

    .services-header-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        min-width: 138px;
        height: 48px;
        padding: 0 1.15rem;
        margin-left: auto;
        border-radius: 100px;
        border: 1px solid #E2E2E2;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        cursor: pointer;
        white-space: nowrap;
        transition: opacity 0.15s ease, transform 0.15s ease;
    }

    .services-header-pill:hover {
        opacity: 0.92;
    }

    .services-header-pill:active {
        transform: translateY(1px);
    }

    .services-header-pill-delete {
        min-width: 162px;
        height: 42px;
        gap: 8px;
        border-color: #ff6e6e;
        color: #ff6e6e;
        box-shadow: 0 0 5px 2px rgba(59, 55, 49, 0.1);
        font-weight: 400;
    }

    .services-header-pill-delete svg {
        flex-shrink: 0;
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

    .service-list-header-btn {
        border: 0;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        padding: 0;
    }

    .active-section-header-stack {
        display: grid;
        min-height: 3.5rem;
    }

    .active-section-header-pane {
        grid-area: 1 / 1;
        width: 100%;
        margin-bottom: 2rem;
    }

    .active-section-header-clients {
        margin-bottom: 40px;
    }

    .section-container {
        position: relative;
        overflow: hidden;
    }

    .section-container:has(.section-panel.section-active .ma-board),
    .section-container:has(.section-panel.section-active .service-form-footer),
    .section-container:has(.section-panel.section-active .clients-list-table-shell) {
        overflow: visible;
    }

    .section-panel {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.38s cubic-bezier(0.22, 1, 0.36, 1), transform 0.38s cubic-bezier(0.22, 1, 0.36, 1);
        pointer-events: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        visibility: hidden;
        z-index: 0;
    }

    .section-panel.section-active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        position: relative;
        visibility: visible;
        z-index: 1;
    }

    @media (prefers-reduced-motion: reduce) {
        .section-panel {
            transition: none;
            transform: none;
        }
    }
</style>
