<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Override;

#[\Illuminate\Database\Eloquent\Attributes\Hidden([
    'password',
    'remember_token',
    'two_factor_secret',
    'two_factor_recovery_codes',
])]
#[\Illuminate\Database\Eloquent\Attributes\Unguarded]
final class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    #[Override]
    protected $attributes = [
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
