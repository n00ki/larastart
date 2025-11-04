<?php

declare(strict_types=1);

use App\Actions\Auth\SendPasswordResetLink;
use App\Models\User;

beforeEach(function () {
    $this->action = new SendPasswordResetLink;
});

test('it returns success message', function () {
    $user = User::factory()->create();

    $data = [
        'email' => $user->email,
    ];

    $message = $this->action->handle($data);

    expect($message)->toBe(__('passwords.sent'));
});

test('it returns same message for non-existent email', function () {
    $data = [
        'email' => 'nonexistent@example.com',
    ];

    $message = $this->action->handle($data);

    expect($message)->toBe(__('passwords.sent'));
});

test('it handles data array correctly', function () {
    $data = [
        'email' => 'test@example.com',
    ];

    $message = $this->action->handle($data);

    expect($message)->toBeString()
        ->and($message)->toBe(__('passwords.sent'));
});
