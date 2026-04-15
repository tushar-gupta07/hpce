<?php
session_start();
require_once '../include/config.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: " . SITE_URL . "/admin/");
    exit;
}

$error   = '';
$success = '';
$step    = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$token   = $_GET['token'] ?? '';

// ─── STEP 3: Reset password form submission ───────────────────────────────────
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token    = trim($_POST['token']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (empty($password) || empty($confirm)) {
        $error = "All fields are required!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Verify token and check expiry
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Update password and clear token
            $upd = $conn->prepare("UPDATE admin_users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $upd->bind_param("si", $hashed, $admin['id']);
            $upd->execute();
            $upd->close();

            $success = "Password reset successfully! You can now login.";
            $step = 4;
        } else {
            $error = "Invalid or expired reset token. Please try again.";
        }
        $stmt->close();
    }
}

// ─── STEP 1: Email submission ─────────────────────────────────────────────────
if ($step === 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Email address is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Generate reset token
            $reset_token   = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $upd = $conn->prepare("UPDATE admin_users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $upd->bind_param("sss", $reset_token, $reset_expires, $email);
            $upd->execute();
            $upd->close();

            // In production: send email. For local dev: show reset link directly
            $reset_link = SITE_URL . "/admin/forgot-password.php?step=3&token=" . $reset_token;
            $success = "Reset link generated! <a href='" . $reset_link . "' class='alert-link'>Click here to reset password</a> (valid for 1 hour).";
            $step = 2;
        } else {
            // Don't reveal if email exists or not (security best practice)
            $success = "If this email exists, a reset link has been generated.";
            $step = 2;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - R.K. Hospital Admin</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/RK-Logo.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a6ef5 0%, #0a4fc4 100%);
            padding: 20px;
        }
        .auth-box {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            display: flex;
            width: 820px;
            max-width: 100%;
        }
        .auth-left {
            background: linear-gradient(135deg, #1a6ef5, #0a4fc4);
            padding: 50px 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-width: 260px;
        }
        .auth-left img { max-width: 150px; }
        .auth-left h3 { color: #fff; margin-top: 20px; font-size: 16px; text-align: center; opacity: 0.85; }
        .auth-right { padding: 40px 35px; flex: 1; }
        .auth-right h1 { font-size: 26px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .auth-right .subtitle { color: #888; margin-bottom: 25px; font-size: 14px; }
        .form-label { font-size: 13px; font-weight: 600; color: #444; margin-bottom: 5px; }
        .form-control { border-radius: 8px; padding: 11px 15px; border: 1.5px solid #e0e0e0; font-size: 14px; }
        .form-control:focus { border-color: #1a6ef5; box-shadow: 0 0 0 3px rgba(26,110,245,0.1); }
        .btn-primary { background: #1a6ef5; border: none; border-radius: 8px; padding: 12px; font-weight: 600; font-size: 15px; }
        .btn-primary:hover { background: #0a4fc4; }
        .auth-footer { font-size: 14px; color: #666; margin-top: 18px; text-align: center; }
        .auth-footer a { color: #1a6ef5; font-weight: 600; text-decoration: none; }
        .alert { border-radius: 8px; font-size: 14px; padding: 10px 14px; }
        @media (max-width: 600px) { .auth-left { display: none; } .auth-right { padding: 30px 20px; } }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">

        <div class="auth-left">
            <img src="../assets/img/RK-Logo.png" alt="RK Hospital">
            <h3>Admin Panel<br>R.K. Hospital</h3>
        </div>

        <div class="auth-right">

            <?php if ($step === 4): ?>
                <!-- Step 4: Success -->
                <div class="text-center py-4">
                    <i class="fa-solid fa-circle-check" style="font-size:50px;color:#28a745;"></i>
                    <h1 class="mt-3">Password Reset!</h1>
                    <p class="subtitle">Your password has been updated successfully.</p>
                    <a href="login" class="btn btn-primary px-4">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Go to Login
                    </a>
                </div>

            <?php elseif ($step === 3): ?>
                <!-- Step 3: New password form -->
                <h1>Reset Password</h1>
                <p class="subtitle">Enter your new password below</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-xmark me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="forgot-password.php?step=3">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm" class="form-control"
                               placeholder="Re-enter new password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-lock me-2"></i>Reset Password
                    </button>
                </form>

            <?php elseif ($step === 2): ?>
                <!-- Step 2: Email sent confirmation -->
                <div class="text-center py-4">
                    <i class="fa-solid fa-envelope-circle-check" style="font-size:50px;color:#1a6ef5;"></i>
                    <h1 class="mt-3">Check Your Email</h1>
                    <p class="subtitle">A password reset link has been generated.</p>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success text-start">
                            <i class="fa-solid fa-circle-check me-2"></i><?= $success ?>
                        </div>
                    <?php endif; ?>

                    <a href="forgot-password" class="btn btn-primary px-4 mt-2">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back
                    </a>
                </div>

            <?php else: ?>
                <!-- Step 1: Email input form -->
                <h1>Forgot Password?</h1>
                <p class="subtitle">Enter your registered email address</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-xmark me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="forgot-password.php">
                    <div class="mb-4">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="Enter your registered email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-paper-plane me-2"></i>Send Reset Link
                    </button>
                </form>

                <div class="auth-footer">
                    Remember your password? <a href="login">Login here</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>