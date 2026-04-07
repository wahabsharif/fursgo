<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="asset-base-url" content="{{ rtrim(asset(''), '/') }}/">

<link rel="icon" href="{{ asset('images/logo/favicon.ico') }}" type="image/x-icon">


<!-- CSS -->
<!-- <link rel="stylesheet" href="{{ asset('css/bookings_status.css') }}"> -->
<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('css/change_groomer_space_bookings.css') }}"> -->
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('css/company_information.css') }}"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/custom.css') }}"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/customer_journey.css') }}"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/groomer_space_profile.css') }}"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}"> -->
<link rel="stylesheet" href="{{ asset('css/media_query.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('css/messages.css') }}"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/my_bookings.css') }}"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/notification.css') }}"> -->
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">


<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" /> -->


@stack('styles')
@yield('styles')
@yield('script')

<style>
    /* N-Progress bar */
    #nprogress .bar {
        background: #f9c87c !important;
        height: 5px !important;
        border-top-right-radius: 5px !important;
        border-bottom-right-radius: 5px !important;
    }

    /* The glow/peg element */
    #nprogress .peg {
        box-shadow: 0 0 10px #f9c87c !important;
    }

    /* The spinner (if shown) */
    #nprogress .spinner-icon {
        border-top-color: #f9c87c !important;
        border-left-color: #f9c87c !important;
    }
</style>
