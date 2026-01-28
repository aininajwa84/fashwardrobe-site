/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 8.0.23 : Database - smart_wardrobe
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

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

/*Data for the table `failed_jobs` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2025_12_24_173442_create_wardrobes_table',1),
(6,'2025_12_24_174059_create_recommendations_table',1),
(7,'2025_12_29_171504_create_shopping_items_table',1),
(8,'2026_01_05_171029_add_profile_image_to_users_table',2),
(9,'2026_01_28_102832_add_ai_fields_to_wardrobes_table',3);

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `recommendations` */

DROP TABLE IF EXISTS `recommendations`;

CREATE TABLE `recommendations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` enum('wardrobe','online') COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recommendations_user_id_foreign` (`user_id`),
  CONSTRAINT `recommendations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `recommendations` */

insert  into `recommendations`(`id`,`user_id`,`theme`,`source`,`data`,`created_at`,`updated_at`) values 
(31,2,'sport','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-05 19:44:07','2026-01-05 19:44:07'),
(32,2,'sport','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-05 19:45:21','2026-01-05 19:45:21'),
(33,2,'sport','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-05 19:45:28','2026-01-05 19:45:28'),
(34,2,'sport','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-05 19:46:34','2026-01-05 19:46:34'),
(35,2,'sport','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-05 19:46:39','2026-01-05 19:46:39'),
(36,2,'sport','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-05 19:47:15','2026-01-05 19:47:15'),
(37,2,'sport','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-05 19:47:20','2026-01-05 19:47:20'),
(38,2,'beach','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-06 03:45:11','2026-01-06 03:45:11'),
(39,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-06 03:45:20','2026-01-06 03:45:20'),
(40,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:53:11\\\"}\"','2026-01-06 03:53:11','2026-01-06 03:53:11'),
(41,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:53:14\\\"}\"','2026-01-06 03:53:14','2026-01-06 03:53:14'),
(42,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:55:09\\\"}\"','2026-01-06 03:55:09','2026-01-06 03:55:09'),
(43,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:55:35\\\"}\"','2026-01-06 03:55:35','2026-01-06 03:55:35'),
(44,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:56:07\\\"}\"','2026-01-06 03:56:07','2026-01-06 03:56:07'),
(45,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:56:32\\\"}\"','2026-01-06 03:56:32','2026-01-06 03:56:32'),
(46,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:56:37\\\"}\"','2026-01-06 03:56:37','2026-01-06 03:56:37'),
(47,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:56:41\\\"}\"','2026-01-06 03:56:41','2026-01-06 03:56:41'),
(48,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:56:43\\\"}\"','2026-01-06 03:56:43','2026-01-06 03:56:43'),
(49,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":2,\\\"scraped_at\\\":\\\"2026-01-06 03:56:47\\\"}\"','2026-01-06 03:56:47','2026-01-06 03:56:47'),
(50,2,'travel','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-06 04:59:28','2026-01-06 04:59:28'),
(51,2,'travel','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-06 04:59:33','2026-01-06 04:59:33'),
(52,2,'travel','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-06 05:05:23','2026-01-06 05:05:23'),
(53,2,'travel','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-06 05:05:28','2026-01-06 05:05:28'),
(54,2,'travel','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-06 05:05:58','2026-01-06 05:05:58'),
(55,2,'travel','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-06 05:06:02','2026-01-06 05:06:02'),
(56,2,'work','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-13 06:23:31','2026-01-13 06:23:31'),
(57,2,'work','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-13 06:23:40','2026-01-13 06:23:40'),
(58,2,'beach','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-13 09:52:55','2026-01-13 09:52:55'),
(59,2,'beach','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-13 09:53:08','2026-01-13 09:53:08'),
(60,3,'casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:18:27','2026-01-20 08:18:27'),
(61,3,'casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:18:37','2026-01-20 08:18:37'),
(62,3,'casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:18:38','2026-01-20 08:18:38'),
(63,3,'casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:18:44','2026-01-20 08:18:44'),
(64,3,'formal','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:26:52','2026-01-20 08:26:52'),
(65,3,'formal','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:27:01','2026-01-20 08:27:01'),
(66,3,'formal','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:32:55','2026-01-20 08:32:55'),
(67,3,'formal','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:33:01','2026-01-20 08:33:01'),
(68,3,'formal','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:35:04','2026-01-20 08:35:04'),
(69,3,'formal','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:35:11','2026-01-20 08:35:11'),
(70,2,'casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:40:25','2026-01-20 08:40:25'),
(71,2,'casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:40:31','2026-01-20 08:40:31'),
(72,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:43:03','2026-01-20 08:43:03'),
(73,2,'Casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:43:08','2026-01-20 08:43:08'),
(74,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:44:21','2026-01-20 08:44:21'),
(75,2,'Casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:44:29','2026-01-20 08:44:29'),
(76,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:44:29','2026-01-20 08:44:29'),
(77,2,'Casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:44:35','2026-01-20 08:44:35'),
(78,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:44:45','2026-01-20 08:44:45'),
(79,2,'Casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:44:52','2026-01-20 08:44:52'),
(80,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:45:10','2026-01-20 08:45:10'),
(81,2,'Casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:45:16','2026-01-20 08:45:16'),
(82,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0}\"','2026-01-20 08:47:29','2026-01-20 08:47:29'),
(83,2,'Casual','online','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"result_count\\\":0,\\\"scraped_data\\\":\\\"simulated\\\"}\"','2026-01-20 08:47:39','2026-01-20 08:47:39'),
(84,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:51:26','2026-01-20 08:51:26'),
(85,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:57:11','2026-01-20 08:57:11'),
(86,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:57:42','2026-01-20 08:57:42'),
(87,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:17','2026-01-20 08:58:17'),
(88,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:20','2026-01-20 08:58:20'),
(89,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:30','2026-01-20 08:58:30'),
(90,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:33','2026-01-20 08:58:33'),
(91,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:33','2026-01-20 08:58:33'),
(92,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:35','2026-01-20 08:58:35'),
(93,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:40','2026-01-20 08:58:40'),
(94,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:47','2026-01-20 08:58:47'),
(95,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:50','2026-01-20 08:58:50'),
(96,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 08:58:50','2026-01-20 08:58:50'),
(97,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:03:48','2026-01-20 09:03:48'),
(98,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:03:55','2026-01-20 09:03:55'),
(99,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:04:01','2026-01-20 09:04:01'),
(100,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:04:09','2026-01-20 09:04:09'),
(101,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:07:33','2026-01-20 09:07:33'),
(102,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:07:52','2026-01-20 09:07:52'),
(103,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:07:55','2026-01-20 09:07:55'),
(104,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:07:59','2026-01-20 09:07:59'),
(105,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:08:03','2026-01-20 09:08:03'),
(106,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:08:04','2026-01-20 09:08:04'),
(107,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:08:09','2026-01-20 09:08:09'),
(108,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:10:56','2026-01-20 09:10:56'),
(109,2,'Casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:11:05','2026-01-20 09:11:05'),
(110,2,'casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:16:41','2026-01-20 09:16:41'),
(111,2,'casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-20 09:56:32','2026-01-20 09:56:32'),
(112,2,'casual','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"casual\\\",\\\"casual day out\\\",\\\"weekend\\\",\\\"outdoor\\\"]}\"','2026-01-27 16:19:44','2026-01-27 16:19:44'),
(113,2,'formal','wardrobe','\"{\\\"color\\\":null,\\\"category\\\":null,\\\"preferences\\\":null,\\\"result_count\\\":0,\\\"total_wardrobe_count\\\":5,\\\"search_terms_used\\\":[\\\"formal\\\",\\\"formal event\\\",\\\"office\\\",\\\"work\\\",\\\"business\\\"]}\"','2026-01-27 16:55:54','2026-01-27 16:55:54');

/*Table structure for table `shopping_items` */

DROP TABLE IF EXISTS `shopping_items`;

CREATE TABLE `shopping_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` text COLLATE utf8mb4_unicode_ci,
  `status` enum('wishlist','in_cart','purchased','removed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wishlist',
  `price_alert` tinyint(1) NOT NULL DEFAULT '0',
  `purchased_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shopping_items_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `shopping_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `shopping_items` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme_preferences` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`full_name`,`email`,`phone`,`profile_image`,`email_verified_at`,`password`,`theme_preferences`,`remember_token`,`created_at`,`updated_at`) values 
(2,'aininajwa411686','Aini Najwa','aininajwa411@gmail.com',NULL,NULL,NULL,'$2y$12$JRms/6KpyfJjiV6VpjSdjO6pgmH.o3xkuCFNq0K0/ege0buYgdRTq','\"[\\\"Casual\\\",\\\"Minimalist\\\"]\"',NULL,'2026-01-05 19:42:56','2026-01-05 19:42:56'),
(3,'najwa21487','Najwa','najwa21@gmail.com','01234567891',NULL,NULL,'$2y$12$9VVN9CidQO0kX3gl9uC9eOG6tuOlojZRwrGmR/yRfFPOvHA4zfKFS','\"[\\\"Casual\\\",\\\"Minimalist\\\"]\"',NULL,'2026-01-20 08:12:29','2026-01-20 08:13:13');

/*Table structure for table `wardrobes` */

DROP TABLE IF EXISTS `wardrobes`;

CREATE TABLE `wardrobes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occasion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `ai_analysis` json DEFAULT NULL,
  `ai_detected` tinyint(1) NOT NULL DEFAULT '0',
  `ai_confidence` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wardrobes_user_id_foreign` (`user_id`),
  CONSTRAINT `wardrobes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `wardrobes` */

insert  into `wardrobes`(`id`,`user_id`,`name`,`category`,`color`,`theme`,`occasion`,`season`,`image`,`notes`,`ai_analysis`,`ai_detected`,`ai_confidence`,`created_at`,`updated_at`) values 
(3,2,'Baju Kurung Moden','dress','red','wedding',NULL,'all','wardrobe/yxGUitMNfL00io3N2CN4qLcmfHtDONRsVt8pTAfu.jpg',NULL,NULL,0,NULL,'2026-01-05 19:43:22','2026-01-27 17:41:19'),
(4,2,'Midi Dress','dress','blue',NULL,NULL,'all','wardrobe/60znWMJkevyN6E6nL0AWqY1uvXkgkHBVUCt8QWlf.jpg','Short sleeve',NULL,0,NULL,'2026-01-06 03:43:51','2026-01-06 03:44:10'),
(5,3,'Dress','dress','brown','Casual',NULL,'all','wardrobe/mEQL1XG5wfD1iapbaPrHt8AlkIERB5UODVu1xGJE.jpg',NULL,NULL,0,NULL,'2026-01-20 08:18:16','2026-01-20 08:18:16'),
(6,2,'Wedges','shoes','black','casual',NULL,'all','wardrobe/0YMsZ08slzBpQnC9It8misgBFLyRE2onKi0D8RAr.jpg',NULL,NULL,0,NULL,'2026-01-20 08:36:43','2026-01-20 09:07:28'),
(7,2,'Blouse','top','white','casual',NULL,'all','wardrobe/t1nh5K4OBkxWP53ceL8LA4kDHsPIc7TbX8qqGIi5.jpg',NULL,NULL,0,NULL,'2026-01-20 08:37:08','2026-01-20 09:11:45'),
(8,2,'Skirt','bottom','brown','casual',NULL,'all','wardrobe/d4tMZE1A8eWDUCzYr6qIxd3HeSKAFDrJjAp2IBdf.jpg',NULL,NULL,0,NULL,'2026-01-20 08:47:20','2026-01-27 16:52:26'),
(11,2,'Red Dress','dress','red','formal',NULL,'all','wardrobe/2026/01/qclQor98M4N0mpJqPBEBppUGkLe0aTCbT5erpkLY.jpg','Auto-detected by Google Vision AI. Confidence: 95%',NULL,0,NULL,'2026-01-28 12:07:21','2026-01-28 12:07:21');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
