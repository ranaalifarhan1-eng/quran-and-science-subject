<?php
/**
 * Quran & Science — Admin Authentication & Security Helper
 */

// Enforce secure session cookie settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

$configFile = dirname(__DIR__) . '/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
} else {
    // Basic fallback e() if config.php does not exist yet
    if (!function_exists('e')) {
        function e($value) {
            return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
        }
    }
}

/**
 * Checks if admin is currently authenticated.
 *
 * @return bool
 */
function isLoggedIn() {
    return !empty($_SESSION['admin_id']);
}

/**
 * Requires admin authentication. Redirects to login.php if not authenticated.
 */
function requireAdmin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Generates and returns a CSRF token stored in the session.
 *
 * @return string
 */
function getCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a submitted CSRF token against the session token.
 *
 * @param string|null $token
 * @return bool
 */
function verifyCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sets a flash message.
 *
 * @param string $type 'success'|'error'|'info'
 * @param string $message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Retrieves and clears the current flash message.
 *
 * @return array|null
 */
function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
