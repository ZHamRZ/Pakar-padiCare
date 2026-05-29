# Refactor Skema Nilai CF Pakar

Perubahan istilah dari **Rating** menjadi **Nilai CF Pakar** lebih tepat secara logika bisnis karena data ini bukan penilaian umum seperti bintang/skor preferensi, melainkan basis pengetahuan pakar untuk metode Certainty Factor. Kolom `mb` dan `md` tetap dipertahankan karena keduanya adalah istilah baku rumus CF: `CF = MB - MD`.

## A. Analisis & Rencana Perubahan

| Nama Lama | Nama Baru (ID) | Tipe Data Lama | Tipe Data Baru | Alasan Pemilihan Tipe Data & Panjang |
| :--- | :--- | :--- | :--- | :--- |
| `rating_pupuk` | `penyakit_pupuk` | Tabel pivot lama | Tabel pivot CF | Nama baru menjelaskan relasi penyakit dan pupuk. Nilai pakar disimpan langsung sebagai MB/MD, bukan rating abstrak. |
| `rating_pestisida` | `penyakit_pestisida` | Tabel pivot lama | Tabel pivot CF | Nama baru menjelaskan relasi penyakit dan pestisida. Ini lebih mudah dipahami admin lokal. |
| `nilai` | `mb` | `DECIMAL(5,2)` | `DECIMAL(4,3)` | `mb` adalah Measure of Belief. Rentang 0 sampai 1 membutuhkan 1 digit sebelum koma dan 3 digit presisi, misalnya `0.700` atau `1.000`; `DECIMAL(4,3)` lebih presisi dari `DECIMAL(3,2)` tanpa boros. |
| `nilai` | `md` | `DECIMAL(5,2)` | `DECIMAL(4,3)` | `md` adalah Measure of Disbelief. Presisi 3 desimal mengikuti form `step=0.001` dan menghindari pembulatan berlebihan pada perhitungan CF. |
| `penyakit.nama` | `penyakit.nama` | `VARCHAR(100)` | `VARCHAR(120)` | Nama penyakit padi umumnya pendek, tetapi 120 karakter memberi ruang nama lokal/ilmiah tanpa memakai `VARCHAR(255)`. |
| `pupuk.nama` | `pupuk.nama` | `VARCHAR(100)` | `VARCHAR(120)` | Nama produk pupuk bisa memuat merek, formula, dan keterangan subsidi; 120 cukup aman dan tetap hemat indeks/penyimpanan. |
| `pupuk.kandungan` | `pupuk.kandungan` | `VARCHAR(200)` | `VARCHAR(160)` | Kandungan ringkas seperti `NPK 15-15-15` atau daftar unsur tidak perlu 200 karakter; detail panjang sudah ada di `kandungan_detail` bertipe `TEXT`. |
| `pestisida.nama` | `pestisida.nama` | `VARCHAR(100)` | `VARCHAR(120)` | Nama dagang pestisida dapat memuat angka/formulasi; 120 lebih longgar tanpa memakai panjang default besar. |
| `pestisida.bahan_aktif` | `pestisida.bahan_aktif` | `VARCHAR(200)` | `VARCHAR(160)` | Bahan aktif ringkas, sedangkan uraian komposisi lengkap dapat disimpan di `kandungan_detail`. |
| `pestisida.dosis` | `pestisida.dosis` | `VARCHAR(100)` | `VARCHAR(80)` | Dosis form umumnya singkat, misalnya `1-2 g/L`; 80 cukup untuk variasi satuan dan instruksi ringkas. |
| `users` | `pengguna` | Tabel akun Laravel | Tabel akun aplikasi | Nama tabel dilokalkan agar ERD/admin database mudah dipahami. Model `User` diarahkan ke tabel `pengguna` supaya auth tetap stabil. |
| `rekomendasi.id_user` | `rekomendasi.id_pengguna` | `BIGINT UNSIGNED` | `BIGINT UNSIGNED` | Nama FK mengikuti tabel induk `pengguna`; tipe tetap unsigned big integer karena merujuk ke primary key Laravel. |
| `password_reset_tokens` | `token_reset_sandi` | Tabel token reset | Tabel token reset | Nama tabel dilokalkan. Kolom internal `email`, `token`, `created_at` tetap dipertahankan karena dipakai langsung oleh password broker Laravel. |
| `kriteria.jenis` | `kriteria.jenis` | `ENUM('benefit','cost')` | `ENUM('manfaat','biaya')` | Nilai enum dilokalkan. `manfaat` berarti semakin tinggi semakin baik, `biaya` berarti semakin rendah semakin baik. |
| `pengguna.nama` | `pengguna.nama` | `VARCHAR(100)` | `VARCHAR(100)` | Nama orang lokal jarang melewati 100 karakter; cukup untuk nama lengkap tanpa memboroskan indeks. |
| `pengguna.username` | `pengguna.username` | `VARCHAR(50)` | `VARCHAR(50)` | Username login dibatasi 50 karakter karena dipakai sebagai identitas singkat dan unique index. |
| `pengguna.no_telp` | `pengguna.no_telp` | `VARCHAR(30)` | `VARCHAR(30)` | Nomor telepon internasional plus pemisah masih aman di 30 karakter. |
| `pengguna.email` | `pengguna.email` | `VARCHAR(255)` | `VARCHAR(255)` | Panjang 255 mengikuti batas umum email dan kompatibel dengan Laravel password broker. |

