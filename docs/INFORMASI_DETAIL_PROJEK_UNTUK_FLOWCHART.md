# Informasi Detail Projek PadiCare untuk Flowchart

Dokumen ini berisi rangkuman detail projek PadiCare yang dapat digunakan sebagai bahan pembuatan flowchart, swimlane diagram, activity diagram, atau alur sistem end-to-end.

## Identitas Projek

| Item | Keterangan |
|---|---|
| Nama sistem | PadiCare |
| Jenis sistem | Sistem pakar dan sistem pendukung keputusan rekomendasi perawatan penyakit padi |
| Framework | Laravel |
| Pengguna utama | Guest, petani, admin |
| Fokus sistem | Diagnosis penyakit padi berdasarkan gejala dan rekomendasi pupuk serta pestisida |
| Metode utama | Certainty Factor |
| Penyimpanan | Database relasional dan session Laravel |

## Tujuan Sistem

PadiCare dibuat untuk membantu petani mengenali kemungkinan penyakit tanaman padi berdasarkan gejala yang dipilih. Setelah penyakit teridentifikasi, sistem memberikan rekomendasi pupuk dan pestisida yang sesuai dengan penyakit tersebut.

Sistem juga menyediakan fitur riwayat rekomendasi bagi petani yang login, serta fitur manajemen data master bagi admin agar data penyakit, gejala, pupuk, pestisida, dan aturan rekomendasi dapat diperbarui.

## Aktor Sistem

### 1. Guest

Guest adalah pengguna yang belum login. Guest tetap dapat menggunakan fitur utama sistem, tetapi hasilnya hanya bersifat sementara.

Aktivitas guest:

- Membuka beranda.
- Membuka halaman diagnosis.
- Memilih gejala tanaman padi.
- Mengisi bobot keyakinan gejala jika tersedia.
- Melihat hasil identifikasi penyakit.
- Memilih preferensi rekomendasi.
- Melihat preview rekomendasi pupuk dan pestisida.
- Mencetak preview rekomendasi.
- Login atau register jika ingin menyimpan hasil.

Data guest tidak disimpan permanen ke database sebagai riwayat. Sistem menyimpan hasil diagnosis dan preview rekomendasi sementara di session.

### 2. Petani

Petani adalah user yang sudah memiliki akun dan login dengan role `petani`.

Aktivitas petani:

- Register akun.
- Login.
- Mengelola profil.
- Melakukan verifikasi email.
- Melakukan diagnosis penyakit padi.
- Melihat hasil identifikasi penyakit.
- Memilih preferensi rekomendasi.
- Mendapatkan rekomendasi pupuk dan pestisida.
- Menyimpan hasil rekomendasi ke database.
- Melihat riwayat rekomendasi.
- Melihat detail rekomendasi.
- Mencetak hasil rekomendasi.
- Logout.

Perbedaan utama petani dengan guest adalah hasil rekomendasi petani disimpan ke database sehingga dapat dilihat kembali melalui menu riwayat.

### 3. Admin

Admin adalah user yang login dengan role `admin` dan bertugas mengelola data sistem.

Aktivitas admin:

- Login sebagai admin.
- Membuka dashboard admin.
- Mengelola data penyakit.
- Mengelola data gejala.
- Mengelola relasi penyakit dan gejala.
- Mengelola data pupuk.
- Mengelola data pestisida.
- Mengelola data kriteria.
- Mengelola rating pupuk.
- Mengelola rating pestisida.
- Mengelola user petani.
- Memverifikasi email user secara manual.
- Reset password user.
- Melihat seluruh riwayat rekomendasi.
- Melihat detail riwayat rekomendasi.
- Mencetak riwayat rekomendasi.
- Logout.

### 4. Sistem

Sistem menjadi penghubung antara user, admin, session, dan database.

Tugas sistem:

- Menampilkan halaman publik.
- Memvalidasi register dan login.
- Mengecek autentikasi dan role.
- Mengarahkan user sesuai role.
- Menyimpan dan membaca data dari database.
- Menyimpan data sementara ke session untuk guest.
- Mengambil data gejala dan penyakit.
- Memproses diagnosis penyakit menggunakan Certainty Factor.
- Mengambil relasi penyakit dengan pupuk dan pestisida.
- Menghitung rekomendasi pupuk dan pestisida.
- Mengurutkan rekomendasi berdasarkan nilai CF.
- Menyimpan hasil rekomendasi bagi petani yang login.
- Menampilkan detail, preview, dan halaman cetak.

