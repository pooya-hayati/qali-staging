<?php

namespace App\Setup;

use Core\MenusCore;

class AppMenu extends MenusCore
{

    public function menus()
    {
        register_nav_menus([
            'primary' => esc_html__('Primary', LANG_STRING),
            'shop'  => esc_html__('Shop', LANG_STRING),
            'footer'  => esc_html__('Footer', LANG_STRING),
            'access'  => esc_html__('Access', LANG_STRING),
            'contact'  => esc_html__('Contact', LANG_STRING),
        ]);
    }
}
