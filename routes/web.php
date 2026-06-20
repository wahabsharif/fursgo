<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingInvoicePdfController;
use App\Http\Controllers\PetDetailController;
use App\Models\GroomerSpacerProfile;
use App\Support\BusinessPageShell;
use App\Support\DashboardNav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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
    Volt::route('/support-and-assistance/search', 'help.search')->name('search');
    Volt::route('/search-results', 'search.results')->name('search-results');
});
Route::redirect('/business/support-and-assistance/help-and-support', '/support-and-assistance/help-and-support');
Route::redirect('/business/support-and-assistance/search', '/support-and-assistance/search');
Route::redirect('/business/business-homepage-groomer-space-owner', '/business-homepage-groomer-space-owner');

Route::get('/support-and-assistance/help-and-support', function () {
    BusinessPageShell::applyFromRequest();

    $component = BusinessPageShell::resolveComponent('help.support-dashboard', 'help.support');

    return app(LivewireManager::class)->new($component)->__invoke();
})->name('help-and-support');

// Business pages
Route::get('/business-homepage-groomer-space-owner', function () {
    BusinessPageShell::applyFromRequest();

    $component = BusinessPageShell::resolveComponent('business.homepage-dashboard', 'business.homepage');

    return app(LivewireManager::class)->new($component)->__invoke();
})->name('business-homepage-groomer-space-owner');

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
    ->middleware(['auth:groomer_spacer', 'verified', 'business.shell.dashboard'])
    ->name('dashboard');

Route::post('dashboard/nav', function (Request $request) {
    DashboardNav::persist(DashboardNav::mergeFromRequest($request));

    return response()->noContent();
})->middleware(['auth:groomer_spacer'])->name('dashboard.nav');

Route::get('dashboard/bookings/{booking}/invoice.pdf', BookingInvoicePdfController::class)
    ->middleware(['auth:groomer_spacer', 'verified'])
    ->name('dashboard.bookings.invoice-pdf');

/** Same invoice as HTML for DevTools (local env only). */
Route::get('dashboard/bookings/{booking}/invoice.html', [BookingInvoicePdfController::class, 'previewHtml'])
    ->middleware(['auth:groomer_spacer', 'verified'])
    ->name('dashboard.bookings.invoice-html');

Route::middleware(['auth:web,groomer_spacer'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'account.profile')->name('settings.profile');
    Volt::route('settings/password', 'account.settings')->name('settings.password');
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

    Route::post('/dev-mode/update-meta', function (Request $request) {
        $request->validate([
            'user_type' => 'nullable|in:groomer,space',
            'account_type' => 'nullable|in:freelance,registered_business',
        ]);

        $guard = Auth::guard('groomer_spacer')->check()
            ? 'groomer_spacer'
            : (Auth::guard('web')->check() ? 'web' : null);

        abort_unless((bool) $guard, 403);

        $user = Auth::guard($guard)->user();
        abort_unless((bool) $user, 403);

        $requestedUserType = $request->input('user_type');
        $requestedAccountType = $request->input('account_type');

        if ($requestedUserType !== null && $requestedUserType !== '') {
            $user->user_type = $requestedUserType;
            $user->save();
        }

        // If the authenticated model is a groomer/spacer profile, persist account_type directly there.
        if ($user instanceof GroomerSpacerProfile) {
            if ($requestedAccountType !== null && $requestedAccountType !== '') {
                $user->account_type = $requestedAccountType;
                $user->save();
            }
        }

        // When logged in via web guard, keep the linked groomer/spacer profile in sync too.
        if ($guard === 'web' && method_exists($user, 'groomerSpacerProfile')) {
            $profile = $user->groomerSpacerProfile;
            if ($profile instanceof GroomerSpacerProfile) {
                if ($requestedUserType !== null && $requestedUserType !== '') {
                    $profile->user_type = $requestedUserType;
                }
                if ($requestedAccountType !== null && $requestedAccountType !== '') {
                    $profile->account_type = $requestedAccountType;
                }
                $profile->save();
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'guard' => $guard,
                'user_id' => $user->id ?? null,
            ]);
        }

        return redirect()->back();
    })->name('dev-mode.update-meta');
});

require __DIR__ . '/auth.php';
