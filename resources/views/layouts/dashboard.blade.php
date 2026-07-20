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
            request()->routeIs('verify-qualify', 'verify-qualify.*') => 'verify-qualify',
            default => 'hub',
        };

        $isDashboardHub = $dashboardNavView === 'hub';
        $dashboardNav = $isDashboardHub ? BusinessHubNav::fromSession() : null;
        $dashboardActiveSection = $dashboardNav['active_section'] ?? 'business-hub';
    @endphp

    <title>@yield('title', $pageTitle)</title>

    @include('partials.head')
    @if (request()->routeIs('account-settings'))
        <link rel="stylesheet" href="{{ asset('css/account-settings.css') }}">
    @endif
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @yield('styles')
    @stack('styles')

    @if ($isDashboardHub)
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
    <x-common.dev-mode-float />

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
            max-width: min(100%, max(110rem, 92vw), 2450px);
            margin-left: auto;
            margin-right: auto;
            padding-left: clamp(0.75rem, 4vw, 2rem);
            padding-right: clamp(0.75rem, 4vw, 2rem);
            padding-bottom: 2rem;
            box-sizing: border-box;
        }

        .dashboard-shell--for-groomers-hosts .dashboard-info-main .container,
        .dashboard-shell--help-centre .dashboard-info-main .container {
            margin-left: auto;
            margin-right: auto;
        }

        /* Verify & Qualify: content width matches header (Bootstrap .container only) */
        .dashboard-shell--verify-qualify {
            --dashboard-sticky-header-offset: 9.5rem;
        }

        .dashboard-shell--verify-qualify .dashboard-info-main {
            max-width: 100%;
            width: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .dashboard-shell--verify-qualify .dashboard-info-main>.container {
            margin-left: auto;
            margin-right: auto;
        }
    </style>

    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/weekly-revenue-chart.js') }}"></script>
    <script src="{{ asset('js/earnings-charts.js') }}"></script>

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
