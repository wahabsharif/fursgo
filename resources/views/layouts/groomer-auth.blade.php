<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $segments = request()->segments();
        $pageTitle = empty($segments)
            ? 'Fursgo'
            : 'Fursgo - ' .
                collect($segments)->map(fn($s) => ucfirst(str_replace(['-', '_'], ' ', $s)))->implode(' - ');
    @endphp

    <title>{{ $pageTitle }}</title>

    @include('partials.head')
    @stack('styles')
</head>

<body>
    <x-common.header />

    <main>
        {{ $slot }}
    </main>

    @stack('script')
</body>

</html>
