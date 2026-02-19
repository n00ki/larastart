<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\User\UpdateUserProfile;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ProfileController
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    public function update(UpdateProfileRequest $request, UpdateUserProfile $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        Inertia::flash([
            'type' => 'success',
            'message' => __('settings.profile_updated'),
        ]);

        return to_route('profile.edit');
    }
}
