# Flowchart Swimlane Sistem PadiCare

Dokumen ini menggambarkan alur sistem PadiCare berdasarkan struktur proyek Laravel yang ada. Flowchart dibagi menjadi tiga swimlane utama, yaitu **User**, **Sistem**, dan **Admin**, dengan satu **Database** sebagai pusat penyimpanan data.

Flowchart ini juga memasukkan alur **guest user**, yaitu pengguna yang belum login tetapi tetap dapat melakukan diagnosis dan melihat preview rekomendasi.

## Flowchart Utama

```mermaid
flowchart LR
    subgraph USER["SWIMLANE USER / GUEST / PETANI"]
        U0([Mulai])
        U1[Buka beranda / dashboard user]
        U2{Status pengguna?}
        U3[Guest membuka fitur diagnosis]
        U4[Register akun]
        U5[Login sebagai petani]
        U6[Petani masuk dashboard]
        U7[Kelola profil]
        U8[Verifikasi email]
        U9[Pilih gejala tanaman padi]
        U10[Lihat hasil identifikasi penyakit]
        U11[Pilih preferensi rekomendasi]
        U12[Lihat preview rekomendasi]
        U13{Ingin menyimpan hasil?}
        U14[Login / register terlebih dahulu]
        U15[Lihat riwayat rekomendasi]
        U16[Lihat detail / cetak hasil]
        U17[Logout]
        U18([Selesai])
    end

    subgraph SISTEM["SWIMLANE SISTEM"]
        S1[Tampilkan halaman publik]
        S2[Validasi data register / login]
        S3[Autentikasi user dan cek role]
        S4[Arahkan sesuai role]
        S5[Validasi dan update profil]
        S6[Kirim link verifikasi email]
        S7[Validasi pilihan gejala]
        S8[Ambil data gejala dan penyakit]
        S9[Proses identifikasi penyakit]
        S10{Penyakit ditemukan?}
        S11[Tampilkan pesan tidak ditemukan]
        S12[Simpan hasil identifikasi sementara di session]
        S13[Validasi preferensi rekomendasi]
        S14[Ambil data pendukung rekomendasi]
        S15[Proses rekomendasi pupuk dan pestisida]
        S16{User sudah login?}
        S17[Simpan preview guest di session]
        S18[Simpan hasil rekomendasi ke database]
        S19[Tampilkan preview hasil]
        S20[Ambil riwayat user]
        S21[Tampilkan detail / halaman cetak]
        S22[Hapus session login]
    end

    subgraph ADMIN["SWIMLANE ADMIN"]
        A1[Login sebagai admin]
        A2[Masuk dashboard admin]
        A3[Kelola data penyakit]
        A4[Kelola data gejala]
        A5[Kelola data pupuk]
        A6[Kelola data pestisida]
        A7[Kelola kriteria]
        A8[Kelola rating pupuk]
        A9[Kelola rating pestisida]
        A10[Kelola user petani]
        A11[Lihat seluruh riwayat rekomendasi]
        A12[Logout admin]
    end

    DB[(DATABASE TUNGGAL)]

    U0 --> U1 --> S1 --> U2

    U2 -->|Guest| U3 --> U9
    U2 -->|Belum punya akun| U4 --> S2
    U2 -->|Sudah punya akun| U5 --> S2

    S2 --> DB
    DB --> S3
    S3 --> S4
    S4 -->|Role petani| U6
    S4 -->|Role admin| A2
    S4 -->|Login gagal / data tidak valid| U1

    U6 --> U7 --> S5 --> DB
    U7 --> U8 --> S6 --> DB

    U9 --> S7 --> S8 --> DB
    DB --> S9 --> S10
    S10 -->|Tidak| S11 --> U9
    S10 -->|Ya| S12 --> U10

    U10 --> U11 --> S13 --> S14 --> DB
    DB --> S15 --> S16
    S16 -->|Guest| S17 --> S19 --> U12
    S16 -->|Petani login| S18 --> DB
    DB --> S19 --> U12

    U12 --> U13
    U13 -->|Ya, tetapi masih guest| U14 --> U4
    U13 -->|Tidak / hanya preview| U18
    U13 -->|Sudah tersimpan| U15

    U15 --> S20 --> DB
    DB --> U15 --> U16 --> S21
    S21 --> U18
    U17 --> S22 --> U18

    A1 --> S2
    A2 --> A3 --> DB
    A2 --> A4 --> DB
    A2 --> A5 --> DB
    A2 --> A6 --> DB
    A2 --> A7 --> DB
    A2 --> A8 --> DB
    A2 --> A9 --> DB
    A2 --> A10 --> DB
    A2 --> A11 --> DB
    DB --> A11
    A12 --> S22
```

