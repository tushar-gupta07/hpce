<?php
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/include/auth.php';

// ── Doctors ───────────────────────────────────────────────────────────────────
$totalDoctors     = (int)$conn->query("SELECT COUNT(*) AS c FROM doctors")->fetch_assoc()['c'];
$publishedDoctors = (int)$conn->query("SELECT COUNT(*) AS c FROM doctors WHERE is_published=1")->fetch_assoc()['c'];
$totalDoctorViews = (int)$conn->query("SELECT COALESCE(SUM(views),0) AS c FROM doctors")->fetch_assoc()['c'];

// ── Blogs ─────────────────────────────────────────────────────────────────────
$totalBlogs      = (int)$conn->query("SELECT COUNT(*) AS c FROM blogs")->fetch_assoc()['c'];
$publishedBlogs  = (int)$conn->query("SELECT COUNT(*) AS c FROM blogs WHERE is_published=1")->fetch_assoc()['c'];
$draftBlogs      = $totalBlogs - $publishedBlogs;
$engRow          = $conn->query("SELECT COALESCE(SUM(views),0) AS v, COALESCE(SUM(comments),0) AS c FROM blogs")->fetch_assoc();
$totalBlogViews  = (int)$engRow['v'];
$totalComments   = (int)$engRow['c'];

// ── Services ──────────────────────────────────────────────────────────────────
$totalServices     = (int)$conn->query("SELECT COUNT(*) AS c FROM services")->fetch_assoc()['c'];
$publishedServices = (int)$conn->query("SELECT COUNT(*) AS c FROM services WHERE is_published=1")->fetch_assoc()['c'];

// ── Categories ────────────────────────────────────────────────────────────────
$totalCategories = (int)$conn->query("SELECT COUNT(*) AS c FROM categories")->fetch_assoc()['c'];

// ── Admin Users ───────────────────────────────────────────────────────────────
$totalUsers      = (int)$conn->query("SELECT COUNT(*) AS c FROM admin_users")->fetch_assoc()['c'];
$activeUsers     = (int)$conn->query("SELECT COUNT(*) AS c FROM admin_users WHERE status=1")->fetch_assoc()['c'];

// ── Doctor specialty breakdown ────────────────────────────────────────────────
$specialties = [];
$res = $conn->query("SELECT specialty, COUNT(*) AS total FROM doctors WHERE specialty != '' GROUP BY specialty ORDER BY total DESC LIMIT 5");
if ($res) while ($r = $res->fetch_assoc()) $specialties[] = $r;

