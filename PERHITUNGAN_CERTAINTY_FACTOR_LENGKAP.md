# PERHITUNGAN CERTAINTY FACTOR (CF) - SISTEM PAKAR DIAGNOSIS PENYAKIT PADI

## 1. DATA DASAR SISTEM

### 1.1 Tabel Gejala
| Kode | ID | Nama Gejala |
|------|----|-------------|
| G001 | 1  | Daun berwarna kuning |
| G002 | 2  | Bercak coklat pada daun |
| G003 | 3  | Daun layu dan mengering |
| G004 | 4  | Batang busuk |
| G005 | 5  | Pertumbuhan terhambat |
| G006 | 6  | Bulat-bulat kecil pada daun |
| G007 | 7  | Daun bergaris-garis coklat |
| G008 | 8  | Malai padi kosong |

### 1.2 Tabel Penyakit
| Kode | ID | Nama Penyakit | Deskripsi |
|------|----|---------------|-----------|
| P001 | 1  | Penyakit Blas | Penyakit jamur yang menyerang daun, leher malai, dan butir padi |
| P002 | 2  | Penyakit Hawar Daun Bakteri | Penyakit bakteri yang menyebabkan daun layu dan mengering |
| P003 | 3  | Penyakit Tungro | Penyakit virus yang ditularkan oleh wereng hijau |
| P004 | 4  | Penyakit Bercak Coklat | Penyakit jamur yang menyebabkan bercak coklat pada daun |

### 1.3 Tabel Penyakit-Gejala (MB dan MD)
| ID | Penyakit | Gejala | MB | MD | CF = MB - MD |
|----|----------|--------|-----|-----|--------------|
| 1  | P001 (Blas) | G001 | 0.800 | 0.100 | **0.700** |
| 2  | P001 (Blas) | G002 | 0.900 | 0.050 | **0.850** |
| 3  | P001 (Blas) | G006 | 0.850 | 0.100 | **0.750** |
| 4  | P001 (Blas) | G008 | 0.750 | 0.150 | **0.600** |
| 5  | P002 (Hawar) | G001 | 0.700 | 0.200 | **0.500** |
| 6  | P002 (Hawar) | G003 | 0.850 | 0.100 | **0.750** |
| 7  | P002 (Hawar) | G004 | 0.900 | 0.050 | **0.850** |
| 8  | P003 (Tungro) | G001 | 0.650 | 0.250 | **0.400** |
| 9  | P003 (Tungro) | G005 | 0.800 | 0.150 | **0.650** |
| 10 | P003 (Tungro) | G007 | 0.750 | 0.200 | **0.550** |
| 11 | P004 (Bercak) | G002 | 0.850 | 0.100 | **0.750** |
| 12 | P004 (Bercak) | G006 | 0.700 | 0.200 | **0.500** |

---

## 2. RUMUS CERTAINTY FACTOR

### 2.1 Rumus Dasar CF
```
CF(H, E) = MB(H, E) - MD(H, E)
```
Keterangan:
- CF(H, E) = Certainty Factor hipotesis H yang dipengaruhi oleh fakta E
- MB(H, E) = Measure of Belief (tingkat kepercayaan)
- MD(H, E) = Measure of Disbelief (tingkat ketidakpercayaan)

### 2.2 Rumus Kombinasi CF (Multiple Symptoms)
Jika ada lebih dari satu gejala untuk penyakit yang sama:

**Untuk CF positif:**
```
CF_combine = CF1 + CF2 × (1 - CF1)
```

**Untuk CF negatif:**
```
CF_combine = CF1 + CF2 × (1 + CF1)
```

**Untuk CF berbeda tanda:**
```
CF_combine = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))
```

### 2.3 Faktor Kelengkapan Gejala
```
Final_CF = Combined_CF × (0.7 + 0.3 × Completeness_Factor)
```
dimana:
```
Completeness_Factor = Jumlah_gejala_yang_cocok / Total_gejala_penyakit
```

### 2.4 Konversi ke Persentase
```
Confidence_Percent = ((CF + 1) / 2) × 100%
```

---

## 3. CONTOH PERHITUNGAN LENGKAP

### KASUS 1: Petani Memilih Gejala G001 dan G002

**Input User:**
- ☑ G001: Daun berwarna kuning
- ☑ G002: Bercak coklat pada daun

#### Langkah 1: Identifikasi Penyakit yang Memiliki Gejala Tersebut

