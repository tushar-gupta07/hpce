<?php
// admin/users/edit.php
// Line 1, col 1 — no blank lines above, no BOM, save as UTF-8
if (session_status() === PHP_SESSION_NONE) session_start();
require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('users');

// ── Fetch user via prepared statement ─────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: ./");
    exit;
}

$errors = [];

// ── POST Handler ──────────────────────────────────────────────
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
    if (!empty($password)) {
        if (strlen($password) < 8)      $errors[] = 'New password must be at least 8 characters.';
        elseif ($password !== $confirm)  $errors[] = 'Passwords do not match.';
    }
    if (!empty($phone) && !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone))
        $errors[] = 'Enter a valid phone number.';

    // ── Duplicate email check (prepared statement) ────────────
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ? LIMIT 1");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Email is already used by another user.';
        $stmt->close();
    }

    // ── Avatar Upload ─────────────────────────────────────────
    $avatar = $user['avatar'] ?? null; // default: keep existing avatar

    if (empty($errors) && !empty($_FILES['avatar']['name'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime    = mime_content_type($_FILES['avatar']['tmp_name']);

        if (!in_array($mime, $allowed)) {
            $errors[] = 'Invalid avatar type. Allowed: JPG, PNG, WEBP, GIF.';
        } elseif ($_FILES['avatar']['size'] > 1 * 1024 * 1024) {
            $errors[] = 'Avatar must be under 1MB.';
        } else {
            $dir = '../../assets/img/avatars/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $fname = 'avatar_' . $id . '_' . uniqid() . '.webp';
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

            // Delete old avatar file if it exists
            if (!empty($user['avatar'])) {
                $oldPath = '../../' . ltrim($user['avatar'], '/');
                if (file_exists($oldPath)) @unlink($oldPath);
            }

            $avatar = 'assets/img/avatars/' . $fname;
        }
    }

    // ── Update DB ─────────────────────────────────────────────
    if (empty($errors)) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            // s s s s s s i i s i = 10 params
            $stmt = $conn->prepare("
                UPDATE admin_users
                SET name=?, email=?, password=?, role=?, phone=?, avatar=?,
                    status=?, two_fa_enabled=?, notes=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->bind_param(
                "ssssssiisi",
                $name, $email, $hashed, $role, $phone, $avatar,
                $status, $two_fa, $notes, $id
            );
        } else {
            // s s s s s i i s i = 9 params
            $stmt = $conn->prepare("
                UPDATE admin_users
                SET name=?, email=?, role=?, phone=?, avatar=?,
                    status=?, two_fa_enabled=?, notes=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->bind_param(
                "sssssiisi",
                $name, $email, $role, $phone, $avatar,
                $status, $two_fa, $notes, $id
            );
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Activity log
            $ip     = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
            $detail = $conn->real_escape_string("Updated user: $name ($email) role: $role");
            $conn->query("
                INSERT INTO admin_activity_log (user_id, action, detail, ip, created_at)
                VALUES ($id, 'user_updated', '$detail', '$ip', NOW())
            ");

            header("Location: ./?msg=updated");
            exit;
        } else {
            $errors[] = 'Database error: ' . htmlspecialchars($stmt->error);
            $stmt->close();
        }
    }
}

// ── Helper: safe POST re-population (POST > DB > default) ─────
$p = fn($k, $default = '') => htmlspecialchars($_POST[$k] ?? $user[$k] ?? $default);

// ── Recent Activity Logs ──────────────────────────────────────
$logs = [];
$stmt = $conn->prepare("
    SELECT action, detail, ip, created_at
    FROM admin_activity_log
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $logs[] = $row;
    $stmt->close();
}

// ── Avatar display helpers ────────────────────────────────────
$initials     = strtoupper(substr($user['name'], 0, 1));
$avatarColors = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#0dcaf0', '#d63384'];
$avatarColor  = $avatarColors[$user['id'] % count($avatarColors)];
$siteUrl      = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$avatarSrc    = !empty($user['avatar']) ? $siteUrl . '/' . ltrim($user['avatar'], '/') : '';

$pageTitle  = 'Edit User — ' . htmlspecialchars($user['name']);
$activePage = 'users-edit';
$assetBase  = '../';

$extraCSS = '
<style>
    .form-label { font-size: 0.72rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 0.45rem; }
    .form-control, .form-select { font-size: 0.88rem; padding: 0.6rem 1rem; border-color: #dee2e6; border-radius: 0.5rem; transition: border-color .2s, box-shadow .2s; }
    .form-control:focus, .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 3px rgba(13,110,253,.12); }
    .input-group .form-control { border-radius: 0 0.5rem 0.5rem 0 !important; }
    .input-group-text { border-color: #dee2e6; }

    .avatar-upload-wrap { position: relative; width: 90px; height: 90px; }
    .avatar-upload-wrap .avatar-img,
    .avatar-upload-wrap .avatar-placeholder {
        width: 90px; height: 90px; border-radius: 50%; object-fit: cover;
        border: 3px solid #e9ecef; display: flex; align-items: center;
        justify-content: center; font-size: 2rem; font-weight: 800; color: #fff;
    }
    .avatar-upload-btn {
        position: absolute; bottom: 2px; right: 2px; width: 26px; height: 26px;
        border-radius: 50%; background: #0d6efd; color: #fff; border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 0.7rem; transition: background .2s;
    }
    .avatar-upload-btn:hover { background: #0b5ed7; }

    .pw-bar { height: 5px; border-radius: 4px; background: #e9ecef; overflow: hidden; margin-top: 8px; }
    .pw-bar-fill { height: 100%; border-radius: 4px; transition: width .4s, background .4s; width: 0%; }
    .req-item { font-size: 0.75rem; display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: #adb5bd; transition: color .2s; }
    .req-item.met { color: #198754; }
    .req-item i { width: 14px; }

    .role-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .role-card { border: 2px solid #e9ecef; border-radius: 0.75rem; padding: 0.85rem 1rem; cursor: pointer; transition: all .2s; background: #fff; }
    .role-card:hover { border-color: #86b7fe; background: #f8fbff; }
    .role-card.selected { border-color: #0d6efd; background: #f0f6ff; }
    .role-card .role-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; margin-bottom: 0.4rem; }
    .role-card .role-name { font-size: 0.8rem; font-weight: 700; color: #212529; }
    .role-card .role-desc { font-size: 0.68rem; color: #6c757d; margin-top: 2px; }

    .section-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; }
    .log-item { border-left: 3px solid #dee2e6; padding: 0.4rem 0.75rem; margin-bottom: 0.5rem; }
    .log-item:last-child { margin-bottom: 0; }
    .stat-mini { text-align: center; padding: 0.75rem; background: #f8f9fa; border-radius: 0.5rem; border: 1px solid #e9ecef; }
    .stat-mini .val { font-size: 1.35rem; font-weight: 800; color: #212529; }
    .stat-mini .lbl { font-size: 0.68rem; text-transform: uppercase; letter-spacing: .4px; color: #6c757d; font-weight: 700; }
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
                    <h3 class="fw-bolder text-dark mb-1">Edit User</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="./" class="text-muted text-decoration-none">Users</a></li>
                            <li class="breadcrumb-item active text-secondary fw-medium"><?= htmlspecialchars($user['name']) ?></li>
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

            <form method="POST" enctype="multipart/form-data" id="editUserForm">
            <div class="row g-4">

                <!-- ══ LEFT COLUMN ════════════════════════════ -->
                <div class="col-xl-8 col-lg-7">

                    <!-- Profile Summary Banner -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4"
                         style="background: linear-gradient(135deg,#0d6efd12,#6f42c112);">
                        <div class="card-body p-4 d-flex align-items-center gap-4">

                            <!-- Avatar -->
                            <div class="avatar-upload-wrap flex-shrink-0" id="avatarWrap">
                                <?php if ($avatarSrc): ?>
                                <img src="<?= htmlspecialchars($avatarSrc) ?>" id="avatarImgEl"
                                     class="avatar-img shadow"
                                     onerror="this.style.display='none';document.getElementById('avatarFallback').style.display='flex';">
                                <div class="avatar-placeholder shadow" id="avatarFallback"
                                     style="display:none;background:<?= $avatarColor ?>;">
                                    <?= $initials ?>
                                </div>
                                <?php else: ?>
                                <div class="avatar-placeholder shadow" id="avatarFallback"
                                     style="background:<?= $avatarColor ?>;">
                                    <?= $initials ?>
                                </div>
                                <?php endif; ?>
                                <div class="avatar-upload-btn shadow"
                                     onclick="document.getElementById('avatarInput').click()">
                                    <i class="fa fa-camera"></i>
                                </div>
                            </div>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none">

                            <!-- Info -->
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h5 class="fw-bolder text-dark mb-0"><?= htmlspecialchars($user['name']) ?></h5>
                                    <?php
                                    $roleBadges = [
                                        'superadmin' => 'bg-danger text-white',
                                        'admin'      => 'bg-primary text-white',
                                        'editor'     => 'bg-success text-white',
                                        'viewer'     => 'bg-info text-white',
                                    ];
                                    $rb = $roleBadges[$user['role']] ?? 'bg-secondary text-white';
                                    ?>
                                    <span class="badge <?= $rb ?> rounded-pill px-3 py-1 fw-semibold text-capitalize">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                    <span class="badge <?= $user['status'] ? 'bg-success' : 'bg-secondary' ?> text-white rounded-pill px-3 py-1 fw-semibold">
                                        <?= $user['status'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                                <div class="text-muted small d-flex flex-wrap gap-3 mb-3">
                                    <span><i class="fa fa-envelope me-1"></i><?= htmlspecialchars($user['email']) ?></span>
                                    <?php if (!empty($user['phone'])): ?>
                                    <span><i class="fa fa-phone me-1"></i><?= htmlspecialchars($user['phone']) ?></span>
                                    <?php endif; ?>
                                    <span><i class="fa fa-calendar-alt me-1"></i>Joined <?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                                </div>
                                <!-- Mini Stats -->
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="stat-mini">
                                            <div class="val"><?= (int)($user['login_count'] ?? 0) ?></div>
                                            <div class="lbl">Logins</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-mini">
                                            <div class="val">
                                                <?= $user['two_fa_enabled']
                                                    ? '<i class="fa fa-shield-alt text-success" style="font-size:1rem;"></i>'
                                                    : '<i class="fa fa-times text-danger" style="font-size:1rem;"></i>' ?>
                                            </div>
                                            <div class="lbl">2FA</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-mini">
                                            <div class="val" style="font-size:.85rem;">
                                                <?= !empty($user['last_login']) ? date('M d', strtotime($user['last_login'])) : '—' ?>
                                            </div>
                                            <div class="lbl">Last Login</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                        <input type="text" name="name" class="form-control border-start-0 ps-0"
                                               value="<?= $p('name') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 ps-0"
                                               value="<?= $p('email') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-phone"></i></span>
                                        <input type="tel" name="phone" class="form-control border-start-0 ps-0"
                                               placeholder="+91 98765 43210" value="<?= $p('phone') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Account Status</label>
                                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-3">
                                            <input class="form-check-input" type="checkbox" name="status" id="statusToggle"
                                                   style="width:2.75rem;height:1.4rem;cursor:pointer;"
                                                   <?= $user['status'] ? 'checked' : '' ?>>
                                            <div>
                                                <div class="fw-semibold text-dark small" id="statusLabel">
                                                    <?= $user['status'] ? 'Active' : 'Inactive' ?>
                                                </div>
                                                <div class="text-muted" style="font-size:.7rem;">User can log in</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Internal Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"
                                              placeholder="e.g. Handles blog section only..."><?= $p('notes') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role & Permissions -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-warning-subtle text-warning"><i class="fa fa-shield-alt"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Role &amp; Permissions</h6>
                        </div>
                        <div class="card-body p-4">
                            <input type="hidden" name="role" id="roleInput" value="<?= $p('role') ?>">
                            <div class="role-cards">
                                <?php
                                $roles = [
                                    'superadmin' => ['icon' => 'fa-crown',       'bg' => 'bg-danger-subtle',  'color' => 'text-danger',  'name' => 'Super Admin', 'desc' => 'Full access to everything'],
                                    'admin'      => ['icon' => 'fa-user-shield',  'bg' => 'bg-primary-subtle', 'color' => 'text-primary', 'name' => 'Admin',       'desc' => 'Manage content, users & media'],
                                    'editor'     => ['icon' => 'fa-pen-nib',      'bg' => 'bg-success-subtle', 'color' => 'text-success', 'name' => 'Editor',      'desc' => 'Create & edit posts, doctors'],
                                    'viewer'     => ['icon' => 'fa-eye',          'bg' => 'bg-info-subtle',    'color' => 'text-info',    'name' => 'Viewer',      'desc' => 'Read-only panel access'],
                                ];
                                $currentRole = $_POST['role'] ?? $user['role'];
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
                            <div class="mt-4 p-3 bg-light rounded-3 border" id="permMatrix">
                                <div class="small fw-bold text-dark mb-3 text-uppercase" style="letter-spacing:.5px;">
                                    Permissions Preview
                                </div>
                                <div class="row g-2" id="permRows"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="section-icon bg-danger-subtle text-danger"><i class="fa fa-lock"></i></div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Change Password</h6>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border rounded-pill px-3 py-1 fw-medium small">
                                Optional
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info border-0 bg-info-subtle rounded-3 p-3 mb-4 d-flex align-items-center gap-2 small">
                                <i class="fa fa-info-circle text-info"></i>
                                <span class="fw-medium">Leave blank to keep the current password unchanged.</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                        <input type="password" name="password" id="passwordInput"
                                               class="form-control border-start-0 border-end-0 ps-0"
                                               placeholder="Enter new password...">
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
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                        <input type="password" name="confirm_password" id="confirmInput"
                                               class="form-control border-start-0 border-end-0 ps-0"
                                               placeholder="Re-enter password...">
                                        <button type="button" class="input-group-text bg-light border-start-0 text-muted"
                                                id="toggleCfm">
                                            <i class="fa fa-eye" id="eyeIcon2"></i>
                                        </button>
                                    </div>
                                    <div id="matchHint" class="small mt-2 fw-medium" style="display:none;min-height:20px;"></div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3" id="reqBox" style="display:none;">
                                        <div class="row g-2">
                                            <div class="col-6"><div class="req-item" id="req-len"><i class="fa fa-times-circle"></i> 8+ characters</div></div>
                                            <div class="col-6"><div class="req-item" id="req-upper"><i class="fa fa-times-circle"></i> Uppercase letter</div></div>
                                            <div class="col-6"><div class="req-item" id="req-num"><i class="fa fa-times-circle"></i> Number (0-9)</div></div>
                                            <div class="col-6"><div class="req-item" id="req-special"><i class="fa fa-times-circle"></i> Special char</div></div>
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

                    <!-- Submit -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm mb-2">
                                <i class="fa fa-save me-2"></i> Update User
                            </button>
                            <a href="./" class="btn btn-light w-100 rounded-pill py-2 fw-semibold border">Cancel</a>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-success-subtle text-success"><i class="fa fa-shield-alt"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Security</h6>
                        </div>
                        <div class="card-body p-4">
                            <!-- 2FA Toggle -->
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border mb-3">
                                <div>
                                    <div class="fw-semibold text-dark small">Two-Factor Auth (2FA)</div>
                                    <div class="text-muted" style="font-size:.7rem;">Require captcha on login</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="two_fa_enabled" id="twoFaToggle"
                                           style="width:2.75rem;height:1.4rem;cursor:pointer;"
                                           <?= $user['two_fa_enabled'] ? 'checked' : '' ?>>
                                </div>
                            </div>

                            <!-- Last Login -->
                            <?php if (!empty($user['last_login'])): ?>
                            <div class="p-3 bg-light rounded-3 border mb-3 small">
                                <div class="text-muted fw-bold text-uppercase mb-2" style="font-size:.68rem;letter-spacing:.4px;">
                                    Last Login
                                </div>
                                <div class="d-flex align-items-center gap-2 text-dark fw-medium">
                                    <i class="fa fa-clock text-primary"></i>
                                    <?= date('M d, Y h:i A', strtotime($user['last_login'])) ?>
                                </div>
                                <?php if (!empty($user['last_login_ip'])): ?>
                                <div class="d-flex align-items-center gap-2 text-muted mt-1">
                                    <i class="fa fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($user['last_login_ip']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Reset Token Status -->
                            <div class="p-3 bg-light rounded-3 border small">
                                <div class="text-muted fw-bold text-uppercase mb-2" style="font-size:.68rem;letter-spacing:.4px;">
                                    Password Reset Token
                                </div>
                                <?php
                                $hasReset = !empty($user['reset_token']) && !empty($user['reset_expires']);
                                $resetExp = $hasReset && strtotime($user['reset_expires']) < time();
                                ?>
                                <?php if (!$hasReset): ?>
                                <span class="text-success fw-medium"><i class="fa fa-check-circle me-1"></i>No pending reset</span>
                                <?php elseif ($resetExp): ?>
                                <span class="text-secondary fw-medium"><i class="fa fa-clock me-1"></i>Expired token</span>
                                <?php else: ?>
                                <span class="text-warning fw-medium"><i class="fa fa-key me-1"></i>Active reset pending</span>
                                <div class="text-muted mt-1">Expires: <?= date('M d h:i A', strtotime($user['reset_expires'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Log -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-info-subtle text-info"><i class="fa fa-history"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Recent Activity</h6>
                        </div>
                        <div class="card-body p-4">
                            <?php if (empty($logs)): ?>
                            <div class="text-center py-3">
                                <i class="fa fa-history text-secondary opacity-50 fs-3 mb-2 d-block"></i>
                                <span class="text-muted small">No activity recorded yet.</span>
                            </div>
                            <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <div class="log-item">
                                <div class="small fw-semibold text-dark">
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $log['action']))) ?>
                                </div>
                                <div class="text-muted" style="font-size:.72rem;">
                                    <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                                    <?php if (!empty($log['ip'])): ?>
                                    &mdash; <?= htmlspecialchars($log['ip']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="card border-0 shadow-sm rounded-4 border border-danger-subtle">
                        <div class="card-header bg-danger-subtle border-bottom py-3 px-4">
                            <h6 class="mb-0 fw-bold text-danger text-uppercase small" style="letter-spacing:.5px;">
                                <i class="fa fa-exclamation-triangle me-2"></i>Danger Zone
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">
                                Permanently delete this user. All associated activity logs will also be removed.
                            </p>
                            <a href="./?delete=<?= $id ?>"
                               class="btn btn-outline-danger w-100 rounded-pill fw-semibold"
                               onclick="return confirm('Permanently delete <?= addslashes(htmlspecialchars($user['name'])) ?>?')">
                                <i class="fa fa-trash-alt me-2"></i> Delete User
                            </a>
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
document.getElementById('avatarInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        let img = document.getElementById('avatarImgEl');
        if (!img) {
            img = document.createElement('img');
            img.id = 'avatarImgEl';
            img.style.cssText = 'width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid #e9ecef;';
            document.getElementById('avatarWrap').insertBefore(img, document.getElementById('avatarWrap').firstChild);
        }
        img.src = e.target.result;
        img.style.display = 'block';
        const fb = document.getElementById('avatarFallback');
        if (fb) fb.style.display = 'none';
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
                <div style="font-size:.68rem;font-weight:700;color:${allowed ? '#198754' : '#adb5bd'};">
                    <i class="fa ${allowed ? 'fa-check' : 'fa-times'} me-1"></i>${mod}
                </div>
            </div>
        </div>`).join('');
}
renderPerms('<?= addslashes($user['role'] ?? 'admin') ?>');

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
    document.getElementById('reqBox').style.display = val ? 'block' : 'none';
    Object.entries(checks).forEach(([id, met]) => {
        const el = document.getElementById(id); if (!el) return;
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
document.getElementById('passwordInput').addEventListener('input', function () { checkStrength(this.value); checkMatch(); });
document.getElementById('confirmInput').addEventListener('input', checkMatch);

// ── Password generator ────────────────────────────────────────
document.getElementById('generatePwBtn').addEventListener('click', function () {
    const lower   = 'abcdefghijklmnopqrstuvwxyz';
    const upper   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const digits  = '0123456789';
    const special = '!@#$%^&*()-_=+';
    const all     = lower + upper + digits + special;
    let pw = '';
    pw += lower  [Math.floor(Math.random() * lower.length)];
    pw += upper  [Math.floor(Math.random() * upper.length)];
    pw += digits [Math.floor(Math.random() * digits.length)];
    pw += special[Math.floor(Math.random() * special.length)];
    for (let i = 4; i < 14; i++) pw += all[Math.floor(Math.random() * all.length)];
    pw = pw.split('').sort(() => Math.random() - 0.5).join('');

    document.getElementById('passwordInput').value = pw;
    document.getElementById('confirmInput').value  = pw;
    document.getElementById('passwordInput').type  = 'text';
    document.getElementById('eyeIcon1').className  = 'fa fa-eye-slash';
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
</script>

<?php require_once '../include/footer.php'; ?>