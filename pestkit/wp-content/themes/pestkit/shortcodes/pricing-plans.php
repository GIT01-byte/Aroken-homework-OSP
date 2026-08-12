<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">
                <?php the_field("pricing_subtitle", "options") ?>
            </h5>
            <h1 class="display-5 w-50 mx-auto">
                <?php the_field("pricing_title", "options") ?>
            </h1>
        </div>
        <div class="row g-5">
            <?php
            if (have_rows('pricing_tariffs_repeater', "options")):
                $delay = 0.3;
                while (have_rows('pricing_tariffs_repeater', "options")) : the_row();
                    $is_accent = get_sub_field("is_accent") ?>

                    <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="<?php echo $delay ?>s">
                        <div class="rounded bg-light pricing-item">
                            <div
                                class="bg-<?php echo $is_accent ? 'dark' : 'primary' ?> 
                                py-3 px-5 text-center rounded-top border-bottom 
                                border-<?php echo $is_accent ? 'primary' : 'dark' ?>">
                                <h2
                                    class="m-0 <?php echo $is_accent ? 'text-primary' : '' ?>">
                                    <?php the_sub_field("title") ?>
                                </h2>
                            </div>
                            <div
                                class="px-4 py-5 text-center 
                                bg-<?php echo $is_accent ? 'dark' : 'primary' ?>
                                pricing-label <?php echo $is_accent ? 'pricing-featured' : '' ?> mb-2">
                                <h1
                                    class="mb-0 
                                    <?php echo $is_accent ? 'text-primary' : '' ?>">
                                    <?php the_sub_field("currency") ?>
                                    <?php the_sub_field("price") ?>
                                    <span class="text-secondary fs-5 fw-normal">
                                        /
                                        <?php the_sub_field("payment_period") ?>
                                    </span>
                                </h1>
                                <p
                                    class="mb-0 
                                    <?php echo $is_accent ? 'text-white' : '' ?>">
                                    <?php the_sub_field("subtitle") ?>
                                </p>
                            </div>
                            <div class="p-4 text-center fs-5">
                                <?php
                                if (have_rows('features_list')):
                                    while (have_rows('features_list')) : the_row();
                                        $is_available = get_sub_field("is_available") ?>

                                        <p>
                                            <i class="fa fa-<?php echo $is_available ? 'check' : 'times' ?>
                                            text-<?php echo $is_available ? 'success' : 'danger' ?> me-2"></i>
                                            <?php the_sub_field("descr") ?>
                                        </p>

                                <?php
                                    endwhile;
                                else :
                                    echo "Ошибка. Поля не найдены";
                                endif;
                                ?>
                                <a
                                    href='<?php echo get_sub_field("link")["url"] ?>'
                                    class="btn btn-<?php echo $is_accent ? 'dark' : 'primary' ?>
                                    <?php echo $is_accent ? 'text-primary' : '' ?>
                                    border-0 rounded-pill px-4 py-3 mt-3">
                                    <?php echo get_sub_field("link")["title"] ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php $delay += 0.2; ?>

            <?php
                endwhile;
            else :
                echo "Ошибка. Поля не найдены";
            endif;
            ?>
            <!-- <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".3s">
                <div class="rounded bg-light pricing-item">
                    <div class="bg-primary py-3 px-5 text-center rounded-top border-bottom border-dark">
                        <h2 class="m-0">Basic</h2>
                    </div>
                    <div class="px-4 py-5 text-center bg-primary pricing-label mb-2">
                        <h1 class="mb-0">$60<span class="text-secondary fs-5 fw-normal">/mo</span></h1>
                        <p class="mb-0">Basic Pest Control</p>
                    </div>
                    <div class="p-4 text-center fs-5">
                        <p><i class="fa fa-check text-success me-2"></i>Household pests Control</p>
                        <p><i class="fa fa-check text-success me-2"></i>Rodent Control</p>
                        <p><i class="fa fa-check text-success me-2"></i>Re-Service at No-Charge</p>
                        <p><i class="fa fa-times text-danger me-2"></i>Termite Control</p>
                        <p><i class="fa fa-times text-danger me-2"></i>Mosquito Reduction</p>
                        <button type="button" class="btn btn-primary border-0 rounded-pill px-4 py-3 mt-3">Get Started</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".5s">
                <div class="rounded bg-light pricing-item">
                    <div class="bg-dark py-3 px-5 text-center rounded-top border-bottom border-primary">
                        <h2 class="m-0 text-primary">Standerd</h2>
                    </div>
                    <div class="px-4 py-5 text-center bg-dark pricing-label pricing-featured mb-2">
                        <h1 class="mb-0 text-primary">$80<span class="fs-5 fw-normal">/mo</span></h1>
                        <p class="mb-0 text-white">Standard Pest Control</p>
                    </div>
                    <div class="p-4 text-center fs-5">
                        <p><i class="fa fa-check text-success me-2"></i>Household pests Control</p>
                        <p><i class="fa fa-check text-success me-2"></i>Rodent Control</p>
                        <p><i class="fa fa-check text-success me-2"></i>Re-Service at No-Charge</p>
                        <p><i class="fa fa-check text-success me-2"></i>Termite Control</p>
                        <p><i class="fa fa-times text-danger me-2"></i>Mosquito Reduction</p>
                        <button type="button" class="btn btn-dark border-0 text-primary rounded-pill px-4 py-3 mt-3">Get Started</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".7s">
                <div class="rounded bg-light pricing-item">
                    <div class="bg-primary py-3 px-5 text-center rounded-top border-bottom border-dark">
                        <h2 class="m-0">Premium</h2>
                    </div>
                    <div class="px-4 py-5 text-center bg-primary pricing-label mb-2">
                        <h1 class="mb-0">$120<span class="text-secondary fs-5 fw-normal">/mo</span></h1>
                        <p class="mb-0">Premium Pest Control</p>
                    </div>
                    <div class="p-4 text-center fs-5">
                        <p><i class="fa fa-check text-success me-2"></i>Household pests Control</p>
                        <p><i class="fa fa-check text-success me-2"></i>Rodent Control</p>
                        <p><i class="fa fa-check text-success me-2"></i>Re-Service at No-Charge</p>
                        <p><i class="fa fa-check text-success me-2"></i>Termite Control</p>
                        <p><i class="fa fa-check text-success me-2"></i>Mosquito Reduction</p>
                        <button type="button" class="btn btn-primary border-0 rounded-pill px-4 py-3 mt-3">Get Started</button>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>