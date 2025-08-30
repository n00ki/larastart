<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\DeleteProfileAction;
use App\Actions\Settings\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DeleteProfileRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile');
    }

    /**
     * Update the user's profile settings.
     */
    public function update(ProfileUpdateRequest $request, UpdateProfileAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return to_route('profile.edit')
            ->with('flash', ['type' => 'success', 'message' => __('settings.profile_updated')]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(DeleteProfileRequest $request, DeleteProfileAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request);

        return redirect('/')
            ->with('flash', ['type' => 'success', 'message' => __('settings.account_deleted')]);
    }
}
