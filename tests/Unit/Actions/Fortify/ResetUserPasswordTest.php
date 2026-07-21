<?php

declare(strict_types=1);

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->action = new ResetUserPassword;
    $this->user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'old-password',
    ]);
});

test('resets a user password', function () {
    $this->action->reset($this->user, [
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'email' => 'different@example.com',
    ]);

    $this->user->refresh();

    expect(Hash::check('new-password', $this->user->password))->toBeTrue()
        ->and($this->user->email)->toBe('user@example.com');
});

test('requires a confirmed password', function () {
    expect(fn () => $this->action->reset($this->user, [
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
    ]))->toThrow(ValidationException::class);
});
