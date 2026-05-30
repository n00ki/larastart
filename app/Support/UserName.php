<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;
use Normalizer;

final class UserName
{
    public static function normalize(string $name): string
    {
        if (class_exists(Normalizer::class)) {
            $name = Normalizer::normalize($name, Normalizer::FORM_C) ?: $name;
        }

        $name = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $name) ?? $name;

        return Str::squish($name);
    }
}
