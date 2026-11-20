<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

/**
 * Checkout — Book a Groomer (design shell from custom checkout_booking_groomer).
 */
new #[Layout('layouts.app'), Title('Fursgo - Book a Groomer')] class extends Component {
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

        // Demo groomers for step 3 (Choose your groomer)
        $groomers = [
            [
                'id' => 'any',
                'name' => 'Any groomer',
                'title' => '',
                'subtitle' => 'First available · fastest booking',
                'image' => '',
                'selected' => false,
            ],
            [
                'id' => 'rebecca',
                'name' => 'Rebecca',
                'title' => 'Senior Groomer',
                'subtitle' => 'Senior Groomer • 8 yrs',
                'image' => 'images/message_profile_1.png',
                'selected' => true,
            ],
            [
                'id' => 'jeremy',
                'name' => 'Jeremy',
                'title' => 'Cat & small-pet specialist',
                'subtitle' => 'Cat & small-pet specialist • 5 yrs',
                'image' => 'images/block_user_1.png',
                'selected' => false,
            ],
            [
                'id' => 'tanya',
                'name' => 'Tanya',
                'title' => 'Junior Groomer',
                'subtitle' => 'Junior Groomer • 3 yrs',
                'image' => 'images/block_user_2.png',
                'selected' => false,
            ],
        ];

        return [
            'pets' => $pets,
            'groomers' => $groomers,
            'studioImage' => asset('images/card1.png'),
        ];
    }
}; ?>

<div>
    @include('partials.checkout.booking-groomer-content', [
        'pets' => $pets,
        'groomers' => $groomers,
        'studioImage' => $studioImage,
    ])
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout_booking_groomer.css') }}">
@endpush

@push('script')
    <script>
        // Mark page + set asset base URL for the checkout JS
        (function () {
            document.body.classList.add('cbg-page');

            var meta = document.querySelector('meta[name="asset-base-url"]');
            var base = '/';

            if (meta && meta.content) {
                base = meta.content.replace(/\/?$/, '/');
            }

            document.body.dataset.baseUrl = base;
        })();
    </script>
    <script src="{{ asset('js/checkout_booking_groomer.js') }}"></script>
@endpush
