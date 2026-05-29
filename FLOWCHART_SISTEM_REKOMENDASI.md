# FLOWCHART SISTEM REKOMENDASI PUPUK DAN PESTISIDA
## Metode Certainty Factor - Daftar Langkah Terstruktur

---

## 1. ALUR MASUK PENGGUNA (USER LOGIN/REGISTRATION)

### 1.1 Pengguna Belum Login (Guest)
1. **Start** → Pengguna mengakses aplikasi
2. **Decision**: Apakah pengguna sudah memiliki akun?
   - **No** → Redirect ke halaman registrasi
     - Input: Nama, Email, Password
     - Validasi data
     - Simpan ke database users
     - Auto login
   - **Yes** → Redirect ke halaman login
     - Input: Email, Password
     - Validasi credentials
     - Create session
3. **End** → User authenticated → Dashboard

### 1.2 Pengguna Sudah Login
1. **Start** → User mengakses login page
2. Input email dan password
3. Sistem validasi credentials di database
4. **Decision**: Credentials valid?
   - **No** → Tampilkan error message → Kembali ke step 2
   - **Yes** → Create session auth
5. Redirect ke dashboard user
6. **End**

---

## 2. ALUR INPUT DATA GEJALA (DIAGNOSIS)

### 2.1 Halaman Diagnosis
1. **Start** → User klik menu "Diagnosis"
2. Sistem load semua data gejala dari database (tabel: gejala)
3. Tampilkan form dengan:
   - Checklist gejala yang tersedia
   - Slider bobot keyakinan untuk setiap gejala (0-100%)
   - Informasi gambar untuk setiap gejala
4. **User Action**: Pilih gejala yang dialami tanaman
5. **User Action**: Atur bobot keyakinan untuk setiap gejala terpilih
6. **Validation**: Minimal 1 gejala harus dipilih
   - **Invalid** → Tampilkan pesan error
   - **Valid** → Lanjut ke proses identifikasi
7. **End** → Submit form ke controller

### 2.2 Proses Identifikasi Gejala
1. **Start** → Form diagnosis di-submit
2. Controller terima data:
   - Array ID gejala terpilih
   - Array bobot keyakinan (gejala_weights)
3. **Validation**:
   - Cek minimal 1 gejala
   - Validasi existensi gejala di database
4. Panggil `DiagnosisService::identify()`
5. **Certainty Factor Calculation** (untuk setiap penyakit):
   - Load relasi penyakit-gejala (MB, MD) dari database
   - Filter hanya gejala yang terpilih oleh user
   - Hitung CF user = bobot_keyakinan / 100
   - Hitung CF pakar = MB - MD untuk setiap gejala
   - Kombinasikan CF dengan rumus: CF_combined = CF_user * CF_pakar
   - Agregasi multi-gejala dengan formula kombinasi CF
6. Hitung skor confidence untuk setiap penyakit
7. Sort penyakit berdasarkan skor tertinggi
8. Simpan hasil ke session:
   - skorPenyakit (array penyakit + skor)
   - gejala_ids
   - gejala_weights
   - summary
9. **End** → Redirect ke halaman hasil identifikasi

---

## 3. ALUR HASIL IDENTIFIKASI PENYAKIT

### 3.1 Tampilan Hasil Identifikasi
1. **Start** → User redirect ke halaman hasil
2. Sistem load data dari session diagnosis_result
3. Load data pendukung:
   - Detail gejala yang dipilih
   - Daftar kriteria untuk preferensi
   - Preset preferensi (seimbang, hemat, efisiensi, dll)
4. Tampilkan:
   - List penyakit terdeteksi dengan skor confidence
   - Progress bar confidence level
   - Gejala-gejala yang cocok
   - Form pemilihan preferensi rekomendasi
5. **User Action**: Pilih satu atau lebih penyakit untuk direkomendasikan
6. **User Action**: Pilih tipe preferensi (seimbang/hemat/efisiensi/custom)
7. **User Action** (opsional): Atur bobot kriteria custom
8. **End** → Submit ke proses rekomendasi

---

## 4. PEMROSESAN METODE CERTAINTY FACTOR UNTUK REKOMENDASI

