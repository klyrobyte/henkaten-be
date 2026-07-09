
-- --------------------------------------------------------
-- Database updates for new features (July 2026)
-- --------------------------------------------------------

-- 1. Support NS (Non-Shift) in members table
ALTER TABLE `members` MODIFY COLUMN `shift` ENUM('A', 'B', 'NS') NOT NULL DEFAULT 'A';

-- 2. Create table member_skills
CREATE TABLE IF NOT EXISTS `member_skills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sc_id` bigint(20) unsigned NOT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  `machine_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `process_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `factory` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skill_pct` tinyint(4) NOT NULL DEFAULT '0',
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_skills_unique` (`sc_id`,`member_id`,`machine_name`,`process_name`,`factory`),
  KEY `member_skills_sc_id_factory_index` (`sc_id`,`factory`),
  KEY `member_skills_sc_id_member_id_index` (`sc_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create table factory_layouts (for Factory-based Floor Plan Manager)
CREATE TABLE IF NOT EXISTS `factory_layouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sc_id` bigint(20) unsigned NOT NULL,
  `factory` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layout_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `layout_width` int(11) DEFAULT NULL,
  `layout_height` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `factory_layouts_sc_id_factory_unique` (`sc_id`,`factory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create table machine_processes
CREATE TABLE IF NOT EXISTS `machine_processes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sc_id` int(11) NOT NULL DEFAULT '1',
  `factory` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `process_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `machine_processes_sc_id_factory_machine_name_index` (`sc_id`,`factory`,`machine_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
