<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendUserPasswordResetLinkAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ForgotPasswordController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(SendPasswordResetLinkRequest $request, SendUserPasswordResetLinkAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        return back()
            ->with('flash', ['type' => 'success', 'message' => __('passwords.reset_link_sent')]);
    }
}
