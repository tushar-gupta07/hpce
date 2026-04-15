<?php $base_url = "http://localhost/rkhospital/"; ?>
<?php include 'include/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cancellation & Refund Policy of Dr. Agrawal's R.K. Hospital Nagpur. Read our policies on appointment cancellations, refunds, rescheduling, and no-show procedures.">
    <meta name="keywords" content="RK Hospital Cancellation Policy, Hospital Refund Policy Nagpur, Appointment Cancellation RK Hospital">
    <meta name="author" content="Dr. Agrawal's R.K. Hospital Nagpur">
    <title>Cancellation & Refund Policy | Dr. Agrawal's R.K. Hospital Nagpur</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/img/RK-Logo.png" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $base_url; ?>assets/img/RK-Logo.png">

    <!-- Theme Settings Js -->
    <script src="assets/js/theme-script.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/animate.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/fontawesome/css/all.min.css">

    <!-- Iconsax CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/iconsax.css">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/feather.css">

    <!-- Slick CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/slick/slick.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/slick/slick-theme.css">

    <!-- Wow CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/wow/css/animate.css">

    <!-- select CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/select2/css/select2.min.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/fancybox/jquery.fancybox.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">

    <style>
        /* ═══════════════════════════════════════════
           THEME VARIABLES — matches homepage red/white
        ═══════════════════════════════════════════ */
        :root {
            --red: #d32f2f;
            --red-dark: #b71c1c;
            --red-light: #ef5350;
            --red-bg: #fff5f5;
            --red-border: #fecaca;
            --text-dark: #1a1a2e;
            --text-mid: #374151;
            --text-soft: #6b7280;
            --white: #ffffff;
            --off-white: #f9fafb;
            --border: #e5e7eb;
            --shadow-sm: 0 2px 12px rgba(211, 47, 47, 0.08);
            --shadow-md: 0 6px 28px rgba(211, 47, 47, 0.13);
            --shadow-lg: 0 20px 60px rgba(211, 47, 47, 0.18);
            --primary: #316dff;
            --blue-dark: #1a3fa3;
            --blue-bg: #eef3ff;
            --blue-border: #c3d0f8;
        }
         /* ─── STICKY SIDEBAR TOC ─── */
