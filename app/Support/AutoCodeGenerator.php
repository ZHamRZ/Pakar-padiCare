<?php

namespace App\Support;

class AutoCodeGenerator
{
    public static function generate(string $modelClass, string $column, string $prefix, int $digits = 2): string
    {
        $maxNumber = $modelClass::query()
            ->where($column, 'like', $prefix.'%')
            ->pluck($column)
            ->map(function (string $code) use ($prefix) {
                if (preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $code, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        do {
            $maxNumber++;
            $candidate = sprintf('%s%0'.$digits.'d', $prefix, $maxNumber);
        } while ($modelClass::query()->where($column, $candidate)->exists());

        return $candidate;
    }
}