### 4.1 Inisialisasi Perhitungan Rekomendasi
1. **Start** → Form preferensi di-submit
2. Controller terima data:
   - ID penyakit terpilih (bisa multiple)
   - Gejala terpilih
   - Tipe preferensi (preset)
   - Bobot kriteria custom (jika ada)
   - Bobot keyakinan gejala (gejala_weights)
3. Load data penyakit dari database
4. **Loop** untuk setiap penyakit terpilih:

### 4.2 Perhitungan CF untuk Pupuk (Fertilizer Recommendation)
1. **Start Loop Pupuk**
2. Query pupuk yang memiliki relasi dengan gejala terpilih:
   ```sql
   SELECT pupuk.* 
   FROM pupuk
   INNER JOIN gejala_pupuk ON pupuk.id = gejala_pupuk.id_pupuk
   WHERE gejala_pupuk.id_gejala IN (gejala_terpilih)
   ```
3. **Untuk setiap pupuk**:
   - Load relasi gejala-pupuk (MB, MD dari pivot table)
   - **Untuk setiap gejala yang match**:
     - Ambil MB (Measure of Belief) dan MD (Measure of Disbelief)
     - Validasi: Jika MB + MD > 1, normalisasi:
       - MB = MB / (MB + MD)
       - MD = MD / (MB + MD)
     - Hitung CF_penyebab = MB - MD
     - Simpan detail: symptom_id, MB, MD, CF_penyebab
   - **Kombinasi Multi-Gejala**:
     - Gunakan formula kombinasi CF:
       - Jika CF1 & CF2 positif: CF_comb = CF1 + CF2 * (1 - CF1)
       - Jika CF1 & CF2 negatif: CF_comb = CF1 + CF2 * (1 + CF1)
       - Jika berbeda tanda: CF_comb = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))
     - Iterasi untuk semua gejala → CF_penyebab_total
   - **TRANSFORMASI CF PUPUK**:
     - Konsep: Pupuk sebagai PENYEBAB gejala
     - CF_positif → pupuk memperparah kondisi (TIDAK direkomendasikan)
     - CF_negatif → pupuk membantu/kurang menyebabkan (DIREKOMENDASIKAN)
     - Rumus transformasi: **CF_rekomendasi = -CF_penyebab_total**
   - Normalisasi CF_rekomendasi ke range [-1, 1]
   - Konversi ke persentase: CF_percentage = (CF_rekomendasi + 1) * 50
   - Tentukan label interpretasi:
     - CF > 0.7 → "Sangat Direkomendasikan"
     - CF > 0.4 → "Direkomendasikan"
     - CF > 0.1 → "Cukup"
     - CF > 0 → "Kurang Direkomendasikan"
     - CF ≤ 0 → "Tidak Direkomendasikan"
4. Filter hanya pupuk dengan CF_rekomendasi > 0 (jika opsi onlyPositive aktif)
5. Sort berdasarkan CF_rekomendasi descending
6. Assign peringkat (ranking)
7. **End Loop Pupuk**

### 4.3 Perhitungan CF untuk Pestisida (Pesticide Recommendation)
1. **Start Loop Pestisida**
2. Query pestisida yang memiliki relasi dengan gejala terpilih:
   ```sql
   SELECT pestisida.* 
   FROM pestisida
   INNER JOIN gejala_pestisida ON pestisida.id = gejala_pestisida.id_pestisida
   WHERE gejala_pestisida.id_gejala IN (gejala_terpilih)
   ```
3. **Untuk setiap pestisida**:
   - Load relasi gejala-pestisida (MB, MD dari pivot table)
   - **Untuk setiap gejala yang match**:
     - Ambil MB dan MD
     - Validasi konsistensi MB + MD ≤ 1
     - Hitung CF_solusi = MB - MD
     - Simpan detail symptom
   - **Kombinasi Multi-Gejala**:
     - Gunakan formula kombinasi CF yang sama seperti pupuk
     - Hasil: CF_solusi_total
   - **TIDAK ADA TRANSFORMASI untuk Pestisida**:
     - Konsep: Pestisida sebagai SOLUSI
     - CF_positif → efektif mengatasi gejala (DIREKOMENDASIKAN)
     - CF_negatif → tidak efektif (TIDAK direkomendasikan)
     - Rumus: **CF_rekomendasi = CF_solusi_total** (langsung)
   - Normalisasi ke range [-1, 1]
   - Konversi ke persentase
   - Tentukan label interpretasi (sama seperti pupuk)
