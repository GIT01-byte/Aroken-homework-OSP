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

/**
 * Регистрация страниц настроек темы через хук acf/init.
 */
add_action('acf/init', 'pestkit_acf_op_init');
function pestkit_acf_op_init()
{

    // Проверяем, активна ли функция в плагине ACF
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

// Шорткод формы для новостной рассылки
add_shortcode('newsletter-form', 'newsletter_form_shortcode');
function newsletter_form_shortcode()
{
    require(get_template_directory() . '/shortcodes/newsletter-form.php');
}

// Шорткод формы для слайдера отзывов
add_shortcode('testinomial-slider', 'testinomial_slider_shortcode');
function testinomial_slider_shortcode()
{
    require(get_template_directory() . '/shortcodes/testinomial-slider.php');
}

// Для отключения авто. тега p плагина Contact Fom 7
add_filter('wpcf7_autop_or_not', '__return_false');

// Шорткод текущего года
add_shortcode('current-year', 'current_year_shortcode');
function current_year_shortcode()
{
    return date("Y");
}

// Фильтр acf поля 'footer_about_copyright' для применения на нем шорткодов
add_filter('acf/format_value/name=footer_about_copyright', 'apply_shortcodes_to_copyright', 10, 3);
function apply_shortcodes_to_copyright($value, $post_id, $field)
{
    if (! empty($value)) {
        return do_shortcode($value);
    }
    return $value;
}

/**
 * Перенаправление статической страницы 404 на шаблон темы 404.php
 */
add_action('template_redirect', 'pestkit_redirect_to_404_template');
function pestkit_redirect_to_404_template()
{
    // Проверяем, находится ли пользователь на странице с ярлыком '404-error'
    if (is_page('404-error')) {
        global $wp_query;
        $wp_query->set_404(); // Принудительно выставляем статус 404
        status_header(404);
        include(get_query_template('404')); // Подгружаем наш файл 404.php
        exit;
    }
}

/**
 * Автоматическая подсветка пункта 404 и его родителя в меню
 */
add_filter('nav_menu_css_class', 'pestkit_fix_404_menu_classes', 10, 2);
function pestkit_fix_404_menu_classes($classes, $item)
{
    // Проверяем, что сейчас открыта страница с ошибкой 404
    if (is_404()) {

        // 1. Ищем саму ссылку на страницу ошибки
        if (strpos($item->url, '404-error') !== false) {
            $classes[] = 'current-menu-item';
            $classes[] = 'current_page_item';
        }

        // 2. Ищем родительский элемент (у выпадающих списков WordPress добавляет класс menu-item-has-children)
        if (in_array('menu-item-has-children', $item->classes)) {
            $classes[] = 'current-menu-ancestor';
            $classes[] = 'current-menu-parent';
            $classes[] = 'current_page_ancestor';
        }
    }
    return $classes;
}

/**
 * AJAX Handler for processing custom feedback with placeholder text fallback
 */
add_action('wp_ajax_submit_custom_feedback_ajax', 'pestkit_handle_custom_feedback_ajax');
add_action('wp_ajax_nopriv_submit_custom_feedback_ajax', 'pestkit_handle_custom_feedback_ajax');

function pestkit_handle_custom_feedback_ajax()
{
    if (! isset($_POST['feedback_nonce']) || ! wp_verify_nonce($_POST['feedback_nonce'], 'feedback_nonce_action')) {
        wp_send_json_error(array('message' => 'Security check failed. Please refresh the page.'));
        wp_die();
    }

    if (! empty($_POST['honeypot_field'])) {
        wp_send_json_success();
        wp_die();
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $author  = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
    $email   = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $comment = isset($_POST['comment']) ? sanitize_textarea_field($_POST['comment']) : '';
    $rating  = isset($_POST['rating']) ? intval($_POST['rating']) : 5;

    // FIX: Removed empty($comment) condition, text field is no longer strictly required
    if (empty($author) || empty($email) || !$post_id) {
        wp_send_json_error(array('message' => 'Please fill in your Name and Email fields.'));
        wp_die();
    }

    // FALLBACK PLACEHOLDER TEXT: If user left textarea empty, inject a nice default message
    if (trim($comment) === '') {
        $comment = "The user chose to leave a high rating without a text review.";
    }

    // Limit check by Email
    $existing_reviews = get_comments(array(
        'author_email' => $email,
        'post_id'      => $post_id,
        'status'       => 'all',
        'count'        => true
    ));

    if ($existing_reviews > 0) {
        wp_send_json_error(array('message' => 'A feedback from this email address has already been submitted for this page!'));
        wp_die();
    }

    $comment_data = array(
        'comment_post_ID'      => $post_id,
        'comment_author'       => $author,
        'comment_author_email' => $email,
        'comment_content'      => $comment, // Saves either real text or our placeholder text
        'comment_type'         => 'comment',
        'comment_approved'     => 0,
    );

    $comment_id = wp_new_comment($comment_data);

    if ($comment_id) {
        update_comment_meta($comment_id, 'wpdiscuz_rating', $rating);
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Failed to save feedback. Please try again later.'));
    }

    wp_die();
}
