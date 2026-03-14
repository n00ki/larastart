<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\User\UpdateUserPassword;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

final readonly class SecurityController implements HasMiddleware
{
    /**
     * @return array<int, Middleware>
     */
    public static function middleware(): array
    {
        if (
            Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
        ) {
            return [new Middleware('password.confirm', only: ['edit'])];
        }

        return [];
    }

    public function edit(TwoFactorAuthenticationRequest $request): Response
    {
        $props = [
            'canManageTwoFactor' => Features::canManageTwoFactorAuthentication(),
        ];

        if (Features::canManageTwoFactorAuthentication()) {
            $request->ensureStateIsValid();

            $props['twoFactorEnabled'] = $request->user()->hasEnabledTwoFactorAuthentication();
            $props['requiresConfirmation'] = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }

        return Inertia::render('settings/security', $props);
    }

    public function update(UpdatePasswordRequest $request, UpdateUserPassword $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        Inertia::flash([
            'type' => 'success',
            'message' => __('settings.password_updated'),
        ]);

        return back();
    }
}
