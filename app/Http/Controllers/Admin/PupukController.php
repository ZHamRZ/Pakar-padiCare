<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pupuk;
use App\Support\AutoCodeGenerator;
use App\Support\ProjectImage;
use Illuminate\Http\Request;

class PupukController extends Controller
{
    public function index()
    {
        $pupuk = Pupuk::orderBy('kode')->paginate(10);

        return view('admin.pupuk.index', compact('pupuk'));
    }

    public function create()
    {
        $nextCode = AutoCodeGenerator::generate(Pupuk::class, 'kode', 'PU');

        return view('admin.pupuk.create', compact('nextCode'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'kode' => AutoCodeGenerator::generate(Pupuk::class, 'kode', 'PU'),
        ]);

        $request->validate([
            'kode' => 'required|string|max:10|unique:pupuk,kode',
            'nama' => 'required|string|max:100',
            'kandungan' => 'nullable|string|max:200',
            'kandungan_detail' => 'nullable|string',
            'fungsi_utama' => 'nullable|string',
            'dosis_per_hektar' => 'required|numeric|min:0.01',
            'satuan_dosis' => 'required|string|max:10',
            'efek_penggunaan' => 'nullable|string',
            'cara_aplikasi' => 'nullable|string',
            'jadwal_umur_aplikasi' => 'nullable|string',
            'frekuensi_aplikasi' => 'nullable|string',
            'harga_per_unit' => 'required|numeric|min:0',
            'satuan_harga_qty' => 'required|numeric|min:0.01',
            'satuan_harga_unit' => 'required|string|max:10',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = $request->only(
            'kode',
            'nama',
            'kandungan',
            'kandungan_detail',
            'fungsi_utama',
            'dosis_per_hektar',
            'satuan_dosis',
            'efek_penggunaan',
            'cara_aplikasi',
            'jadwal_umur_aplikasi',
            'frekuensi_aplikasi',
            'harga_per_unit',
            'satuan_harga_qty',
            'satuan_harga_unit'
        );
        if ($request->hasFile('gambar')) {
            $data['gambar'] = ProjectImage::store($request->file('gambar'), 'pupuk');
        }
        Pupuk::create($data);

        return redirect()->route('admin.pupuk.index')
            ->with('success', '✅ Data pupuk berhasil ditambahkan.');
    }

    public function edit(Pupuk $pupuk)
    {
        return view('admin.pupuk.edit', compact('pupuk'));
    }

    public function update(Request $request, Pupuk $pupuk)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:pupuk,kode,'.$pupuk->id,
            'nama' => 'required|string|max:100',
            'kandungan' => 'nullable|string|max:200',
            'kandungan_detail' => 'nullable|string',
            'fungsi_utama' => 'nullable|string',
            'dosis_per_hektar' => 'required|numeric|min:0.01',
            'satuan_dosis' => 'required|string|max:10',
            'efek_penggunaan' => 'nullable|string',
            'cara_aplikasi' => 'nullable|string',
            'jadwal_umur_aplikasi' => 'nullable|string',
            'frekuensi_aplikasi' => 'nullable|string',
            'harga_per_unit' => 'required|numeric|min:0',
            'satuan_harga_qty' => 'required|numeric|min:0.01',
            'satuan_harga_unit' => 'required|string|max:10',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = $request->only(
            'kode',
            'nama',
            'kandungan',
            'kandungan_detail',
            'fungsi_utama',
            'dosis_per_hektar',
            'satuan_dosis',
            'efek_penggunaan',
            'cara_aplikasi',
            'jadwal_umur_aplikasi',
            'frekuensi_aplikasi',
            'harga_per_unit',
            'satuan_harga_qty',
            'satuan_harga_unit'
        );
        if ($request->hasFile('gambar')) {
            ProjectImage::delete($pupuk->gambar);
            $data['gambar'] = ProjectImage::store($request->file('gambar'), 'pupuk');
        }
        $pupuk->update($data);

        return redirect()->route('admin.pupuk.index')
            ->with('success', '✅ Data pupuk berhasil diperbarui.');
    }

    public function destroy(Pupuk $pupuk)
    {
        try {
            if ($pupuk->gambar) {
                ProjectImage::delete($pupuk->gambar);
            }
            $pupuk->delete();

            return redirect()->route('admin.pupuk.index')
                ->with('success', '✅ Data pupuk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal menghapus data: '.$e->getMessage());
        }
    }
}
