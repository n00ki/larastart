<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\DeleteUserAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DeleteAccountRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class AccountController extends Controller
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
    public function destroy(DeleteAccountRequest $request, DeleteUserAccountAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request);

        return redirect('/')
            ->with('flash', ['type' => 'success', 'message' => __('settings.account_deleted')]);
    }
}
