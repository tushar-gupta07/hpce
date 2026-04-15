<?php $base_url = "http://localhost/hpce/"; ?>
<?php include 'include/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Terms and Conditions of Dr. Agrawal's R.K. Hospital Nagpur. Read our policies on appointments, medical services, patient rights, and more.">
    <meta name="keywords" content="RK Hospital Terms Conditions, Hospital Policy Nagpur, Patient Rights RK Hospital">
    <meta name="author" content="Dr. Agrawal's R.K. Hospital Nagpur">
    <title>Terms & Conditions | Dr. Agrawal's R.K. Hospital Nagpur</title>

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


/* ─── HERO BREADCRUMB OVERRIDE ─── */
.tc-hero {
    background: linear-gradient(135deg, #0d1b4b 0%, #1a1a2e 40%, #7f0000 100%);
    padding: 72px 0 80px;
    position: relative;
    overflow: hidden;
}

.tc-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.tc-hero .deco-circle {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.06);
}
.tc-hero .deco-circle.c1 { width:420px; height:420px; top:-150px; right:-100px; }
.tc-hero .deco-circle.c2 { width:260px; height:260px; bottom:-100px; left:-60px; }
.tc-hero .deco-circle.c3 { width:160px; height:160px; top:40px; left:30%; background: rgba(211,47,47,0.07); border:none; }

.tc-hero-inner { position: relative; z-index: 2; text-align: center; }

.tc-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    color: #fff;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 6px 20px;
    border-radius: 100px;
    margin-bottom: 22px;
}
.tc-hero-eyebrow i { color: var(--red-light); }

.tc-hero h1 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 14px;
    line-height: 1.18;
}
.tc-hero h1 span { color: var(--red-light); }

.tc-hero p {
    color: rgba(255,255,255,0.72);
    font-size: 15.5px;
    max-width: 560px;
    margin: 0 auto 28px;
}

.tc-breadcrumb {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.55);
}
.tc-breadcrumb a { color: rgba(255,255,255,0.75); text-decoration: none; }
.tc-breadcrumb a:hover { color: var(--red-light); }
.tc-breadcrumb .sep { color: rgba(255,255,255,0.3); }
.tc-breadcrumb .current { color: var(--red-light); font-weight: 600; }

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

/* ─── UPDATED BADGE ─── */
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

