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
                        <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
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
                        <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
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
                        <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
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
                        <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
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

                <!--{{-- ── No. Telepon ───────────────────────────── --}}
                <div class="info-row">
                    <div>
                        <div class="small text-muted">No. Telepon</div>
                        <div class="fw-semibold">{{ $user->no_telepon ?: 'Belum diisi' }}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#edit-telp">Edit</button>
                </div>

                <div id="edit-telp" class="collapse collapse-form">
                    <form action="{{ $profileRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="nama" value="{{ $user->nama }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="alamat" value="{{ $user->alamat }}">
                        <input type="hidden" name="catatan_profil" value="{{ $user->catatan_profil }}">

                        <label class="form-label small fw-semibold">No. Telepon / WhatsApp</label>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="tel" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}"
                                    class="form-control @error('no_telepon') is-invalid @enderror"
                                    placeholder="08xxxxxxxxxx" maxlength="15">
                                @error('no_telepon')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-spk w-100">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>-->

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
                        <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
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
                        <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
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
                                <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
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
            });
        });
        </script>
        @endpush
