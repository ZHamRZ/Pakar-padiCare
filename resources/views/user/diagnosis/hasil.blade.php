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

        // Form validation - check if luas_lahan is filled before submit
        const form = document.querySelector("form[action='{{ route('user.diagnosis.proses') }}']");
        if (form) {
            form.addEventListener('submit', function(e) {
                const luasLahan = document.getElementById('luasLahanInput');
                if (!luasLahan || !luasLahan.value || parseFloat(luasLahan.value) <= 0) {
                    e.preventDefault();
                    // Show toast notification
                    const toastHtml = `
                        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Ukuran lahan wajib diisi sebelum memproses rekomendasi
                                    </div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    `;

                    // Remove existing toast if any
                    const existingToast = document.querySelector('.toast-container.position-fixed');
                    if (existingToast) {
                        existingToast.remove();
                    }

                    // Add and show toast
                    document.body.insertAdjacentHTML('beforeend', toastHtml);

                    // Auto remove after 3 seconds
                    setTimeout(() => {
                        const toastEl = document.querySelector('.toast-container.position-fixed');
                        if (toastEl) {
                            toastEl.remove();
                        }
                    }, 3000);

                    // Focus on the input
                    if (luasLahan) {
                        luasLahan.focus();
                    }
                }
            });
        }

        // Calculate average confidence from symptom weights (stored in session)
        const avgConfidenceBar = document.getElementById('avgConfidenceBar');
        const avgConfidenceValue = document.getElementById('avgConfidenceValue');
        const userConfidenceInput = document.getElementById('userConfidenceInput');

        @if(isset($gejalaWeights) && count($gejalaWeights) > 0)
        // Slider values are already in percentage (0-100), so we just average them
        const symptomWeights = @json(array_values($gejalaWeights));
        const avgConfidencePct = symptomWeights.reduce((a, b) => a + b, 0) / symptomWeights.length;
        const avgConfidence = avgConfidencePct / 100; // Convert to 0-1 range for backend

        if (avgConfidenceBar) {
            setTimeout(() => {
                avgConfidenceBar.style.width = avgConfidencePct + '%';
            }, 500);
        }
        if (avgConfidenceValue) {
            avgConfidenceValue.textContent = Math.round(avgConfidencePct) + '%';
            if (avgConfidencePct >= 70) {
                avgConfidenceValue.style.color = 'var(--primary)';
            } else if (avgConfidencePct >= 40) {
                avgConfidenceValue.style.color = '#d97706';
            } else {
                avgConfidenceValue.style.color = '#dc2626';
            }
        }
        if (userConfidenceInput) {
            userConfidenceInput.value = avgConfidence.toFixed(2);
        }
        @else
        // Fallback: no symptom weights, use default 100%
        if (avgConfidenceBar) {
            setTimeout(() => { avgConfidenceBar.style.width = '100%'; }, 500);
        }
        if (avgConfidenceValue) {
            avgConfidenceValue.textContent = '100%';
        }
        if (userConfidenceInput) {
            userConfidenceInput.value = '1.00';
        }
        @endif
    });
</script>
@endpush

