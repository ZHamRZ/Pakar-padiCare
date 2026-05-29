<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Penyakit;
use App\Services\DiagnosisService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class DiagnosisController extends Controller
{
    public function __construct(
        private DiagnosisService $diagnosisService,
        private RecommendationService $recommendationService,
    ) {}

    public function index()
    {
        $gejala = Gejala::orderBy('kode')->get();

        return view('user.diagnosis.index', compact('gejala'));
    }

    public function identifikasi(Request $request)
    {
        // CRITICAL FIX: Clear session diagnosis_result lama sebelum menghitung yang baru
        // Ini mencegah gejala yang di-uncheck tetap terdiagnosa karena data cached
        session()->forget('diagnosis_result');
        session()->forget('guest_rekomendasi');

        $request->validate([
            'gejala' => 'required|array|min:1',
            'gejala.*' => 'exists:gejala,id',
            'gejala_weights' => 'nullable|array',
            'gejala_weights.*' => 'nullable|numeric|min:0|max:100',
        ], [
            'gejala.required' => 'Pilih minimal 1 gejala yang dialami tanaman.',
            'gejala.min' => 'Pilih minimal 1 gejala yang dialami tanaman.',
        ]);

        $idGejalaInput = collect($request->gejala)->map(fn ($id) => (int) $id)->all();

        // Ambil bobot keyakinan user untuk setiap gejala (dari slider input)
        $userWeights = [];
        if ($request->filled('gejala_weights')) {
            foreach ($request->gejala_weights as $gejalaId => $weight) {
                if (in_array((int) $gejalaId, $idGejalaInput, true)) {
                    $userWeights[(int) $gejalaId] = (float) $weight;
                }
            }
        }

        // Jika tidak ada weight spesifik, gunakan default 1.0 untuk semua gejala terpilih
        if (empty($userWeights)) {
            $userWeights = array_fill_keys($idGejalaInput, 1.0);
        }

        $diagnosisResult = $this->diagnosisService->identify($idGejalaInput, $userWeights);
        $skorPenyakit = $diagnosisResult['diagnoses'];

        if (empty($skorPenyakit)) {
            return back()
                ->withInput()
                ->with('error', 'Penyakit tidak ditemukan berdasarkan gejala yang dipilih. Coba tambahkan gejala lain atau konsultasikan dengan pakar pertanian.');
        }

        session([
            'diagnosis_result' => [
                'skorPenyakit' => $skorPenyakit,
                'gejala_ids' => $idGejalaInput,
                'gejala_weights' => $userWeights,
                'summary' => $diagnosisResult['summary'],
            ],
        ]);

        return redirect()->route('user.diagnosis.hasil');
    }

    public function hasilIdentifikasi()
    {
        $payload = session('diagnosis_result');

        if (! $payload) {
            return redirect()
                ->route('user.diagnosis.index')
                ->with('info', 'Silakan pilih gejala terlebih dahulu untuk melakukan identifikasi penyakit.');
        }

        $gejalaInput = Gejala::whereIn('id', $payload['gejala_ids'] ?? [])->get();
        $presetPreferensi = $this->recommendationService->getPreferencePresets();
        $gejalaWeights = $payload['gejala_weights'] ?? [];

        return view('user.diagnosis.hasil', [
            'skorPenyakit' => $payload['skorPenyakit'] ?? [],
            'gejalaInput' => $gejalaInput,
            'presetPreferensi' => $presetPreferensi,
            'diagnosisSummary' => $payload['summary'] ?? [],
            'gejalaWeights' => $gejalaWeights,
        ]);
    }

    public function proses(Request $request)
    {
        $request->validate([
            'id_penyakit' => 'required|array|min:1',
            'id_penyakit.*' => 'exists:penyakit,id',
            'gejala_terpilih' => 'required|array|min:1',
            'gejala_terpilih.*' => 'exists:gejala,id',
            'preferensi_tipe' => 'required|string|in:seimbang,hemat,efisiensi',
            'preferensi_alasan' => 'nullable|string|max:150',
            'preferensi_catatan' => 'nullable|string|max:500',
            'luas_lahan' => 'required|numeric|min:0.01',
        ], [
            'id_penyakit.required' => 'Hasil identifikasi belum siap diproses. Silakan ulangi identifikasi lalu coba lihat rekomendasi lagi.',
            'id_penyakit.*.exists' => 'Penyakit hasil identifikasi tidak ditemukan. Silakan ulangi identifikasi.',
            'gejala_terpilih.required' => 'Gejala terpilih tidak ditemukan. Silakan ulangi identifikasi.',
            'preferensi_tipe.required' => 'Silakan pilih prioritas rekomendasi terlebih dahulu.',
            'luas_lahan.required' => 'Ukuran lahan wajib diisi sebelum memproses rekomendasi.',
            'luas_lahan.numeric' => 'Ukuran lahan harus berupa angka.',
            'luas_lahan.min' => 'Ukuran lahan minimal 0.01 hektar.',
        ]);

        if (! $request->filled('id_penyakit.0')) {
            return back()->with('error', 'Penyakit hasil identifikasi belum terbaca. Silakan ulangi identifikasi lalu coba lihat rekomendasi lagi.');
        }

        // Simpan luas lahan ke session
        session(['luas_lahan' => (float) $request->luas_lahan]);

        $gejalaTerpilih = Gejala::whereIn('id', $request->input('gejala_terpilih', []))
            ->orderBy('kode')
            ->get(Gejala::selectableColumns())
            ->map(fn ($item) => [
                'id' => $item->id,
                'kode' => $item->kode,
                'nama_gejala' => $item->nama_gejala,
                'gambar_url' => $item->gambar_url,
            ])
            ->values()
            ->all();

        // Ambil bobot gejala dari session diagnosis (jika ada)
        $diagnosisPayload = session('diagnosis_result');
        $gejalaWeights = $diagnosisPayload['gejala_weights'] ?? [];

        $userConfidence = (float) ($request->user_confidence ?? 1.0);

        $preferensi = [
            'preset' => $request->preferensi_tipe,
            'alasan' => $request->preferensi_alasan,
            'catatan' => $request->preferensi_catatan,
            'gejala_terpilih' => $gejalaTerpilih,
            'gejala_weights' => $gejalaWeights,
            'user_confidence' => $userConfidence,
        ];

        $presets = $this->recommendationService->getPreferencePresets();
        $idPenyakitList = collect($request->input('id_penyakit', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        try {
            $penyakitList = Penyakit::whereIn('id', $idPenyakitList)->get()->keyBy('id');
            $payloadItems = [];

            foreach ($idPenyakitList as $idPenyakit) {
                $penyakit = $penyakitList->get($idPenyakit);

                // Gunakan calculateWithPreferences untuk integrasi penuh preferensi user
                $preview = $this->recommendationService->calculateWithPreferences(
                    $idPenyakit,
                    $request->preferensi_tipe,
                    $userConfidence,
                    $gejalaWeights
                );

                $rekomendasi = Auth::check()
                    ? $this->recommendationService->saveForUser(Auth::id(), $idPenyakit, $preferensi)
                    : null;

                $payloadItems[] = [
                    'rekomendasi_id' => $rekomendasi?->id,
                    'penyakit_id' => $penyakit->id,
                    'penyakit_nama' => $penyakit->nama,
                    'preferensi_label' => $presets[$request->preferensi_tipe]['label'] ?? Str::headline($request->preferensi_tipe),
                    'preferensi_pengguna' => [
                        'alasan' => $request->preferensi_alasan,
                        'catatan' => $request->preferensi_catatan,
                        'gejala_terpilih' => $gejalaTerpilih,
                        'gejala_weights' => $gejalaWeights,
                        'user_confidence' => $userConfidence,
                    ],
                    'preview' => $preview,
                ];
            }

            session([
                'guest_rekomendasi' => [
                    'mode' => Auth::check() ? 'saved' : 'preview',
                    'items' => $payloadItems,
                ],
            ]);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('user.rekomendasi.preview')
            ->with('success', Auth::check()
                ? 'Semua rekomendasi berhasil dihitung dan disimpan ke riwayat.'
                : 'Rekomendasi berhasil dihitung. Login hanya diperlukan jika Anda ingin menyimpan hasil diagnosis.');
    }
}
