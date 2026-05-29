# Dokumen Informasi Flowchart Sistem SPK Pupuk dan Pestisida Padi

## 1. Tujuan Dokumen

Dokumen ini disusun untuk membantu pembuatan flowchart sistem pada aplikasi SPK Pupuk dan Pestisida Padi berbasis Laravel. Fokus dokumen ini adalah:

- memetakan seluruh halaman yang tersedia di sistem,
- menjelaskan aktor yang terlibat,
- merinci alur proses utama dari sisi pengguna dan admin,
- menjelaskan algoritma identifikasi penyakit dan algoritma SAW,
- memberi saran pemecahan flowchart per modul agar mudah digambar pada Word atau aplikasi diagram.

## 2. Ringkasan Sistem

Sistem ini adalah aplikasi pendukung keputusan untuk membantu petani:

- memilih gejala tanaman padi,
- mengidentifikasi kemungkinan penyakit,
- menentukan prioritas kebutuhan pengguna,
- menghitung rekomendasi pupuk dan pestisida menggunakan metode SAW,
- menyimpan hasil rekomendasi ke riwayat bila pengguna sudah login.

Di sisi admin, sistem dipakai untuk:

- mengelola data penyakit, gejala, pupuk, dan pestisida,
- mengelola bobot kriteria,
- mengisi rating SAW untuk pupuk dan pestisida,
- melihat riwayat rekomendasi seluruh pengguna,
- mengelola akun petani.

## 3. Aktor Sistem

### 3.1 Guest

Guest adalah pengunjung yang belum login. Guest dapat:

- membuka dashboard publik,
- melihat riwayat kasus lama di beranda,
- melakukan diagnosis tanpa login,
- melihat preview hasil rekomendasi,
- login atau register.

Guest tidak dapat:

- menyimpan hasil ke riwayat pribadi,
- membuka detail riwayat pribadi,
- mengakses halaman admin.

### 3.2 Petani atau User

Petani adalah pengguna yang sudah login dengan role `petani`. Petani dapat:

- mengakses dashboard pengguna,
- melakukan diagnosis,
- menyimpan hasil rekomendasi,
- membuka detail hasil dan cetak hasil,
- melihat riwayat rekomendasi pribadi,
- mengubah profil.

### 3.3 Admin

Admin adalah pengguna dengan role `admin`. Admin dapat:

- mengakses dashboard admin,
- mengelola master data,
- mengelola bobot kriteria,
- mengisi rating SAW,
- melihat seluruh riwayat rekomendasi,
- mengelola akun pengguna,
- mengubah profil admin.

## 4. Modul Utama Sistem

Sistem dapat dibagi menjadi modul berikut:

1. Modul autentikasi
2. Modul dashboard publik dan dashboard pengguna
3. Modul diagnosis penyakit
4. Modul rekomendasi SAW
5. Modul riwayat rekomendasi
6. Modul profil
7. Modul admin data master
8. Modul admin kriteria dan rating SAW
9. Modul admin pengguna
10. Modul admin riwayat

## 5. Daftar Seluruh Halaman dan Fungsi

## 5.1 Halaman Publik dan Autentikasi

### 5.1.1 `/` - Dashboard publik atau dashboard pengguna

- Route: `home`
- Controller: `User\DashboardController@index`
- Akses: guest dan user
- Fungsi:
  - bila pengunjung belum login, menampilkan dashboard publik,
  - bila petani login, menampilkan dashboard pengguna,
  - bila admin login, diarahkan ke dashboard admin.
- Informasi utama:
  - statistik ringkas sistem,
  - kartu aksi cepat,
  - riwayat kasus lama,
  - panel login cepat untuk guest,
  - riwayat terbaru untuk user yang login.

### 5.1.2 `/login`

- Route: `login`
- Controller: `AuthController@showLogin`
- Akses: guest
- Fungsi:
  - mengarahkan user ke beranda bagian login.

### 5.1.3 `/register`

- Route: `register`
- Controller: `AuthController@showRegister`
- Akses: guest
- Fungsi:
  - menampilkan halaman registrasi akun petani.

### 5.1.4 Proses login

- Route: `login.post`
- Method: POST
- Controller: `AuthController@login`
- Fungsi:
  - validasi username dan password,
  - autentikasi,
  - regenerasi session,
  - redirect ke dashboard berdasarkan role.

### 5.1.5 Proses register

- Route: `register.post`
- Method: POST
- Controller: `AuthController@register`
- Fungsi:
  - validasi data pengguna,
  - membuat akun baru role petani,
  - login otomatis,
  - redirect ke dashboard user.

### 5.1.6 Proses logout

- Route: `logout` dan `logout.get`
- Controller: `AuthController@logout`
- Fungsi:
  - logout akun,
  - invalidate session,
  - regenerate token,
  - kembali ke dashboard publik.

