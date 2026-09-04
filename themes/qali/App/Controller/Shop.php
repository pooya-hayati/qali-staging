<?php

namespace App\Controller;

use WP_Query;
use WP_Error;

class Shop
{
    public $post_name = 'product';

    public function __construct()
    {

        $this->register();
    }

    /**
     * Register all hooks and filters
     */
    public function register()
    {
        // System
        add_action('after_setup_theme', [$this, 'add_woocommerce_support']);
        add_action('after_setup_theme', [$this, 'register_image_sizes']);
        add_filter('wp_generate_attachment_metadata', [$this, 'convert_custom_sizes_to_webp'], 10, 2);
        add_filter('wp_editor_set_quality', [$this, 'set_webp_quality'], 10, 2);
        add_filter('woocommerce_coming_soon_exclude', [$this, 'theme_coming_soon_exclude'], 999);

        // Cleanup
        add_filter('init', [$this, 'woocommerce_default_actions']);
        add_filter('wp_enqueue_scripts', [$this, 'woocommerce_enqueue_scripts'], 100);
        add_action('wp_head', [$this, 'post_type_wp_head'], 9);
        add_action('wp_dashboard_setup', [$this, 'woocommerce_dashboard_widget']);
        add_action('wp', [$this, 'woocommerce_theme_support']);
        add_filter('manage_product_posts_columns', [$this, 'change_columns'], 999);

        // Customize
        add_action('pre_get_posts', [$this, 'change_default_query'], 1);

        // Enhance
        add_action('wp_enqueue_scripts', [$this, 'enqueue_custom_ajax_scripts']);
        add_action('wp_ajax_' . 'add_to_cart', [$this, 'ajax_add_to_cart']);
        add_action('wp_ajax_' . 'nopriv_add_to_cart', [$this, 'ajax_add_to_cart']);

        add_action('wp_ajax_' . 'get_cart_count', [$this, 'ajax_get_cart_count']);
        add_action('wp_ajax_' . 'nopriv_get_cart_count', [$this, 'ajax_get_cart_count']);

        add_action('wp_ajax_' . 'qali_load_more_products', [$this, 'ajax_load_more_products']);
        add_action('wp_ajax_' . 'nopriv_qali_load_more_products', [$this, 'ajax_load_more_products']);

        add_filter('rwmb_meta_boxes', [$this, 'register_meta']);
        add_action('init', [$this, 'remove_product_features']);
        add_action('add_meta_boxes', [$this, 'remove_meta_boxes'], 99);
        add_filter('manage_edit-product_columns', [$this, 'remove_columns_from_product_list']);
        add_action('init', [$this, 'remove_woocommerce_image_sizes']);
        add_filter('woocommerce_dimension_unit_options', [$this, 'add_custom_dimension_unit'], 5);
        add_filter('woocommerce_available_dimension_units', [$this, 'add_custom_dimension_unit'], 5);
        //add_action('woocommerce_after_register_taxonomy', [$this, 'hierarchical_attributes']);

        // Facet
        add_action('pre_get_posts', [$this, 'handle_query']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('pre_get_posts', [$this, 'modify_search_query']);
    }
    // System
    public function add_woocommerce_support()
    {
        add_theme_support('woocommerce');
    }

    /**
     * Sized intermediate crops used instead of serving full-resolution originals
     * in product cards and the header "Latest News" card. Soft (uncropped) so
     * object-fit: contain in CSS still shows the whole rug without distortion.
     */
    public function register_image_sizes()
    {
        add_image_size('qali-product-card', 260, 346, false);
        add_image_size('qali-product-card-2x', 520, 692, false);
        add_image_size('qali-featured-card', 900, 900, false);
    }

    /**
     * Re-saves just the qali-* intermediate sizes as WebP after WordPress
     * generates them, leaving every other registered size (thumbnail, medium,
     * any plugin-registered size) in its original format untouched.
     */
    public function convert_custom_sizes_to_webp($metadata, $attachment_id)
    {
        if (empty($metadata['sizes'])) {
            return $metadata;
        }

        $custom_sizes = ['qali-product-card', 'qali-product-card-2x', 'qali-featured-card'];
        $file = get_attached_file($attachment_id);
        if (! $file) {
            return $metadata;
        }
        $dir = dirname($file);

        foreach ($custom_sizes as $size_name) {
            if (empty($metadata['sizes'][$size_name])) {
                continue;
            }

            $size_data = $metadata['sizes'][$size_name];
            if (($size_data['mime-type'] ?? '') === 'image/webp') {
                continue;
            }

            $src_path = $dir . '/' . $size_data['file'];
            if (! file_exists($src_path)) {
                continue;
            }

            $editor = wp_get_image_editor($src_path);
            if (is_wp_error($editor)) {
                continue;
            }

            $webp_filename = preg_replace('/\.[^.]+$/', '.webp', $size_data['file']);
            $saved = $editor->save($dir . '/' . $webp_filename, 'image/webp');
            if (is_wp_error($saved)) {
                continue;
            }

            @unlink($src_path);

            $metadata['sizes'][$size_name] = [
                'file'      => $saved['file'],
                'width'     => $saved['width'],
                'height'    => $saved['height'],
                'mime-type' => 'image/webp',
                'filesize'  => $saved['filesize'] ?? (file_exists($dir . '/' . $saved['file']) ? filesize($dir . '/' . $saved['file']) : 0),
            ];
        }

        return $metadata;
    }

    /**
     * WordPress re-derives quality internally whenever WP_Image_Editor::save()
     * converts to a different mime type (see WP_Image_Editor::get_output_format()),
     * which silently overrides any set_quality() call made beforehand — so quality
     * for our WebP conversions has to be set via this filter, not by calling
     * set_quality() directly before save().
     */
    public function set_webp_quality($quality, $mime_type)
    {
        return $mime_type === 'image/webp' ? 75 : $quality;
    }

    public function theme_coming_soon_exclude()
    {
        return true;
    }
    // Cleanup
    public function woocommerce_default_actions()
    {

        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_after_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

        add_filter('woocommerce_allow_marketplace_suggestions', '__return_false', 999);
        add_filter('woocommerce_show_page_title', '__return_false');
    }

    public function woocommerce_enqueue_scripts()
    {
        $scripts_to_deregister = [
            'flexslider',
            'zoom',
            'photoswipe-ui-default',
            //'select2',
        ];
        $styles_to_deregister = [
            'woocommerce-layout',
            'woocommerce-layout-rtl',
            'woocommerce-smallscreen',
            'woocommerce-smallscreen-rtl',
            'woocommerce-general',
            'woocommerce-general-rtl',
            'wc-blocks-style',
            'wc-blocks-style-rtl',
            'woocommerce-inline',
            'global-styles',
            'classic-theme-styles',
            'photoswipe',
            'photoswipe-default-skin',
            //'select2',
            'woocommerce-coming-soon',
            'brands-styles',
        ];

        foreach ($scripts_to_deregister as $script) {
            wp_deregister_script($script);
        }

        foreach ($styles_to_deregister as $style) {
            wp_deregister_style($style);
        }
    }

    public function post_type_wp_head()
    {
        remove_action('wp_head', 'wc_gallery_noscript');
    }

    public function woocommerce_dashboard_widget()
    {
        remove_meta_box('persian_woocommerce_feed', 'dashboard', ' normal');
    }

    public function woocommerce_theme_support()
    {
        remove_theme_support('wc-product-gallery-zoom');
        remove_theme_support('wc-product-gallery-lightbox');
        remove_theme_support('wc-product-gallery-slider');
    }
    public function change_columns($columns)
    {
        unset($columns['thumbnail']);
        return $columns;
    }
    // Customize

    /**
     * True only for a genuine wp-admin screen request — unlike bare is_admin(),
     * this is false for a front-end-triggered admin-ajax.php call (WordPress
     * defines WP_ADMIN, so is_admin() is always true there too). Needed so
     * ajax_load_more_products()'s is_main_query() spoof actually reaches this
     * theme's own product-archive query filters, which must still stay off
     * for the real wp-admin Products list.
     */
    private static function is_real_admin_request()
    {
        return is_admin() && !wp_doing_ajax();
    }

    public function change_default_query($query)
    {
        if (!self::is_real_admin_request() && ($query->is_post_type_archive('product') || $query->is_tax('product_cat') || $query->is_tax('product_tag')) && $query->is_main_query()) {
            $query->set('posts_per_page', '24');

            // محصولات ناموجود به انتهای لیست منتقل می‌شوند
            add_filter('posts_clauses', [$this, 'sort_out_of_stock_products_last'], 10, 2);

            //$query->set('orderby', 'date');
            //$query->set('order', 'DESC');
        }
    }

    /**
     * محصولات ناموجود را به انتهای لیست منتقل می‌کند
     *
     * Gated to is_main_query() — ajax_load_more_products() relies on this
     * still firing for its query by temporarily making that query "the main
     * query" (see there) rather than duplicating this logic, so any future
     * change here stays in sync for both normal page loads and "Show More".
     */
    public function sort_out_of_stock_products_last($clauses, $query)
    {
        global $wpdb;

        if (!self::is_real_admin_request() && $query->is_main_query() && ($query->is_post_type_archive('product') || $query->is_tax('product_cat') || $query->is_tax('product_tag'))) {
            // اضافه کردن JOIN برای meta_stock_status
            $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS mt_stock ON ({$wpdb->posts}.ID = mt_stock.post_id AND mt_stock.meta_key = '_stock_status')";

            // اضافه کردن ORDER BY برای نمایش محصولات موجود ابتدا
            $clauses['orderby'] = " CASE WHEN mt_stock.meta_value = 'instock' THEN 0 ELSE 1 END, " . $clauses['orderby'];

            // جلوگیری از اجرای مجدد فیلتر
            remove_filter('posts_clauses', [$this, 'sort_out_of_stock_products_last'], 10);
        }

        return $clauses;
    }
    // Enhance

    public function register_meta()
    {
        $meta_boxes[] = [
            'id'         => 'standard-' . $this->post_name . '-options',
            'title'      => __('Options', LANG_STRING),
            'post_types' => $this->post_name,
            'context'    => 'normal',
            'priority'   => 'high',
            'autosave'   => false,
            'fields'     => [
                [
                    'name'    => __('Page', LANG_STRING),
                    'id'      => 'page',
                    'type'    => 'group',
                    'fields'  => [
                        [
                            'name'    => __('Cards', LANG_STRING),
                            'id'      => 'card',
                            'type'    => 'group',
                            'clone'      => true,
                            'sort_clone' => true,
                            'collapsible' => true,
                            'default_state' => 'collapsed',
                            'group_title' => '{type} : {size}',
                            'fields'  => [
                                [
                                    'name'    => __('Type', LANG_STRING),
                                    'id'      => 'type',
                                    'type'    => 'select',
                                    'options' => [
                                        'image' => __('Image', LANG_STRING),
                                        'text' => __('Text', LANG_STRING),
                                        'quote' => __('Quote', LANG_STRING),
                                        'video' => __('Video', LANG_STRING),
                                        'json' => __('Json', LANG_STRING),
                                        'embed' => __('Embed', LANG_STRING),
                                        'empty' => __('Empty', LANG_STRING),
                                    ],
                                    'std' => 'image',
                                    'required' => true,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Size', LANG_STRING),
                                    'id'      => 'size',
                                    'type'    => 'select',
                                    'options' => [
                                        12 => __('Full', LANG_STRING),
                                        6 => __('Half', LANG_STRING),
                                        4 => __('Third', LANG_STRING),
                                        3 => __('Quarter', LANG_STRING),
                                    ],
                                    'std' => 12,
                                    'required' => true,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Image', LANG_STRING),
                                    'id'      => 'image',
                                    'type'    => 'single_image',
                                    'visible' => ['type', '=', 'image'],
                                    'columns' => 4,
                                ],
                                [
                                    'name'    => __('Text', LANG_STRING),
                                    'id'      => 'text',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 4,
                                        'media_buttons'  => false,
                                        'teeny'         => false,
                                    ],
                                    'visible' => ['type', 'in', ['text', 'quote']],
                                    'columns' => 12,
                                ],
                                [
                                    'name'    => __('Video', LANG_STRING),
                                    'id'      => 'video',
                                    'type'    => 'video',
                                    'max_file_uploads' => 1,
                                    'max_status' => false,
                                    'visible' => ['type', '=', 'video'],
                                    'columns' => 4,
                                ],
                                [
                                    'name'    => __('Json File', LANG_STRING),
                                    'id'      => 'json',
                                    'type'    => 'file_advanced',
                                    'max_file_uploads' => 1,
                                    'max_status' => false,
                                    'visible' => ['type', '=', 'json'],
                                    'columns' => 4,
                                ],
                                [
                                    'name'    => __('Once', LANG_STRING),
                                    'id'      => 'once_play',
                                    'type'    => 'switch',
                                    'style'   => 'square',
                                    'visible' => ['type', 'in', ['video', 'json']],
                                    'columns' => 4,
                                ],
                                [
                                    'name'    => __('Embed', LANG_STRING),
                                    'id'      => 'embed',
                                    'type'    => 'oembed',
                                    'visible' => ['type', '=', 'embed'],
                                    'columns' => 4,
                                ],
                            ],
                            'columns' => 12,
                        ],
                    ],
                    'columns' => 12,
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-pa_color-options',
            'title'      => __('Options', LANG_STRING),
            'taxonomies' => 'pa_color',
            'fields'     => [
                [
                    'name' => __('Color', LANG_STRING),
                    'id'   => 'color',
                    'type' => 'color',
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-pa_design-options',
            'title'      => __('Options', LANG_STRING),
            'taxonomies' => 'pa_design',
            'fields'     => [
                [
                    'name' => __('Image', LANG_STRING),
                    'id'   => 'image',
                    'type' => 'single_image',
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-pa_size-options',
            'title'      => __('Options', LANG_STRING),
            'taxonomies' => 'pa_size',
            'fields'     => [
                [
                    'name' => __('Subtitle', LANG_STRING),
                    'id'   => 'subtitle',
                    'type' => 'textarea',
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-product_cat-seo',
            'title'      => __('SEO', LANG_STRING),
            'taxonomies' => 'product_cat',
            'fields'     => [
                [
                    'name' => __('Category Title (H1)', LANG_STRING),
                    'id'   => 'seo_title',
                    'type' => 'text',
                ],
                [
                    'name' => __('Category Description', LANG_STRING),
                    'id'   => 'seo_description',
                    'type' => 'wysiwyg',
                ],
            ]
        ];

        return $meta_boxes;
    }

    public function enqueue_custom_ajax_scripts()
    {
        wp_enqueue_script('shop', URL_ASSETS . '/js/shop.js?v=' . THEME_VERSION . '', ['main'], null, true);
        wp_localize_script('shop', 'shop_params', [
            'nonce' => wp_create_nonce('shop_nonce'),
        ]);
    }

    public function ajax_add_to_cart()
    {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);

        if (!$product_id || !$quantity) {
            wp_send_json_error(['message' => 'Invalid data.']);
        }

        $product = wc_get_product($product_id);

        // Check if the product exists
        if (!$product) {
            wp_send_json_error(['message' => 'Product not found.']);
        }

        // Check stock only if stock management is enabled
        if ($product->managing_stock()) {
            if (!$product->is_in_stock() || $quantity > $product->get_stock_quantity()) {
                wp_send_json_error(['message' => 'Not enough stock available.']);
            }
        }

        // Add product to cart
        $added = WC()->cart->add_to_cart($product_id, $quantity);

        if ($added) {
            wp_send_json_success(['message' => 'Product successfully added to the cart.']);
        } else {
            wp_send_json_error(['message' => 'Error adding product to the cart.']);
        }
    }

    public function ajax_get_cart_count()
    {
        $count = WC()->cart->get_cart_contents_count();
        wp_send_json_success(['count' => $count]);
    }

    /**
     * "Show More" pagination for category/tag/shop archives. Builds a WP_Query
     * matching what the real archive page would show for the requested page
     * number (same taxonomy constraint + same $_GET filter state, via
     * build_filter_query_args()), and returns rendered product-card HTML.
     *
     * This is purely additive to how the archive already paginates via real
     * WP_Query/permalinks — it doesn't change what any /page/N/ URL serves
     * server-side, so Yoast's rel=next/prev and canonical output for those
     * URLs are unaffected.
     */
    /**
     * "Show More" pagination for category/tag/shop archives.
     *
     * Rather than manually reconstructing every piece of query logic that
     * normally applies to these archives (this theme's own filter/sort
     * handling in handle_query()/sort_out_of_stock_products_last(), plus
     * WooCommerce's own WC_Query::pre_get_posts() — product visibility,
     * price meta, ordering, etc.) — all of which are gated to
     * `$query->is_main_query()` and would otherwise silently skip a manually
     * built WP_Query — this temporarily makes the query built here BE the
     * main query, so every one of those hooks fires exactly as it would for
     * a real page load. This guarantees "Show More" pages line up with real
     * WP pagination (no skipped/duplicated products) and stays correct
     * automatically if that query logic ever changes, instead of maintaining
     * a second, easily-drifting copy of it here.
     */
    public function ajax_load_more_products()
    {
        $paged = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
        $archive_type = sanitize_key($_GET['archive_type'] ?? '');
        $archive_term = sanitize_title($_GET['archive_term'] ?? '');

        $args = [
            'post_type' => 'product',
            'paged'     => $paged,
        ];

        if ($archive_type === 'product_cat' && $archive_term) {
            $args['product_cat'] = $archive_term;
        } elseif ($archive_type === 'product_tag' && $archive_term) {
            $args['product_tag'] = $archive_term;
        }

        // WooCommerce's own WC_Query only registers its pre_get_posts hook
        // (product visibility exclusion, default catalog ordering, etc.) when
        // is_admin() was false at plugin construction time — and it's never
        // false for an admin-ajax.php request (WordPress always defines
        // WP_ADMIN there), so that hook never runs here, is_main_query() spoof
        // or not. Replicate just the two pieces of it this fetch actually
        // needs, via WooCommerce's own stable public helpers, so the row set
        // and order match the real page exactly.
        if (empty($_GET['sortby']) && function_exists('WC') && WC()->query) {
            $ordering = WC()->query->get_catalog_ordering_args();
            $args['orderby'] = $ordering['orderby'];
            $args['order'] = $ordering['order'];
            if (!empty($ordering['meta_key'])) {
                $args['meta_key'] = $ordering['meta_key'];
            }
        }

        if (function_exists('wc_get_product_visibility_term_ids')) {
            $visibility_terms = wc_get_product_visibility_term_ids();
            $visibility_not_in = array_filter([
                $visibility_terms['exclude-from-catalog'] ?? 0,
                get_option('woocommerce_hide_out_of_stock_items') === 'yes' ? ($visibility_terms['outofstock'] ?? 0) : 0,
            ]);
            if (!empty($visibility_not_in)) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array_values($visibility_not_in),
                    'operator' => 'NOT IN',
                ];
            }
        }

        $original_wp_query     = $GLOBALS['wp_query'] ?? null;
        $original_wp_the_query = $GLOBALS['wp_the_query'] ?? null;

        $query = new WP_Query();
        $GLOBALS['wp_query'] = $query;
        $GLOBALS['wp_the_query'] = $query;

        $query->query($args);

        $GLOBALS['wp_query'] = $original_wp_query;
        $GLOBALS['wp_the_query'] = $original_wp_the_query;

        ob_start();
        foreach ($query->posts as $product_post) {
            $GLOBALS['post'] = $product_post;
            setup_postdata($product_post);
            echo '<div class="col-sm-6 col-md-4 col-xl-5th">';
            get_template_part_var('templates/card/card-product.php', ['post' => $product_post]);
            echo '</div>';
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        wp_send_json_success([
            'html'          => $html,
            'found_posts'   => (int) $query->found_posts,
            'max_num_pages' => (int) $query->max_num_pages,
            'current_page'  => $paged,
            'per_page'      => (int) $query->get('posts_per_page'),
        ]);
    }

    public function remove_product_features()
    {
        // حذف ویرایشگر متن
        remove_post_type_support('product', 'editor');
        // حذف بخش خلاصه
        remove_post_type_support('product', 'excerpt');
        // حذف برچسب‌ها
        unregister_taxonomy('product_tag');
    }
    public function remove_meta_boxes()
    {
        remove_meta_box('tagsdiv-product_tag', 'product', 'side');
        remove_meta_box('postexcerpt', 'product', 'normal');
        remove_meta_box('woocommerce-product-images', 'product', 'side');
        remove_meta_box('commentsdiv', 'product', 'normal');
        remove_meta_box('postcustom', 'product', 'normal');
    }

    public function remove_columns_from_product_list($columns)
    {
        // حذف ستون برچسب‌ها از جدول محصولات
        if (isset($columns['product_tag'])) {
            unset($columns['product_tag']);
        }
        return $columns;
    }

    public function remove_woocommerce_image_sizes()
    {
        // حذف اندازه‌های تصاویر ووکامرس
        remove_image_size('woocommerce_thumbnail');
        remove_image_size('woocommerce_single');
        remove_image_size('woocommerce_gallery_thumbnail');

        // حذف اندازه‌های تصاویر پیش‌فرض وردپرس
        remove_image_size('1536x1536');
        remove_image_size('2048x2048');
        remove_image_size('medium_large');
    }

    public function add_custom_dimension_unit($units)
    {
        $units['ft'] = __('Foot (ft)', 'woocommerce');
        return $units;
    }

    public static function hierarchical_attributes()
    {
        $hierarchical_attributes = ['pa_size']; // Specify your attribute slugs here.

        foreach ($hierarchical_attributes as $attribute) {
            // Check if the taxonomy exists.
            if (taxonomy_exists($attribute)) {
                global $wp_taxonomies;

                // Update the hierarchical property.
                if (isset($wp_taxonomies[$attribute])) {
                    $wp_taxonomies[$attribute]->hierarchical = true;
                }
            }
        }
    }

    // Facet
    public static $tax_map = [
        'design' => 'pa_design',
        'color'  => 'pa_color',
        'origin' => 'pa_origin',
        'size'   => 'pa_size',
    ];

    public static $query_vars = [
        'product_category',
        'design',
        'color',
        'origin',
        'size',
        'sortby',
        'min_price',
        'max_price'
    ];

    public static function add_query_vars($vars)
    {
        return array_merge($vars, self::$query_vars);
    }

    public static function decode_param($param)
    {
        return array_filter(array_map('sanitize_title', explode(',', urldecode($param))));
    }

    /**
     * Builds the tax_query / meta_query / orderby args implied by the current
     * $_GET filter state (color/design/origin/size/product_category/min_price/
     * max_price/sortby). Shared by handle_query() (applied to the main query on
     * normal page loads) and ajax_load_more_products() (applied to the manually
     * built WP_Query used by "Show More"), so both paths stay in sync.
     */
    public static function build_filter_query_args()
    {
        $args = [];
        $tax_query = [];

        foreach (self::$tax_map as $param => $taxonomy) {
            if (!empty($_GET[$param])) {
                $terms = self::decode_param($_GET[$param]);
                if (!empty($terms)) {
                    $tax_query[] = [
                        'taxonomy' => $taxonomy,
                        'field'    => 'slug',
                        'terms'    => $terms,
                    ];
                }
            }
        }

        if (!empty($_GET['product_category'])) {
            $cat = end(self::decode_param($_GET['product_category']));
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat,
                'include_children' => true,
            ];
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $meta_query = [];

        $min = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
        $max = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
        if ($min !== null && $max !== null && $min <= $max) {
            $meta_query[] = [
                'key'     => '_price',
                'value'   => [$min, $max],
                'type'    => 'NUMERIC',
                'compare' => 'BETWEEN',
            ];
        }

        $sortby = sanitize_key($_GET['sortby'] ?? '');
        $valid_sorting = ['featured', 'in_stock', 'lowest_price', 'highest_price', 'latest', 'most_viewed'];
        if (!in_array($sortby, $valid_sorting, true)) {
            $sortby = '';
        }

        switch ($sortby) {
            case 'featured':
                $meta_query[] = ['key' => '_featured', 'value' => 'yes'];
                break;
            case 'in_stock':
                $meta_query[] = ['key' => '_stock_status', 'value' => 'instock'];
                break;
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        switch ($sortby) {
            case 'lowest_price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'ASC';
                break;
            case 'highest_price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'DESC';
                break;
            case 'latest':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'most_viewed':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'post_views_count';
                $args['order'] = 'DESC';
                break;
        }

        return $args;
    }

    public static function handle_query($query)
    {
        if (!self::is_real_admin_request() && $query->is_main_query()) {
            $filter_args = self::build_filter_query_args();

            if (!empty($filter_args['tax_query'])) {
                $tax_query = array_merge($query->get('tax_query') ?: [], $filter_args['tax_query']);
                $query->set('tax_query', $tax_query);
            }

            if (!empty($filter_args['meta_query'])) {
                $meta_query = array_merge($query->get('meta_query') ?: [], $filter_args['meta_query']);
                $query->set('meta_query', $meta_query);
            }

            foreach (['orderby', 'meta_key', 'order'] as $key) {
                if (isset($filter_args[$key])) {
                    $query->set($key, $filter_args[$key]);
                }
            }
        }
    }

    public static function get_terms_for_current_query($taxonomy)
    {
        global $wp_query;

        $all_terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'parent'     => 0,
        ]);

        $is_filter_active = false;
        foreach (self::$query_vars as $var) {
            if (!empty($_GET[$var])) {
                $is_filter_active = true;
                break;
            }
        }

        $post_ids = wp_list_pluck($wp_query->posts, 'ID');

        if (!$is_filter_active || empty($post_ids)) {
            foreach ($all_terms as &$term) {
                $term->available = true;
            }
            return $all_terms;
        }

        $available_terms = wp_get_object_terms($post_ids, $taxonomy, ['fields' => 'ids']);

        foreach ($all_terms as &$term) {
            $term->available = in_array($term->term_id, $available_terms);
        }

        return $all_terms;
    }

    public static function get_min_max_prices()
    {
        global $wpdb;
        $row = $wpdb->get_row("SELECT MIN(CAST(pm.meta_value AS UNSIGNED)) as min_price, MAX(CAST(pm.meta_value AS UNSIGNED)) as max_price FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = '_price' AND p.post_status = 'publish' AND p.post_type = 'product'");
        return [
            'min_price' => floatval($row->min_price),
            'max_price' => floatval($row->max_price),
        ];
    }

    public function get_active_filters()
    {
        $query_string = $_SERVER['QUERY_STRING']; // دریافت پارامترهای URL
        parse_str($query_string, $params); // پارس کردن پارامترها به آرایه

        unset($params['post_type']);
        unset($params['sortby']);

        $filters = [];
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $values = explode(',', $value);
                foreach ($values as $val) {
                    $filters[] = [
                        'key' => $key,
                        'value' => $val
                    ];
                }
            }
        }

        return $filters;
    }

    /**
     * اعمال شرط برای افزودن فیلتر جستجوی عنوان و SKU
     */
    public function modify_search_query($query)
    {
        if (!is_admin() && $query->is_main_query() && $query->is_search()) {
            if (isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
                add_filter('posts_search', [$this, 'filter_search_by_title_and_sku'], 10, 2);
            }
        }
    }

    /**
     * فیلتر جستجو برای اعمال محدودیت به عنوان و SKU
     */
    public function filter_search_by_title_and_sku($search, $query)
    {
        global $wpdb;

        $search_term = $query->get('s');

        if (empty($search_term)) {
            return $search;
        }

        // حذف شرط پیش‌فرض وردپرس
        $search = '';

        // اعمال جستجو فقط بر اساس عنوان و SKU
        $search .= " AND (";
        $search .= $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($search_term) . '%');
        $search .= " OR EXISTS (
			SELECT 1 FROM {$wpdb->postmeta}
			WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
			AND {$wpdb->postmeta}.meta_key = '_sku'
			AND {$wpdb->postmeta}.meta_value LIKE " . $wpdb->prepare('%s', '%' . $wpdb->esc_like($search_term) . '%') . "
		)";
        $search .= ")";

        return $search;
    }
}

class DisableVirtual extends Shop
{

