<?php

namespace App\Controller;

/**
 * WPCustom Class
 * 
 * Handles WordPress customizations including security enhancements,
 * admin interface modifications, and performance optimizations.
 */
class WPCustom
{

    private $primary_user;
    /**
     * Constructor to initialize hooks and filters
     */
    public function __construct()
    {
        $this->primary_user = [1];
        $this->register();
    }

    // Initialize all the required hooks (actions and filters)
    public function register()
    {

        //\\// Security \\//\\-------------------------------------

        // Disable all REST API access
        add_filter('rest_authentication_errors', [$this, 'disable_rest_api']);

        // Remove X-Powered-By header
        add_action('send_headers', [$this, 'remove_x_powered_by']);

        // Disable XML-RPC access
        add_filter('xmlrpc_enabled', [$this, 'disable_xml_rpc']);

        // Disable Heartbeat API or limit its frequency
        add_action('init', [$this, 'disable_heartbeat']);
        add_filter('heartbeat_settings', [$this, 'limit_heartbeat_settings']);

        // Restrict access to PHP files in certain directories
        add_action('template_redirect', [$this, 'restrict_php_file_access']);

        // Disable PHP file uploads
        add_filter('upload_mimes', [$this, 'restrict_php_uploads']);

        // Monitor changes in sensitive files
        add_action('init', [$this, 'monitor_sensitive_files']);

        // Ensure optimal file and directory permissions
        add_action('init', [$this, 'enforce_file_permissions']);

        // Add Content Security Policy (CSP) header
        //add_action('send_headers', [$this, 'add_content_security_policy']);

        // Protect critical files from direct access
        add_action('template_redirect', [$this, 'protect_critical_files']);

        // Add Strict-Transport-Security header
        add_action('send_headers', [$this, 'add_strict_transport_security']);

        // Prevent directory listing
        add_action('template_redirect', [$this, 'disable_directory_listing']);

        // Add X-Frame-Options header to prevent clickjacking
        add_action('send_headers', [$this, 'add_x_frame_options']);

        //\\// Cleaning \\//\\-------------------------------------

        // Remove unnecessary WordPress styles and scripts
        add_action('wp_enqueue_scripts', [$this, 'remove_unnecessary_assets'], 100);

        // Remove auto sizes CSS fix
        add_filter('wp_img_tag_add_auto_sizes', [$this, 'remove_auto_sizes_css_fix']);

        // Remove WordPress version and meta information
        add_action('init', [$this, 'remove_wp_version_and_meta']);

        // Clean WP-Head
        add_action('after_setup_theme', [$this, 'clean_wp_head']);

        // Remove dashboard widgets
        add_action('wp_dashboard_setup', [$this, 'remove_dashboard_widgets']);
        add_action('admin_init', [$this, 'remove_dashboard_widgets_other']);

        // Remove the welcome panel
        add_action('admin_head', [$this, 'remove_welcome_panel']);

        // Remove Dashboard Pages
        add_action('admin_menu', [$this, 'restrict_dashboard_pages'], 99);

        // Restrict access to Dashboard pages
        add_action('admin_init', [$this, 'restrict_dashboard_access']);

        // Remove WooCommerce Pages
        add_action('admin_menu', [$this, 'restrict_woocommerce_pages'], 99);

        // Restrict access to WooCommerce pages
        add_action('admin_init', [$this, 'restrict_woocommerce_access']);

        // Change WooCommerce Menu
        add_action('admin_menu', [$this, 'customize_woocommerce_menu']);

        // Disable WooCommerce Admin Features
        add_filter('woocommerce_admin_features', [$this, 'disable_woocommerce_admin_features']);

        // Remove admin notices
        add_action('admin_init', [$this, 'remove_admin_notices']);

        // Remove help tabs
        add_action('admin_head', [$this, 'remove_help_tabs']);

        // Remove admin bar on the frontend
        add_action('after_setup_theme', [$this, 'disable_admin_bar_for_frontend']);

        // Remove specific menus from the admin bar in the dashboard
        add_action('admin_bar_menu', [$this, 'remove_admin_bar_menus'], 999);

        // Set default admin color scheme
        add_filter('get_user_option_admin_color', [$this, 'set_default_admin_color']);

        // Remove admin color scheme options from profile page
        add_action('admin_init', [$this, 'remove_admin_color_scheme_options']);

        // Remove WordPress version from admin footer
        add_filter('update_footer', [$this, 'remove_wp_version_from_footer'], 11);

        // Customize login error messages
        add_filter('login_errors', [$this, 'custom_login_errors']);

        // Limit login attempts
        add_action('wp_login_failed', [$this, 'track_login_attempts']);
        add_action('wp_authenticate', [$this, 'check_login_attempts']);

        // Remove language selector from login page
        add_filter('login_display_language_dropdown', '__return_false');

        // Remove Privacy Policy link from login page
        remove_action('login_footer', [$this, 'the_privacy_policy_link'], 99);

        // Change login page title
        add_filter('login_title', [$this, 'custom_login_page_title']);

        // Disable author archive pages
        add_action('template_redirect', [$this, 'disable_author_archive_pages']);

        // Remove WooCommerce meta tags and widgets
        add_action('wp_head', [$this, 'remove_woocommerce_meta'], 1);
        add_filter('woocommerce_generator_tag', '__return_empty_string');
        add_action('widgets_init', [$this, 'remove_woocommerce_widgets'], 15);

        // Remove default WordPress widgets
        add_action('widgets_init', [$this, 'remove_default_wp_widgets'], 15);

        // Modify WooCommerce AJAX URL
        add_filter('woocommerce_get_script_data', [$this, 'modify_woocommerce_ajax_url']);
        add_filter('wc_add_to_cart_params', [$this, 'modify_woocommerce_ajax_url']);
        add_filter('wc_order_attribution_params', [$this, 'modify_woocommerce_ajax_url']);

        //\\// Feature \\//\\-------------------------------------

        // Set the number of post revisions to keep
        add_filter('wp_revisions_to_keep', [$this, 'set_revisions_to_keep'], 10, 2);

        // Disable image compression
        add_filter('wp_editor_set_quality', [$this, 'disable_image_compression']);
        add_filter('jpeg_quality', [$this, 'disable_image_compression']);

        // Disable Big Image resizing
        add_action('init', [$this, 'disable_big_image_handling']);

        // Allow SVG uploads with role-based restrictions
        add_filter('upload_mimes', [$this, 'allow_svg_uploads'], 99);

        // Check and sanitize SVG uploads
        add_filter('wp_check_filetype_and_ext', [$this, 'check_svg_uploads'], 10, 4);

        // Add Essential Meta Tags
        add_action('wp_head', [$this, 'add_essential_meta_tags']);

        // Add Icon Meta Tags
        add_action('wp_head', [$this, 'add_icon_meta_tags']);
        add_action('admin_head', [$this, 'add_icon_meta_tags']);

        // Add Social Media Meta Tags
        //add_action('wp_head', [$this, 'add_social_meta_tags']);

        // Add registration date column to the users table
        add_filter('manage_users_columns', [$this, 'add_registration_date_column']);

        // Display data in the registration date column
        add_action('manage_users_custom_column', [$this, 'display_registration_date_column'], 10, 3);

        // Make the registration date column sortable
        add_filter('manage_users_sortable_columns', [$this, 'make_registration_date_column_sortable']);

        // Handle sorting by registration date
        add_action('pre_get_users', [$this, 'sort_by_registration_date']);

        // Set default sorting to registration date in descending order
        add_filter('users_list_table_query_args', [$this, 'set_default_user_sort']);

        // Replace default term description field with WYSIWYG editor
        add_action('admin_init', [$this, 'initialize_term_editor_hooks']);

        //\\// Customizing \\//\\-------------------------------------

        // Customize the admin title
        add_filter('admin_title', [$this, 'custom_admin_title'], 10, 2);

        // Add custom admin bar node
        add_action('admin_bar_menu', [$this, 'add_custom_admin_bar_node'], 100);

        // Add custom dashboard widget
        add_action('wp_dashboard_setup', [$this, 'add_custom_dashboard_widget']);

        // Customize the footer text
        add_filter('admin_footer_text', [$this, 'custom_admin_footer_text']);

        // Customize login page styles
        add_action('login_enqueue_scripts', [$this, 'customize_login_styles']);

        // Customize login logo URL
        add_filter('login_headerurl', [$this, 'custom_login_logo_url']);
        add_filter('login_headertext', [$this, 'custom_login_logo_title']);
    }

