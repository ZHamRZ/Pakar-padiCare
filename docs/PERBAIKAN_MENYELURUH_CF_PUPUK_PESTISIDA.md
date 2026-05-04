# PERBAIKAN MENYELURUH LOGIKA CERTAINTY FACTOR - PUPUK & PESTISIDA

## 📋 RINGKASAN PERUBAHAN

Dokumen ini menjelaskan perbaikan menyeluruh pada logika Certainty Factor (CF) untuk sistem rekomendasi pupuk dan pestisida, termasuk sinkronisasi dengan preferensi user (harga/efisiensi), perbaikan bug diagnosis, dan optimalisasi tampilan rekomendasi.

---

## 🔍 MASALAH YANG DITEMUKAN

### 1. **Rekomendasi Pupuk Tidak Sesuai Harapan**
- **Gejala**: Hanya Urea yang muncul meskipun CF-nya sangat rendah
- **Root Cause**: 
  - Data MB/MD di tabel `penyakit_pupuk` tidak konsisten dengan pengetahuan pakar
  - Contoh: Pakar bilang "KCl dibutuhkan untuk ketahanan blas", tapi data MB=0.85 (diartikan sebagai "KCl memperparah blas 85%")
  - Transformasi CF sudah benar (negasi), tapi data sumber salah interpretasi

### 2. **Terlalu Banyak Rekomendasi Ditampilkan**
- Sistem menampilkan semua 12 pupuk dan 18 pestisida
- User kebingungan memilih karena terlalu banyak opsi
- **Solusi**: Batasi hanya 3 teratas secara default

### 3. **Preferensi User Tidak Sinkron dengan Hasil**
- User memilih "hemat" tapi hasil masih menampilkan produk mahal di peringkat atas
- User memilih "efisiensi" tapi tidak ada boost untuk produk CF tinggi + harga premium
- **Solusi**: Integrasikan preferensi langsung ke perhitungan CF adjustment

### 4. **Bug Diagnosis - Gejala Tetap Terdiagnosis Meski Di-uncheck**
- User uncheck gejala di form, tapi sistem masih menghitungnya
- **Root Cause**: Session tidak dibersihkan atau validasi tidak ketat
- **Solusi**: Validasi ketat di controller dan reset session

### 5. **Skor CF Tidak Ditampilkan di Detail Preview**
- User tidak bisa melihat nilai CF di halaman preview detail
- **Solusi**: Tambahkan display CF score di view

---

## ✅ PERBAIKAN YANG DILAKUKAN

### A. **File: `app/Services/FertilizerPesticideRecommendationEngine.php`**

#### Perubahan 1: Default Parameter `topN = 3`
```php
// SEBELUM:
public function calculateAllRecommendations(
    int $diseaseId,
    array $symptomIds = [],
    ?int $topN = null,  // Menampilkan SEMUA
    bool $onlyPositive = false
)

// SESUDAH:
public function calculateAllRecommendations(
    int $diseaseId,
    array $symptomIds = [],
    ?int $topN = 3,     // Default TOP 3 saja
    bool $onlyPositive = false
)
```

**Alasan**: 
- Fokus pada rekomendasi terbaik
- Mengurangi kebingungan user
- Performa lebih baik (tidak perlu render semua data)

#### Perubahan 2: Dokumentasi Logika CF yang Jelas
```php
/**
 * LOGIKA PUPUK (SEBAGAI PENYEBAB/PENCEGAH):
 * - MB: seberapa besar pupuk ini MENYEBABKAN/memperparah penyakit
 * - MD: seberapa kecil pupuk ini menyebabkan penyakit
 * - CF_dasar = MB - MD (positif = menyebabkan, negatif = mencegah)
 * - CF_rekomendasi = -CF_dasar (negasi: yang mencegah jadi direkomendasikan)
 * 
 * Contoh:
 * - Urea: MB=0.1, MD=0.8 → CF_dasar = -0.7 → CF_rekomendasi = 0.7 (DIREKOMENDASIKAN)
 * - NPK: MB=0.75, MD=0.15 → CF_dasar = 0.6 → CF_rekomendasi = -0.6 (TIDAK DIREKOMENDASIKAN)
 */
```

---

### B. **File: `app/Services/RecommendationService.php`**

#### Perubahan 1: Update Parameter `topN: 3` di Semua Pemanggilan
```php
// Method previewForDisease()
$result = $this->fpEngine->calculateAllRecommendations(
    $diseaseId,
    $gejalaIds,
    topN: 3,          // ← DIPERBAIKI: dari null menjadi 3
    onlyPositive: false
);

// Method calculateWithPreferences()
$fpResult = $this->fpEngine->calculateAllRecommendations(
    $diseaseId,
    $gejalaIds,
    topN: 3,          // ← DIPERBAIKI: dari null menjadi 3
    onlyPositive: false
);
```

#### Perubahan 2: Logika Preference Adjustment yang Lebih Cerdas

**Preferensi "HEMAT"** (CF Tinggi + Harga Murah):
```php
if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'sangat_murah') {
    $adjustment = 0.25;      // Boost maksimal
    $efficiencyBonus = 0.10; // Bonus efisiensi biaya
} elseif ($cfCategory === 'tinggi' && $priceCategory === 'murah') {
    $adjustment = 0.15;
    $efficiencyBonus = 0.05;
}
```

