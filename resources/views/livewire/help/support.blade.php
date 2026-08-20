<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('Fursgo - Help & Support')] class extends Component {
    //
}; ?>

<div>
    @include('help-and-support')
</div>
