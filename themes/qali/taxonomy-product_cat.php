<?php
get_header();
?>
<?php get_template_part('templates/header/header', 'shop') ?>
<main id="page-body">
	<div class="container-fluid">
		<?php get_template_part('templates/shop/product-grid') ?>
	</div>
</main>
<?php
get_footer();
?>