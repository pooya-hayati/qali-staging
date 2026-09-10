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

$chain_title = \App\Controller\Shop::chain_title_text();
// Bare (non-chained) category or single-attribute page H1 — same source Shop::chain_title()
// reads for the <title> tag, so the two never drift apart. Empty on every other page, including
// a chain (chain_title() falls back to this only when $chain_title itself is '').
$bare_archive_title = \App\Controller\Shop::bare_archive_title_text();

$category_term = is_tax('product_cat') ? get_queried_object() : null;
$category_seo_description = '';
if ($category_term instanceof WP_Term) {
	$category_seo_description = get_term_meta($category_term->term_id, 'seo_description', true);
}

/**
 * Single (non-chained) product-attribute archive page (/origin/senneh/, /shape/rectangle/, …).
 * Unlike product_cat, these terms already have real per-term descriptions filled in via
 * wp-admin's native "Description" field — the same one term_description() and Yoast's default
 * og:description already read — so this reads that instead of adding a second meta box field.
 * Gated on $chain_title === '' because a chained URL's first segment (e.g. "origin" in
 * /origin/tabriz/color/red/) also satisfies is_tax('pa_origin'); the chain's own combined H1
 * (already built) is left as-is, with no per-segment description underneath it.
 */
$attribute_taxonomies = \App\Controller\Shop::attribute_taxonomies();
$attribute_term = ($chain_title === '' && is_tax($attribute_taxonomies)) ? get_queried_object() : null;
$attribute_description = ($attribute_term instanceof WP_Term)
	? term_description($attribute_term->term_id, $attribute_term->taxonomy)
	: '';

// "Suggested next filter" chips — any pa_* archive page (single-attribute or chained) and, as of
// this task, product_cat pages too (plain, e.g. /product-category/colorful-vintage/, or chained
// with attributes, e.g. /product-category/colorful-vintage/origin/tabriz/). get_active_path_bases()
// itself is the single source of truth for "is this a page the chip row belongs on" — it already
// returns [] for every other page type (shop, blog, etc.), so no separate gate is needed here.
$next_filter_active = \App\Controller\Shop::get_active_path_bases();
$next_filter_suggestion = !empty($next_filter_active)
	? \App\Controller\Shop::get_next_filter_suggestion($next_filter_active)
	: null;

/**
 * Consolidated filter bar (Filter button + badge, standalone Sort, result count + active-filter
 * pills + Clear all) — the main query has already run by the time header-shop.php renders (same
 * assumption product-grid.php's own $wp_query read already makes), so $wp_query->found_posts is
 * a real, final count here, not an estimate.
 */
global $wp_query;
$result_count = ($wp_query instanceof WP_Query) ? (int) $wp_query->found_posts : 0;
$result_count_text = sprintf(
	/* translators: %s: number of matching products, already formatted */
	_n('%s Result', '%s Results', $result_count, LANG_STRING),
	number_format_i18n($result_count)
);

// Path-based filter pills (e.g. "Rectangle" on a bare /shape/rectangle/ page, which has no $_GET
// filters at all) alongside the existing $_GET-driven ones ($filters, computed above) — see
// Shop::active_path_attribute_terms()'s own doc for why category itself is excluded here.
$active_path_pills = \App\Controller\Shop::active_path_attribute_terms();
$any_filter_active = !empty($active_path_pills) || !empty($filters);
$clear_all_url = $any_filter_active ? get_post_type_archive_link('product') : '';

// Filter-modal badge — Price/Size/Color/Design/Origin only, see Shop::active_modal_filter_count().
$modal_filter_count = \App\Controller\Shop::active_modal_filter_count();

/**
 * List→submenu filter modal (this session): the main screen shows one row per dimension with
 * its currently-selected term's name (or "All") on the right — computed here at page-load time
 * the same way every other $_GET-driven piece of this template already is; shop.js updates a
 * row's displayed value client-side afterward, without a reload, when the visitor picks a new
 * term in that dimension's submenu and taps OK/back.
 */
$filter_all_label = __('All', LANG_STRING);
$current_term_value = function ($param, $terms) use ($filter_all_label) {
	$slug = $_GET[$param] ?? '';
	if ($slug === '') {
		return $filter_all_label;
	}
	foreach ($terms as $term) {
		if ($term->slug === $slug) {
			return $term->name;
		}
	}
	return $filter_all_label;
};

$price_is_default = ((float) $min_price_value <= (float) $prices['min_price']) && ((float) $max_price_value >= (float) $prices['max_price']);
$price_current_value = $price_is_default
	? $filter_all_label
	: sprintf('$%s - $%s', number_format_i18n($min_price_value), number_format_i18n($max_price_value));

