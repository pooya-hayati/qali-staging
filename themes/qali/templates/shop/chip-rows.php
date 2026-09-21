<?php

/**
 * Chip rows for pa_* attribute archive pages (single or chained) and product_cat category pages
 * — see App\Controller\Shop::get_chip_rows(). Every dimension eligible for the current page (up
 * to 3: Origin, Color, Shape) gets its own row, always in the fixed order get_chip_rows() already
 * resolved. Every chip is a real, crawlable <a href> to the resulting chained URL (never a
 * 0-product combination — filtered out before this renders).
 *
 * "Show more" is a pure client-side reveal (shop.js) — every chip past a row's visible cap is
 * already in the markup, just `hidden`, so no AJAX round-trip is needed to expand it (unlike the
 * old single-suggestion "Skip" system this replaced).
 */

defined('ABSPATH') || exit;

$rows = is_array($this->rows) ? $this->rows : [];

if (empty($rows)) {
	return;
}
?>
<div class="page-header-chip-rows">
	<?php foreach ($rows as $row) :
		$visible = max(1, (int) ($row['visible'] ?? 5));
		$chips   = $row['chips'];
		$total   = count($chips);
		$more_count = $total - $visible;
	?>
		<div class="page-header-chip-row" data-base="<?= esc_attr($row['base']) ?>">
			<div class="page-header-chip-row-header">
				<span class="page-header-chip-row-label"><?= esc_html($row['label']) ?></span>
			</div>
			<div class="page-header-chip-row-chips">
				<?php foreach ($chips as $i => $chip) :
					$is_extra = $i >= $visible;
				?>
					<a href="<?= esc_url($chip['url']) ?>" class="next-filter-chip<?= $is_extra ? ' next-filter-chip-extra' : '' ?>"<?= $is_extra ? ' hidden' : '' ?>>
						<?php if (!empty($chip['swatch'])) : ?>
							<?php $swatch = $chip['swatch']; ?>
							<span class="next-filter-chip-swatch" style="background: <?= esc_attr($swatch['gradient'] ?? $swatch['hex']) ?>"></span>
						<?php elseif (!empty($chip['shape_slug'])) : ?>
							<span class="next-filter-chip-icon"><?php echo \App\Controller\Shop::shape_chip_icon_svg($chip['shape_slug']); ?></span>
						<?php elseif (!empty($chip['thumbnail_url'])) : ?>
							<span class="next-filter-chip-thumb"><img src="<?= esc_url($chip['thumbnail_url']) ?>" alt="" loading="lazy" width="20" height="20"></span>
						<?php endif; ?>
						<?= esc_html($chip['name']) ?>
						<span class="next-filter-chip-count">(<?= esc_html(number_format_i18n($chip['count'])) ?>)</span>
					</a>
				<?php endforeach; ?>
				<?php if ($more_count > 0) : ?>
					<button
						type="button"
						class="next-filter-chip next-filter-chip-more"
						aria-expanded="false"
						data-label-more="<?= esc_attr(sprintf(__('+%s more', LANG_STRING), number_format_i18n($more_count))) ?>"
						data-label-less="<?= esc_attr__('Show less', LANG_STRING) ?>"
					><?= esc_html(sprintf(__('+%s more', LANG_STRING), number_format_i18n($more_count))) ?></button>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
