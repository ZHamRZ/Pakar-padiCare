<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rekomendasi;
use App\Models\User;
use App\Services\CertaintyFactorEngine;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function __construct(
        private RecommendationService $recommendationService,
        private CertaintyFactorEngine $cfEngine,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['tanggal_dari', 'tanggal_sampai', 'id_pengguna', 'status']);
        $hasFilter = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        // Set default dates if not provided
        if (! filled($filters['tanggal_dari'] ?? null)) {
            $filters['tanggal_dari'] = now()->toDateString();
        }
        if (! filled($filters['tanggal_sampai'] ?? null)) {
            $filters['tanggal_sampai'] = now()->toDateString();
        }

        $query = Rekomendasi::with([
            'user',
            'penyakit',
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])
            ->withCount(['detailPupuk', 'detailPestisida']);

        // Always apply date filters (with defaults)
        $query->whereDate('tanggal', '>=', $filters['tanggal_dari']);
        $query->whereDate('tanggal', '<=', $filters['tanggal_sampai']);

        if ($request->filled('id_pengguna')) {
            $query->where('id_pengguna', $request->integer('id_pengguna'));
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
                ->header('Content-Disposition', 'attachment; filename="laporan-riwayat-'.$rekomendasi->id.'.html"');
        }

        return view('admin.riwayat.cetak', compact('rekomendasi'));
    }

    public function show(int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'user',
            'penyakit.gejala' => fn ($q) => $q->withPivot(['mb', 'md']),
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])->findOrFail($id);

        return view('admin.riwayat.show', compact('rekomendasi'));
    }

    public function detail(int $id)
    {
        $rekomendasi = Rekomendasi::with([
            'user',
            'penyakit.gejala' => fn ($q) => $q->withPivot(['mb', 'md']),
            'detailPupuk.pupuk',
            'detailPestisida.pestisida',
        ])->findOrFail($id);

        $preview = $this->buildHistoryAnalysisPreview($rekomendasi);
        $gejalaPreview = $this->buildGejalaAnalysisPreview($rekomendasi);

        return view('admin.riwayat.detail', compact('rekomendasi', 'preview', 'gejalaPreview'));
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
                'kriteria' => 'Relasi penyakit dan '.$typeLabel,
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

    private function buildGejalaAnalysisPreview(Rekomendasi $rekomendasi): array
    {
        $gejalaTerpilih = $rekomendasi->preferensi_pengguna['gejala_terpilih'] ?? [];
        $selectedIds = collect($gejalaTerpilih)->pluck('id')->toArray();

        $penyakit = $rekomendasi->penyakit;
        $allGejala = $penyakit->gejala ?? collect();

        $matchedGejala = $allGejala->filter(fn ($g) => in_array($g->id, $selectedIds));

        $rows = [];
        $cfValues = [];

        foreach ($matchedGejala as $gejala) {
            $mb = (float) ($gejala->pivot->mb ?? 0.7);
            $md = (float) ($gejala->pivot->md ?? 0.1);
            $cf = $mb - $md;
            $cfValues[] = $cf;

            $rows[] = [
                'id' => $gejala->id,
                'kode' => $gejala->kode,
                'nama' => $gejala->nama_gejala,
                'mb' => $mb,
                'md' => $md,
                'cf' => $cf,
            ];
        }

        $combinedCf = ! empty($cfValues) ? $this->cfEngine->combineMultipleCf($cfValues) : 0;
        $completenessFactor = $allGejala->count() > 0
            ? $matchedGejala->count() / $allGejala->count()
            : 0;
        $finalCf = $combinedCf * (0.7 + 0.3 * $completenessFactor);

        return [
            'rows' => $rows,
            'combinedCf' => $combinedCf,
            'finalCf' => $finalCf,
            'totalGejala' => $allGejala->count(),
            'matchedCount' => $matchedGejala->count(),
            'completenessFactor' => $completenessFactor,
        ];
    }
}
