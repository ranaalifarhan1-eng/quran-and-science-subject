<?php
/**
 * Quran & Science — Public API: Site Settings
 * Exposes public contact & branding settings (phone numbers, WhatsApp configuration)
 * Safe read-only endpoint with no credentials or private data.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Default fallback settings
$defaultSettings = [
    'phone_us'              => '+1 406 497 0497',
    'phone_us_visible'      => '1',
    'phone_pk'              => '+92 344 5455154',
    'phone_pk_visible'      => '1',
    'contact_email'         => 'onlinelearningacademy03@gmail.com',
    'contact_email_visible' => '1',
    'whatsapp_number'       => '+92 344 5455154',
    'whatsapp_visible'      => '1',
    'whatsapp_message'      => 'Hello, I would like to know more about Quran & Science online learning programs.'
];

$configFile = dirname(__DIR__) . '/config.php';
$source = 'default_fallback';
$settings = $defaultSettings;

if (file_exists($configFile)) {
    try {
        require_once $configFile;
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $dbRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        if (!empty($dbRows)) {
            $settings = array_merge($defaultSettings, $dbRows);
            $source = 'database';
        }
    } catch (Throwable $e) {
        // Fallback to defaultSettings on DB error
        $source = 'error_fallback';
    }
}

// Helpers for phone and email normalization
$phoneUsVal = (string)($settings['phone_us'] ?? $defaultSettings['phone_us']);
$phonePkVal = (string)($settings['phone_pk'] ?? $defaultSettings['phone_pk']);
$emailVal   = (string)($settings['contact_email'] ?? $defaultSettings['contact_email']);
$waNumVal   = (string)($settings['whatsapp_number'] ?? $defaultSettings['whatsapp_number']);
$waMsgVal   = (string)($settings['whatsapp_message'] ?? $defaultSettings['whatsapp_message']);

$phoneUsTel = '+' . preg_replace('/[^\d]/', '', $phoneUsVal);
$phonePkTel = '+' . preg_replace('/[^\d]/', '', $phonePkVal);
$waDigits   = preg_replace('/[^\d]/', '', $waNumVal);

$response = [
    'success'  => true,
    'source'   => $source,
    'phone_us' => [
        'value'   => $phoneUsVal,
        'visible' => ($settings['phone_us_visible'] ?? '1') === '1',
        'tel'     => $phoneUsTel
    ],
    'phone_pk' => [
        'value'   => $phonePkVal,
        'visible' => ($settings['phone_pk_visible'] ?? '1') === '1',
        'tel'     => $phonePkTel
    ],
    'email'    => [
        'value'   => $emailVal,
        'visible' => ($settings['contact_email_visible'] ?? '1') === '1',
        'mailto'  => 'mailto:' . $emailVal
    ],
    'whatsapp' => [
        'number'  => $waNumVal,
        'digits'  => $waDigits,
        'visible' => ($settings['whatsapp_visible'] ?? '1') === '1',
        'message' => $waMsgVal
    ]
];

echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

