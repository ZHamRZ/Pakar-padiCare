<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Kriteria;
use App\Models\Penyakit;
use App\Models\Pestisida;
use App\Models\Pupuk;
use App\Models\Rekomendasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPenyakit = Penyakit::count();
        $totalGejala = Gejala::count();
        $totalPupuk = Pupuk::count();
        $totalPestisida = Pestisida::count();
        $totalKriteria = Kriteria::count();
        $totalPetani = User::where('role', 'petani')->count();
        $totalRekomendasi = Rekomendasi::count();
        $totalAdmin = User::where('role', 'admin')->count();

        $stats = [
            'penyakit' => $totalPenyakit,
            'gejala' => $totalGejala,
            'pupuk' => $totalPupuk,
            'pestisida' => $totalPestisida,
            'kriteria' => $totalKriteria,
            'user' => $totalPetani,
            'rekomendasi' => $totalRekomendasi,
            'admin' => $totalAdmin,
        ];

        $riwayatTerbaru = Rekomendasi::with(['user', 'penyakit'])
            ->latest()
            ->take(8)
            ->get();

        $penggunaTerbaru = User::where('role', 'petani')
            ->withCount('rekomendasi')
            ->latest()
            ->take(6)
            ->get();

        $penyakitTeratas = Penyakit::select('penyakit.id', 'penyakit.nama', DB::raw('COUNT(rekomendasi.id) as total_rekomendasi'))
            ->leftJoin('rekomendasi', 'rekomendasi.id_penyakit', '=', 'penyakit.id')
            ->groupBy('penyakit.id', 'penyakit.nama')
            ->orderByDesc('total_rekomendasi')
            ->take(5)
            ->get();

        $rekomendasiBulanIni = Rekomendasi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $rekomendasi7Hari = Rekomendasi::where('created_at', '>=', now()->subDays(7))->count();

        return view('admin.dashboard', compact(
            'stats',
            'riwayatTerbaru',
            'penggunaTerbaru',
            'penyakitTeratas',
            'rekomendasiBulanIni',
            'rekomendasi7Hari'
        ));
    }
}
