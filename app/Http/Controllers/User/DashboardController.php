<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Penyakit;
use App\Models\Rekomendasi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $totalRekomendasi = 0;
        $riwayatTerbaru = collect();
        $rekomendasiBulanIni = 0;
        $rekomendasi7Hari = 0;

        if ($user && !$user->isAdmin()) {
            $totalRekomendasi = Rekomendasi::where('id_pengguna', $user->id)->count();
            $riwayatTerbaru = Rekomendasi::with(['penyakit', 'detailPupuk.pupuk', 'detailPestisida.pestisida'])
                ->where('id_pengguna', $user->id)
                ->latest()
                ->take(5)
                ->get();

            $rekomendasiBulanIni = Rekomendasi::where('id_pengguna', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $rekomendasi7Hari = Rekomendasi::where('id_pengguna', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        }

        $penyakitTerakhir = optional($riwayatTerbaru->first())->penyakit;
        $rekomendasiTerakhir = $riwayatTerbaru->first();
        $penyakitPopuler = Penyakit::withCount('rekomendasi as total_dicari')
            ->orderByDesc('total_dicari')
            ->orderBy('nama')
            ->get()
            ->filter(fn($penyakit) => $penyakit->total_dicari > 0)
            ->take(6)
            ->values();

        // Ambil riwayat referensi dari semua diagnosa terbaru secara global (bukan per user)
        $riwayatReferensi = $penyakitPopuler
            ->map(function ($penyakit) {
                // Ambil rekomendasi terbaru untuk penyakit ini dari SEMUA user
                $referensi = Rekomendasi::with([
                        'user',
                        'penyakit.gejala',
                        'detailPupuk.pupuk',
                        'detailPestisida.pestisida',
                    ])
                    ->where('id_penyakit', $penyakit->id)
                    ->latest()
                    ->first();

                if ($referensi) {
                    $referensi->total_dicari = $penyakit->total_dicari;
                }

                return $referensi;
            })
            ->filter()
            ->values();

        $progress = [
            'penyakit' => Penyakit::count(),
            'gejala' => Gejala::count(),
            'riwayat' => Rekomendasi::count(),
            'pencarian_populer' => $penyakitPopuler->sum('total_dicari'),
        ];

        $tips = [
            'Pilih gejala yang paling jelas terlihat pada tanaman agar identifikasi lebih akurat.',
            'Pastikan hasil diagnosis diproses setelah admin melengkapi data analisis untuk pupuk dan pestisida.',
            'Gunakan riwayat kasus di beranda untuk menemukan penanganan yang pernah dipakai pada gejala yang mirip.',
        ];

        return view('user.dashboard', compact(
            'user',
            'totalRekomendasi',
            'riwayatTerbaru',
            'rekomendasiBulanIni',
            'rekomendasi7Hari',
            'penyakitTerakhir',
            'rekomendasiTerakhir',
            'riwayatReferensi',
            'progress',
            'tips'
        ));
    }
}