## 5.2 Halaman User atau Petani

### 5.2.1 `/user/dashboard`

- Route: `user.dashboard`
- Controller: `User\DashboardController@index`
- Fungsi:
  - dashboard utama petani,
  - statistik riwayat dan aktivitas diagnosis,
  - akses cepat ke diagnosis dan riwayat,
  - menampilkan riwayat kasus lama,
  - menampilkan riwayat terbaru pribadi.

### 5.2.2 `/user/diagnosis`

- Route: `user.diagnosis.index`
- Controller: `User\DiagnosisController@index`
- Fungsi:
  - menampilkan daftar gejala,
  - mendukung pencarian gejala,
  - memungkinkan user memilih satu atau lebih gejala.

### 5.2.3 Proses identifikasi penyakit

- Route: `user.diagnosis.identifikasi`
- Method: POST
- Controller: `User\DiagnosisController@identifikasi`
- Fungsi:
  - menerima daftar gejala yang dipilih,
  - menghitung penyakit yang cocok,
  - menampilkan halaman hasil identifikasi.

### 5.2.4 Halaman hasil identifikasi

- View: `resources/views/user/diagnosis/hasil.blade.php`
- Ditampilkan oleh proses identifikasi
- Fungsi:
  - menampilkan gejala yang dipilih,
  - menampilkan daftar penyakit yang cocok,
  - memberi pilihan preset prioritas,
  - memungkinkan user memilih beberapa penyakit sekaligus,
  - memulai proses rekomendasi SAW.

### 5.2.5 Proses rekomendasi dari diagnosis

- Route: `user.diagnosis.proses`
- Method: POST
- Controller: `User\DiagnosisController@proses`
- Fungsi:
  - membaca penyakit yang dipilih,
  - membaca prioritas user,
  - membuat preview SAW,
  - menyimpan hasil bila user login,
  - menyimpan payload session untuk mode preview guest.

### 5.2.6 `/user/rekomendasi/preview`

- Route: `user.rekomendasi.preview`
- Controller: `User\RekomendasiController@preview`
- Fungsi:
  - menampilkan hasil rekomendasi batch untuk guest atau hasil yang baru diproses,
  - menampilkan gambar penyakit, gejala yang cocok, pupuk, dan pestisida.

### 5.2.7 `/user/rekomendasi/preview/detail`

- Route: `user.rekomendasi.preview.detail`
- Controller: `User\RekomendasiController@previewDetail`
- Fungsi:
  - menampilkan detail perhitungan SAW,
  - hanya valid bila jumlah penyakit yang dipilih satu.

### 5.2.8 `/user/rekomendasi/{id}`

- Route: `user.rekomendasi.show`
- Controller: `User\RekomendasiController@show`
- Akses: user login
- Fungsi:
  - menampilkan hasil rekomendasi yang sudah tersimpan untuk satu kasus.

### 5.2.9 `/user/rekomendasi/{id}/detail`

- Route: `user.rekomendasi.detail`
- Controller: `User\RekomendasiController@detail`
- Fungsi:
  - menjelaskan detail perhitungan SAW untuk rekomendasi yang tersimpan.

### 5.2.10 `/user/rekomendasi/{id}/cetak`

- Route: `user.rekomendasi.cetak`
- Controller: `User\RekomendasiController@cetak`
- Fungsi:
  - menampilkan versi cetak hasil rekomendasi.

### 5.2.11 `/user/riwayat`

- Route: `user.riwayat.index`
- Controller: `User\RiwayatController@index`
- Fungsi:
  - menampilkan riwayat rekomendasi milik user,
  - memperlihatkan penyakit, pupuk terbaik, pestisida terbaik, dan aksi lihat atau cetak.

### 5.2.12 `/user/profile`

- Route: `user.profile.edit`
- Controller: `ProfileController@edit`
- Fungsi:
  - menampilkan form ubah profil user.

### 5.2.13 Proses update profil user

- Route: `user.profile.update`
- Method: PUT
- Controller: `ProfileController@update`
- Fungsi:
  - mengubah data profil,
  - mengubah foto profil,
  - mengubah password bila password lama sesuai.

## 5.3 Halaman Admin

### 5.3.1 `/admin/dashboard`

- Route: `admin.dashboard`
- Controller: `Admin\DashboardController@index`
- Fungsi:
  - menampilkan statistik data master,
  - memeriksa kesiapan data SAW,
  - menampilkan quick actions,
  - menampilkan riwayat terbaru dan pengguna terbaru.

### 5.3.2 Modul data penyakit

Halaman:

- `/admin/penyakit` - daftar penyakit
- `/admin/penyakit/create` - form tambah penyakit
- `/admin/penyakit/{id}/edit` - form edit penyakit

