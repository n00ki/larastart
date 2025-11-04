<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUser;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final readonly class RegisterController
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
    public function store(RegisterRequest $request, CreateUser $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->safe()->except('password');

        $user = $action->handle(
            $data,
            $request->string('password')->value(),
        );

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('flash', ['type' => 'success', 'message' => __('auth.registered')]);
    }
}
