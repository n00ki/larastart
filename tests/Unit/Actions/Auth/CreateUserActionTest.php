<?php

declare(strict_types=1);

use App\Actions\Auth\CreateUserAction;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->action = new CreateUserAction;
});

test('it creates a user with hashed password', function () {
    Event::fake();

    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ];

    $user = $this->action->handle($data);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com')
        ->and(Hash::check('password123', $user->password))->toBeTrue();

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

test('it fires registered event', function () {
    Event::fake();

    $data = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
    ];

    $user = $this->action->handle($data);

    Event::assertDispatched(Registered::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

test('it returns the created user', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $user = $this->action->handle($data);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->exists)->toBeTrue()
        ->and($user->wasRecentlyCreated)->toBeTrue();
});
