<?php

namespace App\Controller;

use WP_Query;
use WP_Error;
use WP_Term;

class Shop
{
    public $post_name = 'product';

    /**
     * Single source of truth for the product-archive/category/tag grid's
     * per-page count. Set once here in change_default_query(); every other
     * reader (ajax_load_more_products()'s JSON response, product-grid.php's
     * data-per-page attribute, and from there shop.js) reads it back off the
     * live WP_Query rather than hardcoding its own copy, so changing this one
     * constant is enough to keep normal page loads and "Show More" in sync.
     */
    const PRODUCTS_PER_PAGE = 40;

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

        add_action('wp_ajax_' . 'qali_next_filter_suggestion', [$this, 'ajax_next_filter_suggestion']);
        add_action('wp_ajax_' . 'nopriv_qali_next_filter_suggestion', [$this, 'ajax_next_filter_suggestion']);

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

        // Chained attribute-archive URLs (/origin/tabriz/color/red/…)
        add_action('init', [$this, 'register_chain_rewrite_rule']);
        add_action('parse_request', [$this, 'parse_attribute_chain']);
        add_filter('wpseo_title', [$this, 'chain_title'], 10, 1);
        add_filter('wpseo_breadcrumb_links', [$this, 'chain_breadcrumb_links']);
        add_filter('wpseo_canonical', [$this, 'chain_canonical']);
        add_filter('wpseo_robots_array', [$this, 'chain_robots']);

        // Category + attribute chained URLs (/product-category/colorful-vintage/origin/tabriz/…) —
        // separate rewrite rule/query vars from the attribute-only chain above so the two parsers
        // never both fire on the same request (see parse_category_attribute_chain()'s docblock).
        add_action('init', [$this, 'register_category_chain_rewrite_rule']);
        add_action('parse_request', [$this, 'parse_category_attribute_chain']);

