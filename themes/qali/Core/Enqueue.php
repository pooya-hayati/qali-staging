<?php

namespace Core;

class Enqueue
{

    public function register()
    {

        add_action('wp_head', [$this, 'de_register_styles'], 2);
        add_action('wp_footer', [$this, 'de_register_styles'], 2);

        add_action('wp_enqueue_scripts', [$this, 'scripts']);
        add_action('wp_enqueue_scripts', [$this, 'styles']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts'], 100);

        add_action('wp_footer', [$this, 'footer'], 100);
        //add_filter('script_loader_tag', [$this, 'clean_script_tag']);

        add_filter('style_loader_src', [$this, 'change_style_src'], 11, 2);
        add_filter('script_loader_src', [$this, 'change_script_src'], 11, 2);
    }

    public function change_style_src_array()
    {
        $default = [
            //''       => URL_INCLUDE . '/.css',
        ];

        return $default;
    }

    public function change_script_src_array()
    {
        $default = [
            //''        => URL_INCLUDE . '/.js',
        ];

        return $default;
    }

    public function change_script_src_array_method($handle = '')
    {
        $default = $this->change_script_src_array();
        if ($handle != '' && isset($default[$handle])) {
            return $default[$handle];
        } else {
            return $default;
        }
    }

    public function change_style_src_array_method($handle = '')
    {
        $default = $this->change_style_src_array();
        if ($handle != '' && isset($default[$handle])) {
            return $default[$handle];
        } else {
            return $default;
        }
    }

    public function change_script_src($src, $handle)
    {
        $all_changes = $this->change_script_src_array_method();
        if (isset($all_changes[$handle])) {
            $src = $all_changes[$handle];
        }

        return $src;
    }

    public function change_style_src($src, $handle)
    {
        $all_changes = $this->change_style_src_array_method();
        if (isset($all_changes[$handle])) {
            $src = $this->change_style_src_array_method($handle);
        }

        return $src;
    }

    public static function all_scripts_remove()
    {

        $all_scripts = [
            'jquery-core',
            'jquery-migrate',
        ];

        return $all_scripts;
    }

    public static function all_styles_remove()
    {
        return [];
    }

    function de_register_scripts()
    {

        $all_scripts = self::all_scripts_remove();
        foreach ($all_scripts as $script) {
            wp_deregister_script($script);
            wp_dequeue_script($script);
        }
    }

    function de_register_styles()
    {

        $all_styles = self::all_styles_remove();
        foreach ($all_styles as $style) {
            wp_deregister_style($style);
            wp_dequeue_style($style);
        }
    }

    public function all_scripts_hook()
    {


        return [];
    }

    public function all_styles_hook() {}

    public function footer_hook()
    {
        return [];
    }

    public function all_styles()
    {
        return [];
    }

    public function all_scripts()
    {
        return [];
    }

    public function clean_script_tag($tag)
    {

        $input = str_replace("type='text/javascript' ", '', $tag);

        return str_replace("'", '"', $input);
    }

    public function scripts()
    {


        $all_javascript = $this->all_scripts();
        if ($all_javascript) {
            foreach ($all_javascript as $javascript) {
                $ver = isset($javascript['ver']) && $javascript['ver'] != '' ? '?version=' . $javascript['ver'] : '';

                if (isset($javascript['footer'])) {
                    $footer = $javascript['footer'];
                } else {
                    $footer = true;
                }
                $address = assets_url($javascript['address'] . '.js') . $ver;
                if (isset($javascript['url']) && $javascript['url'] == true) {
                    $address = $javascript['address'];
                }

                wp_register_script(
                    $javascript['name'],
                    $address,
                    '',
                    false,
                    $footer
                );
            }

            foreach ($all_javascript as $javascript) {
                if (! (isset($javascript['loop']) && $javascript['loop'] === false)) {
                    wp_enqueue_script($javascript['name']);
                }
            }
        }


        $this->all_scripts_hook();
    }

    public function styles()
    {

        $is_rtl     = is_rtl();
        $all_styles = $this->all_styles();

        if ($all_styles) {
            foreach ($all_styles as $style) {
                $ver     = isset($style['ver']) && $style['ver'] != '' ? '?version=' . $style['ver'] : '';
                $address = assets_url($style['address'] . '.css') . $ver;
                if (isset($style['url']) && $style['url'] == true) {
                    $address = $style['address'];
                }


                wp_register_style($style['name'], $address);
            }

            foreach ($all_styles as $style) {
                $loop       = isset($style['loop']) && $style['loop'] == false ? false : true;
                $worry_show = isset($style['rtl']) && $style['rtl'] == true && $is_rtl ? true : false;
                if ($loop) {
                    if ($is_rtl) {
                        if (isset($style['rtl']) && $style['rtl'] == true) {
                            wp_enqueue_style($style['name']);
                        }
                    } else {
                        if (isset($style['rtl']) && $style['rtl'] == true) {
                            continue;
                        }
                        wp_enqueue_style($style['name']);
                    }
                }
            }
        }
        $this->all_styles_hook();
    }

    public function footer()
    {
        $this->footer_hook();
    }
}