**Penyakit P001 (Blas):**
- Memiliki G001: MB=0.800, MD=0.100 → CF₁ = 0.800 - 0.100 = **0.700**
- Memiliki G002: MB=0.900, MD=0.050 → CF₂ = 0.900 - 0.050 = **0.850**

**Penyakit P002 (Hawar Daun Bakteri):**
- Memiliki G001: MB=0.700, MD=0.200 → CF₁ = 0.700 - 0.200 = **0.500**
- Tidak memiliki G002 → **Tidak dihitung**

**Penyakit P003 (Tungro):**
- Memiliki G001: MB=0.650, MD=0.250 → CF₁ = 0.650 - 0.250 = **0.400**
- Tidak memiliki G002 → **Tidak dihitung**

**Penyakit P004 (Bercak Coklat):**
- Tidak memiliki G001
- Memiliki G002: MB=0.850, MD=0.100 → CF₁ = 0.850 - 0.100 = **0.750**

#### Langkah 2: Kombinasi CF untuk Setiap Penyakit

**P001 (Blas) - 2 gejala cocok:**
```
CF1 = 0.700
CF2 = 0.850

CF_combine = CF1 + CF2 × (1 - CF1)
           = 0.700 + 0.850 × (1 - 0.700)
           = 0.700 + 0.850 × 0.300
           = 0.700 + 0.255
           = 0.955
```

Faktor kelengkapan:
- Gejala cocok: 2 dari 4 total gejala P001
- Completeness = 2/4 = 0.5

```
Final_CF(P001) = 0.955 × (0.7 + 0.3 × 0.5)
               = 0.955 × (0.7 + 0.15)
               = 0.955 × 0.85
               = 0.81175 ≈ 0.812
```

Konversi ke persentase:
```
Confidence = ((0.812 + 1) / 2) × 100%
           = (1.812 / 2) × 100%
           = 0.906 × 100%
           = 90.6% ≈ 91%
```

**P002 (Hawar Daun Bakteri) - 1 gejala cocok:**
```
CF1 = 0.500 (hanya 1 gejala, tidak perlu kombinasi)
```

Faktor kelengkapan:
- Gejala cocok: 1 dari 3 total gejala P002
- Completeness = 1/3 ≈ 0.333

```
Final_CF(P002) = 0.500 × (0.7 + 0.3 × 0.333)
               = 0.500 × (0.7 + 0.1)
               = 0.500 × 0.8
               = 0.400
```

Konversi ke persentase:
```
Confidence = ((0.400 + 1) / 2) × 100%
           = (1.4 / 2) × 100%
           = 0.7 × 100%
           = 70%
```

**P003 (Tungro) - 1 gejala cocok:**
```
CF1 = 0.400 (hanya 1 gejala, tidak perlu kombinasi)
```

Faktor kelengkapan:
- Gejala cocok: 1 dari 3 total gejala P003
- Completeness = 1/3 ≈ 0.333

```
Final_CF(P003) = 0.400 × (0.7 + 0.3 × 0.333)
               = 0.400 × 0.8
               = 0.320
```

Konversi ke persentase:
```
Confidence = ((0.320 + 1) / 2) × 100%
           = (1.32 / 2) × 100%
           = 0.66 × 100%
           = 66%
```

**P004 (Bercak Coklat) - 1 gejala cocok:**
```
CF1 = 0.750 (hanya 1 gejala, tidak perlu kombinasi)
```

Faktor kelengkapan:
- Gejala cocok: 1 dari 2 total gejala P004
- Completeness = 1/2 = 0.5

```
Final_CF(P004) = 0.750 × (0.7 + 0.3 × 0.5)
               = 0.750 × 0.85
               = 0.6375 ≈ 0.638
```

Konversi ke persentase:
```
Confidence = ((0.638 + 1) / 2) × 100%
           = (1.638 / 2) × 100%
           = 0.819 × 100%
           = 81.9% ≈ 82%
```

#### Langkah 3: Ranking Hasil Diagnosis

| Peringkat | Penyakit | CF Akhir | Confidence | Interpretasi |
|-----------|----------|----------|------------|--------------|
| 1 | **P001 - Penyakit Blas** | 0.812 | **91%** | Sangat Tinggi ✓✓ |
| 2 | P004 - Penyakit Bercak Coklat | 0.638 | 82% | Tinggi ✓ |
| 3 | P002 - Hawar Daun Bakteri | 0.400 | 70% | Sedang ~ |
| 4 | P003 - Penyakit Tungro | 0.320 | 66% | Sedang ~ |

