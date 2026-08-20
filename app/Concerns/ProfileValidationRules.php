<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\User;
use App\Support\UserName;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    protected function profileRules(?string $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($userId),
        ];
    }

    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255', 'regex:' . UserName::VALIDATION_PATTERN];
    }

    protected function profileMessages(): array
    {
        return [
            'name.regex' => 'Names may only contain letters, spaces, hyphens, and apostrophes.',
        ];
    }

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
