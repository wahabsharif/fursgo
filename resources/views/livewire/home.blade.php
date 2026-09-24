<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    /**
     * Homepage shell — UI from custom index.php.
     * Search forms GET to /search-results with mode, pet_type, pet_size, serviceType, search.
     * Swap static sections (testimonials, rating copy) to Eloquent via with() later.
     */
    public function with(): array
    {
        return [
            // 'testimonials' => [],
            // 'ratingAverage' => 4.9,
            // 'ratingCountLabel' => '5,000 +',
        ];
    }
}; ?>

<div>
    <x-home.cookie-consent />

    @include('home')
</div>
