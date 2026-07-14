<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    // Help search - static content wrapper
}; ?>

<div>
    @include('search')
</div>
