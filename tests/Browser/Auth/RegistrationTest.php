<?php

declare(strict_types=1);

test('user sees a success toast after registration', function (): void {
    $page = visit('/register');

    $page->assertPathIs('/register')
        ->fill('name', 'Toast Test User')
        ->fill('email', 'toast-register@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->click('@register-button')
        ->assertPathIs('/dashboard')
        ->assertSee('Dashboard')
        ->assertSee(__('auth.registered'))
        ->assertNoJavaScriptErrors();

    $this->assertAuthenticated();
});