Controller: `Admin\PenyakitController`

Fungsi:

- CRUD penyakit,
- upload gambar penyakit,
- menghubungkan penyakit dengan gejala.

### 5.3.3 Modul data gejala

Halaman:

- `/admin/gejala` - daftar gejala
- `/admin/gejala/create` - form tambah gejala
- `/admin/gejala/{id}/edit` - form edit gejala

Controller: `Admin\GejalaController`

Fungsi:

- CRUD gejala,
- upload gambar gejala bila kolom tersedia.

### 5.3.4 Modul data pupuk

Halaman:

- `/admin/pupuk` - daftar pupuk
- `/admin/pupuk/create` - form tambah pupuk
- `/admin/pupuk/{id}/edit` - form edit pupuk

Controller: `Admin\PupukController`

Fungsi:

- CRUD pupuk,
- upload gambar pupuk,
- kelola kandungan, fungsi, harga, takaran, efek, cara aplikasi.

### 5.3.5 Modul data pestisida

Halaman:

- `/admin/pestisida` - daftar pestisida
- `/admin/pestisida/create` - form tambah pestisida
- `/admin/pestisida/{id}/edit` - form edit pestisida

Controller: `Admin\PestisidaController`

Fungsi:

- CRUD pestisida,
- upload gambar pestisida,
- kelola jenis, bahan aktif, harga, dosis, fungsi, dan cara aplikasi.

### 5.3.6 Modul kriteria SAW

Halaman:

- `/admin/kriteria` - daftar kriteria dan total bobot
- `/admin/kriteria/{id}/edit` - form edit kriteria

Controller: `Admin\KriteriaController`

Fungsi:

- menampilkan kriteria,
- mengubah nama, jenis `benefit` atau `cost`, bobot, dan keterangan.

### 5.3.7 Modul rating pupuk

Halaman:

- `/admin/rating/pupuk`

Controller: `Admin\RatingController@pupuk`

Fungsi:

- menampilkan matriks input rating pupuk untuk kombinasi penyakit, pupuk, dan kriteria,
- menyimpan nilai rating 1 sampai 5.

### 5.3.8 Modul rating pestisida

Halaman:

- `/admin/rating/pestisida`

Controller: `Admin\RatingController@pestisida`

Fungsi:

- menampilkan matriks input rating pestisida untuk kombinasi penyakit, pestisida, dan kriteria,
- menyimpan nilai rating 1 sampai 5.

### 5.3.9 Modul data pengguna

Halaman:

- `/admin/users`

Controller: `Admin\UserController@index`

Fungsi:

- menampilkan daftar petani,
- menghapus user petani,
- reset password user ke default `petani123`.

### 5.3.10 Modul riwayat admin

Halaman:

- `/admin/riwayat` - daftar seluruh riwayat rekomendasi
- `/admin/riwayat/{id}` - detail ringkas riwayat
- `/admin/riwayat/{id}/detail` - detail perhitungan SAW admin

Controller: `Admin\RiwayatController`

Fungsi:

- memonitor hasil rekomendasi semua user,
- meninjau penyakit, pupuk, pestisida, dan detail SAW.

### 5.3.11 `/admin/profile`

- Route: `admin.profile.edit`
- Controller: `ProfileController@edit`
- Fungsi:
  - form profil admin.

### 5.3.12 Proses update profil admin

- Route: `admin.profile.update`
- Controller: `ProfileController@update`
- Fungsi:
  - update data profil admin dengan mekanisme yang sama seperti profil user.

## 6. Entitas Data Inti

Entitas penting yang muncul dalam flowchart sistem:

- `users`: menyimpan akun admin dan petani
- `penyakit`: data penyakit padi
- `gejala`: data gejala
- `penyakit_gejala`: relasi many-to-many antara penyakit dan gejala
- `pupuk`: data alternatif pupuk
- `pestisida`: data alternatif pestisida
- `kriteria`: kriteria dan bobot SAW
- `rating_pupuk`: nilai rating pupuk per penyakit dan kriteria
- `rating_pestisida`: nilai rating pestisida per penyakit dan kriteria
- `rekomendasi`: header hasil rekomendasi
- `detail_rekomendasi_pupuk`: ranking hasil pupuk
- `detail_rekomendasi_pestisida`: ranking hasil pestisida

## 6A. Informasi Database Sistem

Bagian ini menjelaskan struktur database yang mendukung aplikasi SPK Pupuk dan Pestisida Padi. Informasi ini berguna untuk:

- membuat flowchart yang terhubung dengan database,
- menulis deskripsi basis data pada skripsi,
- menyusun ERD atau relasi tabel,
- menjelaskan proses input, proses, dan output sistem.

## 6A.1 Database yang digunakan

