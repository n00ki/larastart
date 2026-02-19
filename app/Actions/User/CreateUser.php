<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use SensitiveParameter;

final readonly class CreateUser
{
    /** @param array<string, mixed> $data */
    public function handle(array $data, #[SensitiveParameter] string $password): User
    {
        $user = DB::transaction(fn (): User => User::query()->create([
            ...$data,
            'password' => $password,
        ]), attempts: 3);

        event(new Registered($user));

        return $user;
    }
}
