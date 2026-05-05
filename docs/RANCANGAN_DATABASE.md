# 💾 RANCANGAN BASIS DATA - SISTEM SPK PUPUK & PESTISIDA PADI

## 📋 INFORMASI UMUM
- **DBMS**: MySQL / MariaDB
- **Engine**: InnoDB
- **Charset**: utf8mb4_unicode_ci
- **Framework**: Laravel 11 (Eloquent ORM)

---

## 🗂️ DIAGRAM ERD (ENTITY RELATIONSHIP DIAGRAM)

```
┌─────────────────────┐       ┌─────────────────────┐
│       users         │       │      penyakit       │
├─────────────────────┤       ├─────────────────────┤
│ PK id               │       │ PK id               │
│    nama             │       │    kode (unique)    │
│    username         │       │    nama             │
│    alamat           │       │    deskripsi        │
│    catatan_profil   │       │    gambar           │
│    foto_profil      │       │    created_at       │
│    password         │       │    updated_at       │
│    role             │       └──────────┬──────────┘
│    remember_token   │                  │
│    created_at       │                  │ 1:N
│    updated_at       │                  ▼
└──────────┬──────────┘       ┌─────────────────────┐
           │                  │  penyakit_gejala    │
           │ 1:N              ├─────────────────────┤
           ▼                  │ PK id               │
┌─────────────────────┐       │ FK id_penyakit      │───┐
│    rekomendasi      │       │ FK id_gejala        │   │
├─────────────────────┤       │    mb (decimal)     │   │
│ PK id               │       │    md (decimal)     │   │
│ FK id_user          │───┐   │    created_at       │   │
│ FK id_penyakit      │───┼──►│    updated_at       │   │
│    tanggal          │   │   └─────────────────────┘   │
│    preferensi_label │   │                             │
│    preferensi_pengguna│ │                             │
│    created_at       │   │   ┌─────────────────────┐   │
│    updated_at       │   │   │       gejala        │   │
└──────────┬──────────┘   │   ├─────────────────────┤   │
           │              │   │ PK id               │◄──┘
           │ 1:N          │   │    kode (unique)    │
           ▼              │   │    nama_gejala      │
┌─────────────────────┐   │   │    gambar           │
│detail_rekomendasi_  │   │   │    created_at       │
│      pupuk          │   │   │    updated_at       │
├─────────────────────┤   │   └─────────────────────┘
│ PK id               │   │
│ FK id_rekomendasi   │───┘
│ FK id_pupuk         │
│    nilai_vi         │
│    peringkat        │
└─────────────────────┘

┌─────────────────────┐       ┌─────────────────────┐
│detail_rekomendasi_  │       │       pupuk         │
│    pestisida        │       ├─────────────────────┤
├─────────────────────┤       │ PK id               │
│ PK id               │       │    kode (unique)    │
│ FK id_rekomendasi   │       │    nama             │
│ FK id_pestisida     │       │    kandungan        │
│    nilai_vi         │       │    fungsi_utama     │
│    peringkat        │       │    harga_per_kg     │
└──────────┬──────────┘       │    satuan           │
           │                  │    created_at       │
           │                  │    updated_at       │
           ▼                  └──────────┬──────────┘
┌─────────────────────┐                 │
│     pestisida       │                 │ M:N
├─────────────────────┤                 │
│ PK id               │                 ▼
│    kode (unique)    │       ┌─────────────────────┐
│    nama             │       │   penyakit_pupuk    │
│    jenis            │       ├─────────────────────┤
│    bahan_aktif      │       │ PK id               │
│    dosis            │       │ FK id_penyakit      │
│    harga            │       │ FK id_pupuk         │
│    satuan_harga     │       │    mb (decimal)     │
│    created_at       │       │    md (decimal)     │
│    updated_at       │       │    created_at       │
└──────────┬──────────┘       │    updated_at       │
           │                  └─────────────────────┘
           │
           │ M:N
           ▼
┌─────────────────────┐
│  penyakit_pestisida │
├─────────────────────┤
│ PK id               │
│ FK id_penyakit      │
│ FK id_pestisida     │
│    mb (decimal)     │
│    md (decimal)     │
│    created_at       │
│    updated_at       │
└─────────────────────┘

┌─────────────────────┐       ┌─────────────────────┐
│      kriteria       │       │   rating_pupuk      │
├─────────────────────┤       ├─────────────────────┤
│ PK id               │       │ PK id               │
│    kode (unique)    │       │ FK id_pupuk         │
│    nama             │       │ FK id_kriteria      │
│    jenis            │       │ FK id_penyakit      │
│    bobot            │       │    nilai (decimal)  │
│    keterangan       │       │    created_at       │
│    created_at       │       │    updated_at       │
│    updated_at       │       └─────────────────────┘
└──────────┬──────────┘
           │
           │ 1:N
           ▼
┌─────────────────────┐
│   rating_pestisida  │
├─────────────────────┤
│ PK id               │
│ FK id_pestisida     │
│ FK id_kriteria      │
│ FK id_penyakit      │
│    nilai (decimal)  │
│    created_at       │
│    updated_at       │
└─────────────────────┘
```

