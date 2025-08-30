<?php

declare(strict_types=1);

use App\Http\Requests\Settings\DeleteProfileRequest;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->request = new DeleteProfileRequest;
});

test('it authorizes all requests', function () {
    expect($this->request->authorize())->toBeTrue();
});

test('it validates required fields', function () {
    $validator = Validator::make([], $this->request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

test('it validates password requirements', function () {
    $rules = $this->request->rules();

    // Test required
    $validator = Validator::make(['password' => ''], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    // Test current_password rule exists
    expect($rules['password'])->toContain('current_password');
});

test('it provides custom error messages', function () {
    $messages = $this->request->messages();

    expect($messages)->toHaveKey('password.required')
        ->and($messages)->toHaveKey('password.current_password')
        ->and($messages['password.required'])->toBe('Your current password is required to delete your account.')
        ->and($messages['password.current_password'])->toBe('The provided password does not match your current password.');
});
