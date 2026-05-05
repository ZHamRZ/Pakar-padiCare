@extends('layouts.app')

@section('title', 'Hasil Identifikasi')
@section('page-title', 'Hasil Identifikasi')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Animate confidence bar on load
        const bars = document.querySelectorAll('.conf-bar-fill');
        bars.forEach(bar => {
            const target = bar.dataset.value;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.transition = 'width 1.2s cubic-bezier(0.16, 1, 0.3, 1)';
                bar.style.width = target + '%';
            }, 300);
        });

        // Staggered card entrance
        const cards = document.querySelectorAll('.anim-fade-up');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + i * 80);
        });
    });
</script>
@endpush

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap');

    :root {
        --green-50:  #f0fdf4;
        --green-100: #dcfce7;
        --green-200: #bbf7d0;
        --green-500: #22c55e;
        --green-600: #16a34a;
        --green-700: #15803d;
        --slate-50:  #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --radius-sm: 10px;
        --radius-md: 16px;
        --radius-lg: 22px;
        --radius-xl: 28px;
        --shadow-sm: 0 1px 3px rgba(15,23,42,0.06), 0 1px 2px rgba(15,23,42,0.04);
        --shadow-md: 0 4px 16px rgba(15,23,42,0.07), 0 1px 4px rgba(15,23,42,0.04);
        --shadow-lg: 0 12px 40px rgba(15,23,42,0.09), 0 2px 8px rgba(15,23,42,0.05);
    }

    body { font-family: 'DM Sans', sans-serif; }

    h1, h2, h3, h4, h5, .fw-bold, .fw-semibold {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ── Hero ── */
    .diagnosis-hero {
        background: linear-gradient(135deg, #ecfdf5 0%, #f8fafc 100%);
        border: 1px solid var(--green-200);
        border-radius: var(--radius-xl);
        position: relative;
        overflow: hidden;
    }
    .diagnosis-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(34,197,94,0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-score-card {
        background: #fff;
        border: 1px solid var(--green-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }

    .score-ring {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: conic-gradient(var(--green-500) var(--pct), var(--green-100) 0);
        display: flex; align-items: center; justify-content: center;
        position: relative;
    }
    .score-ring::after {
        content: '';
        position: absolute;
        width: 46px; height: 46px;
        background: #fff;
        border-radius: 50%;
    }
    .score-ring-label {
        position: relative; z-index: 1;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 11px; font-weight: 700;
        color: var(--green-700);
    }

    /* ── Section Label ── */
    .section-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--green-700);
        background: var(--green-100);
        border: 1px solid var(--green-200);
        padding: 4px 10px;
        border-radius: 100px;
        margin-bottom: 14px;
    }

    /* ── Disease Card ── */
    .disease-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .disease-card-image-wrap {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-md);
        background: var(--slate-100);
    }
    .disease-preview-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .disease-card-image-wrap:hover .disease-preview-image {
        transform: scale(1.03);
    }
    .disease-preview-empty {
        height: 220px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--slate-100) 0%, var(--green-50) 100%);
        display: flex; align-items: center; justify-content: center;
    }
    .confidence-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .conf-bar-track {
        height: 8px;
        background: var(--green-100);
        border-radius: 100px;
        overflow: hidden;
    }
    .conf-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--green-500), var(--green-600));
        border-radius: 100px;
    }

    .matched-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: 100px;
        font-size: 12px;
        color: var(--slate-700);
    }
    .matched-chip .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--green-500);
        flex-shrink: 0;
    }

    .alt-diagnosis-card {
        height: 100%;
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        padding: 18px;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
    }
    .alt-diagnosis-card:has(.alt-diagnosis-check:checked) {
        border-color: var(--green-500);
        box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
        background: var(--green-50);
    }
    .alt-diagnosis-media,
    .alt-diagnosis-empty {
        width: 100%;
        height: 120px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        display: block;
        background: var(--slate-100);
    }
    .alt-diagnosis-empty {
        background: linear-gradient(135deg, var(--slate-100) 0%, var(--green-50) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-300);
    }
    .alt-diagnosis-check {
        width: 18px;
        height: 18px;
        accent-color: var(--green-600);
        flex-shrink: 0;
    }

    /* ── Tips Card ── */
    .tips-card {
        background: linear-gradient(135deg, var(--green-50) 0%, #f8fafc 100%);
        border: 1px solid var(--green-200);
        border-radius: var(--radius-lg);
    }
    .tip-item {
        background: #fff;
        border: 1px solid var(--green-100);
        border-radius: var(--radius-md);
        padding: 16px;
    }
    .tip-icon {
        width: 36px; height: 36px;
        border-radius: var(--radius-sm);
        background: var(--green-100);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        margin-bottom: 10px;
    }

    /* ── Preference Section ── */
    .preferences-wrap {
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
    }
    .preference-option {
        border: 2px solid var(--slate-200);
        border-radius: var(--radius-md);
        background: #fff;
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        display: block;
    }
    .preference-option:hover {
        border-color: var(--green-300, #86efac);
        box-shadow: var(--shadow-sm);
    }
    .preference-option:has(input:checked) {
        border-color: var(--green-500);
        background: var(--green-50);
        box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
    }
    .preference-option input[type="radio"] {
        accent-color: var(--green-600);
    }

    .custom-criteria-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
    }
    .criteria-level-select {
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-sm);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 12px;
        background: var(--slate-50);
        color: var(--slate-700);
        width: 100%;
        cursor: pointer;
    }
    .criteria-level-select:focus {
        outline: none;
        border-color: var(--green-500);
        box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
    }

    /* ── Symptom Cards ── */
    .symptom-section {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .symptom-section-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--slate-100);
        background: var(--slate-50);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .picked-symptom {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .picked-symptom:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    .picked-symptom img,
    .symptom-empty {
        width: 100%;
        height: 88px;
        object-fit: cover;
        display: block;
    }
    .picked-symptom img {
        border-bottom: 1px solid var(--slate-100);
    }
    .symptom-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--slate-100) 0%, var(--green-50) 100%);
    }
    .picked-symptom-body {
        padding: 10px;
    }

    /* ── Form Controls ── */
    .form-control, .form-select {
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-sm);
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        color: var(--slate-700);
        padding: 10px 14px;
        background: #fff;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--green-500);
        box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
        outline: none;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--slate-600);
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin-bottom: 6px;
    }
    .form-divider {
        height: 1px;
        background: var(--slate-200);
        margin: 20px 0;
    }

    /* ── CTA Bar ── */
    .cta-bar {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .btn-primary-spk {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--green-600);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 12px 28px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        box-shadow: 0 4px 14px rgba(22,163,74,0.25);
    }
    .btn-primary-spk:hover {
        background: var(--green-700);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(22,163,74,0.3);
        color: #fff;
    }
    .btn-primary-spk:active { transform: translateY(0); }

    .btn-outline-spk {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: var(--green-700);
        border: 2px solid var(--green-200);
        border-radius: var(--radius-md);
        padding: 10px 22px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s;
        text-decoration: none;
    }
    .btn-outline-spk:hover {
        background: var(--green-50);
        border-color: var(--green-400, #4ade80);
        color: var(--green-700);
    }

    /* ── Alert ── */
    .alert-warning-custom {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: var(--radius-md);
        padding: 14px 18px;
        color: #92400e;
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    /* ── Divider Label ── */
    .section-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 28px 0 20px;
    }
    .section-divider::before,
    .section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--slate-200);
    }
    .section-divider span {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--slate-400);
        white-space: nowrap;
    }

    /* ── Card Section Wrapper ── */
    .section-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .section-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--slate-100);
        background: var(--slate-50);
    }
    .section-card-body { padding: 24px; }
