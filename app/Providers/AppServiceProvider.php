<?php

namespace App\Providers;

use App\Auth\GroomerSpacerUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $root = config('app.url');
        if (is_string($root) && $root !== '') {
            URL::forceRootUrl(rtrim($root, '/'));
        }

        Auth::provider('groomer_spacer_eloquent', function ($app, array $config) {
            return new GroomerSpacerUserProvider($app['hash'], $config['model']);
        });
    }
}