// ── Blogs per category ────────────────────────────────────────────────────────
$catStats = [];
$res = $conn->query("
    SELECT c.name, COUNT(b.id) AS total,
           SUM(b.is_published) AS published,
           COALESCE(SUM(b.views),0) AS views
    FROM categories c
    LEFT JOIN blogs b ON b.category_id = c.id
    GROUP BY c.id, c.name
    ORDER BY total DESC
");
if ($res) while ($r = $res->fetch_assoc()) $catStats[] = $r;

// ── Top 5 blogs by views ──────────────────────────────────────────────────────
$topBlogs = [];
$res = $conn->query("
    SELECT b.id, b.title, b.views, b.comments, b.is_published,
           c.name AS category, d.name AS doctor
    FROM blogs b
    LEFT JOIN categories c ON c.id = b.category_id
    LEFT JOIN doctors d ON d.id = b.doctor_id
    ORDER BY b.views DESC
    LIMIT 5
");
if ($res) while ($r = $res->fetch_assoc()) $topBlogs[] = $r;

// ── Recent blogs ──────────────────────────────────────────────────────────────
$recentBlogs = [];
$res = $conn->query("
    SELECT b.id, b.title, b.is_published, b.views, b.comments,
           b.created_at, c.name AS category, d.name AS doctor
    FROM blogs b
    LEFT JOIN categories c ON c.id = b.category_id
    LEFT JOIN doctors d ON d.id = b.doctor_id
    ORDER BY b.created_at DESC
    LIMIT 6
");
if ($res) while ($r = $res->fetch_assoc()) $recentBlogs[] = $r;

// ── Doctor activity (blogs written + views) ────────────────────────────────────
$doctorActivity = [];
$res = $conn->query("
    SELECT d.id, d.name, d.photo, d.specialty, d.satisfaction_rate,
           d.feedback_count, d.is_published,
           COUNT(b.id) AS blog_count,
           COALESCE(SUM(b.views),0) AS blog_views
    FROM doctors d
    LEFT JOIN blogs b ON b.doctor_id = d.id
    GROUP BY d.id, d.name, d.photo, d.specialty, d.satisfaction_rate, d.feedback_count, d.is_published
    ORDER BY d.id DESC
    LIMIT 6
");
if ($res) while ($r = $res->fetch_assoc()) $doctorActivity[] = $r;

// ── Recent activity log ───────────────────────────────────────────────────────
$activityLog = [];
$res = $conn->query("
    SELECT l.action, l.detail, l.ip, l.created_at, u.name AS user_name
    FROM admin_activity_log l
    LEFT JOIN admin_users u ON u.id = l.user_id
    ORDER BY l.created_at DESC
    LIMIT 6
");
if ($res) while ($r = $res->fetch_assoc()) $activityLog[] = $r;

// ── Admin users with role ─────────────────────────────────────────────────────
$adminUsers = [];
$res = $conn->query("SELECT id, name, email, role, status, login_count, last_login, created_at FROM admin_users ORDER BY created_at DESC");
if ($res) while ($r = $res->fetch_assoc()) $adminUsers[] = $r;

// ── Page meta ─────────────────────────────────────────────────────────────────
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
$assetBase  = '';
require_once __DIR__ . '/include/head.php';
?>

    <?php require_once __DIR__ . '/include/header.php'; ?>
    <?php require_once __DIR__ . '/include/sidebar.php'; ?>

    <div class="page-wrapper">
        <div class="content container-fluid">

            <?php if (isset($_GET['err']) && $_GET['err'] === 'noperm'): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fa fa-lock me-2"></i>
                <strong>Access Denied.</strong> You don't have permission to access that section.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Welcome, <?= htmlspecialchars($_ADMIN['name']) ?>!</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ul>
                    </div>
                    <div class="col-auto text-muted" style="font-size:13px;">
                        <i class="fe fe-calendar me-1"></i><?= date('l, d M Y') ?>
                    </div>
                </div>
            </div>

            <!-- ── Row 1: Stat Cards ── -->
            <div class="row">

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-primary border-primary">
                                    <i class="fe fe-user-plus"></i>
                                </span>
                                <div class="dash-count">
                                    <h3><?= $totalDoctors ?></h3>
                                </div>
                            </div>
                            <div class="dash-widget-info">
                                <h6 class="text-muted">
                                    Doctors
                                    <span class="badge bg-success ms-1" style="font-size:10px;"><?= $publishedDoctors ?> active</span>
                                </h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width:<?= $totalDoctors > 0 ? round(($publishedDoctors/$totalDoctors)*100) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-success">
                                    <i class="fe fe-edit"></i>
                                </span>
                                <div class="dash-count">
                                    <h3><?= $totalBlogs ?></h3>
                                </div>
                            </div>
                            <div class="dash-widget-info">
                                <h6 class="text-muted">
                                    Blogs
                                    <span class="badge bg-success ms-1" style="font-size:10px;"><?= $publishedBlogs ?> live</span>
                                    <?php if ($draftBlogs > 0): ?>
                                    <span class="badge bg-secondary ms-1" style="font-size:10px;"><?= $draftBlogs ?> draft</span>
                                    <?php endif; ?>
                                </h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" style="width:<?= $totalBlogs > 0 ? round(($publishedBlogs/$totalBlogs)*100) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-warning border-warning">
                                    <i class="fe fe-layout"></i>
                                </span>
                                <div class="dash-count">
                                    <h3><?= $totalServices ?></h3>
                                </div>
                            </div>
                            <div class="dash-widget-info">
                                <h6 class="text-muted">
                                    Services
                                    <span class="badge bg-success ms-1" style="font-size:10px;"><?= $publishedServices ?> live</span>
                                </h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning" style="width:<?= $totalServices > 0 ? round(($publishedServices/$totalServices)*100) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-danger border-danger">
                                    <i class="fe fe-eye"></i>
                                </span>
                                <div class="dash-count">
                                    <h3><?= number_format($totalBlogViews) ?></h3>
                                </div>
                            </div>
                            <div class="dash-widget-info">
                                <h6 class="text-muted">
                                    Blog Views
                                    <span class="badge bg-info ms-1" style="font-size:10px;"><?= number_format($totalComments) ?> comments</span>
                                </h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-danger" style="width:<?= min(100, round($totalBlogViews / 10)) ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Row 2: Top Blogs + Category Breakdown ── -->
            <div class="row">

                <div class="col-md-7 d-flex">
                    <div class="card card-table flex-fill">
                        <div class="card-header">
                            <h4 class="card-title">Top Blogs by Views</h4>
                            <a href="<?= SITE_URL ?>/admin/blog/add" class="btn btn-sm btn-primary float-end">+ New Blog</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Doctor</th>
                                            <th>Views</th>
                                            <th>Comments</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($topBlogs)): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No blogs found.</td></tr>
                                        <?php else: foreach ($topBlogs as $i => $b): ?>
                                        <tr>
                                            <td><span class="badge bg-primary"><?= $i+1 ?></span></td>
                                            <td style="max-width:190px;">
                                                <a href="<?= SITE_URL ?>/admin/blog/edit?id=<?= $b['id'] ?>" class="text-dark" style="font-size:13px;font-weight:500;">
                                                    <?= htmlspecialchars(mb_strimwidth($b['title'],0,42,'…')) ?>
                                                </a>
                                                <div><span class="badge bg-info" style="font-size:10px;"><?= htmlspecialchars($b['category'] ?? '—') ?></span></div>
                                            </td>
                                            <td style="font-size:12px;color:#6c757d;"><?= htmlspecialchars($b['doctor'] ?? '—') ?></td>
                                            <td><i class="fe fe-eye text-warning me-1" style="font-size:12px;"></i><strong><?= number_format($b['views']) ?></strong></td>
                                            <td><i class="fe fe-message-square text-info me-1" style="font-size:12px;"></i><?= number_format($b['comments']) ?></td>
                                            <td>
                                                <?= $b['is_published']
                                                    ? '<span class="badge bg-success-light text-success">Published</span>'
                                                    : '<span class="badge bg-danger-light text-danger">Draft</span>' ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h4 class="card-title">Blogs by Category</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            $colors  = ['primary','success','warning','danger','info','secondary'];
                            $maxBlogs = max(array_column($catStats,'total') ?: [1]);
                            foreach ($catStats as $ci => $cat):
                                $pct = $maxBlogs > 0 ? round(($cat['total']/$maxBlogs)*100) : 0;
                                $col = $colors[$ci % count($colors)];
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="font-size:13px;font-weight:500;"><?= htmlspecialchars($cat['name']) ?></span>
                                    <span style="font-size:12px;color:#6c757d;">
                                        <?= $cat['total'] ?> blog<?= $cat['total'] != 1 ? 's' : '' ?>
                                        &nbsp;·&nbsp;
                                        <i class="fe fe-eye" style="font-size:11px;"></i> <?= number_format($cat['views']) ?>
                                    </span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-<?= $col ?>" style="width:<?= $pct ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Row 3: Doctors Table ── -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Doctors</h4>
                            <a href="<?= SITE_URL ?>/admin/doctors/add" class="btn btn-sm btn-primary float-end">+ Add Doctor</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Specialty</th>
                                            <th>Satisfaction</th>
                                            <th>Feedbacks</th>
                                            <th>Blogs</th>
                                            <th>Blog Views</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($doctorActivity)): ?>
                                        <tr><td colspan="8" class="text-center text-muted">No doctors found.</td></tr>
                                        <?php else: foreach ($doctorActivity as $doc):
                                            $photo = !empty($doc['photo'])
                                                ? (str_starts_with($doc['photo'],'http') ? $doc['photo'] : SITE_URL.'/'.$doc['photo'])
                                                : SITE_URL.'/assets/img/patients/default.jpg';
                                        ?>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="<?= SITE_URL ?>/admin/doctors/edit?id=<?= $doc['id'] ?>" class="avatar avatar-sm me-2">
                                                        <img class="avatar-img rounded-circle" src="<?= htmlspecialchars($photo) ?>"
                                                             onerror="this.src='<?= SITE_URL ?>/assets/img/patients/default.jpg'" alt="">
                                                    </a>
                                                    <a href="<?= SITE_URL ?>/admin/doctors/edit?id=<?= $doc['id'] ?>" class="text-dark">
                                                        <?= htmlspecialchars($doc['name']) ?>
                                                    </a>
                                                </h2>
                                            </td>
                                            <td>
                                                <?php if (!empty($doc['specialty'])): ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($doc['specialty']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size:12px;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($doc['satisfaction_rate'] > 0): ?>
                                                <div style="min-width:80px;">
                                                    <div class="progress progress-sm mb-1">
                                                        <div class="progress-bar bg-success" style="width:<?= $doc['satisfaction_rate'] ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?= $doc['satisfaction_rate'] ?>%</small>
                                                </div>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size:12px;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($doc['feedback_count']) ?></td>
                                            <td>
                                                <?php if ($doc['blog_count'] > 0): ?>
                                                    <span class="badge bg-primary"><?= $doc['blog_count'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size:12px;">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="fe fe-eye text-warning me-1" style="font-size:12px;"></i>
                                                <?= number_format($doc['blog_views']) ?>
                                            </td>
                                            <td>
                                                <?= $doc['is_published']
                                                    ? '<span class="badge bg-success-light text-success">Active</span>'
                                                    : '<span class="badge bg-danger-light text-danger">Inactive</span>' ?>
                                            </td>
                                            <td>
                                                <a href="<?= SITE_URL ?>/admin/doctors/edit?id=<?= $doc['id'] ?>" class="btn btn-sm btn-white me-1">
                                                    <i class="fe fe-pencil text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Row 4: Recent Blogs + Activity Log ── -->
            <div class="row">

                <!-- Recent Blogs -->
                <div class="col-md-8 d-flex">
                    <div class="card card-table flex-fill">
                        <div class="card-header">
                            <h4 class="card-title">Recent Blogs</h4>
                            <a href="<?= SITE_URL ?>/admin/blog/" class="btn btn-sm btn-primary float-end">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Doctor</th>
                                            <th>Category</th>
                                            <th>Views</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentBlogs)): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No blogs found.</td></tr>
                                        <?php else: foreach ($recentBlogs as $b): ?>
                                        <tr>
                                            <td style="max-width:180px;">
                                                <a href="<?= SITE_URL ?>/admin/blog/edit?id=<?= $b['id'] ?>" class="text-dark" style="font-size:13px;font-weight:500;">
                                                    <?= htmlspecialchars(mb_strimwidth($b['title'],0,38,'…')) ?>
                                                </a>
                                            </td>
                                            <td style="font-size:12px;color:#6c757d;"><?= htmlspecialchars($b['doctor'] ?? '—') ?></td>
                                            <td><span class="badge bg-info" style="font-size:10px;"><?= htmlspecialchars($b['category'] ?? '—') ?></span></td>
                                            <td><?= number_format($b['views']) ?></td>
                                            <td>
                                                <?= $b['is_published']
                                                    ? '<span class="badge bg-success-light text-success">Published</span>'
                                                    : '<span class="badge bg-danger-light text-danger">Draft</span>' ?>
                                            </td>
                                            <td style="font-size:12px;color:#6c757d;"><?= date('d M Y', strtotime($b['created_at'])) ?></td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="col-md-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h4 class="card-title">Recent Activity</h4>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <?php if (empty($activityLog)): ?>
                            <p class="text-muted text-center p-3">No activity yet.</p>
                            <?php else: foreach ($activityLog as $log): ?>
                            <div style="padding:12px 20px;border-bottom:1px solid #f5f5f5;">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="badge bg-primary-light text-primary mt-1" style="font-size:10px;flex-shrink:0;">
                                        <?= htmlspecialchars(str_replace('_',' ', $log['action'])) ?>
                                    </span>
                                    <div>
                                        <div style="font-size:12px;color:#333;font-weight:500;">
                                            <?= htmlspecialchars(mb_strimwidth($log['detail'] ?? '', 0, 55, '…')) ?>
                                        </div>
                                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">
                                            <?= htmlspecialchars($log['user_name'] ?? 'System') ?>
                                            &nbsp;·&nbsp;
                                            <?= date('d M, H:i', strtotime($log['created_at'])) ?>
                                            <?php if (!empty($log['ip'])): ?>
                                            &nbsp;·&nbsp; <span style="font-family:monospace;"><?= htmlspecialchars($log['ip']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Row 5: Admin Users ── -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Admin Users</h4>
                            <a href="<?= SITE_URL ?>/admin/users/add" class="btn btn-sm btn-primary float-end">+ Add User</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Logins</th>
                                            <th>Last Login</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($adminUsers)): ?>
                                        <tr><td colspan="8" class="text-center text-muted">No users found.</td></tr>
                                        <?php else:
                                        $roleBadge = [
                                            'superadmin' => 'bg-danger',
                                            'admin'      => 'bg-primary',
                                            'editor'     => 'bg-info',
                                            'viewer'     => 'bg-secondary',
                                        ];
                                        foreach ($adminUsers as $i => $u): ?>
                                        <tr>
                                            <td><?= $i+1 ?></td>
                                            <td style="font-weight:500;"><?= htmlspecialchars($u['name']) ?></td>
                                            <td style="font-size:13px;color:#6c757d;"><?= htmlspecialchars($u['email']) ?></td>
                                            <td>
                                                <span class="badge <?= $roleBadge[$u['role']] ?? 'bg-secondary' ?>">
                                                    <?= ucfirst(htmlspecialchars($u['role'])) ?>
                                                </span>
                                            </td>
                                            <td><?= number_format($u['login_count']) ?></td>
                                            <td style="font-size:12px;color:#6c757d;">
                                                <?= $u['last_login'] ? date('d M Y, H:i', strtotime($u['last_login'])) : '<span class="text-muted">Never</span>' ?>
                                            </td>
                                            <td>
                                                <?= $u['status']
                                                    ? '<span class="badge bg-success-light text-success">Active</span>'
                                                    : '<span class="badge bg-danger-light text-danger">Inactive</span>' ?>
                                            </td>
                                            <td>
                                                <a href="<?= SITE_URL ?>/admin/users/edit?id=<?= $u['id'] ?>" class="btn btn-sm btn-white">
                                                    <i class="fe fe-pencil text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

<?php require_once __DIR__ . '/include/footer.php'; ?>
