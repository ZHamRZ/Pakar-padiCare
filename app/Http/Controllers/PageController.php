<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use App\Models\Penyakit;
use App\Models\Rekomendasi;

class PageController extends Controller
{
    public function tentang()
    {
        return view('pages.tentang', [
            'stats' => [
                'penyakit' => Penyakit::count(),
                'gejala' => Gejala::count(),
                'riwayat' => Rekomendasi::count(),
            ],
        ]);
    }

    public function bantuan()
    {
        return view('pages.bantuan');
    }
}
