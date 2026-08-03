<section
    class="callto-section set-bg"
    data-setbg="<?php echo get_template_directory_uri() ?>/img/ctc-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 m-auto">
                <div class="ctc-text">
                    <h2><?php echo get_field("pink_banner_title", 'options') ?></h2>
                    <p><?php echo get_field("pink_banner_description_editor", 'options') ?></p>
                    <a
                        href="<?php echo get_field("pink_banner_btn", 'options')['url'] ?>"
                        class="primary-btn ctc-btn">
                        <?php echo get_field("pink_banner_btn", 'options')['title'] ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>