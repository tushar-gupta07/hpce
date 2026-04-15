<?php if (!isset($base_url)) { $base_url = "http://localhost/hpce/"; } ?>

<!-- ============================================
     MOBILE NAV WRAPPER
============================================ -->
<div class="mobile-nav__wrapper">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <div class="mobile-nav__content">
        <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>
        <div class="logo-box">
            <a href="<?php echo $base_url; ?>" aria-label="logo image">
                <img src="<?php echo $base_url; ?>assets/images/resources/logo-2.png" width="150" alt="RK Hospital Logo" />
            </a>
        </div>
        <div class="mobile-nav__container"></div>
        <ul class="mobile-nav__contact list-unstyled">
            <li>
                <i class="fa fa-envelope"></i>
                <a href="mailto:info@rkhospital.com">info@rkhospital.com</a>
            </li>
            <li>
                <i class="fa fa-phone-alt"></i>
                <a href="tel:666-888-0000">666 888 0000</a>
            </li>
        </ul>
        <div class="mobile-nav__top">
            <div class="mobile-nav__social">
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-facebook-square"></a>
                <a href="#" class="fab fa-pinterest-p"></a>
                <a href="#" class="fab fa-instagram"></a>
            </div>
        </div>
    </div>
</div>
<!-- /.mobile-nav__wrapper -->

<!-- ============================================
     SEARCH POPUP
============================================ -->
<div class="search-popup">
    <div class="search-popup__overlay search-toggler"></div>
    <div class="search-popup__content">
        <form action="#">
            <label for="search" class="sr-only">search here</label>
            <input type="text" id="search" placeholder="Search Here..." />
            <button type="submit" aria-label="search submit" class="thm-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>
<!-- /.search-popup -->

<!-- ============================================
     PRELOADER
============================================ -->
<div class="preloader">
    <div class="preloader__image"></div>
</div>

<!-- ============================================
     SCROLL TO TOP
============================================ -->
<a href="#" data-target="html" class="scroll-to-target scroll-to-top">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- ============================================
     SIDEBAR WIDGET
============================================ -->
<div class="xs-sidebar-group info-group info-sidebar">
    <div class="xs-overlay xs-bg-black"></div>
    <div class="xs-sidebar-widget">
        <div class="sidebar-widget-container">
            <div class="widget-heading">
                <a href="#" class="close-side-widget">X</a>
            </div>
            <div class="sidebar-textwidget">
                <div class="sidebar-info-contents">
                    <div class="content-inner">
                        <div class="logo">
                            <a href="<?php echo $base_url; ?>">
                                <img src="<?php echo $base_url; ?>assets/images/resources/logo-2.png" alt="RK Hospital Logo" />
                            </a>
                        </div>
                        <div class="content-box">
                            <h4>About Us</h4>
                            <p>RK Hospital is committed to providing quality healthcare services to our patients.</p>
                        </div>
                        <div class="form-inner">
                            <h4>Get a free quote</h4>
                            <form action="<?php echo $base_url; ?>assets/inc/sendemail.php" class="contact-form-validated" novalidate="novalidate">
                                <div class="form-group">
                                    <input type="text" name="name" placeholder="Name" required="">
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" placeholder="Email" required="">
                                </div>
                                <div class="form-group">
                                    <textarea name="message" placeholder="Message..."></textarea>
                                </div>
                                <div class="form-group message-btn">
                                    <button type="submit" class="thm-btn form-inner__btn">Submit Now</button>
                                </div>
                            </form>
                            <div class="result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.sidebar widget -->

<!-- ============================================
     PAGE WRAPPER START
============================================ -->
<div class="page-wrapper">

    <!-- ============================================
         HEADER
    ============================================ -->
    <header class="main-header">
        <nav class="main-menu">
            <div class="main-menu__wrapper">
                <div class="container">
                    <div class="main-menu__wrapper-inner">
                        <div class="main-menu__left">
                            <div class="main-menu__logo">
                                <a href="<?php echo $base_url; ?>">
                                    <img src="<?php echo $base_url; ?>assets/images/resources/logo-1.png" alt="RK Hospital Logo">
                                </a>
                            </div>
                            <div class="main-menu__main-menu-box">
                                <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                                <ul class="main-menu__list">
                                    <li><a href="<?php echo $base_url; ?>">Home</a></li>
                                    <li><a href="<?php echo $base_url; ?>about-us">About</a></li>
                                    <li><a href="<?php echo $base_url; ?>services">Services</a></li>
                                    <li><a href="<?php echo $base_url; ?>blogs">Blogs</a></li>
                                    <li><a href="<?php echo $base_url; ?>contact-us">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="main-menu__right">
                            <div class="main-menu__search-nav-sidebar-btn-box">
                                <div class="main-menu__search-box">
                                    <a href="#" class="main-menu__search search-toggler fas fa-search"></a>
                                </div>
                                <div class="main-menu__nav-sidebar-icon">
                                    <a class="navSidebar-button" href="#">
                                        <span class="icon-dots-menu-one"></span>
                                        <span class="icon-dots-menu-two"></span>
                                        <span class="icon-dots-menu-three"></span>
                                    </a>
                                </div>
                                <div class="main-menu__btn-box">
                                    <a href="<?php echo $base_url; ?>contact-us" class="thm-btn main-menu__btn">Get A Free Quote<span></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sticky Header -->
    <div class="stricky-header stricked-menu main-menu">
        <div class="sticky-header__content"></div>
    </div>