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
    @yield('styles')
    @stack('styles')

</head>

<body>

    <x-common.header />
    <x-common.dev-mode-float />

    <main>
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <x-common.chat-btn />
    <x-common.footer />

    <!-- <script src="{{ asset('js/common.js') }}" defer></script> -->
    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <!-- <script src="{{ asset('js/customer_journey.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/profile.js') }}" defer></script> -->

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/weekly-revenue-chart.js') }}"></script>

    @stack('script')

</body>

</html>
