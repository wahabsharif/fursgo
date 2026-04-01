<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SearchController;




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

Route::get('/business-homepage-groomer-space-Owner', function () {
    return view('business-homepage-groomer-space-Owner');
})->name('business-homepage-groomer-space-Owner');


Route::get('/cookies', function () {
    return view('components.ui.cookies');
})->name('cookies');

Route::get('/rating-overlay-card', function () {
    return view('components.ui.rating-overlay-card');
})->name('rating-overlay-card');

Route::get('/search-results', function () {
    return view('search-results');
})->name('search-results');

Route::get('/search-results', [SearchController::class, 'index'])->name('search-results');


Route::get('/account-and-setting/settings', function () {
    return view('account-and-setting');
})->name('account-and-setting');

Route::get('/my-account/my-profile', function () {
    return view('my-profile');
})->name('my-profile');

Route::get('/support-and-assistance/search', function () {
    return view('search');
})->name('search');










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
