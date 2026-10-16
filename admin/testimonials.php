<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getDbConnection();

// Ensure testimonials table exists safely and prepare 8 slots if empty
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `testimonials` (
          `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `reviewer_name` VARCHAR(100) NOT NULL,
          `review_text` TEXT NOT NULL,
          `location` VARCHAR(100) NULL,
          `category` VARCHAR(100) NULL,
          `image` VARCHAR(255) NULL,
          `display_order` INT NOT NULL DEFAULT 1,
          `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX `idx_testimonials_status_order` (`status`, `display_order`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $countCheck = (int)$pdo->query("SELECT COUNT(*) FROM `testimonials`")->fetchColumn();
    if ($countCheck === 0) {
        $pdo->exec("
            INSERT IGNORE INTO `testimonials` 
              (`id`, `reviewer_name`, `review_text`, `location`, `category`, `image`, `display_order`, `status`, `created_at`, `updated_at`)
            VALUES
              (1, 'Testimonial Slot 1', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United Kingdom', 'Quran Learning', NULL, 1, 'inactive', NOW(), NOW()),
              (2, 'Testimonial Slot 2', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United States', 'Noorani Qaida', NULL, 2, 'inactive', NOW(), NOW()),
              (3, 'Testimonial Slot 3', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'Canada', 'Tajweed Course', NULL, 3, 'inactive', NOW(), NOW()),
              (4, 'Testimonial Slot 4', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'Australia', 'Academic Tutoring', NULL, 4, 'inactive', NOW(), NOW()),
              (5, 'Testimonial Slot 5', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'Pakistan', 'O Level / IGCSE', NULL, 5, 'inactive', NOW(), NOW()),
              (6, 'Testimonial Slot 6', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United Kingdom', 'Quran Memorization', NULL, 6, 'inactive', NOW(), NOW()),
              (7, 'Testimonial Slot 7', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United States', 'Mathematics', NULL, 7, 'inactive', NOW(), NOW()),
              (8, 'Testimonial Slot 8', 'This slot is reserved for genuine family feedback. Click Edit in the Admin Panel to enter verified student or parent feedback.', 'United Arab Emirates', 'Islamic Studies', NULL, 8, 'inactive', NOW(), NOW());
        ");
    }
} catch (Throwable $e) {
    // Continue gracefully
}

// Handle POST actions first (Toggle Status, Delete) BEFORE any HTML output (PRG architecture)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!verifyCsrfToken($token)) {
        setFlash('error', 'Security token expired. Please try again.');
        header('Location: testimonials.php', true, 303);
        exit;
    }

    $testimonialId = (int)($_POST['testimonial_id'] ?? 0);

    if ($action === 'toggle_status' && $testimonialId > 0) {
        try {
            $stmt = $pdo->prepare("SELECT status FROM testimonials WHERE id = ?");
            $stmt->execute([$testimonialId]);
            $currentStatus = $stmt->fetchColumn();

            if ($currentStatus) {
                $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
                $update = $pdo->prepare("UPDATE testimonials SET status = ?, updated_at = NOW() WHERE id = ?");
                $update->execute([$newStatus, $testimonialId]);
                setFlash('success', 'Testimonial status updated to ' . ucfirst($newStatus) . '.');
            }
        } catch (Throwable $e) {
            setFlash('error', 'Failed to update status: ' . $e->getMessage());
        }
        header('Location: testimonials.php', true, 303);
        exit;
    }

    if ($action === 'delete' && $testimonialId > 0) {
        try {
            // Find existing avatar image to delete from disk safely
            $stmt = $pdo->prepare("SELECT image FROM testimonials WHERE id = ?");
            $stmt->execute([$testimonialId]);
            $imageFile = $stmt->fetchColumn();

            if (!empty($imageFile)) {
                $fullImagePath = dirname(__DIR__) . '/' . ltrim($imageFile, '/');
                if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                    @unlink($fullImagePath);
                }
            }

            $del = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
            $del->execute([$testimonialId]);
            setFlash('success', 'Testimonial successfully deleted.');
        } catch (Throwable $e) {
            setFlash('error', 'Failed to delete testimonial: ' . $e->getMessage());
        }
        header('Location: testimonials.php', true, 303);
        exit;
    }
}

// Fetch all testimonials for display
$testimonials = [];
try {
    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY display_order ASC, id ASC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // Graceful error handling
}

$csrfToken = getCsrfToken();
$pageTitle = 'Testimonials & Reviews';
$topbarActionHtml = '<a href="testimonial-add.php" class="btn btn-primary btn-sm">+ Add Testimonial</a>';

require_once __DIR__ . '/header.php';
?>

<div class="card">
  <div class="card-header">
    <div>
      <h2 class="card-title">Family Testimonials &amp; Reviews</h2>
      <p style="font-size:12.5px;color:var(--slate-500);margin-top:2px">
        Manage genuine student and parent feedback displayed on the homepage. 8 preconfigured review slots are provided below for easy editing; slots marked Inactive remain completely hidden from website visitors.
      </p>
    </div>
    <a href="testimonial-add.php" class="btn btn-primary btn-sm">+ Add New Testimonial</a>
  </div>

  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid var(--slate-200);color:var(--slate-600);font-size:12px;text-transform:uppercase;letter-spacing:0.04em">
          <th style="padding:14px 20px;width:70px">Order</th>
          <th style="padding:14px 20px">Reviewer</th>
          <th style="padding:14px 20px">Category</th>
          <th style="padding:14px 20px;width:38%">Feedback Text</th>
          <th style="padding:14px 20px">Status</th>
          <th style="padding:14px 20px;text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($testimonials)): ?>
          <tr>
            <td colspan="6" style="padding:48px 20px;text-align:center;color:var(--slate-500)">
              <div style="width:52px;height:52px;border-radius:14px;background:#f1f5f9;color:var(--slate-400);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              </div>
              <h3 style="font-size:16px;font-weight:700;color:var(--slate-800);margin-bottom:6px">No testimonials entered yet</h3>
              <p style="font-size:13px;max-width:440px;margin:0 auto 16px;line-height:1.5">
                The homepage Testimonials section is automatically hidden when there are zero active reviews. Add genuine feedback here to display it.
              </p>
              <a href="testimonial-add.php" class="btn btn-primary btn-sm">+ Add First Testimonial</a>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($testimonials as $t): ?>
            <?php 
              $initial = mb_strtoupper(mb_substr(trim($t['reviewer_name']), 0, 1, 'UTF-8'), 'UTF-8');
              $isInactive = ($t['status'] === 'inactive');
            ?>
            <tr style="border-bottom:1px solid var(--slate-100);transition:background 0.15s ease;<?= $isInactive ? 'background:#fafbfc;' : '' ?>">
              <!-- Order -->
              <td style="padding:14px 20px">
                <span class="badge" style="background:#f1f5f9;color:var(--slate-700);font-weight:800;font-size:12px">
                  #<?= (int)$t['display_order'] ?>
                </span>
              </td>

              <!-- Reviewer Profile -->
              <td style="padding:14px 20px">
                <div style="display:flex;align-items:center;gap:12px">
                  <?php if (!empty($t['image'])): ?>
                    <img src="../<?= e($t['image']) ?>" alt="<?= e($t['reviewer_name']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0">
                  <?php else: ?>
                    <div style="width:40px;height:40px;border-radius:50%;background:#e5f5eb;color:#0b5b30;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;flex-shrink:0;border:2px solid #ccebd7">
                      <?= e($initial) ?>
                    </div>
                  <?php endif; ?>
                  <div>
                    <strong style="color:var(--slate-900);font-size:14px;display:block"><?= e($t['reviewer_name']) ?></strong>
                    <?php if (!empty($t['location'])): ?>
                      <span style="font-size:12px;color:var(--slate-500)"><?= e($t['location']) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </td>

              <!-- Category -->
              <td style="padding:14px 20px">
                <?php if (!empty($t['category'])): ?>
                  <span class="badge badge-info" style="font-size:11.5px;font-weight:700">
                    <?= e($t['category']) ?>
                  </span>
                <?php else: ?>
                  <span style="color:var(--slate-400);font-size:12px">—</span>
                <?php endif; ?>
              </td>

              <!-- Testimonial Text -->
              <td style="padding:14px 20px">
                <div style="color:<?= $isInactive ? 'var(--slate-600)' : 'var(--slate-800)' ?>;line-height:1.5;font-size:13px;max-width:380px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden" title="<?= e($t['review_text']) ?>">
                  &ldquo;<?= e($t['review_text']) ?>&rdquo;
                </div>
              </td>

              <!-- Status & Toggle -->
              <td style="padding:14px 20px">
                <form method="POST" action="testimonials.php" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                  <input type="hidden" name="action" value="toggle_status">
                  <input type="hidden" name="testimonial_id" value="<?= (int)$t['id'] ?>">
                  <?php if ($t['status'] === 'active'): ?>
                    <button type="submit" class="badge badge-success" style="cursor:pointer;border:none" title="Click to set Inactive">
                      ● Active
                    </button>
                  <?php else: ?>
                    <button type="submit" class="badge" style="cursor:pointer;border:none;background:#f1f5f9;color:#64748b;font-weight:700" title="Click to set Active">
                      ○ Inactive
                    </button>
                  <?php endif; ?>
                </form>
              </td>

              <!-- Actions -->
              <td style="padding:14px 20px;text-align:right">
                <div style="display:inline-flex;align-items:center;gap:6px">
                  <a href="testimonial-edit.php?id=<?= (int)$t['id'] ?>" class="btn btn-outline btn-sm" title="Edit Review">
                    Edit
                  </a>

                  <form method="POST" action="testimonials.php" onsubmit="return confirm('Are you sure you want to permanently delete this testimonial from <?= e(addslashes($t['reviewer_name'])) ?>?');" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="testimonial_id" value="<?= (int)$t['id'] ?>">
                    <button type="submit" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:rgba(239,68,68,0.25)" title="Delete Review">
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
