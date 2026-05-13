# 📋 INFORMASI LENGKAP FLOWCHART PROGRAM
## Sistem Pendukung Keputusan Rekomendasi Pupuk & Pestisida Tanaman Padi

---

## 🎯 DAFTAR 8 FLOWCHART PROGRAM UTAMA

| No | Nama Flowchart | Modul | File Tujuan |
|----|----------------|-------|-------------|
| 1 | Diagnosis Penyakit (Certainty Factor) | User | `docs/flowchart-program-diagnosis.md` |
| 2 | Rekomendasi Pupuk & Pestisida | User | `docs/flowchart-program-rekomendasi.md` |
| 3 | Autentikasi User | Auth | `docs/flowchart-program-auth.md` |
| 4 | CRUD Gejala | Admin | `docs/flowchart-program-crud-gejala.md` |
| 5 | CRUD Penyakit | Admin | `docs/flowchart-program-crud-penyakit.md` |
| 6 | CRUD Produk (Pupuk & Pestisida) | Admin | `docs/flowchart-program-crud-produk.md` |
| 7 | Riwayat Diagnosis | User | `docs/flowchart-program-riwayat.md` |
| 8 | Dashboard (User & Admin) | Umum | `docs/flowchart-program-dashboard.md` |

---

## 1️⃣ FLOWCHART PROGRAM: DIAGNOSIS PENYAKIT (CERTAINTY FACTOR)

### 📍 Lokasi Kode
- **Controller**: `app/Http/Controllers/User/DiagnosisController.php`
- **Service**: `app/Services/DiagnosisService.php`
- **Engine**: `app/Services/CertaintyFactorEngine.php`

### 🔀 Alur Program Lengkap

```
START
  │
  ▼
[User mengakses halaman diagnosis]
  │
  ▼
[DiagnosisController::index()]
  │
  ├─→ Load semua gejala dari database
  │   └─→ Gejala::orderBy('kode')->get()
  │
  ▼
[Tampilkan form pilihan gejala dengan slider bobot]
  │
  ▼
[User memilih gejala & mengatur bobot keyakinan (0-100%)]
  │
  ▼
[User submit form identifikasi]
  │
  ▼
[DiagnosisController::identifikasi(Request $request)]
  │
  ├─→ VALIDASI INPUT
  │   ├─→ gejala: required|array|min:1
  │   ├─→ gejala.*: exists:gejala,id
  │   └─→ gejala_weights: nullable|array|numeric|min:0|max:100
  │
  ├─→ CLEAR SESSION LAMA
  │   └─→ session()->forget('diagnosis_result')
  │
  ├─→ PROSES INPUT
  │   ├─→ Map ID gejala ke integer
  │   └─→ Extract user weights (default 1.0 jika tidak ada)
  │
  ▼
[DiagnosisService::identify($idGejalaInput, $userWeights)]
  │
  ├─→ LOAD SEMUA PENYAKIT DENGAN GEJALA
  │   └─→ Penyakit::with(['gejala' => function($query) {
  │         $query->withPivot(['mb', 'md']);
  │       }])->get()
  │
  ├─→ LOOP SETIAP PENYAKIT
  │   │
  │   ├─→ Filter gejala yang cocok dengan input user
  │   │   └─→ in_array($gejala->id, $symptomIds)
  │   │
  │   ├─→ CEK: Ada gejala yang cocok?
  │   │   ├─→ TIDAK → Skip penyakit ini
  │   │   └─→ YA → Lanjut hitung CF
  │   │
  │   ├─→ CertaintyFactorEngine::calculateDiagnosisCf()
  │   │   │
  │   │   ├─→ LOOP gejala yang cocok
  │   │   │   ├─→ Ambil MB dan MD dari pivot table
  │   │   │   ├─→ Normalisasi jika MB + MD > 1
  │   │   │   ├─→ Hitung CF_rule = MB - MD
  │   │   │   ├─→ Apply user weight: CF = CF_rule * weight
  │   │   │   └─→ Simpan ke array $cfValues
  │   │   │
  │   │   ├─→ Kombinasi semua CF dengan rumus:
  │   │   │   ├─→ CF1 & CF2 same sign: CF = CF1 + CF2 * (1 - CF1)
  │   │   │   ├─→ CF1 & CF2 opposite sign: CF = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))
  │   │   │   └─→ Sequential combination untuk multiple CF
  │   │   │
  │   │   ├─→ Hitung completeness factor
  │   │   │   └─→ matchedSymptoms.count() / allDiseaseSymptoms.count()
  │   │   │
  │   │   └─→ Final CF = combinedCF * (0.7 + 0.3 * completeness)
  │   │
  │   ├─→ Konversi CF ke persentase: (CF + 1) / 2 * 100
  │   ├─→ Interpretasi CF (Sangat Tinggi, Tinggi, Sedang, Rendah)
  │   └─→ Simpan hasil ke array $diagnoses
  │
  ├─→ URUTKAN berdasarkan confidence tertinggi
  │
  └─→ RETURN [diagnoses, summary]
  │
  ▼
[CEK: Ada hasil diagnosis?]
  ├─→ TIDAK → Redirect back dengan error message
  └─→ YA → Lanjut
  │
  ├─→ SIMPAN KE SESSION
  │   └─→ session(['diagnosis_result' => [...]])
  │
  ▼
[Redirect ke halaman hasil diagnosis]
  │
  ▼
[DiagnosisController::hasilIdentifikasi()]
  │
  ├─→ Load data dari session
  ├─→ Load gejala input dari database
  ├─→ Load preset preferensi dari RecommendationService
  │
  ▼
[Tampilkan halaman hasil dengan:]
  ├─→ List penyakit terdiagnosis (sorted by CF)
  ├─→ Detail gejala yang cocok per penyakit
  ├─→ Nilai CF, persentase, dan interpretasi
  └─→ Form pemilihan preferensi rekomendasi
  │
  ▼
END
```