#### Kesimpulan Kasus 1:
Tanaman padi terdiagnosis terkena **Penyakit Blas (P001)** dengan tingkat kepercayaan **91%**.

**Rekomendasi:**
- **Pupuk:** KCl (MB=0.850, MD=0.100), NPK Phonska (MB=0.750, MD=0.150), Kompos (MB=0.700, MD=0.200)
- **Pestisida:** Filia 525 SE (MB=0.950, MD=0.030), Amistar Top (MB=0.900, MD=0.050)

---

### KASUS 2: Petani Memilih Gejala G001, G003, dan G004

**Input User:**
- ☑ G001: Daun berwarna kuning
- ☑ G003: Daun layu dan mengering
- ☑ G004: Batang busuk

#### Langkah 1: Identifikasi Penyakit

**P001 (Blas):**
- G001: CF = 0.800 - 0.100 = **0.700**
- G003: Tidak ada
- G004: Tidak ada
- Hanya 1 gejala cocok

**P002 (Hawar Daun Bakteri):**
- G001: CF = 0.700 - 0.200 = **0.500**
- G003: CF = 0.850 - 0.100 = **0.750**
- G004: CF = 0.900 - 0.050 = **0.850**
- Ketiga gejala cocok!

**P003 (Tungro):**
- G001: CF = 0.650 - 0.250 = **0.400**
- G003: Tidak ada
- G004: Tidak ada
- Hanya 1 gejala cocok

**P004 (Bercak Coklat):**
- Tidak ada gejala yang cocok
- **Tidak dihitung**

#### Langkah 2: Kombinasi CF

**P001 (Blas) - 1 gejala:**
```
CF = 0.700
Completeness = 1/4 = 0.25
Final_CF = 0.700 × (0.7 + 0.3 × 0.25)
         = 0.700 × (0.7 + 0.075)
         = 0.700 × 0.775
         = 0.5425 ≈ 0.543

Confidence = ((0.543 + 1) / 2) × 100% = 77.15% ≈ 77%
```

**P002 (Hawar Daun Bakteri) - 3 gejala:**

Kombinasi bertahap:
```
CF1 = 0.500 (G001)
CF2 = 0.750 (G003)
CF3 = 0.850 (G004)

Langkah 1: Gabung CF1 dan CF2
CF_combine_12 = 0.500 + 0.750 × (1 - 0.500)
              = 0.500 + 0.750 × 0.500
              = 0.500 + 0.375
              = 0.875

Langkah 2: Gabung hasil dengan CF3
CF_combine_123 = 0.875 + 0.850 × (1 - 0.875)
               = 0.875 + 0.850 × 0.125
               = 0.875 + 0.10625
               = 0.98125 ≈ 0.981
```

Faktor kelengkapan:
- Gejala cocok: 3 dari 3 total gejala P002
- Completeness = 3/3 = 1.0 (LENGKAP!)

```
Final_CF(P002) = 0.981 × (0.7 + 0.3 × 1.0)
               = 0.981 × 1.0
               = 0.981
```

Konversi ke persentase:
```
Confidence = ((0.981 + 1) / 2) × 100%
           = (1.981 / 2) × 100%
           = 0.9905 × 100%
           = 99.05% ≈ 99%
```

**P003 (Tungro) - 1 gejala:**
```
CF = 0.400
Completeness = 1/3 ≈ 0.333
Final_CF = 0.400 × 0.8 = 0.320
Confidence = 66%
```

#### Langkah 3: Ranking Hasil

| Peringkat | Penyakit | CF Akhir | Confidence | Interpretasi |
|-----------|----------|----------|------------|--------------|
| 1 | **P002 - Hawar Daun Bakteri** | 0.981 | **99%** | Sangat Tinggi ✓✓ |
| 2 | P001 - Penyakit Blas | 0.543 | 77% | Tinggi ✓ |
| 3 | P003 - Penyakit Tungro | 0.320 | 66% | Sedang ~ |

#### Kesimpulan Kasus 2:
Tanaman padi terdiagnosis terkena **Hawar Daun Bakteri (P002)** dengan tingkat kepercayaan **99%** (sangat tinggi karena semua gejala penyakit terdeteksi).

**Rekomendasi:**
- **Pupuk:** NPK Phonska (MB=0.800, MD=0.150), Kompos (MB=0.750, MD=0.150)
- **Pestisida:** Agrept 20 WP (MB=0.900, MD=0.050), Bactocyn (MB=0.850, MD=0.100)

---

