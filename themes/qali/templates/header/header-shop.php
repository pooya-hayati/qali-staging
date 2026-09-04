<?php
$shop = new app\Controller\Shop();

$filters = $shop->get_active_filters();
$filters_map = array_column($filters, 'value', 'key');
$active_color  = $filters['color']  ?? [];
$active_design = $filters['design'] ?? [];
$active_origin = $filters['origin'] ?? [];
$active_size   = $filters['size']   ?? [];

$color   = $shop->get_terms_for_current_query('pa_color');
$design  = $shop->get_terms_for_current_query('pa_design');
$origin  = $shop->get_terms_for_current_query('pa_origin');
$size    = $shop->get_terms_for_current_query('pa_size');

$prices = $shop->get_min_max_prices();
$min_price_value = $filters_map['min_price'] ?? $prices['min_price'];
$max_price_value = $filters_map['max_price'] ?? $prices['max_price'];

$category_term = is_tax('product_cat') ? get_queried_object() : null;
$category_seo_title = '';
$category_seo_description = '';
if ($category_term instanceof WP_Term) {
	$category_seo_title = get_term_meta($category_term->term_id, 'seo_title', true);
	if ($category_seo_title === '') {
		$category_seo_title = $category_term->name;
	}
	$category_seo_description = get_term_meta($category_term->term_id, 'seo_description', true);
}

