<?php

namespace Core;

class Settings
{

    public function register()
    {

        add_filter('mb_settings_pages', [$this, 'theme_settings_page']);
        add_filter('rwmb_meta_boxes', [$this, 'field_theme_settings_page']);
    }

    function parent_setting()
    {
        return [];
    }

    function child_setting()
    {
        return [];
    }

    function field_theme_settings_page($meta_boxes)
    {

        if ($this->child_setting()) {
            $meta_boxes = array_merge($meta_boxes, $this->child_setting());
        }
        return $meta_boxes;
    }

    function theme_settings_page($settings_pages)
    {
        if ($this->parent_setting()) {
            $settings_pages[] = $this->parent_setting();
        }


        return $settings_pages;
    }
}
