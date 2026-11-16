<?php

use App\Support\GroomersHostsContent;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    /**
     * For Groomers & Hosts marketing page.
     * Content loads from groomers_hosts_settings / groomers_hosts_items.
     */
    public function with(): array
    {
        return [
            'groomersHosts' => GroomersHostsContent::page(),
        ];
    }
}; ?>

<div>
    @include('business-homepage')
</div>
