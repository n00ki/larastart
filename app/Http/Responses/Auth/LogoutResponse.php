<?php

declare(strict_types=1);

namespace App\Http\Responses\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;

final class LogoutResponse implements LogoutResponseContract
{
    public function toResponse(mixed $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        Inertia::flash([
            'type' => 'success',
            'message' => __('auth.logged_out'),
        ]);

        return redirect(Fortify::redirects('logout', '/'));
    }
}
