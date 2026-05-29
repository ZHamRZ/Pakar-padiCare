@extends('layouts.app')

@section('title', 'Tambah Penyakit')
@section('page-title', 'Tambah Penyakit')

@section('content')
<div class="card">
    <div class="card-header">Form Tambah Penyakit</div>
    <div class="card-body">
        <form action="{{ route('admin.penyakit.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Penyakit</label>
                    <input type="text" name="kode" value="{{ old('kode', $nextCode) }}" readonly class="form-control @error('kode') is-invalid @enderror">
                    <div class="form-text">Kode dibuat otomatis oleh sistem saat data disimpan.</div>
                    @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Penyakit</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gambar Penyakit</label>
                    <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror">
                    @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Rule Gejala Penyakit</label>
                    @unless($cfReady ?? false)
                    <div class="alert alert-warning">
                        Kolom MB/MD untuk relasi gejala belum tersedia di database. Form ini tetap bisa dipakai untuk memilih gejala, tetapi nilai CF gejala baru aktif setelah migration dijalankan.
                    </div>
                    @endunless
                    <div class="alert alert-info">
                        Pilih gejala yang relevan, lalu isi <strong>MB</strong> dan <strong>MD</strong> dari pakar untuk setiap gejala pada penyakit ini.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Gunakan</th>
                                    <th>Gejala</th>
                                    <th>MB</th>
                                    <th>MD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gejala as $item)
                                @php($selected = old("gejala_rules.{$item->id}.selected"))
                                <tr>
                                    <td data-label="Gunakan" class="text-center" style="width:90px;">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="gejala_rules[{{ $item->id }}][selected]"
                                            {{ $selected ? 'checked' : '' }}>
                                    </td>
                                    <td data-label="Gejala">
                                        <strong>{{ $item->kode }}</strong><br>
                                        <small class="text-muted">{{ $item->nama_gejala }}</small>
                                    </td>
                                    <td data-label="MB" style="min-width:140px;">
                                        <input type="number" step="0.001" min="0" max="1"
                                            name="gejala_rules[{{ $item->id }}][mb]"
                                            value="{{ old("gejala_rules.{$item->id}.mb", 0.700) }}"
                                            class="form-control">
                                    </td>
                                    <td data-label="MD" style="min-width:140px;">
                                        <input type="number" step="0.001" min="0" max="1"
                                            name="gejala_rules[{{ $item->id }}][md]"
                                            value="{{ old("gejala_rules.{$item->id}.md", 0.100) }}"
                                            class="form-control">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-spk">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
