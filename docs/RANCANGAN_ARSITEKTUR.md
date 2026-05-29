# 🏗️ RANCANGAN ARSITEKTUR PROGRAM - SISTEM SPK PUPUK & PESTISIDA PADI

## 📋 INFORMASI UMUM
- **Nama Sistem**: Sistem Pendukung Keputusan Rekomendasi Pupuk & Pestisida Padi
- **Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL / MariaDB
- **Frontend**: Blade Templates + TailwindCSS + Alpine.js
- **Metode SPK**: Certainty Factor (CF) + Simple Additive Weighting (SAW)
- **Arsitektur**: MVC (Model-View-Controller) + Service Pattern

---

## 🎯 DIAGRAM ARSITEKTUR SISTEM

```
┌─────────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                                │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              │
│  │   Browser    │  │   Mobile     │  │   Tablet     │              │
│  │  (Desktop)   │  │   Browser    │  │   Browser    │              │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘              │
│         │                 │                  │                       │
│         └─────────────────┴──────────────────┘                       │
│                           │                                          │
│                    HTTP/HTTPS Request                                │
└───────────────────────────┼──────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      PRESENTATION LAYER                             │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                    Laravel Routes                            │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐             │  │
│  │  │ Web Routes │  │ API Routes │  │ Auth Routes│             │  │
│  │  └─────┬──────┘  └─────┬──────┘  └─────┬──────┘             │  │
│  └────────┼────────────────┼────────────────┼───────────────────┘  │
│           │                │                │                       │
│           ▼                ▼                ▼                       │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                   Controllers Layer                          │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │  │
│  │  │   Admin      │  │    User      │  │    Auth      │       │  │
│  │  │ Controllers  │  │ Controllers  │  │ Controller   │       │  │
│  │  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘       │  │
│  └─────────┼─────────────────┼─────────────────┼────────────────┘  │
│            │                 │                 │                    │
│            ▼                 ▼                 ▼                    │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                   View Layer (Blade)                         │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐             │  │
│  │  │  Layouts   │  │ Components │  │   Pages    │             │  │
│  │  └────────────┘  └────────────┘  └────────────┘             │  │
│  └──────────────────────────────────────────────────────────────┘  │
└───────────────────────────┬─────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────────┐
│                     BUSINESS LOGIC LAYER                            │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                    Services Layer                            │  │
│  │  ┌──────────────────┐  ┌──────────────────┐                 │  │
│  │  │ DiagnosisService │  │RecommendationSvc │                 │  │
│  │  └────────┬─────────┘  └────────┬─────────┘                 │  │
│  │           │                     │                            │  │
│  │  ┌────────▼─────────┐  ┌────────▼─────────┐                 │  │
│  │  │ CF Engine        │  │Fertilizer&Pest.  │                 │  │
│  │  │ (CertaintyFactor)│  │ Recommendation   │                 │  │
│  │  └──────────────────┘  └──────────────────┘                 │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                   Domain Models                              │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐            │  │
│  │  │Penyakit │ │ Gejala  │ │  Pupuk  │ │Pestisida│            │  │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘            │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐            │  │
│  │  │Kriteria │ │Rekomend.│ │ Rating  │ │  User   │            │  │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘            │  │
│  └──────────────────────────────────────────────────────────────┘  │
└───────────────────────────┬─────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      DATA ACCESS LAYER                              │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                  Eloquent ORM (Laravel)                      │  │
│  │  - Query Builder                                             │  │
│  │  - Relationships (hasOne, hasMany, belongsToMany)            │  │
│  │  - Scopes & Accessors                                        │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                            │                                       │
│                            ▼                                       │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                    Migrations                                │  │
│  │  - Schema Definitions                                        │  │
│  │  - Seeders (Initial Data)                                    │  │
│  │  - Model Factories                                           │  │
│  └──────────────────────────────────────────────────────────────┘  │
└───────────────────────────┬─────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       DATABASE LAYER                                │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                  MySQL / MariaDB                             │  │
│  │  ┌──────────────────────────────────────────────────────┐   │  │
│  │  │ Tables:                                              │   │  │
│  │  │ - users, penyakit, gejala, pupuk, pestisida          │   │  │
│  │  │ - kriteria, rating_pupuk, rating_pestisida           │   │  │
│  │  │ - penyakit_gejala, penyakit_pupuk, penyakit_pestisida│   │  │
│  │  │ - rekomendasi, detail_rekomendasi_pupuk/pestisida    │   │  │
│  │  └──────────────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📁 STRUKTUR DIRECTORI PROJECT

```
/workspace/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── PenyakitController.php
│   │   │   │   ├── GejalaController.php
│   │   │   │   ├── PupukController.php
│   │   │   │   ├── PestisidaController.php
│   │   │   │   ├── KriteriaController.php
│   │   │   │   ├── RatingController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── RiwayatController.php
│   │   │   ├── User/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DiagnosisController.php
│   │   │   │   ├── RekomendasiController.php
│   │   │   │   └── RiwayatController.php
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php
│   │   │   ├── ProfileController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── RedirectIfAuthenticated.php
│   │   │   ├── EnsureRoleIsAdmin.php
│   │   │   └── EnsureRoleIsPetani.php
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StorePenyakitRequest.php
│   │       │   ├── UpdatePenyakitRequest.php
│   │       │   └── ...
│   │       └── User/
│   │           ├── StoreDiagnosisRequest.php
│   │           └── StoreRekomendasiRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Penyakit.php
│   │   ├── Gejala.php
│   │   ├── Pupuk.php
│   │   ├── Pestisida.php
│   │   ├── Kriteria.php
│   │   ├── Rekomendasi.php
│   │   ├── DetailRekomendasiPupuk.php
│   │   ├── DetailRekomendasiPestisida.php
│   │   ├── PenyakitPupuk.php
│   │   ├── PenyakitPestisida.php
│   │   └── ...
│   ├── Services/
│   │   ├── DiagnosisService.php
│   │   ├── RecommendationService.php
│   │   ├── CertaintyFactorEngine.php
│   │   ├── CertaintyFactorService.php
│   │   └── FertilizerPesticideRecommendationEngine.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Support/
│       ├── CfSchema.php
│       └── ProjectImage.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000100_create_penyakit_table.php
│   │   ├── 2024_01_01_000200_create_all_tables.php
│   │   ├── 2026_04_27_000100_add_cf_rule_tables.php
│   │   └── ...
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── PenyakitSeeder.php
│   │   ├── GejalaSeeder.php
│   │   ├── PupukSeeder.php
│   │   ├── PestisidaSeeder.php
│   │   └── KriteriaSeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── PenyakitFactory.php
│       └── ...
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   └── guest.blade.php
│   │   ├── components/
│   │   │   ├── navbar.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   ├── footer.blade.php
│   │   │   └── alert.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── penyakit/
│   │   │   ├── gejala/
│   │   │   ├── pupuk/
│   │   │   ├── pestisida/
│   │   │   ├── kriteria/
│   │   │   └── riwayat/
│   │   ├── user/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── diagnosis/
│   │   │   ├── rekomendasi/
│   │   │   └── riwayat/
│   │   └── welcome.blade.php
│   └── js/
│       ├── app.js
│       └── bootstrap.js
├── routes/
│   ├── web.php
│   ├── auth.php
│   └── console.php
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── images/
├── tests/
│   ├── Unit/
│   │   ├── Services/
│   │   │   ├── CertaintyFactorEngineTest.php
│   │   │   └── FertilizerPesticideRecommendationEngineTest.php
│   │   └── Models/
│   ├── Feature/
│   │   ├── AuthTest.php
│   │   ├── DiagnosisTest.php
│   │   └── RekomendasiTest.php
│   └── TestCase.php
├── docs/
│   ├── USE_CASE_DIAGRAM.md
│   ├── ACTIVITY_DIAGRAM.md
│   ├── RANCANGAN_DATABASE.md
│   ├── RANCANGAN_ARSITEKTUR.md
│   └── FLOWCHART_SISTEM_REKOMENDASI_UPDATED.drawio
├── .env
├── .env.example
├── composer.json
├── package.json
├── vite.config.js
├── phpunit.xml
└── README.md
```

---

## 🔄 DATA FLOW DIAGRAM (DFD)

### **DFD Level 0 (Context Diagram)**

```
┌──────────────┐
│    Guest     │
└──────┬───────┘
       │
       ▼
