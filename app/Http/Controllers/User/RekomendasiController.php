<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Penyakit;
use App\Models\Rekomendasi;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RekomendasiController extends Controller
{
    public function __construct(private RecommendationService $recommendationService) {}

    public function show(int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'penyakit',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])
            ->where('id_pengguna', Auth::id())
            ->findOrFail($id);

        return view('user.rekomendasi.show', [
            'rekomendasi' => $rekomendasi,
            'isPreview' => false,
        ]);
    }

    public function preview()
    {
        $hasilDiagnosa = $this->buildGuestPreviewCollection();

        return view('user.rekomendasi.preview-batch', [
            'hasilDiagnosa' => $hasilDiagnosa,
            'isPreview' => true,
            'isSavedBatch' => session('guest_rekomendasi.mode') === 'saved',
        ]);
    }

    public function detail(int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'penyakit',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])
            ->where('id_pengguna', Auth::id())
            ->findOrFail($id);

        $preview = $this->recommendationService->previewForDisease($rekomendasi->id_penyakit, $rekomendasi->preferensi_pengguna ?? []);

        return view('user.rekomendasi.detail', [
            'rekomendasi' => $rekomendasi,
            'preview' => $preview,
            'isPreview' => false,
        ]);
    }

    public function previewDetail()
    {
        $hasilDiagnosa = $this->buildGuestPreviewCollection();

        if ($hasilDiagnosa->count() !== 1) {
            return redirect()
                ->route('user.rekomendasi.preview')
                ->with('info', 'Detail analisis sistem pakar ditampilkan untuk satu penyakit. Silakan pilih satu penyakit jika ingin melihat detail perhitungannya.');
        }

        $item = $hasilDiagnosa->first();

        return view('user.rekomendasi.detail', [
            'rekomendasi' => $item['rekomendasi'],
            'preview' => $item['preview'],
            'isPreview' => true,
        ]);
    }

    public function cetak(Request $request, int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'penyakit.gejala:id,kode,nama_gejala,gambar',
            'user',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])
            ->where('id_pengguna', Auth::id())
            ->findOrFail($id);

        // Load gambar untuk pupuk dan pestisida
        $rekomendasi->detailPupuk->each(function ($detail) {
            if ($detail->pupuk) {
                $detail->pupuk->makeVisible(['gambar']);
            }
        });
        $rekomendasi->detailPestisida->each(function ($detail) {
            if ($detail->pestisida) {
                $detail->pestisida->makeVisible(['gambar']);
            }
        });
        
        // Pastikan penyakit memiliki akses ke gambar
        if ($rekomendasi->penyakit) {
            $rekomendasi->penyakit->makeVisible(['gambar']);
        }

        if ($request->boolean('download')) {
            $html = view('user.rekomendasi.cetak', compact('rekomendasi'))->render();

            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="hasil-rekomendasi-' . $rekomendasi->id . '.html"');
        }

        return view('user.rekomendasi.cetak', compact('rekomendasi'));
    }

    public function previewCetak(Request $request)
    {
        $hasilDiagnosa = $this->buildGuestPreviewCollection();

        if ($request->boolean('download')) {
            $html = view('user.rekomendasi.cetak-preview', [
                'hasilDiagnosa' => $hasilDiagnosa,
            ])->render();

            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="hasil-rekomendasi-preview.html"');
        }

        return view('user.rekomendasi.cetak-preview', [
            'hasilDiagnosa' => $hasilDiagnosa,
        ]);
    }

    private function buildGuestPreview(): array
    {
        $payload = session('guest_rekomendasi');

        if (!$payload || empty($payload['preview'])) {
            throw new NotFoundHttpException('Preview rekomendasi tidak ditemukan. Silakan lakukan diagnosis kembali.');
        }

        $penyakitModel = Penyakit::with('gejala:id,kode,nama_gejala,gambar')->find($payload['penyakit_id']);
        $penyakit = new Penyakit([
            'id' => $payload['penyakit_id'],
            'nama' => $payload['penyakit_nama'],
            'gambar' => $penyakitModel?->gambar,
        ]);

        $rekomendasi = (object) [
            'id' => null,
            'penyakit' => $penyakit,
            'preferensi_label' => $payload['preferensi_label'] ?? null,
            'preferensi_pengguna' => $payload['preferensi_pengguna'] ?? [],
            'gejala_cocok' => $this->resolveMatchedSymptoms(
                collect($payload['preferensi_pengguna']['gejala_terpilih'] ?? []),
                $penyakitModel?->gejala ?? collect()
            ),
            'detailPupuk' => $this->buildPreviewAlternatives(collect(data_get($payload, 'preview.pupuk', [])), 'pupuk'),
            'detailPestisida' => $this->buildPreviewAlternatives(collect(data_get($payload, 'preview.pestisida', [])), 'pestisida'),
        ];

        return [$rekomendasi, $payload['preview']];
    }

    private function buildGuestPreviewCollection(): Collection
    {
        $payload = session('guest_rekomendasi');

        if (!$payload) {
            throw new NotFoundHttpException('Preview rekomendasi tidak ditemukan. Silakan lakukan diagnosis kembali.');
        }

        if (!empty($payload['items'])) {
            return collect($payload['items'])->map(function (array $item) {
                if (empty($item['preview'])) {
                    throw new NotFoundHttpException('Preview rekomendasi tidak lengkap. Silakan lakukan diagnosis kembali.');
                }

                $penyakitModel = Penyakit::with('gejala:id,kode,nama_gejala,gambar')->find($item['penyakit_id']);

                $penyakit = new Penyakit([
                    'id' => $item['penyakit_id'],
                    'nama' => $item['penyakit_nama'],
                    'gambar' => $penyakitModel?->gambar,
                ]);

                return [
                    'rekomendasi' => (object) [
                        'id' => $item['rekomendasi_id'] ?? null,
                        'penyakit' => $penyakit,
                        'preferensi_label' => $item['preferensi_label'] ?? null,
                        'preferensi_pengguna' => $item['preferensi_pengguna'] ?? [],
                        'gejala_cocok' => $this->resolveMatchedSymptoms(
                            collect($item['preferensi_pengguna']['gejala_terpilih'] ?? []),
                            $penyakitModel?->gejala ?? collect()
                        ),
                        'detailPupuk' => $this->buildPreviewAlternatives(collect(data_get($item, 'preview.pupuk', [])), 'pupuk'),
                        'detailPestisida' => $this->buildPreviewAlternatives(collect(data_get($item, 'preview.pestisida', [])), 'pestisida'),
                    ],
                    'preview' => $item['preview'],
                ];
            })->values();
        }

        [$rekomendasi, $preview] = $this->buildGuestPreview();

        return collect([[
            'rekomendasi' => $rekomendasi,
            'preview' => $preview,
        ]]);
    }

    private function resolveMatchedSymptoms(Collection $selectedSymptoms, Collection $diseaseSymptoms): array
    {
        $selectedIds = $selectedSymptoms
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        return $diseaseSymptoms
            ->filter(fn($gejala) => in_array((int) $gejala->id, $selectedIds, true))
            ->map(fn($gejala) => [
                'id' => $gejala->id,
                'kode' => $gejala->kode,
                'nama_gejala' => $gejala->nama_gejala,
                'gambar_url' => $gejala->gambar_url,
            ])
            ->values()
            ->all();
    }

    private function buildPreviewAlternatives(Collection $items, string $type): Collection
    {
        return $items->map(function (array $item) use ($type) {
            // Gunakan cf_rekomendasi atau cf_percentage sebagai nilai_vi
            $cfValue = (float) data_get($item, 'cf_rekomendasi', data_get($item, 'vi', 0));
            $cfPercentage = (float) data_get($item, 'cf_percentage', 0);
            
            // Ekstrak MB/MD dari disease_info
            $diseaseInfo = data_get($item, 'disease_info', []);
            $mbPenyakit = (float) data_get($diseaseInfo, 'mb', 0);
            $mdPenyakit = (float) data_get($diseaseInfo, 'md', 0);
            $cfPenyakit = (float) data_get($diseaseInfo, 'cf_raw', $mbPenyakit - $mdPenyakit);
            
            // Ekstrak cf_dasar (CF sebelum adjustment)
            $cfDasar = (float) data_get($item, 'cf_dasar', $cfValue);
            if ($type === 'pestisida') {
                $cfDasar = (float) data_get($item, 'cf_solusi', $cfDasar);
            }
            
            // Flag efisiensi tinggi
            $isHighEfficiency = (bool) data_get($item, 'is_high_efficiency', false);
            
            // Adjustment info
            $adjustmentInfo = data_get($item, 'adjustment_info', []);
            
            $productData = [
                'kode' => data_get($item, 'kode'),
                'nama' => data_get($item, 'nama'),
                'gambar_url' => data_get($item, 'gambar_url'),
                'gejala_cocok' => collect(data_get($item, 'symptom_details', []))->map(function ($symptom) {
                    return [
                        'id' => data_get($symptom, 'id'),
                        'kode' => data_get($symptom, 'kode'),
                        'nama_gejala' => data_get($symptom, 'nama_gejala'),
                    ];
                })->toArray(),
                'mb_penyakit' => $mbPenyakit,
                'md_penyakit' => $mdPenyakit,
                'cf_penyakit' => $cfPenyakit,
            ];
            
            // Tambahkan field spesifik pupuk
            if ($type === 'pupuk') {
                $productData = array_merge($productData, [
                    'kandungan' => data_get($item, 'kandungan'),
                    'kandungan_detail' => data_get($item, 'kandungan_detail'),
                    'fungsi_utama' => data_get($item, 'fungsi_utama'),
                    'harga_per_kg' => data_get($item, 'harga_per_kg'),
                    'satuan' => data_get($item, 'satuan'),
                    'takaran' => data_get($item, 'takaran'),
                    'efek_penggunaan' => data_get($item, 'efek_penggunaan'),
                    'cara_aplikasi' => data_get($item, 'cara_aplikasi'),
                    'jadwal_umur_aplikasi' => data_get($item, 'jadwal_umur_aplikasi'),
                    'frekuensi_aplikasi' => data_get($item, 'frekuensi_aplikasi'),
                ]);
            } 
            // Tambahkan field spesifik pestisida
            else {
                $productData = array_merge($productData, [
                    'jenis' => data_get($item, 'jenis'),
                    'bahan_aktif' => data_get($item, 'bahan_aktif'),
                    'kandungan_detail' => data_get($item, 'kandungan_detail'),
                    'fungsi' => data_get($item, 'fungsi'),
                    'dosis' => data_get($item, 'dosis'),
                    'takaran' => data_get($item, 'takaran'),
                    'harga' => data_get($item, 'harga'),
                    'satuan_harga' => data_get($item, 'satuan_harga'),
                    'efek_penggunaan' => data_get($item, 'efek_penggunaan'),
                    'cara_aplikasi' => data_get($item, 'cara_aplikasi'),
                    'jadwal_umur_aplikasi' => data_get($item, 'jadwal_umur_aplikasi'),
                    'frekuensi_aplikasi' => data_get($item, 'frekuensi_aplikasi'),
                ]);
            }
            
            return (object) [
                'peringkat' => (int) data_get($item, 'peringkat', 0),
                'nilai_vi' => $cfValue,
                'cf_percentage' => $cfPercentage,
                'cf_dasar' => round($cfDasar, 4),
                'is_high_efficiency' => $isHighEfficiency,
                'adjustment_info' => $adjustmentInfo,
                'interpretation' => data_get($item, 'interpretation', []),
                'cf_meta' => [
                    'mb_penyakit' => $mbPenyakit,
                    'md_penyakit' => $mdPenyakit,
                    'cf_penyakit' => $cfPenyakit,
                ],
                $type => (object) $productData,
            ];
        })->values();
    }
}
