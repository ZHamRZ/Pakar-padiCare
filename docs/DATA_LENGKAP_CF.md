# 📊 Data Lengkap Sistem Pakar - Certainty Factor

## 1. DATA PENYAKIT (5 Penyakit)

| Kode | Nama Penyakit | Deskripsi |
|------|---------------|-----------|
| P01 | Blast (Blas) | Penyakit yang disebabkan jamur Pyricularia oryzae. Umumnya menyerang daun dan leher malai padi. |
| P02 | Hawar Daun Bakteri (Kresek) | Penyakit yang disebabkan bakteri Xanthomonas oryzae pv. oryzae. |
| P03 | Tungro | Penyakit virus kompleks RTBV dan RTSV yang ditularkan oleh hama wereng hijau (Nephotettix virescens). |
| P04 | Busuk Pelepah (Sheath Blight) | Penyakit yang disebabkan jamur Rhizoctonia solani dan menyerang pelepah daun padi. |
| P05 | Bercak Coklat (Brown Spot) | Penyakit yang disebabkan jamur Bipolaris oryzae, dahulu dikenal sebagai Helminthosporium oryzae. |

---

## 2. DATA GEJALA (15 Gejala)

| Kode | Nama Gejala |
|------|-------------|
| G01 | Bercak belah ketupat (ujung runcing) pada daun |
| G02 | Leher malai busuk, berubah warna coklat atau hitam dan patah |
| G03 | Bulir padi hampa atau tidak berisi |
| G04 | Daun menguning mulai dari ujung dan tepi (layu) |
| G05 | Tepi daun mengering, bergelombang, dan berwarna kelabu |
| G06 | Seluruh tanaman layu mendadak (serangan berat) |
| G07 | Tanaman menjadi sangat kerdil dibanding tanaman sehat |
| G08 | Daun berubah warna menjadi kuning oranye |
| G09 | Jumlah anakan berkurang drastis dan malai tidak keluar |
| G10 | Bercak oval keabu-abuan pada pelepah (dekat air) |
| G11 | Bercak meluas ke atas membentuk pola seperti awan |
| G12 | Batang tanaman membusuk dan mudah rebah |
| G13 | Bercak coklat oval merata (seperti biji wijen) |
| G14 | Bercak hitam atau coklat pada kulit gabah |
| G15 | Daun mengering dan gugur lebih cepat |

---

## 3. RELASI PENYAKIT - GEJALA dengan CF

### 3.1 Penyakit P01 - Blast (Blas)
| Gejala | MB | MD | CF = MB - MD |
|--------|----|----|--------------|
| G01 - Bercak belah ketupat (ujung runcing) pada daun | 0.700 | 0.100 | **0.600** |
| G02 - Leher malai busuk, berubah warna coklat atau hitam dan patah | 0.700 | 0.100 | **0.600** |
| G03 - Bulir padi hampa atau tidak berisi | 0.700 | 0.100 | **0.600** |
| G14 - Bercak hitam atau coklat pada kulit gabah | 0.700 | 0.100 | **0.600** |

### 3.2 Penyakit P02 - Hawar Daun Bakteri (Kresek)
| Gejala | MB | MD | CF = MB - MD |
|--------|----|----|--------------|
| G04 - Daun menguning mulai dari ujung dan tepi (layu) | 0.700 | 0.100 | **0.600** |
| G05 - Tepi daun mengering, bergelombang, dan berwarna kelabu | 0.700 | 0.100 | **0.600** |
| G06 - Seluruh tanaman layu mendadak (serangan berat) | 0.700 | 0.100 | **0.600** |
| G08 - Daun berubah warna menjadi kuning oranye | 0.700 | 0.100 | **0.600** |

### 3.3 Penyakit P03 - Tungro
| Gejala | MB | MD | CF = MB - MD |
|--------|----|----|--------------|
| G07 - Tanaman menjadi sangat kerdil dibanding tanaman sehat | 0.700 | 0.100 | **0.600** |
| G08 - Daun berubah warna menjadi kuning oranye | 0.700 | 0.100 | **0.600** |
| G09 - Jumlah anakan berkurang drastis dan malai tidak keluar | 0.700 | 0.100 | **0.600** |

