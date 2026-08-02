<?php echo get_field("hero_description") ?>
<?php echo get_field("nspeaks_btn")['url'] ?>
<?php echo get_field("nspeaks_btn")['title'] ?>

<?php the_field("hero_link") ?>

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