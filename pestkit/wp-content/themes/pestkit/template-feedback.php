<?php
// Template name:  ШАБЛОН страницы Feedback
get_header();
?>

<!-- Feedback Form Start -->
<?php if (comments_open() || get_comments_number()) {
    comments_template();
} ?>
<!-- Feedback Form End -->

<?php
get_footer();