    public function __construct()
    {

        $this->register();
    }

    /**
     * Register all hooks and filters
     */
    public function register()
    {
        add_action('init', [$this, 'remove_virtual_and_downloadable_support']);
        add_action('woocommerce_product_options_general_product_data', [$this, 'remove_virtual_and_downloadable_options']);
        add_filter('product_type_options', [$this, 'filter_product_type_options']);
        add_filter('woocommerce_products_admin_list_table_filters', [$this, 'modify_product_type_filter']);
        add_filter('woocommerce_product_data_tabs', [$this, 'remove_product_data_tabs']);
        add_filter('woocommerce_product_filters', [$this, 'remove_product_filters']);
        add_filter('posts_clauses', [$this, 'exclude_virtual_and_downloadable_from_search'], 10, 2);
        add_action('admin_menu', [$this, 'remove_meta_boxes']);
        add_action('woocommerce_order_get_items', [$this, 'filter_order_items'], 10, 2);
        add_action('woocommerce_admin_order_data_after_order_details', [$this, 'remove_download_and_virtual_meta_boxes_from_order']);
        add_action('woocommerce_customer_meta_fields', [$this, 'remove_virtual_download_meta_fields']);
        add_filter('woocommerce_get_settings_pages', [$this, 'remove_virtual_download_settings']);
        add_action('widgets_init', [$this, 'remove_virtual_download_widgets']);
        add_filter('woocommerce_reports_charts', [$this, 'remove_virtual_download_reports']);
        add_filter('woocommerce_email_classes', [$this, 'remove_virtual_download_emails']);
        add_filter('woocommerce_rest_prepare_product_object', [$this, 'exclude_virtual_download_products_from_api'], 10, 2);
        add_filter('woocommerce_coupon_get_product_ids', [$this, 'filter_coupons_for_virtual_download_products']);
        add_filter('woocommerce_account_menu_items', [$this, 'remove_virtual_download_my_account_items']);
    }