### KASUS 3: Petani Memilih Gejala G005 dan G007

**Input User:**
- ☑ G005: Pertumbuhan terhambat
- ☑ G007: Daun bergaris-garis coklat

#### Analisis:

**P001 (Blas):** Tidak ada gejala yang cocok → Skip

**P002 (Hawar):** Tidak ada gejala yang cocok → Skip

**P003 (Tungro):**
- G005: CF = 0.800 - 0.150 = **0.650**
- G007: CF = 0.750 - 0.200 = **0.550**

**P004 (Bercak):** Tidak ada gejala yang cocok → Skip

#### Perhitungan P003 (Tungro):

```
CF1 = 0.650
CF2 = 0.550

CF_combine = 0.650 + 0.550 × (1 - 0.650)
           = 0.650 + 0.550 × 0.350
           = 0.650 + 0.1925
           = 0.8425
```

Faktor kelengkapan:
- Gejala cocok: 2 dari 3 total gejala P003
- Completeness = 2/3 ≈ 0.667

```
Final_CF = 0.8425 × (0.7 + 0.3 × 0.667)
         = 0.8425 × (0.7 + 0.2)
         = 0.8425 × 0.9
         = 0.75825 ≈ 0.758
```

Konversi ke persentase:
```
Confidence = ((0.758 + 1) / 2) × 100%
           = (1.758 / 2) × 100%
           = 0.879 × 100%
           = 87.9% ≈ 88%
```

#### Kesimpulan Kasus 3:
Tanaman padi terdiagnosis terkena **Penyakit Tungro (P003)** dengan tingkat kepercayaan **88%**.

**Rekomendasi:**
- **Pupuk:** Kompos (MB=0.800, MD=0.100), NPK Phonska (MB=0.700, MD=0.200), KCl (MB=0.650, MD=0.250)
- **Pestisida:** Winder 50 EC (MB=0.800, MD=0.150) - Insektisida untuk mengendalikan vektor wereng

---

### KASUS 4: Petani Memilih Semua Gejala P001 (G001, G002, G006, G008)

**Input User:**
- ☑ G001: Daun berwarna kuning
- ☑ G002: Bercak coklat pada daun
- ☑ G006: Bulat-bulat kecil pada daun
- ☑ G008: Malai padi kosong

#### Perhitungan P001 (Blas) - 4 gejala:

**CF Individual:**
- G001: CF₁ = 0.800 - 0.100 = **0.700**
- G002: CF₂ = 0.900 - 0.050 = **0.850**
- G006: CF₃ = 0.850 - 0.100 = **0.750**
- G008: CF₄ = 0.750 - 0.150 = **0.600**

**Kombinasi Bertahap:**

```
Langkah 1: CF1 + CF2
CF_12 = 0.700 + 0.850 × (1 - 0.700)
      = 0.700 + 0.850 × 0.300
      = 0.700 + 0.255
      = 0.955

Langkah 2: CF_12 + CF3
CF_123 = 0.955 + 0.750 × (1 - 0.955)
       = 0.955 + 0.750 × 0.045
       = 0.955 + 0.03375
       = 0.98875

Langkah 3: CF_123 + CF4
CF_1234 = 0.98875 + 0.600 × (1 - 0.98875)
        = 0.98875 + 0.600 × 0.01125
        = 0.98875 + 0.00675
        = 0.9955
```

Faktor kelengkapan:
- Gejala cocok: 4 dari 4 total gejala P001
- Completeness = 4/4 = 1.0 (LENGKAP SEMPURNA!)

```
Final_CF = 0.9955 × (0.7 + 0.3 × 1.0)
         = 0.9955 × 1.0
         = 0.9955
```

Konversi ke persentase:
```
Confidence = ((0.9955 + 1) / 2) × 100%
           = (1.9955 / 2) × 100%
           = 0.99775 × 100%
           = 99.775% ≈ 99.8%
```

#### Kesimpulan Kasus 4:
Tanaman padi terdiagnosis terkena **Penyakit Blas (P001)** dengan tingkat kepercayaan **99.8%** (hampir sempurna).

---

## 4. TABEL RINGKASAN SEMUA SKENARIO

