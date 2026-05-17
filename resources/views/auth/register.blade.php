@extends('layouts.app')
@section('title', 'Registrasi')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
    style="background: linear-gradient(135deg,#14532d 0%,#1e6b3c 60%,#2d8a4e 100%);">
    <div class="card shadow-lg" style="width:100%;max-width:440px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-2"
                    style="width:56px;height:56px;">
                    <i class="bi bi-person-plus-fill text-white fs-4"></i>
                </div>
                <h5 class="fw-bold mb-0">Daftar Akun Baru</h5>
                <small class="text-muted">Daftar cepat dengan username dan password</small>
            </div>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username') }}" placeholder="Buat username unik">
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="register-password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimal 6 karakter">
                        <button class="btn btn-outline-secondary password-toggle" type="button"
                            data-target="register-password" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-spk w-100 py-2 fw-semibold mt-2">
                    <i class="bi bi-person-check me-1"></i> Daftar Sekarang
                </button>
            </form>
            <hr>
            <p class="text-center mb-0 small">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-success fw-semibold">Login di sini</a>
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