</style>
@endpush

@section('content')
@php
    use App\Support\ExpertSystemPresenter;

    $utama = data_get($diagnosisSummary ?? [], 'top_diagnosis');
    $diagnosaList = collect($skorPenyakit ?? []);
    $utamaScore = data_get($utama, 'confidence', data_get($utama, 'persen', 0) / 100);
    $warningUtama = ExpertSystemPresenter::lowConfidenceMessage($utamaScore);
    $selectedTotal = (int) data_get($diagnosisSummary ?? [], 'selected_symptom_total', $gejalaInput->count());
    $matchedSymptoms = $gejalaInput->whereIn('id', data_get($utama, 'matched_gejala_ids', []))->values();
    $pctInt = (int) round($utamaScore * 100);
    $batasDiagnosaTinggi = max(0.6, $utamaScore - 0.1);
    $diagnosaTinggi = $diagnosaList
        ->filter(fn ($item) => (float) data_get($item, 'confidence', 0) >= $batasDiagnosaTinggi)
        ->values();
    $diagnosaTambahan = $diagnosaTinggi
        ->reject(fn ($item) => (int) data_get($item, 'penyakit.id') === (int) data_get($utama, 'penyakit.id'))
        ->values();
@endphp

@guest
<div class="container py-4">
@endguest

