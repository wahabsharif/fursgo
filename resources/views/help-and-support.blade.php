<section class="container mb-5 mt-5">
    <livewire:help.centre />
</section>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
    <script>
        (function () {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            const toTop = function () {
                const root = document.scrollingElement || document.documentElement;
                root.scrollTop = 0;
                document.body.scrollTop = 0;
                window.scrollTo(0, 0);
            };

            toTop();
            window.addEventListener('pageshow', toTop);
            window.addEventListener('load', toTop);
        })();
    </script>
@endpush