### 📊 Rumus Certainty Factor

```
1. CF Dasar: CF = MB - MD

2. Kombinasi CF (same sign):
   CF_combine = CF1 + CF2 * (1 - CF1)

3. Kombinasi CF (opposite sign):
   CF_combine = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))

4. Dengan User Weight:
   CF_weighted = CF_rule * (weight / 100)

5. Final CF dengan Completeness:
   CF_final = CF_combined * (0.7 + 0.3 * completeness_factor)

6. Konversi ke Persentase:
   Percentage = (CF + 1) / 2 * 100
```

### 🔣 Simbol Flowchart
- **Oval**: Start/End
- **Rectangle**: Proses
- **Diamond**: Decision/Keputusan
- **Parallelogram**: Input/Output
- **Arrow**: Alur

---

## 2️⃣ FLOWCHART PROGRAM: REKOMENDASI PUPUK & PESTISIDA

### 📍 Lokasi Kode
- **Controller**: `app/Http/Controllers/User/RekomendasiController.php`
- **Service**: `app/Services/RecommendationService.php`
- **Engine**: `app/Services/FertilizerPesticideRecommendationEngine.php`

### 🔀 Alur Program Lengkap

```
START
  │
  ▼
[User memilih preferensi dari halaman hasil diagnosis]
  │
  ├─→ Preferensi Tipe: seimbang | hemat | efisiensi
  ├─→ Alasan: optional string
  └─→ Catatan: optional string
  │
  ▼
[DiagnosisController::proses(Request $request)]
  │
  ├─→ VALIDASI INPUT
  │   ├─→ id_penyakit: required|array|min:1
  │   ├─→ gejala_terpilih: required|array|min:1
  │   └─→ preferensi_tipe: required|in:seimbang,hemat,efisiensi
  │
  ├─→ LOAD DATA
  │   ├─→ Gejala dari DB berdasarkan ID
  │   ├─→ Penyakit dari DB berdasarkan ID
  │   └─→ Bobot gejala dari session diagnosis
  │
  ▼
[LOOP setiap penyakit yang dipilih]
  │
  ▼
[RecommendationService::calculateWithPreferences()]
  │
  ├─→ FertilizerPesticideRecommendationEngine::calculateAllRecommendations()
  │   │
  │   ├─→ calculateFertilizerRecommendations(diseaseId)
  │   │   │
  │   │   ├─→ Load penyakit dengan relasi pupuk (pivot MB/MD)
  │   │   ├─→ LOOP setiap pupuk
  │   │   │   ├─→ CF_dasar = MB - MD
  │   │   │   ├─→ CEK: CF > 0.01?
  │   │   │   │   ├─→ TIDAK → Skip (tidak direkomendasikan)
  │   │   │   │   └─→ YA → Lanjut
  │   │   │   ├─→ Format data pupuk lengkap
  │   │   │   └─→ Simpan ke array
  │   │   │
  │   │   └─→ Sort by CF descending
  │   │
  │   ├─→ calculatePesticideRecommendations(diseaseId)
  │   │   │
  │   │   ├─→ Load penyakit dengan relasi pestisida (pivot MB/MD)
  │   │   ├─→ LOOP setiap pestisida
  │   │   │   ├─→ CF_solusi = MB - MD
  │   │   │   ├─→ CEK: CF > 0.01?
  │   │   │   │   ├─→ TIDAK → Skip
  │   │   │   │   └─→ YA → Lanjut
  │   │   │   ├─→ Format data pestisida lengkap
  │   │   │   └─→ Simpan ke array
  │   │   │
  │   │   └─→ Sort by CF descending
  │   │
  │   └─→ RETURN [pupuk, pestisida, disease, summary]
  │
  ├─→ APPLY PREFERENCE ADJUSTMENT
  │   │
  │   ├─→ CEK tipe preferensi
  │   │
  │   ├─→ CASE 'hemat':
  │   │   ├─→ IF harga <= 50,000 → +0.10 CF
  │   │   ├─→ IF harga <= 100,000 → +0.05 CF
  │   │   ├─→ IF harga > 100,000 → -0.03 CF
  │   │   └─→ Boost produk murah, penalty produk mahal
  │   │
  │   ├─→ CASE 'efisiensi':
  │   │   ├─→ IF CF >= 0.8 → +0.12 CF
  │   │   ├─→ IF CF >= 0.6 → +0.07 CF
  │   │   ├─→ IF CF >= 0.4 → +0.03 CF
  │   │   └─→ Boost high-confidence products
  │   │
  │   └─→ CASE 'seimbang':
  │       └─→ +0.02 CF (stabilitas)
  │
  ├─→ APPLY SYMPTOM WEIGHT ADJUSTMENT
  │   └─→ Loop gejala dengan weight tinggi
  │       └─→ +0.02 * normalizedWeight (cap di 0.08)
  │
  ├─→ NORMALIZE CF akhir ke range [-1, 1]
  │
  └─→ RE-RANK berdasarkan CF baru
  │
  ▼
[CEK: User login?]
  ├─→ YA → RecommendationService::saveForUser()
  │   └─→ Simpan ke tabel rekomendasi + detail
  │
  └─→ TIDAK → Simpan ke session guest_rekomendasi
  │
  ▼
[Redirect ke halaman preview rekomendasi]
  │
  ▼
[RekomendasiController::preview()]
  │
  ├─→ Load data dari session atau DB
  ├─→ Format tampilan
  │
  ▼
[Tampilkan:]
  ├─→ Top 2 pupuk terbaik (CF tertinggi setelah adjustment)
  ├─→ Top 2 pestisida terbaik
  ├─→ Detail lengkap: nama, kandungan, takaran, harga, cara aplikasi
  ├─→ Nilai CF dasar, CF adjustment, CF akhir
  └─→ Label interpretasi (Sangat Direkomendasikan, dll)
  │
  ▼
END
```

