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

        $dashboardNavView = match (true) {
            request()->routeIs('business-homepage-groomer-space-owner') => 'for-groomers-hosts',
            request()->routeIs('help-and-support') => 'help-centre',
            request()->routeIs('verify-qualify', 'verify-qualify.*') => 'verify-qualify',
            default => 'hub',
        };

        $isDashboardHub = $dashboardNavView === 'hub';
    @endphp

    <title>@yield('title', $pageTitle)</title>

    @include('partials.head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @yield('styles')
    @stack('styles')

</head>

<body class="dashboard-shell dashboard-shell--{{ $dashboardNavView }}" x-data="{
    activeSection: 'business-hub',
    scrollDashboardToTop(smooth = false) {
        const root = document.scrollingElement || document.documentElement;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const behavior = smooth && !reduceMotion ? 'smooth' : 'auto';
        if (smooth && root.scrollTop < 40) return;
        window.scrollTo({ top: 0, left: 0, behavior });
    },
}" x-init="$watch('activeSection', () => scrollDashboardToTop(true));">

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

    @fluxScripts

    @stack('styles')
    @stack('script')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.scrollTo(0, 0);
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;

            const panel = document.querySelector('.dashboard-info-panel--for-groomers');
            if (panel) {
                panel.dispatchEvent(new CustomEvent('bgs-homepage-mounted'));
            }

            const helpPanel = document.querySelector('.dashboard-info-panel--help-centre');
            if (helpPanel) {
                helpPanel.dispatchEvent(new CustomEvent('help-centre-mounted'));
            }
        });

        document.addEventListener('livewire:navigated', () => {
            requestAnimationFrame(() => {
                document.querySelector('.dashboard-info-panel--for-groomers')
                    ?.dispatchEvent(new CustomEvent('bgs-homepage-mounted'));
                document.querySelector('.dashboard-info-panel--help-centre')
                    ?.dispatchEvent(new CustomEvent('help-centre-mounted'));
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
