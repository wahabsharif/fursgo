<?php

use App\Support\MarketingHubStats;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.marketing-hub')] class extends Component {
    public function with(): array
    {
        return [
            'mh' => MarketingHubStats::forSpacer(auth('groomer_spacer')->id()),
        ];
    }
}; ?>

<div>
    @include('marketing-hub')
</div>
