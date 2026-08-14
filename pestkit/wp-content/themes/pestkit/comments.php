<?php
// Защита от прямого обращения к файлу через браузер
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area mt-5 text-start">

    <!-- 1. Если комментарии в базе данных есть, выводим их -->
    <?php if (have_comments()) : ?>

        <!-- Заголовок секции с подсчетом количества -->
        <h3 class="mb-4 text-dark fw-bold">
            <?php
            $comment_count = get_comments_number();
            echo $comment_count . ($comment_count === 1 ? ' Comment' : ' Comments');
            ?>
        </h3>

        <!-- Контейнер-список, куда WordPress начнет вставлять комментарии -->
        <ul class="list-unstyled comment-list mb-5">
            <?php
            wp_list_comments(array(
                'style'       => 'ul',
                'short_ping'  => true,
                'avatar_size' => 60, // Размер аватарки автора в пикселях
                'callback'    => 'pestkit_custom_comment_format' // Имя функции-шаблона из Шага 2
            ));
            ?>
        </ul>

        <!-- Навигация (стрелочки), если комментариев больше 50 и они разбиты на страницы -->
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="navigation comment-navigation mb-4">
                <div class="nav-links d-flex justify-content-between">
                    <div><?php previous_comments_link('&larr; Older Comments'); ?></div>
                    <div><?php next_comments_link('Newer Comments &rarr;'); ?></div>
                </div>
            </nav>
        <?php endif; ?>

    <?php endif; ?>

    <!-- 2. Блок формы Leave A Comment (Ваш исходный шорткод переносим сюда) -->
    <div class="comment-form-wrapper bg-light rounded p-5 mt-5">
        <h3 class="mb-4 text-dark fw-bold">Leave A Comment</h3>
        <?php echo do_shortcode('[contact-form-7 id="6edf665" title="Comment Form"]'); ?>
    </div>

</div>