@push('styles')
<style>
    /* ── Hero ── */
    .diagnosis-hero {
        background: linear-gradient(135deg, var(--soft-bg) 0%, var(--main-bg) 100%);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        position: relative;
        overflow: hidden;
    }
    .diagnosis-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, var(--focus-ring) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-score-card {
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }

    .score-ring {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: conic-gradient(var(--primary) var(--pct), var(--soft-bg) 0);
        display: flex; align-items: center; justify-content: center;
        position: relative;
    }
    .score-ring::after {
        content: '';
        position: absolute;
        width: 46px; height: 46px;
        background: var(--card);
        border-radius: 50%;
    }
    .score-ring-label {
        position: relative; z-index: 1;
        font-size: 11px; font-weight: 700;
        color: var(--primary);
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
        color: var(--primary);
        background: var(--soft-bg);
        border: 1px solid var(--border-light);
        padding: 4px 10px;
        border-radius: 100px;
        margin-bottom: 14px;
    }

    /* ── Disease Card ── */
    .disease-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .disease-card-image-wrap {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-md);
        background: var(--main-bg);
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
        background: linear-gradient(135deg, var(--main-bg) 0%, var(--soft-bg) 100%);
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
    }
    .conf-bar-track {
        height: 8px;
        background: var(--soft-bg);
        border-radius: 100px;
        overflow: hidden;
    }
    .conf-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--primary-hover));
        border-radius: 100px;
    }

    .matched-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: var(--main-bg);
        border: 1px solid var(--border);
        border-radius: 100px;
        font-size: 12px;
        color: var(--heading);
    }
    .matched-chip .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--primary);
        flex-shrink: 0;
    }

    .alt-diagnosis-card {
        height: 100%;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 18px;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
    }
    .alt-diagnosis-card:has(.alt-diagnosis-check:checked) {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--focus-ring);
        background: var(--soft-bg);
    }
    .alt-diagnosis-media,
    .alt-diagnosis-empty {
        width: 100%;
        height: 120px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        display: block;
        background: var(--main-bg);
    }
    .alt-diagnosis-empty {
        background: linear-gradient(135deg, var(--main-bg) 0%, var(--soft-bg) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--border);
    }
    .alt-diagnosis-check {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-hover);
        flex-shrink: 0;
    }

    /* ── Tips Card ── */
    .tips-card {
        background: linear-gradient(135deg, var(--soft-bg) 0%, var(--main-bg) 100%);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
    }
    .tip-item {
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        padding: 16px;
    }
    .tip-icon {
        width: 36px; height: 36px;
        border-radius: var(--radius-sm);
        background: var(--soft-bg);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        margin-bottom: 10px;
    }

    /* ── Preference Section ── */
    .preferences-wrap {
        background: var(--main-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
    }
    .preference-option {
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        background: var(--card);
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        display: block;
    }
    .preference-option:hover {
        border-color: var(--border-light);
        box-shadow: var(--shadow-sm);
    }
    .preference-option:has(input:checked) {
        border-color: var(--primary);
        background: var(--soft-bg);
        box-shadow: 0 0 0 3px var(--focus-ring);
    }
    .preference-option input[type="radio"] {
        accent-color: var(--primary-hover);
    }

    .custom-criteria-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
    }
    .criteria-level-select {
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        padding: 8px 12px;
        background: var(--main-bg);
        color: var(--heading);
        width: 100%;
        cursor: pointer;
    }
    .criteria-level-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--focus-ring);
    }

    /* ── Symptom Cards ── */
    .symptom-section {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .symptom-section-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-light);
        background: var(--main-bg);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .picked-symptom {
        background: var(--card);
        border: 1px solid var(--border);
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
        border-bottom: 1px solid var(--border-light);
    }
    .symptom-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--main-bg) 0%, var(--soft-bg) 100%);
    }
    .picked-symptom-body {
        padding: 10px;
    }

    /* ── Form Controls ── */
    .form-control, .form-select {
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 14px;
        color: var(--heading);
        padding: 10px 14px;
        background: var(--card);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--focus-ring);
        outline: none;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--body-text);
        margin-bottom: 6px;
    }
    .form-divider {
        height: 1px;
        background: var(--border);
        margin: 20px 0;
    }

    /* ── CTA Bar ── */
    .cta-bar {
        background: var(--card);
        border: 1px solid var(--border);
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
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 12px 28px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        box-shadow: 0 4px 14px rgba(21, 128, 61, 0.25);
    }
    .btn-primary-spk:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(21, 128, 61, 0.3);
        color: #fff;
    }
    .btn-primary-spk:active { transform: translateY(0); }

    .btn-outline-spk {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--border-light);
        border-radius: var(--radius-md);
        padding: 10px 22px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s;
        text-decoration: none;
    }
    .btn-outline-spk:hover {
        background: var(--soft-bg);
        border-color: var(--primary);
        color: var(--primary);
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
        background: var(--border);
    }
    .section-divider span {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--muted-text);
        white-space: nowrap;
    }

    /* ── Card Section Wrapper ── */
    .section-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .section-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-light);
        background: var(--main-bg);
    }
    .section-card-body { padding: 24px; }

    .alert-danger {
        border-radius: var(--radius-md);
    }
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
            <h2 class="fw-bold mb-2" style="color:var(--heading); font-size:1.6rem; line-height:1.3;">
                Hasil identifikasi berdasarkan<br>gejala yang Anda pilih
            </h2>
            <p class="mb-4" style="color:var(--body-text); font-size:14px; max-width:520px;">
                Sistem menampilkan penyakit dengan kecocokan tertinggi dan juga kemungkinan lain yang masih kuat agar Anda mendapat gambaran diagnosis yang lebih lengkap.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge" style="background:var(--green-100); color:var(--green-700); border:1px solid var(--green-200); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; padding:5px 11px; border-radius:100px; font-weight:600;">
                    <i class="bi bi-check2-all me-1"></i>{{ $selectedTotal }} gejala dianalisis
                </span>
                @if($utama)
                <span class="badge" style="background:var(--main-bg); color:var(--body-text); border:1px solid var(--border); font-size:12px; padding:5px 11px; border-radius:100px; font-weight:600;">
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
                        <div style="font-size:11px; font-weight:600; color:var(--muted-text); text-transform:uppercase; letter-spacing:0.06em;">Hasil Utama</div>
                        <div style="font-weight:700; color:var(--heading); font-size:15px; line-height:1.3;">
                            {{ data_get($utama, 'penyakit.nama', '-') }}
                        </div>
                    </div>
                </div>
                <div style="font-size:12px; color:var(--muted-text); margin-bottom:8px; font-weight:500;">Tingkat keyakinan</div>
                <div class="conf-bar-track">
                    <div class="conf-bar-fill" data-value="{{ $pctInt }}" style="width:0%"></div>
                </div>
                <div style="font-size:12px; color:var(--primary); font-weight:700; margin-top:6px;">
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
                <div style="font-weight:700; font-size:14px; color:var(--heading);">
                    Penyakit Teridentifikasi
                </div>
                <div style="font-size:12px; color:var(--muted-text);">Lanjutkan dengan penyakit yang paling cocok</div>
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
                            <i class="bi bi-virus fs-1" style="color:var(--border);"></i>
                        </div>
                        @endif
                    </div>

                    {{-- Stats below image --}}
                    <div class="mt-3 row g-2">
                        <div class="col-6">
                            <div style="background:var(--main-bg); border:1px solid var(--border); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                <div style="font-weight:800; font-size:18px; color:var(--primary);">{{ data_get($utama, 'cocok', 0) }}</div>
                                <div style="font-size:11px; color:var(--muted-text); font-weight:500;">Gejala cocok</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background:var(--main-bg); border:1px solid var(--border); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                <div style="font-weight:800; font-size:18px; color:var(--heading);">{{ $selectedTotal }}</div>
                                <div style="font-size:11px; color:var(--muted-text); font-weight:500;">Total dipilih</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3 flex-wrap">
                        <div>
                            <span class="section-eyebrow" style="margin-bottom:8px;">Kecocokan Tertinggi</span>
                            <h4 class="fw-bold mb-0" style="color:var(--heading); font-size:1.35rem;">
                                {{ data_get($utama, 'penyakit.nama') }}
                            </h4>
                            <div style="font-size:12px; color:var(--muted-text); font-weight:500; margin-top:3px;">
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

                    <p style="font-size:14px; color:var(--body-text); line-height:1.7; margin-bottom:20px;">
                        {{ data_get($utama, 'penyakit.deskripsi') ?: 'Deskripsi penyakit belum tersedia.' }}
                    </p>

                    <div class="mb-1" style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; font-weight:600; color:var(--body-text);">Tingkat keyakinan sistem</span>
                        <span style="font-size:13px; font-weight:700; color:var(--primary);">{{ ExpertSystemPresenter::percent($utamaScore) }}</span>
                    </div>
                    <div class="conf-bar-track mb-4">
                        <div class="conf-bar-fill" data-value="{{ $pctInt }}" style="width:0%"></div>
                    </div>

                    @if(!empty(data_get($utama, 'total')))
                    <div style="font-size:12px; color:var(--muted-text); margin-bottom:14px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Data pakar untuk penyakit ini memiliki <strong>{{ data_get($utama, 'total') }}</strong> gejala acuan.
                    </div>
                    @endif

                    @if($matchedSymptoms->isNotEmpty())
                    <div style="font-size:12px; font-weight:600; color:var(--body-text); margin-bottom:8px;">
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
                <div style="font-weight:700; font-size:14px; color:var(--heading);">
                    Penyakit Lain dengan Kecocokan Tinggi
                </div>
                <div style="font-size:12px; color:var(--muted-text);">
                    Kemungkinan ini juga penting karena nilainya masih dekat dengan hasil utama.
                </div>
            </div>
            <span class="badge" style="background:var(--main-bg); color:var(--body-text); border:1px solid var(--border); font-size:12px; padding:5px 11px; border-radius:100px; font-weight:600;">
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
                            <div style="font-weight:700; font-size:15px; color:var(--heading);">
                                {{ data_get($diagnosaItem, 'penyakit.nama') }}
                            </div>
                            <div style="font-size:12px; color:var(--muted-text); margin-top:2px;">
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
                                <div style="font-size:12px; color:var(--body-text); margin-bottom:8px; line-height:1.6;">
                                    Centang jika penyakit ini juga ingin ikut diproses ke rekomendasi.
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                <div style="background:var(--main-bg); border:1px solid var(--border); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                    <div style="font-weight:800; font-size:18px; color:var(--primary);">{{ data_get($diagnosaItem, 'cocok', 0) }}</div>
                                    <div style="font-size:11px; color:var(--muted-text); font-weight:500;">Gejala cocok</div>
                                </div>
                            </div>
                                    <div class="col-6">
                                <div style="background:var(--main-bg); border:1px solid var(--border); border-radius:var(--radius-sm); padding:10px 12px; text-align:center;">
                                    <div style="font-weight:800; font-size:18px; color:var(--heading);">{{ data_get($diagnosaItem, 'total', 0) }}</div>
                                    <div style="font-size:11px; color:var(--muted-text); font-weight:500;">Gejala acuan</div>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>

                        @if($altMatched->isNotEmpty())
                        <div style="font-size:12px; font-weight:600; color:var(--body-text); margin-bottom:8px;">
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
                <div style="font-weight:700; font-size:14px; color:var(--heading);">Gejala yang Dipilih</div>
                <div style="font-size:12px; color:var(--muted-text);">{{ $gejalaInput->count() }} gejala dipilih untuk analisis ini</div>
            </div>
            <span class="badge" style="background:var(--soft-bg); color:var(--primary); border:1px solid var(--border-light); font-size:12px; padding:5px 11px; border-radius:100px;">
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
                            <i class="bi bi-image fs-2" style="color:var(--border);"></i>
                        </div>
                        @endif
                        <div class="picked-symptom-body">
                            <div style="font-weight:700; font-size:12px; color:var(--primary);">{{ $item->kode }}</div>
                            <div style="font-size:13px; color:var(--body-text); margin-top:2px; line-height:1.4;">{{ $item->nama_gejala }}</div>
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
            <div style="font-weight:700; font-size:14px; color:var(--primary); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-lightbulb-fill" style="color:var(--primary);"></i>
                Tips menggunakan fitur prioritas
            </div>

            <div class="row g-3">
                <div class="col-md-4 col-lg-12">
                    <div class="tip-item h-100">
                        <div class="tip-icon">💰</div>
                        <div style="font-size:13px; font-weight:700; color:var(--heading); margin-bottom:4px;">
                            Hemat Biaya
                        </div>
                        <div style="font-size:13px; color:var(--body-text); line-height:1.6;">
                            Sistem lebih condong ke alternatif yang lebih ekonomis namun tetap efektif.
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-12">
                    <div class="tip-item h-100">
                        <div class="tip-icon">⚡</div>
                        <div style="font-size:13px; font-weight:700; color:var(--heading); margin-bottom:4px;">
                            Efisiensi Tinggi
                        </div>
                        <div style="font-size:13px; color:var(--body-text); line-height:1.6;">
                            Menonjolkan alternatif paling kuat sesuai pengetahuan pakar tanpa kompromi.
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-12">
                    <div class="tip-item h-100">
                        <div class="tip-icon">⚖️</div>
                        <div style="font-size:13px; font-weight:700; color:var(--heading); margin-bottom:4px;">
                            Seimbang
                        </div>
                        <div style="font-size:13px; color:var(--body-text); line-height:1.6;">
                            Cocok jika petani ingin hasil yang aman dipakai tanpa perlu mengatur detail tambahan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Preferences & Calculator --}}
    <div class="col-lg-7">
        <div class="preferences-wrap p-4">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div style="font-weight:700; font-size:15px; color:var(--heading);">
                    Atur kebutuhan & prioritas Anda
                </div>
            </div>

            <div style="font-size:13px; color:var(--muted-text); margin-bottom:20px;">
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
                                <div style="font-weight:700; font-size:14px; color:var(--heading);">
                                    {{ $preset['label'] }}
                                </div>
                                <div style="font-size:13px; color:var(--body-text); margin-top:3px; line-height:1.5;">
                                    {{ $preset['description'] }}
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="form-divider"></div>

            {{-- ── Symptom Confidence Summary ── --}}
            <div class="mt-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-shield-check" style="color:var(--primary);"></i>
                    <span style="font-weight:700; font-size:14px; color:var(--heading);">Rata-rata Keyakinan Gejala</span>
                </div>
                <div style="font-size:13px; color:var(--muted-text); margin-bottom:12px;">
                    Dihitung dari slider keyakinan yang Anda atur di halaman pemilihan gejala. Nilai ini akan mengalikan CF akhir.
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size:12px; color:var(--muted-text); font-weight:500;">Tidak yakin</span>
                            <span id="avgConfidenceValue" style="font-weight:800; font-size:18px; color:var(--primary);">—</span>
                            <span style="font-size:12px; color:var(--muted-text); font-weight:500;">Sangat yakin</span>
                        </div>
                        <div class="conf-bar-track" style="height:10px;">
                            <div id="avgConfidenceBar" class="conf-bar-fill" style="width:0%; transition: width 0.8s ease;"></div>
                        </div>
                        <div class="form-text" style="font-size:11px; color:var(--muted-text); margin-top:6px;">
                            <i class="bi bi-info-circle me-1"></i>
                            Untuk mengubah keyakinan, kembali ke halaman pemilihan gejala dan atur slider per gejala.
                        </div>
                    </div>
                </div>
                {{-- Hidden input to pass calculated average to backend --}}
                <input type="hidden" id="userConfidenceInput" name="user_confidence" value="1">
            </div>

            <div class="form-divider"></div>

            {{-- ── Dosage Calculator Input ── --}}
            <div class="mt-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-calculator" style="color:var(--primary);"></i>
                    <span style="font-weight:700; font-size:14px; color:var(--heading);">Kalkulator Dosis & Estimasi Biaya</span>
                </div>
                <div style="font-size:13px; color:var(--muted-text); margin-bottom:12px;">
                    Masukkan luas lahan untuk menghitung kebutuhan pupuk, pestisida, dan estimasi biaya.
                </div>
                <div class="row g-3 align-items-end">
                    <div class="col-md-12">
                        <label class="form-label">Luas Lahan (hektar)</label>
                        <input type="number" id="luasLahanInput" name="luas_lahan" min="0.01" step="0.01"
                               value="{{ old('luas_lahan', session('luas_lahan')) }}"
                               class="form-control @error('luas_lahan') is-invalid @enderror" placeholder="Contoh: 0.5">
                        @error('luas_lahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" style="font-size:11px; color:var(--muted-text); margin-top:4px;">
                            Masukkan ukuran lahan dalam hektar. 1 hektar = 10.000 m².
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

    {{-- ── CTA Bar ── --}}
    <div class="cta-bar anim-fade-up">
        <div>
            <div style="font-weight:700; font-size:14px; color:var(--heading);">Siap melihat rekomendasi?</div>
            <div style="font-size:13px; color:var(--muted-text);">Sistem akan memproses preferensi dan gejala Anda</div>
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
