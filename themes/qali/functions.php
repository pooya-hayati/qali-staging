<?php

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}
if ( class_exists( 'App\\Init' ) ) {
	App\Init::register_services();
}

