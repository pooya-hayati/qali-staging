<?php
if (class_exists('RWMB_Image_Advanced_Field')) {

    class RWMB_Image_Advanced_Serial_Field extends RWMB_Image_Advanced_Field
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
            $field['attributes']['class'] = isset($field['attributes']['class']) ? $field['attributes']['class'] : '' . ' rwmb-image_advanced';

            return $field;
        }

        public static function save($new, $old, $post_id, $field)
        {
            $field['multiple'] = false;
            parent::save($new, array(), $post_id, $field);
        }
    }


}
