<?php

/**
 * Single Product Template
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) :
	the_post();

	$post_id   = get_the_ID();
	$product   = wc_get_product($post_id);

	if (! $product || ! is_a($product, 'WC_Product')) {
		continue;
	}

	$title      = get_the_title($post_id);
	$meta       = get_post_meta_all($post_id);
	$sku        = $product->get_sku();
	$categories = get_the_terms($post_id, 'product_cat');
	$tags       = get_the_terms($post_id, 'product_tag');
	$is_in_stock = $product->is_in_stock();
	$price      = $product->get_price();
	$price_html = $product->get_price_html();
	$image_url  = get_the_post_thumbnail_url($post_id, 'full');
	$image_alt  = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true) ?: $title;

	$user_id    = get_current_user_id();
	$wishlist   = $user_id ? get_user_meta($user_id, '_user_wishlist', true) : [];
	$wishlist   = is_array($wishlist) ? $wishlist : [];
	$is_wished  = in_array($post_id, $wishlist, true);

	$attributes = ! empty($meta['_product_attributes']) ? $meta['_product_attributes'] : [];
	if (isset($attributes['pa_size'])) {
		unset($attributes['pa_size']);
	}

	$size = '';
	$area = '';
	if (! empty($meta['_length']) && ! empty($meta['_width'])) {
		$length_cm = absint($meta['_length']);
		$width_cm  = absint($meta['_width']);
		$length_ft = cmToFt($length_cm);
		$width_ft  = cmToFt($width_cm);
		$area_m    = ($length_cm * $width_cm) / 10000;

		$size = sprintf('%sx%s ft (%sx%s cm)', $length_ft, $width_ft, $length_cm, $width_cm);
		$area = sprintf('%s ft (%s m)', $length_ft * $width_ft, number_format($area_m, 2));
	}

	$term_descriptions = [
		'design'    => wp_get_post_terms($post_id, 'pa_design'),
		'feel'      => wp_get_post_terms($post_id, 'pa_feel'),
		'group'     => $categories,
		'material'  => wp_get_post_terms($post_id, 'pa_material'),
		'origin'    => wp_get_post_terms($post_id, 'pa_origin'),
		'shape'     => wp_get_post_terms($post_id, 'pa_shape'),
		'thickness' => wp_get_post_terms($post_id, 'pa_thickness'),
		'color'     => wp_get_post_terms($post_id, 'pa_color'),
		'size_term' => wp_get_post_terms($post_id, 'pa_size'),
	];

	$descriptions = [];
	foreach ($term_descriptions as $key => $terms) {
		if (! empty($terms) && ! is_wp_error($terms) && isset($terms[0]->description)) {
			$descriptions[$key] = $terms[0]->description;
		} else {
			$descriptions[$key] = '';
		}
	}

	if (! empty($descriptions['size_term']) && ! empty($size)) {
		$descriptions['size_term'] = str_replace('[SIZE]', $size, $descriptions['size_term']);
	}

	$color_tips = [];
	if (! empty($descriptions['color'])) {
		preg_match_all('/<li>\s*<b>(.*?):<\/b>\s*<div>(.*?)<\/div>\s*<\/li>/s', $descriptions['color'], $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$color_tips[] = [
				'title' => trim($match[1]),
				'desc'  => trim($match[2]),
			];
		}
	}
?>

	<article id="product-<?php echo esc_attr($post_id); ?>" <?php post_class('section section-product-single'); ?> itemscope itemtype="https://schema.org/Product">
		<meta itemprop="name" content="<?php echo esc_attr($title); ?>">
		<meta itemprop="sku" content="<?php echo esc_attr($sku); ?>">
		<?php if ($image_url) : ?>
			<meta itemprop="image" content="<?php echo esc_url($image_url); ?>">
		<?php endif; ?>
		<link itemprop="url" href="<?php echo esc_url(get_permalink()); ?>">

		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row g-3">
					<div class="col-lg-3 order-lg-1">
						<div class="product-sidebar">
							<header class="product-sidebar-header">
								<h1 class="product-info-title" itemprop="name">
									<?php echo esc_html($title); ?>
								</h1>
								<?php if ($size) : ?>
									<h2 class="product-info-subtitle">
										<?php echo esc_html($size); ?>
									</h2>
								<?php endif; ?>
								<span class="product-card-badge">
									<?php echo esc_html__('One of One', LANG_STRING); ?>
								</span>
								<?php if ($image_url) : ?>
									<div class="product-sidebar-cover">
										<img src="<?php echo esc_url($image_url); ?>"
											alt="<?php echo esc_attr($image_alt); ?>"
											class="product-sidebar-img"
											loading="lazy">
									</div>
								<?php endif; ?>
							</header>

							<footer class="product-sidebar-footer">
								<div class="product-action">
									<?php if ($price !== '') : ?>
										<div class="product-info-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
											<meta itemprop="price" content="<?php echo esc_attr($price); ?>">
											<meta itemprop="priceCurrency" content="<?php echo esc_attr(get_woocommerce_currency()); ?>">
											<link itemprop="availability" href="https://schema.org/InStock">
											<?php echo wp_kses_post($price_html); ?>
										</div>
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

									<button type="button"
										data-product_id="<?php echo esc_attr($post_id); ?>"
										class="product-action-cart button button-fill-primary button-large <?php echo ! $is_in_stock ? 'soldout' : ''; ?>"
										<?php echo ! $is_in_stock ? 'disabled' : ''; ?>
										aria-label="<?php echo esc_attr($is_in_stock ? __('Buy Now', LANG_STRING) : __('Sold Out', LANG_STRING)); ?>">
										<span class="btn-icon"><i class="icon-cart"></i></span>
										<span class="btn-text">
											<?php echo esc_html($is_in_stock ? __('Buy Now', LANG_STRING) : __('Sold Out', LANG_STRING)); ?>
										</span>
									</button>
								</div>
							</footer>
						</div>
					</div>

					<div class="col-lg-3 order-lg-3">
						<div class="product-sidebar">
							<div class="product-sidebar-footer">
								<ul class="product-info">
									<?php if ($sku) : ?>
										<li>
											<strong><?php echo esc_html__('SKU', LANG_STRING); ?></strong>
											<div><?php echo esc_html($sku); ?></div>
										</li>
									<?php endif; ?>

									<?php if ($size) : ?>
										<li>
											<strong><?php echo esc_html__('Size', LANG_STRING); ?></strong>
											<div><?php echo esc_html($size); ?></div>
										</li>
									<?php endif; ?>

									<?php if ($area) : ?>
										<li>
											<strong><?php echo esc_html__('Area', LANG_STRING); ?></strong>
											<div><?php echo esc_html($area); ?></div>
										</li>
									<?php endif; ?>

									<?php if (! empty($categories) && ! is_wp_error($categories)) : ?>
										<li>
											<strong><?php echo esc_html__('Category', LANG_STRING); ?></strong>
											<div>
												<?php
												$category_links = array_map(
													function ($term) {
														return sprintf(
															'<a href="%s" rel="tag">%s</a>',
															esc_url(get_term_link($term)),
															esc_html($term->name)
														);
													},
													$categories
												);
												echo wp_kses_post(implode(', ', $category_links));
												?>
											</div>
										</li>
									<?php endif; ?>

									<?php if (! empty($attributes)) : ?>
										<?php foreach ($attributes as $attribute_key => $attribute_data) : ?>
											<?php if (empty($attribute_data['is_taxonomy'])) : ?>
												<li>
													<strong><?php echo esc_html($attribute_data['name']); ?></strong>
													<div>
														<ul>
															<li><?php echo wp_kses_post(wrap_non_farsi_start($attribute_data['value'])); ?></li>
														</ul>
													</div>
												</li>
											<?php else : ?>
												<?php
												$tax_attr = wp_get_post_terms($post_id, $attribute_data['name']);
												if (! empty($tax_attr) && ! is_wp_error($tax_attr)) :
													$taxonomy = get_taxonomy($attribute_data['name']);
												?>
													<li>
														<strong><?php echo esc_html($taxonomy->labels->singular_name); ?></strong>
														<div>
															<?php
															$attr_links = array_map(
																function ($term) {
																	return sprintf(
																		'<a href="%s" rel="tag">%s</a>',
																		esc_url(get_term_link($term)),
																		esc_html($term->name)
																	);
																},
																$tax_attr
															);
															echo wp_kses_post(implode(', ', $attr_links));
															?>
														</div>
													</li>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									<?php endif; ?>
								</ul>
							</div>
						</div>
					</div>

					<div class="col-lg-6 order-lg-2">
						<div class="entry">
							<div class="entry-content">
								<?php if (! empty($meta['page']['card'])) : ?>
									<div class="presentation-grid row g-3">
										<?php foreach ((array) $meta['page']['card'] as $index => $card) : ?>
											<?php if ($index === 1) : ?>
												<div class="col-md-12">
													<div class="product-stylist">
														<button type="button"
															class="product-style-action button button-outline-primary button-block"
															data-toggle="modal"
															data-target="#product-style">
															<?php echo esc_html__('See Insights and Styling Guides', LANG_STRING); ?>
														</button>
													</div>
												</div>

												<div class="col-md-12">
													<div class="product-ar">
														<div class="row g-5">
															<div class="col-xl-12">
																<div class="product-ar-header">
																	<h3 class="product-ar-title">
																		<?php echo esc_html__('Use AR to See this Rug in Your Space', LANG_STRING); ?>
																	</h3>
																</div>
															</div>
															<div class="col-xl-6">
																<div class="product-ar-body">
																	<div class="product-ar-desc">
																		<?php echo esc_html__('Open this page using your phone browser.', LANG_STRING); ?>
																	</div>
																	<?php if ($sku) : ?>
																		<div class="product-ar-nav">
																			<?php
																			echo do_shortcode(
																				sprintf(
																					"[dressmycrib-viewer sku='%s' product_title='%s']",
																					esc_attr($sku),
																					esc_attr($title)
																				)
																			);
																			?>
																		</div>
																	<?php endif; ?>
																</div>
															</div>
															<div class="col-xl-6">
																<div class="product-ar-footer">
																	<?php if ($image_url) : ?>
																		<img src="<?php echo esc_url($image_url); ?>"
																			alt="<?php echo esc_attr($image_alt); ?>"
																			class="product-ar-img"
																			loading="lazy">
																	<?php endif; ?>
																</div>
															</div>
														</div>
													</div>
												</div>
											<?php endif; ?>

											<div class="col-md-<?php echo esc_attr($card['size'] ?? '12'); ?>">
												<div class="presentation-card-<?php echo esc_attr($card['type'] ?? 'image'); ?>">
													<?php if (($card['type'] ?? 'image') === 'image' && ! empty($card['image'])) : ?>
														<img src="<?php echo esc_url(image_link($card['image'], 'full')); ?>"
															alt="<?php echo esc_attr($title); ?>"
															loading="lazy">
													<?php elseif (in_array($card['type'] ?? '', ['text', 'quote'], true) && ! empty($card['text'])) : ?>
														<?php echo wp_kses_post(wpautop($card['text'])); ?>
													<?php endif; ?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>

	<div class="modal fade" id="product-style" tabindex="-1" role="dialog" aria-labelledby="product-style-label" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo esc_attr__('Close', LANG_STRING); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
					<h2 class="modal-title" id="product-style-label">
						<?php echo esc_html__('Insights and Styling Guides', LANG_STRING); ?>
					</h2>
				</div>
				<div class="modal-body">
					<?php if (! empty($color_tips)) : ?>
						<div class="product-tip">
							<div class="row g-3">
								<div class="col-xl-4">
									<div class="product-tip-header">
										<h3 class="product-tip-title">
											<?php echo esc_html__('Matching Tips', LANG_STRING); ?>
										</h3>
										<div class="product-tip-desc">
											<?php echo esc_html__('We\'ve got some tips for designing your space with this rug.', LANG_STRING); ?>
										</div>
									</div>
								</div>
								<div class="col-xl-8">
									<div class="row g-3">
										<?php foreach ($color_tips as $tip) : ?>
											<div class="col-md-6">
												<div class="product-tip-card">
													<div class="product-tip-card-body">
														<h3 class="product-tip-card-title">
															<?php echo esc_html($tip['title']); ?>
														</h3>
														<div class="product-tip-card-desc">
															<?php echo wp_kses_post($tip['desc']); ?>
														</div>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<div class="product-feature">
						<div class="row g-3">
							<?php
							$features = [
								'design'    => __('Design and Motif Explanation', LANG_STRING),
								'feel'      => __('Feel', LANG_STRING),
								'group'     => __('Group', LANG_STRING),
								'material'  => __('Material', LANG_STRING),
								'origin'    => __('Origin', LANG_STRING),
								'shape'     => __('Shape', LANG_STRING),
								'size_term' => __('Size', LANG_STRING),
								'thickness' => __('Thickness', LANG_STRING),
							];

							foreach ($features as $key => $label) :
								if (! empty($descriptions[$key])) :
							?>
									<div class="col-xl-6">
										<div class="product-feature-card">
											<div class="product-feature-card-body">
												<h3 class="product-feature-card-title">
													<?php echo esc_html($label); ?>
												</h3>
												<div class="product-feature-card-desc">
													<?php echo wp_kses_post($descriptions[$key]); ?>
												</div>
											</div>
										</div>
									</div>
							<?php
								endif;
							endforeach;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
endwhile;

get_footer();
