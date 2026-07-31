<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package test_hierarchy
 */

get_header();
?>
<h1>INDEX.php</h1>

<main id="primary" class="site-main">

	<?php
	// echo get_post_type_archive_link('post') . '<br>'; 
	if (have_posts()) :

		/* Start the Loop */
		while (have_posts()) :

			the_post();

			the_title(); echo '<br>';

		endwhile;

	endif;
	?>

</main>

<?php
get_footer();
