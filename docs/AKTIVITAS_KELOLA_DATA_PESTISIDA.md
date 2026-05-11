# Informasi Activity Diagram Kelola Data Pestisida

Dokumen ini menjelaskan alur aktivitas **Kelola Data Pestisida** pada sistem PadiCare berdasarkan implementasi Laravel yang ada pada `Admin\PestisidaController`.

## Identitas Aktivitas

| Item | Keterangan |
|---|---|
| Nama aktivitas | Kelola Data Pestisida |
| Aktor utama | Admin |
| Modul | Admin - Master Data Pestisida |
| Controller | `App\Http\Controllers\Admin\PestisidaController` |
| Model | `App\Models\Pestisida` |
| Tabel database | `pestisida` |
| Prefix route | `/admin/pestisida` |
| Hak akses | User login dengan role `admin` |

## Tujuan Aktivitas

Aktivitas ini digunakan admin untuk mengelola data pestisida yang dipakai sistem sebagai dasar rekomendasi penanganan penyakit padi. Data pestisida mencakup nama, kode, jenis, bahan aktif, kandungan detail, fungsi, dosis, takaran, harga, satuan harga, cara aplikasi, jadwal aplikasi, frekuensi, efek penggunaan, dan gambar produk.

## Swimlane yang Disarankan

Activity diagram dapat dibuat dengan swimlane berikut:

| Swimlane | Peran |
|---|---|
| Admin | Membuka menu, memilih aksi, mengisi form, menyimpan, mengubah, atau menghapus data |
| Sistem | Menampilkan halaman, membuat kode otomatis, memvalidasi input, memproses upload gambar, memberi pesan sukses/error |
| Database | Menyimpan, membaca, memperbarui, dan menghapus data pestisida |

## Data yang Dikelola

| Field | Keterangan | Validasi Utama |
|---|---|---|
| kode | Kode pestisida | Otomatis dibuat sistem, wajib, unik, maksimal 10 karakter |
| nama | Nama pestisida | Wajib, maksimal 100 karakter |
| jenis | Jenis pestisida | Wajib, salah satu dari fungisida, bakterisida, insektisida, herbisida |
| bahan_aktif | Bahan aktif pestisida | Opsional, maksimal 200 karakter |
| kandungan_detail | Detail kandungan pestisida | Opsional |
| fungsi | Fungsi pestisida | Opsional |
| dosis | Dosis penggunaan | Opsional, maksimal 100 karakter |
| takaran | Takaran penggunaan | Opsional, maksimal 255 karakter |
| efek_penggunaan | Efek penggunaan pestisida | Opsional |
| cara_aplikasi | Cara aplikasi pestisida | Opsional |
| jadwal_umur_aplikasi | Jadwal atau umur aplikasi | Opsional |
| frekuensi_aplikasi | Frekuensi aplikasi | Opsional |
| harga | Harga pestisida | Wajib, numerik, minimal 0 |
| satuan_harga | Satuan harga | Wajib, maksimal 30 karakter |
| gambar | Gambar pestisida | Opsional, jpg/jpeg/png/webp, maksimal 2 MB |

## Alur Utama Menampilkan Data Pestisida

1. Admin login ke sistem.
2. Sistem memeriksa autentikasi dan role admin.
3. Admin membuka menu **Kelola Data Pestisida**.
4. Sistem mengambil data pestisida dari database.
5. Sistem mengurutkan data berdasarkan `kode`.
6. Sistem menampilkan daftar data pestisida dengan pagination.
7. Admin memilih aksi: tambah, edit, hapus, atau kembali ke dashboard/menu lain.

## Alur Tambah Data Pestisida

1. Admin menekan tombol tambah data pestisida.
2. Sistem membuat kode pestisida otomatis dengan prefix `PS`.
3. Sistem menampilkan form tambah data pestisida.
4. Admin mengisi data pestisida.
5. Admin memilih jenis pestisida.
6. Admin dapat mengunggah gambar pestisida.
7. Admin menekan tombol simpan.
8. Sistem memvalidasi input.
9. Jika data tidak valid, sistem menampilkan pesan error dan mengembalikan admin ke form.
10. Jika data valid, sistem menyimpan gambar ke folder upload pestisida jika ada.
11. Sistem menyimpan data baru ke tabel `pestisida`.
12. Sistem menampilkan pesan sukses.
13. Sistem mengarahkan admin kembali ke daftar data pestisida.

