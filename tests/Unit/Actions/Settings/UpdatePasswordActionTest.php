<?php

declare(strict_types=1);

use App\Actions\Settings\UpdatePasswordAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->action = new UpdatePasswordAction;
    $this->user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);
});

test('it updates user password', function () {
    $data = [
        'current_password' => 'old-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ];

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect(Hash::check('new-password-123', $this->user->password))->toBeTrue()
        ->and(Hash::check('old-password', $this->user->password))->toBeFalse();
});

test('it hashes the new password', function () {
    $plainPassword = 'new-secure-password';

    $data = [
        'current_password' => 'old-password',
        'password' => $plainPassword,
        'password_confirmation' => $plainPassword,
    ];

    $this->action->handle($this->user, $data);

    $this->user->refresh();

    expect($this->user->password)->not->toBe($plainPassword)
        ->and(Hash::check($plainPassword, $this->user->password))->toBeTrue();
});

test('it persists password change to database', function () {
    $originalPasswordHash = $this->user->password;

    $data = [
        'current_password' => 'old-password',
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ];

    $this->action->handle($this->user, $data);

    $this->assertDatabaseMissing('users', [
        'id' => $this->user->id,
        'password' => $originalPasswordHash,
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
    ]);
});
