<?php

declare(strict_types=1);

use App\Actions\Auth\SendPasswordResetLinkAction;
use App\Models\User;

beforeEach(function () {
    $this->action = new SendPasswordResetLinkAction;
});

test('it returns success message', function () {
    $user = User::factory()->create();

    $data = [
        'email' => $user->email,
    ];

    $message = $this->action->handle($data);

    expect($message)->toBe('A password reset link has been sent to your email if the account exists.');
});

test('it returns same message for non-existent email', function () {
    $data = [
        'email' => 'nonexistent@example.com',
    ];

    $message = $this->action->handle($data);

    expect($message)->toBe('A password reset link has been sent to your email if the account exists.');
});

test('it handles data array correctly', function () {
    $data = [
        'email' => 'test@example.com',
    ];

    $message = $this->action->handle($data);

    expect($message)->toBeString()
        ->and($message)->toBe('A password reset link has been sent to your email if the account exists.');
});
