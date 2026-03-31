<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Artisan;


Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    return "All caches cleared!";
});


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/booking-groomer', function () {
    return view('booking-groomer');
})->name('booking-groomer');

Route::get('/cookies-overlay-card', function () {
    return view('components.ui.cookies-overlay-card');
})->name('cookies-overlay-card');

Route::get('/cookies', function () {
    return view('components.ui.cookies');
})->name('cookies');

Route::get('/rating-overlay-card', function () {
    return view('components.ui.rating-overlay-card');
})->name('rating-overlay-card');










// ===============================================================
// Authenticated Routes
// ===============================================================
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