@if(session('error'))
<div class="alert alert-danger mb-4">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger mb-4">
    {{ $errors->first() }}
</div>
@endif

{{-- ── Hero ── --}}
<div class="diagnosis-hero p-4 p-lg-5 mb-4 anim-fade-up">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <div class="section-eyebrow">
                <i class="bi bi-cpu"></i> Analisis Sistem Pakar
            </div>
            <h2 class="fw-bold mb-2" style="color:var(--slate-900); font-size:1.6rem; line-height:1.3;">
                Hasil identifikasi berdasarkan<br>gejala yang Anda pilih
            </h2>
            <p class="mb-4" style="color:var(--slate-500); font-size:14px; max-width:520px;">
                Sistem menampilkan penyakit dengan kecocokan tertinggi dan juga kemungkinan lain yang masih kuat agar Anda mendapat gambaran diagnosis yang lebih lengkap.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge" style="background:var(--green-100); color:var(--green-700); border:1px solid var(--green-200); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; padding:5px 11px; border-radius:100px; font-weight:600;">
                    <i class="bi bi-check2-all me-1"></i>{{ $selectedTotal }} gejala dianalisis
                </span>
                @if($utama)
                <span class="badge" style="background:var(--slate-100); color:var(--slate-600); border:1px solid var(--slate-200); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; padding:5px 11px; border-radius:100px; font-weight:600;">
                    <i class="bi bi-bug me-1"></i>{{ data_get($utama, 'penyakit.nama') }}
                </span>
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="hero-score-card p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="score-ring" style="--pct: {{ $pctInt * 3.6 }}deg;">
                        <span class="score-ring-label">{{ $pctInt }}%</span>
                    </div>
                    <div>
                        <div style="font-size:11px; font-weight:600; color:var(--slate-400); text-transform:uppercase; letter-spacing:0.06em;">Hasil Utama</div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; color:var(--slate-800); font-size:15px; line-height:1.3;">
                            {{ data_get($utama, 'penyakit.nama', '-') }}
                        </div>
                    </div>
                </div>
                <div style="font-size:12px; color:var(--slate-400); margin-bottom:8px; font-weight:500;">Tingkat keyakinan</div>
                <div class="conf-bar-track">
                    <div class="conf-bar-fill" data-value="{{ $pctInt }}" style="width:0%"></div>
                </div>
                <div style="font-size:12px; color:var(--green-600); font-weight:700; margin-top:6px; font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ ExpertSystemPresenter::percent($utamaScore) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Warning ── --}}
@if($warningUtama)
<div class="alert-warning-custom mb-4 anim-fade-up">
    <i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b; margin-top:2px;"></i>
    <span>{{ $warningUtama }}</span>
</div>
@endif

