<?php

declare(strict_types=1);

namespace App\Http\Responses\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Fortify;

final class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    public function toResponse(mixed $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        Inertia::flash([
            'type' => 'success',
            'message' => __('auth.logged_in'),
        ]);

        return redirect()->intended(Fortify::redirects('login'));
    }
}
