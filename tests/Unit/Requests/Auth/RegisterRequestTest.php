<?php

declare(strict_types=1);

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->request = new RegisterRequest;
});

test('allows users to submit the registration request', function () {
    expect($this->request->authorize())->toBeTrue();
});

test('requires name, email, and password for registration', function () {
    $validator = Validator::make([], $this->request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

test('requires a valid registration name', function () {
    $rules = $this->request->rules();

    $validator = Validator::make(['name' => ''], $rules);
    expect($validator->errors()->has('name'))->toBeTrue();

    $validator = Validator::make(['name' => str_repeat('a', 256)], $rules);
    expect($validator->errors()->has('name'))->toBeTrue();

    $validator = Validator::make(['name' => 'John Doe'], $rules);
    expect($validator->errors()->has('name'))->toBeFalse();
});

test('requires a valid registration email', function () {
    $rules = $this->request->rules();

    $validator = Validator::make(['email' => ''], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    $validator = Validator::make(['email' => 'invalid-email'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    $validator = Validator::make(['email' => 'TEST@EXAMPLE.COM'], $rules);
    expect($validator->fails())->toBeTrue();

    $validator = Validator::make(['email' => str_repeat('a', 250) . '@example.com'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('requires a unique email for registration', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $rules = $this->request->rules();
    $validator = Validator::make(['email' => 'existing@example.com'], $rules);

    expect($validator->errors()->has('email'))->toBeTrue();
});

test('requires a confirmed registration password', function () {
    $rules = $this->request->rules();

    $validator = Validator::make(['password' => ''], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    $validator = Validator::make([
        'password' => 'password123',
        'password_confirmation' => 'different',
    ], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    $validator = Validator::make([
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $rules);
    expect($validator->errors()->has('password'))->toBeFalse();
});

test('returns custom registration validation messages', function () {
    $messages = $this->request->messages();

    expect($messages)->toHaveKey('name.required')
        ->and($messages)->toHaveKey('email.required')
        ->and($messages)->toHaveKey('email.unique')
        ->and($messages)->toHaveKey('password.required')
        ->and($messages)->toHaveKey('password.confirmed')
        ->and($messages['name.required'])->toBe('A name is required for registration.')
        ->and($messages['email.unique'])->toBe('This email address is already registered.');
});
