<?php
/*
Template name: ШАБЛОН страницы Contact
*/
get_header();
?>

<!-- Map Section Begin -->
<div class="map">
    <?php echo get_field("contact_map") ?>
</div>
<!-- Map Section End -->

<!-- Contact Section Begin -->
<section class="contact-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="contact-text">
                    <h4>Contacts Us</h4>
                    <?php
                    if (have_rows('contact_info_repeater')):
                        while (have_rows('contact_info_repeater')) : the_row();
                    ?>

                            <div class="ct-item">
                                <div class="ci-icon">
                                    <span class='<?php echo get_sub_field("ti_icon_name") ?>'></span>
                                </div>
                                <div class="ci-text">
                                    <ul>
                                        <li>
                                            <span><?php echo get_sub_field("title") ?></span>
                                            <?php if (!get_sub_field("is_link")) { ?>
                                                <?php echo get_sub_field("text") ?>
                                            <?php } else { ?>
                                                <a href="<?php echo get_sub_field("link")["url"] ?>">
                                                    <?php echo get_sub_field("link")["title"] ?>
                                                </a>
                                            <?php } ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                    <?php
                        endwhile;
                    else :
                        echo "Ошибка. Поля не найдены";
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-option">
                    <h4>Leave Us A Meggase</h4>
                    <?php echo do_shortcode('[contact-form-7 id="3fcd3ac" title="Contact Us" html_class="comment-form"]') ?>
                    <!-- <form action="#" class="comment-form contact-form">
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="text" placeholder="Name">
                            </div>
                            <div class="col-lg-6">
                                <input type="text" placeholder="Email">
                            </div>
                            <div class="col-lg-12">
                                <textarea placeholder="Messages"></textarea>
                                <button type="submit" class="site-btn">Send Message</button>
                            </div>
                        </div>
                    </form> -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Section End -->
<?php get_footer(); ?>