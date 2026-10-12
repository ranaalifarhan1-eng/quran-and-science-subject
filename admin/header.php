<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$currentPage = basename($_SERVER['PHP_SELF']);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Quran &amp; Science Admin Panel</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --brand-950: #0c1810;
      --brand-900: #0f2b17;
      --brand-800: #143d22;
      --brand-700: #19522e;
      --brand-600: #1e663a;
      --brand-100: #d1fae5;
      --brand-50: #ecfdf5;
      --gold-500: #eab308;
      --gold-400: #facc15;
      --slate-900: #0f172a;
      --slate-800: #1e293b;
      --slate-700: #334155;
      --slate-600: #475569;
      --slate-500: #64748b;
      --slate-200: #e2e8f0;
      --slate-100: #f1f5f9;
      --slate-50: #f8fafc;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #f6f8f6;
      color: var(--slate-900);
      min-height: 100vh;
      display: flex;
    }
    /* Layout */
    .admin-sidebar {
      width: 260px;
      background: var(--brand-950);
      color: #e2e8f0;
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 40;
      transition: transform 0.25s ease;
      border-right: 1px solid rgba(255,255,255,0.08);
    }
    .admin-brand {
      padding: 24px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      text-decoration: none;
      color: #fff;
    }
    .admin-brand-icon {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--gold-500), #ca8a04);
      color: var(--brand-950);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 18px;
    }
    .admin-brand-text h1 {
      font-size: 15px;
      font-weight: 800;
      letter-spacing: -0.01em;
      color: #ffffff;
    }
    .admin-brand-text span {
      font-size: 11px;
      color: var(--gold-400);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .admin-nav {
      padding: 20px 12px;
      display: flex;
      flex-direction: column;
      gap: 4px;
      flex: 1;
    }
    .admin-nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      border-radius: 10px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 13.5px;
      font-weight: 600;
      transition: all 0.15s ease;
    }
    .admin-nav-item:hover {
      background: rgba(255,255,255,0.06);
      color: #ffffff;
    }
    .admin-nav-item.active {
      background: var(--brand-800);
      color: #ffffff;
      box-shadow: inset 3px 0 0 var(--gold-400);
    }
    .admin-nav-item svg {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
    }
    .admin-sidebar-footer {
      padding: 16px;
      border-top: 1px solid rgba(255,255,255,0.08);
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 12px;
      color: #64748b;
    }
    .admin-user-info strong {
      display: block;
      color: #f1f5f9;
      font-size: 13px;
    }
    /* Main Area */
    .admin-main {
      margin-left: 260px;
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }
    .admin-topbar {
      height: 64px;
      background: #ffffff;
      border-bottom: 1px solid var(--slate-200);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 28px;
      position: sticky;
      top: 0;
      z-index: 30;
    }
    .admin-topbar-title {
      font-size: 17px;
      font-weight: 800;
      color: var(--slate-900);
    }
    .admin-topbar-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .admin-content {
      padding: 28px;
      max-width: 1200px;
      width: 100%;
    }
    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 16px;
      border-radius: 9px;
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.15s ease;
      font-family: inherit;
    }
    .btn-primary {
      background: var(--brand-900);
      color: #fff;
    }
    .btn-primary:hover {
      background: var(--brand-950);
      color: #fff;
    }
    .btn-gold {
      background: var(--gold-500);
      color: var(--brand-950);
    }
    .btn-gold:hover {
      background: #d97706;
      color: #fff;
    }
    .btn-outline {
      border-color: var(--slate-200);
      background: #ffffff;
      color: var(--slate-700);
    }
    .btn-outline:hover {
      background: var(--slate-50);
      border-color: #cbd5e1;
    }
    .btn-danger {
      background: #ef4444;
      color: #ffffff;
    }
    .btn-danger:hover {
      background: #dc2626;
    }
    .btn-sm {
      padding: 6px 11px;
      font-size: 12px;
      border-radius: 7px;
    }
    /* Cards & Tables */
    .card {
      background: #ffffff;
      border: 1px solid var(--slate-200);
      border-radius: 14px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
      overflow: hidden;
      margin-bottom: 24px;
    }
    .card-header {
      padding: 18px 22px;
      border-bottom: 1px solid var(--slate-100);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .card-title {
      font-size: 15px;
      font-weight: 800;
      color: var(--slate-900);
    }
    .card-body {
      padding: 22px;
    }
    /* Alerts */
    .alert {
      padding: 14px 18px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 13.5px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
    }
    .alert-success {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
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
    /* Mobile toggle */
    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
      color: var(--slate-700);
    }
    @media (max-width: 900px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }
      .admin-sidebar.open {
        transform: translateX(0);
      }
      .admin-main {
        margin-left: 0;
      }
      .mobile-menu-btn {
        display: block;
      }
      .admin-content {
        padding: 18px;
      }
    }
  </style>
</head>
<body>
  <aside class="admin-sidebar" id="adminSidebar">
    <a href="index.php" class="admin-brand">
      <div class="admin-brand-icon">QS</div>
      <div class="admin-brand-text">
        <h1>Quran &amp; Science</h1>
        <span>Admin Panel</span>
      </div>
    </a>

    <nav class="admin-nav">
      <a href="index.php" class="admin-nav-item <?= $currentPage === 'index.php' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Dashboard</span>
      </a>
      <a href="teachers.php" class="admin-nav-item <?= in_array($currentPage, ['teachers.php', 'teacher-edit.php']) ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Teachers / Team</span>
      </a>
      <a href="teacher-add.php" class="admin-nav-item <?= $currentPage === 'teacher-add.php' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Add Teacher</span>
      </a>
      <a href="settings.php" class="admin-nav-item <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Branding &amp; Contact</span>
      </a>
      <a href="../index.html" target="_blank" rel="noopener" class="admin-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        <span>View Website</span>
      </a>
    </nav>

    <div class="admin-sidebar-footer">
      <div class="admin-user-info">
        <span>Logged in as:</span>
        <strong><?= e($_SESSION['admin_username'] ?? 'Admin') ?></strong>
      </div>
      <a href="logout.php" class="btn btn-outline btn-sm" title="Sign out" style="color:#ef4444;border-color:rgba(239,68,68,0.3)">
        Logout
      </a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:12px">
        <button type="button" class="mobile-menu-btn" onclick="document.getElementById('adminSidebar').classList.toggle('open')" aria-label="Toggle Navigation">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <span class="admin-topbar-title"><?= isset($pageTitle) ? e($pageTitle) : 'Admin Area' ?></span>
      </div>
      <div class="admin-topbar-actions">
        <a href="teacher-add.php" class="btn btn-primary btn-sm">+ Add Teacher</a>
      </div>
    </header>

    <div class="admin-content">
      <?php if ($flash): ?>
        <?php 
          $alertClass = 'alert-success';
          $alertIcon  = '✓';
          if ($flash['type'] === 'error') {
            $alertClass = 'alert-error';
            $alertIcon  = '⚠';
          } elseif ($flash['type'] === 'info') {
            $alertClass = 'alert-info';
            $alertIcon  = 'ℹ';
          }
        ?>
        <div class="alert <?= $alertClass ?>">
          <span><?= $alertIcon ?></span>
          <span><?= e($flash['message']) ?></span>
        </div>
      <?php endif; ?>
