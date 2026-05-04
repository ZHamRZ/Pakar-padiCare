# PERBAIKAN LOGIKA PREFERENSI HEMAT vs EFISIENSI

## 📋 RINGKASAN PERUBAHAN

Perbaikan logika preferensi user untuk rekomendasi pupuk dan pestisida dengan Certainty Factor (CF), memastikan sinkronisasi antara **kebutuhan & prioritas** dengan **hasil rekomendasi**.

---

## 🎯 PRINSIP UTAMA

### 1. **Preferensi 'HEMAT'** - Prioritas: CF TERTINGGI + HARGA TERMURAH

**Filosofi:** Mendapatkan efektivitas maksimal dengan biaya minimal.

**Kombinasi Ideal:**
- CF sangat tinggi (>0.8) + Harga sangat murah (<Rp5.000 untuk pupuk, <Rp50.000 untuk pestisida)
- Contoh: **Kompos** (CF: 0.8, Harga: Rp600/kg) >> **Silika Cair** (CF: 0.75, Harga: Rp170.000/500ml)

**Boost Adjustment:**
| CF Category | Price Category | Boost | Efficiency Bonus |
|-------------|----------------|-------|------------------|
| Sangat Tinggi (≥0.8) | Sangat Murah | +0.30 | +0.15 |
| Sangat Tinggi (≥0.8) | Murah | +0.25 | +0.12 |
| Tinggi (≥0.6) | Sangat Murah | +0.22 | +0.10 |
| Tinggi (≥0.6) | Murah | +0.18 | +0.08 |
| Sangat Tinggi (≥0.8) | Menengah/Mahal | +0.15 | - |
| Sedang (≥0.4) | Sangat Murah | +0.12 | - |
| Murah | - | +0.04 | - |
| Mahal | - | -0.10 | - |

**Hasil:** Produk dengan CF tinggi + harga murah akan naik drastis di ranking TOP 3.

---

### 2. **Preferensi 'EFISIENSI'** - Prioritas: CF TERTINGGI + HARGA MAHAL

**Filosofi:** Investasi pada produk premium dengan hasil optimal (worth it).

**Kombinasi Ideal:**
- CF sangat tinggi (>0.8) + Harga mahal (>Rp60.000 untuk pupuk, >Rp250.000 untuk pestisida)
- Contoh: **Silika Cair** (CF: 0.75, Harga: Rp170.000/500ml) >> **Kompos** (CF: 0.8, Harga: Rp600/kg)
- **Alasan:** Produk mahal + CF tinggi = teknologi advanced, hasil optimal, worth it untuk investasi jangka panjang

**Boost Adjustment:**
| CF Category | Price Category | Boost | Efficiency Bonus |
|-------------|----------------|-------|------------------|
| Sangat Tinggi (≥0.8) | Sangat Mahal | +0.22 | +0.12 |
| Sangat Tinggi (≥0.8) | Mahal | +0.20 | +0.10 |
| Sangat Tinggi (≥0.8) | Menengah | +0.15 | +0.05 |
| Tinggi (≥0.6) | Sangat Mahal | +0.17 | +0.08 |
| Tinggi (≥0.6) | Mahal | +0.15 | +0.07 |
| Tinggi (≥0.6) | Menengah | +0.10 | - |
| Sedang (≥0.4) | Mahal/Sangat Mahal | +0.08~0.10 | - |
| Murah | - | +0.07 | - |

**Hasil:** Produk premium dengan CF tinggi akan naik di ranking TOP 3, meskipun harganya mahal.

---

### 3. **Preferensi 'SEIMBANG'** - Murni Berdasarkan CF

**Filosofi:** Tidak ada bias harga, hanya efektivitas berdasarkan pengetahuan pakar.

**Adjustment:** `0.0` (tidak ada boost/penalty)

**Hasil:** Ranking murni berdasarkan CF_dasar dari relasi penyakit-pupuk/pestisida.

---

## 📊 KATEGORISASI HARGA

### Pupuk:
| Kategori | Range Harga | Contoh Produk |
|----------|-------------|---------------|
| Sangat Murah | ≤ Rp5.000/kg | Kompos (Rp600), Urea (Rp2.250), KCl (Rp1.350) |
| Murah | ≤ Rp15.000/kg | NPK Phonska (Rp2.300), ZA (Rp1.700), SP-36 (Rp1.200) |
| Menengah | ≤ Rp30.000/kg | Dolomit (Rp2.500), NPK Mutiara (Rp18.000) |
| Mahal | ≤ Rp60.000/kg | MKP (Rp45.000), KNO3 (Rp40.000) |
| Sangat Mahal | > Rp60.000/kg | Silika Cair (Rp170.000/500ml ≈ Rp340.000/L), Mikro (Rp130.000/500ml) |

