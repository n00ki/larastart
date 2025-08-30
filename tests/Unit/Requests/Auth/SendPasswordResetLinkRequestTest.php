<?php

declare(strict_types=1);

use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->request = new SendPasswordResetLinkRequest;
});

test('it authorizes all requests', function () {
    expect($this->request->authorize())->toBeTrue();
});

test('it validates required fields', function () {
    $validator = Validator::make([], $this->request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
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

test('it accepts any valid email format', function () {
    $rules = $this->request->rules();

    $validEmails = [
        'user@example.com',
        'test.email@domain.co.uk',
        'user+tag@example.org',
        'firstname.lastname@company.com',
    ];

    foreach ($validEmails as $email) {
        $validator = Validator::make(['email' => $email], $rules);
        expect($validator->errors()->has('email'))->toBeFalse("Failed for email: {$email}");
    }
});

test('it provides custom error messages', function () {
    $messages = $this->request->messages();

    expect($messages)->toHaveKey('email.required')
        ->and($messages)->toHaveKey('email.email')
        ->and($messages['email.required'])->toBe('An email address is required.')
        ->and($messages['email.email'])->toBe('Please provide a valid email address.');
});
