<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;

test('email verification screen can be rendered for unverified users', function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/email/verify')
        ->assertSuccessful();
});

test('verified users are redirected away from email verification screen', function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/email/verify')
        ->assertRedirect('/dashboard');
});

test('email verification notice requires authentication', function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());

    $this->get('/email/verify')
        ->assertRedirect('/login');
});

test('email can be verified', function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());

    Event::fake();

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $this->actingAs($user)
        ->get($verificationUrl)
        ->assertRedirect('/dashboard?verified=1');

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('email is not verified with invalid hash', function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')],
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('resend verification email can be sent', function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertRedirect();
});

test('resend verification is a no-op for already verified users', function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertRedirect('/dashboard');
});
