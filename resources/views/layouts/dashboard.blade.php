{{-- Shared dashboard shell for Business Hub, Account Settings, Help Centre, Verify & Qualify, etc. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        use App\Support\BusinessHubNav;

        $segments = request()->segments();

        if (empty($segments)) {
            $pageTitle = 'Fursgo';
        } else {
            $formattedSegments = array_map(fn($segment) => ucfirst(str_replace(['-', '_'], ' ', $segment)), $segments);

            $pageTitle = 'Fursgo - ' . implode(' - ', $formattedSegments);
        }

        $dashboardNavView = match (true) {
            request()->routeIs('business-homepage-groomer-space-owner') => 'for-groomers-hosts',
            request()->routeIs('help-and-support') => 'help-centre',
            request()->routeIs('account-settings') => 'account-settings',
            request()->routeIs('business-verification', 'business-verification.*') => 'business-verification',
            default => 'hub',
        };

        $isDashboardHub = $dashboardNavView === 'hub';
        $dashboardNav = $isDashboardHub ? BusinessHubNav::fromSession() : null;
        $dashboardActiveSection = $dashboardNav['active_section'] ?? 'business-hub';
    @endphp

    <title>@yield('title', $pageTitle)</title>

    <x-partials.head />
    @if (request()->routeIs('account-settings'))
        <link rel="stylesheet" href="{{ asset('css/account-settings.css') }}">
    @endif
    @if ($isDashboardHub)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        @php
            $bhAvatarBg = \App\Support\BusinessHubAvatar::background();
        @endphp
        <style>
            :root {
                --bh-avatar-bg: {{ $bhAvatarBg }};
                --bh-avatar-ring: {{ $bhAvatarBg }};
            }

            .bh-avatar {
                --bh-avatar-size: 42px;
                --bh-avatar-font: 20px;
                position: relative;
                box-sizing: border-box;
                width: var(--bh-avatar-size);
                height: var(--bh-avatar-size);
                border-radius: 999px;
                overflow: hidden;
                display: inline-block;
                flex-shrink: 0;
                vertical-align: middle;
            }

            .bh-avatar.has-photo {
                background: #fff;
                overflow: visible;
            }

            .bh-avatar.is-fallback {
                background: var(--bh-avatar-bg, #FFC97A);
                overflow: hidden;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .bh-avatar__ring {
                position: absolute;
                inset: 0;
                width: 100% !important;
                height: 100% !important;
                max-width: none;
                color: var(--bh-avatar-ring, var(--bh-avatar-bg, #FFC97A));
                pointer-events: none;
                z-index: 0;
            }

            .bh-avatar__ring circle {
                fill: #fff;
                stroke: currentColor;
            }

            .bh-avatar.is-fallback .bh-avatar__ring,
            .bh-avatar__ring[hidden] {
                display: none !important;
            }

            .bh-avatar__photo,
            .bh-avatar.has-photo>img.bh-avatar__photo {
                position: absolute !important;
                top: 2px;
                left: 2px;
                right: auto;
                bottom: auto;
                z-index: 1;
                box-sizing: border-box;
                width: calc(100% - 4px) !important;
                height: calc(100% - 4px) !important;
                max-width: none !important;
                margin: 0 !important;
                border-radius: 999px;
                object-fit: cover !important;
                object-position: center;
                display: block;
            }

            .bh-avatar__initials {
                position: relative;
                z-index: 1;
                width: 100%;
                height: 100%;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #FDFDFD;
                text-align: center;
                font-family: Lato, sans-serif;
                font-size: var(--bh-avatar-font);
                font-style: normal;
                font-weight: 800;
                line-height: normal;
                letter-spacing: 0;
            }

            .bh-avatar img[hidden],
            .bh-avatar [data-bh-avatar-fallback][hidden],
            [data-bh-avatar]>img[hidden],
            [data-bh-avatar] [data-bh-avatar-fallback][hidden] {
                display: none !important;
            }

            /* Photo ring for wrappers without an inline SVG ring */
            [data-bh-avatar].has-photo:not(.bh-avatar):not(:has(> [data-bh-avatar-ring])) {
                background: #fff !important;
                border: 1px solid var(--bh-avatar-ring, var(--bh-avatar-bg, #FFC97A)) !important;
                padding: 1px !important;
                box-sizing: border-box;
                overflow: hidden;
            }

            [data-bh-avatar].has-photo:not(.bh-avatar):not(:has(> [data-bh-avatar-ring]))>img {
                width: 100% !important;
                height: 100% !important;
                max-width: none !important;
                border-radius: inherit;
                object-fit: cover !important;
                object-position: center;
                display: block;
            }

            /* Component / SVG-ring avatars: ring drawn by SVG under the photo */
            .bh-avatar.has-photo,
            [data-bh-avatar].has-photo:has(> [data-bh-avatar-ring]) {
                background: transparent !important;
                border: 0 !important;
                padding: 0 !important;
            }

            [data-bh-avatar].has-photo:has(> [data-bh-avatar-ring]) {
                position: relative;
                overflow: visible;
            }

            [data-bh-avatar].has-photo:has(> [data-bh-avatar-ring])>[data-bh-avatar-ring],
            [data-bh-avatar].has-photo:has(> [data-bh-avatar-ring])>.bh-avatar__ring {
                position: absolute;
                inset: 0;
                width: 100% !important;
                height: 100% !important;
                max-width: none;
                z-index: 0;
                pointer-events: none;
                color: var(--bh-avatar-ring, var(--bh-avatar-bg, #FFC97A));
            }

            [data-bh-avatar].has-photo:has(> [data-bh-avatar-ring])>img.bh-avatar__photo,
            [data-bh-avatar].has-photo:has(> [data-bh-avatar-ring])>img:not([data-bh-avatar-ring]) {
                position: absolute !important;
                top: 2px;
                left: 2px;
                right: auto;
                bottom: auto;
                z-index: 1;
                width: calc(100% - 4px) !important;
                height: calc(100% - 4px) !important;
                max-width: none !important;
                margin: 0 !important;
                border-radius: 999px;
                object-fit: cover !important;
                object-position: center;
                display: block;
            }

            [data-bh-avatar].is-fallback,
            [data-bh-avatar].is-initials {
                border: 0 !important;
                padding: 0 !important;
            }

            /* Unify existing hub fallbacks to logged-in accent + initials typography */
            .clients-avatar-wrap.is-initials,
            .clients-avatar-wrap.is-fallback,
            .clients-pet-avatar-fallback,
            .client-profile-avatar-fallback,
            .pending-detail-avatar.is-fallback,
            .pending-detail-pet-avatar.is-fallback,
            .booking-details-avatar.is-fallback,
            .booking-details-pet-avatar.is-fallback,
            .earnings-recent-avatar-initials,
            .earnings-receipt-modal__avatar span,
            .invoice-preview-avatar-fallback,
            .client-history-modal__avatar span,
            .cancelled-booking-modal-avatar.is-fallback,
            .ma-staff-avatar.is-fallback,
            .client-pet-card__avatar.is-fallback,
            .client-pet-profile-card__avatar.is-fallback {
                background: var(--bh-avatar-bg, #FFC97A) !important;
                color: #FDFDFD !important;
                text-align: center;
                font-family: Lato, sans-serif !important;
                font-style: normal;
                font-weight: 800 !important;
                line-height: normal;
            }
        </style>
        <script>
            window.bhAvatarFallback = function(img) {
                if (!img || img.dataset.bhAvatarFailed === '1') {
                    return;
                }
                img.dataset.bhAvatarFailed = '1';
                img.hidden = true;
                img.removeAttribute('src');
                img.alt = '';

                const root = img.closest('[data-bh-avatar]');
                if (!root) {
                    return;
                }

                root.classList.remove('has-photo');
                root.classList.add('is-fallback');
                root.setAttribute('aria-hidden', 'true');

                const ring = root.querySelector('[data-bh-avatar-ring]');
                if (ring) {
                    ring.hidden = true;
                }

                const fallback = root.querySelector('[data-bh-avatar-fallback]');
                if (fallback) {
                    fallback.hidden = false;
                }
            };
        </script>
    @endif
    @if ($dashboardNavView === 'business-verification')
        <link rel="preload" as="image" href="{{ asset('images/logo/logo.svg') }}">
        <style>
            .dashboard-header {
                position: sticky;
                top: 0;
                z-index: 1020;
                width: 100%;
                background: #fff;
            }

            .dashboard-header.dashboard-header--business-verification .dashboard-navbar {
                min-height: 0;
                padding: 50px 0 40px !important;
            }
        </style>
    @endif
    @yield('styles')
    @stack('styles')

    @if ($isDashboardHub || $dashboardNavView === 'help-centre')
        <script>
            (function() {
                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual';
                }

                window.__scrollDashboardToTop = function() {
                    const root = document.scrollingElement || document.documentElement;
                    root.scrollTop = 0;
                    document.body.scrollTop = 0;
                    window.scrollTo(0, 0);
                };

                window.__scrollDashboardToTop();
            })();
        </script>
    @endif

</head>

<body class="dashboard-shell dashboard-shell--{{ $dashboardNavView }}" x-data="{
    activeSection: @js($dashboardActiveSection),
    scrollDashboardToTop(smooth = false) {
        const root = document.scrollingElement || document.documentElement;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const behavior = smooth && !reduceMotion ? 'smooth' : 'auto';
        if (smooth && root.scrollTop < 40) return;
        root.scrollTop = 0;
        document.body.scrollTop = 0;
        window.scrollTo({ top: 0, left: 0, behavior });
    },
    persistBusinessHubNav(detail = {}) {
        if (!window.__dashboardNavUrl) return;
        const payload = { section: detail.section ?? this.activeSection };
        if (detail.active_booking_status !== undefined) {
            payload.active_booking_status = detail.active_booking_status;
        }
        if (detail.active_service_menu !== undefined) {
            payload.active_service_menu = detail.active_service_menu;
        }
        if (detail.active_earnings_menu !== undefined) {
            payload.active_earnings_menu = detail.active_earnings_menu;
        }
        if (detail.active_settings_menu !== undefined) {
            payload.active_settings_menu = detail.active_settings_menu;
        }
        fetch(window.__dashboardNavUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.__dashboardNavCsrf,
                Accept: 'application/json',
            },
            body: JSON.stringify(payload),
            keepalive: true,
        }).catch(() => {});
    },
}" x-init="scrollDashboardToTop(false);
$watch('activeSection', (section) => {
    scrollDashboardToTop(true);
    persistBusinessHubNav({ section });
});
window.addEventListener('pageshow', () => scrollDashboardToTop(false));
window.addEventListener('load', () => scrollDashboardToTop(false));
window.addEventListener('dashboard-nav-changed', (event) => persistBusinessHubNav(event.detail ?? {}));">

    <x-common.header variant="dashboard" :dashboard-nav-view="$dashboardNavView" />

    @if ($isDashboardHub)
        <div class="dashboard-wrapper">
            <x-common.sidebar variant="dashboard" />

            <main style="width: 100%">
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    @else
        <main class="dashboard-info-main">
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    @endif

    <x-common.footer variant="dashboard" />

    <style>
        [x-cloak] {
            display: none !important;
        }

        .dashboard-shell {
            background-color: #fff;
        }

        .dashboard-wrapper {
            position: relative;
        }

        .dashboard-info-main {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 50px 2rem;
            box-sizing: border-box;
        }

        @media (max-width: 1199.98px) {
            .dashboard-info-main {
                padding-left: 24px;
                padding-right: 24px;
            }
        }

        @media (max-width: 767.98px) {
            .dashboard-info-main {
                padding-left: 16px;
                padding-right: 16px;
            }
        }

        .dashboard-shell--for-groomers-hosts .dashboard-info-main .container,
        .dashboard-shell--help-centre .dashboard-info-main .container {
            margin-left: auto;
            margin-right: auto;
        }

        .dashboard-shell--help-centre {
            --help-tabs-sticky-top: 5.5rem;
        }

        /* Verify & Qualify: content width matches header (Bootstrap .container only) */
        .dashboard-shell--business-verification {
            --dashboard-sticky-header-offset: 8.125rem;
        }

        .dashboard-shell--business-verification .dashboard-info-main {
            max-width: 100%;
            width: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .dashboard-shell--business-verification .dashboard-info-main>.container {
            margin-left: auto;
            margin-right: auto;
        }
    </style>

    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>

    @if ($isDashboardHub)
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
        <script src="{{ asset('js/weekly-revenue-chart.js') }}"></script>
        <script src="{{ asset('js/earnings-charts.js') }}"></script>
    @endif

    @fluxScripts

    @stack('styles')
    @stack('script')

    <script>
        window.__dashboardNavUrl = @json(route('business-hub.nav'));
        window.__dashboardNavCsrf = @json(csrf_token());

        document.addEventListener('DOMContentLoaded', () => {
            window.__scrollDashboardToTop?.();

            const panel = document.querySelector('.dashboard-info-panel--for-groomers');
            if (panel) {
                panel.dispatchEvent(new CustomEvent('bgs-homepage-mounted'));
            }

            const helpPanel = document.querySelector('.dashboard-info-panel--help-centre');
            if (helpPanel) {
                helpPanel.dispatchEvent(new CustomEvent('help-centre-mounted'));
            }
        });

        document.addEventListener('livewire:initialized', () => {
            requestAnimationFrame(() => {
                window.__scrollDashboardToTop?.();
            });
        });

        document.addEventListener('livewire:navigated', () => {
            requestAnimationFrame(() => {
                document.querySelector('.dashboard-info-panel--for-groomers')
                    ?.dispatchEvent(new CustomEvent('bgs-homepage-mounted'));
                document.querySelector('.dashboard-info-panel--help-centre')
                    ?.dispatchEvent(new CustomEvent('help-centre-mounted'));
                window.scheduleWeeklyRevenueChartInit?.();
                window.scheduleEarningsChartsInit?.();
            });

            const root = document.scrollingElement || document.documentElement;
            if (root.scrollTop < 40) {
                return;
            }
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: reduceMotion ? 'auto' : 'smooth',
            });
        });
    </script>

</body>

</html>
