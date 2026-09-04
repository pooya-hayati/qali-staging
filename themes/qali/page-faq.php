<?php

/**
 * Template Name: FAQ
 */
get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);
    $hero = $meta['hero'] ?? '';
    $meta = $meta['page'] ?? '';
?>
	<div id="page-header">
		<div class="container-fluid">
			<div class="page-header-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($hero['title'])) ?></div>
			<div class="page-header-subtitle" data-animate="fadeInUp" data-delay="500"><?= str_replace(['<p>', '</p>'], '', wpautop($hero['subtitle'])) ?></div>
		</div>
	</div>
	<div id="page-body">
		<div class="container-fluid">
			<div class="row g-3 justify-content-center">
				<div class="col-lg-4 col-xl-3">
					<ul class="faq-tab-nav">
						<?php $key = 0;
						foreach ($meta['group'] as $key => $_group) { ?>
							<li <?= $key == 0 ? ' class="active"' : '' ?>><a data-toggle="tab" href="#faq-tab-<?= $key ?>"><?= $_group['category'] ?></a></li>
						<?php } ?>
					</ul>
				</div>
				<div class="col-lg-8 col-xl-8">
					<div class="faq-tab-content">
						<?php $key = 0;
						foreach ($meta['group'] as $key => $_group) { ?>
							<div id="faq-tab-<?= $key ?>" class="tab-pane fade <?= $key == 0 ? ' in active' : '' ?>">
								<div class="faq-list accordion-list">
									<?php foreach ((array) $_group['item'] as $_faq) { ?>
										<div class="faq-card accordion-card">
											<div class="faq-card-header accordion-card-header">
												<h3 class="faq-card-title accordion-card-title"><?= $_faq['title'] ?></h3>
											</div>
											<div class="faq-card-body accordion-card-body"><?= str_replace(['<p>', '</p>'], '', wpautop($_faq['description'])) ?></div>
										</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<section class="section section-cta">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-xl-11">
						<div class="section-cover" data-animate="fadeInUp">
							<img src="<?= URL_ASSETS ?>/img/pattern-2.svg" alt="<?= SITE_NAME ?>">
						</div>
					</div>
					<div class="col-xl-6">
						<div class="section-header">
							<div class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['cta']['title'])) ?></div>
							<div class="section-subtitle" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['cta']['subtitle'])) ?></div>
							<div class="section-nav" data-animate="fadeInDown">
								<a href="<?= get_permalink(get_page_by_path('contact')) ?>" class="section-btn button button-outline-primary"><?= __('Contact us', LANG_STRING) ?></a>
							</div>
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