    /**
     * Remove support for virtual and downloadable products
     */
    public function remove_virtual_and_downloadable_support()
    {
        add_filter('product_type_selector', function ($types) {
            unset($types['virtual']);
            unset($types['downloadable']);
            unset($types['grouped']);
            unset($types['external']);
            return $types;
        });
    }

    /**
     * Remove virtual and downloadable checkboxes from product edit page
     */
    public function remove_virtual_and_downloadable_options()
    {
        add_action('woocommerce_product_options_general_product_data', function () {
            remove_action('woocommerce_product_options_general_product_data', 'woocommerce_product_options_downloadable'); // Remove downloadable option
            remove_action('woocommerce_product_options_general_product_data', 'woocommerce_product_options_virtual');      // Remove virtual option
        });

        // Completely unset the options from the product object
        add_filter('woocommerce_product_class', function ($classname, $product_type) {
            return $classname;
        }, 10, 2);

        // Remove virtual and downloadable options from the save process
        add_filter('woocommerce_process_product_meta', function ($post_id) {
            delete_post_meta($post_id, '_virtual');
            delete_post_meta($post_id, '_downloadable');
        });
    }

    /**
     * Remove virtual and downloadable checkboxes from product edit page
     */
    public function filter_product_type_options($options)
    {
        unset($options['virtual']);
        unset($options['downloadable']);
        return $options;
    }

