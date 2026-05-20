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

<body x-data="{
    activeSection: 'business-hub',
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
}" x-init="scrollDashboardToTop(false);
$watch('activeSection', () => scrollDashboardToTop(true))" @nav-list-loading-start.window="scrollDashboardToTop(true)">

    <x-common.header variant="dashboard" />
    <x-common.dev-mode-float />

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

    <x-common.footer variant="dashboard" />

    <style>
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

    @stack('script')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.scrollTo(0, 0);
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        });

        document.addEventListener('livewire:navigated', () => {
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