Database utama yang dipakai aplikasi adalah MySQL. Dari konfigurasi yang terlihat pada proyek, database aplikasi menggunakan:

- DBMS: MySQL
- koneksi aplikasi: Laravel database connection
- contoh nama database: `db_spk_padi`

Selain tabel aplikasi, sistem juga memakai beberapa tabel bawaan Laravel seperti:

- `users`
- `sessions`
- `password_reset_tokens`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

Untuk flowchart sistem utama, tabel yang paling penting adalah tabel domain aplikasi, bukan tabel pendukung framework.

## 6A.2 Tabel Utama Aplikasi

### a. Tabel `users`

Fungsi:

- menyimpan data akun admin dan petani,
- menjadi sumber autentikasi login,
- menjadi penghubung riwayat rekomendasi berdasarkan user.

Kolom penting:

- `id`
- `nama`
- `username`
- `role` dengan nilai `admin` atau `petani`
- `email`
- `password`
- `no_telp`
- `alamat`
- `catatan_profil`
- `foto_profil`
- `remember_token`
- `created_at`
- `updated_at`

Catatan:

- satu user dapat memiliki banyak rekomendasi,
- admin dan petani disimpan dalam tabel yang sama, dibedakan oleh kolom `role`.

### b. Tabel `penyakit`

Fungsi:

- menyimpan daftar penyakit padi yang menjadi target identifikasi,
- menjadi pusat relasi gejala dan rekomendasi.

Kolom penting:

- `id`
- `kode`
- `nama`
- `deskripsi`
- `gambar`
- `created_at`
- `updated_at`

Catatan:

- satu penyakit dapat memiliki banyak gejala,
- satu penyakit dapat memiliki banyak rating pupuk dan pestisida,
- satu penyakit dapat muncul pada banyak riwayat rekomendasi.

### c. Tabel `gejala`

Fungsi:

- menyimpan daftar gejala penyakit yang dipilih user saat diagnosis.

Kolom penting:

- `id`
- `kode`
- `nama_gejala`
- `gambar`
- `created_at`
- `updated_at`

Catatan:

- satu gejala bisa dipakai oleh lebih dari satu penyakit,
- relasinya ke penyakit bersifat many-to-many.

### d. Tabel `penyakit_gejala`

Fungsi:

- tabel pivot yang menghubungkan penyakit dan gejala.

Kolom penting:

- `id`
- `id_penyakit`
- `id_gejala`

Catatan:

- kombinasi `id_penyakit` dan `id_gejala` bersifat unik,
- tabel ini sangat penting pada proses identifikasi penyakit.

### e. Tabel `kriteria`

Fungsi:

- menyimpan kriteria penilaian SAW untuk pupuk dan pestisida.

Kolom penting:

- `id`
- `kode`
- `nama`
- `jenis` dengan nilai `benefit` atau `cost`
- `bobot`
- `keterangan`
- `created_at`
- `updated_at`

Catatan:

- bobot kriteria dipakai sebagai dasar perhitungan SAW,
- jenis kriteria menentukan rumus normalisasi.

### f. Tabel `pupuk`

Fungsi:

- menyimpan daftar alternatif pupuk yang akan dirangking oleh SAW.

Kolom penting:

- `id`
- `kode`
- `nama`
- `kandungan`
- `kandungan_detail`
- `fungsi_utama`
- `takaran`
- `efek_penggunaan`
- `cara_aplikasi`
- `jadwal_umur_aplikasi`
- `frekuensi_aplikasi`
- `harga_per_kg`
- `satuan`
- `gambar`
- `created_at`
- `updated_at`

Catatan:

- seluruh data pupuk menjadi alternatif pada matriks keputusan SAW pupuk.

### g. Tabel `pestisida`

Fungsi:

- menyimpan daftar alternatif pestisida yang akan dirangking oleh SAW.

Kolom penting:

- `id`
- `kode`
- `nama`
- `jenis`
- `bahan_aktif`
- `kandungan_detail`
- `fungsi`
- `dosis`
- `takaran`
- `efek_penggunaan`
- `cara_aplikasi`
- `jadwal_umur_aplikasi`
- `frekuensi_aplikasi`
- `harga`
- `satuan_harga`
- `gambar`
- `created_at`
- `updated_at`

Catatan:

- seluruh data pestisida menjadi alternatif pada matriks keputusan SAW pestisida.

### h. Tabel `rating_pupuk`

Fungsi:

- menyimpan nilai rating pupuk untuk setiap kombinasi penyakit dan kriteria.

Kolom penting:

- `id`
- `id_pupuk`
- `id_kriteria`
- `id_penyakit`
- `nilai`
- `created_at`
- `updated_at`

Catatan:

