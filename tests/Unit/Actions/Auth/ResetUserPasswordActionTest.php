<?php

declare(strict_types=1);

use App\Actions\Auth\ResetUserPasswordAction;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->action = new ResetUserPasswordAction;
    $this->user = User::factory()->create([
        'remember_token' => Str::random(60),
    ]);
});

test('it resets password successfully', function () {
    Event::fake();

    $token = Password::createToken($this->user);
    $newPassword = 'new-password-123';

    $data = [
        'email' => $this->user->email,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
        'token' => $token,
    ];

    $this->action->handle($data);

    $this->user->refresh();

    expect(Hash::check($newPassword, $this->user->password))->toBeTrue()
        ->and($this->user->remember_token)->toBeNull();
});

test('it fires password reset event', function () {
    Event::fake();

    $token = Password::createToken($this->user);

    $data = [
        'email' => $this->user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
        'token' => $token,
    ];

    $this->action->handle($data);

    Event::assertDispatched(PasswordReset::class, function ($event) {
        return $event->user->id === $this->user->id;
    });
});

test('it throws exception for invalid token', function () {
    $data = [
        'email' => $this->user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
        'token' => 'invalid-token',
    ];

    expect(fn () => $this->action->handle($data))
        ->toThrow(Illuminate\Validation\ValidationException::class);
});

test('it throws exception for non-existent user', function () {
    $token = Str::random(60);

    $data = [
        'email' => 'nonexistent@example.com',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
        'token' => $token,
    ];

    expect(fn () => $this->action->handle($data))
        ->toThrow(Illuminate\Validation\ValidationException::class);
});
