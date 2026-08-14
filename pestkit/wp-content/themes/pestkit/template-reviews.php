<?php
// Template name:  ШАБЛОН страницы Reviews
get_header();
?>

<?php if (comments_open() || get_comments_number()) {
    comments_template();
} ?>

<?php
get_footer();
