<?php
// C:\xampp\htdocs\hpce\admin\services\index.php

// 1. Database Connection & Logic
require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('services');

$limit  = 10;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchLike = '%' . $conn->real_escape_string($search) . '%';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $conn->query("DELETE FROM services WHERE id = $deleteId");
    header("Location: ./?msg=deleted");
    exit;
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $toggleId = (int)$_GET['toggle'];
    $conn->query("UPDATE services SET is_published = NOT is_published WHERE id = $toggleId");
    header("Location: ./");
    exit;
}

$sql = "SELECT s.*, c.name AS category_name
        FROM services s
        LEFT JOIN categories c ON s.category_id = c.id
        WHERE s.title LIKE '$searchLike' OR c.name LIKE '$searchLike'
        ORDER BY s.sort_order ASC, s.created_at DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$services  = [];
if ($result) { while ($row = $result->fetch_assoc()) { $services[] = $row; } }

$countResult = $conn->query("SELECT COUNT(*) AS total FROM services s
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE s.title LIKE '$searchLike' OR c.name LIKE '$searchLike'");
$totalRecords = $countResult ? (int)$countResult->fetch_assoc()['total'] : 0;
$totalPages   = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;

// Stats
$statsRes = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(is_published) as published,
    SUM(!is_published) as drafts
    FROM services");
$stats = $statsRes ? $statsRes->fetch_assoc() : ['total' => 0, 'published' => 0, 'drafts' => 0];

$catStatsRes = $conn->query("SELECT COUNT(*) as total_categories FROM categories");
$catStats = $catStatsRes ? $catStatsRes->fetch_assoc() : ['total_categories' => 0];

// -------------------------------------------------------
define('BASE_PATH', '/hpce/');

function resolveImageSrc($imageField) {
    if (empty($imageField)) return '';
    if (strpos($imageField, 'http://') === 0 || strpos($imageField, 'https://') === 0) {
        return $imageField;
    }
    $clean = ltrim($imageField, '/');
    return BASE_PATH . $clean;
}

function calcSeoScore($service) {
    $score = 0; $issues = []; $good = [];

    $mt = $service['meta_title'] ?? '';
    if (strlen($mt) >= 50 && strlen($mt) <= 60) { $score += 15; $good[] = 'Meta title perfect length'; }
    elseif (strlen($mt) > 0)                    { $score += 7;  $issues[] = 'Meta title not ideal (50-60 chars)'; }
    else                                         { $issues[] = 'Meta title missing'; }

    $md = $service['meta_description'] ?? '';
    if (strlen($md) >= 120 && strlen($md) <= 160) { $score += 15; $good[] = 'Meta description perfect'; }
    elseif (strlen($md) > 0)                      { $score += 7;  $issues[] = 'Meta desc not ideal (120-160 chars)'; }
    else                                           { $issues[] = 'Meta description missing'; }

    $kw = strtolower($service['focus_keyword'] ?? '');
    if (!empty($kw)) {
        $score += 10; $good[] = 'Focus keyword set';
        if (strpos(strtolower($service['title']), $kw) !== false) { $score += 10; $good[] = 'Keyword in title'; }
        else { $issues[] = 'Keyword missing from title'; }
        if (strpos(strtolower($md), $kw) !== false) { $score += 10; $good[] = 'Keyword in meta desc'; }
        else { $issues[] = 'Keyword missing from meta desc'; }
        $kwSlug = str_replace(' ', '-', $kw);
        if (strpos($service['slug'] ?? '', $kwSlug) !== false) { $score += 5; $good[] = 'Keyword in slug'; }
        else { $issues[] = 'Keyword not in slug'; }
    } else {
        $issues[] = 'No focus keyword set';
    }

    $content = strip_tags($service['content'] ?? '');
    $wc = str_word_count($content);
    if ($wc >= 400)      { $score += 15; $good[] = "Good content length ($wc words)"; }
    elseif ($wc >= 150)  { $score += 8;  $issues[] = "Content short ($wc words, aim 400+)"; }
    else                 { $issues[] = "Content too short ($wc words)"; }

    if (!empty($service['image']))       { $score += 5; $good[] = 'Featured image set'; }  else { $issues[] = 'No featured image'; }
    if (!empty($service['og_title']))    { $score += 5; $good[] = 'OG title set'; }         else { $issues[] = 'OG title missing'; }
    if (!empty($service['schema_type'])) { $score += 10; $good[] = 'Schema markup set'; }   else { $issues[] = 'No schema type'; }

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

// 2. Setup Page Variables for Includes
$pageTitle  = 'Services & Treatments';
$activePage = 'services-index';
$assetBase  = '../';

$extraCSS = '
<style>
    .stat-card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-card-hover:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
    .table-hover-soft tbody tr { transition: background-color 0.15s ease; }
    .table-hover-soft tbody tr:hover { background-color: #f8f9fa !important; }
    .service-thumb {
        width: 80px; height: 80px; object-fit: cover;
        border-radius: 0.5rem; flex-shrink: 0; display: block;
    }
    .service-thumb-placeholder {
        width: 80px; height: 80px; border-radius: 0.5rem; flex-shrink: 0;
        background: #e9ecef; display: flex; align-items: center;
        justify-content: center; color: #adb5bd;
    }
    /* AJAX search/pagination styles */
    #tableLoader {
        display: none;
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.75);
        z-index: 10;
        align-items: center;
        justify-content: center;
        border-radius: 0 0 1rem 1rem;
    }
    #tableLoader.active { display: flex; }
    .table-wrapper { position: relative; min-height: 200px; }
    .search-clear-btn { cursor: pointer; }
    .page-link { transition: all 0.15s ease; }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    #searchInput:focus { box-shadow: none; outline: none; }
    .search-bar-wrap {
        position: relative;
        width: 320px;
        max-width: 100%;
    }
    #searchSpinner {
        display: none;
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
    }
    #searchSpinner.active { display: block; }
    #entriesInfo { transition: opacity 0.2s; }
