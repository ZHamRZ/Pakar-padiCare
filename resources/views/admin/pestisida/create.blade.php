@extends('layouts.app')

@section('title', 'Tambah Pestisida')
@section('page-title', 'Tambah Pestisida')

@section('content')
<div class="card">
    <div class="card-header">Form Tambah Pestisida</div>
    <div class="card-body">
        <form action="{{ route('admin.pestisida.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kode</label>
                    <input type="text" name="kode" value="{{ old('kode', $nextCode) }}" readonly class="form-control @error('kode') is-invalid @enderror">
                    <div class="form-text">Kode dibuat otomatis oleh sistem saat data disimpan.</div>
                    @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Pestisida</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror">
                        <option value="">Pilih jenis</option>
                        @foreach(['fungisida','bakterisida','insektisida','herbisida'] as $jenis)
                        <option value="{{ $jenis }}" {{ old('jenis') === $jenis ? 'selected' : '' }}>{{ ucfirst($jenis) }}</option>
                        @endforeach
                    </select>
                    @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gambar Pestisida</label>
                    <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror">
                    @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bahan Aktif</label>
                    <input type="text" name="bahan_aktif" value="{{ old('bahan_aktif') }}" class="form-control @error('bahan_aktif') is-invalid @enderror">
                    @error('bahan_aktif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Kandungan Detail</label>
                    <textarea name="kandungan_detail" rows="2" class="form-control @error('kandungan_detail') is-invalid @enderror">{{ old('kandungan_detail') }}</textarea>
                    @error('kandungan_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Fungsi</label>
                    <textarea name="fungsi" rows="3" class="form-control @error('fungsi') is-invalid @enderror">{{ old('fungsi') }}</textarea>
                    @error('fungsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dosis Singkat</label>
                    <input type="text" name="dosis" value="{{ old('dosis') }}" class="form-control @error('dosis') is-invalid @enderror">
                    @error('dosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Takaran per Hektar</label>
                    <input type="number" min="0.01" step="0.01" name="dosis_per_hektar" value="{{ old('dosis_per_hektar') }}" class="form-control @error('dosis_per_hektar') is-invalid @enderror" required>
                    <div class="form-text">Jumlah untuk lahan 1 hektar (sprayer)</div>
                    @error('dosis_per_hektar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Satuan</label>
                    <select name="satuan_dosis" class="form-select @error('satuan_dosis') is-invalid @enderror" required>
                        <option value="L" {{ old('satuan_dosis', 'L') === 'L' ? 'selected' : '' }}>Liter</option>
                        <option value="ml" {{ old('satuan_dosis') === 'ml' ? 'selected' : '' }}>ml</option>
                        <option value="kg" {{ old('satuan_dosis') === 'kg' ? 'selected' : '' }}>kg</option>
                        <option value="g" {{ old('satuan_dosis') === 'g' ? 'selected' : '' }}>g</option>
                    </select>
                    @error('satuan_dosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga Patokan (Rp)</label>
                    <input type="number" min="0" step="0.01" name="harga_per_unit" value="{{ old('harga_per_unit') }}" class="form-control @error('harga_per_unit') is-invalid @enderror" required>
                    @error('harga_per_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jumlah</label>
                    <input type="number" min="0.01" step="0.01" name="satuan_harga_qty" value="{{ old('satuan_harga_qty', 100) }}" class="form-control @error('satuan_harga_qty') is-invalid @enderror" required>
                    @error('satuan_harga_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Satuan</label>
                    <select name="satuan_harga_unit" class="form-select @error('satuan_harga_unit') is-invalid @enderror" required>
                        <option value="ml" {{ old('satuan_harga_unit', 'ml') === 'ml' ? 'selected' : '' }}>ml</option>
                        <option value="l" {{ old('satuan_harga_unit') === 'l' ? 'selected' : '' }}>L</option>
                        <option value="g" {{ old('satuan_harga_unit') === 'g' ? 'selected' : '' }}>g</option>
                        <option value="kg" {{ old('satuan_harga_unit') === 'kg' ? 'selected' : '' }}>kg</option>
                    </select>
                    @error('satuan_harga_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Efek Penggunaan</label>
                    <textarea name="efek_penggunaan" rows="3" class="form-control @error('efek_penggunaan') is-invalid @enderror">{{ old('efek_penggunaan') }}</textarea>
                    @error('efek_penggunaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Cara Aplikasi</label>
                    <textarea name="cara_aplikasi" rows="3" class="form-control @error('cara_aplikasi') is-invalid @enderror">{{ old('cara_aplikasi') }}</textarea>
                    @error('cara_aplikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jadwal & Umur Aplikasi</label>
                    <textarea name="jadwal_umur_aplikasi" rows="3" class="form-control @error('jadwal_umur_aplikasi') is-invalid @enderror">{{ old('jadwal_umur_aplikasi') }}</textarea>
                    @error('jadwal_umur_aplikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Frekuensi Aplikasi</label>
                    <textarea name="frekuensi_aplikasi" rows="3" class="form-control @error('frekuensi_aplikasi') is-invalid @enderror">{{ old('frekuensi_aplikasi') }}</textarea>
                    @error('frekuensi_aplikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-spk">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
