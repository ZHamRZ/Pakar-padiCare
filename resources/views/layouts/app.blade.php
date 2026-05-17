<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta-description', 'PadiCare Lombok - Sistem Pakar Diagnosis Penyakit dan Rekomendasi Pupuk untuk Tanaman Padi')">
    <meta name="keywords" content="@yield('meta-keywords', 'padi, penyakit padi, pupuk, sistem pakar, diagnosis, pertanian, lombok')">
    <title>@yield('title', 'PadiCare Lombok') - Sistem Pakar Penyakit & Rekomendasi Pupuk Padi</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/icons8-wheat-hatch-16.png') }}">
    
    <!-- PadiCare Design System - Use Vite asset path -->
    @vite(['resources/css/padicare.css'])
    
    <style>
    /* Critical CSS - Inline for faster initial render */
    :root {
        --spk-primary: #1e6b3c;
        --spk-secondary: #2d8a4e;
        --spk-accent: #f5a623;
        --spk-dark: #14532d;
        --spk-green-50: #f0fdf4;
        --spk-green-100: #dcfce7;
        --spk-green-200: #bbf7d0;
        --spk-green-300: #86efac;
        --spk-green-400: #4ade80;
        --spk-green-500: #22c55e;
        --spk-green-600: #16a34a;
        --spk-green-700: #15803d;
        --spk-green-800: #166534;
        --spk-green-900: #14532d;
        --spk-slate-50: #f8fafc;
        --spk-slate-100: #f1f5f9;
        --spk-slate-200: #e2e8f0;
        --spk-slate-300: #cbd5e1;
        --spk-slate-400: #94a3b8;
        --spk-slate-500: #64748b;
        --spk-slate-600: #475569;
        --spk-slate-700: #334155;
        --spk-slate-800: #1e293b;
        --spk-slate-900: #0f172a;
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 20px;
        --shadow-sm: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
        --shadow-md: 0 4px 16px rgba(15,23,42,.07), 0 1px 4px rgba(15,23,42,.04);
        --shadow-lg: 0 12px 40px rgba(15,23,42,.09);
        --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
        --transition-base: 200ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Enhanced Button Styles */
    .btn-light-secondary {
        background: var(--spk-slate-100);
        color: var(--spk-slate-700);
        border: 1px solid var(--spk-slate-200);
        transition: all var(--transition-fast);
    }
    .btn-light-secondary:hover {
        background: var(--spk-slate-200);
        color: var(--spk-slate-800);
        border-color: var(--spk-slate-300);
    }
    
    /* Icon Box Component */
    .icon-box-lg {
        transition: transform var(--transition-base);
    }
    .modal:hover .icon-box-lg {
        transform: scale(1.05);
    }
    
    /* Crop Modal Styles */
    .crop-modal .modal-dialog {
        max-width: 700px;
    }
    .crop-container {
        position: relative;
        width: 100%;
        height: 400px;
        background: #000;
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    .crop-image {
        max-width: 100%;
        display: block;
        cursor: move;
    }
    .crop-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px dashed rgba(255,255,255,0.5);
        pointer-events: none;
    }
    .crop-controls {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .crop-btn {
        flex: 1;
        min-width: 100px;
    }
    .avatar-preview-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }
    .avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--spk-green-100);
        box-shadow: 0 8px 24px rgba(15,23,42,0.12);
    }
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--spk-dark), var(--spk-secondary));
        color: #fff;
        font-size: 3rem;
        font-weight: 700;
        border: 4px solid var(--spk-green-100);
        box-shadow: 0 8px 24px rgba(15,23,42,0.12);
    }

    *, *::before, *::after { box-sizing: border-box; }
    
    html { scroll-behavior: smooth; }
    
    body {
        background: var(--spk-slate-50);
        font-family: 'DM Sans', sans-serif;
        line-height: 1.6;
        color: var(--spk-slate-700);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    h1, h2, h3, h4, h5, h6, .fw-bold, .fw-semibold {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        line-height: 1.2;
        color: var(--spk-slate-900);
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
        top: 5rem;
        left: calc(260px + 1.5rem);
        width: 52px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.98);
        color: var(--spk-dark);
        border: 1px solid rgba(20, 83, 45, 0.15);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
        z-index: 900;
        transition: all 0.2s ease;
    }

    .global-back-button:hover {
        color: var(--spk-dark);
        background: #fff;
        transform: translateX(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.15);
    }

    .global-back-button i {
        font-size: 1.4rem;
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
                <a href="{{ route('admin.nilai-cf.pupuk') }}"
                    class="nav-link {{ request()->routeIs('admin.nilai-cf.pupuk*') ? 'active' : '' }}">
                    <i class="bi bi-table"></i> Nilai CF Pupuk
                </a>
                <a href="{{ route('admin.nilai-cf.pestisida') }}"
                    class="nav-link {{ request()->routeIs('admin.nilai-cf.pestisida*') ? 'active' : '' }}">
                    <i class="bi bi-table"></i> Nilai CF Pestisida
                </a>
                <span class="nav-section">Pengguna</span>
                <a href="{{ route('admin.pengguna.index') }}"
                    class="nav-link {{ request()->routeIs('admin.pengguna*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Data Pengguna
                </a>
                <a href="{{ route('admin.riwayat.index') }}"
                    class="nav-link {{ request()->routeIs('admin.riwayat*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Semua Pengguna
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
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box-lg rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(220, 38, 38, 0.1);">
                            <i class="bi bi-question-circle-fill text-danger fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="logoutModalLabel">Akhiri Sesi</h5>
                            <small class="text-muted">Konfirmasi keluar dari akun Anda</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <p class="mb-0 text-secondary">Apakah Anda yakin ingin keluar dari sesi ini? Anda harus login kembali untuk mengakses aplikasi.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Batalkan
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-box-arrow-left me-2"></i>Akhiri Sesi
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>
    @vite(['resources/js/app.js'])
    <script>
    // SweetAlert2 Toast Configuration with Dark Mode Support
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        },
        customClass: {
            popup: 'rounded-4 shadow-lg',
            icon: 'me-2'
        }
    });

    // Auto-detect dark mode
    const isDarkMode = () => window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Function to show error toast - made globally accessible
    window.showErrorToast = function(message) {
        Toast.fire({
            icon: 'error',
            title: message,
            background: isDarkMode() ? '#1e293b' : '#ffffff',
            color: isDarkMode() ? '#f1f5f9' : '#1e293b',
            iconColor: isDarkMode() ? '#ef4444' : '#dc3545',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    }

    // Function to show success toast - made globally accessible
    window.showSuccessToast = function(message) {
        Toast.fire({
            icon: 'success',
            title: message,
            background: isDarkMode() ? '#1e293b' : '#ffffff',
            color: isDarkMode() ? '#f1f5f9' : '#1e293b',
            iconColor: isDarkMode() ? '#22c55e' : '#198754',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Handle Laravel session errors with SweetAlert2
        @if(session('error'))
            showErrorToast("{{ session('error') }}");
        @endif

        @if(session('success'))
            showSuccessToast("{{ session('success') }}");
        @endif

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ session('info') }}",
                background: isDarkMode() ? '#1e293b' : '#ffffff',
                color: isDarkMode() ? '#f1f5f9' : '#1e293b',
                iconColor: isDarkMode() ? '#3b82f6' : '#0dcaf0'
            });
        @endif

        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('warning') }}",
                background: isDarkMode() ? '#1e293b' : '#ffffff',
                color: isDarkMode() ? '#f1f5f9' : '#1e293b',
                iconColor: isDarkMode() ? '#f59e0b' : '#ffc107'
            });
        @endif

        // Remove old Bootstrap alerts
        document.querySelectorAll('.alert').forEach(el => el.remove());

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
    
    <!-- Modal Crop Foto Profil -->
    <div class="modal fade crop-modal" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box-lg rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(22, 163, 74, 0.1);">
                            <i class="bi bi-crop-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="cropModalLabel">Sesuaikan Foto Profil</h5>
                            <small class="text-muted">Crop dan sesuaikan foto Anda</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <div class="crop-container mb-4" id="cropContainer">
                        <img src="" alt="Preview" class="crop-image" id="cropImage">
                        <div class="crop-overlay" id="cropOverlay"></div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Zoom</label>
                            <input type="range" class="form-range" id="zoomRange" min="1" max="3" step="0.1" value="1">
                        </div>
                        <div class="col-12">
                            <div class="avatar-preview-container">
                                <img src="" alt="Avatar Preview" class="avatar-preview" id="avatarPreview">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <div class="crop-controls w-100">
                        <button type="button" class="btn btn-light-secondary crop-btn" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="button" class="btn btn-outline-primary crop-btn" id="resetCropBtn">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                        </button>
                        <button type="button" class="btn btn-spk crop-btn" id="saveCropBtn">
                            <i class="bi bi-check-circle me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @stack('scripts')
</body>

</html>
