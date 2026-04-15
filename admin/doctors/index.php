<?php
// admin/doctors/index.php
require_once __DIR__ . '/../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('doctors');

$limit  = 10;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchLike = '%' . $conn->real_escape_string($search) . '%';

// ── Delete ───────────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $conn->query("DELETE FROM doctors WHERE id = $deleteId");
    header("Location: ./?msg=deleted");
    exit;
}

// ── Toggle Publish ────────────────────────────────────────────
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $toggleId = (int)$_GET['toggle'];
    $conn->query("UPDATE doctors SET is_published = NOT is_published WHERE id = $toggleId");
    header("Location: ./");
    exit;
}

// ── Fetch Doctors ─────────────────────────────────────────────
$sql = "SELECT * FROM doctors
        WHERE name LIKE '$searchLike'
           OR specialty LIKE '$searchLike'
           OR location LIKE '$searchLike'
        ORDER BY created_at DESC
        LIMIT $limit OFFSET $offset";

$result  = $conn->query($sql);
$doctors = [];
if ($result) { while ($row = $result->fetch_assoc()) { $doctors[] = $row; } }

$countResult  = $conn->query("SELECT COUNT(*) AS total FROM doctors
    WHERE name LIKE '$searchLike' OR specialty LIKE '$searchLike' OR location LIKE '$searchLike'");
$totalRecords = $countResult ? (int)$countResult->fetch_assoc()['total'] : 0;
$totalPages   = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;

// ── Stats ─────────────────────────────────────────────────────
$statsRes = $conn->query("SELECT
    COUNT(*) as total,
    SUM(is_published) as published,
    SUM(!is_published) as drafts,
    SUM(views) as total_views,
    AVG(satisfaction_rate) as avg_satisfaction,
    SUM(feedback_count) as total_feedback
    FROM doctors");
$stats = $statsRes ? $statsRes->fetch_assoc() : [];

// ── Helpers ───────────────────────────────────────────────────
define('BASE_PATH', '/rkhospital/');

function resolveImageSrc($field) {
    if (empty($field)) return '';
    if (str_starts_with($field, 'http://') || str_starts_with($field, 'https://')) return $field;
    return BASE_PATH . ltrim($field, '/');
}

function calcDoctorSeoScore($doc) {
    $score = 0; $issues = []; $good = [];

    $mt = $doc['meta_title'] ?? '';
    if (strlen($mt) >= 50 && strlen($mt) <= 60) { $score += 15; $good[] = 'Meta title perfect length'; }
    elseif (strlen($mt) > 0)                    { $score += 7;  $issues[] = 'Meta title not ideal (50–60 chars)'; }
    else                                         { $issues[] = 'Meta title missing'; }

    $md = $doc['meta_description'] ?? '';
    if (strlen($md) >= 120 && strlen($md) <= 160) { $score += 15; $good[] = 'Meta description perfect'; }
    elseif (strlen($md) > 0)                      { $score += 7;  $issues[] = 'Meta desc not ideal (120–160 chars)'; }
    else                                           { $issues[] = 'Meta description missing'; }

    $kw = strtolower($doc['focus_keyword'] ?? '');
    if (!empty($kw)) {
        $score += 5; $good[] = 'Focus keyword set';
        if (strpos(strtolower($doc['name'] ?? ''), $kw) !== false || strpos(strtolower($doc['specialty'] ?? ''), $kw) !== false)
            { $score += 10; $good[] = 'Keyword in name/specialty'; }
        else { $issues[] = 'Keyword not in name or specialty'; }
        if (strpos(strtolower($md), $kw) !== false) { $score += 10; $good[] = 'Keyword in meta desc'; }
        else { $issues[] = 'Keyword missing from meta desc'; }
        $kwSlug = str_replace(' ', '-', $kw);
        if (strpos($doc['slug'] ?? '', $kwSlug) !== false) { $score += 5; $good[] = 'Keyword in slug'; }
        else { $issues[] = 'Keyword not in slug'; }
    } else {
        $issues[] = 'No focus keyword set';
    }

    if (!empty($doc['photo']) && $doc['photo'] !== 'default.jpg') { $score += 10; $good[] = 'Profile photo uploaded'; }
    else { $issues[] = 'No profile photo'; }

    if (!empty($doc['bio']))           { $score += 10; $good[] = 'Biography filled'; }
    else { $issues[] = 'Biography missing'; }

    if (!empty($doc['og_title']))      { $score += 5;  $good[] = 'OG title set'; }
    else { $issues[] = 'OG title missing'; }

    if (!empty($doc['schema_type']))   { $score += 5;  $good[] = 'Schema markup set'; }
    else { $issues[] = 'No schema type'; }

    if (!empty($doc['education_json']) && $doc['education_json'] !== '[]') { $score += 5; $good[] = 'Education history added'; }
    else { $issues[] = 'No education history'; }

    return ['score' => min(100, $score), 'issues' => $issues, 'good' => $good];
}

function seoGrade($score) {
    if ($score >= 80) return ['A', 'text-success'];
    if ($score >= 65) return ['B', 'text-primary'];
    if ($score >= 50) return ['C', 'text-warning'];
    return ['F', 'text-danger'];
}

function rankPotential($score) {
    if ($score >= 80) return 'Top 10';
    if ($score >= 65) return 'Top 30';
    if ($score >= 50) return 'Top 50';
    return 'Low';
}

// ── Page Setup ─────────────────────────────────────────────────
$pageTitle  = 'Manage Doctors';
$activePage = 'doctors-index';
$assetBase  = '../';

$extraCSS = '
<style>

.table-hover-soft tbody tr:hover { background-color: #f8f9fa !important; }


#doctorsTableBody td { white-space: normal !important; }
#doctorsTableBody .text-truncate { 
    white-space: normal !important; 
    overflow: visible !important;
    text-overflow: unset !important;
    max-width: 250px !important;
}
    .stat-card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-card-hover:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
    .table-hover-soft tbody tr { transition: background-color 0.15s ease; }
    .table-hover-soft tbody tr:hover { background-color: #f8f9fa !important; }
    .doctor-thumb {
        width: 56px; height: 56px; object-fit: cover;
        border-radius: 50%; flex-shrink: 0; display: block;
        border: 2px solid #e9ecef;
    }
    .doctor-thumb-placeholder {
        width: 56px; height: 56px; border-radius: 50%; flex-shrink: 0;
        background: #e9ecef; display: flex; align-items: center;
        justify-content: center; color: #adb5bd; font-size: 1.25rem;
        border: 2px solid #dee2e6;
    }
    #tableLoader {
        display: none;
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.75);
        z-index: 10; align-items: center; justify-content: center;
        border-radius: 0 0 1rem 1rem;
    }
    #tableLoader.active { display: flex; }
    .table-wrapper { position: relative; min-height: 200px; }
    .search-bar-wrap { position: relative; width: 320px; max-width: 100%; }
    #searchSpinner { display: none; position: absolute; right: 14px; top: 50%; transform: translateY(-50%); }
    #searchSpinner.active { display: block; }
    .page-link { transition: all 0.15s ease; }
    .pagination .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
</style>
';

require_once '../include/head.php';
?>

<div class="main-wrapper">

    <?php require_once '../include/header.php'; ?>
    <?php require_once '../include/sidebar.php'; ?>

    <div class="page-wrapper" style="background-color: #f4f6f9; min-height: 100vh;">
        <div class="content container-fluid pt-4 pb-5">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bolder text-dark mb-1">Doctors Directory</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active text-secondary fw-medium">Doctors</li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="add" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="fa fa-plus"></i> Add Doctor
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_GET['msg'])):
                $msgMap = [
                    'deleted' => ['danger',  'Doctor profile removed permanently.', 'fa-trash-alt'],
                    'added'   => ['success', 'New doctor profile added successfully.', 'fa-check-circle'],
                    'updated' => ['success', 'Doctor profile updated successfully.', 'fa-check-circle'],
                ];
                [$msgType, $msgText, $msgIcon] = $msgMap[$_GET['msg']] ?? ['success', 'Action completed.', 'fa-check'];
            ?>
            <div class="alert alert-<?= $msgType ?> border-0 shadow-sm alert-dismissible fade show d-flex align-items-center gap-3 rounded-3" role="alert">
                <i class="fa <?= $msgIcon ?> fs-4"></i>
                <div class="fw-medium"><?= htmlspecialchars($msgText) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing:0.5px;">Total Doctors</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= (int)($stats['total'] ?? 0) ?></h2>
                            </div>
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:54px;height:54px;">
                                <i class="fa fa-user-md fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing:0.5px;">Published</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= (int)($stats['published'] ?? 0) ?></h2>
                            </div>
                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width:54px;height:54px;">
                                <i class="fa fa-globe-americas fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing:0.5px;">Total Views</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= number_format((int)($stats['total_views'] ?? 0)) ?></h2>
                            </div>
                            <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width:54px;height:54px;">
                                <i class="fa fa-eye fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing:0.5px;">Avg Satisfaction</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= number_format((float)($stats['avg_satisfaction'] ?? 0), 1) ?>%</h2>
                            </div>
                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:54px;height:54px;">
                                <i class="fa fa-star fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <!-- Card Header -->
                <div class="card-header bg-white border-bottom py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">All Doctors</h5>
                        <span id="totalBadge" class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-1 fw-semibold border border-secondary-subtle">
                            <?= $totalRecords ?> doctors
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="search-bar-wrap">
                            <div class="input-group input-group-sm rounded-pill border bg-light p-1">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="text" id="searchInput"
                                    class="form-control border-0 shadow-none bg-transparent ps-1"
                                    placeholder="Search by name, specialty, location..."
                                    value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                                <span id="searchSpinner">
                                    <span class="spinner-border spinner-border-sm text-secondary" role="status"></span>
                                </span>
                            </div>
                        </div>
                        <button id="clearSearchBtn" class="btn btn-sm btn-light rounded-pill border px-3 fw-medium"
                            style="display: <?= $search ? 'inline-block' : 'none' ?>;">Clear</button>
                    </div>
                </div>

                <!-- Table Wrapper -->
                <div class="table-wrapper">
                    <div id="tableLoader">
                        <div class="d-flex flex-column align-items-center gap-2">
                            <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem;"></div>
                            <span class="small text-muted fw-medium">Loading…</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover-soft table-borderless align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase text-muted" style="font-size:0.75rem;letter-spacing:0.5px;">
                                    <th class="ps-4 py-3 fw-bold">Doctor</th>
                                    <th class="py-3 fw-bold">Specialty</th>
                                    <th class="py-3 fw-bold text-center">SEO Health</th>
                                    <th class="py-3 fw-bold">Performance</th>
                                    <th class="py-3 fw-bold">Status</th>
                                    <th class="py-3 fw-bold text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="doctorsTableBody">
                                <?php if (empty($doctors)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5">
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                                                <i class="fa fa-user-md fs-1 text-secondary opacity-50"></i>
                                            </div>
                                            <h5 class="text-dark fw-bold">No doctors found</h5>
                                            <p class="text-muted mb-4">Add your first doctor profile to get started.</p>
                                            <a href="add" class="btn btn-primary rounded-pill px-4 shadow-sm">Add Doctor</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($doctors as $doc):
                                    $seo     = calcDoctorSeoScore($doc);
                                    $score   = $seo['score'];
                                    [$grade, $gradeTextColor] = seoGrade($score);
                                    $issueCount  = count($seo['issues']);
                                    $gradeBgClass = str_replace('text-', 'bg-', $gradeTextColor);
                                    $imgSrc = resolveImageSrc($doc['photo'] ?? '');
                                    $satisfaction = (int)($doc['satisfaction_rate'] ?? 0);
                                    $satColor = $satisfaction >= 90 ? 'text-success' : ($satisfaction >= 70 ? 'text-warning' : 'text-danger');
                                    $satBg    = $satisfaction >= 90 ? 'bg-success'   : ($satisfaction >= 70 ? 'bg-warning'   : 'bg-danger');
                                ?>
                                <tr class="border-bottom border-light">

                                  <!-- Doctor Name & Photo -->
<td class="ps-4 py-3" style="max-width:260px;">
    <div class="d-flex align-items-center gap-3">
        <?php if (!empty($imgSrc) && $doc['photo'] !== 'default.jpg'): ?>
        <img src="<?= htmlspecialchars($imgSrc) ?>"
            class="doctor-thumb shadow-sm"
            alt="<?= htmlspecialchars($doc['name']) ?>"
            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
        <div class="doctor-thumb-placeholder" style="display:none;"><i class="fa fa-user-md"></i></div>
        <?php else: ?>
        <div class="doctor-thumb-placeholder"><i class="fa fa-user-md"></i></div>
        <?php endif; ?>

        <div style="min-width:0; max-width:190px;">
       
            <h6 class="mb-1 fw-bold text-dark"
                style="word-break:break-word; white-space:normal;"
                title="<?= htmlspecialchars($doc['name']) ?>">
                <?= htmlspecialchars($doc['name']) ?>
            </h6>
           
            <?php if (!empty($doc['designation'])): ?>
            <div class="small text-muted mb-1"
                style="word-break:break-word; white-space:normal;">
                <?= htmlspecialchars($doc['designation']) ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($doc['location'])): ?>
            <div class="small text-muted"
                style="word-break:break-word; white-space:normal;">
                <i class="fa fa-map-marker-alt me-1 text-danger" style="font-size:0.7rem;"></i>
                <?= htmlspecialchars($doc['location']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</td>
                                    <!-- Specialty -->
                                    <td class="py-3">
                                        <?php if (!empty($doc['specialty'])): ?>
                                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 fw-medium border border-primary-subtle">
                                            <?= htmlspecialchars($doc['specialty']) ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="text-muted small fst-italic">Not set</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- SEO Health -->
                                    <td class="py-3 text-center">
                                        <div class="d-inline-flex align-items-center gap-3"
                                            onclick="openSeoModal(<?= $doc['id'] ?>)"
                                            data-bs-toggle="tooltip" title="Click for SEO details"
                                            style="cursor:pointer;">
                                            <div class="position-relative" style="width:44px;height:44px;">
                                                <svg viewBox="0 0 36 36" class="w-100 h-100" style="transform:rotate(-90deg);">
                                                    <circle cx="18" cy="18" r="15.9" fill="none" class="text-light" stroke="currentColor" stroke-width="3"></circle>
                                                    <circle cx="18" cy="18" r="15.9" fill="none" class="<?= $gradeTextColor ?>"
                                                        stroke="currentColor" stroke-width="3"
                                                        stroke-dasharray="100 100"
                                                        stroke-dashoffset="<?= 100 - $score ?>"
                                                        stroke-linecap="round"
                                                        style="transition:stroke-dashoffset 1s ease-out;"></circle>
                                                </svg>
                                                <div class="position-absolute top-50 start-50 translate-middle fw-bold small <?= $gradeTextColor ?>" style="font-size:0.8rem;"><?= $score ?></div>
                                            </div>
                                            <div class="d-flex flex-column align-items-start">
                                                <span class="badge <?= $gradeBgClass ?> text-white rounded-pill px-2 py-1 fw-bold mb-1" style="font-size:0.75rem;">Grade <?= $grade ?></span>
                                                <span class="badge <?= $issueCount === 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' ?> rounded-pill fw-semibold">
                                                    <?php if ($issueCount === 0): ?>
                                                    <i class="fa fa-check me-1"></i>Perfect
                                                    <?php else: ?>
                                                    <i class="fa fa-exclamation-triangle me-1"></i><?= $issueCount ?> Issues
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Performance -->
                                    <td class="py-3" style="min-width:140px;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small text-muted fw-medium">Satisfaction</span>
                                            <span class="small fw-bold <?= $satColor ?>"><?= $satisfaction ?>%</span>
                                        </div>
                                        <div class="progress rounded-pill bg-light mb-2" style="height:5px;">
                                            <div class="progress-bar <?= $satBg ?> rounded-pill" style="width:<?= $satisfaction ?>%"></div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="small text-dark fw-medium" data-bs-toggle="tooltip" title="Total Views">
                                                <i class="fa fa-eye text-muted me-1"></i><?= number_format((int)($doc['views'] ?? 0)) ?>
                                            </span>
                                            <span class="small text-dark fw-medium" data-bs-toggle="tooltip" title="Feedback Count">
                                                <i class="fa fa-comment text-muted me-1"></i><?= number_format((int)($doc['feedback_count'] ?? 0)) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3">
                                        <a href="./?toggle=<?= $doc['id'] ?>" class="text-decoration-none">
                                            <?php if ($doc['is_published']): ?>
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill py-2 px-3 fw-semibold">
                                                <span class="d-inline-block bg-success rounded-circle me-2" style="width:6px;height:6px;vertical-align:middle;"></span>Live
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill py-2 px-3 fw-semibold">
                                                <span class="d-inline-block bg-secondary rounded-circle me-2" style="width:6px;height:6px;vertical-align:middle;"></span>Draft
                                            </span>
                                            <?php endif; ?>
                                        </a>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3 text-end pe-4">
                                        <div class="btn-group shadow-sm border rounded-pill overflow-hidden bg-white">
                                            <a href="<?= SITE_URL ?>/doctors/<?= htmlspecialchars($doc['slug'] ?? '') ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary"
                                                data-bs-toggle="tooltip" title="View Profile">
                                                <i class="fa fa-external-link-alt"></i>
                                            </a>
                                            <div class="border-start border-light"></div>
                                            <a href="edit?id=<?= $doc['id'] ?>"
                                                class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary"
                                                data-bs-toggle="tooltip" title="Edit Profile">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <div class="border-start border-light"></div>
                                            <a href="./?delete=<?= $doc['id'] ?>"
                                                class="btn btn-sm btn-light border-0 py-2 px-3 text-danger"
                                                onclick="return confirm('Permanently delete Dr. <?= addslashes(htmlspecialchars($doc['name'])) ?>? This cannot be undone.')"
                                                data-bs-toggle="tooltip" title="Delete Profile">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /table-wrapper -->

                <!-- Pagination Footer -->
                <div id="paginationFooter"
                    class="card-footer bg-white border-top py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"
                    style="display: <?= $totalPages > 1 ? 'flex' : 'none' ?> !important;">
                    <div class="small text-muted fw-medium" id="entriesInfo">
                        Showing <span class="text-dark fw-bold" id="entryFrom"><?= $offset + 1 ?></span>
                        to <span class="text-dark fw-bold" id="entryTo"><?= min($offset + $limit, $totalRecords) ?></span>
                        of <span id="entryTotal"><?= $totalRecords ?></span> doctors
                    </div>
                    <nav aria-label="Table navigation">
                        <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
                    </nav>
                </div>

            </div><!-- /card -->

        </div>
    </div>
</div>

<!-- SEO Modal -->
<div class="modal fade" id="seoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-bottom bg-light py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="fa fa-chart-line"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">SEO Analysis</h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white" id="modalBody"></div>
        </div>
    </div>
</div>

<?php
// Build SEO data for JS
$initialSeoData = [];
foreach ($doctors as $doc) {
    $seo   = calcDoctorSeoScore($doc);
    $score = $seo['score'];
    [$grade, $gradeTextColor] = seoGrade($score);
    $satBase  = (int)($doc['satisfaction_rate'] ?? 0);
    $satColor = $satBase >= 90 ? 'bg-success' : ($satBase >= 70 ? 'bg-warning' : 'bg-danger');
    $satTxt   = $satBase >= 90 ? 'text-success' : ($satBase >= 70 ? 'text-warning' : 'text-danger');
    $initialSeoData[$doc['id']] = [
        'name'           => $doc['name'],
        'score'          => (int)$score,
        'grade'          => $grade,
        'gradeTextClass' => $gradeTextColor,
        'issues'         => $seo['issues'],
        'good'           => $seo['good'],
        'satisfaction'   => $satBase,
        'satClass'       => $satColor,
        'satTextClass'   => $satTxt,
        'rank'           => rankPotential($score),
        'metaTitle'      => $doc['meta_title'] ?? '',
        'metaDesc'       => $doc['meta_description'] ?? '',
        'keyword'        => $doc['focus_keyword'] ?? '',
        'schema'         => $doc['schema_type'] ?? '',
        'robots'         => $doc['robots_meta'] ?? '',
        'slug'           => $doc['slug'] ?? '',
        'specialty'      => $doc['specialty'] ?? '',
        'editUrl'        => 'edit.php?id=' . $doc['id'],
        'viewUrl'        => '/rkhospital/doctors/' . ($doc['slug'] ?? ''),
    ];
}

$extraJS = '<script>
window._seoData    = ' . json_encode($initialSeoData) . ';
var _currentPage   = ' . (int)$page . ';
var _currentSearch = ' . json_encode($search) . ';
var _totalPages    = ' . (int)$totalPages . ';
var _totalRecords  = ' . (int)$totalRecords . ';
var _limit         = ' . (int)$limit . ';
var _searchTimer   = null;

// ── AJAX Loader ───────────────────────────────────────────────
function loadDoctors(page, search) {
    page   = page || 1;
    search = (search === undefined) ? _currentSearch : search;
    var loader  = document.getElementById("tableLoader");
    var spinner = document.getElementById("searchSpinner");
    var tbody   = document.getElementById("doctorsTableBody");
    loader.classList.add("active");
    spinner.classList.add("active");
    var params = new URLSearchParams({ page: page, search: search });
    fetch("ajax_doctors.php?" + params.toString())
        .then(r => r.json())
        .then(data => {
            if (data.seoData) Object.assign(window._seoData, data.seoData);
            tbody.innerHTML = data.rows;
            tbody.querySelectorAll("[data-bs-toggle=\'tooltip\']").forEach(el => new bootstrap.Tooltip(el));
            _currentPage   = data.currentPage;
            _currentSearch = data.search;
            _totalPages    = data.totalPages;
            _totalRecords  = data.totalRecords;
            document.getElementById("totalBadge").textContent = data.totalRecords + " doctors";
            var from = data.totalRecords === 0 ? 0 : data.offset + 1;
            var to   = Math.min(data.offset + data.limit, data.totalRecords);
            document.getElementById("entryFrom").textContent  = from;
            document.getElementById("entryTo").textContent    = to;
            document.getElementById("entryTotal").textContent = data.totalRecords;
            document.getElementById("paginationFooter").style.display = data.totalPages > 1 ? "flex" : "none";
            renderPagination(data.currentPage, data.totalPages, data.search);
            window.history.replaceState({page, search}, "", "index.php?" + params.toString());
            document.getElementById("clearSearchBtn").style.display = search ? "inline-block" : "none";
            loader.classList.remove("active");
            spinner.classList.remove("active");
        })
        .catch(err => {
            console.error("AJAX error:", err);
            loader.classList.remove("active");
            spinner.classList.remove("active");
        });
}

// ── Pagination ────────────────────────────────────────────────
function renderPagination(currentPage, totalPages, search) {
    var ul = document.getElementById("paginationList");
    if (!ul) return;
    ul.innerHTML = "";
    var maxVisible = 5, half = Math.floor(maxVisible / 2);
    var start = Math.max(1, currentPage - half);
    var end   = Math.min(totalPages, start + maxVisible - 1);
    if (end - start + 1 < maxVisible) start = Math.max(1, end - maxVisible + 1);

    var prevLi = document.createElement("li");
    prevLi.className = "page-item" + (currentPage <= 1 ? " disabled" : "");
    var prevA = document.createElement("a");
    prevA.className = "page-link text-dark shadow-sm rounded-start-pill px-3";
    prevA.href = "#"; prevA.textContent = "Previous";
    if (currentPage > 1) prevA.addEventListener("click", e => { e.preventDefault(); loadDoctors(currentPage - 1, search); });
    prevLi.appendChild(prevA); ul.appendChild(prevLi);

    if (start > 1) {
        ul.appendChild(makePagerItem(1, currentPage, search));
        if (start > 2) { var d = document.createElement("li"); d.className = "page-item disabled"; d.innerHTML = "<span class=\'page-link shadow-sm\'>…</span>"; ul.appendChild(d); }
    }
    for (var p = start; p <= end; p++) ul.appendChild(makePagerItem(p, currentPage, search));
    if (end < totalPages) {
        if (end < totalPages - 1) { var d2 = document.createElement("li"); d2.className = "page-item disabled"; d2.innerHTML = "<span class=\'page-link shadow-sm\'>…</span>"; ul.appendChild(d2); }
        ul.appendChild(makePagerItem(totalPages, currentPage, search));
    }

    var nextLi = document.createElement("li");
    nextLi.className = "page-item" + (currentPage >= totalPages ? " disabled" : "");
    var nextA = document.createElement("a");
    nextA.className = "page-link text-dark shadow-sm rounded-end-pill px-3";
    nextA.href = "#"; nextA.textContent = "Next";
    if (currentPage < totalPages) nextA.addEventListener("click", e => { e.preventDefault(); loadDoctors(currentPage + 1, search); });
    nextLi.appendChild(nextA); ul.appendChild(nextLi);
}

function makePagerItem(p, currentPage, search) {
    var li = document.createElement("li");
    li.className = "page-item" + (p === currentPage ? " active" : "");
    var a = document.createElement("a");
    a.className = "page-link shadow-sm" + (p === currentPage ? " bg-primary border-primary text-white" : " text-dark");
    a.href = "#"; a.textContent = p;
    if (p !== currentPage) a.addEventListener("click", e => { e.preventDefault(); loadDoctors(p, search); });
    li.appendChild(a); return li;
}

// ── DOMContentLoaded ──────────────────────────────────────────
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-bs-toggle=\'tooltip\']").forEach(el => new bootstrap.Tooltip(el));
    renderPagination(_currentPage, _totalPages, _currentSearch);

    var searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("input", function () {
        clearTimeout(_searchTimer);
        var q = this.value.trim();
        _searchTimer = setTimeout(() => loadDoctors(1, q), 400);
    });
    document.getElementById("clearSearchBtn").addEventListener("click", function () {
        document.getElementById("searchInput").value = "";
        loadDoctors(1, "");
    });
    window.addEventListener("popstate", function (e) {
        if (e.state) {
            document.getElementById("searchInput").value = e.state.search || "";
            loadDoctors(e.state.page || 1, e.state.search || "");
        }
    });
});