## Modul Utama Sistem

| Modul | Fungsi |
|---|---|
| Auth | Login, register, logout, reset password |
| Profile | Update profil dan verifikasi email |
| Dashboard user | Halaman awal petani atau guest |
| Diagnosis | Pemilihan gejala dan identifikasi penyakit |
| Rekomendasi | Perhitungan rekomendasi pupuk dan pestisida |
| Riwayat user | Riwayat hasil rekomendasi milik petani |
| Dashboard admin | Ringkasan data dan akses menu admin |
| Master penyakit | CRUD penyakit padi |
| Master gejala | CRUD gejala penyakit padi |
| Master pupuk | CRUD data pupuk |
| Master pestisida | CRUD data pestisida |
| Kriteria | Pengelolaan kriteria pendukung rekomendasi |
| Rating | Pengelolaan rating pupuk dan pestisida |
| Manajemen user | Kelola user petani |
| Riwayat admin | Melihat seluruh riwayat rekomendasi |

## Data Utama dalam Database

| Data | Isi |
|---|---|
| users | Akun petani dan admin, profil, password, role, status verifikasi email |
| penyakit | Kode, nama penyakit, deskripsi, gambar, informasi penyakit |
| gejala | Kode, nama gejala, gambar |
| penyakit_gejala | Relasi penyakit dan gejala, nilai MB, MD |
| pupuk | Kode, nama, kandungan, fungsi, harga, satuan, takaran, cara aplikasi |
| pestisida | Kode, nama, jenis, bahan aktif, dosis, harga, cara aplikasi |
| penyakit_pupuk | Relasi penyakit dan pupuk, nilai MB, MD |
| penyakit_pestisida | Relasi penyakit dan pestisida, nilai MB, MD |
| kriteria | Kode, nama, jenis benefit/cost, bobot |
| rating_pupuk | Nilai pupuk terhadap kriteria dan penyakit |
| rating_pestisida | Nilai pestisida terhadap kriteria dan penyakit |
| rekomendasi | Hasil rekomendasi milik user login |
| detail_rekomendasi_pupuk | Detail pupuk yang direkomendasikan, nilai, peringkat |
| detail_rekomendasi_pestisida | Detail pestisida yang direkomendasikan, nilai, peringkat |

## Data Session

Session digunakan terutama untuk guest dan proses sementara.

| Session | Fungsi |
|---|---|
| diagnosis_result | Menyimpan hasil diagnosis sementara setelah user memilih gejala |
| guest_rekomendasi | Menyimpan preview rekomendasi untuk guest atau batch hasil yang baru dihitung |

## Alur Login dan Role

1. User membuka halaman login.
2. User mengisi data login.
3. Sistem memvalidasi input.
4. Sistem mengecek akun pada database.
5. Jika login gagal, user kembali ke halaman login.
6. Jika login berhasil, sistem mengecek role.
7. Jika role `petani`, user diarahkan ke dashboard user.
8. Jika role `admin`, user diarahkan ke dashboard admin.

## Alur Register Petani

1. Guest membuka halaman register.
2. Guest mengisi data akun.
3. Sistem memvalidasi data register.
4. Sistem membuat akun baru dengan role petani.
5. Data akun disimpan ke tabel `users`.
6. User dapat login sebagai petani.
7. User dapat melakukan verifikasi email melalui fitur profil.

## Alur Diagnosis Penyakit

1. User atau guest membuka halaman diagnosis.
2. Sistem mengambil daftar gejala dari database.
3. User memilih minimal satu gejala.
4. User dapat mengisi bobot keyakinan untuk gejala.
5. Sistem memvalidasi gejala yang dipilih.
6. Sistem menghapus session diagnosis lama agar hasil tidak tercampur.
7. Sistem mengambil data penyakit dan relasi gejala.
8. Sistem mencocokkan gejala input dengan gejala pada setiap penyakit.
9. Jika tidak ada gejala yang cocok, penyakit tidak diproses.
10. Jika ada gejala yang cocok, sistem menghitung nilai Certainty Factor.
11. Sistem mengurutkan penyakit berdasarkan confidence tertinggi.
12. Sistem menyimpan hasil diagnosis ke session `diagnosis_result`.
13. User diarahkan ke halaman hasil identifikasi.

## Logika Perhitungan Diagnosis

Diagnosis menggunakan metode Certainty Factor.

Rumus dasar:

```text
CF = MB - MD
```

Keterangan:

- `MB` adalah Measure of Belief atau tingkat keyakinan pakar.
- `MD` adalah Measure of Disbelief atau tingkat ketidakyakinan pakar.
- `CF` berada pada rentang -1 sampai 1.

Jika satu penyakit memiliki beberapa gejala yang cocok, nilai CF digabungkan secara bertahap.

Rumus kombinasi CF positif:

```text
CFcombine = CF1 + CF2 * (1 - CF1)
```

Sistem juga memperhitungkan kelengkapan gejala. Semakin banyak gejala penyakit yang cocok dengan input user, semakin kuat hasil diagnosis.

Output diagnosis:

- Penyakit yang cocok.
- Jumlah gejala cocok.
- Total gejala penyakit.
- Nilai confidence.
- Persentase keyakinan.
- Detail gejala yang cocok.
- Interpretasi seperti rendah, sedang, tinggi, atau sangat tinggi.

## Alur Hasil Identifikasi

1. Sistem membaca session `diagnosis_result`.
2. Jika session tidak ada, user dikembalikan ke halaman diagnosis.
3. Sistem menampilkan daftar penyakit hasil diagnosis.
4. Sistem menampilkan gejala yang dipilih.
5. Sistem menampilkan preset preferensi rekomendasi.
6. User memilih penyakit hasil identifikasi dan preferensi rekomendasi.
7. User melanjutkan ke proses rekomendasi.

## Preferensi Rekomendasi

Sistem menyediakan beberapa jenis preferensi:

| Preferensi | Makna |
|---|---|
| Seimbang | Rekomendasi standar berdasarkan nilai CF |
| Hemat | Rekomendasi memberi penyesuaian pada produk yang lebih murah |
| Efisiensi | Rekomendasi memberi penyesuaian pada produk dengan keyakinan pakar tinggi |

Preferensi mempengaruhi urutan rekomendasi, tetapi basis utama rekomendasi tetap relasi penyakit dengan pupuk atau pestisida.

## Alur Rekomendasi Pupuk dan Pestisida

1. User memilih penyakit hasil identifikasi.
2. User memilih preferensi rekomendasi.
3. Sistem memvalidasi id penyakit, gejala terpilih, dan preferensi.
4. Sistem mengambil data penyakit dari database.
5. Sistem mengambil relasi penyakit-pupuk dan penyakit-pestisida.
6. Sistem menghitung CF rekomendasi untuk setiap pupuk.
7. Sistem menghitung CF rekomendasi untuk setiap pestisida.
8. Produk dengan CF negatif atau nol tidak ditampilkan sebagai rekomendasi.
9. Sistem menyesuaikan nilai rekomendasi berdasarkan preferensi user.
10. Sistem mengurutkan produk berdasarkan CF tertinggi.
11. Sistem memberi peringkat pada setiap rekomendasi.
12. Jika user adalah guest, hasil disimpan ke session.
13. Jika user login sebagai petani, hasil disimpan ke database.
14. Sistem menampilkan preview rekomendasi.

## Logika Rekomendasi

Rekomendasi dibuat berdasarkan penyakit, bukan langsung berdasarkan gejala.

Basis data aturan:

- `penyakit_pupuk` untuk menentukan pupuk yang sesuai dengan penyakit.
- `penyakit_pestisida` untuk menentukan pestisida yang efektif terhadap penyakit.

Rumus:

```text
CF rekomendasi = MB - MD
```

Interpretasi:

- CF positif berarti produk cocok atau efektif.
- CF nol berarti netral.
- CF negatif berarti produk tidak direkomendasikan.

Sistem hanya menampilkan pupuk dan pestisida dengan CF lebih dari 0.01.

## Alur Guest Setelah Rekomendasi

1. Guest melihat preview rekomendasi.
2. Guest dapat melihat detail preview.
3. Guest dapat mencetak preview.
4. Jika guest ingin menyimpan hasil, guest harus login atau register.
5. Jika tidak login, hasil hanya tersimpan sementara di session dan tidak masuk riwayat database.

## Alur Petani Setelah Rekomendasi

1. Petani melihat preview rekomendasi.
2. Sistem menyimpan hasil ke tabel `rekomendasi`.
3. Detail pupuk disimpan ke `detail_rekomendasi_pupuk`.
4. Detail pestisida disimpan ke `detail_rekomendasi_pestisida`.
5. Petani dapat membuka halaman riwayat.
6. Petani dapat melihat detail hasil rekomendasi.
7. Petani dapat mencetak laporan rekomendasi.