┌─────────────────────────────────────────────────────────┐
│                                                         │
│          SISTEM SPK PUPUK & PESTISIDA PADI              │
│                                                         │
└──────┬──────────────────────────────────┬──────────────┘
       │                                  │
       ▼                                  ▼
┌──────────────┐                 ┌──────────────┐
│   Petani     │                 │    Admin     │
└──────────────┘                 └──────────────┘
```

### **DFD Level 1**

```
┌─────────────┐
│    Guest    │
└──────┬──────┘
       │
       │ 1. Input Gejala
       ▼
┌─────────────────────────────────────────────────────────────┐
│  Proses 1: Diagnosis Penyakit                               │
│  - Input: Gejala + Bobot Keyakinan                          │
│  - Process: Hitung Certainty Factor                         │
│  - Output: Penyakit Teridentifikasi                         │
└──────┬──────────────────────────────────────────────────────┘
       │
       │ 2. Pilih Penyakit + Preferensi
       ▼
┌─────────────────────────────────────────────────────────────┐
│  Proses 2: Rekomendasi Pupuk & Pestisida                    │
│  - Input: Penyakit + Preferensi User                        │
│  - Process: Hitung SAW (Simple Additive Weighting)          │
│  - Output: Ranking Pupuk & Pestisida                        │
└──────┬──────────────────────────────────────────────────────┘
       │
       │ 3. Simpan Hasil (jika login)
       ▼
