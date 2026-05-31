<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\User;
use App\Support\UserName;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

trait ProfileValidationRules
{
    /**
     * @return array<string, array<int, Unique|string>>
     */
    protected function profileRules(?string $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255', 'regex:' . UserName::VALIDATION_PATTERN];
    }

    /**
     * @return array<string, string>
     */
    protected function profileMessages(): array
    {
        return [
            'name.regex' => 'Names may only contain letters, spaces, hyphens, and apostrophes.',
        ];
    }

    /**
     * @return array<int, Unique|string>
     */
    protected function emailRules(?string $userId = null): array
    {
        return [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