- kombinasi `id_pupuk`, `id_kriteria`, dan `id_penyakit` bersifat unik,
- tabel ini adalah sumber nilai `xij` pada SAW pupuk.

### i. Tabel `rating_pestisida`

Fungsi:

- menyimpan nilai rating pestisida untuk setiap kombinasi penyakit dan kriteria.

Kolom penting:

- `id`
- `id_pestisida`
- `id_kriteria`
- `id_penyakit`
- `nilai`
- `created_at`
- `updated_at`

Catatan:

- kombinasi `id_pestisida`, `id_kriteria`, dan `id_penyakit` bersifat unik,
- tabel ini adalah sumber nilai `xij` pada SAW pestisida.

### j. Tabel `rekomendasi`

Fungsi:

- menyimpan header atau ringkasan hasil rekomendasi yang sudah diproses.

Kolom penting:

- `id`
- `id_user`
- `id_penyakit`
- `tanggal`
- `preferensi_label`
- `preferensi_pengguna`
- `created_at`
- `updated_at`

Catatan:

- `preferensi_pengguna` bertipe JSON dan menyimpan snapshot preferensi saat proses dilakukan,
- satu rekomendasi memiliki banyak detail ranking pupuk dan pestisida.

### k. Tabel `detail_rekomendasi_pupuk`

Fungsi:

- menyimpan hasil ranking pupuk dari satu rekomendasi.

Kolom penting:

- `id`
- `id_rekomendasi`
- `id_pupuk`
- `nilai_vi`
- `peringkat`

Catatan:

- satu rekomendasi dapat memiliki banyak baris detail pupuk,
- nilai `nilai_vi` adalah hasil akhir SAW.

### l. Tabel `detail_rekomendasi_pestisida`

Fungsi:

- menyimpan hasil ranking pestisida dari satu rekomendasi.

Kolom penting:

- `id`
- `id_rekomendasi`
- `id_pestisida`
- `nilai_vi`
- `peringkat`

Catatan:

- satu rekomendasi dapat memiliki banyak baris detail pestisida,
- nilai `nilai_vi` adalah hasil akhir SAW.

## 6A.3 Tabel Pendukung Laravel

### a. `sessions`

Fungsi:

- menyimpan session login pengguna,
- menyimpan payload session seperti preview rekomendasi guest.

Kolom penting:

- `id`
- `user_id`
- `ip_address`
- `user_agent`
- `payload`
- `last_activity`

### b. `password_reset_tokens`

Fungsi:

- menyimpan token reset password bila fitur reset password email digunakan.

### c. `cache` dan `cache_locks`

Fungsi:

- mendukung penyimpanan cache dan lock cache Laravel.

### d. `jobs`, `job_batches`, `failed_jobs`

Fungsi:

- mendukung queue Laravel bila aplikasi memakai background job.

Catatan:

- tabel-tabel ini tidak langsung masuk ke flowchart bisnis inti diagnosis dan rekomendasi.

## 6A.4 Relasi Antar Tabel

Relasi utama sistem:

1. `users` one-to-many `rekomendasi`
2. `penyakit` one-to-many `rekomendasi`
3. `penyakit` many-to-many `gejala` melalui `penyakit_gejala`
4. `penyakit` one-to-many `rating_pupuk`
5. `penyakit` one-to-many `rating_pestisida`
6. `kriteria` one-to-many `rating_pupuk`
7. `kriteria` one-to-many `rating_pestisida`
8. `pupuk` one-to-many `rating_pupuk`
9. `pestisida` one-to-many `rating_pestisida`
10. `rekomendasi` one-to-many `detail_rekomendasi_pupuk`
11. `rekomendasi` one-to-many `detail_rekomendasi_pestisida`
12. `pupuk` one-to-many `detail_rekomendasi_pupuk`
13. `pestisida` one-to-many `detail_rekomendasi_pestisida`

## 6A.5 Tabel yang Dipakai pada Tiap Proses

### Proses login

Tabel yang dipakai:

- `users`
- `sessions`

### Proses diagnosis penyakit

Tabel yang dipakai:

- `gejala`
- `penyakit`
- `penyakit_gejala`

### Proses rekomendasi SAW

Tabel yang dipakai:

- `kriteria`
- `pupuk`
- `pestisida`
- `rating_pupuk`
- `rating_pestisida`
- `rekomendasi`
- `detail_rekomendasi_pupuk`
- `detail_rekomendasi_pestisida`

### Proses riwayat rekomendasi

Tabel yang dipakai:

- `rekomendasi`
- `detail_rekomendasi_pupuk`
- `detail_rekomendasi_pestisida`
- `penyakit`
- `pupuk`
- `pestisida`
- `users`

### Proses dashboard kasus lama

Tabel yang dipakai:

- `rekomendasi`
- `penyakit`
- `gejala`
- `detail_rekomendasi_pupuk`
- `detail_rekomendasi_pestisida`

## 6A.6 Data yang Paling Penting untuk ERD

Jika ingin dibuat ERD pada skripsi, tabel yang paling wajib dimasukkan adalah:

- `users`
- `penyakit`
- `gejala`
- `penyakit_gejala`
- `kriteria`
- `pupuk`
- `pestisida`
- `rating_pupuk`
- `rating_pestisida`
- `rekomendasi`
- `detail_rekomendasi_pupuk`
- `detail_rekomendasi_pestisida`

## 6A.7 Ringkasan Database untuk Penjelasan Skripsi

Secara ringkas, database sistem ini terdiri dari:

- tabel akun pengguna,
- tabel master penyakit dan gejala,
- tabel master pupuk dan pestisida,
- tabel kriteria keputusan,
- tabel rating untuk matriks SAW,
- tabel hasil rekomendasi dan detail ranking,
- tabel session serta tabel pendukung Laravel.

Struktur ini mendukung alur lengkap mulai dari input gejala, identifikasi penyakit, perhitungan SAW, sampai penyimpanan riwayat hasil rekomendasi.

## 7. Alur Sistem yang Paling Penting untuk Flowchart

## 7.1 Flowchart Login dan Redirect Role

Urutan logika:

1. User membuka halaman login.
2. Sistem validasi username dan password.
3. Jika gagal, kembali ke form login dengan pesan error.
4. Jika berhasil, sistem membuat session baru.
5. Sistem cek role user.
6. Jika role admin, arahkan ke dashboard admin.
7. Jika role petani, arahkan ke dashboard user.

Keputusan utama:

- data login valid atau tidak,
- role admin atau petani.

## 7.2 Flowchart Diagnosis Penyakit

Urutan logika:

1. User membuka halaman diagnosis.
2. Sistem menampilkan daftar gejala.
3. User memilih satu atau lebih gejala.
4. Sistem validasi bahwa minimal satu gejala dipilih.
5. Sistem mengambil semua penyakit beserta gejalanya.
6. Sistem menghitung kecocokan setiap penyakit.
7. Jika tidak ada penyakit cocok, tampilkan pesan error.
8. Jika ada penyakit cocok, sistem urutkan penyakit dari kecocokan tertinggi.
9. Sistem tampilkan hasil identifikasi dan opsi prioritas kebutuhan.
10. User memilih satu atau beberapa penyakit untuk diproses.
11. User memilih preset prioritas atau input prioritas custom.
12. Sistem memproses rekomendasi SAW.

Keputusan utama:

- gejala dipilih atau tidak,
- penyakit cocok ditemukan atau tidak,
- user login atau guest,
- preset prioritas atau custom.

## 7.3 Flowchart Proses Rekomendasi

Urutan logika:

1. Sistem menerima daftar penyakit terpilih dan preferensi.
2. Sistem membangun snapshot preferensi user.
3. Untuk setiap penyakit:
4. Sistem hitung preview SAW pupuk.
5. Sistem hitung preview SAW pestisida.
6. Jika user login:
7. Simpan hasil ke tabel rekomendasi dan tabel detail.
8. Jika user guest:
9. Simpan data preview ke session.
10. Arahkan ke halaman hasil rekomendasi.

Keputusan utama:

- user login atau tidak,
- data rating lengkap atau tidak.

## 7.4 Flowchart Admin Data Master

Bisa dipecah menjadi empat flowchart serupa:

- kelola penyakit,
- kelola gejala,
- kelola pupuk,
- kelola pestisida.

Pola umumnya:

1. Admin membuka halaman daftar data.
2. Admin memilih tambah, edit, atau hapus.
3. Jika tambah atau edit:
4. Sistem tampilkan form.
5. Admin isi data.
6. Sistem validasi.
7. Jika valid, simpan perubahan.
8. Kembali ke daftar dengan pesan sukses.

## 7.5 Flowchart Admin Kriteria dan Bobot

Urutan logika:

1. Admin membuka daftar kriteria.
2. Sistem menampilkan semua kriteria dan total bobot.
3. Admin memilih edit kriteria.
4. Admin ubah nama, jenis, bobot, dan keterangan.
5. Sistem validasi.
6. Simpan perubahan.
7. Kembali ke daftar kriteria.

## 7.6 Flowchart Admin Input Rating SAW

Urutan logika:

1. Admin membuka rating pupuk atau rating pestisida.
2. Sistem memuat daftar penyakit, alternatif, dan kriteria.
3. Admin mengisi nilai rating 1 sampai 5.
4. Sistem validasi bahwa semua input numerik dan berada pada rentang 1 sampai 5.
5. Sistem simpan atau update setiap kombinasi.
6. Sistem tampilkan pesan sukses.

