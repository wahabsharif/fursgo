<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('FursGo - Owner Account / Pet Details')] class extends Component {
    //
};
?>

<div>
    @include('partials.login-signup.owner-account-setup-form-content')
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owner_account_setup.css') }}">
    <style>
        header:not(.dashboard-header):not(.header-business-public) {
            background-color: #fdfcf8;
        }
    </style>
@endpush

@push('script')
    <script src="{{ asset('js/owner_account_setup.js') }}" data-navigate-track></script>
@endpush
