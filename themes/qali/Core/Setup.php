<?php

namespace Core;

class Setup
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('manage_posts_custom_column', [$this, 'add_thumbnail_column_content'], 10, 2);
        add_filter('manage_posts_columns', [$this, 'add_thumbnail_column']);
        add_action('admin_enqueue_scripts', [$this, 'admin_inline_css']);
        add_action('init', [$this, 'my_htaccess_contents']);

        return;
    }

    public function setup()
    {


        load_theme_textdomain(LANG_STRING, URL_ROOT_DIR . '/lang');
        add_theme_support('post-thumbnails');

        add_filter('allowed_http_origin', '__return_true');
        add_filter('the_content', 'wpautop');


        remove_action('wp_head', 'ls_meta_generator', 9);

        add_filter('body_class', [$this, 'init_body_classes']);
        add_action('template_redirect', [$this, 'logout_redirect']);
        add_action('init', [$this, 'block_users_init']);
    }

    function logout_redirect()
    {
        $request     = $_SERVER['REQUEST_URI'];
        $request_uri = trim($request, "/");
        if (strpos($request_uri, 'logout') !== false) {
            wp_logout();
            wp_redirect(home_url());
            exit();
        }
    }

    function admin_inline_css()
    {
        $style = "<style type='text/css'>
		.column-thumbnail {width: 88px;}
		</style>";
        echo $style;
    }

    public function add_thumbnail_column($columns)
    {

        global $pagenow;

        if ($pagenow == 'edit.php') {
            $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
            if (post_type_supports($_GET['post_type'], 'thumbnail')) {
                $inserted = ['thumbnail' => __('Thumbnail')];
                $columns  = array_slice($columns, 0, 1, true) + $inserted + array_slice(
                    $columns,
                    1,
                    count($columns) - 1,
                    true
                );
            }
        }

        return $columns;
    }

    function add_thumbnail_column_content($column, $post_id)
    {
        switch ($column) {
            case 'thumbnail':
                $image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');
                echo (isset($image_url[0])) ? '<img src="' . $image_url[0] . '" style="width:100%" />' : '<img src="' . URL_ASSETS . '/img/thumbnail.svg" style="width:100%" />';
                //echo ( isset( $image_url[0] ) ) ? 'YESS' : 'NOO';
                break;
        }
    }

    function init_body_classes($classes)
    {
        $remove_class        = [
            'archive',
            'page-template',
            //'category',
            //'tag',
            'page',
            //'logged-in',
        ];
        $remove_class_number = [
            'page-id-',
            'tag-',
            'category-',
            //'page-template-',
        ];
        foreach ($classes as $key => $class) {
            if (in_array($class, $remove_class)) {
                unset($classes[$key]);
            } else {
                if (strpos('page-id-', $class) === true) {
                    unset($classes[$key]);
                }
                foreach ($remove_class_number as $_class_number) {
                    if (preg_match('/^' . $_class_number . '([A-Za-z0-9\/]+)/', $class) == 1) {
                        unset($classes[$key]);
                    }
                }
            }
        }
        if (is_page('dashboard')) {
            $classes[] = 'dashboard';
        }
        $classes_new = array_values($classes);

        return $classes_new;
    }

    function block_users_init()
    {
        if (is_user_logged_in()) {
            if (
                is_admin() && ! (
                    current_user_can('administrator') ||
                    current_user_can('manager') ||
                    current_user_can('editor') ||
                    current_user_can('contributor') ||
                    current_user_can('product_manager') ||
                    current_user_can('blog_manager') ||
                    current_user_can('manage_woocommerce') ||
                    current_user_can('view_woocommerce_reports')
                ) &&
                ! (defined('DOING_AJAX') && DOING_AJAX)
            ) {
                wp_redirect(home_url());
                exit;
            }
        }
    }

    public function my_htaccess_contents()
    {
        $slug = 'ajax';
        if (defined('AJAX_SLUG') && AJAX_SLUG) {
            $slug = AJAX_SLUG;
        }

        $ajax = str_replace(home_url(), "", admin_url('admin-ajax.php'));
        $ajax = str_replace("//", "/", $ajax);
        add_rewrite_rule($slug . '$', $ajax);
    }
}
