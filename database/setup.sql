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

-- Initial Seed Teachers (Active, displayed in order 1–4)
INSERT INTO `teachers` (`id`, `name`, `role`, `category`, `gender`, `short_description`, `specialties`, `image`, `display_order`, `status`) VALUES
(1, 'Quran & Tajweed Tutor', 'Quran & Tajweed Tutor', 'Quran Learning', 'male', 'Specialized instruction in Makharij, foundational Arabic letters, and applied Tajweed rules for clear and confident recitation.', 'Tajweed Rules, Makharij, Recitation', NULL, 1, 'active'),
(2, 'Female Quran Instructor', 'Female Quran Instructor', 'Sisters & Kids', 'female', 'Patient, supportive learning environment tailored for sisters, girls, and young beginners mastering Qaida and daily prayers.', 'Noorani Qaida, Duas & Salah, Kids Guidance', NULL, 2, 'active'),
(3, 'Hifz Coach', 'Hifz Coach', 'Memorization', 'male', 'Structured daily Sabaq, Sabqi, and Manzil routines to build durable long-term retention and systematic recitation discipline.', 'Sabaq Routine, Sabqi Revision, Manzil', NULL, 3, 'active'),
(4, 'Academic / Science Tutor', 'Academic / Science Tutor', 'Academic & Science', 'unspecified', 'Personalized concept coaching, problem-solving, and exam preparation for Cambridge O/A Levels, IGCSE, and Pakistan curricula.', 'O / A Level, Mathematics, Sciences', NULL, 4, 'active')
ON DUPLICATE KEY UPDATE `id`=`id`;
