<?php
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <!-- Spinner Start -->
    <div
        id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div
            class="spinner-grow text-primary"
            role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid topbar-top bg-primary">
        <div class="container">
            <div class="d-flex justify-content-between topbar py-2">
                <div class="d-flex align-items-center flex-shrink-0 topbar-info">
                    <?php
                    if (have_rows("top_bar_contact_info_repeater", "options")):
                        while (have_rows("top_bar_contact_info_repeater", "options")) : the_row();
                            $is_link = get_sub_field("is_link");
                    ?>
                            <?php if (!$is_link) { ?>

                                <span
                                    class="me-4 text-secondary">
                                    <i class="fas fa-<?php the_sub_field("icon_name") ?> me-2 text-dark"></i>
                                    <?php the_sub_field("text") ?>
                                </span>

                            <?php } else { ?>

                                <a
                                    href="<?php the_sub_field("link") ?>"
                                    class="me-4 text-secondary">
                                    <i class="fas fa-<?php the_sub_field("icon_name") ?> me-2 text-dark"></i>
                                    <?php the_sub_field("text") ?>
                                </a>

                            <?php } ?>

                    <?php
                        endwhile;
                    else :
                        echo "Ошибка. Поля не найдены";
                    endif;
                    ?>
                </div>
                <div class="text-end pe-4 me-4 border-end border-dark search-btn">
                    <div class="search-form">
                        <form
                            method="post"
                            action="index.html">
                            <div class="form-group">
                                <div class="d-flex">
                                    <input
                                        type="search"
                                        class="form-control border-0 rounded-pill"
                                        name="search-input"
                                        value=""
                                        placeholder="Search Here"
                                        required="" />
                                    <button
                                        type="submit"
                                        value="Search Now!"
                                        class="btn"><i class="fa fa-search text-dark"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center topbar-icon">
                    <?php
                    if (have_rows("top_bar_social_links_repeater", "options")):
                        while (have_rows("top_bar_social_links_repeater", "options")) : the_row(); ?>

                            <a
                                href="<?php echo get_sub_field("link") ?>"
                                class="me-4">
                                <i class="fab fa-<?php echo get_sub_field("social") ?> text-dark"></i>
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
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid bg-dark">
        <div class="container">
            <nav class="navbar navbar-dark navbar-expand-lg py-lg-0">
                <a
                    href="/"
                    class="navbar-brand">
                    <h1 class="text-primary mb-0 display-5"><?php echo get_field('logo_text_1', 'option'); ?><span class="text-white"><?php echo get_field('logo_text_2', 'option'); ?></span><i class="fa <?php echo get_field('logo_icon', 'option'); ?> text-primary ms-2"></i></h1>
                </a>
                <button
                    class="navbar-toggler bg-primary"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-dark"></span>
                </button>
                <div
                    class="collapse navbar-collapse me-n3"
                    id="navbarCollapse">
                    <?php wp_nav_menu(array(
                        'theme_location'  => 'header',
                        'depth'           => 2,
                        'container'       => false,
                        'menu_class'      => 'navbar-nav ms-auto',
                    ));
                    ?>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->