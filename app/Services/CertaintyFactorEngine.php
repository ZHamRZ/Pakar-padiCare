<?php

namespace App\Services;

use App\Models\Gejala;
use App\Models\Pestisida;
use App\Models\Pupuk;
use Illuminate\Support\Collection;

/**
 * Certainty Factor Engine - Implementasi profesional metode CF
 * 
 * Rumus dasar:
 * - CF = MB - MD
 * - CFcombine (CF1 & CF2 same sign) = CF1 + CF2 * (1 - CF1)
 * - CFcombine (CF1 & CF2 opposite sign) = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))
 * 
 * Range nilai CF: -1 sampai 1
 * - CF = 1   : Kepastian mutlak benar
 * - CF = 0   : Ketidakpastian penuh
 * - CF = -1  : Kepastian mutlak salah
 */
class CertaintyFactorEngine
{
    /**
     * Hitung CF dari MB dan MD
     */
    public function calculateCf(float $mb, float $md): float
    {
        // Normalisasi MB dan MD ke range [0, 1]
        $mb = $this->normalize($mb);
        $md = $this->normalize($md);
        
        // CF = MB - MD (range: -1 sampai 1)
        return round($mb - $md, 6);
    }

    /**
     * Kombinasi dua nilai CF (rumus kombinasi sequential)
     */
    public function combineCf(float $cf1, float $cf2): float
    {
        $cf1 = $this->normalizeToRange($cf1, -1, 1);
        $cf2 = $this->normalizeToRange($cf2, -1, 1);

        // Jika kedua CF positif atau nol
        if ($cf1 >= 0 && $cf2 >= 0) {
            $result = $cf1 + $cf2 * (1 - $cf1);
        }
        // Jika kedua CF negatif atau nol
        elseif ($cf1 <= 0 && $cf2 <= 0) {
            $result = $cf1 + $cf2 * (1 + $cf1);
        }
        // Jika CF berbeda tanda
        else {
            $minAbs = min(abs($cf1), abs($cf2));
            $denominator = 1 - $minAbs;
            
            // Hindari division by zero
            if ($denominator == 0) {
                $result = 0;
            } else {
                $result = ($cf1 + $cf2) / $denominator;
            }
        }

        return round($this->normalizeToRange($result, -1, 1), 6);
    }

    /**
     * Kombinasi multiple CF values (sequential combination)
     */
    public function combineMultipleCf(array $cfValues): float
    {
        if (empty($cfValues)) {
            return 0.0;
        }

        $result = array_shift($cfValues);

        foreach ($cfValues as $cf) {
            $result = $this->combineCf($result, $cf);
        }

        return $result;
    }

    /**
     * Hitung CF gabungan dari multiple gejala dengan bobot user
     */
    public function calculateWithUserWeight(
        float $baseCf,
        float $userWeight = 1.0,
        float $preferenceAdjustment = 0.0
    ): float {
        // User weight mempengaruhi keyakinan dasar
        // Weight 0-1, dimana 1 = full confidence pada gejala
        $weightedCf = $baseCf * $userWeight;
        
        // Tambahkan adjustment dari preferensi
        $finalCf = $weightedCf + $preferenceAdjustment;
        
        return $this->normalizeToRange($finalCf, -1, 1);
    }

    /**
     * Apply preference adjustment to CF
     * Preferensi user dapat meningkatkan atau menurunkan CF akhir
     */
    public function applyPreferenceAdjustment(
        float $baseCf,
        string $presetType,
        array $criteriaPreferences = [],
        array $alternativeData = []
    ): float {
        $adjustment = 0.0;

        // Adjustments berdasarkan preset
        $adjustment += $this->getPresetAdjustment($presetType, $baseCf, $alternativeData);

        // Adjustments berdasarkan kriteria custom
        foreach ($criteriaPreferences as $criteriaId => $weight) {
            $intensity = $this->normalize($weight / 100);
            $adjustment += $this->getCriteriaAdjustment($criteriaId, $intensity, $alternativeData);
        }

        $finalCf = $baseCf + $adjustment;
        
        return $this->normalizeToRange($finalCf, -1, 1);
    }

    /**
     * Dapatkan adjustment dari preset preferensi
     */
    private function getPresetAdjustment(
        string $presetType,
        float $baseCf,
        array $alternativeData = []
    ): float {
        return match ($presetType) {
            'hemat' => $this->calculateHematAdjustment($alternativeData),
            'efisiensi' => $this->calculateEfisiensiAdjustment($baseCf),
            'seimbang' => 0.02, // Small boost untuk stabilitas
            'custom' => 0.0,    // Custom dihitung terpisah
            default => 0.0,
        };
    }

    /**
     * Adjustment untuk preset hemat biaya
     * Alternatif murah mendapat boost, mahal mendapat penalty
     */
    private function calculateHematAdjustment(array $alternativeData): float
    {
        $harga = (float) ($alternativeData['harga'] ?? 0);
        
        if ($harga <= 0) {
            return 0.02;
        }

        if ($harga <= 50000) {
            return 0.10; // Boost signifikan untuk harga sangat murah
        } elseif ($harga <= 100000) {
            return 0.05; // Boost moderat untuk harga menengah
        } else {
            return -0.03; // Penalty kecil untuk harga tinggi
        }
    }

