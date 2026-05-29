@extends('layouts.app')

@section('title', 'Dashboard Admin — PadiCare Lombok')
@section('page-title', 'Dashboard Admin')

@php
    $maxPenyakitTeratas = max(1, (int) ($penyakitTeratas->max('total_rekomendasi') ?? 0));
@endphp

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap');

    :root {
        --green-50:  #f0fdf4;
        --green-100: #dcfce7;
        --green-200: #bbf7d0;
        --green-500: #22c55e;
        --green-600: #16a34a;
        --green-700: #15803d;
        --green-800: #166534;
        --green-900: #14532d;
        --amber-100: #fef3c7;
        --amber-700: #b45309;
        --slate-50:  #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --r-sm: 10px;
        --r-md: 16px;
        --r-lg: 20px;
        --r-xl: 26px;
        --shadow-sm: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
        --shadow-md: 0 4px 16px rgba(15,23,42,.07), 0 1px 4px rgba(15,23,42,.04);
        --shadow-lg: 0 12px 40px rgba(15,23,42,.09);
    }

    body { font-family: 'DM Sans', sans-serif; }
    h1,h2,h3,h4,h5,h6,.fw-bold,.fw-semibold {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ── Hero with Image Overlay ── */
    .admin-hero {
        background: linear-gradient(135deg, #0f4c28 0%, #1a7a42 50%, #22a856 100%);
        color: #fff;
        border-radius: var(--r-xl);
        position: relative;
        overflow: hidden;
        padding: 48px 52px;
        margin-bottom: 28px;
    }
    .admin-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -60px;
        width: 360px; height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.06) 0%, transparent 70%);
        pointer-events: none;
    }
    .admin-hero::after {
        content: '';
        position: absolute; inset: 0;
        background-image: url("{{ asset('assets/bagraound dashboard.png') }}");
        background-size: cover;
        background-position: center;
        opacity: 0.2;
        pointer-events: none;
    }
    .hero-content { position: relative; z-index: 1; }
    .hero-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 100px;
        font-size: 12px; font-weight: 700;
        color: rgba(255,255,255,.9);
        margin-bottom: 18px;
        letter-spacing: .04em;
    }
    .hero-title {
        font-size: 2rem; font-weight: 800; line-height: 1.25;
        margin-bottom: 14px;
        color: #fff;
    }
    .hero-sub {
        color: rgba(255,255,255,.65); font-size: 14.5px;
        max-width: 540px; line-height: 1.75; margin-bottom: 28px;
    }
    .hero-summary-card {
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: var(--r-lg);
        backdrop-filter: blur(10px);
        padding: 28px;
    }
    .hero-stat-divider {
        border-top: 1px solid rgba(255,255,255,.15);
        padding-top: 16px; margin-top: 16px;
    }
    .hero-floating-stats { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 22px; }
    .hero-stat-pill {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 14px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 100px;
        font-size: 12.5px; font-weight: 600;
        color: rgba(255,255,255,.85);
    }
    .hero-stat-pill i { color: var(--green-500); font-size: 13px; }
    .btn-hero-primary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 26px;
        background: #fff; color: var(--green-800);
        border: none; border-radius: var(--r-md);
        font-size: 14px; font-weight: 700;
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(0,0,0,.18);
        transition: transform .15s, box-shadow .2s;
    }
    .btn-hero-primary:hover {
        color: var(--green-800);
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(0,0,0,.24);
    }
    .btn-hero-outline {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 22px;
        background: rgba(255,255,255,.1);
        color: rgba(255,255,255,.9);
        border: 1.5px solid rgba(255,255,255,.3);
        border-radius: var(--r-md);
        font-size: 14px; font-weight: 600;
        text-decoration: none;
        transition: background .2s, border-color .2s;
    }
    .btn-hero-outline:hover {
        background: rgba(255,255,255,.18);
        border-color: rgba(255,255,255,.6); color: #fff;
    }

    /* ── Stat Cards ── */
    .stat-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--r-lg);
        padding: 18px 20px;
        height: 100%;
        box-shadow: var(--shadow-sm);
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .stat-icon {
        width: 44px; height: 44px;
        border-radius: var(--r-sm);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.05rem; flex-shrink: 0;
    }
    .stat-label {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em;
        color: var(--slate-400);
    }
    .stat-value {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 2rem; font-weight: 800;
        color: var(--slate-900); line-height: 1.1;
    }
    .stat-desc { font-size: 12px; color: var(--slate-400); margin-top: 3px; }

    /* ── Section Card ─ */
    .section-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--r-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        height: 100%;
    }
    .section-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--slate-100);
        background: var(--slate-50);
        display: flex; align-items: center; justify-content: space-between;
    }
    .section-card-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700; font-size: 15px; color: var(--slate-800);
    }
    .section-card-sub { font-size: 12px; color: var(--slate-400); }
    .section-card-body { padding: 0; }

    /* ── Table ── */
    .admin-table { margin: 0; }
    .admin-table thead th {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: var(--slate-400); background: var(--slate-50);
        border-bottom: 1px solid var(--slate-200);
        padding: 14px 24px;
    }
    .admin-table tbody td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--slate-100);
        font-size: 13px; color: var(--slate-600);
        vertical-align: middle;
    }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover { background: var(--green-50); }

    /* ── Mini list ── */
    .mini-item {
        padding: 14px 24px;
        border-bottom: 1px solid var(--slate-100);
        transition: background .15s;
    }
    .mini-item:last-child { border-bottom: none; }
    .mini-item:hover { background: var(--slate-50); }

    /* ── Bar Chart ── */
    .bar-row { padding: 12px 24px; border-bottom: 1px solid var(--slate-100); }
    .bar-row:last-child { border-bottom: none; }
    .bar-label {
        font-size: 13px; font-weight: 600;
        color: var(--slate-700);
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin-bottom: 8px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .bar-count { font-size: 12px; color: var(--slate-400); font-weight: 500; }
    .progress-bar-wrap {
        height: 8px; background: var(--slate-100);
        border-radius: 999px; overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%; border-radius: 999px;
        background: linear-gradient(90deg, var(--green-600), var(--green-500));
        transition: width .8s cubic-bezier(.25,.8,.25,1);
    }
    .progress-bar-fill.ready { width: var(--pct); }

    /* ── Section eyebrow ─ */
    .section-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 700;
        letter-spacing: .08em; text-transform: uppercase;
        color: var(--green-700); background: var(--green-100);
        border: 1px solid var(--green-200);
        padding: 4px 10px; border-radius: 100px;
        margin-bottom: 12px;
    }

    /* ── Anim ── */
    .anim-fade-up {
        opacity: 0; transform: translateY(14px);
        animation: fadeUp .45s ease forwards;
    }
    @keyframes fadeUp { to { opacity:1; transform:translateY(0); } }
    .anim-fade-up:nth-child(1) { animation-delay: .05s; }
    .anim-fade-up:nth-child(2) { animation-delay: .10s; }
    .anim-fade-up:nth-child(3) { animation-delay: .15s; }
    .anim-fade-up:nth-child(4) { animation-delay: .20s; }
    .anim-fade-up:nth-child(5) { animation-delay: .25s; }
    .anim-fade-up:nth-child(6) { animation-delay: .30s; }
    .anim-fade-up:nth-child(7) { animation-delay: .35s; }
    .anim-fade-up:nth-child(8) { animation-delay: .40s; }
