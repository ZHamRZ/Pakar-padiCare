<?php

// ── GejalaController ─────────────────────────────────────────

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Support\AutoCodeGenerator;
use App\Support\ProjectImage;
use Illuminate\Http\Request;

class GejalaController extends Controller
{
    public function index()
    {
        $gejala = Gejala::withCount('penyakit')->orderBy('kode')->paginate(15);

        return view('admin.gejala.index', compact('gejala'));
    }

    public function create()
    {
        $nextCode = AutoCodeGenerator::generate(Gejala::class, 'kode', 'G');

        return view('admin.gejala.create', compact('nextCode'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'kode' => AutoCodeGenerator::generate(Gejala::class, 'kode', 'G'),
        ]);

        $request->validate([
            'kode' => 'required|string|max:10|unique:gejala,kode',
            'nama_gejala' => 'required|string|max:200',
            'gambar' => Gejala::supportsImages() ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048' : 'nullable',
        ]);

        $data = $request->only('kode', 'nama_gejala');

        if (Gejala::supportsImages() && $request->hasFile('gambar')) {
            $data['gambar'] = ProjectImage::store($request->file('gambar'), 'gejala');
        }

        Gejala::create($data);

        return redirect()->route('admin.gejala.index')
            ->with('success', 'Gejala berhasil ditambahkan.');
    }

    public function edit(Gejala $gejala)
    {
        return view('admin.gejala.edit', compact('gejala'));
    }

    public function update(Request $request, Gejala $gejala)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:gejala,kode,'.$gejala->id,
            'nama_gejala' => 'required|string|max:200',
            'gambar' => Gejala::supportsImages() ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048' : 'nullable',
        ]);

        $data = $request->only('kode', 'nama_gejala');

        if (Gejala::supportsImages() && $request->hasFile('gambar')) {
            ProjectImage::delete($gejala->gambar);
            $data['gambar'] = ProjectImage::store($request->file('gambar'), 'gejala');
        }

        $gejala->update($data);

        return redirect()->route('admin.gejala.index')
            ->with('success', 'Gejala berhasil diperbarui.');
    }

    public function destroy(Gejala $gejala)
    {
        if (Gejala::supportsImages() && $gejala->gambar) {
            ProjectImage::delete($gejala->gambar);
        }

        $gejala->delete();

        return redirect()->route('admin.gejala.index')
            ->with('success', 'Gejala berhasil dihapus.');
    }
}
