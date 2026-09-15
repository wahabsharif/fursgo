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

    <x-partials.head />
    @yield('styles')
    @stack('styles')

</head>

<body>

    <x-common.header />

    <main>
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <x-common.footer />

    <!-- <script src="{{ asset('js/common.js') }}" defer></script> -->
    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <!-- <script src="{{ asset('js/customer_journey.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/profile.js') }}" defer></script> -->

    @stack('script')

</body>

</html>