<?php

declare(strict_types=1);

use App\Http\Requests\User\DeleteUserRequest;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->request = new DeleteUserRequest;
});

test('allows users to submit account deletion request', function () {
    expect($this->request->authorize())->toBeTrue();
});

test('requires password to delete account', function () {
    $validator = Validator::make([], $this->request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

test('requires current password validation for account deletion', function () {
    $rules = $this->request->rules();

    $validator = Validator::make(['password' => ''], $rules);
    expect($validator->errors()->has('password'))->toBeTrue();

    expect($rules['password'])->toContain('current_password');
});

test('returns custom account deletion validation messages', function () {
    $messages = $this->request->messages();

    expect($messages)->toHaveKey('password.required')
        ->and($messages)->toHaveKey('password.current_password')
        ->and($messages['password.required'])->toBe('Your current password is required to delete your account.')
        ->and($messages['password.current_password'])->toBe('The provided password does not match your current password.');
});