@if($utama)
<form action="{{ route('user.diagnosis.proses') }}" method="POST">
    @csrf
    @foreach($gejalaInput as $gejalaDipilih)
    <input type="hidden" name="gejala_terpilih[]" value="{{ $gejalaDipilih->id }}">
    @endforeach
    {{-- HIDDEN input id_penyakit[] dihapus - user sekarang memilih via checkbox --}}

    {{-- ── Disease Detail Card ── --}}
    <div class="section-card mb-4 anim-fade-up">
        <div class="section-card-header d-flex align-items-center justify-content-between">
            <div>
                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--slate-700);">
                    Penyakit Teridentifikasi
                </div>
                <div style="font-size:12px; color:var(--slate-400);">Lanjutkan dengan penyakit yang paling cocok</div>
            </div>
            @guest
            <a href="{{ route('login') }}" class="btn-outline-spk" style="font-size:13px; padding:8px 16px;">
                <i class="bi bi-box-arrow-in-right"></i> Login untuk Simpan
            </a>
            @endguest
        </div>
        <div class="section-card-body">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4">
                    <div class="disease-card-image-wrap">
                        @if(data_get($utama, 'penyakit.gambar_url'))
                        <img src="{{ data_get($utama, 'penyakit.gambar_url') }}"
                             alt="{{ data_get($utama, 'penyakit.nama') }}"
                             class="disease-preview-image">
                        @else
                        <div class="disease-preview-empty">
                            <i class="bi bi-virus fs-1" style="color:var(--slate-300);"></i>
                        </div>
                        @endif
                    </div>

                    {{-- Stats below image --}}
                    <div class="mt-3 row g-2">
                        <div class="col-6">
                            <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:18px; color:var(--green-600);">{{ data_get($utama, 'cocok', 0) }}</div>
                                <div style="font-size:11px; color:var(--slate-400); font-weight:500;">Gejala cocok</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:18px; color:var(--slate-700);">{{ $selectedTotal }}</div>
                                <div style="font-size:11px; color:var(--slate-400); font-weight:500;">Total dipilih</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3 flex-wrap">
                        <div>
                            <span class="section-eyebrow" style="margin-bottom:8px;">Kecocokan Tertinggi</span>
                            <h4 class="fw-bold mb-0" style="color:var(--slate-900); font-size:1.35rem;">
                                {{ data_get($utama, 'penyakit.nama') }}
                            </h4>
                            <div style="font-size:12px; color:var(--slate-400); font-weight:500; margin-top:3px;">
                                {{ data_get($utama, 'penyakit.kode') }}
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2">
                            <span class="confidence-badge text-bg-{{ ExpertSystemPresenter::confidenceTone($utamaScore) }}">
                                <i class="bi bi-patch-check-fill"></i>
                                {{ ExpertSystemPresenter::confidenceLabel($utamaScore) }}
                            </span>
                            {{-- Checkbox untuk penyakit utama - selalu default checked --}}
                            <input
                                class="alt-diagnosis-check"
                                type="checkbox"
                                name="id_penyakit[]"
                                value="{{ data_get($utama, 'penyakit.id') }}"
                                checked
                                title="Penyakit utama akan selalu diproses untuk rekomendasi">
                        </div>
                    </div>

                    <p style="font-size:14px; color:var(--slate-500); line-height:1.7; margin-bottom:20px;">
                        {{ data_get($utama, 'penyakit.deskripsi') ?: 'Deskripsi penyakit belum tersedia.' }}
                    </p>

                    <div class="mb-1" style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; font-weight:600; color:var(--slate-600); font-family:'Plus Jakarta Sans',sans-serif;">Tingkat keyakinan sistem</span>
                        <span style="font-size:13px; font-weight:700; color:var(--green-600); font-family:'Plus Jakarta Sans',sans-serif;">{{ ExpertSystemPresenter::percent($utamaScore) }}</span>
                    </div>
                    <div class="conf-bar-track mb-4">
                        <div class="conf-bar-fill" data-value="{{ $pctInt }}" style="width:0%"></div>
                    </div>

                    @if(!empty(data_get($utama, 'total')))
                    <div style="font-size:12px; color:var(--slate-400); margin-bottom:14px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Data pakar untuk penyakit ini memiliki <strong>{{ data_get($utama, 'total') }}</strong> gejala acuan.
                    </div>
                    @endif

                    @if($matchedSymptoms->isNotEmpty())
                    <div style="font-size:12px; font-weight:600; color:var(--slate-500); margin-bottom:8px; font-family:'Plus Jakarta Sans',sans-serif;">
                        Gejala yang cocok:
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($matchedSymptoms as $matched)
                        <span class="matched-chip">
                            <span class="dot"></span>
                            {{ $matched->kode }} — {{ $matched->nama_gejala }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($diagnosaTambahan->isNotEmpty())
    <div class="section-card mb-4 anim-fade-up">
        <div class="section-card-header d-flex align-items-center justify-content-between">
            <div>
                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--slate-700);">
                    Penyakit Lain dengan Kecocokan Tinggi
                </div>
                <div style="font-size:12px; color:var(--slate-400);">
                    Kemungkinan ini juga penting karena nilainya masih dekat dengan hasil utama.
                </div>
            </div>
            <span class="badge" style="background:var(--slate-100); color:var(--slate-600); border:1px solid var(--slate-200); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; padding:5px 11px; border-radius:100px; font-weight:600;">
                {{ $diagnosaTambahan->count() }} alternatif
            </span>
        </div>
        <div class="section-card-body">
            <div class="row g-3">
                @foreach($diagnosaTambahan as $diagnosaItem)
                @php
                    $altScore = (float) data_get($diagnosaItem, 'confidence', data_get($diagnosaItem, 'persen', 0) / 100);
                    $altPct = (int) round($altScore * 100);
                    $altMatched = $gejalaInput->whereIn('id', data_get($diagnosaItem, 'matched_gejala_ids', []))->values();
                    $altPenyakitId = (int) data_get($diagnosaItem, 'penyakit.id');
                @endphp
                <div class="col-lg-6">
                    <label class="alt-diagnosis-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:15px; color:var(--slate-800);">
                                    {{ data_get($diagnosaItem, 'penyakit.nama') }}
                                </div>
                                <div style="font-size:12px; color:var(--slate-400); margin-top:2px;">
                                    {{ data_get($diagnosaItem, 'penyakit.kode') }}
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <span class="confidence-badge text-bg-{{ ExpertSystemPresenter::confidenceTone($altScore) }}">
                                    <i class="bi bi-activity"></i>
                                    {{ $altPct }}%
                                </span>
                                <input
                                    class="alt-diagnosis-check"
                                    type="checkbox"
                                    name="id_penyakit[]"
                                    value="{{ $altPenyakitId }}"
                                    @if($loop->first) checked @endif
                                    title="Centang untuk memproses penyakit ini ke rekomendasi">
                            </div>
                        </div>

                        <div class="row g-3 mb-3 align-items-start">
                            <div class="col-sm-5">
                                @if(data_get($diagnosaItem, 'penyakit.gambar_url'))
                                <img
                                    src="{{ data_get($diagnosaItem, 'penyakit.gambar_url') }}"
                                    alt="{{ data_get($diagnosaItem, 'penyakit.nama') }}"
                                    class="alt-diagnosis-media">
                                @else
                                <div class="alt-diagnosis-empty">
                                    <i class="bi bi-virus fs-2"></i>
                                </div>
                                @endif
                            </div>
                            <div class="col-sm-7">
                                <div style="font-size:12px; color:var(--slate-500); margin-bottom:8px; line-height:1.6;">
                                    Centang jika penyakit ini juga ingin ikut diproses ke rekomendasi.
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                    <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:18px; color:var(--green-600);">{{ data_get($diagnosaItem, 'cocok', 0) }}</div>
                                    <div style="font-size:11px; color:var(--slate-400); font-weight:500;">Gejala cocok</div>
                                </div>
                            </div>
                                    <div class="col-6">
                                <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                    <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:18px; color:var(--slate-700);">{{ data_get($diagnosaItem, 'total', 0) }}</div>
                                    <div style="font-size:11px; color:var(--slate-400); font-weight:500;">Gejala acuan</div>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>

                        @if($altMatched->isNotEmpty())
                        <div style="font-size:12px; font-weight:600; color:var(--slate-500); margin-bottom:8px; font-family:'Plus Jakarta Sans',sans-serif;">
                            Gejala yang juga cocok:
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($altMatched as $matched)
                            <span class="matched-chip">
                                <span class="dot"></span>
                                {{ $matched->kode }} — {{ $matched->nama_gejala }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    {{-- ── Symptoms Grid ── --}}
    <div class="symptom-section mb-4 anim-fade-up">
        <div class="symptom-section-header">
            <div>
                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--slate-700);">Gejala yang Dipilih</div>
                <div style="font-size:12px; color:var(--slate-400);">{{ $gejalaInput->count() }} gejala dipilih untuk analisis ini</div>
            </div>
            <span class="badge" style="background:var(--green-100); color:var(--green-700); border:1px solid var(--green-200); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; padding:5px 11px; border-radius:100px;">
                {{ $gejalaInput->count() }} item
            </span>
        </div>
        <div class="p-4">
            <div class="row g-3">
                @foreach($gejalaInput as $item)
                <div class="col-md-3 col-sm-6 col-6">
                    <div class="picked-symptom h-100">
                        @if($item->gambar_url)
                        <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_gejala }}">
                        @else
                        <div class="symptom-empty">
                            <i class="bi bi-image fs-2" style="color:var(--slate-300);"></i>
                        </div>
                        @endif
                        <div class="picked-symptom-body">
                            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:12px; color:var(--green-600);">{{ $item->kode }}</div>
                            <div style="font-size:13px; color:var(--slate-600); margin-top:2px; line-height:1.4;">{{ $item->nama_gejala }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Tips + Preferences (MERGED) ── --}}
