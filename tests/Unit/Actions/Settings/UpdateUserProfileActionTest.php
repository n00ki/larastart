<?php

declare(strict_types=1);

use App\Actions\Settings\UpdateUserProfileAction;
use App\Models\User;

beforeEach(function () {
    $this->action = new UpdateUserProfileAction;
    $this->user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
});

test('it updates user profile information', function () {
    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect($this->user->name)->toBe('Updated Name')
        ->and($this->user->email)->toBe('updated@example.com');
});

test('it persists changes to database', function () {
    $data = [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ];

    $this->action->handle($this->user, $data);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    $this->assertDatabaseMissing('users', [
        'id' => $this->user->id,
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
});

test('it updates only provided fields', function () {
    $data = [
        'name' => 'Only Name Changed',
    ];

    $originalEmail = $this->user->email;

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect($this->user->name)->toBe('Only Name Changed')
        ->and($this->user->email)->toBe($originalEmail);
});

test('it handles empty data gracefully', function () {
    $originalName = $this->user->name;
    $originalEmail = $this->user->email;

    $data = [];

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect($this->user->name)->toBe($originalName)
        ->and($this->user->email)->toBe($originalEmail);
});
