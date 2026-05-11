<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rekomendasi;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function __construct(private RecommendationService $recommendationService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['tanggal_dari', 'tanggal_sampai', 'user_id', 'status']);
        $hasFilter = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        $query = Rekomendasi::with([
            'user',
            'penyakit',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])
            ->withCount(['detailPupuk', 'detailPestisida']);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->date('tanggal_dari')->toDateString());
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->date('tanggal_sampai')->toDateString());
        }

        if ($request->filled('user_id')) {
            $query->where('id_user', $request->integer('user_id'));
        }

        match ($request->input('status')) {
            'lengkap' => $query->has('detailPupuk')->has('detailPestisida'),
            'tidak_lengkap' => $query->where(fn ($statusQuery) => $statusQuery
                ->doesntHave('detailPupuk')
                ->orDoesntHave('detailPestisida')),
            default => null,
        };

        $riwayat = $query
            ->latest('tanggal')
            ->paginate(15)
            ->withQueryString();

        $users = User::where('role', 'petani')
            ->orderBy('nama')
            ->get(['id', 'nama', 'username']);

        $statusOptions = [
            'lengkap' => 'Lengkap',
            'tidak_lengkap' => 'Tidak Lengkap',
        ];

        return view('admin.riwayat.index', compact('riwayat', 'users', 'filters', 'statusOptions', 'hasFilter'));
    }

    public function cetak(Request $request, int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'user',
            'penyakit.gejala:id,kode,nama_gejala,gambar',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])->findOrFail($id);

        if ($request->boolean('download')) {
            $html = view('admin.riwayat.cetak', compact('rekomendasi'))->render();

            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="laporan-riwayat-' . $rekomendasi->id . '.html"');
        }

        return view('admin.riwayat.cetak', compact('rekomendasi'));
    }

    public function show(int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'user',
            'penyakit',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])->findOrFail($id);

        return view('admin.riwayat.show', compact('rekomendasi'));
    }

    public function detail(int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'user',
            'penyakit',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])->findOrFail($id);

        $preview = $this->buildHistoryAnalysisPreview($rekomendasi);

        return view('admin.riwayat.detail', compact('rekomendasi', 'preview'));
    }

    private function buildHistoryAnalysisPreview(Rekomendasi $rekomendasi): array
    {
        $calculatedPreview = $this->recommendationService->previewForDisease(
            $rekomendasi->id_penyakit,
            $rekomendasi->preferensi_pengguna ?? []
        );

        return [
            'pupuk' => $this->normalizeHistoryItems(
                savedItems: $rekomendasi->detailPupuk,
                calculatedItems: $calculatedPreview['pupuk'] ?? [],
                relationName: 'pupuk',
                productKeyName: 'id_pupuk',
                typeLabel: 'pupuk'
            ),
            'pestisida' => $this->normalizeHistoryItems(
                savedItems: $rekomendasi->detailPestisida,
                calculatedItems: $calculatedPreview['pestisida'] ?? [],
                relationName: 'pestisida',
                productKeyName: 'id_pestisida',
                typeLabel: 'pestisida'
            ),
        ];
    }

    private function normalizeHistoryItems(
        $savedItems,
        array $calculatedItems,
        string $relationName,
        string $productKeyName,
        string $typeLabel
    ): array {
        $calculatedById = collect($calculatedItems)->keyBy('id');

        return $savedItems
            ->sortBy('peringkat')
            ->map(function ($savedItem) use ($calculatedById, $relationName, $productKeyName, $typeLabel) {
                $product = $savedItem->{$relationName};
                $calculatedItem = $calculatedById->get($savedItem->{$productKeyName}, []);
                $score = (float) $savedItem->nilai_vi;

                return [
                    'id' => $savedItem->{$productKeyName},
                    'kode' => data_get($calculatedItem, 'kode', $product->kode ?? '-'),
                    'nama' => data_get($calculatedItem, 'nama', $product->nama ?? '-'),
                    'vi' => $score,
                    'peringkat' => (int) $savedItem->peringkat,
                    'detail' => $this->buildAnalysisRows($calculatedItem, $score, $typeLabel),
                    'cf_meta' => [
                        'cf_awal' => (float) data_get(
                            $calculatedItem,
                            'adjustment_info.base_cf',
                            data_get($calculatedItem, 'cf_dasar', data_get($calculatedItem, 'disease_info.cf_raw', $score))
                        ),
                        'cf_akhir' => $score,
                    ],
                ];
            })
            ->values()
            ->all();
    }

    private function buildAnalysisRows(array $item, float $score, string $typeLabel): array
    {
        $diseaseInfo = data_get($item, 'disease_info', []);
        $adjustmentInfo = data_get($item, 'adjustment_info', []);
        $baseCf = (float) data_get($adjustmentInfo, 'base_cf', data_get($item, 'cf_dasar', data_get($diseaseInfo, 'cf_raw', $score)));
        $adjustment = (float) data_get($adjustmentInfo, 'adjustment', $score - $baseCf);

        $rows = [
            'BASE' => [
                'kriteria' => 'Relasi penyakit dan ' . $typeLabel,
                'jenis' => 'cf',
                'preferensi_user' => null,
                'mb_bonus' => (float) data_get($diseaseInfo, 'mb', 0),
                'md_bonus' => (float) data_get($diseaseInfo, 'md', 0),
                'impact' => $baseCf,
                'catatan' => 'CF dasar dihitung dari nilai MB dan MD pada relasi penyakit dengan alternatif ini.',
            ],
        ];

        if ($adjustment !== 0.0 || data_get($item, 'preference_applied')) {
            $rows['PREFERENSI'] = [
                'kriteria' => 'Penyesuaian preferensi pengguna',
                'jenis' => 'preferensi',
                'preferensi_user' => null,
                'mb_bonus' => max($adjustment, 0),
                'md_bonus' => min($adjustment, 0),
                'impact' => $adjustment,
                'catatan' => data_get($adjustmentInfo, 'reason', 'Skor disesuaikan berdasarkan prioritas pengguna.'),
            ];
        }

        $rows['AKHIR'] = [
            'kriteria' => 'Skor akhir tersimpan di riwayat',
            'jenis' => 'hasil',
            'preferensi_user' => null,
            'mb_bonus' => 0,
            'md_bonus' => 0,
            'impact' => $score,
            'catatan' => 'Nilai ini menjadi acuan peringkat pada riwayat rekomendasi.',
        ];

        return $rows;
    }
}
