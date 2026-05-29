# Blackbox Testing — PadiCare

---

## A. GUEST (Pengunjung / Belum Login)

### A.1 Autentikasi

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
1	Halaman login	Guest membuka halaman login	Sistem menampilkan form login dengan input username & password	☐
2	Login admin berhasil	Guest mengisi username admin, password admin123	Sistem mengarahkan ke dashboard admin	☐
3	Login petani berhasil	Guest mengisi username badaruddin, password petani123	Sistem mengarahkan ke dashboard petani	☐
4	Login gagal username salah	Guest mengisi username tidak terdaftar	Sistem menampilkan pesan error login	☐
5	Login gagal password salah	Guest mengisi password salah	Sistem menampilkan pesan error login	☐
6	Login gagal field kosong	Guest mengosongkan username dan password	Sistem menampilkan validasi wajib isi	☐
7	Login admin endpoint	Guest mengakses POST login admin dengan data benar	Sistem mengarahkan ke dashboard admin	☐
8	Login admin dengan akun petani	Guest mencoba login admin menggunakan akun petani	Sistem menolak akses dengan pesan error	☐
9	Halaman register	Guest membuka halaman register	Sistem menampilkan form registrasi	☐
10	Register berhasil	Guest mengisi seluruh data registrasi dengan benar	Sistem berhasil membuat akun baru dan mengarahkan ke profil	☐
11	Register gagal username duplikat	Guest mendaftar dengan username yang sudah ada	Sistem menampilkan error username sudah digunakan	☐
12	Register gagal password pendek	Guest mengisi password kurang dari 6 karakter	Sistem menampilkan validasi minimal 6 karakter	☐
13	Register gagal konfirmasi tidak cocok	Guest mengisi konfirmasi password berbeda	Sistem menampilkan error konfirmasi tidak cocok	☐
14	Halaman lupa password	Guest membuka halaman lupa password	Sistem menampilkan form input email	☐
15	Logout	User menekan tombol logout	Sistem menghapus session dan mengarahkan ke halaman utama	☐

### A.2 Halaman Statis

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
16	Halaman utama	Guest membuka halaman utama sistem	Sistem menampilkan halaman publik	☐
17	Halaman tentang kami	Guest membuka halaman tentang kami	Sistem menampilkan informasi aplikasi	☐
18	Halaman bantuan	Guest membuka halaman bantuan	Sistem menampilkan panduan penggunaan	☐

### A.3 Diagnosis Penyakit (Publik)

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
19	Form diagnosis	Guest membuka halaman diagnosis	Sistem menampilkan semua gejala dalam grid	☐
20	Diagnosis tanpa gejala	Guest mengirim diagnosis tanpa memilih gejala	Sistem menampilkan error pilih minimal 1 gejala	☐
21	Diagnosis 1 gejala	Guest memilih 1 gejala dan menjalankan diagnosis	Sistem menampilkan hasil dengan skor CF	☐
22	Diagnosis banyak gejala	Guest memilih 3-5 gejala dan menjalankan diagnosis	Sistem menampilkan hasil dengan CF terkombinasi	☐
23	Diagnosis dengan bobot	Guest mengisi bobot keyakinan 0-100	Sistem menghitung CF sesuai bobot	☐
24	Diagnosis bobot tidak valid	Guest mengisi bobot di luar 0-100	Sistem menampilkan validasi error	☐
25	Akses hasil tanpa session	Guest mengakses halaman hasil tanpa diagnosis sebelumnya	Sistem mengarahkan kembali ke form diagnosis	☐
26	Proses rekomendasi	Guest mengisi preferensi dan menjalankan proses	Sistem mengarahkan ke halaman preview rekomendasi	☐
27	Proses tanpa penyakit	Guest mengirim proses tanpa memilih penyakit	Sistem menampilkan validasi pilih minimal 1 penyakit	☐
28	Proses luas lahan 0	Guest mengisi luas lahan 0	Sistem menampilkan validasi minimal 0.01	☐
29	Proses tipe tidak valid	Guest mengisi tipe preferensi tidak dikenal	Sistem menampilkan validasi tipe tidak valid	☐

### A.4 Preview Rekomendasi (Publik)

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
30	Preview rekomendasi	Guest membuka halaman preview setelah proses	Sistem menampilkan daftar pupuk & pestisida dengan skor CF	☐
31	Preview tanpa session	Guest membuka preview tanpa data session	Sistem menampilkan 404 Not Found	☐
32	Detail CF preview	Guest membuka halaman detail CF preview	Sistem menampilkan analisis CF per produk	☐
33	Cetak preview	Guest membuka halaman cetak preview	Sistem menampilkan tampilan cetak yang rapi	☐
34	Download preview	Guest membuka halaman cetak dengan parameter download	Sistem mengunduh file HTML	☐