</style>
';

require_once '../include/head.php';
?>

<div class="main-wrapper">

    <?php require_once '../include/header.php'; ?>
    <?php require_once '../include/sidebar.php'; ?>

    <div class="page-wrapper" style="background-color: #f4f6f9; min-height: 100vh;">
        <div class="content container-fluid pt-4 pb-5">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bolder text-dark mb-1">Services &amp; Treatments</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../"
                                    class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active text-secondary fw-medium">Services Manage</li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="add"
                        class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="fa fa-plus"></i> Add New Service
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['msg'])):
                $msgMap = [
                    'deleted' => ['danger',  'Service removed permanently.', 'fa-trash-alt'],
                    'added'   => ['success', 'New service added successfully.', 'fa-check-circle'],
                    'updated' => ['success', 'Service details updated.', 'fa-check-circle'],
                ];
                [$msgType, $msgText, $msgIcon] = $msgMap[$_GET['msg']] ?? ['success', 'Action completed.', 'fa-check'];
            ?>
            <div class="alert alert-<?= $msgType ?> border-0 shadow-sm alert-dismissible fade show d-flex align-items-center gap-3 rounded-3"
                role="alert">
                <i class="fa <?= $msgIcon ?> fs-4"></i>
                <div class="fw-medium"><?= htmlspecialchars($msgText) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing: 0.5px;">
                                    Total Services</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= (int)($stats['total'] ?? 0) ?></h2>
                            </div>
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
                                <i class="fa fa-briefcase-medical fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing: 0.5px;">
                                    Published</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= (int)($stats['published'] ?? 0) ?></h2>
                            </div>
                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
                                <i class="fa fa-check-circle fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing: 0.5px;">
                                    Drafts</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= (int)($stats['drafts'] ?? 0) ?></h2>
                            </div>
                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
                                <i class="fa fa-file-alt fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card-hover border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase small fw-bold mb-1" style="letter-spacing: 0.5px;">
                                    Categories</p>
                                <h2 class="fw-bolder mb-0 text-dark"><?= (int)($catStats['total_categories'] ?? 0) ?></h2>
                            </div>
                            <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
                                <i class="fa fa-layer-group fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <div
                    class="card-header bg-white border-bottom py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">Services Directory</h5>
                        <span id="totalBadge"
                            class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-1 fw-semibold border border-secondary-subtle">
                            <?= $totalRecords ?> items
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
                                    placeholder="Search services, categories..."
                                    value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                                <span id="searchSpinner">
                                    <span class="spinner-border spinner-border-sm text-secondary" role="status"></span>
                                </span>
                            </div>
                        </div>
                        <button id="clearSearchBtn"
                            class="btn btn-sm btn-light rounded-pill border px-3 fw-medium search-clear-btn"
                            style="display: <?= $search ? 'inline-block' : 'none' ?>;">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <div id="tableLoader">
                        <div class="d-flex flex-column align-items-center gap-2">
                            <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                            </div>
                            <span class="small text-muted fw-medium">Loading…</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover-soft table-borderless align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr class="text-uppercase text-muted"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <th class="ps-4 py-3 fw-bold">Service &amp; Details</th>
                                    <th class="py-3 fw-bold">Category</th>
                                    <th class="py-3 fw-bold text-center">SEO Health</th>
                                    <th class="py-3 fw-bold text-center">Order</th>
                                    <th class="py-3 fw-bold">Status</th>
                                    <th class="py-3 fw-bold text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="servicesTableBody">
                                <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5">
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 80px; height: 80px;">
                                                <i class="fa fa-folder-open fs-1 text-secondary opacity-50"></i>
                                            </div>
                                            <h5 class="text-dark fw-bold">No services found</h5>
                                            <p class="text-muted mb-4">You haven't added any services yet.</p>
                                            <a href="add" class="btn btn-primary rounded-pill px-4 shadow-sm">Add New Service</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($services as $i => $service):
                                    $seo        = calcSeoScore($service);
                                    $score      = $seo['score'];
                                    [$grade, $gradeTextColor] = seoGrade($score);
                                    $rankLabel  = rankPotential($score);
                                    $issueCount = count($seo['issues']);

                                    $mtLen   = strlen($service['meta_title'] ?? '');
                                    $mdLen   = strlen($service['meta_description'] ?? '');
                                    $ctrBase = ($score / 100) * 8;
                                    if ($mtLen >= 50 && $mtLen <= 60) $ctrBase += 1;
                                    if ($mdLen >= 120 && $mdLen <= 160) $ctrBase += 0.5;
                                    $ctrBase     = min(9.9, $ctrBase);
                                    $ctrColor    = $ctrBase >= 5 ? 'text-success' : ($ctrBase >= 3 ? 'text-warning' : 'text-danger');
                                    $ctrBg       = $ctrBase >= 5 ? 'bg-success' : ($ctrBase >= 3 ? 'bg-warning' : 'bg-danger');
                                    $imgSrc = resolveImageSrc($service['image'] ?? '');
                                    $gradeBgClass = str_replace('text-', 'bg-', $gradeTextColor);
                                ?>
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($imgSrc)): ?>
                                            <img src="<?= htmlspecialchars($imgSrc) ?>"
                                                class="service-thumb shadow-sm border border-light"
                                                alt="<?= htmlspecialchars($service['title']) ?>" width="80" height="80"
                                                style="width:80px!important;height:80px!important;object-fit:cover;border-radius:0.5rem;flex-shrink:0;display:block;"
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            <div class="service-thumb-placeholder" style="display:none;"><i
                                                    class="fa fa-stethoscope fs-5"></i></div>
                                            <?php else: ?>
                                            <div class="service-thumb-placeholder"><i class="fa fa-stethoscope fs-5"></i></div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark text-truncate"
                                                    style="max-width: 280px;"
                                                    title="<?= htmlspecialchars($service['title']) ?>">
                                                    <?= htmlspecialchars($service['title']) ?>
                                                </h6>
                                                <div class="small text-muted d-flex align-items-center gap-2">
                                                    <span><?= !empty($service['created_at']) ? date('M d, Y', strtotime($service['created_at'])) : '' ?></span>
                                                    <i class="fa fa-circle" style="font-size: 4px;"></i>
                                                    <span class="text-truncate"
                                                        style="max-width: 150px;">/<?= htmlspecialchars($service['slug'] ?? '') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        <?php if (!empty($service['category_name'])): ?>
                                        <span
                                            class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 fw-medium border border-primary-subtle">
                                            <?= htmlspecialchars($service['category_name']) ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="text-muted small fst-italic">None</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="py-3 text-center">
                                        <div class="d-inline-flex align-items-center gap-3"
                                            onclick="openSeoModal(<?= $service['id'] ?>)" data-bs-toggle="tooltip"
                                            title="Click for SEO details" style="cursor:pointer;">
                                            <div class="position-relative" style="width: 44px; height: 44px;">
                                                <svg viewBox="0 0 36 36" class="w-100 h-100"
                                                    style="transform: rotate(-90deg);">
                                                    <circle cx="18" cy="18" r="15.9" fill="none" class="text-light"
                                                        stroke="currentColor" stroke-width="3"></circle>
                                                    <circle cx="18" cy="18" r="15.9" fill="none"
                                                        class="<?= $gradeTextColor ?>" stroke="currentColor"
                                                        stroke-width="3" stroke-dasharray="100 100"
                                                        stroke-dashoffset="<?= 100 - $score ?>" stroke-linecap="round"
                                                        style="transition: stroke-dashoffset 1s ease-out;"></circle>
                                                </svg>
                                                <div class="position-absolute top-50 start-50 translate-middle fw-bold small <?= $gradeTextColor ?>"
                                                    style="font-size: 0.8rem;">
                                                    <?= $score ?>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column align-items-start">
                                                <span
                                                    class="badge <?= $gradeBgClass ?> text-white rounded-pill px-2 py-1 fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Grade <?= $grade ?></span>
                                                <span
                                                    class="badge <?= $issueCount === 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' ?> rounded-pill fw-semibold">
                                                    <?php if ($issueCount === 0): ?>
                                                    <i class="fa fa-check me-1"></i>Perfect
                                                    <?php else: ?>
                                                    <i class="fa fa-exclamation-triangle me-1"></i><?= $issueCount ?>
                                                    Issues
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3 text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">
                                            #<?= (int)($service['sort_order'] ?? 0) ?>
                                        </span>
                                    </td>

                                    <td class="py-3">
                                        <a href="./?toggle=<?= $service['id'] ?>" class="text-decoration-none">
                                            <?php if ($service['is_published']): ?>
                                            <span
                                                class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill py-2 px-3 fw-semibold">
                                                <span class="d-inline-block bg-success rounded-circle me-2"
                                                    style="width:6px;height:6px;vertical-align:middle;"></span>Live
                                            </span>
                                            <?php else: ?>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill py-2 px-3 fw-semibold">
                                                <span class="d-inline-block bg-secondary rounded-circle me-2"
                                                    style="width:6px;height:6px;vertical-align:middle;"></span>Draft
                                            </span>
                                            <?php endif; ?>
                                        </a>
                                    </td>

                                    <td class="py-3 text-end pe-4">
                                        <div class="btn-group shadow-sm border rounded-pill overflow-hidden bg-white">
                                            <a href="<?= SITE_URL ?>/service/<?= htmlspecialchars($service['slug']) ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary">
                                                <i class="fa fa-external-link-alt"></i>
                                            </a>
                                            <div class="border-start border-light"></div>
                                            <a href="edit?id=<?= $service['id'] ?>"
                                                class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary"
                                                data-bs-toggle="tooltip" title="Edit Service">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <div class="border-start border-light"></div>
                                            <a href="./?delete=<?= $service['id'] ?>"
                                                class="btn btn-sm btn-light border-0 py-2 px-3 text-danger"
                                                onclick="return confirm('Delete this service permanently? This cannot be undone.')"
                                                data-bs-toggle="tooltip" title="Delete Service">
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
                </div><div id="paginationFooter"
                    class="card-footer bg-white border-top py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"
                    style="display: <?= $totalPages > 1 ? 'flex' : 'none' ?> !important;">
                    <div class="small text-muted fw-medium" id="entriesInfo">
                        Showing <span class="text-dark fw-bold" id="entryFrom"><?= $offset + 1 ?></span>
                        to <span class="text-dark fw-bold"
                            id="entryTo"><?= min($offset + $limit, $totalRecords) ?></span>
                        of <span id="entryTotal"><?= $totalRecords ?></span> entries
                    </div>
                    <nav aria-label="Table navigation">
                        <ul class="pagination pagination-sm mb-0" id="paginationList">
                            </ul>
                    </nav>
                </div>

            </div></div>
    </div>
