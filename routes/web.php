<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', fn () => Inertia::render('welcome', [
    'canRegister' => Features::enabled(Features::registration()),
]))->name('home');

if (Features::enabled(Features::passkeys())) {
    Route::get('.well-known/passkey-endpoints', fn () => response()->json([
        'enroll' => route('settings.security.edit'),
        'manage' => route('settings.security.edit'),
    ]))->name('well-known.passkeys');
}

Route::middleware(['auth'])->group(function (): void {
    Route::get('dashboard', fn () => Inertia::render('dashboard'))->name('dashboard');
});

require __DIR__ . '/settings.php';
