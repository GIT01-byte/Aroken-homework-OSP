<?php
// Template name:  ШАБЛОН страницы Services
get_header();
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5">
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


<!-- Services Start -->
<?php echo do_shortcode("[service-capabilities]") ?>
<!-- Services End -->


<!-- Testimonial Start -->
<?php echo do_shortcode("[testimonial-slider]") ?>
<!-- Testimonial End -->


<?php
get_footer();