</div>

<div class="modal fade" id="seoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-bottom bg-light py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center"
                        style="width: 32px; height: 32px;">
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
// Build initial SEO data for JS
$initialSeoData = [];
foreach ($services as $service) {
    $seo   = calcSeoScore($service);
    $score = $seo['score'];
    [$grade, $gradeTextColor] = seoGrade($score);
    $mtLen   = strlen($service['meta_title'] ?? '');
    $mdLen   = strlen($service['meta_description'] ?? '');
    $ctrBase = min(9.9, ($score / 100) * 8 + ($mtLen >= 50 && $mtLen <= 60 ? 1 : 0) + ($mdLen >= 120 && $mdLen <= 160 ? 0.5 : 0));
    $ctrColor = $ctrBase >= 5 ? 'text-success' : ($ctrBase >= 3 ? 'text-warning' : 'text-danger');
    $ctrBg    = $ctrBase >= 5 ? 'bg-success'   : ($ctrBase >= 3 ? 'bg-warning'   : 'bg-danger');
    $initialSeoData[$service['id']] = [
        'title'          => $service['title'],
        'score'          => (int)$score,
        'grade'          => $grade,
        'gradeTextClass' => $gradeTextColor,
        'issues'         => $seo['issues'],
        'good'           => $seo['good'],
        'ctr'            => number_format($ctrBase, 1),
        'ctrClass'       => $ctrBg,
        'ctrTextClass'   => $ctrColor,
        'rank'           => rankPotential($score),
        'metaTitle'      => $service['meta_title'] ?? '',
        'metaDesc'       => $service['meta_description'] ?? '',
        'keyword'        => $service['focus_keyword'] ?? '',
        'schema'         => $service['schema_type'] ?? '',
        'robots'         => $service['robots_meta'] ?? '',
        'slug'           => $service['slug'] ?? '',
        'editUrl'        => 'edit.php?id=' . $service['id'],
        'viewUrl'        => '../service/' . ($service['slug'] ?? ''),
    ];
}

