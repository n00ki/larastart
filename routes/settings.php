<?php

declare(strict_types=1);

use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\PasswordController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function (): void {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/appearance', fn () => Inertia::render('settings/appearance'))->name('appearance');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('settings.password.update');

    Route::get('settings/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::delete('settings/account', [AccountController::class, 'destroy'])->name('account.destroy');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');
});
