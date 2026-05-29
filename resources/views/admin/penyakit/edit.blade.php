@extends('layouts.app')

@section('title', 'Edit Penyakit')
@section('page-title', 'Edit Penyakit')

@section('content')
<div class="card">
    <div class="card-header">Form Edit Penyakit</div>
    <div class="card-body">
        <form action="{{ route('admin.penyakit.update', $penyakit) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Penyakit</label>
                    <input type="text" name="kode" value="{{ old('kode', $penyakit->kode) }}" readonly class="form-control @error('kode') is-invalid @enderror">
                    <div class="form-text">Kode disimpan otomatis oleh sistem dan tidak perlu diubah manual.</div>
                    @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Penyakit</label>
                    <input type="text" name="nama" value="{{ old('nama', $penyakit->nama) }}" class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gambar Penyakit</label>
                    <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror">
                    @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($penyakit->gambar_url)
                    <img src="{{ $penyakit->gambar_url }}" alt="{{ $penyakit->nama }}" class="mt-2" style="width:96px;height:96px;object-fit:cover;border-radius:12px;">
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $penyakit->deskripsi) }}</textarea>
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
                        Perbarui gejala yang relevan beserta nilai <strong>MB</strong> dan <strong>MD</strong> dari pakar untuk penyakit ini.
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
                                @php($rule = old("gejala_rules.{$item->id}", $gejalaRules[$item->id] ?? ['selected' => false, 'mb' => 0.700, 'md' => 0.100]))
                                <tr>
                                    <td data-label="Gunakan" class="text-center" style="width:90px;">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="gejala_rules[{{ $item->id }}][selected]"
                                            {{ !empty($rule['selected']) ? 'checked' : '' }}>
                                    </td>
                                    <td data-label="Gejala">
                                        <strong>{{ $item->kode }}</strong><br>
                                        <small class="text-muted">{{ $item->nama_gejala }}</small>
                                    </td>
                                    <td data-label="MB" style="min-width:140px;">
                                        <input type="number" step="0.001" min="0" max="1"
                                            name="gejala_rules[{{ $item->id }}][mb]"
                                            value="{{ $rule['mb'] }}"
                                            class="form-control">
                                    </td>
                                    <td data-label="MD" style="min-width:140px;">
                                        <input type="number" step="0.001" min="0" max="1"
                                            name="gejala_rules[{{ $item->id }}][md]"
                                            value="{{ $rule['md'] }}"
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
                <button type="submit" class="btn btn-spk">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection
