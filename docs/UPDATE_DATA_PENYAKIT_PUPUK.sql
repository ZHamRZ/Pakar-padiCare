-- ============================================================================
-- SCRIPT UPDATE DATA MB/MD UNTUK PUPUK DAN PESTISIDA
-- ============================================================================
-- 
-- MASALAH: Data MB/MD saat ini tidak konsisten dengan pengetahuan pakar
-- Contoh: Pakar bilang "KCl dibutuhkan untuk ketahanan blas", 
--         tapi data MB=0.85 (diartikan sebagai "KCl memperparah blas 85%")
--
-- SOLUSI: Tukar nilai MB dan MD agar sesuai interpretasi pakar
--         - MB tinggi = mencegah/mengatasi penyakit (BUKAN memperparah!)
--         - MD tinggi = memperparah penyakit
--
-- CATATAN PENTING:
-- - Backup database SEBELUM menjalankan script ini!
-- - Script ini hanya untuk tabel penyakit_pupuk dan penyakit_pestisida
-- - Pastikan data sudah diverifikasi oleh pakar pertanian
-- ============================================================================

-- Backup tabel sebelum update (opsional, sesuaikan nama backup)
-- CREATE TABLE penyakit_pupuk_backup AS SELECT * FROM penyakit_pupuk;
-- CREATE TABLE penyakit_pestisida_backup AS SELECT * FROM penyakit_pestisida;

-- ============================================================================
-- A. UPDATE TABEL penyakit_pupuk
-- ============================================================================
-- LOGIKA: Tukar MB dan MD untuk semua record
-- Sebelum: MB=0.85, MD=0.10 → CF_dasar=0.75 (memperparah)
-- Sesudah: MB=0.10, MD=0.85 → CF_dasar=-0.75 (mencegah) → CF_rekomendasi=0.75 (direkomendasikan!)

-- Update semua record dengan menukar MB dan MD
UPDATE penyakit_pupuk 
SET mb = @old_md := md, 
    md = mb 
WHERE @old_md IS NOT NULL OR TRUE;

-- Verifikasi hasil update
SELECT '=== HASIL UPDATE penyakit_pupuk ===' AS info;
SELECT pp.id, p.nama AS penyakit_nama, pup.nama AS pupuk_nama, 
       pp.mb, pp.md, (pp.mb - pp.md) AS cf_dasar,
       CASE 
           WHEN (pp.mb - pp.md) < 0 THEN 'DIREKOMENDASIKAN (CF negatif)'
           WHEN (pp.mb - pp.md) > 0 THEN 'TIDAK DIREKOMENDASIKAN (CF positif)'
           ELSE 'NETRAL'
       END AS status
FROM penyakit_pupuk pp
JOIN penyakit p ON pp.id_penyakit = p.id
JOIN pupuk pup ON pp.id_pupuk = pup.id
ORDER BY p.kode, pup.kode;

-- ============================================================================
-- B. UPDATE TABEL penyakit_pestisida (JIKA PERLU)
-- ============================================================================
-- LOGIKA: Untuk pestisida, TIDAK PERLU tukar MB/MD karena:
--         - MB tinggi = efektif mengatasi penyakit (sudah benar!)
--         - MD tinggi = tidak efektif
--         - CF_solusi = MB - MD (positif = direkomendasikan)
--
-- HANYA jalankan update di bawah jika ada kesalahan input data

-- Uncomment jika perlu update pestisida juga:
-- UPDATE penyakit_pestisida 
-- SET mb = @old_md := md, 
--     md = mb 
-- WHERE @old_md IS NOT NULL OR TRUE;

