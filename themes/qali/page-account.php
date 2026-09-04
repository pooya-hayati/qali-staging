<?php

/**
 * Template Name: Account
 */
if (!is_user_logged_in()) {
	wp_redirect(home_url('/login'));
	exit;
}

get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);
?>
	<div class="section section-account">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-body">
					<div class="row g-3 justify-content-center">
						<div class="col-xl-10"><?php the_content() ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
}
get_footer();
?>