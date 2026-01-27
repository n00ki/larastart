<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResetPassword;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ResetPasswordController
{
    /**
     * Show the password reset page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/reset-password', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(ResetPasswordRequest $request, ResetPassword $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->only('email', 'password', 'password_confirmation', 'token');

        $action->handle(
            $data,
            $request->string('password')->value(),
        );

        Inertia::flash([
            'type' => 'success',
            'message' => __('passwords.reset'),
        ]);

        return to_route('login');
    }
}
