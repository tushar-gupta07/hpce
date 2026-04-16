<?php $head_title = "About Us || Herbal Pest Control India || Natural & Eco-Friendly Pest Solutions" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?php echo $head_title;?></title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png" />
    <link rel="manifest" href="assets/images/favicons/site.webmanifest" />
    <meta name="description" content="Herbal Pest Control India – India's leading provider of 100% natural, eco-friendly, and herbal pest control solutions for homes, farms, and businesses." />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="assets/vendors/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/vendors/animate/animate.min.css" />
    <link rel="stylesheet" href="assets/vendors/animate/custom-animate.css" />
    <link rel="stylesheet" href="assets/vendors/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/vendors/jarallax/jarallax.css" />
    <link rel="stylesheet" href="assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css" />
    <link rel="stylesheet" href="assets/vendors/odometer/odometer.min.css" />
    <link rel="stylesheet" href="assets/vendors/swiper/swiper.min.css" />
    <link rel="stylesheet" href="assets/vendors/onpoint-icons/style.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel/owl.carousel.min.css" />
    <link rel="stylesheet" href="assets/vendors/owl-carousel/owl.theme.default.min.css" />
    <link rel="stylesheet" href="assets/vendors/bootstrap-select/css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="assets/vendors/nice-select/nice-select.css" />
    <link rel="stylesheet" href="assets/vendors/jquery-ui/jquery-ui.css" />

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
    
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->
    <!--[if lt IE 9]><script src="js/respond.js"></script><![endif]-->

    <!-- responsive -->
    <style>
        /* responsive - global overflow fix */
        body, .page-wrapper {
            overflow-x: hidden;
            max-width: 100%;
        }

        /* responsive - images */
        img {
            max-width: 100%;
            height: auto;
        }

        /* =============================================
           responsive - PAGE HEADER
        ============================================= */
        @media (max-width: 767px) {
            .page-header__inner h2 {
                font-size: 28px;
            }
            .page-header {
                padding: 60px 0;
            }
        }

        /* =============================================
           responsive - ABOUT ONE SECTION
        ============================================= */
        @media (max-width: 1199px) {
            .about-one__img-1,
            .about-one__img-2 {
                max-width: 100%;
            }
            .about-one__img-box {
                margin-top: 40px;
            }
        }

        @media (max-width: 991px) {
            .about-one__left {
                margin-bottom: 40px;
            }
            .about-one__list {
                flex-wrap: wrap;
            }
            .about-one__list li {
                width: 100%;
                margin-bottom: 20px;
            }
            .about-one__img-box {
                position: relative;
            }
            .about-one__img-2 {
                margin-top: 15px;
            }
            .about-one__trusted-box {
                position: relative;
                margin-top: 15px;
                display: inline-flex;
            }
        }

        @media (max-width: 767px) {
            .about-one__list li {
                flex-direction: column;
                text-align: center;
            }
            .about-one__list li h3 {
                font-size: 16px;
            }
            .section-title__title {
                font-size: 26px;
            }
            .about-one__trusted-box {
                left: 0 !important;
                bottom: 0 !important;
                width: 100%;
                justify-content: center;
            }
        }

        /* =============================================
           responsive - COUNTER ONE SECTION
        ============================================= */
        @media (max-width: 1199px) {
            .counter-one__left {
                margin-bottom: 40px;
            }
            .counter-one__img {
                width: 100%;
            }
            .counter-one__img img {
                width: 100%;
            }
        }

        @media (max-width: 991px) {
            .counter-one__count-list {
                flex-wrap: wrap;
            }
            .counter-one__count-list li {
                width: 100%;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 767px) {
            .counter-one__right-text {
                font-size: 15px;
            }
            .counter-one__count-list li {
                flex-direction: column;
                text-align: center;
            }
            .counter-one__icon-and-count {
                justify-content: center;
            }
        }

        /* =============================================
           responsive - TESTIMONIAL ONE SECTION
        ============================================= */
        @media (max-width: 1199px) {
            .testimonial-one__img-one {
                display: none;
            }
            .testimonial-one__right {
                padding-left: 0;
            }
        }

        @media (max-width: 991px) {
            .col-xl-5,
            .col-xl-7 {
                width: 100%;
            }
            .testimonial-one__right {
                margin-top: 0;
            }
        }

        @media (max-width: 767px) {
            .testimonial-one__text {
                font-size: 14px;
            }
            .testimonial-one__client-name {
                font-size: 18px;
            }
        }

        /* =============================================
           responsive - WHY ARE WE SECTION
        ============================================= */
        @media (max-width: 1199px) {
            .why-are-we__left {
                margin-bottom: 40px;
            }
            .why-are-we__img {
                width: 100%;
            }
            .why-are-we__img img {
                width: 100%;
            }
        }

        @media (max-width: 991px) {
            .why-are-we__list li {
                flex-wrap: wrap;
            }
            .why-are-we__img-2 {
                width: 100%;
            }
            .why-are-we__img-2 img {
                width: 100%;
            }
            .why-are-we__year {
                position: relative;
                left: 0 !important;
                bottom: 0 !important;
                width: 100%;
                margin-top: 15px;
            }
        }

        @media (max-width: 767px) {
            .why-are-we__list li .content h3 {
                font-size: 16px;
            }
            .why-are-we__list li .content p {
                font-size: 14px;
            }
            .why-are-we__year h3 {
                font-size: 20px;
            }
            .why-are-we__year p {
                font-size: 13px;
            }
        }

        /* =============================================
           responsive - CTA ONE SECTION
        ============================================= */
        @media (max-width: 991px) {
            .cta-one__inner {
                flex-direction: column;
                text-align: center;
            }
            .cta-one__content-box {
                padding: 30px 20px;
            }
        }

        @media (max-width: 767px) {
            .cta-one__title {
                font-size: 22px;
            }
            .cta-one__contact-box {
                justify-content: center;
            }
        }

        /* =============================================
           responsive - GENERAL COLUMN STACKING
        ============================================= */
        @media (max-width: 1199px) {
            .col-xl-6,
            .col-xl-4,
            .col-xl-8,
            .col-xl-5,
            .col-xl-7 {
                width: 100%;
            }
        }

        /* =============================================
           responsive - SHAPE ELEMENTS
        ============================================= */
        @media (max-width: 767px) {
            .about-one__shape-1 {
                display: none;
            }
        }

        /* =============================================
           responsive - BREADCRUMB
        ============================================= */
        @media (max-width: 575px) {
            .thm-breadcrumb {
                flex-wrap: wrap;
                justify-content: center;
            }
            .page-header__inner {
                text-align: center;
            }
        }

        /* =============================================
           responsive - SECTION TITLE
        ============================================= */
        @media (max-width: 575px) {
            .section-title__title {
                font-size: 22px;
            }
            .section-title__title br {
                display: none;
            }
        }
    </style>
    <!-- end responsive -->
</head>

<body>

<?php require_once('include/header.php'); ?>

    <!-- ============================================
         PAGE WRAPPER START
    ============================================ -->
    <div class="page-wrapper">

        <!-- ============================================
             HEADER
        ============================================ -->

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/page-header-bg.jpg);">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>About Us</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="index.php">Home</a></li>
                            <li><span class="icon-angle-left"></span></li>
                            <li>About Us</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--About One Start-->
        <section class="about-one about-two">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="about-one__left">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">About Us</span>
                                </div>
                                <h2 class="section-title__title">Nature's Power for a <br> Pest-Free India</h2>
                            </div>
                            <p class="about-one__text">Herbal Pest Control India (HPCI) is a pioneer in eco-friendly pest management, harnessing the power of natural herbs, plant extracts, and botanical formulations to eliminate pests — without toxic chemicals, without harming your family, pets, or the environment.</p>
                            <div class="about-one__list-box">
                                <div class="about-one__shape-1">
                                    <img src="assets/images/shapes/about-one-shape-1.png" alt="">
                                </div>
                                <ul class="about-one__list list-unstyled">
                                    <li>
                                        <div class="about-one__icon">
                                            <span class="icon-conveyor-1"></span>
                                        </div>
                                        <h3>100% Natural & Herbal <br> Pest Formulations</h3>
                                    </li>
                                    <li>
                                        <div class="about-one__icon">
                                            <span class="icon-clock"></span>
                                        </div>
                                        <h3>Safe for Children, Pets <br> & the Environment</h3>
                                    </li>
                                    <li>
                                        <div class="about-one__icon">
                                            <span class="icon-fragile"></span>
                                        </div>
                                        <h3>Certified Organic Treatments <br> Across India</h3>
                                    </li>
                                </ul>
                            </div>
                            <div class="about-one__btn-box">
                                <a href="services.php" class="thm-btn about-one__btn">Read More<span></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="about-one__right wow fadeInRight" data-wow-delay="300ms">
                            <div class="about-one__img-box">
                                <div class="about-one__img-1">
                                    <img src="assets/images/resources/about-one-img-1.jpg" alt="Herbal Pest Control Treatment">
                                </div>
                                <div class="about-one__img-2">
                                    <img src="assets/images/resources/about-one-img-2.jpg" alt="Natural Herbal Ingredients">
                                </div>
                                <div class="about-one__trusted-box">
                                    <div class="about-one__trust-icon">
                                        <span class="icon-ionic-ios-people"></span>
                                    </div>
                                    <div class="about-one__trust-content">
                                        <div class="about-one__trust-count count-box">
                                            <h3 class="count-text" data-stop="10" data-speed="1500">00</h3>
                                            <span>k</span>
                                            <span class="about-one__trust-plus">+</span>
                                        </div>
                                        <p class="about-one__trust-text">Trusted Customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--About One End-->

        <!--Counter One Start-->
        <section class="counter-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-5">
                        <div class="counter-one__left wow slideInLeft" data-wow-delay="100ms"
                            data-wow-duration="2500ms">
                            <div class="counter-one__img">
                                <img src="assets/images/resources/counter-one-img-1.jpg" alt="HPCI Herbal Pest Control Team at Work">
                                <div class="counter-one__video-link">
                                    <a href="https://www.youtube.com/watch?v=Get7rqXYrbQ" class="video-popup">
                                        <div class="counter-one__video-icon">
                                            <span class="icon-play"></span>
                                            <i class="ripple"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="counter-one__right wow slideInRight" data-wow-delay="100ms"
                            data-wow-duration="2500ms">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">Our Strengths</span>
                                </div>
                                <h2 class="section-title__title">Why India Chooses Herbal <br> Pest Control</h2>
                            </div>
                            <p class="counter-one__right-text">At Herbal Pest Control India, we combine centuries-old Ayurvedic wisdom with modern pest management techniques. Our botanical-based treatments are scientifically tested, government approved, and proven effective against termites, cockroaches, mosquitoes, rodents, and more — all without a single drop of harmful chemical.</p>
                            <ul class="counter-one__count-list list-unstyled">
                                <li>
                                    <div class="counter-one__icon-and-count">
                                        <div class="counter-one__icon">
                                            <span class="icon-schedule"></span>
                                        </div>
                                        <div class="counter-one__count count-box">
                                            <h3 class="count-text" data-stop="500" data-speed="1500">00</h3>
                                            <span>+</span>
                                        </div>
                                    </div>
                                    <p class="counter-one__count-text">Happy Customers</p>
                                </li>
                                <li>
                                    <div class="counter-one__icon-and-count">
                                        <div class="counter-one__icon">
                                            <span class="icon-schedule"></span>
                                        </div>
                                        <div class="counter-one__count count-box">
                                            <h3 class="count-text" data-stop="1" data-speed="1500">00</h3>
                                            <span>k</span>
                                            <span class="counter-one__count-plus">+</span>
                                        </div>
                                    </div>
                                    <p class="counter-one__count-text">Expert Team Members</p>
                                </li>
                                <li>
                                    <div class="counter-one__icon-and-count">
                                        <div class="counter-one__icon">
                                            <span class="icon-schedule"></span>
                                        </div>
                                        <div class="counter-one__count count-box">
                                            <h3 class="count-text" data-stop="5" data-speed="1500">00</h3>
                                            <span>k</span>
                                            <span class="counter-one__count-plus">+</span>
                                        </div>
                                    </div>
                                    <p class="counter-one__count-text">Positive Client Reviews</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Counter One End-->

        <!--Testimonial One Start-->
        <section class="testimonial-one">
            <div class="container">
                <div class="testimonial-one__inner">
                    <div class="testimonial-one__img-one">
                        <img src="assets/images/testimonial/testimonial-one-img-1.jpg" alt="HPCI Testimonials">
                    </div>
                    <div class="section-title text-center">
                        <div class="section-title__tagline-box">
                            <span class="section-title__tagline">Client Testimonials</span>
                        </div>
                        <h2 class="section-title__title">What Our Customers Say About <br> Our Herbal Solutions</h2>
                    </div>
                    <div class="row">
                        <div class="col-xl-5"></div>
                        <div class="col-xl-7 col-lg-9">
                            <div class="testimonial-one__right">
                                <div class="thm-swiper__slider swiper-container" data-swiper-options='{
                                    "slidesPerView": 1, 
                                    "spaceBetween": 0,
                                    "speed": 2000,
                                    "loop": true,
                                    "pagination": {
                                        "el": ".swiper-dot-style1",
                                        "type": "bullets",
                                        "clickable": true
                                    },
                                    "navigation": {
                                        "nextEl": ".swiper-button-prev1",
                                        "prevEl": ".swiper-button-next1"
                                    },
                                    "autoplay": { "delay": 9000 },
                                    "breakpoints": {
                                            "0": {
                                                "spaceBetween": 0,
                                                "slidesPerView": 1
                                            },
                                            "375": {
                                                "spaceBetween": 0,
                                                "slidesPerView": 1
                                            },
                                            "575": {
                                                "spaceBetween": 0,
                                                "slidesPerView": 1
                                            },
                                            "768": {
                                                "spaceBetween": 30,
                                                "slidesPerView": 1
                                            },
                                            "992": {
                                                "spaceBetween": 30,
                                                "slidesPerView": 1
                                            },
                                            "1200": {
                                                "spaceBetween": 30,
                                                "slidesPerView":1
                                            },
                                            "1320": {
                                                "spaceBetween": 30,
                                                "slidesPerView":1
                                            }
                                        }
                                }'>
                                    <div class="swiper-wrapper">
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote">
                                                    <span class="icon-quote"></span>
                                                </div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-1.jpg" alt="Rajan Sharma">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Rajan Sharma</h3>
                                                <p class="testimonial-one__client-sub-title">Homeowner, Delhi</p>
                                                <p class="testimonial-one__text">We had a severe termite problem in our home for years. After trying multiple chemical treatments with no success, HPCI's herbal termite control completely solved our issue. The treatment was odourless, safe for our kids, and the results have lasted over two years now. Highly recommended!</p>
                                            </div>
                                        </div>
                                        <!--Testimonial One Single End-->
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote">
                                                    <span class="icon-quote"></span>
                                                </div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-2.jpg" alt="Priya Nair">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Priya Nair</h3>
                                                <p class="testimonial-one__client-sub-title">Organic Farmer, Kerala</p>
                                                <p class="testimonial-one__text">As an organic farmer, I cannot afford to use chemical pesticides. HPCI's neem-based and botanical pest sprays have protected my crops effectively while keeping my farm certified organic. Their team is knowledgeable and the products are genuinely natural. My yield has improved significantly.</p>
                                            </div>
                                        </div>
                                        <!--Testimonial One Single End-->
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote">
                                                    <span class="icon-quote"></span>
                                                </div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-3.jpg" alt="Amit Desai">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Amit Desai</h3>
                                                <p class="testimonial-one__client-sub-title">Restaurant Owner, Mumbai</p>
                                                <p class="testimonial-one__text">Running a food business means we cannot use toxic pest control. HPCI provided a complete herbal cockroach and rodent management plan for our restaurant. Their team was professional, discreet, and the treatment complied with FSSAI standards. Our kitchen has been pest-free for over 18 months!</p>
                                            </div>
                                        </div>
                                        <!--Testimonial One Single End-->
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote">
                                                    <span class="icon-quote"></span>
                                                </div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-4.jpg" alt="Sunita Reddy">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Sunita Reddy</h3>
                                                <p class="testimonial-one__client-sub-title">School Principal, Hyderabad</p>
                                                <p class="testimonial-one__text">Children's safety is our top priority at school. HPCI conducted a complete mosquito and ant treatment across our campus using only herbal, non-toxic solutions. Parents were thrilled, and the results were outstanding. It is reassuring to know pest control can be both effective and completely safe.</p>
                                            </div>
                                        </div>
                                        <!--Testimonial One Single End-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-one__dot-style">
                        <div class="swiper-dot-style1"></div>
                    </div>
                </div>
            </div>
        </section>
        <!--Testimonial One End-->

        <!--Why Are We Start-->
        <section class="why-are-we">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="why-are-we__left">
                            <div class="why-are-we__img">
                                <img src="assets/images/resources/why-are-we-img-1.jpg" alt="HPCI Herbal Pest Experts">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="why-are-we__right">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">Why We Are The Best</span>
                                </div>
                                <h2 class="section-title__title">India's Most Trusted Herbal <br> Pest Control Company</h2>
                            </div>
                            <ul class="why-are-we__list list-unstyled">
                                <li>
                                    <div class="why-are-we__icon">
                                        <span class="icon-arrow-down-left"></span>
                                    </div>
                                    <div class="icon">
                                        <span class="icon-location why-are-we__location"></span>
                                    </div>
                                    <div class="content">
                                        <h3>Scientifically Proven Herbal Formulas</h3>
                                        <p>Our treatments are lab-tested, WHO-compliant, and derived from powerful plant extracts like neem, eucalyptus, pyrethrum, and citronella.</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="why-are-we__icon-2">
                                        <span class="icon-arrow-down-right"></span>
                                    </div>
                                    <div class="icon">
                                        <span class="icon-shopping-cart why-are-we__cart"></span>
                                    </div>
                                    <div class="content">
                                        <h3>Long-Lasting, Guaranteed Results</h3>
                                        <p>Our herbal treatments offer residual protection for months, with free follow-up visits to ensure your premises remain completely pest-free.</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-call why-are-we__call"></span>
                                    </div>
                                    <div class="content">
                                        <h3>24/7 Expert Support & Consultation</h3>
                                        <p>Our certified pest control specialists are available around the clock to advise, assist, and schedule treatments at your convenience.</p>
                                    </div>
                                </li>
                            </ul>
                            <div class="why-are-we__img-2">
                                <img src="assets/images/resources/why-are-we-img-2.jpg" alt="HPCI Herbal Treatment in Action">
                                <div class="why-are-we__year wow fadeInLeft" data-wow-delay="300ms">
                                    <h3>Since 2005</h3>
                                    <p>For over two decades, Herbal Pest Control India has been safeguarding homes, farms, offices, and communities with the healing power of nature — delivering pest-free environments the safe and sustainable way.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Why Are We End-->

        <!--CTA One Start-->
        <section class="cta-one cta-two">
            <div class="container">
                <div class="cta-one__inner">
                    <div class="cta-one__bg-img"
                        style="background-image: url(assets/images/resources/cta-one-bg-img.jpg);"></div>
                    <div class="cta-one__content-box">
                        <div class="cta-one__icon">
                            <span class="icon-call"></span>
                            <div class="cta-one__shape-1">
                                <img src="assets/images/shapes/cta-one-shape-1.png" alt="">
                            </div>
                        </div>
                        <h3 class="cta-one__title">Need Herbal Pest Control? <br> Call Us Today!</h3>
                        <div class="cta-one__contact-box">
                            <div class="icon">
                                <span class="icon-phone"></span>
                            </div>
                            <div class="content">
                                <p>Free Consultation Available</p>
                                <h3><a href="tel:3075550133">(307) 555-0133</a></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--CTA One End-->

        <!-- ============================================
             FOOTER
        ============================================ -->
       <?php require_once('include/footer.php'); ?>
        <!--Site Footer End-->

    </div><!-- /.page-wrapper -->
    
    <!-- ===========================================
         VENDOR JS
    ============================================ -->
    <script src="assets/vendors/jquery/jquery-3.6.0.min.js"></script>
    <script src="assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/jarallax/jarallax.min.js"></script>
    <script src="assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js"></script>
    <script src="assets/vendors/jquery-appear/jquery.appear.min.js"></script>
    <script src="assets/vendors/jquery-circle-progress/jquery.circle-progress.min.js"></script>
    <script src="assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="assets/vendors/jquery-validate/jquery.validate.min.js"></script>
    <script src="assets/vendors/odometer/odometer.min.js"></script>
    <script src="assets/vendors/swiper/swiper.min.js"></script>
    <script src="assets/vendors/wnumb/wNumb.min.js"></script>
    <script src="assets/vendors/wow/wow.js"></script>
    <script src="assets/vendors/isotope/isotope.js"></script>
    <script src="assets/vendors/owl-carousel/owl.carousel.min.css" />
    <script src="assets/vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="assets/vendors/jquery-ui/jquery-ui.js"></script>
    <script src="assets/vendors/jquery.circle-type/jquery.circleType.js"></script>
    <script src="assets/vendors/jquery.circle-type/jquery.lettering.min.js"></script>
    <script src="assets/vendors/nice-select/jquery.nice-select.min.js"></script>
    <script src="assets/vendors/marquee/marquee.min.js"></script>
    <script src="assets/vendors/sidebar-content/jquery-sidebar-content.js"></script>
    <script src="assets/js/script.js"></script>

</body>
</html>