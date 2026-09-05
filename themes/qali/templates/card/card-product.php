<?php

/**
 * Product Card Template
 */

defined('ABSPATH') || exit;

if (! isset($this->post) || ! $this->post instanceof WP_Post) {
	return;
}

$post    = $this->post;
$post_id = absint($post->ID);
$product = wc_get_product($post_id);

if (! $product || ! is_a($product, 'WC_Product')) {
	return;
}

$title        = get_the_title($post_id);
$link         = get_permalink($post_id);
$thumbnail_id = get_post_thumbnail_id($post_id);
$image_alt    = $thumbnail_id ? get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) : '';
$image_alt    = $image_alt ?: $title;

// Hover-swap image: first gallery image after the featured one, if any.
// Deferred via data-src/data-srcset (set on hover by shop.js) rather than
// loading="lazy", so products that are never hovered cost zero extra
// requests/bytes on top of #13's image-weight work.
$gallery_ids     = $product->get_gallery_image_ids();
$hover_image_id  = ! empty($gallery_ids) ? reset($gallery_ids) : null;
$hover_image_src = $hover_image_id ? wp_get_attachment_image_src($hover_image_id, 'qali-product-card-2x') : false;
$hover_image_srcset = $hover_image_id ? wp_get_attachment_image_srcset($hover_image_id, 'qali-product-card-2x') : false;
$is_in_stock  = $product->is_in_stock();
$price        = $product->get_price();
$price_html   = $product->get_price_html();
$currency     = get_woocommerce_currency();
$rating       = $product->get_average_rating();
$review_count = $product->get_review_count();
$user_id      = get_current_user_id();
$wishlist     = $user_id ? get_user_meta($user_id, '_user_wishlist', true) : [];
$wishlist     = is_array($wishlist) ? $wishlist : [];
$is_wished    = in_array($post_id, $wishlist, true);
?>
<article class="product-card" data-animate="fadeInUp" itemscope itemtype="https://schema.org/Product">
	<div class="product-card-header">
		<a href="<?php echo esc_url($link); ?>"
			aria-label="<?php echo esc_attr(sprintf(__('View product: %s', LANG_STRING), $title)); ?>">
			<?php if ($thumbnail_id) : ?>
				<?php echo wp_get_attachment_image($thumbnail_id, 'qali-product-card-2x', false, [
					'class'    => 'product-card-img',
					'itemprop' => 'image',
					'loading'  => 'lazy',
					'alt'      => $image_alt,
				]); ?>
			<?php else : ?>
				<img src="<?php echo esc_url(assets_url('img/thumbnail.svg')); ?>"
					alt="<?php echo esc_attr($image_alt); ?>"
					class="product-card-img"
					itemprop="image"
					loading="lazy">
			<?php endif; ?>

			<?php if ($hover_image_src) : ?>
				<img class="product-card-img product-card-img-hover"
					data-src="<?php echo esc_url($hover_image_src[0]); ?>"
					<?php if ($hover_image_srcset) : ?>data-srcset="<?php echo esc_attr($hover_image_srcset); ?>"<?php endif; ?>
					width="<?php echo esc_attr($hover_image_src[1]); ?>"
					height="<?php echo esc_attr($hover_image_src[2]); ?>"
					alt=""
					aria-hidden="true">
			<?php endif; ?>

			<span class="product-card-overlay" aria-hidden="true">
				<span class="product-card-quickview">
					<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="8.5" cy="8.5" r="6.5" stroke="currentColor" stroke-width="1.8" />
						<path d="M17 17L13.5 13.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
					</svg>
				</span>
			</span>
		</a>
	</div>

	<div class="product-card-body">
		<h3 class="product-card-title" itemprop="name">
			<a href="<?php echo esc_url($link); ?>">
				<?php echo esc_html($title); ?>
			</a>
		</h3>

		<div class="product-card-meta">
			<?php if ($is_in_stock && $price !== '') : ?>
				<span class="product-card-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
					<meta itemprop="price" content="<?php echo esc_attr($price); ?>">
					<meta itemprop="priceCurrency" content="<?php echo esc_attr($currency); ?>">
					<link itemprop="availability" href="https://schema.org/InStock">
					<?php echo wp_kses_post($price_html); ?>
				</span>
			<?php elseif (! $is_in_stock) : ?>
				<span class="product-card-price product-card-price-out" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
					<link itemprop="availability" href="https://schema.org/OutOfStock">
					<?php echo esc_html__('Sold Out', LANG_STRING); ?>
				</span>
			<?php endif; ?>

			<?php if ($user_id) : ?>
				<button type="button"
					class="wishlist-button <?php echo $is_wished ? 'active' : ''; ?>"
					data-product-id="<?php echo esc_attr($post_id); ?>"
					aria-label="<?php echo esc_attr($is_wished ? __('Remove from wishlist', LANG_STRING) : __('Add to wishlist', LANG_STRING)); ?>"
					aria-pressed="<?php echo $is_wished ? 'true' : 'false'; ?>">
					<span aria-hidden="true">❤</span>
				</button>
			<?php endif; ?>
		</div>

		<?php if ($rating > 0) : ?>
			<div class="product-card-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
				<meta itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>">
				<meta itemprop="reviewCount" content="<?php echo esc_attr($review_count); ?>">
				<meta itemprop="bestRating" content="5">
				<span class="rating-stars" aria-label="<?php echo esc_attr(sprintf(__('Rated %s out of 5', LANG_STRING), number_format_i18n($rating, 1))); ?>">
					<?php echo wc_get_rating_html($rating, $review_count); ?>
				</span>
			</div>
		<?php endif; ?>

		<link itemprop="url" href="<?php echo esc_url($link); ?>">
	</div>

	<div class="product-card-footer">
		<a href="<?php echo esc_url($link); ?>"
			class="product-card-btn"
			aria-label="<?php echo esc_attr(sprintf(__('View details for: %s', LANG_STRING), $title)); ?>">
			<?php echo esc_html__('View Details', LANG_STRING); ?>
		</a>
	</div>
</article>