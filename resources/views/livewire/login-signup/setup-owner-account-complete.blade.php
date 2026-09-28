<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('FursGo - Setup Owner Account Complete Page')] class extends Component {
    //
};
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-lg-12 d-flex align-items-center justify-content-center">
            <div class="setup-page-wrapper d-flex flex-column align-items-center justify-content-center">
                <img src="{{ asset('icons/owner-setup-complete.svg') }}" alt="Setup Owner Account Icon" width="151"
                    height="100">
                <h1 class="heading">Your account is all set!</h1>
                <p class="heading-desc">We’ve saved your details and you’re ready to book. <br>
                    <span class="sub-desc">You can update your details at any time from your account.</span>
                </p>
                <a href="{{ route('home') }}" class="btn-custom btn-active-bg text-center">Back to Homepage</a>
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
