<?php
// admin/users/add.php
// Line 1, column 1 — no blank lines above, no BOM
if (session_status() === PHP_SESSION_NONE) session_start();
require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('users');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $role     = trim($_POST['role']     ?? 'admin');
    $status   = isset($_POST['status'])          ? 1 : 0;
    $two_fa   = isset($_POST['two_fa_enabled'])   ? 1 : 0;
    $notes    = trim($_POST['notes']    ?? '');
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $allowedRoles = ['superadmin', 'admin', 'editor', 'viewer'];

    // ── Validation ────────────────────────────────────────────
    if (empty($name))  $errors[] = 'Full name is required.';
    if (empty($email)) $errors[] = 'Email address is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!in_array($role, $allowedRoles)) $errors[] = 'Invalid role selected.';
    if (empty($password))          $errors[] = 'Password is required.';
    elseif (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    elseif ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!empty($phone) && !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone))
        $errors[] = 'Enter a valid phone number.';

    // ── Duplicate email check ─────────────────────────────────
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'This email is already registered.';
        $stmt->close();
    }

    // ── Avatar Upload ─────────────────────────────────────────
    $avatar = null; // stays null if no file uploaded

    if (empty($errors) && !empty($_FILES['avatar']['name'])) {
        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime     = mime_content_type($_FILES['avatar']['tmp_name']);

        if (!in_array($mime, $allowed)) {
            $errors[] = 'Invalid avatar type. Allowed: JPG, PNG, WEBP, GIF.';
        } elseif ($_FILES['avatar']['size'] > 1 * 1024 * 1024) {
            $errors[] = 'Avatar must be under 1MB.';
        } else {
            $dir = '../../assets/img/avatars/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $fname = 'avatar_' . uniqid() . '.webp';
            $src   = $_FILES['avatar']['tmp_name'];

            switch ($mime) {
                case 'image/jpeg': $img = imagecreatefromjpeg($src); break;
                case 'image/png':
                    $img = imagecreatefrompng($src);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                    break;
                case 'image/gif': $img = imagecreatefromgif($src);  break;
                default:          $img = imagecreatefromwebp($src); break;
            }

            // Square crop to 200×200
            $ow  = imagesx($img);
            $oh  = imagesy($img);
            $min = min($ow, $oh);
            $cx  = (int)(($ow - $min) / 2);
            $cy  = (int)(($oh - $min) / 2);
            $sq  = imagecreatetruecolor(200, 200);
            imagecopyresampled($sq, $img, 0, 0, $cx, $cy, 200, 200, $min, $min);
            imagewebp($sq, $dir . $fname, 85);
            imagedestroy($img);
            imagedestroy($sq);

            $avatar = 'assets/img/avatars/' . $fname;
        }
    }

    // ── Insert into DB ────────────────────────────────────────
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            INSERT INTO admin_users
                (name, email, password, role, phone, avatar, status, two_fa_enabled, notes, created_at, updated_at)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        // FIX: avatar is nullable — use NULL in SQL when empty
        // bind types: s s s s s s i i s
        $avatarVal = $avatar ?? null; // explicit null
        $stmt->bind_param(
            "ssssssiis",
            $name,
            $email,
            $hashed,
            $role,
            $phone,
            $avatarVal,
            $status,
            $two_fa,
            $notes
        );

        if ($stmt->execute()) {
            $newId = (int)$stmt->insert_id;
            $stmt->close();

            // Activity log — sanitize IP
            $ip     = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
            $detail = $conn->real_escape_string("Created user: $name ($email) with role: $role");
            $conn->query("
                INSERT INTO admin_activity_log (user_id, action, detail, ip, created_at)
                VALUES ($newId, 'user_created', '$detail', '$ip', NOW())
            ");

            header("Location: ./?msg=added");
            exit;
        } else {
            $errors[] = 'Database error: ' . htmlspecialchars($stmt->error);
            $stmt->close();
        }
    }
}

// ── Helper: safe POST re-population ──────────────────────────
$p = fn($k, $default = '') => htmlspecialchars($_POST[$k] ?? $default);

$pageTitle  = 'Add Admin User';
$activePage = 'users-add';
$assetBase  = '../';

