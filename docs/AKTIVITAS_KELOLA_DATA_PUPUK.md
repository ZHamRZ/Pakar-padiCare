# Informasi Activity Diagram Kelola Data Pupuk

Dokumen ini menjelaskan alur aktivitas **Kelola Data Pupuk** pada sistem PadiCare berdasarkan implementasi Laravel yang ada pada `Admin\PupukController`.

## Identitas Aktivitas

| Item | Keterangan |
|---|---|
| Nama aktivitas | Kelola Data Pupuk |
| Aktor utama | Admin |
| Modul | Admin - Master Data Pupuk |
| Controller | `App\Http\Controllers\Admin\PupukController` |
| Model | `App\Models\Pupuk` |
| Tabel database | `pupuk` |
| Prefix route | `/admin/pupuk` |
| Hak akses | User login dengan role `admin` |

## Tujuan Aktivitas

Aktivitas ini digunakan admin untuk mengelola data pupuk yang dipakai sistem sebagai dasar rekomendasi perawatan penyakit padi. Data pupuk mencakup nama, kode, kandungan, fungsi, harga, satuan, takaran, cara aplikasi, jadwal aplikasi, frekuensi, efek penggunaan, dan gambar produk.

## Swimlane yang Disarankan

Activity diagram dapat dibuat dengan swimlane berikut:

| Swimlane | Peran |
|---|---|
| Admin | Membuka menu, memilih aksi, mengisi form, menyimpan, mengubah, atau menghapus data |
| Sistem | Menampilkan halaman, membuat kode otomatis, memvalidasi input, memproses upload gambar, memberi pesan sukses/error |
| Database | Menyimpan, membaca, memperbarui, dan menghapus data pupuk |

## Data yang Dikelola

| Field | Keterangan | Validasi Utama |
|---|---|---|
| kode | Kode pupuk | Otomatis dibuat sistem, wajib, unik, maksimal 10 karakter |
| nama | Nama pupuk | Wajib, maksimal 100 karakter |
| kandungan | Kandungan singkat pupuk | Opsional, maksimal 200 karakter |
| kandungan_detail | Detail kandungan pupuk | Opsional |
| fungsi_utama | Fungsi utama pupuk | Opsional |
| takaran | Takaran penggunaan | Opsional, maksimal 255 karakter |
| efek_penggunaan | Efek penggunaan pupuk | Opsional |
| cara_aplikasi | Cara aplikasi pupuk | Opsional |
| jadwal_umur_aplikasi | Jadwal atau umur aplikasi | Opsional |
| frekuensi_aplikasi | Frekuensi aplikasi | Opsional |
| harga_per_kg | Harga pupuk per kg | Wajib, numerik, minimal 0 |
| satuan | Satuan harga/produk | Wajib, maksimal 20 karakter |
| gambar | Gambar pupuk | Opsional, jpg/jpeg/png/webp, maksimal 2 MB |

## Alur Utama Menampilkan Data Pupuk

1. Admin login ke sistem.
2. Sistem memeriksa autentikasi dan role admin.
3. Admin membuka menu **Kelola Data Pupuk**.
4. Sistem mengambil data pupuk dari database.
5. Sistem mengurutkan data berdasarkan `kode`.
6. Sistem menampilkan daftar data pupuk dengan pagination.
7. Admin memilih aksi: tambah, edit, hapus, atau kembali ke dashboard/menu lain.

## Alur Tambah Data Pupuk

1. Admin menekan tombol tambah data pupuk.
2. Sistem membuat kode pupuk otomatis dengan prefix `PU`.
3. Sistem menampilkan form tambah data pupuk.
4. Admin mengisi data pupuk.
5. Admin dapat mengunggah gambar pupuk.
6. Admin menekan tombol simpan.
7. Sistem memvalidasi input.
8. Jika data tidak valid, sistem menampilkan pesan error dan mengembalikan admin ke form.
9. Jika data valid, sistem menyimpan gambar ke folder upload pupuk jika ada.
10. Sistem menyimpan data baru ke tabel `pupuk`.
11. Sistem menampilkan pesan sukses.
12. Sistem mengarahkan admin kembali ke daftar data pupuk.

## Alur Edit Data Pupuk

1. Admin membuka daftar data pupuk.
2. Admin memilih salah satu data pupuk untuk diedit.
3. Sistem mengambil data pupuk lama dari database.
4. Sistem menampilkan form edit berisi data lama.
5. Admin mengubah informasi yang diperlukan.
6. Admin dapat mengganti gambar pupuk.
7. Admin menekan tombol update.
8. Sistem memvalidasi input.
9. Jika data tidak valid, sistem menampilkan pesan error dan mengembalikan admin ke form edit.
10. Jika data valid dan ada gambar baru, sistem menghapus gambar lama lalu menyimpan gambar baru.
11. Sistem memperbarui data pupuk di database.
12. Sistem menampilkan pesan sukses.
13. Sistem mengarahkan admin kembali ke daftar data pupuk.

## Alur Hapus Data Pupuk

1. Admin membuka daftar data pupuk.
2. Admin memilih data pupuk yang akan dihapus.
3. Sistem atau tampilan meminta konfirmasi penghapusan.
4. Admin memilih konfirmasi hapus.
5. Sistem memeriksa data pupuk yang dipilih.
6. Jika pupuk memiliki gambar, sistem menghapus file gambar.
7. Sistem menghapus data pupuk dari database.
8. Jika proses berhasil, sistem menampilkan pesan sukses.
9. Jika proses gagal, sistem menampilkan pesan error gagal menghapus data.
10. Sistem mengarahkan admin kembali ke daftar data pupuk.

## Titik Keputusan untuk Activity Diagram

| Keputusan | Cabang |
|---|---|
| Admin memilih aksi? | Tambah, edit, hapus, kembali/menu lain |
| Data tambah valid? | Ya: simpan data, Tidak: tampilkan error |
| Ada gambar pada tambah data? | Ya: simpan gambar, Tidak: lanjut simpan data |
| Data edit valid? | Ya: update data, Tidak: tampilkan error |
| Ada gambar baru saat edit? | Ya: hapus gambar lama dan simpan gambar baru, Tidak: update data tanpa ubah gambar |
| Admin mengonfirmasi hapus? | Ya: hapus data, Tidak: batalkan hapus |
| Proses database berhasil? | Ya: pesan sukses, Tidak: pesan error |

## Catatan Kesesuaian dengan Sistem

Pada implementasi saat ini, sistem mengecek keunikan `kode`, bukan keunikan `nama`. Kode pupuk dibuat otomatis oleh sistem menggunakan `AutoCodeGenerator` dengan prefix `PU`. Jika ingin activity diagram benar-benar sesuai kode, gunakan keputusan **Kode sudah ada?** atau cukup masukkan ke proses **Validasi data**.

## Ringkasan Alur Activity

Admin membuka menu kelola data pupuk, lalu sistem menampilkan daftar pupuk dari database. Admin dapat menambah data baru, mengedit data lama, atau menghapus data. Pada tambah dan edit, sistem memvalidasi input terlebih dahulu. Jika valid, sistem menyimpan atau memperbarui data di database. Jika terdapat gambar, sistem juga menangani penyimpanan atau penggantian file gambar. Pada hapus data, sistem menghapus gambar terkait jika ada, lalu menghapus record pupuk. Setelah setiap operasi berhasil, sistem menampilkan pesan sukses dan memperbarui daftar data pupuk.

