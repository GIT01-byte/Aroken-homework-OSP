<?php

get_header();
?>
<!-- Page Header Start -->
<div class="container-fluid page-header py-5">
    <div class="container text-center py-5">
        <h1 class="display-2 text-white mb-4 animated slideInDown">404 Error</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0 animated slideInDown">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item text-white" aria-current="page">404 Error</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->


<!-- 404 Start -->
<div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.3s">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <i class='<?php the_field("404_icon_class", "options") ?> display-1 text-primary'></i>
                <h1 class="display-1">
                    404
                </h1>
                <h1 class="mb-4">
                    <?php the_field("404_title", "options") ?>
                </h1>
                <p class="mb-4">
                    <?php the_field("404_descr", "options") ?>
                </p>
                <a href="<?php echo get_field("404_link_to_home", "options")["url"] ?>" class="btn btn-primary rounded-pill py-3 px-5">
                    <?php echo get_field("404_link_to_home", "options")["title"] ?>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- 404 End -->
<?php
get_footer();
