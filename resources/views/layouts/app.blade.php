<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PadiCare Lombok') - Sistem Pakar Penyakit & Rekomendasi Pupuk Padi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/icons8-wheat-hatch-16.png') }}">
    <style>
    :root {
        --spk-primary: #1e6b3c;
        --spk-secondary: #2d8a4e;
        --spk-accent: #f5a623;
        --spk-dark: #14532d;
    }

    body {
        background: #f4f7f4;
        font-family: 'Segoe UI', sans-serif;
    }

    .sidebar {
        width: 260px;
        height: 100vh;
        background: linear-gradient(180deg, var(--spk-dark) 0%, var(--spk-primary) 100%);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        transition: transform .3s;
        display: flex;
        flex-direction: column;
    }

    .sidebar-brand {
        padding: 1.2rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, .15);
        flex-shrink: 0;
    }

    .sidebar-brand-lockup {
        display: flex;
        align-items: center;
        gap: .8rem;
    }

    .sidebar-brand-badge {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #14532d 0%, #16a34a 100%);
        box-shadow: 0 10px 20px rgba(5, 150, 105, .28);
        flex-shrink: 0;
    }

    .sidebar-brand h6 {
        color: #fff;
        font-size: .8rem;
        opacity: .7;
        margin: 0;
    }

    .sidebar-brand h5 {
        color: #fff;
        font-size: 1rem;
        margin: .2rem 0 0;
        font-weight: 700;
    }

    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        padding: .4rem 0 1rem;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, .8);
        padding: .6rem 1.2rem;
        border-radius: 8px;
        margin: .1rem .5rem;
        font-size: .9rem;
        display: flex;
        align-items: center;
        gap: .6rem;
        transition: background .2s, color .2s;
        text-decoration: none;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background: rgba(255, 255, 255, .15);
        color: #fff;
    }

    .sidebar .nav-section {
        color: rgba(255, 255, 255, .45);
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 1rem 1.2rem .3rem;
        font-weight: 600;
    }

    .sidebar-footer {
        padding: .75rem .5rem 1rem;
        border-top: 1px solid rgba(255, 255, 255, .12);
        flex-shrink: 0;
    }

    .profile-mini {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 0 .6rem .6rem;
        padding: .75rem;
        border-radius: 12px;
        background: rgba(255, 255, 255, .08);
    }

    .profile-mini img,
    .profile-mini .avatar-fallback {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .avatar-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .18);
        color: #fff;
        font-weight: 700;
    }

    .main-content {
        margin-left: 260px;
        min-height: 100vh;
    }

    .topbar {
        background: #fff;
        padding: .8rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 999;
    }

    .page-body {
        padding: 1.5rem;
    }

    .card {
        border: none;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        border-radius: 12px;
    }

    .card-header {
        background: var(--spk-primary);
        color: #fff;
        border-radius: 12px 12px 0 0 !important;
    }

    .stat-card {
        border-radius: 12px;
        padding: 1.2rem;
        color: #fff;
    }

    .btn-spk {
        background: var(--spk-primary);
        color: #fff;
        border: none;
    }

    .btn-spk:hover {
        background: var(--spk-secondary);
        color: #fff;
    }

    .global-back-button {
        position: fixed;
        top: 5.25rem;
        left: calc(260px + 1rem);
        width: 58px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 22px;
        text-decoration: none;
        background: rgba(255, 255, 255, .97);
        color: var(--spk-dark);
        border: 1px solid rgba(20, 83, 45, .18);
        box-shadow: 0 18px 40px rgba(15, 23, 42, .16);
        z-index: 1100;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .global-back-button:hover {
        color: var(--spk-dark);
        background: #fff;
        transform: translateY(-1px);
        box-shadow: 0 22px 44px rgba(15, 23, 42, .20);
    }

    .global-back-button i {
        font-size: 1.6rem;
        line-height: 1;
    }

    .thumb-placeholder {
        background: #f8fafc;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .badge-rank-1 {
        background: #f5a623;
        color: #fff;
    }

    .badge-rank-2 {
        background: #9ca3af;
        color: #fff;
    }

    .badge-rank-3 {
        background: #b45309;
        color: #fff;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .global-back-button {
            top: 5rem;
            left: 1rem;
        }
    }

    @media print {

        .sidebar,
        .topbar,
        .global-back-button,
        .btn,
        .no-print {
            display: none !important;
        }

        .main-content {
            margin-left: 0;
        }
    }
    </style>
    @stack('styles')
</head>

