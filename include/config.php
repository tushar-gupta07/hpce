<?php
// C:\xamppnew\htdocs\rkhospital\include\config.php

// ─── Database Configuration ───────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rkhospital');

// ─── Site Configuration ───────────────────────────────────────────────────────
define('SITE_URL',        'http://localhost/rkhospital');
define('BLOG_IMG_PATH',   'assets/img/blog/');
define('BLOGS_PER_PAGE',  6);
define('SERVICES_PER_PAGE', 6);

// ─── Connect to DB ────────────────────────────────────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// ─── Helper: Sanitize input ───────────────────────────────────────────────────
function clean($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

// ─── Helper: Format date ──────────────────────────────────────────────────────
function formatDate($dateStr) {
    return date('d M Y', strtotime($dateStr));
}

// ─── Helper: Truncate text ────────────────────────────────────────────────────
function truncate($text, $limit = 120) {
    $text = strip_tags($text);
    if (strlen($text) <= $limit) return $text;
    return substr($text, 0, strrpos(substr($text, 0, $limit), ' ')) . '...';
}

// ─── Helper: Asset URL ───────────────────────────────────────────────────────
function asset($path) {
    return SITE_URL . '/' . ltrim($path, '/');
}
?> 