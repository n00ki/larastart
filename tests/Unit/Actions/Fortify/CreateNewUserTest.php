<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->action = new CreateNewUser;
});

test('creates a normalized user account', function () {
    $user = $this->action->create([
        'name' => "  ada\tBYRON   lovelace  ",
        'email' => 'ada@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Ada Byron Lovelace')
        ->and($user->email)->toBe('ada@example.com')
        ->and(Hash::check('password123', $user->password))->toBeTrue();

    $this->assertModelExists($user);
});

test('only persists supported registration data', function () {
    $user = $this->action->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)->toBeNull();
});

test('rejects invalid registration data', function (array $overrides) {
    $input = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        ...$overrides,
    ];

    expect(fn () => $this->action->create($input))
        ->toThrow(ValidationException::class);
})->with([
    'missing name' => [['name' => '']],
    'unsupported name punctuation' => [['name' => 'Test. User']],
    'invalid email' => [['email' => 'invalid-email']],
    'unconfirmed password' => [['password_confirmation' => 'different']],
]);

test('rejects an email address that is already registered', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    expect(fn () => $this->action->create([
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]))->toThrow(ValidationException::class);
});
