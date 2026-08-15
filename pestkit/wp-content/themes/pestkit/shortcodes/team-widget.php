<?php
$subtitle     = $args['subtitle'] ?? '';
$title        = $args['title'] ?? '';
$members_count = intval($args['members_count'] ?? 4);
?>

<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <?php if (!empty($subtitle)) : ?>
                <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                    <?php echo esc_html($subtitle); ?>
                </h5>
            <?php endif; ?>

            <?php if (!empty($title)) : ?>
                <h1 class="display-5 w-50 mx-auto">
                    <?php echo esc_html($title); ?>
                </h1>
            <?php endif; ?>
        </div>
        <div class="row g-5">
            <?php
            // Передаем динамическое количество постов
            $query_args = array(
                'posts_per_page' => $members_count,
                'post_type'      => 'our-team',
                'orderby'        => 'date',
                'order'          => 'ASC'
            );
            $query = new WP_Query($query_args);

            // Цикл
            if ($query->have_posts()) {
                $delay = 0.3;
                while ($query->have_posts()) {
                    $query->the_post(); ?>

                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="<?php echo $delay; ?>s">
                        <div class="rounded team-item">
                            <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" class="img-fluid w-100 rounded-top border border-bottom-0">
                            <div class="team-content bg-primary text-dark text-center py-3">
                                <span class="fs-4 fw-bold">
                                    <?php the_title(); ?>
                                </span>
                                <p class="text-muted mb-0">
                                    <?php the_field("our_team_job"); ?>
                                </p>
                            </div>
                            <div class="team-icon d-flex flex-column">
                                <?php
                                if (have_rows('our_team_socials_repeater')):
                                    while (have_rows('our_team_socials_repeater')) : the_row(); ?>

                                        <a href="<?php the_sub_field('link'); ?>" class="btn btn-primary border-0 mb-2">
                                            <i class="fab fa-<?php the_sub_field('social'); ?>"></i>
                                        </a>

                                <?php
                                    endwhile;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php $delay += 0.2; ?>

            <?php }
            } else {
                echo "<div class='col-12 text-center'>Ошибка. Сотрудников не найдено</div>";
            }
            wp_reset_postdata(); ?>
        </div>
    </div>
</div>