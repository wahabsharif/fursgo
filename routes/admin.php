<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
| Admin routes — separate from pet-owner / groomer auth.
| One main page: tabs switch with Alpine (no reload / no extra routes).
| Auth middleware will be added in a later step.
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Volt::route('login', 'admin.auth.login')->name('login');
    Volt::route('/', 'admin.overview')->name('overview');
});
