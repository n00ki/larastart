<?php

declare(strict_types=1);

use App\Models\User;

test('user sees a success toast after logout', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/dashboard');

    $page->assertPathIs('/dashboard')
        ->click('@sidebar-menu-button')
        ->click('@logout-button')
        ->assertPathIs('/')
        ->assertSee(__('auth.logged_out'))
        ->assertNoJavaScriptErrors();

    $this->assertGuest();
});
