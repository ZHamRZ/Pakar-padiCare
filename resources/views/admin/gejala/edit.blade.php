@extends('layouts.app')

@section('title', 'Edit Gejala')
@section('page-title', 'Edit Gejala')

@section('content')
<div class="card">
    <div class="card-header">Form Edit Gejala</div>
    <div class="card-body">
        <form action="{{ route('admin.gejala.update', $gejala) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Kode Gejala</label>
                <input type="text" name="kode" value="{{ old('kode', $gejala->kode) }}" readonly class="form-control @error('kode') is-invalid @enderror">
                <div class="form-text">Kode disimpan otomatis oleh sistem dan tidak perlu diubah manual.</div>
                @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Gejala</label>
                <textarea name="nama_gejala" rows="3" class="form-control @error('nama_gejala') is-invalid @enderror">{{ old('nama_gejala', $gejala->nama_gejala) }}</textarea>
                @error('nama_gejala')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Gambar Gejala</label>
                <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror">
                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($gejala->gambar_url)
                <img src="{{ $gejala->gambar_url }}" alt="{{ $gejala->nama_gejala }}" class="mt-2" style="width:96px;height:96px;object-fit:cover;border-radius:12px;">
                @endif
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-spk">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection
