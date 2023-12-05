-- -------------------------------------------------------------
-- TablePlus 5.6.6(520)
--
-- https://tableplus.com/
--
-- Database: rio
-- Generation Time: 2023-12-05 18:59:32.4630
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `detail_transactions`;
CREATE TABLE `detail_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) unsigned NOT NULL,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `qty` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('open','close') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `scanned` int(11) NOT NULL DEFAULT '0',
  `gate` int(11) DEFAULT NULL,
  `is_print` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `histories`;
CREATE TABLE `histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `gate` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `histories_member_id_foreign` (`member_id`),
  CONSTRAINT `histories_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `history_penyewaans`;
CREATE TABLE `history_penyewaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `penyewaan_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `history_penyewaans_member_id_foreign` (`member_id`),
  KEY `history_penyewaans_penyewaan_id_foreign` (`penyewaan_id`),
  CONSTRAINT `history_penyewaans_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  CONSTRAINT `history_penyewaans_penyewaan_id_foreign` FOREIGN KEY (`penyewaan_id`) REFERENCES `penyewaans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `jenis_tickets`;
CREATE TABLE `jenis_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rfid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_ktp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_lahir` date NOT NULL,
  `tgl_register` date NOT NULL,
  `tgl_expired` date NOT NULL,
  `saldo` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `members_rfid_unique` (`rfid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `penyewaans`;
CREATE TABLE `penyewaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sewa_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT '1',
  `metode` enum('Tap','Cash') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penyewaans_sewa_id_foreign` (`sewa_id`),
  KEY `penyewaans_user_id_foreign` (`user_id`),
  CONSTRAINT `penyewaans_sewa_id_foreign` FOREIGN KEY (`sewa_id`) REFERENCES `sewa` (`id`),
  CONSTRAINT `penyewaans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ucapan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `use_logo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `sewa`;
CREATE TABLE `sewa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` int(11) NOT NULL,
  `device` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `terusan_ticket`;