---

## 📊 SPESIFIKASI TABEL

### 1. **users**
**Deskripsi**: Menyimpan data pengguna sistem (admin dan petani)

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| nama | VARCHAR(255) | NO | | NULL | |
| username | VARCHAR(100) | NO | UNI | NULL | |
| alamat | TEXT | YES | | NULL | |
| catatan_profil | TEXT | YES | | NULL | |
| foto_profil | VARCHAR(255) | YES | | NULL | |
| password | VARCHAR(255) | NO | | NULL | |
| role | ENUM('admin', 'petani') | NO | | 'petani' | |
| remember_token | VARCHAR(100) | YES | | NULL | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- 1:N → rekomendasi (id_user)

**Index**:
- PRIMARY KEY (id)
- UNIQUE (username)

---

### 2. **penyakit**
**Deskripsi**: Master data penyakit padi

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| kode | VARCHAR(10) | NO | UNI | NULL | |
| nama | VARCHAR(100) | NO | | NULL | |
| deskripsi | TEXT | YES | | NULL | |
| gambar | VARCHAR(255) | YES | | NULL | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- 1:N → penyakit_gejala (id_penyakit)
- 1:N → penyakit_pupuk (id_penyakit)
- 1:N → penyakit_pestisida (id_penyakit)
- 1:N → rekomendasi (id_penyakit)

**Index**:
- PRIMARY KEY (id)
- UNIQUE (kode)

---

### 3. **gejala**
**Deskripsi**: Master data gejala penyakit padi

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| kode | VARCHAR(10) | NO | UNI | NULL | |
| nama_gejala | VARCHAR(200) | NO | | NULL | |
| gambar | VARCHAR(255) | YES | | NULL | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- M:N → penyakit (via penyakit_gejala)

**Index**:
- PRIMARY KEY (id)
- UNIQUE (kode)

---

### 4. **penyakit_gejala**
**Deskripsi**: Tabel relasi many-to-many antara penyakit dan gejala dengan nilai CF (MB/MD)

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_penyakit | BIGINT UNSIGNED | NO | FK | NULL | |
| id_gejala | BIGINT UNSIGNED | NO | FK | NULL | |
| mb | DECIMAL(4,3) | NO | | 0.700 | |
| md | DECIMAL(4,3) | NO | | 0.100 | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- FK id_penyakit → penyakit(id) ON DELETE CASCADE
- FK id_gejala → gejala(id) ON DELETE CASCADE

**Index**:
- PRIMARY KEY (id)
- UNIQUE (id_penyakit, id_gejala)
- INDEX (id_penyakit)
- INDEX (id_gejala)

**Aturan Bisnis**:
- MB (Measure of Belief): 0.0 - 1.0
- MD (Measure of Disbelief): 0.0 - 1.0
- MB + MD tidak harus = 1

---

### 5. **pupuk**
**Deskripsi**: Master data pupuk untuk padi

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| kode | VARCHAR(10) | NO | UNI | NULL | |
| nama | VARCHAR(100) | NO | | NULL | |
| kandungan | VARCHAR(200) | YES | | NULL | |
| fungsi_utama | TEXT | YES | | NULL | |
| harga_per_kg | DECIMAL(10,2) | NO | | NULL | |
| satuan | VARCHAR(20) | NO | | 'kg' | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- M:N → penyakit (via penyakit_pupuk)
- 1:N → detail_rekomendasi_pupuk (id_pupuk)
- 1:N → rating_pupuk (id_pupuk)

**Index**:
- PRIMARY KEY (id)
- UNIQUE (kode)

---

