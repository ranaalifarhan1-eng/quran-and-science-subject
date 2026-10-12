<?php
require_once __DIR__ . '/auth.php';

$configFile = dirname(__DIR__) . '/config.php';
$error = '';
$success = '';

if (!file_exists($configFile)) {
    die("Configuration error: 'config.php' not found. Please copy 'config.example.php' to 'config.php' and enter your cPanel database credentials before running setup.");
}

try {
    $pdo = getDbConnection();
    
    // Check if admins table already has an admin
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM admins");
    $adminCount = (int)$stmt->fetchColumn();

    if ($adminCount > 0) {
        header('Location: login.php');
        exit;
    }
} catch (Throwable $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()) . ". Please make sure database tables are imported via database/setup.sql and credentials in config.php are correct.");
}

// Handle Admin Account Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            try {
                // Secondary lock check to strictly prevent multiple admin creation
                $recheck = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
                if ((int)$recheck > 0) {
                    http_response_code(403);
                    die("Setup is permanently locked. An admin account already exists.");
                }

                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hash]);

                setFlash('success', 'Admin account created successfully! Please sign in with your credentials.');
                header('Location: login.php');
                exit;
            } catch (Throwable $e) {
                $error = 'Failed to create admin: ' . $e->getMessage();
            }
        }
    }
}

$csrfToken = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Initial Setup — Create Admin Account</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --brand-950: #0c1810;
      --brand-900: #0f2b17;
      --brand-800: #143d22;
      --gold-500: #eab308;
      --slate-900: #0f172a;
      --slate-600: #475569;
      --slate-200: #e2e8f0;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
      background: radial-gradient(circle at 50% 20%, #154124 0%, #0c1810 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .setup-card {
      background: #ffffff;
      color: var(--slate-900);
      width: 100%;
      max-width: 440px;
      border-radius: 20px;
      padding: 36px 32px;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    }
    .brand-header {
      text-align: center;
      margin-bottom: 24px;
    }
    .brand-icon {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, var(--gold-500), #ca8a04);
      color: var(--brand-950);
      border-radius: 12px;
      margin: 0 auto 12px;
      display: grid;
      place-items: center;
      font-weight: 800;
      font-size: 20px;
    }
    h1 { font-size: 19px; font-weight: 800; }
    p { font-size: 13px; color: var(--slate-600); margin-top: 4px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 6px; }
    .form-input {
      width: 100%;
      padding: 11px 13px;
      border: 1.5px solid var(--slate-200);
      border-radius: 9px;
      font-size: 14px;
      font-family: inherit;
    }
    .form-input:focus {
      outline: none;
      border-color: var(--brand-800);
      box-shadow: 0 0 0 3px rgba(20, 61, 34, 0.12);
    }
    .btn-submit {
      width: 100%;
      padding: 12px;
      background: var(--brand-900);
      color: #fff;
      border: none;
      border-radius: 9px;
      font-size: 14px;
      font-weight: 800;
      cursor: pointer;
      margin-top: 6px;
    }
    .btn-submit:hover { background: var(--brand-950); }
    .alert-error {
      background: #fef2f2;
      color: #991b1b;
      padding: 12px;
      border-radius: 9px;
      font-size: 12.5px;
      margin-bottom: 18px;
      border: 1px solid #fecaca;
    }
  </style>
</head>
<body>
  <div class="setup-card">
    <div class="brand-header">
      <div class="brand-icon">QS</div>
      <h1>Create Admin Account</h1>
      <p>Initial setup for Quran &amp; Science admin panel</p>
    </div>

    <?php if ($error): ?>
      <div class="alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="install.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

      <div class="form-group">
        <label class="form-label" for="username">Admin Username</label>
        <input class="form-input" type="text" id="username" name="username" required placeholder="admin" value="<?= e($_POST['username'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Admin Email Address</label>
        <input class="form-input" type="email" id="email" name="email" required placeholder="admin@quranandscience.com" value="<?= e($_POST['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Choose Password (min 8 characters)</label>
        <input class="form-input" type="password" id="password" name="password" required placeholder="••••••••••••">
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm_password">Confirm Password</label>
        <input class="form-input" type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••••••">
      </div>

      <button type="submit" class="btn-submit">Create Account &amp; Complete Setup</button>
    </form>
  </div>
</body>
</html>
