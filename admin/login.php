<?php
/**
 * admin/login.php — Fixed: session_start first, pure-PHP step switching, 2FA captcha display
 */

// ── MUST be first — before ANY output or include ──────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Config: DB + constants (no session_start inside config.php) ───────────────
require_once dirname(__DIR__) . '/include/config.php';

// ── Already logged in? Go straight to dashboard ───────────────────────────────
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . SITE_URL . '/admin/index.php');
    exit;
}

// ── Math CAPTCHA generator ─────────────────────────────────────────────────────
function generateCaptcha(): array {
    $a   = rand(2, 19);
    $b   = rand(1, 15);
    $ops = ['+', '-', '*'];
    $op  = $ops[array_rand($ops)];
    switch ($op) {
        case '+': $ans = $a + $b; break;
        case '-': $ans = $a - $b; break;
        case '*': $ans = $a * $b; break;
        default:  $ans = $a + $b;
    }
    return ['a' => $a, 'b' => $b, 'op' => $op, 'answer' => $ans];
}

if (empty($_SESSION['captcha'])) {
    $_SESSION['captcha'] = generateCaptcha();
}

$error        = '';
$success      = '';
$show2fa      = false;
$captchaError = false;

// ── POST Handler ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $formStep = $_POST['form_step'] ?? 'credentials';

    // ════════════════════════════════════════
    // STEP 1 — Email + Password
    // ════════════════════════════════════════
    if ($formStep === 'credentials') {

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $error = 'Email and password are required.';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';

        } else {
            $stmt = $conn->prepare(
                'SELECT id, name, email, password, role, status, two_fa_enabled
                 FROM admin_users WHERE email = ? LIMIT 1'
            );
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$admin || !password_verify($password, $admin['password'])) {
                $error = 'Invalid email or password. Please check and try again.';
                $_SESSION['captcha'] = generateCaptcha();

            } elseif (!(int)$admin['status']) {
                $error = 'Your account has been deactivated. Contact a super admin.';

            } elseif ((int)$admin['two_fa_enabled']) {
                // ✅ Password OK + 2FA enabled → show captcha step
                $_SESSION['pending_admin_id'] = (int)$admin['id'];
                $_SESSION['captcha']          = generateCaptcha();
                $show2fa = true;

            } else {
                // ✅ No 2FA → log in immediately
                session_regenerate_id(true);
                $_SESSION['admin_id']    = $admin['id'];
                $_SESSION['admin_name']  = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role']  = $admin['role'];

                $ip = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
                $conn->query("UPDATE admin_users
                              SET last_login = NOW(), last_login_ip = '$ip',
                                  login_count = login_count + 1
                              WHERE id = {$admin['id']}");
                $conn->query("INSERT INTO admin_activity_log
                              (user_id, action, detail, ip, created_at)
                              VALUES ({$admin['id']}, 'login', 'Standard login', '$ip', NOW())");

                session_write_close();
                header('Location: ' . SITE_URL . '/admin/index.php');
                exit;
            }
        }
    }

    // ════════════════════════════════════════
    // STEP 2 — Math CAPTCHA
    // ════════════════════════════════════════
    elseif ($formStep === 'captcha') {

        if (empty($_SESSION['pending_admin_id'])) {
            $error = 'Session expired. Please log in again.';
            unset($_SESSION['captcha']);

        } else {
            $captchaAnswer = trim($_POST['captcha_answer'] ?? '');

            if ($captchaAnswer === '') {
                $error   = 'Please enter the answer to the security question.';
                $show2fa = true;

            } elseif ((int)$captchaAnswer !== (int)($_SESSION['captcha']['answer'] ?? PHP_INT_MAX)) {
                $error        = 'Incorrect answer. A new question has been generated.';
                $captchaError = true;
                $show2fa      = true;
                $_SESSION['captcha'] = generateCaptcha();

            } else {
                $pendingId = (int)$_SESSION['pending_admin_id'];
                $stmt = $conn->prepare(
                    'SELECT id, name, email, role, status
                     FROM admin_users WHERE id = ? AND status = 1 LIMIT 1'
                );
                $stmt->bind_param('i', $pendingId);
                $stmt->execute();
                $admin = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$admin) {
                    $error = 'Account not found or deactivated.';
                    unset($_SESSION['pending_admin_id'], $_SESSION['captcha']);
                } else {
                    // ✅ Captcha correct → full login
                    unset($_SESSION['pending_admin_id'], $_SESSION['captcha']);

                    session_regenerate_id(true);
                    $_SESSION['admin_id']    = $admin['id'];
                    $_SESSION['admin_name']  = $admin['name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_role']  = $admin['role'];

                    $ip = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
                    $conn->query("UPDATE admin_users
                                  SET last_login = NOW(), last_login_ip = '$ip',
                                      login_count = login_count + 1
                                  WHERE id = {$admin['id']}");
                    $conn->query("INSERT INTO admin_activity_log
                                  (user_id, action, detail, ip, created_at)
                                  VALUES ({$admin['id']}, 'login', 'Login with 2FA captcha', '$ip', NOW())");

                    session_write_close();
                    header('Location: ' . SITE_URL . '/admin/index.php');
                    exit;
                }
            }
        }
    }
}