.tc-sidebar {
    position: sticky;
    top: 90px;
}
        /* ─── META BAR ─── */
        .tc-meta-bar {
            background: var(--off-white);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }

        .tc-meta-inner {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .tc-updated-badge {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 8px;
            font-size: 12.5px;
            color: var(--text-soft);
            line-height: 1.6;
        }
        .tc-updated-badge i { color: var(--primary); flex-shrink: 0; }
        .tc-updated-badge strong { color: var(--text-dark); }
        .tc-updated-badge .sep-pipe { color: var(--border); }

        .tc-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-mid);
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 16px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .tc-print-btn:hover { background: var(--white); border-color: var(--red); color: var(--red); }

        @media (min-width: 768px) {
            .tc-meta-inner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
            .tc-updated-badge .sep-pipe { display: inline; }
        }

        @media (max-width: 767px) {
            .tc-updated-badge .sep-pipe { display: none; }
            .tc-print-btn { width: 100%; justify-content: center; }
        }

        /* ─── MAIN LAYOUT ─── */
        .tc-wrapper {
            padding: 60px 0 80px;
            background: #fff;
        }

        /* ─── STICKY SIDEBAR TOC ─── */
        .tc-sidebar {
            position: sticky;
            top: 90px;
        }

        .tc-toc {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .tc-toc-header {
            background: linear-gradient(135deg, var(--red-dark), var(--red));
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tc-toc-header i { color: #fff; font-size: 16px; }
        .tc-toc-header h5 {
            margin: 0;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .tc-toc-list { padding: 14px 0; }
        .tc-toc-list a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 22px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-mid);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .tc-toc-list a .toc-num {
            min-width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--off-white);
            color: var(--red);
            font-size: 10.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--red-border);
            transition: all 0.2s;
        }
        .tc-toc-list a:hover {
            color: var(--red);
            background: var(--red-bg);
            border-left-color: var(--red);
        }
        .tc-toc-list a:hover .toc-num {
            background: var(--red);
            color: #fff;
            border-color: var(--red);
        }
        .tc-toc-list a.active {
            color: var(--red);
            background: var(--red-bg);
            border-left-color: var(--red);
            font-weight: 700;
        }
        .tc-toc-list a.active .toc-num {
            background: var(--red);
            color: #fff;
        }

        /* Contact Card in Sidebar */
        .tc-contact-card {
            background: linear-gradient(135deg, #0d1b4b 0%, #1a3fa3 100%);
            border-radius: 18px;
            padding: 26px 22px;
            margin-top: 20px;
            text-align: center;
        }
        .tc-contact-card i { font-size: 28px; color: rgba(255,255,255,0.8); margin-bottom: 12px; display: block; }
        .tc-contact-card h6 { color: #fff; font-size: 14px; font-weight: 700; margin-bottom: 6px; }
        .tc-contact-card p { color: rgba(255,255,255,0.7); font-size: 12.5px; margin-bottom: 16px; }
        .tc-contact-card a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--red);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.22s;
        }
        .tc-contact-card a:hover { background: var(--red-dark); transform: translateY(-1px); color: #fff; }

        /* ─── QUICK SUMMARY CARDS ─── */
        .cp-quick-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 48px;
        }
        .cp-quick-card {
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px 18px;
            text-align: center;
            transition: all 0.22s;
        }
        .cp-quick-card:hover {
            border-color: var(--red-border);
            box-shadow: var(--shadow-sm);
            transform: translateY(-2px);
        }
        .cp-quick-card .qc-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 12px;
        }
        .cp-quick-card .qc-icon.red { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
        .cp-quick-card .qc-icon.blue { background: var(--blue-bg); color: var(--primary); border: 1px solid var(--blue-border); }
        .cp-quick-card .qc-icon.green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .cp-quick-card h6 { font-size: 13px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; }
        .cp-quick-card p { font-size: 12px; color: var(--text-soft); margin: 0; line-height: 1.5; }

        /* ─── CONTENT SECTIONS ─── */
        .tc-section {
            margin-bottom: 48px;
            scroll-margin-top: 100px;
        }

        .tc-section-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 2px solid var(--border);
        }

        .tc-section-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .tc-section-icon.red { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
        .tc-section-icon.blue { background: var(--blue-bg); color: var(--primary); border: 1px solid var(--blue-border); }
        .tc-section-icon.green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        .tc-section-header-text { flex: 1; }
        .tc-section-num {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--red);
            margin-bottom: 3px;
        }
        .tc-section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
        }

        .tc-prose {
            font-size: 14.5px;
            color: var(--text-mid);
            line-height: 1.85;
        }

        /* List style */
        .tc-list {
            list-style: none;
            padding: 0;
            margin: 14px 0 0;
        }
        .tc-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: var(--text-mid);
            line-height: 1.65;
        }
        .tc-list li:last-child { border-bottom: none; }
        .tc-list li .li-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--red-bg);
            color: var(--red);
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
            border: 1px solid var(--red-border);
            transition: all 0.2s;
        }
        .tc-list li .li-icon.blue { background: var(--blue-bg); color: var(--primary); border-color: var(--blue-border); }
        .tc-list li .li-icon.green { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
        .tc-list li .li-icon.orange { background: #fff7ed; color: #ea580c; border-color: #fed7aa; }

        /* Highlight box */
        .tc-highlight {
            background: var(--red-bg);
            border: 1px solid var(--red-border);
            border-left: 4px solid var(--red);
            border-radius: 10px;
            padding: 16px 20px;
            margin-top: 16px;
            font-size: 13.5px;
            color: var(--text-mid);
            line-height: 1.75;
        }
        .tc-highlight.blue {
            background: var(--blue-bg);
            border-color: var(--blue-border);
            border-left-color: var(--primary);
        }
        .tc-highlight.green {
            background: #f0fdf4;
            border-color: #bbf7d0;
            border-left-color: #16a34a;
        }
        .tc-highlight.orange {
            background: #fff7ed;
            border-color: #fed7aa;
            border-left-color: #ea580c;
        }
        .tc-highlight strong { color: var(--text-dark); }

        /* Info grid */
        .tc-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 18px;
        }
        .tc-info-card {
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .tc-info-card i {
            font-size: 18px;
            color: var(--primary);
            margin-top: 2px;
            flex-shrink: 0;
        }
        .tc-info-card i.red { color: var(--red); }
        .tc-info-card i.green { color: #16a34a; }
        .tc-info-card h6 { font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; }
        .tc-info-card p { font-size: 12.5px; color: var(--text-soft); margin: 0; line-height: 1.5; }

        /* ─── TIMELINE (Cancellation Window) ─── */
        .cp-timeline {
            position: relative;
            padding-left: 28px;
            margin-top: 20px;
        }
        .cp-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: linear-gradient(180deg, var(--red), var(--primary), #16a34a);
            border-radius: 2px;
        }
        .cp-timeline-item {
            position: relative;
            padding: 0 0 24px 24px;
        }
        .cp-timeline-item:last-child { padding-bottom: 0; }
        .cp-timeline-dot {
            position: absolute;
            left: -28px;
            top: 4px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 700;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px currentColor;
        }
        .cp-timeline-dot.green { background: #16a34a; color: #fff; box-shadow: 0 0 0 2px #16a34a; }
        .cp-timeline-dot.orange { background: #ea580c; color: #fff; box-shadow: 0 0 0 2px #ea580c; }
        .cp-timeline-dot.red { background: var(--red); color: #fff; box-shadow: 0 0 0 2px var(--red); }
        .cp-timeline-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .cp-timeline-label.green { color: #16a34a; }
        .cp-timeline-label.orange { color: #ea580c; }
        .cp-timeline-label.red { color: var(--red); }
        .cp-timeline-text {
            font-size: 13.5px;
            color: var(--text-mid);
            line-height: 1.6;
        }

        /* ─── REFUND TABLE ─── */
        .cp-refund-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
            margin-top: 20px;
            font-size: 13.5px;
        }
        .cp-refund-table thead tr {
            background: linear-gradient(135deg, var(--red-dark), var(--red));
        }
        .cp-refund-table thead th {
            padding: 14px 18px;
            color: #fff;
            font-weight: 700;
            font-size: 12.5px;
            letter-spacing: 0.4px;
            text-align: left;
        }
        .cp-refund-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }
        .cp-refund-table tbody tr:last-child { border-bottom: none; }
        .cp-refund-table tbody tr:hover { background: var(--off-white); }
        .cp-refund-table td {
            padding: 13px 18px;
            color: var(--text-mid);
            vertical-align: middle;
        }
        .cp-refund-table td:first-child { font-weight: 600; color: var(--text-dark); }
        .cp-badge {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 100px;
        }
        .cp-badge.full { background: #dcfce7; color: #15803d; }
        .cp-badge.partial { background: #fef9c3; color: #a16207; }
        .cp-badge.none { background: #fee2e2; color: #b91c1c; }
        .cp-badge.na { background: var(--off-white); color: var(--text-soft); border: 1px solid var(--border); }

        /* ─── AGREEMENT FOOTER BANNER ─── */
        .tc-agreement-banner {
            background: linear-gradient(135deg, #0d1b4b 0%, #1a1a2e 50%, #7f0000 100%);
            border-radius: 20px;
            padding: 48px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: 16px;
        }
        .tc-agreement-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M20 20.5V18H0v5h5v5H0v5h20v-2.5h-5V20.5h5zM15 45V20H0v5h5v5H0v5h5v5H0v5h15v-5H5v-5h5v-5H5v-5h10z'/%3E%3C/g%3E%3C/svg%3E");
        }
        .tc-agreement-banner i {
            font-size: 42px;
            color: rgba(255,255,255,0.35);
            display: block;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }
        .tc-agreement-banner h3 {
            color: #fff;
            font-size: 1.55rem;
            font-weight: 800;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        .tc-agreement-banner p {
            color: rgba(255,255,255,0.7);
            font-size: 14.5px;
            max-width: 540px;
            margin: 0 auto 26px;
            position: relative;
            z-index: 1;
        }
        .tc-agreement-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        .btn-tc-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--red-dark), var(--red-light));
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            padding: 13px 30px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.22s;
            box-shadow: 0 5px 20px rgba(183,28,28,0.4);
        }
        .btn-tc-primary:hover {
            background: linear-gradient(135deg, #7f0000, var(--red-dark));
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(183,28,28,0.55);
            color: #fff;
        }
        .btn-tc-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 2px solid rgba(255,255,255,0.35);
            color: rgba(255,255,255,0.85);
            font-size: 14px;
            font-weight: 700;
            padding: 13px 30px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.22s;
        }
        .btn-tc-outline:hover {
            border-color: #fff;
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        /* ─── GLOBAL OVERFLOW FIX ─── */
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }
        .main-wrapper {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }

        /* ─── CP BANNER (custom prefix cp- to avoid style.css conflicts) ─── */
        .cp-hero-banner {
            position: relative;
            width: 100%;
            max-width: 100vw;
            overflow: hidden;
            min-height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-sizing: border-box;
        }
        .cp-hero-banner .cp-banner-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }
        .cp-hero-banner .cp-banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(13,27,75,0.88) 0%, rgba(26,26,46,0.82) 50%, rgba(127,0,0,0.85) 100%);
            z-index: 1;
        }
        .cp-hero-banner .cp-banner-body {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 100%;
            padding: 60px 20px 60px;
            box-sizing: border-box;
        }
        .cp-banner-eyebrow {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 6px 18px;
            border-radius: 100px;
            margin-bottom: 20px;
            max-width: 100%;
            word-break: break-word;
        }
        .cp-banner-eyebrow i { color: #ef5350; }
        .cp-banner-h1 {
            font-size: clamp(1.6rem, 5vw, 3rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin: 0 auto 14px;
            max-width: 100%;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        .cp-banner-h1 span {
            color: #ef5350;
            display: block;
            font-size: clamp(1.3rem, 4.5vw, 2.6rem);
            word-break: break-word;
        }
        .cp-banner-sub {
            color: rgba(255,255,255,0.75);
            font-size: clamp(13px, 3vw, 15.5px);
            max-width: 600px;
            margin: 0 auto 28px;
            line-height: 1.7;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        .cp-banner-btns {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .cp-btn-red {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #b71c1c, #ef5350);
            color: #fff;
            font-size: clamp(12px, 3vw, 14px);
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.22s;
            box-shadow: 0 4px 18px rgba(183,28,28,0.4);
            white-space: nowrap;
        }
        .cp-btn-red:hover { transform: translateY(-2px); color: #fff; box-shadow: 0 6px 24px rgba(183,28,28,0.55); }
        .cp-btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 2px solid rgba(255,255,255,0.4);
            color: rgba(255,255,255,0.9);
            font-size: clamp(12px, 3vw, 14px);
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.22s;
            white-space: nowrap;
        }
        .cp-btn-outline:hover { border-color: #fff; color: #fff; background: rgba(255,255,255,0.1); }

        /* Stat badges — desktop only */
        .cp-stat-badge {
            display: none;
        }
        @media (min-width: 768px) {
            .cp-stat-badge {
                display: flex;
                align-items: center;
                gap: 10px;
                position: absolute;
                z-index: 3;
                background: rgba(255,255,255,0.12);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 14px;
                padding: 12px 18px;
            }
            .cp-stat-badge.badge-left { left: 24px; bottom: 32px; }
            .cp-stat-badge.badge-right { right: 24px; bottom: 32px; }
            .cp-stat-badge .badge-icon {
                width: 36px; height: 36px;
                background: var(--red); border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                color: #fff; font-size: 14px;
            }
            .cp-stat-badge .num { font-size: 15px; font-weight: 800; color: #fff; line-height: 1; }
            .cp-stat-badge .label { font-size: 11px; color: rgba(255,255,255,0.7); }
            .cp-hero-banner .cp-banner-body { padding: 80px 40px; }
        }

        @media (max-width: 576px) {
            .cp-btn-red, .cp-btn-outline {
                width: 100%;
                justify-content: center;
            }
        }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width: 991px) {
            .tc-sidebar { position: static; margin-bottom: 32px; }
            .tc-toc { position: static; }
            .cp-quick-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .tc-info-grid { grid-template-columns: 1fr; }
            .tc-agreement-banner { padding: 36px 24px; }
            .tc-agreement-banner h3 { font-size: 1.25rem; }
            .tc-wrapper { padding: 40px 0 60px; }
            .cp-refund-table { font-size: 12.5px; }
            .cp-refund-table td, .cp-refund-table thead th { padding: 10px 12px; }
        }
        @media (max-width: 576px) {
            .tc-section-header { flex-direction: column; gap: 12px; }
            .tc-section-title { font-size: 1.1rem; }
            .tc-prose { font-size: 14px; }
            .tc-highlight { font-size: 13px; padding: 14px 16px; }
            .tc-info-card { padding: 14px 14px; }
            .tc-agreement-banner { padding: 28px 18px; }
            .btn-tc-primary, .btn-tc-outline {
                width: 100%;
                justify-content: center;
                padding: 13px 20px;
            }
            .tc-agreement-actions { flex-direction: column; gap: 10px; }
            .tc-section { margin-bottom: 36px; }
            .cp-quick-grid { grid-template-columns: 1fr; }
            .cp-refund-table { display: block; overflow-x: auto; white-space: nowrap; }
        }
    </style>
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <?php include 'include/header.php'; ?>

        <!-- ═══ BANNER ═══ -->
        <section class="cp-hero-banner">
            <img src="assets/img/home/image-crousel1.webp" alt="RK Hospital Nagpur Cancellation Policy" class="cp-banner-img">
            <div class="cp-banner-overlay"></div>

            <!-- Stat Badges — desktop only -->
            <div class="cp-stat-badge badge-left">
                <div class="badge-icon"><i class="fa-solid fa-star"></i></div>
                <div>
                    <div class="num">5.0 ★</div>
                    <div class="label">496+ Reviews</div>
                </div>
            </div>
            <div class="cp-stat-badge badge-right">
                <div class="badge-icon"><i class="fa-solid fa-clock"></i></div>
                <div>
                    <div class="num">24/7</div>
                    <div class="label">Emergency Care</div>
                </div>
            </div>

            <div class="cp-banner-body">
                <div class="cp-banner-eyebrow">
                    <i class="fa-solid fa-hospital"></i>
                    Dr. Agrawal's R.K. Hospital, Nagpur
                </div>
                <h1 class="cp-banner-h1">
                    Legal &amp; Policy Documents
                    <span>Cancellation &amp; Refund Policy</span>
                </h1>
                <p class="cp-banner-sub">
                    Understand our appointment cancellation, rescheduling, and refund procedures at Dr. Agrawal's R.K. Hospital, Nagpur — designed to be fair, transparent, and patient-friendly.
                </p>
                <div class="cp-banner-btns">
                    <a href="tel:+919766057372" class="cp-btn-red">
                        <i class="fa-solid fa-phone"></i>
                        Call Now: +91 97660 57372
                    </a>
                    <a href="contact-us" class="cp-btn-outline">
                        <i class="fa-solid fa-calendar-check"></i>
                        Book Appointment
                    </a>
                </div>
            </div>
        </section>

        <!-- ═══ META BAR ═══ -->
        <div class="tc-meta-bar">
            <div class="container">
                <div class="tc-meta-inner">
                    <div class="tc-updated-badge">
                        <i class="fa-regular fa-calendar-check"></i>
                        Last Updated: <strong>January 1, 2025</strong>
                        &nbsp;|&nbsp;
                        <i class="fa-solid fa-file-lines"></i>
                        Version: <strong>2.0</strong>
                        &nbsp;|&nbsp;
                        <i class="fa-solid fa-globe"></i>
                        Applicable: <strong>R.K. Hospital, Nagpur</strong>
                    </div>
                    <button class="tc-print-btn" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Print / Save PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══ MAIN CONTENT ═══ -->
        <section class="tc-wrapper">
            <div class="container">
                <div class="row g-5">

                    <!-- ── SIDEBAR ── -->
                    <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                        <div class="tc-sidebar">

                            <!-- Table of Contents -->
                            <div class="tc-toc">
                                <div class="tc-toc-header">
                                    <i class="fa-solid fa-list-ul"></i>
                                    <h5>Table of Contents</h5>
                                </div>
                                <div class="tc-toc-list">
                                    <a href="#cp-1" class="active">
                                        <span class="toc-num">01</span> Overview
                                    </a>
                                    <a href="#cp-2">
                                        <span class="toc-num">02</span> Appointment Cancellation
                                    </a>
                                    <a href="#cp-3">
                                        <span class="toc-num">03</span> Cancellation Window
                                    </a>
                                    <a href="#cp-4">
                                        <span class="toc-num">04</span> Refund Policy
                                    </a>
                                    <a href="#cp-5">
                                        <span class="toc-num">05</span> No-Show Policy
                                    </a>
                                    <a href="#cp-6">
                                        <span class="toc-num">06</span> Rescheduling Policy
                                    </a>
                                    <a href="#cp-7">
                                        <span class="toc-num">07</span> Surgery & IPD Cancellation
                                    </a>
                                    <a href="#cp-8">
                                        <span class="toc-num">08</span> Insurance & Cashless
                                    </a>
                                    <a href="#cp-9">
                                        <span class="toc-num">09</span> Emergency Exceptions
                                    </a>
                                    <a href="#cp-10">
                                        <span class="toc-num">10</span> How to Cancel
                                    </a>
                                </div>
                            </div>

                            <!-- Contact Card -->
                            <div class="tc-contact-card">
                                <i class="fa-solid fa-headset"></i>
                                <h6>Need Help Cancelling?</h6>
                                <p>Our billing team is available to assist you with cancellations and refund queries.</p>
                                <a href="contact-us">
                                    <i class="fa-solid fa-phone"></i> Contact Us
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- ── MAIN CONTENT ── -->
                    <div class="col-lg-8 col-xl-9">

                        <!-- QUICK SUMMARY CARDS -->
                        <div class="cp-quick-grid">
                            <div class="cp-quick-card">
                                <div class="qc-icon green">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h6>Free Cancellation</h6>
                                <p>Cancel 2+ hours before appointment with no penalty</p>
                            </div>
                            <div class="cp-quick-card">
                                <div class="qc-icon blue">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                </div>
                                <h6>Reschedule Anytime</h6>
                                <p>Adjust your slot within 30 days at no extra charge</p>
                            </div>
                            <div class="cp-quick-card">
                                <div class="qc-icon red">
                                    <i class="fa-solid fa-indian-rupee-sign"></i>
                                </div>
                                <h6>Refund in 7–10 Days</h6>
                                <p>Eligible refunds processed back to original payment mode</p>
                            </div>
                        </div>

                        <!-- SECTION 01 — Overview -->
                        <div class="tc-section" id="cp-1">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-file-circle-exclamation"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 01</div>
                                    <h2 class="tc-section-title">Overview of This Policy</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>
                                    At <strong>Dr. Agrawal's R.K. Hospital, Nagpur</strong>, we understand that medical plans can change due to unforeseen circumstances. This Cancellation &amp; Refund Policy has been designed to be fair, transparent, and straightforward for all our valued patients.
                                </p>
                                <p style="margin-top:12px;">
                                    This policy applies to all outpatient (OPD) appointments, inpatient (IPD) admissions, surgical procedures, diagnostic bookings, and any other services booked through our hospital — whether booked in person, via phone call, or through our online appointment system.
                                </p>
                                <div class="tc-highlight">
                                    <strong>Important:</strong> Please read this policy carefully before booking any appointment or procedure. By booking a service with R.K. Hospital, you agree to the terms outlined in this policy. For any assistance, call us at <strong>+91 97660 57372</strong>.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 02 — Appointment Cancellation -->
                        <div class="tc-section" id="cp-2">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 02</div>
                                    <h2 class="tc-section-title">Appointment Cancellation Terms</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>Patients who wish to cancel a booked OPD or specialist appointment must adhere to the following conditions:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Cancellation requests must be communicated to our reception or helpline at least <strong>2 hours before</strong> the scheduled appointment time to be eligible for any adjustment or credit.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Cancellations must be made by calling our helpline numbers: <strong>+91 97660 57372</strong>. Walk-in cancellations at the reception desk are also accepted.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Upon successful cancellation, patients will receive a confirmation from our staff. Please retain this confirmation for any future reference or refund claim.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Cancellation requests made after the 2-hour window but before the appointment time will be treated as late cancellations and will not be eligible for a refund, but may qualify for rescheduling.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Free-of-cost OPD slots (walk-in without advance payment) can be cancelled without any restriction or penalty.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECTION 03 — Cancellation Window (Timeline) -->
                        <div class="tc-section" id="cp-3">
                            <div class="tc-section-header">
                                <div class="tc-section-icon green">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 03</div>
                                    <h2 class="tc-section-title">Cancellation Time Window</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>The timing of your cancellation request determines your eligibility for a refund or credit. Refer to the breakdown below:</p>
                                <div class="cp-timeline">
                                    <div class="cp-timeline-item">
                                        <div class="cp-timeline-dot green"><i class="fa-solid fa-check"></i></div>
                                        <div class="cp-timeline-label green">More than 2 Hours Before — Full Credit / Refund Eligible</div>
                                        <div class="cp-timeline-text">
                                            Cancellation made more than 2 hours before the appointment: consultation fee is credited to your account for future use or refunded to your original payment method within 7–10 working days.
                                        </div>
                                    </div>
                                    <div class="cp-timeline-item">
                                        <div class="cp-timeline-dot orange"><i class="fa-solid fa-clock"></i></div>
                                        <div class="cp-timeline-label orange">Within 2 Hours — Reschedule Only (No Refund)</div>
                                        <div class="cp-timeline-text">
                                            Cancellation made within 2 hours of the appointment: no refund is applicable, but the fee can be adjusted for a rescheduled appointment within 30 calendar days.
                                        </div>
                                    </div>
                                    <div class="cp-timeline-item">
                                        <div class="cp-timeline-dot red"><i class="fa-solid fa-xmark"></i></div>
                                        <div class="cp-timeline-label red">No-Show (After Appointment Time) — Fee Forfeited</div>
                                        <div class="cp-timeline-text">
                                            If no cancellation is communicated and the patient does not show up for their scheduled appointment, the consultation fee is forfeited. No refund or credit is applicable.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 04 — Refund Policy -->
                        <div class="tc-section" id="cp-4">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-indian-rupee-sign"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 04</div>
                                    <h2 class="tc-section-title">Refund Policy</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>Refund eligibility varies by service type and timing of cancellation. The table below summarises our refund structure:</p>

                                <table class="cp-refund-table">
                                    <thead>
                                        <tr>
                                            <th>Service Type</th>
                                            <th>Cancellation Timing</th>
                                            <th>Refund Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>OPD Consultation Fee</td>
                                            <td>2+ hours before</td>
                                            <td><span class="cp-badge full">Full Credit</span></td>
                                        </tr>
                                        <tr>
                                            <td>OPD Consultation Fee</td>
                                            <td>Within 2 hours</td>
                                            <td><span class="cp-badge partial">Reschedule Only</span></td>
                                        </tr>
                                        <tr>
                                            <td>OPD Consultation Fee</td>
                                            <td>No-show</td>
                                            <td><span class="cp-badge none">No Refund</span></td>
                                        </tr>
                                        <tr>
                                            <td>Diagnostic / Lab Tests</td>
                                            <td>Before sample collection</td>
                                            <td><span class="cp-badge full">Full Refund</span></td>
                                        </tr>
                                        <tr>
                                            <td>Diagnostic / Lab Tests</td>
                                            <td>After sample collection</td>
                                            <td><span class="cp-badge none">No Refund</span></td>
                                        </tr>
                                        <tr>
                                            <td>Surgical Deposit</td>
                                            <td>Cancelled by hospital</td>
                                            <td><span class="cp-badge full">Full Refund</span></td>
                                        </tr>
                                        <tr>
                                            <td>Surgical Deposit</td>
                                            <td>Cancelled by patient (&gt;7 days)</td>
                                            <td><span class="cp-badge partial">Partial Refund</span></td>
                                        </tr>
                                        <tr>
                                            <td>Surgical Deposit</td>
                                            <td>Cancelled by patient (&lt;7 days)</td>
                                            <td><span class="cp-badge none">No Refund</span></td>
                                        </tr>
                                        <tr>
                                            <td>IPD Advance Deposit</td>
                                            <td>Before admission</td>
                                            <td><span class="cp-badge full">Full Refund</span></td>
                                        </tr>
                                        <tr>
                                            <td>IPD Advance Deposit</td>
                                            <td>After admission</td>
                                            <td><span class="cp-badge na">Adjusted at Discharge</span></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="tc-highlight blue" style="margin-top:20px;">
                                    <strong>Refund Processing Time:</strong> All approved refunds are processed within <strong>7–10 working days</strong> from the date of the approved cancellation request. Refunds are credited to the original mode of payment (cash refund via cheque/NEFT, card refunds via banking channel, UPI refund to source account).
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 05 — No-Show Policy -->
                        <div class="tc-section" id="cp-5">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-user-xmark"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 05</div>
                                    <h2 class="tc-section-title">No-Show Policy</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>A "No-Show" is defined as failing to attend a confirmed appointment without prior cancellation notice. The following terms apply to no-show cases:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        Patients who do not attend their scheduled OPD appointment without any prior notification will forfeit the full consultation fee paid in advance.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        No-show patients are not eligible for a refund or fee credit for the missed appointment slot.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        Repeated no-shows (3 or more instances) may result in a temporary restriction on advance appointment booking at R.K. Hospital.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        In case of a genuine emergency that prevents attendance, the patient or attendant should inform the hospital as soon as possible. Such cases will be reviewed on a compassionate basis by our management.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Documentation (e.g., emergency report, accident certificate) may be required to support a compassionate cancellation request.
                                    </li>
                                </ul>
                                <div class="tc-highlight orange">
                                    <strong>We understand emergencies happen.</strong> Please call us at <strong>+91 97660 57372</strong> as soon as possible if you are unable to make your appointment — even last-minute communication helps us accommodate other waiting patients.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 06 — Rescheduling -->
                        <div class="tc-section" id="cp-6">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 06</div>
                                    <h2 class="tc-section-title">Rescheduling Policy</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>We offer flexible rescheduling options so that patients do not lose the value of their booking in case of genuine inconvenience:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Appointments can be rescheduled free of charge up to <strong>2 times</strong>, provided the rescheduling is requested at least 2 hours before the original appointment time.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        The rescheduled appointment must be taken within <strong>30 calendar days</strong> of the original appointment date. After 30 days, the booking fee will be forfeited.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Rescheduling is subject to doctor availability. The hospital will make every effort to accommodate the patient's preferred time slot.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        If the rescheduled appointment involves a more senior specialist or a different department with a higher fee, the patient must pay the differential amount.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        To reschedule, contact our reception at <strong>+91 97660 57372</strong> during OPD hours: 11AM–4PM and 7PM–9PM, Mon–Saturday.
                                    </li>
                                </ul>
                                <div class="tc-info-grid">
                                    <div class="tc-info-card">
                                        <i class="fa-solid fa-phone-volume"></i>
                                        <div>
                                            <h6>Call to Reschedule</h6>
                                            <p>+91 97660 57372</p>
                                        </div>
                                    </div>
                                    <div class="tc-info-card">
                                        <i class="fa-regular fa-clock"></i>
                                        <div>
                                            <h6>Reschedule Window</h6>
                                            <p>Within 30 days of original date<br>Up to 2 free reschedules</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 07 — Surgery & IPD Cancellation -->
                        <div class="tc-section" id="cp-7">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-kit-medical"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 07</div>
                                    <h2 class="tc-section-title">Surgical Procedure & IPD Cancellation</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>Cancellation of scheduled surgeries, inpatient procedures, or planned admissions is handled with greater care due to the resources involved in pre-operative preparations:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Surgical cancellation requests by the patient must be submitted in writing or via a recorded phone call to our admissions department.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Cancellations made <strong>more than 7 days</strong> before the scheduled surgery date: a partial refund will be issued after deducting administrative and pre-operative preparation costs.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Cancellations made <strong>within 7 days</strong> of surgery: the advance surgical deposit is non-refundable. However, the amount may be adjusted toward a rescheduled procedure within 60 days.
                                    </li>
                                    <li>
                                        <span class="li-icon green"><i class="fa-solid fa-check"></i></span>
                                        If the surgery is cancelled by <strong>the hospital</strong> due to medical necessity, equipment issues, or unavailability of the surgeon, a <strong>full refund</strong> of the deposit is issued within 7–10 working days.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Amounts spent on pre-operative investigations (blood tests, X-rays, ECG, etc.) conducted at R.K. Hospital are non-refundable regardless of the cause of surgery cancellation.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        IPD advance deposits paid at the time of admission are adjusted against the final bill at the time of discharge and cannot be refunded mid-admission except in exceptional circumstances approved by management.
                                    </li>
                                </ul>
                                <div class="tc-highlight">
                                    <strong>Please Note:</strong> For planned surgeries, we request patients to inform us of any cancellation at the earliest possible opportunity. This helps us prioritise other patients awaiting their procedures and avoids unnecessary operational delays.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 08 — Insurance & Cashless -->
                        <div class="tc-section" id="cp-8">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-shield-heart"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 08</div>
                                    <h2 class="tc-section-title">Insurance & Cashless Treatment Cancellation</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>For patients utilising health insurance or cashless treatment facilities, additional terms apply:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Cancellation of cashless admission must be communicated to both R.K. Hospital and the patient's insurance provider, as cancellation procedures may vary by insurer.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Pre-authorisation obtained for a specific procedure cannot be transferred to a different procedure. A fresh pre-authorisation will be required in case of rescheduling.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Any out-of-pocket amounts paid by the patient under co-payment or deductible clauses are governed by the terms of the patient's insurance policy and are subject to that insurer's refund rules.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        R.K. Hospital is not responsible for delays in reimbursement caused by the patient's insurance provider. All insurance-related disputes must be resolved directly with the insurer.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Self-paid amounts deposited for insurance-covered admissions that get rejected by the insurer will be handled as per R.K. Hospital's standard refund policy.
                                    </li>
                                </ul>
                                <div class="tc-highlight blue">
                                    <strong>Cashless Verification:</strong> R.K. Hospital accepts cashless treatment under most major TPA and insurance providers. Please contact our billing desk at <strong>+91 97660 57372</strong> to verify your insurer before scheduling your admission.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 09 — Emergency Exceptions -->
                        <div class="tc-section" id="cp-9">
                            <div class="tc-section-header">
                                <div class="tc-section-icon green">
                                    <i class="fa-solid fa-truck-medical"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 09</div>
                                    <h2 class="tc-section-title">Emergency & Special Exceptions</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>We are a compassionate institution and we review genuine emergency cases on an individual basis. The following exceptions apply:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon green"><i class="fa-solid fa-check"></i></span>
                                        <span>In the event of a <strong>patient's death</strong> prior to the appointment or procedure, the full amount paid will be refunded to the next of kin upon submission of the death certificate.</span>
                                    </li>
                                    <li>
                                        <span class="li-icon green"><i class="fa-solid fa-check"></i></span>
                                        <span>If a patient is <strong>hospitalised elsewhere</strong> due to an unrelated emergency and cannot attend their scheduled appointment, a reschedule or refund may be considered upon submission of hospital documentation.</span>
                                    </li>
                                    <li>
                                        <span class="li-icon green"><i class="fa-solid fa-check"></i></span>
                                        <span>Cancellations arising due to <strong>natural disasters, government-declared curfews, or public health emergencies</strong> (e.g., lockdowns) are treated under a full-credit policy.</span>
                                    </li>
                                    <li>
                                        <span class="li-icon green"><i class="fa-solid fa-check"></i></span>
                                        <span>All exceptional cases must be submitted in writing to our Management Office within <strong>15 days</strong> of the missed appointment, along with supporting documentation.</span>
                                    </li>
                                </ul>
                                <div class="tc-highlight green">
                                    <strong>Our Commitment:</strong> We aim to handle all exceptional cases with empathy and understanding. No patient should feel financially penalised due to circumstances beyond their control. Please reach out to us — we will do our best to help.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 10 — How to Cancel -->
                        <div class="tc-section" id="cp-10">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-list-check"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 10</div>
                                    <h2 class="tc-section-title">How to Cancel Your Appointment</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>Cancelling your appointment at R.K. Hospital is simple. Use any of the following methods:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-phone"></i></span>
                                        <span><strong>By Phone:</strong> Call our helpline at <strong>+91 97660 57372</strong> during OPD hours (11AM–4PM and 7PM–9PM, Mon–Sat). For urgent cancellations, our emergency line is available 24/7.</span>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-person-walking-arrow-right"></i></span>
                                        <span><strong>In Person:</strong> Visit the hospital reception at <strong>27, Central Avenue Road, Beside Hotel Al Zam Zam, Gandhibagh, Nagpur – 440002</strong> and inform the front desk staff of your cancellation request.</span>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-envelope"></i></span>
                                        <span><strong>Via Website:</strong> Use the Contact Us form on our website to submit a written cancellation request. Please include your name, appointment date, and registered phone number.</span>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-receipt"></i></span>
                                        <span><strong>For Refund Requests:</strong> Submit your cancellation confirmation and payment receipt to our billing department. Refunds will be processed within 7–10 working days.</span>
                                    </li>
                                </ul>
                                <div class="tc-info-grid">
                                    <div class="tc-info-card">
                                        <i class="fa-solid fa-phone-volume"></i>
                                        <div>
                                            <h6>Cancellation Helpline</h6>
                                            <p>+91 97660 57372</p>
                                        </div>
                                    </div>
                                    <div class="tc-info-card">
                                        <i class="fa-solid fa-location-dot red"></i>
                                        <div>
                                            <h6>Hospital Address</h6>
                                            <p>27 Chandrashekhar, Azad Square, Central Ave, Ladpura, Itwari, Nagpur, Maharashtra 440002</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tc-highlight blue" style="margin-top:20px;">
                                    For any queries about this Cancellation &amp; Refund Policy, please contact our billing department or reach us via the Contact Us page on our website. We are committed to resolving all concerns promptly and fairly.
                                </div>
                            </div>
                        </div>

                        <!-- AGREEMENT BANNER -->
                        <div class="tc-agreement-banner">
                            <i class="fa-solid fa-handshake"></i>
                            <h3>We're Here to Make It Easy for You</h3>
                            <p>
                                Our cancellation policy is built on trust and transparency. If you have any doubts or concerns, our team is always available to assist you at no extra hassle.
                            </p>
                            <div class="tc-agreement-actions">
                                <a href="contact-us" class="btn-tc-primary">
                                    <i class="fa-solid fa-calendar-plus"></i> Book an Appointment
                                </a>
                                <a href="terms-conditions.php" class="btn-tc-outline">
                                    <i class="fa-solid fa-file-lines"></i> View Terms &amp; Conditions
                                </a>
                            </div>
                        </div>

                    </div>
                    <!-- ── END MAIN CONTENT ── -->

                </div>
            </div>
        </section>

        <?php include 'include/footer.php'; ?>

        <!-- Cursor -->
        <div class="mouse-cursor cursor-outer"></div>
        <div class="mouse-cursor cursor-inner"></div>
    </div>
    <!-- /Main Wrapper -->

    <!-- Offcanvas -->
    <div class="offcanvas offcanvas-offset offcanvas-end support_popup" tabindex="-1" id="support_item">
        <div class="offcanvas-header">
            <a href="index.php"><img src="assets/img/logo.svg" alt="logo" class="img-fluid logo"></a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="isax isax-close-circle"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <div class="about-popup-item">
                <h3 class="title">About R.K. Hospital</h3>
                <p>Leading Orthopedic & Gynecology Hospital in Nagpur with 25+ years of medical excellence.</p>
                <div class="about-img d-flex align-items-center gap-2 justify-content-between">
                    <a href="<?php echo $base_url; ?>assets/img/banner/about-img-1.jpg" data-fancybox="gallery">
                        <img src="assets/img/banner/about-img-1.jpg" alt="RK Hospital Nagpur" class="img-fluid">
                    </a>
                    <a href="<?php echo $base_url; ?>assets/img/banner/about-img-2.jpg" data-fancybox="gallery">
                        <img src="assets/img/banner/about-img-2.jpg" alt="RK Hospital Nagpur" class="img-fluid">
                    </a>
                    <a href="<?php echo $base_url; ?>assets/img/banner/about-img-3.jpg" data-fancybox="gallery">
                        <img src="assets/img/banner/about-img-3.jpg" alt="RK Hospital Nagpur" class="img-fluid">
                    </a>
                </div>
            </div>
            <div class="about-popup-item">
                <h3 class="title">Hospital Location</h3>
                <div class="loction-item mb-3">
                    <h4 class="title">R.K. Hospital Nagpur</h4>
                    <p class="location">27, Central Avenue Road, Beside Hotel Al Zam Zam, Gandhibagh, Nagpur – 440002</p>
                </div>
            </div>
            <div class="about-popup-item">
                <h3 class="title">Contact Information</h3>
                <div class="support-item mb-3">
                    <div class="avatar avatar-lg bg-primary rounded-circle">
                        <i class="isax isax-call-calling"></i>
                    </div>
                    <div>
                        <p class="title">24/7 Emergency</p>
                        <h5 class="link"><a href="tel:+919766057372">+91 97660 57372</a></h5>
                    </div>
                </div>
                <div class="support-item">
                    <div class="avatar avatar-lg bg-primary rounded-circle">
                        <i class="isax isax-call-calling"></i>
                    </div>
                
                </div>
            </div>
            <div class="about-popup-item border-0">
                <h3 class="title">Follow Us</h3>
                <ul class="d-flex align-items-center gap-2 social-iyem">
                    <li><a href="#" class="social-icon"><i class="fa-brands fa-facebook"></i></a></li>
                    <li><a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a></li>
                    <li><a href="#" class="social-icon"><i class="fa-brands fa-youtube"></i></a></li>
                </ul>
            </div>
        </div>
        <img src="assets/img/bg/offcanvas-bg.png" alt="element" class="element-01">
    </div>

    <!-- ScrollToTop -->
    <div class="progress-wrap active-progress">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;"></path>
        </svg>
    </div>

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>
    <!-- BacktoTop JS -->
    <script src="assets/js/backToTop.js"></script>
    <!-- select JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <!-- Slick Slider -->
    <script src="assets/plugins/slick/slick.min.js"></script>
    <!-- Fancybox JS -->
    <script src="assets/plugins/fancybox/jquery.fancybox.min.js"></script>
    <!-- Counter JS -->
    <script src="assets/js/counter.js"></script>
    <!-- Wow JS -->
    <script src="assets/plugins/wow/js/wow.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <script>
        // ── TOC Active State on Scroll ──
        (function () {
            const sections = document.querySelectorAll('.tc-section[id]');
            const tocLinks = document.querySelectorAll('.tc-toc-list a');

            function updateActive() {
                let current = '';
                sections.forEach(sec => {
                    const top = sec.getBoundingClientRect().top;
                    if (top <= 120) current = sec.getAttribute('id');
                });
                tocLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('active');
                    }
                });
            }

            window.addEventListener('scroll', updateActive, { passive: true });
            updateActive();
        })();

        // ── ScrollToTop Progress ──
        (function () {
            const wrap = document.querySelector('.progress-wrap');
            const path = wrap ? wrap.querySelector('path') : null;
            if (!wrap || !path) return;
            const pathLength = path.getTotalLength();
            path.style.strokeDasharray = pathLength + 'px';
            path.style.strokeDashoffset = pathLength + 'px';
            path.style.transition = 'stroke-dashoffset 10ms linear';

            function updateProgress() {
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrolled = scrollTop / docHeight;
                path.style.strokeDashoffset = (pathLength - pathLength * scrolled) + 'px';
                wrap.classList.toggle('active-progress', scrollTop > 200);
            }
            window.addEventListener('scroll', updateProgress, { passive: true });
            updateProgress();
        })();
    </script>

</body>
</html>