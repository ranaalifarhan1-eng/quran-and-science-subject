<?php
$pageTitle = 'Add Instructor Profile';
require_once __DIR__ . '/header.php';

$pdo = getDbConnection();
$error = '';

// Get default next display order
$nextOrder = 1;
try {
    $stmt = $pdo->query("SELECT MAX(display_order) FROM teachers");
    $max = $stmt->fetchColumn();
    if ($max !== false && $max !== null) {
        $nextOrder = (int)$max + 1;
    }
} catch (Throwable $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $error = 'Security token expired. Please refresh and try again.';
    } else {
        $name        = trim($_POST['name'] ?? '');
        $role        = trim($_POST['role'] ?? '');
        $category    = trim($_POST['category'] ?? 'Quran Learning');
        $gender      = in_array($_POST['gender'] ?? '', ['male', 'female', 'unspecified']) ? $_POST['gender'] : 'unspecified';
        $description = trim($_POST['short_description'] ?? '');
        $specialties = trim($_POST['specialties'] ?? '');
        $order       = (int)($_POST['display_order'] ?? 1);
        $status      = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $imagePath   = null;

        if (empty($name) || empty($role) || empty($description)) {
            $error = 'Please complete all required fields (Name, Role/Title, Short Description).';
        } else {
            // Handle image upload if provided
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Image upload failed. Error code: ' . $_FILES['image']['error'];
                } else {
                    $fileTmp  = $_FILES['image']['tmp_name'];
                    $fileSize = $_FILES['image']['size'];
                    $fileName = $_FILES['image']['name'];

                    if ($fileSize > (3 * 1024 * 1024)) {
                        $error = 'Image file size exceeds the 3MB maximum limit.';
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
                            $safeFilename = 'teacher_' . bin2hex(random_bytes(8)) . '.' . $targetExt;
                            $uploadDir = dirname(__DIR__) . '/uploads/teachers/';

                            if (!is_dir($uploadDir)) {
                                @mkdir($uploadDir, 0755, true);
                            }

                            $destPath = $uploadDir . $safeFilename;
                            if (move_uploaded_file($fileTmp, $destPath)) {
                                $imagePath = 'uploads/teachers/' . $safeFilename;
                            } else {
                                $error = 'Failed to save image to server directory. Please check folder permissions.';
                            }
                        }
                    }
                }
            }

            if (!$error) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO teachers 
                        (name, role, category, gender, short_description, specialties, image, display_order, status, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([
                        $name,
                        $role,
                        $category,
                        $gender,
                        $description,
                        $specialties,
                        $imagePath,
                        $order,
                        $status
                    ]);

                    setFlash('success', 'Instructor "' . $name . '" created successfully!');
                    header('Location: teachers.php');
                    exit;
                } catch (Throwable $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

$csrfToken = getCsrfToken();
?>

<div style="max-width:800px">
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">New Instructor Details</h2>
      <a href="teachers.php" class="btn btn-outline btn-sm">&larr; Back to List</a>
    </div>

    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-error">
          <span>⚠</span>
          <span><?= e($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="teacher-add.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:18px;margin-bottom:18px">
          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="name">Instructor Name *</label>
            <input type="text" id="name" name="name" required placeholder="e.g. Qari Abdullah / Dr. Imran Khan" value="<?= e($_POST['name'] ?? '') ?>" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
          </div>

          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="role">Role / Headline Title *</label>
            <input type="text" id="role" name="role" required placeholder="e.g. Quran & Tajweed Tutor / O & A Level Math Specialist" value="<?= e($_POST['role'] ?? '') ?>" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:18px;margin-bottom:18px">
          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="category">Subject Stream / Category</label>
            <select id="category" name="category" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;background:#fff">
              <?php 
                $categories = ['Quran Learning', 'Sisters & Kids', 'Memorization', 'Academic & Science', 'Mathematics', 'General Tutoring'];
                $selectedCat = $_POST['category'] ?? 'Quran Learning';
                foreach ($categories as $cat):
              ?>
                <option value="<?= e($cat) ?>" <?= $selectedCat === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="gender">Instructor Gender</label>
            <select id="gender" name="gender" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;background:#fff">
              <option value="unspecified" <?= ($_POST['gender'] ?? '') === 'unspecified' ? 'selected' : '' ?>>Unspecified</option>
              <option value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male Teacher</option>
              <option value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female Teacher</option>
            </select>
          </div>

          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="display_order">Display Order Number</label>
            <input type="number" id="display_order" name="display_order" value="<?= (int)($_POST['display_order'] ?? $nextOrder) ?>" min="1" max="999" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
          </div>
        </div>

        <div style="margin-bottom:18px">
          <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="short_description">Short Profile Description *</label>
          <textarea id="short_description" name="short_description" rows="3" required placeholder="Describe the instructor's background, teaching style, and primary student focus." style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;resize:vertical"><?= e($_POST['short_description'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom:18px">
          <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="specialties">Specialties / Tags (comma-separated)</label>
          <input type="text" id="specialties" name="specialties" placeholder="e.g. Tajweed Rules, Makharij, Recitation" value="<?= e($_POST['specialties'] ?? '') ?>" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
          <span style="font-size:12px;color:var(--slate-500);display:block;margin-top:4px">Shown as badge chips on the homepage card.</span>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:18px;margin-bottom:24px;padding:18px;background:#f8fafc;border-radius:12px;border:1px solid var(--slate-200)">
          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="image">Profile Photo (Optional)</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" style="font-size:13px">
            <span style="font-size:11.5px;color:var(--slate-500);display:block;margin-top:4px">JPG, PNG, WebP up to 3MB. If omitted, a clean placeholder avatar is used.</span>
          </div>

          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="status">Profile Visibility Status</label>
            <select id="status" name="status" style="width:100%;padding:10px 12px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;background:#fff">
              <option value="active" <?= ($_POST['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active (Visible publicly)</option>
              <option value="inactive" <?= ($_POST['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden from public)</option>
            </select>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px">
          <button type="submit" class="btn btn-primary">Save Instructor Profile</button>
          <a href="teachers.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
