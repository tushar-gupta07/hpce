<?php
// admin/profile.php
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/include/auth.php';

$adminId = $_ADMIN['id'];
$errors  = [];
$success = '';

// ── Fetch current user ────────────────────────────────────────────────────────
$stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: " . SITE_URL . "/admin/login");
    exit;
}

// ── Handle POST ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Update Profile ────────────────────────────────────────────────────────
    if ($action === 'update_profile') {
        $name  = trim($_POST['name']  ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($name)) $errors[] = 'Full name is required.';
        if (!empty($phone) && !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone))
            $errors[] = 'Enter a valid phone number.';

        // Avatar upload
        $avatarPath = $user['avatar'];
        if (!empty($_FILES['avatar']['name'])) {
            $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
            $mime    = mime_content_type($_FILES['avatar']['tmp_name']);
            if (!in_array($mime, $allowed)) {
                $errors[] = 'Invalid avatar type.';
            } elseif ($_FILES['avatar']['size'] > 1 * 1024 * 1024) {
                $errors[] = 'Avatar must be under 1MB.';
            } else {
                $dir = __DIR__ . '/assets/img/avatars/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                // Delete old avatar if exists
                if ($avatarPath && file_exists(__DIR__ . '/' . $avatarPath)) {
                    @unlink(__DIR__ . '/' . $avatarPath);
                }
                $fname = 'avatar_' . uniqid() . '.webp';
                $src   = $_FILES['avatar']['tmp_name'];
                switch ($mime) {
                    case 'image/jpeg': $img = imagecreatefromjpeg($src); break;
                    case 'image/png':
                        $img = imagecreatefrompng($src);
                        imagealphablending($img, true); imagesavealpha($img, true); break;
                    case 'image/gif':  $img = imagecreatefromgif($src);  break;
                    default:           $img = imagecreatefromwebp($src); break;
                }
                $ow = imagesx($img); $oh = imagesy($img);
                $min = min($ow, $oh);
                $cx = (int)(($ow - $min) / 2); $cy = (int)(($oh - $min) / 2);
                $sq = imagecreatetruecolor(200, 200);
                imagecopyresampled($sq, $img, 0, 0, $cx, $cy, 200, 200, $min, $min);
                imagewebp($sq, $dir . $fname, 85);
                imagedestroy($img); imagedestroy($sq);
                $avatarPath = 'assets/img/avatars/' . $fname;
            }
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE admin_users SET name=?, phone=?, notes=?, avatar=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("ssssi", $name, $phone, $notes, $avatarPath, $adminId);
            if ($stmt->execute()) {
                // Update session
                $_SESSION['admin_name'] = $name;
                $success = 'Profile updated successfully.';
                // Re-fetch user
                $s2 = $conn->prepare("SELECT * FROM admin_users WHERE id = ? LIMIT 1");
                $s2->bind_param("i", $adminId);
                $s2->execute();
                $user = $s2->get_result()->fetch_assoc();
                $s2->close();
                // Log
                $ip  = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
                $conn->query("INSERT INTO admin_activity_log (user_id, action, detail, ip, created_at)
                    VALUES ($adminId, 'user_updated', 'Profile updated', '$ip', NOW())");
            } else {
                $errors[] = 'Database error: ' . $stmt->error;
            }
            $stmt->close();
        }
    }

    // ── Change Password ───────────────────────────────────────────────────────
    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $newpw   = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current)) $errors[] = 'Current password is required.';
        elseif (!password_verify($current, $user['password'])) $errors[] = 'Current password is incorrect.';

        if (empty($newpw)) $errors[] = 'New password is required.';
        elseif (strlen($newpw) < 8) $errors[] = 'New password must be at least 8 characters.';
        elseif ($newpw !== $confirm) $errors[] = 'Passwords do not match.';

        if (empty($errors)) {
            $hashed = password_hash($newpw, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE admin_users SET password=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("si", $hashed, $adminId);
            if ($stmt->execute()) {
                $success = 'Password changed successfully.';
                $ip = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
                $conn->query("INSERT INTO admin_activity_log (user_id, action, detail, ip, created_at)
                    VALUES ($adminId, 'user_updated', 'Password changed', '$ip', NOW())");
            } else {
                $errors[] = 'Database error: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// ── Activity log for this user ────────────────────────────────────────────────
$myActivity = [];
$res = $conn->query("SELECT action, detail, ip, created_at FROM admin_activity_log
    WHERE user_id = $adminId ORDER BY created_at DESC LIMIT 10");
if ($res) while ($r = $res->fetch_assoc()) $myActivity[] = $r;

// ── Avatar URL ────────────────────────────────────────────────────────────────
$avatarUrl = $user['avatar']
    ? SITE_URL . '/admin/' . ltrim($user['avatar'], '/')
    : SITE_URL . '/admin/assets/img/profiles/avatar-01.jpg';

$roleBadge = ['superadmin'=>'danger','admin'=>'primary','editor'=>'success','viewer'=>'secondary'][$user['role']] ?? 'primary';

$pageTitle  = 'My Profile';
$activePage = 'profile';
$assetBase  = '';

$extraCSS = '
<style>
.profile-card { border-radius: 16px; overflow: hidden; }
.profile-cover { height: 120px; background: linear-gradient(135deg, #0f2d6b, #1a6ef5); }
.profile-avatar-wrap { margin-top: -50px; }
.profile-avatar-wrap img { width:100px;height:100px;object-fit:cover;border:4px solid #fff;border-radius:50%;box-shadow:0 4px 12px rgba(0,0,0,.15); }
.form-label { font-size:.72rem;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.6px;margin-bottom:.4rem; }
.form-control,.form-select { font-size:.88rem;padding:.6rem 1rem;border-color:#dee2e6;border-radius:.5rem; }
.form-control:focus,.form-select:focus { border-color:#86b7fe;box-shadow:0 0 0 3px rgba(13,110,253,.12); }
.section-icon { width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.88rem; }
.pw-bar { height:5px;border-radius:4px;background:#e9ecef;overflow:hidden;margin-top:6px; }
.pw-bar-fill { height:100%;border-radius:4px;transition:width .4s,background .4s;width:0%; }
</style>
';

require_once __DIR__ . '/include/head.php';
?>

    <?php require_once __DIR__ . '/include/header.php'; ?>
    <?php require_once __DIR__ . '/include/sidebar.php'; ?>

    <div class="page-wrapper" style="background:#f4f6f9;min-height:100vh;">
        <div class="content container-fluid pt-4 pb-5">

            <!-- Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bolder text-dark mb-1">My Profile</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="./" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active text-secondary">My Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-start gap-3 mb-4">
                <i class="fa fa-exclamation-triangle mt-1 fs-5"></i>
                <div><div class="fw-bold mb-1">Please fix the following:</div>
                <ul class="mb-0 ps-3 small"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4">
                <i class="fa fa-check-circle fs-5"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <?php endif; ?>

            <div class="row g-4">

                <!-- ── LEFT: Profile card + Stats ─────────────────── -->
                <div class="col-xl-4 col-lg-4">

                    <!-- Profile Card -->
                    <div class="card border-0 shadow-sm profile-card mb-4">
                        <div class="profile-cover"></div>
                        <div class="card-body text-center pt-0">
                            <div class="profile-avatar-wrap d-flex justify-content-center mb-3">
                                <img src="<?= $avatarUrl ?>" alt="<?= htmlspecialchars($user['name']) ?>" id="profileAvatarPreview">
                            </div>
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></p>
                            <span class="badge bg-<?= $roleBadge ?> px-3 py-1 rounded-pill" style="font-size:.75rem;text-transform:capitalize;">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                            <?php if (!empty($user['phone'])): ?>
                            <p class="text-muted small mt-2 mb-0"><i class="fa fa-phone me-1"></i><?= htmlspecialchars($user['phone']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-top py-3">
                            <div class="row text-center g-0">
                                <div class="col border-end">
                                    <div class="fw-bold text-dark"><?= (int)$user['login_count'] ?></div>
                                    <div class="text-muted" style="font-size:.7rem;">Logins</div>
                                </div>
                                <div class="col border-end">
                                    <div class="fw-bold text-dark"><?= (int)$user['status'] ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>' ?></div>
                                    <div class="text-muted" style="font-size:.7rem;">Status</div>
                                </div>
                                <div class="col">
                                    <div class="fw-bold text-dark" style="font-size:.82rem;"><?= $user['last_login'] ? date('d M', strtotime($user['last_login'])) : '—' ?></div>
                                    <div class="text-muted" style="font-size:.7rem;">Last Login</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Log -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-info-subtle text-info"><i class="fa fa-history"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">My Activity</h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($myActivity)): ?>
                            <p class="text-muted small text-center py-4">No activity yet.</p>
                            <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($myActivity as $a): ?>
                                <li class="list-group-item border-0 py-2 px-4">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <span class="badge bg-light text-dark border" style="font-size:.68rem;"><?= htmlspecialchars($a['action']) ?></span>
                                            <p class="mb-0 mt-1 small text-secondary"><?= htmlspecialchars(mb_strimwidth($a['detail'] ?? '', 0, 50, '…')) ?></p>
                                        </div>
                                        <span class="text-muted text-nowrap" style="font-size:.68rem;"><?= date('d M H:i', strtotime($a['created_at'])) ?></span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- ── RIGHT: Edit forms ───────────────────────────── -->
                <div class="col-xl-8 col-lg-8">

                    <!-- Edit Profile -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-primary-subtle text-primary"><i class="fa fa-user-edit"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Edit Profile</h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" enctype="multipart/form-data" id="profileForm">
                                <input type="hidden" name="action" value="update_profile">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                               value="<?= htmlspecialchars($user['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control bg-light"
                                               value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                        <div class="text-muted mt-1" style="font-size:.7rem;"><i class="fa fa-info-circle me-1"></i>Email cannot be changed here.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control"
                                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                               placeholder="+91 98765 43210">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control bg-light text-capitalize"
                                               value="<?= htmlspecialchars($user['role']) ?>" disabled>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control" rows="2"
                                                  placeholder="Internal notes..."><?= htmlspecialchars($user['notes'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Profile Photo</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= $avatarUrl ?>" id="avatarThumb"
                                                 style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid #dee2e6;">
                                            <div>
                                                <input type="file" name="avatar" id="avatarInput" accept="image/*" class="form-control form-control-sm" style="max-width:260px;">
                                                <div class="text-muted mt-1" style="font-size:.7rem;">JPG, PNG, WEBP — max 1MB. Auto-cropped to square.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                            <i class="fa fa-save me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                            <div class="section-icon bg-danger-subtle text-danger"><i class="fa fa-lock"></i></div>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:.5px;">Change Password</h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" id="pwForm">
                                <input type="hidden" name="action" value="change_password">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="current_password" id="curPw" class="form-control" placeholder="Enter current password" required>
                                            <button type="button" class="input-group-text bg-light" onclick="togglePw('curPw','eyeCur')"><i class="fa fa-eye text-muted" id="eyeCur"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="new_password" id="newPw" class="form-control" placeholder="Min 8 characters" required>
                                            <button type="button" class="input-group-text bg-light" onclick="togglePw('newPw','eyeNew')"><i class="fa fa-eye text-muted" id="eyeNew"></i></button>
                                        </div>
                                        <div class="pw-bar"><div class="pw-bar-fill" id="pwBar"></div></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="confirm_password" id="cfmPw" class="form-control" placeholder="Re-enter new password" required>
                                            <button type="button" class="input-group-text bg-light" onclick="togglePw('cfmPw','eyeCfm')"><i class="fa fa-eye text-muted" id="eyeCfm"></i></button>
                                        </div>
                                        <div id="matchHint" class="small mt-2" style="min-height:20px;"></div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                                            <i class="fa fa-key me-2"></i>Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<script>
// Avatar preview
document.getElementById('avatarInput').addEventListener('change', function () {
    const file = this.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarThumb').src = e.target.result;
    };
    reader.readAsDataURL(file);
});

// Toggle password visibility
function togglePw(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    ico.className = show ? 'fa fa-eye-slash text-muted' : 'fa fa-eye text-muted';
}

// Password strength
document.getElementById('newPw').addEventListener('input', function () {
    const v = this.value;
    const score = [v.length >= 8, /[A-Z]/.test(v), /[0-9]/.test(v), /[^A-Za-z0-9]/.test(v)].filter(Boolean).length;
    const levels = [{pct:'15%',color:'#dc3545'},{pct:'35%',color:'#fd7e14'},{pct:'65%',color:'#ffc107'},{pct:'85%',color:'#0dcaf0'},{pct:'100%',color:'#198754'}];
    const lvl = levels[score] || levels[0];
    const bar = document.getElementById('pwBar');
    bar.style.width = v ? lvl.pct : '0%';
    bar.style.background = lvl.color;
    checkMatch();
});
document.getElementById('cfmPw').addEventListener('input', checkMatch);
function checkMatch() {
    const pw  = document.getElementById('newPw').value;
    const cfm = document.getElementById('cfmPw').value;
    const hint = document.getElementById('matchHint');
    if (!cfm || !pw) { hint.innerHTML = ''; return; }
    hint.innerHTML = pw === cfm
        ? '<i class="fa fa-check-circle text-success me-1"></i><span class="text-success">Passwords match</span>'
        : '<i class="fa fa-times-circle text-danger me-1"></i><span class="text-danger">Do not match</span>';
}
</script>

<?php require_once __DIR__ . '/include/footer.php'; ?>
