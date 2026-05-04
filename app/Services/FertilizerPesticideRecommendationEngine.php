<?php

namespace App\Services;

use App\Models\Penyakit;
use App\Models\Pupuk;
use App\Models\Pestisida;
use Illuminate\Support\Collection;

/**
 * FertilizerPesticideRecommendationEngine
 * 
 * Engine khusus untuk rekomendasi pupuk dan pestisida berdasarkan PENYAKIT menggunakan metode Certainty Factor (CF)
 * dengan memperhatikan perbedaan makna antara penyebab (pupuk) dan solusi (pestisida).
 * 
 * PERUBAHAN LOGIKA (GEJALA → PENYAKIT):
 * =====================================
 * - Sebelumnya: Rekomendasi dihitung berdasarkan relasi gejala-pupuk dan gejala-pestisida
 * - Sekarang: Rekomendasi dihitung berdasarkan relasi penyakit-pupuk dan penyakit-pestisida
 * - Gejala hanya sebagai faktor kelengkapan diagnosis, bukan dasar rekomendasi pupuk/pestisida
 * 
 * KONSEP DASAR:
 * =============
 * 1. Certainty Factor: CF = MB - MD
 * 2. Interpretasi CF:
 *    - CF > 0  → mendukung hipotesis
 *    - CF = 0  → netral
 *    - CF < 0  → menolak hipotesis
 * 
 * PERBEDAAN MAKNA DATA:
 * =====================
 * A. PUPUK (SEBAGAI PENYEBAB/PENCEGAH)
 *    - CF menunjukkan seberapa besar pupuk mencegah/menyebabkan penyakit
 *    - CF positif → pupuk tidak cocok untuk penyakit ini (tidak direkomendasikan)
 *    - CF negatif → pupuk cocok untuk penyakit ini (direkomendasikan)
 *    - Transformasi: CF_rekomendasi = -CF_penyebab
 * 
 * B. PESTISIDA (SEBAGAI SOLUSI/PENGOBATAN)
 *    - CF menunjukkan efektivitas terhadap penyakit
 *    - CF positif → efektif (direkomendasikan)
 *    - CF negatif → tidak efektif (tidak direkomendasikan)
 *    - Tidak ada transformasi: CF_rekomendasi = CF_asli
 */
class FertilizerPesticideRecommendationEngine
{
    public function __construct(
        private CertaintyFactorEngine $cfEngine
    ) {}

