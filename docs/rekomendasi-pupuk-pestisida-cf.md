# Dokumentasi Sistem Rekomendasi Pupuk dan Pestisida dengan Certainty Factor

## Ringkasan Eksekutif

Dokumen ini menjelaskan perbaikan logika sistem rekomendasi pupuk dan pestisida pada aplikasi Laravel SPK Padi menggunakan metode **Certainty Factor (CF)** dengan memperhatikan perbedaan mendasar antara:
- **Pupuk sebagai PENYEBAB** (memerlukan transformasi nilai CF)
- **Pestisida sebagai SOLUSI** (tidak memerlukan transformasi)

---

## 1. Konsep Dasar Certainty Factor

### 1.1 Definisi

**Certainty Factor (CF)** adalah ukuran keyakinan yang didefinisikan sebagai:

```
CF = MB - MD
```

Dimana:
- **MB (Measure of Belief)**: Ukuran kepercayaan terhadap hipotesis (range: 0-1)
- **MD (Measure of Disbelief)**: Ukuran ketidakpercayaan terhadap hipotesis (range: 0-1)

### 1.2 Interpretasi Nilai CF

| Nilai CF | Interpretasi |
|----------|--------------|
| CF > 0   | Mendukung hipotesis |
| CF = 0   | Netral |
| CF < 0   | Menolak hipotesis |

Range nilai CF: **-1 sampai 1**
- CF = 1.0 → Kepastian mutlak benar
- CF = 0.0 → Ketidakpastian penuh
- CF = -1.0 → Kepastian mutlak salah

---

## 2. Perbedaan Makna Data: Pupuk vs Pestisida

### 2.1 Pupuk (Sebagai Penyebab)

Dalam konteks sistem ini, relasi antara gejala dan pupuk menunjukkan **seberapa besar pupuk tersebut menyebabkan atau memperparah gejala**.

| CF Penyebab | Makna | Rekomendasi |
|-------------|-------|-------------|
| CF > 0 (positif) | Pupuk memperparah kondisi | **TIDAK direkomendasikan** |
| CF = 0 (netral) | Pupuk tidak berpengaruh | Netral |
| CF < 0 (negatif) | Pupuk membantu/mengurangi gejala | **DIREKOMENDASIKAN** |

**⚠️ PENTING**: CF penyebab **TIDAK BOLEH** langsung digunakan sebagai rekomendasi!

### 2.2 Pestisida (Sebagai Solusi)

Relasi antara gejala dan pestisida menunjukkan **efektivitas pestisida dalam mengatasi gejala**.

| CF Solusi | Makna | Rekomendasi |
|-----------|-------|-------------|
| CF > 0 (positif) | Pestisida efektif | **DIREKOMENDASIKAN** |
| CF = 0 (netral) | Pestisida netral | Netral |
| CF < 0 (negatif) | Pestisida tidak efektif | **TIDAK direkomendasikan** |

**✓ CF solusi dapat LANGSUNG digunakan untuk rekomendasi tanpa transformasi.**

---

## 3. Transformasi Nilai untuk Rekomendasi

### 3.1 Transformasi untuk Pupuk

Karena CF pupuk menunjukkan "penyebab", kita perlu membalik nilainya untuk mendapatkan "rekomendasi":

```
CF_rekomendasi_pupuk = -CF_penyebab
```

**Contoh:**

| CF Penyebab | Transformasi | CF Rekomendasi | Status |
|-------------|--------------|----------------|--------|
| +0.8 | -(+0.8) | -0.8 | Tidak Direkomendasikan |
| +0.5 | -(+0.5) | -0.5 | Tidak Direkomendasikan |
| 0.0 | -(0.0) | 0.0 | Netral |
| -0.5 | -(-0.5) | +0.5 | Direkomendasikan |
| -0.8 | -(-0.8) | +0.8 | Sangat Direkomendasikan |

### 3.2 Transformasi untuk Pestisida

Tidak ada transformasi untuk pestisida:

```
CF_rekomendasi_pestisida = CF_solusi
```

**Contoh:**

| CF Solusi | CF Rekomendasi | Status |
|-----------|----------------|--------|
| +0.9 | +0.9 | Sangat Direkomendasikan |
| +0.6 | +0.6 | Direkomendasikan |
| 0.0 | 0.0 | Netral |
| -0.4 | -0.4 | Tidak Direkomendasikan |

---

## 4. Perhitungan Multi-Gejala

Ketika user memilih lebih dari satu gejala, sistem harus mengombinasikan semua CF menggunakan rumus kombinasi sequential.

### 4.1 Rumus Kombinasi CF

#### Kasus 1: Kedua CF Positif (atau nol)
```
CFcombine = CF1 + CF2 * (1 - CF1)
```

