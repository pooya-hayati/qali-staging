<?php

/**
 * Template Name: Home
 */
get_header();
while (have_posts()) {
	the_post();

	$settings = get_option('settings');
	$post = get_post();
	$meta = get_post_meta_all($post->ID);
	$hero = $meta['hero'] ?? '';
	$meta = $meta['page'] ?? '';

	$product = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 2, 'fields' => 'ids', 'orderby' => 'rand', 'tax_query' => [['taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured']]]);
	$blog = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'fields' => 'ids']);
	$collector = get_posts(['post_type' => 'collector', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids']);

	// Explore Collections carousel: shown categories are pinned by slug (stable even if a
	// category is renamed in wp-admin), but the name/link/description below are always read
	// live via get_terms() so the section can't drift out of sync with the real taxonomy.
	$explore_category_slugs = [
		'antique-persian-rugs',
		'heritage-rugs',
		'persian-kilim-rugs',
		'modern-persian-rugs',
		'patina-rugs',
		'vintage-persian-rugs',
	];
	$explore_category_taglines = [
		'antique-persian-rugs' => __('Hand-knotted 80 to 100 years ago or more, each one an irreplaceable piece of living history.', LANG_STRING),
		'heritage-rugs'        => __('Timeless craftsmanship featuring Bijar, Qashqai, Heriz, and Bakhtiari designs in luxurious wool and silk.', LANG_STRING),
		'persian-kilim-rugs'   => __('Flat-woven kilim and wool rugs crafted for authentic, timeless style.', LANG_STRING),
		'modern-persian-rugs'  => __('Mid-century modern rugs crafted from premium wool, redefining the classic area rug for contemporary living.', LANG_STRING),
		'patina-rugs'          => __('Intentionally faded and gently distressed for a warm, lived-in character with lasting durability.', LANG_STRING),
		'vintage-persian-rugs' => __('Colorful, character-rich vintage area rugs and vibrant vintage runner rugs.', LANG_STRING),
	];
	$explore_category_terms = get_terms(['taxonomy' => 'product_cat', 'slug' => $explore_category_slugs, 'hide_empty' => false]);
	$explore_categories = [];
	if (!is_wp_error($explore_category_terms)) {
		$explore_category_terms_by_slug = [];
		foreach ($explore_category_terms as $_term) {
			$explore_category_terms_by_slug[$_term->slug] = $_term;
		}
		foreach ($explore_category_slugs as $_slug) {
			if (isset($explore_category_terms_by_slug[$_slug])) {
				$explore_categories[] = $explore_category_terms_by_slug[$_slug];
			}
		}
	}
?>
	<section class="section section-hero section-full">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($hero['title'])) ?></h2>
					<h3 class="section-subtitle" data-animate="fadeInUp" data-delay="500"><?= str_replace(['<p>', '</p>'], '', wpautop($hero['subtitle'])) ?></h3>
					<div class="section-nav" data-animate="fadeInUp">
						<a href="<?= get_post_type_archive_link('product') ?>" class="section-btn button button-fill-light"><?= __('See All', LANG_STRING) ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="section-divider">
			<img src="<?= URL_ASSETS ?>/img/bg-intro.svg" alt="<?= SITE_NAME ?>" data-animate="fadeInDown" data-delay="500" data-duration="1000">
		</div>
	</section>
	<section class="section section-intro section-full section-overlay section-covered">
		<img src="<?= image_link($meta['intro']['image'], 'full') ?>" alt="<?= SITE_NAME ?>" class="section-bg">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['intro']['title'])) ?></h2>
					<h3 class="section-subtitle" data-animate="fadeInUp" data-delay="500"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['intro']['subtitle'])) ?></h3>
					<div class="section-nav" data-animate="fadeInUp">
						<a href="<?= get_permalink(get_page_by_path('rug-is-art')) ?>" class="section-btn button button-fill-light"><?= __('Browse it', LANG_STRING) ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-featured">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['featured']['title'])) ?></h2>
					<h3 class="section-subtitle" data-animate="fadeInUp" data-delay="500"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['featured']['subtitle'])) ?></h3>
				</div>
				<div class="section-body">
					<?php if (!empty($product)) { ?>
						<div class="product-grid row g-3">
							<?php foreach ($product as $key => $_product) { ?>
								<div class="col-md-<?= $key == 0 ? '4' : '8' ?>" data-animate="fadeInUp">
									<?php get_template_part_var('templates/card/card-product.php', ['post' => get_post($_product)]) ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-collection">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header" data-animate="fadeIn">
					<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['collection']['title'])) ?></h2>
					<div class="section-nav" data-animate="fadeInUp">
						<a href="<?= get_permalink(get_page_by_path('collections')) ?>" class="section-btn button button-link button-link-secondary"><?= __('See All', LANG_STRING) ?></a>
					</div>
				</div>
				<div class="section-body">
					<?php if (!empty($explore_categories)) { ?>
						<div class="category-carousel" data-animate="fadeInUp">
							<div class="category-carousel-swiper swiper">
								<div class="swiper-wrapper">
									<?php foreach ($explore_categories as $_category) {
										$_thumb_id = get_term_meta($_category->term_id, 'thumbnail_id', true);
										if (!$_thumb_id) {
											$_fallback_product = get_posts([
												'post_type'      => 'product',
												'post_status'    => 'publish',
												'posts_per_page' => 1,
												'fields'         => 'ids',
												'tax_query'      => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $_category->term_id]],
											]);
											$_thumb_id = !empty($_fallback_product) ? get_post_thumbnail_id($_fallback_product[0]) : null;
										}
										$_tagline = $explore_category_taglines[$_category->slug] ?? wp_trim_words(wp_strip_all_tags($_category->description), 18, '…');
									?>
										<div class="swiper-slide category-carousel-slide">
											<a href="<?= esc_url(get_term_link($_category)) ?>" title="<?= esc_attr($_category->name) ?>" class="category-card">
												<img src="<?= image_link($_thumb_id, 'full') ?>" alt="<?= esc_attr($_category->name) ?>" class="category-card-img">
												<span class="category-card-overlay">
													<span class="category-card-name">
														<?= esc_html($_category->name) ?>
														<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="" class="category-card-arrow">
													</span>
													<?php if ($_tagline !== '') { ?>
														<span class="category-card-desc"><?= esc_html($_tagline) ?></span>
													<?php } ?>
												</span>
											</a>
										</div>
									<?php } ?>
								</div>
							</div>
							<button type="button" class="category-carousel-arrow category-carousel-prev" aria-label="<?= esc_attr__('Previous', LANG_STRING) ?>">
								<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="">
							</button>
							<button type="button" class="category-carousel-arrow category-carousel-next" aria-label="<?= esc_attr__('Next', LANG_STRING) ?>">
								<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="">
							</button>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-banner section-overlay section-covered" data-animate="fadeIn">
		<img src="<?= URL_ASSETS ?>/img/pattern-1.svg" alt="<?= SITE_NAME ?>" class="section-bg">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-xl-11">
						<div class="section-body">
							<div class="section-desc animate-words" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['banner']['description'])) ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-certificate">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['certificate']['title'])) ?></h2>
					<h3 class="section-subtitle" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['certificate']['subtitle'])) ?></h3>
					<div class="section-nav" data-animate="fadeInDown">
						<a href="<?= $meta['certificate']['button']['link'] ?>" class="section-btn button button-outline-primary"><?= $meta['certificate']['button']['title'] ?></a>
					</div>
				</div>
				<div class="section-body">
					<div class="certificate-grid row g-5 justify-content-center">
						<?php foreach ((array) $meta['certificate']['item'] as $_certificate) { ?>
							<div class="col-6 col-sm-4 col-md-3 col-lg-2">
								<div class="certificate-card" data-animate="fadeIn">
									<img src="<?= file_link($_certificate['image']) ?>" alt="<?= $_certificate['title'] ?>" class="certificate-card-img">
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="section-footer">
					<div class="section-desc animate-words" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['certificate']['description'])) ?></div>
				</div>
			</div>
		</div>
	</section>
	<?php /*<section class="section section-collector">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['collector']['title'])) ?></h2>
					<h3 class="section-subtitle" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['collector']['subtitle'])) ?></h3>
				</div>
				<div class="section-body">

					<div class="collector-list" data-animate="fadeInDown">
						<?php foreach ((array) $collector as $_collector) { ?>
							<?php get_template_part_var('templates/card/card-collector.php', ['post' => get_post($_collector)]) ?>
						<?php } ?>
					</div>
				</div>
				<div class="section-footer">
					<div class="section-nav" data-animate="fadeInUp">
						<a href="<?= get_post_type_archive_link('collector') ?>" class="section-btn"><?= __('See All', LANG_STRING) ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>*/ ?>
	<section class="section section-blog">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<div class="row justify-content-center">
						<div class="col-md-10 col-lg-8 col-xl-6 d-flex justify-content-between align-items-end">
							<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['blog']['title'])) ?></h2>
							<div class="section-nav" data-animate="fadeInUp">
								<a href="<?= get_the_permalink(get_option('page_for_posts')) ?>" class="section-btn button button-link button-link-secondary"><?= __('See All', LANG_STRING) ?></a>
							</div>
						</div>
					</div>
				</div>
				<div class="section-body">
					<div class="blog-grid row g-3 justify-content-center">
						<?php foreach ((array) $blog as $_blog) { ?>
							<div class="col-lg-4">
								<?php get_template_part_var('templates/card/card-blog.php', ['post' => get_post($_blog)]) ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
}
get_footer();
?>