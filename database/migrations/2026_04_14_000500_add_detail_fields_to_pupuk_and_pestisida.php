<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->text('kandungan_detail')->nullable()->after('kandungan');
            $table->text('takaran')->nullable()->after('fungsi_utama');
            $table->text('efek_penggunaan')->nullable()->after('takaran');
            $table->text('cara_aplikasi')->nullable()->after('efek_penggunaan');
            $table->text('jadwal_umur_aplikasi')->nullable()->after('cara_aplikasi');
            $table->text('frekuensi_aplikasi')->nullable()->after('jadwal_umur_aplikasi');
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->text('kandungan_detail')->nullable()->after('bahan_aktif');
            $table->text('fungsi')->nullable()->after('kandungan_detail');
            $table->text('takaran')->nullable()->after('fungsi');
            $table->text('efek_penggunaan')->nullable()->after('takaran');
            $table->text('cara_aplikasi')->nullable()->after('efek_penggunaan');
            $table->text('jadwal_umur_aplikasi')->nullable()->after('cara_aplikasi');
            $table->text('frekuensi_aplikasi')->nullable()->after('jadwal_umur_aplikasi');
        });

        $this->seedDetailPestisida();
        $this->seedDetailPupuk();
    }

    public function down(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->dropColumn([
                'kandungan_detail',
                'takaran',
                'efek_penggunaan',
                'cara_aplikasi',
                'jadwal_umur_aplikasi',
                'frekuensi_aplikasi',
            ]);
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->dropColumn([
                'kandungan_detail',
                'fungsi',
                'takaran',
                'efek_penggunaan',
                'cara_aplikasi',
                'jadwal_umur_aplikasi',
                'frekuensi_aplikasi',
            ]);
        });
    }

    private function seedDetailPestisida(): void
    {
        $items = [
            'Amistar Top' => [
                'bahan_aktif' => 'Azoksistrobin + Difenokonazol',
                'kandungan_detail' => 'Azoksistrobin 200 g/l dan Difenokonazol 125 g/l.',
                'fungsi' => 'Mengendalikan penyakit jamur spektrum luas seperti bercak daun, busuk phytophthora, antraknosa/patek, dan blas padi.',
                'takaran' => '0,5 - 1 ml per liter air.',
                'efek_penggunaan' => 'Tanaman terbebas dari jamur serta memiliki efek fitotonik yang membuat daun lebih hijau, tegar, segar, dan memperpanjang masa produktif tanaman.',
                'cara_aplikasi' => 'Disemprotkan secara merata pada bagian daun dan batang tanaman.',
                'jadwal_umur_aplikasi' => '40-45 HST (fase bunting) dan 60-65 HST (saat malai keluar 70%).',
                'frekuensi_aplikasi' => '1 - 2 kali per musim tanam dengan jeda sekitar 2 minggu.',
            ],
            'Filia 525 SE' => [
                'bahan_aktif' => 'Propikonazol + Trisiklazol',
                'kandungan_detail' => 'Propikonazol 125 g/l dan Trisiklazol 400 g/l.',
                'fungsi' => 'Sangat spesifik dan kuat untuk mengendalikan penyakit blas pada padi, hawar pelepah, dan penyakit bulir kusam.',
                'takaran' => '1 - 1,5 ml per liter air.',
                'efek_penggunaan' => 'Menghentikan penyebaran jamur dengan cepat secara sistemik serta membuat bulir padi lebih bersih, bernas, dan mengkilap.',
                'cara_aplikasi' => 'Penyemprotan volume tinggi, terutama pada fase bunting dan saat malai padi mulai keluar.',
                'jadwal_umur_aplikasi' => '30 HST, 40 HST, dan 60 HST pada fase rawan serangan blas.',
                'frekuensi_aplikasi' => '2 - 3 kali per musim tanam; preventif 2 minggu sekali, kuratif 1 minggu sekali.',
            ],
            'Bactocyn' => [
                'bahan_aktif' => 'Oksitetrasiklin',
                'kandungan_detail' => 'Oksitetrasiklin, golongan antibiotik pertanian.',
                'fungsi' => 'Membasmi bakteri patogen penyebab layu bakteri, hawar daun bakteri (kresek pada padi), dan busuk batang bakteri.',
                'takaran' => '1 - 2 ml atau gram per liter air, tergantung formulasi.',
                'efek_penggunaan' => 'Kerusakan tanaman akibat infeksi bakteri berhenti dan penularan ke tanaman sehat dapat dicegah.',
                'cara_aplikasi' => 'Disemprotkan ke seluruh bagian tanaman atau dikocorkan ke area perakaran bila targetnya penyakit layu bakteri.',
                'jadwal_umur_aplikasi' => '20 HST untuk preventif atau kapan saja saat gejala kresek atau bakteri mulai muncul.',
                'frekuensi_aplikasi' => 'Interval 4-7 hari saat ada serangan, atau 2 minggu sekali untuk pencegahan.',
            ],
            'Agrept 20 WP' => [
                'bahan_aktif' => 'Streptomisin sulfat',
                'kandungan_detail' => 'Streptomisin sulfat 20%.',
                'fungsi' => 'Bakterisida sistemik untuk mengendalikan penyakit layu bakteri, bercak daun bakteri, dan hawar daun.',
                'takaran' => '1 - 2 gram per liter air.',
                'efek_penggunaan' => 'Bakteri patogen di jaringan tanaman akan mati secara sistemik dan tanaman layu dapat berangsur pulih jika infeksi belum parah.',
                'cara_aplikasi' => 'Disemprotkan pada daun atau dikocorkan pada pangkal batang dan akar.',
                'jadwal_umur_aplikasi' => 'Segera setelah gejala hawar atau layu bakteri terlihat.',
                'frekuensi_aplikasi' => 'Per minggu dengan interval 5-7 hari hingga penyebaran gejala berhenti.',
            ],
            'Winder 50 EC' => [
                'bahan_aktif' => 'Imidakloprid',
                'kandungan_detail' => 'Imidakloprid.',
                'fungsi' => 'Insektisida sistemik yang ampuh untuk hama tipe penusuk-penghisap seperti kutu daun, wereng, thrips, dan kutu kebul.',
                'takaran' => '0,5 - 1 ml per liter air.',
                'efek_penggunaan' => 'Hama mengalami gangguan saraf, berhenti makan, lumpuh, lalu mati secara perlahan; tanaman terlindungi dari dalam jaringan.',
                'cara_aplikasi' => 'Penyemprotan merata pada daun, terutama bagian bawah daun tempat hama sering bersembunyi.',
                'jadwal_umur_aplikasi' => '15-40 HST pada fase vegetatif jika terlihat ada wereng atau kutu.',
                'frekuensi_aplikasi' => '1 minggu sekali dan hanya disemprotkan saat populasi hama mencapai ambang batas.',
            ],
            'Validacin 3 L' => [
                'bahan_aktif' => 'Validamisin A',
                'kandungan_detail' => 'Validamisin A 3%.',
                'fungsi' => 'Mengendalikan penyakit hawar pelepah pada padi dan jamur upas pada tanaman keras atau sayur.',
                'takaran' => '1 - 2 ml per liter air.',
                'efek_penggunaan' => 'Bercak atau busuk pada pelepah dan batang mengering serta penyebaran jamur berhenti.',
                'cara_aplikasi' => 'Disemprotkan merata dengan fokus pada pelepah dan batang bagian bawah yang dekat permukaan tanah atau air.',
                'jadwal_umur_aplikasi' => '30-50 HST pada fase pembentukan anakan maksimal hingga bunting.',
                'frekuensi_aplikasi' => 'Per minggu dengan interval 7-10 hari, terutama saat cuaca lembap atau hujan.',
            ],
        ];

        foreach ($items as $keyword => $data) {
            DB::table('pestisida')
                ->where('nama', 'like', '%'.$keyword.'%')
                ->update($data);
        }
    }

    private function seedDetailPupuk(): void
    {
        $items = [
            'Urea' => [
                'kandungan' => 'N 46%',
                'kandungan_detail' => 'Nitrogen (N) 46%.',
                'fungsi_utama' => 'Merangsang pertumbuhan vegetatif secara maksimal, terutama daun, batang, dan tunas baru.',
                'takaran' => 'Umumnya 100-300 kg per hektare, tergantung jenis tanaman.',
                'efek_penggunaan' => 'Daun menjadi hijau tua dan rimbun serta pertumbuhan tinggi tanaman meningkat cepat.',
                'cara_aplikasi' => 'Ditabur merata, dibenamkan di sekitar perakaran, atau dilarutkan lalu dikocor pada dosis rendah.',
                'jadwal_umur_aplikasi' => '7-10 HST untuk pemupukan susulan 1 dan 21-25 HST untuk pemupukan susulan 2.',
                'frekuensi_aplikasi' => '2 kali per musim tanam, biasanya per 2 minggu pada bulan pertama.',
            ],
            'NPK Phonska' => [
                'kandungan' => 'N 15%, P 15%, K 15%, S 10%',
                'kandungan_detail' => 'Nitrogen 15%, Fosfat 15%, Kalium 15%, dan Sulfur 10%.',
                'fungsi_utama' => 'Memberikan nutrisi makro seimbang untuk semua fase pertumbuhan, dari vegetatif hingga generatif.',
                'takaran' => '150-300 kg per hektare atau 2-5 gram per liter bila sistem kocor.',
                'efek_penggunaan' => 'Tanaman tumbuh sehat merata, batang lebih kokoh, akar kuat, dan pembungaan serta pembuahan lebih baik.',
                'cara_aplikasi' => 'Ditugal, ditabur, atau dikocor; sering dicampur bersama Urea.',
                'jadwal_umur_aplikasi' => '7-10 HST dan 21-25 HST.',
                'frekuensi_aplikasi' => '2 kali per musim tanam bersamaan dengan jadwal Urea.',
            ],
            'SP-36' => [
                'kandungan' => 'P 36%',
                'kandungan_detail' => 'Fosfat (P2O5) 36%.',
                'fungsi_utama' => 'Sangat penting untuk pembentukan akar, memacu pertumbuhan bunga, dan pematangan buah atau biji.',
                'takaran' => '100-200 kg per hektare.',
                'efek_penggunaan' => 'Akar lebih lebat, bunga keluar serempak, dan risiko rontok bunga atau buah berkurang.',
                'cara_aplikasi' => 'Digunakan sebagai pupuk dasar, ditabur atau dibenamkan saat pengolahan lahan atau awal tanam.',
                'jadwal_umur_aplikasi' => '0 HST sampai maksimal 7 HST sebagai pupuk dasar.',
                'frekuensi_aplikasi' => '1 kali per musim tanam di awal waktu tanam.',
            ],
            'KCl' => [
                'kandungan' => 'K 60%',
                'kandungan_detail' => 'Kalium (K2O) 60%.',
                'fungsi_utama' => 'Meningkatkan kualitas hasil panen, memperkuat jaringan tanaman, dan menambah ketahanan terhadap penyakit serta kekeringan.',
                'takaran' => '50-100 kg per hektare.',
                'efek_penggunaan' => 'Ukuran hasil lebih besar, warna lebih cerah, rasa lebih manis, gabah lebih berbobot, dan hasil panen lebih tahan simpan.',
                'cara_aplikasi' => 'Ditabur atau dibenamkan saat tanaman memasuki fase generatif.',
                'jadwal_umur_aplikasi' => '35-45 HST pada fase primordia atau bunting untuk persiapan pengisian bulir.',
                'frekuensi_aplikasi' => '1 kali per musim tanam menjelang fase generatif.',
            ],
            'Kompos' => [
                'kandungan' => 'C-organik >= 15%',
                'kandungan_detail' => 'Karbon organik, hara makro dan mikro alami, serta mikroorganisme baik.',
                'fungsi_utama' => 'Memperbaiki struktur fisik, kimia, dan biologi tanah.',
                'takaran' => '1-5 ton per hektare sesuai kondisi kerusakan tanah.',
                'efek_penggunaan' => 'Tanah menjadi lebih gembur, mampu menyimpan air lebih baik, dan penyerapan pupuk kimia menjadi lebih efisien.',
                'cara_aplikasi' => 'Dicampurkan merata dengan tanah bedengan saat pengolahan lahan sebelum tanam.',
                'jadwal_umur_aplikasi' => 'H-7 hingga H-2 sebelum pindah tanam pada fase pengolahan lahan.',
                'frekuensi_aplikasi' => '1 kali per musim tanam atau sekitar setiap 6 bulan sekali.',
            ],
            'ZA' => [
                'kandungan' => 'N 21%, S 24%',
                'kandungan_detail' => 'Nitrogen 21% dan Sulfur/Belerang 24%.',
                'fungsi_utama' => 'Sebagai sumber nitrogen pengganti Urea yang tidak terlalu panas sekaligus menyuplai sulfur.',
                'takaran' => '50-150 kg per hektare.',
                'efek_penggunaan' => 'Mempertajam aroma dan rasa hasil panen, daun menjadi hijau segar, dan tanaman lebih kuat terhadap serangan penyakit.',
                'cara_aplikasi' => 'Ditaburkan atau dibenamkan di sekitar pangkal tanaman.',
                'jadwal_umur_aplikasi' => '30-35 HST sebagai pengganti Urea pada fase pembentukan anakan akhir.',
                'frekuensi_aplikasi' => '1 kali per musim tanam.',
            ],
        ];

        foreach ($items as $keyword => $data) {
            DB::table('pupuk')
                ->where('nama', 'like', '%'.$keyword.'%')
                ->update($data);
        }
    }
};
