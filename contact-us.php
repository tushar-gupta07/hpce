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
  
    <!-- /.sidebar widget -->

    <!-- ============================================
         PAGE WRAPPER START
    ============================================ -->
    <div class="page-wrapper">

     

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/page-header-bg.jpg);">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>Contact</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="index.php">Home</a></li>
                            <li><span class="icon-angle-left"></span></li>
                            <li>Contact</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--Contact One Start-->
        <section class="contact-one">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-title__tagline-box">
                        <span class="section-title__tagline">Contact us</span>
                    </div>
                    <h2 class="section-title__title">Get in Touch With Us</h2>
                </div>
                <div class="contact-one__inner">
                    <ul class="contact-one__contact-list list-unstyled">
                        <li>
                            <div class="icon">
                                <span class="icon-call"></span>
                            </div>
                            <div class="content">
                                <h3>Lets Talk us</h3>
                                <p>Phone number: <a href="tel:32566800890">+32566 - 800 - 890</a></p>
                                <p>Fax: <a href="tel:123458963007">1234 -58963 - 007</a></p>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="icon-location location-icon"></span>
                            </div>
                            <div class="content">
                                <h3>Address</h3>
                                <p>Dhaka 102, 8000 sent behaibior<br> road 45 house of street</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="icon-envolop email-icon"></span>
                            </div>
                            <div class="content">
                                <h3>Send us email</h3>
                                <p><a href="mailto:nafizislam1223@gmail.com">nafizislam1223@gmail.com</a></p>
                                <p><a href="mailto:demo23gmail.com">demo23gmail.com</a></p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!--Contact One End-->

        <!--Contact Two Start-->
        <section class="contact-two">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7">
                        <div class="contact-two__left">
                            <div class="section-title text-left">
                                <div class="section-title__tagline-box">
                                    <span class="section-title__tagline">Contact</span>
                                </div>
                                <h2 class="section-title__title">Get Touch Here</h2>
                            </div>
                            <form class="contact-form-validated contact-two__form" action="assets/inc/sendemail.php"
                                method="post" novalidate="novalidate">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="contact-two__input-box">
                                            <input type="text" name="name" placeholder="Name" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="contact-two__input-box">
                                            <input type="email" name="email" placeholder="E-mail" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-lg-12">
                                        <div class="contact-two__input-box">
                                            <input type="text" name="text" placeholder="Subject" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="contact-two__input-box text-message-box">
                                            <textarea name="message" placeholder="Message"></textarea>
                                        </div>
                                    </div>
                                    <div class=" col-xl-12">
                                        <div class="contact-two__btn-box">
                                            <button type="submit" class="thm-btn contact-two__btn">Submit
                                                Now<span></span></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="result"></div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="contact-two__right">
                            <div class="contact-two__img">
                                <img src="assets/images/resources/contact-two-img-1.jpg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Contact Two End-->

        <!--Google Map Start-->
        <section class="google-map-one google-map-two">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4562.753041141002!2d-118.80123790098536!3d34.152323469614075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80e82469c2162619%3A0xba03efb7998eef6d!2sCostco+Wholesale!5e0!3m2!1sbn!2sbd!4v1562518641290!5m2!1sbn!2sbd"
                class="google-map__one" allowfullscreen></iframe>

        </section>
        <!--Google Map End-->

        <!--CTA One Start-->
        <section class="cta-one">
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
                        <h3 class="cta-one__title">Need any help?
                            <br> contact us!</h3>
                        <div class="cta-one__contact-box">
                            <div class="icon">
                                <span class="icon-phone"></span>
                            </div>
                            <div class="content">
                                <p>Need help?</p>
                                <h3><a href="tel:3075550133">(307) 555-0133</a></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================
             FOOTER
        ============================================ -->
       <?php require_once('include/footer.php'); ?>
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