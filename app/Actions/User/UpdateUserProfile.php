<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUserProfile
{
    /** @param array<string, mixed> $data */
    public function handle(User $user, array $data): void
    {
        $emailChanged = isset($data['email']) && $user->email !== $data['email'];

        DB::transaction(function () use ($user, $data): void {
            $user->fill($data);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
        }, attempts: 3);

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }
    }
}
