<?php

/**
 * Template Name: Wishlist
 */


use App\Controller\Wishlist;

get_header();
while (have_posts()) {
	the_post();
	$post = get_post();
	$meta = get_post_meta_all($post->ID);

	$token    = isset($_GET['wishlist_token']) ? sanitize_text_field($_GET['wishlist_token']) : '';
	$user     = null;
	$products = [];

	if (!empty($token) && class_exists(Wishlist::class)) {
		$wishlist = new Wishlist();
		$user     = $wishlist->get_user_by_token($token);

		if ($user) {
			$products = $wishlist->get_user_wishlist_products($user->ID);
		}
	}

?>

	<header id="page-header">
		<div class="container-fluid">
			<div class="row justify-content-center">
				<div class="col-xl-11">
					<div class="section-cover" data-animate="fadeInUp">
						<img src="<?= esc_url(URL_ASSETS) ?>/img/pattern-2.svg" alt="<?= esc_attr(SITE_NAME) ?>">
					</div>
				</div>
			</div>
		</div>
	</header>

	<main id="page-body">
		<div class="container-fluid">
			<?php if ($user && !empty($products)) : ?>
				<div class="wishlist-products-grid row g-3">
					<?php foreach ($products as $product) : ?>
						<div class="col-md-6 col-lg-4">
							<div class="product-card" data-animate="fadeInUp">
								<div class="product-card-header">
									<a href="<?= esc_url($product['url']) ?>" title="<?= esc_attr($product['name']) ?>">
										<img src="<?= esc_url($product['image']) ?>" alt="<?= esc_attr($product['name']) ?>" class="product-card-img">
									</a>
								</div>
								<div class="product-card-body">
									<h3 class="product-card-title">
										<a href="<?= esc_url($product['url']) ?>" title="<?= esc_attr($product['name']) ?>">
											<?= esc_html($product['name']) ?>
										</a>
									</h3>
									<div class="product-card-meta">
										<span class="product-card-price"><?= wp_kses_post($product['price']) ?></span>
									</div>
								</div>
								<div class="product-card-footer">
									<a href="<?= esc_url(add_query_arg('add-to-cart', (string) $product['id'], $product['url'])) ?>" class="product-card-btn">
										<?= esc_html__('Add to Cart', LANG_STRING) ?>
									</a>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php elseif (!empty($token)) : ?>
				<div class="alert alert-warning">
					<?= esc_html__('Invalid or expired wishlist token.', LANG_STRING) ?>
				</div>
			<?php else : ?>
				<div id="wishlist-grid" class="product-grid row g-3"></div>
			<?php endif; ?>
		</div>
	</main>

<?php }
get_footer();
