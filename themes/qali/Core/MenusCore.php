<?php

namespace Core;

class MenusCore
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_action('after_setup_theme', [$this, 'menus']);

        return;
    }
}