$extraJS = '<script>
// ============================================================
// Global SEO data store (merged on every AJAX load)
// ============================================================
window._seoData = ' . json_encode($initialSeoData) . ';

// ============================================================
// State
// ============================================================
var _currentPage   = ' . (int)$page . ';
var _currentSearch = ' . json_encode($search) . ';
var _totalPages    = ' . (int)$totalPages . ';
var _totalRecords  = ' . (int)$totalRecords . ';
var _limit         = ' . (int)$limit . ';
var _searchTimer   = null;

// ============================================================
// AJAX loader
// ============================================================
function loadServices(page, search) {
    page   = page   || 1;
    search = (search === undefined) ? _currentSearch : search;

    var loader  = document.getElementById("tableLoader");
    var spinner = document.getElementById("searchSpinner");
    var tbody   = document.getElementById("servicesTableBody");

    loader.classList.add("active");
    spinner.classList.add("active");

    var params = new URLSearchParams({ page: page, search: search });

    fetch("ajax_services.php?" + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            // Merge SEO data
            if (data.seoData) {
                Object.assign(window._seoData, data.seoData);
            }

            // Update table body
            tbody.innerHTML = data.rows;

            // Re-init tooltips
            var tips = [].slice.call(tbody.querySelectorAll("[data-bs-toggle=\'tooltip\']"));
            tips.forEach(function(el) { new bootstrap.Tooltip(el); });

            // Update state
            _currentPage   = data.currentPage;
            _currentSearch = data.search;
            _totalPages    = data.totalPages;
            _totalRecords  = data.totalRecords;

            // Update badge
            document.getElementById("totalBadge").textContent = data.totalRecords + " items";

            // Update entries info
            var from = data.totalRecords === 0 ? 0 : data.offset + 1;
            var to   = Math.min(data.offset + data.limit, data.totalRecords);
            document.getElementById("entryFrom").textContent  = from;
            document.getElementById("entryTo").textContent    = to;
            document.getElementById("entryTotal").textContent = data.totalRecords;

            // Show/hide pagination footer
            var footer = document.getElementById("paginationFooter");
            footer.style.display = data.totalPages > 1 ? "flex" : "none";

            // Re-render pagination
            renderPagination(data.currentPage, data.totalPages, data.search);

            // Update browser URL (no reload)
            var url = "index.php?" + params.toString();
            window.history.replaceState({page: page, search: search}, "", url);

            // Show/hide clear button
            document.getElementById("clearSearchBtn").style.display = search ? "inline-block" : "none";

            loader.classList.remove("active");
            spinner.classList.remove("active");
        })
        .catch(function(err) {
            console.error("AJAX error:", err);
            loader.classList.remove("active");
            spinner.classList.remove("active");
        });
}

