<?php
require_once __DIR__ . '/../../include/config.php';
$pageTitle = isset($pageTitle) ? $pageTitle : 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RK Hospital - <?= htmlspecialchars($pageTitle) ?></title>

    <link rel="shortcut icon" type="image/x-icon" href="<?= SITE_URL ?>/admin/assets/img/RK-Logo.png">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/feathericon.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/select2.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/custom.css">

    <?php if (!empty($extraCSS)) echo $extraCSS; ?>
</head>
<body>
<div class="main-wrapper">
