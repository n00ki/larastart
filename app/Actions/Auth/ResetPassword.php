<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

final readonly class ResetPassword
{
    /**
     * Reset the user's password using the provided data.
     *
     * @param array<string, mixed> $data
     *
     * @throws ValidationException
     */
    public function handle(array $data, #[SensitiveParameter] string $password): string
    {
        // Attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $data,
            function (User $user) use ($password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ]);

                $user->save();

                event(new PasswordReset($user));
            },
        );

        // If the password was successfully reset, we will return the status.
        // Otherwise, we will throw a validation exception with the error.
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }
}
