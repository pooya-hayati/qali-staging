<?php

namespace App\Setup;

use Core\Enqueue;

class AppEnqueue extends Enqueue
{

    public static function all_scripts_remove()
    {
        $all_scripts = [];
        $all_scripts = array_merge(parent::all_scripts_remove(), $all_scripts);

        return $all_scripts;
    }

    public function change_script_src_array()
    {
        $default = [];
        if (! is_network_admin() && ! is_admin()) {
            $default = [
                'jquery-core'    => URL_ASSETS . '/js/jquery.min.js',
                'jquery-migrate' => URL_ASSETS . '/js/jquery-migrate.min.js',
            ];
        }

        return $default;
    }

    public function admin_scripts($hook)
    {
        if (is_rtl()) {
            wp_enqueue_style('metabox-rtl', assets_url('css/metabox.rtl.css'), '', false, 'all');
        }
    }

    public function footer_hook() {}

    public function all_styles()
    {
        $all_styles = [
            [
                'name'    => 'fw',
                'address' => 'css/fw.min',
                'loop'    => true,
                'rtl'     => false,
            ],
            [
                'name'    => 'swiper',
                'address' => 'css/swiper-bundle.min',
                'loop'    => true,
                'rtl'     => false,
            ],
            [
                'name'    => 'butterup',
                'address' => 'css/butterup.min',
                'loop'    => true,
                'rtl'     => false,
            ],
            [
                'name'    => 'leaflet',
                'address' => 'css/leaflet.min',
                'loop'    => true,
                'rtl'     => false,
            ],
            [
                'name'    => 'main',
                'address' => 'css/main',
                'loop'    => true,
                'rtl'     => false,
                'ver'     => THEME_VERSION,
            ],
            [
                'name'    => 'fw-rtl',
                'address' => 'css/fw.rtl.min',
                'loop'    => true,
                'rtl'     => true,
            ],
            [
                'name'    => 'swiper-rtl',
                'address' => 'css/swiper-bundle.min',
                'loop'    => true,
                'rtl'     => true,
            ],
            [
                'name'    => 'butterup-rtl',
                'address' => 'css/butterup.min',
                'loop'    => true,
                'rtl'     => true,
            ],
            [
                'name'    => 'leaflet-rtl',
                'address' => 'css/leaflet.min',
                'loop'    => true,
                'rtl'     => true,
            ],
            [
                'name'    => 'main-rtl',
                'address' => 'css/main.rtl',
                'loop'    => true,
                'rtl'     => true,
                'ver'     => THEME_VERSION,
            ],

        ];

        return $all_styles;
    }

    public function all_scripts()
    {
        $all_javascript = [
            [
                'name'    => 'jquery',
                'address' => 'js/jquery.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'jquery-migrate',
                'address' => 'js/jquery-migrate.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'imagesloaded',
                'address' => 'js/imagesloaded.pkgd.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'gsap',
                'address' => 'js/gsap.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'ScrollTrigger',
                'address' => 'js/ScrollTrigger.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'masonry',
                'address' => 'js/masonry.pkgd.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'swiper',
                'address' => 'js/swiper-bundle.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'butterup',
                'address' => 'js/butterup.min',
                'footer'  => true,
                'loop'    => true,
            ],
            [
                'name'    => 'customRug',
                'address' => 'js/customRug',
                'footer'  => true,
                'loop'    => false,
            ],
            [
                'name'    => 'rugArt',
                'address' => 'js/rugArt',
                'footer'  => true,
                'loop'    => false,
            ],
            [
                'name'    => 'main',
                'address' => 'js/main',
                'footer'  => true,
                'loop'    => true,
                'ver'     => THEME_VERSION,
            ],

        ];

        return $all_javascript;
    }
    public function all_scripts_hook()
    {

        if (is_page_template('page-custom.php')) {
            wp_enqueue_script('customRug');
        }
        if (is_page_template('page-art.php')) {
            wp_enqueue_script('rugArt');
        }
    }

    public function all_styles_hook() {}
}
