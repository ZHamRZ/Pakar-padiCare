@extends('layouts.app')

@section('title', 'Preview Rekomendasi')
@section('page-title', 'Preview Rekomendasi')

@push('styles')
<style>
    /* ── Reset & Base ──────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Hero Banner ───────────────────────────────── */
    .result-hero {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 50%, #f8fafc 100%);
        border: 1px solid #bbf7d0;
        border-radius: 20px;
    }
    .summary-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .75rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #d1fae5;
        color: #166534;
        font-size: .82rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ── Batch Card ────────────────────────────────── */
    .batch-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    /* ── Disease Sidebar ───────────────────────────── */
    .disease-sidebar {
        background: linear-gradient(160deg, #f0fdf4 0%, #f8fafc 100%);
        border-radius: 16px;
        padding: 20px;
        height: 100%;
    }
    .disease-media {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 14px;
        background: #e2e8f0;
    }
    .media-empty {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        color: #94a3b8;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        gap: 8px;
    }
    .disease-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 14px;
    }
    .disease-meta-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
    }
    .disease-meta-item .label {
        font-size: .72rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 3px;
    }
    .disease-meta-item .value {
        font-size: .84rem;
        font-weight: 700;
        color: #0f172a;
    }
    .disease-meta-item.full-width {
        grid-column: 1 / -1;
    }

    /* ── Symptom Badges ────────────────────────────── */
    .symptom-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .symptom-badge {
        font-size: .78rem;
        padding: .3rem .65rem;
        border-radius: 8px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        font-weight: 500;
    }

    /* ── Section Label ─────────────────────────────── */
    .section-label {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    /* ── Product Cards ─────────────────────────────── */
    .product-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        transition: box-shadow .2s ease, transform .2s ease;
        display: flex;
        flex-direction: column;
        height: auto;
        align-self: start;
        margin-bottom: 0;
    }
    .product-card:hover {
        box-shadow: 0 8px 28px rgba(15,23,42,.08);
        transform: translateY(-2px);
    }
    .product-card-img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        background: #f8fafc;
    }
    .product-card-img-empty {
        width: 100%;
        height: 140px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 2rem;
    }
    .product-card-body {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .product-rank-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: .72rem;
        font-weight: 700;
        padding: .25rem .55rem;
        border-radius: 6px;
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde68a;
        margin-bottom: 6px;
    }
    .product-rank-badge.rank-1 {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }
    .product-type-tag {
        font-size: .7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        margin-bottom: 4px;
    }
    .product-name {
        font-size: .95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
        line-height: 1.3;
    }
    .product-desc {
        font-size: .8rem;
        color: #64748b;
        line-height: 1.5;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-score-row {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .score-bar-wrap {
        flex: 1;
        height: 5px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .score-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #16a34a, #22c55e);
        transition: width .6s ease;
    }
    .score-bar-fill.medium {
        background: linear-gradient(90deg, #ca8a04, #eab308);
    }
    .score-label {
        font-size: .75rem;
        font-weight: 700;
        color: #475569;
        white-space: nowrap;
    }

    /* ── Detail Toggle ─────────────────────────────── */
    .detail-toggle {
        margin-top: 0;
        border-top: 1px solid #f1f5f9;
    }
    .detail-toggle summary {
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        color: #16a34a;
        background: linear-gradient(to right, #f0fdf4, #fff);
        font-size: .82rem;
        font-weight: 700;
        cursor: pointer;
        user-select: none;
        transition: all .2s ease;
        border-radius: 0 0 12px 12px;
    }
    .detail-toggle summary::-webkit-details-marker { display: none; }
    .detail-toggle summary:hover {
        background: linear-gradient(to right, #dcfce7, #f0fdf4);
        padding-left: 18px;
    }
    .detail-toggle[open] summary {
        background: linear-gradient(to right, #dcfce7, #f0fdf4);
        border-bottom: 1px solid #bbf7d0;
        border-radius: 0;
    }
    .detail-toggle summary .chevron {
        transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: .75rem;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f0fdf4;
    }
    .detail-toggle[open] summary .chevron {
        transform: rotate(180deg);
        background: #16a34a;
        color: #fff;
    }
    .detail-panel {
        padding: 0;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out, padding 0.3s ease;
    }
    .detail-toggle[open] .detail-panel {
        max-height: 500px;
        overflow-y: auto;
        padding: 14px;
    }
    .detail-panel::-webkit-scrollbar { width: 5px; }
    .detail-panel::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 3px; }
    .detail-panel::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
    .detail-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 8px;
    }
    .detail-row {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        transition: all .2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }
    .detail-row:hover {
        border-color: #16a34a;
        box-shadow: 0 2px 6px rgba(22, 163, 74, 0.1);
        transform: translateY(-1px);
    }
    .detail-row.full  { grid-column: 1 / -1; }
    .detail-row.span2 { grid-column: span 2; }
    .detail-row .dl {
        font-size: .68rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .detail-row .dl::before {
        content: '';
        width: 3px;
        height: 3px;
        background: #16a34a;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .detail-row .dv {
        font-size: .8rem;
        color: #1e293b;
        font-weight: 500;
        word-break: break-word;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        hyphens: auto;
    }
    .detail-row.full .dv,
    .detail-row.span2 .dv {
        -webkit-line-clamp: 4;
        font-size: .82rem;
    }

    /* Preference Adjustment Badge */
    .adjustment-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.65rem;
        font-weight: 700;
        margin-left: 6px;
    }
    .adjustment-badge.positive { background: #dcfce7; color: #166534; }
    .adjustment-badge.negative { background: #fee2e2; color: #991b1b; }

    /* ── Action Buttons ────────────────────────────── */
    .action-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 20px 0;
    }
    .action-bar .btn {
        padding: .55rem 1.2rem;
        border-radius: 10px;
        font-size: .88rem;
        font-weight: 600;
    }

    /* ── Product Grid ───────────────────────────────── */
    .calc-info-box {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid #fde68a;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .calc-info-box .icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f59e0b;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .calc-info-box .info-title {
        font-weight: 700;
        color: #92400e;
        font-size: .88rem;
    }
    .calc-info-box .info-text {
        color: #78350f;
        font-size: .8rem;
        line-height: 1.55;
    }
    .product-grid-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
        align-items: start;
    }
    .product-empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px;
        color: #64748b;
    }

    /* ── Product Checkbox ───────────────────────────── */
    .product-check-wrap {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
    }
    .product-check {
        width: 22px;
        height: 22px;
        accent-color: #16a34a;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        border-radius: 4px;
    }
    .product-card {
        position: relative;
    }
    .product-card:not(:has(.product-check:checked)) {
        opacity: 0.65;
    }
    .product-card:has(.product-check:checked) {
        opacity: 1;
        box-shadow: 0 0 0 2px #16a34a, 0 8px 28px rgba(22,163,74,0.15);
    }

    /* ── Selection Summary ──────────────────────────── */
    .selection-summary {
        background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 20px;
        margin-top: 24px;
    }
    .selected-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #d1fae5;
        font-size: 0.85rem;
    }
    .selected-item-row:last-child {
        border-bottom: none;
    }
    .selected-item-detail {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 4px;
        line-height: 1.6;
    }
    .selected-item-detail div {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .selected-item-detail i {
        font-size: 0.68rem;
        opacity: 0.7;
    }
    .selected-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        margin-top: 10px;
        border-top: 2px solid #16a34a;
        font-weight: 800;
        font-size: 1.05rem;
        color: #166534;
    }
    .category-subtotal {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        margin-top: 8px;
        border-top: 1px solid #bbf7d0;
        font-size: 0.82rem;
        font-weight: 700;
        color: #166534;
    }

    /* ── Mobile Responsive ─────────────────────────── */
    @media (max-width: 991px) {
        .disease-meta-grid { grid-template-columns: repeat(3, 1fr); }
        .batch-card { border-radius: 16px; }
        .product-grid-wrapper { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
        .detail-list { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
    }
    @media (max-width: 767px) {
        .result-hero { border-radius: 14px; padding: 18px !important; }
        .result-hero h2 { font-size: 1.25rem; }
        .batch-card { border-radius: 12px; padding: 16px !important; }
        .disease-media, .media-empty { height: 180px; }
        .product-card-img, .product-card-img-empty { height: 110px; }
        .disease-meta-grid { grid-template-columns: 1fr 1fr; }
        .detail-list { grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); }
        .detail-row.full  { grid-column: 1 / -1; }
        .detail-row.span2 { grid-column: 1 / -1; }
        .product-grid-wrapper { grid-template-columns: 1fr; gap: 16px; }
        .action-bar .btn { flex: 1; min-width: 140px; text-align: center; }
        .section-label { font-size: .68rem; }
        .detail-toggle[open] .detail-panel { max-height: 450px; }
        .detail-row .dv { -webkit-line-clamp: 3; }
    }
    @media (max-width: 480px) {
        .disease-meta-grid { grid-template-columns: 1fr 1fr; }
        .summary-chip { font-size: .76rem; padding: .3rem .6rem; }
        .product-name { font-size: .88rem; }
        .detail-list { grid-template-columns: 1fr; }
        .detail-row .dv { -webkit-line-clamp: 3; }
    }
</style>
@endpush

@section('content')
@php
    use App\Support\ExpertSystemPresenter;

    $isPreview = $isPreview ?? true;
    $selectedSymptoms = collect(data_get($hasilDiagnosa->first(), 'rekomendasi.preferensi_pengguna.gejala_terpilih', []));
    $formatCurrency = static function ($value) {
        return is_numeric($value) && (float) $value > 0
            ? 'Rp ' . number_format((float) $value, 0, ',', '.')
            : '-';
    };
    $formatUnitPrice = static function ($value, $unit = null) use ($formatCurrency) {
        $formatted = $formatCurrency($value);

        if ($formatted === '-') {
            return '-';
        }

        return trim($formatted . ($unit ? ' / ' . $unit : ''));
    };
@endphp
@guest
<div class="container py-4">
@endguest

{{-- ── Hero ──────────────────────────────────────────── --}}
<div class="result-hero p-4 p-lg-5 mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-success-subtle text-success border border-success-subtle mb-3">
                <i class="bi bi-cpu me-1"></i>Analisis Sistem Pakar
            </span>
            <h2 class="fw-bold mb-2">Preview Hasil Rekomendasi</h2>
            <p class="text-muted mb-3" style="font-size:.9rem;">
                Tampilan ini menampilkan rekomendasi utama agar Anda bisa langsung membaca hasil diagnosa dengan cepat.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="summary-chip">
                    <i class="bi bi-clipboard2-pulse"></i>
                    {{ $hasilDiagnosa->count() }} penyakit dipilih
                </span>
                @if(data_get($hasilDiagnosa->first(), 'rekomendasi.preferensi_label'))
                <span class="summary-chip">
                    <i class="bi bi-sliders"></i>
                    {{ data_get($hasilDiagnosa->first(), 'rekomendasi.preferensi_label') }}
                </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Login Notice ───────────────────────────────────── --}}
@if($isSavedBatch)
<div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill text-success"></i>
    Hasil ini sudah dihitung dan disimpan ke riwayat akun Anda.
</div>
@else
<div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-info-circle-fill"></i>
    Anda sedang melihat hasil tanpa login. Silakan <a href="{{ route('login') }}" class="alert-link">login</a> untuk menyimpan hasil ini.
</div>
@endif

{{-- ── Per-Disease Batch ──────────────────────────────── --}}
@foreach($hasilDiagnosa as $hasil)
@php
    $rekomendasi      = $hasil['rekomendasi'];
    $sortedPupuk      = $rekomendasi->detailPupuk->sortBy('peringkat')->values();
    $sortedPestisida  = $rekomendasi->detailPestisida->sortBy('peringkat')->values();
    
    // CRITICAL FIX: Limit hanya 2 teratas sesuai spesifikasi
    $recommendedPupuk = $sortedPupuk->take(2)->values();
    $recommendedPestisida = $sortedPestisida->take(2)->values();
    
    $topPupuk         = $recommendedPupuk->first();
    $topPestisida     = $recommendedPestisida->first();
    $topScore = max((float) ($topPupuk->nilai_vi ?? 0), (float) ($topPestisida->nilai_vi ?? 0));
@endphp

<div class="batch-card p-3 p-lg-4 mb-4">
    <div class="row g-4">

        {{-- ── Left: Disease Info ──────────────────── --}}
        <div class="col-xl-4 col-lg-4">
            <div class="disease-sidebar">

                {{-- Image --}}
                @if($rekomendasi->penyakit->gambar_url)
                    <img src="{{ $rekomendasi->penyakit->gambar_url }}"
                         alt="{{ $rekomendasi->penyakit->nama }}"
                         class="disease-media">
                @else
                    <div class="media-empty">
                        <i class="bi bi-virus fs-2"></i>
                        <span style="font-size:.75rem; color:#94a3b8;">Tidak ada gambar</span>
                    </div>
                @endif

                {{-- Disease Name & Confidence --}}
                <div class="mt-3 mb-2 d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="text-muted" style="font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">Penyakit Terpilih</div>
                        <div class="fw-bold" style="font-size:1.05rem; color:#0f172a; line-height:1.3;">
                            {{ $rekomendasi->penyakit->nama }}
                        </div>
                    </div>
                    <span class="badge text-bg-{{ ExpertSystemPresenter::confidenceTone($topScore) }} flex-shrink-0">
                        {{ ExpertSystemPresenter::confidenceLabel($topScore) }}
                    </span>
                </div>

                {{-- Confidence Bar --}}
                <div class="mb-1" style="font-size:.78rem; color:#64748b;">
                    Skor: {{ ExpertSystemPresenter::percent($topScore) }}
                </div>
                <x-expert-system.confidence-bar :value="$topScore" />

                {{-- Low confidence warning --}}
                @if(ExpertSystemPresenter::lowConfidenceMessage($topScore))
                <div class="alert alert-warning small mt-2 mb-0 py-2 px-3 rounded-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    {{ ExpertSystemPresenter::lowConfidenceMessage($topScore) }}
                </div>
                @endif

                {{-- Meta grid --}}
                <div class="disease-meta-grid">
                    <div class="disease-meta-item">
                        <div class="label">Pupuk</div>
                        <div class="value">{{ $recommendedPupuk->count() }} opsi</div>
                    </div>
                    <div class="disease-meta-item">
                        <div class="label">Pestisida</div>
                        <div class="value">{{ $recommendedPestisida->count() }} opsi</div>
                    </div>
                    @if($rekomendasi->penyakit->kode ?? null)
                    <div class="disease-meta-item full-width">
                        <div class="label">Kode Penyakit</div>
                        <div class="value">{{ $rekomendasi->penyakit->kode }}</div>
                    </div>
                    @endif
                </div>

                {{-- ── Alasan Rekomendasi ── --}}
                <div class="mt-4">
                    {{-- Alasan Pupuk --}}
                    @if($recommendedPupuk->isNotEmpty())
                    <div class="p-3 rounded-3 mb-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #bbf7d0;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-bag-fill text-success"></i>
                            <span style="font-size:.78rem; font-weight:700; color:#166534;">Alasan Rekomendasi Pupuk</span>
                        </div>
                        <ul class="mb-0 ps-3" style="font-size:.75rem; color:#166534; line-height:1.6;">
                            @foreach($recommendedPupuk as $item)
                            <li class="mb-2">
                                <strong>{{ $item->pupuk->nama }}:</strong> 
                                {{ $item->pupuk->fungsi_utama ?? 'Memenuhi kebutuhan nutrisi tanaman' }}. 
                                @if($item->pupuk->cara_aplikasi)
                                Cara aplikasi: {{ $item->pupuk->cara_aplikasi }}.
                                @endif
                                @if($item->pupuk->frekuensi_aplikasi)
                                Frekuensi: {{ $item->pupuk->frekuensi_aplikasi }}.
                                @endif
                            </li>
                            @endforeach
                            <li class="mt-2 pt-2" style="border-top: 1px dashed #bbf7d0;">
                                Mendukung pemulihan tanaman dari kondisi stres akibat penyakit <strong>{{ $rekomendasi->penyakit->nama }}</strong>
                            </li>
                        </ul>
                    </div>
                    @endif

                    {{-- Alasan Pestisida --}}
                    @if($recommendedPestisida->isNotEmpty())
                    <div class="p-3 rounded-3" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-fill-check text-warning"></i>
                            <span style="font-size:.78rem; font-weight:700; color:#92400e;">Alasan Rekomendasi Pestisida</span>
                        </div>
                        <ul class="mb-0 ps-3" style="font-size:.75rem; color:#92400e; line-height:1.6;">
                            @foreach($recommendedPestisida as $item)
                            <li class="mb-2">
                                <strong>{{ $item->pestisida->nama }}:</strong> 
                                {{ $item->pestisida->fungsi ?? 'Mengendalikan hama dan penyakit' }}. 
                                @if($item->pestisida->efek_penggunaan)
                                Efek: {{ $item->pestisida->efek_penggunaan }}.
                                @endif
                                @if($item->pestisida->cara_aplikasi)
                                Cara aplikasi: {{ $item->pestisida->cara_aplikasi }}.
                                @endif
                            </li>
                            @endforeach
                            <li class="mt-2 pt-2" style="border-top: 1px dashed #fde68a;">
                                Direkomendasikan berdasarkan kecocokan dengan penyakit <strong>{{ $rekomendasi->penyakit->nama }}</strong> yang terdeteksi
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>

                {{-- ── Dosage Calculator Results ─ --}}
                @if($luasLahan > 0)
                <div class="mt-4 p-3 rounded-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%); border: 1px solid #bbf7d0;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-calculator text-success"></i>
                        <span style="font-size:.78rem; font-weight:700; color:#166534; text-transform:uppercase; letter-spacing:.04em;">Kebutuhan Lahan {{ number_format($luasLahan, 0, ',', '.') }} m²</span>
                    </div>
                    
                    @php
                        $totalBiayaPupuk = 0;
                        $totalBiayaPestisida = 0;
                    @endphp
                    
                    {{-- Pupuk --}}
                    @if($recommendedPupuk->isNotEmpty())
                    <div class="mb-3">
                        <div style="font-size:.72rem; font-weight:700; color:#166534; margin-bottom:8px;">
                            <i class="bi bi-bag-fill me-1"></i>PUPUK
                        </div>
                        @foreach($recommendedPupuk->take(2) as $item)
                        @php
                            $pupuk = $item->pupuk;
                            // Fallback: load from database if preview data doesn't have dosis_per_hektar
                            if (!$pupuk->dosis_per_hektar && $pupuk->kode) {
                                $dbPupuk = \App\Models\Pupuk::where('kode', $pupuk->kode)->first();
                                if ($dbPupuk) {
                                    $pupuk->dosis_per_hektar = $dbPupuk->dosis_per_hektar;
                                    $pupuk->satuan_dosis = $dbPupuk->satuan_dosis ?? 'kg';
                                    $pupuk->harga_per_unit = $dbPupuk->harga_per_unit;
                                    $pupuk->satuan_harga_qty = $dbPupuk->satuan_harga_qty;
                                    $pupuk->satuan_harga_unit = $dbPupuk->satuan_harga_unit;
                                    $pupuk->frekuensi_aplikasi = $dbPupuk->frekuensi_aplikasi ?? 1;
                                }
                            }
                            $calc = \App\Helpers\UnitConverter::hitungBiayaAkurat(
                                $luasLahan,
                                (float) ($pupuk->dosis_per_hektar ?? 0),
                                $pupuk->satuan_dosis ?? 'kg',
                                (float) ($pupuk->harga_per_unit ?? 0),
                                (float) ($pupuk->satuan_harga_qty ?? 1),
                                $pupuk->satuan_harga_unit ?? 'kg',
                                $pupuk->frekuensi_aplikasi ?? 1
                            );
                            $totalBiayaPupuk += $calc['total_biaya'];
                        @endphp
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px dashed #d1fae5; font-size:.75rem;">
                            <div>
                                <div style="font-weight:600; color:#0f172a;">{{ $pupuk->nama }}</div>
                                <div style="color:#64748b; font-size:.68rem;">{{ $calc['kebutuhan_riil'] }} · {{ $pupuk->frekuensi_aplikasi ?? '-' }}</div>
                            </div>
                            <div class="text-end">
                                <div style="color:#64748b; font-size:.65rem;">Rp {{ number_format($calc['total_biaya'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    {{-- Pestisida --}}
                    @if($recommendedPestisida->isNotEmpty())
                    <div class="mb-3">
                        <div style="font-size:.72rem; font-weight:700; color:#b45309; margin-bottom:8px;">
                            <i class="bi bi-shield-fill-check me-1"></i>PESTISIDA
                        </div>
                        @foreach($recommendedPestisida->take(2) as $item)
                        @php
                            $pestisida = $item->pestisida;
                            // Fallback: load from database if preview data doesn't have dosis_per_hektar
                            if (!$pestisida->dosis_per_hektar && $pestisida->kode) {
                                $dbPestisida = \App\Models\Pestisida::where('kode', $pestisida->kode)->first();
                                if ($dbPestisida) {
                                    $pestisida->dosis_per_hektar = $dbPestisida->dosis_per_hektar;
                                    $pestisida->satuan_dosis = $dbPestisida->satuan_dosis ?? 'ml';
                                    $pestisida->harga_per_unit = $dbPestisida->harga_per_unit;
                                    $pestisida->satuan_harga_qty = $dbPestisida->satuan_harga_qty;
                                    $pestisida->satuan_harga_unit = $dbPestisida->satuan_harga_unit;
                                    $pestisida->frekuensi_aplikasi = $dbPestisida->frekuensi_aplikasi ?? 1;
                                }
                            }
                            $calc = \App\Helpers\UnitConverter::hitungBiayaAkurat(
                                $luasLahan,
                                (float) ($pestisida->dosis_per_hektar ?? 0),
                                $pestisida->satuan_dosis ?? 'ml',
                                (float) ($pestisida->harga_per_unit ?? 0),
                                (float) ($pestisida->satuan_harga_qty ?? 1),
                                $pestisida->satuan_harga_unit ?? 'ml',
                                $pestisida->frekuensi_aplikasi ?? 1
                            );
                            $totalBiayaPestisida += $calc['total_biaya'];
                        @endphp
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px dashed #fef3c7; font-size:.75rem;">
                            <div>
                                <div style="font-weight:600; color:#0f172a;">{{ $pestisida->nama }}</div>
                                <div style="color:#64748b; font-size:.68rem;">{{ $calc['kebutuhan_riil'] }} · {{ $pestisida->frekuensi_aplikasi ?? '-' }}</div>
                            </div>
                            <div class="text-end">
                                <div style="color:#64748b; font-size:.65rem;">Rp {{ number_format($calc['total_biaya'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    {{-- Total Biaya --}}
                    <div class="pt-3 mt-2" style="border-top: 2px solid #bbf7d0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size:.78rem; font-weight:700; color:#166534;">TOTAL ESTIMASI BIAYA</span>
                            <span style="font-size:.95rem; font-weight:800; color:#166534;">Rp {{ number_format($totalBiayaPupuk + $totalBiayaPestisida, 0, ',', '.') }}</span>
                        </div>
                        <div style="font-size:.65rem; color:#64748b; margin-top:4px; text-align:right;">
                            *Estimasi biaya bahan untuk 1 kali aplikasi
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- end disease-sidebar --}}
        </div>{{-- end col left --}}

        {{-- ── Right: Recommendations ───────────────── --}}
        <div class="col-xl-8 col-lg-8">

            {{-- ── Calculator Info ── --}}
            <div class="calc-info-box d-flex gap-3 align-items-start">
                <div class="icon-circle">
                    <i class="bi bi-calculator"></i>
                </div>
                <div>
                    <div class="info-title mb-1">Pilih Pupuk & Pestisida untuk Kalkulasi</div>
                    <div class="info-text">
                        Centang produk yang ingin Anda gunakan. Perhitungan di bawah hanya mencakup <strong>1 kali pengaplikasian</strong> sesuai jadwal yang tertera pada masing-masing produk. 
                        Biaya yang ditampilkan merupakan <strong>perkiraan nilai bahan yang diserap/habis terpakai</strong> di lahan Anda, berdasarkan konversi dosis riil.
                    </div>
                </div>
            </div>

            {{-- ── Pupuk Section ── --}}
            @if($recommendedPupuk->isNotEmpty())
            <div class="section-label">
                <i class="bi bi-bag-fill text-success"></i> Rekomendasi Pupuk
            </div>
            <div class="product-grid-wrapper">
                @foreach($recommendedPupuk as $index => $item)
                @php
                    $pupukData = $item->pupuk;
                    // Data sudah distandardisasi ke basis terkecil (g atau ml) di database
                    $pupukDosisBase = (float) ($pupukData->dosis_per_hektar ?? 0);
                    $pupukSatuanDosis = $pupukData->satuan_dosis ?? 'g';
                    
                    $pupukHargaPerUnit = (float) ($pupukData->harga_per_unit ?? 0);
                    $pupukPriceUnitBase = (float) ($pupukData->satuan_harga_qty ?? 1);
                @endphp
                <div class="product-card" data-product-type="pupuk" data-product-id="{{ $pupukData->id ?? $loop->index }}">
                    {{-- Checkbox overlay --}}
                    <div class="product-check-wrap">
                          <input type="checkbox" class="product-check" name="selected_pupuk[]" value="{{ $pupukData->id ?? $loop->index }}" 
                                 data-price="{{ $pupukHargaPerUnit }}" 
                                 data-price-unit-base="{{ $pupukPriceUnitBase }}"
                                 data-dosis="{{ $pupukDosisBase }}" 
                                 data-dosis-base="{{ $pupukDosisBase }}"
                                 data-satuan="{{ $pupukSatuanDosis }}" 
                                 data-name="{{ $pupukData->nama ?? '-' }}"
                                 data-dosis-ha="{{ number_format($pupukDosisBase, 0, ',', '.') }} {{ $pupukSatuanDosis }}/ha"
                                 data-harga-satuan="{{ $formatUnitPrice($pupukHargaPerUnit, $pupukPriceUnitBase . ' ' . $pupukSatuanDosis) }}"
                                 data-frekuensi="{{ $pupukData->frekuensi_aplikasi ?? 1 }}"
                                 data-satuan-harga="{{ $pupukData->satuan_harga_unit ?? 'kg' }}"
                                 data-kode="{{ $pupukData->kode ?? '' }}"
                                 @if($index === 0) checked @endif>
                    </div>

                    {{-- Image --}}
                    @if(optional($item->pupuk)->gambar_url)
                        <img src="{{ $item->pupuk->gambar_url }}"
                             alt="{{ $item->pupuk->nama }}"
                             class="product-card-img">
                    @else
                        <div class="product-card-img-empty">
                            <i class="bi bi-bag"></i>
                        </div>
                    @endif

                    <div class="product-card-body">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="product-rank-badge {{ $item->peringkat == 1 ? 'rank-1' : '' }}">
                                <i class="bi bi-award-fill"></i> #{{ $item->peringkat }}
                            </span>
                            @if($item->pupuk->kode ?? null)
                            <span class="badge bg-light text-secondary border" style="font-size:.68rem;">{{ $item->pupuk->kode }}</span>
                            @endif
                            @if($rekomendasi->preferensi_label)
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:.68rem;">
                                <i class="bi bi-stars"></i> {{ $rekomendasi->preferensi_label }}
                            </span>
                            @endif
                        </div>
                        <div class="product-type-tag">Pupuk</div>
                        <div class="product-name">{{ $item->pupuk->nama ?? '-' }}</div>
                        <div class="product-desc">
                            {{ ExpertSystemPresenter::shortDescription(
                                optional($item->pupuk)->fungsi_utama,
                                optional($item->pupuk)->efek_penggunaan
                            ) }}
                        </div>
                        <div class="product-score-row">
                            <div class="score-bar-wrap">
                                <div class="score-bar-fill {{ (float)$item->nilai_vi < 0.7 ? 'medium' : '' }}"
                                     style="width: {{ ExpertSystemPresenter::percent($item->nilai_vi) }}"></div>
                            </div>
                            <span class="score-label">{{ ExpertSystemPresenter::percent($item->nilai_vi) }}</span>
                            @if(data_get($item, 'adjustment_info.preset_boost', 0) != 0)
                                <span class="adjustment-badge {{ data_get($item, 'adjustment_info.preset_boost', 0) > 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-{{ data_get($item, 'adjustment_info.preset_boost', 0) > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ number_format(abs(data_get($item, 'adjustment_info.preset_boost', 0)) * 100, 1) }}%
                                </span>
                            @endif
                            <span class="badge text-bg-{{ ExpertSystemPresenter::confidenceTone($item->nilai_vi) }}" style="font-size:.7rem;">
                                {{ ExpertSystemPresenter::confidenceLabel($item->nilai_vi) }}
                            </span>
                        </div>
                    </div>{{-- end product-card-body --}}

                    {{-- Detail Toggle --}}
                    <details class="detail-toggle">
                        <summary>
                            <span>Lihat Detail</span>
                            <span class="chevron"><i class="bi bi-chevron-down"></i></span>
                        </summary>
                        <div class="detail-panel">
                            <div class="fw-semibold mb-3" style="font-size:.85rem;">
                                <i class="bi bi-info-circle me-1 text-success"></i>
                                {{ $item->pupuk->nama ?? 'Pupuk' }}
                            </div>
                            <div class="detail-list">
                                <div class="detail-row">
                                    <div class="dl">Takaran per Hektar</div>
                                    <div class="dv">{{ $item->pupuk->dosis_per_hektar ?? '-' }} {{ $item->pupuk->satuan_dosis ?? '' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="dl">Harga</div>
                                    <div class="dv">{{ $formatUnitPrice($item->pupuk->harga_per_unit ?? null, $item->pupuk->satuan_harga_qty . ' ' . $item->pupuk->satuan_harga_unit) }}</div>
                                </div>
                                <div class="detail-row span2">
                                    <div class="dl">Fungsi Utama</div>
                                    <div class="dv">{{ $item->pupuk->fungsi_utama ?? '-' }}</div>
                                </div>
                                <div class="detail-row span2">
                                    <div class="dl">Cara Aplikasi</div>
                                    <div class="dv">{{ $item->pupuk->cara_aplikasi ?? '-' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="dl">Frekuensi</div>
                                    <div class="dv">{{ $item->pupuk->frekuensi_aplikasi ?? '-' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="dl">Jadwal Aplikasi</div>
                                    <div class="dv">{{ $item->pupuk->jadwal_umur_aplikasi ?? '-' }}</div>
                                </div>
                            </div>{{-- end detail-list --}}

                        </div>{{-- end detail-panel --}}
                    </details>{{-- end detail-toggle --}}

                </div>{{-- end product-card --}}
                @endforeach
            </div>{{-- end product-grid-wrapper (pupuk) --}}
            @endif{{-- end if recommendedPupuk --}}

            {{-- ── Pestisida Section ── --}}
            @if($recommendedPestisida->isNotEmpty())
            <div class="section-label">
                <i class="bi bi-shield-fill-check text-warning"></i> Rekomendasi Pestisida
            </div>
            <div class="product-grid-wrapper">
                @foreach($recommendedPestisida as $index => $item)
                @php
                    $pestisidaData = $item->pestisida;
                    // Data sudah distandardisasi ke basis terkecil (g atau ml) di database
                    $pestisidaDosisBase = (float) ($pestisidaData->dosis_per_hektar ?? 0);
                    $pestisidaSatuanDosis = $pestisidaData->satuan_dosis ?? 'g';
                    
                    $pestisidaHargaPerUnit = (float) ($pestisidaData->harga_per_unit ?? 0);
                    $pestisidaPriceUnitBase = (float) ($pestisidaData->satuan_harga_qty ?? 1);
                @endphp
                <div class="product-card" data-product-type="pestisida" data-product-id="{{ $pestisidaData->id ?? $loop->index }}">
                    {{-- Checkbox overlay --}}
                    <div class="product-check-wrap">
                         <input type="checkbox" class="product-check" name="selected_pestisida[]" value="{{ $pestisidaData->id ?? $loop->index }}" 
                                data-price="{{ $pestisidaHargaPerUnit }}" 
                                data-price-unit-base="{{ $pestisidaPriceUnitBase }}"
                                data-dosis="{{ $pestisidaDosisBase }}" 
                                data-dosis-base="{{ $pestisidaDosisBase }}"
                                data-satuan="{{ $pestisidaSatuanDosis }}" 
                                data-name="{{ $pestisidaData->nama ?? '-' }}"
                                data-dosis-ha="{{ number_format($pestisidaDosisBase, 0, ',', '.') }} {{ $pestisidaSatuanDosis }}/ha"
                                data-harga-satuan="{{ $formatUnitPrice($pestisidaHargaPerUnit, $pestisidaPriceUnitBase . ' ' . $pestisidaSatuanDosis) }}"
                                 data-frekuensi="{{ $pestisidaData->frekuensi_aplikasi ?? 1 }}"
                                 data-satuan-harga="{{ $pestisidaData->satuan_harga_unit ?? 'ml' }}"
                                 data-kode="{{ $pestisidaData->kode ?? '' }}"
                                 @if($index === 0) checked @endif>
                    </div>

                    {{-- Image --}}
                    @if(optional($item->pestisida)->gambar_url)
                        <img src="{{ $item->pestisida->gambar_url }}"
                             alt="{{ $item->pestisida->nama }}"
                             class="product-card-img">
                    @else
                        <div class="product-card-img-empty">
                            <i class="bi bi-shield"></i>
                        </div>
                    @endif

                    <div class="product-card-body">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="product-rank-badge {{ $item->peringkat == 1 ? 'rank-1' : '' }}">
                                <i class="bi bi-award-fill"></i> #{{ $item->peringkat }}
                            </span>
                            @if($item->pestisida->kode ?? null)
                            <span class="badge bg-light text-secondary border" style="font-size:.68rem;">{{ $item->pestisida->kode }}</span>
                            @endif
                            @if($rekomendasi->preferensi_label)
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:.68rem;">
                                <i class="bi bi-stars"></i> {{ $rekomendasi->preferensi_label }}
                            </span>
                            @endif
                        </div>
                        <div class="product-type-tag">Pestisida</div>
                        <div class="product-name">{{ $item->pestisida->nama ?? '-' }}</div>
                        <div class="product-desc">
                            {{ ExpertSystemPresenter::shortDescription(
                                optional($item->pestisida)->fungsi,
                                optional($item->pestisida)->efek_penggunaan
                            ) }}
                        </div>
                        <div class="product-score-row">
                            <div class="score-bar-wrap">
                                <div class="score-bar-fill {{ (float)$item->nilai_vi < 0.7 ? 'medium' : '' }}"
                                     style="width: {{ ExpertSystemPresenter::percent($item->nilai_vi) }}"></div>
                            </div>
                            <span class="score-label">{{ ExpertSystemPresenter::percent($item->nilai_vi) }}</span>
                            @if(data_get($item, 'adjustment_info.preset_boost', 0) != 0)
                                <span class="adjustment-badge {{ data_get($item, 'adjustment_info.preset_boost', 0) > 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-{{ data_get($item, 'adjustment_info.preset_boost', 0) > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ number_format(abs(data_get($item, 'adjustment_info.preset_boost', 0)) * 100, 1) }}%
                                </span>
                            @endif
                            <span class="badge text-bg-{{ ExpertSystemPresenter::confidenceTone($item->nilai_vi) }}" style="font-size:.7rem;">
                                {{ ExpertSystemPresenter::confidenceLabel($item->nilai_vi) }}
                            </span>
                        </div>
                    </div>{{-- end product-card-body --}}

                    {{-- Detail Toggle --}}
                    <details class="detail-toggle">
                        <summary>
                            <span>Lihat Detail</span>
                            <span class="chevron"><i class="bi bi-chevron-down"></i></span>
                        </summary>
                        <div class="detail-panel">
                            <div class="fw-semibold mb-3" style="font-size:.85rem;">
                                <i class="bi bi-info-circle me-1 text-warning"></i>
                                {{ $item->pestisida->nama ?? 'Pestisida' }}
                            </div>
                            <div class="detail-list">
                                <div class="detail-row">
                                    <div class="dl">Takaran per Hektar</div>
                                    <div class="dv">{{ $item->pestisida->dosis_per_hektar ?? '-' }} {{ $item->pestisida->satuan_dosis ?? '' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="dl">Harga</div>
                                    <div class="dv">{{ $formatUnitPrice($item->pestisida->harga_per_unit ?? null, $item->pestisida->satuan_harga_qty . ' ' . $item->pestisida->satuan_harga_unit) }}</div>
                                </div>
                                <div class="detail-row span2">
                                    <div class="dl">Fungsi</div>
                                    <div class="dv">{{ $item->pestisida->fungsi ?? '-' }}</div>
                                </div>
                                <div class="detail-row span2">
                                    <div class="dl">Cara Aplikasi</div>
                                    <div class="dv">{{ $item->pestisida->cara_aplikasi ?? '-' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="dl">Frekuensi</div>
                                    <div class="dv">{{ $item->pestisida->frekuensi_aplikasi ?? '-' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="dl">Jadwal Aplikasi</div>
                                    <div class="dv">{{ $item->pestisida->jadwal_umur_aplikasi ?? '-' }}</div>
                                </div>
                            </div>{{-- end detail-list --}}

                        </div>{{-- end detail-panel --}}
                    </details>{{-- end detail-toggle --}}

                </div>{{-- end product-card --}}
                @endforeach
            </div>{{-- end product-grid-wrapper (pestisida) --}}
            @else
            <div class="product-empty-state">
                <div class="fw-semibold mb-1">Data pestisida belum tersedia</div>
                <div class="small mb-0">Belum ada pestisida dengan kecocokan positif dari gejala yang dipilih pada analisis ini.</div>
            </div>
            @endif{{-- end if recommendedPestisida --}}

            {{-- ── Selection Summary (Moved here) ── --}}
            @if($luasLahan > 0)
            <div class="selection-summary mt-4" id="selectionSummary">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-check2-square text-success fs-5"></i>
                    <span style="font-size:.95rem; font-weight:700; color:#166534;">Produk yang Dipilih</span>
                    <span class="badge bg-success ms-auto" id="selectedCount">0 item</span>
                </div>
                
                <div id="selectedItemsList">
                    {{-- Items will be populated by JavaScript --}}
                </div>
                
                <div class="selected-total-row">
                    <span>TOTAL ESTIMASI BIAYA</span>
                    <span id="selectedTotalCost">Rp 0</span>
                </div>
                <div style="font-size:.7rem; color:#64748b; margin-top:6px; text-align:right;">
                    *Estimasi berdasarkan harga per satuan & luas lahan {{ number_format($luasLahan, 0, ',', '.') }} m²
                </div>
            </div>
            @endif

        </div>{{-- end col right --}}
    </div>{{-- end row --}}
</div>{{-- end batch-card --}}
@endforeach{{-- end hasilDiagnosa --}}

{{-- ── Action Bar ─────────────────────────────────────── --}}
<div class="action-bar">
    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-house-door me-1"></i>Kembali ke Dashboard
    </a>
    @if($isPreview && !auth()->check())
    <a href="{{ route('login') }}" class="btn btn-outline-success">
        <i class="bi bi-box-arrow-in-right me-1"></i>Login untuk Simpan
    </a>
    <a href="{{ route('user.rekomendasi.preview.cetak') }}" target="_blank" class="btn btn-outline-secondary">
        <i class="bi bi-printer me-1"></i>Cetak Hasil
    </a>
    <a href="{{ route('user.diagnosis.index') }}" class="btn btn-success">
        <i class="bi bi-arrow-repeat me-1"></i>Diagnosis Lagi
    </a>
    @elseif($isPreview && auth()->check())
    <a href="{{ route('user.riwayat.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Lihat Riwayat
    </a>
    <a href="{{ route('user.rekomendasi.preview.cetak') }}" target="_blank" class="btn btn-outline-secondary">
        <i class="bi bi-printer me-1"></i>Cetak Hasil
    </a>
    <a href="{{ route('user.diagnosis.index') }}" class="btn btn-success">
        <i class="bi bi-arrow-repeat me-1"></i>Diagnosis Lagi
    </a>
    @else
    <a href="{{ route('user.riwayat.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Lihat Riwayat
    </a>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const luasLahan = {{ $luasLahan ?? 0 }};
    const luasHa = luasLahan / 10000;
    
    function formatRupiah(angka) {
        return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
    }
    
    function getFaktorKonversi(unit) {
        const u = unit.toLowerCase().trim();
        const map = {
            'kg': 1000, 'g': 1, 'gr': 1, 'gram': 1, 'ton': 1000000, 't': 1000000,
            'l': 1000, 'liter': 1000, 'litre': 1000, 'ml': 1, 'mililiter': 1
        };
        return map[u] || 1;
    }

    function calculateCost(cb) {
        const price = parseFloat(cb.dataset.price) || 0;
        const hargaQty = parseFloat(cb.dataset.priceUnitBase) || 1;
        const dosisPerHa = parseFloat(cb.dataset.dosisBase) || 0;
        const satuanDosis = cb.dataset.satuan || 'g';
        const satuanHarga = cb.dataset.satuanHarga || 'kg';
        
        // 1. Konversi luas ke hektar
        const luasHa = luasLahan / 10000;
        
        // 2. Hitung kebutuhan dasar (dosis × luas) - 1 kali aplikasi
        const kebutuhanDasar = dosisPerHa * luasHa;
        
        // 3. Normalisasi satuan ke basis (gram untuk berat, ml untuk volume)
        const faktorDosis = getFaktorKonversi(satuanDosis);
        const faktorKemasan = getFaktorKonversi(satuanHarga);
        
        const kebutuhanDalamBasis = kebutuhanDasar * faktorDosis;
        const isiKemasanDalamBasis = hargaQty * faktorKemasan;
        
        // 4. Hitung biaya proporsional (bahan yang terpakai)
        let biaya = 0;
        if (isiKemasanDalamBasis > 0) {
            biaya = Math.round((kebutuhanDalamBasis / isiKemasanDalamBasis) * price);
        }
        
        // Format kebutuhan untuk display
        let kebutuhanDisplay = kebutuhanDasar;
        let displayUnit = satuanDosis;
        const baseValue = kebutuhanDalamBasis;
        
        if (satuanDosis.toLowerCase() === 'g' || satuanDosis.toLowerCase() === 'kg') {
            if (baseValue >= 1000000) {
                kebutuhanDisplay = baseValue / 1000000;
                displayUnit = 'Ton';
            } else if (baseValue >= 1000) {
                kebutuhanDisplay = baseValue / 1000;
                displayUnit = 'kg';
            } else {
                kebutuhanDisplay = baseValue;
                displayUnit = 'g';
            }
        } else {
            if (baseValue >= 1000) {
                kebutuhanDisplay = baseValue / 1000;
                displayUnit = 'L';
            } else {
                kebutuhanDisplay = baseValue;
                displayUnit = 'ml';
            }
        }
        
        return { 
            biaya, 
            kebutuhanDisplay, 
            satuan: displayUnit, 
            kebutuhanDasar
        };
    }
    
    function formatQuantity(amount, unit) {
        // Konversi ke kg jika > 1000g dan satuannya g
        if (unit === 'g' && amount >= 1000) {
            return (amount / 1000).toFixed(2) + ' kg';
        }
        // Konversi ke ton jika > 1000kg
        if (unit === 'kg' && amount >= 1000) {
            return (amount / 1000).toFixed(2) + ' Ton';
        }
        // Konversi ke L jika > 1000ml dan satuannya ml
        if (unit === 'ml' && amount >= 1000) {
            return (amount / 1000).toFixed(2) + ' L';
        }
        return amount.toFixed(2) + ' ' + unit;
    }

    function updateSummary() {
        const checkboxes = document.querySelectorAll('.product-check');
        const listEl = document.getElementById('selectedItemsList');
        const totalEl = document.getElementById('selectedTotalCost');
        const countEl = document.getElementById('selectedCount');
        
        if (!listEl || !totalEl) return;
        
        let totalBiaya = 0;
        let totalPupuk = 0;
        let totalPestisida = 0;
        let items = [];
        let hasPupuk = false;
        let hasPestisida = false;
        
        checkboxes.forEach(cb => {
            if (!cb.checked) return;
            
            const card = cb.closest('.product-card');
            const type = card.dataset.productType;
            const name = cb.dataset.name;
            const { biaya, kebutuhanDisplay, satuan } = calculateCost(cb);
            
            totalBiaya += biaya;
            
            if (type === 'pupuk') {
                totalPupuk += biaya;
                hasPupuk = true;
            } else {
                totalPestisida += biaya;
                hasPestisida = true;
            }
            
            items.push({
                type,
                name,
                biaya,
                kebutuhanDisplay,
                satuan,
                dosisHa: cb.dataset.dosisHa || '-',
                hargaSatuan: cb.dataset.hargaSatuan || '-',
                frekuensi: cb.dataset.frekuensi || '-'
            });
        });
        
        // Build HTML
        let html = '';
        
        if (items.length === 0) {
            html = '<div style="text-align:center; color:#94a3b8; padding:16px; font-size:.85rem;">Centang produk yang ingin digunakan</div>';
        } else {
            // Group by type
            const pupukItems = items.filter(i => i.type === 'pupuk');
            const pestisidaItems = items.filter(i => i.type === 'pestisida');
            
            if (pupukItems.length > 0) {
                html += '<div style="font-size:.72rem; font-weight:700; color:#166534; margin-bottom:8px; margin-top:8px;"><i class="bi bi-bag-fill me-1"></i>PUPUK</div>';
                pupukItems.forEach(item => {
                    html += `
                        <div class="selected-item-row">
                            <div>
                                <span style="font-weight:600; color:#0f172a;">${item.name}</span>
                                <div class="selected-item-detail">
                                    <div><i class="bi bi-cup-straw me-1"></i>${formatQuantity(item.kebutuhanDisplay, item.satuan)}</div>
                                    <div><i class="bi bi-calendar me-1"></i>Jadwal: ${item.frekuensi}</div>
                                    <div><i class="bi bi-tag me-1"></i>Harga: ${item.hargaSatuan}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                if (hasPestisida) {
                    html += `<div class="category-subtotal"><span>Subtotal Pupuk</span><span>${formatRupiah(totalPupuk)}</span></div>`;
                }
            }
            
            if (pestisidaItems.length > 0) {
                html += '<div style="font-size:.72rem; font-weight:700; color:#b45309; margin-bottom:8px; margin-top:12px;"><i class="bi bi-shield-fill-check me-1"></i>PESTISIDA</div>';
                pestisidaItems.forEach(item => {
                    html += `
                        <div class="selected-item-row">
                            <div>
                                <span style="font-weight:600; color:#0f172a;">${item.name}</span>
                                <div class="selected-item-detail">
                                    <div><i class="bi bi-cup-straw me-1"></i>${formatQuantity(item.kebutuhanDisplay, item.satuan)}</div>
                                    <div><i class="bi bi-calendar me-1"></i>Jadwal: ${item.frekuensi}</div>
                                    <div><i class="bi bi-tag me-1"></i>Harga: ${item.hargaSatuan}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                if (hasPupuk) {
                    html += `<div class="category-subtotal"><span>Subtotal Pestisida</span><span>${formatRupiah(totalPestisida)}</span></div>`;
                }
            }
        }
        
        listEl.innerHTML = html;
        totalEl.textContent = formatRupiah(totalBiaya);
        countEl.textContent = `${items.length} item`;
    }
    
    // ── Cetak Link: inject selected product kodes ──
    function updateCetakLinks() {
        const selectedPupuk = [];
        const selectedPestisida = [];
        document.querySelectorAll('.product-check:checked').forEach(cb => {
            const code = cb.dataset.kode;
            if (!code) return;
            const type = cb.closest('.product-card').dataset.productType;
            if (type === 'pupuk') selectedPupuk.push(code);
            else selectedPestisida.push(code);
        });
        const params = new URLSearchParams();
        params.set('pupuk', selectedPupuk.join(','));
        params.set('pestisida', selectedPestisida.join(','));
        document.querySelectorAll('a[href*="preview/cetak"]').forEach(link => {
            const baseUrl = link.href.split('?')[0];
            link.href = baseUrl + '?' + params.toString();
        });
    }

    // Initial update
    updateSummary();
    updateCetakLinks();
    
    // Listen for checkbox changes
    document.querySelectorAll('.product-check').forEach(cb => {
        cb.addEventListener('change', () => {
            updateSummary();
            updateCetakLinks();
        });
    });
});
</script>
@endpush

@guest
</div>
@endguest

@endsection