### 📊 Logika Adjustment Preferensi

```php
// Hemat Biaya
if ($harga <= 50_000)  return +0.10; // Sangat murah
if ($harga <= 100_000) return +0.05; // Murah
else                   return -0.03; // Mahal - penalty

// Efisiensi Tinggi
if ($CF >= 0.8) return +0.12; // Very high confidence
if ($CF >= 0.6) return +0.07; // High confidence
if ($CF >= 0.4) return +0.03; // Medium confidence

// Seimbang
return +0.02; // Small boost untuk stabilitas
```

---

## 3️⃣ FLOWCHART PROGRAM: AUTENTIKASI USER

### 📍 Lokasi Kode
- **Controller**: `app/Http/Controllers/Auth/AuthController.php`

### 🔀 Alur Login

```
START
  │
  ▼
[User mengakses form login]
  │
  ▼
[AuthController::showLogin()]
  │
  ├─→ CEK: Sudah login?
  │   ├─→ YA → Redirect sesuai role
  │   │   ├─→ Admin → admin.dashboard
  │   │   └─→ User → user.dashboard
  │   └─→ TIDAK → Tampilkan form
  │
  ▼
[User input username & password]
  │
  ▼
[AuthController::login(Request $request)]
  │
  ├─→ VALIDASI
  │   ├─→ username: required|string
  │   └─→ password: required|string
  │
  ├─→ FIND USER
  │   └─→ User::where('username', $request->username)->first()
  │
  ├─→ CEK: User exists?
  │   ├─→ TIDAK → Return error "Username tidak ditemukan"
  │   └─→ YA → Lanjut
  │
  ├─→ VERIFY PASSWORD
  │   └─→ Hash::check($password, $user->password)
  │
  ├─→ CEK: Password valid?
  │   ├─→ TIDAK → Return error "Password salah"
  │   └─→ YA → Lanjut
  │
  ├─→ LOGIN USER
  │   ├─→ Auth::login($user, remember)
  │   └─→ $request->session()->regenerate()
  │
  ├─→ CEK EMAIL VERIFICATION
  │   ├─→ Email verified?
  │   │   ├─→ YA → Redirect ke dashboard
  │   │   └─→ TIDAK → Redirect ke profil untuk verifikasi
  │
  └─→ REDIRECT sesuai role
      ├─→ Admin → admin.dashboard
      └─→ User → user.dashboard
  │
  ▼
END
```

