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

test('json requests receive json errors instead of the inertia error page', function () {
    Route::middleware('web')->get('/test-json-error-page', function (): never {
        abort(404);
    });

    $this->get('/test-json-error-page', ['Accept' => 'application/json'])
        ->assertNotFound()
        ->assertHeaderMissing('X-Inertia')
        ->assertHeader('Content-Type', 'application/json');
});

test('api routes receive json errors without an explicit json accept header', function () {
    Route::get('/api/test-json-error-page', function (): never {
        abort(404);
    });

    $this->get('/api/test-json-error-page')
        ->assertNotFound()
        ->assertHeaderMissing('X-Inertia')
        ->assertHeader('Content-Type', 'application/json');
});
