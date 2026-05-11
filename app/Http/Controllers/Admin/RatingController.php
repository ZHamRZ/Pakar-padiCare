<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Penyakit;
use App\Models\PenyakitPupuk;
use App\Models\PenyakitPestisida;
use App\Models\Pupuk;
use App\Models\Pestisida;
use App\Support\CfSchema;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function pupuk()
    {
        $penyakit = Penyakit::orderBy('kode')->get();
        $pupuk = Pupuk::orderBy('kode')->get();
        $cfReady = CfSchema::hasPupukRuleTable();
        $rules = $cfReady
            ? PenyakitPupuk::all()->keyBy(fn ($item) => "{$item->id_penyakit}_{$item->id_pupuk}")
            : collect();

        return view('admin.rating.pupuk', compact('penyakit', 'pupuk', 'rules', 'cfReady'));
    }

    public function simpanPupuk(Request $request)
    {
        if (!CfSchema::hasPupukRuleTable()) {
            return redirect()->route('admin.rating.pupuk')
                ->with('error', 'Tabel rule CF pupuk belum tersedia. Jalankan migration database terlebih dahulu.');
        }

        $request->validate([
            'rules' => 'required|array',
            'rules.*.*.mb' => 'required|numeric|min:0|max:1',
            'rules.*.*.md' => 'required|numeric|min:0|max:1',
        ], $this->cfValidationMessages('pupuk'));

        foreach ($request->rules as $idPenyakit => $items) {
            foreach ($items as $idPupuk => $rule) {
                PenyakitPupuk::updateOrCreate(
                    ['id_penyakit' => $idPenyakit, 'id_pupuk' => $idPupuk],
                    [
                        'mb' => round((float) $rule['mb'], 3),
                        'md' => round((float) $rule['md'], 3),
                    ]
                );
            }
        }

        return redirect()->route('admin.rating.pupuk')
            ->with('success', 'Aturan CF pupuk berhasil disimpan.');
    }

    public function pestisida()
    {
        $penyakit = Penyakit::orderBy('kode')->get();
        $pestisida = Pestisida::orderBy('kode')->get();
        $cfReady = CfSchema::hasPestisidaRuleTable();
        $rules = $cfReady
            ? PenyakitPestisida::all()->keyBy(fn ($item) => "{$item->id_penyakit}_{$item->id_pestisida}")
            : collect();

        return view('admin.rating.pestisida', compact('penyakit', 'pestisida', 'rules', 'cfReady'));
    }

    public function simpanPestisida(Request $request)
    {
        if (!CfSchema::hasPestisidaRuleTable()) {
            return redirect()->route('admin.rating.pestisida')
                ->with('error', 'Tabel rule CF pestisida belum tersedia. Jalankan migration database terlebih dahulu.');
        }

        $request->validate([
            'rules' => 'required|array',
            'rules.*.*.mb' => 'required|numeric|min:0|max:1',
            'rules.*.*.md' => 'required|numeric|min:0|max:1',
        ], $this->cfValidationMessages('pestisida'));

        foreach ($request->rules as $idPenyakit => $items) {
            foreach ($items as $idPestisida => $rule) {
                PenyakitPestisida::updateOrCreate(
                    ['id_penyakit' => $idPenyakit, 'id_pestisida' => $idPestisida],
                    [
                        'mb' => round((float) $rule['mb'], 3),
                        'md' => round((float) $rule['md'], 3),
                    ]
                );
            }
        }

        return redirect()->route('admin.rating.pestisida')
            ->with('success', 'Aturan CF pestisida berhasil disimpan.');
    }

    private function cfValidationMessages(string $jenis): array
    {
        return [
            'rules.required' => "Aturan CF {$jenis} wajib diisi.",
            'rules.array' => "Format aturan CF {$jenis} tidak valid.",
            'rules.*.*.mb.required' => 'Nilai MB wajib diisi. Contoh: 0.100 atau 0.900.',
            'rules.*.*.mb.numeric' => 'Nilai MB harus numerik. Contoh: 0.100 atau 0.900.',
            'rules.*.*.mb.min' => 'Nilai MB harus berada pada rentang 0 sampai 1. Contoh: 0.100 atau 0.900.',
            'rules.*.*.mb.max' => 'Nilai MB harus berada pada rentang 0 sampai 1. Contoh: 0.100 atau 0.900.',
            'rules.*.*.md.required' => 'Nilai MD wajib diisi. Contoh: 0.100 atau 0.900.',
            'rules.*.*.md.numeric' => 'Nilai MD harus numerik. Contoh: 0.100 atau 0.900.',
            'rules.*.*.md.min' => 'Nilai MD harus berada pada rentang 0 sampai 1. Contoh: 0.100 atau 0.900.',
            'rules.*.*.md.max' => 'Nilai MD harus berada pada rentang 0 sampai 1. Contoh: 0.100 atau 0.900.',
        ];
    }
}
