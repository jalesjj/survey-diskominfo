-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 05, 2026 at 07:54 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `survei_diskominfo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `role`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 'super_admin', '2026-06-05 00:17:45', '2025-09-07 19:47:24', '2026-06-05 00:17:45'),
(3, 'jales', '$2y$12$7aGtva/uX62asyOiZuyy..Md14iieJxZqZE5oJ4dCbjDCdSoig7yK', 'jales', 'admin', NULL, '2025-11-30 01:33:16', '2025-11-30 01:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `type`, `name`, `file_path`, `original_name`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(23, 'logo', '1775787625_logo-kementerian-komunikasi-dan-informatika-kemkominfo-indonesia-png-2160p-filevector69.png', 'assets/1775787625_logo-kementerian-komunikasi-dan-informatika-kemkominfo-indonesia-png-2160p-filevector69.png', 'Logo Kementerian Komunikasi dan Informatika (Kemkominfo) Indonesia (PNG-2160p) - FileVector69.png', 1, NULL, '2026-04-09 19:20:27', '2026-04-17 01:11:38'),
(24, 'logo', '1775787852_250797.png', 'assets/1775787852_250797.png', '250797.png', 1, NULL, '2026-04-09 19:24:12', '2026-06-04 09:09:28'),
(25, 'logo', '1775788028_dinarpustaka-20240806132405-66b1c18581cc5.png', 'assets/1775788028_dinarpustaka-20240806132405-66b1c18581cc5.png', 'dinarpustaka-20240806132405-66b1c18581cc5.png', 0, NULL, '2026-04-09 19:27:08', '2026-04-17 01:11:29');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` bigint UNSIGNED NOT NULL,
  `department_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dinas Komunikasi dan Informatika',
  `regency_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kabupaten Lamongan',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Jl. Basuki Rahmat No. 1, Lamongan',
  `province` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Jawa Timur 62211',
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '+628113021708',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diskominfo@lamongankab.go.id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `department_name`, `regency_name`, `address`, `province`, `whatsapp`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Dinas Komunikasi dan Informati', 'Kabupaten Lamongan', 'Jl. Basuki Rahmat No. 1, Lamonga', 'Jawa Timur 62211', '+62 811 302 1708', 'diskominfo@lamongankab.go.id', '2025-09-07 19:47:24', '2026-06-04 09:11:59'),
(4, 'Dinas Komunikasi dan Informatika', 'Kabupaten Lamongan', 'Jl. Basuki Rahmat No. 1, Lamongan', 'Jawa Timur 62211', '+628113021708', 'diskominfo@lamongankab.go.id', '2025-09-07 19:47:16', '2025-09-07 19:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `footer_links`
--

CREATE TABLE `footer_links` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('layanan','informasi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_index` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `footer_links`
--

INSERT INTO `footer_links` (`id`, `title`, `url`, `category`, `order_index`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Tentang kami', 'https://lamongankab.go.id/', 'informasi', 1, 1, '2025-09-07 19:47:24', '2025-09-07 19:47:24'),
(2, 'Website Resmi', 'https://lamongankab.go.id/', 'layanan', 1, 1, '2025-09-07 19:47:24', '2025-09-07 19:47:24'),
(3, 'Portal Data', 'https://lamongankab.go.id/', 'layanan', 2, 1, '2025-09-07 19:47:24', '2025-09-07 19:47:24'),
(4, 'Aplikasi Mobile', 'https://lamongankab.go.id/', 'layanan', 3, 1, '2025-09-07 19:47:24', '2025-09-07 19:47:24'),
(5, 'Helpdesk', 'https://laporpakyes.lamongankab.go.id/', 'layanan', 4, 1, '2025-09-07 19:47:24', '2025-09-07 19:47:24'),
(6, 'Kebijakan Privasi', 'https://lamongankab.go.id/', 'informasi', 2, 1, '2025-09-07 19:47:24', '2025-09-07 19:47:24');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_21_045023_create_surveys_table', 1),
(5, '2025_08_21_085158_create_admin_users_table', 2),
(6, '2025_08_25_033432_create_survey_sections_table', 3),
(7, '2025_08_25_033512_create_survey_questions_table', 3),
(8, '2025_08_25_033717_create_survey_responses_table', 3),
(9, '2025_08_26_013950_update_surveys_table', 4),
(10, '2025_08_26_014419_remove_unused_fields_from_surveys_table', 4),
(11, '2025_08_30_170645_add_role_to_admin_users_table', 5),
(12, '2025_08_31_083014_create_assets_table', 6),
(13, '2025_09_03_113520_add_question_description_to_survey_questions_table', 7),
(14, '2025_09_04_115203_create_footer_links_table', 8),
(15, '2025_09_07_125605_create_contact_info_table', 9),
(16, '2026_02_25_154831_create_criteria_table', 10),
(17, '2026_02_25_154929_add_saw_fields_to_survey_questions_table', 10),
(18, '2026_02_25_155305_create_saw_settings_table', 10),
(19, '2026_02_25_165634_add_saw_fields_to_survey_questions_table', 11),
(20, '2026_03_04_131403_update_assets_table_structure', 12),
(21, '2026_04_24_115750_create_survey_periods_table', 13),
(22, '2026_04_24_115821_add_period_id_to_survey_responses_table', 13),
(23, '2026_04_24_120009_create_saw_calculation_results_table', 13),
(24, '2026_05_04_140103_modify_question_id_to_string_in_survey_responses', 14);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saw_calculation_results`
--

CREATE TABLE `saw_calculation_results` (
  `id` bigint UNSIGNED NOT NULL,
  `survey_id` bigint UNSIGNED DEFAULT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `criteria_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `criteria_type` enum('benefit','cost') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'benefit',
  `criteria_weight` decimal(5,2) NOT NULL DEFAULT '0.00',
  `average_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `normalized_score` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `weight_normalized` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `weighted_score` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `total_responses` int NOT NULL DEFAULT '0',
  `questions_count` int NOT NULL DEFAULT '0',
  `calculated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7LoaJz9mgkLvugHOyDBv1i3AWpmNxZGQsZQv6PrK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMW9pVkZNWWtFS3BvVVBWYTlBMkRpMkpkWGVoM2RETVhyWXY3ZjJNVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9xdWVzdGlvbnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjg6ImFkbWluX2lkIjtpOjE7czoxMDoiYWRtaW5fbmFtZSI7czoxOToiU3VwZXIgQWRtaW5pc3RyYXRvciI7czoxMDoiYWRtaW5fcm9sZSI7czoxMToic3VwZXJfYWRtaW4iO30=', 1780646041);

-- --------------------------------------------------------

--
-- Table structure for table `surveys`
--

CREATE TABLE `surveys` (
  `id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surveys`
--

INSERT INTO `surveys` (`id`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(111, '192.168.1.111', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(112, '192.168.1.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(113, '192.168.1.113', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(114, '192.168.1.114', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(115, '192.168.1.115', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(116, '192.168.1.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(117, '192.168.1.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(118, '192.168.1.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(119, '192.168.1.119', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(120, '192.168.1.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(121, '192.168.1.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(122, '192.168.1.122', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(123, '192.168.1.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(124, '192.168.1.124', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(125, '192.168.1.125', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(126, '192.168.1.126', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(127, '192.168.1.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(128, '192.168.1.128', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(129, '192.168.1.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(130, '192.168.1.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(131, '192.168.1.131', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(132, '192.168.1.132', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(133, '192.168.1.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(134, '192.168.1.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(135, '192.168.1.135', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(136, '192.168.1.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(137, '192.168.1.137', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(138, '192.168.1.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(139, '192.168.1.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(140, '192.168.1.140', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Dumy Responden/1.0', '2026-06-05 00:00:00', '2026-06-05 00:00:00'),
(141, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 23:58:11', '2026-06-04 23:58:11'),
(142, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-05 00:00:06', '2026-06-05 00:00:06'),
(143, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-05 00:08:19', '2026-06-05 00:08:19'),
(144, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-05 00:16:05', '2026-06-05 00:16:05'),
(145, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-05 00:17:37', '2026-06-05 00:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `survey_periods`
--

CREATE TABLE `survey_periods` (
  `id` bigint UNSIGNED NOT NULL,
  `survey_id` bigint UNSIGNED NOT NULL,
  `period_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('draft','active','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_periods`
--

INSERT INTO `survey_periods` (`id`, `survey_id`, `period_name`, `year`, `start_date`, `end_date`, `status`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(21, 1, 'SKM 2026', 2026, '2026-06-05', '2027-06-05', 'active', 1, NULL, '2026-06-05 00:53:56', '2026-06-05 00:53:56');

-- --------------------------------------------------------

--
-- Table structure for table `survey_questions`
--

CREATE TABLE `survey_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `question_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_description` text COLLATE utf8mb4_unicode_ci,
  `question_type` enum('short_text','long_text','multiple_choice','checkbox','dropdown','file_upload','linear_scale') COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `order_index` int NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `enable_saw` tinyint(1) NOT NULL DEFAULT '0',
  `criteria_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criteria_weight` decimal(5,3) DEFAULT NULL,
  `criteria_type` enum('benefit','cost') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`id`, `section_id`, `question_text`, `question_description`, `question_type`, `options`, `settings`, `order_index`, `is_required`, `is_active`, `enable_saw`, `criteria_name`, `criteria_weight`, `criteria_type`, `created_at`, `updated_at`) VALUES
(83, 27, 'Apa jenis layanan utama yang sering digunakan ?', NULL, 'multiple_choice', '[\"Internet  dan sarana prasarananya\", \"Layanan informasi publik (PPID, SP4N-LAPOR!)\", \"Komunikasi publik dan kehumasan\", \"statistik\"]', '[]', 1, 1, 1, 0, NULL, NULL, NULL, '2026-04-13 01:14:13', '2026-04-13 01:14:13'),
(84, 27, 'Apakah persyaratan untuk mendapatkan pelayanan mudah dipenuhi/disiapkan?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat mudah\", \"scale_min_label\": \"sangat sulit\"}', 2, 1, 1, 1, 'Persyaratan', '3.000', 'benefit', '2026-04-13 01:19:28', '2026-04-13 01:19:28'),
(85, 27, 'Apakah prosedur pelayanan mudah dan sederhana ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat mudah\", \"scale_min_label\": \"sangat sulit\"}', 3, 1, 1, 1, 'Sistem, Mekanisme, dan Prosedur', '4.000', 'benefit', '2026-04-13 01:21:16', '2026-04-13 01:21:16'),
(86, 27, 'Apakah jam buka pelayanan sudah tepat waktu ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat tepat waktu\", \"scale_min_label\": \"tidak tepat waktu\"}', 4, 1, 1, 1, 'Waktu Pelayanan', '6.000', 'benefit', '2026-04-13 01:22:29', '2026-04-13 01:22:29'),
(87, 27, 'Apakah Waktu pelaksanaan layanan sudah sesuai dengan standar yang ditetapkan ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat sesuai standar\", \"scale_min_label\": \"tidak sesuai standar\"}', 5, 1, 1, 1, 'Waktu Pelayanan', '6.000', 'benefit', '2026-04-13 01:24:33', '2026-04-13 01:24:33'),
(88, 27, 'Menurut saudara apakah biaya/tarid pelayanan wajar dan terjangkau?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"4\", \"scale_min\": \"1\", \"scale_max_label\": \"gratis\", \"scale_min_label\": \"sangat mahal\"}', 6, 1, 1, 1, 'Biaya/Tarif', '7.000', 'cost', '2026-04-13 01:26:37', '2026-06-05 00:53:10'),
(89, 27, 'Menurut saudara aakah setiap pengguna mendapatkan data/ informasi sesuai dengan yang diminta ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat sesuai\", \"scale_min_label\": \"tidak sesuai\"}', 7, 1, 1, 1, 'Produk Spesifikasi Jenis Pelayanan', '5.000', 'benefit', '2026-04-13 01:29:32', '2026-04-13 01:29:32'),
(90, 27, 'Menurut saudara apakah website dan media sosial dinas komunikasi dan informatika Kab lamongan memberikan informasi yang jelas, lengkap, aktual dan mudah dimengerti ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat jelas\", \"scale_min_label\": \"tidak jelas\"}', 8, 1, 1, 1, 'Produk Spesifikasi Jenis Pelayanan', '5.000', 'benefit', '2026-04-13 01:32:43', '2026-04-13 01:34:14'),
(91, 27, 'Bagaimana pendapat saudara tentang kualitas live streaming di youtube dinas komunikasi dan informatika kab lamongan', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat baik\", \"scale_min_label\": \"buruk\"}', 9, 1, 1, 1, 'Produk Spesifikasi Jenis Pelayanan', '5.000', 'benefit', '2026-04-13 01:34:04', '2026-04-13 01:34:04'),
(92, 27, 'Bagaimana pendapat saudara tentang kemudahan akses publikasi statistik pada website lamongankab.go.id', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat mudah\", \"scale_min_label\": \"sangat sulit\"}', 10, 1, 1, 1, 'Produk Spesifikasi Jenis Pelayanan', '5.000', 'benefit', '2026-04-13 01:36:11', '2026-04-13 01:36:11'),
(93, 27, 'Menurut saudara apakah petugas bekerja dengan terampil, cekatan dan profesional ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat baik\", \"scale_min_label\": \"sangat buruk\"}', 11, 1, 1, 1, 'Kompetensi Pelaksana', '9.000', 'benefit', '2026-04-13 01:37:43', '2026-04-13 01:37:43'),
(94, 27, 'Menurut saudara apakah perilaku petugas pelayanan komunikatif dengan menggunakan bahasa yang mudah dipahami dalam menyampaikan informasi ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat komunikatif\", \"scale_min_label\": \"tidak komunikatif\"}', 12, 1, 1, 1, 'Perilaku Pelaksana', '7.000', 'benefit', '2026-04-13 01:39:38', '2026-04-13 01:39:38'),
(95, 27, 'Menurut saudara apakah perilaku petugas pelayanan sopan, ramah dan menghormati dalam memberikan pelayanan ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat sopan dan ramah\", \"scale_min_label\": \"tidak sopan, dan ramah\"}', 13, 1, 1, 1, 'Perilaku Pelaksana', '7.000', 'benefit', '2026-04-13 01:46:03', '2026-04-13 01:46:03'),
(96, 27, 'Apakah saudara memahami alur pengaduan dengan baik ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat memahami\", \"scale_min_label\": \"tidak memahami\"}', 14, 1, 1, 1, 'penanganan Pengaduan, Saran dan Masukan', '3.000', 'benefit', '2026-04-13 01:49:16', '2026-04-13 01:49:16'),
(97, 27, 'apakah petugas pelayanan cepat tanggap dalam menyelesaikan keluhan ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat tanggap\", \"scale_min_label\": \"tidak tanggap\"}', 15, 1, 1, 1, 'penanganan Pengaduan, Saran dan Masukan', '3.000', 'benefit', '2026-04-13 01:58:48', '2026-04-13 01:58:48'),
(98, 27, 'apakah saran dan masukan segera ditindaklanjuti oleh petugas ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat cepat\", \"scale_min_label\": \"tidak cepat\"}', 16, 1, 1, 1, 'penanganan Pengaduan, Saran dan Masukan', '3.000', 'benefit', '2026-04-13 02:00:03', '2026-04-13 02:00:03'),
(99, 27, 'Menurut saudara apakah pengunjung memahami alur pengaduan dengan baik ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat paham\", \"scale_min_label\": \"tidak paham\"}', 17, 1, 1, 1, 'penanganan Pengaduan, Saran dan Masukan', '3.000', 'benefit', '2026-04-13 02:04:29', '2026-04-13 02:04:29'),
(100, 27, 'Menurut saudara apakah penataan eksterior dan interior ruangan pelayanan sudah baik ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat baik\", \"scale_min_label\": \"sangat kurang\"}', 18, 1, 1, 1, 'Sarana dan Prasarana', '2.000', 'benefit', '2026-04-13 02:06:16', '2026-04-13 02:06:42'),
(101, 27, 'Menurut saudara apakah keamanan, kebersihan, kerapian dan kenyamanan ruang pelayanan sudah baik ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat baik\", \"scale_min_label\": \"buruk\"}', 19, 1, 1, 1, 'Sarana dan Prasarana', '2.000', 'benefit', '2026-04-13 02:08:30', '2026-04-13 02:08:30'),
(102, 27, 'menurut saudara apakah kelengakapan dan kesiapan alat yang akan dipakai untuk pelayanan sudah sesuai ?', NULL, 'linear_scale', NULL, '{\"scale_max\": \"5\", \"scale_min\": \"1\", \"scale_max_label\": \"sangat sesuai\", \"scale_min_label\": \"tidak sesuai\"}', 20, 1, 1, 1, 'Sarana dan Prasarana', '2.000', 'benefit', '2026-04-13 02:11:34', '2026-04-13 02:11:34'),
(103, 28, 'mohon masukan dan saran guna perbaikan kedepannya agar pekayanan kamu lebih baik lagi', NULL, 'long_text', NULL, '[]', 1, 0, 1, 0, NULL, NULL, NULL, '2026-04-13 02:12:46', '2026-04-13 02:12:46');

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` bigint UNSIGNED NOT NULL,
  `survey_id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED DEFAULT NULL,
  `question_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `answer_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `survey_sections`
--

CREATE TABLE `survey_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order_index` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_sections`
--

INSERT INTO `survey_sections` (`id`, `title`, `description`, `order_index`, `is_active`, `created_at`, `updated_at`) VALUES
(27, 'Penilaian Kualitas Layanan Publik', 'Kuesioner utama evaluasi pelayanan pada Diskominfo', 2, 1, '2026-06-05 06:32:03', '2026-06-05 06:32:03'),
(28, 'Saran dan Masukan', 'Kritik dan saran terbuka untuk perbaikan layanan', 3, 1, '2026-06-05 06:32:03', '2026-06-05 06:32:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_username_unique` (`username`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `footer_links`
--
ALTER TABLE `footer_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `saw_calculation_results`
--
ALTER TABLE `saw_calculation_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `saw_calculation_results_period_id_criteria_name_unique` (`period_id`,`criteria_name`),
  ADD KEY `saw_calculation_results_period_id_criteria_name_index` (`period_id`,`criteria_name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `surveys`
--
ALTER TABLE `surveys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `survey_periods`
--
ALTER TABLE `survey_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_periods_survey_id_status_index` (`survey_id`,`status`),
  ADD KEY `survey_periods_year_index` (`year`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_questions_section_id_foreign` (`section_id`),
  ADD KEY `survey_questions_enable_saw_criteria_name_index` (`enable_saw`,`criteria_name`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_responses_survey_id_foreign` (`survey_id`),
  ADD KEY `survey_responses_period_id_index` (`period_id`);

--
-- Indexes for table `survey_sections`
--
ALTER TABLE `survey_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `footer_links`
--
ALTER TABLE `footer_links`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `saw_calculation_results`
--
ALTER TABLE `saw_calculation_results`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `surveys`
--
ALTER TABLE `surveys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `survey_periods`
--
ALTER TABLE `survey_periods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2354;

--
-- AUTO_INCREMENT for table `survey_sections`
--
ALTER TABLE `survey_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `saw_calculation_results`
--
ALTER TABLE `saw_calculation_results`
  ADD CONSTRAINT `saw_calculation_results_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `survey_periods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD CONSTRAINT `survey_questions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `survey_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD CONSTRAINT `survey_responses_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `survey_periods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `survey_responses_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
