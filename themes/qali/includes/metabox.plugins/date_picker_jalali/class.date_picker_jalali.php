<?php
if (class_exists('RWMB_Datetime_Field')) {
    class RWMB_Date_Persian_Field extends RWMB_Datetime_Field
    {

        public static function admin_enqueue_scripts()
        {
            parent::admin_enqueue_scripts();
            wp_enqueue_script('jquery-ui-timepicker-cc',
                URL_ROOT . '/libs/metabox.plugins/date_picker_jalali/0/js/jquery-ui-timepicker-cc.min.js',
                array('jquery-ui-datepicker'), null, true);
        }

        public static function normalize($field)
        {
            $field     = parent::normalize($field);
            $old_class = isset($field['attributes']['class']) ? $field['attributes']['class'] : '';

            $field['attributes']['class'] = $old_class . ' rwmb-datetime';

            //var_dump( $field );

            return $field;
        }
    }
}