    /**
     * Hitung rekomendasi pupuk berdasarkan PENYAKIT yang terdiagnosis
     */
    public function calculateFertilizerRecommendations(int $diseaseId, array $symptomIds = []): array
    {
        $disease = Penyakit::with([
            'pupuk' => function ($query) {
                $query->withPivot(['mb', 'md'])
                    ->orderBy('pupuk.kode');
            }
        ])->find($diseaseId);

        if (!$disease || $disease->pupuk->isEmpty()) {
            return [];
        }

        $recommendations = [];

        foreach ($disease->pupuk as $pivotData) {
            $fertilizer = $pivotData;
            
            $mb = (float) ($pivotData->pivot->mb ?? 0.7);
            $md = (float) ($pivotData->pivot->md ?? 0.1);

            if ($mb + $md > 1.0) {
                $total = $mb + $md;
                $mb = $mb / $total;
                $md = $md / $total;
            }

            $cfPenyebab = $this->cfEngine->calculateCf($mb, $md);
            $cfRekomendasi = -$cfPenyebab;
            $cfRekomendasi = $this->cfEngine->normalizeToRange($cfRekomendasi, -1, 1);
            $interpretation = $this->getRecommendationLabel($cfRekomendasi);

            $recommendations[] = [
                'id' => $fertilizer->id,
                'kode' => $fertilizer->kode,
                'nama' => $fertilizer->nama,
                'kandungan' => $fertilizer->kandungan,
                'kandungan_detail' => $fertilizer->kandungan_detail,
                'fungsi_utama' => $fertilizer->fungsi_utama,
                'harga_per_kg' => $fertilizer->harga_per_kg,
                'satuan' => $fertilizer->satuan,
                'takaran' => $fertilizer->takaran,
                'cara_aplikasi' => $fertilizer->cara_aplikasi,
                'frekuensi_aplikasi' => $fertilizer->frekuensi_aplikasi,
                'efek_penggunaan' => $fertilizer->efek_penggunaan,
                'gambar_url' => $fertilizer->gambar_url ?? null,
                'cf_penyebab' => round($cfPenyebab, 4),
                'cf_rekomendasi' => round($cfRekomendasi, 4),
                'cf_percentage' => round($this->cfEngine->toPercentage($cfRekomendasi), 2),
                'interpretation' => $interpretation,
                'disease_info' => [
                    'id' => $disease->id,
                    'nama' => $disease->nama,
                    'mb' => round($mb, 3),
                    'md' => round($md, 3),
                ],
                'matched_symptoms_count' => count($symptomIds),
                'matched_symptoms' => $symptomIds, // Tambahkan untuk symptom weight adjustment
            ];
        }

        usort($recommendations, fn ($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);

        foreach ($recommendations as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        return $recommendations;
    }

    /**
     * Hitung rekomendasi pestisida berdasarkan PENYAKIT yang terdiagnosis
     */
    public function calculatePesticideRecommendations(int $diseaseId, array $symptomIds = []): array
    {
        $disease = Penyakit::with([
            'pestisida' => function ($query) {
                $query->withPivot(['mb', 'md'])
                    ->orderBy('pestisida.kode');
            }
        ])->find($diseaseId);

        if (!$disease || $disease->pestisida->isEmpty()) {
            return [];
        }

        $recommendations = [];

        foreach ($disease->pestisida as $pivotData) {
            $pesticide = $pivotData;
            
            $mb = (float) ($pivotData->pivot->mb ?? 0.7);
            $md = (float) ($pivotData->pivot->md ?? 0.1);

            if ($mb + $md > 1.0) {
                $total = $mb + $md;
                $mb = $mb / $total;
                $md = $md / $total;
            }

            $cfSolusi = $this->cfEngine->calculateCf($mb, $md);
            $cfRekomendasi = $cfSolusi;
            $cfRekomendasi = $this->cfEngine->normalizeToRange($cfRekomendasi, -1, 1);
            $interpretation = $this->getRecommendationLabel($cfRekomendasi);

            $recommendations[] = [
                'id' => $pesticide->id,
                'kode' => $pesticide->kode,
                'nama' => $pesticide->nama,
                'bahan_aktif' => $pesticide->bahan_aktif,
                'kandungan_detail' => $pesticide->kandungan_detail,
                'fungsi' => $pesticide->fungsi,
                'dosis' => $pesticide->dosis,
                'harga' => $pesticide->harga,
                'satuan_harga' => $pesticide->satuan_harga,
                'cara_aplikasi' => $pesticide->cara_aplikasi,
                'frekuensi_aplikasi' => $pesticide->frekuensi_aplikasi,
                'efek_penggunaan' => $pesticide->efek_penggunaan,
                'gambar_url' => $pesticide->gambar_url ?? null,
                'cf_solusi' => round($cfSolusi, 4),
                'cf_rekomendasi' => round($cfRekomendasi, 4),
                'cf_percentage' => round($this->cfEngine->toPercentage($cfRekomendasi), 2),
                'interpretation' => $interpretation,
                'disease_info' => [
                    'id' => $disease->id,
                    'nama' => $disease->nama,
                    'mb' => round($mb, 3),
                    'md' => round($md, 3),
                ],
                'matched_symptoms_count' => count($symptomIds),
                'matched_symptoms' => $symptomIds, // Tambahkan untuk symptom weight adjustment
            ];
        }

        usort($recommendations, fn ($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);

        foreach ($recommendations as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        return $recommendations;
    }

    /**
     * Hitung rekomendasi lengkap (pupuk + pestisida) berdasarkan PENYAKIT
     * Dengan integrasi preferensi user untuk prioritas kebutuhan & harga
     */
    public function calculateAllRecommendations(
        int $diseaseId,
        array $symptomIds = [],
        ?int $topN = 3,  // Default limit ke 3 teratas
        bool $onlyPositive = true,
        string $presetType = 'seimbang'  // Preferensi: seimbang, hemat, efisiensi
    ): array {
        $fertilizerRecs = $this->calculateFertilizerRecommendations($diseaseId, $symptomIds);
        $pesticideRecs = $this->calculatePesticideRecommendations($diseaseId, $symptomIds);

        // Filter hanya yang positive jika diminta
        if ($onlyPositive) {
            $fertilizerRecs = array_values(array_filter($fertilizerRecs, fn ($item) => $item['cf_rekomendasi'] > 0));
            $pesticideRecs = array_values(array_filter($pesticideRecs, fn ($item) => $item['cf_rekomendasi'] > 0));
        }

        // Apply preference adjustment untuk prioritisasi
        $fertilizerRecs = $this->applyPreferenceToRecommendations($fertilizerRecs, $presetType, 'pupuk');
        $pesticideRecs = $this->applyPreferenceToRecommendations($pesticideRecs, $presetType, 'pestisida');

        // Re-calculate peringkat setelah adjustment
        usort($fertilizerRecs, fn ($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);
        usort($pesticideRecs, fn ($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);

        foreach ($fertilizerRecs as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }
        foreach ($pesticideRecs as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        // Limit to top N (default 3) untuk masing-masing
        if ($topN !== null && $topN > 0) {
            $fertilizerRecs = array_slice($fertilizerRecs, 0, $topN);
            $pesticideRecs = array_slice($pesticideRecs, 0, $topN);
            
            // Re-calculate peringkat setelah slicing
            foreach ($fertilizerRecs as $index => &$item) {
                $item['peringkat'] = $index + 1;
            }
            foreach ($pesticideRecs as $index => &$item) {
                $item['peringkat'] = $index + 1;
            }
        }

        $disease = Penyakit::find($diseaseId);

        return [
            'pupuk' => $fertilizerRecs,
            'pestisida' => $pesticideRecs,
            'disease' => $disease ? [
                'id' => $disease->id,
                'nama' => $disease->nama,
                'deskripsi' => $disease->deskripsi,
                'gambar_url' => $disease->gambar_url,
            ] : null,
            'summary' => [
                'disease_id' => $diseaseId,
                'total_gejala' => count($symptomIds),
                'total_pupuk_direkomendasikan' => count($fertilizerRecs),
                'total_pestisida_direkomendasikan' => count($pesticideRecs),
                'filter_positive_only' => $onlyPositive,
                'top_n' => $topN,
                'preset_type' => $presetType,
            ],
            'method_info' => [
                'basis_rekomendasi' => 'Penyakit (bukan gejala)',
                'pupuk_transformation' => 'CF_rekomendasi = -CF_penyebab',
                'pestisida_transformation' => 'CF_rekomendasi = CF_solusi (tanpa perubahan)',
                'preference_integration' => 'Prioritas kebutuhan & harga diintegrasikan dalam perhitungan CF',
            ],
        ];
    }

    /**
     * Apply preference adjustment ke rekomendasi untuk prioritisasi
     * 
     * LOGIKA PRIORITAS:
     * - 'hemat': CF tinggi + harga murah = boost maksimal (efisiensi biaya)
     * - 'efisiensi': CF tinggi + harga mahal = boost (produk premium efisien)
     * - 'seimbang': Tidak ada adjustment signifikan
     */
    private function applyPreferenceToRecommendations(array $recommendations, string $presetType, string $type): array
    {
        if ($presetType === 'seimbang') {
            return $recommendations;
        }

        foreach ($recommendations as &$item) {
            $baseCf = $item['cf_rekomendasi'];
            $harga = $type === 'pupuk' 
                ? (float) data_get($item, 'harga_per_kg', 0)
                : (float) data_get($item, 'harga', 0);
            
            $adjustment = 0.0;
            $efficiencyBonus = 0.0;
            
            // Kategorisasi harga
            $priceCategory = $this->getPriceCategory($harga, $type);
            $cfCategory = $this->getCfCategory($baseCf);
            
            if ($presetType === 'hemat') {
                // HEMAT BIAYA: Prioritaskan CF TERTINGGI + HARGA TERMURAH
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.25;
                    $efficiencyBonus = 0.10;
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'murah') {
                    $adjustment = 0.20;
                    $efficiencyBonus = 0.08;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.18;
                    $efficiencyBonus = 0.07;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'murah') {
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                } elseif ($cfCategory === 'sangat_tinggi') {
                    $adjustment = 0.12;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.08;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'sangat_murah') {
                    $adjustment = 0.10;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'murah') {
                    $adjustment = 0.06;
                } elseif ($priceCategory === 'sangat_murah') {
                    $adjustment = 0.05;
                } elseif ($priceCategory === 'murah') {
                    $adjustment = 0.03;
                } else {
                    $adjustment = $priceCategory === 'mahal' ? -0.08 : -0.04;
                }
            } elseif ($presetType === 'efisiensi') {
                // EFISIENSI TINGGI: CF tinggi + harga mahal = efisiensi tinggi
                if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'mahal') {
                    $adjustment = 0.15;
                    $efficiencyBonus = 0.05;
                } elseif ($cfCategory === 'sangat_tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.12;
                    $efficiencyBonus = 0.03;
                } elseif ($cfCategory === 'sangat_tinggi') {
                    $adjustment = 0.08;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'mahal') {
                    $adjustment = 0.10;
                    $efficiencyBonus = 0.03;
                } elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
                    $adjustment = 0.07;
                } elseif ($cfCategory === 'tinggi') {
                    $adjustment = 0.05;
                } elseif ($cfCategory === 'sedang' && $priceCategory === 'mahal') {
                    $adjustment = 0.05;
                } elseif ($cfCategory === 'sedang') {
                    $adjustment = 0.03;
                }
            }
            
            $adjustedCf = $this->cfEngine->normalizeToRange($baseCf + $adjustment, -1, 1);
            
            $item['cf_rekomendasi'] = round($adjustedCf, 4);
            $item['cf_percentage'] = round($this->cfEngine->toPercentage($adjustedCf), 2);
            $item['interpretation'] = $this->getRecommendationLabel($adjustedCf);
            $item['adjustment_info'] = [
                'preset' => $presetType,
                'adjustment' => round($adjustment, 4),
                'base_cf' => round($baseCf, 4),
                'efficiency_bonus' => $efficiencyBonus > 0 ? round($efficiencyBonus, 4) : null,
                'is_high_efficiency' => $efficiencyBonus > 0,
                'price_category' => $priceCategory,
                'cf_category' => $cfCategory,
            ];
        }
        
        return $recommendations;
    }

    /**
     * Kategorikan harga berdasarkan tipe produk
     */
    private function getPriceCategory(float $harga, string $type): string
    {
        if ($type === 'pupuk') {
            if ($harga <= 5000) return 'sangat_murah';
            if ($harga <= 15000) return 'murah';
            if ($harga <= 30000) return 'menengah';
            if ($harga <= 60000) return 'mahal';
            return 'sangat_mahal';
        } else {
            // Pestisida
            if ($harga <= 50000) return 'sangat_murah';
            if ($harga <= 100000) return 'murah';
            if ($harga <= 150000) return 'menengah';
            if ($harga <= 250000) return 'mahal';
            return 'sangat_mahal';
        }
    }

    /**
     * Kategorikan nilai CF
     */
    private function getCfCategory(float $cf): string
    {
        if ($cf >= 0.8) return 'sangat_tinggi';
        if ($cf >= 0.6) return 'tinggi';
        if ($cf >= 0.4) return 'sedang';
        if ($cf >= 0.2) return 'rendah';
        return 'sangat_rendah';
    }

    /**
     * Dapatkan label rekomendasi berdasarkan nilai CF
     */
    public function getRecommendationLabel(float $cf): array
    {
        $cf = $this->cfEngine->normalizeToRange($cf, -1, 1);

        if ($cf > 0.7) {
            return [
                'label' => 'Sangat Direkomendasikan',
                'color' => 'success',
                'icon' => '✓✓',
                'description' => 'Rekomendasi sangat kuat berdasarkan analisis penyakit.',
                'badge_class' => 'bg-success',
            ];
        } elseif ($cf > 0.4) {
            return [
                'label' => 'Direkomendasikan',
                'color' => 'primary',
                'icon' => '✓',
                'description' => 'Rekomendasi kuat berdasarkan analisis penyakit.',
                'badge_class' => 'bg-primary',
            ];
        } elseif ($cf > 0.1) {
            return [
                'label' => 'Cukup',
                'color' => 'warning',
                'icon' => '~',
                'description' => 'Rekomendasi moderat, pertimbangkan alternatif lain.',
                'badge_class' => 'bg-warning text-dark',
            ];
        } elseif ($cf > 0) {
            return [
                'label' => 'Kurang Direkomendasikan',
                'color' => 'info',
                'icon' => '?',
                'description' => 'Rekomendasi lemah, gunakan dengan pertimbangan.',
                'badge_class' => 'bg-info text-dark',
            ];
        } else {
            return [
                'label' => 'Tidak Direkomendasikan',
                'color' => 'danger',
                'icon' => '✗',
                'description' => 'Tidak direkomendasikan berdasarkan analisis penyakit.',
                'badge_class' => 'bg-danger',
            ];
        }
    }
}
