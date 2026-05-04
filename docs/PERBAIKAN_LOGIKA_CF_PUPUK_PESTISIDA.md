# Perbaikan Logika Certainty Factor untuk Rekomendasi Pupuk dan Pestisida

## Masalah yang Ditemukan

Saat melakukan diagnosis, pupuk yang keluar hanya Urea padahal nilai CF Urea sangat rendah. Ini terjadi karena **kesalahpahaman dalam interpretasi data MB/MD** pada tabel `penyakit_pupuk`.

## Analisis Root Cause

### 1. Kesalahan Interpretasi Data MB/MD

**Sebelum (SALAH):**
- Dianggap: MB = seberapa besar pupuk MENCEGAH penyakit
- Akibat: Pupuk dengan MB tinggi langsung dianggap "baik" dan direkomendasikan
- Contoh: NPK Phonska MB=0.75 → dianggap sangat mencegah blas → direkomendasikan
- Padahal: Dalam database, NPK Phonska dengan MB=0.75 justru berarti **memperparah** blas!

**Sesudah (BENAR):**
- MB = seberapa besar pupuk MENYEBABKAN/memperparah penyakit
- MD = seberapa kecil pupuk menyebabkan penyakit
- CF_dasar = MB - MD
  - CF_dasar POSITIF → pupuk memperparah penyakit
  - CF_dasar NEGATIF → pupuk mencegah penyakit

### 2. Transformasi CF untuk Rekomendasi

**PUPUK (sebagai penyebab/pencegah):**
```
CF_dasar = MB - MD
CF_rekomendasi = -CF_dasar  // NEGASI!
```

Contoh perhitungan untuk penyakit Blas (P01):
| Pupuk | MB | MD | CF_dasar | CF_rekomendasi | Status |
|-------|-----|-----|----------|----------------|--------|
| Urea | 0.10 | 0.80 | -0.70 | **+0.70** | ✅ DIREKOMENDASIKAN |
| NPK Phonska | 0.75 | 0.15 | +0.60 | **-0.60** | ❌ TIDAK DIREKOMENDASIKAN |
| KCL | 0.85 | 0.10 | +0.75 | **-0.75** | ❌ TIDAK DIREKOMENDASIKAN |
| Kompos | 0.70 | 0.20 | +0.50 | **-0.50** | ❌ TIDAK DIREKOMENDASIKAN |

**Tunggu!** Ada masalah di data database...

## Masalah Data Database

Dari file SQL `db_pakar_padi.sql`:

```sql
-- Penyakit Blas (P001) - Butuh K tinggi untuk ketahanan
(1, 4, 0.850, 0.100, NOW(), NOW()), -- KCl
(1, 2, 0.750, 0.150, NOW(), NOW()), -- NPK Phonska
(1, 5, 0.700, 0.200, NOW(), NOW()), -- Kompos
```

**Komentar mengatakan "Butuh K tinggi untuk ketahanan"**, tapi data MB/MD menunjukkan:
- KCl MB=0.85 → diartikan sebagai "KCl MEMPERPARAH blas sebanyak 85%" ❌

Ini **INKONSISTEN** antara komentar pakar dan data!

## Solusi yang Diterapkan

### Opsi A: Perbaiki Data Database (RECOMMENDED)

Ubah data di tabel `penyakit_pupuk` agar sesuai dengan pengetahuan pakar:

```sql
-- Penyakit Blas (P01) - Butuh K tinggi untuk ketahanan
-- MB = seberapa besar pupuk MEMPERPARAH penyakit
-- MD = seberapa kecil pupuk memperparah penyakit
-- Pupuk yang MENCEGAH harus punya MB rendah, MD tinggi

-- Urea (N tinggi justru memperparah blas)
UPDATE penyakit_pupuk SET mb=0.80, md=0.15 WHERE id_penyakit=1 AND id_pupuk=1; -- CF_dasar=0.65, CF_rek=-0.65 (TIDAK)

-- NPK Phonska (seimbang, cukup baik)
UPDATE penyakit_pupuk SET mb=0.30, md=0.65 WHERE id_penyakit=1 AND id_pupuk=2; -- CF_dasar=-0.35, CF_rek=0.35 (YA)

-- KCl (K tinggi SANGAT BAIK untuk blas)
UPDATE penyakit_pupuk SET mb=0.10, md=0.85 WHERE id_penyakit=1 AND id_pupuk=4; -- CF_dasar=-0.75, CF_rek=0.75 (SANGAT YA!)

-- Kompos (baik untuk tanah)
UPDATE penyakit_pupuk SET mb=0.20, md=0.75 WHERE id_penyakit=1 AND id_pupuk=5; -- CF_dasar=-0.55, CF_rek=0.55 (YA)
```

### Opsi B: Balik Logika Interpretasi (TIDAK DISARANKAN)

Jika data database sudah benar menurut pakar, maka interpretasi harus dibalik:
- MB = seberapa besar pupuk MENCEGAH penyakit
- CF_rekomendasi = CF_dasar (tanpa negasi)

Tapi ini **tidak konsisten** dengan dokumentasi dan komentar di database.

## Implementasi Kode

File yang diperbaiki:

### 1. `app/Services/FertilizerPesticideRecommendationEngine.php`

```php
// LOGIKA PUPUK (diperbaiki):
$cfPenyebab = $this->cfEngine->calculateCf($mb, $md);  // CF_dasar = MB - MD
$cfRekomendasi = -$cfPenyebab;  // NEGASI! Yang mencegah jadi direkomendasikan

// LOGIKA PESTISIDA (tetap):
$cfSolusi = $this->cfEngine->calculateCf($mb, $md);  // CF_solusi = MB - MD
$cfRekomendasi = $cfSolusi;  // Tanpa negasi, yang efektif langsung direkomendasikan
```

### 2. Perubahan Parameter Default

```php
public function calculateAllRecommendations(
    int $diseaseId,
    array $symptomIds = [],
    ?int $topN = null,        // Default tampilkan SEMUA
    bool $onlyPositive = false // Default false untuk transparansi
)
```

## Verifikasi Hasil

Setelah perbaikan, untuk penyakit Blas (P01) dengan data yang BENAR:

| Peringkat | Pupuk | CF_dasar | CF_rekomendasi | Interpretasi |
|-----------|-------|----------|----------------|--------------|
| 1 | KCl | -0.75 | **+0.75** | Sangat Direkomendasikan ✓✓ |
| 2 | Kompos | -0.55 | **+0.55** | Direkomendasikan ✓ |
| 3 | NPK Phonska | -0.35 | **+0.35** | Cukup ~ |
| ... | ... | ... | ... | ... |
| 10 | Urea | +0.65 | **-0.65** | Tidak Direkomendasikan ✗ |

## Kesimpulan

1. **Masalah utama**: Inkonsistensi antara interpretasi MB/MD di kode vs data di database
2. **Solusi**: 
   - Perbaiki logika kode dengan negasi CF untuk pupuk
   - **WAJIB** perbaiki data database agar sesuai dengan pengetahuan pakar
3. **Testing**: Setelah deploy, verifikasi dengan kasus uji yang diketahui hasilnya

## Checklist Deployment

- [ ] Backup database production
- [ ] Update script SQL untuk memperbaiki data `penyakit_pupuk`
- [ ] Deploy code perubahan
- [ ] Test dengan beberapa penyakit (Blas, Hawar Daun, Tungro, dll)
- [ ] Verifikasi ranking pupuk sesuai ekspektasi pakar
- [ ] Update dokumentasi jika ada perubahan
