<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="@yield('meta-description', 'PadiCare Lombok - Sistem Pakar Diagnosis Penyakit dan Rekomendasi Pupuk untuk Tanaman Padi')">
    <meta name="keywords"
        content="@yield('meta-keywords', 'padi, penyakit padi, pupuk, sistem pakar, diagnosis, pertanian, lombok')">
    <title>@yield('title', 'PadiCare Lombok') - Sistem Pakar Penyakit & Rekomendasi Pupuk Padi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="icon" type="image/png" href="{{ asset('assets/icons8-wheat-hatch-16.png') }}">

    @vite(['resources/css/padicare.css', 'resources/css/mobile.css'])

    <style>
        :root {
            --primary: #15803D;
            --primary-hover: #166534;
            --primary-light: #22C55E;
            --primary-50: #F0FDF4;
            --primary-100: #DCFCE7;
            --primary-200: #BBF7D0;
            --primary-300: #86EFAC;
            --sidebar: #14532D;
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --sidebar-active: rgba(255, 255, 255, 0.15);
            --bg-main: #F9FAFB;
            --bg-card: #FFFFFF;
            --bg-hover: #F9FAFB;
            --text-heading: #111827;
            --text-body: #6B7280;
            --text-muted: #9CA3AF;
            --text-light: #D1D5DB;
            --danger: #EF4444;
            --danger-hover: #DC2626;
            --danger-50: #FEF2F2;
            --danger-100: #FEE2E2;
            --warning: #F59E0B;
            --warning-hover: #D97706;
            --warning-50: #FFFBEB;
            --warning-100: #FEF3C7;
            --info: #3B82F6;
            --info-50: #EFF6FF;
            --info-100: #DBEAFE;
            --success: #10B981;
            --success-50: #ECFDF5;
            --success-100: #D1FAE5;
            --border: #E5E7EB;
            --border-light: #F3F4F6;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius: 8px;
            --radius-md: 10px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 9999px;
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition: 200ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg-main);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            color: var(--text-body);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            line-height: 1.3;
            color: var(--text-heading);
            margin-bottom: 0.5rem;
        }

        p {
            margin-bottom: 1rem;
        }

        small,
        .small {
            font-size: 0.875rem;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: transform var(--transition-slow);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .sidebar-brand-lockup {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand-badge {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, #22C55E 0%, #15803D 100%);
            box-shadow: 0 8px 16px rgba(34, 197, 94, 0.3);
            flex-shrink: 0;
        }

        .sidebar-brand h6 {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.7rem;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-brand h5 {
            color: #FFFFFF;
            font-size: 1.05rem;
            margin: 0.15rem 0 0;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0.5rem 1rem 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.12) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 999px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.65);
            padding: 0.6rem 0.875rem;
            border-radius: var(--radius);
            margin: 0.125rem 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all var(--transition-fast);
            text-decoration: none;
            position: relative;
        }

        .sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: rgba(255, 255, 255, 0.95);
        }

        .sidebar .nav-link.active {
            background: var(--sidebar-active);
            color: #FFFFFF;
            font-weight: 600;
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: #22C55E;
            border-radius: 0 4px 4px 0;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            opacity: 0.85;
        }

        .sidebar .nav-section {
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.25rem 1rem 0.375rem;
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 0.75rem 0.5rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
        }

        .profile-mini {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0 0.5rem 0.5rem;
            padding: 0.75rem;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.06);
            transition: background var(--transition-fast);
        }

        .profile-mini:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .profile-mini img,
        .profile-mini .avatar-fallback {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .avatar-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--bg-card);
            padding: 0.875rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(8px);
        }

        .page-body {
            padding: 1.5rem;
            flex: 1;
        }

        /* CARDS */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            transition: box-shadow var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-light);
            padding: 1.125rem 1.5rem;
            border-radius: var(--radius-xl) var(--radius-xl) 0 0 !important;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-footer {
            background: var(--bg-hover);
            border-top: 1px solid var(--border-light);
            padding: 1rem 1.5rem;
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
        }

        /* BUTTONS */
        .btn {
            font-weight: 500;
            border-radius: var(--radius);
            transition: all var(--transition-fast);
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-spk {
            background: var(--primary);
            color: #FFFFFF;
            border: none;
            box-shadow: 0 1px 2px rgba(21, 128, 61, 0.1);
        }

        .btn-spk:hover {
            background: var(--primary-hover);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.25);
        }

        .btn-light-secondary {
            background: var(--bg-hover);
            color: var(--text-body);
            border: 1px solid var(--border);
        }

        .btn-light-secondary:hover {
            background: var(--border-light);
            color: var(--text-heading);
            border-color: var(--text-light);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary-200);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #FFFFFF;
        }

        .btn-danger {
            background: var(--danger);
            border-color: var(--danger);
            color: #FFFFFF;
        }

        .btn-danger:hover {
            background: var(--danger-hover);
            border-color: var(--danger-hover);
        }

        /* BADGES */
        .badge {
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: var(--radius-sm);
        }

        .badge-rank-1 {
            background: var(--warning);
            color: #FFFFFF;
        }

        .badge-rank-2 {
            background: var(--text-light);
            color: #FFFFFF;
        }

        .badge-rank-3 {
            background: #B45309;
            color: #FFFFFF;
        }

        /* FORMS */
        .form-control,
        .form-select {
            border-radius: var(--radius);
            border: 1.5px solid var(--border);
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
            background: var(--bg-card);
            color: var(--text-heading);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(21, 128, 61, 0.1);
            outline: none;
        }

        .form-control:hover,
        .form-select:hover {
            border-color: var(--text-light);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--text-heading);
            margin-bottom: 0.375rem;
        }

        .form-text {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* TABLES */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            font-weight: 700;
            background: var(--bg-hover);
            border-bottom: 1px solid var(--border);
            padding: 0.875rem 1.25rem;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-body);
        }

        .table tbody tr {
            transition: background var(--transition-fast);
        }

        .table tbody tr:hover {
            background: var(--primary-50);
        }

        .table tbody tr:nth-child(even) {
            background: var(--bg-hover);
        }

        .table tbody tr:nth-child(even):hover {
            background: var(--primary-50);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* BACK BUTTON */
        .global-back-button {
            position: fixed;
            top: 5.5rem;
            left: calc(260px + 1.5rem);
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            text-decoration: none;
            background: var(--bg-card);
            color: var(--text-heading);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            z-index: 900;
            transition: all var(--transition-fast);
        }

        .global-back-button:hover {
            background: var(--primary-50);
            color: var(--primary);
            border-color: var(--primary-200);
            transform: translateX(-2px);
        }

        .global-back-button i {
            font-size: 1rem;
        }

        /* PAGE HEADER */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-header h4 {
            font-weight: 800;
            margin: 0;
            color: var(--text-heading);
            letter-spacing: -0.02em;
            font-size: 1.5rem;
        }

        .page-header p {
            margin: 0.25rem 0 0;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* STAT PILL */
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-full);
            font-size: 0.8125rem;
            font-weight: 600;
            background: var(--primary-50);
            color: var(--primary);
            border: 1px solid var(--primary-200);
        }

        .stat-pill .stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-light);
        }

        /* DATA CARD */
        .data-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: var(--bg-card);
        }

        .data-card .card-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-light);
            padding: 1rem 1.5rem;
        }

        .data-card .card-header h6 {
            font-weight: 700;
            font-size: 0.9375rem;
            margin: 0;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .data-card .card-header h6 i {
            color: var(--primary);
        }

        .data-count {
            font-size: 0.8125rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ADMIN TABLE */
        .admin-table {
            margin: 0;
        }

        .admin-table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            font-weight: 700;
            background: var(--bg-hover);
            border-bottom: 1px solid var(--border);
            padding: 0.875rem 1.25rem;
            white-space: nowrap;
        }

        .admin-table tbody td {
            vertical-align: middle;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-light);
        }

        .admin-table tbody tr {
            transition: background var(--transition-fast);
        }

        .admin-table tbody tr:hover {
            background: var(--primary-50);
        }

        .admin-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* IMAGE CELL */
        .img-cell img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: var(--radius-lg);
            border: 2px solid var(--border-light);
        }

        .img-placeholder {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            background: var(--bg-hover);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 1.125rem;
        }

        /* NAME CELL */
        .nama-cell .nama-main {
            font-weight: 700;
            color: var(--text-heading);
            font-size: 0.875rem;
        }

        .nama-cell .nama-sub {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* CODE BADGE */
        .kode-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: var(--radius-sm);
            font-weight: 700;
            background: var(--primary-50);
            color: var(--primary);
            display: inline-block;
            margin-top: 4px;
        }

        /* INFO GRID */
        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-item {
            font-size: 0.8rem;
            color: var(--text-body);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-item i {
            color: var(--text-muted);
            font-size: 0.75rem;
            width: 14px;
        }

        .info-item strong {
            color: var(--text-heading);
            font-weight: 600;
        }

        /* HARGA BADGE */
        .harga-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0.375rem 0.875rem;
            border-radius: var(--radius-md);
            font-size: 0.8125rem;
            font-weight: 700;
            background: var(--primary-50);
            color: var(--primary);
            border: 1px solid var(--primary-200);
        }

        /* ACTION BUTTONS */
        .admin-table .btn-group {
            gap: 10px;
        }

        .btn-action {
            min-width: 44px;
            height: 38px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            font-size: 0.875rem;
        }

        .btn-edit {
            background: var(--primary);
            border: 1px solid var(--primary);
            color: #FFFFFF;
        }

        .btn-edit:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.25);
        }

        .btn-delete {
            background: var(--danger);
            border: 1px solid var(--danger);
            color: #FFFFFF;
        }

        .btn-delete:hover {
            background: var(--danger-hover, #dc2626);
            border-color: var(--danger-hover, #dc2626);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }

        /* EMPTY STATE */
        .empty-state {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .empty-state .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--bg-hover);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .empty-state .empty-icon i {
            font-size: 2rem;
            color: var(--text-muted);
        }

        .empty-state h6 {
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 0.25rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin: 0;
        }

        /* PAGINATION */
        .pagination-wrapper {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-hover);
        }

        /* FILTER CARD */
        .filter-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: var(--bg-card);
            margin-bottom: 1.5rem;
        }

        .filter-card .card-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-light);
            padding: 1rem 1.5rem;
        }

        .filter-card .card-header h6 {
            font-weight: 700;
            font-size: 0.9375rem;
            margin: 0;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-card .card-header h6 i {
            color: var(--primary);
        }

        .filter-card .card-body {
            padding: 1.25rem 1.5rem;
        }

        .filter-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-body);
            margin-bottom: 0.375rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .filter-input {
            border-radius: var(--radius);
            border: 1.5px solid var(--border);
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
            background: var(--bg-card);
        }

        .filter-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(21, 128, 61, 0.1);
        }

        .filter-input:hover {
            border-color: var(--text-light);
        }

        .btn-filter {
            padding: 0.625rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
        }

        .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.25);
        }

        .btn-reset-filter {
            padding: 0.625rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
        }

        /* STAT CARDS */
        .stat-card {
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
            border: none;
            box-shadow: var(--shadow-md);
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }

        .stat-card .stat-label {
            font-size: 0.8125rem;
            opacity: 0.85;
            font-weight: 500;
        }

        .stat-card-primary {
            background: linear-gradient(135deg, #15803D 0%, #22C55E 100%);
        }

        .stat-card-success {
            background: linear-gradient(135deg, #166534 0%, #15803D 100%);
        }

        .stat-card-warning {
            background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%);
        }

        .stat-card-danger {
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
        }

        .stat-card-info {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
        }

        /* MODAL */
        .modal-content {
            border: none;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-light);
            padding: 1.25rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-light);
            padding: 1rem 1.5rem;
        }

        .modal-backdrop.show {
            backdrop-filter: blur(4px);
        }

        /* CROP MODAL */
        .crop-modal .modal-dialog {
            max-width: 700px;
        }

        .crop-wrapper {
            position: relative;
            width: min(100%, 480px);
            margin-inline: auto;
            background: var(--bg-hover);
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 2px solid var(--border);
            cursor: crosshair;
            user-select: none;
            touch-action: none;
        }

        .crop-wrapper img.crop-source {
            display: block;
            width: 100%;
            height: auto;
            pointer-events: none;
        }

        .crop-selection {
            position: absolute;
            border: 2px solid var(--primary);
            background: rgba(21, 128, 61, 0.1);
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45);
            cursor: move;
            display: none;
        }

        .crop-selection.active {
            display: block;
        }

        .crop-selection .crop-handle {
            position: absolute;
            width: 12px;
            height: 12px;
            background: var(--primary);
            border: 2px solid #fff;
            border-radius: 50%;
        }

        .crop-selection .crop-handle.tl {
            top: -6px;
            left: -6px;
            cursor: nwse-resize;
        }

        .crop-selection .crop-handle.tr {
            top: -6px;
            right: -6px;
            cursor: nesw-resize;
        }

        .crop-selection .crop-handle.bl {
            bottom: -6px;
            left: -6px;
            cursor: nesw-resize;
        }

        .crop-selection .crop-handle.br {
            bottom: -6px;
            right: -6px;
            cursor: nwse-resize;
        }

        .crop-selection .crop-handle.tm {
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            cursor: ns-resize;
        }

        .crop-selection .crop-handle.bm {
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            cursor: ns-resize;
        }

        .crop-selection .crop-handle.ml {
            top: 50%;
            left: -6px;
            transform: translateY(-50%);
            cursor: ew-resize;
        }

        .crop-selection .crop-handle.mr {
            top: 50%;
            right: -6px;
            transform: translateY(-50%);
            cursor: ew-resize;
        }

        .crop-instructions {
            text-align: center;
            font-size: 0.8rem;
            color: var(--body-text);
            margin-top: 0.75rem;
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
            width: 140px;
            height: 140px;
            margin: 0 auto;
        }

        .avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-200);
            box-shadow: var(--shadow-lg);
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #fff;
            font-size: 3rem;
            font-weight: 700;
            border: 4px solid var(--primary-200);
            box-shadow: var(--shadow-lg);
        }

        .icon-box-lg {
            transition: transform var(--transition);
        }

        .modal:hover .icon-box-lg {
            transform: scale(1.05);
        }

        .thumb-placeholder {
            background: var(--bg-hover);
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* RESPONSIVE */
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

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header h4 {
                font-size: 1.25rem;
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

        /* Hide scrollbar globally */
        html {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        html::-webkit-scrollbar {
            display: none;
        }

        /* FOOTER */
        .app-footer {
            background: linear-gradient(135deg, #0f4c28 0%, #1a7a42 50%, #22a856 100%);
            color: #fff;
            margin-top: auto;
            padding: 0;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 3rem;
            flex-wrap: wrap;
        }

        .footer-brand {
            flex: 1;
            min-width: 280px;
            text-align: left;
        }

        .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #fff;
        }

        .footer-logo i {
            font-size: 1.75rem;
        }

        .footer-desc {
            color: rgba(255, 255, 255, .7);
            font-size: 0.9rem;
            line-height: 1.7;
            max-width: 360px;
            margin: 0;
        }

        .footer-links {
            display: flex;
            gap: 3rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .footer-col h6 {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: rgba(255, 255, 255, .5);
            margin-bottom: 1rem;
        }

        .footer-col a {
            display: flex;
            align-items: center;
            gap: 6px;
            color: rgba(255, 255, 255, .85);
            text-decoration: none;
            font-size: 0.9rem;
            padding: 4px 0;
            transition: color 0.2s, padding-left 0.2s;
        }

        .footer-col a:hover {
            color: #fff;
            padding-left: 4px;
        }

        .footer-col a i {
            font-size: 0.95rem;
            opacity: 0.8;
        }

        .footer-col-contact a {
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                gap: 2rem;
                padding: 2rem 1.5rem 1.5rem;
                text-align: center;
            }

            .footer-brand {
                text-align: center;
            }

            .footer-desc {
                margin: 0 auto;
            }

            .footer-links {
                justify-content: center;
                gap: 2rem;
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
    $mainMenuRoutes = [
    'admin.dashboard',
    'admin.penyakit.index',
    'admin.gejala.index',
    'admin.pupuk.index',
    'admin.pestisida.index',
    'admin.kriteria.index',
    'admin.nilai-cf.pupuk',
    'admin.nilai-cf.pestisida',
    'admin.pengguna.index',
    'admin.riwayat.index',
    'admin.profile.edit',
    'user.dashboard',
    'user.diagnosis.index',
    'user.riwayat.index',
    'user.profile.edit',
    ];
    $hideGlobalBack = request()->routeIs($mainMenuRoutes);
    @endphp
    @auth
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-lockup">
                <div>
                    <h5 style="margin:0; line-height:1.2;">PadiCare <span style="color:#4ADE80;">Lombok</span></h5>
                    <small style="color:rgba(255,255,255,.45);font-size:.68rem;">Sistem Pakar Penyakit &
                        Rekomendasi</small>
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
                    <i class="bi bi-sliders"></i> Kelola Kriteria CF
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
                    <i class="bi bi-clock-history"></i> Riwayat Pengguna
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
                    <i class="bi bi-house-door"></i> Beranda
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
                    <div class="fw-semibold" style="font-size:0.825rem;">{{ auth()->user()->nama }}</div>
                    <div style="opacity:.6;font-size:0.7rem;">{{ auth()->user()->isAdmin() ? 'Admin' : 'Petani' }}</div>
                </div>
            </div>
            <button type="button" class="nav-link w-100 text-start border-0 bg-transparent" data-bs-toggle="modal"
                data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
            <noscript>
                <a href="{{ route('logout.get') }}" class="nav-link">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </noscript>
        </div>
    </div>

    <!-- Modal Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box-lg rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px; background: var(--danger-50);">
                            <i class="bi bi-box-arrow-right text-danger fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="logoutModalLabel">Akhiri Sesi</h5>
                            <small class="text-muted">Konfirmasi keluar dari akun Anda</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <p class="mb-0" style="color: var(--text-body);">Apakah Anda yakin ingin keluar dari sesi ini? Anda
                        harus login kembali untuk mengakses aplikasi.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Batalkan
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-box-arrow-right me-2"></i>Akhiri Sesi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-semibold" style="color: var(--text-muted); font-size: 0.875rem;">@yield('page-title',
                    'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge"
                    style="background: var(--primary-50); color: var(--primary); border: 1px solid var(--primary-200); font-weight: 600;">{{ auth()->user()->role === 'admin' ? 'Admin' : 'Petani' }}</span>
                <span class="fw-semibold"
                    style="font-size: 0.875rem; color: var(--text-heading);">{{ auth()->user()->nama }}</span>
            </div>
        </div>
        <div class="page-body">
            @yield('content')
        </div>

        {{-- Footer (Admin/Petani) --}}
        <footer class="app-footer">
            <div class="footer-container">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <i class="bi bi-flower2"></i>
                        <span>PadiCare</span>
                    </div>
                    <p class="footer-desc">Sistem pakar diagnosis penyakit padi dan rekomendasi penanganan untuk petani
                        di Lombok.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-col">
                        <h6>Navigasi</h6>
                        <a href="{{ route('user.dashboard') }}">Beranda</a>
                        <a href="{{ route('user.diagnosis.index') }}">Diagnosis</a>
                    </div>
                    <div class="footer-col">
                        <h6>Informasi</h6>
                        <a href="{{ route('pages.tentang') }}">Tentang Kami</a>
                        <a href="{{ route('pages.bantuan') }}">Bantuan</a>
                    </div>
                    <div class="footer-col footer-col-contact">
                        <h6>Kontak</h6>
                        <a href="mailto:info@padicare.id"><i class="bi bi-envelope-fill"></i> info@padicare.id</a>
                        <a href="https://wa.me/6281234567890" target="_blank"><i class="bi bi-whatsapp"></i> +62
                            812-3456-7890</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- Bottom Navigation (Mobile/Tablet only, hidden on desktop) --}}
    <nav class="bottom-nav" id="bottomNav" aria-label="Navigasi mobile">
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}"
            class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-route="dashboard">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.penyakit.index') }}"
            class="bottom-nav-item {{ request()->routeIs('admin.penyakit*') ? 'active' : '' }}" data-route="penyakit">
            <i class="bi bi-virus"></i>
            <span>Penyakit</span>
        </a>
        <a href="{{ route('admin.gejala.index') }}"
            class="bottom-nav-item {{ request()->routeIs('admin.gejala*') ? 'active' : '' }}" data-route="gejala">
            <i class="bi bi-clipboard2-pulse"></i>
            <span>Gejala</span>
        </a>
        <a href="{{ route('admin.riwayat.index') }}"
            class="bottom-nav-item {{ request()->routeIs('admin.riwayat*') ? 'active' : '' }}" data-route="riwayat">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('admin.profile.edit') }}"
            class="bottom-nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" data-route="profil">
            <i class="bi bi-person-gear"></i>
            <span>Profil</span>
        </a>
        @else
        <a href="{{ route('user.dashboard') }}"
            class="bottom-nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" data-route="beranda">
            <i class="bi bi-house-door"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('user.diagnosis.index') }}"
            class="bottom-nav-item {{ request()->routeIs('user.diagnosis*') ? 'active' : '' }}" data-route="diagnosis">
            <i class="bi bi-search-heart"></i>
            <span>Diagnosis</span>
        </a>
        <a href="{{ route('user.riwayat.index') }}"
            class="bottom-nav-item {{ request()->routeIs('user.riwayat*') ? 'active' : '' }}" data-route="riwayat">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('user.profile.edit') }}"
            class="bottom-nav-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}" data-route="profil">
            <i class="bi bi-person-gear"></i>
            <span>Profil</span>
        </a>
        @endif
    </nav>
    @else
    @yield('content')

    {{-- Footer (Guest) --}}
    <footer class="app-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-logo">
                    <i class="bi bi-flower2"></i>
                    <span>PadiCare</span>
                </div>
                <p class="footer-desc">Sistem pakar diagnosis penyakit padi dan rekomendasi penanganan untuk petani di
                    Lombok.</p>
            </div>
            <div class="footer-links">
                <div class="footer-col">
                    <h6>Navigasi</h6>
                    <a href="{{ route('user.dashboard') }}">Beranda</a>
                    <a href="{{ route('user.diagnosis.index') }}">Diagnosis</a>
                </div>
                <div class="footer-col">
                    <h6>Informasi</h6>
                    <a href="{{ route('pages.tentang') }}">Tentang Kami</a>
                    <a href="{{ route('pages.bantuan') }}">Bantuan</a>
                </div>
                <div class="footer-col footer-col-contact">
                    <h6>Kontak</h6>
                    <a href="mailto:info@padicare.id"><i class="bi bi-envelope-fill"></i> info@padicare.id</a>
                    <a href="https://wa.me/6281234567890" target="_blank"><i class="bi bi-whatsapp"></i> +62
                        812-3456-7890</a>
                </div>
            </div>
        </div>
    </footer>
    @endauth

    @if (!$hideGlobalBack)
    <a href="{{ $globalBackUrl }}" class="global-back-button" aria-label="Kembali" title="Kembali"
        data-fallback-url="{{ $fallbackBackUrl }}">
        <i class="bi bi-arrow-left"></i>
    </a>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>
    @vite(['resources/js/app.js'])

    <!-- Custom Toast Notification Container -->
    <div id="toastContainer"
        style="position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 12px; max-width: 400px; pointer-events: none;">
    </div>

    <style>
        .custom-toast {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            animation: slideInRight 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .custom-toast.hiding {
            animation: slideOutRight 0.3s ease-in forwards;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .custom-toast.toast-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .custom-toast.toast-info {
            background: #dbeafe;
            border: 1px solid #bfdbfe;
        }

        .custom-toast.toast-warning {
            background: #fef3c7;
            border: 1px solid #fde68a;
        }

        .custom-toast.toast-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        .custom-toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .toast-success .custom-toast-icon {
            background: #22c55e;
            color: #fff;
        }

        .toast-info .custom-toast-icon {
            background: #3b82f6;
            color: #fff;
        }

        .toast-warning .custom-toast-icon {
            background: #f59e0b;
            color: #fff;
        }

        .toast-error .custom-toast-icon {
            background: #ef4444;
            color: #fff;
        }

        .custom-toast-icon i {
            font-size: 12px;
        }

        .custom-toast-content {
            flex: 1;
            min-width: 0;
        }

        .custom-toast-title {
            font-weight: 700;
            font-size: 0.875rem;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .toast-success .custom-toast-title {
            color: #166534;
        }

        .toast-info .custom-toast-title {
            color: #1e40af;
        }

        .toast-warning .custom-toast-title {
            color: #92400e;
        }

        .toast-error .custom-toast-title {
            color: #991b1b;
        }

        .custom-toast-message {
            font-size: 0.8rem;
            line-height: 1.4;
            opacity: 0.85;
        }

        .toast-success .custom-toast-message {
            color: #166534;
        }

        .toast-info .custom-toast-message {
            color: #1e40af;
        }

        .toast-warning .custom-toast-message {
            color: #92400e;
        }

        .toast-error .custom-toast-message {
            color: #991b1b;
        }

        .custom-toast-close {
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
            flex-shrink: 0;
            color: inherit;
        }

        .custom-toast-close:hover {
            opacity: 1;
        }

        .custom-toast-close i {
            font-size: 16px;
        }
    </style>

    <script>
        // Custom Toast Notification System
        window.showToast = function(type, title, message, duration = 5000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const icons = {
                success: 'bi-check-lg',
                info: 'bi-info-lg',
                warning: 'bi-exclamation-lg',
                error: 'bi-x-lg'
            };

            const toast = document.createElement('div');
            toast.className = `custom-toast toast-${type}`;
            toast.innerHTML = `
            <div class="custom-toast-icon"><i class="bi ${icons[type] || 'bi-info-lg'}"></i></div>
            <div class="custom-toast-content">
                <div class="custom-toast-title">${title}</div>
                ${message ? `<div class="custom-toast-message">${message}</div>` : ''}
            </div>
            <button class="custom-toast-close" onclick="this.parentElement.classList.add('hiding'); setTimeout(() => this.parentElement.remove(), 300);">
                <i class="bi bi-x"></i>
            </button>
        `;

            container.appendChild(toast);

            if (duration > 0) {
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.classList.add('hiding');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, duration);
            }
        };

        window.showSuccessToast = function(title, message) {
            window.showToast('success', title, message || '');
        };

        window.showInfoToast = function(title, message) {
            window.showToast('info', title, message || '');
        };

        window.showWarningToast = function(title, message) {
            window.showToast('warning', title, message || '');
        };

        window.showErrorToast = function(title, message) {
            window.showToast('error', title, message || '');
        };

        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
            window.showSuccessToast('Success', "{{ session('success') }}");
            @endif
            @if(session('info'))
            window.showInfoToast('Info', "{{ session('info') }}");
            @endif
            @if(session('warning'))
            window.showWarningToast('Warning', "{{ session('warning') }}");
            @endif
            @if(session('error'))
            window.showErrorToast('Error', "{{ session('error') }}");
            @endif

            // Remove old alert elements
            document.querySelectorAll('.alert').forEach(el => el.remove());

            const bb = document.querySelector('.global-back-button');
            if (!bb) return;
            if (document.body.classList.contains('guest-layout')) {
                bb.style.top = '1rem';
                bb.style.left = '1rem';
            }
            bb.addEventListener('click', (e) => {
                const fb = bb.dataset.fallbackUrl;
                try {
                    if (document.referrer && document.referrer !== window.location.href && new URL(document
                            .referrer).origin === window.location.origin) {
                        e.preventDefault();
                        window.history.back();
                        return;
                    }
                } catch (err) {}
                if (!bb.getAttribute('href')) {
                    e.preventDefault();
                    window.location.href = fb;
                }
            });
        });
    </script>

    <!-- Modal Crop -->
    <div class="modal fade crop-modal" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box-lg rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px; background: var(--primary-50);">
                            <i class="bi bi-crop text-success fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="cropModalLabel">Sesuaikan Foto Profil</h5>
                            <small class="text-muted">Pilih area foto yang ingin digunakan</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <div class="crop-wrapper" id="cropWrapper">
                        <img src="" alt="Preview" class="crop-source" id="cropSource">
                        <div class="crop-selection" id="cropSelection">
                            <div class="crop-handle tl" data-handle="tl"></div>
                            <div class="crop-handle tr" data-handle="tr"></div>
                            <div class="crop-handle bl" data-handle="bl"></div>
                            <div class="crop-handle br" data-handle="br"></div>
                            <div class="crop-handle tm" data-handle="tm"></div>
                            <div class="crop-handle bm" data-handle="bm"></div>
                            <div class="crop-handle ml" data-handle="ml"></div>
                            <div class="crop-handle mr" data-handle="mr"></div>
                        </div>
                    </div>
                    <div class="crop-instructions">
                        <i class="bi bi-hand-index me-1"></i>Klik dan seret pada gambar untuk memilih area crop
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <div class="avatar-preview-container">
                                <img src="" alt="Avatar Preview" class="avatar-preview" id="avatarPreview">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <div class="crop-controls w-100">
                        <button type="button" class="btn btn-light-secondary crop-btn" data-bs-dismiss="modal"><i
                                class="bi bi-x-lg me-2"></i>Batal</button>
                        <button type="button" class="btn btn-outline-primary crop-btn" id="resetCropBtn"><i
                                class="bi bi-arrow-counterclockwise me-2"></i>Reset</button>
                        <button type="button" class="btn btn-spk crop-btn" id="saveCropBtn"><i
                                class="bi bi-check-lg me-2"></i>Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>