### 3.4 Penyakit P04 - Busuk Pelepah (Sheath Blight)
| Gejala | MB | MD | CF = MB - MD |
|--------|----|----|--------------|
| G06 - Seluruh tanaman layu mendadak (serangan berat) | 0.700 | 0.100 | **0.600** |
| G10 - Bercak oval keabu-abuan pada pelepah (dekat air) | 0.700 | 0.100 | **0.600** |
| G11 - Bercak meluas ke atas membentuk pola seperti awan | 0.700 | 0.100 | **0.600** |
| G12 - Batang tanaman membusuk dan mudah rebah | 0.700 | 0.100 | **0.600** |

### 3.5 Penyakit P05 - Bercak Coklat (Brown Spot)
| Gejala | MB | MD | CF = MB - MD |
|--------|----|----|--------------|
| G03 - Bulir padi hampa atau tidak berisi | 0.700 | 0.100 | **0.600** |
| G13 - Bercak coklat oval merata (seperti biji wijen) | 0.700 | 0.100 | **0.600** |
| G14 - Bercak hitam atau coklat pada kulit gabah | 0.700 | 0.100 | **0.600** |
| G15 - Daun mengering dan gugur lebih cepat | 0.700 | 0.100 | **0.600** |

---

## 4. DATA PUPUK (6 Pupuk)

| Kode | Nama | Kandungan | Fungsi Utama | Harga/kg | Satuan |
|------|------|-----------|--------------|----------|--------|
| PK01 | Urea | N 46% | Pupuk nitrogen tinggi untuk pertumbuhan vegetatif | Rp 1.800 | kg |
| PK02 | NPK Phonska | N15% P15% K15% S10% | Pupuk majemuk lengkap untuk semua fase pertumbuhan padi | Rp 1.840 | kg |
| PK03 | SP-36 | P 36% | Pupuk fosfat untuk perkembangan akar dan pembungaan | Rp 9.900 | kg |
| PK04 | KCl | K 60% | Pupuk kalium untuk memperkuat batang dan ketahanan penyakit | Rp 12.900 | kg |
| PK05 | Pupuk Organik Kompos | C-organik ≥15% | Memperbaiki struktur tanah dan kesuburan jangka panjang | Rp 640 | kg |
| PK06 | ZA (Amonium Sulfat) | N 21%, S 24% | Pupuk nitrogen + sulfur, cocok untuk tanah basa atau masam | Rp 1.360 | kg |

### 4.1 Nilai CF Pupuk untuk Setiap Gejala (Default: MB=0.700, MD=0.100, CF=0.600)

Setiap pupuk memiliki relasi dengan semua gejala dengan nilai CF default:
- **MB (Measure of Belief)**: 0.700
- **MD (Measure of Disbelief)**: 0.100
- **CF (Certainty Factor)**: 0.600

Total relasi: 15 gejala × 6 pupuk = **90 aturan CF**

---

## 5. DATA PESTISIDA (6 Pestisida)

| Kode | Nama | Jenis | Bahan Aktif | Dosis | Harga | Satuan |
|------|------|-------|-------------|-------|-------|--------|
| PS01 | Amistartop 325 SC | Fungisida | Azoksistrobin + Difenokonazol | 0,5–1 ml/L | Rp 150.000 | per 100ml |
| PS02 | Filia 525 SE | Fungisida | Propikonazol + Trisiklazol | 1–1,5 ml/L | Rp 125.000 | per 250ml |
| PS03 | Bactocyn 12/5 WP | Bakterisida | Streptomisin Sulfat | 1–2 g/L | Rp 45.000 | per 100g |
| PS04 | Agrept 20 WP | Bakterisida | Streptomisin Sulfat 20% | 1,5 g/L | Rp 25.000 | per 50g |
| PS05 | Winder 50 EC | Insektisida | Imidakloprid | 0,5–1 ml/L | Rp 55.000 | per 100ml |
| PS06 | Validacin 3 L | Fungisida | Validamisin A | 1–2 ml/L | Rp 25.000 | per 250ml |

### 5.1 Nilai CF Pestisida untuk Setiap Gejala (Default: MB=0.700, MD=0.100, CF=0.600)

