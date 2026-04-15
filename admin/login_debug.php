<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../include/config.php';

$testEmail    = 'admin@gmail.com'; // ← same email you used in fix_password.php
$testPassword = 'Abhi@9860!';           // ← same password you used in fix_password.php

echo "<pre style='font:14px monospace;padding:20px;'>";

// ── Check 1: Fetch user ───────────────────────────────────────
$stmt = $conn->prepare("SELECT id, email, password, status, two_fa_enabled FROM admin_users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $testEmail);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin) {
    echo "❌ USER NOT FOUND for email: $testEmail\n";
    echo "\nAll users in DB:\n";
    $all = $conn->query("SELECT id, email, LEFT(password,15) as pw_preview, status, two_fa_enabled FROM admin_users");
    while ($r = $all->fetch_assoc()) print_r($r);
    exit("</pre>");
}

echo "✅ User found: ID={$admin['id']}, Email={$admin['email']}\n";
echo "   Status:       {$admin['status']}\n";
echo "   two_fa_enabled: {$admin['two_fa_enabled']}\n";
echo "   Password hash preview: " . substr($admin['password'], 0, 20) . "...\n\n";

// ── Check 2: Is it a valid bcrypt hash? ───────────────────────
$isBcrypt = substr($admin['password'], 0, 4) === '$2y$' || substr($admin['password'], 0, 4) === '$2b$';
echo ($isBcrypt ? "✅" : "❌") . " Is bcrypt hash: " . ($isBcrypt ? "YES" : "NO — still plain text or MD5!") . "\n";

// ── Check 3: password_verify ──────────────────────────────────
$verified = password_verify($testPassword, $admin['password']);
echo ($verified ? "✅" : "❌") . " password_verify result: " . ($verified ? "TRUE — password matches!" : "FALSE — password does NOT match!") . "\n";

if (!$verified) {
    echo "\n⚠️  Possible reasons:\n";
    echo "   1. You typed a DIFFERENT password in fix_password.php vs what you are testing here\n";
    echo "   2. The UPDATE did not save correctly\n";
    echo "   3. Wrong user was updated (check email)\n";
    echo "\nHash in DB: " . $admin['password'] . "\n";
}

// ── Check 4: Manual fix if still wrong ───────────────────────
if (!$verified) {
    echo "\n🔧 Applying direct fix now with exact password above...\n";
    $newHash = password_hash($testPassword, PASSWORD_BCRYPT);
    $fix = $conn->prepare("UPDATE admin_users SET password = ? WHERE email = ?");
    $fix->bind_param("ss", $newHash, $testEmail);
    $fix->execute();
    echo ($fix->affected_rows > 0 ? "✅ Fixed! New hash: $newHash" : "❌ Still failed to update.") . "\n";
    $fix->close();
}

echo "\n=== DONE — DELETE THIS FILE IMMEDIATELY ===\n";
echo "</pre>";