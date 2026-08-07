<?php
/*
Template name: ШАБЛОН страницы Blog
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

<!-- Blog Section Begin -->
<div class="blog-section spad">
    <div class="container">
        <div class="row">
            <?php
            if (get_field("blog_count")) {
                $postsAmount = get_field("blog_count");
            } else {
                $postsAmount = 6;
            }
            $args = array(
                'posts_per_page' => $postsAmount,
                'post_type' => 'post',
                'orderby' => 'date',
                'order' => 'ASC'
            );
            $query = new WP_Query($args);
            $count = 1;
            if (get_field("blog_accent_post_step")) {
                $blog_step = get_field("blog_accent_post_step");
            } else {
                $blog_step = 3;
            }
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post() ?>

                    <?php if ($count % $blog_step !== 0) { ?>

                        <?php
                        add_filter('excerpt_length', function () {
                            return 10;
                        });
                        add_filter('excerpt_more', function () {
                            global $post;
                            return ' <a href="' . get_permalink($post) . '" style="color: #e32879;; white-space: nowrap; display: inline-block;">Read&nbsp;more <span class="arrow">→</span></a>';
                        }); ?>

                        <div class="col-lg-6">
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

                    <?php } else { ?>

                        <?php
                        add_filter('excerpt_length', function () {
                            return 30;
                        });
                        add_filter('excerpt_more', function () {
                            global $post;
                            return ' <a href="' . get_permalink($post) . '" style="color: #ffffff; font-weight: 600; white-space: nowrap; display: inline-block;">Read&nbsp;more <span class="arrow">→</span></a>';
                        }); ?>

                        <div class="col-lg-6">
                            <div class="blog-item solid-bg">
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

                    <?php } ?>

                    <?php $count++ ?>
            <?php }
            } else {
                echo "Ошибка. Постов не найдено";
            }
            wp_reset_postdata(); ?>

        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="blog-btn">
                    <a href="#" class="primary-btn">Work With Us</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Blog Section End -->

    <?php get_footer(); ?>