/* Desktop meta bar — row layout */
@media (min-width: 768px) {
    .tc-meta-inner {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .tc-updated-badge .sep-pipe {
        display: inline;
    }
}

/* Hide pipes on small mobile, show as new line break via flex wrap */
@media (max-width: 767px) {
    .tc-updated-badge .sep-pipe {
        display: none;
    }
    .tc-print-btn {
        width: 100%;
        justify-content: center;
    }
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
        overflow: hidden;
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
.tc-info-card h6 { font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; }
.tc-info-card p { font-size: 12.5px; color: var(--text-soft); margin: 0; line-height: 1.5; }

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

/* ═══════════════════════════════════════════
   RESPONSIVE BREAKPOINTS
═══════════════════════════════════════════ */

/* ─── TABLET (max 991px) ─── */
@media (max-width: 991px) {
    .tc-sidebar {
        position: static;
        margin-bottom: 32px;
    }
    .tc-toc {
        position: static;
    }
}

/* ─── MOBILE LARGE (max 768px) ─── */
@media (max-width: 768px) {
    .tc-hero {
        padding: 50px 0 60px;
    }
    .tc-info-grid {
        grid-template-columns: 1fr;
    }
    .tc-agreement-banner {
        padding: 36px 24px;
    }
    .tc-agreement-banner h3 {
        font-size: 1.25rem;
    }
    .tc-wrapper {
        padding: 40px 0 60px;
    }
}

/* ─── MOBILE SMALL (max 576px) ─── */
@media (max-width: 576px) {
    .tc-section-header {
        flex-direction: column;
        gap: 12px;
    }
    .tc-section-title {
        font-size: 1.1rem;
    }
    .tc-prose {
        font-size: 14px;
    }
    .tc-highlight {
        font-size: 13px;
        padding: 14px 16px;
    }
    .tc-info-card {
        padding: 14px 14px;
    }
    .tc-agreement-banner {
        padding: 28px 18px;
    }
    .btn-tc-primary,
    .btn-tc-outline {
        width: 100%;
        justify-content: center;
        padding: 13px 20px;
    }
    .tc-agreement-actions {
        flex-direction: column;
        gap: 10px;
    }
    .tc-section {
        margin-bottom: 36px;
    }
}
.contact-hero-banner {
    margin-left: 0 !important;
    margin-right: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
}
    </style>
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <?php include 'include/header.php'; ?>

         <section class="contact-hero-banner">
            <img src="assets/img/home/image-crousel1.webp" alt="RK Hospital Nagpur Contact" class="banner-img">
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
                <div class="badge-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="badge-text">
                    <div class="num">24/7</div>
                    <div class="label">Emergency Care</div>
                </div>
            </div>

            <div class="banner-content">
                <div class="banner-eyebrow">
                    <i class="fa-solid fa-hospital"></i>
                    Dr. Agrawal's R.K. Hospital, Nagpur
                </div>
                <h1 class="banner-heading">
                      Legal & Policy Documents<br>
                    <span>Terms and Conditions</span>
                </h1>
                <p class="banner-sub">
                    Please read these terms carefully before using our hospital services, booking appointments, or accessing any medical treatment at Dr. Agrawal's R.K. Hospital, Nagpur.
                </p>
                <div class="banner-cta-group">
                    <a href="tel:+919766057372" class="banner-btn-primary">
                        <i class="fa-solid fa-phone"></i>
                        Call Now: +91 97660 57372
                    </a>
                    <a href="#contact-form" class="banner-btn-outline">
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
                        Last Updated: <strong>January 1, 2026</strong>
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
                                    <a href="#tc-1" class="active">
                                        <span class="toc-num">01</span> Introduction
                                    </a>
                                    <a href="#tc-2">
                                        <span class="toc-num">02</span> Eligibility & Registration
                                    </a>
                                    <a href="#tc-3">
                                        <span class="toc-num">03</span> Appointment Booking
                                    </a>
                                    <a href="#tc-4">
                                        <span class="toc-num">04</span> Cancellation & Refund
                                    </a>
                                    <a href="#tc-5">
                                        <span class="toc-num">05</span> Medical Disclaimer
                                    </a>
                                    <a href="#tc-6">
                                        <span class="toc-num">06</span> Patient Rights & Duties
                                    </a>
                                    <a href="#tc-7">
                                        <span class="toc-num">07</span> Privacy & Data Protection
                                    </a>
                                    <a href="#tc-8">
                                        <span class="toc-num">08</span> Payment & Billing
                                    </a>
                                    <a href="#tc-9">
                                        <span class="toc-num">09</span> Prohibited Conduct
                                    </a>
                                    <a href="#tc-10">
                                        <span class="toc-num">10</span> Governing Law
                                    </a>
                                </div>
                            </div>

                            <!-- Contact Card -->
                            <div class="tc-contact-card">
                                <i class="fa-solid fa-headset"></i>
                                <h6>Have Questions?</h6>
                                <p>Our team is available 24/7 to help you with any queries.</p>
                                <a href="contact-us">
                                    <i class="fa-solid fa-phone"></i> Contact Us
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- ── MAIN CONTENT ── -->
                    <div class="col-lg-8 col-xl-9">

                        <!-- SECTION 01 — Introduction -->
                        <div class="tc-section" id="tc-1">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-hospital"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 01</div>
                                    <h2 class="tc-section-title">Introduction</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>
                                    Welcome to <strong>Dr. Agrawal's R.K. Hospital</strong>, one of the leading orthopedic and gynecology hospitals in Nagpur, Maharashtra. These Terms and Conditions ("Terms") govern your access to and use of our medical services, website, appointment booking system, and all associated healthcare facilities operated by R.K. Hospital.
                                </p>
                                <p style="margin-top:12px;">
                                    By visiting our hospital, booking an appointment, using our website, or availing any of our medical services, you acknowledge that you have read, understood, and agree to be bound by these Terms. If you do not agree, please do not use our services.
                                </p>
                                <div class="tc-highlight">
                                    <strong>Note:</strong> These terms apply to all patients, attendants, visitors, and any individual accessing the services of Dr. Agrawal's R.K. Hospital,27 Chandrashekhar, Azad Square, Central Ave, Ladpura, Itwari, Nagpur, Maharashtra 440002
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 02 — Eligibility -->
                        <div class="tc-section" id="tc-2">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 02</div>
                                    <h2 class="tc-section-title">Eligibility & Registration</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>To access our medical services and online appointment system, the following eligibility criteria apply:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Patients below 18 years must be accompanied by a parent or legal guardian who shall be responsible for consent and payment.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        All information provided during registration must be accurate, complete, and up-to-date, including name, age, contact number, and medical history.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        You are responsible for maintaining the confidentiality of your registered account and any OTP or login credentials shared with you.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        R.K. Hospital reserves the right to refuse services to anyone who provides false information or behaves in a manner that disrupts hospital operations.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Emergency cases are accepted without registration requirements. Emergency services are available 24/7 at our Nagpur facility.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECTION 03 — Appointments -->
                        <div class="tc-section" id="tc-3">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 03</div>
                                    <h2 class="tc-section-title">Appointment Booking Policy</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>Appointments at R.K. Hospital can be booked via call, walk-in, or our online appointment form. The following conditions apply:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                         Timings are <strong>Opens 24 Hours</strong> Monday to Sunday . Emergency services are available 24/7.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Appointments are subject to doctor availability and are confirmed only upon receipt of acknowledgment from our staff.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Patients are advised to arrive at least 15 minutes before their scheduled appointment with all relevant medical records and reports.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Walk-in patients are treated on a first-come, first-served basis subject to doctor availability.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        The hospital reserves the right to reschedule appointments due to medical emergencies, doctor unavailability, or force majeure events.
                                    </li>
                                </ul>
                                <div class="tc-info-grid">
                                   
                                    <div class="tc-info-card">
                                        <i class="fa-regular fa-clock"></i>
                                        <div>
                                            <h6>OPD Hours</h6>
                                            <p>11AM–4PM & 7PM–9PM<br>Mon – Saturday</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 04 — Cancellation & Refund -->
                        <div class="tc-section" id="tc-4">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 04</div>
                                    <h2 class="tc-section-title">Cancellation & Refund Policy</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>We understand that medical plans can change. Our cancellation and refund policy is designed to be fair and transparent:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Appointment cancellations must be communicated at least <strong>2 hours before</strong> the scheduled time by calling our helpline.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Consultation fees paid in advance are non-refundable but may be adjusted for rescheduled appointments within 30 days.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Surgical procedure deposits are refundable only in the case of surgery cancellation by the hospital due to medical reasons.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        No-show patients without prior cancellation notice will forfeit the consultation fee.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Insurance-covered procedures are subject to the terms and conditions of the respective insurance provider.
                                    </li>
                                </ul>
                                <div class="tc-highlight blue">
                                    <strong>Insurance & Cashless Treatment:</strong> R.K. Hospital accepts cashless treatment under most major insurance providers. Please contact our billing department for verification before your appointment.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 05 — Medical Disclaimer -->
                        <div class="tc-section" id="tc-5">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-stethoscope"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 05</div>
                                    <h2 class="tc-section-title">Medical Disclaimer</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>All medical services provided at Dr. Agrawal's R.K. Hospital are delivered by licensed and experienced healthcare professionals. However:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Medical outcomes can vary from patient to patient. No specific result, recovery timeline, or surgical outcome is guaranteed.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Information provided on our website is for general awareness only and does not constitute professional medical advice, diagnosis, or treatment.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Always consult a qualified doctor at our hospital before making any health-related decisions.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        R.K. Hospital is not responsible for complications arising from non-disclosure of pre-existing conditions, allergies, or ongoing medications by the patient.
                                    </li>
                                </ul>
                                <div class="tc-highlight">
                                    <strong>Important:</strong> In case of a medical emergency, please call our 24/7 emergency line at <strong>+91 97660 57372</strong> or visit the Emergency Department at our Nagpur location immediately.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 06 — Patient Rights -->
                        <div class="tc-section" id="tc-6">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-hand-holding-medical"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 06</div>
                                    <h2 class="tc-section-title">Patient Rights & Responsibilities</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p><strong>Your Rights as a Patient:</strong></p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Right to receive respectful and dignified care irrespective of age, gender, religion, or socioeconomic status.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Right to receive complete information about your diagnosis, treatment options, risks, and expected outcomes in a language you understand.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Right to informed consent before any surgical procedure or invasive treatment.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Right to privacy and confidentiality of your medical records and personal information.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Right to seek a second medical opinion from another doctor without prejudice.
                                    </li>
                                </ul>
                                <p style="margin-top:18px;"><strong>Your Responsibilities as a Patient:</strong></p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Provide accurate and complete medical history, including allergies, medications, and previous surgeries.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Follow prescribed treatment plans and doctor's instructions for best recovery outcomes.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Treat hospital staff, doctors, and other patients with respect and maintain decorum on hospital premises.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Clear all dues before discharge unless a specific payment arrangement has been made with the billing department.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECTION 07 — Privacy -->
                        <div class="tc-section" id="tc-7">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 07</div>
                                    <h2 class="tc-section-title">Privacy & Data Protection</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>Dr. Agrawal's R.K. Hospital takes patient data privacy seriously. We are committed to protecting your personal and medical information:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Patient medical records are strictly confidential and accessible only to authorized medical personnel directly involved in your treatment.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        Personal information (name, contact, address) collected during registration is used solely for appointment management and hospital communication.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        We do not sell, trade, or share your personal data with third parties without your explicit consent, except as required by law.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        CCTV surveillance is operational throughout the hospital premises for security purposes only.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-check"></i></span>
                                        You have the right to request a copy of your medical records. A nominal fee may apply for printed reports.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECTION 08 — Payment -->
                        <div class="tc-section" id="tc-8">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-indian-rupee-sign"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 08</div>
                                    <h2 class="tc-section-title">Payment & Billing Policy</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>R.K. Hospital accepts multiple payment modes for your convenience:</p>
                                <div class="tc-info-grid" style="margin-bottom:18px;">
                                    <div class="tc-info-card">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                        <div>
                                            <h6>Cash Payment</h6>
                                            <p>Accepted at the billing counter for all services</p>
                                        </div>
                                    </div>
                                    <div class="tc-info-card">
                                        <i class="fa-solid fa-credit-card"></i>
                                        <div>
                                            <h6>Card / UPI</h6>
                                            <p>Debit, Credit cards & UPI payments accepted</p>
                                        </div>
                                    </div>
                                    <div class="tc-info-card">
                                        <i class="fa-solid fa-shield-heart"></i>
                                        <div>
                                            <h6>Insurance / Cashless</h6>
                                            <p>Most major insurance providers accepted</p>
                                        </div>
                                    </div>
                                    <div class="tc-info-card">
                                        <i class="fa-solid fa-building-columns"></i>
                                        <div>
                                            <h6>Bank Transfer / NEFT</h6>
                                            <p>For pre-operative deposits and large payments</p>
                                        </div>
                                    </div>
                                </div>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        A detailed billing statement will be provided upon request or at the time of discharge.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        For surgical procedures, an advance deposit is required at the time of admission.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        All billing disputes must be raised within 7 days of receiving the invoice for resolution.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECTION 09 — Prohibited Conduct -->
                        <div class="tc-section" id="tc-9">
                            <div class="tc-section-header">
                                <div class="tc-section-icon red">
                                    <i class="fa-solid fa-ban"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 09</div>
                                    <h2 class="tc-section-title">Prohibited Conduct on Hospital Premises</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <p>To ensure a safe, hygienic, and peaceful environment for all patients, the following are strictly prohibited on R.K. Hospital premises:</p>
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        Smoking, alcohol consumption, or use of any form of tobacco or drugs on hospital premises.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        Verbal or physical harassment of doctors, nurses, hospital staff, or other patients.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        Photography or video recording inside OT, ICU, patient wards, or any restricted area without permission.
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        Bringing large gatherings or crowds to visit patients (restricted to 2 visitors per patient at a time).
                                    </li>
                                    <li>
                                        <span class="li-icon"><i class="fa-solid fa-xmark"></i></span>
                                        Tampering with hospital equipment, medical devices, or hospital property.
                                    </li>
                                </ul>
                                <div class="tc-highlight">
                                    Violation of these conduct rules may result in removal from the premises and legal action as per applicable laws of Maharashtra, India.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 10 — Governing Law -->
                        <div class="tc-section" id="tc-10">
                            <div class="tc-section-header">
                                <div class="tc-section-icon blue">
                                    <i class="fa-solid fa-scale-balanced"></i>
                                </div>
                                <div class="tc-section-header-text">
                                    <div class="tc-section-num">Section 10</div>
                                    <h2 class="tc-section-title">Governing Law & Amendments</h2>
                                </div>
                            </div>
                            <div class="tc-prose">
                                <ul class="tc-list">
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        These Terms and Conditions shall be governed by the laws of the State of Maharashtra, India, and any disputes shall be subject to the exclusive jurisdiction of courts in Nagpur.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        R.K. Hospital reserves the right to amend these Terms at any time without prior notice. Updated terms will be published on our official website.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        Continued use of our services after any amendments constitutes your acceptance of the updated Terms.
                                    </li>
                                    <li>
                                        <span class="li-icon blue"><i class="fa-solid fa-check"></i></span>
                                        If any provision of these Terms is found to be unenforceable, the remaining provisions shall continue in full effect.
                                    </li>
                                </ul>
                                <div class="tc-highlight blue" style="margin-top:20px;">
                                    For any queries or concerns regarding these Terms, please contact us at: <br>
                                    <strong>Dr. Agrawal's R.K. Hospital</strong> | 27, Central Avenue Road, Nagpur – 440002 <br>
                                    <strong>Phone:</strong> +91 97660 57372 &nbsp;|&nbsp;  <br>
                                </div>
                            </div>
                        </div>

                        <!-- AGREEMENT BANNER -->
                        <div class="tc-agreement-banner">
                            <i class="fa-solid fa-file-contract"></i>
                            <h3>By Using Our Services, You Agree to These Terms</h3>
                            <p>
                                These terms are designed to protect both our patients and our hospital. We are committed to delivering world-class orthopedic and gynecology care to every patient who walks through our doors.
                            </p>
                            <div class="tc-agreement-actions">
                                <a href="contact-us" class="btn-tc-primary">
                                    <i class="fa-solid fa-calendar-plus"></i> Book an Appointment
                                </a>
                                <a href="index.php" class="btn-tc-outline">
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
    <script>
    // ── HORIZONTAL SCROLL FIX (sticky-safe) ──
    (function() {
        function fixHScroll() {
            var docW = document.documentElement.scrollWidth;
            var winW = window.innerWidth;
            if (docW > winW) {
                document.documentElement.style.setProperty('--hscroll-fix', 'hidden');
            }
        }
        // Find and clip only the overflowing element
        document.addEventListener('DOMContentLoaded', function() {
            var banner = document.querySelector('.contact-hero-banner');
            if (banner) {
                banner.style.overflow = 'hidden';
                banner.style.maxWidth = '100vw';
            }
            var badges = document.querySelectorAll('.banner-stat-badge');
            badges.forEach(function(b) { b.style.display = 'none'; });
        });
    })();
</script>

</body>
</html>