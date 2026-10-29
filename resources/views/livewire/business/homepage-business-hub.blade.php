<?php

use App\Support\GroomersHostsContent;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.dashboard')] class extends Component {
    /**
     * For Groomers & Hosts inside the business-hub shell.
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
    <x-business-hub.for-groomers-hosts :groomers-hosts="$groomersHosts" />
</div>
