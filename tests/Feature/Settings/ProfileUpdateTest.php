<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/settings/profile');

    $response->assertOk();
});

test('unverified users can access profile settings', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/settings/profile')
        ->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/profile')
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('settings.profile_updated'));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
});

test('users can sign in with their new mixed-case email', function () {
    $user = User::factory()->create();
    $email = 'New@Example.com';

    $this->actingAs($user)
        ->patch('/settings/profile', ['name' => 'Test User', 'email' => $email])
        ->assertSessionHasNoErrors();

    expect($user->refresh()->email)->toBe('new@example.com');

    $this->post('/logout');
    $this->assertGuest();

    $this->post('/login', ['email' => $email, 'password' => 'password']);
    $this->assertAuthenticatedAs($user);
});

test('profile updates reject an email already in use regardless of case', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create(['email' => 'original@example.com']);

    $this->actingAs($user)
        ->patch('/settings/profile', ['name' => 'Test User', 'email' => 'Taken@Example.com'])
        ->assertSessionHasErrors(['email' => __('validation.unique', ['attribute' => 'email'])]);

    expect($user->refresh()->email)->toBe('original@example.com');
});

test('profile updates require lowercase email when Fortify lowercasing is off', function () {
    config(['fortify.lowercase_usernames' => false]);

    $this->actingAs(User::factory()->create())
        ->patch('/settings/profile', ['name' => 'Test User', 'email' => 'New@Example.com'])
        ->assertSessionHasErrors('email');
});

test('profile names are normalized when updated', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->patch('/settings/profile', [
            'name' => "  nOam\tShemESh  ",
            'email' => 'test@example.com',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/profile');

    expect($user->refresh()->name)->toBe('Noam Shemesh');
});

test('profile names reject unsupported punctuation when updated', function () {
    $user = User::factory()->create([
        'name' => 'Original User',
        'email' => 'original@example.com',
    ]);

    $this
        ->actingAs($user)
        ->from('/settings/profile')
        ->patch('/settings/profile', [
            'name' => 'noam. shemesh',
            'email' => 'updated@example.com',
        ])
        ->assertSessionHasErrors('name')
        ->assertRedirect('/settings/profile');

    expect($user->refresh()->name)->toBe('Original User')
        ->and($user->email)->toBe('original@example.com');
});

test('profile updates reject unexpected fields', function () {
    $user = User::factory()->create([
        'name' => 'Original User',
        'email' => 'original@example.com',
    ]);

    $this
        ->actingAs($user)
        ->from('/settings/profile')
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => true,
        ])
        ->assertSessionHasErrors('is_admin')
        ->assertRedirect('/settings/profile');

    expect($user->refresh()->name)->toBe('Original User')
        ->and($user->email)->toBe('original@example.com');
});

test('email verification is reset and notification is sent when email changes', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => 'new@example.com',
        ])
        ->assertRedirect('/settings/profile')
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('profile information can be updated with same email', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/profile');

    expect($user->refresh()->name)->toBe('Test User');
});

test('a case-only email change keeps the address verified', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'verified@example.com']);

    $this->actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => 'Verified@Example.com',
        ])
        ->assertRedirect('/settings/profile')
        ->assertSessionHasNoErrors();

    expect($user->fresh()->email_verified_at)->toEqual($user->email_verified_at);
    Notification::assertNotSentTo($user, VerifyEmail::class);
});
