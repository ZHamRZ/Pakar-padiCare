{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
    style="background: linear-gradient(135deg,#14532d 0%,#1e6b3c 60%,#2d8a4e 100%);">
    <div class="card shadow-lg" style="width:100%;max-width:420px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">SPK Pupuk & Pestisida</h5>
                <small class="text-muted">Login petani dengan username dan password</small>
            </div>

            {{-- Form login petani (AJAX) --}}
            <form id="loginForm" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="username"
                            class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}"
                            placeholder="Masukkan username" autofocus>
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="login-password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan password">
                        <button class="btn btn-outline-secondary password-toggle" type="button"
                            data-target="login-password" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mt-2 text-end">
                        <a href="{{ route('password.request') }}" class="small text-muted">Lupa password?</a>
                    </div>
                </div>
                <button type="submit" id="btnLogin" class="btn btn-spk w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>
            </form>

            <hr>

            {{-- Form login admin (form tradisional) --}}
            <div class="mb-3">
                <div class="small text-muted mb-2">Admin tetap login dengan username dan password:</div>
                <form action="{{ route('login.admin.post') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="username" class="form-control" placeholder="Username admin">
                    </div>
                    <div class="mb-2">
                        <input type="password" name="password" class="form-control" placeholder="Password admin">
                    </div>
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-shield-lock me-1"></i> Login Admin
                    </button>
                </form>
            </div>

            <hr>
            <p class="text-center mb-0 small">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-success fw-semibold">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Password toggle ──────────────────────────────────────────────────────────
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

// ── AJAX Login (petani) ──────────────────────────────────────────────────────
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const btnLogin = document.getElementById('btnLogin');
    const originalBtnText = btnLogin.innerHTML;

    // Loading state
    btnLogin.disabled = true;
    btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

    fetch('{{ route("login.post") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            // Tetap parse JSON meski status 4xx agar pesan error server terbaca
            return response.json().then(data => ({
                ok: response.ok,
                data
            }));
        })
        .then(({
            ok,
            data
        }) => {
            btnLogin.disabled = false;
            btnLogin.innerHTML = originalBtnText;

            if (ok && data.success === true) {
                // ✅ Tampilkan toast success, redirect SETELAH toast selesai
                Toast.fire({
                    icon: 'success',
                    title: data.message || 'Login berhasil',
                    timer: 1800,
                    timerProgressBar: true,
                    background: '#ffffff',
                    color: '#1e293b',
                    iconColor: '#198754',
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                // ✅ Gunakan showErrorToast dari layouts.app (konsisten)
                showErrorToast(data.message || 'Username atau password salah.');
            }
        })
        .catch(() => {
            btnLogin.disabled = false;
            btnLogin.innerHTML = originalBtnText;
            showErrorToast('Terjadi kesalahan koneksi. Silakan coba lagi.');
        });
});
</script>
@endpush