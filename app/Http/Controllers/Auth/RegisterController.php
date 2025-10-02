<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class RegisterController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request, CreateUserAction $action): RedirectResponse
    {
        $user = $action->handle($request->validated());

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('flash', ['type' => 'success', 'message' => __('auth.registered')]);
    }
}