        // A newly add_rewrite_rule()'d pattern has no effect until the cached `rewrite_rules`
        // option is regenerated — normally done by re-saving Permalinks in wp-admin, which this
        // environment has no credentialed access to trigger. Self-flushing once, on the next real
        // request after this file deploys, avoids needing that: priority 20 (after the 'init'
        // rewrite-rule registrations above, both still default priority 10) so the new rule is
        // already registered when the flush runs; the version-gated option makes it a no-op on
        // every request after the first. Bump the version string whenever a rewrite rule changes.
        add_action('init', [$this, 'maybe_flush_rewrite_rules'], 20);
    }

    public function maybe_flush_rewrite_rules()
    {
        $version = '2026-09-06-category-chain';
        if (get_option('qali_rewrite_rules_version') !== $version) {
            flush_rewrite_rules();
            update_option('qali_rewrite_rules_version', $version);
        }
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

    /**
     * ajax_load_more_products() spoofs is_main_query() so this still fires for "Show More" —
     * but that spoofed query always satisfies is_post_type_archive('product') regardless of what
     * archive the visitor is actually paging through, so without this override every "Show More"
     * fetch would get self::PRODUCTS_PER_PAGE (40) even on a pa_* attribute archive, which was
     * never added to this gate below and so really renders at WooCommerce's own default per-page
     * (16 on this site). That per-page mismatch made page 2 of a "Show More" fetch land past the
     * (wrongly-assumed) last page and return nothing. ajax_load_more_products() sets this to
     * whatever per-page the real page actually used before running its query; every other request
     * leaves it null and gets the normal self::PRODUCTS_PER_PAGE behavior, unchanged.
     */
    public static $ajax_per_page_override = null;

    public function change_default_query($query)
    {
        if (!self::is_real_admin_request() && ($query->is_post_type_archive('product') || $query->is_tax('product_cat') || $query->is_tax('product_tag')) && $query->is_main_query()) {
            $query->set('posts_per_page', self::$ajax_per_page_override ?? self::PRODUCTS_PER_PAGE);

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

        // Every active pa_* attribute constraint for the page "Show More" was clicked on — both a
        // plain single-attribute archive (/origin/tabriz/) and a chained one (/origin/tabriz/color/red/)
        // — sent by product-grid.php/shop.js as JSON (see data-archive-pa-filters). This admin-ajax.php
        // request never runs parse_attribute_chain() itself (that's a 'parse_request' hook, and this
        // is a plain ajax action), so without this the pa_* filter(s) would silently be dropped on
        // page 2+. Feeding it into self::$chain_extra_tax reuses build_filter_query_args()'s existing
        // tax_query merge below via the is_main_query() spoof — no second tax_query builder.
        // Match whatever per-page the real page actually rendered at (see self::$ajax_per_page_override) —
        // required for pa_* attribute archives, whose real per-page (WooCommerce's own default) differs
        // from self::PRODUCTS_PER_PAGE; harmless for category/tag/shop pages, which already match today.
        if (isset($_GET['per_page'])) {
            $requested_per_page = (int) $_GET['per_page'];
            if ($requested_per_page > 0 && $requested_per_page <= 100) {
                self::$ajax_per_page_override = $requested_per_page;
            }
        }

        $pa_filters = json_decode(stripslashes((string) ($_GET['archive_pa_filters'] ?? '')), true);
        if (is_array($pa_filters)) {
            foreach ($pa_filters as $filter) {
                $taxonomy = sanitize_key($filter['taxonomy'] ?? '');
                $slug     = sanitize_title($filter['slug'] ?? '');
                if ($taxonomy && $slug && taxonomy_exists($taxonomy) && in_array($taxonomy, self::$tax_map, true)) {
                    self::$chain_extra_tax[$taxonomy][] = $slug;
                }
            }
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
            get_template_part_var('templates/card/card-product.php', ['post' => $product_post, 'no_animate' => true]);
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
        'design'    => 'pa_design',
        'color'     => 'pa_color',
        'origin'    => 'pa_origin',
        'size'      => 'pa_size',
        'feel'      => 'pa_feel',
        'material'  => 'pa_material',
        'shape'     => 'pa_shape',
        'thickness' => 'pa_thickness',
    ];

    public static $query_vars = [
        'product_category',
        'design',
        'color',
        'origin',
        'size',
        'feel',
        'material',
        'shape',
        'thickness',
        'sortby',
        'min_price',
        'max_price'
    ];

    /**
     * The 8 attribute rewrite-slug bases, matching wp_woocommerce_attribute_taxonomies.attribute_name
     * (confirmed 1:1 with self::$tax_map's keys) — used to build the chained-URL rewrite rule below.
     */
    public static $attribute_bases = ['color', 'design', 'feel', 'material', 'origin', 'shape', 'size', 'thickness'];

    /**
     * Populated once per request by parse_attribute_chain(), only when a chained attribute
     * URL (/origin/tabriz/color/red/…) is actually being visited. Ordered exactly as the
     * segments appeared in the URL; each entry is ['base'=>'origin','taxonomy'=>'pa_origin',
     * 'slug'=>'tabriz','term'=>WP_Term]. Stays empty for every other request, including the
     * existing single-attribute archive pages — that's what keeps this purely additive.
     */
    public static $chain_terms = [];

    /**
     * taxonomy => [slug, ...] for every chain segment PAST the first (the first segment is set
     * as the native pa_{base} query var instead, so WP_Query/is_tax()/Yoast's indexable all see
     * a real single-term archive query, same as today). build_filter_query_args() merges this in
     * alongside its existing $_GET-based tax_query — the one shared call site for both.
     */
    public static $chain_extra_tax = [];

    /**
     * Priority order for the "suggested next filter" chip row (see get_next_filter_suggestion()):
     * the first dimension in this list NOT already active in the current URL is the one suggested.
     * Reduced from all 8 attribute dimensions to these 3 per explicit user direction — design,
     * material, feel, thickness, and (as of this list) size should never be suggested as a chip,
     * though they remain fully functional as sidebar filters (size in particular is a normal
     * sidebar <select>) and in manually-typed chain URLs; only this chip-suggestion priority list
     * is narrowed. A fully-chained origin+color+shape page naturally yields an empty $remaining
     * in get_next_filter_suggestion() and renders no chip row — no separate depth-cap constant
     * needed, the 3-element list itself is the cap.
     */
    const NEXT_FILTER_PRIORITY = ['origin', 'color', 'shape'];

    /** Cap on how many candidate-term chips are rendered for the suggested dimension. */
    const NEXT_FILTER_CHIP_CAP = 12;

    public static function attribute_taxonomies()
    {
        return array_values(self::$tax_map);
    }

    /**
     * The ordered, path-based filter currently active for this request — a single native
     * archive term (1 entry, for a plain /origin/tabriz/ or /product-category/colorful-vintage/
     * page), a full pa_*-only chain (2+ entries, from self::$chain_terms, for
     * /origin/tabriz/color/red/), or a category+attribute chain (1+ entries, from
     * self::$category_chain_terms, for /product-category/colorful-vintage/origin/tabriz/).
     * Empty only on a genuinely unrelated page (shop, a non-product page, etc.).
     */
    public static function get_active_path_bases()
    {
        if (!empty(self::$category_chain_terms)) {
            return self::$category_chain_terms;
        }

        if (!empty(self::$chain_terms)) {
            return self::$chain_terms;
        }

        if (is_tax('product_cat')) {
            $term = get_queried_object();
            if ($term instanceof WP_Term) {
                return [['base' => 'product-category', 'taxonomy' => 'product_cat', 'slug' => $term->slug, 'term' => $term]];
            }
        }

        if (is_tax(self::attribute_taxonomies())) {
            $term = get_queried_object();
            if ($term instanceof WP_Term) {
                $base = array_search($term->taxonomy, self::$tax_map, true);
                if ($base !== false) {
                    return [['base' => $base, 'taxonomy' => $term->taxonomy, 'slug' => $term->slug, 'term' => $term]];
                }
            }
        }

        return [];
    }

    /**
     * Computes the "suggested next filter" chip row for a given active path-based filter chain
     * (single or chained — pass self::get_active_path_bases() for a real page load; the "Skip"
     * AJAX handler below reconstructs the same shape from what the client sends, since that's a
     * separate request with no page context of its own, same reason ajax_load_more_products()
     * needs archive_pa_filters instead of relying on self::$chain_terms): the first not-yet-active
     * dimension in self::NEXT_FILTER_PRIORITY (skipping any base named in $skip_bases too, for the
     * "Skip" UI), its candidate terms each counted against $active + that term (reusing
     * build_filter_query_args()'s tax_query for the $_GET-driven part, same single source of truth
     * as everywhere else), zero-result terms excluded, capped at self::NEXT_FILTER_CHIP_CAP terms
     * with the most products first.
     *
     * Returns null when $active is empty (product_cat, shop, etc. should pass []), when every
     * dimension is already active, or — recursing past it — when a dimension turns out to have
     * fewer than 2 viable candidates against the current chain (zero is a dead end same as
     * before; exactly 1 is now treated the same way, since a lone chip with no alternative gives
     * the visitor no real choice and isn't useful navigation).
     */
    public static function get_next_filter_suggestion($active, $skip_bases = [])
    {
        if (empty($active)) {
            return null;
        }

        $active_bases = array_column($active, 'base');
        $remaining = array_values(array_diff(self::NEXT_FILTER_PRIORITY, $active_bases, $skip_bases));
        if (empty($remaining)) {
            return null;
        }

        $next_base = $remaining[0];
        $taxonomy  = self::$tax_map[$next_base];

        $active_clauses = [];
        foreach ($active as $entry) {
            $active_clauses[] = ['taxonomy' => $entry['taxonomy'], 'field' => 'slug', 'terms' => [$entry['slug']]];
        }
        $extra_args = self::build_filter_query_args();
        if (!empty($extra_args['tax_query'])) {
            $active_clauses = array_merge($active_clauses, $extra_args['tax_query']);
        }

        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
        $candidates = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $q = new WP_Query([
                    'post_type'      => 'product',
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                    'no_found_rows'  => false,
                    'tax_query'      => array_merge($active_clauses, [
                        ['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => [$term->slug]],
                    ]),
                ]);
                if ((int) $q->found_posts > 0) {
                    $candidates[] = ['term' => $term, 'count' => (int) $q->found_posts];
                }
            }
        }

        if (count($candidates) < 2) {
            return self::get_next_filter_suggestion($active, array_merge($skip_bases, [$next_base]));
        }

        usort($candidates, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $candidates = array_slice($candidates, 0, self::NEXT_FILTER_CHIP_CAP);

        $path_parts = [];
        foreach ($active as $entry) {
            $path_parts[] = $entry['base'];
            $path_parts[] = $entry['slug'];
        }

        $chips = [];
        foreach ($candidates as $c) {
            $chip = [
                'name'  => $c['term']->name,
                'count' => $c['count'],
                'url'   => home_url('/' . implode('/', array_merge($path_parts, [$next_base, $c['term']->slug])) . '/'),
            ];

            if ($next_base === 'color') {
                $chip['swatch'] = self::color_swatch_for_term($c['term']);
            } elseif ($next_base === 'shape') {
                $chip['shape_slug'] = $c['term']->slug;
            } elseif ($next_base === 'origin') {
                $chip['thumbnail_url'] = self::origin_chip_thumbnail_url($c['term']);
            }

            $chips[] = $chip;
        }

        return [
            'base'  => $next_base,
            'label' => wc_attribute_label('pa_' . $next_base),
            'chips' => $chips,
        ];
    }

    /**
     * Resolves the swatch shown on a color-dimension chip. `pa_color` terms already carry a
     * curated hex value via the Meta Box "Color" term-meta field (id `color` — the same one the
     * sidebar color filter's own swatch dots already read, see header-shop.php's `$color_hex`) —
     * reused as-is when present, never re-guessed. Only a term with no curated value at all falls
     * through to a name-based guess (flagged via `guessed => true`), so any real gap is easy to
     * find and fix later by filling in that same Meta Box field.
     */
    public static function color_swatch_for_term($term)
    {
        $hex = get_term_meta($term->term_id, 'color', true);
        if ($hex) {
            return ['hex' => $hex, 'guessed' => false];
        }

        // "Multicolored" has no single representative hue — a small gradient reads correctly
        // where a flat guessed color would be actively misleading.
        if (strpos($term->slug, 'multicolor') !== false) {
            return [
                'gradient' => 'conic-gradient(from 0deg, #C0392B, #E1B12C, #27AE60, #2980B9, #8E44AD, #C0392B)',
                'guessed'  => true,
            ];
        }

        $guesses = [
            'red' => '#C0392B', 'blue' => '#2980B9', 'green' => '#27AE60', 'black' => '#1C1C1C',
            'white' => '#F6F0E6', 'grey' => '#808080', 'gray' => '#808080', 'brown' => '#8B5E3C',
            'beige' => '#D8C7A1', 'pink' => '#E8A0BF', 'purple' => '#8E44AD', 'yellow' => '#E1B12C',
            'orange' => '#D9822B', 'turquoise' => '#3FBFB0', 'olive' => '#7C7A3B', 'navy' => '#1B3358',
            'cream' => '#F1E7D0', 'gold' => '#C9A24B', 'ivory' => '#F4EEDF', 'charcoal' => '#3A3A3A',
        ];
        foreach ($guesses as $needle => $hex_guess) {
            if (strpos($term->slug, $needle) !== false) {
                return ['hex' => $hex_guess, 'guessed' => true];
            }
        }

        return ['hex' => '#B7A99A', 'guessed' => true];
    }

    /** Term meta key caching the chosen representative-product thumbnail attachment ID per pa_origin term. */
    const ORIGIN_THUMB_META_KEY = 'next_filter_thumbnail_id';

    /**
     * Resolves the circular photo thumbnail shown on an origin-dimension chip. There is no
     * curated "representative image per origin" field anywhere in this theme (only `pa_design`
     * has a manually-uploaded term image) — so this auto-selects one from real product data and
     * caches the chosen attachment ID in term meta, so every origin term's thumbnail is computed
     * at most once ever (until the cached attachment is deleted), not on every page load.
     */
    public static function origin_chip_thumbnail_url($term)
    {
        $cached_id = (int) get_term_meta($term->term_id, self::ORIGIN_THUMB_META_KEY, true);
        if ($cached_id > 0) {
            $url = wp_get_attachment_image_url($cached_id, 'thumbnail');
            if ($url) {
                return $url;
            }
        }

        $thumb_id = self::find_representative_product_thumbnail($term->taxonomy, $term->slug);
        update_term_meta($term->term_id, self::ORIGIN_THUMB_META_KEY, $thumb_id);

        return $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';
    }

    /**
     * Best-selling product in the term first (`total_sales` postmeta — WP_Query's own
     * `meta_value_num` ordering requires the meta key to exist, so a term whose products have
     * never sold legitimately returns zero rows here, not an error); falls back to the term's
     * most recent product otherwise. Either way, skips forward past any candidate with no
     * featured image rather than caching a blank thumbnail for the whole term.
     */
    private static function find_representative_product_thumbnail($taxonomy, $slug)
    {
        $by_sales = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 5,
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'total_sales',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'tax_query'      => [['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => [$slug]]],
        ]);
        foreach ($by_sales->posts as $post_id) {
            $thumb_id = get_post_thumbnail_id($post_id);
            if ($thumb_id) {
                return (int) $thumb_id;
            }
        }

        $by_date = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'tax_query'      => [['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => [$slug]]],
        ]);
        foreach ($by_date->posts as $post_id) {
            $thumb_id = get_post_thumbnail_id($post_id);
            if ($thumb_id) {
                return (int) $thumb_id;
            }
        }

        return 0;
    }

    /**
     * Inline line-art icon for a shape-dimension chip, in the same stroke-based style as this
     * theme's existing line icons (see assets/img/icon-close.svg: fill="none", stroke-width="2",
     * round caps/joins) — except `stroke="currentColor"` instead of a hardcoded gray, the one
     * deviation from that file's convention, needed so CSS can flip the icon to white on the
     * chip's existing red hover state the same way its text already does. Inlined directly
     * (this theme otherwise always references icons via `<img src>`) rather than as an uploaded
     * per-term asset, since the shape set is small, fixed, and known ('rectangle', 'square',
     * 'runner', 'round', 'oval' — confirmed against the live `pa_shape` terms). An unrecognized
     * future shape term just renders with no icon rather than a guessed placeholder.
     */
    public static function shape_chip_icon_svg($slug)
    {
        $shapes = [
            'rectangle' => '<rect x="4" y="8" width="20" height="12" rx="1.5"/>',
            'square'    => '<rect x="6" y="6" width="16" height="16" rx="1.5"/>',
            'runner'    => '<rect x="9" y="3" width="10" height="22" rx="1.5"/>',
            'round'     => '<circle cx="14" cy="14" r="10"/>',
            'oval'      => '<ellipse cx="14" cy="14" rx="11" ry="7"/>',
        ];

        if (!isset($shapes[$slug])) {
            return '';
        }

        return '<svg width="20" height="20" viewBox="0 0 28 28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
            . $shapes[$slug]
            . '</svg>';
    }

    /**
     * AJAX handler for the "Skip" link — returns the next remaining dimension's chip row (or an
     * empty one if none left, so the client hides the whole block) without changing the page URL.
     *
     * This is a plain admin-ajax.php-style request with no page context of its own (same reason
     * ajax_load_more_products() needs its own archive_pa_filters param) — the page's active path
     * chain is reconstructed here from the `active` JSON param the client sends (the same one the
     * initial render exposed via the chip row's own data-active attribute), not from
     * self::get_active_path_bases()/self::$chain_terms, which only ever reflect a real page load.
     */
    public function ajax_next_filter_suggestion()
    {
        $skip_raw   = sanitize_text_field($_GET['skip'] ?? '');
        $skip_bases = array_values(array_filter(array_map('sanitize_key', explode(',', $skip_raw))));

        $active_raw = json_decode(stripslashes((string) ($_GET['active'] ?? '')), true);
        $active = [];
        if (is_array($active_raw)) {
            foreach ($active_raw as $entry) {
                $base = sanitize_key($entry['base'] ?? '');
                $slug = sanitize_title($entry['slug'] ?? '');
                if ($base === '' || $slug === '') {
                    continue;
                }
                // 'product-category' isn't a self::$tax_map key (it's product_cat, not a pa_*
                // attribute) — same special-case as get_active_path_bases()/get_next_filter_suggestion().
                $taxonomy = ($base === 'product-category') ? 'product_cat' : (self::$tax_map[$base] ?? null);
                if (!$taxonomy || !get_term_by('slug', $slug, $taxonomy)) {
                    continue;
                }
                $active[] = ['base' => $base, 'taxonomy' => $taxonomy, 'slug' => $slug];
            }
        }

        $suggestion = self::get_next_filter_suggestion($active, $skip_bases);

        ob_start();
        get_template_part_var('templates/shop/next-filter-chips.php', ['suggestion' => $suggestion, 'skipped' => $skip_bases, 'active' => $active]);
        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'base' => $suggestion['base'] ?? null,
        ]);
    }

    public static function add_query_vars($vars)
    {
        return array_merge($vars, self::$query_vars, [
            'qali_chain_base', 'qali_chain_slug', 'qali_chain_rest',
            'qali_cat_chain_slug', 'qali_cat_chain_base', 'qali_cat_chain_term', 'qali_cat_chain_rest',
        ]);
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

        // Chained attribute-archive URL segments past the first one (see parse_attribute_chain()).
        // Same tax_query shape as the $_GET loop above, so this is the one place both the sidebar
        // filters and the chained URLs build their tax_query — no second/duplicate builder.
        foreach (self::$chain_extra_tax as $taxonomy => $slugs) {
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => array_values(array_unique($slugs)),
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

    /**
     * Registers ONE rewrite rule for every chained attribute-archive URL, in any order
     * (/origin/tabriz/color/red/, /color/red/origin/tabriz/, a 3+ way chain, etc.).
     *
     * Deliberately requires a 3rd path segment ("(.+)$" after the first base/slug pair) so this
     * can never match a plain single-attribute URL like /origin/tabriz/ (that regex would need
     * an empty match there, which "(.+)" — one-or-more chars — cannot satisfy). That keeps this
     * rule additive by construction: WordPress's own generated pa_* rules (see class-wc-post-types.php
     * register_taxonomies()) are untouched and still win for every existing single-attribute URL,
     * regardless of rule ordering.
     */
    public function register_chain_rewrite_rule()
    {
        $bases = implode('|', array_map('preg_quote', self::$attribute_bases));
        add_rewrite_rule(
            '^(' . $bases . ')/([^/]+)/(.+)$',
            'index.php?qali_chain_base=$matches[1]&qali_chain_slug=$matches[2]&qali_chain_rest=$matches[3]',
            'top'
        );
    }

    /**
     * Resolves a matched chain rewrite (see register_chain_rewrite_rule()) into real terms.
     *
     * The first base/slug pair is turned into the native pa_{base} query var, so the rest of
     * WordPress/WooCommerce/Yoast treats this exactly like an existing single-attribute archive
     * page (real is_tax(), real queried object, WC_Query's own pre_get_posts hooks all still
     * fire) — every segment after that is stashed in self::$chain_extra_tax for
     * build_filter_query_args() to fold into the same tax_query it already builds.
     *
     * Any malformed or unresolvable chain (unknown base, odd segment count, duplicate base,
     * or a term slug that doesn't exist in the specific taxonomy its base names — e.g. the
     * pa_shape/pa_size "runner" collision is resolved by which base segment it appeared under,
     * never guessed) is routed to a real 404 rather than silently serving the wrong products.
     */
    public function parse_attribute_chain($wp)
    {
        if (empty($wp->query_vars['qali_chain_base'])) {
            return;
        }

        $chain_base = sanitize_key($wp->query_vars['qali_chain_base']);
        $chain_slug = sanitize_title(urldecode((string) $wp->query_vars['qali_chain_slug']));
        $rest_raw   = trim((string) ($wp->query_vars['qali_chain_rest'] ?? ''), '/');

        unset($wp->query_vars['qali_chain_base'], $wp->query_vars['qali_chain_slug'], $wp->query_vars['qali_chain_rest']);

        /**
         * Strip a trailing "/page/N" pagination segment before validating the rest as base/slug
         * pairs. Without this, "page"/"N" gets misread as an unknown attribute base and 404s —
         * which broke not just chained pagination (/origin/tabriz/color/red/page/2/) but, since
         * this rule's 'top' priority is tried before WooCommerce's own dedicated page/N rewrite
         * rule, EVERY single-attribute archive's own reload/deep-link of its "Show More" pushState
         * URL too (/origin/tabriz/page/2/) — the exact same shop.js pushPageUrl() every archive
         * type relies on. Found via Playwright while verifying chained "Show More".
         */
        $paged = null;
        if (preg_match('#^(.*?)/?page/([0-9]+)$#', $rest_raw, $page_match)) {
            $paged = max(1, (int) $page_match[2]);
            $rest_raw = trim($page_match[1], '/');
        }

        $pairs = [[$chain_base, $chain_slug]];

        if ($rest_raw !== '') {
            $segments = array_values(array_filter(explode('/', $rest_raw), function ($s) {
                return $s !== '';
            }));
            if (count($segments) === 0 || count($segments) % 2 !== 0) {
                $this->send_404($wp);
                return;
            }
            for ($i = 0; $i < count($segments); $i += 2) {
                $pairs[] = [sanitize_key($segments[$i]), sanitize_title(urldecode($segments[$i + 1]))];
            }
        }

        $seen_bases = [];
        $resolved   = [];
        foreach ($pairs as [$base, $slug]) {
            if ($slug === '' || !isset(self::$tax_map[$base]) || isset($seen_bases[$base])) {
                $this->send_404($wp);
                return;
            }
            $seen_bases[$base] = true;

            $taxonomy = self::$tax_map[$base];
            $term     = get_term_by('slug', $slug, $taxonomy);
            if (!$term || is_wp_error($term)) {
                $this->send_404($wp);
                return;
            }

            $resolved[] = ['base' => $base, 'taxonomy' => $taxonomy, 'slug' => $slug, 'term' => $term];
        }

        // First segment: make it a real native attribute-archive query, same as visiting it alone.
        $first = $resolved[0];
        $wp->query_vars[$first['taxonomy']] = $first['slug'];

        if ($paged !== null) {
            $wp->query_vars['paged'] = $paged;
        }

        // Only a genuine 2+ chain populates self::$chain_terms — a URL that turned out to be just
        // "base/slug/page/N" (no real second filter) is a plain single-attribute archive page with
        // pagination, same as visiting it without the rewrite rule at all, and every consumer of
        // self::$chain_terms already gates on count() >= 2, so this keeps that invariant exact.
        if (count($resolved) >= 2) {
            self::$chain_terms = $resolved;
            foreach (array_slice($resolved, 1) as $extra) {
                self::$chain_extra_tax[$extra['taxonomy']][] = $extra['slug'];
            }
        }
    }

    /**
     * Category + one-or-more attribute segments (/product-category/{slug}/{attr-base}/{attr-slug}/…/),
     * populated only when register_category_chain_rewrite_rule()'s pattern actually matched.
     * Unlike the pa_*-only chain above, a *single* trailing attribute pair is already a complete,
     * valid chain here (there's no separate native WooCommerce rule for
     * "category + one attribute" to fall back to the way a plain /origin/tabriz/ falls back to
     * its own native rule) — see register_category_chain_rewrite_rule()'s regex.
     */
    public static $category_chain_terms = [];

    public function register_category_chain_rewrite_rule()
    {
        $bases = implode('|', array_map('preg_quote', self::$attribute_bases));
        add_rewrite_rule(
            '^product-category/([^/]+)/(' . $bases . ')/([^/]+)/?(.*)$',
            'index.php?qali_cat_chain_slug=$matches[1]&qali_cat_chain_base=$matches[2]&qali_cat_chain_term=$matches[3]&qali_cat_chain_rest=$matches[4]',
            'top'
        );
    }

    /**
     * Resolves a matched category-chain rewrite into a real product_cat term plus one or more
     * real attribute terms. Deliberately uses its own query var names (qali_cat_chain_*, not
     * parse_attribute_chain()'s qali_chain_*) so the two `parse_request` handlers never both act
     * on the same request — this one's URL always starts with the literal "product-category/"
     * prefix, which register_chain_rewrite_rule()'s pattern (first segment must be one of the 8
     * attribute base words) can never match, and vice versa.
     *
     * The category becomes the real native `product_cat` query var (same trick
     * parse_attribute_chain() uses for pa_{base} — WP_Query/is_tax()/WC_Query's own hooks and the
     * existing category H1/description branch in header-shop.php all see a genuine category
     * archive query, unchanged), so this is purely additive: a plain `/product-category/{slug}/`
     * URL never matches this rule's pattern at all and keeps using WooCommerce's own native rule.
     */
    public function parse_category_attribute_chain($wp)
    {
        if (empty($wp->query_vars['qali_cat_chain_slug'])) {
            return;
        }

        $cat_slug   = sanitize_title(urldecode((string) $wp->query_vars['qali_cat_chain_slug']));
        $first_base = sanitize_key((string) $wp->query_vars['qali_cat_chain_base']);
        $first_slug = sanitize_title(urldecode((string) $wp->query_vars['qali_cat_chain_term']));
        $rest_raw   = trim((string) ($wp->query_vars['qali_cat_chain_rest'] ?? ''), '/');

        unset(
            $wp->query_vars['qali_cat_chain_slug'],
            $wp->query_vars['qali_cat_chain_base'],
            $wp->query_vars['qali_cat_chain_term'],
            $wp->query_vars['qali_cat_chain_rest']
        );

        $category_term = get_term_by('slug', $cat_slug, 'product_cat');
        if (!$category_term || is_wp_error($category_term)) {
            $this->send_404($wp);
            return;
        }

        // Same trailing "/page/N" handling as parse_attribute_chain() (see §25 in PROGRESS-LOG.md
        // for why this rule's own 'top' priority would otherwise swallow a Show More reload).
        $paged = null;
        if (preg_match('#^(.*?)/?page/([0-9]+)$#', $rest_raw, $page_match)) {
            $paged = max(1, (int) $page_match[2]);
            $rest_raw = trim($page_match[1], '/');
        }

        $pairs = [[$first_base, $first_slug]];
        if ($rest_raw !== '') {
            $segments = array_values(array_filter(explode('/', $rest_raw), function ($s) {
                return $s !== '';
            }));
            if (count($segments) === 0 || count($segments) % 2 !== 0) {
                $this->send_404($wp);
                return;
            }
            for ($i = 0; $i < count($segments); $i += 2) {
                $pairs[] = [sanitize_key($segments[$i]), sanitize_title(urldecode($segments[$i + 1]))];
            }
        }

        $seen_bases = [];
        $resolved   = [];
        foreach ($pairs as [$base, $slug]) {
            if ($slug === '' || !isset(self::$tax_map[$base]) || isset($seen_bases[$base])) {
                $this->send_404($wp);
                return;
            }
            $seen_bases[$base] = true;

            $taxonomy = self::$tax_map[$base];
            $term     = get_term_by('slug', $slug, $taxonomy);
            if (!$term || is_wp_error($term)) {
                $this->send_404($wp);
                return;
            }

            $resolved[] = ['base' => $base, 'taxonomy' => $taxonomy, 'slug' => $slug, 'term' => $term];
        }

        $wp->query_vars['product_cat'] = $cat_slug;

        if ($paged !== null) {
            $wp->query_vars['paged'] = $paged;
        }

        foreach ($resolved as $extra) {
            self::$chain_extra_tax[$extra['taxonomy']][] = $extra['slug'];
        }

        self::$category_chain_terms = array_merge(
            [['base' => 'product-category', 'taxonomy' => 'product_cat', 'slug' => $cat_slug, 'term' => $category_term]],
            $resolved
        );
    }

    private function send_404($wp)
    {
        $wp->query_vars = ['error' => '404'];
    }

    /**
     * Builds the "/base1/slug1/base2/slug2/…/" path for a chain, given an ordered subset of
     * self::$chain_terms entries. Shared by the breadcrumb and canonical-URL hooks below.
     */
    private static function chain_path($terms)
    {
        $parts = [];
        foreach ($terms as $entry) {
            $parts[] = $entry['base'];
            $parts[] = $entry['slug'];
        }
        return '/' . implode('/', $parts) . '/';
    }

    /**
     * H1/title text for a chain: term names in URL order + "Rugs", e.g. "Tabriz Red Rectangle Rugs".
     * Only ever non-empty on an actual chain page (2+ segments) — a plain single-attribute page
     * has an empty self::$chain_terms and is left completely alone.
     */
    public static function chain_title_text()
    {
        if (count(self::$chain_terms) < 2) {
            return '';
        }
        $names = array_map(function ($entry) {
            return $entry['term']->name;
        }, self::$chain_terms);
        return implode(' ', $names) . ' ' . __('Rugs', LANG_STRING);
    }

    public function chain_title($title)
    {
        $text = self::chain_title_text();
        if ($text === '') {
            return $title;
        }
        return $text . ' - ' . get_bloginfo('name');
    }

    /**
     * Replaces Yoast's default single-term crumb with one crumb per active chain filter, in URL
     * order, each linking to the shorter chain it belongs to (a valid, real page); the current
     * (last) crumb is left unlinked, matching how Yoast renders every other terminal crumb.
     *
     * Handles both chain shapes: a pure pa_*-only chain (self::$chain_terms, e.g.
     * /origin/tabriz/color/red/) and a category-leading chain (self::$category_chain_terms, e.g.
     * /product-category/colorful-vintage/origin/tabriz/) — the two are mutually exclusive per
     * request (see parse_category_attribute_chain()'s own comment), and chain_path() already
     * builds the right URL for either: a category_chain_terms entry's 'base' => 'product-category'
     * is the real literal first path segment for that page, same as any pa_{base} entry's own
     * base is for a pure attribute chain.
     */
    public function chain_breadcrumb_links($crumbs)
    {
        if (count(self::$category_chain_terms) >= 2) {
            $chain = self::$category_chain_terms;
        } elseif (count(self::$chain_terms) >= 2) {
            $chain = self::$chain_terms;
        } else {
            return $crumbs;
        }

        array_pop($crumbs); // drop Yoast's own default crumb for the native queried term

        $count = count($chain);
        foreach ($chain as $i => $entry) {
            $is_last  = ($i === $count - 1);
            $crumbs[] = [
                'text' => $entry['term']->name,
                'url'  => $is_last ? '' : home_url(self::chain_path(array_slice($chain, 0, $i + 1))),
            ];
        }

        return $crumbs;
    }

    /**
     * Points every ordering of the same filter set at one canonical URL (bases sorted
     * alphabetically) so N-way chains don't create N! duplicate-content variants — each ordering
     * still renders fully (per spec), it just isn't the one search engines are told to index.
     */
    public function chain_canonical($canonical)
    {
        if (count(self::$chain_terms) < 2) {
            return $canonical;
        }
        $sorted = self::$chain_terms;
        usort($sorted, function ($a, $b) {
            return strcmp($a['base'], $b['base']);
        });
        return home_url(self::chain_path($sorted));
    }

    /**
     * Minimum product count for a chain combination page to be indexed. Below this, the page
     * still renders normally (real content, still linkable/crawlable) but is marked noindex,follow
     * rather than 404 — a thin-but-real combination still helps a visitor navigate. Set to 2 (only
     * 0- or 1-product combinations are noindexed) per explicit user direction, given this site's
     * category sizes — see PROGRESS-LOG.md §23.
     */
    const CHAIN_NOINDEX_MIN_PRODUCTS = 2;

    public function chain_robots($robots)
    {
        if (count(self::$chain_terms) < 2) {
            return $robots;
        }
        global $wp_query;
        $found = $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;
        if ($found < self::CHAIN_NOINDEX_MIN_PRODUCTS) {
            $robots['index']  = 'noindex';
            $robots['follow'] = 'follow';
        }
        return $robots;
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
