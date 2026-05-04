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
        // Parameter presetType sekarang diintegrasikan langsung di engine
        $result = $this->fpEngine->calculateAllRecommendations(
            $diseaseId,       // Disease ID sebagai basis rekomendasi
            $gejalaIds,       // Symptom IDs untuk kelengkapan diagnosis
            topN: 3,          // Limit ke 3 teratas
            onlyPositive: true,
            presetType: $presetType  // Preferensi user untuk prioritisasi
        );
        
        return $result;
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
        
        // Gunakan FertilizerPesticideRecommendationEngine dengan presetType terintegrasi
        // Engine sudah menangani preference adjustment secara internal
        $fpResult = $this->fpEngine->calculateAllRecommendations(
            $diseaseId,       // Disease ID sebagai basis rekomendasi
            $gejalaIds,       // Symptom IDs untuk kelengkapan diagnosis
            topN: 3,          // Limit ke 3 teratas
            onlyPositive: true,
            presetType: $presetType  // Preferensi user untuk prioritisasi
        );
        
        // Apply symptom weight adjustment jika ada (opsional, untuk personalisasi tambahan)
        if (!empty($symptomWeights)) {
            $fpResult = $this->applySymptomWeightAdjustment($fpResult, $symptomWeights);
        }
        
        return $fpResult;
    }

    /**
     * Apply symptom weight adjustment ke hasil dari FertilizerPesticideRecommendationEngine
     * Adjustment ini kecil (max 5%) untuk personalisasi berdasarkan keyakinan user terhadap gejala
     * TIDAK mengubah logika preferensi harga/efisiensi yang sudah dilakukan oleh fpEngine
     */
    private function applySymptomWeightAdjustment(array $fpResult, array $symptomWeights): array
    {
        if (empty($symptomWeights)) {
            return $fpResult;
        }
        
        // Apply adjustment untuk pupuk
        $adjustedPupuk = collect($fpResult['pupuk'])->map(function ($item) use ($symptomWeights) {
            $baseCf = $item['cf_rekomendasi'];
            
            // Hitung adjustment dari symptom weights
            $symptomAdjustment = $this->calculateSymptomWeightAdjustment(
                $item['id'],
                $symptomWeights,
                data_get($item, 'matched_symptoms', [])
            );
            
            $adjustedCf = $this->cfEngine->normalizeToRange($baseCf + $symptomAdjustment, -1, 1);
            
            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
            
            // Update adjustment_info jika sudah ada dari preference adjustment
            if (isset($item['adjustment_info'])) {
                $item['adjustment_info']['symptom_adjustment'] = round($symptomAdjustment, 4);
            } else {
                $item['adjustment_info'] = [
                    'symptom_adjustment' => round($symptomAdjustment, 4),
                    'note' => 'Adjustment berdasarkan keyakinan user terhadap gejala',
                ];
            }
            
            return $item;
        })->sortByDesc('cf_rekomendasi')->values();
        
        // Apply adjustment untuk pestisida
        $adjustedPestisida = collect($fpResult['pestisida'])->map(function ($item) use ($symptomWeights) {
            $baseCf = $item['cf_rekomendasi'];
            
            // Hitung adjustment dari symptom weights
            $symptomAdjustment = $this->calculateSymptomWeightAdjustment(
                $item['id'],
                $symptomWeights,
                data_get($item, 'matched_symptoms', [])
            );
            
            $adjustedCf = $this->cfEngine->normalizeToRange($baseCf + $symptomAdjustment, -1, 1);
            
            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->fpEngine->getRecommendationLabel($adjustedCf);
            
            // Update adjustment_info jika sudah ada dari preference adjustment
            if (isset($item['adjustment_info'])) {
                $item['adjustment_info']['symptom_adjustment'] = round($symptomAdjustment, 4);
            } else {
                $item['adjustment_info'] = [
                    'symptom_adjustment' => round($symptomAdjustment, 4),
                    'note' => 'Adjustment berdasarkan keyakinan user terhadap gejala',
                ];
            }
            
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
            'disease' => $fpResult['disease'],
            'summary' => array_merge($fpResult['summary'], [
                'symptom_weight_applied' => true,
                'total_symptom_weights' => count($symptomWeights),
            ]),
            'method_info' => $fpResult['method_info'],
        ];
    }

    /**
     * Hitung adjustment berdasarkan bobot gejala user
     * Gejala dengan weight tinggi akan memberikan boost kecil pada alternatif yang mendukung gejala tersebut
     * Max adjustment 5% untuk menghindari dominasi berlebihan
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
            $symptomId = is_array($symptom) 
                ? (data_get($symptom, 'id') ?? data_get($symptom, 'gejala_id'))
                : $symptom;
            
            if (!$symptomId) {
                continue;
            }
            
            $weight = $symptomWeights[$symptomId] ?? 100;
            
            // Normalisasi weight ke 0-1
            $normalizedWeight = min(1, $weight / 100);
            
            // Adjustment per gejala: max 0.015 per gejala dengan weight penuh
            $symptomContribution = 0.015 * $normalizedWeight;
            $totalAdjustment += $symptomContribution;
        }

        // Cap total adjustment di 0.05 (5%) untuk menghindari dominasi berlebihan
        return min(0.05, $totalAdjustment);
    }
}
