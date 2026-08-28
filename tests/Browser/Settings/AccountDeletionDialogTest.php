<?php

declare(strict_types=1);

use App\Models\User;

test('user can dismiss the delete account dialog without deleting their account', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/settings/profile');

    $page->assertPathIs('/settings/profile')
        ->click('@delete-user-button')
        ->assertSee('Are you sure you want to delete your account?')
        ->click('Cancel')
        ->assertDontSee('Are you sure you want to delete your account?')
        ->assertPathIs('/settings/profile')
        ->assertNoJavaScriptErrors();

    $this->assertModelExists($user);
});
