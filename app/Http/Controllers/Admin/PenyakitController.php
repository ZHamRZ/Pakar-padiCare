<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Penyakit;
use App\Support\AutoCodeGenerator;
use App\Support\CfSchema;
use App\Support\ProjectImage;
use Illuminate\Http\Request;

class PenyakitController extends Controller
{
    public function index()
    {
        $penyakit = Penyakit::withCount('gejala')->orderBy('kode')->paginate(10);

        return view('admin.penyakit.index', compact('penyakit'));
    }

    public function create()
    {
        $gejala = Gejala::orderBy('kode')->get();
        $nextCode = AutoCodeGenerator::generate(Penyakit::class, 'kode', 'P');
        $cfReady = CfSchema::hasSymptomCfColumns();

        return view('admin.penyakit.create', compact('gejala', 'nextCode', 'cfReady'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'kode' => AutoCodeGenerator::generate(Penyakit::class, 'kode', 'P'),
        ]);

        $request->validate([
            'kode' => 'required|string|max:10|unique:penyakit,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gejala_rules' => 'nullable|array',
            'gejala_rules.*.selected' => 'nullable|boolean',
            'gejala_rules.*.mb' => 'nullable|numeric|min:0|max:1',
            'gejala_rules.*.md' => 'nullable|numeric|min:0|max:1',
        ]);

        $data = $request->only('kode', 'nama', 'deskripsi');
        if ($request->hasFile('gambar')) {
            $data['gambar'] = ProjectImage::store($request->file('gambar'), 'penyakit');
        }

        $p = Penyakit::create($data);

        if (CfSchema::hasSymptomCfColumns()) {
            $p->gejala()->sync($this->buildSymptomRuleSyncData($request->input('gejala_rules', [])));
        } elseif ($request->filled('gejala_rules')) {
            $selectedIds = collect($request->input('gejala_rules', []))
                ->filter(fn ($rule) => ! empty($rule['selected']))
                ->keys()
                ->map(fn ($id) => (int) $id)
                ->all();

            $p->gejala()->sync($selectedIds);
        }

        return redirect()->route('admin.penyakit.index')
            ->with('success', 'Data penyakit berhasil ditambahkan.');
    }

    public function edit(Penyakit $penyakit)
    {
        $gejala = Gejala::orderBy('kode')->get();
        $cfReady = CfSchema::hasSymptomCfColumns();
        $gejalaRules = $penyakit->gejala()
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->id => [
                    'selected' => true,
                    'mb' => (float) ($item->pivot->mb ?? 0.7),
                    'md' => (float) ($item->pivot->md ?? 0.1),
                ],
            ])
            ->all();

        return view('admin.penyakit.edit', compact('penyakit', 'gejala', 'gejalaRules', 'cfReady'));
    }

    public function update(Request $request, Penyakit $penyakit)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:penyakit,kode,'.$penyakit->id,
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gejala_rules' => 'nullable|array',
            'gejala_rules.*.selected' => 'nullable|boolean',
            'gejala_rules.*.mb' => 'nullable|numeric|min:0|max:1',
            'gejala_rules.*.md' => 'nullable|numeric|min:0|max:1',
        ]);

        $data = $request->only('kode', 'nama', 'deskripsi');
        if ($request->hasFile('gambar')) {
            ProjectImage::delete($penyakit->gambar);
            $data['gambar'] = ProjectImage::store($request->file('gambar'), 'penyakit');
        }

        $penyakit->update($data);

        if (CfSchema::hasSymptomCfColumns()) {
            $penyakit->gejala()->sync($this->buildSymptomRuleSyncData($request->input('gejala_rules', [])));
        } elseif ($request->filled('gejala_rules')) {
            $selectedIds = collect($request->input('gejala_rules', []))
                ->filter(fn ($rule) => ! empty($rule['selected']))
                ->keys()
                ->map(fn ($id) => (int) $id)
                ->all();

            $penyakit->gejala()->sync($selectedIds);
        }

        return redirect()->route('admin.penyakit.index')
            ->with('success', 'Data penyakit berhasil diperbarui.');
    }

    public function destroy(Penyakit $penyakit)
    {
        if ($penyakit->gambar) {
            ProjectImage::delete($penyakit->gambar);
        }
        $penyakit->delete();

        return redirect()->route('admin.penyakit.index')
            ->with('success', 'Data penyakit berhasil dihapus.');
    }

    private function buildSymptomRuleSyncData(array $rules): array
    {
        $syncData = [];

        foreach ($rules as $idGejala => $rule) {
            if (empty($rule['selected'])) {
                continue;
            }

            $syncData[(int) $idGejala] = [
                'mb' => round((float) ($rule['mb'] ?? 0.700), 3),
                'md' => round((float) ($rule['md'] ?? 0.100), 3),
            ];
        }

        return $syncData;
    }
}
