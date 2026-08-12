<?php
if (! defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function pestkit_setup()
{
    /*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on PestKit, use a find and replace
		* to change 'pestkit' to the name of your theme in all the template files.
		*/
    load_theme_textdomain('pestkit', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
    add_theme_support('title-tag');

    /*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus([
        'header' => 'ШАПКА',
        'footer_useful' => 'Footer Useful Links',
        'footer_services' => 'Footer Services Links',
    ]);

    /*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');
}
add_action('after_setup_theme', 'pestkit_setup');

/**
 * Enqueue scripts and styles.
 */
function pestkit_scripts()
{
    wp_enqueue_style('pestkit-style', get_stylesheet_uri(), array(), _S_VERSION);

    wp_enqueue_style('nunito-font', "https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap", array(), _S_VERSION);
    wp_enqueue_style('open-sans-font', "https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&display=swap", array(), _S_VERSION);
    wp_enqueue_style('font-awesome', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css", array(), _S_VERSION);
    wp_enqueue_style('bootstrap-icons', "https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css", array(), _S_VERSION);
    wp_enqueue_style('animate', get_template_directory_uri() . "/lib/animate/animate.min.css", array(), _S_VERSION);
    wp_enqueue_style('owl-carousel', get_template_directory_uri() . "/lib/owlcarousel/assets/owl.carousel.min.css", array(), _S_VERSION);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . "/css/bootstrap.min.css", array(), _S_VERSION);
    wp_enqueue_style('pestkit-style-main', get_template_directory_uri() . "/css/style.css", array(), _S_VERSION);

    wp_enqueue_script('bootstrap', "https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js", array(), _S_VERSION, true);
    wp_enqueue_script('wow', get_template_directory_uri() . '/lib/wow/wow.min.js', array(), _S_VERSION, true);
    wp_enqueue_script('easing', get_template_directory_uri() . '/lib/easing/easing.min.js', array("jquery"), _S_VERSION, true);
    wp_enqueue_script('waypoints', get_template_directory_uri() . '/lib/waypoints/waypoints.min.js', array("jquery"), _S_VERSION, true);
    wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/lib/owlcarousel/owl.carousel.min.js', array("jquery"), _S_VERSION, true);
    wp_enqueue_script('pestkit-script-main', get_template_directory_uri() . '/js/main.js', array("wow", "owl-carousel"), _S_VERSION, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'pestkit_scripts');

// Для меню настройки шапки и подвала сайта
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title'    => 'Основные настройки',
        'menu_title'    => 'Настройки темы',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

    acf_add_options_sub_page(array(
        'page_title'    => 'Настройки шапки',
        'menu_title'    => 'Header',
        'parent_slug'   => 'theme-general-settings',
    ));

    acf_add_options_sub_page(array(
        'page_title'    => 'Настройки подвала',
        'menu_title'    => 'Footer',
        'parent_slug'   => 'theme-general-settings',
    ));
}

// Добавляем класс 'nav-item' к тегу <li> ТОЛЬКО ДЛЯ HEADER
add_filter('nav_menu_css_class', 'bootstrap5_li_classes', 10, 4);
function bootstrap5_li_classes($classes, $item, $args, $depth)
{
    if (is_object($args) && isset($args->theme_location) && $args->theme_location === 'header') {
        $classes[] = 'nav-item';

        if (in_array('menu-item-has-children', $item->classes)) {
            $classes[] = 'dropdown';
        }
    }
    return $classes;
}


// Добавляем классы к тегам <a> в зависимости от области меню
add_filter('nav_menu_link_attributes', 'bootstrap5_a_attributes', 10, 4);
function bootstrap5_a_attributes($atts, $item, $args, $depth)
{
    // Проверяем, что это объект меню WordPress
    if (is_object($args) && isset($args->theme_location)) {

        // 1. Условие только для меню в шапке (header)
        if ($args->theme_location === 'header') {
            $classes = isset($atts['class']) ? $atts['class'] . ' nav-link' : 'nav-link';

            if (in_array('menu-item-has-children', $item->classes)) {
                $classes .= ' dropdown-toggle';
                $atts['data-bs-toggle'] = 'dropdown';
                $atts['aria-expanded'] = 'false';
            }

            if ($depth > 0) {
                $classes = 'dropdown-item';
            }

            $atts['class'] = $classes;
        }

        // 2. Условие только для полезных ссылок в футере (footer_useful)
        if ($args->theme_location === 'footer_useful' || $args->theme_location === 'footer_services') {
            // Безопасно дописываем нужные классы к уже имеющимся
            $atts['class'] = isset($atts['class']) ? $atts['class'] . ' btn btn-link ps-0' : 'btn btn-link ps-0';
        }
    }
    return $atts;
}


// Добавляем классы к выпадающему списку <ul> ТОЛЬКО ДЛЯ HEADER
add_filter('nav_menu_submenu_css_class', 'bootstrap5_ul_subclasses', 10, 3);
function bootstrap5_ul_subclasses($classes, $args, $depth)
{
    // Строгая проверка: работаем ТОЛЬКО если это меню 'header'
    if (is_object($args) && isset($args->theme_location) && $args->theme_location === 'header') {
        array_push($classes, "dropdown-menu", "bg-primary", "m-0");
    }
    return $classes;
}

// Шорткод сервисных возможностей 
add_shortcode('service-capabilities', 'service_capabilities_shortcode');
function service_capabilities_shortcode()
{
    require(get_template_directory() . '/shortcodes/service-capabilities.php');
}

// Шорткод тарифных планов
add_shortcode('pricing-plans', 'pricing_plans_shortcode');
function pricing_plans_shortcode()
{
    require(get_template_directory() . '/shortcodes/pricing-plans.php');
}

// Шорткод виджета "О компании"
add_shortcode('about-widget', 'about_widget_shortcode');
function about_widget_shortcode()
{
    require(get_template_directory() . '/shortcodes/about-widget.php');
}
