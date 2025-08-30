<?php

declare(strict_types=1);

use App\Actions\Settings\DeleteProfileAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->action = new DeleteProfileAction;
    $this->user = User::factory()->create();
});

test('it logs out user before deletion', function () {
    Auth::login($this->user);
    $request = Request::create('/', 'DELETE');
    $request->setUserResolver(fn () => $this->user);
    $request->setLaravelSession($this->app['session.store']);

    expect(Auth::check())->toBeTrue();

    $this->action->handle($this->user, $request);

    expect(Auth::check())->toBeFalse();
});

test('it deletes the user', function () {
    $request = Request::create('/', 'DELETE');
    $request->setLaravelSession($this->app['session.store']);
    $userId = $this->user->id;

    $this->action->handle($this->user, $request);

    $this->assertDatabaseMissing('users', [
        'id' => $userId,
    ]);
});

test('it invalidates session', function () {
    $request = Request::create('/', 'DELETE');
    $request->setLaravelSession($session = $this->app['session.store']);
    $session->put('test_key', 'test_value');

    expect($session->has('test_key'))->toBeTrue();

    $this->action->handle($this->user, $request);

    expect($session->has('test_key'))->toBeFalse();
});

test('it regenerates session token', function () {
    $request = Request::create('/', 'DELETE');
    $request->setLaravelSession($session = $this->app['session.store']);
    $originalToken = $session->token();

    $this->action->handle($this->user, $request);

    expect($session->token())->not->toBe($originalToken);
});
