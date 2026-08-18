<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                <?php the_field("blog_subtitle", "options") ?>
            </h5>
            <h1 class="display-5">
                <?php the_field("blog_title", "options") ?>
            </h1>
        </div>
        <div class="owl-carousel blog-carousel wow fadeInUp" data-wow-delay=".5s">
            <?php
            $args = array(
                'post_type' => 'post',
                'orderby' => 'date',
                'order' => 'ASC'
            );
            $query = new WP_Query($args);

            // Цикл
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post(); ?>

                    <div class="blog-item">
                        <?php echo get_the_post_thumbnail(
                            get_the_ID(),
                            'blog-thumb',
                            ['class' => 'img-fluid w-100 rounded-top']
                        ); ?>
                        <div class="rounded-bottom bg-light">
                            <div class="d-flex justify-content-between p-4 pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo get_avatar_url(get_the_author_meta('ID'), 'size=40'); ?>" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <span class="small fw-semibold text-secondary"><?php the_author() ?></span>
                                </div>
                                <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>
                                    <?php echo get_the_date() ?>
                                </span>
                            </div>
                            <div class="px-4 pb-0">
                                <h4>
                                    <?php the_title(); ?>
                                </h4>
                                <p>
                                    <?php echo wp_trim_words(get_the_excerpt(), 12, '...') ?>
                                </p>
                            </div>
                            <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                                <a
                                    href="<?php the_permalink() ?>" type="button" class="btn btn-primary border-0">
                                    <?php the_field("blog_btn_text", "options") ?>
                                </a>
                                <a
                                    href="<?php echo get_permalink() . '#comments' ?>"
                                    class="my-auto btn-primary border-0"><i class="fa fa-comments me-2"></i>
                                    <?php echo get_comments_number(); ?> Comments
                                </a>
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