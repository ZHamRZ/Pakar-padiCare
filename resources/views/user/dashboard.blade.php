@extends('layouts.app')

@section('title', 'Beranda — PadiCare Lombok')
@section('page-title', 'Beranda')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap');

    :root {
        --green-50:  #f0fdf4;
        --green-100: #dcfce7;
        --green-200: #bbf7d0;
        --green-300: #86efac;
        --green-400: #4ade80;
        --green-500: #22c55e;
        --green-600: #16a34a;
        --green-700: #15803d;
        --green-800: #166534;
        --green-900: #14532d;
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
        --shadow-green: 0 8px 32px rgba(22,163,74,.18);
    }

    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'DM Sans', sans-serif; background: var(--slate-50); }
    h1,h2,h3,h4,h5,.fw-bold,.fw-semibold,.fw-800 {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ── Shell ── */
    .dashboard-shell { max-width: 1320px; margin: 0 auto; }

    /* ── Topbar ── */
    .guest-topbar {
        display: flex; align-items: center;
        justify-content: space-between; gap: 1rem;
        margin-bottom: 2rem;
        padding: 16px 24px;
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
    }
    .brand-lockup { display: inline-flex; align-items: center; gap: .875rem; }
    .brand-badge {
        width: 48px; height: 48px;
        border-radius: var(--r-md);
        background: linear-gradient(135deg, var(--green-900), var(--green-600));
        color: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 4px 14px rgba(21,128,61,.35);
        flex-shrink: 0;
    }
    .brand-name {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800; font-size: 17px;
        color: var(--slate-900); line-height: 1.2;
    }
    .brand-sub { font-size: 12px; color: var(--slate-400); font-weight: 500; }

    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .topbar-nav-link {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 600;
        color: var(--slate-500);
        text-decoration: none;
        padding: 8px 14px;
        border-radius: var(--r-sm);
        transition: color .2s, background .2s;
    }
    .topbar-nav-link:hover { color: var(--green-700); background: var(--green-50); }

    .btn-register {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 10px 22px;
        border: none;
        border-radius: var(--r-md);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, var(--green-600), var(--green-500));
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(22,163,74,.3);
        transition: transform .15s, box-shadow .2s;
    }
    .btn-register:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(22,163,74,.4);
    }

    /* ── Hero ── */
    .user-hero {
        background:
            radial-gradient(ellipse at 80% 20%, rgba(74,222,128,.15) 0%, transparent 50%),
            radial-gradient(ellipse at 20% 80%, rgba(245,166,35,.12) 0%, transparent 50%),
            linear-gradient(140deg, #0d3d20 0%, #14532d 40%, #1e6b3c 70%, #2d8a4e 100%);
        color: #fff;
        border-radius: var(--r-xl);
        position: relative;
        overflow: hidden;
        padding: 48px 52px;
        margin-bottom: 28px;
    }
    .user-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -60px;
        width: 360px; height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.06) 0%, transparent 70%);
        pointer-events: none;
    }
    .user-hero::after {
        content: '';
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }
    .hero-content { position: relative; z-index: 1; }
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
    .hero-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 100px;
        font-size: 12px; font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: rgba(255,255,255,.9);
        margin-bottom: 18px;
        letter-spacing: .04em;
    }
    .hero-title {
        font-size: 2rem; font-weight: 800; line-height: 1.25;
        margin-bottom: 14px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .hero-sub {
        color: rgba(255,255,255,.65); font-size: 14.5px;
        max-width: 540px; line-height: 1.75; margin-bottom: 28px;
    }
    .btn-hero-primary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 26px;
        background: #fff; color: var(--green-800);
        border: none; border-radius: var(--r-md);
        font-family: 'Plus Jakarta Sans', sans-serif;
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
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px; font-weight: 600;
        text-decoration: none;
        transition: background .2s, border-color .2s;
    }
    .btn-hero-outline:hover {
        background: rgba(255,255,255,.18);
        border-color: rgba(255,255,255,.6); color: #fff;
    }

    /* ── Floating stat pills ── */
    .hero-floating-stats {
        display: flex; gap: 10px; flex-wrap: wrap;
        margin-top: 22px;
    }
    .hero-stat-pill {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 14px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 100px;
        font-size: 12.5px; font-weight: 600;
        color: rgba(255,255,255,.85);
    }
    .hero-stat-pill i { color: var(--green-300); font-size: 13px; }

    /* ── Metrics ── */
    .metrics-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
    @media (max-width: 1100px) { .metrics-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr; } }

    .metric-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--r-lg);
        padding: 22px 20px;
        box-shadow: var(--shadow-sm);
        transition: box-shadow .25s, transform .25s;
        position: relative; overflow: hidden;
    }
    .metric-card::before {
        content: ''; position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--green-500), var(--green-300));
        opacity: 0; transition: opacity .25s;
    }
    .metric-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
    .metric-card:hover::before { opacity: 1; }
    .metric-icon {
        width: 46px; height: 46px;
        border-radius: var(--r-sm);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.15rem; flex-shrink: 0;
    }
    .metric-value {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 2.2rem; font-weight: 800;
        line-height: 1; color: var(--slate-900);
        margin: 8px 0 4px;
    }
    .metric-label { font-size: 11.5px; color: var(--slate-400); font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
    .metric-desc { font-size: 12.5px; color: var(--slate-500); margin-top: 3px; }

    /* ── Login + Info section ── */
    .login-section { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
    @media (max-width: 900px) { .login-section { grid-template-columns: 1fr; } }

    /* Info Panel */
    .info-panel {
        background: linear-gradient(150deg, var(--green-900) 0%, #1b5e35 50%, #23784a 100%);
        border-radius: var(--r-xl);
        padding: 36px 32px;
        color: #fff;
        position: relative; overflow: hidden;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .info-panel::before {
        content: '';
        position: absolute; top: -80px; right: -80px;
        width: 300px; height: 300px; border-radius: 50%;
        background: radial-gradient(circle, rgba(74,222,128,.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .info-panel::after {
        content: '';
        position: absolute; bottom: -60px; left: -40px;
        width: 220px; height: 220px; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.06) 0%, transparent 70%);
        pointer-events: none;
    }
    .info-panel-content { position: relative; z-index: 1; }
    .info-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 700; letter-spacing: .1em;
        text-transform: uppercase; color: var(--green-300);
        background: rgba(74,222,128,.12);
        border: 1px solid rgba(74,222,128,.2);
        padding: 5px 12px; border-radius: 100px;
        margin-bottom: 20px;
    }
    .info-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.5rem; font-weight: 800;
        line-height: 1.3; color: #fff; margin-bottom: 12px;
    }
    .info-desc {
        font-size: 13.5px; color: rgba(255,255,255,.65);
        line-height: 1.75; margin-bottom: 28px;
    }
    .info-features { display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px; }
    .info-feature {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 16px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: var(--r-md);
        backdrop-filter: blur(4px);
    }
    .info-feature-icon {
        width: 38px; height: 38px; border-radius: var(--r-sm);
        background: linear-gradient(135deg, var(--green-600), var(--green-400));
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem; color: #fff; flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(22,163,74,.3);
    }
    .info-feature-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 2px;
    }
    .info-feature-desc { font-size: 12px; color: rgba(255,255,255,.55); line-height: 1.5; }
    .info-divider {
        border-top: 1px solid rgba(255,255,255,.1);
        padding-top: 20px; margin-top: 4px;
        display: flex; gap: 24px;
    }
    .info-stat-num {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.5rem; font-weight: 800; color: var(--green-300);
    }
    .info-stat-lbl { font-size: 11.5px; color: rgba(255,255,255,.5); margin-top: 1px; }

    /* Login Panel */
    .login-panel {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        display: flex; flex-direction: column;
    }
    .login-panel-header {
        padding: 28px 32px 24px;
        border-bottom: 1px solid var(--slate-100);
        background: linear-gradient(135deg, var(--green-50) 0%, #f8fafc 100%);
    }
    .login-panel-body { padding: 28px 32px; flex: 1; }
    .login-panel-footer {
        padding: 16px 32px 24px;
    }
    .secure-chip {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 11px; border-radius: 100px;
        background: var(--green-100);
        border: 1px solid var(--green-200);
        font-size: 11px; font-weight: 700;
        color: var(--green-700);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .login-avatar {
        width: 52px; height: 52px; border-radius: var(--r-md);
        background: linear-gradient(135deg, var(--green-600), var(--green-400));
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; color: #fff; margin-bottom: 14px;
        box-shadow: var(--shadow-green);
    }
    .form-floating-group { position: relative; margin-bottom: 16px; }
    .form-floating-group .fi { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--slate-400); font-size: .95rem; pointer-events: none; }
    .form-floating-group.has-toggle .form-control { padding-right: 46px; }
    .password-toggle-inline {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border: 0;
        background: transparent;
        color: var(--slate-400);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: background .2s, color .2s;
    }
    .password-toggle-inline:hover {
        background: var(--green-100);
        color: var(--green-700);
    }
    .form-control, .form-select {
        border: 1.5px solid var(--slate-200);
        border-radius: var(--r-sm);
        font-family: 'DM Sans', sans-serif;
        font-size: 14px; color: var(--slate-700);
        padding: 11px 14px 11px 42px;
        background: var(--slate-50);
        width: 100%;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--green-500);
        box-shadow: 0 0 0 3px rgba(34,197,94,.12);
        background: #fff;
        outline: none;
    }
    .form-label {
        font-size: 11.5px; font-weight: 700;
        color: var(--slate-500);
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin-bottom: 7px; letter-spacing: .04em;
        text-transform: uppercase; display: block;
    }
    .btn-login {
        width: 100%; padding: 13px;
        background: linear-gradient(135deg, var(--green-600), var(--green-500));
        color: #fff;
        border: none; border-radius: var(--r-md);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14.5px; font-weight: 700;
        cursor: pointer;
        box-shadow: 0 5px 16px rgba(22,163,74,.3);
        transition: transform .15s, box-shadow .2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(22,163,74,.38);
    }
    .btn-daftar {
        width: 100%; padding: 11px;
        background: transparent;
        color: var(--green-700);
        border: 2px solid var(--green-200);
        border-radius: var(--r-md);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 700;
        cursor: pointer; text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        transition: background .2s, border-color .2s;
    }
    .btn-daftar:hover {
        background: var(--green-50);
        border-color: var(--green-400);
        color: var(--green-800);
    }
    .divider-or {
        display: flex; align-items: center; gap: 12px;
        margin: 16px 0;
    }
    .divider-or::before, .divider-or::after {
        content: ''; flex: 1;
        height: 1px; background: var(--slate-200);
    }
    .divider-or span {
        font-size: 11px; font-weight: 700; color: var(--slate-400);
        font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: .06em;
    }

    /* ── Section Card ── */
    .section-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .section-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--slate-100);
        background: var(--slate-50);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700; font-size: 14px;
        color: var(--slate-700);
        display: flex; align-items: center; justify-content: space-between;
    }
    .section-card-body { padding: 24px; }

    /* ── Case Cards ── */
    .cases-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
    @media (max-width: 1100px) { .cases-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .cases-grid { grid-template-columns: 1fr; } }

    .case-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: var(--r-xl);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: box-shadow .25s, transform .25s;
    }
    .case-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-4px); }
    .case-media {
        width: 100%; height: 190px;
        object-fit: cover; display: block;
    }
    .case-media-empty {
        width: 100%; height: 190px;
        background: linear-gradient(135deg, var(--green-50) 0%, var(--slate-100) 100%);
        display: flex; align-items: center; justify-content: center;
        color: var(--slate-300); font-size: 2.8rem;
    }
    .case-body { padding: 18px 20px; }
    .case-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 14px; }
    .disease-chip {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 100px;
        background: var(--green-50);
        border: 1px solid var(--green-200);
        font-size: 12px; font-weight: 700;
        color: var(--green-700);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .search-count {
        font-size: 12px; font-weight: 700;
        color: var(--green-600);
        font-family: 'Plus Jakarta Sans', sans-serif;
        white-space: nowrap;
        background: var(--green-50);
        border: 1px solid var(--green-100);
        padding: 3px 9px;
        border-radius: 100px;
    }
    .gejala-chip {
        display: inline-flex; align-items: center;
        padding: 3px 9px; border-radius: 100px;
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        font-size: 11px; font-weight: 600;
        color: var(--slate-600);
    }
    .case-divider {
        border-top: 1px solid var(--slate-100);
        padding-top: 11px; margin-top: 11px;
        display: flex; gap: 16px;
    }
    .case-meta-item { flex: 1; }
    .case-meta-label { font-size: 10.5px; color: var(--slate-400); font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 3px; }
    .case-meta-value { font-size: 13px; font-weight: 700; color: var(--slate-700); font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Rekomendasi terakhir ── */
    .rec-item {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid var(--slate-100);
    }
    .rec-item:last-of-type { border-bottom: none; }
    .rec-dot {
        width: 9px; height: 9px; border-radius: 50%;
        background: linear-gradient(135deg, var(--green-500), var(--green-300));
        flex-shrink: 0;
    }
    .rec-key { font-size: 11.5px; color: var(--slate-400); font-weight: 600; }
    .rec-val { font-size: 13.5px; font-weight: 700; color: var(--slate-700); font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Section eyebrow ── */
    .section-eyebrow {
        display: inline-flex; align-items: center; gap: 7px;
        font-size: 11px; font-weight: 700;
        letter-spacing: .09em; text-transform: uppercase;
        color: var(--green-700);
        background: var(--green-100);
        border: 1px solid var(--green-200);
        padding: 5px 12px; border-radius: 100px;
        margin-bottom: 14px;
    }

    /* ── Auth history bottom ── */
    .auth-bottom { display: grid; grid-template-columns: 7fr 5fr; gap: 24px; }
    @media (max-width: 1024px) { .auth-bottom { grid-template-columns: 1fr; } }

    /* ── Btn spk ── */
    .btn-spk-custom {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 26px;
        background: linear-gradient(135deg, var(--green-600), var(--green-500));
        color: #fff; border: none; border-radius: var(--r-md);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px; font-weight: 700;
        text-decoration: none;
        box-shadow: 0 5px 16px rgba(22,163,74,.28);
        transition: transform .15s, box-shadow .2s;
    }
    .btn-spk-custom:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(22,163,74,.38);
    }

    /* ── Table ── */
    .table th {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em;
        color: var(--slate-400); background: var(--slate-50);
        padding: 12px 16px;
    }
    .table td { padding: 13px 16px; vertical-align: middle; }

    /* ── Empty state ── */
    .empty-state {
        border: 1.5px dashed var(--slate-300);
        border-radius: var(--r-xl);
        background: var(--slate-50);
    }

    /* ── Section header row ── */
    .section-header-row {
        display: flex; align-items: flex-end;
        justify-content: space-between; gap: 16px;
        margin-bottom: 20px; flex-wrap: wrap;
    }
    .section-header-row h4 {
        font-size: 1.2rem; font-weight: 800;
        color: var(--slate-900); margin-bottom: 4px;
    }
    .section-header-row p {
        font-size: 13px; color: var(--slate-400);
        max-width: 520px; line-height: 1.6; margin: 0;
    }

    /* ── Anim ── */
    .anim-fade-up {
        opacity: 0; transform: translateY(18px);
        animation: fadeUp .55s cubic-bezier(.25,.8,.25,1) forwards;
    }
    @keyframes fadeUp { to { opacity:1; transform:translateY(0); } }
    .anim-fade-up:nth-child(1) { animation-delay: .04s; }
    .anim-fade-up:nth-child(2) { animation-delay: .1s; }
    .anim-fade-up:nth-child(3) { animation-delay: .16s; }
    .anim-fade-up:nth-child(4) { animation-delay: .22s; }
    .anim-fade-up:nth-child(5) { animation-delay: .28s; }
    .anim-fade-up:nth-child(6) { animation-delay: .34s; }
</style>
@endpush

@section('content')
@guest
<div class="container py-4 py-lg-5">
    <div class="dashboard-shell">

        {{-- Flash --}}
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

        {{-- Topbar --}}
        <div class="guest-topbar anim-fade-up">
            <div class="brand-lockup">
                <span class="brand-badge"><i class="bi bi-leaf-fill"></i></span>
                <div>
                    <div class="brand-name">PadiCare <span style="color:var(--green-600);">Lombok</span></div>
                    <div class="brand-sub">Sistem Pakar Penyakit & Rekomendasi Pupuk Padi</div>
                </div>
            </div>
            <div class="topbar-right">
                <a href="#login" class="topbar-nav-link">Masuk</a>
                <a href="{{ route('register') }}" class="btn-register">
                    <i class="bi bi-person-plus"></i> Daftar Gratis
                </a>
            </div>
        </div>
@endguest

@auth
<div class="dashboard-shell">
@endauth

    {{-- ── Hero ── --}}
    <div class="user-hero mb-4 anim-fade-up">
        <div class="hero-content row align-items-center g-4">
            <div class="col-lg-8">
                <div class="hero-badge">
                    <i class="bi bi-patch-check-fill"></i>
                    {{ $user ? 'Dashboard Pengguna' : 'Sistem Pakar Padi — Lombok' }}
                </div>
                <h2 class="hero-title">
                    {{ $user
                        ? 'Selamat datang kembali, ' . $user->nama . '.'
                        : 'Identifikasi penyakit padi & dapatkan rekomendasi pupuk secara instan.' }}
                </h2>
                <p class="hero-sub">
                    {{ $user
                        ? 'Gunakan dashboard ini untuk memulai diagnosis, melihat hasil rekomendasi, dan membandingkan kasus yang pernah terjadi.'
                        : 'Pilih gejala tanaman Anda, sistem pakar akan mencocokkan penyakit dan merekomendasikan pupuk serta pestisida yang paling tepat untuk lahan Anda.' }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.diagnosis.index') }}" class="btn-hero-primary">
                        <i class="bi bi-search-heart"></i>
                        {{ $user ? 'Mulai Diagnosis' : 'Mulai Diagnosis Sekarang' }}
                    </a>
                    <a href="#riwayat-kasus" class="btn-hero-outline">
                        <i class="bi bi-clock-history"></i> Lihat Riwayat Kasus
                    </a>
                </div>
                @guest
                <div class="hero-floating-stats">
                    <span class="hero-stat-pill"><i class="bi bi-check-circle-fill"></i> {{ $progress['penyakit'] }} penyakit terdata</span>
                    <span class="hero-stat-pill"><i class="bi bi-lightning-fill"></i> Hasil diagnosis instan</span>
                    <span class="hero-stat-pill"><i class="bi bi-shield-fill-check"></i> Data lokal Lombok</span>
                </div>
                @endguest
            </div>
            <div class="col-lg-4">
                <div class="hero-summary-card">
                    <div style="font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:rgba(255,255,255,.5); margin-bottom:14px;">
                        {{ $user ? 'Ringkasan Akun' : 'Ringkasan Sistem' }}
                    </div>
                    @if($user)
                    <div class="mb-3">
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:17px; color:#fff;">{{ $user->nama }}</div>
                        <div style="font-size:12px; color:rgba(255,255,255,.5); margin-top:2px;">{{ $user->username }}</div>
                    </div>
                    <div class="hero-stat-divider d-flex justify-content-between text-center">
                        <div>
                            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:22px; color:#fff;">{{ $rekomendasi7Hari }}</div>
                            <div style="font-size:11px; color:rgba(255,255,255,.5);">7 hari</div>
                        </div>
                        <div>
                            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:22px; color:#fff;">{{ $rekomendasiBulanIni }}</div>
                            <div style="font-size:11px; color:rgba(255,255,255,.5);">Bulan ini</div>
                        </div>
                        <div>
                            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:22px; color:#fff;">{{ $totalRekomendasi }}</div>
                            <div style="font-size:11px; color:rgba(255,255,255,.5);">Total</div>
                        </div>
                    </div>
                    @else
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach([['penyakit','Penyakit Terdata','Sudah terdokumentasi di sistem'],['gejala','Gejala Tersedia','Bahan identifikasi penyakit'],['riwayat','Riwayat Kasus','Referensi cepat petani']] as [$key,$label,$sub])
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid rgba(255,255,255,.1);">
                            <div>
                                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:16px; color:#fff;">{{ $progress[$key] }} {{ $label }}</div>
                                <div style="font-size:12px; color:rgba(255,255,255,.5);">{{ $sub }}</div>
                            </div>
                            <i class="bi bi-check-circle-fill" style="color:var(--green-300); font-size:1.15rem;"></i>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Metrics ── --}}
    <div class="metrics-row anim-fade-up">
        @php
        $metrics = [
            ['label'=>$user?'Total Rekomendasi':'Total Riwayat Kasus','value'=>$user?$totalRekomendasi:$progress['riwayat'],'desc'=>$user?'Semua hasil yang pernah disimpan':'Kasus lama sebagai referensi','icon'=>'bi-award-fill','bg'=>'var(--green-100)','color'=>'var(--green-700)'],
            ['label'=>$user?'Aktivitas Bulan Ini':'Kasus Paling Dicari','value'=>$user?$rekomendasiBulanIni:$riwayatReferensi->count(),'desc'=>$user?'Rekomendasi bulan ini':'Relevan berdasarkan pencarian','icon'=>'bi-calendar-check-fill','bg'=>'#dbeafe','color'=>'#2563eb'],
            ['label'=>'Gejala Tersedia','value'=>$progress['gejala'],'desc'=>'Bahan identifikasi penyakit','icon'=>'bi-clipboard2-pulse-fill','bg'=>'#fef3c7','color'=>'#d97706'],
            ['label'=>'Penyakit Terdata','value'=>$progress['penyakit'],'desc'=>'Kemungkinan hasil identifikasi','icon'=>'bi-virus2','bg'=>'#fce7f3','color'=>'#db2777'],
        ];
        @endphp
        @foreach($metrics as $m)
        <div class="metric-card">
            <div class="d-flex align-items-start justify-content-between">
                <div class="metric-label">{{ $m['label'] }}</div>
                <div class="metric-icon" style="background:{{ $m['bg'] }}; color:{{ $m['color'] }};">
                    <i class="bi {{ $m['icon'] }}"></i>
                </div>
            </div>
            <div class="metric-value">{{ $m['value'] }}</div>
            <div class="metric-desc">{{ $m['desc'] }}</div>
        </div>
        @endforeach
    </div>

    @guest
    {{-- ── Login + Info Panel ── --}}
    <div class="login-section anim-fade-up" id="login">

        {{-- Info Panel (Left) --}}
        <div class="info-panel">
            <div class="info-panel-content">
                <div class="info-eyebrow"><i class="bi bi-info-circle-fill"></i> Tentang PadiCare</div>
                <h3 class="info-title">Solusi Cerdas untuk Petani Padi di Lombok</h3>
                <p class="info-desc">
                    PadiCare Lombok adalah sistem pakar berbasis teknologi yang dirancang khusus untuk membantu petani mengidentifikasi penyakit padi dan mendapatkan rekomendasi pupuk serta pestisida yang tepat sasaran.
                </p>
                <div class="info-features">
                    <div class="info-feature">
                        <div class="info-feature-icon"><i class="bi bi-search-heart"></i></div>
                        <div>
                            <div class="info-feature-title">Diagnosis Penyakit Tanaman Padi</div>
                            <div class="info-feature-desc">Identifikasi penyakit berdasarkan gejala yang terlihat di lapangan menggunakan basis data.</div>
                        </div>
                    </div>
                    <div class="info-feature">
                        <div class="info-feature-icon"><i class="bi bi-droplet-fill"></i></div>
                        <div>
                            <div class="info-feature-title">Rekomendasi Pupuk & Pestisida</div>
                            <div class="info-feature-desc">Dapatkan saran pupuk dan pestisida yang tepat berdasarkan jenis penyakit dan kondisi lahan Anda.</div>
                        </div>
                    </div>
                    <div class="info-feature">
                        <div class="info-feature-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <div class="info-feature-title">Riwayat & Referensi Kasus</div>
                            <div class="info-feature-desc">Simpan dan bandingkan hasil diagnosis untuk memantau kesehatan lahan secara berkala.</div>
                        </div>
                    </div>
                </div>
                <div class="info-divider">
                    <div>
                        <div class="info-stat-num">{{ $progress['penyakit'] }}+</div>
                        <div class="info-stat-lbl">Penyakit Terdata</div>
                    </div>
                    <div>
                        <div class="info-stat-num">{{ $progress['gejala'] }}+</div>
                        <div class="info-stat-lbl">Gejala Tersedia</div>
                    </div>
                    <div>
                        <div class="info-stat-num">{{ $progress['riwayat'] }}+</div>
                        <div class="info-stat-lbl">Kasus Terdokumentasi</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Login Panel (Right) --}}
        <div class="login-panel">
            <div class="login-panel-header">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="login-avatar"><i class="bi bi-person-fill"></i></div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:18px; color:var(--slate-900); margin-bottom:4px;">Masuk ke Akun Anda</div>
                        <div style="font-size:13px; color:var(--slate-500);">Akses riwayat diagnosis dan rekomendasi tersimpan Anda.</div>
                    </div>
                    <span class="secure-chip"><i class="bi bi-shield-lock-fill"></i> Aman</span>
                </div>
            </div>
            <div class="login-panel-body">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-1">
                        <label class="form-label">Username</label>
                        <div class="form-floating-group">
                            <i class="bi bi-person fi"></i>
                            <input type="text" name="username" value="{{ old('username') }}"
                                class="form-control @error('username') is-invalid @enderror"
                                placeholder="Masukkan username Anda">
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="form-floating-group has-toggle">
                            <i class="bi bi-lock fi"></i>
                            <input type="password" id="home-login-password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan password Anda">
                            <button class="password-toggle-inline password-toggle" type="button"
                                data-target="home-login-password" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="text-end mt-2">
                            <a href="{{ route('password.request') }}" style="font-size:13px; font-weight:700; color:var(--green-600); text-decoration:none;">
                                Lupa password?
                            </a>
                        </div>
                    </div>
                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                    </button>
                </form>
                <div class="divider-or"><span>ATAU</span></div>
                <a href="{{ route('register') }}" class="btn-daftar">
                    <i class="bi bi-person-plus"></i> Belum punya akun? Daftar Gratis
                </a>
            </div>
            <div class="login-panel-footer">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <span style="display:inline-flex; align-items:center; gap:5px; font-size:12px; color:var(--slate-400);">
                        <i class="bi bi-check2-circle" style="color:var(--green-500);"></i> Gratis digunakan
                    </span>
                    <span style="display:inline-flex; align-items:center; gap:5px; font-size:12px; color:var(--slate-400);">
                        <i class="bi bi-check2-circle" style="color:var(--green-500);"></i> Data tersimpan aman
                    </span>
                    <span style="display:inline-flex; align-items:center; gap:5px; font-size:12px; color:var(--slate-400);">
                        <i class="bi bi-check2-circle" style="color:var(--green-500);"></i> Mudah di gunakan
                    </span>
                </div>
            </div>
        </div>

    </div>
    @endguest

    {{-- ── Riwayat Kasus ── --}}
    <div class="section-header-row anim-fade-up" id="riwayat-kasus">
        <div>
            <div class="section-eyebrow"><i class="bi bi-collection-fill"></i> Referensi Publik</div>
            <h4>Riwayat Kasus Lama</h4>
            <p>Kasus paling relevan dan sering dicari — petani bisa menemukan acuan yang mirip lebih cepat.</p>
        </div>
    </div>

    @if($riwayatReferensi->isEmpty())
    <div class="empty-state text-center py-5 px-4 mb-4 anim-fade-up">
        <i class="bi bi-inbox fs-1 d-block mb-3" style="color:var(--slate-300);"></i>
        <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; color:var(--slate-600);">Belum ada riwayat kasus</div>
        <div style="font-size:13px; color:var(--slate-400); margin-top:4px;">Setelah pengguna melakukan diagnosis, hasilnya akan tampil di sini.</div>
    </div>
    @else
    <div class="cases-grid">
        @foreach($riwayatReferensi as $riwayat)
        <div class="case-card anim-fade-up">
            @if(optional($riwayat->penyakit)->gambar_url)
            <img src="{{ $riwayat->penyakit->gambar_url }}" alt="{{ $riwayat->penyakit->nama }}" class="case-media">
            @else
            <div class="case-media-empty"><i class="bi bi-virus"></i></div>
            @endif
            <div class="case-body">
                <div class="case-header">
                    <div>
                        <div class="disease-chip mb-1"><i class="bi bi-bug-fill"></i> {{ $riwayat->penyakit->nama ?? 'Belum diketahui' }}</div>
                        <div style="font-size:12px; color:var(--slate-400);">{{ optional($riwayat->created_at)->format('d M Y') }}</div>
                    </div>
                    <span class="search-count">{{ number_format($riwayat->total_dicari ?? 0) }}× dicari</span>
                </div>
                <div style="font-size:11px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.05em; margin-bottom:8px;">Gejala terkait</div>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @php $gejalaList = collect(optional($riwayat->penyakit)->gejala); @endphp
                    @foreach($gejalaList->take(3) as $gejala)
                    <span class="gejala-chip">{{ $gejala->nama_gejala }}</span>
                    @endforeach
                    @if($gejalaList->count() > 3)
                    <span class="gejala-chip" style="background:var(--green-50); color:var(--green-700); border-color:var(--green-200);">+{{ $gejalaList->count() - 3 }} lainnya</span>
                    @endif
                    @if($gejalaList->isEmpty())
                    <span style="font-size:12px; color:var(--slate-400);">Belum dicatat</span>
                    @endif
                </div>
                <div class="case-divider">
                    <div class="case-meta-item">
                        <div class="case-meta-label"><i class="bi bi-droplet-fill me-1" style="color:var(--green-500);"></i>Pupuk</div>
                        <div class="case-meta-value">{{ optional(optional($riwayat->detailPupuk->first())->pupuk)->nama ?: '—' }}</div>
                    </div>
                    <div class="case-meta-item">
                        <div class="case-meta-label"><i class="bi bi-shield-fill me-1" style="color:#2563eb;"></i>Pestisida</div>
                        <div class="case-meta-value">{{ optional(optional($riwayat->detailPestisida->first())->pestisida)->nama ?: '—' }}</div>
                    </div>
                </div>
                <div style="font-size:11px; color:var(--slate-400); margin-top:12px; line-height:1.55; font-style:italic;">
                    Gunakan sebagai acuan awal — lakukan diagnosis pada tanaman Anda untuk hasil lebih tepat.
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Auth-only section ── --}}
    @auth
    <div class="auth-bottom anim-fade-up">
        <div class="section-card">
            <div class="section-card-header">
                <span><i class="bi bi-table me-2" style="color:var(--green-500);"></i>Riwayat Terbaru Saya</span>
                <a href="{{ route('user.riwayat.index') }}" style="font-size:12px; font-weight:700; color:var(--green-600); text-decoration:none;">Lihat semua →</a>
            </div>
            <div class="section-card-body p-0">
                @if($riwayatTerbaru->isEmpty())
                <div class="text-center py-5" style="color:var(--slate-400); font-size:13px;">
                    Belum ada riwayat. Mulai diagnosis untuk membuat rekomendasi pertama.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th><th>Penyakit</th><th>Top Pupuk</th><th>Top Pestisida</th><th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatTerbaru as $r)
                            <tr>
                                <td style="font-size:13px; color:var(--slate-500);">{{ optional($r->created_at)->format('d M Y') }}</td>
                                <td><span style="background:var(--green-100); color:var(--green-700); padding:3px 10px; border-radius:100px; font-size:12px; font-weight:700; font-family:'Plus Jakarta Sans',sans-serif;">{{ $r->penyakit->nama ?? '-' }}</span></td>
                                <td style="font-size:13px; color:var(--slate-600);">{{ optional(optional($r->detailPupuk->first())->pupuk)->nama ?: '-' }}</td>
                                <td style="font-size:13px; color:var(--slate-600);">{{ optional(optional($r->detailPestisida->first())->pestisida)->nama ?: '-' }}</td>
                                <td class="text-end"><a href="{{ route('user.rekomendasi.show', $r->id) }}" style="font-size:12px; font-weight:700; color:var(--green-600); text-decoration:none;">Lihat →</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <div>
            <div class="section-card">
                <div class="section-card-header"><span><i class="bi bi-star-fill me-2" style="color:#f59e0b;"></i>Rekomendasi Terakhir</span></div>
                <div class="section-card-body">
                    @if(!$rekomendasiTerakhir)
                    <div style="font-size:13px; color:var(--slate-400);">Belum ada rekomendasi yang tersimpan.</div>
                    @else
                    <div class="rec-item"><div class="rec-dot"></div><div><div class="rec-key">Penyakit</div><div class="rec-val">{{ $penyakitTerakhir->nama ?? '-' }}</div></div></div>
                    <div class="rec-item"><div class="rec-dot"></div><div><div class="rec-key">Tanggal</div><div class="rec-val">{{ optional($rekomendasiTerakhir->created_at)->format('d M Y H:i') }}</div></div></div>
                    <div class="rec-item"><div class="rec-dot"></div><div><div class="rec-key">Pupuk terbaik</div><div class="rec-val">{{ optional(optional($rekomendasiTerakhir->detailPupuk->first())->pupuk)->nama ?: '-' }}</div></div></div>
                    <div class="rec-item" style="margin-bottom:18px;"><div class="rec-dot"></div><div><div class="rec-key">Pestisida terbaik</div><div class="rec-val">{{ optional(optional($rekomendasiTerakhir->detailPestisida->first())->pestisida)->nama ?: '-' }}</div></div></div>
                    <a href="{{ route('user.rekomendasi.show', $rekomendasiTerakhir->id) }}" class="btn-spk-custom">
                        <i class="bi bi-arrow-right-circle-fill"></i> Buka Hasil Terakhir
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endauth

@auth
</div>
@endauth

@guest
    </div>
</div>
@endguest
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
