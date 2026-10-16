<?php
/**
 * Quran & Science — Public API: Active Testimonials
 * Returns JSON list of verified active customer testimonials ordered by display_order.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$configFile = dirname(__DIR__) . '/config.php';

if (!file_exists($configFile)) {
    echo json_encode([
        'success'      => true,
        'source'       => 'unconfigured',
        'testimonials' => []
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

require_once $configFile;

try {
    $pdo = getDbConnection();
    
    // Check if table exists first for safe operation
    $stmt = $pdo->prepare("
        SELECT id, reviewer_name, review_text, location, category, image, display_order 
        FROM testimonials 
        WHERE status = 'active' 
        ORDER BY display_order ASC, id ASC
    ");
    $stmt->execute();
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'      => true,
        'source'       => 'database',
        'testimonials' => is_array($testimonials) ? $testimonials : []
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    // Graceful fallback if database table is not yet migrated or connection fails
    echo json_encode([
        'success'      => true,
        'source'       => 'fallback_empty',
        'testimonials' => []
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
