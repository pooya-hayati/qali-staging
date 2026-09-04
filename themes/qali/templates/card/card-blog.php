<?php

/**
 * Blog Card Template
 */

defined('ABSPATH') || exit;

if (! isset($this->post) || ! $this->post instanceof WP_Post) {
	return;
}

$post       = $this->post;
$post_id    = absint($post->ID);
$title      = get_the_title($post_id);
$link       = get_permalink($post_id);
$image      = post_image($post_id, 'full');
$image_alt  = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
$image_alt  = $image_alt ?: $title;
$excerpt    = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags(get_the_content(null, false, $post_id)), 30, '…');
$date       = get_the_date('c', $post_id);
$date_display = get_the_date('F j, Y', $post_id);
?>
<article class="blog-card" data-animate="fadeInUp" itemscope itemtype="https://schema.org/BlogPosting">
	<div class="blog-card-header">
		<?php if ($image) : ?>
			<img src="<?php echo esc_url($image); ?>"
				alt="<?php echo esc_attr($image_alt); ?>"
				class="blog-card-img"
				itemprop="image"
				loading="lazy">
		<?php endif; ?>
	</div>
	<div class="blog-card-body">
		<h3 class="blog-card-title" itemprop="headline">
			<?php echo esc_html($title); ?>
		</h3>
		<time class="blog-card-date" datetime="<?php echo esc_attr($date); ?>" itemprop="datePublished">
			<?php echo esc_html($date_display); ?>
		</time>
		<?php if ($excerpt) : ?>
			<meta itemprop="description" content="<?php echo esc_attr(wp_strip_all_tags($excerpt)); ?>">
		<?php endif; ?>
		<meta itemprop="author" content="<?php echo esc_attr(get_the_author_meta('display_name', $post->post_author)); ?>">
		<link itemprop="url" href="<?php echo esc_url($link); ?>">
	</div>
	<a href="<?php echo esc_url($link); ?>"
		class="overlay-link"
		aria-label="<?php echo esc_attr(sprintf(__('Read more about: %s', LANG_STRING), $title)); ?>">
		<span class="screen-reader-text"><?php echo esc_html($title); ?></span>
	</a>
</article>