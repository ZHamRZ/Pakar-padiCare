@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@push('styles')
<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 12px 24px rgba(15, 23, 42, .12);
    transition: transform 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
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

/* Crop Modal Styles */
.crop-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.crop-modal.active {
    display: flex;
}

.crop-container {
    background: #fff;
    border-radius: var(--pc-radius-xl);
    padding: 1.5rem;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--pc-shadow-xl);
}

.crop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--pc-slate-200);
}

.crop-header h5 {
    margin: 0;
    color: var(--pc-slate-900);
    font-weight: 700;
}

.crop-image-container {
    background: var(--pc-slate-100);
    border-radius: var(--pc-radius-lg);
    overflow: hidden;
    margin-bottom: 1.5rem;
    max-height: 500px;
}

.crop-image-container img {
    max-width: 100%;
    display: block;
}

.crop-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.crop-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--pc-green-500);
    box-shadow: var(--pc-shadow-md);
    margin: 1rem auto;
    background: #fff;
}

.crop-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.crop-instructions {
    background: var(--pc-green-50);
    border: 1px solid var(--pc-green-200);
    border-radius: var(--pc-radius-md);
    padding: 1rem;
    margin-bottom: 1rem;
    color: var(--pc-green-900);
    font-size: 0.9rem;
}

.crop-instructions i {
    margin-right: 0.5rem;
    color: var(--pc-green-700);
}

/* Loading overlay */
.crop-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: var(--pc-radius-lg);
}