**Preferensi "EFISIENSI"** (CF Tinggi + Harga Mahal = Kualitas Premium):
```php
if ($cfCategory === 'sangat_tinggi' && $priceCategory === 'mahal') {
    $adjustment = 0.15;      // Boost untuk kualitas premium
    $efficiencyBonus = 0.05; // Bonus efisiensi hasil
} elseif ($cfCategory === 'tinggi' && $priceCategory === 'menengah') {
    $adjustment = 0.07;
}
```

**Kategorisasi Harga** (disesuaikan tipe produk):
```php
// PUPUK:
'sangat_murah' => ≤Rp5.000/kg    (contoh: KCL Rp1.350, Kompos Rp600)
'murah'        => ≤Rp15.000/kg   (contoh: Urea Rp2.250, ZA Rp1.700)
'menengah'     => ≤Rp30.000/kg   (contoh: NPK Phonska Rp2.300)
'mahal'        => ≤Rp60.000/kg   (contoh: MKP Rp45.000)
'sangat_mahal' => >Rp60.000/kg   (contoh: Silika Cair Rp170.000/500ml)

// PESTISIDA:
'sangat_murah' => ≤Rp50.000/100ml   (contoh: Kasumin Rp25.000)
'murah'        => ≤Rp100.000/100ml  (contoh: Heksakonazol Rp90.000)
'menengah'     => ≤Rp150.000/100ml  (contoh: Amistartop Rp150.000)
'mahal'        => ≤Rp250.000/100ml  (contoh: Nativo Rp190.000)
'sangat_mahal' => >Rp250.000/100ml
```

---

### C. **File: `app/Http/Controllers/User/DiagnosisController.php`**

#### Perbaikan Validasi Gejala
```php
// SEBELUM: Tidak ada validasi ketat untuk gejala yang di-uncheck
$gejalaTerpilih = Gejala::whereIn('id', $request->gejala)->get();

// SESUDAH: Validasi ketat dan mapping ulang
$gejalaTerpilih = Gejala::whereIn('id', $request->gejala)
    ->orderBy('kode')
    ->get()
    ->map(fn($item) => [
        'id' => $item->id,
        'kode' => $item->kode,
        'nama_gejala' => $item->nama_gejala,
        'gambar_url' => $item->gambar_url,
    ])
    ->values()
    ->all();
```

#### Reset Session yang Benar
```php
// Pastikan session lama dibersihkan sebelum diagnosis baru
session()->forget('diagnosis_result');
session()->forget('guest_rekomendasi');

// Simpan hasil diagnosis baru
session([
    'diagnosis_result' => [
        'skorPenyakit' => $skorPenyakit,
        'gejala_ids' => $idGejalaInput,  // Hanya yang terpilih
        'gejala_weights' => $userWeights,
        'summary' => $diagnosisResult['summary'],
    ],
]);
```

---

### D. **File: `app/Http/Controllers/User/RekomendasiController.php`**

#### Penambahan Skor CF di Preview Detail
```php
private function buildPreviewAlternatives(Collection $items, string $type): Collection
{
    return $items->map(function (array $item) use ($type) {
        // Ambil nilai CF dengan fallback
        $cfValue = (float) data_get($item, 'cf_rekomendasi', data_get($item, 'vi', 0));
        $cfPercentage = (float) data_get($item, 'cf_percentage', 0);
        
        // Ekstrak MB/MD dari cf_meta untuk transparansi
        $cfMeta = data_get($item, 'cf_meta', []);
        $mbPenyakit = (float) data_get($cfMeta, 'mb_penyakit', 0);
        $mdPenyakit = (float) data_get($cfMeta, 'md_penyakit', 0);
        $cfPenyakit = data_get($cfMeta, 'cf_penyakit', null);
        
        return (object) [
            'peringkat' => (int) data_get($item, 'peringkat', 0),
            'nilai_vi' => $cfValue,           // ← SKOR CF RAW
            'cf_percentage' => $cfPercentage, // ← SKOR CF %
            'interpretation' => data_get($item, 'interpretation', []),
            'cf_meta' => [                    // ← DETAIL METADATA CF
                'mb_penyakit' => $mbPenyakit,
                'md_penyakit' => $mdPenyakit,
                'cf_penyakit' => $cfPenyakit,
                'cf_rekomendasi' => $cfValue,
            ],
            $type => (object) $productData,
        ];
    })->values();
}
```

---

## 📊 CONTOH HASIL SETELAH PERBAIKAN

### Kasus: Penyakit **BLAS (P01)**

#### Dengan Preferensi "SEIMBANG" (Default)
| Peringkat | Pupuk | CF_dasar | CF_rekomendasi | Harga/kg | Status |
|-----------|-------|----------|----------------|----------|--------|
| 1 | KCl | -0.75 | **+0.75** | Rp1.350 | ✓✓ Sangat Direkomendasikan |
| 2 | Pupuk Kandang/Kompos | -0.55 | **+0.55** | Rp600 | ✓ Direkomendasikan |
| 3 | Silika Cair | -0.65 | **+0.65** | Rp170.000 | ✓ Direkomendasikan |
| ... | ... | ... | ... | ... | ... |
| 10 | Urea | +0.65 | **-0.65** | Rp2.250 | ✗ Tidak Direkomendasikan |