// ============================================================
// Pagination renderer
// ============================================================
function renderPagination(currentPage, totalPages, search) {
    var ul = document.getElementById("paginationList");
    if (!ul) return;
    ul.innerHTML = "";

    var maxVisible = 5;
    var half       = Math.floor(maxVisible / 2);
    var start      = Math.max(1, currentPage - half);
    var end        = Math.min(totalPages, start + maxVisible - 1);
    if (end - start + 1 < maxVisible) { start = Math.max(1, end - maxVisible + 1); }

    // Previous
    var prevLi   = document.createElement("li");
    prevLi.className = "page-item" + (currentPage <= 1 ? " disabled" : "");
    var prevLink = document.createElement("a");
    prevLink.className   = "page-link text-dark shadow-sm rounded-start-pill px-3";
    prevLink.href        = "#";
    prevLink.textContent = "Previous";
    if (currentPage > 1) {
        prevLink.addEventListener("click", function(e) { e.preventDefault(); loadServices(currentPage - 1, search); });
    }
    prevLi.appendChild(prevLink);
    ul.appendChild(prevLi);

    // First page + ellipsis
    if (start > 1) {
        ul.appendChild(makePagerItem(1, currentPage, search));
        if (start > 2) {
            var dots = document.createElement("li");
            dots.className = "page-item disabled";
            dots.innerHTML = "<span class=\"page-link shadow-sm\">…</span>";
            ul.appendChild(dots);
        }
    }

    // Numbered pages
    for (var p = start; p <= end; p++) {
        ul.appendChild(makePagerItem(p, currentPage, search));
    }

    // Last page + ellipsis
    if (end < totalPages) {
        if (end < totalPages - 1) {
            var dots2 = document.createElement("li");
            dots2.className = "page-item disabled";
            dots2.innerHTML = "<span class=\"page-link shadow-sm\">…</span>";
            ul.appendChild(dots2);
        }
        ul.appendChild(makePagerItem(totalPages, currentPage, search));
    }

    // Next
    var nextLi   = document.createElement("li");
    nextLi.className = "page-item" + (currentPage >= totalPages ? " disabled" : "");
    var nextLink = document.createElement("a");
    nextLink.className   = "page-link text-dark shadow-sm rounded-end-pill px-3";
    nextLink.href        = "#";
    nextLink.textContent = "Next";
    if (currentPage < totalPages) {
        nextLink.addEventListener("click", function(e) { e.preventDefault(); loadServices(currentPage + 1, search); });
    }
    nextLi.appendChild(nextLink);
    ul.appendChild(nextLi);
}

