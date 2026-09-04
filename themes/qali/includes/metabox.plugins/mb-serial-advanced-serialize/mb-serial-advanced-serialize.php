<?php
/**
 * Plugin Name: Serial Advanced Serialize
 * Plugin URI: https://navidsafavi.com/meta-box/advanced-serialize
 * Description: Select Advance with Serialize save in database
 * Version: 1.2
 * Author: Navidsafavi
 * Author URI: https://navidsafavi.com
 * Plugin URL URI: https://metabox.io
 * License: GPL2+
 *
 * @package Meta Box
 * @subpackage Meta Box Group
 */

defined('ABSPATH') || exit;
if (class_exists('RWMB_Select_Advanced_Field')) {
    class RWMB_Select_Serial_Field extends RWMB_Select_Advanced_Field
    {

        public static function admin_enqueue_scripts()
        {
            parent::admin_enqueue_scripts();
        }

        public static function raw_meta($post_id, $field, $args = array())
        {
            $data = parent::raw_meta($post_id, $field, $args);
            $data = isset($data[0]) ? $data[0] : $data;

            return $data;
        }

        public static function normalize($field)
        {
            $field                        = parent::normalize($field);
            $field['attributes']['class'] = isset($field['attributes']['class']) ? $field['attributes']['class'] : '' . ' rwmb-select_advanced';
            $field['field_type']          = 'select_advanced';

            return $field;
        }

        public static function save($new, $old, $post_id, $field)
        {
            $field['multiple'] = false;
            parent::save($new, array(), $post_id, $field);
        }
    }
}