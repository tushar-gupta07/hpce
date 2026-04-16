<?php
/**
 * admin/include/auth.php
 * Include AFTER config.php on every admin page.
 * Provides: session guard, role helpers, current admin info in $_ADMIN.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Guard: must be logged in ──────────────────────────────────────────────────
if (empty($_SESSION['admin_id'])) {
    $loginUrl = defined('SITE_URL') ? SITE_URL . '/admin/login.php' : '/hpce/admin/login.php';
    header("Location: $loginUrl");
    exit;
}

// ── Role → allowed modules map ────────────────────────────────────────────────
$_ROLE_PERMS = [
    'superadmin' => ['dashboard','users','doctors','blogs','services','settings','logs'],
    'admin'      => ['dashboard','users','doctors','blogs','services'],
    'editor'     => ['dashboard','doctors','blogs','services'],
    'viewer'     => ['dashboard'],
];

/**
 * Check if logged-in admin has one of the given roles.
 * @param  string|string[] $roles
 */
function hasRole($roles): bool {
    return in_array($_SESSION['admin_role'] ?? '', (array)$roles, true);
}

/**
 * Check if logged-in admin can access a module.
 * @param  string $module  e.g. 'users', 'doctors', 'blogs'
 */
function canAccess(string $module): bool {
    global $_ROLE_PERMS;
    $role = $_SESSION['admin_role'] ?? '';
    return in_array($module, $_ROLE_PERMS[$role] ?? [], true);
}

/**
 * Redirect to dashboard with error if the admin cannot access the module.
 */
function requireAccess(string $module): void {
    if (!canAccess($module)) {
        $base = defined('SITE_URL') ? SITE_URL . '/admin/' : '/hpce/admin/';
        header("Location: {$base}index.php?err=noperm");
        exit;
    }
}

// ── Convenience shorthand for templates ──────────────────────────────────────
$_ADMIN = [
    'id'    => (int)($_SESSION['admin_id']    ?? 0),
    'name'  => $_SESSION['admin_name']         ?? 'Admin',
    'email' => $_SESSION['admin_email']        ?? '',
    'role'  => $_SESSION['admin_role']         ?? 'admin',
];
