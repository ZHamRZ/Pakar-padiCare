@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@push('styles')
<style>
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 12px 24px rgba(15, 23, 42, .12);
}

.profile-fallback {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #14532d, #2d8a4e);
    color: #fff;
    font-size: 2.2rem;
    font-weight: 700;
    border: 4px solid #fff;
    box-shadow: 0 12px 24px rgba(15, 23, 42, .12);
}

.info-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding: .9rem 0;
    border-bottom: 1px dashed #e2e8f0;
    align-items: start;
}

.info-row:last-child {
    border-bottom: 0;
}

.badge-verified {
    font-size: .72rem;
    padding: .25em .6em;
}

.collapse-form {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: .5rem;
    padding: 1rem;
    margin-top: .75rem;
}
</style>
@endpush

@section('content')

{{-- ============================================================
     NOTIFIKASI GLOBAL
     - Flash success/error dari controller
     - Status pengiriman email verifikasi
============================================================ --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>
    <strong>Data belum disimpan.</strong> Periksa kembali input yang ditandai.
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@php
// Route helper: tentukan sekali, gunakan di semua form
$profileRoute = $user->isAdmin()
? route('admin.profile.update')
: route('user.profile.update');
$verificationResendAvailableAt = (int) session('verification_resend_available_at', 0);
$verificationResendCooldown = max(0, $verificationResendAvailableAt - now()->timestamp);
@endphp

<div class="row g-4">

    {{-- ============================================================
         KOLOM KIRI — Foto Profil + Identitas Singkat
    ============================================================ --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Profil Utama</div>
            <div class="card-body text-center">

                {{-- Avatar: foto atau inisial --}}
                @if($user->foto_profil_url)
                <img src="{{ $user->foto_profil_url }}" alt="Foto Profil {{ $user->nama }}" class="profile-avatar mb-3">
                @else
                <div class="profile-fallback mx-auto mb-3">
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                </div>
                @endif

                <h5 class="fw-bold mb-1">{{ $user->nama }}</h5>

                {{-- Badge role --}}
                <span class="badge bg-{{ $user->isAdmin() ? 'danger' : 'success' }} mb-3">
                    {{ $user->isAdmin() ? 'Admin' : 'Petani' }}
                </span>

                {{-- Status email singkat di kartu profil --}}
                @if($user->email)
                <div class="small text-muted mb-3">
                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                    @if($user->email_verified_at)
                    <span class="badge bg-success badge-verified ms-1">✓ Terverifikasi</span>
                    @else
                    <span class="badge bg-warning text-dark badge-verified ms-1">⚠ Belum Verifikasi</span>
                    @endif
                </div>
                @endif

                {{-- Edit foto --}}
                <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="collapse"
                    data-bs-target="#form-foto" aria-expanded="false" aria-controls="form-foto">
                    <i class="bi bi-camera me-1"></i>Edit Foto Profil
                </button>

                <div id="form-foto" class="collapse mt-3">
                    <form action="{{ $profileRoute }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Hidden fields wajib agar field lain tidak ter-reset --}}
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                        <input type="hidden" name="catatan_profil" value="{{ $user->catatan_profil }}">

                        <div class="mb-2">
                            <input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp"
                                class="form-control form-control-sm @error('foto_profil') is-invalid @enderror">
                            @error('foto_profil')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPG, PNG, atau WebP — maks. 2MB</div>
                        </div>

                        <button type="submit" class="btn btn-spk btn-sm w-100">
                            <i class="bi bi-upload me-1"></i>Simpan Foto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         KOLOM KANAN — Informasi Akun + Keamanan
    ============================================================ --}}
    <div class="col-lg-8">

        {{-- ---- INFORMASI AKUN ---- --}}
        <div class="card mb-4">
            <div class="card-header">Informasi Akun</div>
            <div class="card-body">

                {{-- ── Nama ─────────────────────────────────── --}}
                <div class="info-row">
                    <div>
                        <div class="small text-muted">Nama Lengkap</div>
                        <div class="fw-semibold">{{ $user->nama }}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-nama">Edit</button>
                </div>

                <div id="edit-nama" class="collapse collapse-form">
                    <form action="{{ $profileRoute }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                        <input type="hidden" name="catatan_profil" value="{{ $user->catatan_profil }}">

                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}"
                                    class="form-control @error('nama') is-invalid @enderror" required>
                                @error('nama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-spk w-100">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ── Username ──────────────────────────────── --}}
                <div class="info-row">
                    <div>
                        <div class="small text-muted">Username</div>
                        <div class="fw-semibold">{{ $user->username }}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-username">Edit</button>
                </div>

                <div id="edit-username" class="collapse collapse-form">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                        <input type="hidden" name="catatan_profil" value="{{ $user->catatan_profil }}">

                        <label class="form-label small fw-semibold">Username</label>
                        <div class="form-text mb-2">Hanya huruf kecil, angka, dan underscore. Username Harus unik.</div>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="form-control @error('username') is-invalid @enderror" pattern="[a-z0-9_]+"
                                    required>
                                @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-spk w-100">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ── Email (baru dari kode baru) ──────────── --}}
                <div class="info-row">
                    <div>
                        <div class="small text-muted">Email</div>
                        <div class="fw-semibold">
                            {{ $user->email ?: 'Belum diisi' }}
                            @if($user->email && $user->email_verified_at)
                            <span class="badge bg-success badge-verified ms-1">✓ Terverifikasi</span>
                            @elseif($user->email && !$user->email_verified_at)
                            <span class="badge bg-warning text-dark badge-verified ms-1">⚠ Belum Verifikasi</span>
                            @endif
                        </div>
                        {{-- Peringatan jika email belum verifikasi (untuk reset password) --}}
                        @if($user->email && !$user->email_verified_at)
                        <div class="small text-warning mt-1">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Verifikasi email untuk mengaktifkan fitur reset password.
                            <form method="POST" action="{{ route('verification.send') }}" class="d-inline verification-resend-form">
                                @csrf
                                <button type="submit"
                                    class="btn btn-link btn-sm p-0 text-warning fw-semibold verification-resend-button"
                                    data-cooldown="{{ $verificationResendCooldown }}">
                                    Kirim Ulang
                                </button>
                            </form>
                        </div>
                        <div class="small text-muted mt-1">
                            Tidak menerima email? Periksa kembali apakah email sudah diisi dengan benar,
                            pastikan email tersebut milik Anda, lalu cek kotak masuk atau folder spam.
                        </div>
                        @endif
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-email">Edit</button>
                </div>

                <div id="edit-email" class="collapse collapse-form @error('email') show @enderror">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                        <input type="hidden" name="catatan_profil" value="{{ $user->catatan_profil }}">

                        <label class="form-label small fw-semibold">Email</label>
                        <div class="form-text mb-2">
                            Opsional. Digunakan untuk reset password dan notifikasi penting.
                            Setelah disimpan, link verifikasi akan dikirim ke email baru.
                        </div>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="contoh@email.com"
                                    pattern="^(?!.*\.\.)[A-Za-z0-9](?:[A-Za-z0-9._%+\-]{0,62}[A-Za-z0-9])?@(?:[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?\.)+[A-Za-z]{2,}$"
                                    title="Masukkan email valid, misalnya nama@gmail.com, nama@yahoo.com, atau nama@domain.co.id">
                                @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-spk w-100">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ── Alamat ────────────────────────────────── --}}
                <div class="info-row">
                    <div>
                        <div class="small text-muted">Alamat</div>
                        <div class="fw-semibold">{{ $user->alamat ?: 'Belum diisi' }}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-alamat">Edit</button>
                </div>

                <div id="edit-alamat" class="collapse collapse-form">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="catatan_profil" value="{{ $user->catatan_profil }}">

                        <label class="form-label small fw-semibold">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3"
                            class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-spk mt-2">Simpan</button>
                    </form>
                </div>

                {{-- ── Catatan Profil ────────────────────────── --}}
                <div class="info-row">
                    <div>
                        <div class="small text-muted">Catatan Profil</div>
                        <div class="fw-semibold">{{ $user->catatan_profil ?: 'Belum diisi' }}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-catatan">Edit</button>
                </div>

                <div id="edit-catatan" class="collapse collapse-form">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">

                        <label class="form-label small fw-semibold">Catatan Profil</label>
                        <textarea name="catatan_profil" rows="3"
                            class="form-control @error('catatan_profil') is-invalid @enderror"
                            placeholder="Tuliskan informasi tambahan tentang Anda...">{{ old('catatan_profil', $user->catatan_profil) }}</textarea>
                        @error('catatan_profil')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-spk mt-2">Simpan</button>
                    </form>
                </div>

            </div>{{-- /card-body --}}
        </div>{{-- /card informasi --}}

                {{-- ---- KEAMANAN AKUN ---- --}}
                <div class="card">
                    <div class="card-header">Keamanan Akun</div>
                    <div class="card-body">

                        {{-- ── Ganti Password ───────────────────────── --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="small text-muted">Password</div>
                                <div class="fw-semibold">••••••••</div>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-shield-check me-1 text-success"></i>
                                    Disembunyikan demi keamanan akun Anda
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#edit-password">Edit</button>
                        </div>

                        <div id="edit-password" class="collapse collapse-form">
                            <form action="{{ $profileRoute }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="nama" value="{{ $user->nama }}">
                                <input type="hidden" name="username" value="{{ $user->username }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                                <input type="hidden" name="catatan_profil" value="{{ $user->catatan_profil }}">

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">
                                            Password Lama <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" name="password_lama"
                                            class="form-control @error('password_lama') is-invalid @enderror"
                                            autocomplete="current-password" required>
                                        @error('password_lama')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">
                                            Password Baru <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            autocomplete="new-password" minlength="8" required>
                                        @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Minimal 8 karakter</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">
                                            Konfirmasi Password Baru <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            autocomplete="new-password" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-spk mt-3">
                                    <i class="bi bi-lock me-1"></i>Simpan Password Baru
                                </button>
                            </form>
                        </div>

                        {{-- ── Reset via Email (banner info jika email belum ada) ── --}}
                        @if(!$user->email)
                        <hr class="my-3">
                        <div class="alert alert-warning mb-0 py-2 px-3 small" role="alert">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Belum ada email terdaftar.</strong>
                            Tambahkan email di atas untuk mengaktifkan fitur reset password jika Anda lupa password
                            lama.
                        </div>
                        @endif

                    </div>{{-- /card-body --}}
                </div>{{-- /card keamanan --}}

            </div>{{-- /col-lg-8 --}}
        </div>{{-- /row --}}
        @endsection

        @push('scripts')
        <script>
        /**
         * Auto-tutup collapse lain ketika user membuka collapse baru.
         * Mencegah banyak form terbuka sekaligus → mengurangi kebingungan user.
         */
        document.querySelectorAll('.collapse').forEach(function(collapseEl) {
            collapseEl.addEventListener('show.bs.collapse', function() {
                document.querySelectorAll('.collapse.show').forEach(function(openEl) {
                    if (openEl !== collapseEl) {
                        bootstrap.Collapse.getInstance(openEl)?.hide();
                    }
                });
            });
        });

        document.querySelectorAll('.verification-resend-button').forEach(function(button) {
            var initialText = button.textContent.trim();
            var remaining = parseInt(button.dataset.cooldown || '0', 10);
            var form = button.closest('.verification-resend-form');
            var countdownTimer = null;

            function renderCountdown() {
                if (remaining <= 0) {
                    button.disabled = false;
                    button.textContent = initialText;
                    if (countdownTimer) {
                        clearInterval(countdownTimer);
                    }
                    return;
                }

                button.disabled = true;
                button.textContent = 'Kirim ulang (' + remaining + ' detik)';
                remaining -= 1;
            }

            if (remaining > 0) {
                renderCountdown();
                countdownTimer = setInterval(renderCountdown, 1000);
            }

            form?.addEventListener('submit', function() {
                remaining = 30;
                renderCountdown();
                countdownTimer = setInterval(renderCountdown, 1000);
        /**
         * ============================================
         * CROP IMAGE FUNCTIONALITY - REFACTORED
         * Modern cropper behavior like Instagram/Google
         * ============================================
         */
        let cropModalInstance = null;
        let currentImageFile = null;
        let imageElement = null;
        let imgNaturalWidth = 0;
        let imgNaturalHeight = 0;
        
        // Crop state - using container-relative coordinates
        let scale = 1;           // Zoom level (1 = fit to container)
        let translateX = 0;      // Pan X in pixels
        let translateY = 0;      // Pan Y in pixels
        let isDragging = false;
        let lastX = 0;
        let lastY = 0;
        
        // Container dimensions
        let containerWidth = 400;
        let containerHeight = 400;

        // Initialize file input listener
        const fotoInput = document.querySelector('input[name="foto_profil"]');
        if (fotoInput) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    showErrorToast('Format file tidak didukung. Gunakan JPG, PNG, atau WebP.');
                    this.value = '';
                    return;
                }

                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    showErrorToast('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                currentImageFile = file;
                openCropModal(file);
            });
        }

        function openCropModal(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const modalEl = document.getElementById('cropModal');
                if (!modalEl) {
                    showErrorToast('Modal crop tidak ditemukan.');
                    return;
                }

                cropModalInstance = new bootstrap.Modal(modalEl);
                cropModalInstance.show();

                // Wait for modal to be fully shown
                modalEl.addEventListener('shown.bs.modal', function initCrop() {
                    modalEl.removeEventListener('shown.bs.modal', initCrop);
                    
                    imageElement = document.getElementById('cropImage');
                    const avatarPreview = document.getElementById('avatarPreview');
                    const cropContainer = document.getElementById('cropContainer');
                    
                    if (!imageElement || !avatarPreview || !cropContainer) {
                        showErrorToast('Elemen crop tidak ditemukan.');
                        return;
                    }

                    // Set image source
                    imageElement.src = e.target.result;
                    
                    // Reset state
                    scale = 1;
                    translateX = 0;
                    translateY = 0;
                    document.getElementById('zoomRange').value = 1;

                    // When image loads, initialize dimensions
                    imageElement.onload = function() {
                        imgNaturalWidth = imageElement.naturalWidth;
                        imgNaturalHeight = imageElement.naturalHeight;
                        
                        // Get container dimensions
                        const rect = cropContainer.getBoundingClientRect();
                        containerWidth = rect.width;
                        containerHeight = rect.height;
                        
                        // Calculate initial scale to fit image in container (cover mode)
                        const scaleX = containerWidth / imgNaturalWidth;
                        const scaleY = containerHeight / imgNaturalHeight;
                        scale = Math.max(scaleX, scaleY);
                        
                        // Center the image
                        translateX = 0;
                        translateY = 0;
                        
                        // Update zoom slider max based on image
                        const maxZoom = Math.max(3, Math.min(5, 1 / Math.min(scaleX, scaleY)));
                        document.getElementById('zoomRange').max = maxZoom.toFixed(1);
                        document.getElementById('zoomRange').value = scale.toFixed(1);
                        
                        updateTransform();
                        renderPreview();
                    };
                }, { once: true });
            };
            reader.readAsDataURL(file);
        }

        function updateTransform() {
            if (!imageElement) return;
            
            // Apply transform: translate then scale, centered
            imageElement.style.transformOrigin = 'center center';
            imageElement.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
        }

        function renderPreview() {
            const avatarPreview = document.getElementById('avatarPreview');
            if (!avatarPreview || !imageElement) return;

            const canvas = document.createElement('canvas');
            const previewSize = 200;
            canvas.width = previewSize;
            canvas.height = previewSize;
            const ctx = canvas.getContext('2d');

            // Clear with white background
            ctx.fillStyle = '#f0fdf4';
            ctx.fillRect(0, 0, previewSize, previewSize);

            // Create circular clip
            ctx.beginPath();
            ctx.arc(previewSize / 2, previewSize / 2, previewSize / 2, 0, Math.PI * 2);
            ctx.closePath();
            ctx.clip();

            // Calculate source rectangle in image coordinates
            const imgCenterX = containerWidth / 2 + translateX;
            const imgCenterY = containerHeight / 2 + translateY;
            
            const srcX = (-imgCenterX + containerWidth / 2) / scale + imgNaturalWidth / 2;
            const srcY = (-imgCenterY + containerHeight / 2) / scale + imgNaturalHeight / 2;
            const srcWidth = containerWidth / scale;
            const srcHeight = containerHeight / scale;

            try {
                ctx.drawImage(
                    imageElement,
                    srcX, srcY, srcWidth, srcHeight,
                    0, 0, previewSize, previewSize
                );
            } catch (err) {
                console.error('Preview render error:', err);
            }

            avatarPreview.src = canvas.toDataURL('image/jpeg', 0.9);
        }

        // Zoom slider handler
        const zoomRange = document.getElementById('zoomRange');
        if (zoomRange) {
            zoomRange.addEventListener('input', function() {
                const newScale = parseFloat(this.value);
                const scaleFactor = newScale / scale;
                translateX *= scaleFactor;
                translateY *= scaleFactor;
                scale = newScale;
                updateTransform();
                renderPreview();
            });
        }

        // Pan/drag functionality
        const cropContainer = document.getElementById('cropContainer');
        if (cropContainer) {
            cropContainer.addEventListener('mousedown', startDrag);
            cropContainer.addEventListener('touchstart', startDrag, { passive: false });
            
            document.addEventListener('mousemove', drag);
            document.addEventListener('touchmove', drag, { passive: false });
            
            document.addEventListener('mouseup', endDrag);
            document.addEventListener('touchend', endDrag);
        }

        function startDrag(e) {
            if (e.type === 'touchstart') e.preventDefault();
            isDragging = true;
            lastX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
            lastY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
            if (cropContainer) cropContainer.style.cursor = 'grabbing';
        }

        function drag(e) {
            if (!isDragging) return;
            if (e.type === 'touchmove') e.preventDefault();

            const currentX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
            const currentY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;

            translateX += currentX - lastX;
            translateY += currentY - lastY;

            lastX = currentX;
            lastY = currentY;

            updateTransform();
            renderPreview();
        }

        function endDrag() {
            isDragging = false;
            if (cropContainer) cropContainer.style.cursor = 'move';
        }

        // Reset button
        const resetBtn = document.getElementById('resetCropBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                scale = 1;
                translateX = 0;
                translateY = 0;
                document.getElementById('zoomRange').value = 1;
                updateTransform();
                
                if (currentImageFile) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const avatarPreview = document.getElementById('avatarPreview');
                        if (avatarPreview) avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(currentImageFile);
                }
            });
        }

        // Save/Crop button
        const saveBtn = document.getElementById('saveCropBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (!imageElement || !currentImageFile) {
                    showErrorToast('Tidak ada gambar untuk diproses.');
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

                const canvas = document.createElement('canvas');
                const outputSize = 400;
                canvas.width = outputSize;
                canvas.height = outputSize;
                const ctx = canvas.getContext('2d');

                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, outputSize, outputSize);

                const imgCenterX = containerWidth / 2 + translateX;
                const imgCenterY = containerHeight / 2 + translateY;
                
                const srcX = (-imgCenterX + containerWidth / 2) / scale + imgNaturalWidth / 2;
                const srcY = (-imgCenterY + containerHeight / 2) / scale + imgNaturalHeight / 2;
                const srcWidth = containerWidth / scale;
                const srcHeight = containerHeight / scale;

                try {
                    ctx.drawImage(imageElement, srcX, srcY, srcWidth, srcHeight, 0, 0, outputSize, outputSize);
                } catch (err) {
                    console.error('Crop error:', err);
                    showErrorToast('Gagal memproses gambar.');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Simpan';
                    return;
                }

                const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.href;

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.content;
                    form.appendChild(csrfInput);
                }

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);

                const croppedInput = document.createElement('input');
                croppedInput.type = 'hidden';
                croppedInput.name = 'cropped_image';
                croppedInput.value = croppedDataUrl;
                form.appendChild(croppedInput);

                ['nama', 'username', 'email', 'alamat', 'catatan_profil'].forEach(function(fieldName) {
                    const existingInput = document.querySelector(`input[name="${fieldName}"], textarea[name="${fieldName}"]`);
                    if (existingInput) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = fieldName;
                        hiddenInput.value = existingInput.value;
                        form.appendChild(hiddenInput);
                    }
                });

                document.body.appendChild(form);
                form.submit();
            });
        }

        // Handle modal close
        const cropModalEl = document.getElementById('cropModal');
        if (cropModalEl) {
            cropModalEl.addEventListener('hidden.bs.modal', function() {
                if (fotoInput && !saveBtn?.dataset.saved) fotoInput.value = '';
                currentImageFile = null;
                imageElement = null;
            });
        }
        </script>
        @endpush
