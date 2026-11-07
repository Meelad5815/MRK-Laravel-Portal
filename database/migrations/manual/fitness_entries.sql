-- MRK Fitness Dashboard: safe, additive MySQL migration for InfinityFree.
-- Run this only after selecting the Laravel database and confirming a fresh SQL export.
-- This creates one new table and records the Laravel migration; it does not drop or alter existing tables.

CREATE TABLE IF NOT EXISTS `fitness_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('workout', 'run', 'weight') NOT NULL,
  `entry_date` DATE NOT NULL,
  `title` VARCHAR(120) NULL,
  `distance_km` DECIMAL(7,2) NULL,
  `duration_minutes` SMALLINT UNSIGNED NULL,
  `weight_kg` DECIMAL(5,2) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fitness_entries_user_id_type_entry_date_index` (`user_id`, `type`, `entry_date`),
  CONSTRAINT `fitness_entries_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Register the migration only if it has not already been recorded.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT
  '2026_11_05_000001_create_fitness_entries_table',
  COALESCE((SELECT MAX(`batch`) FROM `migrations`), 0) + 1
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations`
  WHERE `migration` = '2026_11_05_000001_create_fitness_entries_table'
);
