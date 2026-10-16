-- Quran & Science — Migration 005: 8 Inactive Testimonial Slots
-- Additive, idempotent migration for live cPanel MySQL / MariaDB databases.
-- Prepares 8 inactive testimonial slots for the administrator to easily edit and publish.
-- All 8 records remain INACTIVE by default and will NOT render on the public website.

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