<div class="row g-4 mb-4 anim-fade-up">

    {{-- LEFT: Tips --}}
    <div class="col-lg-5">
        <div class="tips-card p-4 h-100">
            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--green-800, #166534); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-lightbulb-fill" style="color:var(--green-600);"></i>
                Tips menggunakan fitur prioritas
            </div>

            <div class="row g-3">
                <div class="col-md-4 col-lg-12">
                    <div class="tip-item h-100">
                        <div class="tip-icon">💰</div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; color:var(--slate-700); margin-bottom:4px;">
                            Hemat Biaya
                        </div>
                        <div style="font-size:13px; color:var(--slate-500); line-height:1.6;">
                            Sistem lebih condong ke alternatif yang lebih ekonomis namun tetap efektif.
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-12">
                    <div class="tip-item h-100">
                        <div class="tip-icon">⚡</div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; color:var(--slate-700); margin-bottom:4px;">
                            Efisiensi Tinggi
                        </div>
                        <div style="font-size:13px; color:var(--slate-500); line-height:1.6;">
                            Menonjolkan alternatif paling kuat sesuai pengetahuan pakar tanpa kompromi.
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-12">
                    <div class="tip-item h-100">
                        <div class="tip-icon">⚖️</div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; color:var(--slate-700); margin-bottom:4px;">
                            Seimbang
                        </div>
                        <div style="font-size:13px; color:var(--slate-500); line-height:1.6;">
                            Cocok jika petani ingin hasil yang aman dipakai tanpa perlu mengatur detail tambahan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Preferences --}}
    <div class="col-lg-7">
        <div class="preferences-wrap p-4 h-100">

            <div class="d-flex align-items-center justify-content-between mb-1">
                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:15px; color:var(--slate-800);">
                    Atur kebutuhan & prioritas Anda
                </div>
            </div>

            <div style="font-size:13px; color:var(--slate-400); margin-bottom:20px;">
                Pilih salah satu prioritas yang paling sesuai. Sistem akan menyesuaikan nilai keyakinan rekomendasi secara otomatis.
            </div>

            {{-- Preset --}}
            <div class="row g-3 mb-4">
                @foreach($presetPreferensi as $key => $preset)
                <div class="col-md-4">
                    <label class="preference-option p-3 h-100">
                        <div class="d-flex align-items-start gap-2">
                            <input class="form-check-input flex-shrink-0 mt-1 preset-radio"
                                   type="radio"
                                   name="preferensi_tipe"
                                   value="{{ $key }}"
                                   {{ old('preferensi_tipe', 'seimbang') === $key ? 'checked' : '' }}>

                            <div>
                                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--slate-800);">
                                    {{ $preset['label'] }}
                                </div>
                                <div style="font-size:13px; color:var(--slate-500); margin-top:3px; line-height:1.5;">
                                    {{ $preset['description'] }}
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="form-divider"></div>

            {{-- Input tambahan --}}
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label">Catatan tambahan</label>
                    <input type="text" name="preferensi_catatan"
                           value="{{ old('preferensi_catatan') }}"
                           class="form-control"
                           placeholder="cth: anggaran lapangan terbatas">
                </div>
            </div>
        </div>
    </div>

</div>

    

    {{-- ── CTA Bar ── --}}
    <div class="cta-bar anim-fade-up">
        <div>
            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--slate-700);">Siap melihat rekomendasi?</div>
            <div style="font-size:13px; color:var(--slate-400);">Sistem akan memproses preferensi dan gejala Anda</div>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            @guest
            <a href="{{ route('login') }}" class="btn-outline-spk">
                <i class="bi bi-person-circle"></i> Login untuk Simpan
            </a>
            @endguest
            <button type="submit" class="btn-primary-spk">
                <i class="bi bi-arrow-right-circle-fill"></i>
                {{ auth()->check() ? 'Lihat & Simpan Rekomendasi' : 'Lihat Rekomendasi' }}
            </button>
        </div>
    </div>

</form>
@endif

@guest
</div>
@endguest
@endsection
