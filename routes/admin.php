<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
| Admin routes — separate from pet-owner / groomer auth.
| One main page: tabs switch with Alpine (no reload / no extra routes).
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Volt::route('login', 'admin.auth.login')->name('login');

    Route::middleware('auth.admin')->group(function () {
        Volt::route('/', 'admin.overview')->name('overview');
    });
});
