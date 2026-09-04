<?php
define('URL_ROOT', get_template_directory_uri());
define('URL_ROOT_DIR', get_template_directory());
define('URL_INCLUDE', URL_ROOT . '/includes');
define('URL_INCLUDE_DIR', URL_ROOT_DIR . '/includes');
define('URL_ASSETS', URL_ROOT . '/assets');
define('URL_LIBS', URL_ROOT . '/libs');
define('URL_LIBS_DIR', URL_ROOT_DIR . '/libs');
define('SITE_NAME', get_bloginfo('name'));
define('TEMP_DIR', URL_ROOT_DIR . '/temp');
define('TEMP_URL', URL_ROOT . '/temp');
define('SITE_LANG', substr(get_bloginfo('language'), 0, 2));
require_once dirname(dirname(__FILE__)) . '/includes/init.php';
