<?php

declare(strict_types=1);

use App\Models\User;

test('user login happy path from homepage to dashboard', function (): void {
    $user = User::factory()->create();

    $page = visit('/');

    $page->click('Log in')
        ->assertSee('Log in to your account')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Log in')
        ->assertPathIs('/dashboard')
        ->assertSee('Dashboard')
        ->assertSee($user->name);

    $this->assertAuthenticatedAs($user);
});
