<?php
// Template name:  ШАБЛОН страницы-пустышки Pages
get_header();
?>

<!-- Page Header Start -->
<div
    class="container-fluid page-header py-5"
    style="background: linear-gradient(rgba(0, 0, 0, .7), rgba(0, 0, 0, .7)), url(<?php echo get_field("breadcrumbs_bg_image", "options") ?>) 
        center center no-repeat; background-size: cover;">
    <div class="container text-center py-5">
        <h1 class="display-2 text-white mb-4 animated slideInDown">
            <?php the_title() ?>
        </h1>
        <nav aria-label="breadcrumb" typeof="BreadcrumbList" vocab="https://schema.org">
            <ol class="breadcrumb justify-content-center mb-0 animated slideInDown">
                <?php
                if (function_exists('bcn_display')) {
                    bcn_display();
                }
                ?>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->


<!-- Контентная часть страницы-пустышки -->
<div class="container-fluid py-5">
    <div class="container text-center py-5">
        <div class="mx-auto" style="max-width: 600px;">
            <!-- Иконка в тему борьбы с вредителями (используется FontAwesome, которая обычно есть в таких темах) -->
            <i class="fa fa-tools display-1 text-primary mb-4 animate__animated animate__pulse animate__infinite"></i>

            <h2 class="fw-bold text-dark mb-3">Our Pages are Under Construction</h2>

            <p class="text-muted mb-5 fs-5">
                We are currently crafting the best and most affordable plans for your pest control needs.
                Our transparent pricing packages will be available here very soon!
            </p>

            <!-- Кнопка возврата на главную -->
            <a href="/" class="btn btn-primary rounded-pill py-3 px-5">
                Go Home
            </a>
        </div>
    </div>
</div>

<?php
get_footer();