### 🔀 Alur Register

```
START
  │
  ▼
[User mengakses form register]
  │
  ▼
[AuthController::showRegister()]
  │
  ▼
[User input username & password]
  │
  ▼
[AuthController::register(Request $request)]
  │
  ├─→ VALIDASI
  │   ├─→ username: required|unique
  │   └─→ password: required|min:6
  │
  ├─→ CREATE USER
  │   ├─→ nama = username
  │   ├─→ password = Hash::make(password)
  │   └─→ role = 'petani'
  │
  ├─→ AUTO LOGIN
  │   └─→ Auth::login($user)
  │
  └─→ REDIRECT ke profil untuk lengkapi data
  │
  ▼
END
```

### 🔀 Alur Logout

```
START
  │
  ▼
[User klik logout]
  │
  ▼
[AuthController::logout(Request $request)]
  │
  ├─→ Auth::logout()
  ├─→ Session invalidate
  └─→ Regenerate token
  │
  └─→ Redirect ke home dengan success message
  │
  ▼
END
```

---

## 4️⃣ FLOWCHART PROGRAM: CRUD GEJALA (ADMIN)

### 📍 Lokasi Kode
- **Controller**: `app/Http/Controllers/Admin/GejalaController.php`

### 🔀 Alur Create Gejala

```
START
  │
  ▼
[Admin akses halaman gejala]
  │
  ▼
[GejalaController::index()]
  │
  └─→ Gejala::withCount('penyakit')->paginate(15)
  │
  ▼
[Tampilkan list gejala dengan pagination]
  │
  ▼
[Admin klik "Tambah Gejala"]
  │
  ▼
[GejalaController::create()]
  │
  ├─→ Generate kode otomatis
  │   └─→ AutoCodeGenerator::generate(Gejala::class, 'kode', 'G')
  │
  └─→ Tampilkan form create
  │
  ▼
[Admin input data gejala]
  ├─→ Kode (auto-generated)
  ├─→ Nama Gejala
  └─→ Upload Gambar (optional)
  │
  ▼
[GejalaController::store(Request $request)]
  │
  ├─→ VALIDASI
  │   ├─→ kode: required|unique
  │   ├─→ nama_gejala: required
  │   └─→ gambar: nullable|image|max:2048
  │
  ├─→ HANDLE IMAGE UPLOAD
  │   └─→ ProjectImage::store($file, 'gejala')
  │
  ├─→ CREATE RECORD
  │   └─→ Gejala::create($data)
  │
  └─→ REDIRECT ke index dengan success message
  │
  ▼
END
```

### 🔀 Alur Update Gejala