    /**
     * Adjustment untuk preset efisiensi
     * Alternatif dengan CF dasar tinggi mendapat boost ekstra
     */
    private function calculateEfisiensiAdjustment(float $baseCf): float
    {
        if ($baseCf >= 0.8) {
            return 0.12; // High confidence alternatives get strong boost
        } elseif ($baseCf >= 0.6) {
            return 0.07; // Medium-high confidence get moderate boost
        } elseif ($baseCf >= 0.4) {
            return 0.03; // Medium confidence get small boost
        }
        
        return 0.0; // Low confidence tidak mendapat boost
    }

    /**
     * Adjustment berdasarkan kriteria individual
     */
    private function getCriteriaAdjustment(
        int $criteriaId,
        float $intensity,
        array $alternativeData = []
    ): float {
        // Default adjustment kecil
        $baseAdjustment = 0.05 * $intensity;
        
        // Kriteria spesifik dapat memiliki logic khusus
        // ID kriteria dapat di-mapping ke jenis adjustment tertentu
        return $baseAdjustment;
    }

    /**
     * Normalisasi nilai ke range [0, 1]
     */
    public function normalize(float $value): float
    {
        return max(0, min(1, $value));
    }

    /**
     * Normalisasi nilai ke range custom [min, max]
     */
    public function normalizeToRange(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));

    }
    

    /**
     * Konversi CF ke persentase untuk display (0-100%)
     */
    public function toPercentage(float $cf): float
    {
        // Mapping dari [-1, 1] ke [0, 100]
        // CF = -1 → 0%
        // CF = 0  → 50%
        // CF = 1  → 100%
        return round((($cf + 1) / 2) * 100, 2);
    }

    /**
     * Konversi persentase ke CF
     */
    public function fromPercentage(float $percentage): float
    {
        // Mapping dari [0, 100] ke [-1, 1]
        $normalized = $this->normalize($percentage / 100);
        return round(($normalized * 2) - 1, 6);
    }

    /**
     * Interpretasi nilai CF untuk user
     */
    public function interpret(float $cf): array
    {
        $cf = $this->normalizeToRange($cf, -1, 1);
        
        if ($cf >= 0.8) {
            return ['label' => 'Sangat Tinggi', 'color' => 'success', 'icon' => '✓✓'];
        } elseif ($cf >= 0.6) {
            return ['label' => 'Tinggi', 'color' => 'success', 'icon' => '✓'];
        } elseif ($cf >= 0.4) {
            return ['label' => 'Sedang', 'color' => 'warning', 'icon' => '~'];
        } elseif ($cf >= 0.1) {
            return ['label' => 'Rendah', 'color' => 'warning', 'icon' => '…'];
        } elseif ($cf >= 0) {
            return ['label' => 'Sangat Rendah', 'color' => 'danger', 'icon' => '?'];
        } else {
            return ['label' => 'Tidak Direkomendasikan', 'color' => 'danger', 'icon' => '✗'];
        }
    }

    /**
     * Hitung CF untuk diagnosis penyakit berdasarkan gejala
     * 
     * @param Collection $matchedSymptoms Gejala yang cocok (dengan pivot mb/md)
     * @param Collection $allDiseaseSymptoms Semua gejala penyakit ini
     * @param array $userSymptomWeights Bobot keyakinan user per gejala (0-100 atau 0-1)
     * @return float CF akhir dalam range -1 sampai 1
     */
    public function calculateDiagnosisCf(
        Collection $matchedSymptoms,
        Collection $allDiseaseSymptoms,
        array $userSymptomWeights = []
    ): float {
        if ($matchedSymptoms->isEmpty()) {
            return -1.0; // Tidak ada gejala yang cocok
        }

        $cfValues = [];

        foreach ($matchedSymptoms as $symptom) {
            $mb = (float) ($symptom->pivot->mb ?? 0.7);
            $md = (float) ($symptom->pivot->md ?? 0.1);
            
            // Validasi konsistensi MB dan MD
            if ($mb + $md > 1.0) {
                // Normalisasi jika MB + MD > 1
                $total = $mb + $md;
                $mb = $mb / $total;
                $md = $md / $total;
            }
            
            $cfRule = $this->calculateCf($mb, $md);

            // Apply user weight jika ada
            $symptomId = $symptom->id;
            $userWeight = $userSymptomWeights[$symptomId] ?? null;
            
            if ($userWeight !== null && $userWeight > 0) {
                // Normalisasi weight ke range 0-1 jika input dalam persen (0-100)
                $normalizedWeight = min(1, max(0, $userWeight / 100));
                
                // User weight mempengaruhi keyakinan: CF_final = CF_rule * weight
                // Weight 100% = CF penuh, Weight 50% = CF setengah, Weight 0% = tidak yakin
                $cfRule = $this->calculateWithUserWeight($cfRule, $normalizedWeight);
            }

            $cfValues[] = $cfRule;
        }

        // Kombinasi semua CF dari gejala yang cocok menggunakan rumus kombinasi CF
        $combinedCf = $this->combineMultipleCf($cfValues);

        // Faktor kelengkapan gejala (berapa banyak gejala penyakit yang terdeteksi)
        $completenessFactor = $matchedSymptoms->count() / max(1, $allDiseaseSymptoms->count());
        
        // Adjust CF berdasarkan kelengkapan gejala
        // Semakin lengkap gejala yang terdeteksi, semakin tinggi keyakinan
        // Formula: finalCF = combinedCF * (0.7 + 0.3 * completeness)
        // Jika completeness = 1 (100%), faktor = 1.0
        // Jika completeness = 0.5 (50%), faktor = 0.85
        $finalCf = $combinedCf * (0.7 + 0.3 * $completenessFactor);

        return $this->normalizeToRange($finalCf, -1, 1);
    }
}
