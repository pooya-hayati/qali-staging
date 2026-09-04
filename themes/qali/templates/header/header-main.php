<?php
$settings = get_option('settings');

$current_lang  = get_bloginfo('language');
$network_url   = network_site_url('/');
$lang_list = [
	'en-US' => [
		'id' => 1,
		'title' => 'En',
		'link'  => get_home_url(1),
	],
	'ar' => [
		'id' => 2,
		'title' => 'Ar',
		'link'  => get_home_url(2),
	],
];
//$featured = get_posts(['post_type' => 'post', 'meta_key' => '_is_featured', 'meta_value' => '1', 'fields' => 'ids', 'posts_per_page' => 1])[0];
$featured = get_posts(['post_type' => 'post', 'fields' => 'ids', 'posts_per_page' => 1])[0];
?>
<header id="header" data-animate="fadeInDown">
	<div class="container-fluid">
		<a href="<?= get_bloginfo('url') ?>" title="<?= SITE_NAME ?>" class="logo">
			<img src="<?= isset($settings['general']['logo']) ? file_link($settings['general']['logo']) : '' ?>" alt="<?= SITE_NAME ?>">
		</a>
		<ul class="user-nav">
			<li class="nav-toggle">
				<button type="button"><span><?= __('Menu', LANG_STRING) ?></span></button>
				<button type="button"><span><?= __('Close', LANG_STRING) ?></span></button>
			</li>
			<li class="shop-toggle">
				<a href="<?= get_post_type_archive_link('product') ?>"><span><?= __('Products', LANG_STRING) ?></span></a>
			</li>
			<li class="search-toggle">
				<a href="<?= get_post_type_archive_link('product') ?>"><span><?= __('Search', LANG_STRING) ?></span></a>
			</li>
			<li class="wishlist-toggle">
				<a href="<?= get_permalink(get_page_by_path('wishlist')) ?>">
					<span><?= __('Wishlist', LANG_STRING) ?></span>
					<strong></strong>
				</a>
			</li>
			<li class="cart-toggle">
				<a href="<?= get_the_permalink(get_option('woocommerce_cart_page_id')) ?>">
					<span><?= __('Cart', LANG_STRING) ?></span>
					<strong></strong>
				</a>
			</li>
			<li class="account-toggle">
				<a href="<?= get_the_permalink(get_option('woocommerce_myaccount_page_id')) ?>"><span><?= __('Account', LANG_STRING) ?></span></a>
			</li>
		</ul>
	</div>
</header>
<aside id="sidebar">
	<div class="container-fluid">
		<div class="row g-3">
			<div class="col-sm-6 col-lg-4">
				<div class="sblock">
					<div class="sblock-header">
						<h4 class="sblock-title"><?= __('Shop', LANG_STRING) ?></h4>
					</div>
					<div class="sblock-body">
						<?php get_template_part('templates/navigation/nav', 'shop') ?>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-4">
				<div class="sblock">
					<div class="sblock-header">
						<h4 class="sblock-title"><?= __('Content', LANG_STRING) ?></h4>
					</div>
					<div class="sblock-body">
						<?php get_template_part('templates/navigation/nav', 'primary') ?>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-lg-4">
				<?php if (isset($featured)) { ?>
					<div class="sblock">
						<div class="sblock-header">
							<h4 class="sblock-title"><?= __('Latest News', LANG_STRING) ?></h4>
						</div>
						<div class="sblock-body">
							<div class="sblock-card" data-animate="fadeInUp">
								<div class="sblock-card-header">
									<a href="<?= get_the_permalink($featured) ?>" title="<?= get_the_title($featured) ?>">
										<?php $featured_thumb_id = get_post_thumbnail_id($featured); ?>
										<?php if ($featured_thumb_id) : ?>
											<?= wp_get_attachment_image($featured_thumb_id, 'qali-featured-card', false, [
												'class'   => 'sblock-card-img',
												'alt'     => get_the_title($featured),
												'loading' => 'lazy',
											]) ?>
										<?php else : ?>
											<img src="<?= post_image($featured, 'full') ?>" alt="<?= get_the_title($featured) ?>" class="sblock-card-img" loading="lazy">
										<?php endif; ?>
									</a>
								</div>
								<div class="sblock-card-body">
									<h3 class="sblock-title"><?= get_the_title($featured) ?></h3>
								</div>
								<div class="sblock-card-footer">
									<a href="<?= get_the_permalink($featured) ?>" title="<?= get_the_title($featured) ?>" class="sblock-card-btn"><?= __('Read now', LANG_STRING) ?></a>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</aside>
<div class="searchbar">
	<div class="container-fluid">
		<form action="<?= home_url('products') ?>" method="GET" class="searchbar-form">
			<input type="hidden" name="post_type" value="product">
			<input type="text" name="s" placeholder="<?= __('Search for a product', LANG_STRING) ?>" class="searchbar-form-input" autocomplete="off" value="<?= isset($_GET['s']) ? esc_attr($_GET['s']) : '' ?>" >
			<button type="submit" class="searchbar-form-btn"><?= __('Search', LANG_STRING) ?></button>
		</form>
	</div>
</div>