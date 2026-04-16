<?php
/**
 * admin/include/header.php
 * Requires: auth.php already included (provides $_ADMIN, canAccess()).
 */

// ── Fetch current admin from DB (fresh data) ──────────────────────────────────
$_hAdmin = null;
if (!empty($_ADMIN['id'])) {
    $hStmt = $conn->prepare("SELECT id, name, email, role, avatar, phone FROM admin_users WHERE id = ? LIMIT 1");
    $hStmt->bind_param("i", $_ADMIN['id']);
    $hStmt->execute();
    $_hAdmin = $hStmt->get_result()->fetch_assoc();
    $hStmt->close();
}
$_hName   = htmlspecialchars($_hAdmin['name']  ?? $_ADMIN['name']);
$_hRole   = htmlspecialchars($_hAdmin['role']  ?? $_ADMIN['role']);
$_hEmail  = htmlspecialchars($_hAdmin['email'] ?? $_ADMIN['email']);
$_hAvFile = $_hAdmin['avatar'] ?? '';
$_hAvUrl  = $_hAvFile
    ? SITE_URL . '/admin/' . ltrim($_hAvFile, '/')
    : SITE_URL . '/admin/assets/img/profiles/avatar-01.jpg';

// ── Recent Activity (last 6) ───────────────────────────────────────────────────
$_hActivities = [];
$res = $conn->query("
    SELECT al.action, al.detail, al.ip, al.created_at, au.name AS user_name, au.avatar
    FROM admin_activity_log al
    LEFT JOIN admin_users au ON au.id = al.user_id
    ORDER BY al.created_at DESC
    LIMIT 6
");
if ($res) while ($row = $res->fetch_assoc()) $_hActivities[] = $row;

$_hNotifCount = count($_hActivities);

// ── Role badge colour ─────────────────────────────────────────────────────────
$_hRoleBadge = [
    'superadmin' => 'danger',
    'admin'      => 'primary',
    'editor'     => 'success',
    'viewer'     => 'secondary',
][$_hAdmin['role'] ?? 'admin'] ?? 'primary';

// ── Action icon map ───────────────────────────────────────────────────────────
function _notifIcon(string $action): string {
    $map = [
        'login'          => ['fa-sign-in-alt',  'bg-success'],
        'logout'         => ['fa-sign-out-alt', 'bg-secondary'],
        'user_created'   => ['fa-user-plus',    'bg-primary'],
        'user_updated'   => ['fa-user-edit',    'bg-info'],
        'doctor_created' => ['fa-user-md',      'bg-teal'],
        'doctor_updated' => ['fa-user-md',      'bg-info'],
        'blog_created'   => ['fa-pen-nib',      'bg-warning'],
        'blog_updated'   => ['fa-pen-nib',      'bg-warning'],
        'service_added'  => ['fa-briefcase',    'bg-primary'],
    ];
    $v = $map[$action] ?? ['fa-bell', 'bg-secondary'];
    return "<span class=\"avatar avatar-xs flex-shrink-0\">
                <span class=\"avatar-title rounded-circle {$v[1]} text-white\" style=\"width:32px;height:32px;\">
                    <i class=\"fa {$v[0]}\" style=\"font-size:.75rem;\"></i>
                </span>
            </span>";
}

function _timeAgo(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff/60)  . ' min ago';
    if ($diff < 86400)  return floor($diff/3600) . ' hr ago';
    if ($diff < 604800) return floor($diff/86400) . ' day ago';
    return date('d M', strtotime($dt));
}
?>
<!-- Header -->
<div class="header">

    <!-- Logo -->
    <div class="header-left">
        <a href="<?= SITE_URL ?>/admin/" class="logo">
            <img src="<?= SITE_URL ?>/admin/assets/img/logo.png" alt="Logo">
        </a>
        <a href="<?= SITE_URL ?>/admin/" class="logo logo-small">
            <img src="<?= SITE_URL ?>/admin/assets/img/logo-small.png" alt="Logo" width="30" height="30">
        </a>
    </div>

    <a href="javascript:void(0);" id="toggle_btn">
        <i class="fe fe-text-align-left"></i>
    </a>

    <!-- Search Bar -->
    <div class="top-nav-search" style="position:relative;">
        <form onsubmit="return false;">
            <input type="text" id="menuSearch" class="form-control" placeholder="Search menu…"
                   autocomplete="off" role="combobox" aria-autocomplete="list"
                   aria-expanded="false" aria-controls="menuSearchResults">
            <button class="btn" type="button"><i class="fa fa-search"></i></button>
        </form>
        <div id="menuSearchResults" role="listbox"
             style="display:none;position:absolute;top:calc(100% + 6px);left:0;right:0;
                    background:#fff;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.15);
                    z-index:9999;max-height:320px;overflow-y:auto;border:1px solid #e9ecef;">
        </div>
    </div>

    <!-- Mobile Menu Toggle -->
    <a class="mobile_btn" id="mobile_btn">
        <i class="fa fa-bars"></i>
    </a>

    <!-- Header Right Menu -->
    <ul class="nav user-menu">

        <!-- ── Notifications ─────────────────────────────────────────────── -->
        <li class="nav-item dropdown noti-dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <i class="fe fe-bell"></i>
                <?php if ($_hNotifCount > 0): ?>
                <span class="badge rounded-pill"><?= $_hNotifCount ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu notifications" style="width:360px;max-height:480px;">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Recent Activity
                        <?php if ($_hNotifCount): ?>
                        <span class="badge bg-primary rounded-pill ms-1" style="font-size:.68rem;"><?= $_hNotifCount ?></span>
                        <?php endif; ?>
                    </span>
                    <a href="<?= SITE_URL ?>/admin/" class="clear-noti">View All</a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list">
                        <?php if (empty($_hActivities)): ?>
                        <li class="notification-message text-center py-4">
                            <span class="text-muted small">No recent activity</span>
                        </li>
                        <?php else: foreach ($_hActivities as $act): ?>
                        <li class="notification-message">
                            <a href="<?= SITE_URL ?>/admin/">
                                <div class="notify-block d-flex align-items-start gap-2">
                                    <?= _notifIcon($act['action']) ?>
                                    <div class="media-body flex-grow-1" style="min-width:0;">
                                        <p class="noti-details mb-0" style="font-size:.82rem;white-space:normal;line-height:1.3;">
                                            <span class="noti-title fw-semibold"><?= htmlspecialchars($act['user_name'] ?? 'System') ?></span>
                                            &mdash; <?= htmlspecialchars(mb_strimwidth($act['detail'] ?? $act['action'], 0, 55, '…')) ?>
                                        </p>
                                        <p class="noti-time mb-0 mt-1">
                                            <span class="notification-time text-muted" style="font-size:.72rem;">
                                                <i class="fa fa-clock me-1"></i><?= _timeAgo($act['created_at']) ?>
                                                <?php if (!empty($act['ip'])): ?>
                                                · <span class="text-muted"><?= htmlspecialchars($act['ip']) ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="<?= SITE_URL ?>/admin/">View All Activity</a>
                </div>
            </div>
        </li>

        <!-- ── User Menu ──────────────────────────────────────────────────── -->
        <li class="nav-item dropdown has-arrow">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <span class="user-img">
                    <img class="rounded-circle" src="<?= $_hAvUrl ?>" width="31" height="31"
                         alt="<?= $_hName ?>" style="object-fit:cover;">
                </span>
            </a>
            <div class="dropdown-menu" style="min-width:210px;">
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        <img src="<?= $_hAvUrl ?>" alt="<?= $_hName ?>"
                             class="avatar-img rounded-circle" style="object-fit:cover;">
                    </div>
                    <div class="user-text">
                        <h6 class="mb-0"><?= $_hName ?></h6>
                        <p class="text-muted mb-1" style="font-size:.75rem;"><?= $_hEmail ?></p>
                        <span class="badge bg-<?= $_hRoleBadge ?>" style="font-size:.65rem;text-transform:capitalize;">
                            <?= $_hRole ?>
                        </span>
                    </div>
                </div>
                <a class="dropdown-item" href="<?= SITE_URL ?>/admin/profile">
                    <i class="fa fa-user-circle me-2 text-muted"></i>My Profile
                </a>
                <?php if (canAccess('users')): ?>
                <a class="dropdown-item" href="<?= SITE_URL ?>/admin/users/">
                    <i class="fa fa-users me-2 text-muted"></i>Manage Users
                </a>
                <?php endif; ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="<?= SITE_URL ?>/admin/logout">
                    <i class="fa fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </li>

    </ul>
    <!-- /Header Right Menu -->

