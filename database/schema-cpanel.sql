-- =====================================================================
-- Club Yatri - Relational Database Schema
-- Compatible with MySQL 5.7+ / MariaDB / XAMPP phpMyAdmin
-- Legal Entity: Club Yatri Hotel and Resort Pvt Ltd
-- =====================================================================


-- 1. Member Accounts
CREATE TABLE IF NOT EXISTS `members` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id` VARCHAR(32) NOT NULL UNIQUE,
  `full_name` VARCHAR(128) NOT NULL,
  `email` VARCHAR(128) NOT NULL UNIQUE,
  `phone` VARCHAR(32) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `pass_tier` VARCHAR(64) NOT NULL DEFAULT 'Platinum Crest (21 Nights)',
  `points_balance` INT UNSIGNED NOT NULL DEFAULT 38000,
  `total_annual_points` INT UNSIGNED NOT NULL DEFAULT 38000,
  `nights_balance` INT UNSIGNED NOT NULL DEFAULT 21,
  `membership_year_start` DATE NOT NULL,
  `status` ENUM('active', 'suspended', 'expired') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Inquiries and Callbacks
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(32) NOT NULL DEFAULT 'pass_inquiry',
  `full_name` VARCHAR(128) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `phone` VARCHAR(32) NOT NULL,
  `city` VARCHAR(64) DEFAULT NULL,
  `pass_tier` VARCHAR(64) DEFAULT NULL,
  `preferred_time` VARCHAR(64) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `status` ENUM('new', 'contacted', 'converted', 'closed') DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Vacation Booking Requests
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_code` VARCHAR(32) NOT NULL UNIQUE,
  `member_id` VARCHAR(32) DEFAULT NULL,
  `guest_name` VARCHAR(128) NOT NULL,
  `guest_phone` VARCHAR(32) NOT NULL,
  `guest_email` VARCHAR(128) NOT NULL,
  `destination_name` VARCHAR(128) NOT NULL,
  `resort_name` VARCHAR(128) DEFAULT NULL,
  `room_type` VARCHAR(64) NOT NULL,
  `season` VARCHAR(32) DEFAULT 'Purple Studio',
  `checkin_date` DATE NOT NULL,
  `duration_nights` INT UNSIGNED NOT NULL DEFAULT 7,
  `guests_count` INT UNSIGNED NOT NULL DEFAULT 2,
  `points_deducted` INT UNSIGNED DEFAULT 0,
  `status` ENUM('requested', 'confirmed', 'completed', 'cancelled') DEFAULT 'requested',
  `special_requests` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Default Test Member
INSERT INTO `members` (`member_id`, `full_name`, `email`, `phone`, `password_hash`, `pass_tier`, `points_balance`, `total_annual_points`, `nights_balance`, `membership_year_start`)
VALUES (
  'CY-8829-PLT',
  'Arvind Kapadia',
  'arvind@kapadia.com',
  '+919820012345',
  -- Password: Yatri@2026
  '$2y$12$Vgp0g6fqwzuJ3Z5OQm6BaOF14Xx.iJ4jCVixzXfFjg03mFTaVPw06',
  'Platinum Crest (21 Nights)',
  24500,
  38000,
  14,
  '2022-01-15'
) ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 4. Hotel network (data is loaded from database/hotels.sql)
CREATE TABLE IF NOT EXISTS `hotels` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY,
  `hotel_code` VARCHAR(16) NOT NULL UNIQUE,
  `slug` VARCHAR(190) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `location_name` VARCHAR(128) NOT NULL,
  `country` VARCHAR(64) NOT NULL,
  `location_type` ENUM('DOMESTIC', 'INTERNATIONAL') NOT NULL,
  `hotel_type` VARCHAR(32) NOT NULL DEFAULT 'HOTEL',
  `category` ENUM('INTERNAL', 'ASSOCIATED') NOT NULL DEFAULT 'ASSOCIATED',
  `stay_types` VARCHAR(255) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `map_link` TEXT DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_hotels_type` (`location_type`, `is_published`),
  KEY `idx_hotels_country` (`country`),
  KEY `idx_hotels_location` (`location_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hotel_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `hotel_id` INT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  KEY `idx_hotel_images_hotel` (`hotel_id`, `sort_order`),
  CONSTRAINT `fk_hotel_images_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
