<?php
/*
Template name: ШАБЛОН первой страницы
*/
get_header();
?>

<!-- Hero Section Begin -->
<section
    class="hero-section set-bg"
    data-setbg="<?php echo get_field("hero_background") ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="hs-text">
                    <span><?php echo get_field("hero_subtitle") ?></span>
                    <h2><?php echo get_field("hero_title") ?></h2>
                    <p><?php echo get_field("hero_description") ?></p>
                    <a
                        href="<?php echo get_field("hero_btn")["url"] ?>"
                        class="primary-btn">
                        <?php echo get_field("hero_btn")["title"] ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Hero Section End -->

<!-- About Us Section Begin -->
<section class="about-us-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="as-pic">
                    <img
                        src="<?php echo get_field("about_image")["url"] ?>"
                        alt="<?php echo get_field("about_image")["alt"] ?>">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="as-text">
                    <div class="section-title">
                        <span><?php echo get_field("about_subtitle") ?></span>
                        <h2><?php echo get_field("about_title") ?></h2>
                    </div>
                    <p class="f-para"><?php echo get_field("about_description") ?></p>
                    <a
                        href="<?php echo get_field("about_btn")["url"] ?>"
                        class="primary-btn">
                        <?php echo get_field("about_btn")["title"] ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Us Section End -->

<!-- Services Section Begin -->
<section class="services-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span><?php echo get_field("service_subtitle") ?></span>
                    <h2><?php echo get_field("service_title") ?></h2>
                </div>
            </div>
        </div>
        <div class="row services-custom-row">
            <?php
            if (have_rows('services_repeater')) :
                while (have_rows('services_repeater')) : the_row(); ?>

                    <div class="col-lg-4 col-md-6">
                        <div class="service-item">
                            <img
                                src="<?php the_sub_field('image'); ?>">
                            <h4>
                                <?php the_sub_field('title'); ?>
                            </h4>
                            <p>
                                <?php the_sub_field('description'); ?>
                            </p>
                        </div>
                    </div>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif ?>
        </div>
    </div>
</section>
<!-- Services Section End -->

<!-- Portfolio Section Begin -->
<section class="portfolio-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span>Our Portfolio</span>
                    <h2>Turn Your Dream Into Reality</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div
                    class="portfolio-item set-bg large-item"
                    data-setbg="<?php echo get_field("portfolio_image1")["sizes"]['hazze-custom-lg'] ?>">
                    <div class="pi-hover">
                        <a
                            href="#"
                            class="chain-icon"><i class="fa fa-chain"></i></a>
                        <a
                            href="<?php echo get_field("portfolio_image1")["url"] ?>"
                            class="search-icon image-popup"><i class="fa fa-search"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div
                    class="portfolio-item set-bg"
                    data-setbg="<?php echo get_field("portfolio_image2")["sizes"]['hazze-custom-md']  ?>">
                    <div class="pi-hover">
                        <a
                            href="#"
                            class="chain-icon"><i class="fa fa-chain"></i></a>
                        <a
                            href="<?php echo get_field("portfolio_image2")["url"] ?>"
                            class="search-icon image-popup"><i class="fa fa-search"></i></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div
                            class="portfolio-item set-bg"
                            data-setbg="<?php echo get_field("portfolio_image3")["sizes"]['hazze-custom-sm']  ?>">
                            <div class="pi-hover">
                                <a
                                    href="#"
                                    class="chain-icon"><i class="fa fa-chain"></i></a>
                                <a
                                    href="<?php echo get_field("portfolio_image3")["url"] ?>"
                                    class="search-icon image-popup"><i class="fa fa-search"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            class="portfolio-item set-bg"
                            data-setbg="<?php echo get_field("portfolio_image4")["sizes"]['hazze-custom-sm']  ?>">
                            <div class="pi-hover">
                                <a
                                    href="#"
                                    class="chain-icon"><i class="fa fa-chain"></i></a>
                                <a
                                    href="<?php echo get_field("portfolio_image4")["url"] ?>"
                                    class="search-icon image-popup"><i class="fa fa-search"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Portfolio Section End -->

