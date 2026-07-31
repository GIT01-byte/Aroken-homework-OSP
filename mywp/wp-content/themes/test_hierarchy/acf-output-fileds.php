<?php 
/*
Template name: ШАБЛОН вывод полей ACF
*/
acf_form_head();
get_header();
?>

<?php echo esc_html( get_field("text-field") )?><br>

<img src="<?php echo get_field("image")["sizes"]["medium"]?>" alt="<?php echo get_field("image")["alt"] ?>">

<?php if (get_field("truefalse")): ?>
    <?php 
    $images = get_field("gallery");
    var_dump($images);
    $size = "medium";

    if ($images): ?>
        <ul>
            <?php foreach ($images as $image): ?>
                <li>
                    <?php echo wp_get_attachment_image( $image['ID'], $size ) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<h2 class="example-h2">Заголовок H2</h2>

<?php $font_size = get_field("range") ?>
<style type="text/css">
    <?php if ($font_size): ?>
        .example-h2 {
            font-size: <?php echo $font_size ?>px;
        }
    <?php endif; ?>
</style>
<?php

acf_form(array(
    'post_id'       => get_the_ID(), // ID текущего поста
    'fields'        => array('field_6a6b2f2ace44a'), // Сюда нужно вписать КЛЮЧ поля (field_...), а не его имя
    'submit_value'  => 'Сохранить значение',
));
?>

<?php

// проверяем есть ли в повторителе данные
if( have_rows('repeater') ):

 	// перебираем данные
    while ( have_rows('repeater') ) : the_row();

        // отображаем вложенные поля
        the_sub_field("text1"); echo '<br>';
        the_sub_field("text2"); echo '<br>';
    endwhile;

else :

    // вложенных полей не найдено

endif;

?>

<?php
get_footer();
