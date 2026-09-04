<?php

namespace App\Setup;

use App\Controller\Profile;
use Core\Setup;

class AppSetup extends Setup
{

    public function register()
    {

        parent::register();
        add_filter('post_row_actions', [$this, 'edit_row_action'], 999, 2);
        add_action('admin_menu', [$this, 'remove_menus'], 9999);
        remove_action('admin_menu', '_add_themes_utility_last', 101);
    }

    public function remove_menus()
    {

        global $current_user;
        $username = $current_user->user_login;

        if ($username !== 'manager') {
            //remove_menu_page('plugins.php');
            //remove_menu_page('themes.php');
            remove_menu_page('options-general.php');
            remove_menu_page('tools.php');
            remove_submenu_page('themes.php', 'themes.php');
            remove_submenu_page('themes.php', 'theme-editor.php');
            remove_submenu_page('themes.php', add_query_arg('return', urlencode(remove_query_arg(wp_removable_query_args(), wp_unslash($_SERVER['REQUEST_URI']))), 'customize.php'));
            remove_submenu_page('plugins.php', 'plugin-editor.php');
        }
    }

    public function edit_row_action($actions, $post)
    {
        unset($actions['inline hide-if-no-js']);

        return $actions;
    }
}
