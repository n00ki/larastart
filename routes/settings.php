<?php

declare(strict_types=1);

use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function (): void {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/appearance', fn () => Inertia::render('settings/appearance'))->name('settings.appearance.edit');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');

    Route::get('settings/security', [SecurityController::class, 'edit'])->name('settings.security.edit');
    Route::put('settings/password', [SecurityController::class, 'update'])->middleware('throttle:6,1')->name('settings.security.password.update');

    Route::get('settings/account', [AccountController::class, 'edit'])->name('settings.account.edit');
    Route::delete('settings/account', [AccountController::class, 'destroy'])->name('settings.account.destroy');
});
