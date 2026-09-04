<?php
/**
 * Plugin Name: Dressmycrib-Qali
 * Description: Special Dressmycrib plugin for Qali.
 * Version: 1.001
 * Author: Dressmycrib
 */

function inject_react_scripts($atts) {


    // Extract the attributes
    $attributes = shortcode_atts( array(
        'sku' => '', // Product SKU
        'product_title' => '', // Product title
        
    ), $atts );

    $sku = $attributes['sku'];
    $product_title = $attributes['product_title'];
    $termsText = "By uploading an image, you agree to Qali's Terms of Service and acknowledge you have read our Privacy Policy. Your Image will be used to generate a composite of your room, which may also be used to recommend Qali products and personalize your site experience.\n
    *Rug colors and size may vary slightly based on your device and lighting.";

    $clientId = 2; //Qali client

    // Return empty if SKU is not provided
    if ( empty($sku) ) {
        return '';
    }

    // Get the URL for the plugin directory
    $plugin_url = plugins_url('', __FILE__);
    
    // Prepare the HTML and script
    ob_start(); // Start output buffering
    ?>
<script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>

<!-- Load our React component. -->
<script src="<?php echo $plugin_url; ?>/dist/dressmycrib_v1.0.0.js?v=1.4"></script>
<div id="dressmycrib-component-id"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Call the render function from your library
    DressMyCrib.renderRugsInRooms('dressmycrib-component-id', '<?php echo esc_js( $sku ); ?>', '<?php echo esc_js( $product_title ); ?>', '', '<?php echo esc_js( $termsText ); ?>',  '<?php echo esc_js( $clientId ); ?>',  );
});
</script>
<?php
    return ob_get_clean(); // Return the buffered content
}


add_shortcode('dressmycrib-viewer', 'inject_react_scripts');



// // Exit if accessed directly.
// if ( ! defined( 'ABSPATH' ) ) {
//     exit;
// }

// Hook to enqueue custom styles and scripts.
add_action( 'wp_enqueue_scripts', 'cpib_enqueue_scripts' );

function cpib_enqueue_scripts() {
    // global $product;
    // if ( is_product() && $product && !empty($product->get_sku()) ) {
     if ( is_product()) {
        wp_enqueue_style( 'cpib-styles', plugin_dir_url( __FILE__ ) . 'dist/dmc__plugin-styles.css' );
        wp_enqueue_script( 'cpib-script', plugin_dir_url( __FILE__ ) . 'dist/dmc__plugin-script.js', array(), '1.0', true );
   
    }
}



// Hook into WooCommerce single product image gallery.
// add_action( 'woocommerce_before_single_product_summary', 'cpib_add_button_to_gallery', 20 );

function cpib_add_button_to_gallery() {
    global $product;
    $sku = $product->get_sku();
    $product_title = $product->get_name();
    if ( is_product() && $product && !empty($sku) ) {
        // echo '<a href="#custom-link" id="cpib-button" class="cpib-button">Custom Button</a>';
        echo '<div id="cpib-button" class="cpib-button">';
        echo do_shortcode( '[dressmycrib-viewer sku="' . $sku . '" product_title="' . $product_title . '" ]' );
        echo '</div>';
    }
}







// Register the shortcode [product_image_button sku=""]
add_shortcode( 'product_image_button', 'cpib_product_image_button_shortcode' );

function cpib_product_image_button_shortcode( $atts ) {
    // Extract the attributes
    $attributes = shortcode_atts( array(
        'sku' => '', // Product SKU
    ), $atts );

    // Return empty if SKU is not provided
    if ( empty( $attributes['sku'] ) ) {
        return '';
    }

    global $product;

    // Check if we are on a single product page and if the SKU matches the current product
    if ( is_product() && $product && $product->get_sku() === $attributes['sku'] ) {
        // Output the custom button
        return '<a href="#custom-link" id="cpib-button" class="cpib-button" style="display: none;">Custom Button</a>';
    }

    return ''; // Return nothing if conditions don't match
}