<?php
/**
 * Quran & Science — Admin: Branding & Contact Settings
 * Manages global phone numbers, visibility, and WhatsApp configuration.
 */

require_once __DIR__ . '/auth.php';
requireAdmin();

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

$errors = [];

// Handle Form Submission BEFORE any HTML output (PRG architecture)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. CSRF Verification
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid or expired CSRF security token. Please refresh and try again.';
    }

    // 2. Form Token / Nonce Verification (Replay Protection)
    if (!validateAndConsumeFormToken($_POST['form_token'] ?? '', 'admin_settings')) {
        $errors[] = 'Form submission has expired or already been processed. Please refresh and try again.';
    }

    // 3. Sanitization & Validation
    $phone_us = trim(strip_tags($_POST['phone_us'] ?? ''));
    $phone_us_visible = isset($_POST['phone_us_visible']) ? '1' : '0';

    $phone_pk = trim(strip_tags($_POST['phone_pk'] ?? ''));
    $phone_pk_visible = isset($_POST['phone_pk_visible']) ? '1' : '0';

    $contact_email = trim(strip_tags($_POST['contact_email'] ?? ''));
    $contact_email_visible = isset($_POST['contact_email_visible']) ? '1' : '0';

    $whatsapp_number = trim(strip_tags($_POST['whatsapp_number'] ?? ''));
    $whatsapp_visible = isset($_POST['whatsapp_visible']) ? '1' : '0';

    $whatsapp_message = trim(strip_tags($_POST['whatsapp_message'] ?? ''));

    if (empty($phone_us) && $phone_us_visible === '1') {
        $errors[] = 'USA phone number is marked visible but no number was entered.';
    }
    if (empty($phone_pk) && $phone_pk_visible === '1') {
        $errors[] = 'Pakistan phone number is marked visible but no number was entered.';
    }
    if (!empty($contact_email)) {
        if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (strlen($contact_email) > 100) {
            $errors[] = 'Email address is too long (maximum 100 characters).';
        }
    } elseif ($contact_email_visible === '1') {
        $errors[] = 'Email address is marked visible but no email was entered.';
    }
    if (empty($whatsapp_number) && $whatsapp_visible === '1') {
        $errors[] = 'WhatsApp number is marked visible but no number was entered.';
    }

    // Check for excessive lengths or illegal script characters
    if (strlen($phone_us) > 40) $errors[] = 'USA phone number is too long (maximum 40 characters).';
    if (strlen($phone_pk) > 40) $errors[] = 'Pakistan phone number is too long (maximum 40 characters).';
    if (strlen($whatsapp_number) > 40) $errors[] = 'WhatsApp number is too long (maximum 40 characters).';
    if (strlen($whatsapp_message) > 500) $errors[] = 'WhatsApp message is too long (maximum 500 characters).';

    if (empty($errors)) {
        try {
            $pdo = getDbConnection();
            
            // Ensure table exists safely
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `site_settings` (
                  `setting_key` VARCHAR(50) NOT NULL PRIMARY KEY,
                  `setting_value` TEXT NULL,
                  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            $saveItems = [
                'phone_us'              => $phone_us,
                'phone_us_visible'      => $phone_us_visible,
                'phone_pk'              => $phone_pk,
                'phone_pk_visible'      => $phone_pk_visible,
                'contact_email'         => $contact_email,
                'contact_email_visible' => $contact_email_visible,
                'whatsapp_number'       => $whatsapp_number,
                'whatsapp_visible'      => $whatsapp_visible,
                'whatsapp_message'      => $whatsapp_message
            ];

            $stmt = $pdo->prepare("
                INSERT INTO `site_settings` (`setting_key`, `setting_value`) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)
            ");

            foreach ($saveItems as $key => $val) {
                $stmt->execute([$key, $val]);
            }

            setFlash('success', 'Branding & contact settings updated successfully.');
            header('Location: settings.php?saved=1', true, 303);
            exit;

        } catch (Throwable $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch current settings for rendering
$currentSettings = $defaultSettings;
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    $dbRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    if (!empty($dbRows)) {
        $currentSettings = array_merge($defaultSettings, $dbRows);
    }
} catch (Throwable $e) {
    // Fallback to defaultSettings if DB is not initialized yet
}

$pageTitle = 'Branding & Contact Settings';
$formToken = generateFormToken('admin_settings');
require_once __DIR__ . '/header.php';
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
  <div>
    <h2 style="font-size:22px;font-weight:800;color:var(--slate-900);margin:0">Branding &amp; Contact Settings</h2>
    <p style="font-size:13px;color:var(--slate-500);margin:4px 0 0">Manage public contact phone numbers, visibility, and WhatsApp chat links site-wide.</p>
  </div>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-error" style="margin-bottom:20px">
    <span>⚠</span>
    <div>
      <strong>Please correct the following:</strong>
      <ul style="margin:6px 0 0;padding-left:18px;font-size:12.5px">
        <?php foreach ($errors as $err): ?>
          <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>

<form method="POST" action="settings.php" id="settingsForm" style="display:grid;gap:24px">
  <input type="hidden" name="csrf_token" value="<?= e(getCsrfToken()) ?>">
  <input type="hidden" name="form_token" value="<?= e($formToken) ?>">

  <!-- CARD 1: PHONE CONTACTS -->
  <div class="card" style="padding:24px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;border-bottom:1px solid var(--slate-100);padding-bottom:14px">
      <div style="width:36px;height:36px;border-radius:10px;background:#e5f5eb;color:#0b5b30;display:grid;place-items:center;font-weight:800;font-size:16px">
        📞
      </div>
      <div>
        <h3 style="font-size:16px;font-weight:800;color:var(--slate-900);margin:0">Contact Numbers &amp; Visibility</h3>
        <p style="font-size:12px;color:var(--slate-500);margin:2px 0 0">Control phone numbers displayed across topbars, header, Contact page, and footers.</p>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:20px">
      <!-- USA Phone -->
      <div style="background:#f8fafc;padding:18px;border-radius:14px;border:1px solid var(--slate-200)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <label for="phone_us" class="form-label" style="font-weight:800;margin:0">USA Contact Number</label>
          <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--brand-900);cursor:pointer">
            <input type="checkbox" name="phone_us_visible" value="1" <?= ($currentSettings['phone_us_visible'] ?? '1') === '1' ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:var(--brand-900)">
            <span>Show on Website</span>
          </label>
        </div>
        <input type="text" id="phone_us" name="phone_us" class="form-control" value="<?= e($currentSettings['phone_us'] ?? '') ?>" placeholder="e.g. +1 406 497 0497" required>
        <span class="form-hint" style="margin-top:6px;display:block">Displays in top contact ticker, Contact page, and footer USA phone lines.</span>
      </div>

      <!-- Pakistan Phone -->
      <div style="background:#f8fafc;padding:18px;border-radius:14px;border:1px solid var(--slate-200)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <label for="phone_pk" class="form-label" style="font-weight:800;margin:0">Pakistan Contact Number</label>
          <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--brand-900);cursor:pointer">
            <input type="checkbox" name="phone_pk_visible" value="1" <?= ($currentSettings['phone_pk_visible'] ?? '1') === '1' ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:var(--brand-900)">
            <span>Show on Website</span>
          </label>
        </div>
        <input type="text" id="phone_pk" name="phone_pk" class="form-control" value="<?= e($currentSettings['phone_pk'] ?? '') ?>" placeholder="e.g. +92 344 5455154" required>
        <span class="form-hint" style="margin-top:6px;display:block">Displays in top contact ticker, Contact page, and footer PK phone lines.</span>
      </div>

      <!-- Email Address -->
      <div style="background:#f8fafc;padding:18px;border-radius:14px;border:1px solid var(--slate-200);grid-column:1 / -1">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px">
          <label for="contact_email" class="form-label" style="font-weight:800;margin:0">Official Contact Email</label>
          <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--brand-900);cursor:pointer">
            <input type="checkbox" name="contact_email_visible" value="1" <?= ($currentSettings['contact_email_visible'] ?? '1') === '1' ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:var(--brand-900)">
            <span>Show on Website</span>
          </label>
        </div>
        <input type="email" id="contact_email" name="contact_email" class="form-control" value="<?= e($currentSettings['contact_email'] ?? '') ?>" placeholder="e.g. onlinelearningacademy03@gmail.com" required>
        <span class="form-hint" style="margin-top:6px;display:block">Displays in top contact ticker, Contact page, and footer email lines.</span>
      </div>
    </div>
  </div>

  <!-- CARD 2: WHATSAPP INTEGRATION -->
  <div class="card" style="padding:24px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;border-bottom:1px solid var(--slate-100);padding-bottom:14px">
      <div style="width:36px;height:36px;border-radius:10px;background:#e5f5eb;color:#0b5b30;display:grid;place-items:center;font-weight:800;font-size:16px">
        💬
      </div>
      <div>
        <h3 style="font-size:16px;font-weight:800;color:var(--slate-900);margin:0">Floating WhatsApp &amp; Chat Link</h3>
        <p style="font-size:12px;color:var(--slate-500);margin:2px 0 0">Configure the bottom-right floating chat button and public WhatsApp action URLs.</p>
      </div>
    </div>

    <div style="display:grid;gap:18px">
      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:20px;align-items:start">
        <div class="form-group" style="margin:0">
          <label for="whatsapp_number" class="form-label">WhatsApp Contact Number *</label>
          <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="<?= e($currentSettings['whatsapp_number'] ?? '') ?>" placeholder="e.g. +92 344 5455154" required>
          <span class="form-hint">Enter in international format with country code. It is automatically normalized to digits for wa.me links.</span>
        </div>

        <div style="background:#f8fafc;padding:18px;border-radius:14px;border:1px solid var(--slate-200);display:flex;flex-direction:column;justify-content:center">
          <label style="display:inline-flex;align-items:center;gap:8px;font-size:13.5px;font-weight:800;color:var(--slate-900);cursor:pointer">
            <input type="checkbox" name="whatsapp_visible" value="1" <?= ($currentSettings['whatsapp_visible'] ?? '1') === '1' ? 'checked' : '' ?> style="width:18px;height:18px;accent-color:var(--brand-900)">
            <span>Show Floating WhatsApp Button Site-Wide</span>
          </label>
          <span class="form-hint" style="margin-top:6px;display:block">Unchecking this option will immediately hide the floating WhatsApp widget on all public pages.</span>
        </div>
      </div>

      <div class="form-group" style="margin:0">
        <label for="whatsapp_message" class="form-label">Default Prefilled WhatsApp Message</label>
        <textarea id="whatsapp_message" name="whatsapp_message" class="form-control" rows="3" placeholder="Enter message"><?= e($currentSettings['whatsapp_message'] ?? '') ?></textarea>
        <span class="form-hint">Prefilled greeting message automatically loaded when students click the WhatsApp link.</span>
      </div>
    </div>
  </div>

  <!-- SUBMIT BUTTON -->
  <div style="display:flex;align-items:center;gap:14px">
    <button type="submit" id="submitSettingsBtn" class="btn btn-primary" style="padding:11px 24px;font-size:14px">
      Save Settings
    </button>
    <a href="index.php" class="btn btn-outline" style="padding:11px 20px;font-size:14px">
      Cancel
    </a>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('settingsForm');
  var submitBtn = document.getElementById('submitSettingsBtn');
  var submitted = false;

  if (form && submitBtn) {
    form.addEventListener('submit', function() {
      if (submitted) return false;
      submitted = true;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
    });
  }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
