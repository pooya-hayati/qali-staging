<?php

namespace App\Controller;

class Wishlist
{
    const WISHLIST_META_KEY = '_user_wishlist';
    const WISHLIST_COOKIE_NAME = 'guest_wishlist_transfer';
    const WISHLIST_ENDPOINT = 'account-wishlist';
    const WISHLIST_TOKEN = '_wishlist_token';

    public function __construct()
    {
        $this->register();
    }

    /**
     * Register all hooks and actions
     */
    public function register()
    {
        // AJAX actions
        add_action('wp_ajax_toggle_wishlist', [$this, 'toggle_wishlist']);
        add_action('wp_ajax_nopriv_toggle_wishlist', [$this, 'toggle_wishlist']);

        add_action('wp_ajax_get_wishlist_count', [$this, 'get_wishlist_count']);
        add_action('wp_ajax_nopriv_get_wishlist_count', [$this, 'get_wishlist_count']);

        add_action('wp_ajax_get_wishlist_products', [$this, 'get_wishlist_products']);
        add_action('wp_ajax_nopriv_get_wishlist_products', [$this, 'get_wishlist_products']);

        add_action('wp_ajax_get_guest_wishlist_products', [$this, 'get_guest_wishlist_products']);
        add_action('wp_ajax_nopriv_get_guest_wishlist_products', [$this, 'get_guest_wishlist_products']);

        add_action('wp_ajax_generate_wishlist_token', [$this, 'generate_share_token']);
        add_action('wp_ajax_revoke_wishlist_token', [$this, 'revoke_share_token']);
        add_action('wp_ajax_merge_guest_wishlist', [$this, 'merge_guest_wishlist']);

        // Endpoint rewrite
        add_action('init', [$this, 'add_endpoint']);
        add_filter('query_vars', [$this, 'add_query_vars']);

        // WooCommerce menu
        add_filter('woocommerce_account_menu_items', [$this, 'add_account_menu']);
        add_action('woocommerce_account_' . self::WISHLIST_ENDPOINT . '_endpoint', [$this, 'render_account_page']);

        // Cookie merge on login
        add_action('wp_login', [$this, 'handle_merge_cookie_on_login'], 20, 2);

        // Enqueue frontend script
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('wishlist-script', URL_ASSETS . '/js/wishlist.js', ['main'], null, true);
        wp_localize_script('wishlist-script', 'wishlist_params', [
            'nonce' => wp_create_nonce('wishlist_nonce'),
            'isLoggedIn' => is_user_logged_in(),
            'wishlistCount' => is_user_logged_in() ? count((array) get_user_meta(get_current_user_id(), self::WISHLIST_META_KEY, true)) : 0,
        ]);
    }

    public function toggle_wishlist()
    {
        check_ajax_referer('wishlist_nonce', 'nonce');
        $product_id = absint($_POST['product_id']);
        $user_id = get_current_user_id();
        if (!$user_id || !$product_id) wp_send_json_error();

        $wishlist = get_user_meta($user_id, self::WISHLIST_META_KEY, true);
        $wishlist = is_array($wishlist) ? $wishlist : [];

        if (in_array($product_id, $wishlist)) {
            $wishlist = array_diff($wishlist, [$product_id]);
            $status = 'removed';
        } else {
            $wishlist[] = $product_id;
            $status = 'added';
        }

        update_user_meta($user_id, self::WISHLIST_META_KEY, array_values($wishlist));
        wp_send_json_success(['status' => $status]);
    }

