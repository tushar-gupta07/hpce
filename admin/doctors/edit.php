<?php
// admin/doctors/edit.php
require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('doctors');

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM doctors WHERE id = $id");
$doctor = $res ? $res->fetch_assoc() : null;
if (!$doctor) { header("Location: ./"); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Core Fields ──────────────────────────────────────────────
    $name              = trim($_POST['name'] ?? '');
    $slug              = trim($_POST['slug'] ?? '');
    $designation       = trim($_POST['designation'] ?? '');
    $specialty         = trim($_POST['specialty'] ?? '');
    $bio               = $_POST['bio'] ?? '';
    $excerpt           = trim($_POST['excerpt'] ?? '');
    $satisfaction_rate = isset($_POST['satisfaction_rate']) ? (int)$_POST['satisfaction_rate'] : 100;
    $feedback_count    = isset($_POST['feedback_count']) ? (int)$_POST['feedback_count'] : 0;
    $location          = trim($_POST['location'] ?? '');
    $consultation_fee  = trim($_POST['consultation_fee'] ?? '');
    $specializations   = trim($_POST['specializations'] ?? '');
    $map_iframe        = $_POST['map_iframe'] ?? '';
    $tags              = trim($_POST['tags'] ?? '');
    $is_published      = isset($_POST['is_published']) ? 1 : 0;
    $published_at      = !empty($_POST['published_at']) ? trim($_POST['published_at']) : date('Y-m-d');

    // ── SEO Fields ───────────────────────────────────────────────
    $meta_title          = trim($_POST['meta_title'] ?? '');
    $meta_description    = trim($_POST['meta_description'] ?? '');
    $focus_keyword       = trim($_POST['focus_keyword'] ?? '');
    $canonical_url       = trim($_POST['canonical_url'] ?? '');
    $og_title            = trim($_POST['og_title'] ?? '');
    $og_description      = trim($_POST['og_description'] ?? '');
    $og_type             = trim($_POST['og_type'] ?? 'profile');
    $twitter_title       = trim($_POST['twitter_title'] ?? '');
    $twitter_description = trim($_POST['twitter_description'] ?? '');
    $twitter_card        = trim($_POST['twitter_card'] ?? 'summary_large_image');
    $robots_index        = trim($_POST['robots_index'] ?? 'index');
    $robots_follow       = trim($_POST['robots_follow'] ?? 'follow');
    $schema_type         = trim($_POST['schema_type'] ?? 'Physician');

    // ── JSON Fields ──────────────────────────────────────────────
    $education = [];
    if (!empty($_POST['edu_title'])) {
        foreach ($_POST['edu_title'] as $i => $val) {
            if (!empty($val)) {
                $education[] = [
                    'title'  => trim($val),
                    'degree' => trim($_POST['edu_degree'][$i] ?? ''),
                    'year'   => trim($_POST['edu_year'][$i] ?? '')
                ];
            }
        }
    }
    $experience = [];
    if (!empty($_POST['exp_title'])) {
        foreach ($_POST['exp_title'] as $i => $val) {
            if (!empty($val)) {
                $experience[] = [
                    'title' => trim($val),
                    'year'  => trim($_POST['exp_year'][$i] ?? '')
                ];
            }
        }
    }
    $awards = [];
    if (!empty($_POST['awd_title'])) {
        foreach ($_POST['awd_title'] as $i => $val) {
            if (!empty($val)) {
                $awards[] = [
                    'title' => trim($val),
                    'desc'  => trim($_POST['awd_desc'][$i] ?? ''),
                    'year'  => trim($_POST['awd_year'][$i] ?? '')
                ];
            }
        }
    }
    $edu_json = json_encode($education);
    $exp_json = json_encode($experience);
    $awd_json = json_encode($awards);

    // ── Validation ───────────────────────────────────────────────
    if (empty($name))      $errors[] = 'Doctor name is required.';
    if (empty($specialty)) $errors[] = 'Main specialty is required.';
    if (!empty($meta_title) && mb_strlen($meta_title) > 60)
        $errors[] = 'Meta title should not exceed 60 characters.';
    if (!empty($meta_description) && mb_strlen($meta_description) > 160)
        $errors[] = 'Meta description should not exceed 160 characters.';

    // ── Slug Generation ──────────────────────────────────────────
    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    } else {
        $slug = strtolower(preg_replace('/[^a-z0-9-]+/', '-', $slug));
    }
    $slug    = trim($slug, '-');
    $slugEsc = $conn->real_escape_string($slug);

    // Allow same slug as current record
    $chk = $conn->query("SELECT id FROM doctors WHERE slug = '$slugEsc' AND id != $id");
    if ($chk && $chk->num_rows > 0) {
        $errors[] = 'Slug already exists. Please use a different one.';
    }

    // ── SEO Auto-fill Defaults ───────────────────────────────────
    if (empty($meta_title))          $meta_title          = $name;
    if (empty($meta_description))    $meta_description    = $excerpt;
    if (empty($og_title))            $og_title            = $meta_title;
    if (empty($og_description))      $og_description      = $meta_description;
    if (empty($twitter_title))       $twitter_title       = $meta_title;
    if (empty($twitter_description)) $twitter_description = $meta_description;

    // ── WebP Converter ───────────────────────────────────────────
    function convertToWebp($source, $destination, $quality = 80) {
        $info = @getimagesize($source);
        if (!$info) return false;
        switch ($info['mime']) {
            case 'image/jpeg': $img = imagecreatefromjpeg($source); break;
            case 'image/png':
                $img = imagecreatefrompng($source);
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
                break;
            case 'image/gif':  $img = imagecreatefromgif($source);  break;
            case 'image/webp': $img = imagecreatefromwebp($source); break;
            default: return false;
        }
        $success = imagewebp($img, $destination, $quality);
        imagedestroy($img);
        return $success;
    }

    // ── Photo Upload (keep existing if no new upload) ─────────────
    $photo   = $doctor['photo'];
    $ogImage = $doctor['og_image'] ?? '';
    $seoBase = !empty($slug) ? $slug : 'doctor';

    if (!empty($_FILES['photo']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fileType     = mime_content_type($_FILES['photo']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Invalid photo type. Allowed: JPG, PNG, WEBP, GIF.';
        } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Photo size must be under 2MB.';
        } else {
            $uploadDir = '../../assets/img/doctors/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $fileName   = $seoBase . '-' . uniqid() . '.webp';
            $targetPath = $uploadDir . $fileName;

            if (convertToWebp($_FILES['photo']['tmp_name'], $targetPath, 85)) {
                // Delete old photo if it's not default
                if (!empty($doctor['photo']) && $doctor['photo'] !== 'default.jpg') {
                    $oldPath = '../../' . ltrim($doctor['photo'], '/');
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $photo   = 'assets/img/doctors/' . $fileName;
                $ogImage = $photo;
            } else {
                $errors[] = 'Failed to convert photo to WebP.';
            }
        }
    }

    // ── Schema JSON ──────────────────────────────────────────────
    $schema_json = '';
    if (!empty($schema_type)) {
        $schemaData = [
            '@context'         => 'https://schema.org',
            '@type'            => $schema_type,
            'name'             => $name,
            'description'      => $meta_description ?: $excerpt,
            'image'            => $ogImage,
            'url'              => $canonical_url ?: '',
            'medicalSpecialty' => $specialty,
        ];
        $schema_json = json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    // ── Update ───────────────────────────────────────────────────
    if (empty($errors)) {
        $robots_meta = $robots_index . ',' . $robots_follow;

        $stmt = $conn->prepare("
            UPDATE doctors SET
                name=?, slug=?, designation=?, specialty=?,
                satisfaction_rate=?, feedback_count=?, location=?, consultation_fee=?,
                bio=?, excerpt=?, education_json=?, experience_json=?, awards_json=?,
                specializations=?, map_iframe=?, photo=?, tags=?,
                is_published=?, published_at=?,
                meta_title=?, meta_description=?, focus_keyword=?, canonical_url=?,
                og_title=?, og_description=?, og_image=?, og_type=?,
                twitter_title=?, twitter_description=?, twitter_card=?,
                robots_meta=?, schema_type=?, schema_json=?,
                updated_at=NOW()
            WHERE id=?
        ");

        $stmt->bind_param(
            "ssssiisssssssssssisssssssssssssssi",
            $name, $slug, $designation, $specialty,
            $satisfaction_rate, $feedback_count, $location, $consultation_fee,
            $bio, $excerpt, $edu_json, $exp_json, $awd_json,
            $specializations, $map_iframe, $photo, $tags,
            $is_published, $published_at,
            $meta_title, $meta_description, $focus_keyword, $canonical_url,
            $og_title, $og_description, $ogImage, $og_type,
            $twitter_title, $twitter_description, $twitter_card,
            $robots_meta, $schema_type, $schema_json,
            $id
        );

        if ($stmt->execute()) {
            header("Location: ./?msg=updated");
            exit;
        } else {
            $errors[] = 'Database error: ' . $stmt->error;
        }
    }
} else {
    // ── Pre-fill from DB on GET ───────────────────────────────────
    $_POST = $doctor; // allows $p() helper to read DB values
}

// ── Helpers ───────────────────────────────────────────────────
$p = fn($k) => htmlspecialchars($_POST[$k] ?? $doctor[$k] ?? '');

$c_edu = !empty($doctor['education_json'])  ? json_decode($doctor['education_json'], true)  : [];
$c_exp = !empty($doctor['experience_json']) ? json_decode($doctor['experience_json'], true) : [];
$c_awd = !empty($doctor['awards_json'])     ? json_decode($doctor['awards_json'], true)     : [];

// Current robots
$robotsParts  = explode(',', $doctor['robots_meta'] ?? 'index,follow');
$currentIndex = trim($robotsParts[0] ?? 'index');
$currentFollow= trim($robotsParts[1] ?? 'follow');

$pageTitle  = 'Edit Doctor — ' . htmlspecialchars($doctor['name']);
$activePage = 'doctors-edit';
$assetBase  = '../';

$extraCSS = '
<style>
    :root {
        --success: #198754;
        --warning: #ffc107;
        --danger:  #dc3545;
    }
    .form-label { font-size: 0.75rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
    .form-control, .form-select { font-size: 0.9rem; padding: 0.6rem 1rem; border-color: #dee2e6; }
    .form-control:focus, .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.25); }

    .char-counter { display: flex; justify-content: space-between; margin-top: 6px; font-size: 0.7rem; color: #adb5bd; font-family: monospace; }
    .char-counter .count { font-weight: 700; }
    .char-counter .count.ok   { color: var(--success); }
    .char-counter .count.warn { color: var(--warning); }
    .char-counter .count.bad  { color: var(--danger); }
    .char-bar { height: 4px; background: #e9ecef; border-radius: 4px; margin-top: 6px; overflow: hidden; }
    .char-bar-fill { height: 100%; border-radius: 4px; transition: width .3s, background .3s; }

    .img-upload-zone { border: 2px dashed #dee2e6; border-radius: 0.75rem; padding: 2rem 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s; background: #f8f9fa; }
    .img-upload-zone:hover { border-color: #0d6efd; background: #f1f7ff; }
    .img-upload-zone p { font-size: 0.85rem; color: #6c757d; margin: 0; font-weight: 500; }
    .img-upload-zone .preview-img { width: 100%; border-radius: 0.5rem; object-fit: cover; margin-top: 10px; max-height: 220px; }

    .serp-preview { background: #fff; border: 1px solid #dfe1e5; border-radius: 0.5rem; padding: 1rem; margin-top: 0.25rem; }
    .serp-url   { font-size: 0.75rem; color: #202124; font-family: Arial, sans-serif; margin-bottom: 2px; }
    .serp-title { font-size: 1.125rem; color: #1a0dab; font-family: Arial, sans-serif; line-height: 1.3; margin-bottom: 2px; }
    .serp-desc  { font-size: 0.85rem; color: #4d5156; font-family: Arial, sans-serif; line-height: 1.5; }
    .serp-placeholder { color: #9aa0a6 !important; font-style: italic; }

    .nav-pills .nav-link { color: #6c757d; border-radius: 20px; font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; transition: all 0.2s; }
    .nav-pills .nav-link.active { background-color: #e7f1ff; color: #0d6efd; }

    .schema-options, .robots-group { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
    .schema-opt, .robots-btn { padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; cursor: pointer; border: 1px solid #dee2e6; color: #6c757d; background: #fff; transition: all 0.15s; flex: 1; text-align: center; }
    .schema-opt.active, .robots-btn.active-index, .robots-btn.active-follow { border-color: var(--success); color: var(--success); background: rgba(25,135,84,0.05); }
    .robots-btn.active-noindex, .robots-btn.active-nofollow { border-color: var(--danger); color: var(--danger); background: rgba(220,53,69,0.05); }

    .dynamic-row { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.5rem; }
    .current-photo { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #e9ecef; }
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
                    <h3 class="fw-bolder text-dark mb-1">Edit Doctor Profile</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="./" class="text-muted text-decoration-none">Doctors</a></li>
                            <li class="breadcrumb-item active text-secondary fw-medium"><?= htmlspecialchars($doctor['name']) ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <a href="<?= SITE_URL ?>/doctors/<?= htmlspecialchars($doctor['slug'] ?? '') ?>" target="_blank"
                        class="btn btn-light rounded-pill px-4 py-2 shadow-sm fw-semibold border d-inline-flex align-items-center gap-2">
                        <i class="fa fa-external-link-alt"></i> View Profile
                    </a>
                    <a href="./" class="btn btn-light rounded-pill px-4 py-2 shadow-sm fw-semibold border d-inline-flex align-items-center gap-2">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <!-- Errors -->
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

            <form method="POST" enctype="multipart/form-data" id="doctorForm">
                <div class="row g-4">

                    <!-- ── LEFT COLUMN ───────────────────────────────── -->
                    <div class="col-xl-8 col-lg-7">

                        <!-- Core Info Card -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="fa fa-user-md"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:0.5px;">Doctor Information</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="doctorName" class="form-control"
                                               value="<?= $p('name') ?>" required>
                                        <div class="char-counter"><span>Name length</span><span class="count" id="nameCount"><?= mb_strlen($doctor['name']) ?> chars</span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">URL Slug</label>
                                        <div class="input-group">
                                            <input type="text" name="slug" id="doctorSlug" class="form-control"
                                                   value="<?= $p('slug') ?>">
                                            <button type="button" class="btn btn-light border text-secondary" id="generateSlug" title="Re-generate from name">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted" style="font-size:0.75rem;">Changing slug will break existing links to this profile.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Designation / Degrees</label>
                                        <input type="text" name="designation" class="form-control"
                                               value="<?= $p('designation') ?>" placeholder="MBBS, MD, BDS...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Main Specialty <span class="text-danger">*</span></label>
                                        <input type="text" name="specialty" class="form-control"
                                               value="<?= $p('specialty') ?>" placeholder="Dentist, Cardiologist...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control"
                                               value="<?= $p('location') ?>" placeholder="Nagpur, India">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Consultation Fee</label>
                                        <input type="text" name="consultation_fee" class="form-control"
                                               value="<?= $p('consultation_fee') ?>" placeholder="₹500 / visit">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Satisfaction Rate (%)</label>
                                        <input type="number" name="satisfaction_rate" class="form-control"
                                               value="<?= $p('satisfaction_rate') ?>" min="0" max="100">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Feedback Count</label>
                                        <input type="number" name="feedback_count" class="form-control"
                                               value="<?= $p('feedback_count') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Published Date</label>
                                        <input type="date" name="published_at" class="form-control"
                                               value="<?= $p('published_at') ?: date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Specializations <small class="text-muted fw-normal text-lowercase">(comma separated)</small></label>
                                        <input type="text" name="specializations" class="form-control"
                                               value="<?= $p('specializations') ?>"
                                               placeholder="Children Care, Periodontology, Implants...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Tags <small class="text-muted fw-normal text-lowercase">(comma separated)</small></label>
                                        <input type="text" name="tags" class="form-control"
                                               value="<?= $p('tags') ?>"
                                               placeholder="dentist, nagpur, root canal...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Short Excerpt</label>
                                        <textarea name="excerpt" id="doctorExcerpt" class="form-control" rows="2"
                                                  placeholder="One-line profile summary shown in doctor listings..."><?= $p('excerpt') ?></textarea>
                                        <div class="char-counter"><span>Excerpt length</span><span class="count" id="excerptCount"><?= mb_strlen($doctor['excerpt'] ?? '') ?> chars</span></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Full Biography</label>
                                        <textarea name="bio" class="form-control" rows="5"
                                                  placeholder="Detailed bio about the doctor..."><?= $p('bio') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education / Experience / Awards -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:0.5px;">Education, Experience & Awards</h6>
                            </div>
                            <div class="card-body p-4">

                                <!-- Education -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold text-dark small text-uppercase" style="letter-spacing:0.5px;">
                                        <i class="fa fa-university me-2 text-primary"></i>Education History
                                    </span>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="addEdu()">
                                        <i class="fa fa-plus me-1"></i> Add
                                    </button>
                                </div>
                                <div id="edu_wrapper" class="mb-4">
                                    <?php foreach ($c_edu as $e): ?>
                                    <div class="dynamic-row d-flex gap-2 align-items-center">
                                        <input type="text" name="edu_title[]"  class="form-control" value="<?= htmlspecialchars($e['title'] ?? '') ?>" placeholder="University Name">
                                        <input type="text" name="edu_degree[]" class="form-control" value="<?= htmlspecialchars($e['degree'] ?? '') ?>" placeholder="Degree (BDS)">
                                        <input type="text" name="edu_year[]"   class="form-control" value="<?= htmlspecialchars($e['year'] ?? '') ?>"   placeholder="1998–2003" style="max-width:140px;">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-shrink-0" onclick="this.closest('.dynamic-row').remove()"><i class="fa fa-times"></i></button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Experience -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold text-dark small text-uppercase" style="letter-spacing:0.5px;">
                                        <i class="fa fa-briefcase me-2 text-success"></i>Work Experience
                                    </span>
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3" onclick="addExp()">
                                        <i class="fa fa-plus me-1"></i> Add
                                    </button>
                                </div>
                                <div id="exp_wrapper" class="mb-4">
                                    <?php foreach ($c_exp as $e): ?>
                                    <div class="dynamic-row d-flex gap-2 align-items-center">
                                        <input type="text" name="exp_title[]" class="form-control" value="<?= htmlspecialchars($e['title'] ?? '') ?>" placeholder="Clinic / Hospital Name">
                                        <input type="text" name="exp_year[]"  class="form-control" value="<?= htmlspecialchars($e['year'] ?? '') ?>"  placeholder="2010 – Present" style="max-width:160px;">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-shrink-0" onclick="this.closest('.dynamic-row').remove()"><i class="fa fa-times"></i></button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Awards -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold text-dark small text-uppercase" style="letter-spacing:0.5px;">
                                        <i class="fa fa-trophy me-2 text-warning"></i>Awards & Recognition
                                    </span>
                                    <button type="button" class="btn btn-sm btn-warning rounded-pill px-3" onclick="addAwd()">
                                        <i class="fa fa-plus me-1"></i> Add
                                    </button>
                                </div>
                                <div id="awd_wrapper">
                                    <?php foreach ($c_awd as $e): ?>
                                    <div class="dynamic-row d-flex gap-2 align-items-center">
                                        <input type="text" name="awd_year[]"  class="form-control" value="<?= htmlspecialchars($e['year'] ?? '') ?>"  placeholder="Year" style="max-width:110px;">
                                        <input type="text" name="awd_title[]" class="form-control" value="<?= htmlspecialchars($e['title'] ?? '') ?>" placeholder="Award Title">
                                        <input type="text" name="awd_desc[]"  class="form-control" value="<?= htmlspecialchars($e['desc'] ?? '') ?>"  placeholder="Short Description">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-shrink-0" onclick="this.closest('.dynamic-row').remove()"><i class="fa fa-times"></i></button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>

                        <!-- Map Card -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-info-subtle text-info rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="fa fa-map-marker"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:0.5px;">Location Map</h6>
                            </div>
                            <div class="card-body p-4">
                                <label class="form-label">Google Maps Iframe</label>
                                <textarea name="map_iframe" class="form-control" rows="3"
                                          placeholder="Paste Google Maps embed iframe HTML here..."><?= htmlspecialchars($doctor['map_iframe'] ?? '') ?></textarea>
                                <small class="text-muted" style="font-size:0.75rem;">Go to Google Maps → Share → Embed a map → Copy HTML</small>
                            </div>
                        </div>

                        <!-- SEO Card -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success-subtle text-success rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:0.5px;">SEO Settings</h6>
                                </div>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold" id="seoScoreBadge">Score: 0/100</span>
                            </div>
                            <div class="card-body p-4">

                                <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" role="tablist">
                                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-meta"      type="button">Meta Tags</button></li>
                                    <li class="nav-item"><button class="nav-link"        data-bs-toggle="pill" data-bs-target="#tab-og"        type="button">Open Graph</button></li>
                                    <li class="nav-item"><button class="nav-link"        data-bs-toggle="pill" data-bs-target="#tab-twitter"   type="button">Twitter Card</button></li>
                                    <li class="nav-item"><button class="nav-link"        data-bs-toggle="pill" data-bs-target="#tab-technical" type="button">Technical</button></li>
                                </ul>

                                <div class="tab-content">

                                    <!-- Meta Tab -->
                                    <div class="tab-pane fade show active" id="tab-meta">
                                        <div class="mb-4">
                                            <label class="form-label text-primary"><i class="fa fa-key me-1"></i> Focus Keyword</label>
                                            <input type="text" name="focus_keyword" id="focusKeyword" class="form-control bg-primary-subtle border-primary-subtle text-primary-emphasis fw-bold"
                                                   placeholder="E.g. dentist in nagpur..."
                                                   value="<?= $p('focus_keyword') ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Meta Title</label>
                                            <input type="text" name="meta_title" id="metaTitle" class="form-control"
                                                   placeholder="SEO title shown in Google results..."
                                                   value="<?= $p('meta_title') ?>" maxlength="70">
                                            <div class="char-bar"><div class="char-bar-fill" id="metaTitleBar" style="width:0%"></div></div>
                                            <div class="char-counter"><span>Ideal: 50–60 characters</span><span class="count" id="metaTitleCount">0 / 60</span></div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Meta Description</label>
                                            <textarea name="meta_description" id="metaDesc" class="form-control" rows="3"
                                                      placeholder="Compelling description in Google results (max 160 chars)..."
                                                      maxlength="180"><?= $p('meta_description') ?></textarea>
                                            <div class="char-bar"><div class="char-bar-fill" id="metaDescBar" style="width:0%"></div></div>
                                            <div class="char-counter"><span>Ideal: 120–160 characters</span><span class="count" id="metaDescCount">0 / 160</span></div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Canonical URL</label>
                                            <input type="text" name="canonical_url" id="canonicalUrl" class="form-control"
                                                   placeholder="https://hpce.com/doctors/..."
                                                   value="<?= $p('canonical_url') ?>">
                                        </div>
                                        <!-- SERP Preview -->
                                        <div class="mt-4">
                                            <label class="form-label"><i class="fab fa-google text-muted me-1"></i> Google SERP Preview</label>
                                            <div class="serp-preview">
                                                <div class="serp-url">hpce.com › doctors › <span id="serpSlug"><?= htmlspecialchars($doctor['slug'] ?? '') ?></span></div>
                                                <div class="serp-title" id="serpTitle">
                                                    <?php if (!empty($doctor['meta_title'])): ?>
                                                        <?= htmlspecialchars($doctor['meta_title']) ?>
                                                    <?php else: ?>
                                                        <span class="serp-placeholder">Your meta title will appear here...</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="serp-desc" id="serpDesc">
                                                    <?php if (!empty($doctor['meta_description'])): ?>
                                                        <?= htmlspecialchars($doctor['meta_description']) ?>
                                                    <?php else: ?>
                                                        <span class="serp-placeholder">Your meta description will appear here.</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- OG Tab -->
                                    <div class="tab-pane fade" id="tab-og">
                                        <div class="mb-4">
                                            <label class="form-label">OG Title</label>
                                            <input type="text" name="og_title" class="form-control"
                                                   placeholder="Title on Facebook / LinkedIn shares..."
                                                   value="<?= $p('og_title') ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Description</label>
                                            <textarea name="og_description" class="form-control" rows="2"
                                                      placeholder="Description on social shares..."><?= $p('og_description') ?></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Type</label>
                                            <select name="og_type" class="form-select">
                                                <option value="profile" <?= ($p('og_type') ?: 'profile') === 'profile' ? 'selected' : '' ?>>profile</option>
                                                <option value="website" <?= $p('og_type') === 'website' ? 'selected' : '' ?>>website</option>
                                                <option value="article" <?= $p('og_type') === 'article' ? 'selected' : '' ?>>article</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Twitter Tab -->
                                    <div class="tab-pane fade" id="tab-twitter">
                                        <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis rounded-3 p-3 mb-4 d-flex align-items-center gap-2">
                                            <i class="fa fa-lightbulb"></i>
                                            <small class="fw-medium">Leave blank to auto-inherit from Meta Title/Description on save.</small>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Title</label>
                                            <input type="text" name="twitter_title" class="form-control"
                                                   value="<?= $p('twitter_title') ?>"
                                                   placeholder="Title shown on Twitter card...">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Description</label>
                                            <textarea name="twitter_description" class="form-control" rows="3"
                                                      placeholder="Description on Twitter card..."><?= $p('twitter_description') ?></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Card Type</label>
                                            <select name="twitter_card" class="form-select">
                                                <option value="summary_large_image" <?= ($p('twitter_card') ?: 'summary_large_image') === 'summary_large_image' ? 'selected' : '' ?>>summary_large_image (Recommended)</option>
                                                <option value="summary" <?= $p('twitter_card') === 'summary' ? 'selected' : '' ?>>summary</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Technical Tab -->
                                    <div class="tab-pane fade" id="tab-technical">
                                        <div class="mb-4">
                                            <label class="form-label">Robots Meta Tag</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="robots-group" id="robotsIndexGroup">
                                                        <button type="button"
                                                            class="robots-btn shadow-sm <?= $currentIndex === 'index' ? 'active-index' : '' ?>"
                                                            data-val="index" onclick="setRobots('index',this)">✅ INDEX</button>
                                                        <button type="button"
                                                            class="robots-btn shadow-sm <?= $currentIndex === 'noindex' ? 'active-noindex' : '' ?>"
                                                            data-val="noindex" onclick="setRobots('noindex',this)">🚫 NOINDEX</button>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="robots-group" id="robotsFollowGroup">
                                                        <button type="button"
                                                            class="robots-btn shadow-sm <?= $currentFollow === 'follow' ? 'active-follow' : '' ?>"
                                                            data-val="follow" onclick="setFollow('follow',this)">🔗 FOLLOW</button>
                                                        <button type="button"
                                                            class="robots-btn shadow-sm <?= $currentFollow === 'nofollow' ? 'active-nofollow' : '' ?>"
                                                            data-val="nofollow" onclick="setFollow('nofollow',this)">⛔ NOFOLLOW</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="robots_index"  id="robotsIndex"  value="<?= $currentIndex ?>">
                                            <input type="hidden" name="robots_follow" id="robotsFollow" value="<?= $currentFollow ?>">
                                            <small class="fw-medium mt-2 d-block <?= $currentIndex === 'index' && $currentFollow === 'follow' ? 'text-success' : 'text-danger' ?>" id="robotsHint">
                                                <?= $currentIndex === 'index' && $currentFollow === 'follow'
                                                    ? '✅ This page will be indexed and links followed.'
                                                    : '⚠️ This page is restricted from indexing or link-following.' ?>
                                            </small>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Schema / Structured Data Type</label>
                                            <div class="schema-options">
                                                <?php $schemas = ['Physician', 'MedicalBusiness', 'Person', 'ProfilePage', 'LocalBusiness']; ?>
                                                <?php foreach ($schemas as $s): ?>
                                                <button type="button"
                                                    class="schema-opt shadow-sm <?= ($p('schema_type') ?: 'Physician') === $s ? 'active' : '' ?>"
                                                    onclick="setSchema('<?= $s ?>',this)"><?= $s ?></button>
                                                <?php endforeach; ?>
                                            </div>
                                            <input type="hidden" name="schema_type" id="schemaType" value="<?= $p('schema_type') ?: 'Physician' ?>">
                                            <small class="text-muted mt-2 d-block" style="font-size:0.75rem;">For doctors, <strong>Physician</strong> or <strong>MedicalBusiness</strong> gives the best rich-result coverage.</small>
                                        </div>
                                    </div>

                                </div><!-- /tab-content -->
                            </div>
                        </div>

                    </div><!-- /col-xl-8 -->

                    <!-- ── RIGHT SIDEBAR ─────────────────────────────── -->
                    <div class="col-xl-4 col-lg-5">

                        <!-- Publish Card -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-success-subtle text-success rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="fa fa-paper-plane"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:0.5px;">Publish</h6>
                            </div>
                            <div class="card-body p-4">
                                <!-- Last Updated Info -->
                                <div class="d-flex align-items-center gap-2 mb-3 p-3 bg-light rounded-3 small text-muted">
                                    <i class="fa fa-clock text-primary"></i>
                                    <span>Last updated: <strong class="text-dark">
                                        <?= !empty($doctor['updated_at']) ? date('M d, Y H:i', strtotime($doctor['updated_at'])) : 'Never' ?>
                                    </strong></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div>
                                        <div class="fw-semibold text-dark small">Publish Profile</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Make this profile visible on the website</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_published" id="isPublished"
                                               style="width:3rem;height:1.5rem;cursor:pointer;"
                                               <?= $doctor['is_published'] ? 'checked' : '' ?>>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                    <i class="fa fa-save me-2"></i> Update Profile
                                </button>
                                <a href="./" class="btn btn-light w-100 rounded-pill py-2 fw-semibold border mt-2">Cancel</a>
                            </div>
                        </div>

                        <!-- Photo Card -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="fa fa-camera"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing:0.5px;">Profile Photo</h6>
                            </div>
                            <div class="card-body p-4 text-center">
                                <!-- Current Photo -->
                                <?php
                                    $currentPhotoSrc = '';
                                    if (!empty($doctor['photo']) && $doctor['photo'] !== 'default.jpg') {
                                        $rawPhoto = $doctor['photo'];
                                        $currentPhotoSrc = (str_starts_with($rawPhoto, 'http') ? $rawPhoto : SITE_URL . '/' . ltrim($rawPhoto, '/'));
                                    }
                                ?>
                                <?php if ($currentPhotoSrc): ?>
                                <div class="mb-3">
                                    <p class="text-muted small mb-2 fw-medium">Current Photo</p>
                                    <img src="<?= htmlspecialchars($currentPhotoSrc) ?>"
                                         id="currentPhotoDisplay"
                                         class="current-photo shadow-sm mb-1"
                                         style="width:90px;height:90px;"
                                         alt="<?= htmlspecialchars($doctor['name']) ?>">
                                </div>
                                <?php endif; ?>

                                <!-- Upload Zone -->
                                <div class="img-upload-zone" id="photoZone" onclick="document.getElementById('photoInput').click()">
                                    <div class="upload-icon" id="uploadIcon"><i class="fa fa-camera" style="font-size:1.5rem;color:#adb5bd;"></i></div>
                                    <p class="mt-2">Click to replace photo</p>
                                    <small class="text-muted d-block mt-1">JPG, PNG, WEBP — max 2MB<br>Auto-converted to WebP</small>
                                    <img id="photoPreview" class="preview-img" style="display:none;" alt="New Preview">
                                </div>
                                <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                                <small class="text-muted mt-2 d-block text-center" style="font-size:0.72rem;">
                                    <?php if (!empty($currentPhotoSrc)): ?>
                                        ⚠️ Uploading a new photo will replace the existing one.
                                    <?php else: ?>
                                        Recommended: square image, min 400×400px
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="card border-0 shadow-sm rounded-4 border border-danger-subtle">
                            <div class="card-header bg-danger-subtle border-bottom py-3 px-4">
                                <h6 class="mb-0 fw-bold text-danger text-uppercase small" style="letter-spacing:0.5px;">
                                    <i class="fa fa-exclamation-triangle me-2"></i>Danger Zone
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted small mb-3">Permanently delete this doctor profile. This action cannot be undone and will remove all associated data.</p>
                                <a href="./?delete=<?= $id ?>"
                                   class="btn btn-outline-danger w-100 rounded-pill fw-semibold"
                                   onclick="return confirm('Permanently delete Dr. <?= addslashes(htmlspecialchars($doctor['name'])) ?>? This cannot be undone.')">
                                    <i class="fa fa-trash-alt me-2"></i> Delete Profile
                                </a>
                            </div>
                        </div>

                    </div><!-- /col-xl-4 -->

                </div><!-- /row -->
            </form>

        </div>
    </div>
</div>

<script>
// ── Slug ──────────────────────────────────────────────────────
document.getElementById('doctorName').addEventListener('input', function () {
    document.getElementById('nameCount').textContent = this.value.length + ' chars';
    document.getElementById('serpSlug').textContent  = autoSlug(this.value) || 'doctor-slug';
});
document.getElementById('generateSlug').addEventListener('click', function () {
    const name = document.getElementById('doctorName').value;
    document.getElementById('doctorSlug').value = autoSlug(name);
});
document.getElementById('doctorSlug').addEventListener('input', function () {
    document.getElementById('serpSlug').textContent = this.value || '<?= htmlspecialchars($doctor['slug'] ?? '') ?>';
});
function autoSlug(str) {
    return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
}

// ── Char Counters ─────────────────────────────────────────────
document.getElementById('doctorExcerpt').addEventListener('input', function () {
    document.getElementById('excerptCount').textContent = this.value.length + ' chars';
});
function setupCharCounter(inputId, countId, barId, max) {
    const el = document.getElementById(inputId);
    const cnt = document.getElementById(countId);
    const bar = document.getElementById(barId);
    if (!el) return;
    // Init on load
    const initLen = el.value.length;
    updateCounter(initLen, max, cnt, bar);
    el.addEventListener('input', function () {
        updateCounter(this.value.length, max, cnt, bar);
        if (inputId === 'metaTitle') document.getElementById('serpTitle').textContent = this.value || 'Your meta title...';
        if (inputId === 'metaDesc')  document.getElementById('serpDesc').textContent  = this.value || 'Your meta description...';
    });
}
function updateCounter(len, max, cnt, bar) {
    cnt.textContent = len + ' / ' + max;
    cnt.className   = 'count ' + (len < max * 0.7 ? 'ok' : len <= max ? 'warn' : 'bad');
    if (bar) {
        const pct = Math.min((len / max) * 100, 100);
        bar.style.width      = pct + '%';
        bar.style.background = len < max * 0.7 ? '#198754' : len <= max ? '#ffc107' : '#dc3545';
    }
}
setupCharCounter('metaTitle', 'metaTitleCount', 'metaTitleBar', 60);
setupCharCounter('metaDesc',  'metaDescCount',  'metaDescBar',  160);

// ── SEO Score ─────────────────────────────────────────────────
function calcSeoScore() {
    let score = 0;
    if (document.getElementById('doctorName').value.trim())    score += 20;
    if (document.getElementById('focusKeyword').value.trim())  score += 20;
    if (document.getElementById('metaTitle').value.trim())     score += 20;
    if (document.getElementById('metaDesc').value.trim())      score += 20;
    if (document.getElementById('doctorExcerpt').value.trim()) score += 20;
    const badge = document.getElementById('seoScoreBadge');
    badge.textContent = 'Score: ' + score + '/100';
    badge.className   = 'badge border rounded-pill px-3 py-1 fw-bold ' +
        (score >= 80 ? 'bg-success text-white' : score >= 40 ? 'bg-warning text-dark' : 'bg-light text-dark');
}
['doctorName','focusKeyword','metaTitle','metaDesc','doctorExcerpt'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', calcSeoScore);
});
calcSeoScore(); // run on load

// ── Photo Preview ─────────────────────────────────────────────
document.getElementById('photoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('photoPreview');
        preview.src           = e.target.result;
        preview.style.display = 'block';
        document.getElementById('uploadIcon').style.display = 'none';
        document.querySelector('#photoZone p').style.display = 'none';
        // Also update the current photo display if it exists
        const cur = document.getElementById('currentPhotoDisplay');
        if (cur) cur.src = e.target.result;
    };
    reader.readAsDataURL(file);
});

// ── Dynamic Rows ──────────────────────────────────────────────
function addEdu() {
    document.getElementById('edu_wrapper').insertAdjacentHTML('beforeend', `
    <div class="dynamic-row d-flex gap-2 align-items-center">
        <input type="text" name="edu_title[]"  class="form-control" placeholder="University Name">
        <input type="text" name="edu_degree[]" class="form-control" placeholder="Degree (BDS)">
        <input type="text" name="edu_year[]"   class="form-control" placeholder="1998–2003" style="max-width:140px;">
        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-shrink-0" onclick="this.closest('.dynamic-row').remove()"><i class="fa fa-times"></i></button>
    </div>`);
}
function addExp() {
    document.getElementById('exp_wrapper').insertAdjacentHTML('beforeend', `
    <div class="dynamic-row d-flex gap-2 align-items-center">
        <input type="text" name="exp_title[]" class="form-control" placeholder="Clinic / Hospital Name">
        <input type="text" name="exp_year[]"  class="form-control" placeholder="2010 – Present" style="max-width:160px;">
        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-shrink-0" onclick="this.closest('.dynamic-row').remove()"><i class="fa fa-times"></i></button>
    </div>`);
}
function addAwd() {
    document.getElementById('awd_wrapper').insertAdjacentHTML('beforeend', `
    <div class="dynamic-row d-flex gap-2 align-items-center">
        <input type="text" name="awd_year[]"  class="form-control" placeholder="Year" style="max-width:110px;">
        <input type="text" name="awd_title[]" class="form-control" placeholder="Award Title">
        <input type="text" name="awd_desc[]"  class="form-control" placeholder="Short Description">
        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-shrink-0" onclick="this.closest('.dynamic-row').remove()"><i class="fa fa-times"></i></button>
    </div>`);
}

// ── Robots ────────────────────────────────────────────────────
function setRobots(val, btn) {
    document.querySelectorAll('#robotsIndexGroup .robots-btn').forEach(b => b.className = 'robots-btn shadow-sm');
    btn.classList.add(val === 'index' ? 'active-index' : 'active-noindex');
    document.getElementById('robotsIndex').value = val;
    updateRobotsHint();
}
function setFollow(val, btn) {
    document.querySelectorAll('#robotsFollowGroup .robots-btn').forEach(b => b.className = 'robots-btn shadow-sm');
    btn.classList.add(val === 'follow' ? 'active-follow' : 'active-nofollow');
    document.getElementById('robotsFollow').value = val;
    updateRobotsHint();
}
function updateRobotsHint() {
    const i = document.getElementById('robotsIndex').value;
    const f = document.getElementById('robotsFollow').value;
    const hint = document.getElementById('robotsHint');
    const ok   = i === 'index' && f === 'follow';
    hint.className   = ok ? 'text-success fw-medium mt-2 d-block small' : 'text-danger fw-medium mt-2 d-block small';
    hint.textContent = ok ? '✅ This page will be indexed and links followed.'
                          : '⚠️ This page is restricted from indexing or link-following.';
}

// ── Schema ────────────────────────────────────────────────────
function setSchema(val, btn) {
    document.querySelectorAll('.schema-opt').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('schemaType').value = val;
}
</script>

<?php require_once '../include/footer.php'; ?>