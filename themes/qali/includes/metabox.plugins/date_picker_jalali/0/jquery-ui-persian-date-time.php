<?php
/*
Plugin Name:  شمسی سازی Jquery UI
Version: 1.1
Author: نوید صفوی
*/
define( 'URL_JQUERY_PERSIAN', plugins_url( '/',  __FILE__) );
define( 'DIR_JQUERY_PERSIAN', trailingslashit(dirname(__FILE__)) );

function local_date_i18n($format, $timestamp) {
	$timestamp = $timestamp + (  get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	return date_i18n($format, $timestamp );
}

Class JQueryUiPersian {

	public function __construct () {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue') );
	}

	function enqueue() {

		wp_enqueue_script( 'jquery-nonconfictjquery', URL_JQUERY_PERSIAN . 'js/nonConfictjquery.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'jquery-ui-slider');
		wp_enqueue_script( 'jquery-ui-timepicker', URL_JQUERY_PERSIAN . 'js/jquery-ui-timepicker-cc.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'jquery-ui-timepicke-addon', URL_JQUERY_PERSIAN . 'js/jquery-ui-timepicker-addon.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'jquery-ui-timepicke-i18n', URL_JQUERY_PERSIAN . 'js/jquery-ui-timepicker-addon-i18n.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'jquery-ui-timepicke-init', URL_JQUERY_PERSIAN . 'js/init.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'jquery-ui-datepicker-persian', URL_JQUERY_PERSIAN . 'js/datepicker-fa-IR.js', array( 'jquery' ), null, true );

		wp_enqueue_style( 'jquery-ui-datepicker', URL_JQUERY_PERSIAN . 'css/jquery-ui.css' );

		wp_enqueue_style( 'jquery-ui-datepicker-addon', URL_JQUERY_PERSIAN . 'css/jquery-ui-timepicker-addon.css' );
		wp_enqueue_style( 'jquery-ui-datepicker-structure', URL_JQUERY_PERSIAN . 'css/jquery-ui.structure.css' );
		wp_enqueue_style( 'jquery-ui-datepicker-theme', URL_JQUERY_PERSIAN . 'css/jquery-ui.theme.css' );

	}
}
new JQueryUiPersian();