4. Filter hanya pestisida dengan CF_rekomendasi > 0
5. Sort berdasarkan CF_rekomendasi descending
6. Assign peringkat
7. **End Loop Pestisida**

### 4.4 Integrasi Preferensi Pengguna (Preference Adjustment)
1. **Start Preference Integration**
2. **Decision**: Apakah preferensi tipe = 'seimbang' DAN tidak ada custom criteria?
   - **Yes** → Skip adjustment, gunakan CF hasil perhitungan langsung
   - **No** → Lanjut ke adjustment
3. **Adjustment berdasarkan Preset**:
   - **'hemat'**: Boost alternatif dengan harga rendah
     - Jika harga < 50000 → +0.03
     - Jika harga ≥ 50000 → -0.02
   - **'efisiensi'**: Boost alternatif dengan CF tinggi
     - Jika CF > 0.7 → +0.02
   - **'custom'**: Gunakan bobot kriteria dari user
4. **Adjustment berdasarkan Symptom Weights**:
   - Untuk setiap gejala dengan weight tinggi (>80%):
     - Boost kecil (+0.02 max) untuk alternatif yang mendukung gejala tersebut
   - Cap total adjustment di 0.08 (8%)
5. Apply adjustment ke CF_rekomendasi:
   - CF_adjusted = CF_rekomendasi + adjustment
   - Normalisasi ke range [-1, 1]
   - Update persentase dan label
6. Re-calculate peringkat setelah adjustment
7. **End Preference Integration**

### 4.5 Penyimpanan Hasil (Untuk User Logged In)
1. **Decision**: Apakah user logged in?
   - **Yes**:
     - Create record di tabel `rekomendasi`:
       - id_user, id_penyakit, preferensi_pengguna (JSON), timestamp
     - Create records di `detail_pupuk` (pivot):
       - id_rekomendasi, id_pupuk, nilai_cf, peringkat
     - Create records di `detail_pestisida` (pivot):
       - id_rekomendasi, id_pestisida, nilai_cf, peringkat
   - **No** (Guest):
     - Simpan hasil ke session `guest_rekomendasi`
     - Data tersimpan sementara sampai session expire
2. **End**

---

## 5. ALUR TAMPILAN HASIL REKOMENDASI (PREVIEW PAGE)

### 5.1 Persiapan Data Preview
1. **Start** → Redirect ke `/user/rekomendasi/preview`
2. Controller load data dari session `guest_rekomendasi`
3. **Untuk setiap item** dalam session:
   - Extract data preview (pupuk & pestisida arrays)
   - Build objek rekomendasi dengan struktur:
     - penyakit (id, nama, gambar, kode)
     - preferensi_label
     - preferensi_pengguna (gejala_terpilih, kriteria, weights)
     - gejala_cocok (filtered symptoms)
     - detailPupuk (collection dari hasil CF)
     - detailPestisida (collection dari hasil CF)
4. **Mapping Data untuk View**:
   - Untuk setiap pupuk/pestisida:
     - Extract cf_rekomendasi → nilai_vi
     - Extract cf_percentage
     - Extract interpretation (label, color, icon)
     - Map symptom_details → gejala_cocok
     - Map field spesifik (kandungan, dosis, dll)
5. Sort pupuk dan pestisida berdasarkan peringkat
6. Hitung threshold untuk filter tampilan:
   - threshold = max(0.6, top_score - 0.1)
7. Filter hanya item dengan nilai_vi ≥ threshold
8. **End** → Pass data ke view

### 5.2 Rendering Halaman Preview
1. **Start View Rendering**
2. **Hero Section**:
   - Tampilkan judul "Preview Hasil Rekomendasi"
   - Badge jumlah penyakit dipilih
   - Badge tipe preferensi
   - List gejala yang dipakai (max 5 + counter)
3. **Login Notice** (jika guest):
   - Alert info untuk login menyimpan hasil
