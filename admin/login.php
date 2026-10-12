<?php
require_once __DIR__ . '/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$configFile = dirname(__DIR__) . '/config.php';
$needsConfig = !file_exists($configFile);
$needsInstall = false;

if (!$needsConfig) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM admins");
        $adminCount = (int)$stmt->fetchColumn();
        if ($adminCount === 0) {
            $needsInstall = true;
        }
    } catch (Throwable $e) {
        $error = 'Database connection error: ' . $e->getMessage() . '. Please verify credentials in config.php.';
    }
}

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$needsConfig) {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        $loginInput = trim($_POST['username'] ?? '');
        $password   = $_POST['password'] ?? '';

        if (empty($loginInput) || empty($password)) {
            $error = 'Please enter both username/email and password.';
        } else {
            try {
                $pdo = getDbConnection();
                $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM admins WHERE username = ? OR email = ? LIMIT 1");
                $stmt->execute([$loginInput, $loginInput]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Successful login
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_email'] = $admin['email'];
                    // Refresh CSRF token on login
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (Throwable $e) {
                $error = 'Database error during login. Please check database connection.';
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
  <title>Admin Login — Quran &amp; Science</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --brand-950: #0c1810;
      --brand-900: #0f2b17;
      --brand-800: #143d22;
      --gold-500: #eab308;
      --gold-400: #facc15;
      --slate-900: #0f172a;
      --slate-600: #475569;
      --slate-200: #e2e8f0;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background: radial-gradient(circle at 50% 20%, #154124 0%, #0c1810 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      color: #fff;
    }
    .login-card {
      background: #ffffff;
      color: var(--slate-900);
      width: 100%;
      max-width: 420px;
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
      padding: 36px 32px;
      border: 1px solid rgba(255,255,255,0.1);
    }
    .brand-header {
      text-align: center;
      margin-bottom: 28px;
    }
    .brand-icon {
      width: 52px;
      height: 52px;
      background: linear-gradient(135deg, var(--gold-500), #ca8a04);
      color: var(--brand-950);
      border-radius: 14px;
      margin: 0 auto 14px;
      display: grid;
      place-items: center;
      font-weight: 800;
      font-size: 22px;
      box-shadow: 0 10px 20px -5px rgba(234, 179, 8, 0.4);
    }
    .brand-header h1 {
      font-size: 20px;
      font-weight: 800;
      color: var(--slate-900);
      letter-spacing: -0.02em;
    }
    .brand-header p {
      font-size: 13px;
      color: var(--slate-600);
      margin-top: 4px;
    }
    .form-group {
      margin-bottom: 18px;
    }
    .form-label {
      display: block;
      font-size: 12.5px;
      font-weight: 700;
      color: #334155;
      margin-bottom: 6px;
    }
    .form-input {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid var(--slate-200);
      border-radius: 10px;
      font-size: 14px;
      font-family: inherit;
      transition: border-color 0.15s, box-shadow 0.15s;
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
      border-radius: 10px;
      font-size: 14px;
      font-weight: 800;
      cursor: pointer;
      transition: background 0.15s;
      margin-top: 6px;
      font-family: inherit;
    }
    .btn-submit:hover {
      background: var(--brand-950);
    }
    .alert {
      padding: 12px 14px;
      border-radius: 10px;
      font-size: 12.5px;
      margin-bottom: 20px;
      line-height: 1.5;
    }
    .alert-error {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }
    .alert-info {
      background: #eff6ff;
      color: #1e40af;
      border: 1px solid #bfdbfe;
    }
    .card-footer {
      text-align: center;
      margin-top: 24px;
      padding-top: 18px;
      border-top: 1px solid #f1f5f9;
      font-size: 12px;
      color: var(--slate-600);
    }
    .card-footer a {
      color: var(--brand-800);
      text-decoration: none;
      font-weight: 700;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="brand-header">
      <div class="brand-icon">QS</div>
      <h1>Quran &amp; Science</h1>
      <p>Instructor &amp; Team Management</p>
    </div>

    <?php if ($needsConfig): ?>
      <div class="alert alert-info">
        <strong>Setup Notice:</strong> <code>config.php</code> is not yet created. Please copy <code>config.example.php</code> to <code>config.php</code> and set your cPanel MySQL credentials.
      </div>
    <?php elseif ($needsInstall): ?>
      <div class="alert alert-info">
        <strong>First-time Setup:</strong> No admin account exists yet. <br>
        <a href="install.php" style="font-weight:800;color:#1e40af;text-decoration:underline;margin-top:6px;display:inline-block">Click here to create the Initial Admin Account &rarr;</a>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      
      <div class="form-group">
        <label class="form-label" for="username">Username or Email</label>
        <input class="form-input" type="text" id="username" name="username" required autofocus placeholder="admin">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-input" type="password" id="password" name="password" required placeholder="••••••••••••">
      </div>

      <button type="submit" class="btn-submit">Sign In to Dashboard</button>
    </form>

    <div class="card-footer">
      <a href="../index.html">&larr; Return to Public Website</a>
    </div>
  </div>
</body>
</html>
