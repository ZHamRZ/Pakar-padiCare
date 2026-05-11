<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $averageBobot = $kriteria->avg('bobot') ?? 0;
        return view('admin.kriteria.index', compact('kriteria', 'averageBobot'))
            ->with('info', 'Parameter ini digunakan dalam metode Certainty Factor untuk menyesuaikan rekomendasi berdasarkan preferensi pengguna.');
    }

    public function updateBulk(Request $request)
    {
        $request->validate([
            'kriteria' => 'required|array',
            'kriteria.*.nama' => 'required|string|max:100',
            'kriteria.*.jenis' => 'required|in:benefit,cost',
            'kriteria.*.bobot' => 'required|numeric|min:0|max:1',
            'kriteria.*.keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            foreach ($request->kriteria as $id => $data) {
                $kriteria = Kriteria::find($id);
                if ($kriteria) {
                    $kriteria->update([
                        'nama'       => $data['nama'],
                        'jenis'      => $data['jenis'],
                        'bobot'      => round((float) $data['bobot'], 2),
                        'keterangan' => $data['keterangan'] ?? null,
                    ]);
                }
            }
            
            DB::commit();
            
            // Cek jika request adalah AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Parameter prioritas Certainty Factor berhasil diperbarui. Nilai ini akan mempengaruhi adjustment MB/MD dalam perhitungan rekomendasi.'
                ]);
            }
            
            return redirect()->route('admin.kriteria.index')
                ->with('success', '✅ Parameter prioritas Certainty Factor berhasil diperbarui. Nilai ini akan mempengaruhi adjustment MB/MD dalam perhitungan rekomendasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Cek jika request adalah AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Gagal memperbarui data: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.kriteria.index')
                ->with('error', '❌ Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function edit(Kriteria $kriteria)
    {
        return view('admin.kriteria.edit', compact('kriteria'))
            ->with('info', 'Edit parameter Certainty Factor individual.');
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'nama'       => 'required|string|max:100',
            'jenis'      => 'required|in:benefit,cost',
            'bobot'      => 'required|numeric|min:0|max:1',
            'keterangan' => 'nullable|string',
        ]);
        
        try {
            $kriteria->update($request->only('nama', 'jenis', 'bobot', 'keterangan'));
            return redirect()->route('admin.kriteria.index')
                ->with('success', '✅ Parameter Certainty Factor "' . $kriteria->nama . '" berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal memperbarui: ' . $e->getMessage());
        }
    }
}
