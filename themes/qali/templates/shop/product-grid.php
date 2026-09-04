<?php

/**
 * Shared product grid + "Show More" pagination, used by archive-product.php,
 * taxonomy-product_cat.php and taxonomy-product_tag.php (previously identical
 * inline blocks in all three).
 */

defined('ABSPATH') || exit;

global $wp_query;

$current_page = max(1, (int) get_query_var('paged'));
$max_pages     = (int) $wp_query->max_num_pages;
$found_posts   = (int) $wp_query->found_posts;
$per_page      = (int) $wp_query->get('posts_per_page');
$shown_count   = (int) $wp_query->post_count;

$archive_type = '';
$archive_term = '';
if (is_tax('product_cat')) {
	$archive_type = 'product_cat';
	$archive_term = get_queried_object()->slug;
} elseif (is_tax('product_tag')) {
	$archive_type = 'product_tag';
	$archive_term = get_queried_object()->slug;
}

$progress_pct = $found_posts > 0 ? round(($shown_count / $found_posts) * 100, 2) : 0;
?>
<?php if (have_posts()) : ?>
	<div class="product-grid-wrap"
		id="product-grid-wrap"
		data-archive-type="<?= esc_attr($archive_type) ?>"
		data-archive-term="<?= esc_attr($archive_term) ?>"
		data-current-page="<?= esc_attr($current_page) ?>"
		data-max-pages="<?= esc_attr($max_pages) ?>"
		data-found-posts="<?= esc_attr($found_posts) ?>"
		data-per-page="<?= esc_attr($per_page) ?>">
		<div class="product-grid row g-3" id="product-grid">
			<?php while (have_posts()) : the_post(); ?>
				<div class="col-sm-6 col-md-4 col-xl-5th">
					<?php get_template_part_var('templates/card/card-product.php', ['post' => get_post()]) ?>
				</div>
			<?php endwhile ?>
		</div>

		<?php if ($max_pages > 1) : ?>
			<div class="show-more-wrap">
				<p class="show-more-progress-text">
					<?php
					$progress_text = sprintf(
						/* translators: 1: number of rugs shown so far (wrapped in a span), 2: total number of rugs (wrapped in a span) */
						__("You've seen %1\$s of %2\$s rugs", LANG_STRING),
						'<span class="show-more-count-shown">' . number_format_i18n($shown_count) . '</span>',
						'<span class="show-more-count-total">' . number_format_i18n($found_posts) . '</span>'
					);
					echo wp_kses($progress_text, ['span' => ['class' => []]]);
					?>
				</p>
				<div class="show-more-progress-bar">
					<div class="show-more-progress-bar-fill" style="width: <?= esc_attr($progress_pct) ?>%"></div>
				</div>
				<button type="button"
					class="button button-fill-primary button-large show-more-btn"
					<?= $current_page >= $max_pages ? 'hidden' : '' ?>>
					<?= esc_html__('Show More', LANG_STRING) ?>
				</button>
				<button type="button" class="show-more-back-to-top">
					<?= esc_html__('Back to Top', LANG_STRING) ?>
				</button>
			</div>
			<noscript>
				<?php \App\Controller\Pagination::show() ?>
			</noscript>
		<?php endif; ?>
	</div>
<?php else : ?>
	<div class="page-info"><?= __('Not Found', LANG_STRING) ?></div>
<?php endif ?>