### 6. **pestisida**
**Deskripsi**: Master data pestisida untuk padi

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| kode | VARCHAR(10) | NO | UNI | NULL | |
| nama | VARCHAR(100) | NO | | NULL | |
| jenis | ENUM('fungisida', 'bakterisida', 'insektisida', 'herbisida') | NO | | NULL | |
| bahan_aktif | VARCHAR(200) | YES | | NULL | |
| dosis | VARCHAR(100) | YES | | NULL | |
| harga | DECIMAL(10,2) | NO | | NULL | |
| satuan_harga | VARCHAR(30) | NO | | 'per 100ml' | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- M:N → penyakit (via penyakit_pestisida)
- 1:N → detail_rekomendasi_pestisida (id_pestisida)
- 1:N → rating_pestisida (id_pestisida)

**Index**:
- PRIMARY KEY (id)
- UNIQUE (kode)

---

### 7. **penyakit_pupuk**
**Deskripsi**: Tabel relasi many-to-many antara penyakit dan pupuk dengan nilai CF

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_penyakit | BIGINT UNSIGNED | NO | FK | NULL | |
| id_pupuk | BIGINT UNSIGNED | NO | FK | NULL | |
| mb | DECIMAL(4,3) | NO | | 0.700 | |
| md | DECIMAL(4,3) | NO | | 0.100 | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- FK id_penyakit → penyakit(id) ON DELETE CASCADE
- FK id_pupuk → pupuk(id) ON DELETE CASCADE

**Index**:
- PRIMARY KEY (id)
- UNIQUE (id_penyakit, id_pupuk)

---

### 8. **penyakit_pestisida**
**Deskripsi**: Tabel relasi many-to-many antara penyakit dan pestisida dengan nilai CF

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_penyakit | BIGINT UNSIGNED | NO | FK | NULL | |
| id_pestisida | BIGINT UNSIGNED | NO | FK | NULL | |
| mb | DECIMAL(4,3) | NO | | 0.700 | |
| md | DECIMAL(4,3) | NO | | 0.100 | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- FK id_penyakit → penyakit(id) ON DELETE CASCADE
- FK id_pestisida → pestisida(id) ON DELETE CASCADE

**Index**:
- PRIMARY KEY (id)
- UNIQUE (id_penyakit, id_pestisida)

---

### 9. **kriteria**
**Deskripsi**: Master data kriteria untuk metode SAW

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| kode | VARCHAR(10) | NO | UNI | NULL | |
| nama | VARCHAR(100) | NO | | NULL | |
| jenis | ENUM('benefit', 'cost') | NO | | NULL | |
| bobot | DECIMAL(5,2) | NO | | NULL | |
| keterangan | TEXT | YES | | NULL | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- 1:N → rating_pupuk (id_kriteria)
- 1:N → rating_pestisida (id_kriteria)

**Index**:
- PRIMARY KEY (id)
- UNIQUE (kode)

**Contoh Data**:
```
| kode | nama          | jenis  | bobot |
| C01  | Harga         | cost   | 0.25  |
| C02  | Efektivitas   | benefit| 0.25  |
| C03  | Ketersediaan  | benefit| 0.25  |
| C04  | Dosis         | cost   | 0.25  |
```

---

### 10. **rating_pupuk**
**Deskripsi**: Rating alternatif pupuk untuk setiap kriteria dan penyakit

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_pupuk | BIGINT UNSIGNED | NO | FK | NULL | |
| id_kriteria | BIGINT UNSIGNED | NO | FK | NULL | |
| id_penyakit | BIGINT UNSIGNED | NO | FK | NULL | |
| nilai | DECIMAL(5,2) | NO | | NULL | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- FK id_pupuk → pupuk(id) ON DELETE CASCADE
- FK id_kriteria → kriteria(id) ON DELETE CASCADE
- FK id_penyakit → penyakit(id) ON DELETE CASCADE

**Index**:
- PRIMARY KEY (id)
- UNIQUE (id_pupuk, id_kriteria, id_penyakit)

---

### 11. **rating_pestisida**
**Deskripsi**: Rating alternatif pestisida untuk setiap kriteria dan penyakit

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_pestisida | BIGINT UNSIGNED | NO | FK | NULL | |
| id_kriteria | BIGINT UNSIGNED | NO | FK | NULL | |
| id_penyakit | BIGINT UNSIGNED | NO | FK | NULL | |
| nilai | DECIMAL(5,2) | NO | | NULL | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- FK id_pestisida → pestisida(id) ON DELETE CASCADE
- FK id_kriteria → kriteria(id) ON DELETE CASCADE
- FK id_penyakit → penyakit(id) ON DELETE CASCADE

