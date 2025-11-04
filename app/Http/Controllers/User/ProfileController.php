<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\User\UpdateUserProfile;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ProfileController
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/profile');
    }

    /**
     * Update the user's profile settings.
     */
    public function update(UpdateProfileRequest $request, UpdateUserProfile $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return to_route('profile.edit')
            ->with('flash', ['type' => 'success', 'message' => __('settings.profile_updated')]);
    }
}