// ── If pending_admin_id is in session (e.g. page refresh) → show step 2 ──────
if (!empty($_SESSION['pending_admin_id'])) {
    $show2fa = true;
}

$captcha   = $_SESSION['captcha'] ?? generateCaptcha();
$opDisplay = ['+' => '+', '-' => '−', '*' => '×'];
$opSym     = $opDisplay[$captcha['op']] ?? $captcha['op'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – R.K. Hospital</title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/plugins/fontawesome/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh;
            background: linear-gradient(135deg, #1a3fbd 0%, #0b91d4 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-wrapper {
            display: flex; width: 880px; max-width: 96vw;
            min-height: 520px; border-radius: 20px; overflow: hidden;
            box-shadow: 0 24px 60px rgba(0,0,0,.35);
        }
        /* ── Sidebar ── */
        .login-sidebar {
            background: linear-gradient(170deg, #1548c8 0%, #0b7ec0 100%);
            color: #fff; width: 300px; flex-shrink: 0;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 40px 28px;
        }
        .brand-icon {
            width: 70px; height: 70px; border-radius: 50%;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin-bottom: 14px;
        }
        .login-sidebar h2 { font-size: 1.35rem; font-weight: 700; margin: 0 0 4px; }
        .login-sidebar .sub { font-size: .82rem; opacity: .75; margin-bottom: 28px; }
        .feat {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,.12); border-radius: 10px;
            padding: 10px 14px; margin-bottom: 10px;
            width: 100%; font-size: .84rem;
        }
        /* ── Main ── */
        .login-main {
            background: #fff; flex: 1;
            padding: 48px 44px;
            display: flex; flex-direction: column; justify-content: center;
        }
        /* ── Step Indicator ── */
        .steps { display: flex; align-items: flex-start; margin-bottom: 32px; }
        .step  { display: flex; flex-direction: column; align-items: center; }
        .step-circle {
            width: 34px; height: 34px; border-radius: 50%;
            border: 2px solid #d1d5db; color: #9ca3af;
            display: flex; align-items: center; justify-content: center;
            font-size: .84rem; font-weight: 700; background: #fff;
            transition: all .3s;
        }
        .step-circle.active { background: #1a56e0; border-color: #1a56e0; color: #fff; }
        .step-circle.done   { background: #16a34a; border-color: #16a34a; color: #fff; }
        .step-label { font-size: .7rem; color: #6b7280; margin-top: 5px; }
        .step-line  { flex: 1; height: 2px; background: #e5e7eb; margin: 17px 8px 0; transition: background .3s; }
        .step-line.done { background: #16a34a; }
        /* ── Form ── */
        .form-label { font-size: .78rem; font-weight: 600; letter-spacing: .04em; color: #374151; margin-bottom: 6px; }
        .input-group-text { background: #f9fafb; border-right: none; color: #9ca3af; }
        .form-control { border-left: none; }
        .form-control:focus { box-shadow: none; border-color: #1a56e0; }
        .input-group .form-control:not(:last-child) { border-right: none; }
        .btn-eye { background: #f9fafb; border: 1px solid #dee2e6; border-left: none; color: #6b7280; }
        .btn-eye:hover { background: #f0f0f0; color: #374151; }
        .btn-submit {
            background: linear-gradient(90deg, #1a56e0, #1e88e5);
            color: #fff; border: none; padding: 12px;
            font-weight: 600; border-radius: 10px;
            width: 100%; font-size: .98rem; cursor: pointer;
            transition: opacity .2s;
        }
        .btn-submit:hover { opacity: .9; }
        /* ── Captcha ── */
        .captcha-box {
            background: #eff6ff; border: 2px dashed #93c5fd;
            border-radius: 12px; padding: 20px 24px;
            text-align: center; margin-bottom: 18px;
        }
        .captcha-eq { font-size: 1.55rem; font-weight: 800; color: #1a56e0; letter-spacing: .1em; }
        /* ── Alert ── */
        .alert-err {
            background: #fef2f2; border: 1px solid #fca5a5;
            color: #991b1b; border-radius: 10px;
            padding: 10px 14px; font-size: .87rem; margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .footer-txt { text-align: center; font-size: .76rem; color: #9ca3af; margin-top: 28px; }
        @media (max-width: 640px) {
            .login-sidebar { display: none; }
            .login-main { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- ── Sidebar ── -->
    <div class="login-sidebar">
        <div class="brand-icon"><i class="fas fa-hospital-alt"></i></div>
        <h2>R.K. Hospital</h2>
        <p class="sub">Admin Control Panel</p>
        <div class="feat"><i class="fas fa-lock fa-fw"></i> 256-bit Encrypted</div>
        <div class="feat"><i class="fas fa-shield-alt fa-fw"></i> 2FA Protection</div>
        <div class="feat"><i class="fas fa-user-shield fa-fw"></i> Role-Based Access</div>
        <div class="feat"><i class="fas fa-history fa-fw"></i> Activity Logged</div>
    </div>

    <!-- ── Main ── -->
    <div class="login-main">

        <!-- Step Indicator -->
        <div class="steps">
            <div class="step">
                <div class="step-circle <?= $show2fa ? 'done' : 'active' ?>">
                    <?php if ($show2fa): ?><i class="fas fa-check" style="font-size:.7rem"></i><?php else: ?>1<?php endif; ?>
                </div>
                <div class="step-label">Credentials</div>
            </div>
            <div class="step-line <?= $show2fa ? 'done' : '' ?>"></div>
            <div class="step">
                <div class="step-circle <?= $show2fa ? 'active' : '' ?>">2</div>
                <div class="step-label">Verification</div>
            </div>
        </div>

        <!-- Error Alert -->
        <?php if ($error): ?>
            <div class="alert-err">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!$show2fa): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!-- STEP 1 — CREDENTIALS                          -->
        <!-- ══════════════════════════════════════════════ -->
        <h4 style="font-weight:700;margin-bottom:4px">Welcome Back</h4>
        <p style="color:#6b7280;font-size:.9rem;margin-bottom:24px">Sign in to your admin account</p>

        <form method="POST" action="" autocomplete="off">
            <input type="hidden" name="form_step" value="credentials">

            <div class="mb-3">
                <label class="form-label">EMAIL ADDRESS</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@gmail.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required autofocus>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="pwdInput"
                           class="form-control" placeholder="Enter your password" required>
                    <button type="button" class="btn btn-eye" id="togglePwd" tabindex="-1">
                        <i class="fas fa-eye" id="eyeIco"></i>
                    </button>
                </div>
            </div>

            <div class="text-end mb-4">
                <a href="<?= SITE_URL ?>/admin/forgot-password.php"
                   style="font-size:.84rem;color:#1a56e0;text-decoration:none">
                    <i class="fas fa-key me-1"></i>Forgot Password?
                </a>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-arrow-right me-2"></i>Continue
            </button>
        </form>

        <?php else: ?>
        <!-- ══════════════════════════════════════════════ -->
        <!-- STEP 2 — MATH CAPTCHA                         -->
        <!-- ══════════════════════════════════════════════ -->
        <h4 style="font-weight:700;margin-bottom:4px">Verify It's You</h4>
        <p style="color:#6b7280;font-size:.9rem;margin-bottom:24px">Solve the equation to complete sign-in</p>

        <div class="captcha-box">
            <p style="font-size:.83rem;color:#6b7280;margin-bottom:8px">What is the answer to:</p>
            <div class="captcha-eq">
                <?= $captcha['a'] ?> <?= $opSym ?> <?= $captcha['b'] ?> = ?
            </div>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="form_step" value="captcha">

            <div class="mb-4">
                <label class="form-label">YOUR ANSWER</label>
                <input type="number" name="captcha_answer"
                       class="form-control <?= $captchaError ? 'is-invalid' : '' ?>"
                       placeholder="Enter the result" autofocus required>
                <?php if ($captchaError): ?>
                    <div class="invalid-feedback">Wrong answer — a new question has been generated.</div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= SITE_URL ?>/admin/login.php"
               style="font-size:.84rem;color:#6b7280;text-decoration:none">
                <i class="fas fa-arrow-left me-1"></i>Back to Login
            </a>
        </div>
        <?php endif; ?>

        <p class="footer-txt">&copy; <?= date('Y') ?> R.K. Hospital. All rights reserved.</p>
    </div>
</div>

<script src="<?= SITE_URL ?>/admin/assets/js/jquery-3.7.1.min.js"></script>
<script src="<?= SITE_URL ?>/admin/assets/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password show/hide
    const pwdInput = document.getElementById('pwdInput');
    const eyeIco   = document.getElementById('eyeIco');
    document.getElementById('togglePwd')?.addEventListener('click', function () {
        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            eyeIco.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwdInput.type = 'password';
            eyeIco.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
</body>
</html>