</style>
@endpush

@section('content')

{{-- ── Hero with Image Overlay ── --}}
<div class="admin-hero anim-fade-up">
    <div class="hero-content row align-items-center g-4">
        <div class="col-lg-8">
            <div class="hero-badge">
                <i class="bi bi-shield-lock-fill"></i> Panel Admin · PadiCare Lombok
            </div>
            <h2 class="hero-title">
                Kelola basis pengetahuan pakar, rule CF, dan pantau hasil rekomendasi.
            </h2>
            <p class="hero-sub">
                Ikuti alur sistem pakar Certainty Factor: data master → rule MB/MD → parameter prioritas → riwayat rekomendasi.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.penyakit.index') }}" class="btn-hero-primary">
                    <i class="bi bi-virus"></i> Kelola Penyakit
                </a>
                <a href="{{ route('admin.kriteria.index') }}" class="btn-hero-outline">
                    <i class="bi bi-sliders"></i> Kelola Kriteria CF
                </a>
                <a href="{{ route('admin.riwayat.index') }}" class="btn-hero-outline">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>
            </div>
            <div class="hero-floating-stats">
                <span class="hero-stat-pill"><i class="bi bi-virus2"></i> {{ $stats['penyakit'] }} penyakit</span>
                <span class="hero-stat-pill"><i class="bi bi-clipboard2-pulse"></i> {{ $stats['gejala'] }} gejala</span>
                <span class="hero-stat-pill"><i class="bi bi-people-fill"></i> {{ $stats['user'] }} petani</span>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="hero-summary-card">
                <div style="font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:rgba(255,255,255,.5); margin-bottom:14px;">
                    Aktivitas Sistem
                </div>
                <div class="d-flex align-items-end justify-content-between mb-2">
                    <div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:2.4rem; color:#fff; line-height:1;">
                            {{ $rekomendasi7Hari }}
                        </div>
                        <div style="font-size:12px; color:rgba(255,255,255,.5); margin-top:3px;">rekomendasi dalam 7 hari</div>
                    </div>
                    <i class="bi bi-graph-up-arrow" style="font-size:2rem; color:rgba(245,166,35,.7);"></i>
                </div>
                <div class="hero-stat-divider d-flex justify-content-between text-center">
                    <div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $rekomendasiBulanIni }}</div>
                        <div style="font-size:11px; color:rgba(255,255,255,.5);">Bulan ini</div>
                    </div>
                    <div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $stats['user'] }}</div>
                        <div style="font-size:11px; color:rgba(255,255,255,.5);">Petani aktif</div>
                    </div>
                    <div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $stats['admin'] }}</div>
                        <div style="font-size:11px; color:rgba(255,255,255,.5);">Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Stats Grid ── --}}