**Contoh:**
```
CF1 = 0.5, CF2 = 0.6
CFcombine = 0.5 + 0.6 * (1 - 0.5)
          = 0.5 + 0.6 * 0.5
          = 0.5 + 0.3
          = 0.8
```

#### Kasus 2: Kedua CF Negatif (atau nol)
```
CFcombine = CF1 + CF2 * (1 + CF1)
```

**Contoh:**
```
CF1 = -0.5, CF2 = -0.6
CFcombine = -0.5 + (-0.6) * (1 + (-0.5))
          = -0.5 + (-0.6) * 0.5
          = -0.5 - 0.3
          = -0.8
```

#### Kasus 3: CF Berbeda Tanda
```
CFcombine = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))
```

**Contoh:**
```
CF1 = 0.8, CF2 = -0.4
min(|0.8|, |-0.4|) = min(0.8, 0.4) = 0.4
CFcombine = (0.8 + (-0.4)) / (1 - 0.4)
          = 0.4 / 0.6
          = 0.6667
```

### 4.2 Kombinasi Sequential untuk N Gejala

Untuk lebih dari 2 gejala, lakukan kombinasi secara iteratif:

```
result = CF[0]
for i = 1 to n-1:
    result = combineCf(result, CF[i])
```

**Contoh dengan 3 gejala:**
```
CF values: [0.5, 0.6, 0.7]

Step 1: combine(0.5, 0.6) = 0.5 + 0.6 * (1 - 0.5) = 0.8
Step 2: combine(0.8, 0.7) = 0.8 + 0.7 * (1 - 0.8) = 0.8 + 0.14 = 0.94

Final CF = 0.94
```

---

## 5. Alur Sistem Lengkap

### 5.1 Flowchart Alur Rekomendasi

```
┌─────────────────┐
│ User memilih    │
│ beberapa gejala │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────┐
│ Sistem mengambil semua relasi:  │
│ - gejala ↔ pupuk (gejala_pupuk) │
│ - gejala ↔ pestisida            │
│   (gejala_pestisida)            │
└────────┬────────────────────────┘
         │
         ├──────────────────┬──────────────────┐
         │                  │                  │
         ▼                  ▼                  ▼
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│ Untuk setiap    │ │ Untuk setiap    │ │ ... lanjut ke   │
│ pupuk:          │ │ pestisida:      │ │ gejala berikutnya│
│ - Ambil semua   │ │ - Ambil semua   │ │                 │
│   CF dari       │ │   CF dari       │ │                 │
│   gejala        │ │   gejala        │ │                 │
│   terpilih      │ │   terpilih      │ │                 │
│ - Kombinasi CF  │ │ - Kombinasi CF  │ │                 │
│ - Hasil =       │ │ - Hasil =       │ │                 │
│   CF_penyebab   │ │   CF_solusi     │ │                 │
│ - Transformasi: │ │ - TIDAK ada     │ │                 │
│   CF_rekomendasi│ │   transformasi  │ │                 │
│   = -CF_penyebab│ │                 │ │                 │
└────────┬────────┘ └────────┬────────┘ └─────────────────┘
         │                  │
         ▼                  ▼
┌─────────────────────────────────┐
│ Ranking & Filtering:            │
│ - Urutkan berdasarkan CF        │
│   tertinggi                     │
│ - Filter hanya CF > 0           │
│ - Ambil top N (opsional)        │
└────────┬────────────────────────┘
         │
         ▼
┌─────────────────────────────────┐
│ Output:                         │
│ - Rekomendasi pupuk (CF > 0)    │
│ - Rekomendasi pestisida (CF > 0)│
│ - Label interpretasi            │
└─────────────────────────────────┘
```

### 5.2 Pseudocode