## 7.7 Flowchart Riwayat Kasus Lama pada Dashboard

Urutan logika:

1. Sistem mengambil daftar penyakit populer berdasarkan jumlah rekomendasi.
2. Sistem pilih penyakit yang jumlah dicarinya lebih dari nol.
3. Sistem ambil rekomendasi terbaru untuk tiap penyakit populer.
4. Sistem tampilkan di dashboard sebagai kartu referensi.
5. User memakai kartu ini sebagai acuan awal sebelum diagnosis.

## 8. Algoritma Identifikasi Penyakit

Algoritma ini berada pada `User\DiagnosisController@identifikasi`.

### 8.1 Input

- daftar gejala yang dipilih user

### 8.2 Langkah Proses

1. Validasi bahwa `gejala` adalah array dan minimal satu item.
2. Ambil seluruh penyakit beserta relasi gejalanya.
3. Untuk setiap penyakit:
   - ambil daftar ID gejala milik penyakit,
   - hitung irisan antara gejala input user dan gejala penyakit,
   - hitung `jumlahCocok`,
   - hitung `persen = jumlahCocok / totalGejalaPenyakit * 100`.
4. Jika `jumlahCocok > 0`, penyakit dimasukkan ke daftar kandidat.
5. Setelah seluruh penyakit diproses:
   - jika daftar kosong, tampilkan error,
   - jika tidak kosong, urutkan dari `persen` tertinggi ke terendah.
6. Tampilkan daftar kemungkinan penyakit.

### 8.3 Rumus Identifikasi

- `jumlahCocok = jumlah gejala input yang ada pada penyakit`
- `persen = (jumlahCocok / total gejala penyakit) x 100`

### 8.4 Output

Daftar penyakit yang cocok berisi:

- objek penyakit,
- jumlah gejala yang cocok,
- total gejala penyakit,
- persen kecocokan.

## 9. Algoritma Preferensi Pengguna

Sebelum SAW dijalankan, sistem menyesuaikan bobot kriteria berdasarkan kebutuhan user.

### 9.1 Mode Preferensi

Mode yang tersedia:

- `seimbang`
- `efektif`
- `hemat`
- `aman`
- `custom`

### 9.2 Logika Penentuan Prioritas

1. Sistem mengambil bobot awal dari tabel kriteria.
2. Jika user memilih custom:
   - sistem gunakan skor prioritas yang diinput user untuk setiap kriteria.
3. Jika user memilih preset:
   - sistem memakai aturan keyword untuk menentukan prioritas otomatis.
4. Sistem hitung:
   - `bobot_final_raw = bobot_awal x prioritas_user`
5. Sistem jumlahkan seluruh `bobot_final_raw`.
6. Sistem normalisasi:
   - `bobot_final = bobot_final_raw / total_bobot_final_raw`

### 9.3 Output

Setiap kriteria menghasilkan:

- bobot awal,
- prioritas user,
- bobot final.

## 10. Algoritma SAW

Algoritma inti berada pada `App\Services\CertaintyFactorService`.

## 10.1 Tujuan

Menentukan ranking alternatif pupuk dan pestisida terbaik untuk penyakit tertentu berdasarkan data rating dan bobot kriteria.

## 10.2 Input

- ID penyakit
- daftar kriteria dan bobot final
- data alternatif pupuk atau pestisida
- data rating alternatif untuk penyakit dan kriteria terkait

## 10.3 Langkah SAW

### Langkah 1. Ambil data kriteria

Sistem mengambil seluruh kriteria dari database lalu membentuk bobot final berdasarkan preferensi user.

### Langkah 2. Ambil alternatif

- jika jenis = pupuk, ambil semua pupuk
- jika jenis = pestisida, ambil semua pestisida

### Langkah 3. Ambil rating

Sistem mengambil semua rating berdasarkan:

- penyakit terpilih,
- jenis alternatif,
- kriteria.

### Langkah 4. Bangun matriks keputusan

Setiap alternatif memiliki nilai `xij` untuk setiap kriteria.

### Langkah 5. Cari nilai maksimum dan minimum per kriteria

Untuk setiap kriteria:

- cari `max(xij)`
- cari `min(xij)`

### Langkah 6. Normalisasi

Jika kriteria bertipe `benefit`:

- `rij = xij / max(xij)`

Jika kriteria bertipe `cost`:

- `rij = min(xij) / xij`

### Langkah 7. Hitung nilai preferensi akhir

Untuk setiap alternatif:

- `Vi = Σ(wj x rij)`

Keterangan:

- `wj` = bobot final kriteria
- `rij` = nilai normalisasi
- `Vi` = nilai preferensi akhir

### Langkah 8. Ranking

