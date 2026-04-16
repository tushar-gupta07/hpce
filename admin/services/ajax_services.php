<?php
// C:\xampp\htdocs\hpce\admin\services\ajax_services.php
// Handles AJAX requests for services search + pagination

require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
if (!canAccess('services')) { header('Content-Type: application/json'); echo json_encode(['error'=>'Forbidden']); exit; }

header('Content-Type: application/json');

// -------------------------------------------------------
// Helper: resolve image src path
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

$limit  = 10;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchLike = '%' . $conn->real_escape_string($search) . '%';

// Count Total Records
$countResult  = $conn->query("SELECT COUNT(*) AS total FROM services s
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE s.title LIKE '$searchLike' OR c.name LIKE '$searchLike'");
$totalRecords = $countResult ? (int)$countResult->fetch_assoc()['total'] : 0;
$totalPages   = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;
$page         = min($page, $totalPages);
$offset       = ($page - 1) * $limit;

// Fetch Records
$sql = "SELECT s.*, c.name AS category_name
        FROM services s
        LEFT JOIN categories c ON s.category_id = c.id
        WHERE s.title LIKE '$searchLike' OR c.name LIKE '$searchLike'
        ORDER BY s.sort_order ASC, s.created_at DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$services  = [];
if ($result) { while ($row = $result->fetch_assoc()) { $services[] = $row; } }

// Build Response Arrays
$rows    = [];
$seoMap  = [];

foreach ($services as $service) {
    $seo        = calcSeoScore($service);
    $score      = $seo['score'];
    [$grade, $gradeTextColor] = seoGrade($score);
    $rankLabel  = rankPotential($score);
    $issueCount = count($seo['issues']);

    // Calculate simulated CTR
    $mtLen   = strlen($service['meta_title'] ?? '');
    $mdLen   = strlen($service['meta_description'] ?? '');
    $ctrBase = ($score / 100) * 8;
    if ($mtLen >= 50 && $mtLen <= 60) $ctrBase += 1;
    if ($mdLen >= 120 && $mdLen <= 160) $ctrBase += 0.5;
    $ctrBase  = min(9.9, $ctrBase);
    $ctrColor = $ctrBase >= 5 ? 'text-success' : ($ctrBase >= 3 ? 'text-warning' : 'text-danger');
    $ctrBg    = $ctrBase >= 5 ? 'bg-success'   : ($ctrBase >= 3 ? 'bg-warning'   : 'bg-danger');
    
    $imgSrc   = resolveImageSrc($service['image'] ?? '');
    $gradeBgClass = str_replace('text-', 'bg-', $gradeTextColor);

    // Build row HTML
    ob_start();
    ?>
    <tr class="border-bottom border-light">
        <td class="ps-4 py-3">
            <div class="d-flex align-items-center gap-3">
                <?php if (!empty($imgSrc)): ?>
                    <img src="<?= htmlspecialchars($imgSrc) ?>"
                         class="service-thumb shadow-sm border border-light"
                         alt="<?= htmlspecialchars($service['title']) ?>"
                         width="80" height="80"
                         style="width:80px!important;height:80px!important;object-fit:cover;border-radius:0.5rem;flex-shrink:0;display:block;"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="service-thumb-placeholder" style="display:none;"><i class="fa fa-stethoscope fs-5"></i></div>
                <?php else: ?>
                    <div class="service-thumb-placeholder"><i class="fa fa-stethoscope fs-5"></i></div>
                <?php endif; ?>
                <div>
                    <h6 class="mb-1 fw-bold text-dark text-truncate" style="max-width:280px;" title="<?= htmlspecialchars($service['title']) ?>">
                        <?= htmlspecialchars($service['title']) ?>
                    </h6>
                    <div class="small text-muted d-flex align-items-center gap-2">
                        <span><?= !empty($service['created_at']) ? date('M d, Y', strtotime($service['created_at'])) : '' ?></span>
                        <i class="fa fa-circle" style="font-size:4px;"></i>
                        <span class="text-truncate" style="max-width:150px;">/<?= htmlspecialchars($service['slug'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        </td>

        <td class="py-3">
            <?php if (!empty($service['category_name'])): ?>
                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 fw-medium border border-primary-subtle">
                    <?= htmlspecialchars($service['category_name']) ?>
                </span>
            <?php else: ?>
                <span class="text-muted small fst-italic">None</span>
            <?php endif; ?>
        </td>

        <td class="py-3 text-center">
            <div class="d-inline-flex align-items-center gap-3"
                 onclick="openSeoModal(<?= $service['id'] ?>)"
                 data-bs-toggle="tooltip" title="Click for SEO details"
                 style="cursor:pointer;">
                <div class="position-relative" style="width:44px;height:44px;">
                    <svg viewBox="0 0 36 36" class="w-100 h-100" style="transform:rotate(-90deg);">
                        <circle cx="18" cy="18" r="15.9" fill="none" class="text-light" stroke="currentColor" stroke-width="3"></circle>
                        <circle cx="18" cy="18" r="15.9" fill="none" class="<?= $gradeTextColor ?>" stroke="currentColor" stroke-width="3"
                            stroke-dasharray="100 100"
                            stroke-dashoffset="<?= 100 - $score ?>"
                            stroke-linecap="round"
                            style="transition:stroke-dashoffset 1s ease-out;"></circle>
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle fw-bold small <?= $gradeTextColor ?>" style="font-size:0.8rem;">
                        <?= $score ?>
                    </div>
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

        <td class="py-3 text-center">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">
                #<?= (int)($service['sort_order'] ?? 0) ?>
            </span>
        </td>

        <td class="py-3">
            <a href="./?toggle=<?= $service['id'] ?>" class="text-decoration-none">
                <?php if ($service['is_published']): ?>
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

        <td class="py-3 text-end pe-4">
            <div class="btn-group shadow-sm border rounded-pill overflow-hidden bg-white">
                <a href="<?= BASE_PATH ?>service/<?= htmlspecialchars($service['slug'] ?? '') ?>"
                   class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary"
                   target="_blank"
                   data-bs-toggle="tooltip" title="View Service">
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
    <?php
    $rowHtml = ob_get_clean();
    $rows[] = $rowHtml;

    // Build SEO data mapping for the frontend Modal
    $seoMap[$service['id']] = [
        'title'          => $service['title'],
        'score'          => (int)$score,
        'grade'          => $grade,
        'gradeTextClass' => $gradeTextColor,
        'issues'         => $seo['issues'],
        'good'           => $seo['good'],
        'ctr'            => number_format($ctrBase, 1),
        'ctrClass'       => $ctrBg,
        'ctrTextClass'   => $ctrColor,
        'rank'           => $rankLabel,
        'metaTitle'      => $service['meta_title'] ?? '',
        'metaDesc'       => $service['meta_description'] ?? '',
        'keyword'        => $service['focus_keyword'] ?? '',
        'schema'         => $service['schema_type'] ?? '',
        'robots'         => $service['robots_meta'] ?? '',
        'slug'           => $service['slug'] ?? '',
        'editUrl'        => 'edit.php?id=' . $service['id'],
        'viewUrl'        => BASE_PATH . 'service/' . ($service['slug'] ?? ''),
    ];
}

// Build empty state HTML if no results
if (empty($services)) {
    $emptyHtml = '<tr><td colspan="6" class="text-center py-5">
        <div class="py-5">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                <i class="fa fa-folder-open fs-1 text-secondary opacity-50"></i>
            </div>
            <h5 class="text-dark fw-bold">No services found</h5>
            <p class="text-muted mb-4">No services matching <strong>' . htmlspecialchars($search) . '</strong>.</p>
            <a href="./" class="btn btn-primary rounded-pill px-4 shadow-sm">Clear Search</a>
        </div>
    </td></tr>';
    $rows = [$emptyHtml];
}

// Output final JSON response
echo json_encode([
    'rows'         => implode('', $rows),
    'totalRecords' => $totalRecords,
    'totalPages'   => $totalPages,
    'currentPage'  => $page,
    'offset'       => $offset,
    'limit'        => $limit,
    'seoData'      => $seoMap,
    'search'       => $search,
]);