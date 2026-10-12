<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getDbConnection();
$error = '';

$teacherId = (int)($_GET['id'] ?? 0);
if ($teacherId <= 0) {
    setFlash('error', 'Invalid instructor ID specified.');
    header('Location: teachers.php', true, 303);
    exit;
}

// Fetch teacher record
$stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
$stmt->execute([$teacherId]);
$teacher = $stmt->fetch();

if (!$teacher) {
    setFlash('error', 'Instructor profile not found.');
    header('Location: teachers.php', true, 303);
    exit;
}

// Handle POST request first (Before ANY HTML output or header.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token     = $_POST['csrf_token'] ?? '';
    $formToken = $_POST['form_token'] ?? '';

    if (!verifyCsrfToken($token)) {
        $error = 'Security token expired. Please refresh and try again.';
    } elseif (!validateAndConsumeFormToken($formToken, 'teacher_edit_' . $teacherId)) {
        setFlash('info', 'This profile update was already processed.');
        header('Location: teachers.php', true, 303);
        exit;
    } else {
        $name        = trim($_POST['name'] ?? '');
        $role        = trim($_POST['role'] ?? '');
        $category    = trim($_POST['category'] ?? 'Quran Learning');
        $gender      = in_array($_POST['gender'] ?? '', ['male', 'female', 'unspecified']) ? $_POST['gender'] : 'unspecified';
        $description = trim($_POST['short_description'] ?? '');
        $specialties = trim($_POST['specialties'] ?? '');
        $order       = (int)($_POST['display_order'] ?? 1);
        $status      = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $removeImage = !empty($_POST['remove_image']);
        $imagePath   = $teacher['image'];
        $newSavedFile = null;

        if (empty($name) || empty($role) || empty($description)) {
            $error = 'Please complete all required fields (Name, Role/Title, Short Description).';
        } else {
            // Check if user requested removing current image
            if ($removeImage && !empty($imagePath)) {
                $oldFile = dirname(__DIR__) . '/' . ltrim($imagePath, '/');
                if (file_exists($oldFile) && is_file($oldFile)) {
                    @unlink($oldFile);
                }
                $imagePath = null;
            }

            // Handle new image upload if provided
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
                                $newSavedFile = $destPath;
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
                        UPDATE teachers SET 
                            name = ?,
                            role = ?,
                            category = ?,
                            gender = ?,
                            short_description = ?,
                            specialties = ?,
                            image = ?,
                            display_order = ?,
                            status = ?,
                            updated_at = NOW()
                        WHERE id = ?
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
                        $status,
                        $teacherId
                    ]);

                    // Delete previous image file only after successful DB update
                    if ($newSavedFile && !empty($teacher['image']) && $teacher['image'] !== $imagePath) {
                        $oldFile = dirname(__DIR__) . '/' . ltrim($teacher['image'], '/');
                        if (file_exists($oldFile) && is_file($oldFile)) {
                            @unlink($oldFile);
                        }
                    }

                    // Post-Redirect-Get with HTTP 303 See Other
                    setFlash('success', 'Teacher profile updated successfully.');
                    header('Location: teachers.php?updated=1', true, 303);
                    exit;
                } catch (Throwable $e) {
                    // Clean up newly uploaded image if DB update failed
                    if ($newSavedFile && file_exists($newSavedFile)) {
                        @unlink($newSavedFile);
                    }
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Generate security tokens for the form
$csrfToken = getCsrfToken();
$formToken = generateFormToken('teacher_edit_' . $teacherId);

// Include HTML layout Header ONLY AFTER all POST/redirect logic is resolved
$pageTitle = 'Edit Instructor Profile';
require_once __DIR__ . '/header.php';
?>

<div style="max-width:800px">
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Edit Instructor Details</h2>
      <a href="teachers.php" class="btn btn-outline btn-sm">&larr; Back to List</a>
    </div>

    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-error">
          <span>⚠</span>
          <span><?= e($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="teacher-edit.php?id=<?= $teacherId ?>" enctype="multipart/form-data" id="teacherEditForm">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="form_token" value="<?= e($formToken) ?>">

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:18px;margin-bottom:18px">
          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="name">Instructor Name *</label>
            <input type="text" id="name" name="name" required value="<?= e($_POST['name'] ?? $teacher['name']) ?>" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
          </div>

          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="role">Role / Headline Title *</label>
            <input type="text" id="role" name="role" required value="<?= e($_POST['role'] ?? $teacher['role']) ?>" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:18px;margin-bottom:18px">
          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="category">Subject Stream / Category</label>
            <select id="category" name="category" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;background:#fff">
              <?php 
                $categories = ['Quran Learning', 'Sisters & Kids', 'Memorization', 'Academic & Science', 'Mathematics', 'General Tutoring'];
                $selectedCat = $_POST['category'] ?? $teacher['category'];
                foreach ($categories as $cat):
              ?>
                <option value="<?= e($cat) ?>" <?= $selectedCat === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="gender">Instructor Gender</label>
            <select id="gender" name="gender" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;background:#fff">
              <?php $selectedGender = $_POST['gender'] ?? $teacher['gender']; ?>
              <option value="unspecified" <?= $selectedGender === 'unspecified' ? 'selected' : '' ?>>Unspecified</option>
              <option value="male" <?= $selectedGender === 'male' ? 'selected' : '' ?>>Male Teacher</option>
              <option value="female" <?= $selectedGender === 'female' ? 'selected' : '' ?>>Female Teacher</option>
            </select>
          </div>

          <div>
            <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="display_order">Display Order Number</label>
            <input type="number" id="display_order" name="display_order" value="<?= (int)($_POST['display_order'] ?? $teacher['display_order']) ?>" min="1" max="999" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
          </div>
        </div>

        <div style="margin-bottom:18px">
          <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="short_description">Short Profile Description *</label>
          <textarea id="short_description" name="short_description" rows="3" required style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;resize:vertical"><?= e($_POST['short_description'] ?? $teacher['short_description']) ?></textarea>
        </div>

        <div style="margin-bottom:18px">
          <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="specialties">Specialties / Tags (comma-separated)</label>
          <input type="text" id="specialties" name="specialties" value="<?= e($_POST['specialties'] ?? $teacher['specialties']) ?>" style="width:100%;padding:11px 13px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit">
        </div>

        <!-- Image Management -->
        <div style="margin-bottom:24px;padding:18px;background:#f8fafc;border-radius:12px;border:1px solid var(--slate-200)">
          <div style="display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap">
            <?php if (!empty($teacher['image'])): ?>
              <div style="text-align:center">
                <img src="../<?= e($teacher['image']) ?>" alt="<?= e($teacher['name']) ?>" style="width:90px;height:90px;border-radius:12px;object-fit:cover;border:1px solid var(--slate-200);display:block">
                <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#991b1b;margin-top:8px;cursor:pointer">
                  <input type="checkbox" name="remove_image" value="1">
                  Remove photo
                </label>
              </div>
            <?php endif; ?>

            <div style="flex:1;min-width:240px">
              <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="image">
                <?= !empty($teacher['image']) ? 'Replace Profile Photo (Optional)' : 'Upload Profile Photo (Optional)' ?>
              </label>
              <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" style="font-size:13px">
              <span style="font-size:11.5px;color:var(--slate-500);display:block;margin-top:4px">JPG, PNG, WebP up to 3MB.</span>
            </div>

            <div style="min-width:180px">
              <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px" for="status">Profile Visibility</label>
              <select id="status" name="status" style="width:100%;padding:10px 12px;border:1.5px solid var(--slate-200);border-radius:9px;font-size:14px;font-family:inherit;background:#fff">
                <?php $currentStatus = $_POST['status'] ?? $teacher['status']; ?>
                <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active (Visible publicly)</option>
                <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden from public)</option>
              </select>
            </div>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px">
          <button type="submit" id="saveTeacherBtn" class="btn btn-primary">Update Instructor Profile</button>
          <a href="teachers.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('teacherEditForm');
  var btn = document.getElementById('saveTeacherBtn');
  if (!form || !btn) return;

  form.addEventListener('submit', function(e) {
    if (form.getAttribute('data-submitting') === 'true') {
      e.preventDefault();
      return false;
    }
    if (form.checkValidity && !form.checkValidity()) {
      return;
    }
    form.setAttribute('data-submitting', 'true');
    setTimeout(function() {
      btn.disabled = true;
      btn.textContent = 'Saving...';
      btn.style.opacity = '0.75';
      btn.style.cursor = 'not-allowed';
    }, 0);
  });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
