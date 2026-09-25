<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('FursGo - Setup Owner Account')] class extends Component {
    //
};
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="setup-page-wrapper d-flex flex-column align-items-center justify-content-center">
                <img src="{{ asset('icons/setup_owner_account.svg') }}" alt="Setup Owner Account Icon" width="151"
                    height="100">
                <h1 class="heading">Setup Owner Account</h1>
                <a href="{{ route('login-signup.owner-account-setup-form') }}"
                    class="btn-custom btn-active-bg text-center">Lets get started!</a>
                <a href="{{ route('home') }}" class="skip" wire:navigate>Skip</a>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}">
    <style>
        header:not(.dashboard-header):not(.header-business-public) {
            background-color: #fdfcf8;
        }
    </style>
@endpush
