<?php

namespace Database\Seeders;

use App\Models\Gejala;
use App\Models\Kriteria;
use App\Models\Penyakit;
use App\Models\Pestisida;
use App\Models\Pupuk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── USERS ────────────────────────────────────────────
        User::insert([
            ['nama' => 'Administrator',  'username' => 'admin',      'password' => Hash::make('admin123'),   'role' => 'admin',  'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'H. Badaruddin',  'username' => 'badaruddin', 'password' => Hash::make('petani123'),  'role' => 'petani', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Diana Lestari',  'username' => 'diana',      'password' => Hash::make('petani123'),  'role' => 'petani', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── PENYAKIT ──────────────────────────────────────────
        $penyakit = [
            ['kode' => 'P01', 'nama' => 'Blast (Blas)',                'deskripsi' => 'Penyakit yang disebabkan jamur Pyricularia oryzae. Umumnya menyerang daun dan leher malai padi.'],
            ['kode' => 'P02', 'nama' => 'Hawar Daun Bakteri (Kresek)', 'deskripsi' => 'Penyakit yang disebabkan bakteri Xanthomonas oryzae pv. oryzae.'],
            ['kode' => 'P03', 'nama' => 'Tungro',                      'deskripsi' => 'Penyakit virus kompleks RTBV dan RTSV yang ditularkan oleh hama wereng hijau (Nephotettix virescens).'],
            ['kode' => 'P04', 'nama' => 'Busuk Pelepah (Sheath Blight)', 'deskripsi' => 'Penyakit yang disebabkan jamur Rhizoctonia solani dan menyerang pelepah daun padi.'],
            ['kode' => 'P05', 'nama' => 'Bercak Coklat (Brown Spot)',  'deskripsi' => 'Penyakit yang disebabkan jamur Bipolaris oryzae, dahulu dikenal sebagai Helminthosporium oryzae.'],
        ];
        foreach ($penyakit as $p) {
            Penyakit::create($p);
        }

        // ── GEJALA ────────────────────────────────────────────
        $gejala = [
            ['kode' => 'G01', 'nama_gejala' => 'Bercak belah ketupat (ujung runcing) pada daun'],
            ['kode' => 'G02', 'nama_gejala' => 'Leher malai busuk, berubah warna coklat atau hitam dan patah'],
            ['kode' => 'G03', 'nama_gejala' => 'Bulir padi hampa atau tidak berisi'],
            ['kode' => 'G04', 'nama_gejala' => 'Daun menguning mulai dari ujung dan tepi (layu)'],
            ['kode' => 'G05', 'nama_gejala' => 'Tepi daun mengering, bergelombang, dan berwarna kelabu'],
            ['kode' => 'G06', 'nama_gejala' => 'Seluruh tanaman layu mendadak (serangan berat)'],
            ['kode' => 'G07', 'nama_gejala' => 'Tanaman menjadi sangat kerdil dibanding tanaman sehat'],
            ['kode' => 'G08', 'nama_gejala' => 'Daun berubah warna menjadi kuning oranye'],
            ['kode' => 'G09', 'nama_gejala' => 'Jumlah anakan berkurang drastis dan malai tidak keluar'],
            ['kode' => 'G10', 'nama_gejala' => 'Bercak oval keabu-abuan pada pelepah (dekat air)'],
            ['kode' => 'G11', 'nama_gejala' => 'Bercak meluas ke atas membentuk pola seperti awan'],
            ['kode' => 'G12', 'nama_gejala' => 'Batang tanaman membusuk dan mudah rebah'],
            ['kode' => 'G13', 'nama_gejala' => 'Bercak coklat oval merata (seperti biji wijen)'],
            ['kode' => 'G14', 'nama_gejala' => 'Bercak hitam atau coklat pada kulit gabah'],
            ['kode' => 'G15', 'nama_gejala' => 'Daun mengering dan gugur lebih cepat'],
        ];
        foreach ($gejala as $g) {
            Gejala::create($g);
        }

        // ── RELASI PENYAKIT-GEJALA ────────────────────────────
        $p1 = Penyakit::where('kode', 'P01')->first();
        $p2 = Penyakit::where('kode', 'P02')->first();
        $p3 = Penyakit::where('kode', 'P03')->first();
        $p4 = Penyakit::where('kode', 'P04')->first();
        $p5 = Penyakit::where('kode', 'P05')->first();

        $p1->gejala()->attach(Gejala::whereIn('kode', ['G01', 'G02', 'G03', 'G14'])->pluck('id'));
        $p2->gejala()->attach(Gejala::whereIn('kode', ['G04', 'G05', 'G06', 'G08'])->pluck('id'));
        $p3->gejala()->attach(Gejala::whereIn('kode', ['G07', 'G08', 'G09'])->pluck('id'));
        $p4->gejala()->attach(Gejala::whereIn('kode', ['G06', 'G10', 'G11', 'G12'])->pluck('id'));
        $p5->gejala()->attach(Gejala::whereIn('kode', ['G03', 'G13', 'G14', 'G15'])->pluck('id'));

        // ── KRITERIA ──────────────────────────────────────────
        Kriteria::insert([
            ['kode' => 'C1', 'nama' => 'Harga',             'jenis' => 'Murah',     'bobot' => 0.35, 'keterangan' => 'Harga per satuan produk yang tersedia di pasaran',      'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'C2', 'nama' => 'Efektivitas',       'jenis' => 'Efektif',   'bobot' => 0.25, 'keterangan' => 'Tingkat keberhasilan mengendalikan penyakit',           'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'C3', 'nama' => 'Dampak Lingkungan', 'jenis' => 'Aman',      'bobot' => 0.25, 'keterangan' => 'Pengaruh negatif terhadap lingkungan sawah',            'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'C4', 'nama' => 'Kesesuaian Penyakit', 'jenis' => 'Efektif', 'bobot' => 0.15, 'keterangan' => 'Kesesuaian produk terhadap jenis penyakit yang dipilih', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── PUPUK ─────────────────────────────────────────────
        Pupuk::insert([
            ['kode' => 'PK01', 'nama' => 'Urea (Subsidi)',        'kandungan' => 'N 46%',                'fungsi_utama' => 'Memacu vegetatif (Hindari saat Blast/Kresek parah)',                                'dosis_per_hektar' => 200, 'satuan_dosis' => 'kg', 'harga_per_unit' => 2250,   'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK02', 'nama' => 'Phonska (Subsidi)',     'kandungan' => 'NPK 15-15-15',         'fungsi_utama' => 'Nutrisi dasar berimbang',                                                         'dosis_per_hektar' => 300, 'satuan_dosis' => 'kg', 'harga_per_unit' => 2300,   'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK03', 'nama' => 'ZA',                    'kandungan' => 'N 21%, S 24%',         'fungsi_utama' => 'Sulfur menekan perkembangan jamur dibanding Urea',                                'dosis_per_hektar' => 200, 'satuan_dosis' => 'kg', 'harga_per_unit' => 1700,   'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK04', 'nama' => 'Kapur Pertanian (Dolomit)', 'kandungan' => 'Ca, Mg',           'fungsi_utama' => 'Menaikkan pH tanah, menekan busuk pangkal batang',                              'dosis_per_hektar' => 500, 'satuan_dosis' => 'kg', 'harga_per_unit' => 1000,   'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK05', 'nama' => 'Pupuk Kandang / Kompos', 'kandungan' => 'C-Organik',           'fungsi_utama' => 'Memperbaiki struktur mikrobiologi tanah',                                       'dosis_per_hektar' => 2000, 'satuan_dosis' => 'kg', 'harga_per_unit' => 800,    'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK06', 'nama' => 'Silika Cair',           'kandungan' => 'SiO2',                 'fungsi_utama' => 'Menebalkan dinding sel daun, kebal terhadap Blast & Kresek',                    'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 34000,  'satuan_harga_qty' => 100, 'satuan_harga_unit' => 'ml',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK07', 'nama' => 'MKP (Mono Kalium Fosfat)', 'kandungan' => 'P 52%, K 34%',      'fungsi_utama' => 'Nol Nitrogen, menghentikan penyebaran jamur & bakteri',                         'dosis_per_hektar' => 5, 'satuan_dosis' => 'kg', 'harga_per_unit' => 45000,  'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK08', 'nama' => 'KNO3 Putih',            'kandungan' => 'N 13%, K 45%',         'fungsi_utama' => 'Kalium tinggi memperkokoh batang agar tidak rebah (P04)',                       'dosis_per_hektar' => 3, 'satuan_dosis' => 'kg', 'harga_per_unit' => 40000,  'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK09', 'nama' => 'Pupuk Mikro (Zink/Tembaga)', 'kandungan' => 'Zn, Cu, Bo',      'fungsi_utama' => 'Memperbaiki defisiensi yang memicu Bercak Coklat',                              'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 130000, 'satuan_harga_qty' => 500, 'satuan_harga_unit' => 'ml',     'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PK10', 'nama' => 'NPK Mutiara 16-16-16',  'kandungan' => 'NPK 16-16-16',         'fungsi_utama' => 'Penyerapan instan untuk pemulihan setelah terserang Tungro',                    'dosis_per_hektar' => 200, 'satuan_dosis' => 'kg', 'harga_per_unit' => 18000,  'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',     'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── PESTISIDA ─────────────────────────────────────────
        Pestisida::insert([
            ['kode' => 'PS01', 'nama' => 'Nordox 56 WP',        'jenis' => 'bakterisida', 'bahan_aktif' => 'Tembaga Oksida',               'dosis' => '1-2 g/L',    'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 45000,  'satuan_harga_qty' => 100, 'satuan_harga_unit' => 'g',    'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS02', 'nama' => 'Mankozeb (Bion-M)',   'jenis' => 'fungisida',   'bahan_aktif' => 'Mankozeb 80%',                 'dosis' => '2-3 g/L',    'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 85000,  'satuan_harga_qty' => 1, 'satuan_harga_unit' => 'kg',   'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS03', 'nama' => 'Heksakonazol',        'jenis' => 'fungisida',   'bahan_aktif' => 'Heksakonazol 50g/l',           'dosis' => '1-2 ml/L',   'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 60000,  'satuan_harga_qty' => 250, 'satuan_harga_unit' => 'ml',   'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS04', 'nama' => 'BPMC (Bassa)',        'jenis' => 'insektisida', 'bahan_aktif' => 'BPMC 500g/l',                  'dosis' => '1-2 ml/L',   'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 40000,  'satuan_harga_qty' => 500, 'satuan_harga_unit' => 'ml',   'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS05', 'nama' => 'Agrept 20 WP',        'jenis' => 'bakterisida', 'bahan_aktif' => 'Streptomisin',                 'dosis' => '1-2 g/L',    'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 25000,  'satuan_harga_qty' => 50, 'satuan_harga_unit' => 'g',    'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS06', 'nama' => 'Nativo 75 WG',        'jenis' => 'fungisida',   'bahan_aktif' => 'Tebukonazol + Trifloksistrobin', 'dosis' => '0,5-1 g/L',  'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 120000, 'satuan_harga_qty' => 50, 'satuan_harga_unit' => 'g',    'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS07', 'nama' => 'Seltima 100 CS',      'jenis' => 'fungisida',   'bahan_aktif' => 'Piraklostrobin',               'dosis' => '0,5-1 ml/L', 'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 185000, 'satuan_harga_qty' => 250, 'satuan_harga_unit' => 'ml',   'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS08', 'nama' => 'Kasumin 20 L',        'jenis' => 'bakterisida', 'bahan_aktif' => 'Kasugamisin',                  'dosis' => '1-2 ml/L',   'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 75000,  'satuan_harga_qty' => 400, 'satuan_harga_unit' => 'ml',   'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS09', 'nama' => 'Plenum 50 WG',        'jenis' => 'insektisida', 'bahan_aktif' => 'Pimetrozin',                   'dosis' => '0,5-1 g/L',  'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 110000, 'satuan_harga_qty' => 25, 'satuan_harga_unit' => 'g',    'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'PS10', 'nama' => 'Amistartop 325 SC',   'jenis' => 'fungisida',   'bahan_aktif' => 'Azoksistrobin + Difenokonazol', 'dosis' => '0,5-1 ml/L', 'dosis_per_hektar' => 2, 'satuan_dosis' => 'L', 'harga_per_unit' => 150000, 'satuan_harga_qty' => 100, 'satuan_harga_unit' => 'ml',   'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->call(CertaintyFactorRuleSeeder::class);
    }
}