</div>
<!-- /Header -->

<?php
// ── Role-filtered menu for JS search ─────────────────────────────────────────
$_base = defined('SITE_URL') ? SITE_URL . '/admin/' : '/hpce/admin/';
$_menuItems = [];

// Dashboard (everyone)
$_menuItems[] = ['section'=>'Main',     'icon'=>'fe fe-home',      'label'=>'Dashboard',    'url'=>$_base];

// Doctors
if (canAccess('doctors')) {
    $_menuItems[] = ['section'=>'Doctors',  'icon'=>'fe fe-user-plus', 'label'=>'All Doctors',  'url'=>$_base.'doctors/'];
    if (!hasRole('viewer'))
        $_menuItems[] = ['section'=>'Doctors', 'icon'=>'fe fe-user-plus', 'label'=>'Add Doctor', 'url'=>$_base.'doctors/add'];
}

// Users
if (canAccess('users')) {
    $_menuItems[] = ['section'=>'Users',    'icon'=>'fe fe-users',     'label'=>'All Users',    'url'=>$_base.'users/'];
    if (hasRole(['superadmin','admin']))
        $_menuItems[] = ['section'=>'Users', 'icon'=>'fe fe-users',    'label'=>'Add User',     'url'=>$_base.'users/add'];
}

// Blogs
if (canAccess('blogs')) {
    $_menuItems[] = ['section'=>'Blogs',    'icon'=>'fe fe-edit',      'label'=>'All Blogs',    'url'=>$_base.'blog/'];
    if (!hasRole('viewer'))
        $_menuItems[] = ['section'=>'Blogs', 'icon'=>'fe fe-edit',     'label'=>'Add Blog',     'url'=>$_base.'blog/add'];
}

// Services
if (canAccess('services')) {
    $_menuItems[] = ['section'=>'Services', 'icon'=>'fe fe-layout', 'label'=>'All Services', 'url'=>$_base.'services/'];
    if (!hasRole('viewer'))
        $_menuItems[] = ['section'=>'Services','icon'=>'fe fe-layout','label'=>'Add Service', 'url'=>$_base.'services/add'];
}

// Profile (always)
$_menuItems[] = ['section'=>'Account',  'icon'=>'fa fa-user-circle','label'=>'My Profile',   'url'=>$_base.'profile'];
?>
<script>
window.ADMIN_MENU = <?= json_encode($_menuItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
