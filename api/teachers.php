<?php
/**
 * Quran & Science — Public API: Active Teachers
 * Returns JSON list of active teacher profiles ordered by display_order.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$configFile = dirname(__DIR__) . '/config.php';

// Default initial teacher profiles for fallback if DB is not yet configured
$fallbackTeachers = [
    [
        'id' => 1,
        'name' => 'Quran & Tajweed Tutor',
        'role' => 'Quran & Tajweed Tutor',
        'category' => 'Quran Learning',
        'gender' => 'male',
        'short_description' => 'Specialized instruction in Makharij, foundational Arabic letters, and applied Tajweed rules for clear and confident recitation.',
        'specialties' => 'Tajweed Rules, Makharij, Recitation',
        'image' => null,
        'display_order' => 1
    ],
    [
        'id' => 2,
        'name' => 'Female Quran Instructor',
        'role' => 'Female Quran Instructor',
        'category' => 'Sisters & Kids',
        'gender' => 'female',
        'short_description' => 'Patient, supportive learning environment tailored for sisters, girls, and young beginners mastering Qaida and daily prayers.',
        'specialties' => 'Noorani Qaida, Duas & Salah, Kids Guidance',
        'image' => null,
        'display_order' => 2
    ],
    [
        'id' => 3,
        'name' => 'Hifz Coach',
        'role' => 'Hifz Coach',
        'category' => 'Memorization',
        'gender' => 'male',
        'short_description' => 'Structured daily Sabaq, Sabqi, and Manzil routines to build durable long-term retention and systematic recitation discipline.',
        'specialties' => 'Sabaq Routine, Sabqi Revision, Manzil',
        'image' => null,
        'display_order' => 3
    ],
    [
        'id' => 4,
        'name' => 'Academic / Science Tutor',
        'role' => 'Academic / Science Tutor',
        'category' => 'Academic & Science',
        'gender' => 'unspecified',
        'short_description' => 'Personalized concept coaching, problem-solving, and exam preparation for Cambridge O/A Levels, IGCSE, and Pakistan curricula.',
        'specialties' => 'O / A Level, Mathematics, Sciences',
        'image' => null,
        'display_order' => 4
    ]
];

if (!file_exists($configFile)) {
    echo json_encode([
        'success' => true,
        'source'  => 'default_seed',
        'teachers' => $fallbackTeachers
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

    // If database table is empty, serve seed profiles gracefully
    if (empty($teachers)) {
        $teachers = $fallbackTeachers;
    }

    echo json_encode([
        'success'  => true,
        'source'   => 'database',
        'teachers' => $teachers
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    // Graceful fallback if database connection fails
    echo json_encode([
        'success'  => true,
        'source'   => 'fallback_error',
        'teachers' => $fallbackTeachers
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