Setiap pestisida memiliki relasi dengan semua gejala dengan nilai CF default:
- **MB (Measure of Belief)**: 0.700
- **MD (Measure of Disbelief)**: 0.100
- **CF (Certainty Factor)**: 0.600

Total relasi: 15 gejala × 6 pestisida = **90 aturan CF**

---

## 6. RUMUS CERTAINTY FACTOR

### 6.1 Rumus Dasar
```
CF(H,E) = MB(H,E) - MD(H,E)
```

Dimana:
- **CF(H,E)**: Certainty Factor hipotesis H diberikan evidence E
- **MB(H,E)**: Measure of Belief (tingkat kepercayaan)
- **MD(H,E)**: Measure of Disbelief (tingkat ketidakpercayaan)

### 6.2 Kombinasi CF Sequential
Jika ada multiple symptoms/evidence:

**Kombinasi 2 CF:**
```
CF_combine = CF1 + CF2 * (1 - CF1)    [jika keduanya positif]
```

**Kombinasi >2 CF:**
```
CF_combine_1_2 = CF1 + CF2 * (1 - CF1)
CF_combine_final = CF_combine_1_2 + CF3 * (1 - CF_combine_1_2)
...dan seterusnya
```

### 6.3 Contoh Perhitungan

**Contoh Diagnosis Penyakit P01 (Blast):**
- User memilih gejala: G01, G02, G03
- CF masing-masing gejala: 0.600

**Langkah 1:** Kombinasi G01 dan G02
```
CF_1_2 = 0.600 + 0.600 * (1 - 0.600)
       = 0.600 + 0.600 * 0.400
       = 0.600 + 0.240
       = 0.840
```

**Langkah 2:** Kombinasi hasil dengan G03
```
CF_final = 0.840 + 0.600 * (1 - 0.840)
         = 0.840 + 0.600 * 0.160
         = 0.840 + 0.096
         = 0.936
```

**Hasil:** Confidence level diagnosis Blast = **93.6%**

---

## 7. KRITERIA REKOMENDASI (Untuk Ranking Produk)

| Kode | Nama Kriteria | Jenis | Bobot | Keterangan |
|------|---------------|-------|-------|------------|
| C1 | Jenis Penyakit | Benefit | 0.35 | Kesesuaian produk terhadap jenis penyakit yang dipilih |
| C2 | Harga | Cost | 0.25 | Harga per satuan produk yang tersedia di pasaran |
| C3 | Efektivitas | Benefit | 0.25 | Tingkat keberhasilan mengendalikan penyakit |
| C4 | Dampak Lingkungan | Cost | 0.15 | Pengaruh negatif terhadap lingkungan sawah |

**Catatan:** Bobot kriteria digunakan untuk adjustment MB/MD dalam perhitungan Certainty Factor untuk ranking rekomendasi pupuk dan pestisida.

---

## 8. TOTAL ATURAN CF DALAM SISTEM

| Tabel Relasi | Jumlah Aturan |
|--------------|---------------|
| penyakit_gejala (MB, MD) | 5 penyakit × rata-rata 4 gejala = **20 aturan** |
| gejala_pupuk (MB, MD) | 15 gejala × 6 pupuk = **90 aturan** |
| gejala_pestisida (MB, MD) | 15 gejala × 6 pestisida = **90 aturan** |
| penyakit_pupuk (MB, MD) | 5 penyakit × 6 pupuk = **30 aturan** |
| penyakit_pestisida (MB, MD) | 5 penyakit × 6 pestisida = **30 aturan** |
| **TOTAL** | **260 aturan Certainty Factor** |

---

## 9. STRUKTUR DATABASE CF

### Tabel `penyakit_gejala`
```sql
CREATE TABLE penyakit_gejala (
  id BIGINT UNSIGNED PRIMARY KEY,
  id_penyakit BIGINT UNSIGNED NOT NULL,
  id_gejala BIGINT UNSIGNED NOT NULL,
  mb DECIMAL(4,3) DEFAULT 0.700,
  md DECIMAL(4,3) DEFAULT 0.100,
  UNIQUE KEY (id_penyakit, id_gejala)
);
```

