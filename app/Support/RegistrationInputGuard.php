<?php

namespace App\Support;

use Illuminate\Support\Str;

class RegistrationInputGuard
{
    public static function humanNameRules(int $min = 5, int $max = 80): array
    {
        return [
            'required',
            'string',
            "min:{$min}",
            "max:{$max}",
            function ($attribute, $value, $fail) {
                if (! self::looksLikeHumanName($value)) {
                    $fail('Please enter your real full name.');
                }
            },
        ];
    }

    public static function emailRules(string $uniqueRule = 'unique:users,email'): array
    {
        return [
            'required',
            'string',
            'lowercase',
            'email:rfc,dns',
            'max:255',
            $uniqueRule,
            function ($attribute, $value, $fail) {
                if (! self::looksLikeUsableEmail($value)) {
                    $fail('Please use a valid personal or business email address.');
                }
            },
        ];
    }

    public static function businessNameRules(int $min = 3, int $max = 120): array
    {
        return [
            'required',
            'string',
            "min:{$min}",
            "max:{$max}",
            function ($attribute, $value, $fail) {
                if (! self::looksLikeBusinessName($value)) {
                    $fail('Please enter a valid business name.');
                }
            },
        ];
    }

    public static function phoneRules(): array
    {
        return ['nullable', 'string', 'min:7', 'max:20', 'regex:/^[0-9+()\-\s]+$/'];
    }

    public static function looksLikeHumanName(?string $value): bool
    {
        $value = self::normalizeText($value);

        if ($value === '' || Str::length($value) < 5 || Str::length($value) > 80) {
            return false;
        }

        if (preg_match('/https?:\/\/|www\.|@|[0-9_]/i', $value)) {
            return false;
        }

        if (! preg_match('/^[\pL][\pL\s\'\.\-]+$/u', $value)) {
            return false;
        }

        $parts = preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($parts) < 2) {
            return false;
        }

        foreach ($parts as $part) {
            $token = trim($part, " .'-");

            if (Str::length($token) < 2 || Str::length($token) > 30 || self::looksRandomToken($token)) {
                return false;
            }
        }

        return true;
    }

    public static function looksLikeBusinessName(?string $value): bool
    {
        $value = self::normalizeText($value);

        if ($value === '' || Str::length($value) < 3 || Str::length($value) > 120) {
            return false;
        }

        if (preg_match('/https?:\/\/|www\.|@/i', $value)) {
            return false;
        }

        if (! preg_match('/^[\pL\pN\s&\.\,\'\(\)\+\-\/]+$/u', $value)) {
            return false;
        }

        if (self::regexCount('/\pL/u', $value) < 2 || preg_match('/(.)\1{4,}/u', $value)) {
            return false;
        }

        $tokens = preg_split('/[\s&\.\,\'\(\)\+\-\/]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($tokens as $token) {
            if (self::looksRandomToken($token)) {
                return false;
            }
        }

        return true;
    }

    public static function looksLikeUsableEmail(?string $value): bool
    {
        $value = Str::lower(trim((string) $value));

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        [$localPart] = explode('@', $value, 2);

        if (Str::length($localPart) < 2 || Str::length($localPart) > 64) {
            return false;
        }

        if (str_starts_with($localPart, '.') || str_ends_with($localPart, '.') || str_contains($localPart, '..')) {
            return false;
        }

        $segments = explode('.', $localPart);
        $singleCharacterSegments = count(array_filter($segments, fn ($segment) => Str::length($segment) === 1));

        if (substr_count($localPart, '.') > 4 || $singleCharacterSegments >= 3) {
            return false;
        }

        $compactLocalPart = preg_replace('/[^a-z0-9]/i', '', $localPart) ?: '';

        if (self::looksRandomToken($compactLocalPart)) {
            return false;
        }

        return true;
    }

    private static function looksRandomToken(string $token): bool
    {
        $token = trim($token);
        $length = Str::length($token);

        if ($length < 10) {
            return false;
        }

        $uppercase = self::regexCount('/\p{Lu}/u', $token);
        $lowercase = self::regexCount('/\p{Ll}/u', $token);
        $letters = self::regexCount('/\pL/u', $token);
        $vowels = self::regexCount('/[aeiou]/i', $token);

        if ($length >= 12 && $uppercase >= 3 && $lowercase >= 5) {
            return true;
        }

        if ($letters >= 12 && $vowels > 0 && ($vowels / max($letters, 1)) < 0.22) {
            return true;
        }

        if ($length >= 14 && preg_match('/[a-z][A-Z][a-z]|[A-Z][a-z][A-Z]/', $token) && $uppercase >= 3) {
            return true;
        }

        return false;
    }

    private static function normalizeText(?string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value) ?: '');
    }

    private static function regexCount(string $pattern, string $value): int
    {
        return preg_match_all($pattern, $value, $matches) ?: 0;
    }
}