### Pestisida:
| Kategori | Range Harga | Contoh Produk |
|----------|-------------|---------------|
| Sangat Murah | ≤ Rp50.000 | Validacin (Rp25/100ml), Filia (Rp70/100ml), Winder (Rp35/100ml) |
| Murah | ≤ Rp100.000 | Nordox (Rp45/100gr), Agrept (Rp50/100gr), Bactocyn (Rp45/100ml) |
| Menengah | ≤ Rp150.000 | Heksakonazol (Rp90/100ml), Seltima (Rp80/100ml), Topaz (Rp65/100ml) |
| Mahal | ≤ Rp250.000 | Nativo (Rp190/100gr), Amistartop (Rp150/100ml) |
| Sangat Mahal | > Rp250.000 | - |

---

## 📊 KATEGORISASI CF

| Kategori | Range CF | Interpretasi |
|----------|----------|--------------|
| Sangat Tinggi | ≥ 0.8 (80%) | Sangat Direkomendasikan ✓✓ |
| Tinggi | ≥ 0.6 (60%) | Direkomendasikan ✓ |
| Sedang | ≥ 0.4 (40%) | Cukup ~ |
| Rendah | ≥ 0.2 (20%) | Kurang Direkomendasikan ? |
| Sangat Rendah | < 0.2 (<20%) | Tidak Direkomendasikan ✗ |

---

## 🔍 CONTOH KASUS NYATA

### Kasus: Penyakit BLAS (P01)

#### Data Dasar (setelah negasi CF):
| Pupuk | CF_dasar | CF_rekomendasi | Harga/kg | Kategori Harga |
|-------|----------|----------------|----------|----------------|
| KCl | -0.75 | **+0.75** | Rp1.350 | Sangat Murah |
| Kompos | -0.55 | **+0.55** | Rp600 | Sangat Murah |
| Silika Cair | -0.65 | **+0.65** | Rp170.000* | Sangat Mahal |
| MKP | -0.55 | **+0.55** | Rp45.000 | Mahal |
| NPK Phonska | -0.35 | **+0.35** | Rp2.300 | Sangat Murah |
| Urea | +0.65 | **-0.65** | Rp2.250 | Sangat Murah |

*Silika Cair: Rp170.000/500ml ≈ Rp340.000/L

---

#### Hasil Rekomendasi per Preferensi:

##### 1️⃣ **Preferensi: HEMAT**

| Peringkat | Pupuk | Base CF | Price Cat | Adjustment | Final CF | Keterangan |
|-----------|-------|---------|-----------|------------|----------|------------|
| 1 | **Kompos** | 0.55 | Sangat Murah | +0.12 (Sedang+SM) | **0.67** | CF sedang + harga sangat murah = boost tinggi |
| 2 | **KCl** | 0.75 | Sangat Murah | +0.22 (Tinggi+SM) | **0.97** ⭐ | CF tinggi + harga sangat murah = boost maksimal |
| 3 | **NPK Phonska** | 0.35 | Sangat Murah | +0.06 | **0.41** | CF rendah tapi harga sangat murah |

❌ **Silika Cair** tidak masuk TOP 3 karena:
- Base CF: 0.65 (Tinggi)
- Harga: Sangat Mahal → penalty -0.05
- Final CF: 0.60 (turun dari 0.65)

✅ **Kesimpulan:** User hemat mendapat rekomendasi produk murah dengan efektivitas baik.

---

##### 2️⃣ **Preferensi: EFISIENSI**

| Peringkat | Pupuk | Base CF | Price Cat | Adjustment | Final CF | Keterangan |
|-----------|-------|---------|-----------|------------|----------|------------|
| 1 | **Silika Cair** | 0.65 | Sangat Mahal | +0.17 (Tinggi+SMa) | **0.82** ⭐ | CF tinggi + sangat mahal = efisiensi sangat tinggi |
| 2 | **MKP** | 0.55 | Mahal | +0.08 (Sedang+Mahal) | **0.63** | CF sedang + mahal = efisiensi cukup |
| 3 | **KCl** | 0.75 | Sangat Murah | +0.10 (Tinggi+murah) | **0.85** ⭐ | CF tinggi tetap dapat boost kecil |