function makePagerItem(p, currentPage, search) {
    var li   = document.createElement("li");
    li.className = "page-item" + (p === currentPage ? " active" : "");
    var a    = document.createElement("a");
    a.className  = "page-link shadow-sm" + (p === currentPage ? " bg-primary border-primary text-white" : " text-dark");
    a.href       = "#";
    a.textContent = p;
    if (p !== currentPage) {
        (function(pg) {
            a.addEventListener("click", function(e) { e.preventDefault(); loadServices(pg, search); });
        })(p);
    }
    li.appendChild(a);
    return li;
}

// ============================================================
// DOMContentLoaded: wire up search + initial pagination render
// ============================================================
document.addEventListener("DOMContentLoaded", function () {

    // Bootstrap Tooltips
    var tooltipEls = [].slice.call(document.querySelectorAll("[data-bs-toggle=\'tooltip\']"));
    tooltipEls.forEach(function (el) { new bootstrap.Tooltip(el); });

    // Initial pagination render
    renderPagination(_currentPage, _totalPages, _currentSearch);

    // Search input — debounced
    var searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("input", function () {
        clearTimeout(_searchTimer);
        var q = this.value.trim();
        _searchTimer = setTimeout(function () {
            loadServices(1, q);
        }, 400);
    });

    // Clear button
    document.getElementById("clearSearchBtn").addEventListener("click", function () {
        document.getElementById("searchInput").value = "";
        loadServices(1, "");
    });

    // Browser back/forward
    window.addEventListener("popstate", function (e) {
        if (e.state) {
            document.getElementById("searchInput").value = e.state.search || "";
            loadServices(e.state.page || 1, e.state.search || "");
        }
    });
});

