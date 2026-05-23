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
            ->where('canManagePasskeys', true)
            ->where('canManageTwoFactor', true)
            ->where('passkeys', [])
            ->where('passwordRules', 'minlength: 8;')
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
});

test('security page requires password confirmation when configured for passkeys', function () {
    config()->set('fortify.features', [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::passkeys([
            'confirmPassword' => true,
        ]),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/security')
        ->assertRedirect('/user/confirm-password');
});

test('security page includes registered passkeys', function () {
    $user = User::factory()->create();

    $user->passkeys()->create([
        'name' => 'MacBook Pro',
        'credential_id' => 'credential-id',
        'credential' => ['aaguid' => '00000000-0000-0000-0000-000000000000'],
    ]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/security')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/security')
            ->where('canManagePasskeys', true)
            ->has('passkeys', 1)
            ->where('passkeys.0.name', 'MacBook Pro')
            ->where('passkeys.0.authenticator', null)
            ->where('passkeys.0.last_used_at_diff', null),
        );
});

test('security page renders without optional security data when the features are disabled', function () {
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
            ->where('canManagePasskeys', false)
            ->where('canManageTwoFactor', false)
            ->where('passwordRules', 'minlength: 8;')
            ->missing('passkeys')
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