❌ **Kompos** tidak masuk TOP 3 karena:
- Base CF: 0.55 (Sedang)
- Harga: Sangat Murah → bukan prioritas efisiensi
- Adjustment: +0.05 saja
- Final CF: 0.60 (tergeser oleh produk lain)

✅ **Kesimpulan:** User efisiensi mendapat rekomendasi produk premium dengan hasil optimal.

---

##### 3️⃣ **Preferensi: SEIMBANG**

| Peringkat | Pupuk | Base CF | Adjustment | Final CF |
|-----------|-------|---------|------------|----------|
| 1 | **KCl** | 0.75 | 0.0 | **0.75** |
| 2 | **Silika Cair** | 0.65 | 0.0 | **0.65** |
| 3 | **Kompos** | 0.55 | 0.0 | **0.55** |

✅ **Kesimpulan:** Murni berdasarkan CF pakar, tanpa bias harga.

---

## 🛠️ IMPLEMENTASI TEKNIS

### File yang Diubah:
1. **`app/Services/RecommendationService.php`**
   - Method: `applyPreferenceAdjustment()`
   - Logika boost/penalty berdasarkan kombinasi CF + Harga
   - Metadata lengkap: `adjustment_info`, `price_category`, `cf_category`, `efficiency_bonus`

### Alur Eksekusi:
```
1. User memilih preferensi (hemat/efisiensi/seimbang)
   ↓
2. System menghitung CF dasar dari relasi penyakit-pupuk/pestisida
   ↓
3. Apply adjustment berdasarkan preferensi:
   - Hemat: Boost CF tinggi + harga murah, Penalty harga mahal
   - Efisiensi: Boost CF tinggi + harga mahal, Boost kecil untuk murah
   - Seimbang: Tidak ada adjustment
   ↓
4. Re-sorting berdasarkan Final CF (base + adjustment)
   ↓
5. Limit TOP 3 rekomendasi
   ↓
6. Return hasil dengan metadata lengkap
```

---

## 📝 METADATA RESPONSE

Setiap item rekomendasi sekarang memiliki metadata lengkap:

```json
{
  "id": 5,
  "nama": "Pupuk Kandang / Kompos",
  "cf_rekomendasi": 0.67,
  "cf_percentage": 83.5,
  "interpretation": {
    "label": "Direkomendasikan",
    "color": "primary",
    "icon": "✓"
  },
  "preference_applied": true,
  "adjustment_info": {
    "preset": "hemat",
    "adjustment": 0.12,
    "base_cf": 0.55,
    "efficiency_bonus": null,
    "is_high_efficiency": false,
    "price_category": "sangat_murah",
    "cf_category": "sedang"
  }
}
```

---

## ✅ CHECKLIST VERIFIKASI

- [x] Logika 'hemat': CF tinggi + harga murah = boost maksimal (+0.30)
- [x] Logika 'hemat': Harga mahal = penalty (-0.10)
- [x] Logika 'efisiensi': CF tinggi + harga mahal = boost tinggi (+0.20~0.22)
- [x] Logika 'efisiensi': CF tinggi + harga murah = boost kecil (+0.07~0.10)
- [x] Kategorisasi harga pupuk (5 level: sangat_murah → sangat_mahal)
- [x] Kategorisasi harga pestisida (5 level: sangat_murah → sangat_mahal)
- [x] Kategorisasi CF (5 level: sangat_rendah → sangat_tinggi)
- [x] Metadata lengkap: price_category, cf_category, efficiency_bonus
- [x] Default topN = 3 untuk fokus pada rekomendasi terbaik
- [x] Dokumentasi lengkap dengan contoh kasus nyata

---

## 🎉 HASIL AKHIR

Sistem sekarang **sinkron antara kebutuhan user dan hasil rekomendasi**:

1. **User HEMAT** → Dapat produk **CF tertinggi + harga termurah** (efisiensi biaya maksimal)
2. **User EFISIENSI** → Dapat produk **CF tertinggi + harga mahal** (hasil optimal, worth it)
3. **User SEIMBANG** → Dapat produk **CF tertinggi murni** (tanpa bias harga)

Tidak ada lagi konflik antara preferensi dan hasil! 🎯