CREATE TABLE `terusan_ticket` (
  `terusan_id` bigint(20) unsigned NOT NULL,
  `ticket_id` bigint(20) unsigned NOT NULL,
  KEY `terusan_ticket_terusan_id_foreign` (`terusan_id`),
  KEY `terusan_ticket_ticket_id_foreign` (`ticket_id`),
  CONSTRAINT `terusan_ticket_terusan_id_foreign` FOREIGN KEY (`terusan_id`) REFERENCES `terusans` (`id`),
  CONSTRAINT `terusan_ticket_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `terusans`;
CREATE TABLE `terusans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tripod` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `terusans_tripod_unique` (`tripod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `jenis_ticket_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` int(11) NOT NULL,
  `tripod` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_jenis_ticket_id_foreign` (`jenis_ticket_id`),
  CONSTRAINT `tickets_jenis_ticket_id_foreign` FOREIGN KEY (`jenis_ticket_id`) REFERENCES `jenis_tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `topups`;
CREATE TABLE `topups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `jumlah` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topups_member_id_foreign` (`member_id`),
  CONSTRAINT `topups_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL,
  `no_trx` int(11) NOT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('group','individual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'group',
  `amount` int(11) NOT NULL DEFAULT '0',
  `disc` int(11) NOT NULL DEFAULT '0',
  `metode` enum('cash','debit','kredit','qris') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` int(11) NOT NULL DEFAULT '0',
  `amount_scanned` int(11) NOT NULL DEFAULT '0',
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `gate` int(11) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_print` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

INSERT INTO `jenis_tickets` (`id`, `nama_jenis`, `created_at`, `updated_at`) VALUES
(1, 'Reguler', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(2, 'Terusan', '2023-12-05 18:58:32', '2023-12-05 18:58:32');

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_02_16_091237_create_jenis_tickets_table', 1),
(6, '2023_02_16_091239_create_tickets_table', 1),
(7, '2023_02_16_091410_create_transactions_table', 1),
(8, '2023_04_06_022411_create_members_table', 1),
(9, '2023_04_06_030149_create_histories_table', 1),
(10, '2023_04_07_022739_create_sewas_table', 1),
(11, '2023_04_07_024121_create_penyewaans_table', 1),
(12, '2023_04_07_024401_create_history_penyewaans_table', 1),
(13, '2023_04_07_064151_create_permission_tables', 1),
(14, '2023_04_11_073712_create_terusans_table', 1),
(15, '2023_04_12_023911_create_detail_transactions_table', 1),
(16, '2023_04_13_065403_create_terusan_ticket_table', 1),
(17, '2023_04_15_030043_create_topups_table', 1),
(18, '2023_04_27_080534_add_user_id_to_penyewaans_table', 1),
(19, '2023_04_27_085330_add_no_hp_to_members_table', 1),
(20, '2023_04_27_101521_add_discount_to_transactions_table', 1),
(21, '2023_05_03_083958_add_metode_to_transactions_table', 1),
(22, '2023_12_03_210146_add_ticket_code_to_detail_transactions_table', 1),
(23, '2023_12_05_061401_add_is_print_to_transactions_table', 1),
(24, '2023_12_05_061824_add_is_print_to_detail_transactions_table', 1),
(25, '2023_12_05_115018_create_settings_table', 1),
(26, '2023_12_05_183504_add_use_logo_to_settings_table', 1);

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'master-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(2, 'user-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(3, 'ticket-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(4, 'sewa-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(5, 'member-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(6, 'transaction-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(7, 'penyewaan-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(8, 'topup-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(9, 'report-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(10, 'report-transaction-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(11, 'report-penyewaan-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(12, 'transaction-delete', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(13, 'penyewaan-delete', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(14, 'topup-delete', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(15, 'management-access', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32');

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1);

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2023-12-05 18:58:32', '2023-12-05 18:58:32');

INSERT INTO `sewa` (`id`, `name`, `harga`, `device`, `created_at`, `updated_at`) VALUES
(1, 'Sewa Ban', 10000, 1, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(2, 'Sewa Baju Renang', 10000, 2, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(3, 'Sewa Pelampung', 10000, 3, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(4, 'Sewa Tikar', 10000, 4, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(5, 'Tiket Balon Udara', 10000, 5, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(6, 'Tiket Adrenaline Swing', 10000, 6, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(7, 'Tiket Sepeda Terbang', 10000, 7, '2023-12-05 18:58:32', '2023-12-05 18:58:32');

INSERT INTO `tickets` (`id`, `jenis_ticket_id`, `name`, `harga`, `tripod`, `created_at`, `updated_at`) VALUES
(1, 1, 'HTM Reguler Weekday', 20000, 1, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(2, 1, 'HTM Reguler Weekend', 30000, 2, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(3, 2, 'HTM Terusan Weekday', 40000, 3, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(4, 2, 'HTM Terusan Weekend', 50000, 4, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(5, 1, 'HTM Group Character Building', 70000, 5, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(6, 1, 'HTM Olahraga Sekolah', 15000, 6, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(7, 1, 'HTM Club Renang', 15000, 7, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(8, 1, 'HTM Rainbow Slider', 35000, 8, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(9, 1, 'HTM Kereta', 25000, 9, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(10, 1, 'HTM Adrenaline Slider Air Terjun', 25000, 10, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(11, 1, 'Parkir Mobil', 5000, 11, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(12, 1, 'Parkir Motor', 2000, 12, '2023-12-05 18:58:32', '2023-12-05 18:58:32'),
(13, 1, 'Asuransi Jasa Raharja', 2000, 13, '2023-12-05 18:58:32', '2023-12-05 18:58:32');

INSERT INTO `users` (`id`, `name`, `username`, `password`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'Developer', 'developer', '$2y$10$plBPZLcd6wxfYFi0RpmTNOhjYAYY.vLPR83ww90VTqHM4eZnRLbCa', NULL, '2023-12-05 18:58:32', '2023-12-05 18:58:32');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;