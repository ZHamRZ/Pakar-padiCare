@extends('layouts.app')

@section('title', 'Kelola Kriteria CF')
@section('page-title', 'Kelola Kriteria CF')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --green-50: #f0fdf4;
    --green-100: #dcfce7;
    --green-200: #bbf7d0;
    --green-500: #22c55e;
    --green-600: #16a34a;
    --green-700: #15803d;
    --green-800: #166534;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-400: #94a3b8;
    --slate-500: #64748b;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-800: #1e293b;
    --slate-900: #0f172a;
    --r-sm: 8px;
    --r-md: 12px;
    --r-lg: 16px;
    --r-xl: 20px;
    --shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
    --shadow-md: 0 4px 16px rgba(15, 23, 42, .07);
}

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.cf-hero {
    background: linear-gradient(135deg, #0f4c28 0%, #1a7a42 50%, #22a856 100%);
    color: #fff;
    border-radius: var(--r-xl);
    position: relative;
    overflow: hidden;
    padding: 36px 42px;
    margin-bottom: 24px;
}

.cf-hero::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -40px;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, .06) 0%, transparent 70%);
    pointer-events: none;
}

.cf-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("{{ asset('assets/bagraound dashboard.png') }}");
    background-size: cover;
    background-position: center;
    opacity: 0.15;
    pointer-events: none;
}

.cf-hero-content {
    position: relative;
    z-index: 1;
}

.cf-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: rgba(255, 255, 255, .15);
    border: 1px solid rgba(255, 255, 255, .25);
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    color: rgba(255, 255, 255, .9);
    margin-bottom: 14px;
    letter-spacing: .04em;
}

.cf-hero-title {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 10px;
    color: #fff;
}

.cf-hero-sub {
    color: rgba(255, 255, 255, .65);
    font-size: 13.5px;
    max-width: 600px;
    line-height: 1.7;
    margin: 0;
}

.cf-hero-stats {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 18px;
}

.cf-hero-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, .1);
    border: 1px solid rgba(255, 255, 255, .15);
    border-radius: 100px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, .85);
}

.cf-hero-stat-pill i {
    color: #4ade80;
}

.cf-section {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.cf-section-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--slate-100);
    background: var(--slate-50);
    display: flex;
    align-items: center;
    gap: 10px;
}

.cf-section-header i {
    color: var(--green-600);
    font-size: 18px;
}

.cf-section-title {
    font-weight: 700;
    font-size: 15px;
    color: var(--slate-800);
}

.cf-section-desc {
    font-size: 12px;
    color: var(--slate-500);
    margin-left: auto;
}

.cf-section-body {
    padding: 24px;
}

.pref-card {
    background: var(--slate-50);
    border: 1px solid var(--slate-200);
    border-radius: var(--r-md);
    padding: 20px;
    transition: border-color .2s;
}

.pref-card:hover {
    border-color: var(--green-200);
}

.pref-card-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--slate-700);
    margin-bottom: 4px;
}

.pref-card-desc {
    font-size: 11px;
    color: var(--slate-500);
    margin-bottom: 12px;
    line-height: 1.5;
}

.pref-card-input {
    width: 100%;
    border: 1.5px solid var(--slate-200);
    border-radius: var(--r-sm);
    padding: 10px 14px;
    font-size: 14px;
    font-weight: 600;
    color: var(--slate-800);
    font-family: 'Plus Jakarta Sans', sans-serif;
    transition: border-color .2s;
}

.pref-card-input:focus {
    border-color: var(--green-500);
    outline: none;
}

.cf-slider-section {
    background: var(--slate-50);
    border-radius: var(--r-sm);
    padding: 14px 16px;
}

.cf-slider-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.cf-slider-label {
    font-size: 12px;
    color: var(--slate-500);
    font-weight: 600;
}

.cf-slider-value {
    font-weight: 800;
    font-size: 18px;
    color: var(--green-700);
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.cf-slider {
    width: 100%;
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    border-radius: 999px;
    background: var(--slate-200);
    outline: none;
}

.cf-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--green-600);
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(21, 128, 61, .3);
    transition: transform .15s;
}

.cf-slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
}

.cf-slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--green-600);
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 8px rgba(21, 128, 61, .3);
}

.cf-slider-scale {
    display: flex;
    justify-content: space-between;
    margin-top: 6px;
    font-size: 10px;
    color: var(--slate-400);
}

.cf-save-bar {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: var(--r-lg);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    margin-top: 24px;
}

.cf-save-info {
    font-size: 13px;
    color: var(--slate-500);
}

.cf-save-info strong {
    color: var(--slate-700);
}