```
START
  │
  ▼
[Admin klik edit pada gejala]
  │
  ▼
[GejalaController::edit(Gejala $gejala)]
  │
  └─→ Tampilkan form dengan data existing
  │
  ▼
[Admin update data]
  │
  ▼
[GejalaController::update(Request $request, Gejala $gejala)]
  │
  ├─→ VALIDASI (sama dengan store, plus unique except current ID)
  │
  ├─→ HANDLE IMAGE REPLACE
  │   ├─→ Delete old image: ProjectImage::delete($gejala->gambar)
  │   └─→ Store new image: ProjectImage::store($file, 'gejala')
  │
  ├─→ UPDATE RECORD
  │   └─→ $gejala->update($data)
  │
  └─→ REDIRECT dengan success message
  │
  ▼
END
```

### 🔀 Alur Delete Gejala

```
START
  │
  ▼
[Admin klik delete]
  │
  ▼
[GejalaController::destroy(Gejala $gejala)]
  │
  ├─→ CEK: Ada gambar?
  │   └─→ YA → ProjectImage::delete($gejala->gambar)
  │
  ├─→ DELETE RECORD
  │   └─→ $gejala->delete()
  │
  └─→ REDIRECT dengan success message
  │
  ▼
END
```

---

## 5️⃣ FLOWCHART PROGRAM: CRUD PENYAKIT (ADMIN)

### 📍 Lokasi Kode
- **Controller**: `app/Http/Controllers/Admin/PenyakitController.php`

### 🔀 Alur Create Penyakit dengan Rule CF

```
START
  │
  ▼
[Admin akses halaman penyakit]
  │
  ▼
[PenyakitController::index()]
  │
  └─→ Penyakit::withCount('gejala')->paginate(10)
  │
  ▼
[Admin klik "Tambah Penyakit"]
  │
  ▼
[PenyakitController::create()]
  │
  ├─→ Load semua gejala: Gejala::orderBy('kode')->get()
  ├─→ Generate kode: AutoCodeGenerator::generate(Penyakit::class, 'kode', 'P')
  ├─→ CEK: CF schema ready? CfSchema::hasSymptomCfColumns()
  │
  └─→ Tampilkan form dengan:
      ├─→ Data penyakit (kode, nama, deskripsi, gambar)
      └─→ Table rule CF untuk setiap gejala
          ├─→ Checkbox: selected
          ├─→ Input MB: 0-1
          └─→ Input MD: 0-1
  │
  ▼
[Admin input data penyakit & rule CF]
  │
  ▼
[PenyakitController::store(Request $request)]
  │
  ├─→ VALIDASI
  │   ├─→ kode, nama, deskripsi, gambar
  │   └─→ gejala_rules: nullable|array
  │       └─→ *.selected, *.mb, *.md
  │
  ├─→ CREATE PENYAKIT
  │   └─→ Penyakit::create($data)
  │
  ├─→ SYNC RELASI GEJALA DENGAN CF
  │   │
  │   ├─→ buildSymptomRuleSyncData($rules)
  │   │   └─→ LOOP rules
  │   │       ├─→ CEK: selected?
  │   │       │   ├─→ TIDAK → Skip
  │   │       │   └─→ YA → Lanjut
  │   │       └─→ Sync data: ['mb' => value, 'md' => value]
  │   │
  │   └─→ $penyakit->gejala()->sync($syncData)
  │
  └─→ REDIRECT dengan success message
  │
  ▼
END
```

### 📊 Struktur Rule CF Penyakit-Gejala

```php
// Pivot table: penyakit_gejala
[
    'gejala_id' => 1,
    'mb' => 0.800,  // Measure of Belief
    'md' => 0.100,  // Measure of Disbelief
]

// CF = MB - MD = 0.800 - 0.100 = 0.700
```

---

## 6️⃣ FLOWCHART PROGRAM: CRUD PRODUK (PUPUK & PESTISIDA)

### 📍 Lokasi Kode
- **Pupuk**: `app/Http/Controllers/Admin/PupukController.php`
- **Pestisida**: `app/Http/Controllers/Admin/PestisidaController.php`

### 🔀 Alur Create Pupuk

