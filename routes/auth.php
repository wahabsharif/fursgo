<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\GroomerSpacerPrivateFileController;
use App\Http\Controllers\LegalAgreementsPdfController;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Always reachable — guest middleware was sending logged-in users back to home,
// so the header Log in link looked like it did nothing.
Volt::route('login', 'auth.login')
    ->name('login');

Volt::route('login-signup/login', 'auth.login')
    ->name('login-signup.login');

Volt::route('signup', 'auth.signup')
    ->name('signup');

Volt::route('login-signup/signup', 'auth.signup')
    ->name('login-signup.signup');

Route::middleware('guest')->group(function () {
    Volt::route('login-groomer-space', 'auth.login-groomer-space')
        ->name('login-groomer-space');

    Volt::route('signup-groomer-space', 'auth.signup-groomer-space')
        ->name('signup-groomer-space');

    Route::redirect('forgot-password', 'login')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');
});

Route::middleware('auth.groomer_spacer')->group(function () {
    Volt::route('business-verification', 'auth.business-verification')
        ->name('business-verification');

    Route::get('business-verification/legal-agreements.pdf', LegalAgreementsPdfController::class)
        ->name('business-verification.legal-agreements-pdf');

    Route::get('groomer-spacer/business-owner-id-file', [GroomerSpacerPrivateFileController::class, 'businessOwnerIdImage'])
        ->name('groomer-spacer.business-owner-id-file');

    Route::get('groomer-spacer/business-basics-file', [GroomerSpacerPrivateFileController::class, 'businessBasicsFile'])
        ->name('groomer-spacer.business-basics-file');

    Route::get('groomer-spacer/insurance-certificate-file', [GroomerSpacerPrivateFileController::class, 'insuranceCertificate'])
        ->name('groomer-spacer.insurance-certificate-file');
});

Route::post('logout', Logout::class)
    ->name('logout');

// Pet-owner login-signup setup flow (imported from legacy login-signup/)
Volt::route('login-signup/setup_owner_account', 'login-signup.setup-owner-account')
    ->name('login-signup.setup-owner-account');

Volt::route('login-signup/owner_account_setup_form', 'login-signup.owner-account-setup-form')
    ->name('login-signup.owner-account-setup-form');

Volt::route('login-signup/setup_owner_account_complete', 'login-signup.setup-owner-account-complete')
    ->name('login-signup.setup-owner-account-complete');