-- Verifikasi pestisida (tanpa update)
SELECT '=== DATA penyakit_pestisida (TANPA UPDATE) ===' AS info;
SELECT pp.id, p.nama AS penyakit_nama, pes.nama AS pestisida_nama, 
       pp.mb, pp.md, (pp.mb - pp.md) AS cf_solusi,
       CASE 
           WHEN (pp.mb - pp.md) > 0 THEN 'DIREKOMENDASIKAN (CF positif)'
           WHEN (pp.mb - pp.md) < 0 THEN 'TIDAK DIREKOMENDASIKAN (CF negatif)'
           ELSE 'NETRAL'
       END AS status
FROM penyakit_pestisida pp
JOIN penyakit p ON pp.id_penyakit = p.id
JOIN pestisida pes ON pp.id_pestisida = pes.id
ORDER BY p.kode, pes.kode;

-- ============================================================================
-- C. VERIFIKASI AKHIR - CONTOH KASUS BLAS (P01)
-- ============================================================================
SELECT '=== VERIFIKASI: REKOMENDASI UNTUK BLAS (P01) ===' AS info;
SELECT pup.nama AS pupuk, 
       pp.mb, 
       pp.md, 
       ROUND((pp.mb - pp.md), 3) AS cf_dasar,
       ROUND(-(pp.mb - pp.md), 3) AS cf_rekomendasi,
       CASE 
           WHEN (pp.mb - pp.md) < -0.5 THEN '✓✓ SANGAT DIREKOMENDASIKAN'
           WHEN (pp.mb - pp.md) < -0.2 THEN '✓ DIREKOMENDASIKAN'
           WHEN (pp.mb - pp.md) < 0 THEN '~ KURANG DIREKOMENDASIKAN'
           ELSE '✗ TIDAK DIREKOMENDASIKAN'
       END AS status_rekomendasi
FROM penyakit_pupuk pp
JOIN penyakit p ON pp.id_penyakit = p.id
JOIN pupuk pup ON pp.id_pupuk = pup.id
WHERE p.kode = 'P01'
ORDER BY cf_rekomendasi DESC;

-- ============================================================================
-- D. VERIFIKASI - CONTOH KASUS HAWAR DAUN BAKTERI (P02)
-- ============================================================================
SELECT '=== VERIFIKASI: REKOMENDASI UNTUK HAWAR DAUN BAKTERI (P02) ===' AS info;
SELECT pup.nama AS pupuk, 
       pp.mb, 
       pp.md, 
       ROUND((pp.mb - pp.md), 3) AS cf_dasar,
       ROUND(-(pp.mb - pp.md), 3) AS cf_rekomendasi,
       CASE 
           WHEN (pp.mb - pp.md) < -0.5 THEN '✓✓ SANGAT DIREKOMENDASIKAN'
           WHEN (pp.mb - pp.md) < -0.2 THEN '✓ DIREKOMENDASIKAN'
           WHEN (pp.mb - pp.md) < 0 THEN '~ KURANG DIREKOMENDASIKAN'
           ELSE '✗ TIDAK DIREKOMENDASIKAN'
       END AS status_rekomendasi
FROM penyakit_pupuk pp
JOIN penyakit p ON pp.id_penyakit = p.id
JOIN pupuk pup ON pp.id_pupuk = pup.id
WHERE p.kode = 'P02'
ORDER BY cf_rekomendasi DESC;

-- ============================================================================
-- E. RINGKASAN STATISTIK
-- ============================================================================
SELECT '=== RINGKASAN STATISTIK ===' AS info;
SELECT 
    COUNT(*) AS total_record_pupuk,
    SUM(CASE WHEN (mb - md) < 0 THEN 1 ELSE 0 END) AS pupuk_direkomendasikan,
    SUM(CASE WHEN (mb - md) >= 0 THEN 1 ELSE 0 END) AS pupuk_tidak_direkomendasikan,
    ROUND(AVG(mb - md), 3) AS rata_rata_cf_dasar
FROM penyakit_pupuk;

-- ============================================================================
-- SELESAI
-- ============================================================================
-- Jika hasil verifikasi sudah benar, commit transaksi
-- Jika ada kesalahan, rollback dengan: ROLLBACK;
-- ============================================================================
