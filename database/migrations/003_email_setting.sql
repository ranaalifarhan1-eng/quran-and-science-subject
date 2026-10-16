-- Quran & Science — Migration 003: Global Email Setting
-- Additive, idempotent migration for live cPanel MySQL / MariaDB databases.
-- Inserts default contact_email and contact_email_visible into site_settings.

INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`) VALUES
  ('contact_email', 'onlinelearningacademy03@gmail.com'),
  ('contact_email_visible', '1');
