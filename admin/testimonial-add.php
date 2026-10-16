<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getDbConnection();
$error = '';

// Handle POST request first (Before ANY HTML output or header.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token     = $_POST['csrf_token'] ?? '';
    $formToken = $_POST['form_token'] ?? '';

    if (!verifyCsrfToken($token)) {
        $error = 'Security token expired. Please refresh and try again.';
    } elseif (!validateAndConsumeFormToken($formToken, 'testimonial_add')) {
        // Prevent duplicate POST submission
        setFlash('info', 'This testimonial submission was already processed.');
        header('Location: testimonials.php', true, 303);
        exit;
    } else {
        $reviewerName = trim($_POST['reviewer_name'] ?? '');
        $reviewText   = trim($_POST['review_text'] ?? '');
        $location     = trim($_POST['location'] ?? '');
        $category     = trim($_POST['category'] ?? '');
        $order        = (int)($_POST['display_order'] ?? 1);
        $status       = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $imagePath    = null;
        $savedFile    = null;

        if (empty($reviewerName) || empty($reviewText)) {
            $error = 'Please complete all required fields (Reviewer Name, Feedback / Review Text).';
        } else {
            // Handle optional avatar upload
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Avatar upload failed. Error code: ' . $_FILES['image']['error'];
                } else {
                    $fileTmp  = $_FILES['image']['tmp_name'];
                    $fileSize = $_FILES['image']['size'];
                    $fileName = $_FILES['image']['name'];

                    if ($fileSize > (2 * 1024 * 1024)) {
                        $error = 'Avatar image file size exceeds the 2MB maximum limit.';
                    } else {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime  = finfo_file($finfo, $fileTmp);
                        finfo_close($finfo);

                        $allowedMimes = [
                            'image/jpeg' => 'jpg',
                            'image/png'  => 'png',
                            'image/webp' => 'webp'
                        ];

                        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                        if (!array_key_exists($mime, $allowedMimes) || !in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $error = 'Invalid image format. Only JPG, PNG, and WebP images are allowed.';
                        } elseif (!@getimagesize($fileTmp)) {
                            $error = 'The uploaded file is not a valid image.';
                        } else {
                            $targetExt = $allowedMimes[$mime];
                            $safeFilename = 'testimonial_' . bin2hex(random_bytes(8)) . '.' . $targetExt;
                            $uploadDir = dirname(__DIR__) . '/uploads/testimonials/';

                            if (!is_dir($uploadDir)) {
                                @mkdir($uploadDir, 0755, true);
                            }

                            $destPath = $uploadDir . $safeFilename;
                            if (move_uploaded_file($fileTmp, $destPath)) {
                                $imagePath = 'uploads/testimonials/' . $safeFilename;
                                $savedFile = $destPath;
                            } else {
                                $error = 'Failed to save avatar image. Please check folder write permissions.';
                            }
                        }
                    }
                }
            }

            if (!$error) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO `testimonials` 
                          (`reviewer_name`, `review_text`, `location`, `category`, `image`, `display_order`, `status`, `created_at`, `updated_at`)
                        VALUES 
                          (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([
                        $reviewerName,
                        $reviewText,
                        !empty($location) ? $location : null,
                        !empty($category) ? $category : null,
                        $imagePath,
                        $order,
                        $status
                    ]);

                    setFlash('success', 'Testimonial from ' . htmlspecialchars($reviewerName, ENT_QUOTES, 'UTF-8') . ' successfully added.');
                    header('Location: testimonials.php', true, 303);
                    exit;

                } catch (Throwable $e) {
                    // Clean up newly uploaded file if database insert fails
                    if ($savedFile && file_exists($savedFile)) {
                        @unlink($savedFile);
                    }
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Generate new form nonce token for replay protection
$formToken = generateFormToken('testimonial_add');
$csrfToken = getCsrfToken();

$pageTitle = 'Add Testimonial';
require_once __DIR__ . '/header.php';
?>

<div style="max-width:820px;margin:0 auto">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <div>
      <h2 style="font-size:22px;font-weight:800;color:var(--slate-900)">Add Family Testimonial</h2>
      <p style="font-size:13px;color:var(--slate-500);margin-top:2px">
        Add genuine student or parent feedback. Reviews set to Active will appear in the homepage slider.
      </p>
    </div>
    <a href="testimonials.php" class="btn btn-outline btn-sm">&larr; Back to Testimonials</a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:20px">
      <span>⚠</span>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <div class="card" style="padding:28px">
    <form method="POST" action="testimonial-add.php" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <input type="hidden" name="form_token" value="<?= e($formToken) ?>">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <!-- Reviewer Name -->
        <div class="form-group" style="grid-column:1 / -1">
          <label for="reviewer_name" class="form-label">Reviewer Name *</label>
          <input type="text" id="reviewer_name" name="reviewer_name" class="form-control" required value="<?= e($_POST['reviewer_name'] ?? '') ?>" placeholder="e.g. Brother Tariq &amp; Family, or Sarah M.">
          <span class="form-hint">Parent or student name as they would like it displayed.</span>
        </div>

        <!-- Location / Country -->
        <div class="form-group">
          <label for="location" class="form-label">Location / Country (Optional)</label>
          <input type="text" id="location" name="location" class="form-control" value="<?= e($_POST['location'] ?? '') ?>" placeholder="e.g. Dallas, Texas, USA or London, UK">
          <span class="form-hint">City, State or Country of residence.</span>
        </div>

        <!-- Learning Category -->
        <div class="form-group">
          <label for="category" class="form-label">Learning Category (Optional)</label>
          <input type="text" id="category" name="category" list="category_options" class="form-control" value="<?= e($_POST['category'] ?? '') ?>" placeholder="Select or type program category">
          <datalist id="category_options">
            <option value="Quran Learning">
            <option value="Noorani Qaida">
            <option value="Quran Reading &amp; Tajweed">
            <option value="Quran Memorization (Hifz)">
            <option value="Academic Tutoring">
            <option value="O Level / IGCSE">
            <option value="A Level">
            <option value="Mathematics">
            <option value="Both Quran &amp; Academic">
          </datalist>
          <span class="form-hint">Displays as an informative badge on the card.</span>
        </div>

        <!-- Testimonial Text -->
        <div class="form-group" style="grid-column:1 / -1">
          <label for="review_text" class="form-label">Feedback / Testimonial Text *</label>
          <textarea id="review_text" name="review_text" class="form-control" rows="5" required placeholder="What the family shared about tutor punctuality, student progress, recitation improvement, or academic results..."><?= e($_POST['review_text'] ?? '') ?></textarea>
          <span class="form-hint">Enter the authentic testimonial text. Quotes are automatically formatted on display.</span>
        </div>

        <!-- Display Order -->
        <div class="form-group">
          <label for="display_order" class="form-label">Display Order</label>
          <input type="number" id="display_order" name="display_order" class="form-control" value="<?= e($_POST['display_order'] ?? '1') ?>" min="1" step="1">
          <span class="form-hint">Cards with lower numbers appear first (e.g. 1, 2, 3...).</span>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label for="status" class="form-label">Publication Status</label>
          <select id="status" name="status" class="form-control">
            <option value="active" <?= ($_POST['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active (Visible in homepage slider)</option>
            <option value="inactive" <?= ($_POST['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden from public site)</option>
          </select>
          <span class="form-hint">Set to Inactive if review is pending or archived.</span>
        </div>

        <!-- Reviewer Photo / Avatar (Optional) -->
        <div class="form-group" style="grid-column:1 / -1">
          <label for="image" class="form-label">Reviewer Avatar / Photo (Optional)</label>
          <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
          <span class="form-hint">Optional. Max 2MB. If omitted, an elegant avatar with the reviewer's initial letter is automatically generated.</span>
        </div>
      </div>

      <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--slate-100);display:flex;align-items:center;justify-content:flex-end;gap:12px">
        <a href="testimonials.php" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:14px">Save Testimonial</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
