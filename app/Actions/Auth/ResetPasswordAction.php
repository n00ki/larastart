<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

final class ResetPasswordAction
{
    /**
     * Reset the user's password using the provided data.
     *
     * @param array<string, mixed> $data
     *
     * @throws ValidationException
     */
    public function handle(array $data): string
    {
        // Attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $data,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => null,
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
