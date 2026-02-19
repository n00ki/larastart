<?php

declare(strict_types=1);

namespace App\Http\Responses\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse(mixed $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        Inertia::flash([
            'type' => 'success',
            'message' => __('auth.logged_in'),
        ]);

        return redirect()->intended(Fortify::redirects('login'));
    }
}
