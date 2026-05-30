<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use App\Support\UserName;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUserProfile
{
    /** @param array<string, mixed> $data */
    public function handle(User $user, array $data): void
    {
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = UserName::normalize($data['name']);
        }

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
