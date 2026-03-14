<?php

declare(strict_types=1);

use App\Http\Controllers\Settings\SecurityController;
use Laravel\Fortify\Features;

test('middleware returns empty array when password confirmation is disabled', function () {
    // Temporarily disable confirmPassword option
    config()->set('fortify.features', [
        Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => false]),
    ]);

    $middleware = SecurityController::middleware();

    expect($middleware)->toBe([]);
});

test('middleware returns password.confirm middleware when enabled', function () {
    // Ensure confirmPassword is enabled
    config()->set('fortify.features', [
        Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
    ]);

    $middleware = SecurityController::middleware();

    expect($middleware)->toHaveCount(1);
    expect($middleware[0]->middleware)->toBe('password.confirm');
    expect($middleware[0]->only)->toBe(['edit']);
});
