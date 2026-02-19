<?php

declare(strict_types=1);

use App\Models\User;

test('account settings page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/settings/account');

    $response->assertOk();
});

test('unverified users are redirected from account settings', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/settings/account')
        ->assertRedirect('/email/verify');
});

test('unverified users cannot delete their account from settings', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->delete('/settings/account', [
            'password' => 'password',
        ])
        ->assertRedirect('/email/verify');
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/settings/account', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings/account')
        ->delete('/settings/account', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/settings/account');

    expect($user->fresh())->not->toBeNull();
});
