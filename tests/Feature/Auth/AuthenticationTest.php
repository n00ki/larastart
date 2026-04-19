<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('auth.logged_in'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can authenticate using a json request', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    $response
        ->assertOk()
        ->assertJson([
            'two_factor' => false,
        ]);
});

test('users see the login toast after completing two-factor authentication', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication')
        ->assertRedirect();

    $user->forceFill([
        'two_factor_confirmed_at' => now(),
    ])->save();

    auth()->logout();

    $loginResponse = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $loginResponse->assertRedirect('/two-factor-challenge');
    $this->assertGuest();

    $code = app(Google2FA::class)->getCurrentOtp(
        Fortify::currentEncrypter()->decrypt($user->fresh()->two_factor_secret),
    );

    $challengeResponse = $this->post('/two-factor-challenge', [
        'code' => $code,
    ]);

    $this->assertAuthenticatedAs($user->fresh());
    $challengeResponse
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('auth.logged_in'));
});

test('users can authenticate with two-factor authentication using a json request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication')
        ->assertRedirect();

    $user->forceFill([
        'two_factor_confirmed_at' => now(),
    ])->save();

    auth()->logout();

    $loginResponse = $this->postJson('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $loginResponse
        ->assertOk()
        ->assertJson([
            'two_factor' => true,
        ]);

    $this->assertGuest();

    $code = app(Google2FA::class)->getCurrentOtp(
        Fortify::currentEncrypter()->decrypt($user->fresh()->two_factor_secret),
    );

    $challengeResponse = $this->postJson('/two-factor-challenge', [
        'code' => $code,
    ]);

    $this->assertAuthenticatedAs($user->fresh());
    $challengeResponse->assertNoContent();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response
        ->assertRedirect('/')
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('auth.logged_out'));
});

test('users can logout using a json request', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/logout');

    $this->assertGuest();
    $response->assertNoContent();
});

test('login attempts are rate limited after too many failed attempts', function () {
    $user = User::factory()->create();

    // Make 5 failed login attempts to trigger rate limiting
    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    // The 6th attempt should be rate limited
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
    $this->assertGuest();
});

test('login rate limiting is cleared after successful authentication', function () {
    $user = User::factory()->create();

    // Make some failed attempts first
    for ($i = 0; $i < 3; $i++) {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    // Successful login should clear the rate limiting
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('auth.logged_in'));

    // Logout and verify we can login again immediately
    $this->post('/logout');

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('auth.logged_in'));
});

test('users can authenticate with remember me option', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ]);

    $this->assertAuthenticated();
    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('auth.logged_in'));
});
