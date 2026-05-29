{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
    style="background: linear-gradient(135deg,#14532d 0%,#1e6b3c 60%,#2d8a4e 100%);">
    <div class="card shadow-lg" style="width:100%;max-width:420px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">Reset Password</h5>
                <small class="text-muted">Halaman ini hanya dapat digunakan dari tombol Reset Password di email</small>
            </div>

            @if(session('error'))
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
            </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $email ?? '') }}" placeholder="Email" readonly>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="reset-password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan password baru" autofocus>
                        <button class="btn btn-outline-secondary password-toggle" type="button"
                            data-target="reset-password" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" id="reset-password-confirm" name="password_confirmation"
                            class="form-control" placeholder="Konfirmasi password baru">
                        <button class="btn btn-outline-secondary password-toggle" type="button"
                            data-target="reset-password-confirm" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-spk w-100 py-2 fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Reset Password
                </button>
            </form>
            <hr>
            <p class="text-center mb-0 small">
                Kembali ke
                <a href="{{ route('login') }}" class="text-success fw-semibold">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.password-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('i');
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
</script>
@endpush