---

## B. PETANI (Terautentikasi, role=petani)

### B.1 Dashboard

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
35	Dashboard petani	Petani membuka halaman dashboard	Sistem menampilkan statistik pribadi	☐
36	Akses dashboard admin	Petani membuka halaman dashboard admin	Sistem menampilkan 403 Forbidden	☐

### B.2 Riwayat Diagnosis

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
37	Riwayat diagnosis	Petani membuka halaman riwayat	Sistem menampilkan daftar diagnosis milik sendiri	☐
38	Detail riwayat	Petani membuka detail salah satu riwayat	Sistem menampilkan detail rekomendasi	☐
39	Detail CF riwayat	Petani membuka analisis CF riwayat	Sistem menampilkan perhitungan CF lengkap	☐
40	Cetak riwayat	Petani membuka halaman cetak riwayat	Sistem menampilkan tampilan cetak	☐
41	Download riwayat	Petani mengunduh riwayat	Sistem mengunduh file HTML	☐
42	Akses riwayat user lain	Petani mengubah ID riwayat milik orang lain	Sistem menampilkan 404 Not Found	☐

### B.3 Profil

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
43	Halaman profil	Petani membuka halaman profil	Sistem menampilkan form edit dengan data saat ini	☐
44	Update profil berhasil	Petani mengubah nama dan alamat	Sistem menyimpan perubahan	☐
45	Update username duplikat	Petani mengganti username dengan yang sudah ada	Sistem menampilkan error username digunakan	☐
46	Upload foto profil	Petani mengunggah foto ukuran sesuai	Sistem menyimpan dan menampilkan foto baru	☐
47	Upload foto terlalu besar	Petani mengunggah foto lebih dari 2MB	Sistem menampilkan error maksimal 2MB	☐
48	Upload format salah	Petani mengunggah file bukan gambar	Sistem menampilkan error format tidak didukung	☐
49	Ganti password berhasil	Petani mengganti password dengan benar	Sistem menyimpan password baru	☐
50	Ganti password lama salah	Petani mengisi password lama tidak cocok	Sistem menampilkan error password lama salah	☐
51	Ganti email	Petani mengganti alamat email	Sistem mereset verifikasi dan mengirim email	☐

### B.4 Verifikasi Email

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
52	Kirim ulang verifikasi	Petani meminta verifikasi email	Sistem mengirim email verifikasi	☐
53	Verifikasi via link	Petani mengklik link verifikasi dari email	Sistem menandai email sebagai terverifikasi	☐
54	Rate limit verifikasi	Petani meminta verifikasi lebih dari 6 kali	Sistem menampilkan error batas permintaan	☐

---

## C. ADMIN (Terautentikasi, role=admin)

### C.1 Dashboard

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
55	Dashboard admin	Admin membuka halaman dashboard	Sistem menampilkan statistik lengkap	☐
56	Akses dashboard petani	Admin membuka halaman dashboard petani	Sistem menampilkan 403 Forbidden	☐

### C.2 CRUD Penyakit

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
57	Daftar penyakit	Admin membuka halaman penyakit	Sistem menampilkan tabel penyakit	☐
58	Form tambah penyakit	Admin membuka form tambah penyakit	Sistem menampilkan form dengan gejala & field MB/MD	☐
59	Tambah penyakit berhasil	Admin mengisi data penyakit lengkap	Sistem menyimpan penyakit baru	☐
60	Tambah tanpa nama	Admin mengosongkan field nama	Sistem menampilkan validasi nama wajib diisi	☐
61	Tambah dengan MB tidak valid	Admin mengisi MB lebih dari 1	Sistem menampilkan validasi MB 0-1	☐
62	Tambah dengan MD tidak valid	Admin mengisi MD kurang dari 0	Sistem menampilkan validasi MD 0-1	☐
63	Edit penyakit	Admin mengubah data penyakit	Sistem menyimpan perubahan	☐
64	Hapus penyakit	Admin menghapus penyakit	Sistem menghapus penyakit dan mengarahkan ke daftar	☐

### C.3 CRUD Gejala

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
65	Daftar gejala	Admin membuka halaman gejala	Sistem menampilkan tabel gejala	☐
66	Tambah gejala berhasil	Admin mengisi nama gejala	Sistem menyimpan gejala dengan kode otomatis	☐
67	Tambah nama terlalu panjang	Admin mengisi nama lebih dari 200 karakter	Sistem menampilkan validasi maksimal 200	☐
68	Edit gejala	Admin mengubah nama gejala	Sistem menyimpan perubahan	☐
69	Hapus gejala	Admin menghapus gejala	Sistem menghapus gejala dan relasinya	☐

