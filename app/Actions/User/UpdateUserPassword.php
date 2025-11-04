<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class UpdateUserPassword
{
    /**
     * Update the user's password.
     *
     * @param array<string, mixed> $data
     */
    public function handle(User $user, array $data): void
    {
        $user->update([
            'password' => Hash::make($data['password']),
        ]);
    }
}