<body class="{{ auth()->check() ? 'authenticated-layout' : 'guest-layout' }}">
    @php
    $fallbackBackUrl = auth()->check()
    ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard'))
    : route('home');
    $globalBackUrl = url()->previous() !== url()->current() ? url()->previous() : $fallbackBackUrl;
    @endphp
    @auth
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-lockup">
                <span class="sidebar-brand-badge"></span>
                <div>
                    <h5 style="margin:0; line-height:1.2;">PadiCare <span style="color:#4ade80;">Lombok</span></h5>
                    <small style="color:rgba(255,255,255,.55);font-size:.75rem;">Sistem Pakar Penyakit & Rekomendasi
                        Pupuk Padi</small>
                </div>
            </div>
        </div>

        <div class="sidebar-nav">
            <nav>
                @if(auth()->user()->role === 'admin')
                <span class="nav-section">Menu Admin</span>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <span class="nav-section">Data Master</span>
                <a href="{{ route('admin.penyakit.index') }}"
                    class="nav-link {{ request()->routeIs('admin.penyakit*') ? 'active' : '' }}">
                    <i class="bi bi-virus"></i> Data Penyakit
                </a>
                <a href="{{ route('admin.gejala.index') }}"
                    class="nav-link {{ request()->routeIs('admin.gejala*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard2-pulse"></i> Data Gejala
                </a>
                <a href="{{ route('admin.pupuk.index') }}"
                    class="nav-link {{ request()->routeIs('admin.pupuk*') ? 'active' : '' }}">
                    <i class="bi bi-bag-fill"></i> Data Pupuk
                </a>
                <a href="{{ route('admin.pestisida.index') }}"
                    class="nav-link {{ request()->routeIs('admin.pestisida*') ? 'active' : '' }}">
                    <i class="bi bi-capsule"></i> Data Pestisida
                </a>
                <span class="nav-section">Analisis Sistem</span>
                <a href="{{ route('admin.kriteria.index') }}"
                    class="nav-link {{ request()->routeIs('admin.kriteria*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i> Parameter Prioritas
                </a>
                <a href="{{ route('admin.rating.pupuk') }}"
                    class="nav-link {{ request()->routeIs('admin.rating.pupuk*') ? 'active' : '' }}">
                    <i class="bi bi-table"></i> Rule CF Pupuk
                </a>
                <a href="{{ route('admin.rating.pestisida') }}"
                    class="nav-link {{ request()->routeIs('admin.rating.pestisida*') ? 'active' : '' }}">
                    <i class="bi bi-table"></i> Rule CF Pestisida
                </a>
                <span class="nav-section">Pengguna</span>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Data Pengguna
                </a>
                <a href="{{ route('admin.riwayat.index') }}"
                    class="nav-link {{ request()->routeIs('admin.riwayat*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Semua User
                </a>
                <span class="nav-section">Akun</span>
                <a href="{{ route('admin.profile.edit') }}"
                    class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Profil Saya
                </a>
                @else
                <span class="nav-section">Menu Petani</span>
                <a href="{{ route('user.dashboard') }}"
                    class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house"></i> Beranda
                </a>
                <a href="{{ route('user.diagnosis.index') }}"
                    class="nav-link {{ request()->routeIs('user.diagnosis*') ? 'active' : '' }}">
                    <i class="bi bi-search-heart"></i> Diagnosis Penyakit
                </a>
                <a href="{{ route('user.riwayat.index') }}"
                    class="nav-link {{ request()->routeIs('user.riwayat*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Saya
                </a>
                <span class="nav-section">Akun</span>
                <a href="{{ route('user.profile.edit') }}"
                    class="nav-link {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Profil Saya
                </a>
                @endif
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="profile-mini">
                @if(auth()->user()->foto_profil_url)
                <img src="{{ auth()->user()->foto_profil_url }}" alt="Foto Profil">
                @else
                <span class="avatar-fallback">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</span>
                @endif
                <div class="text-white small">
                    <div class="fw-semibold">{{ auth()->user()->nama }}</div>
                    <div style="opacity:.7;">{{ auth()->user()->isAdmin() ? 'Admin' : 'Petani' }}</div>
                </div>
            </div>
            <button type="button" class="nav-link w-100 text-start border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
            <noscript>
                <a href="{{ route('logout.get') }}" class="nav-link">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </noscript>
        </div>
    </div>

    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="logoutModalLabel">Akhiri Sesi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="mb-0">Apakah Anda yakin ingin keluar dari sesi ini?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-left me-1"></i> Akhiri Sesi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light d-md-none"
                    onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-semibold text-muted small">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success">{{ auth()->user()->role === 'admin' ? 'Admin' : 'Petani' }}</span>
                <span class="fw-semibold small">{{ auth()->user()->nama }}</span>
            </div>
        </div>
        <div class="page-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @yield('content')
        </div>
    </div>
    @else
    @yield('content')
    @endauth

    <a href="{{ $globalBackUrl }}" class="global-back-button" aria-label="Kembali ke halaman sebelumnya" title="Kembali"
        data-fallback-url="{{ $fallbackBackUrl }}">
        <i class="bi bi-arrow-left"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const backButton = document.querySelector('.global-back-button');

        if (!backButton) return;

        if (document.body.classList.contains('guest-layout')) {
            backButton.style.top = '1rem';
            backButton.style.left = '1rem';
        }

        backButton.addEventListener('click', (event) => {
            const fallbackUrl = backButton.dataset.fallbackUrl;

            try {
                const hasValidReferrer = document.referrer &&
                    document.referrer !== window.location.href &&
                    new URL(document.referrer).origin === window.location.origin;

                if (hasValidReferrer) {
                    event.preventDefault();
                    window.history.back();
                    return;
                }
            } catch (error) {
                // Ignore malformed referrer and continue to fallback URL.
            }

            if (!backButton.getAttribute('href')) {
                event.preventDefault();
                window.location.href = fallbackUrl;
            }
        });
    });
    </script>
    @stack('scripts')
</body>

</html>