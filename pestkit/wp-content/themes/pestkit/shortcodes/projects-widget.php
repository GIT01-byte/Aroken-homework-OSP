<?php
$subtitle      = $args['subtitle'] ?? '';
$title         = $args['title'] ?? '';
$projects_count = intval($args['projects_count'] ?? 6);
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
                <h1 class="display-5">
                    <?php echo esc_html($title); ?>
                </h1>
            <?php endif; ?>
        </div>
        <div class="row g-5">
            <?php
            // Передаем динамически вычисленное количество постов
            $query_args = array(
                'posts_per_page' => $projects_count,
                'post_type'      => 'our-project',
                'orderby'        => 'date',
                'order'          => 'ASC'
            );

            $query = new WP_Query($query_args);

            // Цикл
            if ($query->have_posts()) {
                $delay = 0.3;
                while ($query->have_posts()) {
                    $query->the_post(); ?>

                    <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="<?php echo $delay ?>s">
                        <div class="project-item">
                            <div class="project-left bg-dark"></div>
                            <div class="project-right bg-dark"></div>
                            <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" class="img-fluid h-100">
                            <a href="<?php the_permalink(); ?>" class="fs-4 fw-bold text-center">
                                <?php the_title(); ?>
                            </a>
                        </div>
                    </div>

                    <?php $delay += 0.2; ?>

            <?php }
            } else {
                echo "<div class='col-12 text-center'>Ошибка. Постов не найдено</div>";
            }
            wp_reset_postdata(); ?>
        </div>
    </div>
</div>