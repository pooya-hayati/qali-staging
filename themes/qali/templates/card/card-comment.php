<?php

/**
 * Comment Card Template
 */

defined('ABSPATH') || exit;

if (! isset($this->post) || ! $this->post instanceof WP_Comment) {
	return;
}

$comment    = $this->post;
$comment_id = absint($comment->comment_ID);
$meta       = get_comment_meta_all($comment_id);
$author     = get_comment_author($comment_id);
$content    = get_comment_text($comment_id);
$date       = get_comment_date('c', $comment_id);
$time       = digitConverter(date_i18n('l، j F Y - H:i:s', strtotime($comment->comment_date)), SITE_LANG);
$rating     = ! empty($meta['rating']) ? absint($meta['rating']) : 0;
$rating     = min(max($rating, 0), 5);
$rating_pct = $rating * 20;
?>
<li class="comment-card" id="comment-<?php echo esc_attr($comment_id); ?>" itemscope itemtype="https://schema.org/Review">
	<div class="comment-card-header">

		<ul class="comment-card-meta">
			<li>
				<span class="comment-card-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
					<span itemprop="name"><?php echo esc_html($author); ?></span>
				</span>
			</li>
			<li>
				<time class="comment-card-time"
					datetime="<?php echo esc_attr($date); ?>"
					itemprop="datePublished">
					<?php echo esc_html($time); ?>
				</time>
			</li>
			<?php if ($rating > 0) : ?>
				<li>
					<div class="rating-wrapper" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
						<meta itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>">
						<meta itemprop="bestRating" content="5">
						<meta itemprop="worstRating" content="1">
						<div class="rating-empty" aria-hidden="true">
							<span>★</span>
							<span>★</span>
							<span>★</span>
							<span>★</span>
							<span>★</span>
						</div>
						<div class="rating-filled" style="width: <?php echo esc_attr($rating_pct); ?>%" aria-hidden="true">
							<span>★</span>
							<span>★</span>
							<span>★</span>
							<span>★</span>
							<span>★</span>
						</div>
						<span class="screen-reader-text">
							<?php
							printf(
								esc_html__('Rated %s out of 5', LANG_STRING),
								number_format_i18n($rating, 1)
							);
							?>
						</span>
					</div>
				</li>
			<?php endif; ?>
		</ul>
	</div>

	<div class="comment-card-body">
		<div class="comment-card-desc" itemprop="reviewBody">
			<?php echo wp_kses_post(wpautop($content)); ?>
		</div>
	</div>
</li>