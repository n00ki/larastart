<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Http\Requests\FormRequest;
use App\Support\UserName;

final class RegisterRequest extends FormRequest
{
    use PasswordValidationRules, ProfileValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->profileMessages(),
            'name.required' => 'A name is required for registration.',
            'email.required' => 'An email address is required for registration.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'A password is required for registration.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');

        if (is_string($name)) {
            $this->merge(['name' => UserName::normalize($name)]);
        }
    }
}
