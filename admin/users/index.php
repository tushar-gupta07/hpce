<?php
// admin/users/index.php
require_once __DIR__ . '/../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('users');

// ── AJAX Handler ──────────────────────────────────────────────
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    $limit  = 10;
    $page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $role   = isset($_GET['role'])   ? trim($_GET['role'])   : '';
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

    $where = ["1=1"];
    $params = []; $types = '';

    if ($search !== '') {
        $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $like = '%' . $search . '%';
        $params = array_merge($params, [$like, $like, $like]);
        $types .= 'sss';
    }
    if (!empty($role)) {
        $where[] = "role = ?";
        $params[] = $role; $types .= 's';
    }
    if ($status !== null) {
        $where[] = "status = ?";
        $params[] = $status; $types .= 'i';
    }

    $whereSQL = implode(' AND ', $where);

    // Count
    $stmtC = $conn->prepare("SELECT COUNT(*) AS total FROM admin_users WHERE $whereSQL");
    if ($types) $stmtC->bind_param($types, ...$params);
    $stmtC->execute();
    $totalRecords = (int)$stmtC->get_result()->fetch_assoc()['total'];
    $totalPages   = max(1, (int)ceil($totalRecords / $limit));

    // Data
    $stmtD = $conn->prepare("SELECT * FROM admin_users WHERE $whereSQL ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $allParams = array_merge($params, [$limit, $offset]);
    $allTypes  = $types . 'ii';
    $stmtD->bind_param($allTypes, ...$allParams);
    $stmtD->execute();
    $rows = $stmtD->get_result()->fetch_all(MYSQLI_ASSOC);

    // Strip sensitive data
    foreach ($rows as &$r) { unset($r['password'], $r['reset_token']); }

    // Stats (always fresh)
    $statsQ = $conn->query("SELECT
        COUNT(*) AS total,
        SUM(status=1) AS active,
        SUM(two_fa_enabled=1) AS twofa,
        SUM(MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())) AS new_month
        FROM admin_users");
    $stats = $statsQ->fetch_assoc();

    $rPend = $conn->query("SELECT COUNT(*) AS c FROM admin_users WHERE reset_token IS NOT NULL AND reset_expires > NOW()");
    $stats['reset_pending'] = (int)$rPend->fetch_assoc()['c'];

    echo json_encode([
        'rows'         => $rows,
        'total'        => $totalRecords,
        'totalPages'   => $totalPages,
        'currentPage'  => $page,
        'stats'        => $stats,
    ]);
    exit;
}

// ── Delete Single ─────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del = (int)$_GET['delete'];
    $conn->query("DELETE FROM admin_users WHERE id = $del");
    header("Location: ./?msg=deleted"); exit;
}

// ── Bulk Delete ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete'])) {
    $ids = array_filter(array_map('intval', $_POST['selected_ids'] ?? []));
    if (!empty($ids)) {
        $in = implode(',', $ids);
        $conn->query("DELETE FROM admin_users WHERE id IN ($in)");
    }
    header("Location: ./?msg=bulk_deleted"); exit;
}

// ── Export CSV ────────────────────────────────────────────────
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $res = $conn->query("SELECT id, name, email, role, phone, status, two_fa_enabled, login_count, last_login, created_at FROM admin_users ORDER BY created_at DESC");
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="admin_users_' . date('Ymd_His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Name','Email','Role','Phone','Status','2FA','Logins','Last Login','Joined']);
    while ($row = $res->fetch_assoc()) {
        $row['status'] = $row['status'] ? 'Active' : 'Inactive';
        $row['two_fa_enabled'] = $row['two_fa_enabled'] ? 'Yes' : 'No';
        fputcsv($out, $row);
    }
    fclose($out); exit;
}

// ── Initial Page Load Stats ───────────────────────────────────
$statsRes = $conn->query("SELECT
    COUNT(*) AS total,
    SUM(status=1) AS active,
    SUM(two_fa_enabled=1) AS twofa,
    SUM(MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())) AS new_month
    FROM admin_users");
$stats = $statsRes->fetch_assoc();

$rPend = $conn->query("SELECT COUNT(*) AS c FROM admin_users WHERE reset_token IS NOT NULL AND reset_expires > NOW()");
$stats['reset_pending'] = (int)$rPend->fetch_assoc()['c'];

