<div
    class="container-fluid py-5 call-to-action wow fadeInUp"
    data-wow-delay=".3s"
    style="margin: 6rem 0; background: linear-gradient(rgba(0, 0, 0, .7), rgba(0, 0, 0, .7)), url(<?php echo get_field("news-form_bg_image", "options") ?>) center center no-repeat; background-size: cover;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <img
                    src='<?php echo get_field("news-form_service_image", "options")["url"] ?>'
                    class="img-fluid w-100 rounded-circle p-5"
                    alt='<?php echo get_field("news-form_service_image", "options")["alt"] ?>'>
            </div>
            <div class="col-lg-6 my-auto">
                <div class="text-start mt-4">
                    <h1 class="pb-4 text-white">
                        <?php echo get_field("news-form_title", "options") ?>
                    </h1>
                </div>
                <?php echo do_shortcode('[contact-form-7 id="4d26106" title="Newsletter Form" html_class="pos-abs-form"]') ?>
            </div>
        </div>
    </div>
</div>