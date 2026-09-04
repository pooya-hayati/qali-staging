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
				<div class="section-footer">
					<div class="row justify-content-center">
						<div class="col-xl-11">
							<div class="section-cover">
								<img src="<?= URL_ASSETS ?>/img/pattern-2.svg" alt="<?= SITE_NAME ?>" data-animate="fadeInUp">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-collection">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-body">
					<?php if (!empty($meta['collection']['item'])) { ?>
						<div class="collection-grid row">
							<div class="col-md-12 col-lg-6 order-lg-1">
								<div class="section-header" data-animate="fadeIn" data-delay="600">
									<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['collection']['title'])) ?></h2>
									<div class="section-nav" data-animate="fadeInUp">
										<a href="<?= get_permalink(get_page_by_path('collections')) ?>" class="section-btn button button-link button-link-secondary"><?= __('See All', LANG_STRING) ?></a>
									</div>
								</div>
							</div>
							<?php foreach ($meta['collection']['item'] as $key => $_collection) { ?>
								<div class="col-md-6 col-lg-<?= $key == 2 ? '6' : '3' ?> order-lg-<?= $key == 0 ? '0' : '2' ?>">
									<div class="collection-card" data-animate="fadeInUp">
										<div class="collection-card-header">
											<img src="<?= image_link($_collection['image'], 'full') ?>" alt="<?= $_collection['title'] ?>" class="collection-card-img">
										</div>
										<div class="collection-card-body">
											<h3 class="collection-card-title"><?= $_collection['title'] ?></h3>
											<div class="collection-card-desc"><?= $_collection['description'] ?></div>
										</div>
										<a href="<?= get_term_link($_collection['taxonomy']) ?>" title="<?= $_collection['title'] ?>" class="overlay-link"><?= $_collection['title'] ?></a>
									</div>
								</div>
							<?php } ?>
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