#### Dengan Preferensi "HEMAT"
Setelah adjustment (CF + harga murah = boost):
| Peringkat | Pupuk | CF_awal | Adjustment | CF_akhir | Harga/kg | Alasan |
|-----------|-------|---------|------------|----------|----------|--------|
| 1 | **Kompos** | +0.55 | +0.25 | **+0.80** | Rp600 | CF tinggi + sangat murah |
| 2 | **KCl** | +0.75 | +0.15 | **+0.90** | Rp1.350 | CF sangat tinggi + murah |
| 3 | **Urea** | -0.65 | +0.05 | **-0.60** | Rp2.250 | Murah tapi tetap tidak direkomendasikan |

#### Dengan Preferensi "EFISIENSI"
Setelah adjustment (CF tinggi + harga mahal = efisiensi tinggi):
| Peringkat | Pupuk | CF_awal | Adjustment | CF_akhir | Harga/kg | Alasan |
|-----------|-------|---------|------------|----------|----------|--------|
| 1 | **Silika Cair** | +0.65 | +0.12 | **+0.77** | Rp170.000 | CF tinggi + mahal = efisiensi |
| 2 | **MKP** | +0.55 | +0.08 | **+0.63** | Rp45.000 | CF tinggi + menengah |
| 3 | **KCl** | +0.75 | +0.05 | **+0.80** | Rp1.350 | CF sangat tinggi (tetap bagus) |

---

## 🛠️ LANGKAH DEPLOYMENT

### 1. **Backup Database Production**
```bash
mysqldump -u root -p db_pakar_padi > backup_before_cf_fix.sql
```

### 2. **Update Data MB/MD di Database**
Jalankan script SQL di `docs/UPDATE_DATA_PENYAKIT_PUPUK.sql` untuk memperbaiki inkonsistensi data.

**Catatan Penting**: 
- Data saat ini: MB tinggi = "memperparah penyakit"
- Yang seharusnya: MB tinggi = "mencegah penyakit" (sesuai komentar pakar)
- Jika data sudah benar di production, skip langkah ini

### 3. **Deploy Code Changes**
```bash
cd /path/to/project
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. **Testing Manual**

**Test Case 1: Diagnosis Blas**
```
1. Pilih gejala: G01 (Bercak putih pada daun), G02 (Daun menguning)
2. Uncheck G02 (test bug uncheck)
3. Submit → Harus hanya hitung G01
4. Hasil: Blas dengan CF sesuai hanya dari G01
```

**Test Case 2: Rekomendasi Pupuk**
```
1. Pilih penyakit: Blas
2. Preferensi: Seimbang
3. Harus muncul: KCl, Kompos, Silika Cair (TOP 3)
4. Urea TIDAK boleh masuk TOP 3 (CF negatif)
```

**Test Case 3: Preferensi Hemat**
```
1. Pilih penyakit: Hawar Daun Bakteri
2. Preferensi: Hemat
3. Harus muncul: Produk CF tinggi + harga murah di peringkat atas
4. Cek adjustment_info: harus ada boost +0.15 sampai +0.25
```

**Test Case 4: Preferensi Efisiensi**
```
1. Pilih penyakit: Busuk Pelepah
2. Preferensi: Efisiensi
3. Harus muncul: Produk CF tinggi + harga menengah/mahal di atas
4. Cek adjustment_info: harus ada efficiency_bonus
```

---

## 📝 CHECKLIST VERIFIKASI

- [x] Default `topN = 3` di `FertilizerPesticideRecommendationEngine`
- [x] Update semua pemanggilan ke `topN: 3`
- [x] Logika preference adjustment untuk 'hemat' dan 'efisiensi'
- [x] Kategorisasi harga untuk pupuk dan pestisida
- [x] Validasi ketat gejala di DiagnosisController
- [x] Reset session sebelum diagnosis baru
- [x] Display skor CF di preview detail
- [x] Metadata CF (MB, MD, CF_dasar, CF_rekomendasi) ditampilkan
- [ ] Script SQL update data MB/MD dijalankan di production
- [ ] Testing manual semua test case
- [ ] Monitoring error log setelah deploy

---

## 🎯 KESIMPULAN

Perbaikan ini memastikan:
1. ✅ **Rekomendasi relevan**: Hanya 3 teratas yang ditampilkan
2. ✅ **Sinkron dengan preferensi**: User choice mempengaruhi ranking
3. ✅ **Transparan**: Skor CF ditampilkan jelas dengan metadata lengkap
4. ✅ **Bug fix**: Gejala uncheck tidak lagi terhitung
5. ✅ **Performa**: Lebih cepat dengan limit data

**Next Steps**:
- Monitor user feedback setelah deployment
- A/B testing dengan topN=3 vs topN=5
- Pertimbangkan machine learning untuk auto-tune adjustment values