?>
<header id="page-header">
	<div class="container-fluid">
		<div class="row justify-content-center">
			<div class="col-xl-11">
				<div class="section-cover" data-animate="fadeInUp">
					<img src="<?= URL_ASSETS ?>/img/pattern-2.svg" alt="<?= SITE_NAME ?>">
				</div>
			</div>
			<?php if (function_exists('yoast_breadcrumb') || $category_term instanceof WP_Term) : ?>
				<div class="col-xl-12">
					<div class="page-header-seo" data-animate="fadeInDown">
						<?php if (function_exists('yoast_breadcrumb')) : ?>
							<div class="page-header-breadcrumb">
								<?php yoast_breadcrumb('<span id="breadcrumbs">', '</span>'); ?>
							</div>
						<?php endif; ?>
						<?php if ($category_term instanceof WP_Term) : ?>
							<h1 class="page-header-category-title"><?= esc_html($category_seo_title) ?></h1>
							<?php if (! empty($category_seo_description)) : ?>
								<div class="page-header-category-description"><?= wp_kses_post($category_seo_description) ?></div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="col-xl-12">
				<div class="page-header-filter">
					<form method="GET" id="filter-form">
						<input type="hidden" name="post_type" value="product">
						<div class="filter-grid row g-3">
							<div class="col-6 col-md-4 col-xl-2">
								<div class="filter-card">
									<div class="filter-card-header">
										<h3 class="filter-card-title"><?= __('Price', LANG_STRING) ?></h3>
									</div>
									<div class="filter-card-body">
										<div class="range-slider" data-step="10" data-min="<?= $prices['min_price'] ?>" data-max="<?= $prices['max_price'] ?>">
											<div class="range-slider-track">
												<div class="range-slider-bar"></div>
												<div class="range-slider-thumb min-thumb"></div>
												<div class="range-slider-thumb max-thumb"></div>
											</div>
											<div class="range-slider-label">
												<span class="range-slider-label-min" data-title="<?= __('From', LANG_STRING) ?> $"><?= $prices['min_price'] ?></span>
												<span class="range-slider-label-max" data-title="<?= __('To', LANG_STRING) ?> $"><?= $prices['max_price'] ?></span>
											</div>
											<input type="hidden" class="range-slider-min-original" value="<?= $prices['min_price'] ?>">
											<input type="hidden" class="range-slider-max-original" value="<?= $prices['max_price'] ?>">
											<input type="hidden" name="min_price" class="range-slider-min" value="<?= $min_price_value ?>">
											<input type="hidden" name="max_price" class="range-slider-max" value="<?= $max_price_value ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-4 col-xl-2">
								<div class="filter-card">
									<div class="filter-card-header">
										<h3 class="filter-card-title"><?= __('Size', LANG_STRING) ?></h3>
									</div>
									<div class="filter-card-body">
										<div class="icon-select">
											<div class="select-selected">
												<span><?= __('Please select…', LANG_STRING) ?></span>
											</div>
											<div class="select-items">
												<label>
													<input type="radio" name="size" value="" <?= !isset($_GET['size']) || $_GET['size'] == '' ? 'checked' : '' ?>>
													<div>
														<span><?= __('All', LANG_STRING) ?></span>
													</div>
												</label>
												<?php
												foreach ($size as $_term):
													//$checked    = in_array($_term->slug, $active_size) ? 'checked' : '';
													$checked = (!empty($_GET['size']) && $_GET['size'] === $_term->slug) ? 'checked' : '';
													$label_cls  = $_term->available ? 'term-label' : 'term-label disabled';
													$subtitle   = get_term_meta($_term->term_id, 'subtitle', true);
												?>
													<label class="<?= esc_attr($label_cls) ?>">
														<input type="radio" name="size" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
														<div>
															<span><?= esc_html($_term->name) ?></span>
															<?php if ($subtitle): ?>
																<small><?= esc_html($subtitle) ?></small>
															<?php endif; ?>
														</div>
													</label>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-4 col-xl-2">
								<div class="filter-card">
									<div class="filter-card-header">
										<h3 class="filter-card-title"><?= __('Color', LANG_STRING) ?></h3>
									</div>
									<div class="filter-card-body">
										<div class="icon-select">
											<div class="select-selected">
												<span><?= __('Please select…', LANG_STRING) ?></span>
											</div>
											<div class="select-items">
												<label>
													<input type="radio" name="color" value="" <?= !isset($_GET['color']) || $_GET['color'] == '' ? '' : 'checked' ?>>
													<div>
														<span><?= __('All', LANG_STRING) ?></span>
													</div>
												</label>
												<?php
												foreach ($color as $_term):
													//$checked   = in_array($_term->slug, $active_color) ? 'checked' : '';
													$checked = (!empty($_GET['color']) && $_GET['color'] === $_term->slug) ? 'checked' : '';
													$label_cls = $_term->available ? 'term-label' : 'term-label disabled';
													$color_hex = get_term_meta($_term->term_id, 'color', true) ?: '#ffffff';
												?>
													<label class="<?= esc_attr($label_cls) ?>">
														<input type="radio" name="color" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
														<div>
															<i style="background-color: <?= esc_attr($color_hex) ?>"></i>
															<span><?= esc_html($_term->name) ?></span>
														</div>
													</label>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-4 col-xl-2">
								<div class="filter-card">
									<div class="filter-card-header">
										<h3 class="filter-card-title"><?= __('Design', LANG_STRING) ?></h3>
									</div>
									<div class="filter-card-body">
										<div class="icon-select">
											<div class="select-selected">
												<span><?= __('Please select…', LANG_STRING) ?></span>
											</div>
											<div class="select-items">
												<label>
													<input type="radio" name="design" value="" <?= !isset($_GET['design']) || $_GET['design'] == '' ? '' : 'checked' ?>>
													<div>
														<span><?= __('All', LANG_STRING) ?></span>
													</div>
												</label>
												<?php
												foreach ($design as $_term):
													//$checked    = in_array($_term->slug, $active_design) ? 'checked' : '';
													$checked = (!empty($_GET['design']) && $_GET['design'] === $_term->slug) ? 'checked' : '';
													$label_cls  = $_term->available ? 'term-label' : 'term-label disabled';
													$image_url  = file_link(get_term_meta($_term->term_id, 'image', true));
												?>
													<label class="<?= esc_attr($label_cls) ?>">
														<input type="radio" name="design" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
														<div>
															<i style="background-image: url('<?= esc_url($image_url) ?>')"></i>
															<span><?= esc_html($_term->name) ?></span>
														</div>
													</label>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-4 col-xl-2">
								<div class="filter-card">
									<div class="filter-card-header">
										<h3 class="filter-card-title"><?= __('Origin', LANG_STRING) ?></h3>
									</div>
									<div class="filter-card-body">
										<div class="icon-select">
											<div class="select-selected">
												<span><?= __('Please select…', LANG_STRING) ?></span>
											</div>
											<div class="select-items">
												<label>
													<input type="radio" name="origin" value="" <?= !isset($_GET['origin']) || $_GET['origin'] == '' ? '' : 'checked' ?>>
													<div>
														<span><?= __('All', LANG_STRING) ?></span>
													</div>
												</label>
												<?php
												foreach ($origin as $_term):
													//$checked   = in_array($_term->slug, $active_origin) ? 'checked' : '';
													$checked = (!empty($_GET['origin']) && $_GET['origin'] === $_term->slug) ? 'checked' : '';
													$label_cls = $_term->available ? 'term-label' : 'term-label disabled';
												?>
													<label class="<?= esc_attr($label_cls) ?>">
														<input type="radio" name="origin" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
														<div>
															<span><?= esc_html($_term->name) ?></span>
														</div>
													</label>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-4 col-xl-2">
								<div class="filter-card">
									<div class="filter-card-header">
										<h3 class="filter-card-title"><?= __('Sort by', LANG_STRING) ?></h3>
									</div>
									<div class="filter-card-body">
										<div class="icon-select">
											<div class="select-selected">
												<span><?= __('Please select…', LANG_STRING) ?></span>
											</div>
											<div class="select-items">
												<label>
													<input type="radio" name="sortby" value="" <?= !isset($_GET['sortby']) || $_GET['sortby'] == '' ? '' : 'checked' ?>>
													<div>
														<span><?= __('Default', LANG_STRING) ?></span>
													</div>
												</label>
												<label>
													<input type="radio" name="sortby" value="lowest_price" <?= isset($_GET['sortby']) && in_array('lowest_price', (array)$_GET['sortby']) ? 'checked' : '' ?>>
													<div><span><?= __('Lowest Price', LANG_STRING) ?></span></div>
												</label>
												<label>
													<input type="radio" name="sortby" value="highest_price" <?= isset($_GET['sortby']) && in_array('highest_price', (array)$_GET['sortby']) ? 'checked' : '' ?>>
													<div><span><?= __('Highest Price', LANG_STRING) ?></span></div>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php if (!empty($filters)) { ?>
								<div class="col-md-12 col-lg-12">
									<div class="page-header-filter-active">
										<span class="filter-tag-title"><?= __('Filters', LANG_STRING) ?>:</span>
										<?php
										foreach ((array) $filters as $filter) {
										?>
											<span class="filter-tag" data-key="<?= esc_attr($filter['key']) ?>" data-value="<?= esc_attr($filter['value']) ?>"><?= esc_html($filter['value']) ?></span>
										<?php } ?>
										<button class="page-header-filter-reset" type="button"><?= __('Reset all', LANG_STRING) ?></button>
									</div>
								</div>
							<?php } ?>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</header>