```
START
  │
  ▼
[Admin akses halaman pupuk]
  │
  ▼
[PupukController::index()]
  │
  └─→ Pupuk::orderBy('kode')->paginate(10)
  │
  ▼
[Admin klik "Tambah Pupuk"]
  │
  ▼
[PupukController::create()]
  │
  └─→ Generate kode: AutoCodeGenerator::generate(Pupuk::class, 'kode', 'PU')
  │
  ▼
[Admin input data lengkap pupuk]
  ├─→ Kode, Nama
  ├─→ Kandungan, Kandungan Detail
  ├─→ Fungsi Utama
  ├─→ Takaran, Cara Aplikasi
  ├─→ Jadwal Umur Aplikasi, Frekuensi
  ├─→ Harga per KG
  ├─→ Satuan
  └─→ Gambar (optional)
  │
  ▼
[PupukController::store(Request $request)]
  │
  ├─→ VALIDASI semua field
  │
  ├─→ HANDLE IMAGE UPLOAD
  │
  ├─→ CREATE RECORD
  │   └─→ Pupuk::create($data)
  │
  └─→ REDIRECT dengan success message
  │
  ▼
END
```

### 📊 Field Penting Pupuk

```php
[
    'kode' => 'PU001',
    'nama' => 'Urea',
    'kandungan' => 'N: 46%',
    'kandungan_detail' => '...',
    'fungsi_utama' => '...',
    'harga_per_kg' => 5000,
    'satuan' => 'kg',
    'takaran' => '200 kg/ha',
    'cara_aplikasi' => '...',
    // ...
]
```

---

## 7️⃣ FLOWCHART PROGRAM: RIWAYAT DIAGNOSIS

### 📍 Lokasi Kode
- **User**: `app/Http/Controllers/User/RiwayatController.php`
- **Admin**: `app/Http/Controllers/Admin/RiwayatController.php`

### 🔀 Alur User Lihat Riwayat

```
START
  │
  ▼
[User akses halaman riwayat]
  │
  ▼
[RiwayatController::index()]
  │
  ├─→ Query rekomendasi milik user
  │   └─→ Rekomendasi::with(['penyakit', 'detailPupuk', 'detailPestisida'])
  │       ->where('id_user', Auth::id())
  │       ->latest()
  │       ->paginate(10)
  │
  └─→ Tampilkan list riwayat dengan:
      ├─→ Tanggal diagnosis
      ├─→ Nama penyakit
      ├─→ Preferensi yang dipilih
      ├─→ Top pupuk & pestisida yang direkomendasikan
      └─→ Link ke detail lengkap
  │
  ▼
END
```

### 📊 Struktur Tabel Rekomendasi

```sql
rekomendasi:
  - id
  - id_user
  - id_penyakit
  - tanggal
  - preferensi_label
  - preferensi_pengguna (JSON)
  
detail_rekomendasi_pupuk:
  - id_rekomendasi
  - id_pupuk
  - nilai_vi (CF akhir)
  - peringkat
  
detail_rekomendasi_pestisida:
  - id_rekomendasi
  - id_pestisida
  - nilai_vi (CF akhir)
  - peringkat
```

---

## 8️⃣ FLOWCHART PROGRAM: DASHBOARD

### 📍 Lokasi Kode
- **User**: `app/Http/Controllers/User/DashboardController.php`
- **Admin**: `app/Http/Controllers/Admin/DashboardController.php`

### 🔀 Alur User Dashboard

```
START
  │
  ▼
[User login berhasil]
  │
  ▼
[DashboardController::index()]
  │
  ├─→ CEK: User isAdmin?
  │   ├─→ YA → Redirect ke admin.dashboard
  │   └─→ TIDAK → Lanjut
  │
  ├─→ HITUNG STATISTIK
  │   ├─→ totalRekomendasi = count(user's recommendations)
  │   ├─→ rekomendasiBulanIni = count(current month)
  │   ├─→ rekomendasi7Hari = count(last 7 days)
  │   │
  │   ├─→ riwayatTerbaru = latest 5 recommendations
  │   │   └─→ with: penyakit, detailPupuk, detailPestisida
  │   │
  │   ├─→ penyakitPopuler = Penyakit with count(rekomendasi)
  │   │   └─→ orderByDesc('total_dicari')->limit(6)
  │   │
  │   └─→ riwayatReferensi = latest recommendation per popular disease
  │
  ├─→ HITUNG PROGRESS SISTEM
  │   ├─→ penyakit.count()
  │   ├─→ gejala.count()
  │   ├─→ rekomendasi.count()
  │   └─→ total pencarian populer
  │
  └─→ LOAD TIPS
  │
  ▼
[Tampilkan dashboard dengan:]
  ├─→ Statistik personal user
  ├─→ Riwayat terbaru
  ├─→ Referensi kasus populer
  ├─→ Progress kelengkapan data sistem
  └─→ Tips penggunaan
  │
  ▼
END
```

