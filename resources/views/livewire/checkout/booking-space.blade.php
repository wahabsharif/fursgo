<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

/**
 * Checkout — Book a Space (design shell from custom checkout_booking_space).
 */
new #[Layout('layouts.app'), Title('Fursgo - Book a Space')] class extends Component {
    public function with(): array
    {
        $pets = [
            [
                'id' => 1,
                'name' => 'Bella',
                'type' => 'Rabbit',
                'breed' => 'Mini Lop',
                'birthday' => '22/08/2018',
                'sex' => 'Female',
                'weight' => '4',
                'image' => 'images/pet_details_1.png',
                'notes' => 'Nervous around hair-dryers.',
            ],
            [
                'id' => 2,
                'name' => 'Louis',
                'type' => 'Dog',
                'breed' => 'Labrador',
                'birthday' => '14/03/2020',
                'sex' => 'Male',
                'weight' => '28',
                'image' => 'images/pet_details_2.png',
                'notes' => 'Allergic to dust.',
            ],
        ];

        return [
            'pets' => $pets,
            'studioImage' => asset('images/booking-space-card-image.svg'),
        ];
    }
}; ?>

<div>
    @include('partials.checkout.booking-space-content', [
        'pets' => $pets,
        'studioImage' => $studioImage,
    ])
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout_booking_space.css') }}">
@endpush

@push('script')
    <script>
        (function () {
            document.body.classList.add('cbg-page');
            var meta = document.querySelector('meta[name="asset-base-url"]');
            var base = (meta && meta.content) ? meta.content.replace(/\/?$/, '/') : '/';
            document.body.dataset.baseUrl = base;
        })();
    </script>
    <script src="{{ asset('js/checkout_booking_space.js') }}"></script>
@endpush
