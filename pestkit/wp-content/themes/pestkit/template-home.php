<?php
// Template name:  ШАБЛОН страницы Home
get_header();
?>

<!-- Carousel Start -->
<div class="container-fluid carousel px-0 mb-5 pb-5">
    <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <?php
            if (have_rows('hero_slider')):
                $item_index = 0;
                while (have_rows('hero_slider')) : the_row();
            ?>

                    <li
                        data-bs-target="#carouselId"
                        data-bs-slide-to="<?php echo $item_index ?>"
                        <?php if ($item_index === 0) echo ' class="active" aria-current="true"' ?>
                        aria-label="<?php echo get_sub_field("bg_image")["alt"] ?>">
                    </li>

                    <?php $item_index++ ?>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
        </ol>
        <div class="carousel-inner" role="listbox">
            <?php
            if (have_rows('hero_slider')):
                $item_index = 0;
                while (have_rows('hero_slider')) : the_row();
            ?>

                    <div class="carousel-item <?php if ($item_index === 0) echo "active" ?>">
                        <img
                            src="<?php echo get_sub_field("bg_image")["url"] ?>"
                            class="img-fluid w-100"
                            alt="<?php echo get_sub_field("bg_image")["alt"] ?>">
                        <div class="carousel-caption">
                            <div class="container carousel-content">
                                <h4 class="text-white mb-4 animated slideInDown">
                                    <?php the_sub_field("subtitle") ?>
                                </h4>
                                <h1 class="text-white display-1 mb-4 animated slideInDown">
                                    <?php the_sub_field("title") ?>
                                </h1>
                                <a
                                    href="<?php echo get_sub_field("link")['url'] ?>"
                                    class="me-2">
                                    <button
                                        type="button"
                                        class="px-5 py-3 btn btn-primary border-2 rounded-pill animated slideInDown">
                                        <?php echo get_sub_field("link")['title'] ?>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php $item_index++ ?>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
        </div>
        <button class="carousel-control-prev btn btn-primary border border-2 border-start-0 border-primary" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><?php _e('Previous', 'PestKit'); ?></span>
        </button>
        <button class="carousel-control-next btn btn-primary border border-2 border-end-0 border-primary" type="button" data-bs-target="#carouselId" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><?php _e('Next', 'PestKit'); ?></span>
        </button>
    </div>
</div>
<!-- Carousel End -->


<!-- Get In Touch Start -->
<div class="container-fluid py-5 wow fadeInUp" data-wow-delay=".3s">
    <div class="container py-5">
        <div class="bg-light px-4 py-5 rounded">
            <div class="text-center">
                <h1 class="display-5 mb-5">
                    <?php the_field("find-pest_title") ?>
                </h1>
            </div>
            <?php echo do_shortcode('[contact-form-7 id="4e8dd28" title="Find Pest" html_class="text-center mb-4 ds-block-form"]') ?>
        </div>
    </div>
</div>
<!-- Get In Touch End -->


<!-- About Start -->
<?php echo do_shortcode("[about-widget]") ?>
<!-- About End -->


<!-- Services Start -->
<?php echo do_shortcode("[service-capabilities]") ?>
<!-- Services End -->


<!-- Project Start -->
<?php echo do_shortcode("[projects-widget]") ?>
<!-- Project End -->


<!-- Blog Start -->
<?php echo do_shortcode("[latest-blog-slider]") ?>
<!-- Blog End -->


<!-- Call To Action Start -->
<?php echo do_shortcode("[newsletter-form]") ?>
<!-- Call To Action End -->


<!-- Pricing Start -->
<?php echo do_shortcode("[pricing-plans]") ?>
<!-- Pricing End -->


<!-- Team Start -->
<?php echo do_shortcode('[team-widget members_count="4"]') ?>
<!-- Team End -->


<!-- Testimonial Start -->
<?php echo do_shortcode("[testimonial-slider]") ?>
<!-- Testimonial End -->

<?php
get_footer();
