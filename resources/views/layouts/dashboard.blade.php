<!-- app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $segments = request()->segments();

        if (empty($segments)) {
            $pageTitle = 'Fursgo Dashboard';
        } else {
            $pageTitle =
                'Fursgo Dashboard - ' .
                collect($segments)->map(fn($s) => ucfirst(str_replace(['-', '_'], ' ', $s)))->implode(' - ');
        }
    @endphp

    <title>@yield('title', $pageTitle)</title>

    @include('partials.head')

</head>

<body>

    <x-common.header variant="dashboard" />

    <div class="dashboard-wrapper" x-data="{ activeSection: 'business-hub' }">
        <!-- Active Section Header -->
        <div class="active-section-header"
            x-text="activeSection.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())"></div>

        <x-common.sidebar variant="dashboard" />

        <main>
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>

    <x-common.footer variant="dashboard" />

    <style>
        .active-section-header {
            position: absolute;
            right: 0;
            top: 2rem;
            color: #3B3731;
            text-align: right;
            font-family: "Playfair Display";
            font-size: 28px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-transform: capitalize;
            z-index: 10;
        }

        .dashboard-wrapper {
            position: relative;
        }
    </style>

    @stack('styles')

    <!-- <script src="{{ asset('js/common.js') }}" defer></script> -->
    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <!-- <script src="{{ asset('js/customer_journey.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/profile.js') }}" defer></script> -->

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('script')

</body>

</html>
