<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\User\UpdateUserPassword;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use Laravel\Passkeys\Passkey;

final readonly class SecurityController implements HasMiddleware
{
    /**
     * @return array<int, Middleware>
     */
    public static function middleware(): array
    {
        if (self::requiresPasswordConfirmation()) {
            return [new Middleware('password.confirm', only: ['edit'])];
        }

        return [];
    }

    private static function requiresPasswordConfirmation(): bool
    {
        if (Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            return true;
        }

        return Features::canManagePasskeys()
            && Features::optionEnabled(Features::passkeys(), 'confirmPassword');
    }

    public function edit(TwoFactorAuthenticationRequest $request): Response
    {
        $props = [
            'canManageTwoFactor' => Features::canManageTwoFactorAuthentication(),
            'canManagePasskeys' => Features::canManagePasskeys(),
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ];

        if (Features::canManagePasskeys()) {
            $props['passkeys'] = $request->user()->passkeys()
                ->select(['id', 'name', 'credential', 'created_at', 'last_used_at'])
                ->latest('id')
                ->get()
                ->map(fn (Passkey $passkey): array => [
                    'id' => $passkey->id,
                    'name' => $passkey->name,
                    'authenticator' => $passkey->authenticator,
                    'created_at_diff' => $passkey->created_at?->diffForHumans(),
                    'last_used_at_diff' => $passkey->last_used_at?->diffForHumans(),
                ])->all();
        }

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
