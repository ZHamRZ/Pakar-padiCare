@extends('layouts.app')

@section('title', 'Bantuan')
@section('page-title', 'Pusat Bantuan')

@push('styles')
<style>
    .help-hero {
        background: linear-gradient(135deg, #0f4c28 0%, #1a7a42 50%, #22a856 100%);
        color: #fff;
        border-radius: var(--radius-xl);
        padding: 3rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    .help-hero h1 {
        font-size: 2.25rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }
    .help-hero p {
        color: rgba(255,255,255,.8);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }
    .help-search-box {
        max-width: 500px;
        margin: 1.5rem auto 0;
        position: relative;
    }
    .help-search-box input {
        width: 100%;
        padding: 14px 20px 14px 48px;
        border: none;
        border-radius: var(--radius-lg);
        font-size: 1rem;
        background: rgba(255,255,255,.2);
        color: #fff;
        backdrop-filter: blur(10px);
    }
    .help-search-box input::placeholder {
        color: rgba(255,255,255,.6);
    }
    .help-search-box i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,.6);
        font-size: 1.1rem;
    }
    .help-section {
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        margin-bottom: 2rem;
    }
    .help-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--heading);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .help-section h2 i {
        color: var(--primary);
    }
    .faq-item {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        overflow: hidden;
        transition: border-color 0.2s;
    }
    .faq-item:hover {
        border-color: var(--primary);
    }
    .faq-question {
        padding: 1.25rem 1.5rem;
        background: var(--main-bg);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: var(--heading);
        transition: background 0.2s;
    }
    .faq-question:hover {
        background: var(--primary-50);
    }
    .faq-question i {
        transition: transform 0.2s;
        color: var(--primary);
    }
    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }
    .faq-answer {
        padding: 0 1.5rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }
    .faq-item.active .faq-answer {
        padding: 1.25rem 1.5rem;
        max-height: 500px;
    }
    .faq-answer p {
        color: var(--body-text);
        line-height: 1.7;
        margin: 0;
    }
    .help-steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }
    .help-step {
        display: flex;
        gap: 1rem;
        padding: 1.5rem;
        background: var(--main-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
    }
    .help-step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .help-step h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--heading);
        margin-bottom: 0.25rem;
    }
    .help-step p {
        font-size: 0.85rem;
        color: var(--muted-text);
        margin: 0;
        line-height: 1.5;
    }
    .help-contact-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .help-contact-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--main-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .help-contact-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .help-contact-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .help-contact-icon.email { background: #dbeafe; color: #2563eb; }
    .help-contact-icon.whatsapp { background: #dcfce7; color: #16a34a; }
    .help-contact-icon.location { background: #fee2e2; color: #dc2626; }
    .help-contact-card h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--heading);
        margin-bottom: 0.15rem;
    }
    .help-contact-card p {
        font-size: 0.8rem;
        color: var(--muted-text);
        margin: 0;
    }
    @media (max-width: 768px) {
        .help-hero { padding: 2rem 1.5rem; }
        .help-hero h1 { font-size: 1.75rem; }
        .help-section { padding: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- Hero Section --}}
    <div class="help-hero anim-fade-up">
        <h1><i class="bi bi-life-preserver me-2"></i>Pusat Bantuan</h1>
        <p>Temukan jawaban atas pertanyaan umum Anda tentang PadiCare atau hubungi tim kami untuk bantuan lebih lanjut.</p>
        <div class="help-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="faqSearch" placeholder="Cari pertanyaan...">
        </div>
    </div>

    {{-- Panduan Penggunaan --}}
    <div class="help-section anim-fade-up">
        <h2><i class="bi bi-book"></i>Panduan Penggunaan</h2>
        <div class="help-steps">
            <div class="help-step">
                <div class="help-step-number">1</div>
                <div>
                    <h4>Daftar / Login</h4>
                    <p>Buat akun gratis atau masuk untuk menyimpan riwayat diagnosis Anda.</p>
                </div>
            </div>
            <div class="help-step">
                <div class="help-step-number">2</div>
                <div>
                    <h4>Pilih Gejala</h4>
                    <p>Centang gejala yang terlihat pada tanaman padi Anda di halaman diagnosis.</p>
                </div>
            </div>
            <div class="help-step">
                <div class="help-step-number">3</div>
                <div>
                    <h4>Proses Diagnosis</h4>
                    <p>Klik "Identifikasi Penyakit" untuk mendapatkan hasil analisis sistem pakar.</p>
                </div>
            </div>
            <div class="help-step">
                <div class="help-step-number">4</div>
                <div>
                    <h4>Lihat Rekomendasi</h4>
                    <p>Dapatkan saran pupuk, pestisida, dan estimasi biaya untuk lahan Anda.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="help-section anim-fade-up">
        <h2><i class="bi bi-question-circle"></i>Pertanyaan Umum (FAQ)</h2>
        
        <div class="faq-item" data-faq>
            <div class="faq-question">
                <span>Apa itu PadiCare?</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>PadiCare adalah sistem pakar berbasis web yang membantu petani padi di Lombok mengidentifikasi penyakit tanaman berdasarkan gejala yang diamati. Sistem ini menggunakan metode Certainty Factor (CF) untuk memberikan diagnosis dan rekomendasi penanganan yang akurat.</p>
            </div>
        </div>

        <div class="faq-item" data-faq>
            <div class="faq-question">
                <span>Apakah PadiCare gratis?</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Ya, PadiCare sepenuhnya gratis untuk digunakan. Anda bisa melakukan diagnosis tanpa login, namun disarankan untuk mendaftar agar bisa menyimpan riwayat diagnosis dan melihat rekomendasi yang lebih personal.</p>
            </div>
        </div>

        <div class="faq-item" data-faq>
            <div class="faq-question">
                <span>Bagaimana cara melakukan diagnosis?</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Masuk ke halaman "Diagnosis Penyakit", pilih gejala yang terlihat pada tanaman Anda, lalu klik tombol "Identifikasi Penyakit". Sistem akan menganalisis gejala tersebut dan memberikan hasil diagnosis beserta rekomendasi penanganan.</p>
            </div>
        </div>

        <div class="faq-item" data-faq>
            <div class="faq-question">
                <span>Apa itu Certainty Factor (CF)?</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Certainty Factor adalah metode dalam sistem pakar untuk mengukur tingkat keyakinan suatu diagnosis. Nilai CF berkisar antara -1 (pasti salah) hingga +1 (pasti benar). Semakin tinggi nilai CF, semakin yakin sistem terhadap diagnosis yang diberikan.</p>
            </div>
        </div>

        <div class="faq-item" data-faq>
            <div class="faq-question">
                <span>Bagaimana cara menghitung kebutuhan pupuk?</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Setelah mendapatkan hasil diagnosis, masukkan luas lahan Anda (dalam meter persegi) pada halaman rekomendasi. Sistem akan otomatis menghitung estimasi kebutuhan pupuk dan pestisida beserta biaya yang diperlukan untuk 1 kali aplikasi.</p>
            </div>
        </div>

        <div class="faq-item" data-faq>
            <div class="faq-question">
                <span>Apakah hasil diagnosis bisa disimpan?</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Ya, jika Anda sudah login sebagai petani, hasil diagnosis akan otomatis tersimpan di menu "Riwayat Saya". Anda bisa melihat kembali, mencetak, atau membandingkan hasil diagnosis sebelumnya.</p>
            </div>
        </div>

        <div class="faq-item" data-faq>
            <div class="faq-question">
                <span>Bagaimana jika hasil diagnosis tidak sesuai?</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Sistem pakar memberikan rekomendasi berdasarkan data yang ada. Jika hasil tidak sesuai dengan kondisi lapangan, kami sarankan untuk berkonsultasi langsung dengan penyuluh pertanian atau ahli tanaman di daerah Anda. Anda juga bisa menghubungi kami untuk memberikan masukan.</p>
            </div>
        </div>
    </div>

    {{-- Kontak --}}
    <div class="help-section anim-fade-up">
        <h2><i class="bi bi-headset"></i>Butuh Bantuan Lebih Lanjut?</h2>
        <p style="color: var(--body-text); margin-bottom: 1.5rem;">Jika pertanyaan Anda tidak terjawab di atas, silakan hubungi tim kami melalui:</p>
        <div class="help-contact-cards">
            <a href="mailto:info@padicare.id" class="help-contact-card">
                <div class="help-contact-icon email"><i class="bi bi-envelope-fill"></i></div>
                <div>
                    <h4>Email</h4>
                    <p>info@padicare.id</p>
                </div>
            </a>
            <a href="https://wa.me/6281234567890" target="_blank" class="help-contact-card">
                <div class="help-contact-icon whatsapp"><i class="bi bi-whatsapp"></i></div>
                <div>
                    <h4>WhatsApp</h4>
                    <p>+62 812-3456-7890</p>
                </div>
            </a>
            <div class="help-contact-card">
                <div class="help-contact-icon location"><i class="bi bi-geo-alt-fill"></i></div>
                <div>
                    <h4>Lokasi</h4>
                    <p>Lombok, NTB</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // FAQ Accordion
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentElement;
            const isActive = item.classList.contains('active');
            
            // Close all
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            
            // Toggle current
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // FAQ Search
    const searchInput = document.getElementById('faqSearch');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.faq-item').forEach(item => {
                const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
                const visible = question.includes(query) || answer.includes(query);
                item.style.display = visible ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
