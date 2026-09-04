<?php

namespace App\Controller;

class Invoice
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
        add_action('init', [$this, 'add_endpoints']);
        add_action('template_redirect', [$this, 'handle_requests']);

        add_filter('woocommerce_admin_order_actions', [$this, 'add_custom_order_actions_buttons'], 10, 2);
        add_action('admin_head', [$this, 'order_button_admin_styles']);


        add_action('woocommerce_order_details_after_order_table', [$this, 'add_invoice_button'], 10);
        add_filter('woocommerce_my_account_my_orders_actions', [$this, 'add_order_list_buttons'], 10, 2);
    }

    public function add_endpoints()
    {
        add_rewrite_endpoint('view-invoice', EP_ROOT);
        add_rewrite_endpoint('view-shipping-label', EP_ROOT);
        flush_rewrite_rules();
    }

    public function add_invoice_button($order)
    {
        $invoice_url = home_url("/view-invoice/{$order->get_id()}");

        echo '<div class="order-details-footer"><a href="' . esc_url($invoice_url) . '" class="button" target="_blank">مشاهده فاکتور</a></div>';
    }

    function add_custom_order_actions_buttons($actions, $order)
    {
        // URL برای چاپ فاکتور
        $invoice_url = home_url("/view-invoice/{$order->get_id()}");

        // URL برای چاپ برچسب
        $shipping_label_url = home_url("/view-shipping-label/{$order->get_id()}");

        // دکمه چاپ فاکتور
        $actions['print_invoice'] = [
            'url'    => $invoice_url,
            'name'   => __('Invoice', LANG_STRING),
            'action' => 'print_invoice',
        ];

        // دکمه چاپ برچسب
        $actions['print_shipping_label'] = [
            'url'    => $shipping_label_url,
            'name'   => __('Label', LANG_STRING),
            'action' => 'print_shipping_label',
        ];

        return $actions;
    }
    public function order_button_admin_styles()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'wc-orders') {
            echo '<style>
            .wc-action-button-print_invoice::after {
                content: "\f121";
            }
            .wc-action-button-print_shipping_label::after {
                content: "\f480";
            }
            </style><script>document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".wc-action-button-print_invoice, .wc-action-button-print_shipping_label").forEach(link => {
                link.setAttribute("target", "_blank");
                });
            });</script>';
        }
    }

    public function add_order_list_buttons($actions, $order)
    {
        $actions['view_invoice'] = array(
            'url'  => home_url("/view-invoice/{$order->get_id()}"),
            'name' => 'دریافت فاکتور'
        );

        /*(if ($order->has_shipping_address()) {
            $actions['view_shipping_label'] = array(
                'url'  => home_url("/view-shipping-label/{$order->get_id()}"),
                'name' => 'برچسب پستی'
            );
        }*/

        return $actions;
    }

    public function handle_requests()
    {
        global $wp_query;

        if (isset($wp_query->query_vars['view-invoice'])) {
            $order_id = $wp_query->query_vars['view-invoice'];

            $order = wc_get_order($order_id);
            if (!$order) {
                wp_die(__('Order not found.', LANG_STRING));
            }

            if (!current_user_can('manage_woocommerce', $order_id) && $order->get_user_id() !== get_current_user_id()) {
                wp_die(__('You do not have permission to view this order.', LANG_STRING));
            }

            $this->display_invoice($order);
            exit;
        }

        if (isset($wp_query->query_vars['view-shipping-label'])) {
            $order_id = $wp_query->query_vars['view-shipping-label'];

            $order = wc_get_order($order_id);
            if (!$order) {
                wp_die(__('Order not found.', LANG_STRING));
            }

            if (!current_user_can('manage_woocommerce', $order_id)) {
                wp_die(__('You do not have permission to view this order.', LANG_STRING));
            }

            $this->display_shipping_label($order);
            exit;
        }
    }

    public function display_invoice($order)
    {
        get_template_part_var('templates/print/print-invoice.php', ['order' => $order]);
    }

    public function display_shipping_label($order)
    {
        get_template_part_var('templates/print/print-label.php', ['order' => $order]);
    }

    public function get_formatted_item_meta($item)
    {
        $formatted_meta = [];
        $meta_data = $item->get_formatted_meta_data();

        foreach ($meta_data as $meta) {
            $formatted_meta[] = $meta->display_key . ': ' . wp_strip_all_tags($meta->display_value);
        }

        return !empty($formatted_meta) ? '<br><small>' . implode(', ', $formatted_meta) . '</small>' : '';
    }

    public function calculate_total_weight($order)
    {
        $total_weight = 0;

        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && $product->has_weight()) {
                $total_weight += $product->get_weight() * $item->get_quantity();
            }
        }

        return round($total_weight, 2);
    }

    public function count_items($order)
    {
        $count = 0;
        foreach ($order->get_items() as $item) {
            $count += $item->get_quantity();
        }
        return $count;
    }

    public function get_tracking_info($order)
    {
        // WooCommerce Shipment Tracking
        $tracking_items = $order->get_meta('_wc_shipment_tracking_items');
        if (!empty($tracking_items)) {
            return $tracking_items[0]['tracking_number'];
        }

        // Persian Woocommerce Shipping
        $tracking = $order->get_meta('_shipping_tracking_code');
        if (!empty($tracking)) {
            return $tracking;
        }

        return false;
    }
    public function generate_qr_code($order)
    {
        $qr_data = [
            'order_id' => $order->get_order_number(),
            'total' => $order->get_total(),
            'date' => $order->get_date_created()->format('Y-m-d H:i:s'),
            'customer' => $order->get_formatted_billing_full_name()
        ];
        return '<img src="" alt="QR Code">';
    }
    public function generate_barcode($number)
    {
        return '<img src="" alt="Barcode">';
    }
}
