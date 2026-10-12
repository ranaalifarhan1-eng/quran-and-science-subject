<?php
/**
 * Quran & Science — Configuration Template
 * 
 * Instructions:
 * 1. Copy this file to "config.php" in the same root directory.
 * 2. Replace the DB_* placeholder values with your actual cPanel MySQL credentials.
 * 3. Never commit real production passwords to Git.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'cpanel_quran_science_db');
define('DB_USER', 'cpanel_db_user');
define('DB_PASSWORD', 'YOUR_STRONG_DATABASE_PASSWORD');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

// Upload Directory Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/teachers/');
define('UPLOAD_URL', 'uploads/teachers/');
define('MAX_UPLOAD_SIZE', 3 * 1024 * 1024); // 3 MB max

/**
 * Returns a configured PDO database connection.
 * Throws PDOException on connection failure.
 *
 * @return PDO
 */
function getDbConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    }
    return $pdo;
}

/**
 * Cleanly escapes a string for HTML output (XSS protection).
 *
 * @param mixed $value
 * @return string
 */
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
