<?php 
/*
Template name: ШАБЛОН первой страницы
*/
get_header();
?>
    <h1>ШАБЛОН первой страницы</h1>

    <main id="primary" class="site-main"> 
    <h1>SINGLE.php</h1> 
        <?php
        while ( have_posts() ) :
            the_post();

            the_title();

            the_content();

            the_post_navigation(
                array(
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'test_hierarchy' ) . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'test_hierarchy' ) . '</span> <span class="nav-title">%title</span>',
                )
            );

        endwhile;
        ?>

    </main>

<?php
get_footer();
?>