<?php
/*
Template name: ШАБЛОН страницы About Us
*/
get_header();
?>
<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="breadcrumb-option">
                    <?php if (function_exists('kama_breadcrumbs')) kama_breadcrumbs(""); ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 text-right">
                <div class="breadcrumb-text">
                    <h3><?php the_title() ?></h3>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- About Us Section Begin -->
<section class="about-us-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="as-pic">
                    <img
                        src="<?php echo get_field("hero_image")["url"] ?>"
                        alt="<?php echo get_field("hero_image")["alt"] ?>">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="as-text ap-text">
                    <div class="section-title">
                        <span><?php echo get_field("hero_subtitle") ?></span>
                        <h2><?php echo get_field("hero_title") ?></h2>
                    </div>
                    <p class="f-para"><?php echo get_field("hero_descr") ?></p>
                    <div class="about-counter">
                        <?php
                        if (have_rows('hero_counters_repeater')):
                            while (have_rows('hero_counters_repeater')) : the_row(); ?>

                                <div class="ac-item">
                                    <h2 class="ab-count"><?php echo get_sub_field("number") ?></h2>
                                    <p><?php echo get_sub_field("name") ?></p>
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
        </div>
    </div>
</section>
<!-- About Us Section End -->

<!-- Member Section Begin -->
<section class="member-section spad ap-member">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span><?php echo get_field("designers_subtitle") ?></span>
                    <h2><?php echo get_field("designers_title") ?></h2>
                </div>
            </div>
        </div>
        <div class="row member-custom-row">
            <?php
            if (get_field("designers_count_employees")) {
                $postsAmount = get_field("designers_count_employees");
            } else {
                $postsAmount = 3;
            }
            $args = array(
                'posts_per_page' => $postsAmount,
                'post_type' => 'our-team',
                'orderby' => 'date',
                'order' => 'ASC'
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $is_accent = get_field("our_team_is_accent") ?>

                    <div class="col-lg-4 col-md-6 <?php if ($is_accent) echo "member-accent-card" ?>">
                        <div
                            class="member-item set-bg"
                            data-setbg="<?php echo get_the_post_thumbnail_url(); ?>">
                            <div class="mi-text">
                                <?php the_content() ?>
                                <div class="mt-title">
                                    <h4><?php the_title() ?></h4>
                                    <span><?php the_field("our_team_job") ?></span>
                                </div>
                                <div class="mt-social">
                                    <?php
                                    if (have_rows('our_team_socials_repeater')):
                                        while (have_rows('our_team_socials_repeater')) : the_row(); ?>

                                            <a
                                                href="<?php the_sub_field('link'); ?>">
                                                <i class="fa fa-<?php the_sub_field('social'); ?>"></i>
                                            </a>

                                    <?php
                                        endwhile;
                                    else :
                                        echo "Ошибка. Поля не найдены";
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php }
            } else {
                echo "Ошибка. Постов не найдено";
            }
            wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<!-- Member Section End -->

<!-- Call To Action Section Begin -->
<?php echo do_shortcode("[pink-banner]") ?>
<!-- Call To Action Section End -->
<?php get_footer(); ?>