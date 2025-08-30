<?php

declare(strict_types=1);

namespace App\Actions\Settings;

use App\Models\User;

final class UpdateProfileAction
{
    /**
     * Update the user's profile information.
     *
     * @param array<string, mixed> $data
     */
    public function handle(User $user, array $data): void
    {
        $user->fill($data);
        $user->save();
    }
}
