@extends('layouts.app')

@section('title', 'Diagnosis Penyakit')
@section('page-title', 'Diagnosis Penyakit')

@push('styles')
<style>
    .symptom-toolbar {
        background: linear-gradient(135deg, var(--soft-bg) 0%, var(--main-bg) 100%);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
    }

    .symptom-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        background: var(--card);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .symptom-card:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
        box-shadow: var(--shadow-md);
    }

    .symptom-card input:checked+label .symptom-shell {
        border-color: var(--primary);
        background: var(--soft-bg);
    }

    .symptom-card .form-check-input {
        position: absolute;
        top: 1rem;
        right: 1rem;
        float: none;
        margin: 0;
        z-index: 2;
    }

    .symptom-card .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .symptom-card .form-check-label {
        display: block;
    }

    .symptom-shell {
        border: 1px solid transparent;
        border-radius: var(--radius-md);
        padding: 1rem;
        min-height: 100%;
    }

    .symptom-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: var(--radius-md);
        background: var(--main-bg);
    }

    .symptom-empty {
        height: 160px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--main-bg) 0%, var(--border) 100%);
    }

    /* Symptom Weight Slider */
    .weight-slider-container {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed var(--border);
        display: none;
    }

    .symptom-card input:checked+label .weight-slider-container {
        display: block;
    }

    .weight-slider-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--body-text);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .weight-slider {
        width: 100%;
        height: 6px;
        -webkit-appearance: none;
        appearance: none;
        background: var(--border);
        border-radius: 999px;
        outline: none;
    }

    .weight-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        background: var(--primary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(21, 128, 61, 0.3);
        transition: transform 0.15s ease;
    }

    .weight-slider::-webkit-slider-thumb:hover {
        transform: scale(1.15);
    }

    .weight-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: var(--primary);
        border-radius: 50%;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(21, 128, 61, 0.3);
    }

    .weight-value-display {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--primary);
        text-align: right;
        margin-top: 4px;
    }

    .weight-hint {
        font-size: 0.65rem;
        color: var(--muted-text);
        margin-top: 2px;
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

    .input-group-text {
        background: var(--card) !important;
        border-color: var(--border) !important;
    }

    .form-control {
        border-color: var(--border);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--focus-ring);
    }

    .badge.text-bg-success {
        background-color: var(--soft-bg) !important;
        color: var(--primary) !important;
        border: 1px solid var(--border-light);
    }

    .alert-info {
        background: var(--soft-bg);
        border-color: var(--border-light);
        color: var(--primary);
    }

    .alert-light {
        background: var(--main-bg);
        border-color: var(--border);
    }

    /* Selection Counter */
    .selection-counter {
        position: sticky;
        bottom: 20px;
        background: var(--card);
        border: 1px solid var(--primary);
        border-radius: var(--radius-lg);
        padding: 12px 20px;
        box-shadow: var(--shadow-lg);
        z-index: 100;
        display: none;
    }

    .selection-counter.active {
        display: block;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Search functionality
        const input = document.getElementById('diagnosis-search');
        const cards = Array.from(document.querySelectorAll('[data-gejala-card]'));
        const emptyState = document.getElementById('diagnosis-empty-state');
        const selectionCounter = document.getElementById('selectionCounter');
        const selectedCount = document.getElementById('selectedCount');

        if (!input) return;

        input.addEventListener('input', () => {
            const query = input.value.trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const haystack = `${card.dataset.kode} ${card.dataset.nama}`.toLowerCase();
                const visible = haystack.includes(query);
                card.classList.toggle('d-none', !visible);
                if (visible) visibleCount += 1;
            });

            if (emptyState) {
                emptyState.classList.toggle('d-none', visibleCount !== 0);
            }
        });

        // Update selection counter
        function updateCounter() {
            const checked = document.querySelectorAll('.symptom-checkbox:checked').length;
            if (selectedCount) {
                selectedCount.textContent = `${checked} gejala dipilih`;
            }
            if (selectionCounter) {
                selectionCounter.classList.toggle('active', checked > 0);
            }
        }

        // Initial counter update
        updateCounter();

        // Weight slider functionality - toggle visibility based on checkbox state
        const checkboxes = document.querySelectorAll('.symptom-checkbox');
        checkboxes.forEach(checkbox => {
            const sliderId = checkbox.dataset.weightSlider;
            const sliderContainer = document.getElementById(sliderId)?.closest('.weight-slider-container');

            // Initial state: show/hide slider based on checkbox
            if (sliderContainer) {
                sliderContainer.style.display = checkbox.checked ? 'block' : 'none';
            }

            checkbox.addEventListener('change', function() {
                if (sliderContainer) {
                    sliderContainer.style.display = this.checked ? 'block' : 'none';
                }
                updateCounter();
            });
        });

        const sliders = document.querySelectorAll('.weight-slider');
        sliders.forEach(slider => {
            const display = slider.parentElement.querySelector('.weight-value-display');
            if (display) {
                display.textContent = `${slider.value}%`;

                slider.addEventListener('input', function() {
                    display.textContent = `${this.value}%`;

                    // Update gradient color based on value
                    const percentage = this.value;
                    const color = percentage >= 70 ? 'var(--primary)' : (percentage >= 40 ?
                        'var(--warning)' : 'var(--danger)');
                    this.style.background =
                        `linear-gradient(to right, ${color} 0%, ${color} ${percentage}%, var(--border) ${percentage}%, var(--border) 100%)`;
                });

                // Initialize gradient
                const percentage = slider.value;
                const color = percentage >= 70 ? 'var(--primary)' : (percentage >= 40 ? 'var(--warning)' :
                    'var(--danger)');
                slider.style.background =
                    `linear-gradient(to right, ${color} 0%, ${color} ${percentage}%, var(--border) ${percentage}%, var(--border) 100%)`;
            }
        });

        // Auto-check checkbox when slider is moved with significant value
        sliders.forEach(slider => {
            slider.addEventListener('change', function() {
                const card = this.closest('.symptom-card');
                if (card) {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    if (checkbox && parseInt(this.value) > 50 && !checkbox.checked) {
                        checkbox.checked = true;
                        // Show slider container
                        const sliderContainer = this.closest('.weight-slider-container');
                        if (sliderContainer) {
                            sliderContainer.style.display = 'block';
                        }
                        // Trigger visual update
                        const event = new Event('change', {
                            bubbles: true
                        });
                        checkbox.dispatchEvent(event);
                        updateCounter();
                    }
                }
            });
        });

        // Form validation - check if at least one symptom is selected
        const form = document.querySelector("form[action='{{ route('user.diagnosis.identifikasi') }}']");
        if (form) {
            form.addEventListener('submit', function(e) {
                const checkedCheckboxes = document.querySelectorAll('.symptom-checkbox:checked');
                if (checkedCheckboxes.length === 0) {
                    e.preventDefault();
                    // Show toast notification
                    const toastHtml = `
                        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Pilih minimal satu gejala
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
                }
            });
        }
    });
</script>
@endpush

@section('content')
@guest
<div class="container py-4">
    @endguest

    {{-- Back to Dashboard --}}
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('user.dashboard') }}" class="btn btn-light-secondary btn-sm" style="padding: 0.45rem 0.75rem; font-size: 0.8rem;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <span style="font-size: 0.85rem; color: var(--text-muted);">Kembali ke Beranda</span>
    </div>

    {{-- Step Guide --}}
    <div class="alert alert-light border mb-4">
        <div class="d-flex align-items-start gap-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                style="width: 32px; height: 32px; flex-shrink: 0;">
                <i class="bi bi-lightbulb"></i>
            </div>
            <div>
                <h6 class="mb-1">Cara Diagnosis</h6>
                <p class="mb-0 small text-muted">
                    Centang gejala yang terlihat pada tanaman padi Anda. Gunakan slider untuk mengatur tingkat keyakinan
                    jika diperlukan.
                    Klik <strong>"Identifikasi Penyakit"</strong> untuk melihat hasil diagnosis.
                </p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clipboard-check me-2"></i>Pilih Gejala yang Dialami Tanaman</span>
            <span class="badge bg-success-subtle text-success" id="totalGejala">{{ $gejala->count() }} gejala
                tersedia</span>
        </div>
        <div class="card-body">
            @guest
            <div class="alert alert-info">
                Anda bisa melakukan diagnosis tanpa login. Login hanya dibutuhkan jika hasil diagnosis ingin disimpan ke
                riwayat pribadi.
            </div>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('login') }}" class="btn btn-outline-success">Login untuk Simpan Hasil</a>
            </div>
            @endguest
            <form action="{{ route('user.diagnosis.identifikasi') }}" method="POST">
                @csrf
                <div class="symptom-toolbar p-3 p-lg-4 mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-7">
                            <div class="fw-semibold mb-1">Cari diagnosa berdasarkan gejala yang terlihat</div>
                            <div class="small text-muted">
                                Ketik kode atau nama gejala untuk mempercepat pencarian sebelum Anda mencentang gejala
                                yang sesuai.
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" id="diagnosis-search" class="form-control border-start-0"
                                    placeholder="Cari gejala, misalnya bercak daun atau bulir hampa">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach($gejala as $item)
                    <div class="col-md-6 col-xl-4" data-gejala-card data-kode="{{ $item->kode }}"
                        data-nama="{{ $item->nama_gejala }}">
                        <div class="form-check symptom-card position-relative h-100">
                            <input class="form-check-input symptom-checkbox" type="checkbox" name="gejala[]"
                                value="{{ $item->id }}" id="gejala-{{ $item->id }}"
                                data-weight-slider="weight-slider-{{ $item->id }}"
                                {{ in_array($item->id, old('gejala', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="gejala-{{ $item->id }}">
                                <div class="symptom-shell h-100">
                                    @if($item->gambar_url)
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_gejala }}"
                                        class="symptom-image mb-3">
                                    @else
                                    <div class="symptom-empty d-flex align-items-center justify-content-center mb-3">
                                        <i class="bi bi-image fs-1 text-secondary"></i>
                                    </div>
                                    @endif
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                        <span class="badge text-bg-success">{{ $item->kode }}</span>
                                        <span class="small text-muted">Pilih jika gejala terlihat</span>
                                    </div>
                                    <div class="fw-semibold">{{ $item->nama_gejala }}</div>

                                    {{-- Weight Slider - Only visible when checked --}}
                                    <div class="weight-slider-container">
                                        <div class="weight-slider-label">
                                            <i class="bi bi-sliders"></i>
                                            Tingkat Keyakinan Gejala
                                        </div>
                                        <input type="range" class="weight-slider" id="weight-slider-{{ $item->id }}"
                                            name="gejala_weights[{{ $item->id }}]" min="0" max="100"
                                            value="{{ old('gejala_weights.' . $item->id, 80) }}"
                                            data-symptom-id="{{ $item->id }}">
                                        <div class="weight-value-display">{{ old('gejala_weights.' . $item->id, 80) }}%
                                        </div>
                                        <div class="weight-hint">
                                            <span class="text-success">≥70%:</span> Yakin
                                            <span class="text-warning ms-2">40-69%:</span> Ragu-ragu
                                            <span class="text-danger ms-2">&lt;40%:</span> Tidak yakin
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div id="diagnosis-empty-state" class="alert alert-light border text-muted mt-3 d-none">
                    Gejala yang Anda cari belum ditemukan. Coba kata kunci lain.
                </div>
                @error('gejala')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

                {{-- Floating Selection Counter --}}
                <div class="selection-counter" id="selectionCounter">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            <span class="fw-semibold" id="selectedCount">0 gejala dipilih</span>
                        </div>
                        <button type="submit" class="btn btn-spk">
                            <i class="bi bi-search me-1"></i>Identifikasi Penyakit
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4" id="submitBtnContainer">
                    @guest

                    @endguest
                    <button type="submit" class="btn btn-spk">
                        <i class="bi bi-search me-1"></i>Identifikasi Penyakit
                    </button>
                </div>
            </form>
        </div>
    </div>
    @guest
</div>
@endguest
@endsection