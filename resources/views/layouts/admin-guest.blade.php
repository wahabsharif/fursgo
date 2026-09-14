<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Fursgo Admin - Log in</title>

    @include('partials.head')
    @stack('styles')
</head>

<body class="admin-guest-body">
    {{ $slot }}

    @stack('script')
</body>

</html>
