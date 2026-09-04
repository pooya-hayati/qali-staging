<?php

/**
 * Template Name: Shopping
 */
get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);
?>
	<div class="section section-shopping">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title"><?= the_title() ?></h2>
				</div>
				<div class="section-body">
					<div class="row g-3 justify-content-center">
						<div class="col-md-8 col-lg-7 col-xl-6"><?php the_content() ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
}
get_footer();
?>