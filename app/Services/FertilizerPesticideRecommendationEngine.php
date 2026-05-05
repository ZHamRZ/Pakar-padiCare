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
     * 
     * CRITICAL: Filter hanya pupuk dengan CF_Final > 0
     * Pupuk dengan CF negatif (seperti Urea pada Blast) TIDAK akan direkomendasikan
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

            // CF_Penyebab: seberapa besar pupuk menyebabkan/memperparah penyakit
            $cfPenyebab = $this->cfEngine->calculateCf($mb, $md);

            // Transformasi: CF_Rekomendasi = -CF_Penyebab
            // Jika pupuk memperparah penyakit (CF_Penyebab positif), maka CF_Rekomendasi negatif → TIDAK direkomendasikan
            // Jika pupuk mencegah penyakit (CF_Penyebab negatif), maka CF_Rekomendasi positif → DIREKOMENDASIKAN
            $cfRekomendasi = -$cfPenyebab;
            $cfRekomendasi = $this->cfEngine->normalizeToRange($cfRekomendasi, -1, 1);

            // CRITICAL FIX: Skip pupuk dengan CF negatif atau nol
            // Contoh: Urea pada Blast memiliki CF negatif karena nitrogen tinggi memperparah blast
            if ($cfRekomendasi <= 0) {
                continue;
            }

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
                'adjustment_info' => [
                    'base_cf' => round($cfRekomendasi, 4),
                    'adjustment' => 0.0,
                    'reason' => 'CF dasar dari relasi penyakit-pupuk',
                ],
                'is_high_efficiency' => false,
                'disease_info' => [
                    'id' => $disease->id,
                    'nama' => $disease->nama,
                    'mb' => round($mb, 3),
                    'md' => round($md, 3),
                ],
                'matched_symptoms_count' => count($symptomIds),
            ];
        }

        usort($recommendations, fn($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);

        foreach ($recommendations as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        return $recommendations;
    }

    /**
     * Hitung rekomendasi pestisida berdasarkan PENYAKIT yang terdiagnosis
     * 
     * CRITICAL: Filter hanya pestisida dengan CF_Final > 0
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

            // CF_Solusi: efektivitas pestisida mengatasi penyakit (tanpa transformasi)
            $cfSolusi = $this->cfEngine->calculateCf($mb, $md);
            $cfRekomendasi = $cfSolusi;
            $cfRekomendasi = $this->cfEngine->normalizeToRange($cfRekomendasi, -1, 1);

            // CRITICAL FIX: Skip pestisida dengan CF negatif atau nol
            if ($cfRekomendasi <= 0) {
                continue;
            }

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
                'adjustment_info' => [
                    'base_cf' => round($cfRekomendasi, 4),
                    'adjustment' => 0.0,
                    'reason' => 'CF dasar dari relasi penyakit-pestisida',
                ],
                'is_high_efficiency' => false,
                'disease_info' => [
                    'id' => $disease->id,
                    'nama' => $disease->nama,
                    'mb' => round($mb, 3),
                    'md' => round($md, 3),
                ],
                'matched_symptoms_count' => count($symptomIds),
            ];
        }

        usort($recommendations, fn($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);

        foreach ($recommendations as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        return $recommendations;
    }

    /**
     * Hitung rekomendasi lengkap (pupuk + pestisida) berdasarkan PENYAKIT
     * 
     * CRITICAL: Filter negatif sudah dilakukan di level calculateFertilizerRecommendations
     * dan calculatePesticideRecommendations. Method ini hanya untuk limit top N.
     */
    public function calculateAllRecommendations(
        int $diseaseId,
        array $symptomIds = [],
        ?int $topN = 3,  // Default limit ke 3 teratas
        bool $onlyPositive = true
    ): array {
        $fertilizerRecs = $this->calculateFertilizerRecommendations($diseaseId, $symptomIds);
        $pesticideRecs = $this->calculatePesticideRecommendations($diseaseId, $symptomIds);

        // Catatan: Filter onlyPositive sudah dilakukan di level method individual
        // Ini adalah defensive layer tambahan
        if ($onlyPositive) {
            $fertilizerRecs = array_values(array_filter($fertilizerRecs, fn($item) => $item['cf_rekomendasi'] > 0));
            $pesticideRecs = array_values(array_filter($pesticideRecs, fn($item) => $item['cf_rekomendasi'] > 0));

            foreach ($fertilizerRecs as $index => &$item) {
                $item['peringkat'] = $index + 1;
            }
            foreach ($pesticideRecs as $index => &$item) {
                $item['peringkat'] = $index + 1;
            }
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
            ],
            'method_info' => [
                'basis_rekomendasi' => 'Penyakit (bukan gejala)',
                'pupuk_transformation' => 'CF_rekomendasi = -CF_penyebab',
                'pestisida_transformation' => 'CF_rekomendasi = CF_solusi (tanpa perubahan)',
                'negative_filter' => 'Pupuk/Pestisida dengan CF <= 0 otomatis difilter',
            ],
        ];
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