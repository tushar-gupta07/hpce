<?php
// C:\xampp\htdocs\rkhospital\admin\blog\ajax_blogs.php
// Handles AJAX requests for blog search + pagination

require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
if (!canAccess('blogs')) { header('Content-Type: application/json'); echo json_encode(['error'=>'Forbidden']); exit; }

header('Content-Type: application/json');

// -------------------------------------------------------
// Helper: resolve image src path
// -------------------------------------------------------
define('BASE_PATH', '/rkhospital/');

function resolveImageSrc($imageField) {
    if (empty($imageField)) return '';
    if (strpos($imageField, 'http://') === 0 || strpos($imageField, 'https://') === 0) {
        return $imageField;
    }
    $clean = ltrim($imageField, '/');
    return BASE_PATH . $clean;
}

function calcSeoScore($blog) {
    $score = 0; $issues = []; $good = [];

    $mt = $blog['meta_title'] ?? '';
    if (strlen($mt) >= 50 && strlen($mt) <= 60) { $score += 15; $good[] = 'Meta title perfect length'; }
    elseif (strlen($mt) > 0)                    { $score += 7;  $issues[] = 'Meta title not ideal (50-60 chars)'; }
    else                                         { $issues[] = 'Meta title missing'; }

    $md = $blog['meta_description'] ?? '';
    if (strlen($md) >= 120 && strlen($md) <= 160) { $score += 15; $good[] = 'Meta description perfect'; }
    elseif (strlen($md) > 0)                      { $score += 7;  $issues[] = 'Meta desc not ideal (120-160 chars)'; }
    else                                           { $issues[] = 'Meta description missing'; }

    $kw = strtolower($blog['focus_keyword'] ?? '');
    if (!empty($kw)) {
        $score += 5; $good[] = 'Focus keyword set';
        if (strpos(strtolower($blog['title']), $kw) !== false) { $score += 10; $good[] = 'Keyword in title'; }
        else { $issues[] = 'Keyword missing from title'; }
        if (strpos(strtolower($md), $kw) !== false) { $score += 10; $good[] = 'Keyword in meta desc'; }
        else { $issues[] = 'Keyword missing from meta desc'; }
        $kwSlug = str_replace(' ', '-', $kw);
        if (strpos($blog['slug'] ?? '', $kwSlug) !== false) { $score += 5; $good[] = 'Keyword in slug'; }
        else { $issues[] = 'Keyword not in slug'; }
    } else {
        $issues[] = 'No focus keyword set';
    }

    $content = strip_tags($blog['content'] ?? '');
    $wc = str_word_count($content);
    if ($wc >= 600)      { $score += 15; $good[] = "Good content length ($wc words)"; }
    elseif ($wc >= 200)  { $score += 8;  $issues[] = "Content short ($wc words, aim 600+)"; }
    else                 { $issues[] = "Content too short ($wc words)"; }

    if (!empty($blog['image']))       { $score += 5; $good[] = 'Featured image set'; }  else { $issues[] = 'No featured image'; }
    if (!empty($blog['og_title']))    { $score += 5; $good[] = 'OG title set'; }         else { $issues[] = 'OG title missing'; }
    if (!empty($blog['schema_type'])) { $score += 5; $good[] = 'Schema markup set'; }   else { $issues[] = 'No schema type'; }
    if (!empty($blog['reading_time'])){ $score += 5; $good[] = 'Reading time set'; }

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

// Count
$countResult  = $conn->query("SELECT COUNT(*) AS total FROM blogs b
    LEFT JOIN categories bc ON b.category_id = bc.id
    LEFT JOIN doctors ba ON b.id = ba.id
    WHERE b.title LIKE '$searchLike' OR ba.name LIKE '$searchLike' OR bc.name LIKE '$searchLike'");
$totalRecords = $countResult ? (int)$countResult->fetch_assoc()['total'] : 0;
$totalPages   = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;
$page         = min($page, $totalPages);
$offset       = ($page - 1) * $limit;

// Fetch
$sql = "SELECT b.*, bc.name AS category_name, ba.name AS author_name
        FROM blogs b
        LEFT JOIN categories bc ON b.category_id = bc.id
        LEFT JOIN doctors ba ON b.id = ba.id
        WHERE b.title LIKE '$searchLike' OR ba.name LIKE '$searchLike' OR bc.name LIKE '$searchLike'
        ORDER BY b.created_at DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$blogs  = [];
if ($result) { while ($row = $result->fetch_assoc()) { $blogs[] = $row; } }

// Build response
$rows    = [];
$seoMap  = [];

foreach ($blogs as $blog) {
    $seo        = calcSeoScore($blog);
    $score      = $seo['score'];
    [$grade, $gradeTextColor] = seoGrade($score);
    $rankLabel  = rankPotential($score);
    $issueCount = count($seo['issues']);

    $mtLen   = strlen($blog['meta_title'] ?? '');
    $mdLen   = strlen($blog['meta_description'] ?? '');
    $ctrBase = ($score / 100) * 8;
    if ($mtLen >= 50 && $mtLen <= 60) $ctrBase += 1;
    if ($mdLen >= 120 && $mdLen <= 160) $ctrBase += 0.5;
    $ctrBase  = min(9.9, $ctrBase);
    $ctrColor = $ctrBase >= 5 ? 'text-success' : ($ctrBase >= 3 ? 'text-warning' : 'text-danger');
    $ctrBg    = $ctrBase >= 5 ? 'bg-success'   : ($ctrBase >= 3 ? 'bg-warning'   : 'bg-danger');
    $imgSrc   = resolveImageSrc($blog['image'] ?? '');

    $gradeBgClass = str_replace('text-', 'bg-', $gradeTextColor);

    // Build row HTML
    ob_start();
    ?>
    <tr class="border-bottom border-light">
        <!-- Title & Image -->
        <td class="ps-4 py-3">
            <div class="d-flex align-items-center gap-3">
                <?php if (!empty($imgSrc)): ?>
                    <img src="<?= htmlspecialchars($imgSrc) ?>"
                         class="blog-thumb shadow-sm border border-light"
                         alt="<?= htmlspecialchars($blog['title']) ?>"
                         width="80" height="80"
                         style="width:80px!important;height:80px!important;object-fit:cover;border-radius:0.5rem;flex-shrink:0;display:block;"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="blog-thumb-placeholder" style="display:none;"><i class="fa fa-image fs-5"></i></div>
                <?php else: ?>
                    <div class="blog-thumb-placeholder"><i class="fa fa-image fs-5"></i></div>
                <?php endif; ?>
                <div>
                    <h6 class="mb-1 fw-bold text-dark text-truncate" style="max-width:280px;" title="<?= htmlspecialchars($blog['title']) ?>">
                        <?= htmlspecialchars($blog['title']) ?>
                    </h6>
                    <div class="small text-muted d-flex align-items-center gap-2">
                        <span><?= !empty($blog['published_at']) ? date('M d, Y', strtotime($blog['published_at'])) : 'Draft' ?></span>
                        <i class="fa fa-circle" style="font-size:4px;"></i>
                        <span class="text-truncate" style="max-width:150px;">/<?= htmlspecialchars($blog['slug'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        </td>

        <!-- Category -->
        <td class="py-3">
            <?php if (!empty($blog['category_name'])): ?>
                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 fw-medium border border-primary-subtle">
                    <?= htmlspecialchars($blog['category_name']) ?>
                </span>
            <?php else: ?>
                <span class="text-muted small fst-italic">None</span>
            <?php endif; ?>
        </td>

        <!-- SEO Health -->
        <td class="py-3 text-center">
            <div class="d-inline-flex align-items-center gap-3"
                 onclick="openSeoModal(<?= $blog['id'] ?>)"
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

        <!-- Performance -->
        <td class="py-3" style="min-width:140px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted fw-medium">CTR</span>
                <span class="small fw-bold <?= $ctrColor ?>">~<?= number_format($ctrBase, 1) ?>%</span>
            </div>
            <div class="progress rounded-pill bg-light mb-2" style="height:5px;">
                <div class="progress-bar <?= $ctrBg ?> rounded-pill" style="width:<?= min(100, $ctrBase * 10) ?>%"></div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-dark fw-medium" data-bs-toggle="tooltip" title="Total Views">
                    <i class="fa fa-eye text-muted me-1"></i><?= number_format((int)($blog['views'] ?? 0)) ?>
                </span>
                <span class="small text-dark fw-medium" data-bs-toggle="tooltip" title="Total Comments">
                    <i class="fa fa-comment text-muted me-1"></i><?= (int)($blog['comments'] ?? 0) ?>
                </span>
            </div>
        </td>

        <!-- Status Toggle -->
        <td class="py-3">
            <a href="./?toggle=<?= $blog['id'] ?>" class="text-decoration-none">
                <?php if ($blog['is_published']): ?>
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
                <a href="../blog/<?= htmlspecialchars($blog['slug'] ?? '') ?>"
                   class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary"
                   target="_blank"
                   data-bs-toggle="tooltip" title="View Article">
                    <i class="fa fa-external-link-alt"></i>
                </a>
                <div class="border-start border-light"></div>
                <a href="edit?id=<?= $blog['id'] ?>"
                   class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary"
                   data-bs-toggle="tooltip" title="Edit Article">
                    <i class="fa fa-pencil-alt"></i>
                </a>
                <div class="border-start border-light"></div>
                <a href="./?delete=<?= $blog['id'] ?>"
                   class="btn btn-sm btn-light border-0 py-2 px-3 text-danger"
                   onclick="return confirm('Delete this blog permanently? This cannot be undone.')"
                   data-bs-toggle="tooltip" title="Delete Article">
                    <i class="fa fa-trash-alt"></i>
                </a>
            </div>
        </td>
    </tr>
    <?php
    $rowHtml = ob_get_clean();
    $rows[] = $rowHtml;

    // SEO data for modal
    $seoMap[$blog['id']] = [
        'title'          => $blog['title'],
        'score'          => (int)$score,
        'grade'          => $grade,
        'gradeTextClass' => $gradeTextColor,
        'issues'         => $seo['issues'],
        'good'           => $seo['good'],
        'ctr'            => number_format($ctrBase, 1),
        'ctrClass'       => $ctrBg,
        'ctrTextClass'   => $ctrColor,
        'rank'           => $rankLabel,
        'metaTitle'      => $blog['meta_title'] ?? '',
        'metaDesc'       => $blog['meta_description'] ?? '',
        'keyword'        => $blog['focus_keyword'] ?? '',
        'schema'         => $blog['schema_type'] ?? '',
        'robots'         => $blog['robots_meta'] ?? '',
        'readingTime'    => (int)($blog['reading_time'] ?? 0),
        'slug'           => $blog['slug'] ?? '',
        'editUrl'        => 'edit.php?id=' . $blog['id'],
        'viewUrl'        => '../blog/' . ($blog['slug'] ?? ''),
    ];
}

// Build empty state HTML if no results
if (empty($blogs)) {
    $emptyHtml = '<tr><td colspan="6" class="text-center py-5">
        <div class="py-5">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                <i class="fa fa-folder-open fs-1 text-secondary opacity-50"></i>
            </div>
            <h5 class="text-dark fw-bold">No articles found</h5>
            <p class="text-muted mb-4">No blogs matching <strong>' . htmlspecialchars($search) . '</strong>.</p>
            <a href="./" class="btn btn-primary rounded-pill px-4 shadow-sm">Clear Search</a>
        </div>
    </td></tr>';
    $rows = [$emptyHtml];
}

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