### 🔀 Alur Admin Dashboard

```
START
  │
  ▼
[Admin login berhasil]
  │
  ▼
[Admin\DashboardController::index()]
  │
  ├─→ HITUNG STATISTIK GLOBAL
  │   ├─→ Total users, gejala, penyakit
  │   ├─→ Total pupuk, pestisida
  │   ├─→ Total rekomendasi (semua user)
  │   ├─→ Diagnosis bulan ini
  │   │
  │   ├─→ penyakitTerpopuler = top 5 diseases
  │   └─→ penggunaAktif = users with recent activity
  │
  ├─→ HITUNG PROGRESS DATA
  │   └─→ Kelengkapan master data
  │
  └─→ LOAD QUICK ACTIONS
  │
  ▼
[Tampilkan admin dashboard dengan:]
  ├─→ Statistik global sistem
  ├─→ Grafik/chart (jika ada)
  ├─→ Quick links ke CRUD modules
  └─→ Recent activities
  │
  ▼
END
```

---

## 📎 LAMPIRAN: STRUKTUR DATABASE RELEVAN

### Tabel Utama

```sql
-- Users
users: id, nama, username, password, role, email, ...

-- Master Data
gejala: id, kode, nama_gejala, gambar
penyakit: id, kode, nama, deskripsi, gambar
pupuk: id, kode, nama, kandungan, harga_per_kg, ...
pestisida: id, kode, nama, bahan_aktif, harga, ...

-- Relasi CF
penyakit_gejala: penyakit_id, gejala_id, mb, md
penyakit_pupuk: penyakit_id, pupuk_id, mb, md
penyakit_pestisida: penyakit_id, pestisida_id, mb, md

-- Transaksi
rekomendasi: id, id_user, id_penyakit, tanggal, preferensi...
detail_rekomendasi_pupuk: id_rekomendasi, id_pupuk, nilai_vi, peringkat
detail_rekomendasi_pestisida: id_rekomendasi, id_pestisida, nilai_vi, peringkat

-- Support Tables
kriteria: id, kode, nama, jenis, ...
```

---

## 🎨 STANDAR PENGGAMBARAN FLOWCHART

### Simbol
- **○ Oval** : Start / End
- **▭ Rectangle** : Process / Action
- **◇ Diamond** : Decision / Condition
- **▱ Parallelogram** : Input / Output
- **→ Arrow** : Flow direction
- **⊕ Circle** : Connector (off-page)

### Konvensi
- **Warna Hijau** : Success path
- **Warna Merah** : Error/Exception path
- **Warna Biru** : Database operation
- **Warna Kuning** : Decision point

---

## ✅ CHECKLIST IMPLEMENTASI

Setiap flowchart program harus mencakup:

- [ ] Start & End points
- [ ] Semua input validation
- [ ] Decision branches (IF/ELSE)
- [ ] Loop iterations (FOREACH/WHILE)
- [ ] Database operations (CRUD)
- [ ] Session handling
- [ ] Error handling
- [ ] Redirect routes
- [ ] Response rendering

---

## 📚 REFERENSI DOKUMENTASI

- File sistem flowchart: `docs/flowchart-sistem-spk-padi.md`
- Swimlane diagram: `docs/FLOWCHART_SWIMLANE_SISTEM.md`
- Informasi detail: `docs/INFORMASI_DETAIL_PROJEK_UNTUK_FLOWCHART.md`

---

**Dibuat untuk**: Proyek SPK Rekomendasi Pupuk & Pestisida Padi  
**Metode**: Certainty Factor  
**Framework**: Laravel 11  
**Total Flowchart Program**: 8 modul utama
