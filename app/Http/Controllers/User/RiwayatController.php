<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rekomendasi;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat = Rekomendasi::with(['penyakit', 'detailPupuk', 'detailPestisida'])
            ->where('id_pengguna', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.riwayat.index', compact('riwayat'));
    }
}