## Alur Edit Data Pestisida

1. Admin membuka daftar data pestisida.
2. Admin memilih salah satu data pestisida untuk diedit.
3. Sistem mengambil data pestisida lama dari database.
4. Sistem menampilkan form edit berisi data lama.
5. Admin mengubah informasi yang diperlukan.
6. Admin dapat mengganti gambar pestisida.
7. Admin menekan tombol update.
8. Sistem memvalidasi input.
9. Jika data tidak valid, sistem menampilkan pesan error dan mengembalikan admin ke form edit.
10. Jika data valid dan ada gambar baru, sistem menghapus gambar lama lalu menyimpan gambar baru.
11. Sistem memperbarui data pestisida di database.
12. Sistem menampilkan pesan sukses.
13. Sistem mengarahkan admin kembali ke daftar data pestisida.

## Alur Hapus Data Pestisida

1. Admin membuka daftar data pestisida.
2. Admin memilih data pestisida yang akan dihapus.
3. Sistem atau tampilan meminta konfirmasi penghapusan.
4. Admin memilih konfirmasi hapus.
5. Sistem memeriksa data pestisida yang dipilih.
6. Jika pestisida memiliki gambar, sistem menghapus file gambar.
7. Sistem menghapus data pestisida dari database.
8. Sistem menampilkan pesan sukses.
9. Sistem mengarahkan admin kembali ke daftar data pestisida.

## Titik Keputusan untuk Activity Diagram

| Keputusan | Cabang |
|---|---|
| Admin memilih aksi? | Tambah, edit, hapus, kembali/menu lain |
| Data tambah valid? | Ya: simpan data, Tidak: tampilkan error |
| Jenis pestisida valid? | Ya: lanjut simpan, Tidak: tampilkan error |
| Ada gambar pada tambah data? | Ya: simpan gambar, Tidak: lanjut simpan data |
| Data edit valid? | Ya: update data, Tidak: tampilkan error |
| Ada gambar baru saat edit? | Ya: hapus gambar lama dan simpan gambar baru, Tidak: update data tanpa ubah gambar |
| Admin mengonfirmasi hapus? | Ya: hapus data, Tidak: batalkan hapus |
| Proses database berhasil? | Ya: pesan sukses, Tidak: pesan error |

## Catatan Kesesuaian dengan Sistem

Pada implementasi saat ini, sistem mengecek keunikan `kode`, bukan keunikan `nama`. Kode pestisida dibuat otomatis oleh sistem menggunakan `AutoCodeGenerator` dengan prefix `PS`. Jenis pestisida dibatasi pada empat nilai, yaitu `fungisida`, `bakterisida`, `insektisida`, dan `herbisida`.

Pada controller pestisida saat ini, proses hapus tidak memakai `try-catch` seperti controller pupuk. Secara activity diagram, proses error database tetap boleh ditampilkan sebagai kemungkinan sistem, tetapi jika ingin sangat sesuai kode, alur hapus pestisida dapat dibuat lebih sederhana: konfirmasi hapus, hapus gambar jika ada, hapus data, tampilkan pesan sukses.

## Ringkasan Alur Activity

Admin membuka menu kelola data pestisida, lalu sistem menampilkan daftar pestisida dari database. Admin dapat menambah data baru, mengedit data lama, atau menghapus data. Pada tambah dan edit, sistem memvalidasi input, termasuk validasi jenis pestisida dan file gambar. Jika valid, sistem menyimpan atau memperbarui data di database. Jika terdapat gambar, sistem menangani penyimpanan atau penggantian file gambar. Pada hapus data, sistem menghapus gambar terkait jika ada, lalu menghapus record pestisida. Setelah operasi berhasil, sistem menampilkan pesan sukses dan memperbarui daftar data pestisida.

