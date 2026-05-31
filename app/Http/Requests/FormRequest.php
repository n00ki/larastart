<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

abstract class FormRequest extends BaseFormRequest
{
    protected function shouldFailOnUnknownFields(): bool
    {
        return ! app()->isProduction();
    }
}