```php
// Input: array symptomIds
function calculateRecommendations(symptomIds):
    
    // === PUPUK ===
    fertilizerRecommendations = []
    
    for each fertilizer in allFertilizers:
        cfValues = []
        
        for each symptom in fertilizer.symptoms where symptom.id in symptomIds:
            mb = symptom.pivot.mb
            md = symptom.pivot.md
            
            // Normalisasi jika MB + MD > 1
            if mb + md > 1:
                total = mb + md
                mb = mb / total
                md = md / total
            
            // Hitung CF individual
            cf = mb - md
            cfValues.append(cf)
        
        if cfValues is not empty:
            // Kombinasi semua CF
            cfPenyebabTotal = combineMultipleCf(cfValues)
            
            // TRANSFORMASI untuk pupuk
            cfRekomendasi = -cfPenyebabTotal
            
            // Normalisasi ke range [-1, 1]
            cfRekomendasi = normalize(cfRekomendasi, -1, 1)
            
            fertilizerRecommendations.append({
                fertilizer: fertilizer,
                cf_rekomendasi: cfRekomendasi,
                interpretation: getLabel(cfRekomendasi)
            })
    
    // === PESTISIDA ===
    pesticideRecommendations = []
    
    for each pesticide in allPesticides:
        cfValues = []
        
        for each symptom in pesticide.symptoms where symptom.id in symptomIds:
            mb = symptom.pivot.mb
            md = symptom.pivot.md
            
            // Normalisasi jika MB + MD > 1
            if mb + md > 1:
                total = mb + md
                mb = mb / total
                md = md / total
            
            // Hitung CF individual
            cf = mb - md
            cfValues.append(cf)
        
        if cfValues is not empty:
            // Kombinasi semua CF
            cfSolusiTotal = combineMultipleCf(cfValues)
            
            // TIDAK ADA TRANSFORMASI untuk pestisida
            cfRekomendasi = cfSolusiTotal
            
            // Normalisasi ke range [-1, 1]
            cfRekomendasi = normalize(cfRekomendasi, -1, 1)
            
            pesticideRecommendations.append({
                pesticide: pesticide,
                cf_rekomendasi: cfRekomendasi,
                interpretation: getLabel(cfRekomendasi)
            })
    
    // FILTER dan RANKING
    fertilizerRecommendations = filter(fertilizerRecommendations, cf > 0)
    pesticideRecommendations = filter(pesticideRecommendations, cf > 0)
    
    sort descending by cf_rekomendasi
    
    return {
        pupuk: fertilizerRecommendations,
        pestisida: pesticideRecommendations
    }
```

---

## 6. Label Interpretasi

Sistem memberikan label berdasarkan nilai CF rekomendasi:

| Range CF | Label | Badge Class | Deskripsi |
|----------|-------|-------------|-----------|
| CF > 0.7 | Sangat Direkomendasikan | bg-success | Rekomendasi sangat kuat |
| 0.4 < CF ≤ 0.7 | Direkomendasikan | bg-primary | Rekomendasi kuat |
| 0.1 < CF ≤ 0.4 | Cukup | bg-warning | Rekomendasi moderat |
| 0 < CF ≤ 0.1 | Kurang Direkomendasikan | bg-info | Rekomendasi lemah |
| CF ≤ 0 | Tidak Direkomendasikan | bg-danger | Tidak direkomendasikan |

---

## 7. Implementasi Laravel

### 7.1 File Service Baru

File `app/Services/FertilizerPesticideRecommendationEngine.php` telah dibuat dengan method utama:

```php
// Hitung semua rekomendasi
$engine->calculateAllRecommendations(
    symptomIds: [1, 2, 3],
    topN: 3,              // Opsional: batasi top 3
    onlyPositive: true    // Hanya tampilkan CF > 0
);

// Hitung rekomendasi pupuk saja
$engine->calculateFertilizerRecommendations([1, 2, 3]);

// Hitung rekomendasi pestisida saja
$engine->calculatePesticideRecommendations([1, 2, 3]);

// Detail perhitungan untuk satu item
$engine->calculateSingleFertilizerDetail($fertilizerId, [1, 2, 3]);
$engine->calculateSinglePesticideDetail($pesticideId, [1, 2, 3]);
```

### 7.2 Integrasi dengan Controller

Contoh penggunaan di controller:

```php
use App\Services\FertilizerPesticideRecommendationEngine;

class RekomendasiController extends Controller
{
    public function show(Request $request, FertilizerPesticideRecommendationEngine $engine)
    {
        $symptomIds = $request->input('gejala', []);
        
        $result = $engine->calculateAllRecommendations(
            symptomIds: $symptomIds,
            topN: 5,
            onlyPositive: true
        );
        
        return view('user.rekomendasi.hasil', [
            'pupuk' => $result['pupuk'],
            'pestisida' => $result['pestisida'],
            'summary' => $result['summary'],
        ]);
    }
}
```

### 7.3 Contoh Output

```json
{
  "pupuk": [
    {
      "id": 1,
      "kode": "PUP-001",
      "nama": "Urea",
      "cf_penyebab_total": -0.65,
      "cf_rekomendasi": 0.65,
      "cf_percentage": 82.5,
      "interpretation": {
        "label": "Direkomendasikan",
        "color": "primary",
        "icon": "✓"
      },
      "peringkat": 1,
      "matched_symptoms_count": 3
    }
  ],
  "pestisida": [
    {
      "id": 2,
      "kode": "PES-002",
      "nama": "Abamectin",
      "cf_solusi_total": 0.85,
      "cf_rekomendasi": 0.85,
      "cf_percentage": 92.5,
      "interpretation": {
        "label": "Sangat Direkomendasikan",
        "color": "success",
        "icon": "✓✓"
      },
      "peringkat": 1,
      "matched_symptoms_count": 4
    }
  ],
  "summary": {
    "total_gejala": 5,
    "total_pupuk_direkomendasikan": 3,
    "total_pestisida_direkomendasikan": 4,
    "filter_positive_only": true,
    "top_n": 5
  }
}
```

