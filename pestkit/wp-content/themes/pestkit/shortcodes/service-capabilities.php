<div class="container-fluid services py-5">
    <div class="container text-center py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                <?php the_field("services_subtitle", "options") ?>
            </h5>
            <h1 class="display-5">
                <?php the_field("services_title", "options") ?>
            </h1>
        </div>
        <div class="row g-5">
            <?php
            if (have_rows('service_widget_repeater', "options")):
                $delay = 0.3;
                while (have_rows('service_widget_repeater', "options")) : the_row(); ?>

                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="<?php echo $delay ?>s">
                        <div class="bg-light rounded p-5 services-item">
                            <div class="d-flex" style="align-items: center; justify-content: center;">
                                <div class="mb-4 rounded-circle services-inner-icon">
                                    <i class="fa <?php echo get_sub_field("logo_icon") ?> fa-3x text-primary"></i>
                                </div>
                            </div>
                            <h4>
                                <?php echo get_sub_field("title") ?>
                            </h4>
                            <p class="fs-5">
                                <?php echo get_sub_field("descr") ?>
                            </p>
                            <a
                                href='<?php echo get_sub_field("learn_more_link")["url"] ?>'
                                class="btn btn-primary border-0 rounded-pill px-4 py-3">
                                <?php echo get_sub_field("learn_more_link")["title"] ?>
                            </a>
                        </div>
                    </div>

                    <?php $delay += 0.2; ?>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
        </div>
        <a
            href='<?php echo get_field("more_services_link", "options")["url"] ?>'
            class="btn btn-primary border-0 rounded-pill px-4 py-3 mt-4 wow fadeInUp" data-wow-delay=".3s">
            <?php echo get_field("more_services_link", "options")["title"] ?>
        </a>
    </div>
</div>