4. **Loop untuk Setiap Penyakit**:
   - **Disease Sidebar (Left Column)**:
     - Gambar penyakit (atau placeholder)
     - Nama penyakit + badge confidence
     - Confidence bar (visual progress bar)
     - Meta grid: jumlah opsi pupuk & pestisida
     - Badge gejala cocok
   - **Recommendations (Right Column)**:
     - **Pupuk Section**:
       - Section header "Rekomendasi Pupuk"
       - Grid layout product cards
       - **Untuk setiap pupuk**:
         - Card dengan:
           - Gambar produk
           - Badge peringkat (#1, #2, dst)
           - Badge kode pupuk
           - Badge preferensi
           - Nama produk
           - Deskripsi singkat (fungsi_utama + efek)
           - Score bar (visual bar dengan width = CF%)
           - Label confidence (%)
           - Badge adjustment (jika ada preference boost)
           - Toggle detail (accordion)
         - **Detail Panel** (expandable):
           - Grid informasi lengkap:
             - Kode, Peringkat, Skor CF
             - Takaran, Frekuensi
             - Kandungan, Detail Kandungan
             - Fungsi Utama
             - Efek Penggunaan
             - Cara Aplikasi
             - Jadwal/Umur Aplikasi
           - Badge gejala didukung
     - **Pestisida Section** (struktur sama dengan pupuk):
       - Section header "Rekomendasi Pestisida"
       - Product cards dengan field spesifik:
         - Bahan Aktif
         - Dosis
         - Fungsi
         - dll
5. **Action Bar**:
   - Button: Login untuk Simpan (guest) / Lihat Riwayat (logged in)
   - Button: Cetak Hasil
   - Button: Diagnosis Lagi
6. **JavaScript Enhancement**:
   - Accordion toggle behavior
   - Smooth animation untuk detail panel
7. **End** → Halaman siap interaksi

---

## 6. ALUR CETAK HASIL (PRINT/EXPORT)

### 6.1 Preview Cetak
1. **Start** → User klik "Cetak Hasil"
2. Route ke `/user/rekomendasi/preview/cetak`
3. Load data dari session (sama seperti preview page)
4. Render view `cetak-preview.blade.php`:
   - Layout khusus print-friendly
   - Hide interactive elements (buttons, toggles)
   - Show all details expanded
   - Optimized CSS for print
5. **Decision**: User request download?
   - **Yes** → Set headers:
     - Content-Type: text/html
     - Content-Disposition: attachment
     - Filename: hasil-rekomendasi-preview.html
   - **No** → Open in new tab untuk print preview
6. **End**

### 6.2 Cetak Riwayat (Logged User)
1. **Start** → User klik "Cetak" dari halaman riwayat
2. Route ke `/user/rekomendasi/{id}/cetak`
3. Query database:
   - Load rekomendasi by ID + user_id
   - Eager load: penyakit, user, detailPupuk.pupuk, detailPestisida.pestisida
   - Include gambar untuk produk
4. Render view `cetak.blade.php` dengan data lengkap
5. **Decision**: Download mode?
   - **Yes** → Return as downloadable HTML file
   - **No** → Display print view
6. **End**

---

## 7. ALUR RIWAYAT REKOMENDASI (HISTORY)

### 7.1 Tampilan Riwayat
1. **Start** → User klik menu "Riwayat"
2. Query database:
   ```sql
   SELECT rekomendasi.*, penyakit.nama as penyakit_nama
   FROM rekomendasi
   INNER JOIN penyakit ON rekomendasi.id_penyakit = penyakit.id
   WHERE rekomendasi.id_user = Auth::id()
   ORDER BY rekomendasi.created_at DESC
   ```
3. Tampilkan list riwayat:
   - Tanggal diagnosis
   - Nama penyakit
   - Jumlah pupuk & pestisida direkomendasikan
   - Tipe preferensi
   - Actions: Lihat Detail, Cetak, Hapus
4. **End**

### 7.2 Detail Riwayat
1. **Start** → User klik "Lihat Detail"
2. Load rekomendasi dengan relationships:
   - penyakit (with gejala)
   - detailPupuk (with pupuk)
   - detailPestisida (with pestisida)
3. Re-calculate preview menggunakan `RecommendationService::previewForDisease()`
4. Render view `detail.blade.php` (mirip preview tapi dengan data saved)
5. **End**

---

## 8. DIAGRAM RINGKASAN ALUR LENGKAP

```
[START] 
   ↓
[Login/Register] → [Dashboard]
   ↓
[Menu Diagnosis]
   ↓
[Input Gejala + Bobot Keyakinan] ←───┐
   ↓                                   │
[Validasi: Min 1 Gejala] ─(Invalid)───┘
   ↓ (Valid)
[Proses Identifikasi - CF Calculation]
   ↓
[Hitung Skor Confidence per Penyakit]
   ↓
[Tampil Hasil Identifikasi]
   ↓
[Pilih Penyakit + Preferensi]
   ↓
[Proses Rekomendasi - CF Method]
   │
   ├─→ [Load Pupuk + Relasi Gejala]
   │      ↓
   │   [Hitung CF Penyebab per Pupuk]
   │      ↓
   │   [Transformasi: CF_rec = -CF_cause]
   │      ↓
   │   [Filter CF > 0]
   │
   ├─→ [Load Pestisida + Relasi Gejala]
   │      ↓
   │   [Hitung CF Solusi per Pestisida]
   │      ↓
   │   [No Transform: CF_rec = CF_solution]
   │      ↓
   │   [Filter CF > 0]
   │
   ↓
[Apply Preference Adjustment]
   ↓
[Sort & Ranking]
   ↓
[Save to DB (logged in) / Session (guest)]
   ↓
[Tampil Preview Rekomendasi]
   │
   ├─→ [Disease Info Panel]
   ├─→ [Pupuk Recommendations Grid]
   └─→ [Pestisida Recommendations Grid]
   ↓
[User Actions]
   │
   ├─→ [Cetak Hasil] → [Print/Download HTML]
   ├─→ [Diagnosis Lagi] → [Kembali ke Input Gejala]
   └─→ [Login/Simpan] → [Save to History]
   ↓
[END]
```

---

## 9. TABEL DATABASE YANG TERLIBAT

### 9.1 Tabel Utama
1. **users** - Data pengguna
2. **gejala** - Master data gejala
3. **penyakit** - Master data penyakit
4. **penyakit_gejala** - Relasi penyakit-gejala (MB, MD pakar)
5. **pupuk** - Master data pupuk
6. **pestisida** - Master data pestisida
7. **gejala_pupuk** - Relasi gejala-pupuk (MB, MD)
8. **gejala_pestisida** - Relasi gejala-pestisida (MB, MD)
9. **kriteria** - Kriteria untuk preferensi SAW
10. **rekomendasi** - Header hasil rekomendasi (user, penyakit, preferensi)
11. **detail_pupuk** - Pivot rekomendasi-pupuk (CF, ranking)
12. **detail_pestisida** - Pivot rekomendasi-pestisida (CF, ranking)

### 9.2 Session Storage
- **diagnosis_result** - Hasil identifikasi sementara
- **guest_rekomendasi** - Preview rekomendasi untuk guest user

---

## 10. FORMULA CERTAINTY FACTYANG DIGUNAKAN

### 10.1 CF Dasar
```
CF = MB - MD
Range: [-1, 1]
```

### 10.2 CF User dengan Bobot Keyakinan
```
CF_user = (bobot_keyakinan / 100) * CF_pakar
```

### 10.3 Kombinasi Multi-Gejala
```
Jika CF1, CF2 > 0:
  CF_comb = CF1 + CF2 * (1 - CF1)

Jika CF1, CF2 < 0:
  CF_comb = CF1 + CF2 * (1 + CF1)

Jika berbeda tanda:
  CF_comb = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))
```

### 10.4 Transformasi Pupuk
```
CF_rekomendasi = -CF_penyebab_total
```

### 10.5 Pestisida (No Transform)
```
CF_rekomendasi = CF_solusi_total
```

### 10.6 Konversi ke Persentase
```
CF_percentage = (CF_rekomendasi + 1) * 50
Range: [0%, 100%]
```

---

## 11. INTERPRETASI NILAI CF

| Range CF | Label | Interpretasi |
|----------|-------|--------------|
| > 0.7 | Sangat Direkomendasikan | Rekomendasi sangat kuat |
| 0.4 - 0.7 | Direkomendasikan | Rekomendasi kuat |
| 0.1 - 0.4 | Cukup | Rekomendasi moderat |
| 0 - 0.1 | Kurang Direkomendasikan | Rekomendasi lemah |
| ≤ 0 | Tidak Direkomendasikan | Tidak direkomendasikan |

---

**Dokumentasi ini mencakup seluruh alur sistem dari input hingga output rekomendasi dengan metode Certainty Factor.**
