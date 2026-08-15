<?php
// Template name:  ШАБЛОН страницы About
get_header();
?>

<!-- About Start -->
<?php echo do_shortcode("[about-widget]") ?>
<!-- About End -->


<!-- Call To Action Start -->
<?php echo do_shortcode("[newsletter-form]") ?>
<!-- Call To Action End -->


<!-- Team Start -->
<?php echo do_shortcode("[team-widget]") ?>
<!-- Team End -->

<?php
get_footer();
