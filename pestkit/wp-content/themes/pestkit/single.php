<?php
get_header();
?>

<!-- Hero Section Start -->
<div class="container-fluid page-header position-relative py-5 mb-2"
    style="background: linear-gradient(rgba(15, 23, 42, .85), rgba(15, 23, 42, .85)), url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>) center center no-repeat; background-size: cover;">
    <div class="container text-center py-5">
        <!-- Categories -->
        <div class="mb-3">
            <?php
            $categories = get_the_category();
            foreach ($categories as $cat) {
                echo '<span class="badge bg-warning text-dark px-3 py-2 me-2 text-uppercase fw-bold" style="font-size:0.875rem;">' . esc_html($cat->name) . '</span>';
            }
            ?>
        </div>

        <!-- Title and Subtitle -->
        <h1 class="display-3 text-white mb-3 fw-black text-shadow">
            <?php the_title() ?>
        </h1>
        <?php if (get_field('post_subtitle')): ?>
            <p class="lead text-light mx-auto mb-4" style="max-width: 800px; font-size: 1.25rem;">
                <?php the_field('post_subtitle'); ?>
            </p>
        <?php endif; ?>

        <!-- Post Metadata -->
        <div class="d-flex justify-content-center align-items-center text-white-50 flex-wrap" style="gap: 20px; font-size: 0.95rem;">
            <span>
                <i class="fa fa-user text-warning me-2"></i>
                <?php
                while (have_posts()) :
                    the_post();

                    the_author();
                endwhile ?>
            </span>
            <span>
                <i class="fa fa-calendar text-warning me-2"></i>
                <?php echo get_the_date(); ?>
            </span>
        </div>
    </div>
</div>
<!-- Hero Section End -->


<!-- Main Content Area Start -->
<section class="blog-details-section py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Column: Article -->
            <div class="bg-white rounded-4 shadow-sm p-4 p-md-5 content-area">

                <!-- Output Main Content from Gutenberg -->
                <div class="custom-content article-body lh-lg" style="font-size: 1.1rem; color: #334155;">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php the_content() ?>
                    <?php endwhile; ?>
                </div>

                <!-- ACF Repeater: Infographics / Key Numbers Block inside the Article -->
                <?php if (have_rows('infographic_repeater')): ?>
                    <div class="row my-5 g-4 bg-light rounded-3 p-4 border-start border-warning border-4">
                        <?php while (have_rows('infographic_repeater')): the_row(); ?>
                            <div class="col-sm-4 text-center">
                                <h3 class="display-5 fw-bold text-warning mb-1"><?php the_sub_field('number'); ?></h3>
                                <p class="small text-muted text-uppercase fw-semibold mb-0" style="letter-spacing: 0.5px;"><?php the_sub_field('label'); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <!-- Tags and Social Share -->
                <div class="tag-share d-flex flex-column flex-md-row justify-content-between align-items-md-center pt-4 mt-5 border-top border-light-subtle gap-3">
                    <div class="tags d-flex flex-wrap align-items-center gap-2">
                        <span class="me-2 text-muted fw-bold">Tags:</span>
                        <?php the_tags('', ' ', ''); ?>
                    </div>
                    <div class="social-share d-flex align-items-center gap-2">
                        <span class="fw-bold text-muted me-2">Share:</span>
                        <?php if (have_rows('social_links_repeater', 'options')): ?>
                            <?php while (have_rows('social_links_repeater', 'options')) : the_row(); ?>
                                <a href="<?php the_sub_field('link'); ?>" class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                    <i class="fab fa-<?php the_sub_field('social'); ?>"></i>
                                </a>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Author Bio Block -->
                <div class="blog-author d-flex align-items-start gap-4 p-4 mt-5 bg-light rounded-4 border border-light-subtle">
                    <?php
                    global $post;
                    $url = get_avatar_url($post, "size=100&default=monsterid");
                    ?>
                    <img alt="<?php the_author() ?>" src="<?php echo $url; ?>" class="rounded-circle shadow-sm flex-shrink-0" style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h5 class="fw-bold mb-2"><?php the_author() ?></h5>
                        <span class="badge bg-secondary mb-2 text-uppercase" style="font-size: 0.65rem;">Author</span>
                        <p class="text-muted small mb-0"><?php the_author_meta("description"); ?></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- Main Content Area End -->


<!-- Comments Section -->
<section class="comments-section bg-white pb-5 #comments">
    <div class="container">
        <div class="row">
            <?php
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>
        </div>
    </div>
</section>



<!-- Recommendation Section Start -->
<section class="recommend-section py-5 bg-light border-top border-light-subtle">
    <div class="container">
        <!-- Section Title -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold mb-0 text-dark">
                <?php echo get_field("blog_recommended_title") ? esc_html(get_field("blog_recommended_title")) : 'Related Posts'; ?>
            </h2>
            <div class="bg-warning rounded-pill d-none d-sm-block" style="width: 80px; height: 5px;"></div>
        </div>

        <div class="row g-4">
            <?php
            // Set up query for 2 random posts, excluding the current post
            $args = array(
                'posts_per_page' => 2,
                'post_type'      => "post",
                'orderby'        => "rand",
                'post__not_in'   => array(get_the_ID())
            );
            $query = new WP_Query($args);
            $parent_ID = get_the_ID();

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="col-md-6">
                        <!-- Post Card with Hover Animation -->
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden blog-item-custom transition-up" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <div class="row g-0 h-100">
                                <!-- Post Image Preview -->
                                <div class="col-sm-5 position-relative" style="min-height: 200px;">
                                    <div class="position-absolute w-100 h-100"
                                        style="background: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium_large'); ?>) center center no-repeat; background-size: cover;">
                                    </div>
                                </div>
                                <!-- Card Text Content -->
                                <div class="col-sm-7 d-flex flex-column justify-content-between bg-white">
                                    <div class="p-4">
                                        <div class="text-muted mb-2 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?php echo get_avatar_url(get_the_author_meta('ID'), 'size=140'); ?>" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                                <span class="fw-semibold text-secondary"><?php the_author() ?></span>
                                            </div>
                                            <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>
                                                <?php echo get_the_date() ?>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold mb-2">
                                            <a href="<?php the_permalink() ?>" class="text-decoration-none text-dark hover-warning" style="transition: color 0.2s;">
                                                <?php the_title() ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            <?php echo wp_trim_words(get_the_excerpt(), 12, '...'); ?>
                                        </p>
                                    </div>
                                    <div class="p-4 py-1 d-flex justify-content-between bg-primary blog-btn">
                                        <a
                                            href="<?php the_permalink() ?>" class="btn btn-primary border-0">
                                            <?php the_field("blog_recommended_btn_text", $parent_ID) ?>
                                        </a>
                                        <a
                                            href="<?php echo get_permalink() . '#comments' ?>"
                                            class="my-auto btn-primary border-0"><i class="fa fa-comments me-2"></i>
                                            <?php echo get_comments_number(); ?> Comments
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); // Mandatory reset of global post data
            else : ?>
                <div class="col-12">
                    <div class="p-4 text-center rounded-3 bg-white border border-dashed text-muted">
                        <i class="fa fa-folder-open-o display-6 mb-2"></i>
                        <p class="mb-0">
                            <?php the_field("blog_no_post_text") ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- Recommendation Section End -->

<?php
// Подключение стандартного подвала темы
get_footer();
?>