<?php

/**
 * Template Name: Collection
 */
get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);
	$hero = $meta['hero'] ?? '';
	$meta = $meta['page'] ?? '';
?>
	<section class="section section-collection-hero section-full section-covered">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInDown"><?= isset($hero['title']) ? str_replace(['<p>', '</p>'], '', wpautop($hero['title'])) : $post->post_title ?></h2>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-collection-intro section-full section-covered">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-body">
					<div class="section-desc no-animate" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['intro']['description'])) ?></div>
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
									<h3 class="section-subtitle" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['collection']['subtitle'])) ?></h3>
								</div>
							</div>
							<?php foreach ($meta['collection']['item'] as $key => $_collection) { ?>
								<div class="col-md-6 <?= in_array($key, [3, 4, 7]) ? ' col-lg-6' : ' col-lg-3' ?> order-lg-<?= $key == 0 ? '0' : '2' ?><?= in_array($key, [2, 5]) ? ' offset-lg-3' : '' ?>">
									<?php if (in_array($key, [3, 4])) { ?>
										<div class="collection-cover">
											<?php if ($key == 3) { ?>
												<img src="<?= image_link($meta['collection']['image'][0], 'full') ?>" alt="<?= get_bloginfo('name') ?>">
											<?php } ?>
											<?php if ($key == 4) { ?>
												<img src="<?= image_link($meta['collection']['image'][1], 'full') ?>" alt="<?= get_bloginfo('name') ?>">
											<?php } ?>
										</div>
									<?php } else { ?>
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
									<?php } ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<div class="section-footer">
					<div class="row justify-content-center">
						<div class="col-xl-11">
							<div class="section-cover" data-animate="fadeInUp">
								<img src="<?= URL_ASSETS ?>/img/pattern-2.svg" alt="<?= SITE_NAME ?>">
							</div>
							<div class="section-desc animate-words" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['collection']['description'])) ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
}
get_footer();
?>