| No | Gejala Dipilih | Penyakit Terbanyak | CF Akhir | Confidence | Keterangan |
|----|----------------|-------------------|----------|------------|------------|
| 1 | G001, G002 | P001 (Blas) | 0.812 | 91% | 2 dari 4 gejala |
| 2 | G001, G003, G004 | P002 (Hawar) | 0.981 | 99% | 3 dari 3 gejala (lengkap) |
| 3 | G005, G007 | P003 (Tungro) | 0.758 | 88% | 2 dari 3 gejala |
| 4 | G001, G002, G006, G008 | P001 (Blas) | 0.9955 | 99.8% | 4 dari 4 gejala (lengkap) |
| 5 | G002, G006 | P004 (Bercak) | ? | ? | Lihat perhitungan di bawah |

---

### KASUS 5: Petani Memilih Gejala G002 dan G006

**Input User:**
- ☑ G002: Bercak coklat pada daun
- ☑ G006: Bulat-bulat kecil pada daun

#### Analisis:

**P001 (Blas):**
- G002: CF = 0.900 - 0.050 = **0.850**
- G006: CF = 0.850 - 0.100 = **0.750**

**P004 (Bercak Coklat):**
- G002: CF = 0.850 - 0.100 = **0.750**
- G006: CF = 0.700 - 0.200 = **0.500**

#### Perhitungan P001 (Blas):
```
CF1 = 0.850
CF2 = 0.750

CF_combine = 0.850 + 0.750 × (1 - 0.850)
           = 0.850 + 0.750 × 0.150
           = 0.850 + 0.1125
           = 0.9625

Completeness = 2/4 = 0.5
Final_CF = 0.9625 × (0.7 + 0.3 × 0.5)
         = 0.9625 × 0.85
         = 0.818125 ≈ 0.818

Confidence = ((0.818 + 1) / 2) × 100% = 90.9% ≈ 91%
```

#### Perhitungan P004 (Bercak Coklat):
```
CF1 = 0.750
CF2 = 0.500

CF_combine = 0.750 + 0.500 × (1 - 0.750)
           = 0.750 + 0.500 × 0.250
           = 0.750 + 0.125
           = 0.875

Completeness = 2/2 = 1.0 (LENGKAP!)
Final_CF = 0.875 × (0.7 + 0.3 × 1.0)
         = 0.875 × 1.0
         = 0.875

Confidence = ((0.875 + 1) / 2) × 100% = 93.75% ≈ 94%
```

#### Ranking:
| Peringkat | Penyakit | CF Akhir | Confidence |
|-----------|----------|----------|------------|
| 1 | **P004 - Bercak Coklat** | 0.875 | **94%** |
| 2 | P001 - Blas | 0.818 | 91% |

#### Kesimpulan Kasus 5:
Meskipun P001 memiliki CF combine lebih tinggi (0.9625 vs 0.875), **P004 (Bercak Coklat)** menang karena **kelengkapan gejala 100%** (2 dari 2), sedangkan P001 hanya 50% (2 dari 4). Ini menunjukkan pentingnya faktor kelengkapan dalam sistem ini.

---

## 5. INTERPRETASI NILAI CONFIDENCE

| Range CF | Range % | Label | Icon | Warna | Tindakan |
|----------|---------|-------|------|-------|----------|
| 0.8 - 1.0 | 90% - 100% | Sangat Tinggi | ✓✓ | Success (Hijau) | Segera ambil tindakan, kepercayaan sangat tinggi |
| 0.6 - 0.8 | 80% - 90% | Tinggi | ✓ | Success (Hijau) | Diagnosis dapat diandalkan |
| 0.4 - 0.6 | 70% - 80% | Sedang | ~ | Warning (Kuning) | Perlu observasi tambahan |
| 0.1 - 0.4 | 55% - 70% | Rendah | … | Warning (Kuning) | Pertimbangkan konsultasi ahli |
| 0.0 - 0.1 | 50% - 55% | Sangat Rendah | ? | Danger (Merah) | Diagnosis tidak meyakinkan |
| < 0.0 | < 50% | Tidak Direkomendasikan | ✗ | Danger (Merah) | Kemungkinan bukan penyakit ini |

---

## 6. IMPLEMENTASI DALAM KODE PHP

Berikut adalah cuplikan kode dari `CertaintyFactorEngine.php` yang mengimplementasikan rumus-rumus di atas:

```php
// Menghitung CF dari MB dan MD
public function calculateCf(float $mb, float $md): float
{
    return round($mb - $md, 6);
}

// Kombinasi dua nilai CF
public function combineCf(float $cf1, float $cf2): float
{
    if ($cf1 >= 0 && $cf2 >= 0) {
        $result = $cf1 + $cf2 * (1 - $cf1);
    } elseif ($cf1 <= 0 && $cf2 <= 0) {
        $result = $cf1 + $cf2 * (1 + $cf1);
    } else {
        $minAbs = min(abs($cf1), abs($cf2));
        $denominator = 1 - $minAbs;
        $result = ($cf1 + $cf2) / ($denominator == 0 ? 1 : $denominator);
    }
    return round($result, 6);
}

// Kombinasi multiple CF
public function combineMultipleCf(array $cfValues): float
{
    $result = array_shift($cfValues);
    foreach ($cfValues as $cf) {
        $result = $this->combineCf($result, $cf);
    }
    return $result;
}

// Hitung CF diagnosis dengan faktor kelengkapan
public function calculateDiagnosisCf(
    Collection $matchedSymptoms,
    Collection $allDiseaseSymptoms,
    array $userSymptomWeights = []
): float {
    // Hitung CF untuk setiap gejala
    $cfValues = [];
    foreach ($matchedSymptoms as $symptom) {
        $mb = (float) ($symptom->pivot->mb ?? 0.7);
        $md = (float) ($symptom->pivot->md ?? 0.1);
        $cfRule = $this->calculateCf($mb, $md);
        
        // Apply user weight jika ada
        if (isset($userSymptomWeights[$symptom->id])) {
            $normalizedWeight = min(1, max(0, $userSymptomWeights[$symptom->id] / 100));
            $cfRule = $cfRule * $normalizedWeight;
        }
        
        $cfValues[] = $cfRule;
    }
    
    // Kombinasi semua CF
    $combinedCf = $this->combineMultipleCf($cfValues);
    
    // Faktor kelengkapan
    $completenessFactor = $matchedSymptoms->count() / max(1, $allDiseaseSymptoms->count());
    
    // Adjust CF berdasarkan kelengkapan
    $finalCf = $combinedCf * (0.7 + 0.3 * $completenessFactor);
    
    return $finalCf;
}

// Konversi ke persentase
public function toPercentage(float $cf): float
{
    return round((($cf + 1) / 2) * 100, 2);
}
```

---

## 7. REKOMENDASI PUPUK DAN PESTISIDA BERBASIS CF

Setelah diagnosis selesai, sistem menghitung rekomendasi pupuk dan pestisida menggunakan CF juga:

### Contoh: Diagnosis P001 (Blas) dengan CF = 0.91

**Rekomendasi Pupuk:**

| Pupuk | MB | MD | CF | CF_Rekomendasi |
|-------|-----|-----|-----|----------------|
| KCl | 0.850 | 0.100 | 0.750 | 0.750 × 0.91 = **0.683** (68%) |
| NPK Phonska | 0.750 | 0.150 | 0.600 | 0.600 × 0.91 = **0.546** (55%) |
| Kompos | 0.700 | 0.200 | 0.500 | 0.500 × 0.91 = **0.455** (46%) |

**Rekomendasi Pestisida:**

| Pestisida | MB | MD | CF | CF_Rekomendasi |
|-----------|-----|-----|-----|----------------|
| Filia 525 SE | 0.950 | 0.030 | 0.920 | 0.920 × 0.91 = **0.837** (84%) |
| Amistar Top | 0.900 | 0.050 | 0.850 | 0.850 × 0.91 = **0.774** (77%) |

Rumus:
```
CF_rekomendasi = CF_produk × CF_diagnosis
```

---

## 8. KESIMPULAN

Sistem pakar diagnosis penyakit padi ini menggunakan metode **Certainty Factor (CF)** dengan fitur-fitur berikut:

1. **Perhitungan CF dasar:** CF = MB - MD
2. **Kombinasi multiple gejala:** CF_combine = CF1 + CF2 × (1 - CF1)
3. **Faktor kelengkapan gejala:** Meningkatkan akurasi jika lebih banyak gejala terdeteksi
4. **Bobot user:** Memungkinkan petani menyatakan tingkat keyakinan terhadap gejala yang dipilih
5. **Konversi ke persentase:** Memudahkan interpretasi oleh pengguna awam
6. **Rekomendasi berbasis CF:** Pupuk dan pestisida direkomendasikan berdasarkan efektivitasnya terhadap penyakit yang terdiagnosis

Dengan pendekatan ini, sistem dapat memberikan diagnosis yang akurat dan transparan, dengan tingkat kepercayaan yang dapat dipahami oleh petani.

---

**Dibuat berdasarkan proyek Sistem Pakar Diagnosis Penyakit Padi**  
**Metode: Certainty Factor (CF)**  
**Tanggal:** {{ date('Y-m-d') }}