**Index**:
- PRIMARY KEY (id)
- UNIQUE (id_pestisida, id_kriteria, id_penyakit)

---

### 12. **rekomendasi**
**Deskripsi**: Header tabel untuk menyimpan hasil rekomendasi user

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_user | BIGINT UNSIGNED | NO | FK | NULL | |
| id_penyakit | BIGINT UNSIGNED | NO | FK | NULL | |
| tanggal | TIMESTAMP | NO | | CURRENT_TIMESTAMP | |
| preferensi_label | VARCHAR(50) | YES | | NULL | |
| preferensi_pengguna | JSON | YES | | NULL | |
| created_at | TIMESTAMP | YES | | NULL | |
| updated_at | TIMESTAMP | YES | | NULL | |

**Relasi**:
- FK id_user → users(id) ON DELETE CASCADE
- FK id_penyakit → penyakit(id) ON DELETE CASCADE
- 1:N → detail_rekomendasi_pupuk (id_rekomendasi)
- 1:N → detail_rekomendasi_pestisida (id_rekomendasi)

**Index**:
- PRIMARY KEY (id)
- INDEX (id_user)
- INDEX (id_penyakit)
- INDEX (tanggal)

**Contoh preferensi_pengguna (JSON)**:
```json
{
  "Harga": 0.40,
  "Efektivitas": 0.20,
  "Ketersediaan": 0.20,
  "Dosis": 0.20
}
```

---

### 13. **detail_rekomendasi_pupuk**
**Deskripsi**: Detail pupuk dalam satu rekomendasi

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_rekomendasi | BIGINT UNSIGNED | NO | FK | NULL | |
| id_pupuk | BIGINT UNSIGNED | NO | FK | NULL | |
| nilai_vi | DECIMAL(8,6) | NO | | NULL | |
| peringkat | INT | NO | | NULL | |

**Relasi**:
- FK id_rekomendasi → rekomendasi(id) ON DELETE CASCADE
- FK id_pupuk → pupuk(id) ON DELETE CASCADE

**Index**:
- PRIMARY KEY (id)
- INDEX (id_rekomendasi)
- INDEX (peringkat)

---

### 14. **detail_rekomendasi_pestisida**
**Deskripsi**: Detail pestisida dalam satu rekomendasi

| Kolom | Tipe Data | Null | Key | Default | Extra |
|-------|-----------|------|-----|---------|-------|
| id | BIGINT UNSIGNED | NO | PRI | NULL | auto_increment |
| id_rekomendasi | BIGINT UNSIGNED | NO | FK | NULL | |
| id_pestisida | BIGINT UNSIGNED | NO | FK | NULL | |
| nilai_vi | DECIMAL(8,6) | NO | | NULL | |
| peringkat | INT | NO | | NULL | |

**Relasi**:
- FK id_rekomendasi → rekomendasi(id) ON DELETE CASCADE
- FK id_pestisida → pestisida(id) ON DELETE CASCADE

**Index**:
- PRIMARY KEY (id)
- INDEX (id_rekomendasi)
- INDEX (peringkat)

---

## 🔗 DIAGRAM RELASI LENGKAP

```
┌──────────────┐
│    users     │
│ (role-based) │
└──────┬───────┘
       │ 1:N
       ▼
┌──────────────┐       ┌──────────────┐
│ rekomendasi  │──────▶│   penyakit   │
└──────┬───────┘ 1:N   └──────┬───────┘
       │                      │
       │ 1:N                  │ 1:N
       ▼                      ▼
┌──────────────────┐  ┌──────────────────┐
│ detail_pupuk     │  │ penyakit_gejala  │
│ detail_pestisida │  │ penyakit_pupuk   │
└──────────────────┘  │ penyakit_pestisida│
                      └─────────┬────────┘
                                │
                       ┌────────┼────────┐
                       ▼        ▼        ▼
                 ┌────────┐ ┌──────┐ ┌──────────┐
                 │ gejala │ │pupuk │ │pestisida │
                 └────────┘ └──┬───┘ └────┬─────┘
                               │           │
                               │ 1:N       │ 1:N
                               ▼           ▼
                         ┌──────────┐ ┌────────────┐
                         │rating_ppk│ │rating_pstsd│
                         └────┬─────┘ └─────┬──────┘
                              │             │
                              └──────┬──────┘
                                     │
                                     ▼
                               ┌──────────┐
                               │ kriteria │
                               └──────────┘
```

