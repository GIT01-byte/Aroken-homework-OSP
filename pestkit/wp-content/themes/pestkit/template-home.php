<?php
// Template name:  ШАБЛОН страницы Home
get_header();
?>
<!-- Carousel Start -->
<div class="container-fluid carousel px-0 mb-5 pb-5">
    <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <?php
            if (have_rows('hero_slider')):
                $item_index = 0;
                while (have_rows('hero_slider')) : the_row();
            ?>

                    <li
                        data-bs-target="#carouselId"
                        data-bs-slide-to="<?php echo $item_index ?>"
                        <?php if ($item_index === 0) echo ' class="active" aria-current="true"' ?>
                        aria-label="<?php echo get_sub_field("bg_image")["alt"] ?>">
                    </li>

                    <?php $item_index++ ?>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
        </ol>
        <div class="carousel-inner" role="listbox">
            <?php
            if (have_rows('hero_slider')):
                $item_index = 0;
                while (have_rows('hero_slider')) : the_row();
            ?>

                    <div class="carousel-item <?php if ($item_index === 0) echo "active" ?>">
                        <img
                            src="<?php echo get_sub_field("bg_image")["url"] ?>"
                            class="img-fluid w-100"
                            alt="<?php echo get_sub_field("bg_image")["alt"] ?>">
                        <div class="carousel-caption">
                            <div class="container carousel-content">
                                <h4 class="text-white mb-4 animated slideInDown">
                                    <?php the_sub_field("subtitle") ?>
                                </h4>
                                <h1 class="text-white display-1 mb-4 animated slideInDown">
                                    <?php the_sub_field("title") ?>
                                </h1>
                                <a
                                    href="<?php echo get_sub_field("link")['url'] ?>"
                                    class="me-2">
                                    <button
                                        type="button"
                                        class="px-5 py-3 btn btn-primary border-2 rounded-pill animated slideInDown">
                                        <?php echo get_sub_field("link")['title'] ?>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php $item_index++ ?>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
        </div>
        <button class="carousel-control-prev btn btn-primary border border-2 border-start-0 border-primary" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><?php _e('Previous', 'PestKit'); ?></span>
        </button>
        <button class="carousel-control-next btn btn-primary border border-2 border-end-0 border-primary" type="button" data-bs-target="#carouselId" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><?php _e('Next', 'PestKit'); ?></span>
        </button>
    </div>
</div>
<!-- Carousel End -->


<!-- Get In Touch Start -->
<div class="container-fluid py-5 wow fadeInUp" data-wow-delay=".3s">
    <div class="container py-5">
        <div class="bg-light px-4 py-5 rounded">
            <div class="text-center">
                <h1 class="display-5 mb-5">
                    <?php the_field("find-pest_title") ?>
                </h1>
            </div>
            <?php echo do_shortcode('[contact-form-7 id="4e8dd28" title="Find Pest" html_class="text-center mb-4 ds-block-form"]') ?>
        </div>
    </div>
</div>
<!-- Get In Touch End -->


<!-- About Start -->
<?php do_shortcode("[about-widget]") ?>
<!-- About End -->


<!-- Services Start -->
<?php do_shortcode("[service-capabilities]") ?>
<!-- Services End -->


<!-- Project Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">Our Project</h5>
            <h1 class="display-5">Our recently completed projects</h1>
        </div>
        <div class="row g-5">
            <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".3s">
                <div class="project-item">
                    <div class="project-left bg-dark"></div>
                    <div class="project-right bg-dark"></div>
                    <img src="<?php echo get_template_directory_uri() ?>/img/project-1.jpg" class="img-fluid h-100" alt="img">
                    <a href="" class="fs-4 fw-bold text-center">Whole Home Sanitizing</a>
                </div>
            </div>
            <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".5s">
                <div class="project-item">
                    <div class="project-left bg-dark"></div>
                    <div class="project-right bg-dark"></div>
                    <img src="<?php echo get_template_directory_uri() ?>/img/project-2.jpg" class="img-fluid h-100" alt="img">
                    <a href="" class="fs-4 fw-bold text-center">Education center Cleaning</a>
                </div>
            </div>
            <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".7s">
                <div class="project-item">
                    <div class="project-left bg-dark"></div>
                    <div class="project-right bg-dark"></div>
                    <img src="<?php echo get_template_directory_uri() ?>/img/project-3.jpg" class="img-fluid h-100" alt="img">
                    <a href="" class="fs-4 fw-bold text-center">Warehouse Cleaning</a>
                </div>
            </div>
            <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".3s">
                <div class="project-item">
                    <div class="project-left bg-dark"></div>
                    <div class="project-right bg-dark"></div>
                    <img src="<?php echo get_template_directory_uri() ?>/img/project-4.jpg" class="img-fluid h-100" alt="img">
                    <a href="" class="fs-4 fw-bold text-center">Hospital Cleaning</a>
                </div>
            </div>
            <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".5s">
                <div class="project-item">
                    <div class="project-left bg-dark"></div>
                    <div class="project-right bg-dark"></div>
                    <img src="<?php echo get_template_directory_uri() ?>/img/project-5.jpg" class="img-fluid h-100" alt="img">
                    <a href="" class="fs-4 fw-bold text-center">Factory Cleaning</a>
                </div>
            </div>
            <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".7s">
                <div class="project-item">
                    <div class="project-left bg-dark"></div>
                    <div class="project-right bg-dark"></div>
                    <img src="<?php echo get_template_directory_uri() ?>/img/project-6.jpg" class="img-fluid h-100" alt="img">
                    <a href="" class="fs-4 fw-bold text-center">Furniture Sanitizing</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Project End -->


