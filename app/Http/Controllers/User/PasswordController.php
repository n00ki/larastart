<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\User\UpdateUserPassword;
use App\Http\Requests\User\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class PasswordController
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/password');
    }

    /**
     * Update the user's password.
     */
    public function update(UpdatePasswordRequest $request, UpdateUserPassword $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return back()
            ->with('flash', ['type' => 'success', 'message' => __('settings.password_updated')]);
    }
}