    public function get_wishlist_count()
    {
        check_ajax_referer('wishlist_nonce', 'nonce');
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, self::WISHLIST_META_KEY, true);
        wp_send_json_success(['count' => is_array($wishlist) ? count($wishlist) : 0]);
    }

    public function get_wishlist_products()
    {
        check_ajax_referer('wishlist_nonce', 'nonce');
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, self::WISHLIST_META_KEY, true);
        $product_ids = is_array($wishlist) ? $wishlist : [];
        wp_send_json_success(['products' => $this->prepare_products($product_ids)]);
    }

    public function get_guest_wishlist_products()
    {
        $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('absint', $_POST['ids']) : [];
        if (empty($ids)) {
            wp_send_json_error(['message' => __('Invalid guest wishlist', LANG_STRING)]);
        }
        wp_send_json_success(['products' => $this->prepare_products($ids)]);
    }

    public function merge_guest_wishlist()
    {
        check_ajax_referer('wishlist_nonce', 'nonce');
        $user_id = get_current_user_id();
        if (!$user_id || empty($_POST['ids']) || !is_array($_POST['ids'])) {
            wp_send_json_error(['message' => __('Invalid request', LANG_STRING)]);
        }
        $guest_ids = array_map('absint', $_POST['ids']);
        $current = get_user_meta($user_id, self::WISHLIST_META_KEY, true);
        $current = is_array($current) ? $current : [];
        $merged = array_unique(array_merge($current, $guest_ids));
        update_user_meta($user_id, self::WISHLIST_META_KEY, array_values($merged));
        wp_send_json_success(['message' => __('Wishlist merged successfully.', LANG_STRING)]);
    }

    public function generate_share_token()
    {
        check_ajax_referer('wishlist_nonce', 'nonce');
        $user_id = get_current_user_id();
        $token = wp_generate_password(16, false);
        update_user_meta($user_id, self::WISHLIST_TOKEN, $token);

        // Always build absolute URL manually to avoid fallback to wrong base path
        $url = home_url('/wishlist/') . '?wishlist_token=' . urlencode($token);

        wp_send_json_success([
            'url' => $url,
            'message' => __('Share link generated.', LANG_STRING)
        ]);
    }


    public function revoke_share_token()
    {
        check_ajax_referer('wishlist_nonce', 'nonce');
        $user_id = get_current_user_id();
        delete_user_meta($user_id, '_wishlist_token');
        wp_send_json_success(['message' => __('Share link revoked.', LANG_STRING)]);
    }

    public function add_endpoint()
    {
        add_rewrite_endpoint(self::WISHLIST_ENDPOINT, EP_ROOT | EP_PAGES);
    }

    public function add_query_vars($vars)
    {
        $vars[] = self::WISHLIST_ENDPOINT;
        $vars[] = 'wishlist_token';
        return $vars;
    }

    public function add_account_menu($items)
    {
        $logout = $items['customer-logout'] ?? null;
        unset($items['customer-logout']);
        $items[self::WISHLIST_ENDPOINT] = __('Wishlist', LANG_STRING);
        if ($logout) $items['customer-logout'] = $logout;
        return $items;
    }

    public function render_account_page()
    {
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, self::WISHLIST_META_KEY, true) ?: [];

        echo '<h2>' . __('My Wishlist', LANG_STRING) . '</h2>';

        if (empty($wishlist)) {
            echo '<div class="woocommerce-info">' . __('Your wishlist is empty.', LANG_STRING) . '</div>';
            return;
        }

        echo '<table class="woocommerce-orders-table woocommerce-table--wishlist shop_table wishlist_table">
      <thead>
        <tr>
          <th class="product-thumbnail">' . __('Image', LANG_STRING) . '</th>
          <th class="product-name">' . __('Product', LANG_STRING) . '</th>
          <th class="product-price">' . __('Price', LANG_STRING) . '</th>
          <th class="product-stock">' . __('Stock', LANG_STRING) . '</th>
          <th class="product-actions">' . __('Actions', LANG_STRING) . '</th>
        </tr>
      </thead>
      <tbody>';

        foreach ($wishlist as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) continue;

            $thumbnail = $product->get_image('thumbnail');
            $stock_status = $product->is_in_stock()
                ? '<span class="in-stock">' . __('In stock', LANG_STRING) . '</span>'
                : '<span class="out-of-stock">' . __('Out of stock', LANG_STRING) . '</span>';

            $add_to_cart_url = $product->is_purchasable() && $product->is_in_stock()
                ? esc_url(wc_get_cart_url() . '?add-to-cart=' . $product_id)
                : get_permalink($product_id);

            $add_to_cart_text = $product->is_purchasable() && $product->is_in_stock()
                ? __('Add to Cart', LANG_STRING)
                : __('View Product', LANG_STRING);

            echo '<tr>
        <td class="product-thumbnail">' . $thumbnail . '</td>
        <td class="product-name"><a href="' . get_permalink($product_id) . '">' . $product->get_name() . '</a></td>
        <td class="product-price">' . $product->get_price_html() . '</td>
        <td class="product-stock">' . $stock_status . '</td>
        <td class="product-actions">
          <div class="wishlist-actions">
            <a href="' . $add_to_cart_url . '" class="button">' . $add_to_cart_text . '</a>
            <button class="button remove-from-wishlist" data-product-id="' . esc_attr($product_id) . '">' . __('Remove', LANG_STRING) . '</button>
          </div>
        </td>
      </tr>';
        }

        echo '</tbody></table>';

        // Share functionality
        $token = get_user_meta($user_id, self::WISHLIST_TOKEN, true);
        echo '<div class="wishlist-share-box">';
        if ($token) {
            $wishlist_url = site_url('/wishlist/?wishlist_token=' . $token);
            echo '<p><strong>' . __('Share your wishlist:', LANG_STRING) . '</strong></p>
        <input type="text" class="form-control" value="' . esc_url($wishlist_url) . '" readonly onclick="this.select();" />
        <button id="revoke-share-link" class="button" style="margin-top:10px;">' . __('Revoke Share Link', LANG_STRING) . '</button>';
        } else {
            echo '<p><strong>' . __('No share link available.', LANG_STRING) . '</strong></p>
        <button id="generate-share-link" class="button" style="margin-top:10px;">' . __('Generate New Share Link', LANG_STRING) . '</button>';
        }
        echo '</div>';
    }

    public function handle_merge_cookie_on_login($user_login, $user)
    {
        if (isset($_COOKIE[self::WISHLIST_COOKIE_NAME])) {
            $ids = json_decode(stripslashes($_COOKIE[self::WISHLIST_COOKIE_NAME]), true);
            if (is_array($ids) && !empty($ids)) {
                $existing = get_user_meta($user->ID, self::WISHLIST_META_KEY, true);
                $existing = is_array($existing) ? $existing : [];
                $merged = array_unique(array_merge($existing, array_map('absint', $ids)));
                update_user_meta($user->ID, self::WISHLIST_META_KEY, array_values($merged));
                setcookie(self::WISHLIST_COOKIE_NAME, '', time() - 3600, '/');
            }
        }
    }

    public function prepare_products($ids)
    {
        $products = [];
        foreach ($ids as $id) {
            $product = wc_get_product($id);
            if ($product && $product->is_visible()) {
                $products[] = [
                    'id'    => $id,
                    'name'  => $product->get_name(),
                    'price' => $product->get_price_html(),
                    'url'   => get_permalink($id),
                    'image' => post_image($id, 'full'),
                ];
            }
        }
        return $products;
    }

    public function get_user_by_token(string $token)
    {
        if (empty($token)) {
            return null;
        }

        global $wpdb;

        $user_id = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
            self::WISHLIST_TOKEN,
            $token
        ));

        return $user_id ? get_user_by('id', (int) $user_id) : null;
    }

    public function get_user_wishlist_products(int $user_id): array
    {
        if ($user_id <= 0) {
            return [];
        }

        $product_ids = get_user_meta($user_id, self::WISHLIST_META_KEY, true);

        if (!is_array($product_ids) || empty($product_ids)) {
            return [];
        }

        $products = [];

        foreach ($product_ids as $id) {
            $product = wc_get_product((int) $id);

            if (!$product || !$product->is_visible()) {
                continue;
            }

            $products[] = [
                'id'           => $id,
                'name'         => $product->get_name(),
                'price'        => $product->get_price_html(),
                'url'          => get_permalink($id),
                'image'        => post_image($id, 'full'),
                'stock_status' => $product->get_stock_status(),
                'in_stock'     => $product->is_in_stock(),
            ];
        }

        return $products;
    }
}
