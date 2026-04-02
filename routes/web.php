<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PetDetailController;




Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    return "All caches cleared!";
});


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/booking-groomer', function () {
    $petDetails = PetDetailController::getPetDetailsForUser();
    return view('booking-groomer', compact('petDetails'));
})->middleware(['auth'])->name('booking-groomer');

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

Route::get('/support-and-assistance/help-and-support', function () {
    return view('help-and-support');
})->name('help-and-support');

Route::get('/groomer-unavailability/location-unavailability', function () {
    return view('groomer-unavailability.location-unavailability');
})->name('groomer-unavailability.location-unavailability');

Route::get('/groomer-unavailability/location-unavailability', function () {

    $groomers = collect([
        [
            'name' => 'Sarah W.',
            'studio_name' => 'Sarah’s Grooming Studio',
            'distance' => '2.5',
            'rating' => '4.3',
            'reviews_count' => 20,
            'experience_text' => '6+ years experience',
            'price' => 38,
            'image_url' => 'assets/images/card1.png',
            'tags' => ['Home Visit'],
            'slots' => ['Mon 1, 08:30 AM'],
            'is_top_rated' => true,
        ]
    ]);

    $spaces = collect([
        [
            'name' => 'Pet Care Space',
            'location' => 'London',
            'price' => 25,
            'image_url' => 'assets/images/space1.png',
        ]
    ]);

    return view('groomer-unavailability.location-unavailability', compact('groomers', 'spaces'));
});








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

    // Pet Details Routes
    Route::get('/pet-details', [PetDetailController::class, 'show'])->name('pet-details.show');
    Route::post('/pet-details', [PetDetailController::class, 'store'])->name('pet-details.store');
    Route::delete('/pet-details', [PetDetailController::class, 'destroy'])->name('pet-details.destroy');
});

require __DIR__ . '/auth.php';
