<!-- app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $segments = request()->segments();

        if (empty($segments)) {
            $pageTitle = 'Fursgo';
        } else {
            $pageTitle =
                'Fursgo - ' .
                collect($segments)->map(fn($s) => ucfirst(str_replace(['-', '_'], ' ', $s)))->implode(' - ');
        }
    @endphp

    <title>@yield('title', $pageTitle)</title>

    @include('partials.head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @yield('styles')
    @stack('styles')

</head>

@php
    $initialDashboardNavView = match (true) {
        request()->routeIs('business-homepage-groomer-space-owner') => 'for-groomers-hosts',
        request()->routeIs('help-and-support') => 'help-centre',
        default => 'hub',
    };
@endphp

<body x-data="{
    activeSection: 'business-hub',
    dashboardNavView: @js($initialDashboardNavView),
    dashboardUrls: {
        hub: @js(route('dashboard')),
        forGroomers: @js(route('business-homepage-groomer-space-owner')),
        help: @js(route('help-and-support')),
    },
    dashboardUrlForView(view) {
        if (view === 'for-groomers-hosts') return this.dashboardUrls.forGroomers;
        if (view === 'help-centre') return this.dashboardUrls.help;
        return this.dashboardUrls.hub;
    },
    syncDashboardNavViewFromUrl() {
        const path = window.location.pathname.replace(/\/$/, '');
        const forGroomersPath = new URL(this.dashboardUrls.forGroomers, window.location.origin).pathname.replace(/\/$/, '');
        const helpPath = new URL(this.dashboardUrls.help, window.location.origin).pathname.replace(/\/$/, '');
        const hubPath = new URL(this.dashboardUrls.hub, window.location.origin).pathname.replace(/\/$/, '');

        if (path === forGroomersPath) {
            this.dashboardNavView = 'for-groomers-hosts';
        } else if (path === helpPath) {
            this.dashboardNavView = 'help-centre';
        } else if (path === hubPath) {
            this.dashboardNavView = 'hub';
        }
    },
    pushDashboardUrl(view) {
        const url = this.dashboardUrlForView(view);
        if (window.location.pathname.replace(/\/$/, '') === new URL(url, window.location.origin).pathname.replace(/\/$/, '')) {
            return;
        }
        history.pushState({ dashboardNavView: view }, '', url);
    },
    showDashboardHub() {
        this.dashboardNavView = 'hub';
        this.pushDashboardUrl('hub');
        this.scrollDashboardToTop(true);
    },
    showDashboardInfoView(view) {
        this.dashboardNavView = view;
        this.pushDashboardUrl(view);
        this.scrollDashboardToTop(true);
        if (view === 'for-groomers-hosts') {
            requestAnimationFrame(() => {
                document.querySelector('.dashboard-info-panel--for-groomers')
                    ?.dispatchEvent(new CustomEvent('bgs-homepage-mounted'));
            });
        }
    },
    scrollDashboardToTop(smooth = false) {
        const root = document.scrollingElement || document.documentElement;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const behavior = smooth && !reduceMotion ? 'smooth' : 'auto';

        if (smooth && root.scrollTop < 40) {
            return;
        }

        const go = () => window.scrollTo({ top: 0, left: 0, behavior });

        if (smooth) {
            requestAnimationFrame(() => setTimeout(go, 50));
            return;
        }

        root.scrollTop = 0;
        document.body.scrollTop = 0;
        go();
        requestAnimationFrame(() => {
            root.scrollTop = 0;
            document.body.scrollTop = 0;
            setTimeout(() => {
                root.scrollTop = 0;
                document.body.scrollTop = 0;
            }, 350);
        });
    },
}" x-init="syncDashboardNavViewFromUrl();
scrollDashboardToTop(false);
if (history.state?.dashboardNavView === undefined) {
    history.replaceState({ dashboardNavView: dashboardNavView }, '', window.location.href);
}
$watch('activeSection', () => scrollDashboardToTop(true));" @nav-list-loading-start.window="showDashboardHub()">

    <x-common.header variant="dashboard" />
    <x-common.dev-mode-float />

    <div class="dashboard-wrapper" x-show="dashboardNavView === 'hub'" x-cloak>
        <x-common.sidebar variant="dashboard" />

        <main style="width: 100%">
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>

    <x-common.footer variant="dashboard" />

    <style>
        [x-cloak] {
            display: none !important;
        }

        .dashboard-wrapper {
            position: relative;
        }
    </style>

    <!-- <script src="{{ asset('js/common.js') }}" defer></script> -->
    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <!-- <script src="{{ asset('js/customer_journey.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/profile.js') }}" defer></script> -->

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- Pushed from embedded views (e.g. help-and-support); head @stack runs before body --}}
    @stack('styles')

    @stack('script')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.scrollTo(0, 0);
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        });

        window.addEventListener('popstate', (event) => {
            const body = document.body;
            if (!body._x_dataStack || typeof Alpine === 'undefined') {
                return;
            }

            const data = Alpine.$data(body);
            if (!data || typeof data.syncDashboardNavViewFromUrl !== 'function') {
                return;
            }

            if (event.state?.dashboardNavView) {
                data.dashboardNavView = event.state.dashboardNavView;
            } else {
                data.syncDashboardNavViewFromUrl();
            }

            if (data.dashboardNavView === 'for-groomers-hosts') {
                requestAnimationFrame(() => {
                    document.querySelector('.dashboard-info-panel--for-groomers')
                        ?.dispatchEvent(new CustomEvent('bgs-homepage-mounted'));
                });
            }

            data.scrollDashboardToTop(true);
        });

        document.addEventListener('livewire:navigated', () => {
            const body = document.body;
            if (body._x_dataStack && typeof Alpine !== 'undefined') {
                const data = Alpine.$data(body);
                if (data && typeof data.syncDashboardNavViewFromUrl === 'function') {
                    data.syncDashboardNavViewFromUrl();
                    if (history.state?.dashboardNavView === undefined) {
                        history.replaceState({
                            dashboardNavView: data.dashboardNavView
                        }, '', window.location.href);
                    }
                }
            }

            const root = document.scrollingElement || document.documentElement;
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (root.scrollTop < 40) {
                return;
            }
            const behavior = reduceMotion ? 'auto' : 'smooth';
            requestAnimationFrame(() => {
                window.scrollTo({
                    top: 0,
                    left: 0,
                    behavior
                });
            });
        });

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                window.scrollTo(0, 0);
                document.documentElement.scrollTop = 0;
                document.body.scrollTop = 0;
            }
        });
    </script>

</body>

</html>
