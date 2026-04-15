<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PetDetailController;


Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    return "All caches cleared!";
});

// Public pages - converted to Volt
Volt::route('/', 'home')->name('home');
Route::view('/business-landing-page', 'business-landing-page')->name('business-landing-page');
Volt::route('/support-and-assistance/help-and-support', 'help.support')->name('help-and-support');
Volt::route('/support-and-assistance/search', 'help.search')->name('search');

// Search results - LiveWire component
Volt::route('/search-results', 'search.results')->name('search-results');

// Business pages
Volt::route('/business-homepage-groomer-space-owner', 'business.homepage')->name('business-homepage-groomer-space-owner');

// Authenticated pages (web + groomer/spacer guard)
Volt::route('/booking-groomer', 'booking.groomer')
    ->middleware(['auth:web,groomer_spacer'])
    ->name('booking-groomer');

Volt::route('/my-account/pet-owner-profile', 'account.profile')
    ->middleware(['auth:web,groomer_spacer'])
    ->name('pet-owner-profile');

Volt::route('/account-and-setting/settings', 'account.settings')
    ->middleware(['auth:web,groomer_spacer'])
    ->name('account-and-setting');

// Cookie and overlay components
Route::get('/cookies-overlay-card', function () {
    return view('components.ui.cookies-overlay-card');
})->name('cookies-overlay-card');

Route::get('/cookies', function () {
    return view('components.ui.cookies');
})->name('cookies');

Route::get('/rating-overlay-card', function () {
    return view('components.ui.rating-overlay-card');
})->name('rating-overlay-card');

// Groomer unavailability
Volt::route('/groomer-unavailability/location-unavailability', 'groomer.unavailability')
    ->name('groomer-unavailability.location-unavailability');

// ===============================================================
// Authenticated Routes
// ===============================================================
Volt::route('dashboard', 'dashboard')
    ->middleware(['auth:groomer_spacer', 'verified'])
    ->name('dashboard');

Route::middleware(['auth:web,groomer_spacer'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Pet Details - handled by LiveWire Volt component
    Volt::route('/pet-details', 'pet-details.manager')->name('pet-details.show');

    // Pet Details form submission (traditional POST for booking-groomer page)
    Route::post('/pet-details', [PetDetailController::class, 'store'])->name('pet-details.store');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::patch('/bookings/{booking}/accept', [BookingController::class, 'accept'])->name('bookings.accept');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

require __DIR__ . '/auth.php';
