<?php

namespace Core;

class WidgetsCore
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_action('after_setup_theme', [$this, 'widgets']);

        return;
    }
}