// ============================================================
// SEO Modal
// ============================================================
function openSeoModal(id) {
    var d = window._seoData[id];
    if (!d) return;

    var seoModal = new bootstrap.Modal(document.getElementById("seoModal"));

    var shortTitle = d.title.length > 40 ? d.title.substring(0, 40) + "..." : d.title;
    document.getElementById("modalTitle").textContent = "SEO Audit: " + shortTitle;

    var verdicts = {
        "A": "Excellent optimisation! Built to rank.",
        "B": "Solid SEO. A few minor tweaks needed.",
        "C": "Average health. Needs focused improvements.",
        "F": "Failing SEO. Requires a complete overhaul."
    };

    var goodHtml  = d.good.map(function(g)    { return "<div class=\'p-2 mb-2 rounded-3 bg-success-subtle text-success-emphasis border border-success-subtle d-flex align-items-start gap-2 small fw-medium\'><i class=\'fa fa-check-circle mt-1 text-success\'></i><span>" + g + "</span></div>"; }).join("");
    var issueHtml = d.issues.map(function(iss) { return "<div class=\'p-2 mb-2 rounded-3 bg-danger-subtle text-danger-emphasis border border-danger-subtle d-flex align-items-start gap-2 small fw-medium\'><i class=\'fa fa-exclamation-circle mt-1 text-danger\'></i><span>" + iss + "</span></div>"; }).join("");

    var rankMap  = {"Top 10": 90, "Top 30": 65, "Top 50": 40, "Low": 15};
    var rankPct  = rankMap[d.rank] || 15;
    var rankColorClass = rankPct > 60 ? "bg-success"   : (rankPct > 30 ? "bg-warning"   : "bg-danger");
    var rankTextClass  = rankPct > 60 ? "text-success" : (rankPct > 30 ? "text-warning" : "text-danger");
    var ctrPct   = Math.min(100, parseFloat(d.ctr) * 10);

    var metaHtml = "";
    if (d.metaTitle || d.metaDesc) {
        metaHtml = "<div class=\'card mb-4 border rounded-3 bg-light shadow-none\'><div class=\'card-body p-3\'><div class=\'small text-muted mb-1 font-monospace\' style=\'font-size:0.75rem;\'>hpces.com \u203a service \u203a " + (d.slug || "") + "</div><div class=\'fs-5 fw-medium mb-1\' style=\'color:#1a0dab;max-width:100%;overflow:hidden;text-overflow:ellipsis;\'>" + (d.metaTitle || "<em class=\'text-muted\'>No meta title</em>") + "</div><div class=\'small text-dark\' style=\'line-height:1.4;\'>" + (d.metaDesc || "<em class=\'text-muted\'>No meta description</em>") + "</div></div></div>";
    }

    var tips = [];
    if (!d.keyword)     tips.push("Focus keyword missing — required for full analysis");
    if (!d.metaTitle)   tips.push("Add a meta title (50–60 characters)");
    if (!d.metaDesc)    tips.push("Write a compelling meta description (120–160 chars)");
    if (!d.schema)      tips.push("Add Schema markup (MedicalProcedure recommended)");

    var tipsHtml = tips.length > 0
        ? "<h6 class=\'text-uppercase fw-bold text-muted small mb-3 mt-4\' style=\'letter-spacing:0.5px;\'>Actionable Advice</h6>"
          + tips.map(function(t){ return "<div class=\'p-2 mb-2 rounded-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle d-flex align-items-start gap-2 small fw-medium\'><i class=\'fa fa-lightbulb mt-1 text-warning\'></i><span>" + t + "</span></div>"; }).join("")
        : "";

    var gradeBgClass = d.gradeTextClass.replace("text-", "bg-");

    document.getElementById("modalBody").innerHTML =
        "<div class=\'row g-4\'>" +
            "<div class=\'col-lg-6\'>" +
                "<div class=\'card bg-light border-0 mb-4 shadow-sm rounded-4\'><div class=\'card-body p-4 d-flex align-items-center gap-4\'>" +
                "<div class=\'position-relative\' style=\'width:86px;height:86px;flex-shrink:0;\'>" +
                "<svg viewBox=\'0 0 36 36\' class=\'w-100 h-100\' style=\'transform:rotate(-90deg);\'>" +
                "<circle cx=\'18\' cy=\'18\' r=\'15.9\' fill=\'none\' stroke=\'#dee2e6\' stroke-width=\'4\'></circle>" +
                "<circle cx=\'18\' cy=\'18\' r=\'15.9\' fill=\'none\' class=\'" + d.gradeTextClass + "\' stroke=\'currentColor\' stroke-width=\'4\' stroke-dasharray=\'100 100\' stroke-dashoffset=\'" + (100 - d.score) + "\' stroke-linecap=\'round\'></circle>" +
                "</svg>" +
                "<div class=\'position-absolute top-50 start-50 translate-middle text-center\'><div class=\'fw-bold fs-3 lh-1 " + d.gradeTextClass + "\'>" + d.score + "</div></div></div>" +
                "<div><div class=\'d-flex align-items-center gap-2 mb-1\'><span class=\'badge " + gradeBgClass + " text-white px-2 py-1 fs-6\'>" + d.grade + "</span><span class=\'text-dark fw-bold fs-5\'>Overall Health</span></div>" +
                "<p class=\'text-muted small mb-0 fw-medium\'>" + (verdicts[d.grade] || "Review required.") + "</p></div>" +
                "</div>" +
                "<div class=\'card-footer bg-white p-3 border-top d-flex justify-content-between small text-center rounded-bottom-4\'>" +
                "<div><div class=\'text-muted text-uppercase fw-bold\' style=\'font-size:0.65rem;\'>Keyword</div><div class=\'fw-bold text-dark text-truncate\' style=\'max-width:80px;\'>" + (d.keyword || "—") + "</div></div>" +
                "<div class=\'border-start border-light\'></div>" +
                "<div><div class=\'text-muted text-uppercase fw-bold\' style=\'font-size:0.65rem;\'>Schema</div><div class=\'fw-bold text-dark\'>" + (d.schema || "—") + "</div></div>" +
                "<div class=\'border-start border-light\'></div>" +
                "<div><div class=\'text-muted text-uppercase fw-bold\' style=\'font-size:0.65rem;\'>Robots</div><div class=\'fw-bold text-dark\'>" + (d.robots || "—") + "</div></div>" +
                "</div></div>" +
                "<h6 class=\'text-uppercase fw-bold text-muted small mb-3\' style=\'letter-spacing:0.5px;\'>Search Preview</h6>" +
                metaHtml +
                "<div class=\'card border border-light shadow-sm bg-white rounded-4\'><div class=\'card-body p-4\'>" +
                "<h6 class=\'text-uppercase fw-bold text-muted small mb-4\' style=\'letter-spacing:0.5px;\'>Analytics Forecast</h6>" +
                "<div class=\'mb-3\'><div class=\'d-flex justify-content-between small mb-2\'><span class=\'text-dark fw-medium\'>Projected CTR</span><span class=\'fw-bold " + d.ctrTextClass + "\'>~" + d.ctr + "%</span></div><div class=\'progress bg-light rounded-pill\' style=\'height:6px;\'><div class=\'progress-bar rounded-pill " + d.ctrClass + "\' style=\'width:" + ctrPct + "%\'></div></div></div>" +
                "<div><div class=\'d-flex justify-content-between small mb-2\'><span class=\'text-dark fw-medium\'>Ranking Potential</span><span class=\'fw-bold " + rankTextClass + "\'>" + d.rank + "</span></div><div class=\'progress bg-light rounded-pill\' style=\'height:6px;\'><div class=\'progress-bar rounded-pill " + rankColorClass + "\' style=\'width:" + rankPct + "%\'></div></div></div>" +
                "</div></div>" +
            "</div>" +
            "<div class=\'col-lg-6\'><div class=\'d-flex flex-column h-100\'><div class=\'flex-grow-1\'>" +
                (goodHtml  ? "<h6 class=\'text-uppercase fw-bold text-muted small mb-3\' style=\'letter-spacing:0.5px;\'>Passed Checks (" + d.good.length + ")</h6>" + goodHtml   : "") +
                (issueHtml ? "<h6 class=\'text-uppercase fw-bold text-muted small mb-3 mt-4\' style=\'letter-spacing:0.5px;\'>Errors &amp; Warnings (" + d.issues.length + ")</h6>" + issueHtml : "") +
                tipsHtml +
            "</div>" +
            "<div class=\'d-flex gap-2 mt-4 pt-4 border-top\'>" +
                "<a href=\'" + d.editUrl + "\' class=\'btn btn-primary w-100 shadow-sm rounded-pill fw-semibold\'><i class=\'fa fa-wrench me-2\'></i> Fix Issues</a>" +
                "<a href=\'" + d.viewUrl + "\' target=\'_blank\' class=\'btn btn-light text-primary w-100 shadow-sm rounded-pill border fw-semibold\'><i class=\'fa fa-external-link-alt me-2\'></i> Live Preview</a>" +
            "</div></div></div>" +
        "</div>";

    seoModal.show();
}
</script>';

require_once '../include/footer.php';
?>