---

## 8. Testing

File test `tests/Unit/Services/FertilizerPesticideRecommendationEngineTest.php` mencakup:

1. ✅ Test transformasi CF untuk pupuk
2. ✅ Test tidak ada transformasi untuk pestisida
3. ✅ Test kombinasi CF positif
4. ✅ Test kombinasi CF negatif
5. ✅ Test kombinasi CF berbeda tanda
6. ✅ Test label interpretasi
7. ✅ Test kombinasi sequential multi-gejala
8. ✅ Test normalisasi CF
9. ✅ Test konversi CF ke persentase
10. ✅ Test validasi konsistensi MB+MD
11. ✅ Test flow lengkap perhitungan pupuk
12. ✅ Test flow lengkap perhitungan pestisida

Jalankan test:
```bash
php artisan test --filter FertilizerPesticideRecommendationEngineTest
```

---

## 9. Contoh Perhitungan Manual

### Skenario Diagnosis

**Gejala yang dipilih user:**
- G01: Daun menguning (MB=0.8, MD=0.1)
- G02: Pertumbuhan terhambat (MB=0.7, MD=0.2)
- G03: Batang lemah (MB=0.6, MD=0.3)

### Perhitungan untuk Pupuk Urea

**Relasi gejala-pupuk:**
| Gejala | MB | MD | CF = MB-MD |
|--------|----|----|------------|
| G01 | 0.7 | 0.2 | 0.5 |
| G02 | 0.6 | 0.3 | 0.3 |
| G03 | 0.8 | 0.1 | 0.7 |

**Kombinasi CF penyebab:**
```
Step 1: combine(0.5, 0.3) 
      = 0.5 + 0.3 * (1 - 0.5)
      = 0.5 + 0.15
      = 0.65

Step 2: combine(0.65, 0.7)
      = 0.65 + 0.7 * (1 - 0.65)
      = 0.65 + 0.245
      = 0.895

CF_penyebab_total = 0.895
```

**Transformasi untuk rekomendasi:**
```
CF_rekomendasi = -CF_penyebab
               = -0.895
```

**Interpretasi:**
- CF = -0.895 → **Tidak Direkomendasikan**
- Artinya: Urea cenderung memperparah gejala yang dialami

### Perhitungan untuk Pestisida Abamectin

**Relasi gejala-pestisida:**
| Gejala | MB | MD | CF = MB-MD |
|--------|----|----|------------|
| G01 | 0.9 | 0.1 | 0.8 |
| G02 | 0.8 | 0.2 | 0.6 |
| G03 | 0.7 | 0.2 | 0.5 |

**Kombinasi CF solusi:**
```
Step 1: combine(0.8, 0.6)
      = 0.8 + 0.6 * (1 - 0.8)
      = 0.8 + 0.12
      = 0.92

Step 2: combine(0.92, 0.5)
      = 0.92 + 0.5 * (1 - 0.92)
      = 0.92 + 0.04
      = 0.96

CF_solusi_total = 0.96
```

**Tidak ada transformasi:**
```
CF_rekomendasi = CF_solusi = 0.96
```

**Interpretasi:**
- CF = 0.96 → **Sangat Direkomendasikan**
- Artinya: Abamectin sangat efektif mengatasi gejala yang dialami

---

## 10. Kesimpulan

Perbaikan logika sistem ini memastikan:

1. ✅ **Konsistensi ilmiah**: CF dihitung sesuai rumus standar Certainty Factor
2. ✅ **Perbedaan makna**: Pupuk (penyebab) dan pestisida (solusi) diperlakukan berbeda
3. ✅ **Transformasi tepat**: CF pupuk ditransformasi dengan negasi, pestisida tidak
4. ✅ **Multi-gejala support**: Kombinasi CF dilakukan secara sequential
5. ✅ **Filtering otomatis**: Hanya rekomendasi dengan CF > 0 ditampilkan
6. ✅ **Label interpretasi**: User mendapat penjelasan yang mudah dipahami
7. ✅ **Transparansi**: Langkah perhitungan dapat ditampilkan untuk audit

Sistem sekarang menghasilkan rekomendasi yang **logis, ilmiah, dan dapat dipertanggungjawabkan** untuk diagnosis dan rekomendasi pada tanaman padi.