<!-- Blog Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">Our Blog</h5>
            <h1 class="display-5">Latest Blog & News</h1>
        </div>
        <div class="owl-carousel blog-carousel wow fadeInUp" data-wow-delay=".5s">
            <div class="blog-item">
                <img src="<?php echo get_template_directory_uri() ?>/img/blog-1.jpg" class="img-fluid w-100 rounded-top" alt="">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>10 Feb, 2023</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>How To Build A Cleaning Plan</h4>
                        <p>Lorem ipsum dolor sit amet consectur adip sed eiusmod tempor.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto btn-primary border-0"><i class="fa fa-comments me-2"></i>23 Comments</a>
                    </div>
                </div>
            </div>
            <div class="blog-item">
                <img src="<?php echo get_template_directory_uri() ?>/img/blog-3.jpg" class="img-fluid w-100 rounded-top" alt="">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>10 Feb, 2023</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>How To Build A Cleaning Plan</h4>
                        <p>Lorem ipsum dolor sit amet consectur adip sed eiusmod tempor.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto text-dark"><i class="fa fa-comments me-2"></i>23 Comments</a>
                    </div>
                </div>
            </div>
            <div class="blog-item">
                <img src="<?php echo get_template_directory_uri() ?>/img/blog-2.jpg" class="img-fluid w-100 rounded-top" alt="">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>10 Feb, 2023</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>How To Build A Cleaning Plan</h4>
                        <p>Lorem ipsum dolor sit amet consectur adip sed eiusmod tempor.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto text-dark"><i class="fa fa-comments me-2"></i>23 Comments</a>
                    </div>
                </div>
            </div>
            <div class="blog-item">
                <img src="<?php echo get_template_directory_uri() ?>/img/blog-1.jpg" class="img-fluid w-100 rounded-top" alt="">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>10 Feb, 2023</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>How To Build A Cleaning Plan</h4>
                        <p>Lorem ipsum dolor sit amet consectur adip sed eiusmod tempor.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto text-dark"><i class="fa fa-comments me-2"></i>23 Comments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Blog End -->


<!-- Call To Action Start -->
<?php do_shortcode("[newsletter-form]") ?>
<!-- Call To Action End -->


<!-- pricing Start -->
<?php do_shortcode("[pricing-plans]") ?>
<!-- Pricing End -->


<!-- Team Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                <?php the_field("our_team_subtitle") ?>
            </h5>
            <h1 class="display-5 w-50 mx-auto">
                <?php the_field("our_team_title") ?>
            </h1>
        </div>
        <div class="row g-5">
            <?php
            if (get_field("our_team_count_employees")) {
                $postsAmount = get_field("our_team_count_employees");
            } else {
                $postsAmount = 4;
            }
            $args = array(
                'posts_per_page' => $postsAmount,
                'post_type' => 'our-team',
                'orderby' => 'date',
                'order' => 'ASC'
            );
            $query = new WP_Query($args);

            // Цикл
            if ($query->have_posts()) {
                $delay = 0.3;
                while ($query->have_posts()) {
                    $query->the_post(); ?>

                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="<?php echo $delay ?>s">
                        <div class="rounded team-item">
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="img-fluid w-100 rounded-top border border-bottom-0">
                            <div class="team-content bg-primary text-dark text-center py-3">
                                <span class="fs-4 fw-bold">
                                    <?php the_title() ?>
                                </span>
                                <p class="text-muted mb-0">
                                    <?php the_field("our_team_job") ?>
                                </p>
                            </div>
                            <div class="team-icon d-flex flex-column ">
                                <?php
                                if (have_rows('our_team_socials_repeater')):
                                    while (have_rows('our_team_socials_repeater')) : the_row(); ?>

                                        <a
                                            href="<?php the_sub_field('link'); ?>"
                                            class="btn btn-primary border-0 mb-2">
                                            <i class="fab fa-<?php the_sub_field('social'); ?>"></i>
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

                    <?php $delay += 0.2 ?>

            <?php }
            } else {
                echo "Ошибка. Постов не найдено";
            }
            wp_reset_postdata(); ?>
        </div>
    </div>
