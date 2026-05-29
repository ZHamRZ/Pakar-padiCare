<?php

namespace App\Services;

use App\Models\Rekomendasi;

class RecommendationService
{
    public function __construct(
        private CertaintyFactorService $cfService,
        private CertaintyFactorEngine $cfEngine,
        private FertilizerPesticideRecommendationEngine $fpEngine
    ) {}

    /**
     * Preview rekomendasi dengan logika CF yang benar.
     *
     * LOGIKA: Rekomendasi berbasis PENYAKIT, bukan gejala.
     * - Gejala hanya sebagai faktor kelengkapan diagnosis.
     * - CF dihitung berdasarkan relasi penyakit-pupuk dan penyakit-pestisida.
     * - Integrasi preferensi user untuk menyesuaikan rekomendasi (harga/efisiensi).
     */
    public function previewForDisease(int $diseaseId, array $preferences = []): array
    {
        $gejalaIds = collect($preferences['gejala_terpilih'] ?? [])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $presetType = $preferences['preset'] ?? 'seimbang';

        $result = $this->fpEngine->calculateAllRecommendations(
            $diseaseId,
            $gejalaIds,
            topN: null,
            onlyPositive: true
        );

        if ($presetType !== 'seimbang') {
            $result = $this->applyPreferenceAdjustment($result, $presetType);
        }

        return $result;
    }

    /**
     * Hitung rekomendasi dengan integrasi preferensi user yang mempengaruhi CF.
     *
     * LOGIKA: Rekomendasi berbasis PENYAKIT, bukan gejala.
     *
     * @param  int  $diseaseId  ID penyakit (basis utama rekomendasi)
     * @param  string  $presetType  Tipe preset preferensi
     * @param  float  $userConfidence  Tingkat keyakinan user (0.0 - 1.0)
     * @param  array  $symptomWeights  Bobot keyakinan user untuk setiap gejala
     */
    public function calculateWithPreferences(
        int $diseaseId,
        string $presetType = 'seimbang',
        float $userConfidence = 1.0,
        array $symptomWeights = []
    ): array {
        $gejalaIds = array_keys($symptomWeights);

        $fpResult = $this->fpEngine->calculateAllRecommendations(
            $diseaseId,
            $gejalaIds,
            topN: null,
            onlyPositive: true
        );

        if ($presetType !== 'seimbang' || $userConfidence < 1.0) {
            $fpResult = $this->applyPreferenceToFPResult(
                $fpResult,
                $presetType,
                $userConfidence,
                $symptomWeights
            );
        }

        return $fpResult;
    }

    public function saveForUser(int $userId, int $diseaseId, array $preferences = []): Rekomendasi
    {
        return $this->cfService->hitung($userId, $diseaseId, $preferences);
    }

    public function getPreferencePresets(): array
    {
        return $this->cfService->getPreferencePresets();
    }
    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Terapkan preference adjustment ke hasil mentah fpEngine.
     * Digunakan oleh previewForDisease() harga diambil langsung dari item.
     */
    private function applyPreferenceAdjustment(array $result, string $presetType): array
    {
        // Apply adjustment untuk pupuk
        $adjustedPupuk = collect($result['pupuk'])
            ->map(function (array $item) use ($presetType): array {
                $harga = (float) data_get($item, 'harga_per_unit', 0);

                return $this->adjustItem($item, $presetType, $harga, 'pupuk');
            })
            ->sortByDesc('cf_rekomendasi')
            ->values()
            ->all();

        // Apply adjustment untuk pestisida
        $adjustedPestisida = collect($result['pestisida'])
            ->map(function (array $item) use ($presetType): array {
                $harga = (float) data_get($item, 'harga_per_unit', 0);

                return $this->adjustItem($item, $presetType, $harga, 'pestisida');
            })
            ->sortByDesc('cf_rekomendasi')
            ->values()
            ->all();

        // Re-rank setelah sorting dan limit max 2 teratas
        $adjustedPupuk = $this->rerank(array_slice($adjustedPupuk, 0, 2));
        $adjustedPestisida = $this->rerank(array_slice($adjustedPestisida, 0, 2));

        return [
            'pupuk' => $adjustedPupuk,
            'pestisida' => $adjustedPestisida,
            'disease' => $result['disease'],
            'summary' => array_merge($result['summary'], [
                'total_pupuk_direkomendasikan' => count($adjustedPupuk),
                'total_pestisida_direkomendasikan' => count($adjustedPestisida),
                'preference_applied' => true,
                'preset_type' => $presetType,
                'max_recommendations' => 2,
            ]),
            'method_info' => $result['method_info'],
            'preference_info' => [
                'preset' => $presetType,
                'description' => $this->getPreferenceDescription($presetType),
                'applied' => true,
            ],
        ];
    }

