<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('Fursgo - Contact Us')] class extends Component {}; ?>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
@endpush

<div>
    @include('profile_pets_preferences.contact_us')
</div>