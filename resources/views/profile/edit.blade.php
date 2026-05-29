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
    object-position: center;
    border: 4px solid #fff;
    box-shadow: 0 12px 24px rgba(15, 23, 42, .12);
    display: block;
    margin-left: auto;
    margin-right: auto;
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
                    <form id="profilePhotoForm" action="{{ $profileRoute }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Hidden fields wajib agar field lain tidak ter-reset --}}
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                        <input type="hidden" name="cropped_image" id="croppedImageInput">

                        <div class="mb-2">
                            <input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp"
                                class="form-control form-control-sm @error('foto_profil') is-invalid @enderror">
                            @error('foto_profil')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPG, PNG, atau WebP — maks. 2MB</div>
                        </div>

                        <button type="button" class="btn btn-spk btn-sm w-100" id="choosePhotoBtn">
                            <i class="bi bi-crop me-1"></i>Pilih & Edit Foto
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
                    <button class="btn btn-sm btn-spk" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-nama">Edit</button>
                </div>

                <div id="edit-nama" class="collapse collapse-form @error('nama') show @enderror">
                    <form action="{{ $profileRoute }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}"
                                    class="form-control @error('nama') is-invalid @enderror" required>
                                @error('nama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 d-flex gap-1">
                                <button type="submit" class="btn btn-spk flex-fill">Simpan</button>
                                <button type="button" class="btn btn-light-secondary flex-fill" data-bs-toggle="collapse" data-bs-target="#edit-nama">Batal</button>
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
                    <button class="btn btn-sm btn-spk" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-username">Edit</button>
                </div>

                <div id="edit-username" class="collapse collapse-form @error('username') show @enderror">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
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
                            <div class="col-md-4 d-flex gap-1">
                                <button type="submit" class="btn btn-spk flex-fill">Simpan</button>
                                <button type="button" class="btn btn-light-secondary flex-fill" data-bs-toggle="collapse" data-bs-target="#edit-username">Batal</button>
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
                    <button class="btn btn-sm btn-spk" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-email">Edit</button>
                </div>

                <div id="edit-email" class="collapse collapse-form @error('email') show @enderror">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
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
                            <div class="col-md-4 d-flex gap-1">
                                <button type="submit" class="btn btn-spk flex-fill">Simpan</button>
                                <button type="button" class="btn btn-light-secondary flex-fill" data-bs-toggle="collapse" data-bs-target="#edit-email">Batal</button>
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
                    <button class="btn btn-sm btn-spk" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-alamat">Edit</button>
                </div>

                <div id="edit-alamat" class="collapse collapse-form @error('alamat') show @enderror">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <label class="form-label small fw-semibold">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3"
                            class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-spk">Simpan</button>
                            <button type="button" class="btn btn-light-secondary" data-bs-toggle="collapse" data-bs-target="#edit-alamat">Batal</button>
                        </div>
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
                            <button class="btn btn-sm btn-spk" type="button" data-bs-toggle="collapse"
                                data-bs-target="#edit-password">Edit</button>
                        </div>

                        <div id="edit-password" class="collapse collapse-form @error('password') show @enderror @error('password_lama') show @enderror">
                            <form action="{{ $profileRoute }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="nama" value="{{ $user->nama }}">
                                <input type="hidden" name="username" value="{{ $user->username }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="alamat" value="{{ $user->alamat }}">
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

                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-spk">
                                        <i class="bi bi-lock me-1"></i>Simpan Password Baru
                                    </button>
                                    <button type="button" class="btn btn-light-secondary" data-bs-toggle="collapse" data-bs-target="#edit-password">Batal</button>
                                </div>
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
                    if (countdownTimer) clearInterval(countdownTimer);
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

        const photoForm = document.getElementById('profilePhotoForm');
        const fotoInput = document.querySelector('input[name="foto_profil"]');
        const choosePhotoBtn = document.getElementById('choosePhotoBtn');
        const croppedImageInput = document.getElementById('croppedImageInput');
        const cropModalEl = document.getElementById('cropModal');
        const cropWrapper = document.getElementById('cropWrapper');
        const cropSource = document.getElementById('cropSource');
        const cropSelection = document.getElementById('cropSelection');
        const avatarPreview = document.getElementById('avatarPreview');
        const resetCropBtn = document.getElementById('resetCropBtn');
        const saveCropBtn = document.getElementById('saveCropBtn');

        let cropModalInstance = null;
        let objectUrl = null;
        let imageLoaded = false;
        let selection = { x: 0, y: 0, w: 0, h: 0 };
        let isDrawing = false;
        let isDragging = false;
        let isResizing = false;
        let resizeHandle = '';
        let drawStart = { x: 0, y: 0 };
        let dragStart = { x: 0, y: 0, selX: 0, selY: 0 };

        choosePhotoBtn?.addEventListener('click', function() {
            fotoInput?.click();
        });

        photoForm?.addEventListener('submit', function(event) {
            if (fotoInput?.files?.length && !croppedImageInput?.value) {
                event.preventDefault();
                openCropModal(fotoInput.files[0]);
            }
        });

        fotoInput?.addEventListener('change', function() {
            const file = this.files?.[0];
            if (!file) return;

            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                window.showErrorToast?.('Format file tidak didukung. Gunakan JPG, PNG, atau WebP.');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                window.showErrorToast?.('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }

            openCropModal(file);
        });

        function openCropModal(file) {
            if (!cropModalEl || !cropSource || !cropWrapper || !avatarPreview || !croppedImageInput) {
                window.showErrorToast?.('Editor foto tidak tersedia.');
                return;
            }

            if (objectUrl) URL.revokeObjectURL(objectUrl);
            objectUrl = URL.createObjectURL(file);
            imageLoaded = false;
            croppedImageInput.value = '';
            resetSelection();
            resetSaveButton();

            cropSource.onload = function() {
                imageLoaded = true;
                // Set default selection to center square
                const wrapperRect = cropWrapper.getBoundingClientRect();
                const imgW = cropSource.clientWidth;
                const imgH = cropSource.clientHeight;
                const size = Math.min(imgW, imgH) * 0.7;
                selection = {
                    x: (imgW - size) / 2,
                    y: (imgH - size) / 2,
                    w: size,
                    h: size
                };
                renderSelection();
                updatePreview();
            };

            cropSource.src = objectUrl;
            cropModalInstance = bootstrap.Modal.getOrCreateInstance(cropModalEl);
            cropModalInstance.show();
        }

        function resetSelection() {
            selection = { x: 0, y: 0, w: 0, h: 0 };
            cropSelection.classList.remove('active');
            if (avatarPreview) avatarPreview.src = '';
        }

        function renderSelection() {
            if (!cropSelection) return;
            if (selection.w > 0 && selection.h > 0) {
                cropSelection.style.left = selection.x + 'px';
                cropSelection.style.top = selection.y + 'px';
                cropSelection.style.width = selection.w + 'px';
                cropSelection.style.height = selection.h + 'px';
                cropSelection.classList.add('active');
            } else {
                cropSelection.classList.remove('active');
            }
        }

        function updatePreview() {
            if (!imageLoaded || !cropSource || !avatarPreview || selection.w <= 0) return;

            const imgNaturalW = cropSource.naturalWidth;
            const imgNaturalH = cropSource.naturalHeight;
            const imgDisplayW = cropSource.clientWidth;
            const imgDisplayH = cropSource.clientHeight;
            const scaleX = imgNaturalW / imgDisplayW;
            const scaleY = imgNaturalH / imgDisplayH;

            const sourceX = selection.x * scaleX;
            const sourceY = selection.y * scaleY;
            const sourceW = selection.w * scaleX;
            const sourceH = selection.h * scaleY;

            const canvas = document.createElement('canvas');
            const size = 300;
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');

            ctx.beginPath();
            ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
            ctx.clip();
            ctx.drawImage(cropSource, sourceX, sourceY, sourceW, sourceH, 0, 0, size, size);

            avatarPreview.src = canvas.toDataURL('image/jpeg', 0.9);
        }

        function constrainSelection() {
            const imgW = cropSource.clientWidth;
            const imgH = cropSource.clientHeight;

            if (selection.x < 0) selection.x = 0;
            if (selection.y < 0) selection.y = 0;
            if (selection.x + selection.w > imgW) selection.x = imgW - selection.w;
            if (selection.y + selection.h > imgH) selection.y = imgH - selection.h;

            if (selection.w < 30) selection.w = 30;
            if (selection.h < 30) selection.h = 30;

            renderSelection();
            updatePreview();
        }

        // Mouse/Touch events for drawing, dragging, and resizing
        cropWrapper?.addEventListener('pointerdown', function(e) {
            if (!imageLoaded) return;
            e.preventDefault();

            const rect = cropWrapper.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            // Check if clicking on a handle
            const handle = e.target.closest('.crop-handle');
            if (handle && cropSelection.classList.contains('active')) {
                isResizing = true;
                resizeHandle = handle.dataset.handle;
                dragStart = { x: e.clientX, y: e.clientY, selX: selection.x, selY: selection.y, selW: selection.w, selH: selection.h };
                cropWrapper.setPointerCapture(e.pointerId);
                return;
            }

            // Check if clicking inside selection (drag)
            if (cropSelection.classList.contains('active') &&
                x >= selection.x && x <= selection.x + selection.w &&
                y >= selection.y && y <= selection.y + selection.h) {
                isDragging = true;
                dragStart = { x: e.clientX, y: e.clientY, selX: selection.x, selY: selection.y };
                cropWrapper.setPointerCapture(e.pointerId);
                return;
            }

            // Start new selection
            isDrawing = true;
            drawStart = { x, y };
            selection = { x, y, w: 0, h: 0 };
            cropSelection.classList.add('active');
            cropWrapper.setPointerCapture(e.pointerId);
        });

        cropWrapper?.addEventListener('pointermove', function(e) {
            if (!imageLoaded) return;

            const rect = cropWrapper.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (isDrawing) {
                selection.x = Math.min(drawStart.x, x);
                selection.y = Math.min(drawStart.y, y);
                selection.w = Math.abs(x - drawStart.x);
                selection.h = Math.abs(y - drawStart.y);
                renderSelection();
                updatePreview();
            } else if (isDragging) {
                const dx = e.clientX - dragStart.x;
                const dy = e.clientY - dragStart.y;
                selection.x = dragStart.selX + dx;
                selection.y = dragStart.selY + dy;
                constrainSelection();
            } else if (isResizing) {
                const dx = e.clientX - dragStart.x;
                const dy = e.clientY - dragStart.y;
                const s = dragStart;

                switch (resizeHandle) {
                    case 'br':
                        selection.w = s.selW + dx;
                        selection.h = s.selH + dy;
                        break;
                    case 'bl':
                        selection.x = s.selX + dx;
                        selection.w = s.selW - dx;
                        selection.h = s.selH + dy;
                        break;
                    case 'tr':
                        selection.y = s.selY + dy;
                        selection.w = s.selW + dx;
                        selection.h = s.selH - dy;
                        break;
                    case 'tl':
                        selection.x = s.selX + dx;
                        selection.y = s.selY + dy;
                        selection.w = s.selW - dx;
                        selection.h = s.selH - dy;
                        break;
                    case 'tm':
                        selection.y = s.selY + dy;
                        selection.h = s.selH - dy;
                        break;
                    case 'bm':
                        selection.h = s.selH + dy;
                        break;
                    case 'ml':
                        selection.x = s.selX + dx;
                        selection.w = s.selW - dx;
                        break;
                    case 'mr':
                        selection.w = s.selW + dx;
                        break;
                }
                constrainSelection();
            }
        });

        function stopInteraction() {
            isDrawing = false;
            isDragging = false;
            isResizing = false;
            resizeHandle = '';
            constrainSelection();
        }

        cropWrapper?.addEventListener('pointerup', stopInteraction);
        cropWrapper?.addEventListener('pointercancel', stopInteraction);

        resetCropBtn?.addEventListener('click', function() {
            if (!imageLoaded) return;
            const imgW = cropSource.clientWidth;
            const imgH = cropSource.clientHeight;
            const size = Math.min(imgW, imgH) * 0.7;
            selection = {
                x: (imgW - size) / 2,
                y: (imgH - size) / 2,
                w: size,
                h: size
            };
            renderSelection();
            updatePreview();
        });

        saveCropBtn?.addEventListener('click', function() {
            if (!imageLoaded || !photoForm || !croppedImageInput || selection.w <= 0) {
                window.showErrorToast?.('Pilih area crop terlebih dahulu.');
                return;
            }

            saveCropBtn.disabled = true;
            saveCropBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            const imgNaturalW = cropSource.naturalWidth;
            const imgNaturalH = cropSource.naturalHeight;
            const imgDisplayW = cropSource.clientWidth;
            const imgDisplayH = cropSource.clientHeight;
            const scaleX = imgNaturalW / imgDisplayW;
            const scaleY = imgNaturalH / imgDisplayH;

            const sourceX = selection.x * scaleX;
            const sourceY = selection.y * scaleY;
            const sourceW = selection.w * scaleX;
            const sourceH = selection.h * scaleY;

            const canvas = document.createElement('canvas');
            canvas.width = 500;
            canvas.height = 500;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(cropSource, sourceX, sourceY, sourceW, sourceH, 0, 0, 500, 500);

            croppedImageInput.value = canvas.toDataURL('image/jpeg', 0.9);
            cropModalInstance?.hide();
            photoForm.submit();
        });

        cropModalEl?.addEventListener('hidden.bs.modal', function() {
            if (!croppedImageInput?.value && fotoInput) fotoInput.value = '';
            resetSaveButton();
            imageLoaded = false;
            resetSelection();
        });

        function resetSaveButton() {
            if (!saveCropBtn) return;
            saveCropBtn.disabled = false;
            saveCropBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Simpan';
        }
        </script>
        @endpush
