/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `detail_rekomendasi_pestisida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_rekomendasi_pestisida` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rekomendasi` bigint unsigned NOT NULL,
  `id_pestisida` bigint unsigned NOT NULL,
  `nilai_vi` decimal(8,6) NOT NULL,
  `peringkat` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_rekomendasi_pestisida_id_rekomendasi_foreign` (`id_rekomendasi`),
  KEY `detail_rekomendasi_pestisida_id_pestisida_foreign` (`id_pestisida`),
  CONSTRAINT `detail_rekomendasi_pestisida_id_pestisida_foreign` FOREIGN KEY (`id_pestisida`) REFERENCES `pestisida` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_rekomendasi_pestisida_id_rekomendasi_foreign` FOREIGN KEY (`id_rekomendasi`) REFERENCES `rekomendasi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `detail_rekomendasi_pupuk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_rekomendasi_pupuk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rekomendasi` bigint unsigned NOT NULL,
  `id_pupuk` bigint unsigned NOT NULL,
  `nilai_vi` decimal(8,6) NOT NULL,
  `peringkat` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_rekomendasi_pupuk_id_rekomendasi_foreign` (`id_rekomendasi`),
  KEY `detail_rekomendasi_pupuk_id_pupuk_foreign` (`id_pupuk`),
  CONSTRAINT `detail_rekomendasi_pupuk_id_pupuk_foreign` FOREIGN KEY (`id_pupuk`) REFERENCES `pupuk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_rekomendasi_pupuk_id_rekomendasi_foreign` FOREIGN KEY (`id_rekomendasi`) REFERENCES `rekomendasi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gejala`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gejala` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_gejala` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gejala_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gejala_pestisida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gejala_pestisida` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_gejala` bigint unsigned NOT NULL,
  `id_pestisida` bigint unsigned NOT NULL,
  `mb` decimal(4,3) NOT NULL DEFAULT '0.700',
  `md` decimal(4,3) NOT NULL DEFAULT '0.100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gejala_pestisida_id_gejala_id_pestisida_unique` (`id_gejala`,`id_pestisida`),
  KEY `gejala_pestisida_id_pestisida_foreign` (`id_pestisida`),
  CONSTRAINT `gejala_pestisida_id_gejala_foreign` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gejala_pestisida_id_pestisida_foreign` FOREIGN KEY (`id_pestisida`) REFERENCES `pestisida` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gejala_pupuk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gejala_pupuk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_gejala` bigint unsigned NOT NULL,
  `id_pupuk` bigint unsigned NOT NULL,
  `mb` decimal(4,3) NOT NULL DEFAULT '0.700',
  `md` decimal(4,3) NOT NULL DEFAULT '0.100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gejala_pupuk_id_gejala_id_pupuk_unique` (`id_gejala`,`id_pupuk`),
  KEY `gejala_pupuk_id_pupuk_foreign` (`id_pupuk`),
  CONSTRAINT `gejala_pupuk_id_gejala_foreign` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gejala_pupuk_id_pupuk_foreign` FOREIGN KEY (`id_pupuk`) REFERENCES `pupuk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kriteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kriteria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('benefit','cost') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kriteria_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penyakit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyakit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penyakit_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penyakit_gejala`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyakit_gejala` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_penyakit` bigint unsigned NOT NULL,
  `id_gejala` bigint unsigned NOT NULL,
  `mb` decimal(4,3) NOT NULL DEFAULT '0.700',
  `md` decimal(4,3) NOT NULL DEFAULT '0.100',
  PRIMARY KEY (`id`),
  UNIQUE KEY `penyakit_gejala_id_penyakit_id_gejala_unique` (`id_penyakit`,`id_gejala`),
  KEY `penyakit_gejala_id_gejala_foreign` (`id_gejala`),
  CONSTRAINT `penyakit_gejala_id_gejala_foreign` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penyakit_gejala_id_penyakit_foreign` FOREIGN KEY (`id_penyakit`) REFERENCES `penyakit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penyakit_pestisida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyakit_pestisida` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_penyakit` bigint unsigned NOT NULL,
  `id_pestisida` bigint unsigned NOT NULL,
  `mb` decimal(4,3) NOT NULL DEFAULT '0.700',
  `md` decimal(4,3) NOT NULL DEFAULT '0.100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penyakit_pestisida_id_penyakit_id_pestisida_unique` (`id_penyakit`,`id_pestisida`),
  KEY `penyakit_pestisida_id_pestisida_foreign` (`id_pestisida`),
  CONSTRAINT `penyakit_pestisida_id_penyakit_foreign` FOREIGN KEY (`id_penyakit`) REFERENCES `penyakit` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penyakit_pestisida_id_pestisida_foreign` FOREIGN KEY (`id_pestisida`) REFERENCES `pestisida` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penyakit_pupuk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyakit_pupuk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_penyakit` bigint unsigned NOT NULL,
  `id_pupuk` bigint unsigned NOT NULL,
  `mb` decimal(4,3) NOT NULL DEFAULT '0.700',
  `md` decimal(4,3) NOT NULL DEFAULT '0.100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penyakit_pupuk_id_penyakit_id_pupuk_unique` (`id_penyakit`,`id_pupuk`),
  KEY `penyakit_pupuk_id_pupuk_foreign` (`id_pupuk`),
  CONSTRAINT `penyakit_pupuk_id_penyakit_foreign` FOREIGN KEY (`id_penyakit`) REFERENCES `penyakit` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penyakit_pupuk_id_pupuk_foreign` FOREIGN KEY (`id_pupuk`) REFERENCES `pupuk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pestisida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pestisida` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('fungisida','bakterisida','insektisida','herbisida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bahan_aktif` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kandungan_detail` text COLLATE utf8mb4_unicode_ci,
  `fungsi` text COLLATE utf8mb4_unicode_ci,
  `takaran` text COLLATE utf8mb4_unicode_ci,
  `efek_penggunaan` text COLLATE utf8mb4_unicode_ci,
  `cara_aplikasi` text COLLATE utf8mb4_unicode_ci,
  `jadwal_umur_aplikasi` text COLLATE utf8mb4_unicode_ci,
  `frekuensi_aplikasi` text COLLATE utf8mb4_unicode_ci,
  `dosis` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `satuan_harga` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'per 100ml',
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pestisida_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pupuk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pupuk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kandungan` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kandungan_detail` text COLLATE utf8mb4_unicode_ci,
  `fungsi_utama` text COLLATE utf8mb4_unicode_ci,
  `takaran` text COLLATE utf8mb4_unicode_ci,
  `efek_penggunaan` text COLLATE utf8mb4_unicode_ci,
  `cara_aplikasi` text COLLATE utf8mb4_unicode_ci,
  `jadwal_umur_aplikasi` text COLLATE utf8mb4_unicode_ci,
  `frekuensi_aplikasi` text COLLATE utf8mb4_unicode_ci,
  `harga_per_kg` decimal(10,2) NOT NULL,
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pupuk_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rekomendasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rekomendasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_penyakit` bigint unsigned NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `preferensi_label` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferensi_pengguna` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rekomendasi_id_user_foreign` (`id_user`),
  KEY `rekomendasi_id_penyakit_foreign` (`id_penyakit`),
  CONSTRAINT `rekomendasi_id_penyakit_foreign` FOREIGN KEY (`id_penyakit`) REFERENCES `penyakit` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rekomendasi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','petani') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'petani',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `catatan_profil` text COLLATE utf8mb4_unicode_ci,
  `foto_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2024_01_01_000100_create_penyakit_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2024_01_01_000200_create_all_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_04_11_000300_add_profile_and_preference_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_04_11_000400_add_image_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_04_14_000500_add_detail_fields_to_pupuk_and_pestisida',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_04_18_000500_add_gambar_to_gejala_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_04_21_000600_add_phone_login_columns_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_04_21_001000_ensure_otp_columns_exist_on_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_04_27_000100_add_cf_rule_tables',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_04_28_000200_add_symptom_rule_tables',4);
