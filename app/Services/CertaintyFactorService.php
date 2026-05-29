<?php

namespace App\Services;

use App\Models\DetailRekomendasiPestisida;
use App\Models\DetailRekomendasiPupuk;
use App\Models\KriteriaPreference;
use App\Models\Penyakit;
use App\Models\Pestisida;
use App\Models\Pupuk;
use App\Models\Rekomendasi;
use App\Support\CfSchema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CertaintyFactorService
{
    public function __construct(
        private CertaintyFactorEngine $cfEngine,
        private FertilizerPesticideRecommendationEngine $fpEngine
    ) {}

    /**
     * Hitung rekomendasi dengan logika CF yang benar:
     * - Pupuk: CF_rekomendasi = -CF_penyebab (transformasi negasi)
     * - Pestisida: CF_rekomendasi = CF_solusi (tanpa transformasi)
     */
    public function hitung(int $idUser, int $idPenyakit, array $preferensi = []): Rekomendasi
    {
        $preferensi = $this->applySystemPreferences($preferensi);
        $preview = $this->preview($idPenyakit, $preferensi);
        $preferensiSnapshot = $this->buildPreferenceSnapshot($preferensi);

        return DB::transaction(function () use ($idUser, $idPenyakit, $preview, $preferensiSnapshot) {
            $rekomendasi = Rekomendasi::create([
                'id_pengguna' => $idUser,
                'id_penyakit' => $idPenyakit,
                'tanggal' => now(),
                'preferensi_label' => $preferensiSnapshot['preset_label'],
                'preferensi_pengguna' => $preferensiSnapshot,
            ]);

            foreach ($preview['pupuk'] as $item) {
                DetailRekomendasiPupuk::create([
                    'id_rekomendasi' => $rekomendasi->id,
                    'id_pupuk' => $item['id'],
                    'nilai_vi' => $item['vi'],
                    'peringkat' => $item['peringkat'],
                ]);
            }

            foreach ($preview['pestisida'] as $item) {
                DetailRekomendasiPestisida::create([
                    'id_rekomendasi' => $rekomendasi->id,
                    'id_pestisida' => $item['id'],
                    'nilai_vi' => $item['vi'],
                    'peringkat' => $item['peringkat'],
                ]);
            }

            return $rekomendasi;
        });
    }

    public function preview(int $idPenyakit, array $preferensi = []): array
    {
        // Ambil gejala terpilih dari preferensi
        $gejalaIds = collect($preferensi['gejala_terpilih'] ?? [])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Gunakan FertilizerPesticideRecommendationEngine untuk menghitung rekomendasi
        // berdasarkan PENYAKIT dengan gejala sebagai faktor kelengkapan diagnosis
        $fpResult = $this->fpEngine->calculateAllRecommendations(
            $idPenyakit,        // Parameter 1: Disease ID (basis rekomendasi)
            $gejalaIds,         // Parameter 2: Symptom IDs (untuk kelengkapan diagnosis)
            topN: null,
            onlyPositive: false // Tampilkan semua untuk preview lengkap
        );

        // Format hasil agar kompatibel dengan struktur yang diharapkan
        $pupukFormatted = $this->formatFpResultToLegacy($fpResult['pupuk'], 'pupuk');
        $pestisidaFormatted = $this->formatFpResultToLegacy($fpResult['pestisida'], 'pestisida');

        return [
            'rumus' => [
                'cf_rule' => 'CF = MB - MD',
                'cf_combine' => 'CFcombine = CF1 + CF2 * (1 - CF1)',
                'pupuk_transformation' => 'CF_rekomendasi = -CF_penyebab (negasi untuk pupuk)',
                'pestisida_transformation' => 'CF_rekomendasi = CF_solusi (tanpa perubahan)',
                'preferensi' => 'CF akhir = CF dasar + penyesuaian MB/MD berdasarkan preset pengguna',
            ],
            'pupuk' => $pupukFormatted,
            'pestisida' => $pestisidaFormatted,
        ];
    }

    /**
     * Format hasil dari FertilizerPesticideRecommendationEngine ke format legacy
     *
     * LOGIKA BARU: Rekomendasi berbasis PENYAKIT, bukan gejala
     * - Gejala hanya digunakan sebagai faktor kelengkapan diagnosis
     * - CF dihitung berdasarkan relasi penyakit-pupuk dan penyakit-pestisida
     */
    private function formatFpResultToLegacy(array $fpResults, string $type): array
    {
        return collect($fpResults)->map(function ($item) use ($type) {
            // Metadata dasar
            $meta = [
                'gambar_url' => data_get($item, 'gambar_url'),
                // Gejala yang cocok sekarang hanya sebagai informasi tambahan
                'gejala_cocok' => data_get($item, 'matched_symptoms', []),
                'basis_rekomendasi' => 'penyakit', // Tandai bahwa rekomendasi berbasis penyakit
            ];

            if ($type === 'pupuk') {
                $meta = array_merge($meta, [
                    'kandungan' => data_get($item, 'kandungan'),
                    'kandungan_detail' => data_get($item, 'kandungan_detail'),
                    'fungsi_utama' => data_get($item, 'fungsi_utama'),
                    'dosis_per_hektar' => data_get($item, 'dosis_per_hektar'),
                    'satuan_dosis' => data_get($item, 'satuan_dosis'),
                    'efek_penggunaan' => data_get($item, 'efek_penggunaan'),
                    'cara_aplikasi' => data_get($item, 'cara_aplikasi'),
                    // Informasi disease-specific untuk pupuk
                    'cf_penyebab' => data_get($item, 'cf_penyebab'),
                    'disease_info' => data_get($item, 'disease_info'),
                ]);
            } else {
                $meta = array_merge($meta, [
                    'bahan_aktif' => data_get($item, 'bahan_aktif'),
                    'fungsi' => data_get($item, 'fungsi'),
                    'dosis' => data_get($item, 'dosis'),
                    'efek_penggunaan' => data_get($item, 'efek_penggunaan'),
                    'cara_aplikasi' => data_get($item, 'cara_aplikasi'),
                    // Informasi disease-specific untuk pestisida
                    'cf_solusi' => data_get($item, 'cf_solusi'),
                    'disease_info' => data_get($item, 'disease_info'),
                ]);
            }

            return [
                'id' => data_get($item, 'id'),
                'kode' => data_get($item, 'kode'),
                'nama' => data_get($item, 'nama'),
                'vi' => data_get($item, 'cf_rekomendasi'),
                'peringkat' => data_get($item, 'peringkat'),
                'meta' => $meta,
                'cf_meta' => [
                    // CF sudah ditransformasi dengan benar oleh FertilizerPesticideRecommendationEngine
                    'cf_rekomendasi' => data_get($item, 'cf_rekomendasi'),
                    'cf_percentage' => data_get($item, 'cf_percentage'),
                    'interpretation' => data_get($item, 'interpretation'),
                    // Informasi penyakit yang mendasari rekomendasi
                    'disease_id' => data_get($item, 'disease_info.id'),
                    'disease_name' => data_get($item, 'disease_info.nama'),
                    // Transformasi yang diterapkan
                    'transformation' => $type === 'pupuk'
                        ? 'CF_rekomendasi = -CF_penyebab (negasi)'
                        : 'CF_rekomendasi = CF_solusi (tanpa perubahan)',
                ],
                'interpretation' => data_get($item, 'interpretation'),
            ];
        })->sortByDesc('vi')->values()->all();
    }

    public function hitungAlternatif(
        string $jenis,
        int $idPenyakit,
        array $preferensi = []
    ): array {
        if (! CfSchema::isReady()) {
            throw new RuntimeException('Struktur Certainty Factor belum lengkap. Jalankan migration dan lengkapi rule CF terlebih dahulu.');
        }

        $penyakit = Penyakit::with('gejala')->findOrFail($idPenyakit);
        $matchedSymptoms = $this->resolveMatchedSymptoms($penyakit, $preferensi);

        if ($matchedSymptoms->isEmpty()) {
            throw new RuntimeException("Gejala yang cocok untuk penyakit terpilih belum tersedia, sehingga rekomendasi {$jenis} belum bisa dihitung.");
        }

        $alternatif = $this->loadAlternativesForSymptoms($jenis, $matchedSymptoms->pluck('id')->all());

        if ($alternatif->isEmpty()) {
            throw new RuntimeException("Aturan CF {$jenis} untuk gejala yang cocok belum diisi oleh pakar.");
        }

        $hasil = $alternatif->map(function ($item) use ($jenis, $preferensi, $matchedSymptoms) {
            $matchedRules = $item->gejala
                ->filter(fn ($gejala) => $matchedSymptoms->contains('id', $gejala->id))
                ->values();

            if ($matchedRules->isEmpty()) {
                return null;
            }

            [$baseMb, $baseMd, $baseCf, $baseDetail, $matchedSymptomMeta] = $this->combineSymptomRules($matchedRules);

            [$finalMb, $finalMd, $detail] = $this->applyPreferenceRules(
                item: $item,
                type: $jenis,
                baseMb: $baseMb,
                baseMd: $baseMd,
                baseCf: $baseCf,
                preferensi: $preferensi,
                baseDetail: $baseDetail
            );

            $finalCf = $this->calculateCf($finalMb, $finalMd);

            return [
                'id' => $item->id,
                'kode' => $item->kode,
                'nama' => $item->nama,
                'vi' => $finalCf,
                'meta' => [
                    'gambar_url' => method_exists($item, 'getGambarUrlAttribute') ? $item->gambar_url : null,
                    'kandungan' => $jenis === 'pupuk' ? ($item->kandungan ?? null) : null,
                    'kandungan_detail' => $item->kandungan_detail ?? null,
                    'bahan_aktif' => $jenis === 'pestisida' ? ($item->bahan_aktif ?? null) : null,
                    'fungsi_utama' => $jenis === 'pupuk' ? ($item->fungsi_utama ?? null) : null,
                    'fungsi' => $jenis === 'pestisida' ? ($item->fungsi ?? null) : null,
                    'dosis_per_hektar' => $item->dosis_per_hektar ?? null,
                    'satuan_dosis' => $item->satuan_dosis ?? null,
                    'dosis' => $jenis === 'pestisida' ? ($item->dosis ?? null) : null,
                    'efek_penggunaan' => $item->efek_penggunaan ?? null,
                    'cara_aplikasi' => $item->cara_aplikasi ?? null,
                    'jadwal_umur_aplikasi' => $item->jadwal_umur_aplikasi ?? null,
                    'frekuensi_aplikasi' => $item->frekuensi_aplikasi ?? null,
                    'gejala_cocok' => $matchedSymptomMeta,
                ],
                'detail' => $detail,
                'cf_meta' => [
                    'mb_awal' => $baseMb,
                    'md_awal' => $baseMd,
                    'cf_awal' => $baseCf,
                    'mb_akhir' => $finalMb,
                    'md_akhir' => $finalMd,
                    'cf_akhir' => $finalCf,
                ],
            ];
        })->filter()->sortByDesc('vi')->values()->all();

        foreach ($hasil as $index => &$item) {
            $item['peringkat'] = $index + 1;
        }

        return $hasil;
    }

    public function getPreferencePresets(): array
    {
        return [
            'seimbang' => [
                'label' => 'Seimbang',
                'description' => 'Memberi penyesuaian ringan dan merata agar rekomendasi tetap stabil.',
            ],
            'hemat' => [
                'label' => 'Hemat Biaya',
                'description' => 'Meningkatkan keyakinan pada alternatif yang lebih hemat dan menekan alternatif mahal.',
            ],
            'efisiensi' => [
                'label' => 'Efisiensi Tinggi',
                'description' => 'Mendorong alternatif yang paling kuat berdasarkan keyakinan dasar pakar.',
            ],
        ];
    }

    public function calculateCf(float $mb, float $md): float
    {
        // Delegate ke CF Engine untuk konsistensi
        return $this->cfEngine->calculateCf($mb, $md);
    }

    public function combineCf(float $cf1, float $cf2): float
    {
        // Delegate ke CF Engine untuk konsistensi rumus kombinasi
        return $this->cfEngine->combineCf($cf1, $cf2);
    }

    public function applyPreferenceRules(
        object $item,
        string $type,
        float $baseMb,
        float $baseMd,
        float $baseCf,
        array $preferensi,
        array $baseDetail = []
    ): array {
        $preset = $this->normalizePreset($preferensi['preset'] ?? 'seimbang');
        $harga = (float) ($item->harga_per_unit ?? 0);
        $userConfidence = (float) ($preferensi['user_confidence'] ?? 1.0);

        $finalMb = $baseMb;
        $finalMd = $baseMd;
        $detail = $baseDetail + [
            'BASE' => [
                'kriteria' => 'Akumulasi keyakinan dasar pakar',
                'jenis' => 'base',
                'mb_bonus' => 0,
                'md_bonus' => 0,
                'impact' => $baseCf,
                'mb_awal' => $baseMb,
                'md_awal' => $baseMd,
                'cf' => $baseCf,
                'catatan' => 'Nilai awal dibentuk dari gabungan semua rule gejala yang cocok dengan alternatif ini.',
            ],
        ];

        if ($preset === 'hemat') {
            $bonus = $this->resolvePriceBonus($harga);
            $penalty = $this->resolvePricePenalty($harga);
            $finalMb += $bonus;
            $finalMd += $penalty;
            $detail['PRESET'] = [
                'kriteria' => 'Preset hemat biaya',
                'jenis' => 'preset',
                'mb_bonus' => $bonus,
                'md_bonus' => $penalty,
                'impact' => round($bonus - $penalty, 6),
                'cf' => null,
                'catatan' => 'Alternatif murah diperkuat, alternatif mahal ditekan.',
            ];
        }

        if ($preset === 'efisiensi') {
            $bonus = $baseCf >= 0.8 ? 0.12 : ($baseCf >= 0.6 ? 0.08 : 0.04);
            $finalMb += $bonus;
            $detail['PRESET'] = [
                'kriteria' => 'Preset efisiensi tinggi',
                'jenis' => 'preset',
                'mb_bonus' => $bonus,
                'md_bonus' => 0,
                'impact' => round($bonus, 6),
                'cf' => null,
                'catatan' => 'Alternatif dengan keyakinan dasar pakar lebih tinggi diperkuat.',
            ];
        }

        if ($preset === 'seimbang') {
            $finalMb += 0.03;
            $finalMd = max(0, $finalMd - 0.01);
            $detail['PRESET'] = [
                'kriteria' => 'Preset seimbang',
                'jenis' => 'preset',
                'mb_bonus' => 0.03,
                'md_bonus' => -0.01,
                'impact' => 0.04,
                'cf' => null,
                'catatan' => 'Semua alternatif mendapat penyesuaian moderat dan stabil.',
            ];
        }

        $finalMb = round(min(1, max(0, $finalMb)), 6);
        $finalMd = round(min(1, max(0, $finalMd)), 6);

        return [$finalMb, $finalMd, $detail];
    }

    private function resolveMatchedSymptoms(Penyakit $penyakit, array $preferensi): Collection
    {
        $selectedIds = $this->resolveSelectedSymptomIds($preferensi);
        $diseaseSymptoms = $penyakit->gejala
            ->sortBy('kode')
            ->values();

        if ($selectedIds === []) {
            return $diseaseSymptoms;
        }

        return $diseaseSymptoms
            ->filter(fn ($gejala) => in_array((int) $gejala->id, $selectedIds, true))
            ->values();
    }

    private function resolveSelectedSymptomIds(array $preferensi): array
    {
        return collect($preferensi['gejala_terpilih'] ?? [])
            ->map(function ($gejala) {
                if (is_array($gejala)) {
                    return (int) ($gejala['id'] ?? 0);
                }

                if (is_object($gejala)) {
                    return (int) ($gejala->id ?? 0);
                }

                return (int) $gejala;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function loadAlternativesForSymptoms(string $jenis, array $matchedSymptomIds): Collection
    {
        $model = $jenis === 'pupuk' ? Pupuk::class : Pestisida::class;

        return $model::query()
            ->with(['gejala' => fn ($query) => $query
                ->whereIn('gejala.id', $matchedSymptomIds)
                ->orderBy('gejala.kode')])
            ->whereHas('gejala', fn ($query) => $query->whereIn('gejala.id', $matchedSymptomIds))
            ->orderBy('kode')
            ->get();
    }

    private function combineSymptomRules(Collection $matchedRules): array
    {
        $combinedMb = 0.0;
        $combinedMd = 0.0;
        $detail = [];

        foreach ($matchedRules as $index => $gejala) {
            $mb = round((float) ($gejala->pivot->mb ?? 0), 6);
            $md = round((float) ($gejala->pivot->md ?? 0), 6);
            $cf = $this->calculateCf($mb, $md);

            if ($index === 0) {
                $combinedMb = $mb;
                $combinedMd = $md;
            } else {
                $combinedMb = $this->combineCf($combinedMb, $mb);
                $combinedMd = $this->combineCf($combinedMd, $md);
            }

            $detail['GEJALA_'.$gejala->id] = [
                'kriteria' => ($gejala->kode ? $gejala->kode.' - ' : '').$gejala->nama_gejala,
                'jenis' => 'gejala',
                'mb_bonus' => $mb,
                'md_bonus' => $md,
                'impact' => $cf,
                'cf' => $cf,
                'catatan' => 'Rule pakar langsung antara gejala dan alternatif ini.',
            ];
        }

        $baseCf = $this->calculateCf($combinedMb, $combinedMd);
        $matchedSymptomMeta = $matchedRules
            ->map(fn ($gejala) => [
                'id' => $gejala->id,
                'kode' => $gejala->kode,
                'nama_gejala' => $gejala->nama_gejala,
                'gambar_url' => $gejala->gambar_url,
                'mb' => round((float) ($gejala->pivot->mb ?? 0), 3),
                'md' => round((float) ($gejala->pivot->md ?? 0), 3),
            ])
            ->values()
            ->all();

        return [$combinedMb, $combinedMd, $baseCf, $detail, $matchedSymptomMeta];
    }

    private function normalizePreset(string $preset): string
    {
        return match ($preset) {
            'efektif' => 'efisiensi',
            'aman', 'custom' => 'seimbang',
            default => $preset,
        };
    }

    private function resolvePriceBonus(float $harga): float
    {
        if ($harga <= 0) {
            return 0.02;
        }

        return $harga <= 50000 ? 0.12 : ($harga <= 100000 ? 0.07 : 0.02);
    }

    private function resolvePricePenalty(float $harga): float
    {
        if ($harga <= 0) {
            return 0;
        }

        return $harga > 100000 ? 0.06 : ($harga > 50000 ? 0.03 : 0);
    }

    private function applySystemPreferences(array $preferensi): array
    {
        $luasLahan = (float) ($preferensi['luas_lahan'] ?? 0);
        $hargaPerHa = (float) ($preferensi['harga_per_ha'] ?? 0);
        $totalBudget = $luasLahan > 0 && $hargaPerHa > 0 ? $luasLahan * $hargaPerHa : 0;

        if ($totalBudget > 0) {
            $hematMax = KriteriaPreference::get('budget_threshold_hemat', ['max' => 50000])['max'] ?? 50000;
            $seimbangMax = KriteriaPreference::get('budget_threshold_seimbang', ['max' => 150000])['max'] ?? 150000;

            if ($totalBudget <= $hematMax) {
                $preferensi['preset'] = 'hemat';
            } elseif ($totalBudget <= $seimbangMax) {
                $preferensi['preset'] = 'seimbang';
            } else {
                $preferensi['preset'] = 'efisiensi';
            }
        }

        $defaultConfidence = KriteriaPreference::get('default_confidence', ['value' => 1.0])['value'] ?? 1.0;

        if (! isset($preferensi['user_confidence'])) {
            $preferensi['user_confidence'] = $defaultConfidence;
        }

        return $preferensi;
    }

    private function buildPreferenceSnapshot(array $preferensi = []): array
    {
        $presets = $this->getPreferencePresets();
        $presetKey = $this->normalizePreset($preferensi['preset'] ?? 'seimbang');

        return [
            'preset' => $presetKey,
            'preset_label' => $presets[$presetKey]['label'] ?? 'Seimbang',
            'alasan' => $preferensi['alasan'] ?? null,
            'catatan' => $preferensi['catatan'] ?? null,
            'gejala_terpilih' => $preferensi['gejala_terpilih'] ?? [],
            'user_confidence' => $preferensi['user_confidence'] ?? 1.0,
        ];
    }
}
