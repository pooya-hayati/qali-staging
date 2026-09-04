<?php

/**
 * Template Name: About
 */
get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);
	$hero = $meta['hero'] ?? '';
	$meta = $meta['page'] ?? '';
?>
	<section class="section section-about-hero section-full section-covered">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInDown"><?= isset($hero['title']) ? str_replace(['<p>', '</p>'], '', wpautop($hero['title'])) : $post->post_title ?></h2>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-about-hero-2 section-full section-covered">
		<img src="<?= URL_ASSETS ?>/img/pattern-4.svg" alt="<?= SITE_NAME ?>" class="section-bg">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-xl-11">
						<div class="section-body">
							<div class="section-desc"><?= str_replace(['<p>', '</p>'], '', wpautop($hero['description'])) ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-about-intro">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-xl-11">
						<div class="section-header">
							<h2 class="section-title"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['intro']['title'])) ?></h2>
						</div>
						<div class="section-body">
							<div class="section-desc"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['intro']['description'])) ?></div>
						</div>
						<div class="section-footer">
							<div class="section-cover">
								<img src="<?= URL_ASSETS ?>/img/pattern-2.svg" alt="<?= SITE_NAME ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-about-mission">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row g-3 justify-content-center">
					<div class="col-lg-5 col-xl-4">
						<div class="section-header">
							<h2 class="section-title"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['mission']['title'])) ?></h2>
						</div>
					</div>
					<div class="col-lg-6 col-xl-7">
						<div class="section-body">
							<div class="section-desc"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['mission']['description'])) ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-about-gallery">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-lg-11 col-xl-11">
						<div class="about-gallery-carousel swiper">
							<div class="swiper-wrapper">
								<?php foreach ($meta['gallery']['image'] as $_gallery) {
									$image_data = wp_get_attachment_image_src($_gallery, 'full'); ?>
									<div class="swiper-slide" style="width: <?= round(($image_data[1] * 480) / $image_data[2]) ?>px">
										<div class="about-gallery-card">
											<img src="<?= image_link($_gallery, 'full') ?>" alt="<?= SITE_NAME ?>" class="about-gallery-card-img">
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-about-vision section-covered">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row g-3 justify-content-center">
					<div class="col-lg-5 col-xl-4">
						<div class="section-header">
							<h2 class="section-title"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['vision']['title'])) ?></h2>
						</div>
					</div>
					<div class="col-lg-6 col-xl-7">
						<div class="section-body">
							<div class="section-desc"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['vision']['description'])) ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="section-divider">
			<img src="<?= URL_ASSETS ?>/img/pattern-7.svg" alt="<?= SITE_NAME ?>">
		</div>
	</section>
	<section class="section section-about-member">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row g-3 justify-content-center">
					<div class="col-lg-5 col-xl-4">
						<div class="section-header">
							<h2 class="section-title"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['member']['title'])) ?></h2>
						</div>
					</div>
					<div class="col-lg-6 col-xl-7">
						<div class="section-body">
							<div class="member-grid row g-3">
								<?php foreach ($meta['member']['item'] as $_member) { ?>
									<div class="col-md-6">
										<div class="member-card" data-animate="fadeInUp">
											<div class="member-card-header">
												<img src="<?= image_link($_member['image'], 'full') ?>" alt="<?= $_member['title'] ?>" class="member-card-img">
											</div>
											<div class="member-card-body">
												<h3 class="member-card-title"><?= $_member['title'] ?></h3>
												<h4 class="member-card-subtitle"><?= $_member['subtitle'] ?></h4>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="section section-about-service section-full section-covered">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-lg-11 col-xl-11">
						<div class="section-body">
							<div class="section-desc"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['service']['description'])) ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php /*<section class="section section-about-certificate">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row g-3 justify-content-center">
					<div class="col-lg-5 col-xl-4">
						<div class="section-header">
							<h2 class="section-title"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['certificate']['title'])) ?></h2>
						</div>
					</div>
					<div class="col-lg-6 col-xl-7">
						<div class="section-body">
							<div class="certificate-grid row g-3">
								<?php foreach ($meta['certificate']['item'] as $_certificate) { ?>
									<div class="col-6 col-sm-4 col-md-3 col-lg-2">
										<div class="certificate-card">
											<img src="<?= file_link($_certificate['image']) ?>" alt="<?= $_certificate['title'] ?>" class="certificate-card-img">
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>*/ ?>
	<section class="section section-cta">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<div class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['cta']['title'])) ?></div>
					<div class="section-subtitle" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['cta']['subtitle'])) ?></div>
					<div class="section-nav" data-animate="fadeInDown">
						<a href="<?= $meta['cta']['button']['link'] ?>" class="section-btn button button-outline-primary"><?= $meta['cta']['button']['title'] ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
}
get_footer();
?>