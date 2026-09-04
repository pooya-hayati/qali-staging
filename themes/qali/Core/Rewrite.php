<?php

namespace Core;


class Rewrite
{


    function __construct()
    {
        //add_action( 'admin_url', [$this, 'change_admin_url'], 10, 1 );

    }

    function change_admin_url($url)
    {

        if (preg_match('/\/admin-ajax\.php$/', $url)) {
            $url = home_url('/ajax');
        }

        return $url;
    }
}
