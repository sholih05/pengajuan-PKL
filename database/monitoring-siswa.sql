-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table monitoring_siswa.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.cache: ~5 rows (approximately)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('captcha_76415e17a7a6c1378f69b830d464f382', 's:9:"22 + 8 = ";', 1733725391),
	('captcha_91e91e7b8049428c2131b863feba6955', 's:9:"17 + 7 = ";', 1737164085),
	('captcha_b6674793aa12c08f9abd5909a6a0c735', 's:9:"16 + 6 = ";', 1737271123),
	('captcha_c9fbfac198d3924cc8901dd54dda27c8', 's:9:"21 + 5 = ";', 1736937618),
	('captcha_d5eb91d51348b0bcacb0ff6327904788', 's:9:"28 + 2 = ";', 1737113695);

-- Dumping structure for table monitoring_siswa.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.cache_locks: ~0 rows (approximately)
DELETE FROM `cache_locks`;

-- Dumping structure for table monitoring_siswa.catatan
CREATE TABLE IF NOT EXISTS `catatan` (
  `id_catatan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `catatan` varchar(225)  NOT NULL,
  `kategori` char(1) NOT NULL,
  `id_instruktur` varchar(15) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_catatan`) USING BTREE,
  KEY `catatan_instruktur_fk` (`id_instruktur`) USING BTREE,
  CONSTRAINT `catatan_instruktur_fk` FOREIGN KEY (`id_instruktur`) REFERENCES `instruktur` (`id_instruktur`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.catatan: ~0 rows (approximately)
DELETE FROM `catatan`;
INSERT INTO `catatan` (`id_catatan`, `tanggal`, `catatan`, `kategori`, `id_instruktur`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(1, '2024-11-28', 'qqqqqqqq', 'S', '222222222222222', '2024-11-28 11:23:32', 1, '2024-11-28 11:30:56', 1, 1);

-- Dumping structure for table monitoring_siswa.dudi
CREATE TABLE IF NOT EXISTS `dudi` (
  `id_dudi` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(30)  NOT NULL,
  `alamat` varchar(100)  NOT NULL,
  `no_kontak` varchar(14) NOT NULL,
  `longitude` varchar(50)  NOT NULL,
  `latitude` varchar(50)  NOT NULL,
  `radius` varchar(255) DEFAULT NULL COMMENT 'radius absen',
  `nama_pimpinan` varchar(50)  NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_dudi`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.dudi: ~5 rows (approximately)
DELETE FROM `dudi`;
INSERT INTO `dudi` (`id_dudi`, `nama`, `alamat`, `no_kontak`, `longitude`, `latitude`, `radius`, `nama_pimpinan`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(1, 'PT. HUJAN', 'JL. MAWAR 1', '081500009999', '109.13389205932619', '-6.983715249074065', '8', 'BAMBANG SUTOYO', '2024-11-14 13:31:23', NULL, '2024-11-19 10:06:22', NULL, 1),
	(2, 'PT. ANGIN', 'JL. ANGGUR', '081599997777', '109.10699806523947', '-6.868668820042076', '8', 'JUNAEDI', '2024-11-14 13:32:19', NULL, '2024-11-19 10:06:22', NULL, 1),
	(3, 'POLTEK KK', 'JL PESURUNGAN', '09998767866', '109.10704582929613', '-6.868695301569697', '10', 'HERU', '2024-11-19 09:45:59', NULL, '2024-11-19 10:05:06', 1, 1),
	(6, 'PT. CONTOH', 'JL. MAWAR 1', '081500009999', '109.13389205933', '-6.9837152490741', '8', 'BAMBANG SUTOYO', '2024-12-01 22:20:37', 1, '2024-12-01 23:27:24', 1, 1),
	(7, 'PT. CONTOH', 'JL. MAWAR 1', '081500009999', '109.13389205933', '-6.9837152490741', '8', 'BAMBANG SUTOYO', '2024-12-01 22:20:37', 1, '2024-12-01 23:20:23', 1, 1);

-- Dumping structure for table monitoring_siswa.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table monitoring_siswa.guru
CREATE TABLE IF NOT EXISTS `guru` (
  `id_guru` varchar(15) NOT NULL,
  `nama` varchar(50)  NOT NULL,
  `gender` char(1) NOT NULL,
  `no_kontak` varchar(14) NOT NULL,
  `email` varchar(35) NOT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `id_user` bigint unsigned DEFAULT NULL,
  `id_jurusan` char(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_guru`) USING BTREE,
  UNIQUE KEY `guru__idx` (`id_user`) USING BTREE,
  KEY `guru_jurusan_fk` (`id_jurusan`) USING BTREE,
  CONSTRAINT `guru_jurusan_fk` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `guru_user_fk` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.guru: ~3 rows (approximately)
DELETE FROM `guru`;
INSERT INTO `guru` (`id_guru`, `nama`, `gender`, `no_kontak`, `email`, `alamat`, `id_user`, `id_jurusan`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	('111199999999999', 'TYAS MIRASIH', 'P', '085677779999', 'tyas@mail.com', 'JL. MAKAM 1', 31, 'AK', '2024-12-02 00:14:24', 1, '2024-12-02 00:14:24', 1, 1),
	('444444444444444', 'FAJAR ZUL', 'L', '081566667777', 'zul@mail.com', 'JL. SENTOSA', 29, 'TKR', '2024-11-14 21:35:04', NULL, '2024-12-02 00:14:24', 1, 1),
	('999999999999999', 'JONO', 'L', '085677779889', 'jono@mail.com', 'JL. MAKAM 2', 26, 'AK', '2024-11-14 13:37:49', NULL, '2024-12-02 00:14:24', 1, 1);

-- Dumping structure for table monitoring_siswa.instruktur
CREATE TABLE IF NOT EXISTS `instruktur` (
  `id_instruktur` varchar(15)  NOT NULL,
  `nama` varchar(50) NOT NULL,
  `gender` char(1) NOT NULL,
  `no_kontak` varchar(14) NOT NULL,
  `email` varchar(35) NOT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `id_dudi` int NOT NULL,
  `id_user` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_instruktur`) USING BTREE,
  UNIQUE KEY `instruktur__idx` (`id_user`) USING BTREE,
  KEY `instruktur_dudi_fk` (`id_dudi`) USING BTREE,
  CONSTRAINT `instruktur_dudi_fk` FOREIGN KEY (`id_dudi`) REFERENCES `dudi` (`id_dudi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `instruktur_user_fk` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.instruktur: ~3 rows (approximately)
DELETE FROM `instruktur`;
INSERT INTO `instruktur` (`id_instruktur`, `nama`, `gender`, `no_kontak`, `email`, `alamat`, `id_dudi`, `id_user`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	('111111111111111', 'SUPRI', 'L', '081577778888', 'supri@example.com', 'JL. SEHAT SENTOSA', 1, 24, '2024-11-14 13:34:36', NULL, '2024-12-02 00:14:55', 1, 1),
	('222222222222222', 'AYU PUTRI', 'P', '085622221111', 'putri@mail.com', 'JL. ALAM INDAH', 2, 25, '2024-11-14 13:35:45', NULL, '2024-12-02 00:14:55', 1, 1),
	('333322222222222', 'OBET', 'L', '85622221114', 'obet@mail.com', 'JL. ALAM INDAH', 3, 32, '2024-12-02 00:14:56', 1, '2024-12-02 00:14:56', 1, 1);

-- Dumping structure for table monitoring_siswa.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `jobs_queue_index` (`queue`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table monitoring_siswa.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table monitoring_siswa.jurusan
CREATE TABLE IF NOT EXISTS `jurusan` (
  `id_jurusan` char(5) NOT NULL,
  `jurusan` varchar(35) NOT NULL,
  `singkatan` char(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_jurusan`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.jurusan: ~4 rows (approximately)
DELETE FROM `jurusan`;
INSERT INTO `jurusan` (`id_jurusan`, `jurusan`, `singkatan`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	('123', 'Teknik Informatika', 'TI', '2024-11-28 14:36:05', NULL, '2024-11-28 14:40:40', 1, 1),
	('124', 'Teknik Mesin', 'TM', '2024-11-28 14:36:05', NULL, '2024-11-28 14:40:40', 1, 1),
	('37327', 'hqwhh', '7y7', '2024-11-28 14:40:40', NULL, '2024-11-28 14:40:40', 1, 1),
	('AK', 'Akuntansi', 'AK', '2024-11-14 13:25:26', 1, '2024-11-14 13:25:26', NULL, 1),
	('TKR', 'Teknik Kendaraan Ringan', 'TKR', '2024-11-14 13:25:08', 1, '2024-11-14 13:25:08', NULL, 1);

-- Dumping structure for table monitoring_siswa.ketersediaan
CREATE TABLE IF NOT EXISTS `ketersediaan` (
  `id_ketersediaan` int NOT NULL AUTO_INCREMENT,
  `tanggal` date DEFAULT NULL,
  `id_jurusan` char(5) NOT NULL,
  `id_dudi` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_ketersediaan`) USING BTREE,
  KEY `ketersediaan_dudi_fk` (`id_dudi`) USING BTREE,
  KEY `ketersediaan_jurusan_fk` (`id_jurusan`) USING BTREE,
  CONSTRAINT `ketersediaan_dudi_fk` FOREIGN KEY (`id_dudi`) REFERENCES `dudi` (`id_dudi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ketersediaan_jurusan_fk` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.ketersediaan: ~4 rows (approximately)
DELETE FROM `ketersediaan`;
INSERT INTO `ketersediaan` (`id_ketersediaan`, `tanggal`, `id_jurusan`, `id_dudi`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(1, '2024-11-14', 'AK', 2, '2024-11-14 13:36:03', 1, '2024-11-14 13:36:03', NULL, 1),
	(2, '2024-11-14', '124', 2, '2024-11-14 13:36:17', 1, '2024-12-24 10:31:28', 26, 0),
	(3, '2024-12-24', 'TKR', 2, '2024-12-24 10:26:35', 26, '2024-12-24 10:26:35', NULL, 1),
	(4, '2024-12-26', '123', 2, '2024-12-24 10:28:01', 26, '2024-12-24 10:28:01', NULL, 1),
	(5, '2025-01-18', '123', 1, '2025-01-18 08:25:03', 26, '2025-01-18 08:25:03', NULL, 1);

-- Dumping structure for table monitoring_siswa.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.migrations: ~0 rows (approximately)
DELETE FROM `migrations`;

-- Dumping structure for table monitoring_siswa.nilai_quesioner
CREATE TABLE IF NOT EXISTS `nilai_quesioner` (
  `id_nilai` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nilai` char(1) NOT NULL,
  `tanggal` date NOT NULL,
  `nis` varchar(255) DEFAULT NULL,
  `id_quesioner` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_nilai`) USING BTREE,
  KEY `nilai_quesioner_quesioner_fk` (`id_quesioner`) USING BTREE,
  KEY `nilai_quesioner_siswa_fk` (`nis`) USING BTREE,
  CONSTRAINT `nilai_quesioner_quesioner_fk` FOREIGN KEY (`id_quesioner`) REFERENCES `quesioner` (`id_quesioner`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `nilai_quesioner_siswa_fk` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.nilai_quesioner: ~9 rows (approximately)
DELETE FROM `nilai_quesioner`;
INSERT INTO `nilai_quesioner` (`id_nilai`, `nilai`, `tanggal`, `nis`, `id_quesioner`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(39, '1', '2024-12-24', '5555555555', 23, '2024-12-24 09:49:01', 26, '2024-12-24 09:49:01', NULL, 1),
	(40, '1', '2024-12-24', '5555555555', 27, '2024-12-24 09:49:01', 26, '2024-12-24 09:49:01', NULL, 1),
	(41, '0', '2024-12-24', '5555555555', 28, '2024-12-24 09:49:01', 26, '2024-12-24 09:49:28', NULL, 1),
	(42, '0', '2024-12-24', '5555555555', 29, '2024-12-24 09:49:01', 26, '2024-12-24 09:49:28', NULL, 1),
	(43, '1', '2025-01-18', '5555555555', 1, '2024-12-24 09:50:48', 27, '2025-01-18 08:45:44', NULL, 1),
	(44, '1', '2025-01-18', '5555555555', 2, '2024-12-24 09:50:48', 27, '2025-01-18 08:45:44', NULL, 1),
	(45, '1', '2025-01-18', '5555555555', 30, '2024-12-24 09:50:48', 27, '2025-01-18 08:45:44', NULL, 1),
	(46, '1', '2025-01-18', '5555555555', 31, '2024-12-24 09:50:48', 27, '2025-01-18 08:45:44', NULL, 1),
	(47, '1', '2025-01-18', '5555555555', 32, '2024-12-24 09:50:48', 27, '2025-01-18 08:45:44', NULL, 1);

-- Dumping structure for table monitoring_siswa.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table monitoring_siswa.penempatan
CREATE TABLE IF NOT EXISTS `penempatan` (
  `nis` char(10) NOT NULL,
  `id_penempatan` bigint NOT NULL AUTO_INCREMENT,
  `id_ta` int NOT NULL,
  `id_guru` varchar(15) NOT NULL,
  `id_instruktur` varchar(15)  NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_penempatan`) USING BTREE,
  KEY `penempatan_guru_fk` (`id_guru`) USING BTREE,
  KEY `penempatan_instruktur_fk` (`id_instruktur`) USING BTREE,
  KEY `penempatan_siswa_fk` (`nis`) USING BTREE,
  KEY `penempatan_thn_akademik_fk` (`id_ta`) USING BTREE,
  CONSTRAINT `penempatan_guru_fk` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `penempatan_instruktur_fk` FOREIGN KEY (`id_instruktur`) REFERENCES `instruktur` (`id_instruktur`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `penempatan_siswa_fk` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `penempatan_thn_akademik_fk` FOREIGN KEY (`id_ta`) REFERENCES `thn_akademik` (`id_ta`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.penempatan: ~6 rows (approximately)
DELETE FROM `penempatan`;
INSERT INTO `penempatan` (`nis`, `id_penempatan`, `id_ta`, `id_guru`, `id_instruktur`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	('5555555555', 1, 5, '999999999999999', '222222222222222', '2024-11-14 14:20:07', 1, '2024-12-20 14:02:12', 26, 1),
	('8888888888', 2, 5, '111199999999999', '222222222222222', '2024-11-14 16:53:21', 1, '2024-12-02 01:14:30', 1, 1),
	('5555555555', 3, 4, '999999999999999', '222222222222222', '2024-12-02 01:14:47', NULL, '2024-12-02 01:14:47', 1, 1),
	('8888888888', 4, 4, '111199999999999', '222222222222222', '2024-12-02 01:14:47', NULL, '2024-12-02 01:14:47', 1, 1),
	('5555555555', 5, 4, '444444444444444', '111111111111111', '2024-12-20 13:27:33', NULL, NULL, NULL, 1),
	('5555555555', 6, 4, '444444444444444', '222222222222222', '2024-12-20 13:58:59', 26, '2024-12-20 13:58:59', NULL, 1),
	('5555555555', 7, 5, '111199999999999', '333322222222222', '2025-01-22 09:29:54', 26, '2025-01-22 09:29:54', NULL, 1);

-- Dumping structure for table monitoring_siswa.penilaian
CREATE TABLE IF NOT EXISTS `penilaian` (
  `id_penilaian` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nilai_guru_pembimbing` int NOT NULL,
  `nilai_instruktur` int NOT NULL,
  `waktu_guru_pembimbing` date NOT NULL,
  `waktu_instruktur` date NOT NULL,
  `id_prg_obsvr` bigint unsigned NOT NULL,
  `id_guru` varchar(15) NOT NULL,
  `id_instruktur` varchar(15) NOT NULL,
  `nis` char(10)  NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_penilaian`) USING BTREE,
  KEY `penilaian_guru_fk` (`id_guru`) USING BTREE,
  KEY `penilaian_instruktur_fk` (`id_instruktur`) USING BTREE,
  KEY `penilaian_prg_obsvr_fk` (`id_prg_obsvr`) USING BTREE,
  KEY `penilaian_siswa_fk` (`nis`) USING BTREE,
  CONSTRAINT `penilaian_guru_fk` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `penilaian_instruktur_fk` FOREIGN KEY (`id_instruktur`) REFERENCES `instruktur` (`id_instruktur`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `penilaian_prg_obsvr_fk` FOREIGN KEY (`id_prg_obsvr`) REFERENCES `prg_obsvr` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `penilaian_siswa_fk` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.penilaian: ~0 rows (approximately)
DELETE FROM `penilaian`;

-- Dumping structure for table monitoring_siswa.presensi
CREATE TABLE IF NOT EXISTS `presensi` (
  `id_presensi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_penempatan` bigint DEFAULT NULL,
  `tanggal` date NOT NULL,
  `masuk` time NOT NULL,
  `pulang` time DEFAULT NULL,
  `kegiatan` varchar(100)DEFAULT NULL,
  `foto_masuk` varchar(50) DEFAULT NULL,
  `foto_pulang` varchar(50) DEFAULT NULL,
  `is_acc_instruktur` tinyint(1) DEFAULT NULL,
  `is_acc_guru` tinyint(1) DEFAULT NULL,
  `catatan` varchar(225) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_presensi`) USING BTREE,
  KEY `presensi_penempatan_fk` (`id_penempatan`),
  CONSTRAINT `presensi_penempatan_fk` FOREIGN KEY (`id_penempatan`) REFERENCES `penempatan` (`id_penempatan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.presensi: ~2 rows (approximately)
DELETE FROM `presensi`;
INSERT INTO `presensi` (`id_presensi`, `id_penempatan`, `tanggal`, `masuk`, `pulang`, `kegiatan`, `foto_masuk`, `foto_pulang`, `is_acc_instruktur`, `is_acc_guru`, `catatan`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(6, 3, '2024-12-20', '11:28:25', '11:28:27', NULL, NULL, NULL, 0, 1, NULL, '2024-12-20 11:28:45', NULL, '2024-12-24 11:04:10', NULL, 1),
	(7, 2, '2024-12-20', '13:10:00', '13:15:00', NULL, NULL, NULL, 1, 0, NULL, '2024-12-20 13:11:20', 26, '2025-01-18 08:37:28', 26, 1),
	(8, 6, '2024-12-23', '01:28:36', NULL, 'makan sate', '1734892115.9755.jpg', NULL, 0, NULL, 'bagus', '2024-12-23 01:28:36', 26, '2025-01-18 08:28:57', 26, 1),
	(9, 1, '2025-01-18', '08:43:44', '08:44:18', 'adaadd', '1737164623.0218.png', '1737164658.6124.png', 0, NULL, NULL, '2025-01-18 08:43:44', 27, '2025-01-22 09:22:26', 27, 1),
	(10, 1, '2025-01-22', '09:15:31', NULL, 'nfkjsdhfjsdkh', '1737512130.3857.png', NULL, 1, NULL, 'jangan telat', '2025-01-22 09:15:31', 26, '2025-01-22 09:22:40', 25, 1);

-- Dumping structure for table monitoring_siswa.prg_obsvr
CREATE TABLE IF NOT EXISTS `prg_obsvr` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `indikator` varchar(100)NOT NULL,
  `is_nilai` char(1)  NOT NULL,
  `id_ta` int NOT NULL,
  `id_guru` varchar(15) NOT NULL,
  `id_jurusan` char(5) NOT NULL,
  `id1` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `prg_obsvr_guru_fk` (`id_guru`) USING BTREE,
  KEY `prg_obsvr_jurusan_fk` (`id_jurusan`) USING BTREE,
  KEY `prg_obsvr_prg_obsvr_fk` (`id1`) USING BTREE,
  KEY `prg_obsvr_thn_akademik_fk` (`id_ta`) USING BTREE,
  CONSTRAINT `prg_obsvr_guru_fk` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `prg_obsvr_jurusan_fk` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `prg_obsvr_prg_obsvr_fk` FOREIGN KEY (`id1`) REFERENCES `prg_obsvr` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `prg_obsvr_thn_akademik_fk` FOREIGN KEY (`id_ta`) REFERENCES `thn_akademik` (`id_ta`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.prg_obsvr: ~0 rows (approximately)
DELETE FROM `prg_obsvr`;

-- Dumping structure for table monitoring_siswa.quesioner
CREATE TABLE IF NOT EXISTS `quesioner` (
  `id_quesioner` bigint unsigned NOT NULL AUTO_INCREMENT,
  `soal` varchar(225)  NOT NULL,
  `id_ta` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_quesioner`) USING BTREE,
  KEY `quesioner_thn_akademik_fk` (`id_ta`) USING BTREE,
  CONSTRAINT `quesioner_thn_akademik_fk` FOREIGN KEY (`id_ta`) REFERENCES `thn_akademik` (`id_ta`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.quesioner: ~9 rows (approximately)
DELETE FROM `quesioner`;
INSERT INTO `quesioner` (`id_quesioner`, `soal`, `id_ta`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(1, 'Kamu lapar?', 5, '2024-11-14 13:29:39', 1, '2024-11-14 13:29:39', NULL, 1),
	(2, 'Sudah makan?', 5, '2024-11-14 13:29:48', 1, '2024-11-14 13:29:48', NULL, 1),
	(23, 'Q1', 4, '2024-12-01 21:39:06', 1, '2024-12-01 21:39:06', NULL, 1),
	(27, 'Contoh soal 1', 4, '2024-12-01 21:43:45', 1, '2024-12-01 21:43:45', 1, 1),
	(28, 'Contoh soal 2', 4, '2024-12-01 21:43:45', 1, '2024-12-01 21:43:45', 1, 1),
	(29, 'Contoh soal 3', 4, '2024-12-01 21:43:45', 1, '2024-12-01 21:43:45', 1, 1),
	(30, 'Contoh soal 1', 5, '2024-12-01 21:44:08', 1, '2024-12-01 21:44:08', 1, 1),
	(31, 'Contoh soal 2', 5, '2024-12-01 21:44:08', 1, '2024-12-01 21:44:08', 1, 1),
	(32, 'Contoh soal 3', 5, '2024-12-01 21:44:08', 1, '2024-12-01 21:44:08', 1, 1);

-- Dumping structure for table monitoring_siswa.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `sessions_user_id_index` (`user_id`) USING BTREE,
  KEY `sessions_last_activity_index` (`last_activity`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.sessions: ~2 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('3hOq3TB8oKuLv6LclVpu7UkfZXAwUl9bVnGVPWdU', 26, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWGdUWFFrN0RyUXZqWk1iSTIwM3Byc3hRbENXS3NjVGtoRm0yWk8zcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kL3Npc3dhP25pcz01NTU1NTU1NTU1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjY7fQ==', 1737513440),
	('NTG55z28tF6sgLnH2Iw35KsxkmbOwhpo5kmbwIFb', 25, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiNWVlUk00TVo0OE1Hdm1HbnphTG1PeUM0Qk5HN0ZoNUpCZWxwWUZtZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjU7czoxMzoiaWRfaW5zdHJ1a3R1ciI7czoxNToiMjIyMjIyMjIyMjIyMjIyIjtzOjc6ImlkX2R1ZGkiO2k6MjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2QvaW5zdHJ1a3R1ciI7fX0=', 1737512586);

-- Dumping structure for table monitoring_siswa.siswa
CREATE TABLE IF NOT EXISTS `siswa` (
  `nis` char(10)  NOT NULL,
  `nisn` char(10) NOT NULL,
  `nama` varchar(50)  NOT NULL,
  `tempat_lahir` varchar(20)  NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `golongan_darah` varchar(2) DEFAULT NULL,
  `gender` char(1) NOT NULL,
  `foto` text,
  `no_kontak` varchar(14) NOT NULL,
  `email` varchar(35)  NOT NULL,
  `alamat` varchar(225)  NOT NULL,
  `id_jurusan` char(5) NOT NULL,
  `id_user` bigint unsigned DEFAULT NULL,
  `nama_wali` varchar(35) NOT NULL,
  `alamat_wali` varchar(225) NOT NULL,
  `no_kontak_wali` varchar(14) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `status_bekerja` enum('WFO','WFA') DEFAULT 'WFO',
  PRIMARY KEY (`nis`) USING BTREE,
  UNIQUE KEY `siswa__idx` (`id_user`) USING BTREE,
  KEY `siswa_jurusan_fk` (`id_jurusan`) USING BTREE,
  CONSTRAINT `siswa_jurusan_fk` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `siswa_user_fk` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.siswa: ~4 rows (approximately)
DELETE FROM `siswa`;
INSERT INTO `siswa` (`nis`, `nisn`, `nama`, `tempat_lahir`, `tanggal_lahir`, `golongan_darah`, `gender`, `foto`, `no_kontak`, `email`, `alamat`, `id_jurusan`, `id_user`, `nama_wali`, `alamat_wali`, `no_kontak_wali`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`, `status_bekerja`) VALUES
	('5555555555', '5555555555', 'AA', 'TEGAL', '2000-02-02', 'B', 'L', 'uploads/img_users/dtpt83X8MlhoQ6wQgsIpzYeZqUhYyIpR9UCLhlTk.jpg', '085699998888', 'asde@mail.com', 'JL. MUARA ANGKE', 'AK', 27, 'SUTRISNO', 'JL. MUARA ANGKE', '08122233345', '2024-11-14 13:40:05', 1, '2025-01-22 09:15:45', 26, 1, 'WFO'),
	('7777777777', '7777777777', 'BB', 'TEGAL', '2024-11-19', 'B', 'P', 'uploads/img_users/xz13bQOaFml5Okl6NvYdbLuo0gO1BIY9zCQPUZgP.png', '7777777777', 'hyda.arif@gmail.com', '7777', 'AK', 30, '777777', '777777', '777777777777', '2024-11-19 10:44:49', 1, '2024-12-20 08:54:48', 26, 1, 'WFA'),
	('7777777778', '7777777778', 'ADINDA BINTANG FEBIOLA', 'TEGAL', '2024-12-02', 'B', 'L', 'uploads/img_users/YDSMFtarYj7Xk5J4OBjaDhGaIt7ZwD8JUYXqJORZ.jpg', '090909090909', 'hyda.arif@gmail.com', 'ppp', '123', 33, 'SANTOSO', '0000', '0808086756565', '2024-12-02 08:52:34', 26, '2024-12-02 08:52:34', NULL, 1, 'WFO'),
	('8888888888', '8888888888', 'CC', 'TEGAL', '2000-11-14', 'AB', 'P', NULL, '081568888999', 'pria@mail.com', 'JL. LERENG BUKIT', 'TKR', 28, 'SUDARSO', 'JL. LERENG BUKIT', '086755552222', '2024-11-14 14:21:46', 1, '2024-12-02 08:54:11', 26, 1, 'WFO');

-- Dumping structure for table monitoring_siswa.thn_akademik
CREATE TABLE IF NOT EXISTS `thn_akademik` (
  `id_ta` int NOT NULL AUTO_INCREMENT,
  `tahun_akademik` char(9) NOT NULL,
  `mulai` date NOT NULL,
  `selesai` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_ta`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.thn_akademik: ~6 rows (approximately)
DELETE FROM `thn_akademik`;
INSERT INTO `thn_akademik` (`id_ta`, `tahun_akademik`, `mulai`, `selesai`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(1, '2020/2021', '2020-07-16', '2021-07-15', '2024-11-14 13:26:07', 1, '2024-11-14 13:26:45', 1, 1),
	(2, '2021/2022', '2021-07-16', '2022-07-15', '2024-11-14 13:26:38', 1, '2024-11-14 13:26:38', NULL, 1),
	(3, '2022/2023', '2022-07-16', '2023-07-15', '2024-11-14 13:27:53', 1, '2024-11-14 13:27:53', NULL, 1),
	(4, '2023/2024', '2023-07-16', '2024-12-31', '2024-11-14 13:28:45', 1, '2024-12-23 00:22:11', NULL, 1),
	(5, '2024/2025', '2024-07-16', '2025-07-15', '2024-11-14 13:29:13', 1, '2024-11-14 13:29:13', NULL, 1);

-- Dumping structure for table monitoring_siswa.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1:superadmin 2:admin prodi 3:guru 4:instruktur 5:siswa',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_username_unique` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table monitoring_siswa.users: ~12 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
	(1, 'superadmin', '$2y$12$rHd6icUDOJm.QG2IxJQQn.g7rjV5Ct2Ac.w.s/J7TRGUH6We5bMJS', '3', '2024-09-08 03:35:12', NULL, '2024-12-02 03:46:03', NULL, 1),
	(17, '1334121212', '$2y$12$8WejJ2MIvCFdyOAqArgtzeIkabpMtv8OKhuraFyCVfYH643xKibrW', '5', '2024-10-29 14:27:39', NULL, '2024-11-11 14:30:31', NULL, 1),
	(18, '1334121999', '$2y$12$.V3I2Ak.OtjBgFfDdIi/IOh8GjV4jWomFcbmkCJV0UWMYtAtisJTe', '5', '2024-10-29 14:31:45', NULL, '2024-10-29 14:31:45', NULL, 1),
	(19, '121211212121212', '$2y$12$AO8CCZHqVlY1MSHr0cqrMuT2bWobKU8IO7JHLG/WsHbXBGu9BRYvq', '3', '2024-10-29 14:39:38', NULL, '2024-11-12 23:36:02', NULL, 1),
	(20, '121211212121222', '$2y$12$6NCI1FDVPkg406e0ym4I6ewev36cclDS/GbLir6CH2qjMdZ0wLwuO', '3', '2024-10-29 14:40:03', NULL, '2024-10-29 14:40:03', NULL, 1),
	(21, '353533534535345', '$2y$12$FkEJv2kZ3H8zgDgn8CW5huGrRpkF7r4mfZ16tkpZwEq0ShFPEfXgO', '4', '2024-10-29 14:42:20', NULL, '2024-10-29 14:42:20', NULL, 1),
	(22, '423424423423432', '$2y$12$Wj8fBuTwfSXMpkKMyC7NZO3eZK2vkUQp8NjMoyuAdIkWW42yNB9tm', '4', '2024-11-12 11:32:42', NULL, '2024-11-12 11:32:42', NULL, 1),
	(23, '1332222222', '$2y$12$C2tWQQrKRpx0pxNAsMg1tutXQ8s0OA5KwDAYmHC89qVUvyxPeQ7NS', '5', '2024-11-14 12:48:19', NULL, '2024-11-14 12:48:19', NULL, 1),
	(24, '111111111111111', '$2y$12$2ziPHAFE8LL252DhGoFFp.p9URfCImhYsC/noloPsNnzp291nP.aW', '4', '2024-11-14 13:34:36', NULL, '2024-11-14 13:34:36', NULL, 1),
	(25, '222222222222222', '$2y$12$5oKwVYp3MR8SjvS.czH0m.jJdtUMzLPLqjIaD7Xmvtkq63/ier9eO', '4', '2024-11-14 13:35:45', NULL, '2024-11-14 13:35:45', NULL, 1),
	(26, '999999999999999', '$2y$12$hsReWUQtG7NPKA2R.P0N5eSz5iy5iNLdZnX5xXGuXuOct/K.mUMJa', '1', '2024-11-14 13:37:49', NULL, '2024-12-02 03:54:33', NULL, 1),
	(27, '5555555555', '$2y$12$IDgO4lpCRFczrFRPf3d66eZearvTF0aepLiA9CgaNMfXSR4sUuZBi', '5', '2024-11-14 13:40:05', NULL, '2024-11-19 09:30:53', NULL, 1),
	(28, '8888888888', '$2y$12$CnRhPmOfnX9V/8gelTyme.XDEnZph/t9NcvXFqORJxWLXwjaECM3q', '5', '2024-11-14 14:21:46', NULL, '2024-11-14 14:21:46', NULL, 1),
	(29, '444444444444444', '$2y$12$kocTLDP.yicwcu7xxIdutee11BamlJTfLUV1poFfBJaksqPu1xMZa', '3', '2024-11-14 21:35:04', NULL, '2024-12-02 03:54:33', NULL, 1),
	(30, '7777777777', '$2y$12$prDyDZiAkraGU8DikGVdium/6sOMiApwHEdrBsjuKAcCk1N1xiwjm', '5', '2024-11-19 10:44:49', NULL, '2024-11-19 10:44:49', NULL, 1),
	(31, '111199999999999', '$2y$12$V6UiW/7cQMyn7HWLHelIVuIHfD3Zu4jzepBF19pZvVo0jNTxS0ySO', '3', '2024-12-02 00:14:24', NULL, '2025-01-18 08:35:14', NULL, 1),
	(32, '333322222222222', '$2y$12$q8xClRLP4jba1qz2vEbroeVU8jt0pddEq0jnp2AZyxw9l/oUGb1R.', '4', '2024-12-02 00:14:56', NULL, '2024-12-02 00:14:56', NULL, 1),
	(33, '7777777778', '$2y$12$TCRxvyOJBJeZfYYVNy1R8eg/Ge7tdlssONm5keGkE.BVL4eCael.i', '5', '2024-12-02 08:52:34', NULL, '2024-12-02 08:52:34', NULL, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
