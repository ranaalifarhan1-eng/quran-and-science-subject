-- Quran & Science — Migration 004: Testimonials Management System
-- Additive, idempotent migration for live cPanel MySQL / MariaDB databases.
-- Creates the `testimonials` table without altering existing data.

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reviewer_name` VARCHAR(100) NOT NULL,
  `review_text` TEXT NOT NULL,
  `location` VARCHAR(100) NULL,
  `category` VARCHAR(100) NULL,
  `image` VARCHAR(255) NULL,
  `display_order` INT NOT NULL DEFAULT 1,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_testimonials_status_order` (`status`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
