<?php
require_once '../include/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Log the logout action
if (!empty($_SESSION['admin_id'])) {
    $uid = (int)$_SESSION['admin_id'];
    $ip  = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    $conn->query("INSERT INTO admin_activity_log (user_id, action, detail, ip, created_at)
        VALUES ($uid, 'logout', 'Admin logged out', '$ip', NOW())");
}

session_unset();
session_destroy();

header("Location: " . SITE_URL . "/admin/login");
exit;
