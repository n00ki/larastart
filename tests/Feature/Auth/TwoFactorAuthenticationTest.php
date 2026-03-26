<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Features;

test('two-factor authentication settings page requires authentication', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $this->get('/settings/security')
        ->assertRedirect('/login');
});

test('unverified users can access two-factor authentication settings page', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/security')
        ->assertOk();
});

test('two-factor authentication settings page requires password confirmation', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/security')
        ->assertRedirect('/user/confirm-password');
})->skip(fn () => ! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'), 'password confirmation not required for two-factor authentication');

test('two-factor authentication settings page can be rendered', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/security')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('settings/security')
            ->where('canManageTwoFactor', true)
            ->has('twoFactorEnabled')
            ->has('requiresConfirmation'),
        );
});

test('two-factor authentication is disabled by default', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/security')
        ->assertInertia(fn ($page) => $page
            ->where('twoFactorEnabled', false),
        );
});

test('user can enable two-factor authentication', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication')
        ->assertRedirect();

    expect($user->fresh()->two_factor_secret)->not->toBeNull();
});

test('user can disable two-factor authentication', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication');

    $this->actingAs($user->fresh())
        ->withSession(['auth.password_confirmed_at' => time()])
        ->delete('/user/two-factor-authentication')
        ->assertRedirect();

    expect($user->fresh()->two_factor_secret)->toBeNull();
});

test('invalid two-factor confirmation does not confirm two-factor authentication', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post('/user/two-factor-authentication')
        ->assertRedirect();

    $this->actingAs($user->fresh())
        ->withSession(['auth.password_confirmed_at' => time()])
        ->from('/settings/security')
        ->post('/user/confirmed-two-factor-authentication', [
            'code' => '000000',
        ])
        ->assertRedirect('/settings/security');

    expect($user->fresh()->two_factor_confirmed_at)->toBeNull();
});

test('two-factor settings page works without password confirmation requirement', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    // Temporarily disable confirmPassword option
    config()->set('fortify.features', [
        Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => false]),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/security')
        ->assertSuccessful();
});

test('two-factor state tracks when user begins confirming', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

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
        ->get('/settings/security')
        ->assertSuccessful();
});
