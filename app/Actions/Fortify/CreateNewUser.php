<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Support\UserName;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use SensitiveParameter;

final readonly class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    public function create(#[SensitiveParameter] array $input): User
    {
        return $this->handle($input);
    }

    public function handle(#[SensitiveParameter] array $input): User
    {
        if (isset($input['name']) && is_string($input['name'])) {
            $input['name'] = UserName::normalize($input['name']);
        }

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ], $this->profileMessages())->validate();

        return DB::transaction(fn (): User => User::query()->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]), attempts: 3);
    }
}