### Tabel `gejala_pupuk`
```sql
CREATE TABLE gejala_pupuk (
  id BIGINT UNSIGNED PRIMARY KEY,
  id_gejala BIGINT UNSIGNED NOT NULL,
  id_pupuk BIGINT UNSIGNED NOT NULL,
  mb DECIMAL(4,3) DEFAULT 0.700,
  md DECIMAL(4,3) DEFAULT 0.100,
  UNIQUE KEY (id_gejala, id_pupuk)
);
```

### Tabel `gejala_pestisida`
```sql
CREATE TABLE gejala_pestisida (
  id BIGINT UNSIGNED PRIMARY KEY,
  id_gejala BIGINT UNSIGNED NOT NULL,
  id_pestisida BIGINT UNSIGNED NOT NULL,
  mb DECIMAL(4,3) DEFAULT 0.700,
  md DECIMAL(4,3) DEFAULT 0.100,
  UNIQUE KEY (id_gejala, id_pestisida)
);
```

### Tabel `penyakit_pupuk`
```sql
CREATE TABLE penyakit_pupuk (
  id BIGINT UNSIGNED PRIMARY KEY,
  id_penyakit BIGINT UNSIGNED NOT NULL,
  id_pupuk BIGINT UNSIGNED NOT NULL,
  mb DECIMAL(4,3) DEFAULT 0.700,
  md DECIMAL(4,3) DEFAULT 0.100,
  UNIQUE KEY (id_penyakit, id_pupuk)
);
```

### Tabel `penyakit_pestisida`
```sql
CREATE TABLE penyakit_pestisida (
  id BIGINT UNSIGNED PRIMARY KEY,
  id_penyakit BIGINT UNSIGNED NOT NULL,
  id_pestisida BIGINT UNSIGNED NOT NULL,
  mb DECIMAL(4,3) DEFAULT 0.700,
  md DECIMAL(4,3) DEFAULT 0.100,
  UNIQUE KEY (id_penyakit, id_pestisida)
);
```

---

## 10. IMPLEMENTASI DI CODEBASE

### File Seeder: `DatabaseSeeder.php`
Berisi data master:
- 3 Users (1 admin, 2 petani)
- 5 Penyakit
- 15 Gejala
- 4 Kriteria
- 6 Pupuk
- 6 Pestisida
- Relasi penyakit-gejala

### File Seeder: `CertaintyFactorRuleSeeder.php`
Mengisi otomatis semua tabel CF rules dengan nilai default:
- MB = 0.700
- MD = 0.100

### Service: `CertaintyFactorService.php` (sebelumnya SAWService)
Mengimplementasikan logika perhitungan CF untuk:
- Diagnosis penyakit berdasarkan gejala
- Ranking rekomendasi pupuk
- Ranking rekomendasi pestisida

### Engine: `CertaintyFactorEngine.php`
Core engine untuk perhitungan MB-MD dan kombinasi CF sequential.

---

## 11. CATATAN PENTING

1. **Nilai Default CF**: Saat ini semua relasi menggunakan MB=0.700 dan MD=0.100 (CF=0.600). Nilai ini dapat disesuaikan melalui database atau UI admin untuk meningkatkan akurasi diagnosis.

2. **Custom CF Values**: Admin dapat mengubah nilai MB/MD untuk setiap relasi gejala-penyakit, gejala-pupuk, atau gejala-pestisida melalui interface admin untuk menyesuaikan dengan pengetahuan pakar.

3. **Bobot Kriteria**: Bobot kriteria (C1-C4) mempengaruhi adjustment MB/MD dalam proses ranking rekomendasi produk.

4. **Threshold Diagnosis**: Sistem menggunakan threshold minimal CF untuk menentukan apakah diagnosis dianggap valid (biasanya CF > 0.2 atau 20%).

5. **Kombinasi Symptoms**: Semakin banyak gejala yang dipilih user, semakin tinggi confidence level diagnosis (dengan asumsi semua gejala mendukung hipotesis yang sama).

---

*Dokumentasi ini dibuat untuk referensi sistem pakar diagnosis penyakit padi dengan metode Certainty Factor.*
