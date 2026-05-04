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
            topN: 3,          // Tampilkan TOP 3 saja untuk fokus pada rekomendasi terbaik
            onlyPositive: false  // Tampilkan semua agar user bisa lihat ranking lengkap
        );
        
        // Apply preference adjustment untuk menyesuaikan dengan kebutuhan user
        if ($presetType !== 'seimbang') {
            $result = $this->applyPreferenceAdjustment($result, $presetType);
        }
        
        return $result;
    }

    /**
     * Apply preference adjustment untuk menyesuaikan rekomendasi dengan kebutuhan user
     * 
     * PRINSIP UTAMA:
     * - 'hemat': CF TERTINGGI + HARGA TERMURAH = Prioritas Utama (boost maksimal)
     *   Contoh: Kompos (CF: 0.8, Harga: Rp600) >> Silika Cair (CF: 0.75, Harga: Rp170.000)
     * 
     * - 'efisiensi': CF TERTINGGI + HARGA MAHAL = Efisiensi Tinggi (boost ekstra)
     *   Contoh: Silika Cair (CF: 0.75, Harga: Rp170.000) >> Kompos (CF: 0.8, Harga: Rp600)
     *   Alasan: Produk mahal + CF tinggi = hasil optimal, worth it untuk investasi
     * 
     * - 'seimbang': Tidak ada adjustment, murni berdasarkan CF
     */
    private function applyPreferenceAdjustment(array $result, string $presetType): array
    {
        // Adjust pupuk
        $adjustedPupuk = collect($result['pupuk'])->map(function ($item) use ($presetType) {
            $baseCf = $item['cf_rekomendasi'];
            $harga = (float) data_get($item, 'harga_per_kg', 0);
            
            $adjustment = 0.0;
            $efficiencyBonus = 0.0;
            
            if ($presetType === 'hemat') {
                // LOGIKA HEMAT BIAYA:
                // PRIORITAS: CF TERTINGGI + HARGA TERMURAH
                // Kombinasi ideal: CF sangat tinggi (>0.8) + harga sangat murah (<Rp5.000)
                
                $priceCategory = $this->getPriceCategory($harga, 'pupuk');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_murah') {
                    // PRIORITAS UTAMA: CF sangat tinggi + harga sangat murah
                    $adjustment = 0.30; // Boost maksimal untuk hemat biaya
                    $efficiencyBonus = 0.15; // Bonus efisiensi biaya tertinggi
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'murah') {
                    // CF sangat tinggi + harga murah
                    $adjustment = 0.25;
                    $efficiencyBonus = 0.12;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'sangat_murah') {
                    // CF tinggi + harga sangat murah
                    $adjustment = 0.22;
                    $efficiencyBonus = 0.10;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'murah') {
                    // CF tinggi + harga murah
                    $adjustment = 0.18;
                    $efficiencyBonus = 0.08;
                } elseif ($cfCategory === 'sangat_tinggi') {
                    // CF sangat tinggi tapi harga menengah/mahal (tetap bagus untuk hemat)
                    $adjustment = 0.15;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.12;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'murah') {
                    $adjustment = 0.08;
                } elseif ($priceCategory === 'sangat_murah') {
                    $adjustment = 0.06;
                } elseif ($priceCategory === 'murah') {
                    $adjustment = 0.04;
                } else {
                    // Harga mahal - penalty berdasarkan tingkat kemahalan
                    $adjustment = $priceCategory === 'mahal' ? -0.10 : -0.05;
                }
            } elseif ($presetType === 'efisiensi') {
                // LOGIKA EFISIENSI TINGGI:
                // PRIORITAS: CF TERTINGGI + HARGA MAHAL = Efisiensi Tinggi
                // Produk mahal + CF tinggi = hasil optimal, worth it untuk investasi
                
                $priceCategory = $this->getPriceCategory($harga, 'pupuk');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'mahal') {
                    // CF sangat tinggi + mahal = efisiensi tinggi (prioritas utama)
                    $adjustment = 0.20;
                    $efficiencyBonus = 0.10;
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_mahal') {
                    // CF sangat tinggi + sangat mahal = efisiensi sangat tinggi
                    $adjustment = 0.22;
                    $efficiencyBonus = 0.12;
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'menengah') {
                    // CF sangat tinggi + menengah = efisiensi cukup tinggi
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                } elseif ($cfCategory === 'sangat_tinggi') {
                    // CF sangat tinggi + murah = bagus tapi bukan prioritas efisiensi
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'mahal') {
                    // CF tinggi + mahal = efisiensi tinggi
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.07;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'sangat_mahal') {
                    // CF tinggi + sangat mahal = efisiensi tinggi
                    $adjustment = 0.17;
                    $efficiencyBonus = 0.08;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'tinggi') {
                    $adjustment = 0.07;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'mahal') {
                    $adjustment = 0.08;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'sangat_mahal') {
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'sedang') {
                    $adjustment = 0.05;
                }
            }
            
            $adjustedCf = $this->cfEngine->normalizeToRange($baseCf + $adjustment, -1, 1);
            
            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
            $item['preference_applied'] = true;
            $item['adjustment_info'] = [
                'preset' => $presetType,
                'adjustment' => round($adjustment, 4),
                'base_cf' => round($baseCf, 4),
                'efficiency_bonus' => $efficiencyBonus > 0 ? round($efficiencyBonus, 4) : null,
                'is_high_efficiency' => $efficiencyBonus > 0,
                'price_category' => $priceCategory ?? null,
                'cf_category' => $cfCategory ?? null,
            ];
            
            return $item;
        })->sortByDesc('cf_rekomendasi')->values();
        
        // Adjust pestisida
        $adjustedPestisida = collect($result['pestisida'])->map(function ($item) use ($presetType) {
            $baseCf = $item['cf_rekomendasi'];
            $harga = (float) data_get($item, 'harga', 0);
            
            $adjustment = 0.0;
            $efficiencyBonus = 0.0;
            
            if ($presetType === 'hemat') {
                // LOGIKA HEMAT BIAYA:
                // PRIORITAS: CF TERTINGGI + HARGA TERMURAH
                // Kombinasi ideal: CF sangat tinggi (>0.8) + harga sangat murah (<Rp50.000)
                
                $priceCategory = $this->getPriceCategory($harga, 'pestisida');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_murah') {
                    // PRIORITAS UTAMA: CF sangat tinggi + harga sangat murah
                    $adjustment = 0.30; // Boost maksimal untuk hemat biaya
                    $efficiencyBonus = 0.15; // Bonus efisiensi biaya tertinggi
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'murah') {
                    // CF sangat tinggi + harga murah
                    $adjustment = 0.25;
                    $efficiencyBonus = 0.12;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'sangat_murah') {
                    // CF tinggi + harga sangat murah
                    $adjustment = 0.22;
                    $efficiencyBonus = 0.10;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'murah') {
                    // CF tinggi + harga murah
                    $adjustment = 0.18;
                    $efficiencyBonus = 0.08;
                } elseif ($cfCategory === 'sangat_tinggi') {
                    // CF sangat tinggi tapi harga menengah/mahal (tetap bagus untuk hemat)
                    $adjustment = 0.15;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.12;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'murah') {
                    $adjustment = 0.08;
                } elseif ($priceCategory === 'sangat_murah') {
                    $adjustment = 0.06;
                } elseif ($priceCategory === 'murah') {
                    $adjustment = 0.04;
                } else {
                    // Harga mahal - penalty berdasarkan tingkat kemahalan
                    $adjustment = $priceCategory === 'mahal' ? -0.10 : -0.05;
                }
            } elseif ($presetType === 'efisiensi') {
                // LOGIKA EFISIENSI TINGGI:
                // PRIORITAS: CF TERTINGGI + HARGA MAHAL = Efisiensi Tinggi
                // Produk mahal + CF tinggi = hasil optimal, worth it untuk investasi
                
                $priceCategory = $this->getPriceCategory($harga, 'pestisida');
                $cfCategory = $this->getCfCategory($baseCf);
                
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'mahal') {
                    // CF sangat tinggi + mahal = efisiensi tinggi (prioritas utama)
                    $adjustment = 0.20;
                    $efficiencyBonus = 0.10;
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_mahal') {
                    // CF sangat tinggi + sangat mahal = efisiensi sangat tinggi
                    $adjustment = 0.22;
                    $efficiencyBonus = 0.12;
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'menengah') {
                    // CF sangat tinggi + menengah = efisiensi cukup tinggi
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                } elseif ($cfCategory === 'sangat_tinggi') {
                    // CF sangat tinggi + murah = bagus tapi bukan prioritas efisiensi
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'mahal') {
                    // CF tinggi + mahal = efisiensi tinggi
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.07;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'sangat_mahal') {
                    // CF tinggi + sangat mahal = efisiensi tinggi
                    $adjustment = 0.17;
                    $efficiencyBonus = 0.08;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'tinggi') {
                    $adjustment = 0.07;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'mahal') {
                    $adjustment = 0.08;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'sangat_mahal') {
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'sedang') {
                    $adjustment = 0.05;
                }
            }
            
            $adjustedCf = $this->cfEngine->normalizeToRange($baseCf + $adjustment, -1, 1);
            
            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
            $item['preference_applied'] = true;
            $item['adjustment_info'] = [
                'preset' => $presetType,
                'adjustment' => round($adjustment, 4),
                'base_cf' => round($baseCf, 4),
                'efficiency_bonus' => $efficiencyBonus > 0 ? round($efficiencyBonus, 4) : null,
                'is_high_efficiency' => $efficiencyBonus > 0,
                'price_category' => $priceCategory ?? null,
                'cf_category' => $cfCategory ?? null,
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
        
        // Limit to top 3 untuk masing-masing
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
            'hemat' => 'Preferensi ini memprioritaskan produk dengan CF TERTINGGI + HARGA TERMURAH. Kombinasi ideal: CF sangat tinggi (>0.8) dengan harga sangat murah untuk efisiensi biaya maksimal. Contoh: Kompos (CF tinggi, Rp600/kg) lebih diprioritaskan daripada Silika Cair (CF tinggi, Rp170.000).',
            'efisiensi' => 'Preferensi ini memperkuat alternatif dengan keyakinan pakar tertinggi DAN harga premium. Produk dengan CF tinggi + harga mahal dianggap sebagai solusi efisiensi tinggi (hasil optimal, worth it untuk investasi). Contoh: Silika Cair (CF tinggi, Rp170.000) lebih diprioritaskan daripada Kompos (CF tinggi, Rp600).',
            'seimbang' => 'Preferensi standar tanpa adjustment signifikan. Rekomendasi murni berdasarkan analisis Certainty Factor dari pengetahuan pakar.',
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
            topN: 3,          // Tampilkan TOP 3 saja untuk fokus pada rekomendasi terbaik
            onlyPositive: false  // Tampilkan semua agar user bisa lihat ranking lengkap
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
