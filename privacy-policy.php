<?php $base_url = "http://localhost/hpce/"; ?>
<?php include 'include/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Privacy Policy of Dr. Agrawal's R.K. Hospital Nagpur. Learn how we collect, use, and protect your personal and medical information.">
    <meta name="keywords" content="RK Hospital Privacy Policy, Patient Data Protection Nagpur, Hospital Data Privacy RK Hospital">
    <meta name="author" content="Dr. Agrawal's R.K. Hospital Nagpur">
    <title>Privacy Policy | Dr. Agrawal's R.K. Hospital Nagpur</title>

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

        /* ─── BANNER ─── */
        .contact-hero-banner {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* ─── META BAR ─── */
        .pp-meta-bar {
            background: var(--off-white);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }

        .pp-meta-inner {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .pp-updated-badge {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 8px;
            font-size: 12.5px;
            color: var(--text-soft);
            line-height: 1.6;
        }
        .pp-updated-badge i { color: var(--primary); flex-shrink: 0; }
        .pp-updated-badge strong { color: var(--text-dark); }

        .pp-print-btn {
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
        .pp-print-btn:hover { background: var(--white); border-color: var(--red); color: var(--red); }

        @media (min-width: 768px) {
            .pp-meta-inner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        @media (max-width: 767px) {
            .pp-print-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* ─── MAIN LAYOUT ─── */
        .pp-wrapper {
            padding: 60px 0 80px;
            background: #fff;
        }

        /* ─── STICKY SIDEBAR TOC ─── */
        .pp-sidebar {
            position: sticky;
            top: 90px;
        }

        .pp-toc {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .pp-toc-header {
            background: linear-gradient(135deg, var(--red-dark), var(--red));
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .pp-toc-header i { color: #fff; font-size: 16px; }
        .pp-toc-header h5 {
            margin: 0;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .pp-toc-list { padding: 14px 0; }
        .pp-toc-list a {
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
        .pp-toc-list a .toc-num {
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
            flex-shrink: 0;
        }
        .pp-toc-list a:hover {
            color: var(--red);
            background: var(--red-bg);
            border-left-color: var(--red);
        }
        .pp-toc-list a:hover .toc-num {
            background: var(--red);
            color: #fff;
            border-color: var(--red);
        }
        .pp-toc-list a.active {
            color: var(--red);
            background: var(--red-bg);
            border-left-color: var(--red);
            font-weight: 700;
        }
        .pp-toc-list a.active .toc-num {
            background: var(--red);
            color: #fff;
        }

        /* Contact Card in Sidebar */
        .pp-contact-card {
            background: linear-gradient(135deg, #0d1b4b 0%, #1a3fa3 100%);
            border-radius: 18px;
            padding: 26px 22px;
            margin-top: 20px;
            text-align: center;
        }
        .pp-contact-card i { font-size: 28px; color: rgba(255,255,255,0.8); margin-bottom: 12px; display: block; }
        .pp-contact-card h6 { color: #fff; font-size: 14px; font-weight: 700; margin-bottom: 6px; }
        .pp-contact-card p { color: rgba(255,255,255,0.7); font-size: 12.5px; margin-bottom: 16px; }
        .pp-contact-card a {
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
        .pp-contact-card a:hover { background: var(--red-dark); transform: translateY(-1px); color: #fff; }

        /* ─── CONTENT SECTIONS ─── */
        .pp-section {
            margin-bottom: 48px;
            scroll-margin-top: 100px;
        }

        .pp-section-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 2px solid var(--border);
        }

        .pp-section-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .pp-section-icon.red { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
        .pp-section-icon.blue { background: var(--blue-bg); color: var(--primary); border: 1px solid var(--blue-border); }

        .pp-section-header-text { flex: 1; }
        .pp-section-num {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--red);
            margin-bottom: 3px;
        }
        .pp-section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
        }

        .pp-prose {
            font-size: 14.5px;
            color: var(--text-mid);
            line-height: 1.85;
        }

        /* List style */
        .pp-list {
            list-style: none;
            padding: 0;
            margin: 14px 0 0;
        }
        .pp-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: var(--text-mid);
            line-height: 1.65;
        }
        .pp-list li:last-child { border-bottom: none; }
        .pp-list li .li-icon {
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
        .pp-list li .li-icon.blue { background: var(--blue-bg); color: var(--primary); border-color: var(--blue-border); }

        /* Highlight box */
        .pp-highlight {
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
        .pp-highlight.blue {
            background: var(--blue-bg);
            border-color: var(--blue-border);
            border-left-color: var(--primary);
        }
        .pp-highlight strong { color: var(--text-dark); }

        /* Info grid */
        .pp-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 18px;
        }
        .pp-info-card {
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .pp-info-card i {
            font-size: 18px;
            color: var(--primary);
            margin-top: 2px;
            flex-shrink: 0;
        }
        .pp-info-card h6 { font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; }
        .pp-info-card p { font-size: 12.5px; color: var(--text-soft); margin: 0; line-height: 1.5; }

        /* Data Table */
        .pp-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            font-size: 13.5px;
            overflow: hidden;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .pp-data-table thead tr {
            background: linear-gradient(135deg, var(--red-dark), var(--red));
        }
        .pp-data-table thead th {
            color: #fff;
            font-weight: 700;
            padding: 12px 16px;
            text-align: left;
            font-size: 12.5px;
            letter-spacing: 0.3px;
        }
        .pp-data-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }
        .pp-data-table tbody tr:last-child { border-bottom: none; }
        .pp-data-table tbody tr:nth-child(even) { background: var(--off-white); }
        .pp-data-table tbody tr:hover { background: var(--red-bg); }
        .pp-data-table tbody td {
            padding: 11px 16px;
            color: var(--text-mid);
            vertical-align: top;
            line-height: 1.6;
        }
        .pp-data-table tbody td:first-child {
            font-weight: 600;
            color: var(--text-dark);
        }

        /* ─── AGREEMENT FOOTER BANNER ─── */
        .pp-agreement-banner {
            background: linear-gradient(135deg, #0d1b4b 0%, #1a1a2e 50%, #7f0000 100%);
            border-radius: 20px;
            padding: 48px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: 16px;
        }
        .pp-agreement-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M20 20.5V18H0v5h5v5H0v5h20v-2.5h-5V20.5h5zM15 45V20H0v5h5v5H0v5h5v5H0v5h15v-5H5v-5h5v-5H5v-5h10z'/%3E%3C/g%3E%3C/svg%3E");
        }
        .pp-agreement-banner i {
            font-size: 42px;
            color: rgba(255,255,255,0.35);
            display: block;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }
        .pp-agreement-banner h3 {
            color: #fff;
            font-size: 1.55rem;
            font-weight: 800;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        .pp-agreement-banner p {
            color: rgba(255,255,255,0.7);
            font-size: 14.5px;
            max-width: 540px;
            margin: 0 auto 26px;
            position: relative;
            z-index: 1;
        }
        .pp-agreement-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        .btn-pp-primary {
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
        .btn-pp-primary:hover {
            background: linear-gradient(135deg, #7f0000, var(--red-dark));
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(183,28,28,0.55);
            color: #fff;
        }
        .btn-pp-outline {
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
        .btn-pp-outline:hover {
            border-color: #fff;
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
        ═══════════════════════════════════════════ */

        @media (max-width: 991px) {
            .pp-sidebar {
                position: static;
                margin-bottom: 32px;
            }
        }

        @media (max-width: 768px) {
            .pp-info-grid {
                grid-template-columns: 1fr;
            }
            .pp-agreement-banner {
                padding: 36px 24px;
            }
            .pp-agreement-banner h3 {
                font-size: 1.25rem;
            }
            .pp-wrapper {
                padding: 40px 0 60px;
            }
            .pp-data-table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (max-width: 576px) {
            .pp-section-header {
                flex-direction: column;
                gap: 12px;
            }
            .pp-section-title {
                font-size: 1.1rem;
            }
            .pp-prose {
                font-size: 14px;
            }
            .pp-highlight {
                font-size: 13px;
                padding: 14px 16px;
            }
            .pp-info-card {
                padding: 14px 14px;
            }
            .pp-agreement-banner {
                padding: 28px 18px;
            }
            .btn-pp-primary,
            .btn-pp-outline {
                width: 100%;
                justify-content: center;
                padding: 13px 20px;
            }
            .pp-agreement-actions {
                flex-direction: column;
                gap: 10px;
            }
            .pp-section {
                margin-bottom: 36px;
            }
        }
       
.tc-sidebar {
    position: -webkit-sticky;
    position: sticky;
    top: 100px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}
    </style>
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <?php include 'include/header.php'; ?>

        <!-- ═══ HERO BANNER ═══ -->
        <section class="contact-hero-banner">
            <img src="assets/img/home/image-crousel1.webp" alt="RK Hospital Nagpur Privacy Policy" class="banner-img">
            <div class="banner-grid-pattern"></div>
            <div class="banner-overlay"></div>

            <!-- Floating Stat Badges -->
            <div class="banner-stat-badge badge-left">
                <div class="badge-icon"><i class="fa-solid fa-star"></i></div>
                <div class="badge-text">
                    <div class="num">5.0 ★</div>
                    <div class="label">496+ Reviews</div>
                </div>
            </div>

            <div class="banner-stat-badge badge-right">
                <div class="badge-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="badge-text">
                    <div class="num">100%</div>
                    <div class="label">Data Secure</div>
                </div>
            </div>

            <div class="banner-content">
                <div class="banner-eyebrow">
                    <i class="fa-solid fa-hospital"></i>
                    Dr. Agrawal's R.K. Hospital, Nagpur
                </div>
                <h1 class="banner-heading">
                    Legal &amp; Policy Documents<br>
                    <span>Privacy Policy</span>
                </h1>
                <p class="banner-sub">
                    Your privacy and the security of your personal and medical information is our highest priority. Learn how Dr. Agrawal's R.K. Hospital collects, uses, and protects your data.
                </p>
                <div class="banner-cta-group">
                    <a href="tel:+919766057372" class="banner-btn-primary">
                        <i class="fa-solid fa-phone"></i>
                        Call Now: +91 97660 57372
                    </a>
                    <a href="contact-us" class="banner-btn-outline">
                        <i class="fa-solid fa-calendar-check"></i>
                        Book Appointment
                    </a>
                </div>
            </div>
        </section>

        <!-- ═══ META BAR ═══ -->
        <div class="pp-meta-bar">
            <div class="container">
                <div class="pp-meta-inner">
                    <div class="pp-updated-badge">
                        <i class="fa-regular fa-calendar-check"></i>
                        Last Updated: <strong>January 1, 2026</strong>
                        &nbsp;|&nbsp;
                        <i class="fa-solid fa-file-shield"></i>
                        Version: <strong>2.0</strong>
                        &nbsp;|&nbsp;
                        <i class="fa-solid fa-globe"></i>
                        Applicable: <strong>R.K. Hospital, Nagpur</strong>
                    </div>
                    <button class="pp-print-btn" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Print / Save PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══ MAIN CONTENT ═══ -->
        <section class="pp-wrapper">
            <div class="container">
                <div class="row g-5">

                    <!-- ── SIDEBAR ── -->
                    <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                        <div class="pp-sidebar">

                            <!-- Table of Contents -->
                            <div class="pp-toc">
                                <div class="pp-toc-header">
                                    <i class="fa-solid fa-list-ul"></i>
                                    <h5>Table of Contents</h5>
                                </div>
                                <div class="pp-toc-list">
                                    <a href="#pp-1" class="active">
                                        <span class="toc-num">01</span> Introduction
                                    </a>
                                    <a href="#pp-2">
                                        <span class="toc-num">02</span> Information We Collect
                                    </a>
                                    <a href="#pp-3">
                                        <span class="toc-num">03</span> How We Use Your Data
                                    </a>
                                    <a href="#pp-4">
                                        <span class="toc-num">04</span> Data Sharing & Disclosure
                                    </a>
                                    <a href="#pp-5">
                                        <span class="toc-num">05</span> Medical Records Policy
                                    </a>
                                    <a href="#pp-6">
                                        <span class="toc-num">06</span> Cookies & Website Data
                                    </a>
                                    <a href="#pp-7">
                                        <span class="toc-num">07</span> Data Security
                                    </a>
                                    <a href="#pp-8">
                                        <span class="toc-num">08</span> Your Rights & Choices
                                    </a>
                                    <a href="#pp-9">
                                        <span class="toc-num">09</span> Children's Privacy
                                    </a>
                                    <a href="#pp-10">
                                        <span class="toc-num">10</span> Policy Updates & Contact
                                    </a>
                                </div>
                            </div>

                            <!-- Contact Card -->
                            <div class="pp-contact-card">
                                <i class="fa-solid fa-shield-halved"></i>
                                <h6>Privacy Concerns?</h6>
                                <p>Contact our Data Protection Officer for any privacy-related queries.</p>
                                <a href="contact-us">
                                    <i class="fa-solid fa-envelope"></i> Contact Us
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- ── MAIN CONTENT ── -->
                    <div class="col-lg-8 col-xl-9">

                        <!-- SECTION 01 — Introduction -->
                        <div class="pp-section" id="pp-1">
                            <div class="pp-section-header">
                                <div class="pp-section-icon red">
                                    <i class="fa-solid fa-hospital"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 01</div>
                                    <h2 class="pp-section-title">Introduction</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>
                                    At <strong>Dr. Agrawal's R.K. Hospital, Nagpur</strong>, we believe that every patient has a fundamental right to privacy. This Privacy Policy ("Policy") explains how we collect, use, store, protect, and share your personal and medical information when you visit our hospital, use our website, or avail any of our healthcare services.
                                </p>
                                <p style="margin-top:12px;">
                                    This Policy applies to all patients, attendants, website visitors, and any individual whose information is processed by R.K. Hospital. By using our services or providing your information, you consent to the practices described in this Privacy Policy.
                                </p>
                                <p style="margin-top:12px;">
                                    We are committed to complying with applicable Indian laws including the <strong>Information Technology Act, 2000</strong>, the <strong>IT (Amendment) Act, 2008</strong>, and the <strong>Digital Personal Data Protection Act, 2023 (DPDPA)</strong> to ensure the highest standard of data privacy.
                                </p>
                                <div class="pp-highlight">
                                    <strong>Scope:</strong> This Privacy Policy applies to all services offered at Dr. Agrawal's R.K. Hospital, 27, Central Avenue Road, Beside Hotel Al Zam Zam, Gandhibagh, Nagpur – 440002, Maharashtra, including our official website and appointment booking system.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 02 — Information We Collect -->
                        <div class="pp-section" id="pp-2">
                            <div class="pp-section-header">
                                <div class="pp-section-icon blue">
                                    <i class="fa-solid fa-database"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 02</div>
                                    <h2 class="pp-section-title">Information We Collect</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>We collect different types of information depending on the nature of your interaction with us. This includes:</p>

                                <p style="margin-top:16px;"><strong>A. Personal Identification Information</strong></p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Full name, date of birth, gender, and age.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Contact details: mobile number, email address, and residential address.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Government-issued ID (Aadhaar, PAN, or Passport) where required for billing and insurance purposes.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Emergency contact information and next-of-kin details.
                                    </li>
                                </ul>

                                <p style="margin-top:18px;"><strong>B. Medical & Health Information</strong></p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Medical history, past diagnoses, surgeries, and treatment records.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Current medications, allergies, and pre-existing conditions.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Lab reports, X-rays, MRI, USG scans, and other diagnostic data.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Doctor consultation notes, prescription records, and discharge summaries.
                                    </li>
                                </ul>

                                <p style="margin-top:18px;"><strong>C. Financial Information</strong></p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Payment details including billing records, invoices, and receipts.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Health insurance policy number and TPA (Third Party Administrator) details.
                                    </li>
                                </ul>

                                <p style="margin-top:18px;"><strong>D. Website & Digital Information</strong></p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        IP address, browser type, device type, and operating system.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Pages visited, time spent on website, and clickstream data.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Information submitted via our contact form or appointment booking system.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECTION 03 — How We Use Your Data -->
                        <div class="pp-section" id="pp-3">
                            <div class="pp-section-header">
                                <div class="pp-section-icon red">
                                    <i class="fa-solid fa-gears"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 03</div>
                                    <h2 class="pp-section-title">How We Use Your Information</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>Your information is used exclusively for legitimate healthcare and operational purposes:</p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Patient Care & Treatment:</strong> To provide accurate diagnosis, treatment plans, surgical procedures, and follow-up care tailored to your specific medical needs.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Appointment Management:</strong> To schedule, confirm, reschedule, and send reminders for your OPD or IPD appointments via SMS or WhatsApp.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Billing & Insurance:</strong> To generate invoices, process insurance claims, verify coverage, and manage financial transactions related to your treatment.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Legal & Regulatory Compliance:</strong> To maintain medical records as mandated under applicable Indian healthcare laws and government regulations.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Safety & Security:</strong> To monitor hospital premises via CCTV for the safety of patients, staff, and visitors.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Service Improvement:</strong> To analyze anonymized, aggregated data for enhancing our healthcare services, infrastructure, and patient experience.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Communication:</strong> To send you important health updates, post-treatment care instructions, and information about our services. You may opt out of non-essential communications at any time.</div>
                                    </li>
                                </ul>
                                <div class="pp-highlight blue">
                                    <strong>Important:</strong> We will <strong>never</strong> use your personal or medical information for marketing or advertising purposes without your explicit written consent.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 04 — Data Sharing -->
                        <div class="pp-section" id="pp-4">
                            <div class="pp-section-header">
                                <div class="pp-section-icon blue">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 04</div>
                                    <h2 class="pp-section-title">Data Sharing & Disclosure</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>R.K. Hospital does not sell, rent, or trade your personal information. We share data only in the following strictly controlled circumstances:</p>

                                <table class="pp-data-table">
                                    <thead>
                                        <tr>
                                            <th>Recipient</th>
                                            <th>Purpose</th>
                                            <th>Basis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Treating Doctors & Specialists</td>
                                            <td>Direct patient care, referrals, and second opinions</td>
                                            <td>Medical Necessity</td>
                                        </tr>
                                        <tr>
                                            <td>Insurance Companies / TPA</td>
                                            <td>Cashless claim processing and reimbursement</td>
                                            <td>Patient Consent</td>
                                        </tr>
                                        <tr>
                                            <td>Diagnostic Laboratories</td>
                                            <td>Processing tests, reports, and pathology work</td>
                                            <td>Treatment Purpose</td>
                                        </tr>
                                        <tr>
                                            <td>Government Authorities</td>
                                            <td>Legal obligations, court orders, public health reporting</td>
                                            <td>Legal Compliance</td>
                                        </tr>
                                        <tr>
                                            <td>IT Service Providers</td>
                                            <td>Hospital management software and website hosting (under NDA)</td>
                                            <td>Operational Necessity</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="pp-highlight" style="margin-top:20px;">
                                    All third parties with whom we share data are bound by strict confidentiality agreements. We do not transfer your data outside India without your explicit written consent.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 05 — Medical Records Policy -->
                        <div class="pp-section" id="pp-5">
                            <div class="pp-section-header">
                                <div class="pp-section-icon red">
                                    <i class="fa-solid fa-file-medical"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 05</div>
                                    <h2 class="pp-section-title">Medical Records Policy</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>Medical records are among the most sensitive personal data we hold. Our policy for their management is as follows:</p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        All patient medical records — including OPD notes, IPD files, surgical records, and diagnostic reports — are maintained in strict confidentiality and accessible only to authorized medical personnel.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Medical records are retained for a minimum period of <strong>7 years</strong> from the date of last treatment as per Indian Medical Council guidelines. For minors, records are retained until 7 years after they attain the age of 18.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        You or your authorized legal guardian may request a copy of your medical records. Requests must be submitted in writing to the Medical Records Department with valid photo ID proof.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        A nominal administrative fee may be charged for providing printed copies of medical records, X-rays, or other imaging reports.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        In medico-legal cases, medical records may be disclosed to law enforcement agencies or courts upon receipt of a valid legal order.
                                    </li>
                                </ul>

                                <div class="pp-info-grid">
                                    <div class="pp-info-card">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                        <div>
                                            <h6>Retention Period</h6>
                                            <p>Minimum 7 years as per MCI guidelines</p>
                                        </div>
                                    </div>
                                    <div class="pp-info-card">
                                        <i class="fa-solid fa-file-export"></i>
                                        <div>
                                            <h6>Record Request</h6>
                                            <p>Submit written request with photo ID to MRD department</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 06 — Cookies -->
                        <div class="pp-section" id="pp-6">
                            <div class="pp-section-header">
                                <div class="pp-section-icon blue">
                                    <i class="fa-solid fa-cookie-bite"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 06</div>
                                    <h2 class="pp-section-title">Cookies & Website Data</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>Our website uses cookies and similar technologies to enhance your browsing experience. Here is how we use them:</p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Essential Cookies:</strong> Required for the basic functioning of our website, such as maintaining your session when booking an appointment or filling contact forms.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Analytics Cookies:</strong> We use tools like Google Analytics to understand how visitors interact with our website. All data collected is anonymized and aggregated.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Performance Cookies:</strong> Help us identify slow-loading pages and improve website speed for a better user experience.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        We do <strong>not</strong> use cookies to track patients across third-party websites, build advertising profiles, or share data with marketing agencies.
                                    </li>
                                </ul>
                                <div class="pp-highlight blue">
                                    You can control or disable cookies through your browser settings at any time. Disabling certain cookies may affect the functionality of our appointment booking system. We recommend keeping essential cookies enabled for the best experience.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 07 — Data Security -->
                        <div class="pp-section" id="pp-7">
                            <div class="pp-section-header">
                                <div class="pp-section-icon red">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 07</div>
                                    <h2 class="pp-section-title">Data Security Measures</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>We take the security of your data extremely seriously. The following safeguards are in place to protect your information:</p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Physical Security:</strong> Patient files and medical records are stored in locked, access-controlled areas accessible only to authorized hospital staff.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Digital Security:</strong> Electronic medical records are protected with password-controlled access, role-based permissions, and regular security audits.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>CCTV Surveillance:</strong> The hospital premises are monitored 24/7 by CCTV cameras for the safety of patients and staff. Footage is retained for 30 days and accessible only to authorized personnel.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Staff Training:</strong> All hospital staff handling patient data receive regular training on data privacy, confidentiality, and information security protocols.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Data Breach Protocol:</strong> In the event of any data breach that may affect your personal information, we will notify affected individuals and relevant authorities as required by applicable law.</div>
                                    </li>
                                </ul>
                                <div class="pp-highlight">
                                    <strong>Note:</strong> While we implement robust security measures, no method of transmission over the internet or electronic storage is 100% secure. We continuously work to strengthen our data protection systems.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 08 — Your Rights -->
                        <div class="pp-section" id="pp-8">
                            <div class="pp-section-header">
                                <div class="pp-section-icon blue">
                                    <i class="fa-solid fa-person-circle-check"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 08</div>
                                    <h2 class="pp-section-title">Your Rights & Choices</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>Under applicable Indian privacy laws, you have the following rights with respect to your personal data held by R.K. Hospital:</p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Right to Access:</strong> You may request a copy of the personal and medical information we hold about you. We will respond to such requests within 30 working days.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Right to Correction:</strong> If any of your personal details are inaccurate or outdated, you may request us to correct or update them. Please bring valid supporting documents for verification.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Right to Erasure:</strong> In certain circumstances (not applicable to mandatory medical record retention), you may request deletion of personal data not required for legal or medical purposes.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Right to Withdraw Consent:</strong> You may withdraw consent for non-essential communications (such as promotional messages) at any time by contacting our reception or using the opt-out link in messages.</div>
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        <div><strong>Right to Complain:</strong> If you feel your data privacy rights have been violated, you may file a complaint with the Data Protection Board of India or approach the appropriate consumer forum.</div>
                                    </li>
                                </ul>

                                <div class="pp-info-grid">
                                    <div class="pp-info-card">
                                        <i class="fa-solid fa-envelope"></i>
                                        <div>
                                            <h6>Submit Data Request</h6>
                                            <p>Visit reception or contact us via our website contact form</p>
                                        </div>
                                    </div>
                                    <div class="pp-info-card">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                        <div>
                                            <h6>Response Timeline</h6>
                                            <p>We respond to all data requests within 30 working days</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 09 — Children's Privacy -->
                        <div class="pp-section" id="pp-9">
                            <div class="pp-section-header">
                                <div class="pp-section-icon red">
                                    <i class="fa-solid fa-child-reaching"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 09</div>
                                    <h2 class="pp-section-title">Children's Privacy</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <p>R.K. Hospital provides medical services to patients of all age groups, including children. Special protections apply to the data of minors:</p>
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        All medical information, consent forms, and data-related decisions for patients under 18 years of age must be handled by a parent or legal guardian.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        We do not knowingly collect personal data directly from children without verifiable parental or guardian consent.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Medical records for minor patients are retained until 7 years after the patient attains the age of 18, as per MCI guidelines.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        If a parent or guardian believes that personal information of a minor has been collected without proper consent, they should contact us immediately so we can take corrective action.
                                    </li>
                                </ul>
                                <div class="pp-highlight blue">
                                    Parents and guardians have the same data access and correction rights on behalf of their minor children as adult patients have for themselves.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 10 — Policy Updates & Contact -->
                        <div class="pp-section" id="pp-10">
                            <div class="pp-section-header">
                                <div class="pp-section-icon blue">
                                    <i class="fa-solid fa-rotate"></i>
                                </div>
                                <div class="pp-section-header-text">
                                    <div class="pp-section-num">Section 10</div>
                                    <h2 class="pp-section-title">Policy Updates & Contact Us</h2>
                                </div>
                            </div>
                            <div class="pp-prose">
                                <ul class="pp-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        R.K. Hospital reserves the right to update or revise this Privacy Policy at any time to reflect changes in law, regulation, or our internal data practices. All updates will be published on this page with a revised "Last Updated" date.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        For significant changes, we will notify registered patients via SMS or email where contact information is available.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Continued use of our services after any updates to this policy constitutes your acceptance of the revised Privacy Policy.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        This Privacy Policy is governed by the laws of the State of Maharashtra, India. Any disputes shall be subject to the jurisdiction of courts in Nagpur.
                                    </li>
                                </ul>

                                <div class="pp-highlight blue" style="margin-top:20px;">
                                    For any privacy-related concerns, data requests, or complaints, please contact us at:<br>
                                    <strong>Dr. Agrawal's R.K. Hospital</strong> | 27, Central Avenue Road, Nagpur – 440002<br>
                                    <strong>Phone:</strong> +91 97660 57372 &nbsp;|&nbsp; <br>
                                    <strong>Email:</strong> info@rkhospitalnagpur.com
                                </div>
                            </div>
                        </div>

                        <!-- AGREEMENT BANNER -->
                        <div class="pp-agreement-banner">
                            <i class="fa-solid fa-shield-halved"></i>
                            <h3>Your Privacy Is Our Commitment</h3>
                            <p>
                                We are dedicated to keeping your personal and medical information safe, secure, and confidential. Trust is the foundation of every patient relationship at R.K. Hospital.
                            </p>
                            <div class="pp-agreement-actions">
                                <a href="contact-us" class="btn-pp-primary">
                                    <i class="fa-solid fa-calendar-plus"></i> Book an Appointment
                                </a>
                                <a href="index.php" class="btn-pp-outline">
                                    <i class="fa-solid fa-house"></i> Back to Home
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
                <p>Leading Orthopedic &amp; Gynecology Hospital in Nagpur with 5+ years of medical excellence.</p>
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
                    <p class="location">27 Chandrashekhar, Azad Square, Central Ave, Ladpura, Itwari, Nagpur, Maharashtra 440002</p>
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
            const sections = document.querySelectorAll('.pp-section[id]');
            const tocLinks = document.querySelectorAll('.pp-toc-list a');

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