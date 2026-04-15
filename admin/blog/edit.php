<?php
// C:\xamppnew\htdocs\rkhospital\admin\blog\edit.php

require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('blogs');

// ── Helper: Convert Image to WebP (Moved to top for global availability) ──
function convertToWebp($source, $destination, $quality = 80) {
    $info = getimagesize($source);
    if (!$info) return false;

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
    } elseif ($info['mime'] == 'image/webp') {
        $image = imagecreatefromwebp($source);
    } else {
        return false;
    }

    $success = imagewebp($image, $destination, $quality);
    imagedestroy($image);
    return $success;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ./");
    exit;
}
$id = (int)$_GET['id'];

// Fetch existing blog data
$res  = $conn->query("SELECT * FROM blogs WHERE id = $id");
$blog = $res ? $res->fetch_assoc() : null;
if (!$blog) { header("Location: ./"); exit; }

$errors = [];

$categories = [];
$res = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($res) { while ($r = $res->fetch_assoc()) { $categories[] = $r; } }

$doctors = [];
$res = $conn->query("SELECT id, name FROM doctors ORDER BY name ASC");
if ($res) { while ($r = $res->fetch_assoc()) { $doctors[] = $r; } }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Core Fields ──────────────────────────────────────────────
    $title        = trim($_POST['title'] ?? '');
    $slug         = trim($_POST['slug'] ?? '');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = $_POST['content'] ?? '';
    $category_id  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $doctor_id    = !empty($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 'NULL';
    $tags         = trim($_POST['tags'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $published_at = !empty($_POST['published_at']) ? trim($_POST['published_at']) : date('Y-m-d');

    // ── SEO Fields ───────────────────────────────────────────────
    $meta_title          = trim($_POST['meta_title'] ?? '');
    $meta_description    = trim($_POST['meta_description'] ?? '');
    $focus_keyword       = trim($_POST['focus_keyword'] ?? '');
    $canonical_url       = trim($_POST['canonical_url'] ?? '');
    $og_title            = trim($_POST['og_title'] ?? '');
    $og_description      = trim($_POST['og_description'] ?? '');
    $og_type             = trim($_POST['og_type'] ?? 'article');
    $twitter_title       = trim($_POST['twitter_title'] ?? '');
    $twitter_description = trim($_POST['twitter_description'] ?? '');
    $robots_index        = trim($_POST['robots_index'] ?? 'index');
    $robots_follow       = trim($_POST['robots_follow'] ?? 'follow');
    $schema_type         = trim($_POST['schema_type'] ?? 'BlogPosting');
    $reading_time        = !empty($_POST['reading_time']) ? (int)$_POST['reading_time'] : null;
    
    // ── Validation ───────────────────────────────────────────────
    if (empty($title))   $errors[] = 'Title is required.';
    if (empty($content) || $content === '<p><br></p>') $errors[] = 'Content is required.';
    if (!empty($meta_title) && mb_strlen($meta_title) > 60)
        $errors[] = 'Meta title should not exceed 60 characters.';
    if (!empty($meta_description) && mb_strlen($meta_description) > 160)
        $errors[] = 'Meta description should not exceed 160 characters.';

    // ── Slug Generation ──────────────────────────────────────────
    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
    } else {
        $slug = strtolower(preg_replace('/[^a-z0-9-]+/', '-', $slug));
    }
    $slug    = trim($slug, '-');
    $slugEsc = $conn->real_escape_string($slug);

    $chk = $conn->query("SELECT id FROM blogs WHERE slug = '$slugEsc' AND id != $id");
    if ($chk && $chk->num_rows > 0) {
        $errors[] = 'Slug already exists. Please use a different one.';
    }

    // ── Image Upload ─────────────────────────────────────────────
    $imagePath   = $blog['image'];
    $ogImagePath = $blog['og_image'];
    $seoBaseName = !empty($slug) ? $slug : 'blog-image'; // Use slug for SEO friendly name

    if (!empty($_FILES['image']['name'])) {
        $allowedTypes = ['image/jpeg','image/png','image/webp','image/gif'];
        $fileType     = mime_content_type($_FILES['image']['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Invalid main image type. Allowed: JPG, PNG, WEBP, GIF.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Main image size must be under 2MB.';
        } else {
            // Point to the root assets folder (two levels up from admin/blog/edit.php)
            $uploadDir = '../../assets/img/blog/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            // Create SEO friendly WebP filename
            $fileName = $seoBaseName . '-' . uniqid() . '.webp';
            $targetPath = $uploadDir . $fileName;

            if (convertToWebp($_FILES['image']['tmp_name'], $targetPath, 85)) {
                // Remove old image from root if it exists
                if (!empty($blog['image']) && file_exists('../../' . $blog['image'])) {
                    @unlink('../../' . $blog['image']);
                }
                
                // Save DB path as relative to the root
                $imagePath = 'assets/img/blog/' . $fileName;
                
                // Auto-set OG if missing
                if (empty($ogImagePath)) $ogImagePath = $imagePath;
            } else {
                $errors[] = 'Failed to convert main image to WebP format.';
            }
        }
    }

    // ── OG Image Upload ──────────────────────────────────────────
    if (!empty($_FILES['og_image']['name'])) {
        $fileType2 = mime_content_type($_FILES['og_image']['tmp_name']);
        if (in_array($fileType2, ['image/jpeg','image/png','image/webp','image/gif'])) {
            $uploadDirOg = '../../assets/img/blog/og/';
            if (!is_dir($uploadDirOg)) mkdir($uploadDirOg, 0755, true);
            
            // Create SEO friendly WebP filename for OG
            $ogFile  = $seoBaseName . '-og-' . uniqid() . '.webp';
            $targetPathOg = $uploadDirOg . $ogFile;

            if (convertToWebp($_FILES['og_image']['tmp_name'], $targetPathOg, 80)) {
                if (!empty($blog['og_image']) && file_exists('../../' . $blog['og_image']) && $blog['og_image'] !== $blog['image']) {
                    @unlink('../../' . $blog['og_image']);
                }
                $ogImagePath = 'assets/img/blog/og/' . $ogFile;
            } else {
                $errors[] = 'Failed to convert Open Graph image to WebP format.';
            }
        }
    }

    // ── Build Schema JSON ────────────────────────────────────────
    $schema_json = '';
    if (!empty($schema_type)) {
        $schemaData = [
            '@context' => 'https://schema.org',
            '@type'    => $schema_type,
            'headline' => $meta_title ?: $title,
            'description' => $meta_description ?: $excerpt,
            'url'      => (!empty($canonical_url) ? $canonical_url : ''),
            'image'    => (!empty($ogImagePath) ? $ogImagePath : ''),
            'datePublished' => $published_at ?: date('Y-m-d'),
            'dateModified'  => date('Y-m-d'),
        ];
        $schema_json = json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    // ── Update ───────────────────────────────────────────────────
    if (empty($errors)) {
        $s = fn($v) => $conn->real_escape_string($v);

        $robots_meta = $robots_index . ',' . $robots_follow;
        $pubAt   = "'" . $s($published_at) . "'";
        $catVal  = $category_id  ? (int)$category_id : 'NULL';
        $rtVal   = $reading_time ? (int)$reading_time : 'NULL';

        $sql = "UPDATE blogs SET
                    title = '{$s($title)}',
                    slug = '{$s($slug)}',
                    excerpt = '{$s($excerpt)}',
                    content = '{$s($content)}',
                    image = '{$s($imagePath)}',
                    category_id = $catVal,
                    doctor_id = $doctor_id,
                    tags = '{$s($tags)}',
                    is_published = $is_published,
                    published_at = $pubAt,
                    meta_title = '{$s($meta_title)}',
                    meta_description = '{$s($meta_description)}',
                    focus_keyword = '{$s($focus_keyword)}',
                    canonical_url = '{$s($canonical_url)}',
                    og_title = '{$s($og_title)}',
                    og_description = '{$s($og_description)}',
                    og_image = '{$s($ogImagePath)}',
                    og_type = '{$s($og_type)}',
                    twitter_title = '{$s($twitter_title)}',
                    twitter_description = '{$s($twitter_description)}',
                    robots_meta = '{$s($robots_meta)}',
                    schema_type = '{$s($schema_type)}',
                    schema_json = '{$s($schema_json)}',
                    reading_time = $rtVal,
                    updated_at = NOW()
                WHERE id = $id";

        if ($conn->query($sql)) {
            header("Location: ./?msg=updated");
            exit;
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
    }

    // Repopulate form with failed POST data
    $blog = array_merge($blog, $_POST);
}

// helper for repopulate (prioritize POST over DB)
$p = fn($k) => htmlspecialchars($_POST[$k] ?? $blog[$k] ?? '');

// 2. Setup Page Variables for Includes
$pageTitle  = 'Edit Blog';
$activePage = 'blogs-edit';
$assetBase  = '../';

$extraCSS = '
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    :root {
        --success: #198754;
        --warning: #ffc107;
        --danger: #dc3545;
        --text-1: #212529;
        --text-2: #6c757d;
        --text-3: #adb5bd;
    }

    .form-label { font-size: 0.75rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
    .input-group-text { background: #f8f9fa; color: #6c757d; font-size: 0.875rem; border-color: #dee2e6; }
    .form-control, .form-select { font-size: 0.9rem; padding: 0.6rem 1rem; border-color: #dee2e6; }
    .form-control:focus, .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }

    .char-counter { display: flex; justify-content: space-between; margin-top: 6px; font-size: 0.7rem; color: #adb5bd; font-family: monospace; }
    .char-counter .count { font-weight: 700; }
    .char-counter .count.ok    { color: var(--success); }
    .char-counter .count.warn  { color: var(--warning); }
    .char-counter .count.bad   { color: var(--danger); }
    .char-bar { height: 4px; background: #e9ecef; border-radius: 4px; margin-top: 6px; overflow: hidden; }
    .char-bar-fill { height: 100%; border-radius: 4px; transition: width .3s, background .3s; }

    .seo-score-ring { width: 70px; height: 70px; position: relative; flex-shrink: 0; }
    .seo-score-ring svg { transform: rotate(-90deg); width: 100%; height: 100%; }
    .seo-score-ring .score-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
    .seo-score-ring .score-num { font-size: 1.25rem; font-weight: 800; line-height: 1; display: block; }
    .seo-score-ring .score-label { font-size: 0.55rem; color: #adb5bd; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .seo-checks { flex: 1; min-width: 0; }
    .seo-check-item { display: flex; align-items: flex-start; gap: 8px; padding: 6px 0; font-size: 0.8rem; color: #495057; border-bottom: 1px solid #f1f3f5; }
    .seo-check-item:last-child { border-bottom: none; }
    .seo-check-item .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
    .dot-ok   { background: var(--success); box-shadow: 0 0 4px rgba(25, 135, 84, 0.4); }
    .dot-warn { background: var(--warning); }
    .dot-bad  { background: var(--danger); }

    .serp-preview { background: #fff; border: 1px solid #dfe1e5; border-radius: 0.5rem; padding: 1rem; margin-top: 0.25rem; }
    .serp-url { font-size: 0.75rem; color: #202124; font-family: Arial, sans-serif; margin-bottom: 2px; }
    .serp-title { font-size: 1.125rem; color: #1a0dab; font-family: Arial, sans-serif; cursor: pointer; line-height: 1.3; margin-bottom: 2px; }
    .serp-desc { font-size: 0.85rem; color: #4d5156; font-family: Arial, sans-serif; line-height: 1.5; }
    .serp-date { font-size: 0.85rem; color: #70757a; font-family: Arial, sans-serif; }
    .serp-placeholder { color: #9aa0a6 !important; font-style: italic; }

    .og-preview-card { border: 1px solid #e0e0e0; border-radius: 0.5rem; overflow: hidden; background: #f8f9fa; margin-top: 6px; }
    .og-preview-img { width: 100%; height: 160px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #adb5bd; font-size: 0.8rem; overflow: hidden; }
    .og-preview-img img { width: 100%; height: 100%; object-fit: cover; }
    .og-preview-body { padding: 0.75rem 1rem; background: #fff; border-top: 1px solid #e0e0e0; }
    .og-preview-domain { font-size: 0.65rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-family: Arial, sans-serif; }
    .og-preview-title  { font-size: 1rem; font-weight: 700; color: #1a1a1a; font-family: Arial, sans-serif; margin: 4px 0; }
    .og-preview-desc   { font-size: 0.85rem; color: #495057; font-family: Arial, sans-serif; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .ql-toolbar.ql-snow { background: #f8f9fa; border-color: #dee2e6; border-radius: 0.5rem 0.5rem 0 0; font-family: inherit; }
    .ql-container.ql-snow { border-color: #dee2e6; border-radius: 0 0 0.5rem 0.5rem; font-family: inherit; }
    .ql-editor { min-height: 350px; font-size: 0.95rem; line-height: 1.7; color: #212529; }

    .img-upload-zone { border: 2px dashed #dee2e6; border-radius: 0.75rem; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s ease; background: #f8f9fa; position: relative; }
    .img-upload-zone:hover { border-color: #0d6efd; background: #f1f7ff; }
    .img-upload-zone .upload-icon { font-size: 2rem; color: #adb5bd; margin-bottom: 0.5rem; }
    .img-upload-zone p { font-size: 0.85rem; color: #6c757d; margin: 0; font-weight: 500; }
    .img-upload-zone .preview-img { width: 100%; border-radius: 0.5rem; object-fit: cover; display: none; margin-top: 10px; max-height: 200px; }

    .keyword-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .keyword-tag { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 20px; padding: 3px 12px; font-size: 0.75rem; color: #495057; cursor: pointer; transition: all 0.15s; }
    .keyword-tag:hover { border-color: #0d6efd; color: #0d6efd; background: #f1f7ff; }

    .schema-options, .robots-group { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
    .schema-opt, .robots-btn { padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; cursor: pointer; border: 1px solid #dee2e6; color: #6c757d; background: #fff; transition: all 0.15s; flex: 1; text-align: center; }
    .schema-opt.active, .robots-btn.active-index { border-color: var(--success); color: var(--success); background: rgba(25, 135, 84, 0.05); }
    .robots-btn.active-noindex, .robots-btn.active-nofollow { border-color: var(--danger); color: var(--danger); background: rgba(220, 53, 69, 0.05); }
    .robots-btn.active-follow { border-color: var(--success); color: var(--success); background: rgba(25, 135, 84, 0.05); }

    .kd-bar { height: 5px; background: #e9ecef; border-radius: 4px; overflow: hidden; margin: 6px 0; }
    .kd-fill { height: 100%; border-radius: 4px; background: #0d6efd; transition: width .4s; }

    .nav-pills .nav-link { color: #6c757d; border-radius: 20px; font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; transition: all 0.2s; }
    .nav-pills .nav-link.active { background-color: #e7f1ff; color: #0d6efd; }
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
                    <h3 class="fw-bolder text-dark mb-1">Edit Blog Post</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="./" class="text-muted text-decoration-none">Blogs</a></li>
                            <li class="breadcrumb-item active text-secondary fw-medium">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <a href="<?= SITE_URL ?>/blog/<?= htmlspecialchars($blog['slug']) ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm fw-semibold d-inline-flex align-items-center gap-2 bg-white">
                        <i class="fa fa-external-link-alt"></i> View on Site
                    </a>
                    <a href="./" class="btn btn-light rounded-pill px-4 py-2 shadow-sm fw-semibold border d-inline-flex align-items-center gap-2">
                        <i class="fa fa-arrow-left"></i> Back to Blogs
                    </a>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-start gap-3" role="alert">
                    <i class="fa fa-exclamation-triangle mt-1 fs-5"></i>
                    <div>
                        <div class="fw-bold mb-1">Please fix the following errors:</div>
                        <ul class="mb-0 ps-3 small">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="blogForm">
                <div class="row g-4">

                    <div class="col-xl-8 col-lg-7">

                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fa fa-edit"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">Blog Content</h6>
                            </div>
                            <div class="card-body p-4">

                                <div class="mb-4">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="blogTitle" class="form-control"
                                           placeholder="Enter an engaging blog title..."
                                           value="<?= $p('title') ?>">
                                    <div class="char-counter">
                                        <span>Title length</span>
                                        <span class="count" id="titleCount">0 chars</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">URL Slug</label>
                                    <div class="input-group">
                                        <input type="text" name="slug" id="blogSlug" class="form-control"
                                               placeholder="auto-generated-from-title"
                                               value="<?= $p('slug') ?>">
                                        <button type="button" class="btn btn-light border text-secondary" id="generateSlug" title="Auto-generate from title">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">
                                        Lowercase letters, numbers and hyphens only.
                                        <a href="<?= SITE_URL ?>/blog/<?= htmlspecialchars($blog['slug']) ?>" target="_blank" class="text-decoration-none ms-2 text-primary">
                                            <i class="fa fa-link"></i> Preview URL
                                        </a>
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Excerpt / Short Description</label>
                                    <textarea name="excerpt" id="blogExcerpt" class="form-control" rows="3"
                                              placeholder="Write a compelling 1-2 sentence summary shown in blog listings and social shares..."><?= $p('excerpt') ?></textarea>
                                    <div class="char-counter">
                                        <span>Excerpt length</span>
                                        <span class="count" id="excerptCount">0 chars</span>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span>Content <span class="text-danger">*</span></span>
                                        <span id="readingTimeDisplay" class="badge bg-light border text-secondary fw-medium rounded-pill" style="display:none!important;">
                                            <i class="fa fa-clock me-1"></i> <span id="readingTimeText">~0 min read</span>
                                        </span>
                                    </label>
                                    <div id="quillEditor" class="bg-white"></div>
<textarea name="content" id="blogContent" class="d-none"><?= htmlspecialchars($blog['content'] ?? '') ?></textarea>                                    <input type="hidden" name="reading_time" id="readingTimeInput">
                                </div>

                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success-subtle text-success rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">SEO Settings</h6>
                                </div>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold" id="seoScoreBadge">Score: 0/100</span>
                            </div>
                            <div class="card-body p-4">

                                <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-meta" type="button" role="tab">Meta Tags</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-og" type="button" role="tab">Open Graph</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-twitter" type="button" role="tab">Twitter Card</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-technical" type="button" role="tab">Technical</button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab-meta" role="tabpanel">

                                        <div class="mb-4">
                                            <label class="form-label text-primary"><i class="fa fa-key me-1"></i> Focus Keyword</label>
                                            <input type="text" name="focus_keyword" id="focusKeyword" class="form-control bg-primary-subtle border-primary-subtle text-primary-emphasis fw-bold"
                                                   placeholder="Primary keyword you're targeting..."
                                                   value="<?= $p('focus_keyword') ?>">
                                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Used to analyse keyword density and SEO score in real-time.</small>
                                            <div id="keywordSuggestions" class="keyword-tags"></div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label">Meta Title <span class="text-danger">*</span></label>
                                            <input type="text" name="meta_title" id="metaTitle" class="form-control"
                                                   placeholder="SEO title shown in Google search results..."
                                                   value="<?= $p('meta_title') ?>" maxlength="70">
                                            <div class="char-bar"><div class="char-bar-fill" id="metaTitleBar" style="width:0%"></div></div>
                                            <div class="char-counter">
                                                <span>Ideal: 50–60 characters</span>
                                                <span class="count" id="metaTitleCount">0 / 60</span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label">Meta Description</label>
                                            <textarea name="meta_description" id="metaDesc" class="form-control" rows="3"
                                                      placeholder="Compelling description shown under your title in Google (max 160 chars)..."
                                                      maxlength="180"><?= $p('meta_description') ?></textarea>
                                            <div class="char-bar"><div class="char-bar-fill" id="metaDescBar" style="width:0%"></div></div>
                                            <div class="char-counter">
                                                <span>Ideal: 120–160 characters</span>
                                                <span class="count" id="metaDescCount">0 / 160</span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label">Canonical URL</label>
                                            <input type="text" name="canonical_url" id="canonicalUrl" class="form-control"
                                                   placeholder="https://yourdomain.com/blog/your-post-slug"
                                                   value="<?= $p('canonical_url') ?>">
                                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Leave blank to auto-generate. Use if this content exists on another URL.</small>
                                        </div>

                                        <div class="mt-4">
                                            <label class="form-label"><i class="fab fa-google text-muted me-1"></i> Google SERP Preview</label>
                                            <div class="serp-preview">
                                                <div class="serp-url" id="serpUrl">rkhospital.com › blog › <span id="serpSlug">your-post-slug</span></div>
                                                <div class="serp-title" id="serpTitle"><span class="serp-placeholder">Your meta title will appear here...</span></div>
                                                <div class="serp-date d-inline-block pe-1" id="serpDate"><?= date('M j, Y') ?> — </div>
                                                <div class="serp-desc d-inline" id="serpDesc"><span class="serp-placeholder">Your meta description will appear here. Make it compelling to improve click-through rate.</span></div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="tab-og" role="tabpanel">
                                        <div class="mb-4">
                                            <label class="form-label">OG Title</label>
                                            <input type="text" name="og_title" id="ogTitle" class="form-control"
                                                   placeholder="Title shown when shared on Facebook, LinkedIn..."
                                                   value="<?= $p('og_title') ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Description</label>
                                            <textarea name="og_description" id="ogDesc" class="form-control" rows="2"
                                                      placeholder="Description shown on social media shares..."><?= $p('og_description') ?></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Type</label>
                                            <select name="og_type" class="form-select">
                                                <option value="article" <?= ($p('og_type')||'article')==='article' ? 'selected' : '' ?>>article</option>
                                                <option value="website" <?= $p('og_type')==='website' ? 'selected' : '' ?>>website</option>
                                                <option value="blog"    <?= $p('og_type')==='blog'     ? 'selected' : '' ?>>blog</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Image <span class="text-muted fw-normal text-lowercase">(1200×630 recommended)</span></label>
                                            <div class="img-upload-zone" id="ogImageZone" onclick="document.getElementById('ogImageInput').click()">
                                                <?php if(!empty($blog['og_image'])): ?>
                                                    <img id="ogImagePreview" class="preview-img" src="../../<?= htmlspecialchars($blog['og_image']) ?>" alt="OG Preview" style="display:block;">
                                                    <div id="ogImgPlaceholder" style="display:none;">
                                                <?php else: ?>
                                                    <img id="ogImagePreview" class="preview-img" alt="OG Preview">
                                                    <div id="ogImgPlaceholder">
                                                <?php endif; ?>
                                                        <div class="upload-icon"><i class="fa fa-image"></i></div>
                                                        <p>Click to upload custom OG image</p>
                                                        <small class="text-muted">Will fallback to Featured Image if empty</small>
                                                    </div>
                                            </div>
                                            <input type="file" name="og_image" id="ogImageInput" accept="image/*" class="d-none">
                                        </div>

                                        <div class="mt-4">
                                            <label class="form-label"><i class="fab fa-facebook text-primary me-1"></i> Social Card Preview</label>
                                            <div class="og-preview-card shadow-sm">
                                                <div class="og-preview-img" id="ogPreviewImgBox">
                                                    <?php if(!empty($blog['og_image'])): ?>
                                                        <img src="../../<?= htmlspecialchars($blog['og_image']) ?>">
                                                    <?php elseif(!empty($blog['image'])): ?>
                                                        <img src="../../<?= htmlspecialchars($blog['image']) ?>">
                                                    <?php else: ?>
                                                        <span>No image selected</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="og-preview-body">
                                                    <div class="og-preview-domain">yourdomain.com</div>
                                                    <div class="og-preview-title" id="ogPreviewTitle">OG Title will appear here</div>
                                                    <div class="og-preview-desc" id="ogPreviewDesc">OG description will appear here</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab-twitter" role="tabpanel">
                                        <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis rounded-3 p-3 mb-4 d-flex align-items-center gap-2">
                                            <i class="fa fa-lightbulb"></i>
                                            <small class="fw-medium">Leave these blank to auto-inherit from Meta Title/Description on save.</small>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Title</label>
                                            <input type="text" name="twitter_title" class="form-control"
                                                   placeholder="Title shown on Twitter card..."
                                                   value="<?= $p('twitter_title') ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Description</label>
                                            <textarea name="twitter_description" class="form-control" rows="3"
                                                      placeholder="Description shown on Twitter card..."><?= $p('twitter_description') ?></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Card Type</label>
                                            <select name="twitter_card" class="form-select">
                                                <option value="summary_large_image" selected>summary_large_image (Recommended)</option>
                                                <option value="summary">summary</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab-technical" role="tabpanel">
                                        <?php 
                                            $robots = explode(',', $p('robots_meta') ?: 'index,follow');
                                            $rIndex = $robots[0] ?? 'index';
                                            $rFollow = $robots[1] ?? 'follow';
                                        ?>
                                        <div class="mb-4">
                                            <label class="form-label">Robots Meta Tag</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="robots-group" id="robotsIndexGroup">
                                                        <button type="button" class="robots-btn shadow-sm <?= $rIndex==='index'?'active-index':'' ?>" data-val="index" onclick="setRobots('index',this)">✅ INDEX</button>
                                                        <button type="button" class="robots-btn shadow-sm <?= $rIndex==='noindex'?'active-noindex':'' ?>" data-val="noindex" onclick="setRobots('noindex',this)">🚫 NOINDEX</button>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="robots-group" id="robotsFollowGroup">
                                                        <button type="button" class="robots-btn shadow-sm <?= $rFollow==='follow'?'active-follow':'' ?>" data-val="follow" onclick="setFollow('follow',this)">🔗 FOLLOW</button>
                                                        <button type="button" class="robots-btn shadow-sm <?= $rFollow==='nofollow'?'active-nofollow':'' ?>" data-val="nofollow" onclick="setFollow('nofollow',this)">⛔ NOFOLLOW</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="robots_index" id="robotsIndex" value="<?= $rIndex ?>">
                                            <input type="hidden" name="robots_follow" id="robotsFollow" value="<?= $rFollow ?>">
                                            <small class="fw-medium mt-2 d-block text-success" id="robotsHint">✅ This page will be indexed and links followed by search engines.</small>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label">Schema / Structured Data Type</label>
                                            <div class="schema-options">
                                                <?php $schemas = ['BlogPosting','Article','NewsArticle','MedicalWebPage','FAQPage','HowTo']; ?>
                                                <?php foreach ($schemas as $s): ?>
                                                <button type="button" class="schema-opt shadow-sm <?= ($p('schema_type') ?: 'BlogPosting') === $s ? 'active' : '' ?>"
                                                        onclick="setSchema('<?= $s ?>',this)"><?= $s ?></button>
                                                <?php endforeach; ?>
                                            </div>
                                            <input type="hidden" name="schema_type" id="schemaType" value="<?= $p('schema_type') ?: 'BlogPosting' ?>">
                                            <small class="text-muted mt-2 d-block" style="font-size: 0.75rem;">For healthcare blogs, <strong>MedicalWebPage</strong> or <strong>Article</strong> gives best rich-result coverage.</small>
                                        </div>

                                        <div class="mb-3 p-3 bg-light rounded-3 border">
                                            <label class="form-label text-dark mb-2">Keyword Density Analyser</label>
                                            <div id="kdResults" style="color:#6c757d;font-size:0.8rem;">Enter a focus keyword in Meta Tags and write content to see density analysis.</div>
                                        </div>
                                    </div>

                                </div> </div>
                        </div></div><div class="col-xl-4 col-lg-5">

                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fa fa-paper-plane"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">Update Status</h6>
                                </div>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold" id="publishStatusBadge">Draft</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <div class="form-check form-switch d-flex align-items-center gap-2 m-0 p-0">
                                        <input class="form-check-input ms-0 me-2 mt-0" type="checkbox" role="switch" id="isPublished"
                                               name="is_published" value="1" style="width: 40px; height: 20px;"
                                               <?= $p('is_published') ? 'checked' : '' ?>
                                               onchange="updatePublishBadge(this)">
                                        <label class="form-check-label fw-bold text-dark" for="isPublished">Publish Immediately</label>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Scheduled Publish Date</label>
                                    <input type="datetime-local" name="published_at" class="form-control"
                                           value="<?= !empty($p('published_at')) ? date('Y-m-d\TH:i', strtotime($p('published_at'))) : '' ?>">
                                </div>
                                
                                <div class="row text-center bg-light border rounded-3 p-3 mx-0 mb-4 g-2">
                                    <div class="col-6 border-end">
                                        <div class="fw-bolder fs-5 text-primary"><?= (int)$blog['views'] ?></div>
                                        <div class="small text-muted fw-medium text-uppercase" style="font-size: 0.65rem;">Views</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bolder fs-5 text-success"><?= (int)$blog['comments'] ?></div>
                                        <div class="small text-muted fw-medium text-uppercase" style="font-size: 0.65rem;">Comments</div>
                                    </div>
                                </div>

                                <hr class="border-light-subtle my-4">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm fw-bold mb-2 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa fa-save"></i> Update Blog Post
                                </button>
                                <a href="./" class="btn btn-light w-100 rounded-pill py-2 border text-secondary fw-semibold">Cancel</a>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-success-subtle text-success rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fa fa-chart-line"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">Live SEO Audit</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex gap-3 align-items-center mb-4">
                                    <div class="seo-score-ring">
                                        <svg viewBox="0 0 80 80">
                                            <circle cx="40" cy="40" r="34" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                            <circle id="scoreCircle" cx="40" cy="40" r="34" fill="none" stroke="#198754"
                                                    stroke-width="8" stroke-linecap="round"
                                                    stroke-dasharray="213.6" stroke-dashoffset="213.6"
                                                    style="transition:stroke-dashoffset .5s ease-out, stroke .5s;"/>
                                        </svg>
                                        <div class="score-text">
                                            <span class="score-num text-success" id="scoreNum">0</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="fw-bolder mb-0 text-dark">Overall Health</h5>
                                        <span class="small text-muted fw-medium" id="scoreVerdict">Review required</span>
                                    </div>
                                </div>
                                <div class="seo-checks" id="seoChecklist"></div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fa fa-image"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">Featured Image</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="img-upload-zone" id="mainImageZone" onclick="document.getElementById('imageInput').click()">
                                    <?php if (!empty($blog['image'])): ?>
                                        <img id="imagePreview" class="preview-img shadow-sm" src="../../<?= htmlspecialchars($blog['image']) ?>" alt="Featured Image Preview" style="display:block;">
                                        <div id="imgPlaceholder" style="display:none;">
                                    <?php else: ?>
                                        <img id="imagePreview" class="preview-img shadow-sm" alt="Featured Image Preview">
                                        <div id="imgPlaceholder">
                                    <?php endif; ?>
                                            <div class="upload-icon"><i class="fa fa-cloud-upload-alt"></i></div>
                                            <p>Click or drag to upload</p>
                                            <small class="text-muted" style="font-size: 0.75rem;">JPG, PNG, WEBP · Max 2MB</small>
                                        </div>
                                </div>
                                <input type="file" name="image" id="imageInput" accept="image/*" class="d-none">
                                <div class="alert alert-light border mt-3 mb-0 p-2 d-flex align-items-center gap-2 small">
                                    <i class="fa fa-info-circle text-primary"></i>
                                    <span>Recommended: 1200×628px for best appearance.</span>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-info-subtle text-info rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fa fa-tags"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">Taxonomy & Author</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select rounded-3">
                                        <option value="">— Select Category —</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $p('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Doctor (Author)</label>
                                    <select name="doctor_id" class="form-select rounded-3">
                                        <option value="">— Select Author —</option>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <option value="<?= $doctor['id'] ?>" <?= $p('doctor_id') == $doctor['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($doctor['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Tags</label>
                                    <input type="text" name="tags" id="tagsInput" class="form-control rounded-3"
                                           placeholder="e.g. Orthopedics, Health Tips"
                                           value="<?= $p('tags') ?>">
                                    <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Comma-separated tags.</small>
                                    <div id="tagPreview" class="keyword-tags mt-2"></div>
                                </div>
                            </div>
                        </div>

                    </div></div></form>

        </div>
    </div>
</div>

<?php
$extraJS = '
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
/* ═══════════════════════════════════════════════════════════════
   QUILL EDITOR
══════════════════════════════════════════════════════════════════ */
var quill = new Quill("#quillEditor", {
    theme: "snow",
    placeholder: "Write your blog content here. Use headings, lists, and keywords naturally...",
    modules: {
        toolbar: [
            [{ header: [2,3,4,false] }],
            ["bold","italic","underline","strike"],
            [{ color:[] },{ background:[] }],
            [{ list:"ordered" },{ list:"bullet" }],
            [{ align:[] }],
            ["link","image","blockquote"],
            ["clean"]
        ]
    }
});

' . (!empty($blog['content']) ? 'quill.clipboard.dangerouslyPasteHTML(' . json_encode(html_entity_decode($blog['content'])) . ');' : '') . '

quill.on("text-change", function() {
    var html = quill.root.innerHTML;
    document.getElementById("blogContent").value = html;
    updateReadingTime(quill.getText());
    updateSeoScore();
    updateKdAnalysis();
    document.getElementById("readingTimeDisplay").style.display = "inline-flex";
});

document.getElementById("blogForm").addEventListener("submit", function(e) {
    var content = quill.root.innerHTML;
    document.getElementById("blogContent").value = content;
    
    var textContent = quill.getText().trim();
    if (!textContent || textContent === "" || content === "<p><br></p>") {
        e.preventDefault();
        alert("Content is required! Please write something.");
        quill.focus();
        return false;
    }
    return true;
});




/* ═══════════════════════════════════════════════════════════════
   SLUG & CANONICAL
══════════════════════════════════════════════════════════════════ */
function toSlug(str) {
    return str.toLowerCase().trim().replace(/[^a-z0-9\s-]/g,"").replace(/\s+/g,"-").replace(/-+/g,"-");
}

document.getElementById("blogTitle").addEventListener("input", function() {
    if (!document.getElementById("blogSlug").dataset.manual) {
        document.getElementById("blogSlug").value = toSlug(this.value);
    }
    updateCharCount("blogTitle","titleCount",null,null,999);
    autoFillSeoFields();
    updateSeoScore();
    updateSerpPreview();
});

function autoFillCanonical(slug) {
    var canon = document.getElementById("canonicalUrl");
    if (canon && canon.value === "") {
        // FIX: Using PHP SITE_URL constant to form correct base canonical URL
        canon.value = "<?= SITE_URL ?>/blog/" + slug;
    }
}

document.getElementById("blogSlug").addEventListener("input", function() {
    this.dataset.manual = "true";
    this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g,"-");
    updateSerpPreview();
    autoFillCanonical(this.value);
});

document.getElementById("generateSlug").addEventListener("click", function() {
    var s = document.getElementById("blogSlug");
    s.value = toSlug(document.getElementById("blogTitle").value);
    delete s.dataset.manual;
    updateSerpPreview();
    autoFillCanonical(s.value);
});

/* ═══════════════════════════════════════════════════════════════
   CHAR COUNTERS & SERP
══════════════════════════════════════════════════════════════════ */
function updateCharCount(fieldId, countId, barId, max, warnAt) {
    var el = document.getElementById(fieldId);
    if (!el) return;
    var len = el.value.length;
    var countEl = document.getElementById(countId);
    if (countEl) {
        countEl.textContent = max ? len + " / " + max : len + " chars";
        countEl.className = "count";
        if (max) {
            if (len > max)        countEl.classList.add("bad");
            else if (len > warnAt) countEl.classList.add("warn");
            else if (len >= 10)    countEl.classList.add("ok");
        }
    }
    if (barId && max) {
        var pct = Math.min(len / max * 100, 100);
        var bar = document.getElementById(barId);
        bar.style.width  = pct + "%";
        bar.style.background = len > max ? "var(--danger)" : len > warnAt ? "var(--warning)" : "var(--success)";
    }
}

["metaTitle", "metaDesc", "blogExcerpt", "ogTitle", "ogDesc"].forEach(function(id) {
    var el = document.getElementById(id);
    if(el) {
        el.addEventListener("input", function() {
            if(id === "metaTitle") { updateCharCount("metaTitle","metaTitleCount","metaTitleBar",60,50); this.dataset.manual = "1"; }
            if(id === "metaDesc")  updateCharCount("metaDesc","metaDescCount","metaDescBar",160,120);
            if(id === "blogExcerpt") updateCharCount("blogExcerpt","excerptCount",null,null,999);
            
            if(id === "ogTitle") document.getElementById("ogPreviewTitle").textContent = this.value || "OG Title will appear here";
            if(id === "ogDesc")  document.getElementById("ogPreviewDesc").textContent = this.value || "OG description will appear here";
            
            updateSerpPreview();
            updateSeoScore();
        });
    }
});

function updateSerpPreview() {
    var title = document.getElementById("metaTitle").value || document.getElementById("blogTitle").value;
    var desc  = document.getElementById("metaDesc").value  || document.getElementById("blogExcerpt").value;
    var slug  = document.getElementById("blogSlug").value  || "your-post-slug";

    var titleEl = document.getElementById("serpTitle");
    var descEl  = document.getElementById("serpDesc");

    titleEl.innerHTML = title ? truncate(title, 60) : \'<span class="serp-placeholder">Your meta title will appear here...</span>\';
    descEl.innerHTML  = desc ? truncate(desc, 160) : \'<span class="serp-placeholder">Your meta description will appear here.</span>\';
    document.getElementById("serpSlug").textContent = slug;
}

function truncate(str, max) { return str.length > max ? str.substring(0, max) + "…" : str; }

/* ═══════════════════════════════════════════════════════════════
   SEO ENGINE & HELPERS
══════════════════════════════════════════════════════════════════ */
function updateReadingTime(text) {
    var words = text.trim().split(/\s+/).filter(Boolean).length;
    var mins  = Math.max(1, Math.ceil(words / 220));
    document.getElementById("readingTimeText").textContent = "~" + mins + " min read (" + words + " words)";
    document.getElementById("readingTimeInput").value = mins;
}

function autoFillSeoFields() {
    var title = document.getElementById("blogTitle").value;
    var mt = document.getElementById("metaTitle");
    if (!mt.dataset.manual && title) {
        mt.value = title.substring(0,60);
        updateCharCount("metaTitle","metaTitleCount","metaTitleBar",60,50);
    }
}

document.getElementById("focusKeyword").addEventListener("input", function() {
    updateSeoScore();
    updateKdAnalysis();
    generateKeywordSuggestions(this.value);
});

function updateSeoScore() {
    var title     = document.getElementById("blogTitle").value;
    var slug      = document.getElementById("blogSlug").value;
    var metaT     = document.getElementById("metaTitle").value;
    var metaD     = document.getElementById("metaDesc").value;
    var keyword   = document.getElementById("focusKeyword").value.trim().toLowerCase();
    var content   = quill.getText();
    
    var checks = [];

    if (title.length >= 30 && title.length <= 70) checks.push({ok:true,  msg:"Title length is good (" + title.length + " chars)"});
    else if (title.length > 0) checks.push({ok:false, msg:"Title: aim for 30–70 chars"});
    else checks.push({ok:false, msg:"Title is missing"});

    if (keyword && title.toLowerCase().includes(keyword)) checks.push({ok:true,  msg:"Focus keyword in title"});
    else if (keyword) checks.push({ok:false, msg:"Add focus keyword to title"});
    else checks.push({warn:true, msg:"No focus keyword set"});

    if (metaT.length >= 50 && metaT.length <= 60) checks.push({ok:true,  msg:"Meta title length perfect"});
    else if (metaT.length > 0) checks.push({warn:true, msg:"Meta title: aim for 50–60 chars"});
    else checks.push({ok:false, msg:"Meta title missing"});

    if (metaD.length >= 120 && metaD.length <= 160) checks.push({ok:true,  msg:"Meta description length perfect"});
    else if (metaD.length > 0) checks.push({warn:true, msg:"Meta desc: aim for 120–160 chars"});
    else checks.push({ok:false, msg:"Meta description missing"});

    var wc = content.trim().split(/\s+/).filter(Boolean).length;
    if (wc >= 600) checks.push({ok:true,  msg:"Content length (" + wc + " words) is good"});
    else if (wc >= 200) checks.push({warn:true, msg:"Content length: aim for 600+ words"});
    else checks.push({ok:false, msg:"Content too short"});

    var score = 0;
    checks.forEach(function(c) { if (c.ok) score += 20; else if (c.warn) score += 10; });
    score = Math.min(100, score);

    var html = "";
    checks.slice(0,5).forEach(function(c) {
        var cls = c.ok ? "dot-ok" : (c.warn ? "dot-warn" : "dot-bad");
        html += "<div class=\"seo-check-item\"><div class=\"dot "+cls+"\"></div><span>"+c.msg+"</span></div>";
    });
    document.getElementById("seoChecklist").innerHTML = html;

    var circ = 213.6;
    var offset = circ - (score/100 * circ);
    var ring = document.getElementById("scoreCircle");
    ring.style.strokeDashoffset = offset;
    
    var color = score >= 80 ? "var(--success)" : score >= 50 ? "var(--warning)" : "var(--danger)";
    ring.style.stroke = color;

    var numEl = document.getElementById("scoreNum");
    numEl.textContent = score;
    numEl.style.color = color;
    
    var verdict = score >= 80 ? "Excellent. Ready to rank!" : score >= 50 ? "Good, but needs tweaks." : "Requires improvements.";
    document.getElementById("scoreVerdict").textContent = verdict;
    
    var badge = document.getElementById("seoScoreBadge");
    badge.textContent = "Score: " + score + "/100";
    badge.className = "badge border rounded-pill px-3 py-1 fw-bold " + (score >= 80 ? "bg-success-subtle text-success border-success-subtle" : score >= 50 ? "bg-warning-subtle text-warning-emphasis border-warning-subtle" : "bg-danger-subtle text-danger border-danger-subtle");
}

function updateKdAnalysis() {
    var keyword = document.getElementById("focusKeyword").value.trim().toLowerCase();
    var content = quill.getText().toLowerCase();
    var kdEl    = document.getElementById("kdResults");

    if (!keyword || content.trim().length < 10) {
        kdEl.innerHTML = "Enter a focus keyword in Meta Tags and write content to see analysis.";
        return;
    }
    var words  = content.trim().split(/\s+/).filter(Boolean);
    var re     = new RegExp(keyword,"gi");
    var kCount = (content.match(re) || []).length;
    var dens   = words.length > 0 ? (kCount / words.length * 100).toFixed(2) : 0;
    var color  = dens >= 0.5 && dens <= 2.5 ? "var(--success)" : dens > 2.5 ? "var(--danger)" : "var(--warning)";

    kdEl.innerHTML =
        "<div class=\"d-flex justify-content-between mb-1\">" +
        "<span class=\"fw-bold text-dark\">" + keyword + "</span>" +
        "<span class=\"fw-bold\" style=\"color:" + color + "\">" + dens + "%</span></div>" +
        "<div class=\"kd-bar\"><div class=\"kd-fill\" style=\"width:" + Math.min(dens/3*100,100) + "%;background:" + color + "\"></div></div>" +
        "<div class=\"d-flex justify-content-between mt-1\" style=\"font-size:0.75rem;\">" +
        "<span>Found " + kCount + " times</span><span>Ideal: 0.5%–2.5%</span></div>";
}

function generateKeywordSuggestions(kw) {
    if (!kw || kw.length < 3) { document.getElementById("keywordSuggestions").innerHTML = ""; return; }
    var suggestions = ["best " + kw, kw + " guide", kw + " benefits"];
    var html = suggestions.map(function(s) {
        return "<span class=\"keyword-tag shadow-sm\" onclick=\"document.getElementById(\'focusKeyword\').value=\'" + s.replace(/\'/g,"\\\'") + "\';updateSeoScore();updateKdAnalysis();\">" + s + "</span>";
    }).join("");
    document.getElementById("keywordSuggestions").innerHTML = html;
}

/* ═══════════════════════════════════════════════════════════════
   TOGGLES & UPLOADS
══════════════════════════════════════════════════════════════════ */
function setRobots(val, btn) {
    document.querySelectorAll("#robotsIndexGroup .robots-btn").forEach(function(b) { b.classList.remove("active-index", "active-noindex"); });
    btn.classList.add(val === "index" ? "active-index" : "active-noindex");
    document.getElementById("robotsIndex").value = val;
    updateRobotsHint();
}
function setFollow(val, btn) {
    document.querySelectorAll("#robotsFollowGroup .robots-btn").forEach(function(b) { b.classList.remove("active-follow", "active-nofollow"); });
    btn.classList.add(val === "follow" ? "active-follow" : "active-nofollow");
    document.getElementById("robotsFollow").value = val;
    updateRobotsHint();
}
function updateRobotsHint() {
    var idx = document.getElementById("robotsIndex").value;
    var hint = document.getElementById("robotsHint");
    hint.textContent = idx === "index" ? "✅ This page will be indexed and links followed." : "🚫 This page will NOT be indexed.";
    hint.className = "fw-medium mt-2 d-block " + (idx === "index" ? "text-success" : "text-danger");
}

function setSchema(val, btn) {
    document.querySelectorAll(".schema-opt").forEach(function(b) { b.classList.remove("active"); });
    btn.classList.add("active");
    document.getElementById("schemaType").value = val;
}

// Generate initial tags view
function renderTags() {
    var input = document.getElementById("tagsInput");
    if(!input) return;
    var tags = input.value.split(",").map(function(t) { return t.trim(); }).filter(Boolean);
    document.getElementById("tagPreview").innerHTML = tags.map(function(t) { return "<span class=\"keyword-tag shadow-sm\">" + t + "</span>"; }).join("");
}
document.getElementById("tagsInput").addEventListener("input", renderTags);

function updatePublishBadge(cb) {
    var badge = document.getElementById("publishStatusBadge");
    if (cb.checked) {
        badge.textContent = "Live";
        badge.className = "badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 fw-bold";
    } else {
        badge.textContent = "Draft";
        badge.className = "badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold";
    }
}

// Images
["imageInput", "ogImageInput"].forEach(function(id) {
    var input = document.getElementById(id);
    if(input) {
        input.addEventListener("change", function() {
            if (this.files[0]) {
                var r = new FileReader();
                r.onload = function(e) {
                    if(id === "imageInput") {
                        document.getElementById("imagePreview").src = e.target.result;
                        document.getElementById("imagePreview").style.display = "block";
                        document.getElementById("imgPlaceholder").style.display = "none";
                        document.getElementById("ogPreviewImgBox").innerHTML = "<img src=\"" + e.target.result + "\" style=\"width:100%;height:100%;object-fit:cover;\">";
                    } else {
                        document.getElementById("ogImagePreview").src = e.target.result;
                        document.getElementById("ogImagePreview").style.display = "block";
                        document.getElementById("ogImgPlaceholder").style.display = "none";
                        document.getElementById("ogPreviewImgBox").innerHTML = "<img src=\"" + e.target.result + "\" style=\"width:100%;height:100%;object-fit:cover;\">";
                    }
                };
                r.readAsDataURL(this.files[0]);
            }
        });
    }
});

// Setup Init values safely
document.getElementById("blogSlug").dataset.manual = "1"; // Since this is an edit page, prevent auto-slug generation by default

// Trigger updates to render UI matching the existing content
setTimeout(function(){
    updateCharCount("blogTitle","titleCount",null,null,999);
    updateCharCount("metaTitle","metaTitleCount","metaTitleBar",60,50);
    updateCharCount("metaDesc","metaDescCount","metaDescBar",160,120);
    updateCharCount("blogExcerpt","excerptCount",null,null,999);
    
    document.getElementById("ogPreviewTitle").textContent = document.getElementById("ogTitle").value || "OG Title will appear here";
    document.getElementById("ogPreviewDesc").textContent = document.getElementById("ogDesc").value || "OG description will appear here";
    
    renderTags();
    updateSeoScore();
    updateSerpPreview();
    updateKdAnalysis();
    updateRobotsHint();
    if(document.getElementById("isPublished").checked) updatePublishBadge(document.getElementById("isPublished"));
}, 100);

</script>
';

require_once '../include/footer.php';
?>