    /**
     * Terapkan preference adjustment ke hasil fpEngine yang sudah lengkap.
     * Digunakan oleh calculateWithPreferences()  harga diambil dari meta.
     */
    private function applyPreferenceToFPResult(
        array $fpResult,
        string $presetType,
        float $userConfidence,
        array $symptomWeights
    ): array {
        $adjustItem = function (array $item, string $productType) use (
            $presetType,
            $userConfidence,
            $symptomWeights
        ): array {
            $baseCf = $item['cf_rekomendasi'];

            $adjustedCf = $this->cfEngine->applyPreferenceAdjustment(
                $baseCf,
                $presetType,
                $userConfidence,
                ['harga' => data_get($item, 'meta.harga', 0)]
            );

            $symptomAdjustment = 0.0;
            if (! empty($symptomWeights)) {
                $symptomAdjustment = $this->calculateSymptomWeightAdjustment(
                    $item['id'],
                    $symptomWeights,
                    data_get($item, 'meta.gejala_cocok', [])
                );
                $adjustedCf = $this->cfEngine->normalizeToRange(
                    $adjustedCf + $symptomAdjustment,
                    -1,
                    1
                );
            }

            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
            $item['preference_applied'] = true;
            $item['adjustment_info'] = [
                'preset_boost' => round($adjustedCf - $baseCf, 4),
                'symptom_adjustment' => round($symptomAdjustment, 4),
                'user_confidence' => $userConfidence,
            ];

            return $item;
        };

        $adjustedPupuk = collect($fpResult['pupuk'])
            ->map(fn (array $item) => $adjustItem($item, 'pupuk'))
            ->sortByDesc('cf_rekomendasi')
            ->values()
            ->all();

        $adjustedPestisida = collect($fpResult['pestisida'])
            ->map(fn (array $item) => $adjustItem($item, 'pestisida'))
            ->sortByDesc('cf_rekomendasi')
            ->values()
            ->all();

        return [
            'pupuk' => $this->rerank($adjustedPupuk),
            'pestisida' => $this->rerank($adjustedPestisida),
            'disease' => $fpResult['disease'] ?? null,
            'summary' => $fpResult['summary'],
            'method_info' => $fpResult['method_info'],
            'preference_info' => [
                'preset' => $presetType,
                'user_confidence' => $userConfidence,
                'symptom_weights' => $symptomWeights,
                'description' => $this->getPreferenceDescription($presetType),
                'applied' => true,
            ],
        ];
    }

    /**
     * Hitung adjustment untuk satu item berdasarkan preset dan harga.
     */
    private function adjustItem(array $item, string $presetType, float $harga, string $productType): array
    {
        $baseCf = $item['cf_rekomendasi'];
        $priceCategory = $this->getPriceCategory($harga, $productType);
        $cfCategory = $this->getCfCategory($baseCf);

        [$adjustment, $efficiencyBonus, $reason] = $this->resolveAdjustment(
            $presetType,
            $cfCategory,
            $priceCategory
        );

        $adjustedCf = $this->cfEngine->normalizeToRange($baseCf + $adjustment, -1, 1);

        $item['cf_rekomendasi'] = round($adjustedCf, 4);
        $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
        $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
        $item['preference_applied'] = true;
        $item['is_high_efficiency'] = $efficiencyBonus > 0;
        $item['adjustment_info'] = [
            'preset' => $presetType,
            'base_cf' => round($baseCf, 4),
            'adjusted_cf' => round($adjustedCf, 4),
            'adjustment' => round($adjustment, 4),
            'adjustment_percentage' => round($adjustment * 100, 1).'%',
            'efficiency_bonus' => $efficiencyBonus > 0 ? round($efficiencyBonus, 4) : null,
            'reason' => ! empty($reason) ? implode(', ', $reason) : 'Tidak ada adjustment signifikan',
            'price_category' => $priceCategory,
            'cf_category' => $cfCategory,
        ];

        return $item;
    }

