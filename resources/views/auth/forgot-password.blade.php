{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.app')
@section('title', 'Lupa Password')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
    style="background: linear-gradient(135deg,#14532d 0%,#1e6b3c 60%,#2d8a4e 100%);">
    <div class="card shadow-lg" style="width:100%;max-width:420px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">Lupa Password</h5>
                <small class="text-muted">Email harus sudah terverifikasi untuk menerima link reset password</small>
            </div>

            @if(session('success'))
            <div class="alert alert-success py-2">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
            </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="Masukkan email Anda" autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-spk w-100 py-2 fw-semibold">
                    <i class="bi bi-send me-1"></i> Kirim Link Reset Password
                </button>
                <div class="form-text mt-2 text-center">
                    Jika email belum terverifikasi, sistem akan mengirim link verifikasi terlebih dahulu.
                </div>
            </form>
            <hr>
            <p class="text-center mb-0 small">
                Sudah ingat password?
                <a href="{{ route('login') }}" class="text-success fw-semibold">Login di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
