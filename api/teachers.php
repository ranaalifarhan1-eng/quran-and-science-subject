<?php
/**
 * Quran & Science — Public API: Active Teachers
 * Returns JSON list of active teacher profiles ordered by display_order.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$configFile = dirname(__DIR__) . '/config.php';

if (!file_exists($configFile)) {
    echo json_encode([
        'success'  => true,
        'source'   => 'unconfigured',
        'teachers' => []
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

require_once $configFile;

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT id, name, role, category, gender, short_description, specialties, image, display_order 
        FROM teachers 
        WHERE status = 'active' 
        ORDER BY display_order ASC, id ASC
    ");
    $stmt->execute();
    $teachers = $stmt->fetchAll();

    echo json_encode([
        'success'  => true,
        'source'   => 'database',
        'teachers' => is_array($teachers) ? $teachers : []
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    // Graceful fallback if database connection fails
    echo json_encode([
        'success'  => true,
        'source'   => 'fallback_error',
        'teachers' => []
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

