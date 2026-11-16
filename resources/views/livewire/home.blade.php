<?php

use App\Support\HomepageContent;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    /**
     * Homepage shell — search forms GET to /search-results.
     * Marketing sections load from homepage_settings / homepage_items.
     */
    public function with(): array
    {
        return [
            'homepage' => HomepageContent::customer(),
        ];
    }
}; ?>

<div class="home-page">
    <x-home.cookie-consent />

    @include('home')
</div>
