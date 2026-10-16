<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getDbConnection();
$error = '';

$id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
if ($id <= 0) {
    setFlash('error', 'Invalid testimonial ID specified.');
    header('Location: testimonials.php', true, 303);
    exit;
}

// Fetch existing record
$stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
$stmt->execute([$id]);
$testimonial = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$testimonial) {
    setFlash('error', 'Testimonial record not found.');
    header('Location: testimonials.php', true, 303);
    exit;
}

// Handle POST request first (Before ANY HTML output or header.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token     = $_POST['csrf_token'] ?? '';
    $formToken = $_POST['form_token'] ?? '';

    if (!verifyCsrfToken($token)) {
        $error = 'Security token expired. Please refresh and try again.';
    } elseif (!validateAndConsumeFormToken($formToken, 'testimonial_edit_' . $id)) {
        setFlash('info', 'This update was already processed.');
        header('Location: testimonials.php', true, 303);
        exit;
    } else {
        $reviewerName = trim($_POST['reviewer_name'] ?? '');
        $reviewText   = trim($_POST['review_text'] ?? '');
        $location     = trim($_POST['location'] ?? '');
        $category     = trim($_POST['category'] ?? '');
        $order        = (int)($_POST['display_order'] ?? 1);
        $status       = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $removeImage  = isset($_POST['remove_image']);
        $currentImage = $testimonial['image'] ?? null;
        $imagePath    = $currentImage;
        $savedFile    = null;

        if (empty($reviewerName) || empty($reviewText)) {
            $error = 'Please complete all required fields (Reviewer Name, Feedback / Review Text).';
        } else {
            // Check if user requested to remove existing image
            if ($removeImage && !empty($currentImage)) {
                $fullOld = dirname(__DIR__) . '/' . ltrim($currentImage, '/');
                if (file_exists($fullOld) && is_file($fullOld)) {
                    @unlink($fullOld);
                }
                $imagePath = null;
            }

            // Handle new avatar upload if provided
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
                                // If there was an old image file, unlink it
                                if (!empty($currentImage)) {
                                    $fullOld = dirname(__DIR__) . '/' . ltrim($currentImage, '/');
                                    if (file_exists($fullOld) && is_file($fullOld)) {
                                        @unlink($fullOld);
                                    }
                                }
                                $imagePath = 'uploads/testimonials/' . $safeFilename;
                                $savedFile = $destPath;
                            } else {
                                $error = 'Failed to save new avatar image. Please check folder write permissions.';
                            }
                        }
                    }
                }
            }

            if (!$error) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE `testimonials` 
                        SET 
                          `reviewer_name` = ?,
                          `review_text`   = ?,
                          `location`      = ?,
                          `category`      = ?,
                          `image`         = ?,
                          `display_order` = ?,
                          `status`        = ?,
                          `updated_at`    = NOW()
                        WHERE `id` = ?
                    ");
                    $stmt->execute([
                        $reviewerName,
                        $reviewText,
                        !empty($location) ? $location : null,
                        !empty($category) ? $category : null,
                        $imagePath,
                        $order,
                        $status,
                        $id
                    ]);

                    setFlash('success', 'Testimonial from ' . htmlspecialchars($reviewerName, ENT_QUOTES, 'UTF-8') . ' successfully updated.');
                    header('Location: testimonials.php', true, 303);
                    exit;

                } catch (Throwable $e) {
                    // Clean up newly uploaded file if database update fails
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
$formToken = generateFormToken('testimonial_edit_' . $id);
$csrfToken = getCsrfToken();

$pageTitle = 'Edit Testimonial';
require_once __DIR__ . '/header.php';
?>

<div style="max-width:820px;margin:0 auto">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <div>
      <h2 style="font-size:22px;font-weight:800;color:var(--slate-900)">Edit Family Testimonial</h2>
      <p style="font-size:13px;color:var(--slate-500);margin-top:2px">
        Update reviewer details, testimonial copy, category or display order.
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
    <form method="POST" action="testimonial-edit.php?id=<?= $id ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <input type="hidden" name="form_token" value="<?= e($formToken) ?>">
      <input type="hidden" name="id" value="<?= $id ?>">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <!-- Reviewer Name -->
        <div class="form-group" style="grid-column:1 / -1">
          <label for="reviewer_name" class="form-label">Reviewer Name *</label>
          <input type="text" id="reviewer_name" name="reviewer_name" class="form-control" required value="<?= e($_POST['reviewer_name'] ?? $testimonial['reviewer_name']) ?>" placeholder="e.g. Brother Tariq &amp; Family, or Sarah M.">
          <span class="form-hint">Parent or student name as they would like it displayed.</span>
        </div>

        <!-- Location / Country -->
        <div class="form-group">
          <label for="location" class="form-label">Location / Country (Optional)</label>
          <input type="text" id="location" name="location" class="form-control" value="<?= e($_POST['location'] ?? $testimonial['location'] ?? '') ?>" placeholder="e.g. Dallas, Texas, USA or London, UK">
          <span class="form-hint">City, State or Country of residence.</span>
        </div>

        <!-- Learning Category -->
        <div class="form-group">
          <label for="category" class="form-label">Learning Category (Optional)</label>
          <input type="text" id="category" name="category" list="category_options" class="form-control" value="<?= e($_POST['category'] ?? $testimonial['category'] ?? '') ?>" placeholder="Select or type program category">
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
          <textarea id="review_text" name="review_text" class="form-control" rows="5" required placeholder="What the family shared..."><?= e($_POST['review_text'] ?? $testimonial['review_text']) ?></textarea>
          <span class="form-hint">Enter the authentic testimonial text. Quotes are automatically formatted on display.</span>
        </div>

        <!-- Display Order -->
        <div class="form-group">
          <label for="display_order" class="form-label">Display Order</label>
          <input type="number" id="display_order" name="display_order" class="form-control" value="<?= e($_POST['display_order'] ?? $testimonial['display_order']) ?>" min="1" step="1">
          <span class="form-hint">Cards with lower numbers appear first (e.g. 1, 2, 3...).</span>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label for="status" class="form-label">Publication Status</label>
          <?php $currentStatus = $_POST['status'] ?? $testimonial['status']; ?>
          <select id="status" name="status" class="form-control">
            <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active (Visible in homepage slider)</option>
            <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden from public site)</option>
          </select>
          <span class="form-hint">Set to Inactive to hide review without deleting.</span>
        </div>

        <!-- Reviewer Photo / Avatar (Optional) -->
        <div class="form-group" style="grid-column:1 / -1">
          <label for="image" class="form-label">Reviewer Avatar / Photo (Optional)</label>
          <?php if (!empty($testimonial['image'])): ?>
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;padding:12px;background:#f8fafc;border-radius:12px;border:1px solid var(--slate-200)">
              <img src="../<?= e($testimonial['image']) ?>" alt="Current Avatar" style="width:50px;height:50px;border-radius:50%;object-fit:cover;border:2px solid #cbd5e1">
              <div>
                <strong style="display:block;font-size:13px;color:var(--slate-800)">Current Photo</strong>
                <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#ef4444;cursor:pointer;margin-top:4px">
                  <input type="checkbox" name="remove_image" value="1">
                  <span>Remove this photo (fall back to letter initial avatar)</span>
                </label>
              </div>
            </div>
          <?php endif; ?>
          <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
          <span class="form-hint">Optional. Max 2MB. Upload a new image to replace the current avatar.</span>
        </div>
      </div>

      <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--slate-100);display:flex;align-items:center;justify-content:flex-end;gap:12px">
        <a href="testimonials.php" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:14px">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
