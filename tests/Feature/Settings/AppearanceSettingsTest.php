<?php

declare(strict_types=1);

use App\Models\User;

test('appearance settings page requires authentication', function () {
    $this->get('/settings/appearance')->assertRedirect('/login');
});

test('unverified users can access appearance settings page', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/settings/appearance')
        ->assertOk();
});