</div>
<!-- Team End -->


<!-- Testiminial Start -->
<div class="container-fluid testimonial py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">Testimonial</h5>
            <h1 class="display-5 w-50 mx-auto">What Clients Say About Our Services</h1>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay=".5s">
            <div class="testimonial-item">
                <div class="testimonial-content rounded mb-4 p-4">
                    <p class="fs-5 m-0">Lorem ipsum dolor sit amet elit, sed do eiusmod tempor ut labore et dolore magna aliqua. Ut enim ad minim veniam quis tempor.</p>
                </div>
                <div class="d-flex align-items-center  mb-4" style="padding: 0 0 0 25px;">
                    <div class="position-relative">
                        <img src="<?php echo get_template_directory_uri() ?>/img/testimonial-1.jpg" class="img-fluid rounded-circle py-2" alt="">
                        <div class="position-absolute" style="top: 33px; left: -25px;">
                            <i class="fa fa-quote-left rounded-pill bg-primary text-dark p-3"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0">Client Name</h4>
                        <p class="mb-1">Profession</p>
                        <div class="d-flex">
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-item">
                <div class="testimonial-content rounded mb-4 p-4">
                    <p class="fs-5 m-0">Lorem ipsum dolor sit amet elit, sed do eiusmod tempor ut labore et dolore magna aliqua. Ut enim ad minim veniam quis tempor.</p>
                </div>
                <div class="d-flex align-items-center  mb-4" style="padding: 0 0 0 25px;">
                    <div class="position-relative">
                        <img src="<?php echo get_template_directory_uri() ?>/img/testimonial-2.jpg" class="img-fluid rounded-circle py-2" alt="">
                        <div class="position-absolute" style="top: 33px; left: -25px;">
                            <i class="fa fa-quote-left rounded-pill bg-primary text-dark p-3"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0">Client Name</h4>
                        <p class="mb-1">Profession</p>
                        <div class="d-flex">
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-item">
                <div class="testimonial-content rounded mb-4 p-4">
                    <p class="fs-5 m-0">Lorem ipsum dolor sit amet elit, sed do eiusmod tempor ut labore et dolore magna aliqua. Ut enim ad minim veniam quis tempor.</p>
                </div>
                <div class="d-flex align-items-center  mb-4" style="padding: 0 0 0 25px;">
                    <div class="position-relative">
                        <img src="<?php echo get_template_directory_uri() ?>/img/testimonial-3.jpg" class="img-fluid rounded-circle py-2" alt="">
                        <div class="position-absolute" style="top: 33px; left: -25px;">
                            <i class="fa fa-quote-left rounded-pill bg-primary text-dark p-3"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0">Client Name</h4>
                        <p class="mb-1">Profession</p>
                        <div class="d-flex">
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-item">
                <div class="testimonial-content rounded mb-4 p-4">
                    <p class="fs-5 m-0">Lorem ipsum dolor sit amet elit, sed do eiusmod tempor ut labore et dolore magna aliqua. Ut enim ad minim veniam quis tempor.</p>
                </div>
                <div class="d-flex align-items-center  mb-4" style="padding: 0 0 0 25px;">
                    <div class="position-relative">
                        <img src="<?php echo get_template_directory_uri() ?>/img/testimonial-4.jpg" class="img-fluid rounded-circle py-2" alt="">
                        <div class="position-absolute" style="top: 33px; left: -25px;">
                            <i class="fa fa-quote-left rounded-pill bg-primary text-dark p-3"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0">Client Name</h4>
                        <p class="mb-1">Profession</p>
                        <div class="d-flex">
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                            <small class="fas fa-star text-primary me-1"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Testiminial End -->
<?php
get_footer();
