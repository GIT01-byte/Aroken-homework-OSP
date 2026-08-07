<!-- Footer Section Begin -->
<section class="footer-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="footer-option">
                    <div class="fo-logo">
                        <a href="/">
                            <img
                                src="<?php echo get_field('header_logo', 'option')['url'] ?>"
                                alt="<?php echo get_field('header_logo', 'option')['alt'] ?>">
                        </a>
                    </div>
                    <ul>
                        <?php
                        if (have_rows('footer_inform_repeater', 'options')):
                            while (have_rows('footer_inform_repeater', 'options')) : the_row(); ?>
                                <?php if (!get_sub_field('is_second_text_link', 'options')) { ?>
                                    <li>
                                        <?php the_sub_field('first_text', 'options'); ?><?php the_sub_field('second_text', 'options'); ?>
                                    </li>
                                <?php } else { ?>

                                    <li>
                                        <?php the_sub_field('first_text', 'options'); ?>
                                        <a href="<?php echo get_sub_field('link', 'options')['url']; ?>">
                                            <?php echo get_sub_field('link', 'options')['title']; ?>
                                        </a>

                                    </li>

                                <?php } ?>
                        <?php
                            endwhile;
                        else :
                            echo "Ошибка. Поля не найдены";
                        endif;
                        ?>

                    </ul>
                    <div class="fo-social">
                        <?php
                        if (have_rows('footer_social_links_repeater', 'options')):
                            while (have_rows('footer_social_links_repeater', 'options')) : the_row(); ?>

                                <a
                                    href="<?php the_sub_field('link', 'options'); ?>">
                                    <i class="fa fa-<?php the_sub_field('social', 'options'); ?>"></i>
                                </a>

                        <?php
                            endwhile;
                        else :
                            echo "Ошибка. Поля не найдены";
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget fw-links">
                    <h5><?php echo get_field("footer_menu_title", 'options') ?></h5>
                    <?php wp_nav_menu(array(
                        'container'       => '',
                        'depth'           => 0,
                        'theme_location'  => 'footer',
                    )); ?>
                    <!-- <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Model</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Serivius</a></li>
                    </ul> -->
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <!-- <h5>Join The Newsletter</h5>
                    <p>Get E-mail updates about our latest shop and special offers.</p>
                    <form
                        action="#"
                        class="news-form">
                        <input
                            type="text"
                            placeholder="Enter your mail">
                        <button type="submit">Subscribe</button>
                    </form> -->
                    <?php echo do_shortcode('[contact-form-7 id="9c85498" title="Contact form 1" html_class="news-form"]') ?>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h5><?php echo get_field("footer_gallery_title", 'options') ?></h5>
                    <div class="insta-pic">
                        <?php

                        $images = get_field('footer_gallery', 'options');

                        if ($images): ?>
                            <?php foreach ($images as $image): ?>
                                <img
                                    src="<?php echo esc_url($image['sizes']['thumbnail']); ?>"
                                    alt="<?php echo esc_attr($image['alt']); ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-text">
            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->

                <?php
                $acf_copyright = get_field("footer_copyright", 'options');
                echo do_shortcode($acf_copyright);
                ?>
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </p>
        </div>
    </div>
</section>
<!-- Footer Section End -->


<?php wp_footer(); ?>

</body>

</html>