<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Password;

final class SendUserPasswordResetLinkAction
{
    /**
     * Send a password reset link to the provided email.
     *
     * @param array<string, mixed> $data
     */
    public function handle(array $data): string
    {
        Password::sendResetLink($data);

        return __('passwords.sent');
    }
}
