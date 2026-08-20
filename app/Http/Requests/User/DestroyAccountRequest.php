<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use Override;

final class DestroyAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'password.required' => 'Your current password is required to delete your account.',
            'password.current_password' => 'The provided password does not match your current password.',
        ];
    }
}