.spinner-grow-sm {
    width: 2rem;
    height: 2rem;
    color: var(--pc-green-600);
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

                {{-- Edit foto dengan crop --}}
                <button class="btn btn-outline-success btn-sm" type="button" id="btn-edit-foto"
                    aria-label="Edit Foto Profil">
                    <i class="bi bi-camera me-1"></i>Edit Foto Profil
                </button>

                <div class="mt-3">
                    <form id="form-foto-crop" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="foto-profil-input" name="foto_profil" accept="image/jpeg,image/png,image/webp"
                            class="d-none" required>
                    </form>
                    <div class="form-text">JPG, PNG, atau WebP — maks. 2MB</div>
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

                <!--{{-- ── Catatan Profil ────────────────────────── --}}
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
        </div>{{-- /card informasi --}}-->

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
        <!-- Cropper.js Library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            });
        });

        // ============================================
        // PROFIL PHOTO CROP FEATURE
        // ============================================
        (function() {
            let cropper = null;
            let originalImage = null;
            const isUser = {{ auth()->user()->isAdmin() ? 'false' : 'true' }};
            const profileRoute = isUser 
                ? "{{ route('user.profile.photo') }}"
                : "{{ route('admin.profile.photo') }}";
            
            // Elements
            const btnEditFoto = document.getElementById('btn-edit-foto');
            const fotoInput = document.getElementById('foto-profil-input');
            
            // Create crop modal HTML
            const cropModalHTML = `
                <div id="crop-modal" class="crop-modal">
                    <div class="crop-container">
                        <div class="crop-header">
                            <h5><i class="bi bi-crop me-2"></i>Crop Foto Profil</h5>
                            <button type="button" class="btn-close" id="crop-close-btn"></button>
                        </div>
                        
                        <div class="crop-instructions">
                            <i class="bi bi-info-circle-fill"></i>
                            <strong>Tips:</strong> Drag untuk menggeser area, scroll mouse untuk zoom in/out, 
                            atau gunakan tombol di bawah untuk mengatur ukuran.
                        </div>
                        
                        <div class="crop-image-container position-relative">
                            <div id="crop-loading" class="crop-loading d-none">
                                <div class="spinner-grow spinner-grow-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <img id="crop-image" src="" alt="Crop image">
                        </div>
                        
                        <div class="crop-preview">
                            <img id="preview-image" src="" alt="Preview">
                        </div>
                        
                        <div class="crop-actions">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="crop-reset">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="crop-zoom-in">
                                <i class="bi bi-zoom-in me-1"></i>Zoom In
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="crop-zoom-out">
                                <i class="bi bi-zoom-out me-1"></i>Zoom Out
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="crop-rotate-left">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Rotate Left
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="crop-rotate-right">
                                <i class="bi bi-arrow-clockwise me-1"></i>Rotate Right
                            </button>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between gap-2">
                            <button type="button" class="btn btn-outline-danger" id="crop-cancel">
                                <i class="bi bi-x-lg me-1"></i>Batal
                            </button>
                            <button type="button" class="btn btn-spk" id="crop-save">
                                <i class="bi bi-check-lg me-1"></i>Simpan & Upload
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            if (!document.getElementById('crop-modal')) {
                document.body.insertAdjacentHTML('beforeend', cropModalHTML);
            }
            
            const cropModal = document.getElementById('crop-modal');
            const cropImage = document.getElementById('crop-image');
            const previewImage = document.getElementById('preview-image');
            const cropLoading = document.getElementById('crop-loading');
            
            // Open file picker when edit button clicked
            btnEditFoto?.addEventListener('click', function() {
                fotoInput.click();
            });
            
            // Handle file selection
            fotoInput?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (!file) return;
                
                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal adalah 2MB. Silakan pilih file yang lebih kecil.',
                        confirmButtonColor: '#1e6b3c'
                    });
                    fotoInput.value = '';
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Hanya file JPG, PNG, atau WebP yang diperbolehkan.',
                        confirmButtonColor: '#1e6b3c'
                    });
                    fotoInput.value = '';
                    return;
                }
                
                // Read and display image
                const reader = new FileReader();
                reader.onload = function(event) {
                    originalImage = event.target.result;
                    cropImage.src = originalImage;
                    cropModal.classList.add('active');
                    
                    // Initialize cropper after image loads
                    cropImage.onload = function() {
                        initCropper();
                    };
                };
                reader.readAsDataURL(file);
            });
            
            // Initialize Cropper.js
            function initCropper() {
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1, // Square crop for profile
                    viewMode: 1,    // Restrict crop box to image
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    responsive: true,
                    background: false,
                    ready: function() {
                        updatePreview();
                    },
                    crop: function(event) {
                        updatePreview();
                    }
                });
            }
            
            // Update preview
            function updatePreview() {
                if (!cropper) return;
                
                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });
                
                if (canvas) {
                    previewImage.src = canvas.toDataURL('image/jpeg', 0.9);
                }
            }
            
            // Close modal
            function closeCropModal() {
                cropModal.classList.remove('active');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                cropImage.src = '';
                previewImage.src = '';
                fotoInput.value = '';
                originalImage = null;
            }
            
            // Event listeners for modal controls
            document.getElementById('crop-close-btn')?.addEventListener('click', closeCropModal);
            document.getElementById('crop-cancel')?.addEventListener('click', closeCropModal);
            
            // Close on backdrop click
            cropModal?.addEventListener('click', function(e) {
                if (e.target === cropModal) {
                    closeCropModal();
                }
            });
            
            // Reset
            document.getElementById('crop-reset')?.addEventListener('click', function() {
                if (cropper) {
                    cropper.reset();
                }
            });
            
            // Zoom controls
            document.getElementById('crop-zoom-in')?.addEventListener('click', function() {
                if (cropper) {
                    cropper.zoom(0.1);
                }
            });
            
            document.getElementById('crop-zoom-out')?.addEventListener('click', function() {
                if (cropper) {
                    cropper.zoom(-0.1);
                }
            });
            
            // Rotate controls
            document.getElementById('crop-rotate-left')?.addEventListener('click', function() {
                if (cropper) {
                    cropper.rotate(-90);
                }
            });
            
            document.getElementById('crop-rotate-right')?.addEventListener('click', function() {
                if (cropper) {
                    cropper.rotate(90);
                }
            });
            
            // Save cropped image
            document.getElementById('crop-save')?.addEventListener('click', function() {
                if (!cropper) return;
                
                // Show loading
                cropLoading.classList.remove('d-none');
                
                // Get cropped canvas
                const canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });
                
                // Convert to blob
                canvas.toBlob(function(blob) {
                    // Create form data
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    // Append cropped image as file
                    const file = new File([blob], 'profile-crop.jpg', { type: 'image/jpeg' });
                    formData.append('foto_profil', file);
                    
                    // Submit via AJAX
                    fetch(profileRoute, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        cropLoading.classList.add('d-none');
                        
                        if (data.success) {
                            // Reload page to show updated photo
                            window.location.reload();
                        } else {
                            cropLoading.classList.add('d-none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Gagal',
                                text: typeof data.message === 'object' 
                                    ? Object.values(data.message).flat().join('<br>')
                                    : data.message || 'Terjadi kesalahan saat upload foto.',
                                confirmButtonColor: '#1e6b3c',
                                html: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        cropLoading.classList.add('d-none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Gagal',
                            text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                            confirmButtonColor: '#1e6b3c'
                        });
                    });
                }, 'image/jpeg', 0.9);
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (!cropModal.classList.contains('active')) return;
                
                if (e.key === 'Escape') {
                    closeCropModal();
                } else if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    document.getElementById('crop-save')?.click();
                }
            });
        })();
        </script>
        @endpush
