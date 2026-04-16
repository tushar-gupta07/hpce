<?php
// C:\xamppnew\htdocs\hpce\admin\services\add.php

require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
requireAccess('services');

// ── Helper: Convert Image to WebP ─────────────────────────────────────────
if (!function_exists('convertToWebp')) {
    function convertToWebp($source, $destination, $quality = 80) {
        $info = getimagesize($source);
        if (!$info) return false;
        if      ($info['mime'] == 'image/jpeg') { $image = imagecreatefromjpeg($source); }
        elseif  ($info['mime'] == 'image/png')  {
            $image = imagecreatefrompng($source);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }
        elseif ($info['mime'] == 'image/gif')  { $image = imagecreatefromgif($source); }
        elseif ($info['mime'] == 'image/webp') { $image = imagecreatefromwebp($source); }
        else { return false; }
        $success = imagewebp($image, $destination, $quality);
        imagedestroy($image);
        return $success;
    }
}

$errors = [];

// ── Fetch data ──────────────────────────────────────────────────────────────
$categories = [];
$res = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($res) { while ($r = $res->fetch_assoc()) { $categories[] = $r; } }

$all_services = [];
$res_srv = $conn->query("SELECT slug, title FROM services ORDER BY title ASC");
if ($res_srv) { while ($r = $res_srv->fetch_assoc()) { $all_services[] = $r; } }

$doctors = [];
$res = $conn->query("SELECT id, name FROM doctors ORDER BY name ASC");
if ($res) { while ($r = $res->fetch_assoc()) { $doctors[] = $r; } }

