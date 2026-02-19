<?php

declare(strict_types=1);

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard')->assertOk();
});

test('unverified users are redirected to email verification page', function () {
    $this->actingAs(User::factory()->unverified()->create());

    $this->get('/dashboard')->assertRedirect('/email/verify');
});
