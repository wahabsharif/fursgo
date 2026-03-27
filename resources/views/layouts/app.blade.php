<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Laravel')</title>

    <!-- Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" /> -->

    <!-- CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/bookings_status.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/change_groomer_space_bookings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer_journey.css') }}">
    <link rel="stylesheet" href="{{ asset('css/groomer_space_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/media_query.css') }}">
    <link rel="stylesheet" href="{{ asset('css/messages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/my_bookings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    @stack('styles')
</head>

<body>

    <x-ui.header />

    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>

    <x-ui.footer />
    <!-- JS -->
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/custom-dropdown.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/customer_journey.js') }}"></script>
    <script src="{{ asset('js/profile.js') }}"></script>
    <!-- If you have more -->

    @stack('scripts')
</body>

</html>
