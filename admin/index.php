<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/header.php';

$totalTeachers = 0;
$activeTeachers = 0;
$inactiveTeachers = 0;
$recentTeachers = [];

try {
    $pdo = getDbConnection();
    
    // Fetch counts
    $stmt = $pdo->query("SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_count,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) AS inactive_count
        FROM teachers");
    $counts = $stmt->fetch();
    if ($counts) {
        $totalTeachers = (int)($counts['total'] ?? 0);
        $activeTeachers = (int)($counts['active_count'] ?? 0);
        $inactiveTeachers = (int)($counts['inactive_count'] ?? 0);
    }

    // Fetch up to 6 teachers
    $stmt = $pdo->query("SELECT id, name, role, category, status, display_order, image, updated_at FROM teachers ORDER BY display_order ASC, id ASC LIMIT 6");
    $recentTeachers = $stmt->fetchAll();
} catch (Throwable $e) {
    echo '<div class="alert alert-error">Database connection error: ' . e($e->getMessage()) . '</div>';
}
?>

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:18px;margin-bottom:28px">
  <!-- Card 1: Total -->
  <div class="card" style="margin-bottom:0;padding:22px">
    <div style="font-size:12px;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:0.04em">Total Profiles</div>
    <div style="font-size:32px;font-weight:800;color:var(--slate-900);margin-top:6px"><?= $totalTeachers ?></div>
    <div style="font-size:12.5px;color:var(--slate-600);margin-top:4px">All configured team profiles</div>
  </div>

  <!-- Card 2: Active -->
  <div class="card" style="margin-bottom:0;padding:22px;border-top:3px solid var(--brand-700)">
    <div style="font-size:12px;font-weight:700;color:var(--brand-700);text-transform:uppercase;letter-spacing:0.04em">Active On Website</div>
    <div style="font-size:32px;font-weight:800;color:var(--brand-900);margin-top:6px"><?= $activeTeachers ?></div>
    <div style="font-size:12.5px;color:var(--slate-600);margin-top:4px">Publicly visible on homepage</div>
  </div>

  <!-- Card 3: Inactive -->
  <div class="card" style="margin-bottom:0;padding:22px;border-top:3px solid var(--slate-400)">
    <div style="font-size:12px;font-weight:700;color:var(--slate-600);text-transform:uppercase;letter-spacing:0.04em">Hidden / Draft</div>
    <div style="font-size:32px;font-weight:800;color:var(--slate-700);margin-top:6px"><?= $inactiveTeachers ?></div>
    <div style="font-size:12.5px;color:var(--slate-600);margin-top:4px">Temporarily deactivated profiles</div>
  </div>

  <!-- Card 4: Branding & Contact Settings -->
  <div class="card" style="margin-bottom:0;padding:22px;border-top:3px solid var(--gold-500)">
    <div style="display:flex;align-items:center;justify-content:space-between">
      <div style="font-size:12px;font-weight:700;color:var(--gold-600);text-transform:uppercase;letter-spacing:0.04em">Site Settings</div>
      <a href="settings.php" class="btn btn-gold btn-sm" style="padding:4px 10px;font-size:11.5px">Edit</a>
    </div>
    <div style="font-size:16px;font-weight:800;color:var(--slate-900);margin-top:14px">Branding &amp; Contact</div>
    <div style="font-size:12.5px;color:var(--slate-600);margin-top:4px">Phones, WhatsApp, &amp; display toggles</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">Homepage Instructor Profiles</h2>
    <div style="display:flex;gap:10px">
      <a href="teachers.php" class="btn btn-outline btn-sm">Manage All</a>
      <a href="teacher-add.php" class="btn btn-primary btn-sm">+ Add New</a>
    </div>
  </div>

  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid var(--slate-200);color:var(--slate-600);font-size:12px;text-transform:uppercase;letter-spacing:0.04em">
          <th style="padding:14px 20px">Order</th>
          <th style="padding:14px 20px">Instructor</th>
          <th style="padding:14px 20px">Category</th>
          <th style="padding:14px 20px">Status</th>
          <th style="padding:14px 20px;text-align:right">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentTeachers)): ?>
          <tr>
            <td colspan="5" style="padding:32px;text-align:center;color:var(--slate-500)">
              No teachers found. <a href="teacher-add.php" style="color:var(--brand-800);font-weight:700">Add your first teacher profile</a>.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($recentTeachers as $t): ?>
            <tr style="border-bottom:1px solid var(--slate-100)">
              <td style="padding:16px 20px;font-weight:800;color:var(--slate-500)">
                #<?= e($t['display_order']) ?>
              </td>
              <td style="padding:16px 20px">
                <div style="display:flex;align-items:center;gap:12px">
                  <?php if (!empty($t['image'])): ?>
                    <img src="../<?= e($t['image']) ?>" alt="<?= e($t['name']) ?>" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1px solid var(--slate-200)">
                  <?php else: ?>
                    <div style="width:40px;height:40px;border-radius:10px;background:#eef7f1;color:var(--brand-900);display:grid;place-items:center;font-weight:800;font-size:13px">
                      <?= strtoupper(substr($t['name'], 0, 2)) ?>
                    </div>
                  <?php endif; ?>
                  <div>
                    <strong style="color:var(--slate-900);display:block"><?= e($t['name']) ?></strong>
                    <span style="font-size:12px;color:var(--slate-500)"><?= e($t['role']) ?></span>
                  </div>
                </div>
              </td>
              <td style="padding:16px 20px;color:var(--slate-600)">
                <span style="display:inline-block;padding:3px 10px;border-radius:6px;background:var(--slate-100);font-size:11.5px;font-weight:700">
                  <?= e($t['category'] ?? 'General') ?>
                </span>
              </td>
              <td style="padding:16px 20px">
                <?php if ($t['status'] === 'active'): ?>
                  <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;background:#ecfdf5;color:#065f46;font-size:11.5px;font-weight:700">
                    <span style="width:6px;height:6px;border-radius:50%;background:#10b981"></span>Active
                  </span>
                <?php else: ?>
                  <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;background:#f1f5f9;color:#475569;font-size:11.5px;font-weight:700">
                    <span style="width:6px;height:6px;border-radius:50%;background:#94a3b8"></span>Inactive
                  </span>
                <?php endif; ?>
              </td>
              <td style="padding:16px 20px;text-align:right">
                <a href="teacher-edit.php?id=<?= $t['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
