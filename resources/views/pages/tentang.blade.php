@extends('layouts.app')

@section('title', 'Tentang Kami')
@section('page-title', 'Tentang PadiCare')

@push('styles')
<style>
    .about-hero {
        background: linear-gradient(135deg, #0f4c28 0%, #1a7a42 50%, #22a856 100%);
        color: #fff;
        border-radius: var(--radius-xl);
        padding: 3rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .about-hero h1 {
        font-size: 2.25rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .about-hero p {
        color: rgba(255, 255, 255, .8);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .about-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .about-stat-card {
        background: rgba(255, 255, 255, .15);
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        text-align: center;
    }

    .about-stat-card .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #fff;
    }

    .about-stat-card .stat-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, .7);
        margin-top: 0.25rem;
    }

    .about-section {
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        margin-bottom: 2rem;
    }

    .about-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--heading);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .about-section h2 i {
        color: var(--primary);
    }

    .about-section p {
        color: var(--body-text);
        line-height: 1.8;
        margin-bottom: 1rem;
    }

    .about-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .about-feature-card {
        background: var(--main-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .about-feature-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .about-feature-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        background: var(--primary-50);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .about-feature-card h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--heading);
        margin-bottom: 0.5rem;
    }

    .about-feature-card p {
        font-size: 0.9rem;
        color: var(--muted-text);
        margin: 0;
        line-height: 1.6;
    }

    .about-team {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        background: var(--main-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        margin-top: 1rem;
    }

    .about-team-info h4 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--heading);
        margin-bottom: 0.25rem;
    }

    .about-team-info p {
        font-size: 0.9rem;
        color: var(--muted-text);
        margin: 0;
    }

    @media (max-width: 768px) {
        .about-hero {
            padding: 2rem 1.5rem;
        }

        .about-hero h1 {
            font-size: 1.75rem;
        }

        .about-section {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- Hero Section --}}
    <div class="about-hero anim-fade-up">
        <h1>Tentang PadiCare Lombok</h1>
        <p>Sistem pakar berbasis teknologi yang dirancang khusus untuk membantu petani mengidentifikasi penyakit padi
            dan mendapatkan rekomendasi penanganan yang tepat sasaran.</p>
        <div class="about-stats">
            <div class="about-stat-card">
                <div class="stat-number">{{ $stats['penyakit'] }}+</div>
                <div class="stat-label">Penyakit Terdata</div>
            </div>
            <div class="about-stat-card">
                <div class="stat-number">{{ $stats['gejala'] }}+</div>
                <div class="stat-label">Gejala Tersedia</div>
            </div>
            <div class="about-stat-card">
                <div class="stat-number">{{ $stats['riwayat'] }}+</div>
                <div class="stat-label">Kasus Terdokumentasi</div>
            </div>
        </div>
    </div>

    {{-- Visi & Misi --}}
    <div class="about-section anim-fade-up">
        <h2><i class="bi bi-bullseye"></i>Visi & Misi</h2>
        <p><strong>Visi:</strong> Menjadi solusi teknologi terdepan dalam membantu petani padi di Lombok meningkatkan
            produktivitas dan mengurangi kerugian akibat penyakit tanaman.</p>
        <p><strong>Misi:</strong></p>
        <ul style="color: var(--body-text); line-height: 1.8; padding-left: 1.5rem;">
            <li>Menyediakan sistem diagnosis penyakit padi yang akurat dan mudah digunakan.</li>
            <li>Memberikan rekomendasi pupuk dan pestisida yang tepat berdasarkan jenis penyakit.</li>
            <li>Membantu petani membuat keputusan berbasis data untuk pengelolaan lahan yang lebih baik.</li>
            <li>Mendukung ketahanan pangan melalui teknologi pertanian modern.</li>
        </ul>
    </div>

    {{-- Fitur Utama --}}
    <div class="about-section anim-fade-up">
        <h2><i class="bi bi-stars"></i>Fitur Utama</h2>
        <div class="about-features">
            <div class="about-feature-card">
                <div class="about-feature-icon"><i class="bi bi-search-heart"></i></div>
                <h3>Diagnosis Penyakit Tanaman Padi</h3>
                <p>Identifikasi penyakit berdasarkan gejala yang terlihat di lapangan menggunakan basis data pakar.</p>
            </div>
            <div class="about-feature-card">
                <div class="about-feature-icon"><i class="bi bi-droplet-fill"></i></div>
                <h3>Rekomendasi Pupuk & Pestisida</h3>
                <p>Dapatkan saran pupuk dan pestisida yang tepat berdasarkan jenis penyakit dan kondisi lahan Anda.</p>
            </div>
            <div class="about-feature-card">
                <div class="about-feature-icon"><i class="bi bi-clock-history"></i></div>
                <h3>Riwayat & Referensi Kasus</h3>
                <p>Simpan dan bandingkan hasil diagnosis untuk memantau kesehatan lahan secara berkala.</p>
            </div>
            <div class="about-feature-card">
                <div class="about-feature-icon"><i class="bi bi-calculator"></i></div>
                <h3>Kalkulator Kebutuhan Lahan</h3>
                <p>Hitung estimasi kebutuhan pupuk dan pestisida berdasarkan luas lahan Anda.</p>
            </div>
        </div>
    </div>

    {{-- Teknologi --}}
    <div class="about-section anim-fade-up">
        <h2><i class="bi bi-cpu"></i>Teknologi yang Digunakan</h2>
        <p>PadiCare menggunakan metode <strong>Certainty Factor (CF)</strong> untuk menghitung tingkat keyakinan
            diagnosis penyakit. Sistem ini menggabungkan pengetahuan pakar pertanian dengan algoritma kecerdasan buatan
            untuk memberikan hasil yang akurat dan dapat diandalkan.</p>
        <p>Database penyakit dan gejala dikembangkan berdasarkan penelitian dan konsultasi dengan ahli pertanian di
            Lombok, memastikan informasi yang disajikan relevan dengan kondisi lokal.</p>
    </div>

    {{-- Tim Pengembang --}}
    <div class="about-section anim-fade-up">
        <h2><i class="bi bi-people-fill"></i>Tim Pengembang</h2>
        <p>PadiCare dikembangkan sebagai bagian dari penelitian akademis untuk mendukung petani lokal di Lombok. Sistem
            ini terus dikembangkan dan diperbarui berdasarkan masukan dari pengguna dan ahli pertanian.</p>
        <div class="about-team">
            <div class="about-team-info">
                <h4>Tim Penelitian PadiCare</h4>
                <p>Universitas Teknologi Mataram, Indonesia</p>
            </div>
        </div>
    </div>

    {{-- Kontak --}}
    <div class="about-section anim-fade-up">
        <h2><i class="bi bi-envelope-fill"></i>Hubungi Kami</h2>
        <p>Jika Anda memiliki pertanyaan, saran, atau ingin berkolaborasi, jangan ragu untuk menghubungi kami melalui:
        </p>
        <ul style="color: var(--body-text); line-height: 2; padding-left: 1.5rem;">
            <li><i class="bi bi-envelope me-2 text-primary"></i>Email: <strong>info@padicare.id</strong></li>
            <li><i class="bi bi-whatsapp me-2 text-success"></i>WhatsApp: <strong>+62 819-9112-3632</strong></li>
            <li><i class="bi bi-geo-alt me-2 text-danger"></i>Lokasi: <strong>Lombok, Nusa Tenggara Barat</strong></li>
        </ul>
    </div>
</div>
@endsection