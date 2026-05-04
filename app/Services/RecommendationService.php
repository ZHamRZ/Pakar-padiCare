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
     * Preview rekomendasi dengan logika CF yang benar
     * 
     * LOGIKA BARU: Rekomendasi berbasis PENYAKIT, bukan gejala
     * - Gejala hanya sebagai faktor kelengkapan diagnosis
     * - CF dihitung berdasarkan relasi penyakit-pupuk dan penyakit-pestisida
     * - Integrasi preferensi user untuk menyesuaikan rekomendasi (harga/efisiensi)
     */
    public function previewForDisease(int $diseaseId, array $preferences = []): array
    {
        // Ekstrak gejala terpilih dari preferensi (hanya untuk kelengkapan diagnosis)
        $gejalaIds = collect($preferences['gejala_terpilih'] ?? [])
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
        
        // Ekstrak preferensi tipe
        $presetType = $preferences['preset'] ?? 'seimbang';
        
        // Gunakan FertilizerPesticideRecommendationEngine dengan parameter yang benar
        // Parameter 1: diseaseId (basis rekomendasi)
        // Parameter 2: symptomIds (untuk kelengkapan diagnosis)
        $result = $this->fpEngine->calculateAllRecommendations(
            $diseaseId,       // Disease ID sebagai basis rekomendasi
            $gejalaIds,       // Symptom IDs untuk kelengkapan diagnosis
            topN: null,
            onlyPositive: true
        );
        
        // Apply preference adjustment untuk menyesuaikan dengan kebutuhan user
        if ($presetType !== 'seimbang') {
            $result = $this->applyPreferenceAdjustment($result, $presetType);
        }
        
        return $result;
    }

    /**
     * Apply preference adjustment untuk menyesuaikan rekomendasi dengan kebutuhan user
     * - 'hemat': Prioritaskan harga murah dengan boost CF (kombinasi CF tinggi + harga murah)
     * - 'efisiensi': Prioritaskan produk premium dengan CF tinggi (produk mahal + CF tinggi = efisiensi tinggi)
     * - 'seimbang': Tidak ada adjustment signifikan
     * 
     * CRITICAL: Adjustment info ditambahkan untuk transparansi ke user
     */
    private function applyPreferenceAdjustment(array $result, string $presetType): array
    {
        // Adjust pupuk
        $adjustedPupuk = collect($result['pupuk'])->map(function ($item) use ($presetType) {
            $baseCf = $item['cf_rekomendasi'];
            $harga = (float) data_get($item, 'harga_per_kg', 0);
            
            $adjustment = 0.0;
            $efficiencyBonus = 0.0;
            $reason = [];
            
            if ($presetType === 'hemat') {
                // LOGIKA HEMAT BIAYA:
                // Prioritaskan produk dengan CF TERTINGGI + HARGA TERMURAH
                // Kombinasi ideal: CF tinggi (>0.7) + harga sangat murah (<Rp10.000)
                
                $priceCategory = $this->getPriceCategory($harga, 'pupuk');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.25;
                    $efficiencyBonus = 0.10;
                    $reason = ['CF sangat tinggi', 'harga sangat murah', 'kombinasi optimal hemat'];
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'murah') {
                    $adjustment = 0.20;
                    $efficiencyBonus = 0.08;
                    $reason = ['CF sangat tinggi', 'harga murah'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.18;
                    $efficiencyBonus = 0.07;
                    $reason = ['CF tinggi', 'harga sangat murah'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'murah') {
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                    $reason = ['CF tinggi', 'harga murah'];
                } elseif ($cfCategory === 'sangat_tinggi') {
                    $adjustment = 0.12;
                    $reason = ['CF sangat tinggi'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.08;
                    $reason = ['CF tinggi', 'harga menengah'];
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.10;
                    $reason = ['harga sangat murah'];
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'murah') {
                    $adjustment = 0.06;
                    $reason = ['harga murah'];
                } elseif ($priceCategory === 'sangat_murah') {
                    $adjustment = 0.05;
                    $reason = ['harga sangat murah'];
                } elseif ($priceCategory === 'murah') {
                    $adjustment = 0.03;
                    $reason = ['harga murah'];
                } else {
                    $adjustment = $priceCategory === 'mahal' ? -0.08 : -0.04;
                    $reason = ['harga mahal - penalty'];
                }
            } elseif ($presetType === 'efisiensi') {
                // LOGIKA EFISIENSI TINGGI:
                // Produk dengan CF tinggi DAN harga mahal = efisiensi tinggi (boost ekstra)
                
                $priceCategory = $this->getPriceCategory($harga, 'pupuk');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'mahal') {
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                    $reason = ['CF sangat tinggi', 'produk premium', 'efisiensi maksimal'];
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.12;
                    $efficiencyBonus = 0.03;
                    $reason = ['CF sangat tinggi', 'harga menengah'];
                } elseif ($cfCategory === 'sangat_tinggi') {
                    $adjustment = 0.08;
                    $reason = ['CF sangat tinggi'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'mahal') {
                    $adjustment = 0.10;
                    $efficiencyBonus = 0.03;
                    $reason = ['CF tinggi', 'produk premium'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.07;
                    $reason = ['CF tinggi', 'harga menengah'];
                } elseif ($cfCategory === 'tinggi') {
                    $adjustment = 0.05;
                    $reason = ['CF tinggi'];
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'mahal') {
                    $adjustment = 0.05;
                    $reason = ['produk premium'];
                } elseif ($cfCategory === 'sedang') {
                    $adjustment = 0.03;
                    $reason = ['CF sedang'];
                }
            }
            
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
                'adjustment_percentage' => round($adjustment * 100, 1) . '%',
                'efficiency_bonus' => $efficiencyBonus > 0 ? round($efficiencyBonus, 4) : null,
                'reason' => !empty($reason) ? implode(', ', $reason) : 'Tidak ada adjustment signifikan',
                'price_category' => $this->getPriceCategory($harga, 'pupuk'),
                'cf_category' => $this->getCfCategory($baseCf),
            ];
            
            return $item;
        })->sortByDesc('cf_rekomendasi')->values();
        
        // Adjust pestisida
        $adjustedPestisida = collect($result['pestisida'])->map(function ($item) use ($presetType) {
            $baseCf = $item['cf_rekomendasi'];
            $harga = (float) data_get($item, 'harga', 0);
            
            $adjustment = 0.0;
            $efficiencyBonus = 0.0;
            $reason = [];
            
            if ($presetType === 'hemat') {
                $priceCategory = $this->getPriceCategory($harga, 'pestisida');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.25;
                    $efficiencyBonus = 0.10;
                    $reason = ['CF sangat tinggi', 'harga sangat murah', 'kombinasi optimal hemat'];
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'murah') {
                    $adjustment = 0.20;
                    $efficiencyBonus = 0.08;
                    $reason = ['CF sangat tinggi', 'harga murah'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.18;
                    $efficiencyBonus = 0.07;
                    $reason = ['CF tinggi', 'harga sangat murah'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'murah') {
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                    $reason = ['CF tinggi', 'harga murah'];
                } elseif ($cfCategory === 'sangat_tinggi') {
                    $adjustment = 0.12;
                    $reason = ['CF sangat tinggi'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.08;
                    $reason = ['CF tinggi', 'harga menengah'];
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.10;
                    $reason = ['harga sangat murah'];
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'murah') {
                    $adjustment = 0.06;
                    $reason = ['harga murah'];
                } elseif ($priceCategory === 'sangat_murah') {
                    $adjustment = 0.05;
                    $reason = ['harga sangat murah'];
                } elseif ($priceCategory === 'murah') {
                    $adjustment = 0.03;
                    $reason = ['harga murah'];
                } else {
                    $adjustment = $priceCategory === 'mahal' ? -0.08 : -0.04;
                    $reason = ['harga mahal - penalty'];
                }
            } elseif ($presetType === 'efisiensi') {
                $priceCategory = $this->getPriceCategory($harga, 'pestisida');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'mahal') {
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                    $reason = ['CF sangat tinggi', 'produk premium', 'efisiensi maksimal'];
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.12;
                    $efficiencyBonus = 0.03;
                    $reason = ['CF sangat tinggi', 'harga menengah'];
                } elseif ($cfCategory === 'sangat_tinggi') {
                    $adjustment = 0.08;
                    $reason = ['CF sangat tinggi'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'mahal') {
                    $adjustment = 0.10;
                    $efficiencyBonus = 0.03;
                    $reason = ['CF tinggi', 'produk premium'];
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.07;
                    $reason = ['CF tinggi', 'harga menengah'];
                } elseif ($cfCategory === 'tinggi') {
                    $adjustment = 0.05;
                    $reason = ['CF tinggi'];
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'mahal') {
                    $adjustment = 0.05;
                    $reason = ['produk premium'];
                } elseif ($cfCategory === 'sedang') {
                    $adjustment = 0.03;
                    $reason = ['CF sedang'];
                }
            }
            
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
                'adjustment_percentage' => round($adjustment * 100, 1) . '%',
                'efficiency_bonus' => $efficiencyBonus > 0 ? round($efficiencyBonus, 4) : null,
                'reason' => !empty($reason) ? implode(', ', $reason) : 'Tidak ada adjustment signifikan',
                'price_category' => $this->getPriceCategory($harga, 'pestisida'),
                'cf_category' => $this->getCfCategory($baseCf),
            ];
            
            return $item;
        })->sortByDesc('cf_rekomendasi')->values();
        
        // Re-calculate peringkat setelah adjustment
        foreach ($adjustedPupuk as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }
        foreach ($adjustedPestisida as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }
        
        // Limit to top 3 untuk masing-masing (CRITICAL: Maksimal 3 rekomendasi)
        $adjustedPupuk = array_slice($adjustedPupuk, 0, 3);
        $adjustedPestisida = array_slice($adjustedPestisida, 0, 3);
        
        return [
            'pupuk' => $adjustedPupuk,
            'pestisida' => $adjustedPestisida,
            'disease' => $result['disease'],
            'summary' => array_merge($result['summary'], [
                'total_pupuk_direkomendasikan' => count($adjustedPupuk),
                'total_pestisida_direkomendasikan' => count($adjustedPestisida),
                'preference_applied' => true,
                'preset_type' => $presetType,
                'max_recommendations' => 3,
            ]),
            'method_info' => $result['method_info'],
            'preference_info' => [
                'preset' => $presetType,
                'description' => $this->getPreferenceDescription($presetType),
                'applied' => true,
            ],
        ];
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

    /**
     * Kategorikan harga berdasarkan tipe produk (pupuk/pestisida)
     */
    private function getPriceCategory(float $harga, string $type): string
    {
        if ($type === 'pupuk') {
            if ($harga <= 5000) return 'sangat_murah';      // ≤Rp5.000
            if ($harga <= 15000) return 'murah';            // ≤Rp15.000
            if ($harga <= 30000) return 'menengah';         // ≤Rp30.000
            if ($harga <= 60000) return 'mahal';            // ≤Rp60.000
            return 'sangat_mahal';                          // >Rp60.000
        } else {
            // Pestisida
            if ($harga <= 50000) return 'sangat_murah';     // ≤Rp50.000
            if ($harga <= 100000) return 'murah';           // ≤Rp100.000
            if ($harga <= 150000) return 'menengah';        // ≤Rp150.000
            if ($harga <= 250000) return 'mahal';           // ≤Rp250.000
            return 'sangat_mahal';                          // >Rp250.000
        }
    }

    /**
     * Kategorikan nilai CF
     */
    private function getCfCategory(float $cf): string
    {
        if ($cf >= 0.8) return 'sangat_tinggi';   // ≥80%
        if ($cf >= 0.6) return 'tinggi';          // ≥60%
        if ($cf >= 0.4) return 'sedang';          // ≥40%
        if ($cf >= 0.2) return 'rendah';          // ≥20%
        return 'sangat_rendah';                   // <20%
    }

    public function saveForUser(int $userId, int $diseaseId, array $preferences = []): Rekomendasi
    {
        return $this->cfService->hitung($userId, $diseaseId, $preferences);
    }

    public function getPreferencePresets(): array
    {
        return $this->cfService->getPreferencePresets();
    }

    /**
     * Hitung rekomendasi dengan integrasi preferensi user yang mempengaruhi CF
     * Menggunakan FertilizerPesticideRecommendationEngine untuk logika CF yang benar
     * 
     * LOGIKA BARU: Rekomendasi berbasis PENYAKIT, bukan gejala
     * - Gejala hanya sebagai faktor kelengkapan diagnosis
     * - CF dihitung berdasarkan relasi penyakit-pupuk dan penyakit-pestisida
     * 
     * @param int $diseaseId ID penyakit (basis utama rekomendasi)
     * @param string $presetType Tipe preset preferensi
     * @param array $criteriaWeights Bobot kriteria custom dari user
     * @param array $symptomWeights Bobot keyakinan user untuk setiap gejala
     */
    public function calculateWithPreferences(
        int $diseaseId,
        string $presetType = 'seimbang',
        array $criteriaWeights = [],
        array $symptomWeights = []
    ): array {
        // Ekstrak gejala terpilih dari symptom weights (hanya untuk kelengkapan diagnosis)
        $gejalaIds = array_keys($symptomWeights);
        
        // Gunakan FertilizerPesticideRecommendationEngine dengan parameter yang benar
        // Parameter 1: diseaseId (basis rekomendasi)
        // Parameter 2: symptomIds (untuk kelengkapan diagnosis)
        $fpResult = $this->fpEngine->calculateAllRecommendations(
            $diseaseId,       // Disease ID sebagai basis rekomendasi
            $gejalaIds,       // Symptom IDs untuk kelengkapan diagnosis
            topN: null,
            onlyPositive: true
        );
        
        // Apply preference adjustment jika diperlukan
        // Catatan: adjustment ini bersifat opsional dan tidak mengubah logika dasar CF
        if ($presetType !== 'seimbang' || !empty($criteriaWeights)) {
            $fpResult = $this->applyPreferenceToFPResult($fpResult, $presetType, $criteriaWeights, $symptomWeights);
        }
        
        return $fpResult;
    }

    /**
     * Apply preference adjustment ke hasil dari FertilizerPesticideRecommendationEngine
     * Tanpa mengubah logika dasar CF (transformasi sudah dilakukan oleh fpEngine)
     */
    private function applyPreferenceToFPResult(
        array $fpResult,
        string $presetType,
        array $criteriaWeights,
        array $symptomWeights
    ): array {
        // Untuk saat ini, kembalikan hasil fpEngine tanpa modifikasi
        // karena transformasi CF sudah benar dilakukan oleh fpEngine
        // Preference adjustment dapat ditambahkan di sini jika diperlukan
        // dengan tetap menjaga integritas transformasi CF
        
        if (empty($criteriaWeights) && $presetType === 'seimbang') {
            return $fpResult;
        }
        
        // Apply minor adjustment untuk pupuk dan pestisida
        $adjustedPupuk = collect($fpResult['pupuk'])->map(function ($item) use ($presetType, $criteriaWeights, $symptomWeights) {
            $baseCf = $item['cf_rekomendasi'];
            
            $adjustedCf = $this->cfEngine->applyPreferenceAdjustment(
                $baseCf,
                $presetType,
                $criteriaWeights,
                ['harga' => data_get($item, 'meta.harga', 0)]
            );
            
            // Tambahkan adjustment dari symptom weights jika ada
            if (!empty($symptomWeights)) {
                $symptomAdjustment = $this->calculateSymptomWeightAdjustment(
                    $item['id'],
                    $symptomWeights,
                    data_get($item, 'meta.gejala_cocok', [])
                );
                $adjustedCf = $this->cfEngine->normalizeToRange($adjustedCf + $symptomAdjustment, -1, 1);
            }

            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
            $item['preference_applied'] = true;
            $item['adjustment_info'] = [
                'preset_boost' => round($adjustedCf - $baseCf, 4),
                'symptom_adjustment' => !empty($symptomWeights) ? round($symptomAdjustment, 4) : 0,
            ];
            
            return $item;
        })->sortByDesc('cf_rekomendasi')->values();
        
        $adjustedPestisida = collect($fpResult['pestisida'])->map(function ($item) use ($presetType, $criteriaWeights, $symptomWeights) {
            $baseCf = $item['cf_rekomendasi'];
            
            $adjustedCf = $this->cfEngine->applyPreferenceAdjustment(
                $baseCf,
                $presetType,
                $criteriaWeights,
                ['harga' => data_get($item, 'meta.harga', 0)]
            );
            
            // Tambahkan adjustment dari symptom weights jika ada
            if (!empty($symptomWeights)) {
                $symptomAdjustment = $this->calculateSymptomWeightAdjustment(
                    $item['id'],
                    $symptomWeights,
                    data_get($item, 'meta.gejala_cocok', [])
                );
                $adjustedCf = $this->cfEngine->normalizeToRange($adjustedCf + $symptomAdjustment, -1, 1);
            }

            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
            $item['preference_applied'] = true;
            $item['adjustment_info'] = [
                'preset_boost' => round($adjustedCf - $baseCf, 4),
                'symptom_adjustment' => !empty($symptomWeights) ? round($symptomAdjustment, 4) : 0,
            ];
            
            return $item;
        })->sortByDesc('cf_rekomendasi')->values();
        
        // Re-calculate peringkat setelah adjustment
        foreach ($adjustedPupuk as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }
        foreach ($adjustedPestisida as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }
        
        return [
            'pupuk' => $adjustedPupuk->all(),
            'pestisida' => $adjustedPestisida->all(),
            'summary' => $fpResult['summary'],
            'method_info' => $fpResult['method_info'],
            'preference_info' => [
                'preset' => $presetType,
                'criteria_weights' => $criteriaWeights,
                'symptom_weights' => $symptomWeights,
                'description' => $this->getPreferenceDescription($presetType),
                'applied' => true,
            ],
        ];
    }

    /**
     * Hitung adjustment berdasarkan bobot gejala user
     * Gejala dengan weight tinggi akan memberikan boost kecil pada alternatif yang mendukung gejala tersebut
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
        $maxPossibleAdjustment = 0.0;

        foreach ($matchedSymptoms as $symptom) {
            $symptomId = data_get($symptom, 'id');
            $weight = $symptomWeights[$symptomId] ?? 100;
            
            // Normalisasi weight ke 0-1
            $normalizedWeight = min(1, $weight / 100);
            
            // Adjustment per gejala: max 0.02 per gejala dengan weight penuh
            $symptomContribution = 0.02 * $normalizedWeight;
            $totalAdjustment += $symptomContribution;
            $maxPossibleAdjustment += 0.02;
        }

        // Cap total adjustment di 0.08 (8%) untuk menghindari dominasi berlebihan
        return min(0.08, $totalAdjustment);
    }
}
