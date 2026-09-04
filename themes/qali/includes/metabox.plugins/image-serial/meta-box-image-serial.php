<?php
/**
 * Plugin Name: Meta Box Image Serial
 * Description: Easily show/hide meta boxes by various conditions using JavaScript.
 * Version: 1.1
 * Author: Navid Safavi
 * Author URI: http://navidsafavi.com
 */

	define( 'RWMB_Serial_Image_Dir', dirname(__FILE__) . '/' );
	define( 'RWMB_Serial_Image_Url', URL_LIBS . '/metabox.plugins/image-serial/' );

    add_action( 'init', 'prefix_load_phone_type', 1 );
    function prefix_load_phone_type() {
		$path =  RWMB_Serial_Image_Dir . 'Class-meta-box-image-serial.php';
		require $path;
        
    }