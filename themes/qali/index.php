<?php
get_header();
$meta = get_post_meta_all(get_option('page_for_posts'));
$category = get_terms(['taxonomy' => 'category', 'parent' => 0, 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC']);
?>
<section class="section section-page-hero section-full section-covered">
	<div class="section-wrapper">
		<div class="container-fluid">
			<div class="section-header">
				<h2 class="section-title" data-animate="fadeInDown"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['hero']['title'])) ?></h2>
				<h3 class="section-subtitle" data-animate="fadeInDown"><?= str_replace(['<p>', '</p>'], '', wpautop_with_shortcodes($meta['hero']['description'])) ?></h3>
			</div>
		</div>
	</div>
	<img src="<?= image_link($meta['hero']['image'], 'full') ?>" alt="<?= SITE_NAME ?>" class="section-bg">
</section>
<div class="section section-blog">
	<div class="section-wrapper">
		<div class="container-fluid">
			<div class="section-header">
				<div class="row justify-content-end">
					<div class="col-md-10 col-lg-8 col-xl-8 d-flex justify-content-between align-items-end">
						<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['hero']['subtitle'])) ?></h2>
						<h3 class="section-subtitle">Blog Posts & News</h3>
					</div>
					<div class="col-md-12">

						<div class="page-header-filter">
							<form method="GET">
								<input type="hidden" name="post_type" value="post">
								<div class="filter-grid row g-3">
									<div class="col-sm-4 col-md-3">
										<div class="filter-card">
											<div class="filter-card-header">
												<h3 class="filter-card-title"><?= __('Category', LANG_STRING) ?></h3>
											</div>
											<div class="filter-card-body">
												<div class="icon-select">
													<div class="select-selected">
														<span><?= __('Please select…', LANG_STRING) ?></span>
													</div>
													<div class="select-items">
														<label>
															<input type="radio" name="category" value="" <?= !isset($_GET['category']) || $_GET['category'] == '' ? '' : 'checked' ?>>
															<div>
																<span><?= __('All', LANG_STRING) ?></span>
															</div>
														</label>
														<?php foreach ($category as $_term) { ?>
															<label>
																<input type="radio" name="category" value="<?= $_term->slug ?>" <?= isset($_GET['category']) && in_array($_term->slug, (array)$_GET['category']) ? 'checked' : '' ?>>
																<div>
																	<span><?= $_term->name ?></span>
																</div>
															</label>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-8 col-md-9">
										<div class="filter-card">
											<div class="filter-card-header">
												<h3 class="filter-card-title"><?= __('Keyword', LANG_STRING) ?></h3>
											</div>
											<div class="filter-card-body">
												<input name="s" class="filter-text" type="text" placeholder="<?= __('Type and press Enter to search…', LANG_STRING) ?>">
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="section-body">
				<?php if (have_posts()) : ?>
					<div class="blog-masonry masonry-grid row g-3">
						<div class="masonry-grid-sizer col-sm-6 col-lg-4"></div>
						<?php while (have_posts()) : the_post(); ?>
							<div class="masonry-grid-item col-sm-6 col-lg-4">
								<?php get_template_part_var('templates/card/card-blog.php', ['post' => get_post()]) ?>
							</div>
						<?php endwhile ?>
					</div>
					<?php \App\Controller\Pagination::show() ?>
				<?php else : ?>
					<div class="page-info"><?= __('Not Found', LANG_STRING) ?></div>
				<?php endif ?>
				<?php /*<div id="infinite-loading">
					<img src="<?= URL_ASSETS ?>/img/icon-loading.svg" alt="<?= __('Loading…', LANG_STRING) ?>">
				</div>
				<div id="infinite-scroll" data-max-pages="<?= $wp_query->max_num_pages ?>"></div>*/ ?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
?>