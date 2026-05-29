@extends('layouts.app')

@section('title', 'Riwayat Saya')
@section('page-title', 'Riwayat Saya')

@push('styles')
<style>
    .history-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        background: var(--card);
        box-shadow: var(--shadow-md);
    }

    .history-disease-image,
    .history-disease-empty {
        width: 70px;
        height: 70px;
        border-radius: var(--radius-lg);
        flex-shrink: 0;
    }

    .history-disease-image {
        object-fit: cover;
        background: var(--main-bg);
    }

    .history-disease-empty {
        background: linear-gradient(135deg, var(--main-bg) 0%, var(--border) 100%);
        color: var(--muted-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .badge.bg-light.text-dark.border {
        background: var(--main-bg) !important;
        color: var(--heading) !important;
        border-color: var(--border) !important;
    }

    .border.rounded-4 {
        border-color: var(--border) !important;
        border-radius: var(--radius-md) !important;
        background: var(--main-bg);
    }

    .btn-outline-success {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-outline-success:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .btn-outline-secondary {
        border-color: var(--border);
        color: var(--body-text);
    }

    .btn-outline-secondary:hover {
        background: var(--main-bg);
        border-color: var(--body-text);
        color: var(--heading);
    }

    .card {
        border: none;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    @forelse($riwayat as $item)
    <div class="col-xl-6">
        <div class="history-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    @if(optional($item->penyakit)->gambar_url)
                    <img src="{{ $item->penyakit->gambar_url }}" alt="{{ $item->penyakit->nama }}" class="history-disease-image">
                    @else
                    <div class="history-disease-empty">
                        <i class="bi bi-virus fs-4"></i>
                    </div>
                    @endif
                    <div>
                        <div class="small text-muted">Penyakit utama</div>
                        <div class="fw-bold">{{ $item->penyakit->nama ?? '-' }}</div>
                        <div class="small text-muted">{{ $item->penyakit->kode ?? 'Kode tidak tersedia' }}</div>
                    </div>
                </div>
                <span class="badge bg-light text-dark border">{{ $item->preferensi_label ?: 'Analisis Sistem Pakar' }}</span>
            </div>

            <div class="small text-muted mb-3">Tanggal analisis: {{ optional($item->created_at)->format('d M Y H:i') }}</div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="border rounded-4 p-3 h-100">
                        <div class="small text-muted">Pupuk utama</div>
                        <div class="fw-semibold">{{ optional(optional($item->detailPupuk->sortBy('peringkat')->first())->pupuk)->nama ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded-4 p-3 h-100">
                        <div class="small text-muted">Pestisida utama</div>
                        <div class="fw-semibold">{{ optional(optional($item->detailPestisida->sortBy('peringkat')->first())->pestisida)->nama ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('user.rekomendasi.show', $item->id) }}" class="btn btn-outline-success">Lihat Detail</a>
                <a href="{{ route('user.rekomendasi.cetak', $item->id) }}" target="_blank" class="btn btn-outline-secondary">Cetak</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">Belum ada riwayat rekomendasi.</div>
        </div>
    </div>
    @endforelse
</div>

@if($riwayat->hasPages())
<div class="mt-4">{{ $riwayat->links() }}</div>
@endif
@endsection
