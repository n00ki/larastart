<?php

declare(strict_types=1);

use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'existing@example.com',
    ]);
});

test('it validates required fields', function () {
    $request = new ProfileUpdateRequest;
    $request->setUserResolver(fn () => $this->user);

    $validator = Validator::make([], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

test('it validates name requirements', function () {
    $request = new ProfileUpdateRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    // Test required
    $validator = Validator::make(['name' => ''], $rules);
    expect($validator->errors()->has('name'))->toBeTrue();

    // Test max length
    $validator = Validator::make(['name' => str_repeat('a', 256)], $rules);
    expect($validator->errors()->has('name'))->toBeTrue();

    // Test valid name
    $validator = Validator::make(['name' => 'John Doe'], $rules);
    expect($validator->errors()->has('name'))->toBeFalse();
});

test('it validates email requirements', function () {
    $request = new ProfileUpdateRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    // Test required
    $validator = Validator::make(['email' => ''], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    // Test valid email format
    $validator = Validator::make(['email' => 'invalid-email'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    // Test lowercase requirement - uppercase should fail
    $validator = Validator::make(['email' => 'TEST@EXAMPLE.COM'], $rules);
    expect($validator->fails())->toBeTrue();

    // Test max length
    $validator = Validator::make(['email' => str_repeat('a', 250).'@example.com'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('it validates email uniqueness except for current user', function () {
    // Create another user with different email
    User::factory()->create(['email' => 'other@example.com']);

    $request = new ProfileUpdateRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    // Test that current user's email is allowed
    $validator = Validator::make(['email' => $this->user->email], $rules);
    expect($validator->errors()->has('email'))->toBeFalse();

    // Test that other user's email is not allowed
    $validator = Validator::make(['email' => 'other@example.com'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('it allows updating to same email', function () {
    $request = new ProfileUpdateRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Updated Name',
        'email' => $this->user->email, // Same email
    ], $rules);

    expect($validator->passes())->toBeTrue();
});
