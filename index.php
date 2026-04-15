<?php $head_title = "Home One || onpoint || onpoint HTML 5 Template" ?>
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
    <meta name="description" content="onpoint HTML 5 Template " />

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
</head>

<body>
    <?php require_once('include/header.php'); ?>


        <!-- ============================================
             SECTION 1: MAIN SLIDER / BANNER
        ============================================ -->
        <section class="main-slider">
            <div class="swiper-container thm-swiper__slider" data-swiper-options='{"slidesPerView": 1, "loop": true,
                "effect": "fade",
                "pagination": {
                    "el": "#main-slider-pagination",
                    "type": "bullets",
                    "clickable": true
                },
                "navigation": {
                    "nextEl": "#main-slider__swiper-button-next",
                    "prevEl": "#main-slider__swiper-button-prev"
                },
                "autoplay": {
                    "delay": 8000
                }
            }'>
                <div class="swiper-wrapper">

                    <!-- Slide 1 -->
                    <div class="swiper-slide">
                        <div class="main-slider__bg" style="background-image: url(assets/images/backgrounds/slider-1-1.jpg);"></div>
                        <div class="main-slider__shape-1"></div>
                        <div class="main-slider__shape-2"></div>
                        <div class="main-slider__shape-3"></div>
                        <div class="main-slider__shape-4"></div>
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="main-slider__content">
                                        <div class="main-slider__sub-title-box">
                                            <div class="main-slider__sub-title-icon">
                                                <img src="assets/images/icon/main-slider-sub-title-icon.png" alt="">
                                            </div>
                                            <p class="main-slider__sub-title">Best shipping</p>
                                        </div>
                                        <h2 class="main-slider__title">Reliable <span>Responsive</span> <br> Driven Logistics</h2>
                                        <p class="main-slider__text">We have been operating for over a decade, providing top-notch services to <br> our clients and building a strong track record in the industry.</p>
                                        <div class="main-slider__btn-and-call-box">
                                            <div class="main-slider__btn-box">
                                                <a href="about.php" class="thm-btn main-slider__btn">Read more<span></span></a>
                                            </div>
                                            <div class="main-slider__call">
                                                <div class="main-slider__call-icon">
                                                    <span class="icon-phone"></span>
                                                </div>
                                                <div class="main-slider__call-number">
                                                    <p>Need help?</p>
                                                    <h5><a href="tel:307555-0133">(307) 555-0133</a></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide">
                        <div class="main-slider__bg" style="background-image: url(assets/images/backgrounds/slider-1-2.jpg);"></div>
                        <div class="main-slider__shape-1"></div>
                        <div class="main-slider__shape-2"></div>
                        <div class="main-slider__shape-3"></div>
                        <div class="main-slider__shape-4"></div>
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="main-slider__content">
                                        <div class="main-slider__sub-title-box">
                                            <div class="main-slider__sub-title-icon">
                                                <img src="assets/images/icon/main-slider-sub-title-icon.png" alt="">
                                            </div>
                                            <p class="main-slider__sub-title">Best shipping</p>
                                        </div>
                                        <h2 class="main-slider__title">Reliable <span>Responsive</span> <br> Driven Logistics</h2>
                                        <p class="main-slider__text">We have been operating for over a decade, providing top-notch services to <br> our clients and building a strong track record in the industry.</p>
                                        <div class="main-slider__btn-and-call-box">
                                            <div class="main-slider__btn-box">
                                                <a href="about.php" class="thm-btn main-slider__btn">Read more<span></span></a>
                                            </div>
                                            <div class="main-slider__call">
                                                <div class="main-slider__call-icon">
                                                    <span class="icon-phone"></span>
                                                </div>
                                                <div class="main-slider__call-number">
                                                    <p>Need help?</p>
                                                    <h5><a href="tel:307555-0133">(307) 555-0133</a></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="swiper-slide">
                        <div class="main-slider__bg" style="background-image: url(assets/images/backgrounds/slider-1-3.jpg);"></div>
                        <div class="main-slider__shape-1"></div>
                        <div class="main-slider__shape-2"></div>
                        <div class="main-slider__shape-3"></div>
                        <div class="main-slider__shape-4"></div>
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="main-slider__content">
                                        <div class="main-slider__sub-title-box">
                                            <div class="main-slider__sub-title-icon">
                                                <img src="assets/images/icon/main-slider-sub-title-icon.png" alt="">
                                            </div>
                                            <p class="main-slider__sub-title">Best shipping</p>
                                        </div>
                                        <h2 class="main-slider__title">Reliable <span>Responsive</span> <br> Driven Logistics</h2>
                                        <p class="main-slider__text">We have been operating for over a decade, providing top-notch services to <br> our clients and building a strong track record in the industry.</p>
                                        <div class="main-slider__btn-and-call-box">
                                            <div class="main-slider__btn-box">
                                                <a href="about.php" class="thm-btn main-slider__btn">Read more<span></span></a>
                                            </div>
                                            <div class="main-slider__call">
                                                <div class="main-slider__call-icon">
                                                    <span class="icon-phone"></span>
                                                </div>
                                                <div class="main-slider__call-number">
                                                    <p>Need help?</p>
                                                    <h5><a href="tel:307555-0133">(307) 555-0133</a></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="swiper-pagination" id="main-slider-pagination"></div>
            </div>
        </section>
        <!-- /.main-slider -->

        <!-- ============================================
             SECTION 2: SERVICES ONE
        ============================================ -->
        <section class="services-one">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-title__tagline-box">
                        <span class="section-title__tagline">LATEST SERVICE</span>
                    </div>
                    <h2 class="section-title__title">Your supply chain partner<br> for success</h2>
                </div>
                <div class="row">
                    <!--Services One Single Start-->
                    <div class="col-xl-4 col-lg-4 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="services-one__single">
                            <div class="services-one__icon">
                                <span class="icon-postbox"></span>
                            </div>
                            <h3 class="services-one__title"><a href="express-freight-solutions.php">Fast and reliable logistics the solutions</a></h3>
                            <div class="services-one__btn-box">
                                <a href="express-freight-solutions.php" class="thm-btn services-one__btn">Read more<span></span></a>
                            </div>
                        </div>
                    </div>
                    <!--Services One Single End-->
                    <!--Services One Single Start-->
                    <div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="200ms">
                        <div class="services-one__single">
                            <div class="services-one__icon">
                                <span class="icon-customer-service"></span>
                            </div>
                            <h3 class="services-one__title"><a href="quick-move-logistics.php">Bridges Construction is an essen industry</a></h3>
                            <div class="services-one__btn-box">
                                <a href="quick-move-logistics.php" class="thm-btn services-one__btn">Read more<span></span></a>
                            </div>
                        </div>
                    </div>
                    <!--Services One Single End-->
                    <!--Services One Single Start-->
                    <div class="col-xl-4 col-lg-4 wow fadeInRight" data-wow-delay="300ms">
                        <div class="services-one__single">
                            <div class="services-one__icon">
                                <span class="icon-container"></span>
                            </div>
                            <h3 class="services-one__title"><a href="speedy-dispatch.php">That involves building adesig the a structures</a></h3>
                            <div class="services-one__btn-box">
                                <a href="speedy-dispatch.php" class="thm-btn services-one__btn">Read more<span></span></a>
                            </div>
                        </div>
                    </div>
                    <!--Services One Single End-->
                </div>
            </div>
        </section>
        <!-- /.services-one -->

        <!-- ============================================
             SECTION 3: WHY CHOOSE ONE
        ============================================ -->
        <section class="why-choose-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="why-choose-one__left">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">Why Chose us</span>
                                </div>
                                <h2 class="section-title__title">Delivering excellence every a time Express Logistics</h2>
                            </div>
                            <p class="why-choose-one__text">Construction is an essential industry that involves building adesigning the an structures such as buildings roads, bridges Construction is an essent industry that involves building adesigning the a structures such </p>
                            <div class="why-choose-one__btn-box">
                                <a href="about.php" class="thm-btn why-choose-one__btn">Read more<span></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="why-choose-one__right">
                            <div class="why-choose-one__img wow slideInRight" data-wow-delay="100ms" data-wow-duration="2500ms">
                                <img src="assets/images/resources/why-choose-one-img-1.jpg" alt="">
                                <div class="why-choose-one__delivery-box">
                                    <div class="icon">
                                        <span class="icon-airplane"></span>
                                    </div>
                                    <p>2 dAYS<br> DELIVARY</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.why-choose-one -->

        <!-- ============================================
             SECTION 4: PROJECT ONE
        ============================================ -->
        <section class="project-one">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-title__tagline-box">
                        <span class="section-title__tagline">Our projects</span>
                    </div>
                    <h2 class="section-title__title">Let's discover all our <br>recent project</h2>
                </div>
                <div class="row masonary-layout">
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-1.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-2.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-3.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-4.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-5.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-6.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-7.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-8.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                    <!--Project One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="project-one__single">
                            <div class="project-one__img-box">
                                <div class="project-one__img">
                                    <img src="assets/images/project/project-1-9.jpg" alt="">
                                </div>
                                <div class="project-one__content">
                                    <p class="project-one__sub-title">Express Logistics</p>
                                    <h3 class="project-one__title"><a href="project-details.php">Delivering success through logistics</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Project One Single End-->
                </div>
            </div>
        </section>
        <!-- /.project-one -->

        <!-- ============================================
             SECTION 5: BRAND ONE
        ============================================ -->
        <section class="brand-one">
            <div class="container">
                <p class="brand-one__text count-box">Join the <span class="count-text" data-stop="150" data-speed="1500">00</span><span>+</span> companies trusting maxline company</p>
                <div class="thm-swiper__slider swiper-container" data-swiper-options='{"spaceBetween": 100,
                "slidesPerView": 5,
                "loop": true,
                "navigation": {
                    "nextEl": "#brand-one__swiper-button-next",
                    "prevEl": "#brand-one__swiper-button-prev"
                },
                "autoplay": { "delay": 5000 },
                "breakpoints": {
                    "0": {"spaceBetween": 30,"slidesPerView": 1},
                    "375": {"spaceBetween": 30,"slidesPerView": 1},
                    "575": {"spaceBetween": 30,"slidesPerView": 2},
                    "767": {"spaceBetween": 50,"slidesPerView": 3},
                    "991": {"spaceBetween": 50,"slidesPerView": 4},
                    "1199": {"spaceBetween": 100,"slidesPerView": 5}
                }}'>
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-1.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-2.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-3.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-4.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-5.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-1.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-2.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-3.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-4.png" alt=""></div>
                        <div class="swiper-slide"><img src="assets/images/brand/brand-1-5.png" alt=""></div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.brand-one -->

        <!-- ============================================
             SECTION 6: ABOUT ONE
        ============================================ -->
        <section class="about-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="about-one__left">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">About Us</span>
                                </div>
                                <h2 class="section-title__title">Seamless logistics for your <br> business needs</h2>
                            </div>
                            <p class="about-one__text">Construction is an essential industry that involves building adesigning the a structures such as buildings roads, bridges</p>
                            <div class="about-one__list-box">
                                <div class="about-one__shape-1">
                                    <img src="assets/images/shapes/about-one-shape-1.png" alt="">
                                </div>
                                <ul class="about-one__list list-unstyled">
                                    <li>
                                        <div class="about-one__icon">
                                            <span class="icon-conveyor-1"></span>
                                        </div>
                                        <h3>Delivering successthe <br> through logistics</h3>
                                    </li>
                                    <li>
                                        <div class="about-one__icon">
                                            <span class="icon-clock"></span>
                                        </div>
                                        <h3>Logistics expertise for your<br> competitive </h3>
                                    </li>
                                    <li>
                                        <div class="about-one__icon">
                                            <span class="icon-fragile"></span>
                                        </div>
                                        <h3>Streamliningm supply chain<br> processes</h3>
                                    </li>
                                </ul>
                            </div>
                            <div class="about-one__btn-box">
                                <a href="about.php" class="thm-btn about-one__btn">Read more<span></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="about-one__right wow fadeInRight" data-wow-delay="300ms">
                            <div class="about-one__img-box">
                                <div class="about-one__img-1">
                                    <img src="assets/images/resources/about-one-img-1.jpg" alt="">
                                </div>
                                <div class="about-one__img-2">
                                    <img src="assets/images/resources/about-one-img-2.jpg" alt="">
                                </div>
                                <div class="about-one__trusted-box">
                                    <div class="about-one__trust-icon">
                                        <span class="icon-ionic-ios-people"></span>
                                    </div>
                                    <div class="about-one__trust-content">
                                        <div class="about-one__trust-count count-box">
                                            <h3 class="count-text" data-stop="6" data-speed="1500">00</h3>
                                            <span>k</span>
                                            <span class="about-one__trust-plus">+</span>
                                        </div>
                                        <p class="about-one__trust-text">Trusted Customer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.about-one -->

        <!-- ============================================
             SECTION 7: COUNTER ONE
        ============================================ -->
        <section class="counter-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-5">
                        <div class="counter-one__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                            <div class="counter-one__img">
                                <img src="assets/images/resources/counter-one-img-1.jpg" alt="">
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
                        <div class="counter-one__right wow slideInRight" data-wow-delay="100ms" data-wow-duration="2500ms">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">Our Features</span>
                                </div>
                                <h2 class="section-title__title">Simplifying your logistics of <br>the challenges</h2>
                            </div>
                            <p class="counter-one__right-text">Logistic service involves the planning, implementation, and control of the efficient and effective movement and storage of goods and materials.</p>
                            <ul class="counter-one__count-list list-unstyled">
                                <li>
                                    <div class="counter-one__icon-and-count">
                                        <div class="counter-one__icon"><span class="icon-schedule"></span></div>
                                        <div class="counter-one__count count-box">
                                            <h3 class="count-text" data-stop="100" data-speed="1500">00</h3>
                                            <span>+</span>
                                        </div>
                                    </div>
                                    <p class="counter-one__count-text">Our Happy Customer</p>
                                </li>
                                <li>
                                    <div class="counter-one__icon-and-count">
                                        <div class="counter-one__icon"><span class="icon-schedule"></span></div>
                                        <div class="counter-one__count count-box">
                                            <h3 class="count-text" data-stop="2" data-speed="1500">00</h3>
                                            <span>k</span>
                                            <span class="counter-one__count-plus">+</span>
                                        </div>
                                    </div>
                                    <p class="counter-one__count-text">Our Team Member</p>
                                </li>
                                <li>
                                    <div class="counter-one__icon-and-count">
                                        <div class="counter-one__icon"><span class="icon-schedule"></span></div>
                                        <div class="counter-one__count count-box">
                                            <h3 class="count-text" data-stop="3" data-speed="1500">00</h3>
                                            <span>k</span>
                                            <span class="counter-one__count-plus">+</span>
                                        </div>
                                    </div>
                                    <p class="counter-one__count-text">Our Clients Reviews</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.counter-one -->

        <!-- ============================================
             SECTION 8: TEAM ONE
        ============================================ -->
        <section class="team-one">
            <div class="container">
                <div class="team-one__top">
                    <div class="section-title text-left">
                        <div class="section-title__tagline-box">
                            <span class="section-title__tagline">Our Team</span>
                        </div>
                        <h2 class="section-title__title">Simplifying your logistics of <br>the challenges</h2>
                    </div>
                    <div class="team-one__nav">
                        <div class="swiper-button-next1"><i class="icon-arrow-left"></i></div>
                        <div class="swiper-button-prev1"><i class="icon-arrow-right"></i></div>
                    </div>
                </div>
                <div class="team-one__bottom">
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
                            "0": {"spaceBetween": 0,"slidesPerView": 1},
                            "375": {"spaceBetween": 0,"slidesPerView": 1},
                            "575": {"spaceBetween": 0,"slidesPerView": 1},
                            "768": {"spaceBetween": 30,"slidesPerView": 1},
                            "992": {"spaceBetween": 30,"slidesPerView": 2},
                            "1200": {"spaceBetween": 30,"slidesPerView": 3},
                            "1320": {"spaceBetween": 30,"slidesPerView": 3}
                        }
                    }'>
                        <div class="swiper-wrapper">
                            <!--Team One Single Start-->
                            <div class="swiper-slide">
                                <div class="team-one__single">
                                    <div class="team-one__img">
                                        <img src="assets/images/team/team-1-1.jpg" alt="">
                                    </div>
                                    <div class="team-one__content">
                                        <h3 class="team-one__title"><a href="team-details.php">Brooklyn Simmons</a></h3>
                                        <p class="team-one__sub-title">Quick Cargo</p>
                                        <div class="team-one__social">
                                            <a href="#"><span class="icon-instagram"></span></a>
                                            <a href="#"><span class="icon-linkedin-in"></span></a>
                                            <a href="#"><span class="icon-Vector"></span></a>
                                            <a href="#"><span class="icon-facebook-f"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Team One Single End-->
                            <!--Team One Single Start-->
                            <div class="swiper-slide">
                                <div class="team-one__single">
                                    <div class="team-one__img">
                                        <img src="assets/images/team/team-1-2.jpg" alt="">
                                    </div>
                                    <div class="team-one__content">
                                        <h3 class="team-one__title"><a href="team-details.php">Sakib Hasan</a></h3>
                                        <p class="team-one__sub-title">Speedy Trans</p>
                                        <div class="team-one__social">
                                            <a href="#"><span class="icon-instagram"></span></a>
                                            <a href="#"><span class="icon-linkedin-in"></span></a>
                                            <a href="#"><span class="icon-Vector"></span></a>
                                            <a href="#"><span class="icon-facebook-f"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Team One Single End-->
                            <!--Team One Single Start-->
                            <div class="swiper-slide">
                                <div class="team-one__single">
                                    <div class="team-one__img">
                                        <img src="assets/images/team/team-1-3.jpg" alt="">
                                    </div>
                                    <div class="team-one__content">
                                        <h3 class="team-one__title"><a href="team-details.php">Fahda Hossain</a></h3>
                                        <p class="team-one__sub-title">Efficient Transport</p>
                                        <div class="team-one__social">
                                            <a href="#"><span class="icon-instagram"></span></a>
                                            <a href="#"><span class="icon-linkedin-in"></span></a>
                                            <a href="#"><span class="icon-Vector"></span></a>
                                            <a href="#"><span class="icon-facebook-f"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Team One Single End-->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.team-one -->

        <!-- ============================================
             SECTION 9: WHY ARE WE
        ============================================ -->
        <section class="why-are-we">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="why-are-we__left">
                            <div class="why-are-we__img">
                                <img src="assets/images/resources/why-are-we-img-1.jpg" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="why-are-we__right">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">Why are we best</span>
                                </div>
                                <h2 class="section-title__title">Efficiency at its best with our<br> logistics services</h2>
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
                                        <h3>Real Time tracking</h3>
                                        <p>Logistic service involves the ntation and control </p>
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
                                        <h3>On time delivary</h3>
                                        <p>Logistic service involves the ntation and control </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-call why-are-we__call"></span>
                                    </div>
                                    <div class="content">
                                        <h3>24/7 online support</h3>
                                        <p>Logistic service involves the ntation and control </p>
                                    </div>
                                </li>
                            </ul>
                            <div class="why-are-we__img-2">
                                <img src="assets/images/resources/why-are-we-img-2.jpg" alt="">
                                <div class="why-are-we__year wow fadeInLeft" data-wow-delay="300ms">
                                    <h3>Since 1920</h3>
                                    <p>Logistic service involves the planning, implementation, and control of the efficient and effective movement and storage</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.why-are-we -->

        <!-- ============================================
             SECTION 10: FAQ ONE
        ============================================ -->
        <section class="faq-one">
            <div class="faq-one__bg-color"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="faq-one__left">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">FAQ</span>
                                </div>
                                <h2 class="section-title__title">Do You Have Any <br> Question Please?</h2>
                            </div>
                            <div class="accrodion-grp faq-one-accrodion" data-grp-name="faq-one-accrodion-1">
                                <div class="accrodion active">
                                    <div class="accrodion-title">
                                        <h4>How can I track my shipment?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>It is a long established fact that a reader will be distr acted bioiiy the real ism dablea content of a page when looking at its layout </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accrodion">
                                    <div class="accrodion-title">
                                        <h4>What is the average delivery time?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>It is a long established fact that a reader will be distr acted bioiiy the real ism dablea content of a page when looking at its layout </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accrodion">
                                    <div class="accrodion-title">
                                        <h4>Do you offer Smooth Running Supply?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>It is a long established fact that a reader will be distr acted bioiiy the real ism dablea content of a page when looking at its layout </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="faq-one__right wow fadeInRight" data-wow-delay="300ms">
                            <h3 class="faq-one__from-title">Our One-Stop Car Repair Shop</h3>
                            <form class="contact-form-validated faq-one__form" action="assets/inc/sendemail.php" method="post" novalidate="novalidate">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="faq-one__input-box">
                                            <input type="text" name="name" placeholder="Your Name" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="faq-one__input-box">
                                            <input type="email" name="email" placeholder="Your Email" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="faq-one__input-box">
                                            <input type="text" name="text" placeholder="Phone Number" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="faq-one__input-box">
                                            <div class="select-box">
                                                <select class="selectmenu wide">
                                                    <option selected="selected">Choose a Option</option>
                                                    <option>Type Of Service 01</option>
                                                    <option>Type Of Service 02</option>
                                                    <option>Type Of Service 03</option>
                                                    <option>Type Of Service 04</option>
                                                    <option>Type Of Service 05</option>
                                                    <option>Type Of Service 06</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="faq-one__input-box text-message-box">
                                            <textarea name="message" placeholder="Message here.."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="faq-one__btn-box">
                                            <button type="submit" class="thm-btn faq-one__btn">Submit Now<span></span></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.faq-one -->

        <!-- ============================================
             SECTION 11: TESTIMONIAL ONE
        ============================================ -->
        <section class="testimonial-one">
            <div class="container">
                <div class="testimonial-one__inner">
                    <div class="testimonial-one__img-one">
                        <img src="assets/images/testimonial/testimonial-one-img-1.jpg" alt="">
                    </div>
                    <div class="section-title text-center">
                        <div class="section-title__tagline-box">
                            <span class="section-title__tagline">clients testimonial</span>
                        </div>
                        <h2 class="section-title__title">Your supply chain partner<br> for success</h2>
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
                                        "0": {"spaceBetween": 0,"slidesPerView": 1},
                                        "375": {"spaceBetween": 0,"slidesPerView": 1},
                                        "575": {"spaceBetween": 0,"slidesPerView": 1},
                                        "768": {"spaceBetween": 30,"slidesPerView": 1},
                                        "992": {"spaceBetween": 30,"slidesPerView": 1},
                                        "1200": {"spaceBetween": 30,"slidesPerView": 1},
                                        "1320": {"spaceBetween": 30,"slidesPerView": 1}
                                    }
                                }'>
                                    <div class="swiper-wrapper">
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote"><span class="icon-quote"></span></div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-1.jpg" alt="">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Nafiz Bhuiyan</h3>
                                                <p class="testimonial-one__client-sub-title">Manegar</p>
                                                <p class="testimonial-one__text">Logistic service involves the planning implementation an and control of the efficient and effective movement and storage of goods and materials Logistic service involves the planning implementation and control</p>
                                            </div>
                                        </div>
                                        <!--Testimonial One Single End-->
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote"><span class="icon-quote"></span></div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-2.jpg" alt="">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Robert Son</h3>
                                                <p class="testimonial-one__client-sub-title">Manegar</p>
                                                <p class="testimonial-one__text">Logistic service involves the planning implementation an and control of the efficient and effective movement and storage of goods and materials Logistic service involves the planning implementation and control</p>
                                            </div>
                                        </div>
                                        <!--Testimonial One Single End-->
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote"><span class="icon-quote"></span></div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-3.jpg" alt="">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Harbert Spin</h3>
                                                <p class="testimonial-one__client-sub-title">Manegar</p>
                                                <p class="testimonial-one__text">Logistic service involves the planning implementation an and control of the efficient and effective movement and storage of goods and materials Logistic service involves the planning implementation and control</p>
                                            </div>
                                        </div>
                                        <!--Testimonial One Single End-->
                                        <!--Testimonial One Single Start-->
                                        <div class="swiper-slide">
                                            <div class="testimonial-one__single">
                                                <div class="testimonial-one__quote"><span class="icon-quote"></span></div>
                                                <div class="testimonial-one__client-img">
                                                    <img src="assets/images/testimonial/testimonial-1-4.jpg" alt="">
                                                </div>
                                                <div class="testimonial-one__ratting">
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star"></span>
                                                    <span class="icon-star last-star"></span>
                                                </div>
                                                <h3 class="testimonial-one__client-name">Mainto Vula</h3>
                                                <p class="testimonial-one__client-sub-title">Manegar</p>
                                                <p class="testimonial-one__text">Logistic service involves the planning implementation an and control of the efficient and effective movement and storage of goods and materials Logistic service involves the planning implementation and control</p>
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
        <!-- /.testimonial-one -->

        <!-- ============================================
             SECTION 12: BLOG ONE
        ============================================ -->
        <section class="blog-one">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-title__tagline-box">
                        <span class="section-title__tagline">Latests Blog</span>
                    </div>
                    <h2 class="section-title__title">Streamlining your supply<br> chain processes</h2>
                </div>
                <div class="row">
                    <!--Blog One Single Start-->
                    <div class="col-xl-4 col-lg-4 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="blog-one__single">
                            <div class="blog-one__img-box">
                                <div class="blog-one__img">
                                    <img src="assets/images/blog/blog-1-1.jpg" alt="">
                                </div>
                                <div class="blog-one__date"><p>24 March</p></div>
                            </div>
                            <div class="blog-one__content">
                                <h3 class="blog-one__title"><a href="blog-details.php">Your trusted logistics provider Express Logistics</a></h3>
                                <p class="blog-one__text">It is a long established fact that a reader will williljl be distracted </p>
                                <div class="blog-one__read-more">
                                    <a href="blog-details.php">Read More<span class="icon-arrow-right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Blog One Single End-->
                    <!--Blog One Single Start-->
                    <div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="200ms">
                        <div class="blog-one__single">
                            <div class="blog-one__img-box">
                                <div class="blog-one__img">
                                    <img src="assets/images/blog/blog-1-2.jpg" alt="">
                                </div>
                                <div class="blog-one__date"><p>24 March</p></div>
                            </div>
                            <div class="blog-one__content">
                                <h3 class="blog-one__title"><a href="blog-details.php">Logistics expertise for your the competitive advantage</a></h3>
                                <p class="blog-one__text">It is a long established fact that a reader will williljl be distracted </p>
                                <div class="blog-one__read-more">
                                    <a href="blog-details.php">Read More<span class="icon-arrow-right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Blog One Single End-->
                    <!--Blog One Single Start-->
                    <div class="col-xl-4 col-lg-4 wow fadeInRight" data-wow-delay="300ms">
                        <div class="blog-one__single">
                            <div class="blog-one__img-box">
                                <div class="blog-one__img">
                                    <img src="assets/images/blog/blog-1-3.jpg" alt="">
                                </div>
                                <div class="blog-one__date"><p>24 March</p></div>
                            </div>
                            <div class="blog-one__content">
                                <h3 class="blog-one__title"><a href="blog-details.php">Streamlining your supply chain processes Express </a></h3>
                                <p class="blog-one__text">It is a long established fact that a reader will williljl be distracted </p>
                                <div class="blog-one__read-more">
                                    <a href="blog-details.php">Read More<span class="icon-arrow-right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Blog One Single End-->
                </div>
            </div>
        </section>
        <!-- /.blog-one -->

        <!-- ============================================
             SECTION 13: CTA ONE
        ============================================ -->
        <section class="cta-one">
            <div class="container">
                <div class="cta-one__inner">
                    <div class="cta-one__bg-img" style="background-image: url(assets/images/resources/cta-one-bg-img.jpg);"></div>
                    <div class="cta-one__content-box">
                        <div class="cta-one__icon">
                            <span class="icon-call"></span>
                            <div class="cta-one__shape-1">
                                <img src="assets/images/shapes/cta-one-shape-1.png" alt="">
                            </div>
                        </div>
                        <h3 class="cta-one__title">Need any help? <br> contact us!</h3>
                        <div class="cta-one__contact-box">
                            <div class="icon"><span class="icon-phone"></span></div>
                            <div class="content">
                                <p>Need help?</p>
                                <h3><a href="tel:3075550133">(307) 555-0133</a></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.cta-one -->
<?php require_once('include/footer.php'); ?>
        <!-- ============================================
             FOOTER
        ============================================ -->
      
        <!--Site Footer End-->

    </div><!-- /.page-wrapper -->
    
    <!-- ============================================
         VENDOR JS - Loaded at top (as per original)
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
    <script src="assets/vendors/owl-carousel/owl.carousel.min.js"></script>
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