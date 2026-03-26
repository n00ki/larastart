<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

test('missing pages render the inertia error page with shared props', function () {
    $this->withUnencryptedCookie('theme', 'dark')
        ->get('/missing-page')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page
            ->component('error')
            ->where('status', 404)
            ->where('name', config('app.name'))
            ->where('theme', 'dark'),
        );
});

test('missing pages render the inertia error page in local environments', function () {
    config()->set('app.env', 'local');

    $this->get('/missing-page')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page
            ->component('error')
            ->where('status', 404),
        );
});

test('server errors render the inertia error page', function () {
    Route::middleware('web')->get('/test-error-page', function (): never {
        abort(500);
    });

    $this->get('/test-error-page')
        ->assertStatus(500)
        ->assertInertia(fn ($page) => $page
            ->component('error')
            ->where('status', 500)
            ->where('name', config('app.name')),
        );
});
