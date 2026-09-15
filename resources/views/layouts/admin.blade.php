<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="admin-html">

<head>
    <title>{{ $title ?? 'Fursgo Admin' }}</title>

    @include('partials.head')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('styles')
</head>

<body class="admin-body">
    {{ $slot }}

    @stack('script')
</body>

</html>