$extraCSS = '
<style>
    .form-label { font-size: 0.72rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 0.45rem; }
    .form-control, .form-select { font-size: 0.88rem; padding: 0.6rem 1rem; border-color: #dee2e6; border-radius: 0.5rem; transition: border-color .2s, box-shadow .2s; }
    .form-control:focus, .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 3px rgba(13,110,253,.12); }
    .input-group .form-control { border-radius: 0 0.5rem 0.5rem 0 !important; }
    .input-group-text { border-color: #dee2e6; }

    /* Avatar uploader */
    .avatar-upload-wrap { position: relative; width: 110px; height: 110px; margin: 0 auto 1rem; }
    .avatar-upload-wrap img,
    .avatar-upload-wrap .avatar-placeholder {
        width: 110px; height: 110px; border-radius: 50%;
        object-fit: cover; border: 3px solid #e9ecef;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-weight: 800; color: #fff;
        background: #6c757d;
    }
    .avatar-upload-btn {
        position: absolute; bottom: 4px; right: 4px;
        width: 30px; height: 30px; border-radius: 50%;
        background: #0d6efd; color: #fff; border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 0.75rem; transition: background .2s;
    }
    .avatar-upload-btn:hover { background: #0b5ed7; }

    /* Password */
    .pw-bar { height: 5px; border-radius: 4px; background: #e9ecef; overflow: hidden; margin-top: 8px; }
    .pw-bar-fill { height: 100%; border-radius: 4px; transition: width .4s, background .4s; width: 0%; }
    .req-item { font-size: 0.75rem; display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: #adb5bd; transition: color .2s; }
    .req-item.met { color: #198754; }
    .req-item i { width: 14px; }

    /* Role cards */
    .role-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .role-card { border: 2px solid #e9ecef; border-radius: 0.75rem; padding: 0.85rem 1rem; cursor: pointer; transition: all .2s; background: #fff; }
    .role-card:hover { border-color: #86b7fe; background: #f8fbff; }
    .role-card.selected { border-color: #0d6efd; background: #f0f6ff; }
    .role-card .role-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; margin-bottom: 0.5rem; }
    .role-card .role-name { font-size: 0.82rem; font-weight: 700; color: #212529; }
    .role-card .role-desc { font-size: 0.7rem; color: #6c757d; margin-top: 2px; }

    /* Section card header */
    .section-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; }
</style>
';

require_once '../include/head.php';
?>

<div class="main-wrapper">
    <?php require_once '../include/header.php'; ?>
    <?php require_once '../include/sidebar.php'; ?>

    <div class="page-wrapper" style="background-color:#f4f6f9;min-height:100vh;">
        <div class="content container-fluid pt-4 pb-5">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bolder text-dark mb-1">Add Admin User</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="./" class="text-muted text-decoration-none">Users</a></li>
                            <li class="breadcrumb-item active text-secondary fw-medium">Add New</li>
                        </ol>
                    </nav>
                </div>
                <a href="./" class="btn btn-light rounded-pill px-4 py-2 shadow-sm fw-semibold border mt-3 mt-md-0 d-inline-flex align-items-center gap-2">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <!-- Validation Errors -->
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-start gap-3 mb-4">
                <i class="fa fa-exclamation-triangle mt-1 fs-5"></i>
                <div>
                    <div class="fw-bold mb-1">Please fix the following:</div>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="addUserForm">
            <div class="row g-4">

                <!-- ══ LEFT COLUMN ════════════════════════════ -->
                <div class="col-xl-8 col-lg-7">

                    <!-- Account Details -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-primary-subtle text-primary"><i class="fa fa-user"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Account Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-user"></i></span>
                                        <input type="text" name="name" id="nameInput"
                                               class="form-control border-start-0 ps-0"
                                               placeholder="John Smith"
                                               value="<?= $p('name') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="email"
                                               class="form-control border-start-0 ps-0"
                                               placeholder="admin@rkhospital.com"
                                               value="<?= $p('email') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-phone"></i></span>
                                        <input type="tel" name="phone"
                                               class="form-control border-start-0 ps-0"
                                               placeholder="+91 98765 43210"
                                               value="<?= $p('phone') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Account Status</label>
                                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-3">
                                            <input class="form-check-input" type="checkbox" name="status" id="statusToggle"
                                                   style="width:2.75rem;height:1.4rem;cursor:pointer;"
                                                   <?= ($p('status', '1') !== '0') ? 'checked' : '' ?>>
                                            <div>
                                                <div class="fw-semibold text-dark small" id="statusLabel">Active</div>
                                                <div class="text-muted" style="font-size:0.7rem;">User can log in</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">
                                        Internal Notes
                                        <span class="text-muted fw-normal text-lowercase">(optional)</span>
                                    </label>
                                    <textarea name="notes" class="form-control" rows="2"
                                              placeholder="e.g. Managing blog section only..."><?= $p('notes') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-warning-subtle text-warning"><i class="fa fa-shield-alt"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Role &amp; Permissions</h6>
                        </div>
                        <div class="card-body p-4">
                            <input type="hidden" name="role" id="roleInput" value="<?= $p('role', 'admin') ?>">
                            <div class="role-cards">
                                <?php
                                $roles = [
                                    'superadmin' => ['icon' => 'fa-crown',       'bg' => 'bg-danger-subtle',  'color' => 'text-danger',  'name' => 'Super Admin', 'desc' => 'Full access to all modules & settings'],
                                    'admin'      => ['icon' => 'fa-user-shield',  'bg' => 'bg-primary-subtle', 'color' => 'text-primary', 'name' => 'Admin',       'desc' => 'Manage content, users & media'],
                                    'editor'     => ['icon' => 'fa-pen-nib',      'bg' => 'bg-success-subtle', 'color' => 'text-success', 'name' => 'Editor',      'desc' => 'Create & edit posts and pages'],
                                    'viewer'     => ['icon' => 'fa-eye',          'bg' => 'bg-info-subtle',    'color' => 'text-info',    'name' => 'Viewer',      'desc' => 'Read-only access to the panel'],
                                ];
                                $currentRole = $_POST['role'] ?? 'admin';
                                foreach ($roles as $val => $r):
                                ?>
                                <div class="role-card <?= $currentRole === $val ? 'selected' : '' ?>"
                                     onclick="selectRole('<?= $val ?>', this)">
                                    <div class="role-icon <?= $r['bg'] ?> <?= $r['color'] ?>">
                                        <i class="fa <?= $r['icon'] ?>"></i>
                                    </div>
                                    <div class="role-name"><?= $r['name'] ?></div>
                                    <div class="role-desc"><?= $r['desc'] ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Permissions Matrix -->
                            <div class="mt-4 p-3 bg-light rounded-3 border" id="permMatrix">
                                <div class="small fw-bold text-dark mb-3 text-uppercase" style="letter-spacing:.5px;">
                                    Permissions Preview
                                </div>
                                <div class="row g-2" id="permRows"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-danger-subtle text-danger"><i class="fa fa-lock"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Set Password</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                        <input type="password" name="password" id="passwordInput"
                                               class="form-control border-start-0 border-end-0 ps-0"
                                               placeholder="Min 8 characters" required>
                                        <button type="button" class="input-group-text bg-light border-start-0 text-muted"
                                                id="togglePw">
                                            <i class="fa fa-eye" id="eyeIcon1"></i>
                                        </button>
                                    </div>
                                    <div class="pw-bar mt-2"><div class="pw-bar-fill" id="pwBar"></div></div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span style="font-size:.7rem;" class="text-muted">Strength</span>
                                        <span style="font-size:.7rem;" class="fw-bold" id="pwLabel">—</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                        <input type="password" name="confirm_password" id="confirmInput"
                                               class="form-control border-start-0 border-end-0 ps-0"
                                               placeholder="Re-enter password" required>
                                        <button type="button" class="input-group-text bg-light border-start-0 text-muted"
                                                id="toggleCfm">
                                            <i class="fa fa-eye" id="eyeIcon2"></i>
                                        </button>
                                    </div>
                                    <div id="matchHint" class="small mt-2 fw-medium" style="display:none;min-height:20px;"></div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3">
                                        <div class="row g-2">
                                            <div class="col-6"><div class="req-item" id="req-len"><i class="fa fa-times-circle"></i> 8+ characters</div></div>
                                            <div class="col-6"><div class="req-item" id="req-upper"><i class="fa fa-times-circle"></i> Uppercase letter</div></div>
                                            <div class="col-6"><div class="req-item" id="req-num"><i class="fa fa-times-circle"></i> Number (0-9)</div></div>
                                            <div class="col-6"><div class="req-item" id="req-special"><i class="fa fa-times-circle"></i> Special char (!@#$)</div></div>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex align-items-center justify-content-between">
                                        <span class="small text-muted fw-medium">Generate a secure password:</span>
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold"
                                                id="generatePwBtn">
                                            <i class="fa fa-magic me-1"></i> Generate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /col-xl-8 -->

                <!-- ══ RIGHT SIDEBAR ══════════════════════════ -->
                <div class="col-xl-4 col-lg-5">

                    <!-- Avatar Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-info-subtle text-info"><i class="fa fa-camera"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Avatar Photo</h6>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="avatar-upload-wrap" id="avatarWrap">
                                <div class="avatar-placeholder" id="avatarPlaceholder" style="background:#6c757d;">
                                    <span id="avatarInitial">?</span>
                                </div>
                                <img id="avatarPreview" src="" alt=""
                                     style="display:none;width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid #e9ecef;">
                                <div class="avatar-upload-btn" onclick="document.getElementById('avatarInput').click()">
                                    <i class="fa fa-camera"></i>
                                </div>
                            </div>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none">
                            <p class="text-muted small mb-0 mt-1">JPG, PNG, WEBP — max 1MB</p>
                            <p class="text-muted mb-0" style="font-size:.7rem;">Auto-cropped square &amp; converted to WebP</p>
                        </div>
                    </div>

                    <!-- Security Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-success-subtle text-success"><i class="fa fa-shield-alt"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Security</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                <div>
                                    <div class="fw-semibold text-dark small">Two-Factor Auth (2FA)</div>
                                    <div class="text-muted" style="font-size:.7rem;">Require captcha on login</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="two_fa_enabled" id="twoFaToggle"
                                           style="width:2.75rem;height:1.4rem;cursor:pointer;"
                                           <?= isset($_POST['two_fa_enabled']) ? 'checked' : '' ?>>
                                </div>
                            </div>
                            <div class="alert alert-warning border-0 bg-warning-subtle rounded-3 p-3 mt-3 small d-flex align-items-start gap-2"
                                 id="twoFaNote" style="display:none;">
                                <i class="fa fa-info-circle mt-1 text-warning"></i>
                                <span>User will be asked to solve a math captcha on every login.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm mb-2">
                                <i class="fa fa-user-plus me-2"></i> Create User
                            </button>
                            <a href="./" class="btn btn-light w-100 rounded-pill py-2 fw-semibold border">Cancel</a>
                        </div>
                    </div>

                </div><!-- /col-xl-4 -->

            </div>
            </form>

        </div>
    </div>
</div>

<script>
// ── Avatar preview ────────────────────────────────────────────
document.getElementById('nameInput').addEventListener('input', function () {
    const v = this.value.trim();
    document.getElementById('avatarInitial').textContent = v ? v[0].toUpperCase() : '?';
});
document.getElementById('avatarInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarPreview').src          = e.target.result;
        document.getElementById('avatarPreview').style.display = 'block';
        document.getElementById('avatarPlaceholder').style.display = 'none';
    };
    reader.readAsDataURL(file);
});

// ── Role selection & permissions matrix ──────────────────────
const perms = {
    superadmin: { Dashboard: true,  Users: true,  Doctors: true,  Blogs: true,  Services: true,  Settings: true,  Logs: true  },
    admin:      { Dashboard: true,  Users: true,  Doctors: true,  Blogs: true,  Services: true,  Settings: false, Logs: false },
    editor:     { Dashboard: true,  Users: false, Doctors: true,  Blogs: true,  Services: true,  Settings: false, Logs: false },
    viewer:     { Dashboard: true,  Users: false, Doctors: false, Blogs: false, Services: false, Settings: false, Logs: false },
};
function selectRole(val, el) {
    document.getElementById('roleInput').value = val;
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    renderPerms(val);
}
function renderPerms(role) {
    const map  = perms[role] || {};
    const cont = document.getElementById('permRows');
    cont.innerHTML = Object.entries(map).map(([mod, allowed]) => `
        <div class="col-6 col-sm-4 col-md-3">
            <div class="p-2 rounded-3 text-center border ${allowed ? 'bg-success-subtle border-success-subtle' : 'bg-light border-light'}">
                <div style="font-size:.7rem;font-weight:700;color:${allowed ? '#198754' : '#adb5bd'};">
                    <i class="fa ${allowed ? 'fa-check' : 'fa-times'} me-1"></i>${mod}
                </div>
            </div>
        </div>`).join('');
}
renderPerms(document.getElementById('roleInput').value || 'admin');

// ── Password visibility toggle ────────────────────────────────
function toggleVis(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const show = inp.type === 'password';
    inp.type       = show ? 'text'            : 'password';
    icon.className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
}
document.getElementById('togglePw').addEventListener('click',  () => toggleVis('passwordInput', 'eyeIcon1'));
document.getElementById('toggleCfm').addEventListener('click', () => toggleVis('confirmInput',  'eyeIcon2'));

// ── Password strength meter ───────────────────────────────────
function checkStrength(val) {
    const checks = {
        'req-len':     val.length >= 8,
        'req-upper':   /[A-Z]/.test(val),
        'req-num':     /[0-9]/.test(val),
        'req-special': /[!@#$%^&*()\-_=+{};:,<.>]/.test(val),
    };
    let score = Object.values(checks).filter(Boolean).length;
    Object.entries(checks).forEach(([id, met]) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('met', met);
        el.querySelector('i').className = met ? 'fa fa-check-circle' : 'fa fa-times-circle';
    });
    const levels = [
        { pct: '10%',  color: '#dc3545', label: 'Very Weak' },
        { pct: '30%',  color: '#fd7e14', label: 'Weak'      },
        { pct: '60%',  color: '#ffc107', label: 'Fair'      },
        { pct: '80%',  color: '#0dcaf0', label: 'Good'      },
        { pct: '100%', color: '#198754', label: 'Strong'    },
    ];
    const lvl          = levels[score] ?? levels[0];
    const bar          = document.getElementById('pwBar');
    const lbl          = document.getElementById('pwLabel');
    bar.style.width      = val ? lvl.pct   : '0%';
    bar.style.background = lvl.color;
    lbl.textContent      = val ? lvl.label : '—';
    lbl.style.color      = val ? lvl.color : '#adb5bd';
}
document.getElementById('passwordInput').addEventListener('input', function () {
    checkStrength(this.value);
    checkMatch();
});
document.getElementById('confirmInput').addEventListener('input', checkMatch);

function checkMatch() {
    const pw   = document.getElementById('passwordInput').value;
    const cfm  = document.getElementById('confirmInput').value;
    const hint = document.getElementById('matchHint');
    if (!cfm || !pw) { hint.style.display = 'none'; return; }
    hint.style.display = 'block';
    hint.innerHTML = pw === cfm
        ? '<i class="fa fa-check-circle text-success me-1"></i><span class="text-success">Passwords match</span>'
        : '<i class="fa fa-times-circle text-danger me-1"></i><span class="text-danger">Passwords do not match</span>';
}

// ── Password generator ────────────────────────────────────────
document.getElementById('generatePwBtn').addEventListener('click', function () {
    const lower   = 'abcdefghijklmnopqrstuvwxyz';
    const upper   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const digits  = '0123456789';
    const special = '!@#$%^&*()-_=+';
    const all     = lower + upper + digits + special;
    let pw = '';
    // Guarantee at least one of each required type
    pw += lower  [Math.floor(Math.random() * lower.length)];
    pw += upper  [Math.floor(Math.random() * upper.length)];
    pw += digits [Math.floor(Math.random() * digits.length)];
    pw += special[Math.floor(Math.random() * special.length)];
    for (let i = 4; i < 14; i++) pw += all[Math.floor(Math.random() * all.length)];
    // Shuffle
    pw = pw.split('').sort(() => Math.random() - 0.5).join('');

    document.getElementById('passwordInput').value         = pw;
    document.getElementById('confirmInput').value          = pw;
    document.getElementById('passwordInput').type          = 'text';
    document.getElementById('eyeIcon1').className          = 'fa fa-eye-slash';
    checkStrength(pw);
    checkMatch();

    navigator.clipboard.writeText(pw).then(() => {
        this.innerHTML = '<i class="fa fa-check me-1 text-success"></i> Copied!';
        setTimeout(() => { this.innerHTML = '<i class="fa fa-magic me-1"></i> Generate'; }, 2000);
    });
});

// ── Status toggle label ───────────────────────────────────────
document.getElementById('statusToggle').addEventListener('change', function () {
    document.getElementById('statusLabel').textContent = this.checked ? 'Active' : 'Inactive';
});

// ── 2FA hint ──────────────────────────────────────────────────
document.getElementById('twoFaToggle').addEventListener('change', function () {
    document.getElementById('twoFaNote').style.display = this.checked ? 'flex' : 'none';
});
</script>

<?php require_once '../include/footer.php'; ?>