    /**
     * Tentukan nilai adjustment, efficiency bonus, dan alasan berdasarkan preset.
     *
     * @return array{float, float, string[]}
     */
    private function resolveAdjustment(
        string $presetType,
        string $cfCategory,
        string $priceCategory
    ): array {
        $adjustment = 0.0;
        $efficiencyBonus = 0.0;
        $reason = [];

        if ($presetType === 'hemat') {
            [$adjustment, $efficiencyBonus, $reason] = match (true) {
                $cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_murah' => [0.25, 0.10, ['CF sangat tinggi', 'harga sangat murah', 'kombinasi optimal hemat']],
                $cfCategory === 'sangat_tinggi' && $priceCategory === 'murah' => [0.20, 0.08, ['CF sangat tinggi', 'harga murah']],
                $cfCategory === 'tinggi' && $priceCategory === 'sangat_murah' => [0.18, 0.07, ['CF tinggi', 'harga sangat murah']],
                $cfCategory === 'tinggi' && $priceCategory === 'murah' => [0.15, 0.05, ['CF tinggi', 'harga murah']],
                $cfCategory === 'sangat_tinggi' => [0.12, 0.0, ['CF sangat tinggi']],
                $cfCategory === 'tinggi' && $priceCategory === 'menengah' => [0.08, 0.0, ['CF tinggi', 'harga menengah']],
                $cfCategory === 'sedang' && $priceCategory === 'sangat_murah' => [0.10, 0.0, ['harga sangat murah']],
                $cfCategory === 'sedang' && $priceCategory === 'murah' => [0.06, 0.0, ['harga murah']],
                $priceCategory === 'sangat_murah' => [0.05, 0.0, ['harga sangat murah']],
                $priceCategory === 'murah' => [0.03, 0.0, ['harga murah']],
                default => [$priceCategory === 'mahal' ? -0.08 : -0.04, 0.0, ['harga mahal - penalty']],
            };
        } elseif ($presetType === 'efisiensi') {
            [$adjustment, $efficiencyBonus, $reason] = match (true) {
                $cfCategory === 'sangat_tinggi' && $priceCategory === 'mahal' => [0.15, 0.05, ['CF sangat tinggi', 'produk premium', 'efisiensi maksimal']],
                $cfCategory === 'sangat_tinggi' && $priceCategory === 'menengah' => [0.12, 0.03, ['CF sangat tinggi', 'harga menengah']],
                $cfCategory === 'sangat_tinggi' => [0.08, 0.0, ['CF sangat tinggi']],
                $cfCategory === 'tinggi' && $priceCategory === 'mahal' => [0.10, 0.03, ['CF tinggi', 'produk premium']],
                $cfCategory === 'tinggi' && $priceCategory === 'menengah' => [0.07, 0.0, ['CF tinggi', 'harga menengah']],
                $cfCategory === 'tinggi' => [0.05, 0.0, ['CF tinggi']],
                $cfCategory === 'sedang' && $priceCategory === 'mahal' => [0.05, 0.0, ['produk premium']],
                $cfCategory === 'sedang' => [0.03, 0.0, ['CF sedang']],
                default => [0.0, 0.0, []],
            };
        }

        return [$adjustment, $efficiencyBonus, $reason];
    }

    /**
     * Re-assign peringkat (1-based) sesuai urutan array yang sudah disort.
     */
    private function rerank(array $items): array
    {
        foreach ($items as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }
        unset($item);

        return $items;
    }

    /**
     * Hitung adjustment berdasarkan bobot gejala user.
     * Gejala dengan weight tinggi memberikan boost kecil pada alternatif yang cocok.
     */
    private function calculateSymptomWeightAdjustment(
        int $alternativeId,
        array $symptomWeights,
        array $matchedSymptoms
    ): float {
        if (empty($matchedSymptoms) || empty($symptomWeights)) {
            return 0.0;
        }

        $totalAdjustment = 0.0;

        foreach ($matchedSymptoms as $symptom) {
            $symptomId = data_get($symptom, 'id');
            $weight = $symptomWeights[$symptomId] ?? 100;
            $normalizedWeight = min(1.0, $weight / 100);
            $totalAdjustment += 0.02 * $normalizedWeight;
        }

        // Cap di 0.08 (8%) untuk menghindari dominasi berlebihan.
        return min(0.08, $totalAdjustment);
    }

    /**
     * Kategorikan harga berdasarkan tipe produk (pupuk/pestisida).
     */
    private function getPriceCategory(float $harga, string $type): string
    {
        if ($type === 'pupuk') {
            return match (true) {
                $harga <= 5_000 => 'sangat_murah',
                $harga <= 15_000 => 'murah',
                $harga <= 30_000 => 'menengah',
                $harga <= 60_000 => 'mahal',
                default => 'sangat_mahal',
            };
        }

        // Pestisida
        return match (true) {
            $harga <= 50_000 => 'sangat_murah',
            $harga <= 100_000 => 'murah',
            $harga <= 150_000 => 'menengah',
            $harga <= 250_000 => 'mahal',
            default => 'sangat_mahal',
        };
    }

    /**
     * Kategorikan nilai CF.
     */
    private function getCfCategory(float $cf): string
    {
        return match (true) {
            $cf >= 0.8 => 'sangat_tinggi',
            $cf >= 0.6 => 'tinggi',
            $cf >= 0.4 => 'sedang',
            $cf >= 0.2 => 'rendah',
            default => 'sangat_rendah',
        };
    }

    private function getPreferenceDescription(string $preset): string
    {
        return match ($preset) {
            'hemat' => 'Preferensi ini memprioritaskan produk dengan CF TERTINGGI + HARGA TERMURAH. Kombinasi ideal: CF tinggi (>0.7) dengan harga sangat murah untuk efisiensi biaya maksimal.',
            'efisiensi' => 'Preferensi ini memperkuat alternatif dengan keyakinan pakar tertinggi. Produk dengan CF tinggi + harga mahal dianggap sebagai solusi efisiensi tinggi (hasil optimal).',
            'seimbang' => 'Preferensi standar tanpa adjustment signifikan. Rekomendasi murni berdasarkan analisis CF.',
            default => 'Preferensi standar dengan penyesuaian minimal.',
        };
    }
}
