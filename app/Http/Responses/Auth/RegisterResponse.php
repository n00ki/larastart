<?php

declare(strict_types=1);

namespace App\Http\Responses\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;

final class RegisterResponse implements RegisterResponseContract
{
    public function toResponse(mixed $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 201);
        }

        Inertia::flash([
            'type' => 'success',
            'message' => __('auth.registered'),
        ]);

        return redirect()->intended(Fortify::redirects('register'));
    }
}
