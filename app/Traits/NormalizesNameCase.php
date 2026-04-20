<?php

namespace App\Traits;

trait NormalizesNameCase
{
    /**
     * Normalize person/business/account names into title case.
     * - Keeps all-uppercase strings unchanged (e.g., ADEMOLA JOHN)
     * - Lowercases connector words in the middle (e.g., and, of, the)
     */
    protected function normalizeNameCase(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/', ' ', $value));

        if ($value === '') {
            return $value;
        }

        // Keep intentionally all-caps input as-is.
        if ($this->isAllCapsText($value)) {
            return $value;
        }

        $smallWords = [
            'a', 'an', 'and', 'as', 'at', 'by', 'for', 'from',
            'in', 'of', 'on', 'or', 'the', 'to', 'vs', 'via', 'with'
        ];

        $parts = explode(' ', strtolower($value));
        $lastIndex = count($parts) - 1;

        foreach ($parts as $index => $part) {
            if ($part === '') {
                continue;
            }

            $coreWord = preg_replace('/[^a-z\'-]/', '', $part);

            if ($coreWord !== '' && in_array($coreWord, $smallWords, true) && $index !== 0 && $index !== $lastIndex) {
                $parts[$index] = strtolower($part);
                continue;
            }

            $parts[$index] = preg_replace_callback(
                '/[a-z][a-z\'-]*/',
                function ($matches) {
                    return $this->titleizeToken($matches[0]);
                },
                $part
            );
        }

        return implode(' ', $parts);
    }

    protected function isAllCapsText(string $value): bool
    {
        $lettersOnly = preg_replace('/[^A-Za-z]/', '', $value);

        if ($lettersOnly === '') {
            return false;
        }

        return strtoupper($lettersOnly) === $lettersOnly;
    }

    protected function titleizeToken(string $token): string
    {
        $segments = preg_split('/([\'-])/', strtolower($token), -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($segments as $i => $segment) {
            if ($segment === "'" || $segment === '-') {
                continue;
            }

            if ($segment !== '') {
                $segments[$i] = ucfirst($segment);
            }
        }

        return implode('', $segments);
    }
}
