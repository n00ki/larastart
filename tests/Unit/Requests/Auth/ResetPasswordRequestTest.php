<?php

declare(strict_types=1);

use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->request = new ResetPasswordRequest;
});

test('allows users to submit the password reset request', function () {
    expect($this->request->authorize())->toBeTrue();
});

test('requires token, email, and password when resetting password', function () {
    $validator = Validator::make([], $this->request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('token'))->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

test('requires a valid password reset token', function () {
    $rules = $this->request->rules();

    $validator = Validator::make(['token' => ''], $rules);
    expect($validator->errors()->has('token'))->toBeTrue();

    $validator = Validator::make(['token' => 'valid-token-string'], $rules);
    expect($validator->errors()->has('token'))->toBeFalse();
});

test('requires a valid reset email address', function () {
    $rules = $this->request->rules();

    $validator = Validator::make(['email' => ''], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    $validator = Validator::make(['email' => 'invalid-email'], $rules);
    expect($validator->errors()->has('email'))->toBeTrue();

    $validator = Validator::make(['email' => 'test@example.com'], $rules);
    expect($validator->errors()->has('email'))->toBeFalse();
});

test('requires a confirmed new password', function () {
    $rules = $this->request->rules();

    $validator = Validator::make(['password' => ''], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    $validator = Validator::make([
        'password' => 'newpassword123',
        'password_confirmation' => 'different',
    ], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    $validator = Validator::make([
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ], $rules);
    expect($validator->errors()->has('password'))->toBeFalse();
});

test('returns custom password reset validation messages', function () {
    $messages = $this->request->messages();

    expect($messages)->toHaveKey('token.required')
        ->and($messages)->toHaveKey('email.required')
        ->and($messages)->toHaveKey('email.email')
        ->and($messages)->toHaveKey('password.required')
        ->and($messages)->toHaveKey('password.confirmed')
        ->and($messages['token.required'])->toBe('A password reset token is required.')
        ->and($messages['email.email'])->toBe('Please provide a valid email address.');
});
