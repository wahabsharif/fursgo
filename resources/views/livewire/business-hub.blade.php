<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.dashboard')] class extends Component {
    // Business Hub - can add Livewire functionality later
}; ?>

<div>
    @include('business-hub')
</div>
