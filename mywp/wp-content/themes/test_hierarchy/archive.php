<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package test_hierarchy
 */

get_header();
?>
<h1>ARCHIVE.php</h1>

<main id="primary" class="site-main">

	<?php if (have_posts()) : ?>

		<header class="page-header">
			<?php
			//Выводит название архива
			the_archive_title('<h1 class="page-title">', '</h1>');
			the_archive_description('<div class="archive-description">', '</div>');
			?>
		</header>

	<?php
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
