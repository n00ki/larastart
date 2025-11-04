<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use SensitiveParameter;

final readonly class CreateUser
{
    /**
     * Create a new user with the provided data.
     *
     * @param array<string, mixed> $data
     */
    public function handle(array $data, #[SensitiveParameter] string $password): User
    {
        $user = User::query()->create([
            ...$data,
            'password' => Hash::make($password),
        ]);

        event(new Registered($user));

        return $user;
    }
}