┌─────────────────────────────────────────────────────────────┐
│  Proses 3: Manajemen Riwayat                                │
│  - Input: Hasil Diagnosis + Rekomendasi                     │
│  - Process: Save to Database                                │
│  - Output: Riwayat Tersimpan                                │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────┐
│   Petani    │
└─────────────┘

┌─────────────┐
│    Admin    │
└──────┬──────┘
       │
       │ 4. Kelola Data Master
       ▼
┌─────────────────────────────────────────────────────────────┐
│  Proses 4: Administrasi Master Data                         │
│  - Input: CRUD Penyakit, Gejala, Pupuk, Pestisida, Kriteria │
│  - Process: Validate + Save to Database                     │
│  - Output: Data Master Terupdate                            │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧩 KOMPONEN UTAMA SISTEM

### **1. CONTROLLERS**

#### **Admin Controllers**
| Controller | Fungsi Utama | Methods |
|------------|--------------|---------|
| `DashboardController` | Statistik & analytics | index() |
| `PenyakitController` | CRUD penyakit | index, create, store, edit, update, destroy |
| `GejalaController` | CRUD gejala | index, create, store, edit, update, destroy |
| `PupukController` | CRUD pupuk | index, create, store, edit, update, destroy |
| `PestisidaController` | CRUD pestisida | index, create, store, edit, update, destroy |
| `KriteriaController` | CRUD kriteria SAW | index, create, store, edit, update, destroy |
| `RatingController` | Set rating pupuk/pestisida | index, edit, update |
| `UserController` | Manage user accounts | index, edit, update, toggleStatus |
| `RiwayatController` | View all user history | index, show, destroy |