// ── POST Handler ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title             = trim($_POST['title']             ?? '');
    $slug              = trim($_POST['slug']              ?? '');
    $h1_title          = trim($_POST['h1_title']          ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    $content           = $_POST['content']                ?? '';
    $category_id       = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $icon              = trim($_POST['icon']              ?? '');
    $sort_order        = (int)($_POST['sort_order']       ?? 0);
    $is_published      = isset($_POST['is_published'])    ? 1 : 0;

    $hero_title     = trim($_POST['hero_title']     ?? '');
    $hero_subtitle  = trim($_POST['hero_subtitle']  ?? '');
    $hero_image_alt = trim($_POST['hero_image_alt'] ?? '');
    $image_alt      = trim($_POST['image_alt']      ?? '');

    $meta_title          = trim($_POST['meta_title']          ?? '');
    $meta_description    = trim($_POST['meta_description']    ?? '');
    $focus_keyword       = trim($_POST['focus_keyword']       ?? '');
    $canonical_url       = trim($_POST['canonical_url']       ?? '');
    $og_title            = trim($_POST['og_title']            ?? '');
    $og_description      = trim($_POST['og_description']      ?? '');
    $og_type             = trim($_POST['og_type']             ?? 'website');
    $twitter_title       = trim($_POST['twitter_title']       ?? '');
    $twitter_description = trim($_POST['twitter_description'] ?? '');
    $twitter_card        = trim($_POST['twitter_card']        ?? 'summary_large_image');
    $robots_index        = trim($_POST['robots_index']        ?? 'index');
    $robots_follow       = trim($_POST['robots_follow']       ?? 'follow');
    $schema_type         = trim($_POST['schema_type']         ?? 'MedicalProcedure');

    // Validation
    if (empty($title))      $errors[] = 'Service Title is required.';
    if (empty($content) || $content === '<p><br></p>') $errors[] = 'Main Content is required.';
    if (empty($category_id)) $errors[] = 'Please select a Category.';
    if (!empty($meta_title) && mb_strlen($meta_title) > 70)       $errors[] = 'Meta title must not exceed 70 characters.';
    if (!empty($meta_description) && mb_strlen($meta_description) > 180) $errors[] = 'Meta description must not exceed 180 characters.';

    // Slug
    if (empty($slug)) { $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title)); }
    else              { $slug = strtolower(preg_replace('/[^a-z0-9-]+/', '-', $slug)); }
    $slug    = trim($slug, '-');
    $slugEsc = $conn->real_escape_string($slug);
    $chk = $conn->query("SELECT id FROM services WHERE slug = '$slugEsc'");
    if ($chk && $chk->num_rows > 0) $errors[] = 'Slug already exists. Please choose a different one.';

    // SEO defaults
    if (empty($meta_title))          $meta_title          = $title;
    if (empty($meta_description))    $meta_description    = $short_description;
    if (empty($og_title))            $og_title            = $meta_title;
    if (empty($og_description))      $og_description      = $meta_description;
    if (empty($twitter_title))       $twitter_title       = $meta_title;
    if (empty($twitter_description)) $twitter_description = $meta_description;

    // Image setup
    $imagePath     = 'assets/img/services/default.jpg';
    $heroImagePath = '';
    $ogImagePath   = '';
    $seoBaseName   = !empty($slug) ? $slug : 'service';
    $uploadDir     = '../../assets/img/services/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $allowedTypes  = ['image/jpeg','image/png','image/webp','image/gif'];

    // 1. Main Image
    if (!empty($_FILES['image']['name'])) {
        $ft = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($ft, $allowedTypes)) { $errors[] = 'Invalid main image type.'; }
        elseif ($_FILES['image']['size'] > 2*1024*1024) { $errors[] = 'Main image must be under 2MB.'; }
        else {
            $fn = $seoBaseName.'-main-'.uniqid().'.webp';
            if (convertToWebp($_FILES['image']['tmp_name'], $uploadDir.$fn, 85)) {
                $imagePath = 'assets/img/services/'.$fn;
                if (empty($ogImagePath)) $ogImagePath = $imagePath;
                if (!empty($_POST['use_image_as_hero'])) $heroImagePath = $imagePath;
            } else { $errors[] = 'Failed to convert main image to WebP.'; }
        }
    }

    // 2. Hero Image
    if (!empty($_FILES['hero_image']['name'])) {
        $ft = mime_content_type($_FILES['hero_image']['tmp_name']);
        if (in_array($ft, $allowedTypes)) {
            if ($_FILES['hero_image']['size'] > 3*1024*1024) { $errors[] = 'Hero image must be under 3MB.'; }
            else {
                $fn = $seoBaseName.'-hero-'.uniqid().'.webp';
                if (convertToWebp($_FILES['hero_image']['tmp_name'], $uploadDir.$fn, 85))
                    $heroImagePath = 'assets/img/services/'.$fn;
                else $errors[] = 'Failed to convert hero image.';
            }
        }
    }

    // 3. OG Image
    if (!empty($_FILES['og_image']['name'])) {
        $ft = mime_content_type($_FILES['og_image']['tmp_name']);
        if (in_array($ft, $allowedTypes)) {
            $ogDir = '../../assets/img/services/og/';
            if (!is_dir($ogDir)) mkdir($ogDir, 0755, true);
            $fn = $seoBaseName.'-og-'.uniqid().'.webp';
            if (convertToWebp($_FILES['og_image']['tmp_name'], $ogDir.$fn, 80))
                $ogImagePath = 'assets/img/services/og/'.$fn;
            else $errors[] = 'Failed to convert OG image.';
        }
    }

    // 4. Hero Content Image
    $heroContentImagePath = '';
    if (!empty($_FILES['hero_content_image']['name'])) {
        $ft = mime_content_type($_FILES['hero_content_image']['tmp_name']);
        if (in_array($ft, $allowedTypes)) {
            if ($_FILES['hero_content_image']['size'] > 3*1024*1024) { $errors[] = 'Hero content image must be under 3MB.'; }
            else {
                $fn = $seoBaseName.'-hc-'.uniqid().'.webp';
                if (convertToWebp($_FILES['hero_content_image']['tmp_name'], $uploadDir.$fn, 85))
                    $heroContentImagePath = 'assets/img/services/'.$fn;
                else $errors[] = 'Failed to convert hero content image.';
            }
        }
    }

    // 5. Service Card Thumbnail
    $serviceCardThumbPath = '';
    if (!empty($_FILES['service_card_thumbnail']['name'])) {
        $ft = mime_content_type($_FILES['service_card_thumbnail']['tmp_name']);
        if (in_array($ft, $allowedTypes)) {
            if ($_FILES['service_card_thumbnail']['size'] > 2*1024*1024) { $errors[] = 'Card thumbnail must be under 2MB.'; }
            else {
                $fn = $seoBaseName.'-card-'.uniqid().'.webp';
                if (convertToWebp($_FILES['service_card_thumbnail']['tmp_name'], $uploadDir.$fn, 85))
                    $serviceCardThumbPath = 'assets/img/services/'.$fn;
                else $errors[] = 'Failed to convert card thumbnail.';
            }
        }
    }

    // ── JSON Builders ──────────────────────────────────────────────────────

    // FAQs
    $faqs = [];
    if (!empty($_POST['faq_q']) && is_array($_POST['faq_q'])) {
        foreach ($_POST['faq_q'] as $i => $q) {
            $a = $_POST['faq_a'][$i] ?? '';
            if (!empty(trim($q)) && !empty(trim($a)))
                $faqs[] = ['q' => trim($q), 'a' => trim($a)];
        }
    }
    $faqs_json = empty($faqs) ? null : json_encode($faqs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    // Sections
    $sections = [];
    if (!empty($_POST['sec_h2']) && is_array($_POST['sec_h2'])) {
        foreach ($_POST['sec_h2'] as $i => $h2) {
            $sc   = $_POST['sec_content'][$i] ?? '';
            $sl   = $_POST['sec_list'][$i]    ?? '';
            $list = array_values(array_filter(array_map('trim', explode("\n", $sl))));
            if (!empty(trim($h2)) || !empty(trim($sc)) || !empty($list)) {
                $sec = [];
                if (!empty(trim($h2))) $sec['h2']      = trim($h2);
                if (!empty(trim($sc))) $sec['content']  = trim($sc);
                if (!empty($list))     $sec['list']     = $list;
                $sections[] = $sec;
            }
        }
    }
    $sections_json = empty($sections) ? null : json_encode($sections, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    // Related Services
    $related_services = [];
    if (!empty($_POST['related_services']) && is_array($_POST['related_services']))
        $related_services = array_map('trim', $_POST['related_services']);
    $related_services_json = empty($related_services) ? null : json_encode($related_services, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    // Breadcrumb
    $catName = 'Services';
    if ($category_id) {
        $catRes = $conn->query("SELECT name FROM categories WHERE id = ".(int)$category_id);
        if ($catRes && $catRes->num_rows > 0) $catName = $catRes->fetch_assoc()['name'];
    }
    $breadcrumb_json = json_encode([
        ['name'=>'Home',     'url'=>'/'],
        ['name'=>'Services', 'url'=>'/services.php'],
        ['name'=>$catName,   'url'=>'/services.php?category='.urlencode(strtolower($catName))],
        ['name'=>$title,     'url'=>'/'.$slug]
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

    // Gallery
    $gallery = [];
    if (!empty($_FILES['gallery_images']['name'][0])) {
        $galleryDir = '../../assets/img/services/gallery/';
        if (!is_dir($galleryDir)) mkdir($galleryDir, 0755, true);
        foreach ($_FILES['gallery_images']['name'] as $k => $name) {
            if ($_FILES['gallery_images']['error'][$k] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['gallery_images']['tmp_name'][$k];
                $ft  = mime_content_type($tmp);
                if (in_array($ft, $allowedTypes)) {
                    $gf = $seoBaseName.'-gallery-'.uniqid().'.webp';
                    if (convertToWebp($tmp, $galleryDir.$gf, 80))
                        $gallery[] = ['src'=>'assets/img/services/gallery/'.$gf, 'alt'=>$title];
                }
            }
        }
    }
    $gallery_json = empty($gallery) ? null : json_encode($gallery, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

    // Schema
    $schema_json = null;
    if (!empty($schema_type)) {
        $schema_json = json_encode([
            '@context'    => 'https://schema.org',
            '@type'       => $schema_type,
            'name'        => $meta_title ?: $title,
            'description' => $meta_description ?: $short_description,
            'url'         => $canonical_url ?: '',
            'image'       => $ogImagePath   ?: ''
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    // Hero Content JSON
    $hc_tagline     = trim($_POST['hc_tagline']     ?? '');
    $hc_heading     = trim($_POST['hc_heading']     ?? '');
    $hc_description = trim($_POST['hc_description'] ?? '');
    $hc_features    = [];
    if (!empty($_POST['hc_feat_title']) && is_array($_POST['hc_feat_title'])) {
        foreach ($_POST['hc_feat_title'] as $i => $ft) {
            $fd = $_POST['hc_feat_desc'][$i] ?? '';
            $fi = $_POST['hc_feat_icon'][$i] ?? 'star';
            if (!empty(trim($ft)))
                $hc_features[] = ['title'=>trim($ft), 'description'=>trim($fd), 'icon'=>trim($fi)];
        }
    }
    $hero_content_data = [];
    if (!empty($hc_tagline))            $hero_content_data['tagline']     = $hc_tagline;
    if (!empty($hc_heading))            $hero_content_data['heading']     = $hc_heading;
    if (!empty($hc_description))        $hero_content_data['description'] = $hc_description;
    if (!empty($heroContentImagePath))  $hero_content_data['hero_image']  = $heroContentImagePath;
    if (!empty($hc_features))           $hero_content_data['features']    = $hc_features;
    $hero_content_json = empty($hero_content_data) ? null : json_encode($hero_content_data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    // Service Card JSON
    $sc_title       = trim($_POST['sc_title']       ?? '');
    $sc_department  = trim($_POST['sc_department']  ?? '');
    $sc_location    = trim($_POST['sc_location']    ?? '');
    $sc_description = trim($_POST['sc_description'] ?? '');
    $sc_thumb_alt   = trim($_POST['sc_thumb_alt']   ?? '');
    $service_card_data = [];
    if (!empty($sc_title))             $service_card_data['title']           = $sc_title;
    if (!empty($sc_department))        $service_card_data['department']      = $sc_department;
    if (!empty($sc_location))          $service_card_data['location']        = $sc_location;
    if (!empty($sc_description))       $service_card_data['description']     = $sc_description;
    if (!empty($serviceCardThumbPath)) $service_card_data['thumbnail_image'] = $serviceCardThumbPath;
    if (!empty($sc_thumb_alt))         $service_card_data['thumbnail_alt']   = $sc_thumb_alt;
    $service_card_json = empty($service_card_data) ? null : json_encode($service_card_data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    // Why Choose JSON
    $why_choose = [];
    if (!empty($_POST['wc_title']) && is_array($_POST['wc_title'])) {
        foreach ($_POST['wc_title'] as $i => $wt) {
            $wd = $_POST['wc_desc'][$i] ?? '';
            if (!empty(trim($wt)))
                $why_choose[] = ['title'=>trim($wt), 'description'=>trim($wd)];
        }
    }
    $why_choose_json = empty($why_choose) ? null : json_encode($why_choose, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    // ── INSERT ─────────────────────────────────────────────────────────────
    if (empty($errors)) {
        $s = fn($v) => ($v !== '' && $v !== null) ? "'".$conn->real_escape_string($v)."'" : "NULL";
        $e = fn($v) => $conn->real_escape_string($v);
        $robots_meta = $robots_index.','.$robots_follow;
        $catVal      = $category_id ? (int)$category_id : 'NULL';

        $sql = "INSERT INTO services (
                    title, hero_title, hero_subtitle, hero_image, hero_image_alt,
                    slug, short_description, h1_title, breadcrumb_json, content,
                    sections_json, faqs_json, image, image_alt, gallery_json, icon,
                    category_id, related_services_json, is_published, sort_order,
                    meta_title, meta_description, focus_keyword, canonical_url,
                    og_title, og_description, og_image, og_type,
                    twitter_title, twitter_description, twitter_card,
                    robots_meta, schema_type, schema_json,
                    hero_content_json, service_card_json, why_choose_json,
                    created_at, updated_at
                ) VALUES (
                    {$s($title)}, {$s($hero_title)}, {$s($hero_subtitle)},
                    {$s($heroImagePath)}, {$s($hero_image_alt)},
                    {$s($slug)}, {$s($short_description)}, {$s($h1_title)},
                    {$s($breadcrumb_json)}, '{$e($content)}',
                    {$s($sections_json)}, {$s($faqs_json)},
                    {$s($imagePath)}, {$s($image_alt)}, {$s($gallery_json)}, {$s($icon)},
                    $catVal, {$s($related_services_json)}, $is_published, $sort_order,
                    {$s($meta_title)}, {$s($meta_description)}, {$s($focus_keyword)},
                    {$s($canonical_url)}, {$s($og_title)}, {$s($og_description)},
                    {$s($ogImagePath)}, {$s($og_type)},
                    {$s($twitter_title)}, {$s($twitter_description)}, {$s($twitter_card)},
                    {$s($robots_meta)}, {$s($schema_type)}, {$s($schema_json)},
                    {$s($hero_content_json)}, {$s($service_card_json)}, {$s($why_choose_json)},
                    NOW(), NOW()
                )";

        if ($conn->query($sql)) {
            header("Location: ./?msg=added");
            exit;
        } else {
            $errors[] = 'Database error: '.$conn->error;
        }
    }
}

$p = fn($k) => htmlspecialchars($_POST[$k] ?? '');

$pageTitle  = 'Add New Service';
$activePage = 'services-add';
$assetBase  = '../';

$extraCSS = '
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
:root { --c-success:#198754; --c-warn:#ffc107; --c-danger:#dc3545; --c-primary:#0d6efd; }

/* ── Builder empty states ── */
.builder-empty-state { text-align:center; padding:2rem 1rem; color:#adb5bd; border:2px dashed #dee2e6; border-radius:.75rem; background:#fafafa; display:none; }
.builder-empty-state.show { display:block; }
.builder-empty-state i { font-size:2rem; margin-bottom:.5rem; display:block; }
.builder-empty-state p { font-size:.8rem; margin:0; }

/* ── Section Row ── */
.section-row { background:#fff; border:1.5px solid #e9ecef; border-radius:.75rem; margin-bottom:.75rem; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.04); transition:box-shadow .2s,border-color .2s; }
.section-row:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
.section-row.drag-over { border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,.15); }
.section-row-header { display:flex; align-items:center; gap:10px; padding:.75rem 1rem; background:#f8f9fa; border-bottom:1.5px solid #e9ecef; cursor:pointer; user-select:none; }
.section-row-header:hover { background:#f1f3f5; }
.drag-handle { color:#ced4da; cursor:grab; font-size:.9rem; padding:2px 4px; }
.drag-handle:active { cursor:grabbing; }
.section-row-num { width:22px; height:22px; background:#0d6efd; color:#fff; border-radius:50%; font-size:.65rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.section-row-label { flex:1; font-size:.82rem; font-weight:600; color:#212529; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.section-row-label.placeholder { color:#adb5bd; font-weight:400; font-style:italic; }
.section-row-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
.sec-collapse-btn { background:none; border:none; color:#6c757d; font-size:.75rem; padding:3px 6px; border-radius:.3rem; cursor:pointer; }
.sec-collapse-btn:hover { background:#e9ecef; }
.sec-delete-btn { background:#fff0f0; border:1px solid #ffc9c9; color:#dc3545; font-size:.7rem; padding:3px 8px; border-radius:.3rem; cursor:pointer; }
.sec-delete-btn:hover { background:#dc3545; color:#fff; border-color:#dc3545; }
.section-row-body { padding:1.25rem; }
.section-row-body.collapsed { display:none; }
.sec-tab-btns { display:flex; gap:4px; margin-bottom:1rem; background:#f1f3f5; border-radius:.5rem; padding:4px; }
.sec-tab-btn { flex:1; padding:5px 8px; border:none; border-radius:.35rem; font-size:.72rem; font-weight:700; cursor:pointer; background:none; color:#6c757d; }
.sec-tab-btn.active { background:#fff; color:#0d6efd; box-shadow:0 1px 4px rgba(0,0,0,.1); }
.sec-tab-panel { display:none; }
.sec-tab-panel.active { display:block; }

/* ── FAQ Row ── */
.faq-row { background:#fff; border:1.5px solid #e9ecef; border-radius:.75rem; margin-bottom:.75rem; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.04); }
.faq-row:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
.faq-row-header { display:flex; align-items:center; gap:10px; padding:.75rem 1rem; cursor:pointer; user-select:none; background:#fffbf0; border-bottom:1.5px solid #e9ecef; }
.faq-row-header:hover { background:#fff8e0; }
.faq-row-num { width:22px; height:22px; background:#fd7e14; color:#fff; border-radius:50%; font-size:.65rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.faq-q-preview { flex:1; font-size:.82rem; font-weight:600; color:#212529; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.faq-q-preview.placeholder { color:#adb5bd; font-weight:400; font-style:italic; }
.faq-row-body { padding:1.25rem; }
.faq-row-body.collapsed { display:none; }

/* ── Why Choose Row ── */
.wc-row { background:#fff; border:1.5px solid #e9ecef; border-radius:.75rem; margin-bottom:.75rem; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.04); }
.wc-row:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
.wc-row-header { display:flex; align-items:center; gap:10px; padding:.75rem 1rem; cursor:pointer; user-select:none; background:#f0fff4; border-bottom:1.5px solid #e9ecef; }
.wc-row-header:hover { background:#e6faf0; }
.wc-row-num { width:22px; height:22px; background:#198754; color:#fff; border-radius:50%; font-size:.65rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.wc-title-preview { flex:1; font-size:.82rem; font-weight:600; color:#212529; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.wc-title-preview.placeholder { color:#adb5bd; font-weight:400; font-style:italic; }
.wc-row-body { padding:1.25rem; }
.wc-row-body.collapsed { display:none; }

/* ── Hero Feature Row ── */
.hcf-row { background:#fff; border:1.5px solid #e9ecef; border-radius:.75rem; margin-bottom:.75rem; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.04); }
.hcf-row:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
.hcf-row-header { display:flex; align-items:center; gap:10px; padding:.65rem 1rem; cursor:pointer; user-select:none; background:#f0f4ff; border-bottom:1.5px solid #e9ecef; }
.hcf-row-num { width:22px; height:22px; background:#6610f2; color:#fff; border-radius:50%; font-size:.65rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.hcf-title-preview { flex:1; font-size:.82rem; font-weight:600; color:#212529; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.hcf-title-preview.placeholder { color:#adb5bd; font-weight:400; font-style:italic; }
.hcf-row-body { padding:1.25rem; }
.hcf-row-body.collapsed { display:none; }

/* ── Gallery ── */
.gallery-thumb-wrap { position:relative; border-radius:.5rem; overflow:hidden; aspect-ratio:1/1; background:#e9ecef; border:2px solid #dee2e6; }
.gallery-thumb-wrap:hover { border-color:#0d6efd; }
.gallery-thumb-wrap img { width:100%; height:100%; object-fit:cover; display:block; }
.gallery-thumb-remove { position:absolute; top:4px; right:4px; background:rgba(220,53,69,.85); color:#fff; border:none; border-radius:50%; width:22px; height:22px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.65rem; }
.gallery-thumb-remove:hover { background:#dc3545; }
.gallery-thumb-name { position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,.55); color:#fff; font-size:.6rem; padding:3px 5px; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* ── Form ── */
.form-label { font-size:.75rem; font-weight:700; color:#6c757d; text-transform:uppercase; letter-spacing:.5px; margin-bottom:.4rem; }
.form-control, .form-select { font-size:.9rem; padding:.55rem .9rem; border-color:#dee2e6; border-radius:.5rem; }
.form-control:focus, .form-select:focus { border-color:#86b7fe; box-shadow:0 0 0 .2rem rgba(13,110,253,.2); }
.input-group-text { background:#f8f9fa; color:#6c757d; font-size:.875rem; border-color:#dee2e6; }
.svc-card { background:#fff; border:1px solid #e9ecef; border-radius:1rem; margin-bottom:1.5rem; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.04); }
.svc-card-header { padding:.9rem 1.25rem; background:#fff; border-bottom:1px solid #f1f3f5; display:flex; align-items:center; gap:.75rem; }
.svc-card-icon { width:32px; height:32px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0; }
.svc-card-title { font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:#495057; margin:0; }
.svc-card-body { padding:1.5rem; }
.char-counter { display:flex; justify-content:space-between; margin-top:5px; font-size:.7rem; color:#adb5bd; font-family:monospace; }
.cc { font-weight:700; }
.cc-ok   { color:var(--c-success) !important; }
.cc-warn { color:var(--c-warn)    !important; }
.cc-bad  { color:var(--c-danger)  !important; }
.char-bar { height:3px; background:#e9ecef; border-radius:2px; margin-top:4px; overflow:hidden; }
.char-bar-fill { height:100%; border-radius:2px; transition:width .3s,background .3s; }

/* ── SEO ── */
.seo-ring { width:68px; height:68px; position:relative; flex-shrink:0; }
.seo-ring svg { transform:rotate(-90deg); width:100%; height:100%; }
.seo-ring .rt { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; }
.seo-ring .rn { font-size:1.2rem; font-weight:900; line-height:1; display:block; }
.seo-item { display:flex; align-items:flex-start; gap:8px; padding:5px 0; font-size:.78rem; color:#495057; border-bottom:1px solid #f8f9fa; }
.seo-item:last-child { border-bottom:none; }
.seo-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:4px; }
.dot-ok   { background:var(--c-success); }
.dot-warn { background:var(--c-warn); }
.dot-bad  { background:var(--c-danger); }
.serp-box { background:#fff; border:1px solid #dfe1e5; border-radius:.5rem; padding:1rem; }
.serp-url   { font-size:.75rem; color:#202124; font-family:Arial,sans-serif; }
.serp-title { font-size:1.1rem; color:#1a0dab; font-family:Arial,sans-serif; line-height:1.3; }
.serp-desc  { font-size:.82rem; color:#4d5156; font-family:Arial,sans-serif; line-height:1.5; }
.serp-ph    { color:#adb5bd !important; font-style:italic; }
.og-card { border:1px solid #dee2e6; border-radius:.5rem; overflow:hidden; background:#f8f9fa; }
.og-img  { width:100%; height:140px; background:#e9ecef; display:flex; align-items:center; justify-content:center; color:#adb5bd; font-size:.8rem; overflow:hidden; }
.og-img img { width:100%; height:100%; object-fit:cover; }
.og-body { padding:.75rem 1rem; background:#fff; }
.og-domain { font-size:.62rem; color:#6c757d; text-transform:uppercase; letter-spacing:.5px; }
.og-title  { font-size:.95rem; font-weight:700; color:#1a1a1a; margin:3px 0; }
.og-desc   { font-size:.8rem; color:#495057; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

/* ── Quill ── */
.ql-toolbar.ql-snow   { background:#f8f9fa; border-color:#dee2e6; border-radius:.5rem .5rem 0 0; }
.ql-container.ql-snow { border-color:#dee2e6; border-radius:0 0 .5rem .5rem; }
.ql-editor { min-height:320px; font-size:.93rem; line-height:1.75; color:#212529; }

/* ── Upload Zone ── */
.upload-zone { border:2px dashed #dee2e6; border-radius:.75rem; padding:2rem 1.25rem; text-align:center; cursor:pointer; transition:.2s; background:#fafafa; }
.upload-zone:hover { border-color:var(--c-primary); background:#f0f7ff; }
.upload-zone .uz-icon { font-size:1.8rem; color:#ced4da; margin-bottom:.4rem; }
.upload-zone p { font-size:.83rem; color:#6c757d; margin:0; font-weight:500; }
.upload-zone .uz-prev { width:100%; max-height:180px; border-radius:.4rem; object-fit:cover; display:none; margin-top:8px; }

/* ── Keywords / Schema / Robots ── */
.kw-tags { display:flex; flex-wrap:wrap; gap:5px; margin-top:7px; }
.kw-tag  { background:#f8f9fa; border:1px solid #dee2e6; border-radius:20px; padding:3px 11px; font-size:.72rem; color:#495057; cursor:pointer; transition:.15s; }
.kw-tag:hover { border-color:var(--c-primary); color:var(--c-primary); background:#f0f7ff; }
.schema-opts, .robots-grp { display:flex; flex-wrap:wrap; gap:6px; margin-top:6px; }
.schema-btn, .robots-btn { padding:5px 12px; border-radius:20px; font-size:.72rem; font-weight:700; cursor:pointer; border:1px solid #dee2e6; color:#6c757d; background:#fff; flex:1; text-align:center; transition:.15s; }
.schema-btn.active   { border-color:var(--c-success); color:var(--c-success); background:rgba(25,135,84,.05); }
.robots-btn.a-index, .robots-btn.a-follow   { border-color:var(--c-success); color:var(--c-success); background:rgba(25,135,84,.05); }
.robots-btn.a-noindex, .robots-btn.a-nofollow { border-color:var(--c-danger); color:var(--c-danger); background:rgba(220,53,69,.05); }
.kd-bar  { height:4px; background:#e9ecef; border-radius:3px; overflow:hidden; margin:5px 0; }
.kd-fill { height:100%; border-radius:3px; background:var(--c-primary); transition:width .4s; }
.nav-pills .nav-link { color:#6c757d; border-radius:20px; font-size:.82rem; font-weight:600; padding:.45rem .9rem; }
.nav-pills .nav-link.active { background:#e7f1ff; color:var(--c-primary); }
.pub-badge { transition:all .3s; }
.select2-container--default .select2-selection--multiple { border-color:#dee2e6 !important; border-radius:.5rem !important; min-height:42px !important; padding:.3rem .5rem !important; }
</style>
';

require_once '../include/head.php';
?>

<div class="main-wrapper">
    <?php require_once '../include/header.php'; ?>
    <?php require_once '../include/sidebar.php'; ?>

    <div class="page-wrapper" style="background:#f4f6f9;min-height:100vh;">
        <div class="content container-fluid pt-4 pb-5">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h4 class="fw-bolder text-dark mb-1">
                        <i class="fa fa-plus-circle text-primary me-2"></i>Create New Service
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="../" class="text-muted text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="./" class="text-muted text-decoration-none">Services</a></li>
                            <li class="breadcrumb-item active">Add New</li>
                        </ol>
                    </nav>
                </div>
                <a href="./" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold mt-3 mt-md-0 d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="fa fa-arrow-left"></i> Back to Services
                </a>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-start gap-3 mb-4" role="alert">
                <i class="fa fa-exclamation-triangle mt-1 fs-5 flex-shrink-0"></i>
                <div>
                    <div class="fw-bold mb-1">Please fix the following errors:</div>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="serviceForm" novalidate>
                <div class="row g-4">

                    <!-- ════════════ LEFT COLUMN ════════════ -->
                    <div class="col-xl-8 col-lg-7">

                        <!-- 1. Service Content -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-primary-subtle text-primary"><i class="fa fa-edit"></i></div>
                                <h6 class="svc-card-title">Service Content</h6>
                            </div>
                            <div class="svc-card-body">
                                <div class="mb-4">
                                    <label class="form-label">Service Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="svcTitle" class="form-control"
                                        placeholder="e.g. PCOD / PCOS Treatment" value="<?= $p('title') ?>">
                                    <div class="char-counter"><span>Title length</span><span class="cc" id="ccTitle">0 chars</span></div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">URL Slug</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="font-size:.8rem;color:#adb5bd;">/service/</span>
                                        <input type="text" name="slug" id="svcSlug" class="form-control"
                                            placeholder="auto-generated-from-title" value="<?= $p('slug') ?>">
                                        <button type="button" class="btn btn-light border" id="btnGenSlug" title="Re-generate from title">
                                            <i class="fa fa-magic"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size:.72rem;">Lowercase, hyphens only. Auto-generated if left blank.</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">H1 Title</label>
                                    <input type="text" name="h1_title" class="form-control"
                                        placeholder="e.g. Best PCOD Treatment in Nagpur" value="<?= $p('h1_title') ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Short Description / Excerpt</label>
                                    <textarea name="short_description" id="svcExcerpt" class="form-control" rows="3"
                                        placeholder="1–2 line summary shown on listing cards..."><?= $p('short_description') ?></textarea>
                                    <div class="char-counter"><span>Excerpt length</span><span class="cc" id="ccExcerpt">0 chars</span></div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span>Main Content <span class="text-danger">*</span></span>
                                        <span id="readBadge" class="badge bg-light border text-secondary rounded-pill" style="display:none;">
                                            <i class="fa fa-clock me-1"></i><span id="readText">~0 min</span>
                                        </span>
                                    </label>
                                    <div id="quillEditor"></div>
                                    <textarea name="content" id="svcContent" class="d-none"><?= $p('content') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Hero Banner -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-warning-subtle text-warning"><i class="fa fa-image"></i></div>
                                <h6 class="svc-card-title">Hero Banner</h6>
                            </div>
                            <div class="svc-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Hero Title</label>
                                        <input type="text" name="hero_title" class="form-control"
                                            placeholder="Large heading inside hero banner" value="<?= $p('hero_title') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Hero Subtitle</label>
                                        <input type="text" name="hero_subtitle" class="form-control"
                                            placeholder="Sub-text below hero title" value="<?= $p('hero_subtitle') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Hero Image Alt Text</label>
                                        <input type="text" name="hero_image_alt" class="form-control"
                                            placeholder="Descriptive alt text" value="<?= $p('hero_image_alt') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Hero Image <span class="text-muted fw-normal text-lowercase">(max 3MB)</span></label>
                                        <!-- ✅ id="heroZone" — NO onclick here, JS handles it -->
                                        <div class="upload-zone" id="heroZone">
                                            <div class="uz-icon"><i class="fa fa-image"></i></div>
                                            <p>Click or drag Hero Background image</p>
                                            <small class="text-muted" style="font-size:.72rem;">Recommended: 1920×600px, auto-converted to WebP</small>
                                            <img id="heroPrev" class="uz-prev" alt="Hero Preview">
                                        </div>
                                        <input type="file" name="hero_image" id="heroInput" accept="image/*" class="d-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Hero Content Section -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon" style="background:#f3e8ff;color:#6610f2;"><i class="fa fa-layer-group"></i></div>
                                <h6 class="svc-card-title">Hero Content Section</h6>
                                <span class="badge bg-info-subtle text-info ms-auto" style="font-size:.65rem;">hero_content_json</span>
                            </div>
                            <div class="svc-card-body">
                                <p class="text-muted mb-3" style="font-size:.8rem;">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Renders the "About" section below hero — tagline, heading, description, side image, feature points.
                                </p>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tagline</label>
                                        <input type="text" name="hc_tagline" class="form-control"
                                            placeholder="e.g. Gynaecology — Nagpur" value="<?= $p('hc_tagline') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Section Heading</label>
                                        <input type="text" name="hc_heading" class="form-control"
                                            placeholder="e.g. Advanced PCOS Treatment at RK Hospital" value="<?= $p('hc_heading') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Section Description</label>
                                        <textarea name="hc_description" class="form-control" rows="3"
                                            placeholder="Introductory paragraph shown in the about section..."><?= $p('hc_description') ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Section Image <span class="text-muted fw-normal text-lowercase">(max 3MB)</span></label>
                                        <!-- ✅ id="hcImgZone" -->
                                        <div class="upload-zone" id="hcImgZone">
                                            <div class="uz-icon"><i class="fa fa-image"></i></div>
                                            <p>Click or drag Hero Content Image</p>
                                            <small class="text-muted" style="font-size:.72rem;">Recommended: 600×700px — auto-converted to WebP</small>
                                            <img id="hcImgPrev" class="uz-prev" alt="Hero Content Image Preview">
                                        </div>
                                        <input type="file" name="hero_content_image" id="hcImgInput" accept="image/*" class="d-none">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2 mt-2">
                                    <label class="form-label mb-0">Feature Points <span class="text-muted fw-normal text-lowercase">(icon badges)</span></label>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAddHcFeature">
                                        <i class="fa fa-plus me-1"></i> Add Feature
                                    </button>
                                </div>
                                <div id="hcFeaturesEmpty" class="builder-empty-state show">
                                    <i class="fa fa-star"></i>
                                    <p>No features added yet. Click "Add Feature" to add icon points.</p>
                                </div>
                                <div id="hcFeaturesWrap"></div>
                            </div>
                        </div>

                        <!-- 4. Service Card Info -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-success-subtle text-success"><i class="fa fa-id-card"></i></div>
                                <h6 class="svc-card-title">Service Card Info</h6>
                                <span class="badge bg-info-subtle text-info ms-auto" style="font-size:.65rem;">service_card_json</span>
                            </div>
                            <div class="svc-card-body">
                                <p class="text-muted mb-3" style="font-size:.8rem;">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Populates the service card widget (title, department, location, thumbnail).
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Card Title</label>
                                        <input type="text" name="sc_title" class="form-control"
                                            placeholder="e.g. PCOD / PCOS Treatment" value="<?= $p('sc_title') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Department</label>
                                        <input type="text" name="sc_department" class="form-control"
                                            placeholder="e.g. Department of Gynaecology" value="<?= $p('sc_department') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="sc_location" class="form-control"
                                            placeholder="e.g. RK Hospital, Nagpur" value="<?= $p('sc_location') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Thumbnail Alt Text</label>
                                        <input type="text" name="sc_thumb_alt" class="form-control"
                                            placeholder="Alt text for card thumbnail" value="<?= $p('sc_thumb_alt') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Card Description</label>
                                        <textarea name="sc_description" class="form-control" rows="2"
                                            placeholder="Short text shown under location in card..."><?= $p('sc_description') ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Card Thumbnail <span class="text-muted fw-normal text-lowercase">(max 2MB)</span></label>
                                        <!-- ✅ id="scThumbZone" -->
                                        <div class="upload-zone" id="scThumbZone">
                                            <div class="uz-icon"><i class="fa fa-image"></i></div>
                                            <p>Click or drag Service Card Thumbnail</p>
                                            <small class="text-muted" style="font-size:.72rem;">Recommended: 400×400px square — auto-converted to WebP</small>
                                            <img id="scThumbPrev" class="uz-prev" alt="Service Card Thumbnail Preview">
                                        </div>
                                        <input type="file" name="service_card_thumbnail" id="scThumbInput" accept="image/*" class="d-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Why Choose -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-success-subtle text-success"><i class="fa fa-check-circle"></i></div>
                                <h6 class="svc-card-title">Why Choose RK Hospital</h6>
                                <span class="badge bg-info-subtle text-info ms-auto" style="font-size:.65rem;">why_choose_json</span>
                            </div>
                            <div class="svc-card-body">
                                <p class="text-muted mb-3" style="font-size:.8rem;">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Renders as the "Why Choose RK Hospital?" grid section with custom benefit cards.
                                </p>
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" id="btnAddWC">
                                        <i class="fa fa-plus me-1"></i> Add Point
                                    </button>
                                </div>
                                <div id="wcEmpty" class="builder-empty-state show">
                                    <i class="fa fa-check-double"></i>
                                    <p>No points added yet. Click "Add Point" to highlight service benefits.</p>
                                </div>
                                <div id="wcWrap"></div>
                            </div>
                        </div>

                        <!-- 6. Content Sections Builder -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-info-subtle text-info"><i class="fa fa-list-alt"></i></div>
                                <h6 class="svc-card-title">Content Sections</h6>
                            </div>
                            <div class="svc-card-body">
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" id="btnAddSection">
                                        <i class="fa fa-plus me-1"></i> Add Section
                                    </button>
                                </div>
                                <div id="sectionsEmpty" class="builder-empty-state show">
                                    <i class="fa fa-layer-group"></i>
                                    <p>No sections added yet. Add sections to structure your content.</p>
                                </div>
                                <div id="sectionsWrap"></div>
                            </div>
                        </div>

                        <!-- 7. FAQ Builder -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-warning-subtle text-warning"><i class="fa fa-question-circle"></i></div>
                                <h6 class="svc-card-title">FAQs</h6>
                            </div>
                            <div class="svc-card-body">
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" id="btnAddFaq">
                                        <i class="fa fa-plus me-1"></i> Add FAQ
                                    </button>
                                </div>
                                <div id="faqsEmpty" class="builder-empty-state show">
                                    <i class="fa fa-question"></i>
                                    <p>No FAQs yet. FAQs generate FAQ Schema and improve SEO.</p>
                                </div>
                                <div id="faqsWrap"></div>
                            </div>
                        </div>

                        <!-- 8. SEO Panel -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-success-subtle text-success"><i class="fa fa-search"></i></div>
                                <h6 class="svc-card-title">SEO & Meta</h6>
                            </div>
                            <div class="svc-card-body p-0">
                                <ul class="nav nav-pills px-3 pt-3 pb-2 gap-1 border-bottom" role="tablist">
                                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#t-basic">Basic SEO</a></li>
                                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#t-og">Open Graph</a></li>
                                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#t-twitter">Twitter</a></li>
                                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#t-tech">Technical</a></li>
                                </ul>
                                <div class="tab-content p-3">

                                    <!-- Basic SEO -->
                                    <div class="tab-pane fade show active" id="t-basic">
                                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-4">
                                            <div class="seo-ring">
                                                <svg viewBox="0 0 36 36">
                                                    <circle cx="18" cy="18" r="15.915" fill="none" stroke="#e9ecef" stroke-width="3"/>
                                                    <circle id="seoArc" cx="18" cy="18" r="15.915" fill="none" stroke="#0d6efd" stroke-width="3"
                                                        stroke-dasharray="0 100" stroke-linecap="round"/>
                                                </svg>
                                                <div class="rt"><span class="rn" id="seoScoreNum" style="color:#0d6efd;">0</span><span style="font-size:.55rem;color:#adb5bd;">/ 100</span></div>
                                            </div>
                                            <div style="flex:1;">
                                                <div class="fw-bold text-dark mb-1" style="font-size:.85rem;">SEO Health Score</div>
                                                <div id="seoChecklist" style="font-size:.75rem;"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Meta Title <span class="text-muted fw-normal text-lowercase">Recommended: 50–60 chars</span></label>
                                            <input type="text" name="meta_title" id="metaTitle" class="form-control"
                                                placeholder="Auto-fills from Service Title if blank" maxlength="70" value="<?= $p('meta_title') ?>">
                                            <div class="char-bar mt-2"><div class="char-bar-fill" id="mtBar" style="width:0%;background:#0d6efd;"></div></div>
                                            <div class="char-counter"><span>Characters</span><span class="cc" id="ccMT">0 / 70</span></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Meta Description <span class="text-muted fw-normal text-lowercase">Recommended: 140–160 chars</span></label>
                                            <textarea name="meta_description" id="metaDesc" class="form-control" rows="3"
                                                placeholder="Description shown in Google search results..." maxlength="180"><?= $p('meta_description') ?></textarea>
                                            <div class="char-bar mt-2"><div class="char-bar-fill" id="mdBar" style="width:0%;background:#0d6efd;"></div></div>
                                            <div class="char-counter"><span>Characters</span><span class="cc" id="ccMD">0 / 180</span></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Focus Keyword</label>
                                            <input type="text" name="focus_keyword" id="focusKw" class="form-control"
                                                placeholder="e.g. PCOD treatment Nagpur" value="<?= $p('focus_keyword') ?>">
                                            <div class="kw-tags" id="kwSuggestions"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Canonical URL <span class="text-muted fw-normal text-lowercase">(leave blank for auto)</span></label>
                                            <input type="text" name="canonical_url" class="form-control"
                                                placeholder="https://hpce.com/service/your-slug" value="<?= $p('canonical_url') ?>">
                                        </div>
                                        <label class="form-label">SERP Preview</label>
                                        <div class="serp-box shadow-sm">
                                            <div class="serp-url">hpce.com › service › <span id="serpSlug">your-slug</span></div>
                                            <div class="serp-title serp-ph" id="serpTitle">Meta title will appear here</div>
                                            <div class="serp-desc serp-ph" id="serpDesc">Meta description will appear here</div>
                                        </div>
                                    </div>

                                    <!-- Open Graph -->
                                    <div class="tab-pane fade" id="t-og">
                                        <div class="mb-4">
                                            <label class="form-label">OG Title</label>
                                            <input type="text" name="og_title" id="ogTitle" class="form-control"
                                                placeholder="Title shown when shared on Facebook / LinkedIn..." value="<?= $p('og_title') ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Description</label>
                                            <textarea name="og_description" id="ogDesc" class="form-control" rows="2"
                                                placeholder="Description shown on social media cards..."><?= $p('og_description') ?></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Type</label>
                                            <select name="og_type" class="form-select">
                                                <option value="website" <?= ($p('og_type') ?: 'website') === 'website' ? 'selected' : '' ?>>website</option>
                                                <option value="article" <?= $p('og_type') === 'article' ? 'selected' : '' ?>>article</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">OG Image <span class="text-muted fw-normal text-lowercase">(1200×630 recommended)</span></label>
                                            <!-- ✅ id="ogZone" -->
                                            <div class="upload-zone" id="ogZone">
                                                <div class="uz-icon"><i class="fa fa-image"></i></div>
                                                <p>Click or drag OG / Social image</p>
                                                <small class="text-muted" style="font-size:.72rem;">Falls back to Featured Image if left empty</small>
                                                <img id="ogPrev" class="uz-prev" alt="OG Preview">
                                            </div>
                                            <input type="file" name="og_image" id="ogInput" accept="image/*" class="d-none">
                                        </div>
                                        <label class="form-label"><i class="fab fa-facebook text-primary me-1"></i>Social Card Preview</label>
                                        <div class="og-card shadow-sm">
                                            <div class="og-img" id="ogImgBox"><span>No image selected</span></div>
                                            <div class="og-body">
                                                <div class="og-domain">hpce.com</div>
                                                <div class="og-title" id="ogCardTitle">OG Title will appear here</div>
                                                <div class="og-desc" id="ogCardDesc">OG description will appear here</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Twitter -->
                                    <div class="tab-pane fade" id="t-twitter">
                                        <div class="alert alert-info border-0 bg-info-subtle rounded-3 p-3 mb-4 d-flex gap-2 align-items-center">
                                            <i class="fa fa-info-circle text-info"></i>
                                            <small class="fw-medium">Leave blank to auto-inherit from Meta Title / Description on save.</small>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Title</label>
                                            <input type="text" name="twitter_title" class="form-control"
                                                placeholder="Title shown on Twitter/X card..." value="<?= $p('twitter_title') ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Twitter Description</label>
                                            <textarea name="twitter_description" class="form-control" rows="3"
                                                placeholder="Description shown on Twitter/X card..."><?= $p('twitter_description') ?></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Twitter Card Type</label>
                                            <select name="twitter_card" class="form-select">
                                                <option value="summary_large_image" selected>summary_large_image — Large image (Recommended)</option>
                                                <option value="summary">summary — Small thumbnail</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Technical -->
                                    <div class="tab-pane fade" id="t-tech">
                                        <?php
                                        $rb = explode(',', $p('robots_meta') ?: 'index,follow');
                                        $rI = trim($rb[0] ?? 'index');
                                        $rF = trim($rb[1] ?? 'follow');
                                        ?>
                                        <div class="mb-4">
                                            <label class="form-label">Robots Meta Tag</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="robots-grp" id="rgIndex">
                                                        <button type="button" class="robots-btn <?= $rI==='index'?'a-index':'' ?>" data-val="index" onclick="setRobot('i',this)">✅ INDEX</button>
                                                        <button type="button" class="robots-btn <?= $rI==='noindex'?'a-noindex':'' ?>" data-val="noindex" onclick="setRobot('i',this)">🚫 NOINDEX</button>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="robots-grp" id="rgFollow">
                                                        <button type="button" class="robots-btn <?= $rF==='follow'?'a-follow':'' ?>" data-val="follow" onclick="setRobot('f',this)">🔗 FOLLOW</button>
                                                        <button type="button" class="robots-btn <?= $rF==='nofollow'?'a-nofollow':'' ?>" data-val="nofollow" onclick="setRobot('f',this)">⛔ NOFOLLOW</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="robots_index" id="riVal" value="<?= htmlspecialchars($rI) ?>">
                                            <input type="hidden" name="robots_follow" id="rfVal" value="<?= htmlspecialchars($rF) ?>">
                                            <small class="fw-semibold mt-2 d-block text-success" id="robotsHint">✅ Indexed &amp; links followed.</small>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Schema / Structured Data Type</label>
                                            <div class="schema-opts">
                                                <?php
                                                $schemas   = ['MedicalProcedure','MedicalSpecialty','MedicalClinic','MedicalWebPage','Service'];
                                                $curSchema = $p('schema_type') ?: 'MedicalProcedure';
                                                foreach ($schemas as $sc):
                                                ?>
                                                <button type="button" class="schema-btn <?= $curSchema===$sc?'active':'' ?>" onclick="setSchema('<?= $sc ?>',this)"><?= $sc ?></button>
                                                <?php endforeach; ?>
                                            </div>
                                            <input type="hidden" name="schema_type" id="schemaVal" value="<?= htmlspecialchars($curSchema) ?>">
                                        </div>
                                        <div class="p-3 bg-light rounded-3 border">
                                            <label class="form-label text-dark mb-2">Keyword Density Analyser</label>
                                            <div id="kdResults" class="text-muted" style="font-size:.8rem;">Set a focus keyword above and write content to see analysis.</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div><!-- /.col left -->

                    <!-- ════════════ RIGHT COLUMN ════════════ -->
                    <div class="col-xl-4 col-lg-5">

                        <!-- Publish Settings -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-success-subtle text-success"><i class="fa fa-paper-plane"></i></div>
                                <h6 class="svc-card-title">Publish Settings</h6>
                            </div>
                            <div class="svc-card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_published"
    id="isPublished" <?= (!isset($_POST['is_published']) || !empty($_POST['is_published'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="isPublished">
                                        <span class="pub-badge badge" id="pubBadge" style="background:#e9ecef;color:#6c757d;">Draft</span>
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" min="0" value="<?= $p('sort_order') ?: 0 ?>">
                                    <small class="text-muted" style="font-size:.72rem;">Lower number = higher in listing</small>
                                </div>
                              <div class="mb-3">
    <label class="form-label">Icon Class <span class="text-muted fw-normal text-lowercase">(FontAwesome)</span></label>
    <div class="input-group">
        <span class="input-group-text" id="iconPreviewBox" style="min-width:42px;justify-content:center;">
            <i id="iconPreviewEl" class="fa fa-star" style="font-size:1rem;color:#6c757d;"></i>
        </span>
        <input type="text" name="icon" id="iconInput" class="form-control"
            placeholder="e.g. fa fa-heartbeat" value="<?= $p('icon') ?>"
            autocomplete="off">
        <button type="button" class="btn btn-outline-primary" id="btnOpenIconPicker" title="Browse Icons">
            <i class="fa fa-th"></i> Browse
        </button>
    </div>
    <small class="text-muted d-block mt-1" style="font-size:.72rem;">
        Type manually or click Browse to search all icons.
    </small>
</div>
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select">
                                        <option value="">— Select Category —</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $p('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Related Services</label>
                                    <select name="related_services[]" class="form-control select2-multi" multiple>
                                        <?php foreach ($all_services as $sv): ?>
                                        <option value="<?= htmlspecialchars($sv['slug']) ?>"><?= htmlspecialchars($sv['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted" style="font-size:.72rem;">Search & select multiple</small>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow">
                                        <i class="fa fa-save me-2"></i>Save Service
                                    </button>
                                    <a href="./" class="btn btn-light border rounded-pill fw-semibold">
                                        <i class="fa fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-secondary-subtle text-secondary"><i class="fa fa-image"></i></div>
                                <h6 class="svc-card-title">Featured Image</h6>
                            </div>
                            <div class="svc-card-body">
                                <!-- ✅ id="imgZone" — NO onclick -->
                                <div class="upload-zone" id="imgZone">
                                    <div class="uz-icon"><i class="fa fa-upload"></i></div>
                                    <p>Click or drag Featured Image</p>
                                    <small class="text-muted" style="font-size:.72rem;">JPG, PNG, WebP — max 2MB — auto-converted to WebP</small>
                                    <img id="imgPrev" class="uz-prev" alt="Image Preview">
                                </div>
                                <input type="file" name="image" id="imgInput" accept="image/*" class="d-none">
                                <div class="mt-3">
                                    <label class="form-label">Image Alt Text</label>
                                    <input type="text" name="image_alt" class="form-control"
                                        placeholder="Descriptive alt text for accessibility & SEO" value="<?= $p('image_alt') ?>">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="use_image_as_hero" id="useImgAsHero">
                                    <label class="form-check-label" for="useImgAsHero" style="font-size:.78rem;">Use this as Hero background too</label>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery -->
                        <div class="svc-card">
                            <div class="svc-card-header">
                                <div class="svc-card-icon bg-secondary-subtle text-secondary"><i class="fa fa-images"></i></div>
                                <h6 class="svc-card-title">Gallery Images</h6>
                            </div>
                            <div class="svc-card-body">
                                <!-- ✅ id="galleryZone" -->
                                <div class="upload-zone" id="galleryZone">
                                    <div class="uz-icon"><i class="fa fa-images"></i></div>
                                    <p>Click or drag Gallery Images</p>
                                    <small class="text-muted" style="font-size:.72rem;">Multiple files — auto-converted to WebP</small>
                                </div>
                                <input type="file" id="galleryPicker" accept="image/*" class="d-none" multiple>
                                <input type="file" name="gallery_images[]" id="galleryInput" accept="image/*" class="d-none" multiple>
                                <div class="row g-2 mt-2" id="galleryPreview"></div>
                            </div>
                        </div>

                    </div><!-- /.col right -->
                </div><!-- /.row -->
            </form>

        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     ICON PICKER MODAL
══════════════════════════════════════════ -->
<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#0d6efd,#6610f2);color:#fff;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-icons fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="iconPickerLabel">FontAwesome Icon Picker</h5>
                        <small style="opacity:.8;">Click any icon to select it</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <!-- Search bar -->
                <div class="p-3 border-bottom bg-light sticky-top" style="z-index:10;">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                                <input type="text" id="iconSearch" class="form-control border-start-0"
                                    placeholder="Search icons... e.g. heart, doctor, phone"
                                    style="font-size:.9rem;">
                                <button type="button" class="btn btn-light border" id="iconSearchClear" title="Clear">
                                    <i class="fa fa-times text-muted"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex gap-2 flex-wrap align-items-center">
                            <span class="text-muted small"><i class="fa fa-filter me-1"></i>Category:</span>
                            <div id="iconCatBtns" class="d-flex flex-wrap gap-1"></div>
                        </div>
                    </div>
                    <!-- Selected preview bar -->
                    <div class="d-flex align-items-center gap-3 mt-2 p-2 rounded-3 border" id="iconSelectedBar" style="background:#fff;display:none!important;">
                        <div style="width:36px;height:36px;background:#e7f1ff;border-radius:.4rem;display:flex;align-items:center;justify-content:center;">
                            <i id="iconBarPreview" class="fa fa-star" style="font-size:1.1rem;color:#0d6efd;"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:.7rem;color:#adb5bd;">Selected</div>
                            <div id="iconBarClass" class="fw-bold text-dark" style="font-size:.85rem;font-family:monospace;">—</div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" id="btnConfirmIcon" data-bs-dismiss="modal">
                            <i class="fa fa-check me-1"></i> Use Icon
                        </button>
                    </div>
                </div>

                <!-- Icon Count -->
                <div class="px-3 pt-2 pb-1 d-flex align-items-center justify-content-between">
                    <small class="text-muted"><span id="iconCountShown">0</span> icons shown</small>
                    <small class="text-muted" style="font-size:.7rem;">FontAwesome Free 6.x</small>
                </div>

                <!-- Grid -->
                <div id="iconGrid" class="p-3" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:6px;"></div>

                <div id="iconNoResult" class="text-center py-5 text-muted d-none">
                    <i class="fa fa-search-minus" style="font-size:3rem;opacity:.2;"></i>
                    <p class="mt-2 fw-semibold">No icons match "<span id="iconNoQ"></span>"</p>
                    <small>Try a different keyword</small>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- SCRIPTS — load order matters -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// ══════════════════════════════════════════════
// SAFE HELPER — get element, log if missing
// ══════════════════════════════════════════════
function el(id) {
    var e = document.getElementById(id);
    if (!e) console.warn('[ServiceAdd] Element not found: #' + id);
    return e;
}

// ══════════════════════════════════════════════
// 1. QUILL EDITOR
// ══════════════════════════════════════════════
var quill = null;
var quillEl = el('quillEditor');
if (quillEl) {
    quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Write detailed service description here...',
        modules: {
            toolbar: [
                [{ header: [1,2,3,4,5,6,false] }],
                [{ 'align': [] }], // ◄ Added this line explicitly to enable justify dropdown
                ['bold','italic','underline','strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ list:'ordered' },{ list:'bullet' }],
                ['blockquote','link'],
                ['clean']
            ]
        }
    });


    var savedContent = el('svcContent') ? el('svcContent').value : '';
    if (savedContent) quill.clipboard.dangerouslyPasteHTML(savedContent);

    quill.on('text-change', function () {
        if (el('svcContent')) el('svcContent').value = quill.root.innerHTML;
        var words = quill.getText().trim().split(/\s+/).filter(Boolean).length;
        var badge = el('readBadge');
        if (badge) {
            badge.style.display = words > 0 ? '' : 'none';
            if (el('readText')) el('readText').textContent = '~' + Math.ceil(words/200) + ' min read';
        }
        updateSEO();
        updateKD();
    });
}

// ══════════════════════════════════════════════
// 2. SELECT2 — only if jQuery loaded
// ══════════════════════════════════════════════
try {
    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        jQuery('.select2-multi').select2({
            placeholder: 'Search & select related services...',
            allowClear: true
        });
    }
} catch(e) { console.warn('[Select2 init failed]', e.message); }

// ══════════════════════════════════════════════
// 3. SLUG
// ══════════════════════════════════════════════
function slugify(s) {
    return s.toLowerCase()
        .replace(/[^a-z0-9\s-]/g,'')
        .trim()
        .replace(/[\s-]+/g,'-')
        .replace(/^-+|-+$/g,'');
}

var svcTitle = el('svcTitle');
var svcSlug  = el('svcSlug');
var manualSlug = false;

if (svcTitle) {
    svcTitle.oninput = function () {
        if (el('ccTitle')) el('ccTitle').textContent = this.value.length + ' chars';
        if (!manualSlug && svcSlug) svcSlug.value = slugify(this.value);
        updateSEO();
    };
}
if (svcSlug) {
    svcSlug.oninput = function () {
        manualSlug = true;
        this.value = slugify(this.value);
        updateSEO();
    };
}
if (el('btnGenSlug')) {
    el('btnGenSlug').onclick = function () {
        if (svcSlug && svcTitle) { svcSlug.value = slugify(svcTitle.value); manualSlug = false; updateSEO(); }
    };
}
if (el('svcExcerpt')) {
    el('svcExcerpt').oninput = function () {
        if (el('ccExcerpt')) el('ccExcerpt').textContent = this.value.length + ' chars';
    };
}

// ══════════════════════════════════════════════
// 4. PUBLISH BADGE
// ══════════════════════════════════════════════
function updatePubBadge(chk) {
    var b = el('pubBadge');
    if (!b) return;
    if (chk.checked) { b.style.cssText='background:#d1e7dd;color:#0a3622;'; b.textContent='Published'; }
    else             { b.style.cssText='background:#e9ecef;color:#6c757d;'; b.textContent='Draft'; }
}
var pubChk = el('isPublished');
if (pubChk) {
    pubChk.onchange = function () { updatePubBadge(this); };
    updatePubBadge(pubChk);
}

// ══════════════════════════════════════════════
// 5. IMAGE UPLOAD → WebP CANVAS PREVIEW
// ══════════════════════════════════════════════
function convertAndPreview(file, imgEl, boxEl, quality) {
    if (!file || !imgEl) return;
    quality = quality || 0.85;
    var reader = new FileReader();
    reader.onload = function (ev) {
        var img = new Image();
        img.onload = function () {
            var canvas  = document.createElement('canvas');
            canvas.width  = img.width;
            canvas.height = img.height;
            canvas.getContext('2d').drawImage(img, 0, 0);
            var url = canvas.toDataURL('image/webp', quality);
            imgEl.src = url;
            imgEl.style.display = 'block';
            if (boxEl) boxEl.innerHTML = '<img src="'+url+'" style="width:100%;height:100%;object-fit:cover;">';
            // WebP size hint
            var kb   = Math.round((url.length * 3) / 4 / 1024);
            var hint = imgEl.parentElement.querySelector('.uz-webp-hint');
            if (!hint) {
                hint = document.createElement('small');
                hint.className = 'uz-webp-hint text-success d-block mt-1';
                imgEl.parentElement.appendChild(hint);
            }
            hint.innerHTML = '<i class="fa fa-check-circle me-1"></i>WebP preview — ~' + kb + ' KB';
        };
        img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
}

function bindZone(zoneId, inputId, prevId, boxId, quality) {
    var zone  = el(zoneId);
    var input = el(inputId);
    var prev  = el(prevId);
    var box   = boxId ? el(boxId) : null;
    if (!zone || !input) return;

    zone.onclick  = function () { input.click(); };
    zone.ondragover = function (e) { e.preventDefault(); this.style.borderColor='#0d6efd'; this.style.background='#f0f7ff'; };
    zone.ondragleave= function ()  { this.style.borderColor=''; this.style.background=''; };
    zone.ondrop     = function (e) {
        e.preventDefault(); this.style.borderColor=''; this.style.background='';
        if (e.dataTransfer.files[0]) {
            try { var dt=new DataTransfer(); dt.items.add(e.dataTransfer.files[0]); input.files=dt.files; } catch(ex){}
            convertAndPreview(e.dataTransfer.files[0], prev, box, quality);
        }
    };
    input.onchange = function () {
        if (this.files && this.files[0]) convertAndPreview(this.files[0], prev, box, quality);
    };
}

bindZone('imgZone',     'imgInput',     'imgPrev',     null,       0.85);
bindZone('heroZone',    'heroInput',    'heroPrev',    null,       0.85);
bindZone('ogZone',      'ogInput',      'ogPrev',      'ogImgBox', 0.80);
bindZone('hcImgZone',   'hcImgInput',   'hcImgPrev',   null,       0.85);
bindZone('scThumbZone', 'scThumbInput', 'scThumbPrev', null,       0.85);

// Gallery — accumulating multi-file with individual remove
var galleryFiles = []; // tracks all selected File objects
var galleryZone   = el('galleryZone');
var galleryPicker = el('galleryPicker'); // UI trigger only — safe to reset
var galleryInput  = el('galleryInput'); // form submit input — rebuilt via DataTransfer, never reset

function rebuildGalleryInput() {
    if (!galleryInput) return;
    try {
        var dt = new DataTransfer();
        galleryFiles.forEach(function(f){ dt.items.add(f); });
        galleryInput.files = dt.files; // never reset this input after setting
    } catch(ex) { console.warn('[Gallery] DataTransfer not supported', ex); }
}

function renderGalleryPreviews() {
    var wrap = el('galleryPreview');
    if (!wrap) return;
    wrap.innerHTML = '';
    if (galleryFiles.length === 0) return;
    galleryFiles.forEach(function(file, idx) {
        var reader = new FileReader();
        reader.onload = function(ev) {
            var img = new Image();
            img.onload = function() {
                var canvas = document.createElement('canvas');
                canvas.width = img.width; canvas.height = img.height;
                canvas.getContext('2d').drawImage(img, 0, 0);
                var url = canvas.toDataURL('image/webp', 0.80);
                var col = document.createElement('div');
                col.className = 'col-4';
                col.setAttribute('data-gallery-idx', idx);
                col.innerHTML =
                    '<div class="gallery-thumb-wrap">' +
                    '<img src="' + url + '" alt="">' +
                    '<button type="button" class="gallery-thumb-remove" data-idx="' + idx + '" title="Remove"><i class="fa fa-times"></i></button>' +
                    '<span class="gallery-thumb-name">' + file.name + '</span>' +
                    '</div>';
                col.querySelector('.gallery-thumb-remove').onclick = function() {
                    var i = parseInt(this.getAttribute('data-idx'));
                    galleryFiles.splice(i, 1);
                    rebuildGalleryInput();
                    renderGalleryPreviews();
                };
                wrap.appendChild(col);
            };
            img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function addFilesToGallery(files) {
    Array.from(files).forEach(function(f){ galleryFiles.push(f); });
    rebuildGalleryInput();
    renderGalleryPreviews();
}

if (galleryZone && galleryPicker) {
    galleryZone.onclick    = function () { galleryPicker.click(); }; // open PICKER not submit input
    galleryZone.ondragover = function (e){ e.preventDefault(); this.style.borderColor='#0d6efd'; this.style.background='#f0f7ff'; };
    galleryZone.ondragleave= function ()  { this.style.borderColor=''; this.style.background=''; };
    galleryZone.ondrop     = function (e) {
        e.preventDefault(); this.style.borderColor=''; this.style.background='';
        if (e.dataTransfer.files.length) addFilesToGallery(e.dataTransfer.files);
    };
    // Picker onchange: accumulate then reset PICKER (safe — galleryInput is the real submit input)
    galleryPicker.onchange = function () {
        if (this.files && this.files.length) addFilesToGallery(this.files);
        this.value = ''; // safe: resets only the picker, not galleryInput
    };
}

// ══════════════════════════════════════════════
// 6. SEO
// ══════════════════════════════════════════════
var metaTitleEl = el('metaTitle');
var metaDescEl  = el('metaDesc');
var focusKwEl   = el('focusKw');

if (metaTitleEl) {
    metaTitleEl.oninput = function () {
        var l = this.value.length;
        var cc = el('ccMT'); if(cc){ cc.textContent=l+' / 70'; cc.className='cc '+(l<30?'cc-bad':l<=60?'cc-ok':'cc-warn'); }
        var bar = el('mtBar'); if(bar) bar.style.cssText='width:'+Math.min(100,(l/70)*100)+'%;background:'+(l<=60?'#198754':'#dc3545')+';';
        updateSEO();
    };
}
if (metaDescEl) {
    metaDescEl.oninput = function () {
        var l = this.value.length;
        var cc = el('ccMD'); if(cc){ cc.textContent=l+' / 180'; cc.className='cc '+(l<80?'cc-bad':l<=160?'cc-ok':'cc-warn'); }
        var bar = el('mdBar'); if(bar) bar.style.cssText='width:'+Math.min(100,(l/180)*100)+'%;background:'+(l<=160?'#198754':'#dc3545')+';';
        updateSEO();
    };
}
if (focusKwEl) {
    focusKwEl.oninput = function () {
        var kw  = this.value.trim();
        var sug = el('kwSuggestions');
        if (!sug) return;
        sug.innerHTML = '';
        if (kw.length > 2) {
            [kw+' Nagpur', kw+' treatment', 'best '+kw, kw+' hospital', kw+' doctor'].forEach(function(v){
                var tag = document.createElement('span');
                tag.className = 'kw-tag'; tag.textContent = v;
                tag.onclick   = function(){ focusKwEl.value=v; sug.innerHTML=''; updateSEO(); updateKD(); };
                sug.appendChild(tag);
            });
        }
        updateSEO(); updateKD();
    };
}
if (el('ogTitle')) el('ogTitle').oninput = function(){ var t=el('ogCardTitle'); if(t) t.textContent=this.value||'OG Title here'; };
if (el('ogDesc'))  el('ogDesc').oninput  = function(){ var d=el('ogCardDesc');  if(d) d.textContent=this.value||'OG description here'; };

function updateSEO() {
    var title   = svcTitle   ? svcTitle.value.trim()   : '';
    var slug    = svcSlug    ? svcSlug.value.trim()    : '';
    var mt      = metaTitleEl? metaTitleEl.value.trim(): '';
    var md      = metaDescEl ? metaDescEl.value.trim() : '';
    var kw      = focusKwEl  ? focusKwEl.value.trim().toLowerCase() : '';
    var content = quill      ? quill.getText() : '';
    var score=0, items=[];

    var st=el('serpTitle'), sd=el('serpDesc'), ss=el('serpSlug');
    if(st){ st.textContent=mt||title||'— no title —'; st.className='serp-title'+((!mt&&!title)?' serp-ph':''); }
    if(sd){ sd.textContent=md||'— no description —'; sd.className='serp-desc'+(!md?' serp-ph':''); }
    if(ss)  ss.textContent=slug||'your-slug';

    if(title)                                    {score+=20;items.push({c:'ok',  t:'Title is set'});}
    else                                         {          items.push({c:'bad', t:'Title missing'});}
    if(mt&&mt.length>=30&&mt.length<=60)         {score+=20;items.push({c:'ok',  t:'Meta title OK ('+mt.length+' chars)'});}
    else if(mt)                                  {score+=10;items.push({c:'warn',t:'Meta title: '+mt.length+' chars (aim 30–60)'});}
    else                                         {          items.push({c:'bad', t:'Meta title not set'});}
    if(md&&md.length>=80&&md.length<=160)        {score+=20;items.push({c:'ok',  t:'Meta description OK ('+md.length+' chars)'});}
    else if(md)                                  {score+=10;items.push({c:'warn',t:'Meta desc: '+md.length+' chars (aim 80–160)'});}
    else                                         {          items.push({c:'bad', t:'Meta description not set'});}
    if(kw&&title.toLowerCase().indexOf(kw)>-1)  {score+=20;items.push({c:'ok',  t:'Keyword in title ✅'});}
    else if(kw)                                  {          items.push({c:'warn',t:'Keyword not in title'});}
    var wc=content.trim().split(/\s+/).filter(Boolean).length;
    if(wc>200){score+=20;items.push({c:'ok', t:'Content OK ('+wc+' words)'});}
    else      {          items.push({c:'warn',t:'Content short ('+wc+' words — aim 200+)'});}

    var arc=el('seoArc'),num=el('seoScoreNum');
    var col=score>=80?'#198754':score>=50?'#ffc107':'#dc3545';
    if(arc){arc.setAttribute('stroke-dasharray',score+' '+(100-score));arc.setAttribute('stroke',col);}
    if(num){num.textContent=score;num.style.color=col;}
    var cl=el('seoChecklist');
    if(cl){cl.innerHTML='';items.forEach(function(it){cl.innerHTML+='<div class="seo-item"><span class="seo-dot dot-'+it.c+'"></span>'+it.t+'</div>';});}
}

function updateKD() {
    var kw  = focusKwEl ? focusKwEl.value.trim().toLowerCase() : '';
    var txt = quill ? quill.getText().toLowerCase() : '';
    var res = el('kdResults');
    if (!res) return;
    if (!kw || !txt.trim()) { res.innerHTML='<span class="text-muted">Set a focus keyword and write content.</span>'; return; }
    var words=txt.trim().split(/\s+/).filter(Boolean), count=0;
    words.forEach(function(w){ if(w.replace(/[^a-z0-9]/g,'').indexOf(kw)>-1) count++; });
    var density=words.length>0?((count/words.length)*100).toFixed(2):0;
    var cls=density<0.5?'text-danger':density<=2.5?'text-success':'text-warning';
    var note=density<0.5?'Too low':'density>2.5'?'Too high':'Good range ✅';
    res.innerHTML='<div class="kd-bar"><div class="kd-fill" style="width:'+Math.min(100,density*20)+'%;"></div></div>'+
        '<span class="'+cls+'"><strong>'+density+'%</strong> — '+count+'/'+words.length+' words</span>';
}

// ══════════════════════════════════════════════
// 7. ROBOTS & SCHEMA
// ══════════════════════════════════════════════
window.setRobot = function(type, btn) {
    var grp=el(type==='i'?'rgIndex':'rgFollow'), inp=el(type==='i'?'riVal':'rfVal');
    if(!grp||!inp) return;
    grp.querySelectorAll('.robots-btn').forEach(function(b){b.className='robots-btn';});
    var val=btn.dataset.val;
    if(val==='index')    btn.classList.add('a-index');
    else if(val==='noindex')  btn.classList.add('a-noindex');
    else if(val==='follow')   btn.classList.add('a-follow');
    else if(val==='nofollow') btn.classList.add('a-nofollow');
    inp.value=val;
    var ri=el('riVal').value, rf=el('rfVal').value, hint=el('robotsHint');
    if(hint){
        if(ri==='index'&&rf==='follow'){hint.textContent='✅ Page indexed & links followed.';hint.className='fw-semibold mt-2 d-block text-success';}
        else if(ri==='noindex'){hint.textContent='🚫 Page will NOT be indexed.';hint.className='fw-semibold mt-2 d-block text-danger';}
        else{hint.textContent='⛔ Links will NOT be followed.';hint.className='fw-semibold mt-2 d-block text-warning';}
    }
};
window.setSchema = function(val, btn) {
    document.querySelectorAll('.schema-btn').forEach(function(b){b.classList.remove('active');});
    btn.classList.add('active');
    var sv=el('schemaVal'); if(sv) sv.value=val;
};

// ══════════════════════════════════════════════
// BUILDER UTILITIES
// ══════════════════════════════════════════════
function toggleEmpty(wrapId, emptyId) {
    var w=el(wrapId), e=el(emptyId);
    if(w&&e) e.classList.toggle('show', w.children.length===0);
}
function renumberAll() {
    [{wrap:'sectionsWrap',sel:'.section-row-num'},{wrap:'faqsWrap',sel:'.faq-row-num'},
     {wrap:'wcWrap',sel:'.wc-row-num'},{wrap:'hcFeaturesWrap',sel:'.hcf-row-num'}
    ].forEach(function(cfg){
        var w=el(cfg.wrap); if(!w) return;
        w.querySelectorAll(cfg.sel).forEach(function(n,i){n.textContent=i+1;});
    });
}

// ══════════════════════════════════════════════
// 8. ADD SECTION BUTTON
// ══════════════════════════════════════════════
var btnAddSection = el('btnAddSection');
if (btnAddSection) {
    btnAddSection.onclick = function () {
        var uid  = 'S' + Date.now();
        var wrap = el('sectionsWrap');
        var num  = wrap ? wrap.children.length + 1 : 1;
        var row  = document.createElement('div');
        row.className = 'section-row';
        row.setAttribute('draggable','true');
        row.innerHTML =
            '<div class="section-row-header js-sec-hdr" style="cursor:pointer;">' +
                '<span class="drag-handle"><i class="fa fa-grip-vertical"></i></span>' +
                '<span class="section-row-num">'+num+'</span>' +
                '<span class="section-row-label placeholder" id="lbl'+uid+'">Untitled Section</span>' +
                '<div class="section-row-actions">' +
                    '<button type="button" class="sec-collapse-btn js-toggle-sec" style="background:none;border:none;color:#6c757d;cursor:pointer;padding:3px 6px;"><i class="fa fa-chevron-up"></i></button>' +
                    '<button type="button" class="sec-delete-btn js-del-sec" style="background:#fff0f0;border:1px solid #ffc9c9;color:#dc3545;font-size:.7rem;padding:3px 8px;border-radius:.3rem;cursor:pointer;">'+
                        '<i class="fa fa-trash"></i> Remove</button>' +
                '</div>' +
            '</div>' +
            '<div class="section-row-body" id="body'+uid+'">' +
                '<div style="display:flex;gap:4px;margin-bottom:1rem;background:#f1f3f5;border-radius:.5rem;padding:4px;">' +
                    '<button type="button" class="sec-tab-btn active" style="flex:1;padding:5px 8px;border:none;border-radius:.35rem;font-size:.72rem;font-weight:700;cursor:pointer;background:#fff;color:#0d6efd;" data-target="h2'+uid+'"><i class="fa fa-heading me-1"></i>Heading</button>' +
                    '<button type="button" class="sec-tab-btn" style="flex:1;padding:5px 8px;border:none;border-radius:.35rem;font-size:.72rem;font-weight:700;cursor:pointer;background:none;color:#6c757d;" data-target="cnt'+uid+'"><i class="fa fa-paragraph me-1"></i>Content</button>' +
                    '<button type="button" class="sec-tab-btn" style="flex:1;padding:5px 8px;border:none;border-radius:.35rem;font-size:.72rem;font-weight:700;cursor:pointer;background:none;color:#6c757d;" data-target="lst'+uid+'"><i class="fa fa-list me-1"></i>List Items</button>' +
                '</div>' +
                '<div id="h2'+uid+'" style="display:block;">' +
                    '<input type="text" name="sec_h2[]" class="form-control" placeholder="Section heading (H3)">' +
                '</div>' +
                '<div id="cnt'+uid+'" style="display:none;">' +
                    '<textarea name="sec_content[]" class="form-control" rows="4" placeholder="Section paragraph content..."></textarea>' +
                '</div>' +
                '<div id="lst'+uid+'" style="display:none;">' +
                    '<textarea name="sec_list[]" class="form-control" rows="5" placeholder="One list item per line&#10;e.g.&#10;Advanced equipment&#10;Expert surgeons"></textarea>' +
                    '<small class="text-muted d-block mt-1" style="font-size:.72rem;">Each new line = one bullet point.</small>' +
                '</div>' +
            '</div>';

        // Live label
        row.querySelector('input[name="sec_h2[]"]').oninput = function(){
            var lbl=el('lbl'+uid); if(!lbl) return;
            lbl.textContent=this.value||'Untitled Section';
            lbl.classList.toggle('placeholder',!this.value);
        };

        // Tab switching
        row.querySelectorAll('.sec-tab-btn').forEach(function(btn){
            btn.onclick = function(){
                row.querySelectorAll('.sec-tab-btn').forEach(function(b){
                    b.style.background='none'; b.style.color='#6c757d';
                });
                this.style.background='#fff'; this.style.color='#0d6efd';
                ['h2'+uid,'cnt'+uid,'lst'+uid].forEach(function(id){
                    var p=el(id); if(p) p.style.display='none';
                });
                var target=el(this.dataset.target); if(target) target.style.display='block';
            };
        });

        // Toggle collapse
        var toggleCollapse = function(){
            var body=el('body'+uid); if(!body) return;
            var collapsed=body.style.display==='none';
            body.style.display=collapsed?'block':'none';
            var icon=row.querySelector('.js-toggle-sec i');
            if(icon) icon.className=collapsed?'fa fa-chevron-up':'fa fa-chevron-down';
        };
        row.querySelector('.js-toggle-sec').onclick = function(e){ e.stopPropagation(); toggleCollapse(); };
        row.querySelector('.section-row-header').onclick = function(e){
            if(e.target.closest('.js-del-sec')||e.target.closest('.js-toggle-sec')) return;
            toggleCollapse();
        };

        // Delete
        row.querySelector('.js-del-sec').onclick = function(e){
            e.stopPropagation();
            row.remove();
            toggleEmpty('sectionsWrap','sectionsEmpty');
            renumberAll();
        };

        if (wrap) wrap.appendChild(row);
        toggleEmpty('sectionsWrap','sectionsEmpty');
        row.querySelector('input[name="sec_h2[]"]').focus();
        initDrag();
    };
} else {
    console.error('[ServiceAdd] #btnAddSection NOT FOUND in DOM');
}

// ══════════════════════════════════════════════
// 9. ADD FAQ BUTTON
// ══════════════════════════════════════════════
var btnAddFaq = el('btnAddFaq');
if (btnAddFaq) {
    btnAddFaq.onclick = function () {
        var uid  = 'F' + Date.now();
        var wrap = el('faqsWrap');
        var num  = wrap ? wrap.children.length + 1 : 1;
        var row  = document.createElement('div');
        row.className = 'faq-row';
        row.innerHTML =
            '<div class="faq-row-header" style="display:flex;align-items:center;gap:10px;padding:.75rem 1rem;cursor:pointer;background:#fffbf0;border-bottom:1.5px solid #e9ecef;">' +
                '<span class="faq-row-num" style="width:22px;height:22px;background:#fd7e14;color:#fff;border-radius:50%;font-size:.65rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">'+num+'</span>' +
                '<span class="faq-q-preview placeholder" id="lbl'+uid+'" style="flex:1;font-size:.82rem;font-weight:600;color:#adb5bd;font-style:italic;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">Untitled Question</span>' +
                '<button type="button" class="sec-delete-btn js-del-faq" style="background:#fff0f0;border:1px solid #ffc9c9;color:#dc3545;font-size:.7rem;padding:3px 8px;border-radius:.3rem;cursor:pointer;flex-shrink:0;"><i class="fa fa-trash"></i> Remove</button>' +
            '</div>' +
            '<div class="faq-row-body" id="body'+uid+'" style="padding:1.25rem;">' +
                '<div class="mb-3">' +
                    '<label class="form-label">Question <span class="text-danger">*</span></label>' +
                    '<input type="text" name="faq_q[]" class="form-control" placeholder="e.g. What is the recovery time after surgery?">' +
                '</div>' +
                '<div class="mb-0">' +
                    '<label class="form-label">Answer <span class="text-danger">*</span></label>' +
                    '<textarea name="faq_a[]" class="form-control" rows="3" placeholder="Provide a clear, concise answer..."></textarea>' +
                '</div>' +
            '</div>';

        // Live label
        row.querySelector('input[name="faq_q[]"]').oninput = function(){
            var lbl=el('lbl'+uid); if(!lbl) return;
            lbl.textContent=this.value||'Untitled Question';
            lbl.style.color=this.value?'#212529':'#adb5bd';
            lbl.style.fontStyle=this.value?'normal':'italic';
        };

        // Toggle collapse on header
        row.querySelector('.faq-row-header').onclick = function(e){
            if(e.target.closest('.js-del-faq')) return;
            var body=el('body'+uid); if(!body) return;
            body.style.display=body.style.display==='none'?'block':'none';
        };

        // Delete
        row.querySelector('.js-del-faq').onclick = function(e){
            e.stopPropagation();
            row.remove();
            toggleEmpty('faqsWrap','faqsEmpty');
            renumberAll();
        };

        if (wrap) wrap.appendChild(row);
        toggleEmpty('faqsWrap','faqsEmpty');
        row.querySelector('input[name="faq_q[]"]').focus();
    };
} else {
    console.error('[ServiceAdd] #btnAddFaq NOT FOUND in DOM');
}

// ══════════════════════════════════════════════
// 10. ADD WHY CHOOSE POINT BUTTON
// ══════════════════════════════════════════════
var btnAddWC = el('btnAddWC');
if (btnAddWC) {
    btnAddWC.onclick = function () {
        var uid  = 'W' + Date.now();
        var wrap = el('wcWrap');
        var num  = wrap ? wrap.children.length + 1 : 1;
        var row  = document.createElement('div');
        row.className = 'wc-row';
        row.innerHTML =
            '<div class="wc-row-header" style="display:flex;align-items:center;gap:10px;padding:.75rem 1rem;cursor:pointer;background:#f0fff4;border-bottom:1.5px solid #e9ecef;">' +
                '<span class="wc-row-num" style="width:22px;height:22px;background:#198754;color:#fff;border-radius:50%;font-size:.65rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">'+num+'</span>' +
                '<span class="wc-title-preview placeholder" id="lbl'+uid+'" style="flex:1;font-size:.82rem;font-weight:600;color:#adb5bd;font-style:italic;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">Untitled Point</span>' +
                '<button type="button" class="sec-delete-btn js-del-wc" style="background:#fff0f0;border:1px solid #ffc9c9;color:#dc3545;font-size:.7rem;padding:3px 8px;border-radius:.3rem;cursor:pointer;flex-shrink:0;"><i class="fa fa-trash"></i> Remove</button>' +
            '</div>' +
            '<div class="wc-row-body" id="body'+uid+'" style="padding:1.25rem;">' +
                '<div class="mb-3">' +
                    '<label class="form-label">Point Title <span class="text-danger">*</span></label>' +
                    '<input type="text" name="wc_title[]" class="form-control" placeholder="e.g. Expert Laparoscopic Surgeons">' +
                '</div>' +
                '<div class="mb-0">' +
                    '<label class="form-label">Description</label>' +
                    '<textarea name="wc_desc[]" class="form-control" rows="2" placeholder="Brief description of this benefit..."></textarea>' +
                '</div>' +
            '</div>';

        // Live label
        row.querySelector('input[name="wc_title[]"]').oninput = function(){
            var lbl=el('lbl'+uid); if(!lbl) return;
            lbl.textContent=this.value||'Untitled Point';
            lbl.style.color=this.value?'#212529':'#adb5bd';
            lbl.style.fontStyle=this.value?'normal':'italic';
        };

        // Toggle collapse
        row.querySelector('.wc-row-header').onclick = function(e){
            if(e.target.closest('.js-del-wc')) return;
            var body=el('body'+uid); if(!body) return;
            body.style.display=body.style.display==='none'?'block':'none';
        };

        // Delete
        row.querySelector('.js-del-wc').onclick = function(e){
            e.stopPropagation();
            row.remove();
            toggleEmpty('wcWrap','wcEmpty');
            renumberAll();
        };

        if (wrap) wrap.appendChild(row);
        toggleEmpty('wcWrap','wcEmpty');
        row.querySelector('input[name="wc_title[]"]').focus();
    };
} else {
    console.error('[ServiceAdd] #btnAddWC NOT FOUND in DOM');
}

// ══════════════════════════════════════════════
// 11. ADD HERO FEATURE BUTTON
// ══════════════════════════════════════════════
var btnAddHcFeature = el('btnAddHcFeature');
if (btnAddHcFeature) {
    btnAddHcFeature.onclick = function () {
        var uid  = 'H' + Date.now();
        var wrap = el('hcFeaturesWrap');
        var num  = wrap ? wrap.children.length + 1 : 1;
        var row  = document.createElement('div');
        row.className = 'hcf-row';
        row.innerHTML =
            '<div class="hcf-row-header" style="display:flex;align-items:center;gap:10px;padding:.65rem 1rem;cursor:pointer;background:#f0f4ff;border-bottom:1.5px solid #e9ecef;">' +
                '<span class="hcf-row-num" style="width:22px;height:22px;background:#6610f2;color:#fff;border-radius:50%;font-size:.65rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">'+num+'</span>' +
                '<span class="hcf-title-preview placeholder" id="lbl'+uid+'" style="flex:1;font-size:.82rem;font-weight:600;color:#adb5bd;font-style:italic;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">Untitled Feature</span>' +
                '<button type="button" class="sec-delete-btn js-del-hcf" style="background:#fff0f0;border:1px solid #ffc9c9;color:#dc3545;font-size:.7rem;padding:3px 8px;border-radius:.3rem;cursor:pointer;flex-shrink:0;"><i class="fa fa-trash"></i> Remove</button>' +
            '</div>' +
            '<div class="hcf-row-body" id="body'+uid+'" style="padding:1.25rem;">' +
                '<div class="mb-3">' +
                    '<label class="form-label">Feature Title <span class="text-danger">*</span></label>' +
                    '<input type="text" name="hc_feat_title[]" class="form-control" placeholder="e.g. Minimally Invasive Surgery">' +
                '</div>' +
                '<div class="mb-3">' +
                    '<label class="form-label">Description</label>' +
                    '<textarea name="hc_feat_desc[]" class="form-control" rows="2" placeholder="Short feature description..."></textarea>' +
                '</div>' +
                '<div class="mb-0">' +
                    '<label class="form-label">Icon</label>' +
                    '<select name="hc_feat_icon[]" class="form-select">' +
                        '<option value="star">⭐ star</option>' +
                        '<option value="doctor">👨‍⚕️ doctor</option>' +
                        '<option value="heart">❤️ heart</option>' +
                        '<option value="shield">🛡️ shield</option>' +
                        '<option value="clock">⏰ clock</option>' +
                        '<option value="award">🏆 award</option>' +
                    '</select>' +
                '</div>' +
            '</div>';

        // Live label
        row.querySelector('input[name="hc_feat_title[]"]').oninput = function(){
            var lbl=el('lbl'+uid); if(!lbl) return;
            lbl.textContent=this.value||'Untitled Feature';
            lbl.style.color=this.value?'#212529':'#adb5bd';
            lbl.style.fontStyle=this.value?'normal':'italic';
        };

        // Toggle collapse
        row.querySelector('.hcf-row-header').onclick = function(e){
            if(e.target.closest('.js-del-hcf')) return;
            var body=el('body'+uid); if(!body) return;
            body.style.display=body.style.display==='none'?'block':'none';
        };

        // Delete
        row.querySelector('.js-del-hcf').onclick = function(e){
            e.stopPropagation();
            row.remove();
            toggleEmpty('hcFeaturesWrap','hcFeaturesEmpty');
            renumberAll();
        };

        if (wrap) wrap.appendChild(row);
        toggleEmpty('hcFeaturesWrap','hcFeaturesEmpty');
        row.querySelector('input[name="hc_feat_title[]"]').focus();
    };
} else {
    console.error('[ServiceAdd] #btnAddHcFeature NOT FOUND in DOM');
}

// ══════════════════════════════════════════════
// 12. DRAG & DROP (Sections)
// ══════════════════════════════════════════════
function initDrag() {
    var wrap = el('sectionsWrap');
    if (!wrap) return;
    var dragged = null;
    wrap.querySelectorAll('.section-row').forEach(function (row) {
        row.ondragstart = function (e) { dragged=this; e.dataTransfer.effectAllowed='move'; setTimeout(function(){row.style.opacity='.4';},0); };
        row.ondragend   = function ()  { this.style.opacity='1'; dragged=null; renumberAll(); };
        row.ondragover  = function (e) { e.preventDefault(); this.classList.add('drag-over'); };
        row.ondragleave = function ()  { this.classList.remove('drag-over'); };
        row.ondrop      = function (e) {
            e.preventDefault(); this.classList.remove('drag-over');
            if (dragged && dragged!==this) {
                var all=Array.from(wrap.querySelectorAll('.section-row'));
                var from=all.indexOf(dragged), to=all.indexOf(this);
                if(from<to) wrap.insertBefore(dragged, this.nextSibling);
                else        wrap.insertBefore(dragged, this);
                renumberAll();
            }
        };
    });
}

// ══════════════════════════════════════════════
// 13. FORM SUBMIT
// ══════════════════════════════════════════════
var serviceForm = el('serviceForm');
if (serviceForm) {
    serviceForm.onsubmit = function (e) {
        if (quill) el('svcContent').value = quill.root.innerHTML;
        var text = quill ? quill.getText().trim() : '';
        if (!text || text.length < 2) {
            e.preventDefault();
            alert('⚠️ Main Content is required. Please write a service description.');
            return false;
        }
    };
}

// ══════════════════════════════════════════════
// INIT
// ══════════════════════════════════════════════
updateSEO();
console.log('[ServiceAdd] ✅ All builders initialized successfully');


// ══════════════════════════════════════════════
// ICON PICKER
// ══════════════════════════════════════════════

var ICON_LIST = [
    // ── Medical / Health ──
    {c:'fa fa-heartbeat',       t:'heartbeat',       cat:'Medical'},
    {c:'fa fa-heart',           t:'heart',           cat:'Medical'},
    {c:'fa fa-heart-pulse',     t:'heart pulse',     cat:'Medical'},
    {c:'fa fa-stethoscope',     t:'stethoscope',     cat:'Medical'},
    {c:'fa fa-user-doctor',     t:'doctor',          cat:'Medical'},
    {c:'fa fa-hospital',        t:'hospital',        cat:'Medical'},
    {c:'fa fa-hospital-user',   t:'hospital user',   cat:'Medical'},
    {c:'fa fa-pills',           t:'pills medicine',  cat:'Medical'},
    {c:'fa fa-capsules',        t:'capsules',        cat:'Medical'},
    {c:'fa fa-syringe',         t:'syringe injection',cat:'Medical'},
    {c:'fa fa-microscope',      t:'microscope lab',  cat:'Medical'},
    {c:'fa fa-dna',             t:'dna genetics',    cat:'Medical'},
    {c:'fa fa-bone',            t:'bone ortho',      cat:'Medical'},
    {c:'fa fa-brain',           t:'brain neuro',     cat:'Medical'},
    {c:'fa fa-eye',             t:'eye vision',      cat:'Medical'},
    {c:'fa fa-tooth',           t:'tooth dental',    cat:'Medical'},
    {c:'fa fa-lungs',           t:'lungs respiratory',cat:'Medical'},
    {c:'fa fa-ribbon',          t:'ribbon cancer',   cat:'Medical'},
    {c:'fa fa-baby',            t:'baby pediatric',  cat:'Medical'},
    {c:'fa fa-baby-carriage',   t:'baby carriage',   cat:'Medical'},
    {c:'fa fa-person-pregnant', t:'pregnant maternity',cat:'Medical'},
    {c:'fa fa-wheelchair',      t:'wheelchair',      cat:'Medical'},
    {c:'fa fa-ambulance',       t:'ambulance emergency',cat:'Medical'},
    {c:'fa fa-kit-medical',     t:'first aid kit',   cat:'Medical'},
    {c:'fa fa-vials',           t:'vials blood test',cat:'Medical'},
    {c:'fa fa-x-ray',           t:'x-ray radiology', cat:'Medical'},
    {c:'fa fa-weight-scale',    t:'weight bmi',      cat:'Medical'},
    {c:'fa fa-notes-medical',   t:'notes medical',   cat:'Medical'},
    {c:'fa fa-file-medical',    t:'file medical',    cat:'Medical'},
    {c:'fa fa-bed-pulse',       t:'icu patient',     cat:'Medical'},
    {c:'fa fa-hand-holding-medical', t:'care support',cat:'Medical'},
    {c:'fa fa-plus',            t:'plus cross',      cat:'Medical'},
    {c:'fa fa-circle-plus',     t:'plus circle add', cat:'Medical'},
    {c:'fa fa-virus',           t:'virus infection', cat:'Medical'},
    {c:'fa fa-virus-slash',     t:'virus free',      cat:'Medical'},

    // ── People / Users ──
    {c:'fa fa-user',            t:'user person',     cat:'People'},
    {c:'fa fa-users',           t:'users group',     cat:'People'},
    {c:'fa fa-user-tie',        t:'user professional',cat:'People'},
    {c:'fa fa-user-nurse',      t:'nurse',           cat:'People'},
    {c:'fa fa-user-graduate',   t:'graduate student',cat:'People'},
    {c:'fa fa-user-shield',     t:'user security',   cat:'People'},
    {c:'fa fa-child',           t:'child',           cat:'People'},
    {c:'fa fa-person',          t:'person',          cat:'People'},
    {c:'fa fa-people-group',    t:'people group',    cat:'People'},
    {c:'fa fa-handshake',       t:'handshake deal',  cat:'People'},

    // ── Communication ──
    {c:'fa fa-phone',           t:'phone call',      cat:'Communication'},
    {c:'fa fa-phone-flip',      t:'phone flip',      cat:'Communication'},
    {c:'fa fa-envelope',        t:'email envelope',  cat:'Communication'},
    {c:'fa fa-envelope-open',   t:'email open',      cat:'Communication'},
    {c:'fa fa-comment',         t:'comment chat',    cat:'Communication'},
    {c:'fa fa-comments',        t:'comments chat',   cat:'Communication'},
    {c:'fa fa-message',         t:'message sms',     cat:'Communication'},
    {c:'fa fa-bell',            t:'bell notification',cat:'Communication'},
    {c:'fa fa-paper-plane',     t:'send paper plane',cat:'Communication'},
    {c:'fa fa-headset',         t:'headset support', cat:'Communication'},
    {c:'fa fa-video',           t:'video call',      cat:'Communication'},
    {c:'fa fa-mobile',          t:'mobile phone',    cat:'Communication'},
    {c:'fa fa-at',              t:'at email',        cat:'Communication'},

    // ── Location / Navigation ──
    {c:'fa fa-location-dot',    t:'location pin map',cat:'Location'},
    {c:'fa fa-map-marker',      t:'map marker',      cat:'Location'},
    {c:'fa fa-map',             t:'map',             cat:'Location'},
    {c:'fa fa-compass',         t:'compass navigate',cat:'Location'},
    {c:'fa fa-route',           t:'route direction', cat:'Location'},
    {c:'fa fa-road',            t:'road',            cat:'Location'},
    {c:'fa fa-building',        t:'building office', cat:'Location'},
    {c:'fa fa-city',            t:'city',            cat:'Location'},
    {c:'fa fa-home',            t:'home house',      cat:'Location'},
    {c:'fa fa-house-medical',   t:'house medical',   cat:'Location'},

    // ── Awards / Quality ──
    {c:'fa fa-star',            t:'star rating',     cat:'Awards'},
    {c:'fa fa-star-half-stroke',t:'star half',       cat:'Awards'},
    {c:'fa fa-award',           t:'award medal',     cat:'Awards'},
    {c:'fa fa-trophy',          t:'trophy winner',   cat:'Awards'},
    {c:'fa fa-certificate',     t:'certificate',     cat:'Awards'},
    {c:'fa fa-medal',           t:'medal',           cat:'Awards'},
    {c:'fa fa-crown',           t:'crown best',      cat:'Awards'},
    {c:'fa fa-shield',          t:'shield safety',   cat:'Awards'},
    {c:'fa fa-shield-halved',   t:'shield secure',   cat:'Awards'},
    {c:'fa fa-thumbs-up',       t:'thumbs up like',  cat:'Awards'},
    {c:'fa fa-check-circle',    t:'check circle ok', cat:'Awards'},
    {c:'fa fa-circle-check',    t:'circle check',    cat:'Awards'},
    {c:'fa fa-badge-check',     t:'badge verified',  cat:'Awards'},

    // ── Time / Schedule ──
    {c:'fa fa-clock',           t:'clock time',      cat:'Time'},
    {c:'fa fa-calendar',        t:'calendar date',   cat:'Time'},
    {c:'fa fa-calendar-check',  t:'calendar checked',cat:'Time'},
    {c:'fa fa-calendar-days',   t:'calendar days',   cat:'Time'},
    {c:'fa fa-hourglass',       t:'hourglass wait',  cat:'Time'},
    {c:'fa fa-stopwatch',       t:'stopwatch timer', cat:'Time'},
    {c:'fa fa-history',         t:'history recent',  cat:'Time'},

    // ── Technology / Tools ──
    {c:'fa fa-cog',             t:'settings gear',   cat:'Tech'},
    {c:'fa fa-cogs',            t:'gears settings',  cat:'Tech'},
    {c:'fa fa-wrench',          t:'wrench tool',     cat:'Tech'},
    {c:'fa fa-laptop',          t:'laptop computer', cat:'Tech'},
    {c:'fa fa-desktop',         t:'desktop monitor', cat:'Tech'},
    {c:'fa fa-mobile-screen',   t:'mobile screen',   cat:'Tech'},
    {c:'fa fa-wifi',            t:'wifi internet',   cat:'Tech'},
    {c:'fa fa-database',        t:'database',        cat:'Tech'},
    {c:'fa fa-server',          t:'server',          cat:'Tech'},
    {c:'fa fa-lock',            t:'lock security',   cat:'Tech'},
    {c:'fa fa-unlock',          t:'unlock open',     cat:'Tech'},
    {c:'fa fa-key',             t:'key access',      cat:'Tech'},
    {c:'fa fa-qrcode',          t:'qr code scan',    cat:'Tech'},
    {c:'fa fa-barcode',         t:'barcode scan',    cat:'Tech'},
    {c:'fa fa-print',           t:'print printer',   cat:'Tech'},

    // ── Finance ──
    {c:'fa fa-indian-rupee-sign',t:'rupee inr india',cat:'Finance'},
    {c:'fa fa-dollar-sign',     t:'dollar usd',      cat:'Finance'},
    {c:'fa fa-credit-card',     t:'credit card pay', cat:'Finance'},
    {c:'fa fa-wallet',          t:'wallet money',    cat:'Finance'},
    {c:'fa fa-coins',           t:'coins money',     cat:'Finance'},
    {c:'fa fa-piggy-bank',      t:'savings bank',    cat:'Finance'},
    {c:'fa fa-receipt',         t:'receipt bill',    cat:'Finance'},
    {c:'fa fa-file-invoice',    t:'invoice bill',    cat:'Finance'},
    {c:'fa fa-money-bill',      t:'money bill cash', cat:'Finance'},
    {c:'fa fa-chart-line',      t:'chart growth',    cat:'Finance'},
    {c:'fa fa-chart-bar',       t:'bar chart',       cat:'Finance'},
    {c:'fa fa-chart-pie',       t:'pie chart',       cat:'Finance'},
    {c:'fa fa-percent',         t:'percent discount',cat:'Finance'},

    // ── Documents / Files ──
    {c:'fa fa-file',            t:'file document',   cat:'Documents'},
    {c:'fa fa-file-pdf',        t:'pdf file',        cat:'Documents'},
    {c:'fa fa-file-word',       t:'word document',   cat:'Documents'},
    {c:'fa fa-file-image',      t:'image file',      cat:'Documents'},
    {c:'fa fa-folder',          t:'folder',          cat:'Documents'},
    {c:'fa fa-folder-open',     t:'folder open',     cat:'Documents'},
    {c:'fa fa-clipboard',       t:'clipboard report',cat:'Documents'},
    {c:'fa fa-clipboard-list',  t:'checklist',       cat:'Documents'},
    {c:'fa fa-list',            t:'list items',      cat:'Documents'},
    {c:'fa fa-list-check',      t:'list checked',    cat:'Documents'},
    {c:'fa fa-pen',             t:'pen edit write',  cat:'Documents'},
    {c:'fa fa-pencil',          t:'pencil edit',     cat:'Documents'},
    {c:'fa fa-signature',       t:'signature sign',  cat:'Documents'},

    // ── Arrows / UI ──
    {c:'fa fa-arrow-right',     t:'arrow right next',cat:'UI'},
    {c:'fa fa-arrow-left',      t:'arrow left back', cat:'UI'},
    {c:'fa fa-arrow-up',        t:'arrow up',        cat:'UI'},
    {c:'fa fa-arrow-down',      t:'arrow down',      cat:'UI'},
    {c:'fa fa-chevron-right',   t:'chevron right',   cat:'UI'},
    {c:'fa fa-chevron-left',    t:'chevron left',    cat:'UI'},
    {c:'fa fa-angles-right',    t:'angles double',   cat:'UI'},
    {c:'fa fa-circle-arrow-right',t:'circle arrow',  cat:'UI'},
    {c:'fa fa-external-link',   t:'external link',   cat:'UI'},
    {c:'fa fa-link',            t:'link chain',      cat:'UI'},
    {c:'fa fa-share',           t:'share',           cat:'UI'},
    {c:'fa fa-share-nodes',     t:'share social',    cat:'UI'},
    {c:'fa fa-search',          t:'search find',     cat:'UI'},
    {c:'fa fa-info',            t:'info',            cat:'UI'},
    {c:'fa fa-info-circle',     t:'info circle',     cat:'UI'},
    {c:'fa fa-question',        t:'question faq',    cat:'UI'},
    {c:'fa fa-question-circle', t:'question circle', cat:'UI'},
    {c:'fa fa-exclamation',     t:'exclamation alert',cat:'UI'},
    {c:'fa fa-ban',             t:'ban blocked',     cat:'UI'},
    {c:'fa fa-bars',            t:'menu bars',       cat:'UI'},
    {c:'fa fa-ellipsis',        t:'more options',    cat:'UI'},
    {c:'fa fa-grid-2',          t:'grid layout',     cat:'UI'},
    {c:'fa fa-th',              t:'grid table',      cat:'UI'},
    {c:'fa fa-sliders',         t:'sliders filter',  cat:'UI'},
    {c:'fa fa-filter',          t:'filter',          cat:'UI'},
    {c:'fa fa-sort',            t:'sort order',      cat:'UI'},
    {c:'fa fa-toggle-on',       t:'toggle on',       cat:'UI'},
    {c:'fa fa-toggle-off',      t:'toggle off',      cat:'UI'},
    {c:'fa fa-eye',             t:'eye view',        cat:'UI'},
    {c:'fa fa-eye-slash',       t:'eye hidden',      cat:'UI'},
    {c:'fa fa-download',        t:'download',        cat:'UI'},
    {c:'fa fa-upload',          t:'upload',          cat:'UI'},
    {c:'fa fa-trash',           t:'trash delete',    cat:'UI'},
    {c:'fa fa-edit',            t:'edit pen',        cat:'UI'},
    {c:'fa fa-save',            t:'save floppy',     cat:'UI'},
    {c:'fa fa-copy',            t:'copy duplicate',  cat:'UI'},
    {c:'fa fa-paste',           t:'paste',           cat:'UI'},
    {c:'fa fa-refresh',         t:'refresh reload',  cat:'UI'},
    {c:'fa fa-sync',            t:'sync rotate',     cat:'UI'},
    {c:'fa fa-times',           t:'close times x',   cat:'UI'},
    {c:'fa fa-times-circle',    t:'close circle',    cat:'UI'},
    {c:'fa fa-check',           t:'check tick ok',   cat:'UI'},
    {c:'fa fa-minus',           t:'minus remove',    cat:'UI'},
    {c:'fa fa-plus-circle',     t:'plus add circle', cat:'UI'},
    {c:'fa fa-image',           t:'image photo',     cat:'UI'},
    {c:'fa fa-images',          t:'gallery photos',  cat:'UI'},
    {c:'fa fa-camera',          t:'camera photo',    cat:'UI'},
    {c:'fa fa-video',           t:'video play',      cat:'UI'},
    {c:'fa fa-play',            t:'play video',      cat:'UI'},
    {c:'fa fa-pause',           t:'pause',           cat:'UI'},
    {c:'fa fa-volume-up',       t:'volume audio',    cat:'UI'},
    {c:'fa fa-moon',            t:'moon night',      cat:'UI'},
    {c:'fa fa-sun',             t:'sun day light',   cat:'UI'},
    {c:'fa fa-fire',            t:'fire hot trend',  cat:'UI'},
    {c:'fa fa-bolt',            t:'bolt lightning',  cat:'UI'},
    {c:'fa fa-leaf',            t:'leaf nature green',cat:'UI'},
    {c:'fa fa-spa',             t:'spa wellness',    cat:'UI'},
    {c:'fa fa-gem',             t:'gem diamond',     cat:'UI'},
    {c:'fa fa-layer-group',     t:'layers stack',    cat:'UI'},
    {c:'fa fa-cubes',           t:'cubes 3d',        cat:'UI'},
    {c:'fa fa-globe',           t:'globe world web', cat:'UI'},
    {c:'fa fa-flag',            t:'flag marker',     cat:'UI'},
    {c:'fa fa-bookmark',        t:'bookmark save',   cat:'UI'},
    {c:'fa fa-tag',             t:'tag label',       cat:'UI'},
    {c:'fa fa-tags',            t:'tags labels',     cat:'UI'},
    {c:'fa fa-cart-shopping',   t:'cart shop buy',   cat:'UI'},
    {c:'fa fa-bag-shopping',    t:'shopping bag',    cat:'UI'},
    {c:'fa fa-gift',            t:'gift present',    cat:'UI'},
    {c:'fa fa-truck',           t:'truck delivery',  cat:'UI'},
    {c:'fa fa-box',             t:'box package',     cat:'UI'},
    {c:'fa fa-boxes-stacked',   t:'inventory stock', cat:'UI'},
];

var selectedIconClass = '';
var iconCurrentCat    = 'All';

function buildIconCatButtons() {
    var cats = ['All'];
    ICON_LIST.forEach(function(ic){ if(cats.indexOf(ic.cat)===-1) cats.push(ic.cat); });
    var wrap = document.getElementById('iconCatBtns');
    if (!wrap) return;
    wrap.innerHTML = '';
    cats.forEach(function(cat){
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = cat;
        btn.className = 'btn btn-sm rounded-pill ' + (cat==='All'?'btn-primary':'btn-outline-secondary');
        btn.style.cssText = 'font-size:.7rem;padding:3px 10px;';
        btn.onclick = function(){
            iconCurrentCat = cat;
            wrap.querySelectorAll('button').forEach(function(b){
                b.className='btn btn-sm rounded-pill btn-outline-secondary';
                b.style.cssText='font-size:.7rem;padding:3px 10px;';
            });
            this.className='btn btn-sm rounded-pill btn-primary';
            this.style.cssText='font-size:.7rem;padding:3px 10px;';
            renderIcons(document.getElementById('iconSearch').value.trim().toLowerCase());
        };
        wrap.appendChild(btn);
    });
}

function renderIcons(query) {
    var grid    = document.getElementById('iconGrid');
    var noRes   = document.getElementById('iconNoResult');
    var noQ     = document.getElementById('iconNoQ');
    var counter = document.getElementById('iconCountShown');
    if (!grid) return;

    var filtered = ICON_LIST.filter(function(ic){
        var catOk = iconCurrentCat==='All' || ic.cat===iconCurrentCat;
        var kwOk  = !query || ic.t.indexOf(query)>-1 || ic.c.indexOf(query)>-1;
        return catOk && kwOk;
    });

    if (counter) counter.textContent = filtered.length;

    if (filtered.length === 0) {
        grid.innerHTML = '';
        if (noRes) noRes.classList.remove('d-none');
        if (noQ)   noQ.textContent = query;
        return;
    }
    if (noRes) noRes.classList.add('d-none');

    grid.innerHTML = '';
    filtered.forEach(function(ic){
        var isActive = ic.c === selectedIconClass;
        var cell = document.createElement('div');
        cell.title = ic.c;
        cell.setAttribute('data-icon', ic.c);
        cell.style.cssText =
            'display:flex;flex-direction:column;align-items:center;justify-content:center;' +
            'gap:5px;padding:10px 6px;border-radius:.5rem;cursor:pointer;transition:.15s;' +
            'border:2px solid '+(isActive?'#0d6efd':'#e9ecef')+';' +
            'background:'+(isActive?'#e7f1ff':'#fff')+';' +
            'min-height:70px;';

        cell.innerHTML =
            '<i class="'+ic.c+'" style="font-size:1.3rem;color:'+(isActive?'#0d6efd':'#495057')+';line-height:1;"></i>' +
            '<span style="font-size:.58rem;color:#6c757d;text-align:center;line-height:1.2;word-break:break-all;max-width:72px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'+
                ic.c.replace('fa fa-','').replace('fab fa-','')+'</span>';

        cell.onmouseenter = function(){
            if(ic.c!==selectedIconClass){
                this.style.borderColor='#86b7fe';
                this.style.background='#f0f7ff';
                this.querySelector('i').style.color='#0d6efd';
            }
        };
        cell.onmouseleave = function(){
            if(ic.c!==selectedIconClass){
                this.style.borderColor='#e9ecef';
                this.style.background='#fff';
                this.querySelector('i').style.color='#495057';
            }
        };
        cell.onclick = function(){
            // Deselect old
            var prev = grid.querySelector('[data-icon="'+selectedIconClass+'"]');
            if(prev){
                prev.style.borderColor='#e9ecef'; prev.style.background='#fff';
                var pi=prev.querySelector('i'); if(pi) pi.style.color='#495057';
            }
            selectedIconClass = ic.c;
            this.style.borderColor='#0d6efd'; this.style.background='#e7f1ff';
            this.querySelector('i').style.color='#0d6efd';

            // Update selection bar
            var bar = document.getElementById('iconSelectedBar');
            var bp  = document.getElementById('iconBarPreview');
            var bc  = document.getElementById('iconBarClass');
            if(bar){ bar.style.display='flex'; bar.style.removeProperty('display'); bar.classList.remove('d-none'); }
            if(bp)  bp.className = ic.c;
            if(bc)  bc.textContent = ic.c;

            // Update live preview in form
            var prev2 = document.getElementById('iconPreviewEl');
            if(prev2) prev2.className = ic.c + ' ' + (prev2.className.split(' ').slice(-1)[0]==='text-muted'?'':prev2.className.split(' ').slice(-1)[0]);
            if(prev2) prev2.className = ic.c;
        };

        grid.appendChild(cell);
    });
}

// Open modal
var btnOpenIconPicker = document.getElementById('btnOpenIconPicker');
if (btnOpenIconPicker) {
    btnOpenIconPicker.onclick = function(){
        // Pre-select current value
        var current = document.getElementById('iconInput');
        if(current && current.value.trim()) selectedIconClass = current.value.trim();
        buildIconCatButtons();
        renderIcons('');
        var search = document.getElementById('iconSearch');
        if(search) search.value='';
        var modal = new bootstrap.Modal(document.getElementById('iconPickerModal'));
        modal.show();
        setTimeout(function(){ if(search) search.focus(); }, 400);
    };
}

// Search input
var iconSearch = document.getElementById('iconSearch');
if (iconSearch) {
    iconSearch.oninput = function(){
        renderIcons(this.value.trim().toLowerCase());
    };
    iconSearch.onkeydown = function(e){
        if(e.key==='Escape'){ this.value=''; renderIcons(''); }
    };
}

// Clear search
var iconSearchClear = document.getElementById('iconSearchClear');
if(iconSearchClear) {
    iconSearchClear.onclick = function(){
        var s = document.getElementById('iconSearch');
        if(s){ s.value=''; renderIcons(''); s.focus(); }
    };
}

// Confirm icon button
var btnConfirmIcon = document.getElementById('btnConfirmIcon');
if (btnConfirmIcon) {
    btnConfirmIcon.onclick = function(){
        var input = document.getElementById('iconInput');
        var prev  = document.getElementById('iconPreviewEl');
        if(input && selectedIconClass){ input.value = selectedIconClass; }
        if(prev  && selectedIconClass){ prev.className = selectedIconClass; }
    };
}

// Live preview as user types in input directly
var iconInputField = document.getElementById('iconInput');
if (iconInputField) {
    iconInputField.oninput = function(){
        var prev = document.getElementById('iconPreviewEl');
        if(prev && this.value.trim()){ prev.className = this.value.trim(); }
    };
}
</script>

<?php require_once '../include/footer.php'; ?>