### C.4 CRUD Pupuk

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
70	Daftar pupuk	Admin membuka halaman pupuk	Sistem menampilkan tabel pupuk	☐
71	Tambah pupuk berhasil	Admin mengisi data pupuk lengkap	Sistem menyimpan pupuk baru	☐
72	Tambah harga negatif	Admin mengisi harga minus	Sistem menampilkan validasi minimal 0	☐
73	Tambah dosis 0	Admin mengisi dosis 0	Sistem menampilkan validasi minimal 0.01	☐
74	Edit pupuk	Admin mengubah data pupuk	Sistem menyimpan perubahan	☐
75	Hapus pupuk	Admin menghapus pupuk	Sistem menghapus pupuk dan relasinya	☐

### C.5 CRUD Pestisida

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
76	Daftar pestisida	Admin membuka halaman pestisida	Sistem menampilkan tabel pestisida	☐
77	Tambah pestisida berhasil	Admin mengisi data pestisida lengkap	Sistem menyimpan pestisida baru	☐
78	Tambah jenis tidak valid	Admin mengisi jenis tidak dikenal	Sistem menampilkan validasi jenis tidak valid	☐
79	Edit pestisida	Admin mengubah data pestisida	Sistem menyimpan perubahan	☐
80	Hapus pestisida	Admin menghapus pestisida	Sistem menghapus pestisida	☐

### C.6 Nilai CF Pakar

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
81	Form CF pupuk	Admin membuka halaman CF pupuk	Sistem menampilkan matriks penyakit x pupuk	☐
82	Simpan CF pupuk	Admin mengubah nilai MB/MD pupuk	Sistem menyimpan nilai CF ke database	☐
83	CF desimal berlebih	Admin mengisi MB dengan 4 desimal	Sistem menampilkan validasi maksimal 3 desimal	☐
84	Form CF pestisida	Admin membuka halaman CF pestisida	Sistem menampilkan matriks penyakit x pestisida	☐
85	Simpan CF pestisida	Admin mengubah nilai MB/MD pestisida	Sistem menyimpan nilai CF ke database	☐

### C.7 Preferensi Sistem

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
86	Halaman kriteria	Admin membuka halaman preferensi	Sistem menampilkan form budget & confidence	☐
87	Update kriteria berhasil	Admin mengisi data preferensi valid	Sistem menyimpan preferensi	☐
88	Seimbang lebih kecil dari hemat	Admin mengisi batas seimbang < hemat	Sistem menampilkan error validasi	☐
89	Confidence di luar range	Admin mengisi confidence lebih dari 1	Sistem menampilkan validasi 0-1	☐

### C.8 Manajemen Petani

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
90	Daftar petani	Admin membuka halaman pengguna	Sistem menampilkan tabel petani	☐
91	Reset password	Admin mereset password petani	Sistem mengubah password menjadi petani123	☐
92	Hapus petani	Admin menghapus akun petani	Sistem menghapus akun petani	☐
93	Hapus admin	Admin mencoba menghapus akun admin	Sistem menampilkan 403 Forbidden	☐
94	Verifikasi email petani	Admin memverifikasi email petani	Sistem menandai email sebagai terverifikasi	☐
95	Verifikasi tanpa email	Admin memverifikasi petani tanpa alamat email	Sistem menampilkan error tidak memiliki email	☐

### C.9 Riwayat Diagnosis (Admin)

No	Fitur yang Diuji	Skenario Uji	Hasil yang Diharapkan	Hasil
96	Semua riwayat	Admin membuka halaman riwayat	Sistem menampilkan semua rekomendasi dari semua user	☐
97	Filter tanggal	Admin memfilter riwayat berdasarkan rentang tanggal	Sistem menampilkan riwayat sesuai tanggal	☐
98	Filter user	Admin memfilter berdasarkan pengguna	Sistem menampilkan riwayat user tertentu	☐
99	Filter status	Admin memfilter berdasarkan kelengkapan data	Sistem menampilkan riwayat sesuai status	☐
100	Detail riwayat	Admin membuka detail riwayat	Sistem menampilkan informasi lengkap	☐
101	Detail CF riwayat	Admin membuka analisis CF	Sistem menampilkan perhitungan CF	☐
102	Cetak riwayat	Admin membuka halaman cetak	Sistem menampilkan tampilan cetak	☐
103	Download riwayat	Admin mengunduh riwayat	Sistem mengunduh file HTML	☐
104	Riwayat tidak ditemukan	Admin membuka ID riwayat yang tidak ada	Sistem menampilkan 404 Not Found	☐

---

## Ringkasan

Aktor	Jumlah Uji
GUEST (publik)	34 kasus (No 1-34)
PETANI (terautentikasi)	20 kasus (No 35-54)
ADMIN (terautentikasi)	50 kasus (No 55-104)
Total	104 kasus uji