1. Sistem urutkan alternatif berdasarkan `Vi` tertinggi.
2. Sistem beri nomor peringkat mulai dari 1.

### Langkah 9. Simpan hasil

Jika user login:

- simpan ke tabel `rekomendasi`,
- simpan ranking pupuk ke `detail_rekomendasi_pupuk`,
- simpan ranking pestisida ke `detail_rekomendasi_pestisida`.

Jika guest:

- hasil hanya disimpan di session sebagai preview.

## 10.4 Output SAW

Output per alternatif berisi:

- ID alternatif
- kode
- nama
- nilai `Vi`
- detail kontribusi setiap kriteria
- peringkat

## 11. Algoritma Riwayat Kasus Lama di Dashboard

Alur ringkas:

1. Sistem ambil daftar penyakit dengan jumlah rekomendasi terbanyak.
2. Sistem pilih hanya penyakit yang pernah memiliki rekomendasi.
3. Sistem ambil rekomendasi terbaru untuk masing-masing penyakit populer.
4. Sistem tampilkan sebagai kartu kasus lama.

Informasi yang tampil:

- nama penyakit,
- gambar penyakit,
- tanggal kasus,
- jumlah kemunculan,
- gejala terkait,
- pupuk yang pernah direkomendasikan,
- pestisida yang pernah direkomendasikan.

## 12. Saran Pemecahan Flowchart per Diagram

Agar flowchart lebih mudah dibuat, disarankan dipisah menjadi beberapa diagram:

1. Flowchart login, register, logout, dan redirect role
2. Flowchart dashboard publik dan dashboard user
3. Flowchart diagnosis penyakit
4. Flowchart proses rekomendasi SAW
5. Flowchart riwayat dan cetak hasil
6. Flowchart admin kelola penyakit dan gejala
7. Flowchart admin kelola pupuk dan pestisida
8. Flowchart admin kelola kriteria dan rating SAW
9. Flowchart admin kelola pengguna
10. Flowchart admin melihat riwayat rekomendasi

## 13. Simbol Flowchart yang Disarankan

Untuk menggambar flowchart, simbol yang disarankan:

- Terminator: mulai dan selesai
- Process: validasi, hitung kecocokan, hitung SAW, simpan data
- Input or Output: input gejala, input prioritas, tampil hasil
- Decision: login atau tidak, data valid atau tidak, penyakit cocok atau tidak
- Database: tabel penyakit, gejala, kriteria, rating, rekomendasi
- Connector: perpindahan antar subflow bila diagram dipisah

## 14. Ringkasan Paling Penting untuk Bab Flowchart

Jika ingin membuat versi singkat flowchart skripsi, urutan inti sistem adalah:

1. Pengguna membuka dashboard.
2. Pengguna memilih diagnosis.
3. Pengguna memilih gejala.
4. Sistem mengidentifikasi penyakit berdasarkan kecocokan gejala.
5. Pengguna memilih penyakit dan prioritas kebutuhan.
6. Sistem menghitung ranking pupuk dan pestisida dengan SAW.
7. Sistem menampilkan hasil rekomendasi.
8. Jika pengguna login, hasil disimpan ke riwayat.
9. Admin mengelola data master, bobot, dan rating agar perhitungan SAW tetap valid.

## 15. File Kode yang Paling Penting sebagai Referensi Flowchart

Jika sewaktu-waktu perlu menelusuri detail implementasi, file yang paling penting adalah:

- `routes/web.php`
- `app/Http/Controllers/Auth/AuthController.php`
- `app/Http/Controllers/User/DashboardController.php`
- `app/Http/Controllers/User/DiagnosisController.php`
- `app/Http/Controllers/User/RekomendasiController.php`
- `app/Http/Controllers/User/RiwayatController.php`
- `app/Http/Controllers/ProfileController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/PenyakitController.php`
- `app/Http/Controllers/Admin/GejalaController.php`
- `app/Http/Controllers/Admin/PupukController.php`
- `app/Http/Controllers/Admin/PestisidaController.php`
- `app/Http/Controllers/Admin/KriteriaController.php`
- `app/Http/Controllers/Admin/RatingController.php`
- `app/Http/Controllers/Admin/UserController.php`
- `app/Http/Controllers/Admin/RiwayatController.php`
- `app/Services/CertaintyFactorService.php`

## 16. Penutup

Dokumen ini dapat langsung dijadikan dasar untuk:

- pembuatan flowchart sistem keseluruhan,
- pembuatan flowchart per modul,
- penulisan analisis sistem pada skripsi,
- penjelasan algoritma identifikasi dan SAW.

Jika dibutuhkan, dokumen ini dapat dilanjutkan lagi menjadi:

- flowchart per halaman,
- DFD,
- use case diagram,
- activity diagram,
- sequence diagram.
