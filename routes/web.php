<?php

use App\Http\Controllers\AccountDataExportController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingInvoicePdfController;
use App\Http\Controllers\PetDetailController;
use App\Support\BusinessHubNav;
use App\Support\BusinessPageShell;
use App\Support\MarketingHubNav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\LivewireManager;
use Livewire\Volt\Volt;

Route::get('/clear', function () {
    Artisan::call('optimize:clear');

    return 'All caches cleared!';
});

Route::get('/seed', function () {
    if (app()->isProduction()) {
        abort(403, 'The seed route is disabled in production.');
    }

    // Allow long-running migrations + seeds via the web request.
    @set_time_limit(0);
    @ini_set('memory_limit', '512M');

    try {
        $commands = [
            ['optimize:clear'],
            ['storage:link', [
                '--force' => true,
            ]],
            ['migrate:fresh', [
                '--seed' => true,
                '--force' => true,
            ]],
        ];

        $output = collect($commands)->map(function (array $command) {
            [$name, $parameters] = [$command[0], $command[1] ?? []];

            Artisan::call($name, $parameters);

            return ">>> php artisan {$name}\n" . trim(Artisan::output());
        })->implode("\n\n");

        return response(
            "<pre>Setup commands completed successfully.\n\n"
                . e($output) . '</pre>',
            200
        )->header('Content-Type', 'text/html');
    } catch (\Throwable $e) {
        return response(
            "<pre>Seeding failed:\n\n" . e($e->getMessage()) . '</pre>',
            500
        )->header('Content-Type', 'text/html');
    }
})->name('seed');

// Public pages - converted to Volt (web header shell for shared business/help pages)
Route::middleware('business.shell.web')->group(function () {
    Volt::route('/', 'home')->name('home');
    Route::view('/business-landing-page', 'business-landing-page')->name('business-landing-page');
    Route::livewire('/support-and-assistance/search', 'help.search')->name('search');
    Volt::route('/search-results', 'search/results')->name('search-results');
});
Route::redirect('/business/support-and-assistance/help-and-support', '/support-and-assistance/help-and-support');
Route::redirect('/business/support-and-assistance/search', '/support-and-assistance/search');
Route::redirect('/business/business-homepage-groomer-space-owner', '/business-homepage-groomer-space-owner');

Route::get('/support-and-assistance/help-and-support', function () {
    BusinessPageShell::applyFromRequest();

    $component = BusinessPageShell::resolveComponent('help.support-business-hub', 'help.support');

    return app(LivewireManager::class)->new($component)->__invoke();
})->name('help-and-support');

// Business pages
Route::get('/business-homepage-groomer-space-owner', function () {
    BusinessPageShell::applyFromRequest();

    $component = BusinessPageShell::resolveComponent('business.homepage-business-hub', 'business.homepage');

    return app(LivewireManager::class)->new($component)->__invoke();
})->name('business-homepage-groomer-space-owner');

// Authenticated pages (web + groomer/spacer guard)
Volt::route('/booking-groomer', 'booking/groomer')
    ->middleware(['auth:web,groomer_spacer'])
    ->name('booking-groomer');

Volt::route('/my-account/pet-owner-profile', 'account/profile')
    ->middleware(['auth:web,groomer_spacer'])
    ->name('pet-owner-profile');

Volt::route('/account-settings', 'account/settings')
    ->middleware(['auth:web,groomer_spacer', 'business.shell.business-hub'])
    ->name('account-settings');

Route::get('/account-settings/download-data', AccountDataExportController::class)
    ->middleware(['auth:web,groomer_spacer', 'business.shell.business-hub'])
    ->name('account-settings.download-data');

Route::redirect('/account-and-setting/settings', '/account-settings')
    ->middleware(['auth:web,groomer_spacer']);

Volt::route('/account_and_setting/settings', 'account_and_setting/settings')
    ->name('account_and_setting.settings');

Volt::route('/profile_pets_preferences/about_us', 'profile_pets_preferences/about_us')
    ->name('profile_pets_preferences.about_us');

Volt::route('/profile_pets_preferences/contact_us', 'profile_pets_preferences/contact_us')
    ->name('profile_pets_preferences.contact_us');

Volt::route('/profile_pets_preferences/company_information', 'profile_pets_preferences/company_information')
    ->name('profile_pets_preferences.company_information');

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
Volt::route('/groomer-unavailability/location-unavailability', 'groomer/unavailability')
    ->name('groomer-unavailability.location-unavailability');

// ===============================================================
// Authenticated Routes
// ===============================================================
Volt::route('business-hub', 'business-hub')
    ->middleware(['auth:groomer_spacer', 'verified', 'business.shell.business-hub'])
    ->name('business-hub');

Route::post('business-hub/nav', function (Request $request) {
    BusinessHubNav::persist(BusinessHubNav::mergeFromRequest($request));

    return response()->noContent();
})->middleware(['auth:groomer_spacer'])->name('business-hub.nav');

Volt::route('marketing-hub', 'marketing-hub')
    ->middleware(['auth:groomer_spacer', 'verified', 'business.shell.business-hub'])
    ->name('marketing-hub');

Route::post('marketing-hub/nav', function (Request $request) {
    MarketingHubNav::persist(MarketingHubNav::mergeFromRequest($request));

    return response()->noContent();
})->middleware(['auth:groomer_spacer'])->name('marketing-hub.nav');

Route::get('business-hub/bookings/{booking}/invoice.pdf', BookingInvoicePdfController::class)
    ->middleware(['auth:groomer_spacer', 'verified'])
    ->name('business-hub.bookings.invoice-pdf');

/** Same invoice as HTML for DevTools (local env only). */
Route::get('business-hub/bookings/{booking}/invoice.html', [BookingInvoicePdfController::class, 'previewHtml'])
    ->middleware(['auth:groomer_spacer', 'verified'])
    ->name('business-hub.bookings.invoice-html');

Route::middleware(['auth:web,groomer_spacer'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'account/profile')->name('settings.profile');
    Route::redirect('settings/password', '/account-settings?tab=login_and_security');
    Volt::route('settings/appearance', 'settings/appearance')->name('settings.appearance');

    // Pet Details - handled by LiveWire Volt component
    Volt::route('/pet-details', 'pet-details/manager')->name('pet-details.show');

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
