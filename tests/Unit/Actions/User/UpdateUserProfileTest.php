<?php

declare(strict_types=1);

use App\Actions\User\UpdateUserProfile;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->action = new UpdateUserProfile;
    $this->user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
});

test('updates profile name and email', function () {
    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect($this->user->name)->toBe('Updated Name')
        ->and($this->user->email)->toBe('updated@example.com');
});

test('resets email verification and sends notification when email changes', function () {
    Notification::fake();

    $this->action->handle($this->user, [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $this->user->refresh();

    expect($this->user->email_verified_at)->toBeNull();

    Notification::assertSentTo($this->user, VerifyEmail::class);
});

test('keeps email verification and does not send notification when email is unchanged', function () {
    Notification::fake();

    $verifiedAt = now();
    $this->user->forceFill(['email_verified_at' => $verifiedAt])->save();

    $this->action->handle($this->user, [
        'name' => 'Updated Name',
        'email' => $this->user->email,
    ]);

    $this->user->refresh();

    expect($this->user->email_verified_at)->not->toBeNull();

    Notification::assertNotSentTo($this->user, VerifyEmail::class);
});

test('persists updated profile data', function () {
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

test('keeps unchanged fields intact', function () {
    $data = [
        'name' => 'Only Name Changed',
    ];

    $originalEmail = $this->user->email;

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect($this->user->name)->toBe('Only Name Changed')
        ->and($this->user->email)->toBe($originalEmail);
});

test('keeps profile unchanged when no data is provided', function () {
    $originalName = $this->user->name;
    $originalEmail = $this->user->email;

    $data = [];

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect($this->user->name)->toBe($originalName)
        ->and($this->user->email)->toBe($originalEmail);
});