$filter_dimensions = [
	['base' => 'price', 'label' => __('Price', LANG_STRING), 'current' => $price_current_value],
	['base' => 'size', 'label' => __('Size', LANG_STRING), 'current' => $current_term_value('size', $size)],
	['base' => 'color', 'label' => __('Color', LANG_STRING), 'current' => $current_term_value('color', $color)],
	['base' => 'design', 'label' => __('Design', LANG_STRING), 'current' => $current_term_value('design', $design)],
	['base' => 'origin', 'label' => __('Origin', LANG_STRING), 'current' => $current_term_value('origin', $origin)],
];

// Reused by every submenu's OK button — defined once here rather than repeated 5 times inline.
$filter_ok_icon_svg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>';

?>
<header id="page-header">
	<div class="container-fluid">
		<div class="row justify-content-center">
			<div class="col-xl-11">
				<div class="section-cover" data-animate="fadeInUp">
					<img src="<?= URL_ASSETS ?>/img/pattern-2.svg" alt="<?= SITE_NAME ?>">
				</div>
			</div>
			<?php if (function_exists('yoast_breadcrumb') || $category_term instanceof WP_Term || $chain_title !== '' || $attribute_term instanceof WP_Term) : ?>
				<div class="col-xl-12">
					<div class="page-header-seo" data-animate="fadeInDown">
						<?php if (function_exists('yoast_breadcrumb')) : ?>
							<div class="page-header-breadcrumb">
								<?php yoast_breadcrumb('<span id="breadcrumbs">', '</span>'); ?>
							</div>
						<?php endif; ?>
						<?php if ($chain_title !== '') : ?>
							<h1 class="page-header-category-title"><?= esc_html($chain_title) ?></h1>
						<?php elseif ($category_term instanceof WP_Term) : ?>
							<h1 class="page-header-category-title"><?= esc_html($bare_archive_title) ?></h1>
							<?php if (! empty($category_seo_description)) : ?>
								<div class="page-header-category-description"><?= wp_kses_post($category_seo_description) ?></div>
							<?php endif; ?>
						<?php elseif ($attribute_term instanceof WP_Term) : ?>
							<h1 class="page-header-category-title"><?= esc_html($bare_archive_title) ?></h1>
							<?php if (! empty($attribute_description)) : ?>
								<div class="page-header-category-description"><?= wp_kses_post($attribute_description) ?></div>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($next_filter_suggestion) : ?>
							<?php get_template_part_var('templates/shop/next-filter-chips.php', ['suggestion' => $next_filter_suggestion, 'skipped' => [], 'active' => $next_filter_active]) ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="col-xl-12">
				<div class="page-header-filter">
					<form method="GET" id="filter-form">
						<input type="hidden" name="post_type" value="product">

						<div class="page-header-filter-bar">
							<div class="page-header-filter-summary">
								<span class="page-header-filter-count"><?= esc_html($result_count_text) ?></span>
								<?php if ($any_filter_active) : ?>
									<div class="page-header-filter-active">
										<?php foreach ($active_path_pills as $pill) : ?>
											<a href="<?= esc_url(\App\Controller\Shop::path_url_without($pill['base'])) ?>" class="filter-tag"><?= esc_html($pill['term']->name) ?></a>
										<?php endforeach; ?>
										<?php foreach ((array) $filters as $filter) : ?>
											<span class="filter-tag" data-key="<?= esc_attr($filter['key']) ?>" data-value="<?= esc_attr($filter['value']) ?>"><?= esc_html($filter['value']) ?></span>
										<?php endforeach; ?>
									</div>
									<a href="<?= esc_url($clear_all_url) ?>" class="page-header-filter-reset"><?= __('Clear all', LANG_STRING) ?></a>
								<?php endif; ?>
							</div>
							<div class="page-header-filter-controls">
								<div class="page-header-sort icon-select">
									<div class="select-selected">
										<span><?= __('Sort by', LANG_STRING) ?></span>
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
								<button type="button" class="filter-modal-toggle button button-outline-primary">
									<?= __('Filter', LANG_STRING) ?>
									<?php if ($modal_filter_count > 0) : ?>
										<span class="filter-modal-badge"><?= (int) $modal_filter_count ?></span>
									<?php endif; ?>
								</button>
							</div>
						</div>

						<div id="filter-modal" class="filter-modal">
							<div class="filter-modal-dialog">

								<!-- MAIN SCREEN: one row per dimension, current value + chevron on the right. -->
								<div class="filter-modal-screen filter-list active" data-screen="list">
									<div class="filter-modal-header">
										<h3 class="filter-modal-title"><?= __('Filter', LANG_STRING) ?></h3>
										<button type="button" class="filter-modal-close" aria-label="<?= esc_attr__('Close', LANG_STRING) ?>"></button>
									</div>
									<div class="filter-modal-body">
										<div class="filter-list-rows">
											<?php foreach ($filter_dimensions as $dim) : ?>
												<button type="button" class="filter-list-row" data-dimension="<?= esc_attr($dim['base']) ?>">
													<span class="filter-list-row-icon"><?php echo \App\Controller\Shop::dimension_row_icon_svg($dim['base']); ?></span>
													<span class="filter-list-row-label"><?= esc_html($dim['label']) ?></span>
													<span class="filter-list-row-value"><?= esc_html($dim['current']) ?></span>
													<img class="filter-list-row-chevron" src="<?= URL_ASSETS ?>/img/icon-arrow-chevron.svg" alt="" width="20" height="20">
												</button>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="filter-modal-footer">
										<button type="submit" class="button button-fill-primary button-block filter-modal-apply">
											<?= sprintf(esc_html__('Show %s Results', LANG_STRING), number_format_i18n($result_count)) ?>
										</button>
									</div>
								</div>

								<!-- PRICE submenu: same range-slider markup as before, just relocated behind its own row. -->
								<div class="filter-modal-screen filter-submenu" data-screen="submenu" data-dimension="price" data-all-label="<?= esc_attr($filter_all_label) ?>">
									<div class="filter-modal-header filter-modal-header-submenu">
										<button type="button" class="filter-submenu-back" aria-label="<?= esc_attr__('Back', LANG_STRING) ?>">
											<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="" width="20" height="20">
										</button>
										<span class="filter-submenu-heading-icon"><?php echo \App\Controller\Shop::dimension_row_icon_svg('price'); ?></span>
										<h3 class="filter-modal-title"><?= __('Price', LANG_STRING) ?></h3>
									</div>
									<div class="filter-modal-body">
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
									<div class="filter-modal-footer">
										<button type="button" class="button button-outline-primary button-block filter-submenu-ok"><?php echo $filter_ok_icon_svg; ?> <?= __('OK', LANG_STRING) ?></button>
									</div>
								</div>

								<!-- SIZE submenu: plain-text term rows (no swatch/icon), single-select radio. -->
								<div class="filter-modal-screen filter-submenu" data-screen="submenu" data-dimension="size">
									<div class="filter-modal-header filter-modal-header-submenu">
										<button type="button" class="filter-submenu-back" aria-label="<?= esc_attr__('Back', LANG_STRING) ?>">
											<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="" width="20" height="20">
										</button>
										<span class="filter-submenu-heading-icon"><?php echo \App\Controller\Shop::dimension_row_icon_svg('size'); ?></span>
										<h3 class="filter-modal-title"><?= __('Size', LANG_STRING) ?></h3>
									</div>
									<div class="filter-modal-body">
										<div class="filter-submenu-rows">
											<label class="filter-submenu-row">
												<input type="radio" name="size" value="" <?= !isset($_GET['size']) || $_GET['size'] == '' ? 'checked' : '' ?>>
												<span class="filter-submenu-row-name"><?= esc_html($filter_all_label) ?></span>
											</label>
											<?php
											foreach ($size as $_term):
												$checked    = (!empty($_GET['size']) && $_GET['size'] === $_term->slug) ? 'checked' : '';
												$label_cls  = $_term->available ? 'term-label' : 'term-label disabled';
												$subtitle   = get_term_meta($_term->term_id, 'subtitle', true);
											?>
												<label class="filter-submenu-row <?= esc_attr($label_cls) ?>">
													<input type="radio" name="size" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
													<span class="filter-submenu-row-name">
														<?= esc_html($_term->name) ?>
														<?php if ($subtitle): ?>
															<small><?= esc_html($subtitle) ?></small>
														<?php endif; ?>
													</span>
												</label>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="filter-modal-footer">
										<button type="button" class="button button-outline-primary button-block filter-submenu-ok"><?php echo $filter_ok_icon_svg; ?> <?= __('OK', LANG_STRING) ?></button>
									</div>
								</div>

								<!-- COLOR submenu: swatch (existing per-term color meta, same source as chip swatches) + name. -->
								<div class="filter-modal-screen filter-submenu" data-screen="submenu" data-dimension="color">
									<div class="filter-modal-header filter-modal-header-submenu">
										<button type="button" class="filter-submenu-back" aria-label="<?= esc_attr__('Back', LANG_STRING) ?>">
											<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="" width="20" height="20">
										</button>
										<span class="filter-submenu-heading-icon"><?php echo \App\Controller\Shop::dimension_row_icon_svg('color'); ?></span>
										<h3 class="filter-modal-title"><?= __('Color', LANG_STRING) ?></h3>
									</div>
									<div class="filter-modal-body">
										<div class="filter-submenu-rows">
											<label class="filter-submenu-row">
												<input type="radio" name="color" value="" <?= !isset($_GET['color']) || $_GET['color'] == '' ? 'checked' : '' ?>>
												<span class="filter-submenu-row-name"><?= esc_html($filter_all_label) ?></span>
											</label>
											<?php
											foreach ($color as $_term):
												$checked   = (!empty($_GET['color']) && $_GET['color'] === $_term->slug) ? 'checked' : '';
												$label_cls = $_term->available ? 'term-label' : 'term-label disabled';
												$color_hex = get_term_meta($_term->term_id, 'color', true) ?: '#ffffff';
											?>
												<label class="filter-submenu-row <?= esc_attr($label_cls) ?>">
													<input type="radio" name="color" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
													<span class="filter-submenu-row-swatch" style="background-color: <?= esc_attr($color_hex) ?>"></span>
													<span class="filter-submenu-row-name"><?= esc_html($_term->name) ?></span>
												</label>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="filter-modal-footer">
										<button type="button" class="button button-outline-primary button-block filter-submenu-ok"><?php echo $filter_ok_icon_svg; ?> <?= __('OK', LANG_STRING) ?></button>
									</div>
								</div>

								<!-- DESIGN submenu: per-term uploaded image (existing meta) as the row icon + name. -->
								<div class="filter-modal-screen filter-submenu" data-screen="submenu" data-dimension="design">
									<div class="filter-modal-header filter-modal-header-submenu">
										<button type="button" class="filter-submenu-back" aria-label="<?= esc_attr__('Back', LANG_STRING) ?>">
											<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="" width="20" height="20">
										</button>
										<span class="filter-submenu-heading-icon"><?php echo \App\Controller\Shop::dimension_row_icon_svg('design'); ?></span>
										<h3 class="filter-modal-title"><?= __('Design', LANG_STRING) ?></h3>
									</div>
									<div class="filter-modal-body">
										<div class="filter-submenu-rows">
											<label class="filter-submenu-row">
												<input type="radio" name="design" value="" <?= !isset($_GET['design']) || $_GET['design'] == '' ? 'checked' : '' ?>>
												<span class="filter-submenu-row-name"><?= esc_html($filter_all_label) ?></span>
											</label>
											<?php
											foreach ($design as $_term):
												$checked    = (!empty($_GET['design']) && $_GET['design'] === $_term->slug) ? 'checked' : '';
												$label_cls  = $_term->available ? 'term-label' : 'term-label disabled';
												$image_url  = file_link(get_term_meta($_term->term_id, 'image', true));
											?>
												<label class="filter-submenu-row <?= esc_attr($label_cls) ?>">
													<input type="radio" name="design" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
													<span class="filter-submenu-row-swatch filter-submenu-row-swatch-image" style="background-image: url('<?= esc_url($image_url) ?>')"></span>
													<span class="filter-submenu-row-name"><?= esc_html($_term->name) ?></span>
												</label>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="filter-modal-footer">
										<button type="button" class="button button-outline-primary button-block filter-submenu-ok"><?php echo $filter_ok_icon_svg; ?> <?= __('OK', LANG_STRING) ?></button>
									</div>
								</div>

								<!-- ORIGIN submenu: plain-text term rows, per explicit task direction. -->
								<div class="filter-modal-screen filter-submenu" data-screen="submenu" data-dimension="origin">
									<div class="filter-modal-header filter-modal-header-submenu">
										<button type="button" class="filter-submenu-back" aria-label="<?= esc_attr__('Back', LANG_STRING) ?>">
											<img src="<?= URL_ASSETS ?>/img/icon-arrow.svg" alt="" width="20" height="20">
										</button>
										<span class="filter-submenu-heading-icon"><?php echo \App\Controller\Shop::dimension_row_icon_svg('origin'); ?></span>
										<h3 class="filter-modal-title"><?= __('Origin', LANG_STRING) ?></h3>
									</div>
									<div class="filter-modal-body">
										<div class="filter-submenu-rows">
											<label class="filter-submenu-row">
												<input type="radio" name="origin" value="" <?= !isset($_GET['origin']) || $_GET['origin'] == '' ? 'checked' : '' ?>>
												<span class="filter-submenu-row-name"><?= esc_html($filter_all_label) ?></span>
											</label>
											<?php
											foreach ($origin as $_term):
												$checked   = (!empty($_GET['origin']) && $_GET['origin'] === $_term->slug) ? 'checked' : '';
												$label_cls = $_term->available ? 'term-label' : 'term-label disabled';
											?>
												<label class="filter-submenu-row <?= esc_attr($label_cls) ?>">
													<input type="radio" name="origin" value="<?= esc_attr($_term->slug) ?>" <?= $checked ?>>
													<span class="filter-submenu-row-name"><?= esc_html($_term->name) ?></span>
												</label>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="filter-modal-footer">
										<button type="button" class="button button-outline-primary button-block filter-submenu-ok"><?php echo $filter_ok_icon_svg; ?> <?= __('OK', LANG_STRING) ?></button>
									</div>
								</div>

							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</header>