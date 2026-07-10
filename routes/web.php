<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SedeConfigController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication (guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    // Settings
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('settings.profile');
    Route::put('settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');
    Route::put('settings/profile/password', [ProfileController::class, 'updatePassword'])->name('settings.profile.password');

    // Sede Config — solo admin
    Route::middleware('role:admin')->group(function () {
        Route::get('settings/sede', [SedeConfigController::class, 'edit'])->name('settings.sede.edit');
        Route::post('settings/sede', [SedeConfigController::class, 'update'])->name('settings.sede.update');
    });
});

/*
|--------------------------------------------------------------------------
| Redirect /
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('pos.dashboard');
    }

    return redirect()->route('login');
});

require __DIR__.'/pos.php';