## Penjelasan Alur

### 1. Alur Guest User

Guest user adalah pengguna yang belum login. Pada proyek ini, guest tetap dapat membuka halaman utama, masuk ke fitur diagnosis, memilih gejala tanaman padi, melihat hasil identifikasi penyakit, memilih preferensi rekomendasi, dan melihat preview rekomendasi.

Perbedaan utamanya adalah hasil guest tidak disimpan permanen ke database. Sistem hanya menyimpan hasil sementara di session. Jika guest ingin menyimpan hasil diagnosis ke riwayat, pengguna harus register atau login sebagai petani terlebih dahulu.

### 2. Alur User Login / Petani

Petani dapat melakukan register, login, mengelola profil, dan melakukan verifikasi email. Setelah login, petani dapat menjalankan diagnosis seperti guest, tetapi hasil rekomendasi akan disimpan ke database.

Hasil yang sudah tersimpan dapat dibuka kembali melalui menu riwayat, dilihat detailnya, dan dicetak sebagai laporan rekomendasi pupuk serta pestisida.

### 3. Alur Sistem

Sistem bertugas sebagai penghubung antara user, admin, dan database. Sistem melakukan validasi input, autentikasi, pengecekan role, pengambilan data gejala dan penyakit, pemrosesan identifikasi penyakit, pemrosesan rekomendasi pupuk dan pestisida, penyimpanan session untuk guest, serta penyimpanan riwayat untuk user login.

Pada flowchart ini, proses perhitungan dibuat secara umum sebagai "proses identifikasi" dan "proses rekomendasi", tanpa memasukkan rumus atau detail perhitungan.

### 4. Alur Admin

Admin masuk melalui login admin dan diarahkan ke dashboard admin. Dari dashboard, admin dapat mengelola data master yang dibutuhkan sistem, yaitu penyakit, gejala, pupuk, pestisida, kriteria, rating pupuk, rating pestisida, user petani, dan riwayat rekomendasi.

Semua perubahan dari admin langsung berhubungan dengan database tunggal. Data inilah yang digunakan sistem saat user melakukan diagnosis dan meminta rekomendasi.

## Database Tunggal

Database pada flowchart ini dibuat sebagai satu pusat data. Database menyimpan data berikut:

| Kelompok Data | Isi Data |
|---|---|
| User | akun petani, akun admin, profil, password, status verifikasi email |
| Data penyakit | daftar penyakit padi dan informasinya |
| Data gejala | daftar gejala dan relasi gejala dengan penyakit |
| Data produk | data pupuk dan pestisida |
| Data pendukung rekomendasi | kriteria, rating pupuk, rating pestisida |
| Data hasil | rekomendasi, detail rekomendasi pupuk, detail rekomendasi pestisida |

## Ringkasan Alur End-to-End

Alur sistem dimulai ketika pengguna membuka beranda. Jika pengguna belum login, pengguna tetap bisa melakukan diagnosis sebagai guest dan melihat preview rekomendasi. Sistem mengambil data gejala dan penyakit dari database, memproses identifikasi, lalu menghasilkan rekomendasi berdasarkan data pendukung yang sudah dikelola admin. Untuk guest, hasil hanya disimpan sementara di session.

Jika pengguna login sebagai petani, hasil rekomendasi disimpan ke database sehingga dapat dilihat kembali melalui riwayat. Di sisi lain, admin bertugas menyiapkan dan memperbarui data master, data kriteria, rating, user, serta memantau riwayat rekomendasi. Dengan demikian, user memberikan input, sistem memproses keputusan, admin mengelola data, dan database tunggal menjadi pusat penyimpanan seluruh data permanen.
