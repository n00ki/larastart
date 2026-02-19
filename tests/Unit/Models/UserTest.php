<?php

declare(strict_types=1);

use App\Models\User;

test('user array includes expected visible attributes', function () {
    $user = User::factory()->create()->refresh();

    $attributes = array_keys($user->toArray());

    expect($attributes)
        ->toContain(
            'id',
            'name',
            'email',
            'email_verified_at',
            'two_factor_confirmed_at',
            'created_at',
            'updated_at',
        )
        ->not->toContain('password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes');
});