#### **User Controllers**
| Controller | Fungsi Utama | Methods |
|------------|--------------|---------|
| `DashboardController` | User dashboard | index() |
| `DiagnosisController` | Diagnosis penyakit | create, store, show |
| `RekomendasiController` | Generate rekomendasi | create, store, show, print |
| `RiwayatController` | View personal history | index, show, destroy |

#### **Auth Controller**
| Controller | Fungsi Utama | Methods |
|------------|--------------|---------|
| `AuthController` | Login/Register/Logout | showLogin, login, showRegister, register, logout |

---

### **2. SERVICES (BUSINESS LOGIC)**

#### **DiagnosisService**
```php
class DiagnosisService {
    public function identify(array $symptomIds, array $userWeights): array
    public function getDetailedDiagnosis(int $diseaseId, array $symptomIds): array
}
```
**Tanggung Jawab**:
- Orchestrate proses diagnosis
- Load data penyakit + gejala dari DB
- Delegate perhitungan CF ke CertaintyFactorEngine
- Format hasil untuk presentation layer

#### **CertaintyFactorEngine**
```php
class CertaintyFactorEngine {
    public function calculateCf(float $mb, float $md): float
    public function calculateCombinedCf(array $cfs): float
    public function calculateDiagnosisCf(Collection $matchedSymptoms, ...): float
    public function toPercentage(float $cf): float
    public function interpret(float $cf): string
}
```
**Tanggung Jawab**:
- Implementasi rumus CF: CF = MB - MD
- Combination formula: CFcomb = CF1 + CF2 * (1 - CF1)
- Interpretasi nilai CF
- Utility functions (normalize, percentage)

#### **RecommendationService**
```php
class RecommendationService {
    public function generateRecommendations(int $diseaseId, array $preferences): array
    public function saveRecommendation(array $data): Rekomendasi
    public function getHistory(int $userId): Collection
}
```
**Tanggung Jawab**:
- Orchestrate proses rekomendasi
- Call FertilizerPesticideRecommendationEngine
- Handle save to database (transactional)
- Retrieve user history

#### **FertilizerPesticideRecommendationEngine**
```php
class FertilizerPesticideRecommendationEngine {
    public function calculateFertilizerRecommendations(int $diseaseId): array
    public function calculatePesticideRecommendations(int $diseaseId): array
    private function calculateSawScore(array $ratings, array $weights): float
}
```
**Tanggung Jawab**:
- Filter pupuk/pestisida berdasarkan CF > 0
- Implementasi metode SAW
- Normalisasi matriks
- Hitung nilai Vi (Valorization Index)
- Ranking alternatif

---

### **3. MODELS (DOMAIN)**

| Model | Table | Relationships |
|-------|-------|---------------|
| `User` | users | hasMany(Rekomendasi) |
| `Penyakit` | penyakit | belongsToMany(Gejala), belongsToMany(Pupuk), belongsToMany(Pestisida), hasMany(Rekomendasi) |
| `Gejala` | gejala | belongsToMany(Penyakit) |
| `Pupuk` | pupuk | belongsToMany(Penyakit), hasMany(DetailRekomendasiPupuk) |
| `Pestisida` | pestisida | belongsToMany(Penyakit), hasMany(DetailRekomendasiPestisida) |
| `Kriteria` | kriteria | hasMany(RatingPupuk), hasMany(RatingPestisida) |
| `Rekomendasi` | rekomendasi | belongsTo(User), belongsTo(Penyakit), hasMany(DetailRekomendasiPupuk), hasMany(DetailRekomendasiPestisida) |
| `DetailRekomendasiPupuk` | detail_rekomendasi_pupuk | belongsTo(Rekomendasi), belongsTo(Pupuk) |
| `DetailRekomendasiPestisida` | detail_rekomendasi_pestisida | belongsTo(Rekomendasi), belongsTo(Pestisida) |

---

## 🔒 SECURITY ARCHITECTURE

