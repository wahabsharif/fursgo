<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        use App\Support\MarketingHubNav;

        $segments = request()->segments();

        if (empty($segments)) {
            $pageTitle = 'Fursgo';
        } else {
            $formattedSegments = array_map(fn($segment) => ucfirst(str_replace(['-', '_'], ' ', $segment)), $segments);

            $pageTitle = 'Fursgo - ' . implode(' - ', $formattedSegments);
        }

        $dashboardNavView = 'marketing-hub';
        $dashboardNav = MarketingHubNav::fromSession();
        $dashboardActiveSection = $dashboardNav['active_section'] ?? 'marketing-hub';
    @endphp

    <title>@yield('title', $pageTitle)</title>

    @include('partials.head')
    @yield('styles')
    @stack('styles')

    <script>
        (function() {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            window.__scrollMarketingHubToTop = function() {
                const root = document.scrollingElement || document.documentElement;
                root.scrollTop = 0;
                document.body.scrollTop = 0;
                window.scrollTo(0, 0);
            };

            window.__scrollMarketingHubToTop();
        })();
    </script>
</head>

<body class="dashboard-shell dashboard-shell--marketing-hub" x-data="{
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
    persistMarketingHubNav(detail = {}) {
        if (!window.__dashboardNavUrl) return;
        const payload = { section: detail.section ?? this.activeSection };
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
    persistMarketingHubNav({ section });
});
window.addEventListener('pageshow', () => scrollDashboardToTop(false));
window.addEventListener('load', () => scrollDashboardToTop(false));
window.addEventListener('dashboard-nav-changed', (event) => persistMarketingHubNav(event.detail ?? {}));">

    <x-common.header variant="dashboard" :dashboard-nav-view="$dashboardNavView" />
    <x-common.dev-mode-float />

    <div class="dashboard-wrapper">
        <x-common.marketing-sidebar variant="dashboard" />

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

        .dashboard-shell {
            background-color: #fff;
        }

        .dashboard-wrapper {
            position: relative;
        }
    </style>

    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>

    @fluxScripts

    @stack('styles')
    @stack('script')

    <script>
        window.__dashboardNavUrl = @json(route('marketing-hub.nav'));
        window.__dashboardNavCsrf = @json(csrf_token());

        document.addEventListener('DOMContentLoaded', () => {
            window.__scrollMarketingHubToTop?.();
        });

        document.addEventListener('livewire:initialized', () => {
            requestAnimationFrame(() => {
                window.__scrollMarketingHubToTop?.();
            });
        });

        document.addEventListener('livewire:navigated', () => {
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
