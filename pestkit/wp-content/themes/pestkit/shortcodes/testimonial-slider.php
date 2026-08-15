<div class="container-fluid testimonial py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                <?php the_field("testimonial_subtitle", "options") ?>
            </h5>
            <h1 class="display-5 w-50 mx-auto">
                <?php the_field("testimonial_title", "options") ?>
            </h1>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay=".5s">
            <?php
            // 1. Настраиваем глобальный запрос ко ВСЕМ комментариям сайта
            $comment_args = array(
                'status'  => 'approve', // Только одобренные модератором
                'number'  => 12,        // Максимальное количество отзывов в слайдере
                'type'    => 'comment', // Только обычные комментарии (без уведомлений пингов)
                'order'   => 'DESC',     // Сначала самые свежие

                'post_id' => 268,
            );

            $comments_query = new WP_Comment_Query;
            $global_comments = $comments_query->query($comment_args);

            // 2. Запускаем цикл перебора отзывов
            if (! empty($global_comments)) {
                foreach ($global_comments as $comment) {

                    $author_name  = $comment->comment_author;
                    $comment_text = $comment->comment_content;
                    $comment_date = mysql2date('M d, Y', $comment->comment_date);

                    // Безопасно получаем аватарку автора по его Email
                    $avatar_url = get_avatar_url($comment->comment_author_email, array('size' => 80));
            ?>

                    <div class="testimonial-item">
                        <div class="testimonial-content rounded mb-4 p-4">
                            <?php echo esc_html($comment_text); ?>
                        </div>
                        <div class="d-flex align-items-center  mb-4" style="padding: 0 0 0 25px;">
                            <div class="position-relative">
                                <img
                                    src="<?php echo esc_url($avatar_url); ?>"
                                    class="img-fluid rounded-circle py-2"
                                    alt="<?php echo esc_attr($author_name); ?>">
                                <div class="position-absolute" style="top: 33px; left: -25px;">
                                    <i class="fa fa-quote-left rounded-pill bg-primary text-dark p-3"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0">
                                    <?php echo esc_html($author_name); ?>
                                </h4>
                                <p class="mb-1">
                                    <?php echo $comment_date; ?>
                                </p>
                                <div class="d-flex">
                                    <?php
                                    // 1. Сначала пробуем взять оценку из конкретного текстового комментария
                                    $rating = get_comment_meta($comment->comment_ID, 'rating', true);

                                    // 3. Если вообще никаких оценок нет, ставим 5 по умолчанию
                                    $final_rating = !empty($rating) ? round(floatval($rating)) : 5;

                                    // Рисуем звёздочки на основе финального значения
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $final_rating) {
                                            echo '<small class="fas fa-star text-primary me-1"></small>';
                                        } else {
                                            echo '<small class="far fa-star text-primary me-1"></small>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                }
            } else {
                // Дефолтные карточки-заглушки, если база данных пуста
                ?>
                <div class="testimonial-item bg-light rounded p-4">
                    <p class="fs-5 text-muted mb-4">
                        There are no reviews yet. Be the first one!
                    </p>
                    <h5 class="mb-1 text-dark fw-bold">
                        <?php echo esc_html(get_users(array('role' => 'administrator', 'number' => 1))[0]->user_login) ?>
                    </h5>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<div class="text-center mt-5 mb-4 wow fadeInUp" data-wow-delay=".3s">
    <a
        href="<?php echo get_field("testimonial_lint_to_leave_feedback", "options")["url"] ?>"
        class="btn btn-primary border-0 rounded-pill px-5 py-3 text-uppercase fw-bold btn-leave-feedback">
        <?php echo get_field("testimonial_lint_to_leave_feedback", "options")["title"] ?>
    </a>
</div>