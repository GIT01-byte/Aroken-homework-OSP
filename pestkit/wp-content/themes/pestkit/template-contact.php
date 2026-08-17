<?php
// Template name:  ШАБЛОН страницы Contact
get_header();
?>

<!-- Page Header Start -->
<div
    class="container-fluid page-header py-5"
    style="background: linear-gradient(rgba(0, 0, 0, .7), rgba(0, 0, 0, .7)), url(<?php echo get_field("breadcrumbs_bg_image", "options") ?>) 
        center center no-repeat; background-size: cover;">
    <div class="container text-center py-5">
        <h1 class="display-2 text-white mb-4 animated slideInDown">
            <?php the_title() ?>
        </h1>
        <nav aria-label="breadcrumb" typeof="BreadcrumbList" vocab="https://schema.org">
            <ol class="breadcrumb justify-content-center mb-0 animated slideInDown">
                <?php
                if (function_exists('bcn_display')) {
                    bcn_display();
                }
                ?>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->


<!-- Contact Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                <?php the_field("contact_subtitle") ?>
            </h5>
            <h1 class="display-5 w-50 mx-auto">
                <?php the_field("contact_title") ?>
            </h1>
        </div>
        <div class="row g-5 mb-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="h-100">
                    <?php echo get_field("contact_map") ?>
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay=".5s">
                <?php echo do_shortcode('[contact-form-7 id="ec81201" title="Contact Form" html_class="rounded contact-form"]') ?>
            </div>
        </div>
        <div class="row g-4 wow fadeInUp" data-wow-delay=".3s">
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="d-flex bg-light p-3 rounded contact-btn-link">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                        <i class="fa fa-share text-dark"></i>
                    </div>
                    <div class="ms-3 contact-link">
                        <h4 class="text-dark">fallow Us</h4>
                        <div class="d-flex justify-content-center">
                            <a class="pe-2" href="#"><i class="fab fa-facebook-f text-dark"></i></a>
                            <a class="px-2" href="#"><i class="fab fa-twitter text-dark"></i></a>
                            <a class="px-2" href="#"><i class="fab fa-instagram text-dark"></i></a>
                            <a class="px-2" href="#"><i class="fab fa-linkedin-in text-dark"></i></a>
                            <a class="px-2" href="#"><i class="fab fa-youtube text-dark"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="d-flex bg-light p-3 rounded contact-btn-link">
                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                        <i class="fas fa-map-marker-alt text-dark"></i>
                    </div>
                    <div class="ms-3 contact-link">
                        <h4 class="text-dark">Address</h4>
                        <a href="#">
                            <h5 class="text-dark d-inline fs-6">123 Street, CA, USA</h5>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="d-flex bg-light p-3 rounded contact-btn-link">
                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                        <i class="fa fa-phone text-dark"></i>
                    </div>
                    <div class="ms-3 contact-link">
                        <h4 class="text-dark">Call Us</h4>
                        <a class="h5 text-dark fs-6" href="tel:+0123456789">+012 3456 7890</a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="d-flex bg-light p-3 rounded contact-btn-link">
                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                        <i class="fa fa-envelope text-dark"></i>
                    </div>
                    <div class="ms-3 contact-link">
                        <h4 class="text-dark">Email Us</h4>
                        <a class="h5 text-dark fs-6" href="#">info@example.com</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Contact End -->

<?php
get_footer();