@php
$statItems = [
    ['label'=>'Data Penyakit',        'value'=>$stats['penyakit'],    'desc'=>'Basis identifikasi penyakit padi',           'icon'=>'bi-virus2',             'bg'=>'var(--green-100)',     'color'=>'var(--green-700)'],
    ['label'=>'Data Gejala',          'value'=>$stats['gejala'],      'desc'=>'Gejala untuk proses diagnosis',              'icon'=>'bi-clipboard2-pulse',   'bg'=>'var(--green-100)',     'color'=>'var(--green-600)'],
    ['label'=>'Alternatif Pupuk',     'value'=>$stats['pupuk'],       'desc'=>'Data alternatif pemupukan',                  'icon'=>'bi-bag-fill',           'bg'=>'var(--slate-100)',     'color'=>'var(--slate-500)'],
    ['label'=>'Alternatif Pestisida', 'value'=>$stats['pestisida'],   'desc'=>'Data pengendalian penyakit',                 'icon'=>'bi-capsule',            'bg'=>'var(--slate-100)',     'color'=>'var(--slate-500)'],
    ['label'=>'Parameter Prioritas',  'value'=>$stats['kriteria'],    'desc'=>'Dasar preferensi & penyesuaian rule',        'icon'=>'bi-sliders',            'bg'=>'var(--slate-100)',     'color'=>'var(--slate-400)'],
    ['label'=>'Pengguna Petani',      'value'=>$stats['user'],        'desc'=>'Akun pengguna yang bisa diagnosa',           'icon'=>'bi-people-fill',        'bg'=>'var(--green-50)',      'color'=>'var(--green-600)'],
    ['label'=>'Total Rekomendasi',    'value'=>$stats['rekomendasi'], 'desc'=>'Riwayat hasil perhitungan tersimpan',        'icon'=>'bi-clock-history',      'bg'=>'var(--slate-100)',     'color'=>'var(--slate-600)'],
    ['label'=>'Akun Admin',           'value'=>$stats['admin'],       'desc'=>'Pengelola sistem saat ini',                  'icon'=>'bi-shield-lock-fill',   'bg'=>'var(--amber-100)',     'color'=>'var(--amber-700)'],
];
@endphp
<div class="row g-3 mb-4">
    @foreach($statItems as $s)
    <div class="col-md-6 col-xl-3 anim-fade-up">
        <div class="stat-card">
            <div class="stat-label mb-2">{{ $s['label'] }}</div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-value">{{ $s['value'] }}</div>
                <div class="stat-icon" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">
                    <i class="bi {{ $s['icon'] }}"></i>
                </div>
            </div>
            <div class="stat-desc">{{ $s['desc'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ─ Riwayat + Charts ── --}}
<div class="row g-4">
    <div class="col-xl-7 anim-fade-up">
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-title">Riwayat Rekomendasi Terbaru</div>
                <a href="{{ route('admin.riwayat.index') }}"
                   style="font-size:13px; font-weight:700; color:var(--green-600); text-decoration:none;">
                    Lihat semua →
                </a>
            </div>
            @if($riwayatTerbaru->isEmpty())
            <div class="text-center py-5" style="color:var(--slate-400); font-size:13px;">
                Belum ada riwayat rekomendasi yang tersimpan.
            </div>
            @else
            <div class="table-responsive">
                <table class="admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Penyakit</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatTerbaru as $item)
                        <tr>
                            <td data-label="Pengguna">
                                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--slate-800);">
                                    {{ $item->user->nama ?? '-' }}
                                </div>
                                <div style="font-size:12px; color:var(--slate-400);">{{ $item->user->username ?? '-' }}</div>
                            </td>
                            <td data-label="Penyakit">
                                <span style="background:var(--green-100); color:var(--green-700); padding:4px 12px; border-radius:100px; font-size:12px; font-weight:700; font-family:'Plus Jakarta Sans',sans-serif;">
                                    {{ $item->penyakit->nama ?? '-' }}
                                </span>
                            </td>
                            <td data-label="Tanggal" style="font-size:13px; color:var(--slate-500);">
                                {{ optional($item->created_at)->format('d M Y H:i') }}
                            </td>
                            <td data-label="Aksi" class="text-end">
                                <a href="{{ route('admin.riwayat.show', $item->id) }}"
                                   style="font-size:13px; font-weight:700; color:var(--green-600); text-decoration:none;">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="col-xl-5 anim-fade-up">
        {{-- Top Penyakit --}}
        <div class="section-card mb-4" style="height:auto;">
            <div class="section-card-header">
                <div class="section-card-title">Penyakit Terbanyak Direkomendasikan</div>
                <span style="font-size:12px; font-weight:700; background:var(--slate-100); color:var(--slate-500); padding:4px 10px; border-radius:100px;">Top 5</span>
            </div>
            <div class="section-card-body">
                @forelse($penyakitTeratas as $item)
                @php $percent = round(($item->total_rekomendasi / $maxPenyakitTeratas) * 100); @endphp
                <div class="bar-row">
                    <div class="bar-label">
                        {{ $item->nama }}
                        <span class="bar-count">{{ $item->total_rekomendasi }} rekomendasi</span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill ready"
                             style="--pct: {{ $percent }}%; width: {{ $percent }}%;"></div>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:24px 0; font-size:13px; color:var(--slate-400);">
                    Belum ada data distribusi rekomendasi.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pengguna Terbaru --}}
        <div class="section-card" style="height:auto;">
            <div class="section-card-header">
                <div class="section-card-title">Pengguna Terbaru</div>
                <a href="{{ route('admin.pengguna.index') }}"
                   style="font-size:13px; font-weight:700; color:var(--green-600); text-decoration:none;">
                    Kelola user →
                </a>
            </div>
            <div class="section-card-body">
                @forelse($penggunaTerbaru as $user)
                <div class="mini-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; color:var(--slate-800);">
                                {{ $user->nama }}
                            </div>
                            <div style="font-size:12px; color:var(--slate-400);">
                                {{ $user->username }}
                            </div>
                            <div style="font-size:11px; color:var(--slate-400); margin-top:2px;">
                                Terdaftar {{ optional($user->created_at)->format('d M Y') }}
                            </div>
                        </div>
                        <span style="background:var(--slate-100); color:var(--slate-500); padding:4px 10px; border-radius:100px; font-size:11px; font-weight:700; white-space:nowrap;">
                            {{ $user->rekomendasi_count }} riwayat
                        </span>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:24px 0; font-size:13px; color:var(--slate-400);">
                    Belum ada pengguna petani yang terdaftar.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
