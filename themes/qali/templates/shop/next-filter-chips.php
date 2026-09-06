<?php

/**
 * "Suggested next filter" chip row for pa_* attribute archive pages (single or chained) — see
 * App\Controller\Shop::get_next_filter_suggestion(). Every chip is a real, crawlable <a href> to
 * the resulting chained URL (never a 0-product combination — filtered out before this renders).
 * "Skip" re-fetches the next remaining dimension's row via AJAX (App\Controller\Shop::
 * ajax_next_filter_suggestion(), shop.js) without changing the page URL.
 *
 * Rendered both directly (header-shop.php, initial page load) and as the AJAX "Skip" response —
 * this file is the one place its markup exists, so both stay in sync.
 */

defined('ABSPATH') || exit;

$suggestion = $this->suggestion;

if (empty($suggestion)) {
	return;
}

$skipped = is_array($this->skipped) ? $this->skipped : [];
$active  = is_array($this->active) ? $this->active : [];
$active_json = wp_json_encode(array_map(function ($entry) {
	return ['base' => $entry['base'], 'slug' => $entry['slug']];
}, $active));
?>
<div class="page-header-next-filter" id="next-filter-suggestion" data-skipped="<?= esc_attr(implode(',', $skipped)) ?>" data-active='<?= esc_attr($active_json) ?>'>
	<div class="page-header-next-filter-header">
		<span class="page-header-next-filter-label">
			<?= esc_html(sprintf(__('Narrow by %s', LANG_STRING), $suggestion['label'])) ?>
		</span>
		<button type="button" class="page-header-next-filter-skip" data-base="<?= esc_attr($suggestion['base']) ?>">
			<?= esc_html__('Skip', LANG_STRING) ?>
		</button>
	</div>
	<div class="page-header-next-filter-chips">
		<?php foreach ($suggestion['chips'] as $chip) : ?>
			<a href="<?= esc_url($chip['url']) ?>" class="next-filter-chip">
				<?= esc_html($chip['name']) ?>
				<span class="next-filter-chip-count">(<?= esc_html(number_format_i18n($chip['count'])) ?>)</span>
			</a>
		<?php endforeach; ?>
	</div>
</div>