.btn-cf-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    background: linear-gradient(135deg, var(--green-600), var(--green-500));
    color: #fff;
    border: none;
    border-radius: var(--r-md);
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(21, 128, 61, .3);
    transition: transform .15s, box-shadow .2s;
}

.btn-cf-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(21, 128, 61, .4);
}

.btn-cf-save:disabled {
    opacity: .6;
    cursor: not-allowed;
    transform: none;
}

.anim-fade-up {
    opacity: 0;
    transform: translateY(14px);
    animation: fadeUp .45s ease forwards;
}

@keyframes fadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.anim-fade-up:nth-child(1) {
    animation-delay: .05s;
}

.anim-fade-up:nth-child(2) {
    animation-delay: .10s;
}

.anim-fade-up:nth-child(3) {
    animation-delay: .15s;
}

.anim-fade-up:nth-child(4) {
    animation-delay: .20s;
}
</style>
@endpush

@section('content')

<div class="cf-hero anim-fade-up">
    <div class="cf-hero-content">
        <div class="cf-hero-badge">
            <i class="bi bi-sliders"></i> Kelola Kriteria CF · Preferensi Sistem
        </div>
        <h2 class="cf-hero-title">
            Atur preferensi konteks untuk memperkuat logika Certainty Factor
        </h2>
        <p class="cf-hero-sub">
            Preferensi ini mempengaruhi bagaimana sistem menyesuaikan CF berdasarkan budget pengguna. Bukan weighting
            ala SAW, tapi adjustment kontekstual yang sesuai metode CF.
        </p>
        <div class="cf-hero-stats">
            <span class="cf-hero-stat-pill"><i class="bi bi-wallet2"></i> Threshold Budget</span>
            <span class="cf-hero-stat-pill"><i class="bi bi-shield-check"></i> Default Confidence</span>
        </div>
    </div>
</div>

