<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Features;

test('security page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/security')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/security')
            ->where('canManageTwoFactor', true)
            ->where('twoFactorEnabled', false)
            ->where('requiresConfirmation', true),
        );
});

test('unverified users can access security settings', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/security')
        ->assertOk();
});

test('security page requires password confirmation when configured for two-factor authentication', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    config()->set('fortify.features', [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
        ]),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/security')
        ->assertRedirect('/user/confirm-password');
})->skip(fn () => ! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'), 'password confirmation not required for two-factor authentication');

test('security page renders without two-factor data when the feature is disabled', function () {
    config()->set('fortify.features', [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/security')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/security')
            ->where('canManageTwoFactor', false)
            ->missing('twoFactorEnabled')
            ->missing('requiresConfirmation'),
        );
});

test('password can be updated from the security page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings/security')
        ->put('/settings/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/security')
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('settings.password_updated'));

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update the password from the security page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings/security')
        ->put('/settings/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect('/settings/security');
});
