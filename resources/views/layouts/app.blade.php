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
        <div class="container">
            @yield('content')
        </div>
    </main>

    <x-ui.footer />

    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/custom-dropdown.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/customer_journey.js') }}"></script>
    <script src="{{ asset('js/profile.js') }}"></script>

    @stack('scripts')

</body>

</html>
