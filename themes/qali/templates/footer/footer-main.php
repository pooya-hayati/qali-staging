<?php
$settings = get_option('settings');
?>
<footer id="footer">
	<div class="footer-top">
		<div class="container-fluid">
			<div class="footer-logo">
				<a href="<?= get_bloginfo('url') ?>" title="<?= SITE_NAME ?>">
					<img src="<?= isset($settings['general']['logo']) ? file_link($settings['general']['logo']) : '' ?>" alt="<?= SITE_NAME ?>">
				</a>
			</div>
			<?php get_template_part('templates/navigation/nav', 'contact') ?>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container-fluid">
			<div class="copyright">© <?= wp_date('Y') ?> - <?= $settings['general']['copyright'] ?? '' ?></div>
			<?php get_template_part('templates/navigation/nav', 'access') ?>
			<?php get_template_part('templates/navigation/nav', 'footer') ?>
		</div>
	</div>
</footer>