<form id="kriteriaForm">
    @csrf

    {{-- Budget Thresholds --}}
    <div class="cf-section mb-4 anim-fade-up">
        <div class="cf-section-header">
            <i class="bi bi-wallet2"></i>
            <div>
                <div class="cf-section-title">Batas Anggaran

                </div>
            </div>
            <span class="cf-section-desc">Menentukan preset berdasarkan luas lahan × harga produk</span>
        </div>
        <div class="cf-section-body">
            <div class="alert"
                style="background:var(--green-50); border:1px solid var(--green-200); border-radius:var(--r-md); padding:16px 20px; margin-bottom:20px;">
                <div style="font-size:13px; font-weight:700; color:var(--green-800); margin-bottom:4px;">
                    <i class="bi bi-info-circle me-1"></i> Cara Mengatur Threshold Budget
                </div>
                <div style="font-size:12px; color:var(--green-700); line-height:1.6;">
                    Sistem akan otomatis memilih preset berdasarkan <strong>total budget = luas lahan (ha) × harga
                        produk per hektar</strong>.
                    Contoh: Lahan 1 ha × pupuk Rp 150.000/ha = Rp 150.000 → masuk kategori <strong>Seimbang</strong>.
                    <br><strong>Rekomendasi:</strong> Hemat ≤100rb, Seimbang 100rb-300rb, Efisiensi >300rb per hektar.
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="pref-card">
                        <div class="pref-card-label">💰 Hemat Biaya</div>
                        <div class="pref-card-desc">Budget per hektar di bawah nilai ini → preset Hemat.
                            <strong>Rekomendasi: 75.000</strong>
                        </div>
                        <input type="number" name="budget_hemat_max"
                            value="{{ old('budget_hemat_max', $budgetPrefs['budget_threshold_hemat']['max_raw'] ?? 75000) }}"
                            class="pref-card-input" min="10000" max="500000" step="10000" placeholder="75000"
                            oninput="validateBudget(this, 500000)">
                        <div style="font-size:11px; color:var(--slate-400); margin-top:6px;">
                            Range: Rp 0 – Rp <span
                                id="hematMaxDisplay">{{ number_format($budgetPrefs['budget_threshold_hemat']['max_raw'] ?? 75000, 0, ',', '.') }}</span>/ha
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pref-card">
                        <div class="pref-card-label">️ Seimbang</div>
                        <div class="pref-card-desc">Budget per hektar di rentang ini → preset Seimbang.
                            <strong>Rekomendasi: 200.000</strong>
                        </div>
                        <input type="number" name="budget_seimbang_max"
                            value="{{ old('budget_seimbang_max', $budgetPrefs['budget_threshold_seimbang']['max_raw'] ?? 200000) }}"
                            class="pref-card-input" min="50000" max="1000000" step="10000" placeholder="200000"
                            oninput="validateBudget(this, 1000000)">
                        <div style="font-size:11px; color:var(--slate-400); margin-top:6px;">
                            Range: Rp <span
                                id="seimbangMinDisplay">{{ number_format($budgetPrefs['budget_threshold_hemat']['max_raw'] ?? 75000, 0, ',', '.') }}</span>
                            – Rp <span
                                id="seimbangMaxDisplay">{{ number_format($budgetPrefs['budget_threshold_seimbang']['max_raw'] ?? 200000, 0, ',', '.') }}</span>/ha
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pref-card">
                        <div class="pref-card-label">⚡ Efisiensi Tinggi</div>
                        <div class="pref-card-desc">Budget per hektar di atas nilai Seimbang → preset Efisiensi</div>
                        <div
                            style="padding: 10px 14px; background: var(--green-50); border: 1px solid var(--green-200); border-radius: var(--r-sm); font-size: 13px; color: var(--green-700); font-weight: 600;">
                            Di atas Rp <span
                                id="efisiensiMinDisplay">{{ number_format($budgetPrefs['budget_threshold_seimbang']['max_raw'] ?? 200000, 0, ',', '.') }}</span>/ha
                        </div>
                        <div style="font-size:10px; color:var(--slate-400); margin-top:6px; text-align:center;">
                            <i class="bi bi-info-circle"></i> Otomatis mengikuti nilai Seimbang
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-12">
            {{-- Default Confidence --}}
            <div class="cf-section mb-4 anim-fade-up">
                <div class="cf-section-header">
                    <i class="bi bi-shield-check"></i>
                    <div class="cf-section-title">Default Tingkat Keyakinan Pengguna</div>
                    <span class="cf-section-desc">Nilai default untuk slider confidence di halaman diagnosis</span>
                </div>
                <div class="cf-section-body">
                    <div class="cf-slider-section">
                        <div class="cf-slider-header">
                            <span class="cf-slider-label">Confidence Default (0 – 100%)</span>
                            <span class="cf-slider-value"
                                id="confidenceValue">{{ number_format(($confidencePref->value['value'] ?? 1.0) * 100, 0) }}%</span>
                        </div>
                        <input type="range" class="cf-slider" id="confidenceSlider" name="default_confidence" min="0"
                            max="1" step="0.05"
                            value="{{ old('default_confidence', $confidencePref->value['value'] ?? 1.0) }}"
                            oninput="updateConfidence(this)">
                        <div class="cf-slider-scale">
                            <span>0% (tidak yakin)</span>
                            <span>50% (ragu-ragu)</span>
                            <span>100% (sangat yakin)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Save Bar --}}
    <div class="cf-save-bar anim-fade-up">
        <div class="cf-save-info">
            <i class="bi bi-info-circle me-1" style="color:var(--green-600);"></i>
            Preferensi ini akan mempengaruhi <strong>konteks diagnosis</strong> dan <strong>adjustment CF</strong>
            secara otomatis.
        </div>
        <button type="button" id="btnSaveAll" class="btn-cf-save" data-bs-toggle="modal"
            data-bs-target="#confirmSaveModal">
            <i class="bi bi-check2-circle"></i> Simpan Preferensi
        </button>
    </div>
</form>

{{-- Modal Konfirmasi Simpan --}}
<div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-labelledby="confirmSaveModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box-lg rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 52px; height: 52px; background: var(--green-50);">
                        <i class="bi bi-check-circle text-success fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="confirmSaveModalLabel">Konfirmasi Simpan</h5>
                        <small class="text-muted">Perubahan preferensi Certainty Factor</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4">
                <p class="mb-2" style="color: var(--text-body);">Apakah Anda yakin ingin menyimpan perubahan preferensi
                    berikut?</p>
                <ul class="mb-0" style="color: var(--text-body); padding-left: 1.25rem;">
                    <li>Threshold Budget Hemat: <strong id="confirmHemat">-</strong></li>
                    <li>Threshold Budget Seimbang: <strong id="confirmSeimbang">-</strong></li>
                    <li>Default Confidence: <strong id="confirmConfidence">-</strong></li>
                </ul>
                <p class="mt-3 mb-0 small text-muted">Perubahan ini akan langsung mempengaruhi hasil diagnosis pengguna.
                </p>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-light-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Batalkan
                </button>
                <button type="button" class="btn btn-spk px-4" id="btnConfirmSave">
                    <i class="bi bi-check-lg me-2"></i>Ya, Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function validateBudget(input, maxAllowed) {
    let val = parseInt(input.value) || 0;
    if (val > maxAllowed) {
        input.value = maxAllowed;
        val = maxAllowed;
    }
    if (val < 0) {
        input.value = 0;
        val = 0;
    }
    const displayId = input.name === 'budget_hemat_max' ? 'hematMaxDisplay' : 'seimbangMaxDisplay';
    const displayEl = document.getElementById(displayId);
    if (displayEl) {
        displayEl.textContent = val.toLocaleString('id-ID');
    }
    if (input.name === 'budget_seimbang_max') {
        const efMin = document.getElementById('efisiensiMinDisplay');
        if (efMin) efMin.textContent = val.toLocaleString('id-ID');
    }
    if (input.name === 'budget_hemat_max') {
        const sMin = document.getElementById('seimbangMinDisplay');
        if (sMin) sMin.textContent = val.toLocaleString('id-ID');
    }
}

