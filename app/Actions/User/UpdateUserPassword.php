<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUserPassword
{
    /** @param array<string, mixed> $data */
    public function handle(User $user, array $data): void
    {
        DB::transaction(function () use ($user, $data): void {
            $user->update([
                'password' => $data['password'],
            ]);
        }, attempts: 3);
    }
}
