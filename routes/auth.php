<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\GroomerSpacerPrivateFileController;
use App\Http\Controllers\LegalAgreementsPdfController;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('login-groomer-space', 'auth.login-groomer-space')
        ->name('login-groomer-space');

    Volt::route('signup', 'auth.signup')
        ->name('signup');

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
    Volt::route('verify-qualify', 'auth.verify-qualify')
        ->name('verify-qualify');

    Route::get('verify-qualify/legal-agreements.pdf', LegalAgreementsPdfController::class)
        ->name('verify-qualify.legal-agreements-pdf');

    Route::get('groomer-spacer/business-owner-id-file', [GroomerSpacerPrivateFileController::class, 'businessOwnerIdImage'])
        ->name('groomer-spacer.business-owner-id-file');

    Route::get('groomer-spacer/business-basics-file', [GroomerSpacerPrivateFileController::class, 'businessBasicsFile'])
        ->name('groomer-spacer.business-basics-file');

    Route::get('groomer-spacer/insurance-certificate-file', [GroomerSpacerPrivateFileController::class, 'insuranceCertificate'])
        ->name('groomer-spacer.insurance-certificate-file');
});

Route::post('logout', Logout::class)
    ->name('logout');
