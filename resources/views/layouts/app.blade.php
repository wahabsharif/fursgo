<!-- app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $segments = request()->segments();

        if (empty($segments)) {
            $pageTitle = 'Fursgo';
        } else {
            $pageTitle = 'Fursgo - ' . collect($segments)
                ->map(fn($s) => ucfirst(str_replace(['-', '_'], ' ', $s)))
                ->implode(' - ');
        }
    @endphp

    <title>@yield('title', $pageTitle)</title>

    @include('partials.head')

</head>

<body>

    <x-ui.header />

    <main>
        @yield('content')
    </main>

    <x-ui.chat-btn />
    <x-ui.footer />

    @stack('styles')

    <!-- <script src="{{ asset('js/common.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/custom-dropdown.js') }}" defer></script> -->
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <!-- <script src="{{ asset('js/customer_journey.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/profile.js') }}" defer></script> -->

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')

</body>

</html>
