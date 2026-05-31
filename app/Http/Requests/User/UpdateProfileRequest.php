<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Concerns\ProfileValidationRules;
use App\Http\Requests\FormRequest;
use App\Support\UserName;

final class UpdateProfileRequest extends FormRequest
{
    use ProfileValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->profileRules($this->user()->id);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->profileMessages();
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');

        if (is_string($name)) {
            $this->merge(['name' => UserName::normalize($name)]);
        }
    }
}
