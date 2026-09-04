<?php

namespace App\Controller;

class CustomPay
{
    private const ACTION = 'custompay_generate_link';
    private const PRODUCT_SKU = 'custom-rug';
    private const SESSION_KEY = 'wc_custom_payment_payload';
    private const ENDPOINT_SLUG = 'custompay';
    private const ADMIN_CAPABILITY = 'manage_woocommerce';
    private const NONCE_ACTION = 'wc_custom_pay_make_link';

    private $secret_key = '';
    private $product_id = 0;
    private $is_multisite = false;
    private $current_blog_id = 1;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->secret_key = defined('QALI_PAYMENT_SECRET_KEY') ? QALI_PAYMENT_SECRET_KEY : '';
        $this->is_multisite = is_multisite();
        if ($this->is_multisite) {
            $this->current_blog_id = get_current_blog_id();
        }
        $this->register();
    }

    /**
     * Register all hooks and actions
     */
    public function register(): void
    {
        // Core initialization
        add_action('init', [$this, 'init_plugin'], 1);

        // Multisite specific handling
        if ($this->is_multisite) {
            add_action('parse_request', [$this, 'multisite_parse_request'], 1);
        }

        // WooCommerce hooks
        add_action('woocommerce_init', [$this, 'woocommerce_ready'], 5);
        add_action('template_redirect', [$this, 'handle_payment_request'], 5);
        add_action('woocommerce_before_calculate_totals', [$this, 'apply_custom_price'], 999);

        // Cart management
        add_filter('woocommerce_add_to_cart_validation', [$this, 'restrict_cart_additions'], 10, 6);
        add_filter('woocommerce_cart_item_quantity', [$this, 'lock_quantity_display'], 10, 3);
        add_filter('woocommerce_coupons_enabled', [$this, 'maybe_disable_coupons']);

        // Order processing
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'decorate_order_item'], 10, 4);
        add_action('woocommerce_checkout_create_order', [$this, 'add_order_metadata'], 10, 2);
        add_filter('woocommerce_checkout_get_value', [$this, 'prefill_checkout_fields'], 10, 2);
        add_action('woocommerce_thankyou', [$this, 'cleanup_session']);

        // Admin interface
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_ajax_' . self::ACTION, [$this, 'ajax_generate_link']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // Error display
        add_action('woocommerce_before_shop_loop', [$this, 'display_shop_errors']);
        add_action('wp_head', [$this, 'display_shop_errors']);
    }

    /**
     * Handle multisite URL parsing
     */
    public function multisite_parse_request($wp): void
    {
        if (!$this->is_multisite) {
            return;
        }

        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $site_path = parse_url(get_site_url(), PHP_URL_PATH);

        if ($site_path && strpos($request_uri, $site_path) === 0) {
            $request_uri = substr($request_uri, strlen($site_path));
        }

        if (
            strpos($request_uri, '/' . self::ENDPOINT_SLUG) === 0 ||
            strpos($request_uri, self::ENDPOINT_SLUG) === 0
        ) {
            $_GET['custompay'] = 1;
        }
    }

    /**
     * Initialize plugin
     */
    public function init_plugin(): void
    {
        if ($this->is_payment_request()) {
            add_action('woocommerce_init', [$this, 'handle_direct_payment'], 1);
            return;
        }

        if (!$this->is_multisite && is_admin() && !get_option('custompay_rules_flushed_v3')) {
            flush_rewrite_rules();
            update_option('custompay_rules_flushed_v3', 1);
        }
    }

    /**
     * WooCommerce initialization complete
     */
    public function woocommerce_ready(): void
    {
        if (is_admin()) {
            $this->get_or_create_product();
        }
    }

    /**
     * Check if current request is a payment request
     */
    private function is_payment_request(): bool
    {
        if (isset($_GET['custompay'])) {
            return true;
        }

        $request_uri = $_SERVER['REQUEST_URI'] ?? '';

        if ($this->is_multisite) {
            $site_url = get_site_url();
            $site_path = parse_url($site_url, PHP_URL_PATH);
            if ($site_path && strpos($request_uri, $site_path) === 0) {
                $request_uri = substr($request_uri, strlen($site_path));
            }
        }

        return strpos($request_uri, '/' . self::ENDPOINT_SLUG) !== false ||
            strpos($request_uri, self::ENDPOINT_SLUG) === 0;
    }

    /**
     * Process payment request directly
     */
    public function handle_direct_payment(): void
    {
        if (!$this->is_payment_request()) {
            return;
        }

        try {
            if (!$this->ensure_woocommerce()) {
                throw new \Exception('WooCommerce is not available.');
            }

            $this->process_payment_request();
        } catch (\Exception $e) {
            $this->redirect_with_error($e->getMessage());
        }
    }

    /**
     * Template redirect handler (backup method)
     */
    public function handle_payment_request(): void
    {
        if (!$this->is_payment_request() || !function_exists('WC')) {
            return;
        }

        if (!did_action('custompay_payment_processed')) {
            $this->handle_direct_payment();
        }
    }

    /**
     * Ensure WooCommerce is properly loaded
     */
    private function ensure_woocommerce(): bool
    {
        if (!function_exists('WC') || !WC()) {
            return false;
        }

        if (!is_user_logged_in()) {
            $this->init_guest_session();
        }

        return true;
    }

    /**
     * Initialize session for guest users
     */
    private function init_guest_session(): void
    {
        if (!WC()->session) {
            WC()->session = new \WC_Session_Handler();
            WC()->session->init();
            WC()->session->set_customer_session_cookie(true);
        }

        if (!WC()->customer) {
            WC()->customer = new \WC_Customer(0, true);
        }

        if (!WC()->cart) {
            WC()->frontend_includes();
            WC()->cart = new \WC_Cart();
        }
    }

    /**
     * Process payment request parameters
     */
    private function process_payment_request(): void
    {
        do_action('custompay_payment_processed');

        // Extract and sanitize parameters
        $amount = $this->sanitize_amount($_GET['amount'] ?? '');
        $qty = max(1, absint($_GET['qty'] ?? 1));

        $label = $_GET['label'] ?? '';
        if (!empty($label)) {
            $label = rawurldecode($label);
        }
        $label = sanitize_text_field($label);

        $exp = absint($_GET['exp'] ?? 0);
        $signature = sanitize_text_field($_GET['sig'] ?? '');
        $from_order_id = absint($_GET['from'] ?? 0);
        $from_order_key = sanitize_text_field($_GET['okey'] ?? '');

        $currency = sanitize_text_field($_GET['cur'] ?? '');
        if (empty($currency)) {
            $currency = get_woocommerce_currency();
        }

        // Validate parameters
        if ($amount <= 0) {
            throw new \Exception('Invalid amount specified.');
        }

        if (empty($signature) || empty($exp)) {
            throw new \Exception('Invalid payment link parameters.');
        }

        if (time() > $exp) {
            throw new \Exception('Payment link has expired.');
        }

        // Verify signature
        if (!$this->verify_signature($amount, $qty, $exp, $label, self::PRODUCT_SKU, $currency, $from_order_id, $signature)) {
            throw new \Exception('Invalid payment link signature.');
        }

        // Verify prefill order if specified
        if ($from_order_id > 0) {
            $order = wc_get_order($from_order_id);
            if (!$order || $order->get_order_key() !== $from_order_key) {
                throw new \Exception('Invalid order information.');
            }
        }

        // Get or create product
        $product_id = $this->get_or_create_product();
        if (!$product_id) {
            throw new \Exception('Payment product is not available.');
        }

        // Setup cart and redirect
        $this->setup_cart($product_id, $qty, $amount, $label, $signature, $from_order_id);

        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    /**
     * Setup cart with payment item
     */
    private function setup_cart(int $product_id, int $qty, float $amount, string $label, string $signature, int $from_order_id): void
    {
        WC()->cart->empty_cart();

        $cart_item_data = [
            '_custom_pay' => 1,
            '_custom_pay_label' => $label,
            '_custom_pay_signature' => $signature,
            '_custom_pay_from_order' => $from_order_id,
            '_custom_pay_amount' => $amount,
        ];

        $cart_key = WC()->cart->add_to_cart($product_id, $qty, 0, [], $cart_item_data);

        if (!$cart_key) {
            throw new \Exception('Failed to add payment item to cart.');
        }

        $payment_data = [
            'amount' => $amount,
            'qty' => $qty,
            'label' => $label,
            'product_id' => $product_id,
            'signature' => $signature,
            'from_order_id' => $from_order_id,
            'cart_key' => $cart_key,
        ];

        WC()->session->set(self::SESSION_KEY, $payment_data);
        WC()->cart->calculate_totals();
    }

    /**
     * Get or create payment product
     */
    private function get_or_create_product()
    {
        if ($this->product_id > 0) {
            return $this->product_id;
        }

        $product_id = wc_get_product_id_by_sku(self::PRODUCT_SKU);
        if ($product_id) {
            return $this->product_id = $product_id;
        }

        try {
            $product = new \WC_Product_Simple();
            $product->set_name('Custom Payment');
            $product->set_status('publish');
            $product->set_catalog_visibility('hidden');
            $product->set_sku(self::PRODUCT_SKU);
            $product->set_price(0);
            $product->set_regular_price(0);
            $product->set_virtual(true);
            $product->set_downloadable(false);
            $product->set_sold_individually(true);
            $product->set_manage_stock(false);

            $product_id = $product->save();

            if (!$product_id) {
                throw new \Exception('Failed to create payment product');
            }

            return $this->product_id = $product_id;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Redirect with error message
     */
    private function redirect_with_error(string $message): void
    {
        if ($this->ensure_woocommerce() && WC()->session) {
            WC()->session->set('custompay_error', $message);
        }

        $redirect_url = $this->get_site_url();
        if (function_exists('wc_get_page_permalink')) {
            $shop_url = wc_get_page_permalink('shop');
            if ($shop_url) {
                $redirect_url = $shop_url;
            }
        }

        wp_safe_redirect(add_query_arg('custompay_error', '1', $redirect_url));
        exit;
    }

    /**
     * Get proper site URL for multisite
     */
    private function get_site_url($path = ''): string
    {
        if ($this->is_multisite) {
            return get_site_url($this->current_blog_id, $path);
        }
        return home_url($path);
    }

    /**
     * Generate signature for payment link
     */
    private static function generate_signature(float $amount, int $qty, int $exp, string $label, string $product_sku, string $currency, int $from_order_id): string
    {
        $formatted_amount = number_format($amount, 2, '.', '');

        $payload = implode('|', [
            $formatted_amount,
            $qty,
            $exp,
            $label,
            $product_sku,
            $currency,
            $from_order_id,
        ]);

        return hash_hmac('sha256', $payload, $this->secret_key);
    }

    /**
     * Verify payment signature
     */
    private function verify_signature(float $amount, int $qty, int $exp, string $label, string $product_sku, string $currency, int $from_order_id, string $signature): bool
    {
        $expected_signature = self::generate_signature($amount, $qty, $exp, $label, $product_sku, $currency, $from_order_id);
        return hash_equals($expected_signature, $signature);
    }

    /**
     * Apply custom price to cart items
     */
    public function apply_custom_price($cart): void
    {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        if (!WC()->session) {
            return;
        }

        $payload = WC()->session->get(self::SESSION_KEY);

        if (!is_array($payload) || empty($payload['amount'])) {
            return;
        }

        $product_id = $payload['product_id'] ?? 0;
        $amount = (float) $payload['amount'];

        foreach ($cart->get_cart() as $cart_item) {
            if (
                !empty($cart_item['_custom_pay']) &&
                isset($cart_item['data']) &&
                (int) $cart_item['product_id'] === $product_id
            ) {
                $cart_item['data']->set_price($amount);
            }
        }
    }

    /**
     * Restrict adding other products during payment
     */
    public function restrict_cart_additions($passed, $product_id, $qty, $variation_id, $variations, $cart_item_data): bool
    {
        if (!WC()->session) {
            return $passed;
        }

        $payload = WC()->session->get(self::SESSION_KEY);

        if ($payload && empty($cart_item_data['_custom_pay'])) {
            wc_add_notice('Cannot add other products during custom payment process.', 'error');
            return false;
        }

        return $passed;
    }

    /**
     * Lock quantity display for payment items
     */
    public function lock_quantity_display($product_quantity, $cart_item_key, $cart_item): string
    {
        if (!empty($cart_item['_custom_pay'])) {
            return (string) max(1, (int) ($cart_item['quantity'] ?? 1));
        }
        return $product_quantity;
    }

    /**
     * Disable coupons during custom payment
     */
    public function maybe_disable_coupons($enabled): bool
    {
        if (!WC()->session) {
            return $enabled;
        }

        $payload = WC()->session->get(self::SESSION_KEY);
        return $payload ? false : $enabled;
    }

    /**
     * Add custom label to order item
     */
    public function decorate_order_item($item, $cart_item_key, $values, $order): void
    {
        if (empty($values['_custom_pay'])) {
            return;
        }

        $label = $values['_custom_pay_label'] ?? '';

        if (!empty($label)) {
            $item->add_meta_data('Custom Label', $label, true);
            $item->set_name($item->get_name() . ' — ' . $label);
        }
    }

    /**
     * Add metadata to order
     */
    public function add_order_metadata($order, $data): void
    {
        if (!WC()->session) {
            return;
        }

        $payload = WC()->session->get(self::SESSION_KEY);

        if (!$payload) {
            return;
        }

        $order->update_meta_data('_is_custom_payment', 1);

        if (!empty($payload['label'])) {
            $order->update_meta_data('_custom_payment_label', $payload['label']);
        }

        if (!empty($payload['signature'])) {
            $order->update_meta_data('_custom_payment_signature', $payload['signature']);
        }

        $order->save();
    }

    /**
     * Prefill checkout fields from existing order
     */
    public function prefill_checkout_fields($value, $input_key)
    {
        if (!WC()->session) {
            return $value;
        }

        $payload = WC()->session->get(self::SESSION_KEY);
        $from_order_id = $payload['from_order_id'] ?? 0;

        if (!$from_order_id) {
            return $value;
        }

        $order = wc_get_order($from_order_id);

        if (!$order) {
            return $value;
        }

        $field_mapping = [
            'billing_first_name' => $order->get_billing_first_name(),
            'billing_last_name' => $order->get_billing_last_name(),
            'billing_company' => $order->get_billing_company(),
            'billing_country' => $order->get_billing_country(),
            'billing_address_1' => $order->get_billing_address_1(),
            'billing_address_2' => $order->get_billing_address_2(),
            'billing_city' => $order->get_billing_city(),
            'billing_state' => $order->get_billing_state(),
            'billing_postcode' => $order->get_billing_postcode(),
            'billing_phone' => $order->get_billing_phone(),
            'billing_email' => $order->get_billing_email(),
            'shipping_first_name' => $order->get_shipping_first_name(),
            'shipping_last_name' => $order->get_shipping_last_name(),
            'shipping_company' => $order->get_shipping_company(),
            'shipping_country' => $order->get_shipping_country(),
            'shipping_address_1' => $order->get_shipping_address_1(),
            'shipping_address_2' => $order->get_shipping_address_2(),
            'shipping_city' => $order->get_shipping_city(),
            'shipping_state' => $order->get_shipping_state(),
            'shipping_postcode' => $order->get_shipping_postcode(),
        ];

        return $field_mapping[$input_key] ?? $value;
    }

    /**
     * Clean up session after order completion
     */
    public function cleanup_session($order_id): void
    {
        if (WC()->session) {
            WC()->session->__unset(self::SESSION_KEY);
        }
    }

    /**
     * Sanitize amount value
     */
    private function sanitize_amount($amount): float
    {
        if (empty($amount)) {
            return 0.0;
        }
        return (float) number_format((float) $amount, 2, '.', '');
    }

    /**
     * Generate payment link
     */
    public function generate_link(float $amount, string $label = '', int $qty = 1, int $ttl_hours = 24, int $from_order_id = 0): string
    {
        $exp = time() + ($ttl_hours * 3600);
        $amount = (float) number_format($amount, 2, '.', '');
        $qty = max(1, $qty);
        $currency = get_woocommerce_currency();

        $signature = self::generate_signature($amount, $qty, $exp, $label, self::PRODUCT_SKU, $currency, $from_order_id);

        $args = [
            'amount' => $amount,
            'qty' => $qty,
            'exp' => $exp,
            'sig' => $signature,
            'cur' => $currency,
        ];

        if (!empty($label)) {
            $args['label'] = rawurlencode($label);
        }

        if ($from_order_id > 0) {
            $order = wc_get_order($from_order_id);
            if ($order) {
                $args['from'] = $from_order_id;
                $args['okey'] = $order->get_order_key();
            }
        }

        $base_url = $this->get_site_url('/' . self::ENDPOINT_SLUG . '/');

        return add_query_arg($args, $base_url);
    }

    /**
     * Display error messages
     */
    public function display_shop_errors(): void
    {
        if (!isset($_GET['custompay_error'])) {
            return;
        }

        $error_message = '';

        if ($this->ensure_woocommerce() && WC()->session) {
            $error_message = WC()->session->get('custompay_error');
            WC()->session->__unset('custompay_error');
        }

        if (empty($error_message)) {
            $error_message = 'An error occurred processing your payment link.';
        }

        echo '<div class="woocommerce-error" style="margin: 20px 0; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 4px;">'
            . esc_html($error_message) . '</div>';
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu(): void
    {
        add_submenu_page(
            'woocommerce',
            'Custom Payment Links',
            'Payment Links',
            self::ADMIN_CAPABILITY,
            'custom-payment-links',
            [$this, 'render_admin_page']
        );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook): void
    {
        if (strpos($hook, 'custom-payment-links') === false) {
            return;
        }

        $this->enqueue_inline_admin_script();

        wp_localize_script('jquery', 'customPayAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::NONCE_ACTION),
            'action' => self::ACTION,
            'strings' => [
                'generating' => 'Generating link...',
                'copied' => 'Link copied to clipboard!',
                'copy_failed' => 'Failed to copy link. Please copy manually.',
                'invalid_amount' => 'Please enter a valid amount.',
                'error' => 'Error',
            ]
        ]);
    }

    /**
     * Add inline admin JavaScript
     */
    private function enqueue_inline_admin_script(): void
    {
        $js_content = "
        jQuery(document).ready(function($) {
            $('#custom-payment-form').on('submit', function(e) {
                e.preventDefault();
                
                var amount = $('#payment_amount').val();
                if (!amount || amount <= 0) {
                    alert(customPayAjax.strings.invalid_amount);
                    return;
                }
                
                $('.spinner').addClass('is-active');
                $('button[type=submit]').prop('disabled', true);
                
                $.post(customPayAjax.ajaxurl, {
                    action: customPayAjax.action,
                    amount: amount,
                    qty: $('#payment_qty').val(),
                    label: $('#payment_label').val(),
                    ttl: $('#payment_ttl').val(),
                    from_order_id: $('#prefill_order_id').val(),
                    _wpnonce: $('[name=\"_wpnonce\"]').val()
                }, function(response) {
                    $('.spinner').removeClass('is-active');
                    $('button[type=submit]').prop('disabled', false);
                    
                    if (response.success) {
                        $('#generated_link').val(response.data.url);
                        $('#test-link-btn').attr('href', response.data.url);
                        $('#payment-link-result').show();
                        $('html, body').animate({scrollTop: $('#payment-link-result').offset().top - 50}, 500);
                    } else {
                        alert(customPayAjax.strings.error + ': ' + (response.data.message || 'Unknown error'));
                    }
                }).fail(function() {
                    $('.spinner').removeClass('is-active');
                    $('button[type=submit]').prop('disabled', false);
                    alert(customPayAjax.strings.error + ': Network error');
                });
            });
            
            $('#copy-link-btn').on('click', function() {
                var linkField = $('#generated_link');
                linkField.select();
                try {
                    document.execCommand('copy');
                    alert(customPayAjax.strings.copied);
                } catch(err) {
                    alert(customPayAjax.strings.copy_failed);
                }
            });
            
            $('#generate-another-btn').on('click', function() {
                $('#payment-link-result').hide();
                $('#custom-payment-form')[0].reset();
            });
        });";

        wp_add_inline_script('jquery', $js_content);
    }

    /**
     * AJAX handler for link generation
     */
    public function ajax_generate_link(): void
    {
        if (!current_user_can(self::ADMIN_CAPABILITY)) {
            wp_send_json_error(['message' => 'Unauthorized access']);
        }

        check_ajax_referer(self::NONCE_ACTION);

        try {
            $amount = $this->sanitize_amount($_POST['amount'] ?? '');
            $qty = max(1, absint($_POST['qty'] ?? 1));
            $label = sanitize_text_field($_POST['label'] ?? '');
            $ttl = max(1, absint($_POST['ttl'] ?? 24));
            $from_order_id = absint($_POST['from_order_id'] ?? 0);

            if ($amount <= 0) {
                throw new \Exception('Invalid amount specified');
            }

            if ($from_order_id > 0) {
                $order = wc_get_order($from_order_id);
                if (!$order) {
                    throw new \Exception('Order not found');
                }
            }

            $url = $this->generate_link($amount, $label, $qty, $ttl, $from_order_id);

            if ($from_order_id > 0 && !empty($url)) {
                $order = wc_get_order($from_order_id);
                if ($order) {
                    $order->add_order_note('Custom payment link generated: ' . $url);
                }
            }

            wp_send_json_success(['url' => esc_url_raw($url)]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Render admin page
     */
    public function render_admin_page(): void
    {
?>
        <div class="wrap">
            <h1>Generate Custom Payment Link</h1>

            <div class="card" style="max-width: 900px;">
                <div class="inside">
                    <form id="custom-payment-form" style="padding: 20px;">
                        <?php wp_nonce_field(self::NONCE_ACTION); ?>

                        <table class="form-table">
                            <tr>
                                <th><label for="payment_amount">Amount *</label></th>
                                <td>
                                    <input type="number" step="0.01" min="0.01" id="payment_amount" name="amount" class="regular-text" required>
                                    <p class="description">Amount in <?php echo get_woocommerce_currency(); ?> (required)</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="payment_qty">Quantity</label></th>
                                <td>
                                    <input type="number" id="payment_qty" name="qty" class="small-text" value="1" min="1" max="999">
                                    <p class="description">Number of items (default: 1)</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="payment_label">Label</label></th>
                                <td>
                                    <input type="text" id="payment_label" name="label" class="regular-text" maxlength="255" placeholder="e.g., Invoice #123">
                                    <p class="description">Optional description (will appear in order)</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="payment_ttl">Expiration (Hours)</label></th>
                                <td>
                                    <input type="number" id="payment_ttl" name="ttl" class="small-text" value="24" min="1" max="8760">
                                    <p class="description">Link validity period (default: 24 hours)</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="prefill_order_id">Prefill from Order</label></th>
                                <td>
                                    <input type="number" id="prefill_order_id" name="from_order_id" class="small-text" min="1" placeholder="Order ID">
                                    <p class="description">Optional: Order ID to prefill checkout fields</p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" class="button button-primary">
                                <span class="dashicons dashicons-admin-links" style="margin-top: 3px;"></span>
                                Generate Payment Link
                            </button>
                            <span class="spinner" style="float: none; margin: 5px 10px 0 10px;"></span>
                        </p>
                    </form>

                    <div id="payment-link-result" style="display: none; margin-top: 20px; padding: 20px; border-top: 1px solid #ddd;">
                        <div class="notice notice-success inline">
                            <p><strong>✅ Payment link generated successfully!</strong></p>
                            <p><small>This link works for both logged-in users and guests</small></p>
                        </div>

                        <table class="form-table">
                            <tr>
                                <th><label for="generated_link">Payment Link</label></th>
                                <td>
                                    <textarea id="generated_link" class="large-text code" rows="3" readonly style="resize: vertical;"></textarea>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="button" class="button" id="copy-link-btn">
                                <span class="dashicons dashicons-clipboard" style="margin-top: 3px;"></span>
                                Copy Link
                            </button>
                            <a href="#" target="_blank" class="button" id="test-link-btn">
                                <span class="dashicons dashicons-external" style="margin-top: 3px;"></span>
                                Test Link
                            </a>
                            <button type="button" class="button" id="generate-another-btn">
                                <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                                Generate Another
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    /**
     * Get singleton instance
     */
    public static function init(): CustomPay
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Activation hook
     */
    public static function activate(): void
    {
        $instance = new self();

        if (function_exists('WC')) {
            $instance->get_or_create_product();
        }

        if (!is_multisite()) {
            flush_rewrite_rules();
        }

        update_option('custompay_activated', 1);
        update_option('custompay_rules_flushed_v3', 1);
    }

    /**
     * Deactivation hook
     */
    public static function deactivate(): void
    {
        delete_option('custompay_rules_flushed_v3');
        delete_option('custompay_activated');

        if (!is_multisite()) {
            flush_rewrite_rules();
        }
    }
}
