<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Settings\ProfileController;
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

require __DIR__ . '/pos.php';