// ── Initial Data ──────────────────────────────────────────────
$limit  = 10;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchLike = '%' . $conn->real_escape_string($search) . '%';

$users  = [];
$result = $conn->query("SELECT * FROM admin_users
    WHERE name LIKE '$searchLike' OR email LIKE '$searchLike' OR phone LIKE '$searchLike'
    ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
if ($result) { while ($row = $result->fetch_assoc()) { $users[] = $row; } }

$countRes     = $conn->query("SELECT COUNT(*) AS total FROM admin_users WHERE name LIKE '$searchLike' OR email LIKE '$searchLike'");
$totalRecords = $countRes ? (int)$countRes->fetch_assoc()['total'] : 0;
$totalPages   = max(1, (int)ceil($totalRecords / $limit));

$pageTitle  = 'Admin Users';
$activePage = 'users-index';
$assetBase  = '../';

$extraCSS = '
<style>
    /* ── Stat Cards ──────────────────────────────── */
    .stat-card { transition: transform .2s, box-shadow .2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.09)!important; }
    .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }

    /* ── Table ───────────────────────────────────── */
    .table-wrapper { position: relative; min-height: 260px; }
    .table-hover-soft tbody tr { transition: background .15s; }
    .table-hover-soft tbody tr:hover { background: #f8f9ff !important; }
    .avatar-cell { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid #e9ecef; }
    .avatar-initials {
        width: 42px; height: 42px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 800; color: #fff;
    }

    /* ── Role Badges ─────────────────────────────── */
    .role-badge { font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: capitalize; }
    .role-superadmin { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .role-admin      { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }
    .role-editor     { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
    .role-viewer     { background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; }

    /* ── Status Dot ──────────────────────────────── */
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

    /* ── Search Bar ──────────────────────────────── */
    .search-wrap { position: relative; }
    .search-spinner { display: none; position: absolute; right: 40px; top: 50%; transform: translateY(-50%); }
    .search-spinner.active { display: block; }

    /* ── Table Loader Overlay ────────────────────── */
    #tableLoader {
        display: none; position: absolute; inset: 0;
        background: rgba(255,255,255,.8); z-index: 10;
        align-items: center; justify-content: center;
    }
    #tableLoader.active { display: flex; }

    /* ── Bulk Bar ────────────────────────────────── */
    #bulkBar {
        display: none;
        background: #1a6ef5; color: #fff;
        border-radius: 0.5rem; padding: 10px 16px;
        align-items: center; gap: 12px;
        margin-bottom: 12px;
        animation: slideDown .2s ease;
    }
    #bulkBar.active { display: flex; }
    @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }

    /* ── Filter Pills ────────────────────────────── */
    .filter-pill { font-size: .78rem; padding: 5px 14px; border-radius: 20px; border: 1.5px solid #dee2e6;
        background: #fff; color: #6c757d; cursor: pointer; transition: all .15s; font-weight: 600; }
    .filter-pill:hover   { border-color: #1a6ef5; color: #1a6ef5; }
    .filter-pill.active  { background: #1a6ef5; border-color: #1a6ef5; color: #fff; }
    .filter-pill.danger  { background: #dc3545; border-color: #dc3545; color: #fff; }

    /* ── Empty State ─────────────────────────────── */
    .empty-state { padding: 3.5rem 1rem; text-align: center; }
    .empty-icon  { width: 80px; height: 80px; border-radius: 50%; background: #f1f5f9;
        display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; }

    /* ── Checkbox ────────────────────────────────── */
    .row-check { width: 16px; height: 16px; cursor: pointer; accent-color: #1a6ef5; }
</style>
';

require_once '../include/head.php';
?>

<div class="main-wrapper">
    <?php require_once '../include/header.php'; ?>
    <?php require_once '../include/sidebar.php'; ?>

    <div class="page-wrapper" style="background-color:#f4f6f9;min-height:100vh;">
        <div class="content container-fluid pt-4 pb-5">

            <!-- ── Page Header ──────────────────────────────── -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bolder text-dark mb-1">Admin Users</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active fw-medium text-secondary">Users</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0 flex-wrap">
                    <a href="?export=csv" class="btn btn-light rounded-pill px-4 py-2 fw-semibold border shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fa fa-file-csv text-success"></i> Export CSV
                    </a>
                    <a href="add" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fa fa-plus"></i> Add User
                    </a>
                </div>
            </div>

            <!-- ── Flash Messages ────────────────────────────── -->
            <?php if (isset($_GET['msg'])):
                $msgMap = [
                    'deleted'      => ['danger',  'User removed permanently.',       'fa-trash-alt'],
                    'bulk_deleted' => ['danger',  'Selected users removed.',         'fa-trash-alt'],
                    'added'        => ['success', 'New user created successfully.',  'fa-check-circle'],
                    'updated'      => ['success', 'User details updated.',           'fa-check-circle'],
                ];
                [$msgType, $msgText, $msgIcon] = $msgMap[$_GET['msg']] ?? ['info','Done.','fa-check'];
            ?>
            <div class="alert alert-<?= $msgType ?> border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 alert-dismissible fade show mb-4">
                <i class="fa <?= $msgIcon ?> fs-5"></i>
                <span class="fw-medium"><?= htmlspecialchars($msgText) ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- ── Stats Cards ───────────────────────────────── -->
            <div class="row g-3 mb-4">
                <?php
                $statCards = [
                    ['label'=>'Total Users',    'key'=>'total',         'icon'=>'fa-users',      'bg'=>'bg-primary-subtle', 'color'=>'text-primary'],
                    ['label'=>'Active',         'key'=>'active',        'icon'=>'fa-user-check', 'bg'=>'bg-success-subtle', 'color'=>'text-success'],
                    ['label'=>'2FA Enabled',    'key'=>'twofa',         'icon'=>'fa-shield-alt', 'bg'=>'bg-info-subtle',    'color'=>'text-info'],
                    ['label'=>'Reset Pending',  'key'=>'reset_pending', 'icon'=>'fa-key',        'bg'=>'bg-warning-subtle', 'color'=>'text-warning'],
                    ['label'=>'New This Month', 'key'=>'new_month',     'icon'=>'fa-user-plus',  'bg'=>'bg-danger-subtle',  'color'=>'text-danger'],
                ];
                foreach ($statCards as $sc):
                ?>
                <div class="col-6 col-lg">
                    <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="stat-icon <?= $sc['bg'] ?> <?= $sc['color'] ?>">
                                <i class="fa <?= $sc['icon'] ?>"></i>
                            </div>
                            <div>
                                <div class="text-muted fw-bold text-uppercase mb-0" style="font-size:.65rem;letter-spacing:.5px;"><?= $sc['label'] ?></div>
                                <div class="fw-bolder text-dark" id="stat-<?= $sc['key'] ?>" style="font-size:1.5rem;line-height:1.2;">
                                    <?= (int)($stats[$sc['key']] ?? 0) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- ── Table Card ────────────────────────────────── -->
            <form method="POST" id="bulkForm">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <!-- Card Header -->
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">

                        <!-- Left: Title + Badge -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="mb-0 fw-bold text-dark">All Users</h5>
                            <span id="totalBadge" class="badge bg-secondary-subtle text-secondary-emphasis border rounded-pill px-3 py-1 fw-semibold">
                                <?= $totalRecords ?> users
                            </span>
                        </div>

                        <!-- Right: Filters + Search -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Role Filter -->
                            <div class="d-flex gap-1 flex-wrap" id="roleFilters">
                                <button type="button" class="filter-pill active" data-role="">All</button>
                                <button type="button" class="filter-pill" data-role="superadmin">Super Admin</button>
                                <button type="button" class="filter-pill" data-role="admin">Admin</button>
                                <button type="button" class="filter-pill" data-role="editor">Editor</button>
                                <button type="button" class="filter-pill" data-role="viewer">Viewer</button>
                            </div>

                            <!-- Status Filter -->
                            <select id="statusFilter" class="form-select form-select-sm rounded-pill border" style="width:auto;">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>

                            <!-- Search -->
                            <div class="search-wrap">
                                <div class="input-group input-group-sm" style="min-width:220px;">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    <input type="text" id="searchInput"
                                        class="form-control border-start-0 border-end-0 bg-light shadow-none"
                                        placeholder="Search name, email, phone..."
                                        value="<?= htmlspecialchars($search) ?>"
                                        autocomplete="off">
                                    <button type="button" class="input-group-text bg-light border-start-0 text-muted" id="clearSearch"
                                        style="display:<?= $search ? 'flex' : 'none' ?>;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                                <span class="search-spinner" id="searchSpinner">
                                    <span class="spinner-border spinner-border-sm text-secondary"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Action Bar -->
                <div id="bulkBar" class="mx-3 mt-3">
                    <i class="fa fa-check-square"></i>
                    <span id="bulkCount" class="fw-bold">0</span> selected
                    <button type="submit" name="bulk_delete" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 ms-2"
                        onclick="return confirm('Delete selected users permanently?')">
                        <i class="fa fa-trash-alt me-1"></i> Delete Selected
                    </button>
                    <button type="button" class="btn btn-sm btn-light text-white fw-bold rounded-pill px-3 ms-auto"
                        onclick="clearSelection()" style="background:rgba(255,255,255,.15);border:none;">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                </div>

                <!-- Table -->
                <div class="table-wrapper">
                    <div id="tableLoader">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-2" style="width:2rem;height:2rem;"></div>
                            <div class="small text-muted fw-medium">Loading users…</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover-soft table-borderless align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.5px;">
                                    <th class="ps-4 py-3" style="width:40px;">
                                        <input type="checkbox" class="row-check" id="checkAll">
                                    </th>
                                    <th class="py-3 fw-bold">User</th>
                                    <th class="py-3 fw-bold">Role</th>
                                    <th class="py-3 fw-bold">Contact</th>
                                    <th class="py-3 fw-bold text-center">Status</th>
                                    <th class="py-3 fw-bold text-center">2FA</th>
                                    <th class="py-3 fw-bold">Last Login</th>
                                    <th class="py-3 fw-bold">Joined</th>
                                    <th class="py-3 fw-bold text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <?php
                                $avatarColors = ['#0d6efd','#198754','#dc3545','#fd7e14','#6f42c1','#0dcaf0','#d63384'];
                                if (empty($users)):
                                ?>
                                <tr><td colspan="9" class="p-0">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa fa-users fs-1 text-secondary opacity-50"></i></div>
                                        <h5 class="fw-bold text-dark">No users found</h5>
                                        <p class="text-muted mb-4 small">Add your first admin user to get started.</p>
                                        <a href="add" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                            <i class="fa fa-plus me-2"></i>Add User
                                        </a>
                                    </div>
                                </td></tr>
                                <?php else:
                                foreach ($users as $i => $user):
                                    $initials = strtoupper(substr($user['name'], 0, 1));
                                    $color    = $avatarColors[$user['id'] % count($avatarColors)];
                                    echo renderUserRow($user, $initials, $color);
                                endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="card-footer bg-white border-top py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="small text-muted fw-medium" id="paginationInfo">
                        Showing <b><?= $offset + 1 ?></b> to <b><?= min($offset + $limit, $totalRecords) ?></b>
                        of <b><?= $totalRecords ?></b> users
                    </div>
                    <nav><ul class="pagination pagination-sm mb-0" id="paginationList"></ul></nav>
                </div>

            </div>
            </form>

        </div>
    </div>
</div>

<?php
// ── PHP Row Renderer (used on initial load) ───────────────────
function renderUserRow($user, $initials, $color) {
    $avatarSrc = !empty($user['avatar']) ? (defined('SITE_URL') ? SITE_URL : '') . '/' . ltrim($user['avatar'],'/') : '';
    $roleBadge = [
        'superadmin' => 'role-superadmin',
        'admin'      => 'role-admin',
        'editor'     => 'role-editor',
        'viewer'     => 'role-viewer',
    ][$user['role'] ?? 'admin'] ?? 'role-admin';
    $roleLabel  = ucfirst($user['role'] ?? 'admin');
    $statusDot  = $user['status'] ? 'bg-success' : 'bg-secondary';
    $statusText = $user['status'] ? 'Active'     : 'Inactive';
    $twoFa      = $user['two_fa_enabled'] ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2 fw-semibold" style="font-size:.68rem;"><i class="fa fa-shield-alt me-1"></i>ON</span>'
                                          : '<span class="badge bg-light text-muted border rounded-pill py-1 px-2 fw-semibold" style="font-size:.68rem;">OFF</span>';
    $lastLogin  = !empty($user['last_login'])
                    ? '<div class="small text-dark fw-medium">' . date('M d, Y', strtotime($user['last_login'])) . '</div>'
                      . '<div class="small text-muted">' . date('h:i A', strtotime($user['last_login'])) . '</div>'
                    : '<span class="small text-muted">Never</span>';
    $joined     = !empty($user['created_at'])
                    ? '<div class="small text-dark fw-medium">' . date('M d, Y', strtotime($user['created_at'])) . '</div>'
                      . '<div class="small text-muted">' . date('h:i A', strtotime($user['created_at'])) . '</div>'
                    : '—';
    $avatar     = $avatarSrc
                    ? '<img src="' . htmlspecialchars($avatarSrc) . '" class="avatar-cell shadow-sm" alt=""
                           onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">
                       <div class="avatar-initials shadow-sm" style="background:' . $color . ';display:none;">' . $initials . '</div>'
                    : '<div class="avatar-initials shadow-sm" style="background:' . $color . ';">' . $initials . '</div>';

    return '<tr class="border-bottom border-light" data-id="' . $user['id'] . '">
        <td class="ps-4 py-3">
            <input type="checkbox" name="selected_ids[]" value="' . $user['id'] . '" class="row-check row-checkbox">
        </td>
        <td class="py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex flex-shrink-0">' . $avatar . '</div>
                <div>
                    <div class="fw-bold text-dark">' . htmlspecialchars($user['name']) . '</div>
                    <div class="small text-muted"><a href="mailto:' . htmlspecialchars($user['email']) . '" class="text-muted text-decoration-none">' . htmlspecialchars($user['email']) . '</a></div>
                </div>
            </div>
        </td>
        <td class="py-3"><span class="role-badge ' . $roleBadge . '">' . $roleLabel . '</span></td>
        <td class="py-3">
            <div class="small text-dark fw-medium">' . htmlspecialchars($user['phone'] ?? '—') . '</div>
        </td>
        <td class="py-3 text-center">
            <span class="d-inline-flex align-items-center gap-1 badge ' . ($user['status'] ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-secondary-subtle text-secondary-emphasis border') . ' rounded-pill px-3 py-2 fw-semibold" style="font-size:.7rem;">
                <span class="status-dot ' . $statusDot . '"></span>' . $statusText . '
            </span>
        </td>
        <td class="py-3 text-center">' . $twoFa . '</td>
        <td class="py-3">' . $lastLogin . '</td>
        <td class="py-3">' . $joined . '</td>
        <td class="py-3 text-end pe-4">
            <div class="d-flex justify-content-end gap-1">
                <a href="edit?id=' . $user['id'] . '"
                    class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                    style="width:32px;height:32px;" data-bs-toggle="tooltip" title="Edit">
                    <i class="fa fa-pencil-alt text-secondary" style="font-size:.75rem;"></i>
                </a>
                <a href="./?delete=' . $user['id'] . '"
                    class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                    style="width:32px;height:32px;" data-bs-toggle="tooltip" title="Delete"
                    onclick="return confirm(\'Delete ' . addslashes(htmlspecialchars($user['name'])) . '? This cannot be undone.\')">
                    <i class="fa fa-trash-alt text-danger" style="font-size:.75rem;"></i>
                </a>
            </div>
        </td>
    </tr>';
}
?>

<script>
// ── State ─────────────────────────────────────────────────────
var _state = {
    page:   <?= $page ?>,
    total:  <?= $totalPages ?>,
    search: <?= json_encode($search) ?>,
    role:   '',
    status: '',
    timer:  null
};

const avatarColors = ['#0d6efd','#198754','#dc3545','#fd7e14','#6f42c1','#0dcaf0','#d63384'];

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    renderPagination(_state.page, _state.total);

    // Search
    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(_state.timer);
        document.getElementById('searchSpinner').classList.add('active');
        document.getElementById('clearSearch').style.display = this.value ? 'flex' : 'none';
        const q = this.value.trim();
        _state.timer = setTimeout(() => {
            _state.search = q;
            _state.page   = 1;
            document.getElementById('searchSpinner').classList.remove('active');
            fetchUsers();
        }, 400);
    });

    document.getElementById('clearSearch').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        this.style.display = 'none';
        _state.search = '';
        _state.page   = 1;
        fetchUsers();
    });

    // Role filter
    document.getElementById('roleFilters').addEventListener('click', function (e) {
        const btn = e.target.closest('.filter-pill');
        if (!btn) return;
        document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        _state.role = btn.dataset.role;
        _state.page = 1;
        fetchUsers();
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function () {
        _state.status = this.value;
        _state.page   = 1;
        fetchUsers();
    });

    // Check all
    document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkBar();
    });
});