function updateConfidence(slider) {
    const pct = Math.round(slider.value * 100);
    document.getElementById('confidenceValue').textContent = pct + '%';
    const pctVal = slider.value * 100;
    slider.style.background =
        `linear-gradient(to right, #16a34a 0%, #22c55e ${pctVal}%, #e2e8f0 ${pctVal}%, #e2e8f0 100%)`;
}

function updateConfirmationModal() {
    const hematMax = parseInt(document.querySelector('[name="budget_hemat_max"]').value || 0);
    const seimbangMax = parseInt(document.querySelector('[name="budget_seimbang_max"]').value || 0);
    const confidence = parseFloat(document.querySelector('[name="default_confidence"]').value || 0);

    document.getElementById('confirmHemat').textContent = 'Rp ' + hematMax.toLocaleString('id-ID') + '/ha';
    document.getElementById('confirmSeimbang').textContent = 'Rp ' + seimbangMax.toLocaleString('id-ID') + '/ha';
    document.getElementById('confirmConfidence').textContent = Math.round(confidence * 100) + '%';
}

function savePreferences() {
    const hematMax = parseInt(document.querySelector('[name="budget_hemat_max"]').value || 0);
    const seimbangMax = parseInt(document.querySelector('[name="budget_seimbang_max"]').value || 0);

    if (seimbangMax <= hematMax) {
        window.showToast('error', 'Validasi Gagal', 'Budget Seimbang harus lebih besar dari Budget Hemat.');
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmSaveModal'));
        if (modal) modal.hide();
        return;
    }

    const btnSave = document.getElementById('btnConfirmSave');
    const originalHtml = btnSave.innerHTML;
    btnSave.disabled = true;
    btnSave.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('budget_hemat_max', document.querySelector('[name="budget_hemat_max"]').value);
    formData.append('budget_seimbang_max', document.querySelector('[name="budget_seimbang_max"]').value);
    formData.append('default_confidence', document.querySelector('[name="default_confidence"]').value);

    fetch('{{ route("admin.kriteria.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.showToast('success', 'Berhasil', 'Preferensi Certainty Factor berhasil diperbarui.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmSaveModal'));
                if (modal) modal.hide();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(err => {
            window.showToast('error', 'Gagal', err.message || 'Terjadi kesalahan saat menyimpan.');
        })
        .finally(() => {
            btnSave.disabled = false;
            btnSave.innerHTML = originalHtml;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cf-slider').forEach(slider => {
        const max = parseFloat(slider.max) || 1;
        const pctVal = (slider.value / max) * 100;
        slider.style.background =
            `linear-gradient(to right, #16a34a 0%, #22c55e ${pctVal}%, #e2e8f0 ${pctVal}%, #e2e8f0 100%)`;
    });

    const hematInput = document.querySelector('[name="budget_hemat_max"]');
    const seimbangInput = document.querySelector('[name="budget_seimbang_max"]');

    function updateBudgetDisplays() {
        const hematMax = parseInt(hematInput.value || 0);
        const seimbangMax = parseInt(seimbangInput.value || 0);

        document.getElementById('hematMaxDisplay').textContent = hematMax.toLocaleString('id-ID');
        document.getElementById('seimbangMinDisplay').textContent = hematMax.toLocaleString('id-ID');
        document.getElementById('seimbangMaxDisplay').textContent = seimbangMax.toLocaleString('id-ID');
        document.getElementById('efisiensiMinDisplay').textContent = seimbangMax.toLocaleString('id-ID');
    }

    hematInput.addEventListener('input', updateBudgetDisplays);
    seimbangInput.addEventListener('input', updateBudgetDisplays);
    updateBudgetDisplays();

    const confirmModal = document.getElementById('confirmSaveModal');
    if (confirmModal) {
        confirmModal.addEventListener('show.bs.modal', updateConfirmationModal);
    }

    const btnConfirmSave = document.getElementById('btnConfirmSave');
    if (btnConfirmSave) {
        btnConfirmSave.addEventListener('click', savePreferences);
    }
});
</script>
@endsection