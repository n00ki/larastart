<?php

declare(strict_types=1);

use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Features;

test('skips two-factor state validation when confirmation is disabled', function () {
    config()->set('fortify.features', [
        Features::twoFactorAuthentication(['confirm' => false]),
    ]);

    $user = User::factory()->create();

    $formRequest = TwoFactorAuthenticationRequest::createFrom(
        Request::create('/settings/two-factor', 'GET'),
    );
    $formRequest->setUserResolver(fn () => $user);

    expect(fn () => $formRequest->ensureStateIsValid())
        ->not->toThrow(Throwable::class);
});

test('records confirmation start time when user begins two-factor confirmation', function () {
    config()->set('fortify.features', [
        Features::twoFactorAuthentication(['confirm' => true]),
    ]);

    $user = User::factory()->create([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_confirmed_at' => null,
    ]);

    Session::start();
    Session::put('two_factor_empty_at', time() - 10);

    $request = Request::create('/settings/two-factor', 'GET');
    $request->setLaravelSession(Session::driver());

    $formRequest = TwoFactorAuthenticationRequest::createFrom($request);
    $formRequest->setUserResolver(fn () => $user);
    $formRequest->setContainer(app());

    $formRequest->ensureStateIsValid();

    expect(Session::has('two_factor_confirming_at'))->toBeTrue();
});