    /**
     * Modify the product type filter
     */
    public function modify_product_type_filter($filters)
    {
        if (isset($filters['product_type'])) {
            $filters['product_type'] = [$this, 'product_type_callback'];
        }
        return $filters;
    }

    /**
     * Display the product type filter dropdown
     */
    public function product_type_callback()
    {
        $current_type = isset($_REQUEST['product_type']) ? wc_clean(wp_unslash($_REQUEST['product_type'])) : false;
?>
        <select name="product_type" id="dropdown_product_type">
            <option value=""><?php esc_html_e('Filter by product type', LANG_STRING); ?></option>
            <?php foreach (wc_get_product_types() as $value => $label) : ?>
                <option value="<?= esc_attr($value) ?>" <?php selected($value, $current_type) ?>>
                    <?= esc_html($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
<?php
    }

    /**
     * Remove virtual and downloadable tabs from product data metabox
     */
    public function remove_product_data_tabs($tabs)
    {
        unset($tabs['linked_product']);
        unset($tabs['advanced']);
        return $tabs;
    }

    /**
     * Remove virtual and downloadable filters from admin products list
     */
    public function remove_product_filters($filters)
    {
        if (isset($filters['product_type'])) {
            unset($filters['product_type']['virtual']);
            unset($filters['product_type']['downloadable']);
        }
        return $filters;
    }

    /**
     * Exclude virtual and downloadable products from search
     */
    public function exclude_virtual_and_downloadable_from_search($clauses, $query)
    {
        if (!is_admin() && $query->is_search) {
            global $wpdb;
            $clauses['where'] .= " AND ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ('_virtual', '_downloadable') AND meta_value = 'yes')";
        }
        return $clauses;
    }

    /**
     * Remove meta boxes related to virtual and downloadable products
     */
    public function remove_meta_boxes()
    {
        remove_meta_box('woocommerce-product-downloads', 'product', 'normal');
        remove_meta_box('woocommerce-product-shipping', 'product', 'normal');
    }

    /**
     * Filter order items to exclude virtual and downloadable products
     */
    public function filter_order_items($items, $order)
    {
        foreach ($items as $key => $item) {
            $product = $item->get_product();
            if ($product && ($product->is_virtual() || $product->is_downloadable())) {
                unset($items[$key]);
            }
        }
        return $items;
    }


    /**
     * Remove meta boxes related to virtual and downloadable products from order details
     */
    public function remove_download_and_virtual_meta_boxes_from_order($order)
    {
        remove_meta_box('woocommerce-order-downloads', 'shop_order', 'normal');
    }

    /**
     * Remove virtual and downloadable related fields from customer profile
     */
    public function remove_virtual_download_meta_fields($fields)
    {
        if (isset($fields['billing']['billing_virtual'])) {
            unset($fields['billing']['billing_virtual']);
        }
        if (isset($fields['billing']['billing_downloadable'])) {
            unset($fields['billing']['billing_downloadable']);
        }
        return $fields;
    }

    /**
     * Remove settings related to virtual and downloadable products from WooCommerce settings pages
     */
    public function remove_virtual_download_settings($settings)
    {
        foreach ($settings as $key => $page) {
            if (is_object($page) && method_exists($page, 'get_id') && in_array($page->get_id(), ['downloads', 'virtual_products'])) {
                unset($settings[$key]);
            }
        }
        return $settings;
    }

    /**
     * Remove widgets related to virtual and downloadable products
     */
    public function remove_virtual_download_widgets()
    {
        unregister_widget('WC_Widget_Product_Categories');
        unregister_widget('WC_Widget_Recent_Products');
    }

    /**
     * Remove virtual and downloadable products from reports
     */
    public function remove_virtual_download_reports($reports)
    {
        if (isset($reports['orders']['reports']['downloadable_products'])) {
            unset($reports['orders']['reports']['downloadable_products']);
        }
        return $reports;
    }

    /**
     * Remove emails related to virtual and downloadable products
     */
    public function remove_virtual_download_emails($email_classes)
    {
        unset($email_classes['WC_Email_Customer_Downloadable_Product']);
        return $email_classes;
    }

    /**
     * Exclude virtual and downloadable products from REST API responses
     */
    public function exclude_virtual_download_products_from_api($response, $object)
    {
        if ($object->is_virtual() || $object->is_downloadable()) {
            return null;
        }
        return $response;
    }

    /**
     * Exclude virtual and downloadable products from coupons
     */
    public function filter_coupons_for_virtual_download_products($product_ids)
    {
        foreach ($product_ids as $key => $product_id) {
            $product = wc_get_product($product_id);
            if ($product && ($product->is_virtual() || $product->is_downloadable())) {
                unset($product_ids[$key]);
            }
        }
        return $product_ids;
    }

    /**
     * Remove virtual and downloadable items from My Account menu
     */
    public function remove_virtual_download_my_account_items($items)
    {
        unset($items['downloads']);
        return $items;
    }
}
