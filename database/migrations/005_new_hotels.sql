-- Worldwide hotel directory (country > city > hotel). Rows are loaded from
-- database/stuba-hotel-directory.csv by database/new-hotels-import.php (run by deploy-migrate.php).
-- Safe to re-run.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `new_hotels` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `country` VARCHAR(100) NOT NULL,
  `city` VARCHAR(150) NOT NULL,
  `hotel_name` VARCHAR(255) NOT NULL,
  KEY `idx_new_hotels_country_city` (`country`, `city`),
  KEY `idx_new_hotels_hotel` (`country`, `city`, `hotel_name`(80))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