---

## 📝 NORMALISASI DATABASE

### **Bentuk Normal 1 (1NF)**
✅ Semua atribut atomic (tidak ada multi-value)
✅ Setiap tabel memiliki primary key
✅ Tidak ada repeating groups

### **Bentuk Normal 2 (2NF)**
✅ Sudah dalam 1NF
✅ Semua atribut non-key bergantung penuh pada primary key
✅ Tidak ada partial dependency

### **Bentuk Normal 3 (3NF)**
✅ Sudah dalam 2NF
✅ Tidak ada transitive dependency
✅ Semua atribut non-key hanya bergantung pada primary key

---

## 🔐 INTEGRITY CONSTRAINTS

### **Referential Integrity**
- Semua foreign key menggunakan `ON DELETE CASCADE`
- Relasi many-to-many menggunakan junction tables
- Circular references dihindari

### **Domain Integrity**
- ENUM types untuk role, jenis pestisida, jenis kriteria
- DECIMAL precision untuk nilai numerik (MB, MD, bobot, harga)
- UNIQUE constraints untuk kode-kode master data
- NOT NULL untuk required fields

### **Entity Integrity**
- Primary key pada semua tabel
- Auto-increment untuk surrogate keys
- No duplicate primary keys

---

## 📊 CONTOH QUERY PENTING

### **1. Diagnosis Penyakit dengan CF**
```sql
SELECT 
    p.nama AS penyakit,
    pg.mb,
    pg.md,
    (pg.mb - pg.md) AS cf
FROM penyakit p
JOIN penyakit_gejala pg ON p.id = pg.id_penyakit
JOIN gejala g ON pg.id_gejala = g.id
WHERE g.id IN (1, 3, 5)  -- Gejala yang dipilih user
ORDER BY cf DESC;
```

### **2. Rekomendasi Pupuk dengan SAW**
```sql
SELECT 
    pup.nama AS pupuk,
    SUM(k.bobot * rp.nilai) AS nilai_vi,
    RANK() OVER (ORDER BY SUM(k.bobot * rp.nilai) DESC) AS peringkat
FROM pupuk pup
JOIN rating_pupuk rp ON pup.id = rp.id_pupuk
JOIN kriteria k ON rp.id_kriteria = k.id
WHERE rp.id_penyakit = 1
GROUP BY pup.id, pup.nama
ORDER BY nilai_vi DESC
LIMIT 3;
```

### **3. Riwayat User**
```sql
SELECT 
    r.id,
    r.tanggal,
    p.nama AS penyakit,
    r.preferensi_label,
    COUNT(DISTINCT dp.id) AS jumlah_pupuk,
    COUNT(DISTINCT dpe.id) AS jumlah_pestisida
FROM rekomendasi r
JOIN penyakit p ON r.id_penyakit = p.id
LEFT JOIN detail_rekomendasi_pupuk dp ON r.id = dp.id_rekomendasi
LEFT JOIN detail_rekomendasi_pestisida dpe ON r.id = dpe.id_rekomendasi
WHERE r.id_user = 1
GROUP BY r.id, r.tanggal, p.nama, r.preferensi_label
ORDER BY r.tanggal DESC;
```

---

## 🚀 OPTIMISASI DATABASE

### **Indexes**
```sql
-- Index untuk performa query diagnosis
CREATE INDEX idx_penyakit_gejala ON penyakit_gejala(id_penyakit, id_gejala);

-- Index untuk query riwayat
CREATE INDEX idx_rekomendasi_user_tanggal ON rekomendasi(id_user, tanggal DESC);

-- Index untuk ranking
CREATE INDEX idx_detail_pupuk_peringkat ON detail_rekomendasi_pupuk(id_rekomendasi, peringkat);
CREATE INDEX idx_detail_pestisida_peringkat ON detail_rekomendasi_pestisida(id_rekomendasi, peringkat);
```

### **Partitioning** (Opsional untuk data besar)
```sql
-- Partition tabel rekomendasi berdasarkan tahun
ALTER TABLE rekomendasi
PARTITION BY RANGE (YEAR(tanggal)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION pmax VALUES LESS THAN MAXVALUE
);
```

---

**Dibuat**: 2025
**Versi**: 1.0
**Status**: Final
