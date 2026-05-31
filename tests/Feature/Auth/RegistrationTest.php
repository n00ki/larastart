<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

test('registration screen can be rendered', function () {
    $this->skipUnlessFortifyFeature(Features::registration());

    $response = $this->get('/register');

    $response
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('auth/register')
            ->where('passwordRules', 'minlength: 8;'),
        );
});

test('new users can register', function () {
    $this->skipUnlessFortifyFeature(Features::registration());

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertInertiaFlash('type', 'success')
        ->assertInertiaFlash('message', __('auth.registered'));
});

test('new user names are normalized during registration', function () {
    $this->skipUnlessFortifyFeature(Features::registration());

    $this->post('/register', [
        'name' => "  nOam\tShemESh  ",
        'email' => 'normalized@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', [
        'name' => 'Noam Shemesh',
        'email' => 'normalized@example.com',
    ]);
});

test('new user names reject unsupported punctuation during registration', function () {
    $this->skipUnlessFortifyFeature(Features::registration());

    $this->from('/register')->post('/register', [
        'name' => 'noam. shemesh',
        'email' => 'invalid-name@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors('name');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', [
        'email' => 'invalid-name@example.com',
    ]);
});

test('new users can register using a json request', function () {
    $this->skipUnlessFortifyFeature(Features::registration());

    $response = $this->postJson('/register', [
        'name' => 'Json Test User',
        'email' => 'json-test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertCreated();
});