## B. Script Migrasi Database (SQL)

```sql
ALTER TABLE penyakit MODIFY nama VARCHAR(120) NOT NULL;

ALTER TABLE pupuk
  MODIFY nama VARCHAR(120) NOT NULL,
  MODIFY kandungan VARCHAR(160) NULL,
  MODIFY satuan VARCHAR(20) NOT NULL DEFAULT 'kg';

ALTER TABLE pestisida
  MODIFY nama VARCHAR(120) NOT NULL,
  MODIFY bahan_aktif VARCHAR(160) NULL,
  MODIFY dosis VARCHAR(80) NULL,
  MODIFY satuan_harga VARCHAR(30) NOT NULL DEFAULT 'per 100ml';

ALTER TABLE penyakit_pupuk
  MODIFY mb DECIMAL(4,3) NOT NULL DEFAULT 0.700,
  MODIFY md DECIMAL(4,3) NOT NULL DEFAULT 0.100,
  ADD CONSTRAINT penyakit_pupuk_mb_range_check CHECK (mb >= 0 AND mb <= 1),
  ADD CONSTRAINT penyakit_pupuk_md_range_check CHECK (md >= 0 AND md <= 1);

ALTER TABLE penyakit_pestisida
  MODIFY mb DECIMAL(4,3) NOT NULL DEFAULT 0.700,
  MODIFY md DECIMAL(4,3) NOT NULL DEFAULT 0.100,
  ADD CONSTRAINT penyakit_pestisida_mb_range_check CHECK (mb >= 0 AND mb <= 1),
  ADD CONSTRAINT penyakit_pestisida_md_range_check CHECK (md >= 0 AND md <= 1);

ALTER TABLE penyakit_gejala
  MODIFY mb DECIMAL(4,3) NOT NULL DEFAULT 0.700,
  MODIFY md DECIMAL(4,3) NOT NULL DEFAULT 0.100,
  ADD CONSTRAINT penyakit_gejala_mb_range_check CHECK (mb >= 0 AND mb <= 1),
  ADD CONSTRAINT penyakit_gejala_md_range_check CHECK (md >= 0 AND md <= 1);

DROP TABLE IF EXISTS rating_pupuk;
DROP TABLE IF EXISTS rating_pestisida;

ALTER TABLE users RENAME TO pengguna;
ALTER TABLE password_reset_tokens RENAME TO token_reset_sandi;

ALTER TABLE rekomendasi
  DROP FOREIGN KEY rekomendasi_id_user_foreign,
  CHANGE id_user id_pengguna BIGINT UNSIGNED NOT NULL,
  ADD CONSTRAINT rekomendasi_id_pengguna_foreign
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE;

ALTER TABLE kriteria MODIFY jenis ENUM('benefit', 'cost', 'manfaat', 'biaya') NOT NULL;
UPDATE kriteria SET jenis = 'manfaat' WHERE jenis = 'benefit';
UPDATE kriteria SET jenis = 'biaya' WHERE jenis = 'cost';
ALTER TABLE kriteria MODIFY jenis ENUM('manfaat', 'biaya') NOT NULL;

DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS gejala_pestisida;
DROP TABLE IF EXISTS gejala_pupuk;
```

Implementasi Laravel tersedia di migration `2026_05_16_000100_optimize_cf_schema_names_and_types.php` dan `2026_05_16_000200_localize_auth_tables_and_drop_unused_tables.php`.

## C. Refactoring Kode Program

1. **Model**
   - `PenyakitPupuk` dan `PenyakitPestisida` tetap memakai fillable `id_penyakit`, `id_pupuk`/`id_pestisida`, `mb`, `md`.
   - Ditambahkan cast `decimal:3` agar nilai CF pakar konsisten dengan `DECIMAL(4,3)`.

2. **Controller**
   - `RatingController` diganti menjadi `NilaiCfController`.
   - Route berubah dari `/admin/rating/...` menjadi `/admin/nilai-cf/...`.
   - Validasi input MB/MD memakai `numeric|min:0|max:1` dan regex maksimal 3 desimal.

3. **Service/Logic**
   - Perhitungan tetap `CF = MB - MD`.
   - Nama variabel service tetap memakai `$mb` dan `$md` karena itu istilah metode, bukan istilah UI yang rancu.

4. **View**
   - View dipindah dari `admin.rating.*` ke `admin.nilai_cf.*`.
   - Label form menjadi `MB (Kepercayaan)` dan `MD (Ketidakyakinan)`.
   - Menu admin berubah menjadi `Nilai CF Pupuk` dan `Nilai CF Pestisida`.

5. **Lokalisasi tabel pengguna dan pembersihan tabel**
   - Model `User` memakai tabel `pengguna`.
   - FK rekomendasi berubah dari `id_user` menjadi `id_pengguna`.
   - Route admin berubah dari `/admin/users` menjadi `/admin/pengguna`.
   - Password reset memakai tabel `token_reset_sandi`.
   - Session, cache, dan queue diarahkan ke driver file/sync sehingga tabel `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, dan `failed_jobs` dihapus.
   - Tabel `gejala_pupuk` dan `gejala_pestisida` dihapus karena rekomendasi produk saat ini berbasis `penyakit_pupuk` dan `penyakit_pestisida`.
