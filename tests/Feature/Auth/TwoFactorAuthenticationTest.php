<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Features;

test('two-factor authentication settings page requires authentication', function () {
    $this->get('/settings/two-factor')
        ->assertRedirect('/login');
});

test('two-factor authentication settings page requires verified email', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/two-factor')
        ->assertRedirect('/email/verify');
});

test('two-factor authentication settings page requires password confirmation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/two-factor')
        ->assertRedirect('/user/confirm-password');
})->skip(
    fn () => ! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
    'password confirmation not required for two-factor authentication',
);

test('two-factor authentication settings page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/two-factor')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('settings/two-factor')
            ->has('twoFactorEnabled')
            ->has('requiresConfirmation'),
        );
});

test('two-factor authentication is disabled by default', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/two-factor')
        ->assertInertia(fn ($page) => $page
            ->where('twoFactorEnabled', false),
        );
})->skip(fn () => ! Features::enabled(Features::twoFactorAuthentication()), 'two-factor authentication not enabled');

test('user can enable two-factor authentication', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication')
        ->assertRedirect();

    expect($user->fresh()->two_factor_secret)->not->toBeNull();
})->skip(fn () => ! Features::enabled(Features::twoFactorAuthentication()), 'two-factor authentication not enabled');

test('user can disable two-factor authentication', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication');

    $this->actingAs($user->fresh())
        ->withSession(['auth.password_confirmed_at' => time()])
        ->delete('/user/two-factor-authentication')
        ->assertRedirect();

    expect($user->fresh()->two_factor_secret)->toBeNull();
})->skip(fn () => ! Features::enabled(Features::twoFactorAuthentication()), 'two-factor authentication not enabled');

test('two-factor settings page works without password confirmation requirement', function () {
    // Temporarily disable confirmPassword option
    config()->set('fortify.features', [
        Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => false]),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/two-factor')
        ->assertSuccessful();
});

test('two-factor state tracks when user begins confirming', function () {
    $user = User::factory()->create();

    // Enable 2FA to get a secret without confirmation
    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication');

    $user->refresh();
    expect($user->two_factor_secret)->not->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();

    // Visit the page - this should trigger hasJustBegunConfirmingTwoFactorAuthentication
    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
            'two_factor_empty_at' => time() - 10, // Was empty before
        ])
        ->get('/settings/two-factor')
        ->assertSuccessful();
})->skip(fn () => ! Features::enabled(Features::twoFactorAuthentication()), 'two-factor authentication not enabled');
