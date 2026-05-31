<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;
use Normalizer;

final class UserName
{
    public const string VALIDATION_PATTERN = '/^\p{L}[\p{L}\p{M}]*(?:[ \-\'\x{2019}]\p{L}[\p{L}\p{M}]*)*$/u';

    public static function normalize(string $name): string
    {
        if (class_exists(Normalizer::class)) {
            $name = Normalizer::normalize($name, Normalizer::FORM_C) ?: $name;
        }

        $name = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $name) ?? $name;

        return self::titleCase(Str::squish($name));
    }

    private static function titleCase(string $name): string
    {
        return preg_replace_callback(
            '/\p{L}[\p{L}\p{M}]*/u',
            fn (array $matches): string => self::capitalize($matches[0]),
            $name,
        ) ?? $name;
    }

    private static function capitalize(string $part): string
    {
        return Str::ucfirst(Str::lower($part));
    }
}
