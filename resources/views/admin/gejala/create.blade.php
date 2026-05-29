@extends('layouts.app')

@section('title', 'Tambah Gejala')
@section('page-title', 'Tambah Gejala')

@section('content')
<div class="card">
    <div class="card-header">Form Tambah Gejala</div>
    <div class="card-body">
        <form action="{{ route('admin.gejala.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Kode Gejala</label>
                <input type="text" name="kode" value="{{ old('kode', $nextCode) }}" readonly class="form-control @error('kode') is-invalid @enderror">
                <div class="form-text">Kode dibuat otomatis oleh sistem saat data disimpan.</div>
                @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Gejala</label>
                <textarea name="nama_gejala" rows="3" class="form-control @error('nama_gejala') is-invalid @enderror">{{ old('nama_gejala') }}</textarea>
                @error('nama_gejala')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Gambar Gejala</label>
                <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror">
                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-spk">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
