<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdatePasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class PasswordController extends Controller
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
    public function update(UpdatePasswordRequest $request, UpdatePasswordAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return back()
            ->with('flash', ['type' => 'success', 'message' => __('settings.password_updated')]);
    }
}
