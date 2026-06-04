<?php

use Livewire\Volt\Component;

new class extends Component {
    public function layout(): string
    {
        return auth('groomer_spacer')->check() ? 'layouts.dashboard' : 'layouts.app';
    }
}; ?>

<div>
    @auth('groomer_spacer')
        {{-- Content is rendered in the dashboard header welcome-section --}}
    @else
        @include('business-homepage')
    @endauth
</div>
