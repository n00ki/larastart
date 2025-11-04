<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\User\DeleteUser;
use App\Http\Requests\User\DeleteUserRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class AccountController
{
    /**
     * Show the user's account settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/account');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(DeleteUserRequest $request, #[CurrentUser] User $user, DeleteUser $action): RedirectResponse
    {
        $action->handle($user, $request);

        return redirect('/')
            ->with('flash', ['type' => 'success', 'message' => __('settings.account_deleted')]);
    }
}
