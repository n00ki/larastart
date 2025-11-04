<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;

final readonly class UpdateUserProfile
{
    /**
     * Update the user's profile information.
     *
     * @param array<string, mixed> $data
     */
    public function handle(User $user, array $data): void
    {
        $user->update($data);
    }
}
