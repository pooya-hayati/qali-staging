<?php

/**
 * Helpers methods
 * List all your static functions you wish to use globally on your theme
 *
 * @package awps
 */

if (! class_exists('Template_Part')) {
    class Template_Part_Custom
    {
        private $args;
        private $file;

        public function __get($name)
        {
            return isset($this->args[$name]) ? $this->args[$name] : '';
        }

        public function __construct($file, $args = array())
        {
            $this->file = $file;
            $this->args = $args;
        }

        public function __isset($name)
        {
            return isset($this->args[$name]);
        }

        public function render()
        {
            if (locate_template($this->file)) {
                include(locate_template($this->file)); //Theme Check free. Child themes support.
                $check = true;
            } else {
                $check = false;
            }

            return $check;
        }
    }
}

if (! function_exists('get_template_part_var')) {
    function get_template_part_var($file, $args = array())
    {
        $template = new Template_Part_Custom($file, $args);
        $check    = $template->render();

        return $check;
    }
}
if (! function_exists('get_post_by_meta_value')) {
    function get_post_by_meta_value($key = '', $status = 'publish')
    {
        global $wpdb;
        if (empty($key)) {
            return;
        }
        $r = $wpdb->get_results($wpdb->prepare("
        SELECT p.ID, pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = '%s' 
        AND p.post_status = '%s' 
    ", $key, $status));

        foreach ($r as $my_r) {
            $metas[$my_r->ID] = $my_r->meta_value;
        }

        return $metas;
    }
}
function digitConverter($str, $convert_to = 'fa')
{
    $fa = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
    $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $en = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

    $str = str_replace($fa, $en, $str);
    $str = str_replace($ar, $en, $str);

    if ($convert_to === 'fa') {
        return str_replace($en, $fa, $str);
    } elseif ($convert_to === 'ar') {
        return str_replace($en, $ar, $str);
    } elseif ($convert_to === 'en') {
        return $str;
    }

    return $str;
}
function numberToWords($number)
{
    $ones = [
        "",
        "یک",
        "دو",
        "سه",
        "چهار",
        "پنج",
        "شش",
        "هفت",
        "هشت",
        "نه",
    ];

    $tens = [
        "",
        "ده",
        "بیست",
        "سی",
        "چهل",
        "پنجاه",
        "شصت",
        "هفتاد",
        "هشتاد",
        "نود",
    ];

    $teens = [
        "ده",
        "یازده",
        "دوازده",
        "سیزده",
        "چهارده",
        "پانزده",
        "شانزده",
        "هفده",
        "هجده",
        "نوزده",
    ];

    $hundreds = [
        "",
        "یکصد",
        "دویست",
        "سیصد",
        "چهارصد",
        "پانصد",
        "ششصد",
        "هفتصد",
        "هشتصد",
        "نهصد",
    ];

    $thousands = [
        "",
        "هزار",
        "میلیون",
        "میلیارد",
        "تریلیون",
        "کوآدریلیون",
        "کوانتیلیون",
    ];

    if ($number == 0) {
        return "صفر";
    }

    $negative = $number < 0;
    $number = abs($number);

    $numberParts = explode('.', (string)$number);
    $integerPart = (int)$numberParts[0];
    $decimalPart = isset($numberParts[1]) ? $numberParts[1] : null;

    $result = convertInteger($integerPart, $ones, $tens, $teens, $hundreds, $thousands);

    if ($decimalPart !== null) {
        $result .= " ممیز " . convertDecimals($decimalPart, $ones);
    }

    return ($negative ? "منفی " : "") . $result;
}

function convertInteger($number, $ones, $tens, $teens, $hundreds, $thousands)
{
    $parts = [];
    $thousandCounter = 0;

    while ($number > 0) {
        $chunk = $number % 1000;
        $number = intdiv($number, 1000);

        if ($chunk > 0) {
            $parts[] = convertChunk($chunk, $ones, $tens, $teens, $hundreds) . ($thousands[$thousandCounter] ? " " . $thousands[$thousandCounter] : "");
        }

        $thousandCounter++;
    }

    return implode(" و ", array_reverse($parts));
}

function convertDecimals($decimalPart, $ones)
{
    $digits = str_split($decimalPart);
    $words = array_map(function ($digit) use ($ones) {
        return $ones[(int)$digit];
    }, $digits);

    return implode(" ", $words);
}

function convertChunk($number, $ones, $tens, $teens, $hundreds)
{
    $result = [];

    if ($number >= 100) {
        $result[] = $hundreds[intdiv($number, 100)];
        $number %= 100;
    }

    if ($number >= 10 && $number < 20) {
        $result[] = $teens[$number - 10];
        $number = 0;
    } elseif ($number >= 20) {
        $result[] = $tens[intdiv($number, 10)];
        $number %= 10;
    }

    if ($number > 0) {
        $result[] = $ones[$number];
    }

    return implode(" و ", $result);
}
function assets_url($path)
{
    return URL_ASSETS . '/' . $path;
}

function trim_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function wp_get_attachment($attachment_id)
{

    $attachment = get_post($attachment_id);

    return array(
        'alt'         => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'caption'     => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href'        => get_permalink($attachment->ID),
        'src'         => $attachment->guid,
        'title'       => $attachment->post_title
    );
}

function check_mobile_number($mobile)
{
    if (preg_match("/^09\d{9}$/", $mobile) && strlen($mobile) == 11) {
        return true;
    } else {
        return false;
    }
}
function convertToInternational($mobile)
{
    if (strpos($mobile, '0') === 0) {
        // '۰' را حذف کرده و '۹۸' را به ابتدای آن اضافه می‌کنیم
        $convertedNumber = '98' . substr($mobile, 1);
        return $convertedNumber;
    }
}

function log_msg($message, $level = 1)
{
    if (is_array($message)) {
        $message = print_r($message, true);
    } elseif (is_object($message)) {
        $message = print_r((array)$message, true);
    }
    $now  = current_time('timestamp');
    $date = date('Y-m-d H:i:s', $now);
    switch ($level) {
        case 1:
            $error_level = "INFO";
            break;
        case 2:
            $error_level = "WARNING";
            break;
        case 3:
            $error_level = "ERROR";
            break;
        default:
            $error_level = '';
    }

    error_log("[{$date}] {$error_level} | {$message} \n", 3, ABSPATH . '/theme.log');
}

function shamsi_to_mildai($date)
{

    if (class_exists('bn_parsidate')) {

        $date_persian = new bn_parsidate();
        list($year, $month, $day) = explode('-', $date);

        $date_miladi    = $date_persian->persian_to_gregorian($year, $month, $day);
        $date_miladi[1] = $date_miladi[1] >= 10 ? $date_miladi[1] : '0' . $date_miladi[1];
        $date_miladi[2] = $date_miladi[2] >= 10 ? $date_miladi[2] : '0' . $date_miladi[2];
        $date           = implode('-', $date_miladi);
    }

    return $date;
}

function miladi_to_shamsi($date, $format = 'Y-m-d')
{

    if (class_exists('bn_parsidate')) {
        $date_persian = new bn_parsidate();
        $date         = $date_persian->persian_date($format, $date, 'en');
    }

    return $date;
}

function parse_args($default, $_data)
{
    $pars  = wp_parse_args($_data, $default);
    $short = shortcode_atts($default, $pars);

    return $short;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) == $date;
}

function _get_all_meta($id, $type = 'post')
{
    if (empty($id)) {
        return []; // Always return an array for consistency
    }

    // Retrieve metadata based on type
    $meta_functions = [
        'post' => 'get_post_meta',
        'term' => 'get_term_meta',
        'user' => 'get_user_meta',
        'comment' => 'get_comment_meta',
    ];

    if (!array_key_exists($type, $meta_functions)) {
        return []; // Unsupported type
    }

    $all_meta = call_user_func($meta_functions[$type], $id);
    $processed_meta = [];

    foreach ($all_meta as $key_meta => $meta_values) {
        $processed_meta[$key_meta] = array_map('_process_meta_value', $meta_values);

        // Flatten single-value metas for ease of use
        if (count($processed_meta[$key_meta]) === 1) {
            $processed_meta[$key_meta] = $processed_meta[$key_meta][0];
        }
    }

    // Filter out invalid or empty values
    return array_filter($processed_meta, '_is_valid_meta_value');
}

/**
 * Process individual meta value.
 *
 * @param mixed $value Meta value to process.
 * @return mixed Processed meta value.
 */
function _process_meta_value($value)
{
    // Handle serialized data
    if (is_serialized($value)) {
        $value = maybe_unserialize($value);
    }

    // Handle JSON encoded data
    if (is_string($value) && is_json($value)) {
        $value = json_decode($value, true);
    }

    // Handle boolean values stored as strings
    if (is_string($value) && ($value === 'true' || $value === 'false')) {
        $value = ($value === 'true');
    }

    // Recursively process nested data with recursion limit
    if (is_array($value) || is_object($value)) {
        $value = _maybe_recursive_unserialize($value);
    }

    return $value;
}

/**
 * Recursively unserialize arrays or objects with recursion limit.
 *
 * @param mixed $data Data to recursively unserialize.
 * @param int $depth Current recursion depth.
 * @param int $max_depth Maximum allowed recursion depth.
 * @return mixed Unserialized data.
 */
function _maybe_recursive_unserialize($data, $depth = 0, $max_depth = 10)
{
    if ($depth > $max_depth) {
        return $data; // Prevent infinite recursion
    }

    if (is_array($data)) {
        return array_map(function ($value) use ($depth, $max_depth) {
            return _process_meta_value($value, $depth + 1, $max_depth);
        }, $data);
    }

    if (is_object($data)) {
        foreach ($data as $key => $value) {
            $data->$key = _maybe_recursive_unserialize($value, $depth + 1, $max_depth);
        }
    }

    return $data;
}

/**
 * Check if a value is valid meta data.
 *
 * @param mixed $value Value to check.
 * @return bool True if valid, false otherwise.
 */
function _is_valid_meta_value($value)
{
    return !(is_null($value) || $value === false || $value === '');
}

/**
 * Check if a string is valid JSON.
 *
 * @param string $string String to check.
 * @return bool True if valid JSON, false otherwise.
 */
function is_json($string)
{
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

function get_term_meta_all($ID)
{
    return _get_all_meta($ID, 'term');
}
function get_post_meta_all($ID)
{
    return _get_all_meta($ID);
}
function get_user_meta_all($user_id)
{
    return _get_all_meta($user_id, 'user');
}
function get_comment_meta_all($comment_id)
{
    return _get_all_meta($comment_id, 'comment');
}

function convertNumbers($srting, $toPersian = true)
{
    $en_num = array(
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0'
    );
    $fa_num = array(
        '۰',
        '۱',
        '۲',
        '۳',
        '۴',
        '۵',
        '۶',
        '۷',
        '۸',
        '۹',
        '١',
        '٢',
        '٣',
        '٤',
        '٥',
        '٦',
        '٧',
        '٨',
        '٩',
        '٠'
    );
    if ($toPersian) {
        return str_replace($en_num, $fa_num, $srting);
    } else {
        return str_replace($fa_num, $en_num, $srting);
    }
}

function get_post_id_by_meta_value_key($key, $value, $post_type = 'all')
{
    global $wpdb;
    if ($post_type == 'all') {
        $sql = "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key='" . $key . "' AND meta_value='" . $value . "'";
    } else {
        $sql = "
          SELECT POSTMETA.* FROM " . $wpdb->postmeta . " AS POSTMETA
          INNER JOIN " . $wpdb->posts . " AS POST ON POST.ID = POSTMETA.post_id
          WHERE meta_key='" . $key . "' AND meta_value='" . $value . "' AND POST.post_type = '" . $post_type . "'";
    }

    $meta = $wpdb->get_results($sql);
    if (is_array($meta) && ! empty($meta)) {

        if (count($meta) === 1) {
            $meta = $meta[0];
        } else {
            $objects = [];
            foreach ($meta as $_meta) {
                $objects[] = $_meta->post_id;
            }
            return $objects;
        }
    }
    if (is_object($meta)) {
        return $meta->post_id;
    } else {
        return false;
    }
}

function get_meta_values($key = '', $status = 'publish')
{
    global $wpdb;
    if (empty($key)) {
        return;
    }
    $r = $wpdb->get_results($wpdb->prepare("
        SELECT p.ID, pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = '%s' 
        AND p.post_status = '%s' 
    ", $key, $status));

    foreach ($r as $my_r) {
        $metas[$my_r->ID] = $my_r->meta_value;
    }

    return $metas;
}

function image_link($thumb_id, $size = 'thumbnail', $only_image = true, $default_image = null)
{
    $default_image = $default_image != null ? $default_image : assets_url('img/thumbnail.svg');
    $image_url     = wp_get_attachment_image_src($thumb_id, $size);
    if (! $image_url) {
        $image_url = array(
            $default_image,
            400,
            650
        );
    }

    return $only_image ? $image_url[0] : $image_url;
}

function file_link($attach_id)
{
    $file_url = wp_get_attachment_url($attach_id);

    return $file_url;
}

function post_image($ID, $size = 'thumbnail', $only_image = true)
{
    return image_link(get_post_thumbnail_id($ID), $size, $only_image);
}

function seconds_to_time($seconds, $string_type = true, $without_seconds = true)
{

    $hours = floor($seconds / 3600);
    $mins  = floor($seconds / 60 % 60);
    $secs  = floor($seconds % 60);
    $text  = '';

    if ($without_seconds) {
        $timeFormat = sprintf('%02d:%02d', $hours, $mins);
        $text       = sprintf(__('%s Hours and %s Minute', LANG_STRING), $hours, $mins);
    } else {
        $timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        $text       = sprintf(__('%s Hours and %s Minute and %s Seconds', LANG_STRING), $hours, $mins, $secs);
    }
    if ($string_type) {
        return $text;
    } else {
        return $timeFormat;
    }
}

function time_to_seconds($str_time)
{

    $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

    $hours = $minutes = $seconds = 0;
    sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

    $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

    return $time_seconds;
}

function show_price($price)
{
    if ($price) {
        $price = sprintf(__('%s $', LANG_STRING), number_format($price));
    } else {
        $price = __('Free', LANG_STRING);
    }

    return $price;
}
