<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getDbConnection();

// Handle POST actions first (Toggle Status, Delete) BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!verifyCsrfToken($token)) {
        setFlash('error', 'Security token expired. Please try again.');
        header('Location: teachers.php', true, 303);
        exit;
    }

    $teacherId = (int)($_POST['teacher_id'] ?? 0);

    if ($action === 'toggle_status' && $teacherId > 0) {
        try {
            $stmt = $pdo->prepare("SELECT status FROM teachers WHERE id = ?");
            $stmt->execute([$teacherId]);
            $currentStatus = $stmt->fetchColumn();

            if ($currentStatus) {
                $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
                $update = $pdo->prepare("UPDATE teachers SET status = ?, updated_at = NOW() WHERE id = ?");
                $update->execute([$newStatus, $teacherId]);
                setFlash('success', 'Instructor status updated to ' . ucfirst($newStatus) . '.');
            }
        } catch (Throwable $e) {
            setFlash('error', 'Failed to update status: ' . $e->getMessage());
        }
        header('Location: teachers.php', true, 303);
        exit;
    }

    if ($action === 'delete' && $teacherId > 0) {
        try {
            // Find existing image to delete from disk
            $stmt = $pdo->prepare("SELECT image FROM teachers WHERE id = ?");
            $stmt->execute([$teacherId]);
            $imageFile = $stmt->fetchColumn();

            if (!empty($imageFile)) {
                $fullImagePath = dirname(__DIR__) . '/' . ltrim($imageFile, '/');
                if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                    @unlink($fullImagePath);
                }
            }

            $del = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $del->execute([$teacherId]);
            setFlash('success', 'Instructor profile successfully deleted.');
        } catch (Throwable $e) {
            setFlash('error', 'Failed to delete profile: ' . $e->getMessage());
        }
        header('Location: teachers.php', true, 303);
        exit;
    }
}

// Fetch all teachers for display
$teachers = [];
try {
    $stmt = $pdo->query("SELECT * FROM teachers ORDER BY display_order ASC, id ASC");
    $teachers = $stmt->fetchAll();
} catch (Throwable $e) {
    // Graceful error logging
}

$csrfToken = getCsrfToken();

// Include HTML layout Header ONLY AFTER POST handling is complete
$pageTitle = 'Teachers / Team Profiles';
require_once __DIR__ . '/header.php';
?>

<div class="card">
  <div class="card-header">
    <div>
      <h2 class="card-title">All Instructor Profiles</h2>
      <p style="font-size:12.5px;color:var(--slate-500);margin-top:2px">
        Manage active tutors shown on the homepage. Drag/reorder via display order numbers.
      </p>
    </div>
    <a href="teacher-add.php" class="btn btn-primary btn-sm">+ Add New Instructor</a>
  </div>

  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid var(--slate-200);color:var(--slate-600);font-size:12px;text-transform:uppercase;letter-spacing:0.04em">
          <th style="padding:14px 20px;width:70px">Order</th>
          <th style="padding:14px 20px">Instructor Profile</th>
          <th style="padding:14px 20px">Category</th>
          <th style="padding:14px 20px">Specialties</th>
          <th style="padding:14px 20px">Visibility</th>
          <th style="padding:14px 20px;text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($teachers)): ?>
          <tr>
            <td colspan="6" style="padding:40px;text-align:center;color:var(--slate-500)">
              No instructors have been created yet. <br>
              <a href="teacher-add.php" class="btn btn-primary btn-sm" style="margin-top:12px">Create First Instructor</a>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($teachers as $t): ?>
            <tr style="border-bottom:1px solid var(--slate-100)">
              <td style="padding:16px 20px;font-weight:800;color:var(--slate-500)">
                #<?= e($t['display_order']) ?>
              </td>
              <td style="padding:16px 20px">
                <div style="display:flex;align-items:center;gap:14px">
                  <?php if (!empty($t['image'])): ?>
                    <img src="../<?= e($t['image']) ?>" alt="<?= e($t['name']) ?>" style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:1px solid var(--slate-200);flex-shrink:0">
                  <?php else: ?>
                    <div style="width:48px;height:48px;border-radius:12px;background:#eef7f1;color:var(--brand-900);display:grid;place-items:center;font-weight:800;font-size:14px;flex-shrink:0">
                      <?= strtoupper(substr($t['name'], 0, 2)) ?>
                    </div>
                  <?php endif; ?>
                  <div>
                    <strong style="color:var(--slate-900);font-size:14px;display:block"><?= e($t['name']) ?></strong>
                    <span style="font-size:12px;color:var(--brand-700);font-weight:600"><?= e($t['role']) ?></span>
                    <p style="font-size:12px;color:var(--slate-500);margin-top:2px;max-width:320px;line-height:1.4">
                      <?= e(mb_strimwidth($t['short_description'], 0, 75, '...')) ?>
                    </p>
                  </div>
                </div>
              </td>
              <td style="padding:16px 20px;color:var(--slate-600);white-space:nowrap">
                <span style="display:inline-block;padding:3px 10px;border-radius:6px;background:var(--slate-100);font-size:11.5px;font-weight:700">
                  <?= e($t['category'] ?? 'General') ?>
                </span>
              </td>
              <td style="padding:16px 20px">
                <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:260px">
                  <?php 
                    $specs = array_map('trim', explode(',', $t['specialties'] ?? ''));
                    foreach ($specs as $spec): 
                      if (!$spec) continue;
                  ?>
                    <span style="display:inline-block;padding:2px 7px;border-radius:5px;background:#ecfdf5;color:#065f46;font-size:11px;font-weight:600">
                      <?= e($spec) ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              </td>
              <td style="padding:16px 20px;white-space:nowrap">
                <form method="POST" action="teachers.php" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                  <input type="hidden" name="action" value="toggle_status">
                  <input type="hidden" name="teacher_id" value="<?= (int)$t['id'] ?>">
                  
                  <?php if ($t['status'] === 'active'): ?>
                    <button type="submit" title="Click to Deactivate" style="cursor:pointer;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:11.5px;font-weight:700;font-family:inherit">
                      <span style="width:6px;height:6px;border-radius:50%;background:#10b981"></span>Active
                    </button>
                  <?php else: ?>
                    <button type="submit" title="Click to Activate" style="cursor:pointer;background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:11.5px;font-weight:700;font-family:inherit">
                      <span style="width:6px;height:6px;border-radius:50%;background:#94a3b8"></span>Inactive
                    </button>
                  <?php endif; ?>
                </form>
              </td>
              <td style="padding:16px 20px;text-align:right;white-space:nowrap">
                <div style="display:inline-flex;align-items:center;gap:6px">
                  <a href="teacher-edit.php?id=<?= (int)$t['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                  
                  <form method="POST" action="teachers.php" style="display:inline" onsubmit="return confirm('Are you sure you want to delete profile &quot;<?= addslashes(e($t['name'])) ?>&quot;? This cannot be undone.');">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="teacher_id" value="<?= (int)$t['id'] ?>">
                    <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;cursor:pointer">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