### **Authentication**
- **Driver**: Laravel Session + Cookie
- **Password Hashing**: bcrypt (cost factor 12)
- **Remember Me**: 30 days token
- **CSRF Protection**: Enabled on all POST requests
- **Rate Limiting**: Login attempts throttled

### **Authorization**
- **Middleware**: 
  - `auth`: Require authentication
  - `guest`: Only for non-authenticated users
  - `role:admin`: Admin-only access
  - `role:petani`: Petani-only access
- **Gates & Policies**: Fine-grained authorization for resources

### **Data Validation**
- **Form Requests**: Server-side validation
- **Sanitization**: XSS prevention via Blade escaping
- **SQL Injection**: Prevented by Eloquent ORM (prepared statements)

---

## 🚀 DEPLOYMENT ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────┐
│                         CDN                                 │
│              (Static Assets: CSS, JS, Images)               │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    Load Balancer                            │
│                   (Nginx Reverse Proxy)                     │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  Application Server                         │
│              (Laravel + PHP-FPM 8.2)                        │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Queue Worker (Redis)                                │  │
│  │  - Email notifications                               │  │
│  │  - PDF generation                                    │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    Database Server                          │
│              (MySQL 8.0 / MariaDB 10.6)                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Read Replica (Optional for scaling)                 │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                   Cache Server                              │
│                     (Redis)                                 │
│  - Session storage                                          │
│  - Query cache                                              │
│  - Rate limiting                                            │
└─────────────────────────────────────────────────────────────┘
```

---

## 📊 TECHNOLOGY STACK

| Layer | Technology | Version |
|-------|------------|---------|
| **Backend Framework** | Laravel | 11.x |
| **Language** | PHP | 8.2+ |
| **Database** | MySQL / MariaDB | 8.0 / 10.6+ |
| **ORM** | Eloquent | Built-in Laravel |
| **Frontend** | Blade Templates | Built-in Laravel |
| **CSS Framework** | TailwindCSS | 3.x |
| **JavaScript** | Alpine.js | 3.x |
| **Build Tool** | Vite | 5.x |
| **Cache** | Redis | 7.x |
| **Web Server** | Nginx | 1.24+ |
| **Testing** | PHPUnit + Pest | 10.x |

---

## 🔄 REQUEST LIFECYCLE

```
1. User Request (HTTP)
         │
         ▼
2. Nginx (Reverse Proxy)
         │
         ▼
3. Laravel Entry Point (public/index.php)
         │
         ▼
4. Autoloader (Composer)
         │
         ▼
5. Service Providers Boot
         │
         ▼
6. Middleware Pipeline
   - Check Maintenance Mode
   - Handle CORS
   - Verify CSRF Token
   - Authenticate User
   - Check Authorization
         │
         ▼
7. Router Matching
         │
         ▼
8. Controller Execution
         │
         ▼
9. Service Layer (Business Logic)
   - DiagnosisService
   - RecommendationService
   - CF Engine
   - SAW Engine
         │
         ▼
10. Model Layer (Eloquent)
    - Query Database
    - Hydrate Models
         │
         ▼
11. Return Response
    - Blade View Rendering
    - JSON Response (API)
         │
         ▼
12. Middleware Response Handling
         │
         ▼
13. Send HTTP Response to Client
```

---

## 📈 SCALABILITY CONSIDERATIONS

### **Horizontal Scaling**
- **Stateless Application**: Session stored in Redis
- **Load Balancer**: Distribute traffic across multiple app servers
- **Database Read Replicas**: Separate read/write operations

### **Vertical Scaling**
- **Database Optimization**: Indexes, query optimization
- **Caching**: Redis for frequently accessed data
- **CDN**: Static assets offloading

### **Performance Optimization**
- **Eager Loading**: Prevent N+1 queries
- **Query Caching**: Cache expensive queries
- **OPcache**: PHP opcode caching
- **Queue System**: Async processing for heavy tasks

---

**Dibuat**: 2025
**Versi**: 1.0
**Status**: Final
