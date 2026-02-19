<?php

declare(strict_types=1);

use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'existing@example.com',
    ]);
});

test('requires name and email when updating profile', function () {
    $request = new UpdateProfileRequest;
    $request->setUserResolver(fn () => $this->user);

    $validator = Validator::make([], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

test('requires a valid profile name', function () {
    $request = new UpdateProfileRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    $validator = Validator::make(['name' => ''], $rules);
    expect($validator->errors()->has('name'))->toBeTrue();

    $validator = Validator::make(['name' => str_repeat('a', 256)], $rules);
    expect($validator->errors()->has('name'))->toBeTrue();

    $validator = Validator::make(['name' => 'John Doe'], $rules);
    expect($validator->errors()->has('name'))->toBeFalse();
});

test('requires a valid profile email', function () {
    $request = new UpdateProfileRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    $validator = Validator::make(['email' => ''], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    $validator = Validator::make(['email' => 'invalid-email'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    $validator = Validator::make(['email' => 'TEST@EXAMPLE.COM'], $rules);
    expect($validator->fails())->toBeTrue();

    $validator = Validator::make(['email' => str_repeat('a', 250) . '@example.com'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('allows keeping current email but rejects another user email', function () {
    User::factory()->create(['email' => 'other@example.com']);

    $request = new UpdateProfileRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    $validator = Validator::make(['email' => $this->user->email], $rules);
    expect($validator->errors()->has('email'))->toBeFalse();

    $validator = Validator::make(['email' => 'other@example.com'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('allows updating profile without changing email', function () {
    $request = new UpdateProfileRequest;
    $request->setUserResolver(fn () => $this->user);
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Updated Name',
        'email' => $this->user->email,
    ], $rules);

    expect($validator->passes())->toBeTrue();
});