<!-- Counter Section Begin -->
<section class="counter-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="counter-text">
                    <div class="section-title">
                        <span><?php echo get_field("nspeaks_subtitle") ?></span>
                        <h2><?php echo get_field("nspeaks_title") ?></h2>
                    </div>
                    <a
                        href="<?php echo get_field("nspeaks_btn")['url'] ?>"
                        class="primary-btn">
                        <?php echo get_field("nspeaks_btn")['title'] ?>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <?php
                if (have_rows('nspeaks_achievements_repeater')):
                    while (have_rows('nspeaks_achievements_repeater')) : the_row(); ?>

                        <div class="counter-item">
                            <div class="ci-number count">
                                <?php the_sub_field('number'); ?>
                            </div>
                            <div class="ci-text">
                                <h4><?php the_sub_field('title'); ?></h4>
                                <p><?php the_sub_field('description'); ?></p>
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
</section>
<!-- Counter Section End -->

<!-- Testimonial Section Begin -->
<section class="testimonial-section spad">
    <div class="container">
        <div class="row testimonial-slider owl-carousel">
            <?php
            if (have_rows('testimonial_designer_repeater')):
                $i = 1;
                while (have_rows('testimonial_designer_repeater')) : the_row();
                    $isEven = false;
                    if ($i % 2 === 0) $isEven = true;
            ?>

                    <div class="col-lg-6">
                        <div class="testimonial-item"
                            <?php if ($isEven) echo ' style="background: #e32879;"'; ?>">
                            <div class="ti-pic">
                                <img
                                    src="<?php echo get_sub_field('image')['sizes']['thumbnail'] ?>"
                                    alt="<?php echo get_sub_field('image')['alt'] ?>">
                            </div>
                            <div class="ti-text">
                                <div class="ti-title">
                                    <h4 <?php if ($isEven) echo ' style="color: #ffffff;"'; ?>><?php the_sub_field("title") ?></h4>
                                    <span <?php if ($isEven) echo ' style="color: #ffffff;"'; ?>><?php the_sub_field("subtitle") ?></span>
                                </div>
                                <p <?php if ($isEven) echo ' style="color: #ffffff;"'; ?>><?php the_sub_field("description") ?></p>
                            </div>
                        </div>
                    </div>

                    <?php $i++; ?>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
        </div>
    </div>
</section>

<?php echo do_shortcode("[pink-banner]") ?>
<!-- Testimonial Section End -->

<!-- Member Section Begin -->
<section class="member-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span>Our Team</span>
                    <h2>Top Designers</h2>
                </div>
            </div>
        </div>
        <div class="row member-custom-row">
            <?php
            $args = array(
                'posts_per_page' => get_field("our_team_count_employees"),
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

<!-- Blog Section Begin -->
<div class="blog-section latest-blog spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span><?php echo get_field("lblog_subtitle") ?></span>
                    <h2><?php echo get_field("lblog_title") ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            $args = array(
                'posts_per_page' => 2,
                'post_type' => 'post',
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post() ?>

                    <div class="col-md-6">
                        <div class="blog-item">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div
                                        class="bi-pic set-bg"
                                        data-setbg="<?php echo get_the_post_thumbnail_url(); ?>"></div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="bi-text">
                                        <ul>
                                            <li class="bt-date"><i class="fa fa-calendar-o"></i><?php echo get_the_date() ?></li>
                                            <li class="bt-comments"><i class="fa fa-commenting-o"></i> 0</li>
                                        </ul>
                                        <h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
                                        <?php the_excerpt() ?>
                                        <div class="bt-author">
                                            <div class="ba-pic">
                                                <?php global $post;
                                                $url = get_avatar_url($post, "size=100&default=monsterid");
                                                $img = '<img alt="" src="' . $url . '">';
                                                echo $img; ?>
                                            </div>
                                            <div class="ba-text">
                                                <h5><?php the_author() ?></h5>
                                                <span><?php echo wp_roles()->roles['administrator']['name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
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
</div>
<!-- Blog Section End -->
<?php
get_footer();
?>