<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6 col-md-12 wow fadeInUp" data-wow-delay=".3s">
                <div class="about-img">
                    <div class="rotate-left bg-dark"></div>
                    <div class="rotate-right bg-dark"></div>
                    <img src="<?php echo get_field('about-pest_image', 'option')['url']; ?>"
                        class="img-fluid h-100"
                        alt="<?php echo get_field('about-pest_image', 'option')['alt']; ?>">
                    <div class="bg-white experiences">
                        <h1 class="display-3">
                            <?php the_field('about-pest_exp_number', 'option'); ?>
                        </h1>
                        <h6 class="fw-bold">
                            <?php the_field('about-pest_exp_title', 'option'); ?>
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 wow fadeInUp" data-wow-delay=".6s">
                <div class="about-item overflow-hidden">
                    <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                        <?php the_field('about-pest_subtitle', 'option'); ?>
                    </h5>
                    <h1 class="display-5 mb-2">
                        <?php the_field('about-pest_title', 'option'); ?>
                    </h1>
                    <p class="fs-5" style="text-align: justify;">
                        <?php the_field('about-pest_descr', 'option'); ?>
                    </p>
                    <div class="row">
                        <?php
                        if (have_rows('about-pest_services_repeater', 'options')):
                            while (have_rows('about-pest_services_repeater', 'options')) : the_row(); ?>

                                <div class="col-3">
                                    <div class="text-center">
                                        <div class="p-4 bg-dark rounded d-flex" style="align-items: center; justify-content: center;">
                                            <i
                                                class="fas <?php the_sub_field("icon_class") ?> 
                                                fa-4x text-primary">
                                            </i>
                                        </div>
                                        <div class="my-2">
                                            <h5>
                                                <?php the_sub_field("title") ?>
                                            </h5>
                                        </div>
                                    </div>
                                </div>

                        <?php
                            endwhile;
                        else :
                            echo "Ошибка. Поля не найдены";
                        endif;
                        ?>
                    </div>
                    <a href="<?php echo get_field('about-pest_link', 'option')['url']; ?>"
                        class="btn btn-primary border-0 rounded-pill px-4 py-3 mt-5">
                        <?php echo get_field('about-pest_link', 'option')['title']; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>