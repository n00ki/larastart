<?php

declare(strict_types=1);

use App\Actions\User\DeleteUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->action = new DeleteUser;
    $this->user = User::factory()->create();
});

test('logs out the user when deleting the account', function () {
    Auth::login($this->user);
    $request = Request::create('/', 'DELETE');
    $request->setUserResolver(fn () => $this->user);
    $request->setLaravelSession($this->app['session.store']);

    expect(Auth::check())->toBeTrue();

    $this->action->handle($this->user, $request);

    expect(Auth::check())->toBeFalse();
});

test('deletes the user account from storage', function () {
    $request = Request::create('/', 'DELETE');
    $request->setLaravelSession($this->app['session.store']);
    $userId = $this->user->id;

    $this->action->handle($this->user, $request);

    $this->assertDatabaseMissing('users', [
        'id' => $userId,
    ]);
});

test('invalidates session after account deletion', function () {
    $request = Request::create('/', 'DELETE');
    $request->setLaravelSession($session = $this->app['session.store']);
    $session->put('test_key', 'test_value');

    expect($session->has('test_key'))->toBeTrue();

    $this->action->handle($this->user, $request);

    expect($session->has('test_key'))->toBeFalse();
});

test('regenerates session token after account deletion', function () {
    $request = Request::create('/', 'DELETE');
    $request->setLaravelSession($session = $this->app['session.store']);
    $originalToken = $session->token();

    $this->action->handle($this->user, $request);

    expect($session->token())->not->toBe($originalToken);
});