## Alur Riwayat User

1. Petani login.
2. Petani membuka menu riwayat.
3. Sistem mengambil data rekomendasi berdasarkan `id_user`.
4. Sistem menampilkan daftar riwayat.
5. Petani memilih salah satu riwayat.
6. Sistem mengambil detail penyakit, pupuk, dan pestisida.
7. Sistem menampilkan detail atau halaman cetak.

## Alur Admin Mengelola Data Master

1. Admin login.
2. Sistem mengecek role admin.
3. Admin masuk dashboard admin.
4. Admin memilih menu master data.
5. Admin menambah, mengubah, atau menghapus data.
6. Sistem memvalidasi input admin.
7. Sistem menyimpan perubahan ke database.
8. Data baru digunakan pada proses diagnosis dan rekomendasi.

## Alur Admin Melihat Riwayat

1. Admin membuka menu riwayat.
2. Sistem mengambil seluruh data rekomendasi dari database.
3. Admin melihat daftar riwayat rekomendasi semua petani.
4. Admin membuka detail riwayat.
5. Sistem menampilkan penyakit, gejala, pupuk, pestisida, nilai, dan peringkat.
6. Admin dapat mencetak riwayat.

## Titik Keputusan untuk Flowchart

Titik keputusan yang penting dimasukkan dalam flowchart:

| Keputusan | Cabang |
|---|---|
| Status pengguna | Guest, petani login, admin login |
| Login valid? | Ya, tidak |
| Role user? | Petani, admin |
| Gejala dipilih? | Ya, tidak |
| Penyakit ditemukan? | Ya, tidak |
| User sudah login? | Guest, petani |
| Ingin menyimpan hasil? | Ya, tidak |
| Preferensi rekomendasi | Seimbang, hemat, efisiensi |
| Admin memilih menu | Penyakit, gejala, pupuk, pestisida, kriteria, rating, user, riwayat |

## Input dan Output Tiap Proses

| Proses | Input | Output |
|---|---|---|
| Register | Data akun petani | Akun petani baru |
| Login | Username/email/telepon dan password | Session login dan role |
| Update profil | Data profil | Profil tersimpan |
| Pilih gejala | Daftar id gejala dan bobot | Data input diagnosis |
| Diagnosis | Gejala terpilih, MB, MD | Daftar penyakit dengan nilai CF |
| Pilih preferensi | Penyakit, gejala, preferensi | Parameter rekomendasi |
| Rekomendasi | Penyakit, relasi produk, preferensi | Daftar pupuk dan pestisida |
| Simpan riwayat | User, penyakit, rekomendasi | Data rekomendasi permanen |
| Cetak | Data rekomendasi | Halaman laporan |
| Kelola master | Data admin | Data master tersimpan |

## Rancangan Swimlane yang Disarankan

Flowchart dapat dibuat dengan swimlane berikut:

1. User atau Guest
2. Sistem
3. Admin
4. Database

Pembagian ini cocok karena:

- User dan guest melakukan input diagnosis dan melihat hasil.
- Sistem menjalankan validasi, autentikasi, diagnosis, rekomendasi, session, dan redirect.
- Admin mengelola data master dan riwayat.
- Database menjadi pusat penyimpanan permanen.

## Ringkasan Alur End-to-End

Alur dimulai ketika pengguna membuka beranda. Guest dapat langsung melakukan diagnosis tanpa login. Sistem mengambil daftar gejala dari database, kemudian guest memilih gejala yang dialami tanaman padi. Sistem mencocokkan gejala tersebut dengan data penyakit dan menghitung nilai Certainty Factor. Jika penyakit ditemukan, sistem menampilkan hasil identifikasi.

Setelah itu, user memilih preferensi rekomendasi. Sistem mengambil data relasi penyakit dengan pupuk dan pestisida, menghitung CF rekomendasi, memfilter produk yang tidak sesuai, mengurutkan hasil, lalu menampilkan preview rekomendasi.

Jika pengguna masih guest, hasil hanya disimpan sementara di session. Jika pengguna login sebagai petani, sistem menyimpan hasil ke database sehingga dapat dilihat kembali melalui riwayat. Admin berperan mengelola data penyakit, gejala, produk, kriteria, rating, user, dan riwayat. Semua data master yang dikelola admin menjadi dasar proses diagnosis dan rekomendasi.

