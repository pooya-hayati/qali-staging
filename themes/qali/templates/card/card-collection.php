<?php

/**
 * Collection Card Template
 */

defined('ABSPATH') || exit;

if (! isset($this->post) || ! $this->post instanceof WP_Term) {
	return;
}

$term        = $this->post;
$term_id     = absint($term->term_id);
$meta        = get_term_meta_all($term_id);
$title       = esc_html($term->name);
$description = ! empty($term->description) ? wp_trim_words(wp_strip_all_tags($term->description), 20, '…') : '';
$link        = get_term_link($term);

if (is_wp_error($link)) {
	return;
}

$image       = ! empty($meta['thumbnail_id']) ? image_link($meta['thumbnail_id'], 'full') : '';
$image_alt   = ! empty($meta['thumbnail_id']) ? get_post_meta(absint($meta['thumbnail_id']), '_wp_attachment_image_alt', true) : '';
$image_alt   = $image_alt ?: $title;
$term_count  = absint($term->count);
?>
<article class="collection-card" data-animate="fadeInUp" itemscope itemtype="https://schema.org/Collection">
	<div class="collection-card-header">
		<?php if ($image) : ?>
			<img src="<?php echo esc_url($image); ?>"
				alt="<?php echo esc_attr($image_alt); ?>"
				class="collection-card-img"
				itemprop="image"
				loading="lazy">
		<?php endif; ?>
	</div>
	<div class="collection-card-body">
		<h3 class="collection-card-title" itemprop="name">
			<?php echo esc_html($title); ?>
		</h3>
		<?php if ($description) : ?>
			<div class="collection-card-desc" itemprop="description">
				<?php echo esc_html($description); ?>
			</div>
		<?php endif; ?>
		<?php if ($term_count > 0) : ?>
			<div class="collection-card-count">
				<?php
				printf(
					esc_html(_n('%s item', '%s items', $term_count, 'qali')),
					number_format_i18n($term_count)
				);
				?>
			</div>
		<?php endif; ?>
		<link itemprop="url" href="<?php echo esc_url($link); ?>">
	</div>
	<a href="<?php echo esc_url($link); ?>"
		class="overlay-link"
		aria-label="<?php echo esc_attr(sprintf(__('View collection: %s', 'qali'), $title)); ?>">
		<span class="screen-reader-text"><?php echo esc_html($title); ?></span>
	</a>
</article>