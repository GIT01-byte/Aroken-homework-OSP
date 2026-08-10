<!-- Footer Start -->
<div
    class="container-fluid footer py-5 wow fadeIn"
    data-wow-delay=".3s">
    <div class="container py-5">
        <div class="row g-4 footer-inner">
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h4 class="text-white fw-bold mb-4"><?php echo get_field('footer_about_title', 'option'); ?></h4>
                    <p><?php the_field('footer_about_descr', 'option'); ?></p>
                    <p class="mb-0">
                        <a
                            class=""
                            href="<?php echo get_field('footer_about_copyright', 'option')['url']; ?>">
                            <?php echo get_field('footer_about_copyright', 'option')['title']; ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h4 class="text-white fw-bold mb-4"><?php echo get_field('footer_usefull_title', 'option'); ?></h4>
                    <div class="d-flex flex-column align-items-start">
                        <?php wp_nav_menu(array(
                            'theme_location'  => 'footer_useful',
                            'depth'           => 0,
                            'container'       => false,
                            'menu_class'      => 'footer-menu',
                            'link_before'     => '<i class="fa fa-check me-2"></i>'
                        )); ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h4 class="text-white fw-bold mb-4"><?php echo get_field('footer_services_title', 'option'); ?></h4>
                    <div class="d-flex flex-column align-items-start">
                        <?php wp_nav_menu(array(
                            'theme_location'  => 'footer_services',
                            'depth'           => 0,
                            'container'       => false,
                            'menu_class'      => 'footer-menu',
                            'link_before'     => '<i class="fa fa-check me-2"></i>'
                        )); ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h4 class="text-white fw-bold mb-4"><?php the_field('footer_contact_title', 'option'); ?>
                    </h4>
                    <?php
                    if (have_rows("footer_contact_info_repeater", "options")):
                        while (have_rows("footer_contact_info_repeater", "options")) : the_row(); ?>

                            <a
                                href="<?php the_sub_field('link'); ?>"
                                class="btn btn-link w-100 text-start ps-0 pb-3 border-bottom rounded-0">
                                <i class="fa fa-<?php the_sub_field('icon_name'); ?> me-3"></i>
                                <?php the_sub_field('text'); ?>
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
    </div>
</div>
<!-- Footer End -->

<!-- Copyright Start -->
<div class="container-fluid copyright bg-dark py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <a href="/" class="text-primary mb-0 display-6"><?php echo get_field('logo_text_1', 'option'); ?><span class="text-white"><?php echo get_field('logo_text_2', 'option'); ?></span><i class="fa <?php echo get_field('logo_icon', 'option'); ?> text-primary ms-2"></i></a>
            </div>
            <div class="col-md-4 copyright-btn text-center text-md-start mb-3 mb-md-0 flex-shrink-0">
                <?php
                if (have_rows('copyright_social_links_repeater', 'options')):
                    while (have_rows('copyright_social_links_repeater', 'options')) : the_row(); ?>

                        <a
                            class="btn btn-primary rounded-circle me-3 copyright-icon"
                            href="<?php the_sub_field('link') ?>">
                            <i class="fab fa-<?php the_sub_field('social') ?>"></i>
                        </a>

                <?php
                    endwhile;
                else :
                    echo "Ошибка. Поля не найдены";
                endif;
                ?>
            </div>
            <div class="col-md-4 my-auto text-center text-md-end text-white">
                <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                Designed By <a
                    class="border-bottom"
                    href="https://htmlcodex.com">HTML Codex</a>
            </div>
        </div>
    </div>
</div>
<!-- Copyright End -->


<!-- Back to Top -->
<a
    href="#"
    class="btn btn-primary rounded-circle border-3 back-to-top"><i class="fa fa-arrow-up"></i></a>

<?php wp_footer() ?>

</body>

</html>