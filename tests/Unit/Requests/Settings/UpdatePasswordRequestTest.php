<?php

declare(strict_types=1);

use App\Http\Requests\Settings\UpdatePasswordRequest;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->request = new UpdatePasswordRequest;
});

test('it authorizes all requests', function () {
    expect($this->request->authorize())->toBeTrue();
});

test('it validates required fields', function () {
    $validator = Validator::make([], $this->request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('current_password'))->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

test('it validates current password requirements', function () {
    $rules = $this->request->rules();

    // Test required
    $validator = Validator::make(['current_password' => ''], $rules);
    expect($validator->errors()->has('current_password'))->toBeTrue();

    // Test current_password rule exists
    expect($rules['current_password'])->toContain('current_password');
});

test('it validates new password requirements', function () {
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

    expect($messages)->toHaveKey('current_password.required')
        ->and($messages)->toHaveKey('current_password.current_password')
        ->and($messages)->toHaveKey('password.required')
        ->and($messages)->toHaveKey('password.confirmed')
        ->and($messages['current_password.required'])->toBe('Your current password is required.')
        ->and($messages['current_password.current_password'])->toBe('The provided password does not match your current password.')
        ->and($messages['password.required'])->toBe('A new password is required.')
        ->and($messages['password.confirmed'])->toBe('The password confirmation does not match.');
});
