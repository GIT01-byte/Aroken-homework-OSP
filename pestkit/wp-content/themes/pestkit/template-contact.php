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
            <?php
            if (have_rows('about_contact_info_repeater')):
                while (have_rows('about_contact_info_repeater')) : the_row();
                    $content_type = get_sub_field("content_type") ?>

                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="d-flex bg-light p-3 rounded contact-btn-link">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                                <i class="<?php the_sub_field('icon_class'); ?> text-dark"></i>
                            </div>
                            <div class="ms-3 contact-link">
                                <h4 class="text-dark">
                                    <?php the_sub_field('title'); ?>
                                </h4>

                                <?php if ($content_type === "social_links") { ?>

                                    <div class="d-flex justify-content-center">
                                        <?php
                                        if (have_rows('social_links_repeater')):
                                            while (have_rows('social_links_repeater')) : the_row(); ?>

                                                <a class="pe-2" href="<?php echo get_sub_field('link') ?>">
                                                    <i class="fab fa-<?php echo get_sub_field('social') ?> text-dark"></i>
                                                </a>

                                        <?php
                                            endwhile;
                                        else :
                                            echo "Ошибка. Поля не найдены";
                                        endif;
                                        ?>

                                    </div>

                                    <?php } else if ($content_type === "text_info") {
                                    $is_link = get_sub_field("is_link");
                                    if ($is_link) { ?>

                                        <a
                                            href="<?php the_sub_field('link'); ?>">
                                            <h5 class="text-dark d-inline fs-6">
                                                <?php the_sub_field('text'); ?>
                                            </h5>
                                        </a>

                                    <?php } else { ?>

                                        <h5 class="text-dark d-inline fs-6">
                                            <?php the_sub_field('text'); ?>
                                        </h5>

                                    <?php } ?>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
        </div>
    </div>
</div>
<!-- Contact End -->

<?php
get_footer();
