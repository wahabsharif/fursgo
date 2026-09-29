<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

/**
 * My Bookings — design shell from custom my_bookings/my_bookings.php.
 */
new #[Layout('layouts.app'), Title('Bookings - My Bookings')] class extends Component {
    // Static design shell (demo booking cards / modals).
}; ?>

<div>
    @include('partials.my-bookings.content')
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/my_bookings.css') }}">
    @include('partials.my-bookings.inline-styles')
@endpush

@push('script')
    <script>
        (function () {
            document.body.classList.add('status-cancel', 'my-bookings-page');
        })();
    </script>
    <script src="{{ asset('js/common.js') }}"></script>
    @include('partials.my-bookings.scripts')
@endpush
