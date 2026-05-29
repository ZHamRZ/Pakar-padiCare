@extends('layouts.app')

@section('title', 'Hasil Rekomendasi')
@section('page-title', 'Hasil Rekomendasi')

@push('styles')
<style>
    .result-hero {
        background: linear-gradient(135deg, var(--soft-bg) 0%, var(--main-bg) 100%);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
    }
    .summary-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        background: var(--card);
        box-shadow: var(--shadow-md);
    }
    .insight-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .4rem .75rem;
        border-radius: 999px;
        background: var(--card);
        border: 1px solid var(--border-light);
        color: var(--primary);
        font-size: .85rem;
        font-weight: 600;
    }
    .card {
        border: none;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
    }
    .card-header {
        background: var(--card);
        border-bottom: 1px solid var(--border-light);
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        color: var(--heading);
    }
    .card-body {
        padding: 1.5rem;
    }
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-weight: 800;
        font-size: .85rem;
    }
    .rank-1 { background: #dcfce7; color: #166534; }
    .rank-2 { background: #e0f2fe; color: #075985; }
    .rank-3 { background: #fef3c7; color: #92400e; }
    .rank-other { background: #f1f5f9; color: #475569; }
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 8px 0;
        border-bottom: 1px dashed var(--border);
        font-size: .82rem;
        gap: 10px;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { 
        color: var(--muted-text); 
        flex-shrink: 0; 
        min-width: 100px;
    }
    .detail-value { 
        font-weight: 600; 
        color: var(--heading); 
        word-break: break-word; 
        overflow-wrap: break-word; 
        text-align: right;
        max-width: 60%;
    }
    .product-mini-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px;
        background: var(--card);
        transition: box-shadow .2s;
    }
    .product-mini-card:hover {
        box-shadow: var(--shadow-md);
    }
</style>
@endpush

@section('content')
@php
    use App\Support\ExpertSystemPresenter;

    $isPreview = $isPreview ?? false;
    $sortedPupuk = $rekomendasi->detailPupuk->sortBy('peringkat')->values();
    $sortedPestisida = $rekomendasi->detailPestisida->sortBy('peringkat')->values();
    $topPupuk = $sortedPupuk->first();
    $topPestisida = $sortedPestisida->first();
    $gejalaTerpilih = collect(data_get($rekomendasi, 'preferensi_pengguna.gejala_terpilih', []));
    $luasLahan = data_get($rekomendasi, 'preferensi_pengguna.luas_lahan', 0);
    $topScore = max((float) ($topPupuk->nilai_vi ?? 0), (float) ($topPestisida->nilai_vi ?? 0));
    $warning = ExpertSystemPresenter::lowConfidenceMessage($topScore);

    // Helper untuk format harga
    $formatUnitPrice = function($price, $unitLabel) {
        if (!$price) return '-';
        return 'Rp ' . number_format($price, 0, ',', '.') . ' / ' . $unitLabel;
    };

    // Helper untuk format kuantitas
    $formatQuantity = function($amount, $unit) {
        if ($unit === 'g' && $amount >= 1000) return number_format($amount / 1000, 2, ',', '.') . ' kg';
        if ($unit === 'kg' && $amount >= 1000) return number_format($amount / 1000, 2, ',', '.') . ' Ton';
        if ($unit === 'ml' && $amount >= 1000) return number_format($amount / 1000, 2, ',', '.') . ' L';
        return number_format($amount, 2, ',', '.') . ' ' . $unit;
    };

    // Hitung total biaya
    $totalBiaya = 0;
    $totalBiayaPupuk = 0;
    $totalBiayaPestisida = 0;
@endphp

@guest
<div class="container py-4">
@endguest

{{-- ── Hero Section ── --}}
<div class="result-hero p-4 p-lg-5 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-success-subtle text-success border border-success-subtle mb-3">
                <i class="bi bi-check-circle-fill me-1"></i>Hasil Diagnosis
            </span>
            <h2 class="fw-bold mb-2">{{ $rekomendasi->penyakit->nama }}</h2>
            <p class="text-muted mb-3">
                Rekomendasi penanganan berdasarkan analisis sistem pakar untuk penyakit yang terdeteksi.
            </p>
            <div class="d-flex flex-wrap gap-2">
                @if($rekomendasi->preferensi_label)
                <span class="insight-chip"><i class="bi bi-sliders"></i>{{ $rekomendasi->preferensi_label }}</span>
                @endif
                @if($luasLahan > 0)
                <span class="insight-chip"><i class="bi bi-rulers"></i>Luas Lahan: {{ number_format($luasLahan, 0, ',', '.') }} m²</span>
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="summary-card p-4 h-100">
                <div class="small text-muted mb-2">Skor Rekomendasi</div>
                <div class="fw-bold fs-3 mb-1" style="color: var(--primary);">{{ ExpertSystemPresenter::percent($topScore) }}</div>
                <div class="small text-muted mb-3">{{ ExpertSystemPresenter::confidenceLabel($topScore) }}</div>
                <x-expert-system.confidence-bar :value="$topScore" />
            </div>
        </div>
    </div>
</div>

@if($isPreview)
<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle-fill me-2"></i>Anda sedang melihat hasil tanpa login. Hasil ini belum disimpan ke riwayat pribadi.
</div>
@endif

@if($warning)
<div class="alert alert-warning mb-4">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $warning }}
</div>
@endif

{{-- ── Diagnosis Info ── --}}
<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="summary-card p-4 h-100">
            <div class="fw-semibold mb-3"><i class="bi bi-clipboard2-pulse me-2 text-primary"></i>Gejala yang Dianalisis</div>
            @if($gejalaTerpilih->isEmpty())
            <div class="small text-muted">Data gejala belum tersedia.</div>
            @else
            <div class="d-flex flex-wrap gap-2">
                @foreach($gejalaTerpilih as $gejala)
                <span class="badge rounded-pill bg-success-subtle text-success" style="white-space: normal; word-break: break-word; text-align: left; padding: 6px 10px;">
                    {{ data_get($gejala, 'kode') ? data_get($gejala, 'kode') . ' · ' : '' }}{{ data_get($gejala, 'nama_gejala') }}
                </span>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    <div class="col-xl-4">
        <div class="summary-card p-4 h-100">
            <div class="fw-semibold mb-3"><i class="bi bi-rulers me-2 text-primary"></i>Informasi Lahan</div>
            @if($luasLahan > 0)
            <div class="detail-row">
                <span class="detail-label">Luas Lahan</span>
                <span class="detail-value">{{ number_format($luasLahan, 0, ',', '.') }} m²</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dalam Hektar</span>
                <span class="detail-value">{{ number_format($luasLahan / 10000, 4) }} ha</span>
            </div>
            @else
            <div class="small text-muted">Luas lahan tidak diinput.</div>
            @endif
        </div>
    </div>
    <div class="col-xl-4">
        <div class="summary-card p-4 h-100">
            <div class="fw-semibold mb-3"><i class="bi bi-calculator me-2 text-primary"></i>Estimasi Biaya</div>
            @php
                $luasHa = $luasLahan > 0 ? $luasLahan / 10000 : 0;
                $pupukCalculations = [];
                $pestisidaCalculations = [];
            @endphp
            @if($luasHa > 0)
                @foreach($sortedPupuk->take(6) as $item)
                    @php
                        $pupukData = $item->pupuk;
                        $calc = \App\Helpers\UnitConverter::hitungBiayaAkurat(
                            $luasLahan,
                            (float) ($pupukData->dosis_per_hektar ?? 0),
                            $pupukData->satuan_dosis ?? 'g',
                            (float) ($pupukData->harga_per_unit ?? 0),
                            (float) ($pupukData->satuan_harga_qty ?? 1),
                            $pupukData->satuan_harga_unit ?? 'kg',
                            $pupukData->frekuensi_aplikasi ?? 1
                        );
                        $totalBiayaPupuk += $calc['total_biaya'];
                        $pupukCalculations[] = array_merge(['nama' => $pupukData->nama], $calc);
                    @endphp
                @endforeach
                @foreach($sortedPestisida->take(6) as $item)
                    @php
                        $pestisidaData = $item->pestisida;
                        $calc = \App\Helpers\UnitConverter::hitungBiayaAkurat(
                            $luasLahan,
                            (float) ($pestisidaData->dosis_per_hektar ?? 0),
                            $pestisidaData->satuan_dosis ?? 'ml',
                            (float) ($pestisidaData->harga_per_unit ?? 0),
                            (float) ($pestisidaData->satuan_harga_qty ?? 1),
                            $pestisidaData->satuan_harga_unit ?? 'ml',
                            $pestisidaData->frekuensi_aplikasi ?? 1
                        );
                        $totalBiayaPestisida += $calc['total_biaya'];
                        $pestisidaCalculations[] = array_merge(['nama' => $pestisidaData->nama], $calc);
                    @endphp
                @endforeach
                <div class="detail-row">
                    <span class="detail-label">Total Pupuk</span>
                    <span class="detail-value" style="color: #166534;">Rp {{ number_format($totalBiayaPupuk, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Pestisida</span>
                    <span class="detail-value" style="color: #92400e;">Rp {{ number_format($totalBiayaPestisida, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row" style="border-top: 2px solid var(--primary); padding-top: 12px; margin-top: 8px;">
                    <span class="detail-label fw-bold">TOTAL ESTIMASI</span>
                    <span class="detail-value fw-bold fs-5" style="color: var(--primary);">Rp {{ number_format($totalBiayaPupuk + $totalBiayaPestisida, 0, ',', '.') }}</span>
                </div>
                <div class="small text-muted mt-2" style="font-size: 0.65rem;">
                    <i class="bi bi-info-circle me-1"></i>Estimasi biaya bahan untuk 1 kali aplikasi.
                </div>
            @else
            <div class="small text-muted">Masukkan luas lahan untuk menghitung estimasi biaya.</div>
            @endif
        </div>
    </div>
</div>

{{-- ── Rekomendasi Utama (Top 1) ── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bag-fill text-success"></i>
                <span>Rekomendasi Pupuk Utama</span>
                @if($topPupuk)
                <span class="badge bg-success ms-auto">Peringkat #{{ $topPupuk->peringkat }}</span>
                @endif
            </div>
            <div class="card-body">
                @if($topPupuk)
                <div class="d-flex gap-3 mb-3">
                    @if($topPupuk->pupuk->gambar_url)
                    <img src="{{ $topPupuk->pupuk->gambar_url }}" alt="{{ $topPupuk->pupuk->nama }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius-md);">
                    @else
                    <div style="width: 80px; height: 80px; background: var(--main-bg); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-bag fs-2 text-muted"></i>
                    </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-1">{{ $topPupuk->pupuk->nama }}</h5>
                        <div class="small text-muted mb-2">{{ $topPupuk->pupuk->fungsi_utama ?? '-' }}</div>
                        <span class="badge bg-success-subtle text-success">{{ ExpertSystemPresenter::percent($topPupuk->nilai_vi) }}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Dosis per Ha</span>
                    <span class="detail-value">{{ $formatQuantity($topPupuk->pupuk->dosis_per_hektar ?? 0, $topPupuk->pupuk->satuan_dosis ?? 'g') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Harga</span>
                    <span class="detail-value">{{ $formatUnitPrice($topPupuk->pupuk->harga_per_unit, $topPupuk->pupuk->satuan_harga_qty . ' ' . $topPupuk->pupuk->satuan_harga_unit) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Cara Aplikasi</span>
                    <span class="detail-value">{{ $topPupuk->pupuk->cara_aplikasi ?? '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Frekuensi</span>
                    <span class="detail-value">{{ $topPupuk->pupuk->frekuensi_aplikasi ?? '-' }}</span>
                </div>
                @else
                <div class="text-muted text-center py-4">Tidak ada rekomendasi pupuk.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-shield-fill-check text-warning"></i>
                <span>Rekomendasi Pestisida Utama</span>
                @if($topPestisida)
                <span class="badge bg-warning text-dark ms-auto">Peringkat #{{ $topPestisida->peringkat }}</span>
                @endif
            </div>
            <div class="card-body">
                @if($topPestisida)
                <div class="d-flex gap-3 mb-3">
                    @if($topPestisida->pestisida->gambar_url)
                    <img src="{{ $topPestisida->pestisida->gambar_url }}" alt="{{ $topPestisida->pestisida->nama }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius-md);">
                    @else
                    <div style="width: 80px; height: 80px; background: var(--main-bg); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-shield fs-2 text-muted"></i>
                    </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-1">{{ $topPestisida->pestisida->nama }}</h5>
                        <div class="small text-muted mb-2">{{ $topPestisida->pestisida->fungsi ?? '-' }}</div>
                        <span class="badge bg-warning-subtle text-warning">{{ ExpertSystemPresenter::percent($topPestisida->nilai_vi) }}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Dosis per Ha</span>
                    <span class="detail-value">{{ $formatQuantity($topPestisida->pestisida->dosis_per_hektar ?? 0, $topPestisida->pestisida->satuan_dosis ?? 'ml') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Harga</span>
                    <span class="detail-value">{{ $formatUnitPrice($topPestisida->pestisida->harga_per_unit, $topPestisida->pestisida->satuan_harga_qty . ' ' . $topPestisida->pestisida->satuan_harga_unit) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Efek Penggunaan</span>
                    <span class="detail-value">{{ $topPestisida->pestisida->efek_penggunaan ?? '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Cara Aplikasi</span>
                    <span class="detail-value">{{ $topPestisida->pestisida->cara_aplikasi ?? '-' }}</span>
                </div>
                @else
                <div class="text-muted text-center py-4">Tidak ada rekomendasi pestisida.</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Ranking Pupuk (Top 6) ── --}}
@if($sortedPupuk->isNotEmpty())
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-trophy-fill text-success"></i>
        <span>Ranking Rekomendasi Pupuk (Top 6)</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($sortedPupuk->take(6) as $item)
            <div class="col-md-6 col-lg-4">
                <div class="product-mini-card">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="rank-badge {{ $item->peringkat <= 3 ? 'rank-' . $item->peringkat : 'rank-other' }}">
                            {{ $item->peringkat }}
                        </span>
                        <div class="fw-semibold" style="font-size: .9rem;">{{ $item->pupuk->nama }}</div>
                        <span class="badge bg-success-subtle text-success ms-auto" style="font-size: .7rem;">{{ ExpertSystemPresenter::percent($item->nilai_vi) }}</span>
                    </div>
                    <div class="small text-muted mb-2">{{ $item->pupuk->fungsi_utama ?? '-' }}</div>
                    <div class="detail-row" style="font-size: .75rem; padding: 4px 0;">
                        <span class="detail-label">Dosis</span>
                        <span class="detail-value">{{ $formatQuantity($item->pupuk->dosis_per_hektar ?? 0, $item->pupuk->satuan_dosis ?? 'g') }}</span>
                    </div>
                    <div class="detail-row" style="font-size: .75rem; padding: 4px 0;">
                        <span class="detail-label">Harga</span>
                        <span class="detail-value">{{ $formatUnitPrice($item->pupuk->harga_per_unit, $item->pupuk->satuan_harga_qty . ' ' . $item->pupuk->satuan_harga_unit) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Ranking Pestisida (Top 6) ── --}}
@if($sortedPestisida->isNotEmpty())
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-trophy-fill text-warning"></i>
        <span>Ranking Rekomendasi Pestisida (Top 6)</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($sortedPestisida->take(6) as $item)
            <div class="col-md-6 col-lg-4">
                <div class="product-mini-card">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="rank-badge {{ $item->peringkat <= 3 ? 'rank-' . $item->peringkat : 'rank-other' }}">
                            {{ $item->peringkat }}
                        </span>
                        <div class="fw-semibold" style="font-size: .9rem;">{{ $item->pestisida->nama }}</div>
                        <span class="badge bg-warning-subtle text-warning ms-auto" style="font-size: .7rem;">{{ ExpertSystemPresenter::percent($item->nilai_vi) }}</span>
                    </div>
                    <div class="small text-muted mb-2">{{ $item->pestisida->fungsi ?? '-' }}</div>
                    <div class="detail-row" style="font-size: .75rem; padding: 4px 0;">
                        <span class="detail-label">Dosis</span>
                        <span class="detail-value">{{ $formatQuantity($item->pestisida->dosis_per_hektar ?? 0, $item->pestisida->satuan_dosis ?? 'ml') }}</span>
                    </div>
                    <div class="detail-row" style="font-size: .75rem; padding: 4px 0;">
                        <span class="detail-label">Harga</span>
                        <span class="detail-value">{{ $formatUnitPrice($item->pestisida->harga_per_unit, $item->pestisida->satuan_harga_qty . ' ' . $item->pestisida->satuan_harga_unit) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Action Buttons ── --}}
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('user.riwayat.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Lihat Riwayat
    </a>
    <a href="{{ route('user.rekomendasi.detail', $rekomendasi->id) }}" class="btn btn-outline-success">
        <i class="bi bi-graph-up me-1"></i>Lihat Detail Analisis
    </a>
    <a href="{{ route('user.rekomendasi.cetak', $rekomendasi->id) }}" target="_blank" class="btn btn-spk">
        <i class="bi bi-printer me-1"></i>Cetak Hasil
    </a>
    <a href="{{ route('user.diagnosis.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-repeat me-1"></i>Diagnosis Lagi
    </a>
</div>

@guest
</div>
@endguest
@endsection
