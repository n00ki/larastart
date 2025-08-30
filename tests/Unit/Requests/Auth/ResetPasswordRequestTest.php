<?php

declare(strict_types=1);

use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->request = new ResetPasswordRequest;
});

test('it authorizes all requests', function () {
    expect($this->request->authorize())->toBeTrue();
});

test('it validates required fields', function () {
    $validator = Validator::make([], $this->request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('token'))->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

test('it validates token requirements', function () {
    $rules = $this->request->rules();

    // Test required
    $validator = Validator::make(['token' => ''], $rules);
    expect($validator->errors()->has('token'))->toBeTrue();

    // Test valid token
    $validator = Validator::make(['token' => 'valid-token-string'], $rules);
    expect($validator->errors()->has('token'))->toBeFalse();
});

test('it validates email requirements', function () {
    $rules = $this->request->rules();

    // Test required
    $validator = Validator::make(['email' => ''], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    // Test valid email format
    $validator = Validator::make(['email' => 'invalid-email'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    // Test valid email
    $validator = Validator::make(['email' => 'test@example.com'], $rules);
    expect($validator->errors()->has('email'))->toBeFalse();
});

test('it validates password requirements', function () {
    $rules = $this->request->rules();

    // Test required
    $validator = Validator::make(['password' => ''], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    // Test confirmation required
    $validator = Validator::make([
        'password' => 'newpassword123',
        'password_confirmation' => 'different',
    ], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    // Test valid password with confirmation
    $validator = Validator::make([
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ], $rules);
    expect($validator->errors()->has('password'))->toBeFalse();
});

test('it provides custom error messages', function () {
    $messages = $this->request->messages();

    expect($messages)->toHaveKey('token.required')
        ->and($messages)->toHaveKey('email.required')
        ->and($messages)->toHaveKey('email.email')
        ->and($messages)->toHaveKey('password.required')
        ->and($messages)->toHaveKey('password.confirmed')
        ->and($messages['token.required'])->toBe('A password reset token is required.')
        ->and($messages['email.email'])->toBe('Please provide a valid email address.');
});
