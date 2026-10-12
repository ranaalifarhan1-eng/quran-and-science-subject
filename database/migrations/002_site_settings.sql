-- Quran & Science — Migration 002: Site Settings Table
-- Safe, idempotent migration for live cPanel MySQL / MariaDB databases.
-- Does NOT drop or alter existing admins or teachers tables.

CREATE TABLE IF NOT EXISTS `site_settings` (
  `setting_key` VARCHAR(50) NOT NULL PRIMARY KEY,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default initial site branding & contact settings
INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`) VALUES
  ('phone_us', '+1 406 497 0497'),
  ('phone_us_visible', '1'),
  ('phone_pk', '+92 344 5455154'),
  ('phone_pk_visible', '1'),
  ('whatsapp_number', '+92 344 5455154'),
  ('whatsapp_visible', '1'),
  ('whatsapp_message', 'Hello, I would like to know more about Quran & Science online learning programs.');
