<?php

declare(strict_types=1);

use App\Models\User;

test('user can navigate between settings pages', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/settings/profile');

    $page->assertPathIs('/settings/profile')
        ->click('@settings-nav-appearance')
        ->assertPathIs('/settings/appearance')
        ->assertSee('Appearance settings')
        ->click('@settings-nav-profile')
        ->assertPathIs('/settings/profile')
        ->assertSee('Profile Information')
        ->assertNoJavaScriptErrors();
});
