<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
| Admin routes — separate from pet-owner / groomer auth.
| Phase 1 UI: login page only. Auth wiring comes in later steps.
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Volt::route('login', 'admin.auth.login')->name('login');
});
