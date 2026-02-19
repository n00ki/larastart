<?php

declare(strict_types=1);

use App\Models\User;

test('user sees a success toast after updating profile', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/settings/profile');

    $page->assertPathIs('/settings/profile')
        ->fill('name', 'Updated User')
        ->fill('email', 'updated@example.com')
        ->click('@update-profile-button')
        ->assertPathIs('/settings/profile')
        ->assertSee(__('settings.profile_updated'))
        ->assertNoJavaScriptErrors();
});
