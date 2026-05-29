@extends('layouts.app')

@section('title', 'Detail Analisis')
@section('page-title', 'Detail Analisis')

@push('styles')
<style>
    .detail-hero {
        background: linear-gradient(135deg, var(--soft-bg) 0%, var(--main-bg) 100%);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
    }
    .detail-summary-card,
    .analysis-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }
    .analysis-card {
        overflow: hidden;
    }
    .analysis-media,
    .analysis-media-empty {
        width: 100%;
        height: 220px;
        border-radius: var(--radius-lg);
        object-fit: cover;
        background: var(--main-bg);
    }
    .analysis-media-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted-text);
        font-size: 2rem;
    }
    .mini-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .7rem;
        border-radius: 999px;
        background: var(--main-bg);
        color: var(--heading);
        border: 1px solid var(--border);
        font-size: .8rem;
        font-weight: 600;
    }
    .product-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .product-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 12px;
    }
    .product-detail-item {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 14px;
        background: var(--main-bg);
    }
    .product-detail-item.full {
        grid-column: 1 / -1;
    }
    .product-detail-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--body-text);
        font-weight: 700;
        margin-bottom: 6px;
    }
    .product-detail-value {
        font-size: .86rem;
        line-height: 1.5;
        color: var(--heading);
        word-break: break-word;
    }
    .linked-symptoms {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .linked-symptom {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: .4rem .7rem;
        border-radius: 999px;
        background: var(--soft-bg);
        border: 1px solid var(--border-light);
        color: var(--primary);
        font-size: .76rem;
        font-weight: 600;
    }
    .empty-note {
        border: 1px dashed var(--border);
        border-radius: var(--radius-lg);
        padding: 18px;
        background: var(--main-bg);
        color: var(--body-text);
    }
    @media (max-width: 767px) {
        .analysis-media,
        .analysis-media-empty {
            height: 180px;
        }
    }

    .alert-success {
        border-radius: var(--radius-md);
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
</style>
@endpush

@section('content')
@php
    use App\Support\ExpertSystemPresenter;

    $isPreview = $isPreview ?? false;
    $pupukItems = $rekomendasi->detailPupuk->sortBy('peringkat')->values();
    $pestisidaItems = $rekomendasi->detailPestisida->sortBy('peringkat')->values();
    $topPupuk = $pupukItems->first();
    $topPestisida = $pestisidaItems->first();
    $topScore = max((float) ($topPupuk->nilai_vi ?? 0), (float) ($topPestisida->nilai_vi ?? 0));
    $gejalaCocok = collect(data_get($rekomendasi, 'gejala_cocok', data_get($rekomendasi, 'preferensi_pengguna.gejala_terpilih', [])));
@endphp
@guest
<div class="container py-4">
@endguest

<div class="detail-hero p-4 p-lg-5 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-success-subtle text-success border border-success-subtle mb-3">Detail Rekomendasi</span>
            <h2 class="fw-bold mb-2">Rincian pupuk dan pestisida untuk {{ $rekomendasi->penyakit->nama }}</h2>
            <p class="text-muted mb-3">
                Halaman ini merangkum pilihan rekomendasi beserta informasi penggunaan, skor kecocokan, dan gejala yang paling relevan.
            </p>
            <div class="d-flex flex-wrap gap-2">
                @if($rekomendasi->preferensi_label)
                <span class="mini-chip"><i class="bi bi-sliders"></i>{{ $rekomendasi->preferensi_label }}</span>
                @endif
                <span class="mini-chip"><i class="bi bi-graph-up-arrow"></i>{{ ExpertSystemPresenter::percent($topScore) }} kecocokan tertinggi</span>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="detail-summary-card p-4 h-100">
                <div class="small text-muted mb-2">Ringkasan utama</div>
                <div class="mb-2"><strong>Pupuk terbaik:</strong> {{ optional(optional($topPupuk)->pupuk)->nama ?? '-' }}</div>
                <div class="mb-3"><strong>Pestisida terbaik:</strong> {{ optional(optional($topPestisida)->pestisida)->nama ?? '-' }}</div>
                <div class="small text-muted mb-2">Tingkat keyakinan</div>
                <x-expert-system.confidence-bar :value="$topScore" />
            </div>
        </div>
    </div>
</div>

@if($rekomendasi->preferensi_label)
<div class="alert alert-success mb-4">
    <div class="fw-semibold mb-1">Prioritas yang dipilih: {{ $rekomendasi->preferensi_label }}</div>
    @if(data_get($rekomendasi->preferensi_pengguna, 'alasan'))
    <div class="small">Alasan: {{ data_get($rekomendasi->preferensi_pengguna, 'alasan') }}</div>
    @endif
    @if(data_get($rekomendasi->preferensi_pengguna, 'catatan'))
    <div class="small">Catatan: {{ data_get($rekomendasi->preferensi_pengguna, 'catatan') }}</div>
    @endif
</div>
@endif

@if($gejalaCocok->isNotEmpty())
<div class="detail-summary-card p-4 mb-4">
    <div class="fw-semibold mb-3">Gejala yang dipakai dalam analisis</div>
    <div class="linked-symptoms">
        @foreach($gejalaCocok as $gejala)
        <span class="linked-symptom">
            {{ data_get($gejala, 'kode') ? data_get($gejala, 'kode') . ' · ' : '' }}{{ data_get($gejala, 'nama_gejala') }}
        </span>
        @endforeach
    </div>
</div>
@endif

@foreach([
    [
        'key' => 'pupuk',
        'label' => 'Pupuk',
        'icon' => 'bi-bag-fill',
        'iconColor' => 'text-success',
        'empty' => 'Belum ada data pupuk yang direkomendasikan untuk gejala yang dipilih.',
        'items' => $pupukItems,
    ],
    [
        'key' => 'pestisida',
        'label' => 'Pestisida',
        'icon' => 'bi-shield-fill-check',
        'iconColor' => 'text-warning',
        'empty' => 'Belum ada data pestisida yang direkomendasikan untuk gejala yang dipilih.',
        'items' => $pestisidaItems,
    ],
] as $section)
<div class="mb-4">
    <div class="product-section-head">
        <div>
            <div class="fw-bold fs-5">{{ $section['label'] }} yang Direkomendasikan</div>
            <div class="text-muted small">Urutan sudah disusun dari skor kecocokan tertinggi.</div>
        </div>
        <span class="mini-chip"><i class="bi {{ $section['icon'] }} {{ $section['iconColor'] }}"></i>{{ $section['items']->count() }} item</span>
    </div>

    @if($section['items']->isEmpty())
    <div class="empty-note">{{ $section['empty'] }}</div>
    @else
    <div class="row g-4">
        @foreach($section['items'] as $item)
        @php
            $product = $section['key'] === 'pupuk' ? $item->pupuk : $item->pestisida;
            $description = $section['key'] === 'pupuk'
                ? ExpertSystemPresenter::shortDescription(optional($product)->fungsi_utama, optional($product)->efek_penggunaan)
                : ExpertSystemPresenter::shortDescription(optional($product)->fungsi, optional($product)->efek_penggunaan);
            $detailRows = $section['key'] === 'pupuk'
                ? [
                    ['label' => 'Kode', 'value' => $product->kode ?? '-'],
                    ['label' => 'Skor', 'value' => number_format((float) ($item->nilai_vi ?? 0), 4)],
                    ['label' => 'Takaran per Hektar', 'value' => ($product->dosis_per_hektar ?? '-') . ' ' . ($product->satuan_dosis ?? '')],
                    ['label' => 'Frekuensi', 'value' => $product->frekuensi_aplikasi ?? '-'],
                    ['label' => 'Kandungan', 'value' => $product->kandungan ?? '-'],
                    ['label' => 'Detail Kandungan', 'value' => $product->kandungan_detail ?? '-', 'full' => true],
                    ['label' => 'Fungsi Utama', 'value' => $product->fungsi_utama ?? '-', 'full' => true],
                    ['label' => 'Efek Penggunaan', 'value' => $product->efek_penggunaan ?? '-', 'full' => true],
                    ['label' => 'Cara Aplikasi', 'value' => $product->cara_aplikasi ?? '-', 'full' => true],
                    ['label' => 'Jadwal / Umur Aplikasi', 'value' => $product->jadwal_umur_aplikasi ?? '-', 'full' => true],
                ]
                : [
                    ['label' => 'Kode', 'value' => $product->kode ?? '-'],
                    ['label' => 'Skor', 'value' => number_format((float) ($item->nilai_vi ?? 0), 4)],
                    ['label' => 'Jenis', 'value' => $product->jenis ?? '-'],
                    ['label' => 'Dosis', 'value' => $product->dosis ?? '-'],
                    ['label' => 'Takaran per Hektar', 'value' => ($product->dosis_per_hektar ?? '-') . ' ' . ($product->satuan_dosis ?? '')],
                    ['label' => 'Frekuensi', 'value' => $product->frekuensi_aplikasi ?? '-'],
                    ['label' => 'Bahan Aktif', 'value' => $product->bahan_aktif ?? '-', 'full' => true],
                    ['label' => 'Detail Kandungan', 'value' => $product->kandungan_detail ?? '-', 'full' => true],
                    ['label' => 'Fungsi', 'value' => $product->fungsi ?? '-', 'full' => true],
                    ['label' => 'Efek Penggunaan', 'value' => $product->efek_penggunaan ?? '-', 'full' => true],
                    ['label' => 'Cara Aplikasi', 'value' => $product->cara_aplikasi ?? '-', 'full' => true],
                    ['label' => 'Jadwal / Umur Aplikasi', 'value' => $product->jadwal_umur_aplikasi ?? '-', 'full' => true],
                ];
            $linkedSymptoms = collect($product->gejala_cocok ?? []);
        @endphp
        <div class="col-12">
            <div class="analysis-card p-3 p-lg-4">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-4">
                        @if(optional($product)->gambar_url)
                        <img src="{{ $product->gambar_url }}" alt="{{ $product->nama }}" class="analysis-media">
                        @else
                        <div class="analysis-media-empty">
                            <i class="bi {{ $section['key'] === 'pupuk' ? 'bi-bag' : 'bi-shield' }}"></i>
                        </div>
                        @endif
                    </div>
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-warning-subtle text-warning-emphasis border">#{{ $item->peringkat }}</span>
                            <span class="badge text-bg-{{ ExpertSystemPresenter::confidenceTone($item->nilai_vi) }}">
                                {{ ExpertSystemPresenter::confidenceLabel($item->nilai_vi) }}
                            </span>
                        </div>
                        <h3 class="h5 fw-bold mb-1">{{ $product->nama ?? '-' }}</h3>
                        <p class="text-muted mb-3">{{ $description }}</p>
                        <div class="product-detail-grid">
                            @foreach($detailRows as $row)
                            <div class="product-detail-item {{ !empty($row['full']) ? 'full' : '' }}">
                                <div class="product-detail-label">{{ $row['label'] }}</div>
                                <div class="product-detail-value">{{ $row['value'] }}</div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <div class="fw-semibold mb-2">Gejala terkait</div>
                            @if($linkedSymptoms->isNotEmpty())
                            <div class="linked-symptoms">
                                @foreach($linkedSymptoms as $gejala)
                                <span class="linked-symptom">
                                    {{ data_get($gejala, 'kode') ? data_get($gejala, 'kode') . ' · ' : '' }}{{ data_get($gejala, 'nama_gejala') }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <div class="small text-muted">Belum ada gejala pendukung yang ditautkan pada item ini.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endforeach

<div class="d-flex flex-wrap gap-2">
    @if($isPreview)
    <a href="{{ route('login') }}" class="btn btn-outline-success">Login untuk Simpan Hasil</a>
    <a href="{{ route('user.diagnosis.index') }}" class="btn btn-spk">Diagnosis Lagi</a>
    @else
    <a href="{{ route('user.riwayat.index') }}" class="btn btn-outline-secondary">Buka Riwayat</a>
    @endif
</div>
@guest
</div>
@endguest
@endsection