    //\\// Security \\//\\-------------------------------------

    /**
     * Disable REST API access for non-admin users
     *
     * @param mixed $access Current REST API access state
     * @return WP_Error|null Updated access state
     */
    public function disable_rest_api($access)
    {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return new \WP_Error(
                'rest_api_disabled',
                __('An error occurred. Please try again later.', LANG_STRING),
                ['status' => 403]
            );
        }
        return $access;
    }

    /**
     * Remove X-Powered-By header for security
     *
     * @return void
     */
    public function remove_x_powered_by()
    {
        header_remove('X-Powered-By');
    }

    /**
     * Disable XML-RPC completely
     *
     * @return bool
     */
    public function disable_xml_rpc()
    {
        return false; // Disable XML-RPC
    }

    /**
     * Disable Heartbeat API in the front-end to reduce server load
     *
     * @return void
     */
    public function disable_heartbeat()
    {
        if (!is_admin()) { // Only disable in front-end
            wp_deregister_script('heartbeat');
        }
    }

    /**
     * Limit the frequency of Heartbeat API requests
     *
     * @param array $settings Current Heartbeat settings
     * @return array Modified Heartbeat settings with reduced frequency
     */
    public function limit_heartbeat_settings($settings)
    {
        $settings['interval'] = 60; // Set interval to 60 seconds
        return $settings;
    }

    /**
     * Restrict access to PHP files in certain directories
     *
     * @return void
     */
    public function restrict_php_file_access()
    {
        if (is_admin()) {
            return;
        }

        $restricted_dirs = ['/wp-content/uploads/', '/wp-includes/'];
        $requested_file = $_SERVER['SCRIPT_FILENAME'];

        foreach ($restricted_dirs as $dir) {
            if (strpos($requested_file, $dir) !== false && pathinfo($requested_file, PATHINFO_EXTENSION) === 'php') {
                wp_die(__('Access to this file is restricted.', LANG_STRING));
            }
        }
    }

    /**
     * Disable PHP file uploads
     *
     * @param array $mimes Current mime types
     * @return array Updated mime types without PHP
     */
    public function restrict_php_uploads($mimes)
    {
        unset($mimes['php']);
        unset($mimes['php4']);
        unset($mimes['php5']);
        unset($mimes['phtml']);
        unset($mimes['sh']);
        return $mimes;
    }

    /**
     * Monitor changes in sensitive files and folders and log them
     *
     * @return void
     */
    public function monitor_sensitive_files()
    {
        $sensitive_files = ['wp-config.php', '.htaccess', '.env'];
        $sensitive_dirs = [ABSPATH . 'app/themes/', ABSPATH . 'app/modules/', ABSPATH . 'app/plugins/'];
        $log_dir = WP_CONTENT_DIR . '/logs/';
        $log_file = $log_dir . 'log-monitor.txt';
        $alert_threshold = 1800; // 30 minutes

        // Ensure log directory exists
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $current_time = time();

        // Monitor sensitive files
        foreach ($sensitive_files as $file) {
            $file_path = ABSPATH . $file;
            if (file_exists($file_path)) {
                $last_modified = filemtime($file_path);
                if ($current_time - $last_modified < $alert_threshold) {
                    $log_entry = sprintf(
                        "%s - %s was modified (file).",
                        date('Y-m-d H:i:s', $last_modified),
                        $file_path
                    );

                    file_put_contents($log_file, $log_entry . PHP_EOL, FILE_APPEND);

                    wp_mail(
                        get_option('admin_email'),
                        'Sensitive File Change Alert',
                        "$file_path has been modified recently."
                    );
                }
            }
        }

        // Monitor sensitive directories
        foreach ($sensitive_dirs as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
                foreach ($iterator as $file_info) {
                    if ($file_info->isFile()) {
                        $file_path = $file_info->getPathname();
                        $last_modified = $file_info->getMTime();
                        if ($current_time - $last_modified < $alert_threshold) {
                            $log_entry = sprintf(
                                "%s - %s was modified (directory file).",
                                date('Y-m-d H:i:s', $last_modified),
                                $file_path
                            );

                            file_put_contents($log_file, $log_entry . PHP_EOL, FILE_APPEND);

                            wp_mail(
                                get_option('admin_email'),
                                'Sensitive Directory File Change Alert',
                                "$file_path has been modified recently."
                            );
                        }
                    }
                }
            }
        }

        // Ensure the log file is protected
        if (file_exists($log_file)) {
            chmod($log_file, 0640);
        }
    }

    /**
     * Enforce secure file and directory permissions
     *
     * @return void
     */
    public function enforce_file_permissions()
    {
        $dirs = ['/app/', '/core/'];
        foreach ($dirs as $dir) {
            $path = ABSPATH . $dir;
            if (file_exists($path)) { // Check if the directory exists
                chmod($path, 0755);
            }
        }

        $files = [
            'wp-config.php' => 0644,
            '.env' => 0640,
        ];
        foreach ($files as $file => $permission) {
            $path = ABSPATH . $file;
            if (file_exists($path)) { // Check if the file exists
                chmod($path, $permission);
            }
        }
    }

    /**
     * Add Content Security Policy (CSP) header
     *
     * @return void
     */
    public function add_content_security_policy()
    {
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self';");
    }

    /**
     * Protect critical files from direct access
     *
     * @return void
     */
    public function protect_critical_files()
    {
        if (preg_match('/(wp-config\.php|\.htaccess|\.env)/i', $_SERVER['REQUEST_URI'])) {
            wp_die(__('Access denied.', LANG_STRING));
        }
    }

    /**
     * Add Strict-Transport-Security header
     *
     * @return void
     */
    public function add_strict_transport_security()
    {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    }

    /**
     * Prevent directory listing
     *
     * @return void
     */
    public function disable_directory_listing()
    {
        if (is_dir(ABSPATH) && !file_exists(ABSPATH . '/index.php')) {
            wp_die(__('Directory listing is disabled.', LANG_STRING));
        }
    }

    /**
     * Add X-Frame-Options header to prevent clickjacking
     *
     * @return void
     */
    public function add_x_frame_options()
    {
        header("X-Frame-Options: SAMEORIGIN");
    }

    //\\// Cleaning \\//\\-------------------------------------

    /**
     * Remove WordPress version and meta information from front-end
     *
     * @return void
     */
    public function remove_wp_version_and_meta()
    {
        // Remove WordPress version from head
        remove_action('wp_head', 'wp_generator');
        // Remove WordPress version from RSS feeds
        add_filter('the_generator', '__return_empty_string');
        // Remove version from scripts and styles
        add_filter('style_loader_src', [$this, 'remove_version_from_assets'], 10, 2);
        add_filter('script_loader_src', [$this, 'remove_version_from_assets'], 10, 2);
    }

    /**
     * Remove version query string from scripts and styles
     *
     * @param string $src The source URL of the asset
     * @param string $handle The handle name of the asset
     * @return string Updated source URL without version
     */
    public function remove_version_from_assets($src, $handle)
    {
        if (strpos($src, 'ver=') !== false) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }

    /**
     * Method to clean up unnecessary items from wp-head
     */
    public function clean_wp_head()
    {
        // Remove rel canonical links (useful for SEO; keep if using an SEO plugin for management)
        remove_action('wp_head', 'rel_canonical');

        // Remove shortlink (rarely used; safe to remove unless explicitly required)
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

        // Remove RSD link (needed for XML-RPC; remove if not using external publishing tools)
        remove_action('wp_head', 'rsd_link');

        // Remove feed links (keep if RSS feeds are necessary)
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);

        // Remove unnecessary rel links (generally safe to remove)
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
        remove_action('wp_head', 'parent_post_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

        // Remove wlwmanifest link (only needed for Windows Live Writer; safe to remove)
        remove_action('wp_head', 'wlwmanifest_link');

        // Remove emoji scripts and styles (safe to remove for performance improvements)
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('wp_footer', 'wp_print_footer_scripts');
        remove_action('admin_print_styles', 'print_emoji_styles');

        // Remove REST API link (keep if using REST API functionality in the head)
        remove_action('wp_head', 'rest_output_link_wp_head', 10);

        // Remove oEmbed discovery links (keep if embedding content into other sites is necessary)
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

        // Remove Local Font
        remove_action('wp_head', 'wp_print_font_faces', 50);
    }

    /**
     * Remove unnecessary dashboard widgets to declutter admin panel
     *
     * @return void
     */
    public function remove_dashboard_widgets()
    {
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_secondary', 'dashboard', 'side');
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
        remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

        remove_meta_box('wpp_dashboard_primary', 'dashboard', 'normal');

        remove_meta_box('dashboard_primary', 'dashboard-network', 'side');
        remove_meta_box('dashboard_secondary', 'dashboard-network', 'side');
        remove_meta_box('network_dashboard_right_now', 'dashboard-network', 'core');

        remove_meta_box('wc_admin_dashboard_setup', 'dashboard', 'normal');
        remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
        remove_meta_box('persian_woocommerce_feed', 'dashboard', 'normal');
        remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
    }
    public function remove_dashboard_widgets_other()
    {

        remove_meta_box('dashboard_primary', 'dashboard-network', 'side');
        remove_meta_box('dashboard_secondary', 'dashboard-network', 'side');
        remove_meta_box('network_dashboard_right_now', 'dashboard-network', 'core');
    }

    /**
     * Remove the welcome panel from the WordPress dashboard.
     *
     * This method hides the welcome panel to declutter the admin dashboard.
     *
     * @return void
     */
    public function remove_welcome_panel()
    {
        remove_action('welcome_panel', 'wp_welcome_panel');
    }

    /**
     * Remove Dashboard pages.
     *
     * This method removes unnecessary Dashboard pages.
     *
     * @return void
     */
    public function restrict_dashboard_pages()
    {
        if (in_array(get_current_user_id(), $this->primary_user)) {
            return;
        }

        remove_submenu_page('index.php', 'my-sites.php');

        remove_submenu_page('themes.php', 'themes.php');
        remove_submenu_page('themes.php', 'site-editor.php?path=/patterns');
        remove_submenu_page('themes.php', add_query_arg('return', urlencode(remove_query_arg(wp_removable_query_args(), wp_unslash($_SERVER['REQUEST_URI']))), 'customize.php'));

        remove_menu_page('plugins.php');

        remove_menu_page('tools.php');
        remove_submenu_page('tools.php', 'import.php');
        remove_submenu_page('tools.php', 'export.php');
        remove_submenu_page('tools.php', 'site-health.php');
        remove_submenu_page('tools.php', 'export-personal-data.php');
        remove_submenu_page('tools.php', 'erase-personal-data.php');
        remove_submenu_page('tools.php', 'action-scheduler');
        remove_submenu_page('tools.php', 'wp-crontrol');

        remove_menu_page('options-general.php');
        remove_submenu_page('options-general.php', 'options-writing.php');
        remove_submenu_page('options-general.php', 'options-reading.php');
        remove_submenu_page('options-general.php', 'options-discussion.php');
        remove_submenu_page('options-general.php', 'options-media.php');
        remove_submenu_page('options-general.php', 'options-permalink.php');
        remove_submenu_page('options-general.php', 'options-privacy.php');
        remove_submenu_page('options-general.php', 'options-media.php');
        remove_submenu_page('options-general.php', 'wp-crontrol-schedules');
        remove_submenu_page('options-general.php', 'wp-parsi-settings');
    }

    /**
     * Restrict access to Dashboard pages.
     *
     * This method blocks unauthorized users from directly accessing Dashboard pages.
     *
     * @return void
     */
    public function restrict_dashboard_access()
    {
        if (in_array(get_current_user_id(), $this->primary_user)) {
            return;
        }

        $restricted_pages = [
            'themes.php',
            'customize.php',
            'site-editor.php',

            'plugins.php',

            'tools.php',
            'import.php',
            'export.php',
            'site-health.php',
            'export-personal-data.php',
            'erase-personal-data.php',
            'action-scheduler.php',
            'wp-crontrol',

            'options-general.php',
            'options-writing.php',
            'options-reading.php',
            'options-discussion.php',
            'options-media.php',
            'options-permalink.php',
            'options-privacy.php',
            'wp-crontrol-schedules',
            'wp-parsi-settings',

            'sites.php',
            'settings.php',
        ];

        $current_page = $_SERVER['REQUEST_URI'] ?? '';

        foreach ($restricted_pages as $page) {
            if (strpos($current_page, $page) !== false) {
                wp_die(__('You do not have permission to access this page.', LANG_STRING));
            }
        }
    }

    /**
     * Remove WooCommerce pages.
     *
     * This method removes unnecessary WooCommerce pages like the setup wizard and settings.
     *
     * @return void
     */
    public function restrict_woocommerce_pages()
    {
        if (in_array(get_current_user_id(), $this->primary_user)) {
            return;
        }

        remove_submenu_page('woocommerce', 'wc-settings');
        remove_submenu_page('woocommerce', 'wc-addons');
        remove_submenu_page('woocommerce', 'wc-status');
        remove_submenu_page('woocommerce', 'wc-admin&path=/extensions');
        remove_menu_page('admin.php?page=wc-admin&path=/marketing');
        remove_menu_page('admin.php?page=wc-admin&task=payments');

        remove_submenu_page('woocommerce', 'wc-persian-plugins');
        remove_menu_page('persian-wc');

        // Helper function to check if a string contains any of the specified needles.
        $string_contains_any = function (string $haystack, array $needles, int $offset = 0): bool {
            foreach ($needles as $needle) {
                if (strpos($haystack, $needle, $offset) !== false) {
                    return false;
                }
            }
            return true;
        };

        // Contains the URI of the current page.
        $current_url = $_SERVER['REQUEST_URI'];

        // Pages that must remain accessible
        $should_still_work = ['customers', 'analytics', 'marketing'];

        // Make sure some WooCommerce pages will still work
        if ($string_contains_any($current_url, $should_still_work)) {
            remove_submenu_page('woocommerce', 'wc-admin');
        }
    }

    /**
     * Restrict access to WooCommerce pages.
     *
     * This method blocks unauthorized users from directly accessing WooCommerce setup and settings pages.
     *
     * @return void
     */
    public function restrict_woocommerce_access()
    {
        if (in_array(get_current_user_id(), $this->primary_user)) {
            return;
        }

        $restricted_pages = [
            'wc-settings',
            'wc-addons',
            'wc-status',
            'path=%2Fsetup-wizard',
            'path=%2Fanalytics%2Fsettings',
            'path=%2Fmarketing',
            'task=payments',
            'extensions',

            'persian-wc',
            'wc-persian-plugins',
        ];

        $current_page = $_SERVER['REQUEST_URI'] ?? '';

        foreach ($restricted_pages as $page) {
            if (strpos($current_page, $page) !== false) {
                wp_die(__('You do not have permission to access this page.', LANG_STRING));
            }
        }
    }

    /**
     * Filters WooCommerce admin features to remove unwanted ones.
     *
     * This method hooks into the `woocommerce_admin_features` filter and removes specific features
     * based on the defined conditions.
     *
     * @param array $features List of available WooCommerce admin features.
     * @return array Modified list of features after removing unwanted ones.
     */
    public function disable_woocommerce_admin_features($features)
    {
        return array_values(
            array_filter($features, function ($feature) {
                return ! in_array($feature, ['marketing', 'analytics-dashboard/customizable']);
            })
        );
    }

    /**
     * Customize the WooCommerce admin menu.
     *
     * This method changes the menu title to "Shop" and the icon to "dashicons-cart".
     *
     * @param array $menu Global WordPress admin menu structure passed by reference.
     * @return void
     */
    public function customize_woocommerce_menu()
    {
        global $menu;

        foreach ($menu as &$item) {
            if ($item[2] === 'woocommerce') {
                $item[0] = __('Shop', LANG_STRING);
                $item[6] = 'dashicons-cart';
                break;
            }
        }
    }

    /**
     * Remove admin notices
     *
     * @return void
     */
    public function remove_admin_notices()
    {
        remove_all_actions('admin_notices');
        remove_all_actions('update_nag');
        remove_all_actions('all_admin_notices');
        remove_all_actions('user_admin_notices');
        remove_all_actions('network_admin_notices');
    }

    /**
     * Remove help tabs from admin pages
     *
     * @return void
     */
    public function remove_help_tabs()
    {
        $screen = get_current_screen();
        if ($screen) {
            $screen->remove_help_tabs();
        }
    }

    /**
     * Disable admin bar for non-admin users on the frontend
     *
     * @return void
     */
    public function disable_admin_bar_for_frontend()
    {
        if (!is_admin()) {
            show_admin_bar(false);
        }
    }

    /**
     * Remove unnecessary menus from the admin bar in the admin panel
     *
     * @param WP_Admin_Bar $wp_admin_bar The WordPress Admin Bar object
     * @return void
     */
    public function remove_admin_bar_menus($wp_admin_bar)
    {
        $wp_admin_bar->remove_node('wp-logo');
        $wp_admin_bar->remove_node('updates');
        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('woocommerce-site-visibility-badge');

        if (is_multisite()) {
            foreach ((array) $wp_admin_bar->get_nodes() as $node) {
                if (strpos($node->id, 'blog-') !== false) {
                    $node->title = preg_replace('/<div class="blavatar".*?<\/div>/', '', $node->title);
                    $wp_admin_bar->add_node($node);
                }
                if (strpos($node->href, 'post-new.php') !== false || strpos($node->href, 'edit-comments.php') !== false) {
                    $wp_admin_bar->remove_node($node->id);
                }
            }
        }

        if (!in_array(get_current_user_id(), $this->primary_user)) {
            $wp_admin_bar->remove_node('network-admin');
        }
    }

    /**
     * Set the default admin color scheme for all users
     *
     * @return string
     */
    public function set_default_admin_color()
    {
        return 'midnight'; // Set to the desired color scheme slug
    }

    /**
     * Remove admin color scheme options from the user profile page
     *
     * @return void
     */
    public function remove_admin_color_scheme_options()
    {
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
    }

    /**
     * Remove WordPress version from admin footer.
     *
     * This method removes the WordPress version displayed in the admin footer.
     *
     * @return string Empty string to replace the version text.
     */
    public function remove_wp_version_from_footer()
    {
        return '';
    }

    /**
     * Customize login error messages to avoid revealing sensitive information
     *
     * @return string
     */
    public function custom_login_errors()
    {
        return __('An error occurred. Please try again.', LANG_STRING);
    }

    /**
     * Track failed login attempts
     *
     * @param string $username The username used in the login attempt
     * @return void
     */
    public function track_login_attempts($username)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts = get_transient('login_attempts_' . $ip) ?: 0;
        set_transient('login_attempts_' . $ip, $attempts + 1, 15 * MINUTE_IN_SECONDS);
    }

    /**
     * Check the number of login attempts before authenticating
     *
     * @return void
     */
    public function check_login_attempts()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts = get_transient('login_attempts_' . $ip);

        if ($attempts && $attempts >= 5) {
            wp_die(__('Too many failed login attempts. Please try again later.', LANG_STRING));
        }
    }

    /**
     * Change login page title
     *
     * @return void
     */
    public function custom_login_page_title()
    {
        return get_bloginfo('name') . ' - ' . __('Login', LANG_STRING);
    }

    /**
     * Disable access to author archive pages
     *
     * @return void
     */
    public function disable_author_archive_pages()
    {
        if (is_author()) {
            wp_redirect(home_url());
            exit;
        }
    }

    /**
     * Remove unnecessary WordPress styles and scripts
     *
     * @return void
     */
    public function remove_unnecessary_assets()
    {
        // Remove emoji scripts and styles
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
    }

    /**
     * Remove auto sizes CSS fix for images.
     *
     * This method disables the addition of auto sizes attribute to images.
     *
     * @return bool
     */
    public function remove_auto_sizes_css_fix($add_auto_sizes)
    {
        return false;
    }

    /**
     * Remove WooCommerce meta tags from head
     *
     * @return void
     */
    public function remove_woocommerce_meta()
    {
        remove_action('wp_head', 'wc_generator_tag');
    }

    /**
     * Remove unnecessary WooCommerce widgets
     *
     * @return void
     */
    public function remove_woocommerce_widgets()
    {
        unregister_widget('WC_Widget_Cart');
        unregister_widget('WC_Widget_Layered_Nav');
        unregister_widget('WC_Widget_Layered_Nav_Filters');
        unregister_widget('WC_Widget_Product_Categories');
        unregister_widget('WC_Widget_Product_Search');
        unregister_widget('WC_Widget_Products');
        unregister_widget('WC_Widget_Recent_Reviews');
        unregister_widget('WC_Widget_Recent_Products');
        unregister_widget('WC_Widget_Top_Rated_Products');
        unregister_widget('WC_Widget_Rating_Filter');
        unregister_widget('WC_Widget_Price_Filter');
    }

    /**
     * Remove default WordPress widgets
     *
     * @return void
     */
    public function remove_default_wp_widgets()
    {
        //unregister_widget('WP_Nav_Menu_Widget');
        //unregister_widget('WP_Widget_Text');
        unregister_widget('WP_Widget_Pages');
        unregister_widget('WP_Widget_Calendar');
        unregister_widget('WP_Widget_Archives');
        unregister_widget('WP_Widget_Meta');
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Categories');
        unregister_widget('WP_Widget_Recent_Posts');
        unregister_widget('WP_Widget_Recent_Comments');
        unregister_widget('WP_Widget_RSS');
        unregister_widget('WP_Widget_Tag_Cloud');
        unregister_widget('WP_Widget_Media_Audio');
        unregister_widget('WP_Widget_Media_Image');
        unregister_widget('WP_Widget_Media_Video');
        unregister_widget('WP_Widget_Media_Gallery');
    }


    /**
     * Modify WooCommerce AJAX URL.
     *
     * This method updates the WooCommerce AJAX URL to use a custom endpoint.
     *
     * @param array $params The WooCommerce parameters.
     * @return array Modified parameters.
     */
    public function modify_woocommerce_ajax_url($params)
    {
        if (isset($params['ajax_url'])) {
            $params['ajax_url'] = AJAX_URL;
        }
        return $params;
    }

    //\\// Feature \\//\\-------------------------------------

    /**
     * Disable image compression
     *
     * @param int $quality The default quality value for images
     * @return int
     */
    public function disable_image_compression($quality)
    {
        return 100; // Set quality to 100 to disable compression
    }

    /**
     * Disable Big Image Handling.
     *
     * This method prevents WordPress from automatically resizing large images.
     *
     * @return void
     */
    public function disable_big_image_handling()
    {
        add_filter('big_image_size_threshold', '__return_false');
    }

    /**
     * Allow SVG uploads based on user roles
     *
     * @param array $mimes Existing mime types
     * @return array Updated mime types
     */
    public function allow_svg_uploads($mimes)
    {
        // Restrict SVG uploads to administrators if needed
        if (current_user_can('administrator')) {
            $mimes['svg'] = 'image/svg+xml';
            $mimes['svgz'] = 'image/svg+xml';
        }

        return $mimes;
    }

    /**
     * Check and sanitize SVG uploads
     *
     * @param array  $checked Existing file check data
     * @param string $file Full path to the file
     * @param string $filename Name of the file
     * @param array  $mimes Allowed mime types
     * @return array Updated file check data
     */
    public function check_svg_uploads($checked, $file, $filename, $mimes)
    {
        if (!$checked['type']) {
            $check_filetype = wp_check_filetype($filename, $mimes);
            $ext = $check_filetype['ext'];
            $type = $check_filetype['type'];
            $proper_filename = $filename;

            if ($type && 0 === strpos($type, 'image/') && $ext !== 'svg') {
                $ext = $type = false;
            }

            $checked = compact('ext', 'type', 'proper_filename');
        }

        return $checked;
    }

    /**
     * Set the number of revisions to keep for each post
     *
     * @param int $num The default number of revisions
     * @param WP_Post $post The current post object
     * @return int The updated number of revisions to keep
     */
    public function set_revisions_to_keep($num, $post)
    {
        return 3; // Adjust this number to set the desired limit
    }
    /**
     * Adds essential meta tags for compatibility, viewport, and theme settings.
     */
    public function add_essential_meta_tags()
    {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">' . "\n"; // Optimizes the page for mobile devices.
        echo '<meta name="color-scheme" content="light">' . "\n"; // Specifies that only the light theme is supported.
        echo '<meta name="theme-color" content="' . THEME_COLOR . '">' . "\n"; // Sets the theme color for supported browsers.
    }

    /**
     * Adds meta tags and links for various device icons and manifests.
     */
    public function add_icon_meta_tags()
    {
        echo '<link rel="apple-touch-icon" sizes="180x180" href="' . URL_ASSETS . '/img/icon/apple-touch-icon.png">' . "\n"; // Icon for Apple devices.
        echo '<link rel="icon" type="image/png" href="' . URL_ASSETS . '/img/icon/favicon-96x96.png" sizes="96x96">' . "\n"; // Favicon for general browsers.
        echo '<link rel="icon" type="image/svg+xml" href="' . URL_ASSETS . '/img/icon/favicon.svg">' . "\n"; // SVG favicon for modern browsers.
        echo '<link rel="shortcut icon" href="' . URL_ASSETS . '/img/icon/favicon.ico">' . "\n"; // Fallback favicon.
        echo '<link rel="manifest" href="' . URL_ASSETS . '/img/icon/site.webmanifest">' . "\n"; // Web app manifest.
        echo '<meta name="msapplication-TileColor" content="#ffffff">' . "\n"; // Tile color for Microsoft devices.
        echo '<meta name="msapplication-TileImage" content="' . URL_ASSETS . '/img/icon/mstile-144x144.png">' . "\n"; // Tile image for Microsoft devices.
        echo '<link rel="mask-icon" href="' . URL_ASSETS . '/img/icon/safari-pinned-tab.svg" color="' . THEME_COLOR . '">' . "\n"; // Safari pinned tab icon.
    }

    /**
     * Adds dynamic meta tags for Open Graph and Twitter cards based on the current page context.
     */
    public function add_social_meta_tags()
    {
        global $post;

        // Determine Open Graph properties dynamically based on page type.
        if (is_front_page() || is_home()) {
            $og_title = get_bloginfo('name');
            $og_description = get_bloginfo('description');
            $og_image = URL_ASSETS . '/img/icon/og-image.png';
            $og_url = home_url();
            $og_type = 'website';
        } elseif (is_singular()) {
            $og_title = get_the_title($post->ID);
            $og_description = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words(get_the_content(), 30);
            $og_image = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID, 'full') : URL_ASSETS . '/img/icon/og-image.png';
            $og_url = get_permalink($post->ID);
            $og_type = 'article';
        } else {
            $og_title = get_bloginfo('name');
            $og_description = get_bloginfo('description');
            $og_image = URL_ASSETS . '/img/icon/og-image.png';
            $og_url = home_url();
            $og_type = 'website';
        }

        // Open Graph meta tags.
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($og_url) . '">' . "\n";
        echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";

        // Twitter card meta tags.
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n"; // Specifies the type of Twitter card.
        echo '<meta name="twitter:title" content="' . esc_attr($og_title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($og_description) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
        //echo '<meta name="twitter:site" content="">' . "\n"; // Twitter handle for the website.
    }

    /**
     * Adds the "Registration Date" column to the users table.
     *
     * @param array $columns Existing columns in the users table.
     * @return array Modified array of columns with "Registration Date" added.
     */
    public function add_registration_date_column($columns)
    {
        if (isset($columns['posts'])) {
            unset($columns['posts']); // Remove the posts column (optional).
        }
        $columns['registration_date'] = __('Registration date', LANG_STRING);
        return $columns;
    }

    /**
     * Displays the registration date in the custom column.
     *
     * @param string $value Current value of the column.
     * @param string $column_name The name of the column being rendered.
     * @param int    $user_id The ID of the user for the current row.
     * @return string The formatted registration date or the original value.
     */
    public function display_registration_date_column($value, $column_name, $user_id)
    {
        if ('registration_date' === $column_name) {
            $user = get_userdata($user_id);
            return date_i18n('Y/m/d H:i:s', strtotime($user->user_registered));
        }
        return $value;
    }

    /**
     * Makes the "Registration Date" column sortable.
     *
     * @param array $sortable_columns Array of sortable columns.
     * @return array Modified array with "Registration Date" column as sortable.
     */
    public function make_registration_date_column_sortable($sortable_columns)
    {
        $sortable_columns['registration_date'] = 'user_registered';
        return $sortable_columns;
    }

    /**
     * Modifies the query to handle sorting by registration date.
     *
     * @param WP_User_Query $query The query object for retrieving users.
     */
    public function sort_by_registration_date($query)
    {
        if (! is_admin()) {
            return;
        }
        $orderby = $query->get('orderby');
        if ('user_registered' === $orderby) {
            $query->set('orderby', 'user_registered');
        }
    }

    /**
     * Sets default sorting for the users table to "Registration Date" in descending order.
     *
     * @param array $query_vars Existing query arguments for the users list.
     * @return array Modified query arguments with default sorting set.
     */
    public function set_default_user_sort($query_vars)
    {
        if (! is_admin() || ! isset($query_vars['orderby'])) {
            $query_vars['orderby'] = 'user_registered';
            $query_vars['order'] = 'DESC'; // Default order: descending.
        }
        return $query_vars;
    }

    /**
     * Initialize hooks for all taxonomies with description fields.
     */
    public function initialize_term_editor_hooks()
    {
        $taxonomies = get_taxonomies(['public' => true], 'names');
        foreach ($taxonomies as $taxonomy) {
            add_action("{$taxonomy}_edit_form_fields", [$this, 'replace_description_with_wysiwyg'], 10, 2);
        }

        // Remove the default description field globally
        add_action('admin_footer', [$this, 'remove_default_description']);
    }

    /**
     * Replace the default term description field with a WYSIWYG editor.
     *
     * @param WP_Term $term The term object being edited.
     * @param string $taxonomy The taxonomy of the term being edited.
     */
    public function replace_description_with_wysiwyg($term, $taxonomy)
    {
?>
        <tr class="form-field term-description-wrap">
            <th scope="row">
                <label for="description"><?= __('Description', LANG_STRING) ?></label>
            </th>
            <td>
                <?php
                // Get the current term description
                $description = html_entity_decode(term_description($term->term_id, $taxonomy));

                // Render the WYSIWYG editor
                wp_editor(
                    $description, // Current content
                    'description', // Editor ID
                    [
                        'textarea_name' => 'description', // Name attribute for the textarea
                        'textarea_rows' => 10,           // Number of rows for the editor
                        'media_buttons' => true,         // Enable media buttons
                    ]
                );
                ?>
                <p class="description">
                    <?= __('The description is not prominent by default; however, some themes may show it.', LANG_STRING) ?>
                </p>
            </td>
        </tr>
    <?php
    }

    /**
     * Remove the default description field from all taxonomy edit screens.
     */
    public function remove_default_description()
    {
    ?>
        <script>
            jQuery(document).ready(function() {
                // Remove the default description textarea
                jQuery('textarea#description').closest('tr').remove();
            });
        </script>
<?php
    }

    //\\// Customizing \\//\\-------------------------------------

    /**
     * Customize the admin title format
     *
     * @param string $admin_title The current admin title
     * @param string $title The current page title
     * @return string The updated admin title
     */
    public function custom_admin_title($admin_title, $title)
    {
        return get_bloginfo('name') . ' | ' . $title;
    }

    /**
     * Add a custom node to the admin bar
     *
     * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance
     * @return void
     */
    public function add_custom_admin_bar_node($wp_admin_bar)
    {
        $wp_admin_bar->add_node([
            'id' => 'theme-powered',
            'title' => '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 28 17" style="fill:#fff;height:15px;vertical-align:text-bottom"><path d="M24.7,10.5V.6h-2.5l-8.5,12.5h8.4v3.5h2.5v-3.5h3.2v-2.6h-3.2,0ZM22.2,10.5h-3.8l3.8-5.5v5.5Z"/> <path d="M6,.6l-3.7,9.9h0c0,0-2.3,6.1-2.3,6.1h2.8l1.3-3.5h6.8l1.4,3.5h2.9L8.8.6h-2.8ZM7.5,4.3l2.4,6.2h-4.7l2.3-6.2Z"/></svg>',
            'href' => 'https://a4.design',
            'meta' => [
                'title' => __('A4 Design House', LANG_STRING),
                'target' => '_blank',
            ]
        ]);
    }

    /**
     * Add a custom dashboard widget
     *
     * @return void
     */
    public function add_custom_dashboard_widget()
    {
        wp_add_dashboard_widget(
            'theme_dashboard_widget',
            'Howdy,' . ' ' . wp_get_current_user()->display_name,
            function () {
                echo '<p>' . __('Welcome to Admin Panel', LANG_STRING) . '</p>';
            }
        );
    }

    /**
     * Customize the footer text in the admin panel
     *
     * @return string The updated footer text
     */
    public function custom_admin_footer_text()
    {
        return __('Designed and Developed by', LANG_STRING) . ' ' . __('A4 Design House', LANG_STRING);
    }

    /**
     * Customize the login page styles
     *
     * @return void
     */
    public function customize_login_styles()
    {
        echo "<style>
        body.login {
            background-color: background: rgba(34, 14, 14, 0.05);
        }
        .wp-login-logo > a {
            background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 28 17'%3E%3Cpath d='M24.7 10.5V.6h-2.5l-8.5 12.5h8.4v3.5h2.5v-3.5h3.2v-2.6h-3.2Zm-2.5 0h-3.8L22.2 5v5.5Z'/%3E%3Cpath d='m6 .6-3.7 9.9L0 16.6h2.8l1.3-3.5h6.8l1.4 3.5h2.9L8.8.6H6Zm1.5 3.7 2.4 6.2H5.2l2.3-6.2Z'/%3E%3C/svg%3E\") !important;
            background-repeat: no-repeat no-repeat !important;
            background-position: center center !important;
            background-size: auto 100% !important;
            width: 100% !important;
            height: 100px !important;
        }
        .login form {
            border: 1px solid #ddd;
            padding: 20px;
        }
        .privacy-policy-page-link {
            display: none;
        }
        </style>";
    }

    /**
     * Customize the login logo URL
     *
     * @return string The custom URL for the login logo
     */
    public function custom_login_logo_url()
    {
        return 'https://a4.design';
    }
    public function custom_login_logo_title()
    {
        return __('Designed and Developed by', LANG_STRING) . ' ' . __('A4 Design House', LANG_STRING);
    }
}
