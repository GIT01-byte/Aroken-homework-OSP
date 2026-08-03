<?php
get_header();
?>
    <?php
    while (have_posts()) :
        the_post();

        the_title();

        the_content();

        the_date();
        the_author();
    endwhile; ?>
<?php
get_footer();
