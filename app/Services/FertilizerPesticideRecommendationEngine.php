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
 *    - Data MB/MD dalam tabel penyakit_pupuk menunjukkan:
 *      * MB: seberapa besar pupuk ini MENYEBABKAN/memperparah penyakit (bukan mencegah!)
 *      * MD: seberapa kecil pupuk ini menyebabkan penyakit
 *    - CF_dasar = MB - MD
 *      * CF_dasar POSITIF (MB > MD) → pupuk menyebabkan/memperparah penyakit
 *      * CF_dasar NEGATIF (MB < MD) → pupuk mencegah/mengatasi penyakit
 *    - Transformasi untuk rekomendasi:
 *      * CF_rekomendasi = -CF_dasar
 *      * Contoh: Urea MB=0.1, MD=0.8 → CF_dasar = -0.7 → CF_rekomendasi = 0.7 (DIREKOMENDASIKAN)
 *      * Contoh: NPK MB=0.75, MD=0.15 → CF_dasar = 0.6 → CF_rekomendasi = -0.6 (TIDAK DIREKOMENDASIKAN)
 * 
 * B. PESTISIDA (SEBAGAI SOLUSI/PENGOBATAN)
 *    - Data MB/MD dalam tabel penyakit_pestisida menunjukkan:
 *      * MB: seberapa efektif pestisida mengatasi penyakit
 *      * MD: seberapa tidak efektif pestisida
 *    - CF_solusi = MB - MD
 *      * CF_positif → pestisida efektif (direkomendasikan)
 *      * CF_negatif → pestisida tidak efektif (tidak direkomendasikan)
 *    - Tidak ada transformasi: CF_rekomendasi = CF_solusi
 */
class FertilizerPesticideRecommendationEngine
{
    public function __construct(
        private CertaintyFactorEngine $cfEngine
    ) {}

    /**
     * Hitung rekomendasi pupuk berdasarkan PENYAKIT yang terdiagnosis
     * 
     * LOGIKA PUPUK:
     * - MB: seberapa besar pupuk ini MENYEBABKAN/memperparah penyakit
     * - MD: seberapa kecil pupuk ini menyebabkan penyakit
     * - CF_dasar = MB - MD (positif = menyebabkan, negatif = mencegah)
     * - CF_rekomendasi = -CF_dasar (negasi: yang mencegah jadi direkomendasikan)
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

            // CF dasar = MB - MD (menunjukkan seberapa pupuk menyebabkan penyakit)
            $cfPenyebab = $this->cfEngine->calculateCf($mb, $md);
            
            // NEGASI: Pupuk yang TIDAK menyebabkan penyakit (CF negatif) jadi DIREKOMENDASIKAN
            // Contoh: Urea CF_dasar = -0.7 → CF_rekomendasi = 0.7 (sangat direkomendasikan)
            // Contoh: NPK CF_dasar = 0.6 → CF_rekomendasi = -0.6 (tidak direkomendasikan)
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
                    'cf_penyebab' => round($cfPenyebab, 4),
                    'cf_rekomendasi' => round($cfRekomendasi, 4),
                ],
                'matched_symptoms_count' => count($symptomIds),
            ];
        }

        // Sortir DESCENDING: CF tertinggi (paling direkomendasikan) di atas
        usort($recommendations, fn ($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);

        foreach ($recommendations as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        return $recommendations;
    }

    /**
     * Hitung rekomendasi pestisida berdasarkan PENYAKIT yang terdiagnosis
     * 
     * LOGIKA PESTISIDA:
     * - MB: seberapa efektif pestisida ini MENGATASI penyakit (tingkat keberhasilan)
     * - MD: seberapa tidak efektif pestisida ini (tingkat kegagalan)
     * - CF_solusi = MB - MD (positif = efektif, negatif = tidak efektif)
     * - CF_rekomendasi = CF_solusi (TANPA negasi, karena pestisida adalah solusi langsung)
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

            // CF solusi = MB - MD (menunjukkan efektivitas pestisida)
            $cfSolusi = $this->cfEngine->calculateCf($mb, $md);
            
            // TANPA NEGASI: Pestisida yang efektif (CF positif) langsung DIREKOMENDASIKAN
            // Contoh: Amistar Top CF = 0.85 → sangat direkomendasikan
            // Contoh: Pestisida lemah CF = 0.2 → kurang direkomendasikan
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
                    'cf_solusi' => round($cfSolusi, 4),
                    'cf_rekomendasi' => round($cfRekomendasi, 4),
                ],
                'matched_symptoms_count' => count($symptomIds),
            ];
        }

        // Sortir DESCENDING: CF tertinggi (paling efektif) di atas
        usort($recommendations, fn ($a, $b) => $b['cf_rekomendasi'] <=> $a['cf_rekomendasi']);

        foreach ($recommendations as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        return $recommendations;
    }

    /**
     * Hitung rekomendasi lengkap (pupuk + pestisida) berdasarkan PENYAKIT
     * 
     * PERBAIKAN LOGIKA:
     * - Default topN = 3 untuk menampilkan hanya 3 rekomendasi teratas
     * - onlyPositive default false untuk transparansi (tampilkan semua dengan label jelas)
     * - User dapat melihat ranking lengkap dengan interpretasi yang jelas
     */
    public function calculateAllRecommendations(
        int $diseaseId,
        array $symptomIds = [],
        ?int $topN = 3,   // Default tampilkan TOP 3 saja
        bool $onlyPositive = false  // Default false - tampilkan semua untuk transparansi
    ): array {
        $fertilizerRecs = $this->calculateFertilizerRecommendations($diseaseId, $symptomIds);
        $pesticideRecs = $this->calculatePesticideRecommendations($diseaseId, $symptomIds);

        // Filter onlyPositive opsional - default tampilkan semua untuk transparansi
        if ($onlyPositive) {
            $fertilizerRecs = array_values(array_filter($fertilizerRecs, fn ($item) => $item['cf_rekomendasi'] > 0));
            $pesticideRecs = array_values(array_filter($pesticideRecs, fn ($item) => $item['cf_rekomendasi'] > 0));

            foreach ($fertilizerRecs as $index => &$item) {
                $item['peringkat'] = $index + 1;
            }
            foreach ($pesticideRecs as $index => &$item) {
                $item['peringkat'] = $index + 1;
            }
        }

        // Limit to top N jika diminta (default 3 teratas)
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
                'pupuk_transformation' => 'CF_rekomendasi = -CF_penyebab (negasi: yang mencegah jadi direkomendasikan)',
                'pestisida_transformation' => 'CF_rekomendasi = CF_solusi (tanpa perubahan: yang efektif langsung direkomendasikan)',
                'interpretation_guide' => [
                    'pupuk_cf_positif' => 'Pupuk ini DIREKOMENDASIKAN karena membantu mencegah/mengatasi penyakit',
                    'pupuk_cf_negatif' => 'Pupuk ini KURANG DIREKOMENDASIKAN karena dapat memperparah penyakit',
                    'pestisida_cf_positif' => 'Pestisida ini EFEKTIF dan DIREKOMENDASIKAN untuk mengatasi penyakit',
                    'pestisida_cf_negatif' => 'Pestisida ini KURANG EFEKTIF dan kurang direkomendasikan',
                ],
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