// ── Fetch Users via AJAX ──────────────────────────────────────
function fetchUsers() {
    document.getElementById('tableLoader').classList.add('active');
    const params = new URLSearchParams({
        ajax:   1,
        page:   _state.page,
        search: _state.search,
        role:   _state.role,
        status: _state.status
    });

    fetch('index.php?' + params)
        .then(r => r.json())
        .then(data => {
            _state.total = data.totalPages;
            _state.page  = data.currentPage;

            // Update stats
            const sm = {
                'stat-total':         data.stats.total,
                'stat-active':        data.stats.active,
                'stat-twofa':         data.stats.twofa,
                'stat-reset_pending': data.stats.reset_pending,
                'stat-new_month':     data.stats.new_month,
            };
            Object.entries(sm).forEach(([id, val]) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val ?? 0;
            });

            // Update badge
            document.getElementById('totalBadge').textContent = data.total + ' users';

            // Render rows
            const tbody = document.getElementById('usersTableBody');
            if (data.rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="p-0">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa fa-users fs-1 text-secondary opacity-50"></i></div>
                        <h5 class="fw-bold text-dark">No users found</h5>
                        <p class="text-muted small mb-4">Try adjusting your search or filters.</p>
                    </div>
                </td></tr>`;
            } else {
                tbody.innerHTML = data.rows.map(u => buildRow(u)).join('');
                // Re-init tooltips
                tbody.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
            }

            // Update pagination info
            const offset = (_state.page - 1) * 10;
            document.getElementById('paginationInfo').innerHTML =
                `Showing <b>${offset + 1}</b> to <b>${Math.min(offset + 10, data.total)}</b> of <b>${data.total}</b> users`;

            // Render pagination
            renderPagination(_state.page, _state.total);

            // Re-attach row checkboxes
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });

            document.getElementById('tableLoader').classList.remove('active');
        })
        .catch(() => {
            document.getElementById('tableLoader').classList.remove('active');
        });
}

// ── Build Row HTML from JSON ──────────────────────────────────
function buildRow(u) {
    const color    = avatarColors[u.id % avatarColors.length];
    const initial  = (u.name || '?')[0].toUpperCase();
    const roleMap  = { superadmin:'role-superadmin', admin:'role-admin', editor:'role-editor', viewer:'role-viewer' };
    const roleClass = roleMap[u.role] || 'role-admin';
    const roleLabel = (u.role || 'admin').charAt(0).toUpperCase() + (u.role || 'admin').slice(1);
    const status    = parseInt(u.status);
    const twofa     = parseInt(u.two_fa_enabled);

    const avatarHtml = u.avatar
        ? `<img src="${escHtml(u.avatar)}" class="avatar-cell shadow-sm" alt=""
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
           <div class="avatar-initials shadow-sm" style="background:${color};display:none;">${initial}</div>`
        : `<div class="avatar-initials shadow-sm" style="background:${color};">${initial}</div>`;

    const lastLogin = u.last_login
        ? `<div class="small text-dark fw-medium">${fmtDate(u.last_login)}</div><div class="small text-muted">${fmtTime(u.last_login)}</div>`
        : `<span class="small text-muted">Never</span>`;
    const joined = u.created_at
        ? `<div class="small text-dark fw-medium">${fmtDate(u.created_at)}</div><div class="small text-muted">${fmtTime(u.created_at)}</div>`
        : '—';

    return `<tr class="border-bottom border-light" data-id="${u.id}">
        <td class="ps-4 py-3">
            <input type="checkbox" name="selected_ids[]" value="${u.id}" class="row-check row-checkbox">
        </td>
        <td class="py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex flex-shrink-0">${avatarHtml}</div>
                <div>
                    <div class="fw-bold text-dark">${escHtml(u.name)}</div>
                    <div class="small text-muted"><a href="mailto:${escHtml(u.email)}" class="text-muted text-decoration-none">${escHtml(u.email)}</a></div>
                </div>
            </div>
        </td>
        <td class="py-3"><span class="role-badge ${roleClass}">${roleLabel}</span></td>
        <td class="py-3"><div class="small text-dark fw-medium">${escHtml(u.phone || '—')}</div></td>
        <td class="py-3 text-center">
            <span class="d-inline-flex align-items-center gap-1 badge ${status ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-secondary-subtle text-secondary-emphasis border'} rounded-pill px-3 py-2 fw-semibold" style="font-size:.7rem;">
                <span class="status-dot ${status ? 'bg-success' : 'bg-secondary'}"></span>${status ? 'Active' : 'Inactive'}
            </span>
        </td>
        <td class="py-3 text-center">
            ${twofa
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2 fw-semibold" style="font-size:.68rem;"><i class="fa fa-shield-alt me-1"></i>ON</span>'
                : '<span class="badge bg-light text-muted border rounded-pill py-1 px-2 fw-semibold" style="font-size:.68rem;">OFF</span>'
            }
        </td>
        <td class="py-3">${lastLogin}</td>
        <td class="py-3">${joined}</td>
        <td class="py-3 text-end pe-4">
            <div class="d-flex justify-content-end gap-1">
                <a href="edit?id=${u.id}"
                    class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                    style="width:32px;height:32px;" data-bs-toggle="tooltip" title="Edit">
                    <i class="fa fa-pencil-alt text-secondary" style="font-size:.75rem;"></i>
                </a>
                <a href="./?delete=${u.id}"
                    class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                    style="width:32px;height:32px;" data-bs-toggle="tooltip" title="Delete"
                    onclick="return confirm('Delete ${escHtml(u.name)}? This cannot be undone.')">
                    <i class="fa fa-trash-alt text-danger" style="font-size:.75rem;"></i>
                </a>
            </div>
        </td>
    </tr>`;
}

// ── Bulk Checkbox ─────────────────────────────────────────────
function updateBulkBar() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    const bar     = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = checked;
    bar.classList.toggle('active', checked > 0);
}
function clearSelection() {
    document.querySelectorAll('.row-checkbox, #checkAll').forEach(cb => cb.checked = false);
    document.getElementById('bulkBar').classList.remove('active');
}
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-checkbox')) updateBulkBar();
});

// ── Pagination ────────────────────────────────────────────────
function renderPagination(currentPage, totalPages) {
    const ul = document.getElementById('paginationList');
    if (!ul) return;
    ul.innerHTML = '';
    if (totalPages <= 1) return;

    const maxV = 5, half = Math.floor(maxV / 2);
    let start = Math.max(1, currentPage - half);
    let end   = Math.min(totalPages, start + maxV - 1);
    if (end - start + 1 < maxV) start = Math.max(1, end - maxV + 1);

    const mkLi = (p, label, disabled, active) => {
        const li = document.createElement('li');
        li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
        const a = document.createElement('a');
        a.className = 'page-link' + (active ? ' bg-primary border-primary text-white' : ' text-dark');
        a.href = '#';
        a.innerHTML = label;
        if (!disabled) a.addEventListener('click', e => { e.preventDefault(); _state.page = p; fetchUsers(); });
        li.appendChild(a); return li;
    };
    const mkDots = () => { const li = document.createElement('li'); li.className='page-item disabled'; li.innerHTML="<span class='page-link'>…</span>"; return li; };

    ul.appendChild(mkLi(currentPage - 1, 'Prev', currentPage <= 1, false));
    if (start > 1)          { ul.appendChild(mkLi(1, '1', false, false)); if (start > 2) ul.appendChild(mkDots()); }
    for (let p = start; p <= end; p++) ul.appendChild(mkLi(p, p, false, p === currentPage));
    if (end < totalPages)   { if (end < totalPages - 1) ul.appendChild(mkDots()); ul.appendChild(mkLi(totalPages, totalPages, false, false)); }
    ul.appendChild(mkLi(currentPage + 1, 'Next', currentPage >= totalPages, false));
}

// ── Helpers ───────────────────────────────────────────────────
function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fmtDate(dt) {
    const d = new Date(dt.replace(' ','T'));
    return d.toLocaleDateString('en-US', { month:'short', day:'2-digit', year:'numeric' });
}
function fmtTime(dt) {
    const d = new Date(dt.replace(' ','T'));
    return d.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' });
}
</script>

<?php require_once '../include/footer.php'; ?>