// ── SEO Modal ─────────────────────────────────────────────────
function openSeoModal(id) {
    var d = window._seoData[id];
    if (!d) return;
    var modal = new bootstrap.Modal(document.getElementById("seoModal"));
    var shortName = d.name.length > 40 ? d.name.substring(0, 40) + "..." : d.name;
    document.getElementById("modalTitle").textContent = "SEO Audit: " + shortName;

    var verdicts = { "A": "Excellent! Profile is built to rank.", "B": "Solid SEO. Minor improvements recommended.", "C": "Average. Needs focused improvements.", "F": "Failing SEO. Requires a full overhaul." };
    var goodHtml  = d.good.map(g   => `<div class="p-2 mb-2 rounded-3 bg-success-subtle text-success-emphasis border border-success-subtle d-flex align-items-start gap-2 small fw-medium"><i class="fa fa-check-circle mt-1 text-success"></i><span>${g}</span></div>`).join("");
    var issueHtml = d.issues.map(i => `<div class="p-2 mb-2 rounded-3 bg-danger-subtle text-danger-emphasis border border-danger-subtle d-flex align-items-start gap-2 small fw-medium"><i class="fa fa-exclamation-circle mt-1 text-danger"></i><span>${i}</span></div>`).join("");
    var rankMap   = {"Top 10": 90, "Top 30": 65, "Top 50": 40, "Low": 15};
    var rankPct   = rankMap[d.rank] || 15;
    var rankBg    = rankPct > 60 ? "bg-success"   : (rankPct > 30 ? "bg-warning"   : "bg-danger");
    var rankTxt   = rankPct > 60 ? "text-success" : (rankPct > 30 ? "text-warning" : "text-danger");
    var satPct    = Math.min(100, d.satisfaction);
    var gradeBg   = d.gradeTextClass.replace("text-", "bg-");

    var tips = [];
    if (!d.keyword)   tips.push("Add a focus keyword — e.g. \"dentist in nagpur\"");
    if (!d.metaTitle) tips.push("Add a meta title (50–60 characters)");
    if (!d.metaDesc)  tips.push("Write a meta description (120–160 chars)");
    if (!d.schema)    tips.push("Add Schema markup (Physician recommended)");
    var tipsHtml = tips.length ? "<h6 class=\'text-uppercase fw-bold text-muted small mb-3 mt-4\' style=\'letter-spacing:0.5px;\'>Actionable Advice</h6>" + tips.map(t => `<div class="p-2 mb-2 rounded-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle d-flex align-items-start gap-2 small fw-medium"><i class="fa fa-lightbulb mt-1 text-warning"></i><span>${t}</span></div>`).join("") : "";

    var metaHtml = (d.metaTitle || d.metaDesc)
        ? `<div class="card mb-4 border rounded-3 bg-light shadow-none"><div class="card-body p-3">
            <div class="small text-muted mb-1 font-monospace" style="font-size:0.75rem;">rkhospital.com › doctors › ${d.slug}</div>
            <div class="fs-5 fw-medium mb-1" style="color:#1a0dab;">${d.metaTitle || "<em class=\'text-muted\'>No meta title</em>"}</div>
            <div class="small text-dark" style="line-height:1.4;">${d.metaDesc || "<em class=\'text-muted\'>No meta description</em>"}</div>
           </div></div>` : "";

    document.getElementById("modalBody").innerHTML = `
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card bg-light border-0 mb-4 shadow-sm rounded-4">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="position-relative" style="width:86px;height:86px;flex-shrink:0;">
                            <svg viewBox="0 0 36 36" class="w-100 h-100" style="transform:rotate(-90deg);">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#dee2e6" stroke-width="4"></circle>
                                <circle cx="18" cy="18" r="15.9" fill="none" class="${d.gradeTextClass}" stroke="currentColor" stroke-width="4" stroke-dasharray="100 100" stroke-dashoffset="${100 - d.score}" stroke-linecap="round"></circle>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle text-center"><div class="fw-bold fs-3 lh-1 ${d.gradeTextClass}">${d.score}</div></div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge ${gradeBg} text-white px-2 py-1 fs-6">${d.grade}</span>
                                <span class="text-dark fw-bold fs-5">SEO Health</span>
                            </div>
                            <p class="text-muted small mb-0 fw-medium">${verdicts[d.grade] || "Review required."}</p>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-3 border-top d-flex justify-content-between small text-center rounded-bottom-4">
                        <div><div class="text-muted text-uppercase fw-bold" style="font-size:0.65rem;">Keyword</div><div class="fw-bold text-dark text-truncate" style="max-width:80px;">${d.keyword || "—"}</div></div>
                        <div class="border-start border-light"></div>
                        <div><div class="text-muted text-uppercase fw-bold" style="font-size:0.65rem;">Schema</div><div class="fw-bold text-dark">${d.schema || "—"}</div></div>
                        <div class="border-start border-light"></div>
                        <div><div class="text-muted text-uppercase fw-bold" style="font-size:0.65rem;">Robots</div><div class="fw-bold text-dark">${d.robots || "—"}</div></div>
                    </div>
                </div>
                <h6 class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing:0.5px;">Search Preview</h6>
                ${metaHtml}
                <div class="card border border-light shadow-sm bg-white rounded-4">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase fw-bold text-muted small mb-4" style="letter-spacing:0.5px;">Profile Performance</h6>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-2"><span class="text-dark fw-medium">Patient Satisfaction</span><span class="fw-bold ${d.satTextClass}">${d.satisfaction}%</span></div>
                            <div class="progress bg-light rounded-pill" style="height:6px;"><div class="progress-bar rounded-pill ${d.satClass}" style="width:${satPct}%"></div></div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between small mb-2"><span class="text-dark fw-medium">Ranking Potential</span><span class="fw-bold ${rankTxt}">${d.rank}</span></div>
                            <div class="progress bg-light rounded-pill" style="height:6px;"><div class="progress-bar rounded-pill ${rankBg}" style="width:${rankPct}%"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex flex-column h-100">
                    <div class="flex-grow-1">
                        ${goodHtml  ? `<h6 class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing:0.5px;">Passed Checks (${d.good.length})</h6>${goodHtml}` : ""}
                        ${issueHtml ? `<h6 class="text-uppercase fw-bold text-muted small mb-3 mt-4" style="letter-spacing:0.5px;">Issues (${d.issues.length})</h6>${issueHtml}` : ""}
                        ${tipsHtml}
                    </div>
                    <div class="d-flex gap-2 mt-4 pt-4 border-top">
                        <a href="${d.editUrl}" class="btn btn-primary w-100 shadow-sm rounded-pill fw-semibold"><i class="fa fa-wrench me-2"></i> Fix Issues</a>
                        <a href="${d.viewUrl}" target="_blank" class="btn btn-light text-primary w-100 shadow-sm rounded-pill border fw-semibold"><i class="fa fa-external-link-alt me-2"></i> View Profile</a>
                    </div>
                </div>
            </div>
        </div>`;
    modal.show();
}
</script>';

require_once '../include/footer.php';
?>