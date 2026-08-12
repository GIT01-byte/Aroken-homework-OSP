<?php echo get_field("hero_description") ?>
<?php echo get_field("nspeaks_btn")['url'] ?>
<?php echo get_field("nspeaks_btn")['title'] ?>
<?php echo get_field("footer_menu_title", 'options') ?>

<?php the_field("hero_link") ?>
<?php the_field('logo_text_1', 'option'); ?>

<img
    src="<?php echo get_field("about_image")["url"] ?>"
    alt="<?php echo get_field("about_image")["alt"] ?>">

<?php
if (have_rows('repeater_field_name')):
    while (have_rows('repeater_field_name')) : the_row(); ?>

        <?php the_sub_field('sub_field_name'); ?>

<?php
    endwhile;
else :
    echo "Ошибка. Поля не найдены";
endif;
?>

<?php echo get_sub_field("link") ?>
<?php the_sub_field("title") ?>

<?php
// задаем нужные нам критерии выборки данных из БД
$args = array(
    'posts_per_page' => 5,
    'orderby' => 'comment_count'
);

$query = new WP_Query($args);

// Цикл
if ($query->have_posts()) {
    echo '<ul>';
    while ($query->have_posts()) {
        $query->the_post();
        echo '<li>' . esc_html(get_the_title()) . '</li>';
    }
    echo '</ul>';
} else {
    // Постов не найдено
}

// Возвращаем оригинальные данные поста. Сбрасываем $post.
wp_reset_postdata(); ?>

<?php

$images = get_field('gallery');

if ($images): ?>
    <ul>
        <?php foreach ($images as $image): ?>
            <li>
                <a href="<?php echo $image['url']; ?>">
                    <img src="<?php echo $image['sizes']['thumbnail']; ?>" alt="<?php echo $image['alt']; ?>" />
                </a>
                <p><?php echo $image['caption']; ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

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