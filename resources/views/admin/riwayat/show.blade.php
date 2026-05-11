@extends('layouts.app')

@section('title', 'Detail Riwayat')
@section('page-title', 'Detail Riwayat')

@push('styles')
<style>
    .summary-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="summary-card p-4 h-100">
            <div class="small text-muted mb-2">Informasi hasil</div>
            <p class="mb-2"><strong>Pengguna:</strong> {{ $rekomendasi->user->nama ?? '-' }}</p>
            <p class="mb-2"><strong>Penyakit:</strong> {{ $rekomendasi->penyakit->nama ?? '-' }}</p>
            <p class="mb-0"><strong>Tanggal:</strong> {{ optional($rekomendasi->created_at)->format('d M Y H:i') }}</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Ringkasan Pupuk</div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($rekomendasi->detailPupuk->sortBy('peringkat') as $item)
                    <div class="col-xl-6">
                        <x-expert-system.result-card
                            type="Pupuk"
                            :title="$item->pupuk->nama ?? '-'"
                            :code="$item->pupuk->kode ?? null"
                            :description="App\Support\ExpertSystemPresenter::shortDescription(optional($item->pupuk)->fungsi_utama, optional($item->pupuk)->efek_penggunaan)"
                            :score="$item->nilai_vi"
                            :rank="$item->peringkat"
                            :image-url="optional($item->pupuk)->gambar_url"
                            :badge="$rekomendasi->preferensi_label"
                        />
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Ringkasan Pestisida</div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($rekomendasi->detailPestisida->sortBy('peringkat') as $item)
                    <div class="col-xl-6">
                        <x-expert-system.result-card
                            type="Pestisida"
                            :title="$item->pestisida->nama ?? '-'"
                            :code="$item->pestisida->kode ?? null"
                            :description="App\Support\ExpertSystemPresenter::shortDescription(optional($item->pestisida)->fungsi, optional($item->pestisida)->efek_penggunaan)"
                            :score="$item->nilai_vi"
                            :rank="$item->peringkat"
                            :image-url="optional($item->pestisida)->gambar_url"
                            :badge="$rekomendasi->preferensi_label"
                        />
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.riwayat.index') }}" class="btn btn-outline-secondary">Kembali ke Monitoring</a>
            <a href="{{ route('admin.riwayat.detail', $rekomendasi->id) }}" class="btn btn-outline-success">Lihat Detail Analisis</a>
            <a href="{{ route('admin.riwayat.cetak', $rekomendasi->id) }}" class="btn btn-success">Cetak / Ekspor Laporan</a>
        </div>
    </div>
</div>
@endsection
