<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

test('registration screen can be rendered', function () {
    $this->skipUnlessFortifyFeature(Features::registration());

    $response = $this->get('/register');

    $response->assertStatus(200);
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
