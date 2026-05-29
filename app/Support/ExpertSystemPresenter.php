<?php

namespace App\Support;

class ExpertSystemPresenter
{
    public static function percent(float|int|null $value, int $precision = 0): string
    {
        $normalized = self::normalize($value) * 100;

        return number_format($normalized, $precision, ',', '.').'%';
    }

    public static function rawPercent(float|int|null $value, int $precision = 0): float
    {
        return round(self::normalize($value) * 100, $precision);
    }

    public static function confidenceLabel(float|int|null $value): string
    {
        $normalized = self::normalize($value);

        return match (true) {
            $normalized < 0.5 => 'Rendah',
            $normalized < 0.8 => 'Sedang',
            default => 'Tinggi',
        };
    }

    public static function confidenceTone(float|int|null $value): string
    {
        $normalized = self::normalize($value);

        return match (true) {
            $normalized < 0.5 => 'warning',
            $normalized < 0.8 => 'info',
            default => 'success',
        };
    }

    public static function recommendationBadge(?string $preference): array
    {
        $key = strtolower(trim((string) $preference));

        return match ($key) {
            'hemat', 'hemat biaya' => [
                'label' => 'Hemat Biaya',
                'tone' => 'warning',
                'icon' => 'bi-piggy-bank',
            ],
            'efisiensi', 'efisiensi tinggi' => [
                'label' => 'Efisiensi Tinggi',
                'tone' => 'success',
                'icon' => 'bi-lightning-charge',
            ],
            default => [
                'label' => 'Seimbang',
                'tone' => 'info',
                'icon' => 'bi-sliders',
            ],
        };
    }

    public static function lowConfidenceMessage(float|int|null $value): ?string
    {
        return self::normalize($value) < 0.5
            ? 'Hasil kurang meyakinkan, disarankan konsultasi dengan pakar.'
            : null;
    }

    public static function shortDescription(
        ?string $primary,
        ?string $secondary = null,
        int $limit = 140
    ): string {
        $text = trim((string) ($primary ?: $secondary ?: 'Informasi detail akan membantu pengguna memahami rekomendasi ini.'));

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit - 3)).'...';
    }

    private static function normalize(float|int|null $value): float
    {
        if ($value === null) {
            return 0;
        }

        return max(0, min(1, (float) $value));
    }
}
