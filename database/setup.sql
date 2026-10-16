-- Quran & Science — Online Learning Academy
-- Database Setup Schema for cPanel / MySQL / MariaDB
-- Character set: utf8mb4, Collation: utf8mb4_unicode_ci

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `teachers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `role` VARCHAR(140) NOT NULL,
  `category` VARCHAR(80) NOT NULL DEFAULT 'Quran Learning',
  `gender` ENUM('male', 'female', 'unspecified') NOT NULL DEFAULT 'unspecified',
  `short_description` TEXT NOT NULL,
  `specialties` VARCHAR(255) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 1,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: No default teacher profiles are seeded.
-- Teacher profiles should be added securely via the Admin Panel at /admin/ once verified.

CREATE TABLE IF NOT EXISTS `site_settings` (
  `setting_key` VARCHAR(50) NOT NULL PRIMARY KEY,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`) VALUES
  ('phone_us', '+1 406 497 0497'),
  ('phone_us_visible', '1'),
  ('phone_pk', '+92 344 5455154'),
  ('phone_pk_visible', '1'),
  ('contact_email', 'onlinelearningacademy03@gmail.com'),
  ('contact_email_visible', '1'),
  ('whatsapp_number', '+92 344 5455154'),
  ('whatsapp_visible', '1'),
  ('whatsapp_message', 'Hello, I would like to know more about Quran & Science online learning programs.');

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

-- 8 Inactive Testimonial Slots (Inactive by default; edit via Admin Panel at /admin/testimonials.php)
INSERT IGNORE INTO `testimonials` 
  (`id`, `reviewer_name`, `review_text`, `location`, `category`, `image`, `display_order`, `status`, `created_at`, `updated_at`)
VALUES
  (1, 'Testimonial Slot 1', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United Kingdom', 'Quran Learning', NULL, 1, 'inactive', NOW(), NOW()),
  (2, 'Testimonial Slot 2', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United States', 'Noorani Qaida', NULL, 2, 'inactive', NOW(), NOW()),
  (3, 'Testimonial Slot 3', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'Canada', 'Tajweed Course', NULL, 3, 'inactive', NOW(), NOW()),
  (4, 'Testimonial Slot 4', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'Australia', 'Academic Tutoring', NULL, 4, 'inactive', NOW(), NOW()),
  (5, 'Testimonial Slot 5', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'Pakistan', 'O Level / IGCSE', NULL, 5, 'inactive', NOW(), NOW()),
  (6, 'Testimonial Slot 6', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United Kingdom', 'Quran Memorization', NULL, 6, 'inactive', NOW(), NOW()),
  (7, 'Testimonial Slot 7', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United States', 'Mathematics', NULL, 7, 'inactive', NOW(), NOW()),
  (8, 'Testimonial Slot 8', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United Arab Emirates', 'Islamic Studies', NULL, 8, 'inactive', NOW(), NOW());




