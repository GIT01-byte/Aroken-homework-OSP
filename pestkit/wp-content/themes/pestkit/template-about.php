<?php
// Template name:  ШАБЛОН страницы About
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


<!-- About Start -->
<?php echo do_shortcode("[about-widget]") ?>
<!-- About End -->


<!-- Call To Action Start -->
<?php echo do_shortcode("[newsletter-form]") ?>
<!-- Call To Action End -->


<!-- Team Start -->
<?php echo do_shortcode("[team-widget]") ?>
<!-- Team End -->

<?php
get_footer();
