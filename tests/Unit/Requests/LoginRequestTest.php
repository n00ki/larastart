<?php

declare(strict_types=1);

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authorize method returns true', function () {
    $request = new LoginRequest;

    expect($request->authorize())->toBeTrue();
});

test('rules method returns correct validation rules', function () {
    $request = new LoginRequest;

    $rules = $request->rules();

    expect($rules)->toEqual([
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ]);
});

test('throttle key is generated correctly', function () {
    $request = new LoginRequest;
    $request->merge(['email' => 'test@example.com']);
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    $throttleKey = $request->throttleKey();

    expect($throttleKey)->toEqual('test@example.com|127.0.0.1');
});

test('throttle key handles special characters in email', function () {
    $request = new LoginRequest;
    $request->merge(['email' => 'tëst@ëxample.com']);
    $request->server->set('REMOTE_ADDR', '192.168.1.1');

    $throttleKey = $request->throttleKey();

    // Should transliterate special characters
    expect($throttleKey)->toEqual('test@example.com|192.168.1.1');
});
