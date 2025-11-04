<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendPasswordResetLink;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ForgotPasswordController
{
    /**
     * Show the password reset link request page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(SendPasswordResetLinkRequest $request, SendPasswordResetLink $action): RedirectResponse
    {
        $action->handle($request->validated());

        return back()
            ->with('flash', ['type' => 'success', 'message' => __('passwords.sent')]);
    }
}
