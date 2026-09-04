<?php

/**
 * Template Name: Contact
 */
$settings = get_option('settings');
get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);
	$hero = $meta['hero'] ?? '';
	$meta = $meta['page'] ?? '';
?>
	<section class="section section-page-hero section-full section-covered">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="section-header">
					<h2 class="section-title" data-animate="fadeInDown"><?= isset($hero['title']) ? str_replace(['<p>', '</p>'], '', wpautop($hero['title'])) : $post->post_title ?></h2>
					<h3 class="section-subtitle" data-animate="fadeInDown"><?= str_replace(['<p>', '</p>'], '', wpautop($hero['subtitle'])) ?></h3>
				</div>
			</div>
		</div>
		<div class="section-divider" data-animate="fadeInUp">
			<img src="<?= URL_ASSETS ?>/img/pattern-6.svg" alt="<?= SITE_NAME ?>">
		</div>
	</section>
	<section class="section section-contact-form">
		<div class="section-wrapper">
			<div class="container-fluid">
				<div class="row justify-content-between">
					<div class="col-1 col-md-3 col-lg-3 col-xl-3">
						<div class="contact-form-before"></div>
					</div>
					<div class="col-9 col-md-6 col-lg-5 col-xl-4">
						<div class="section-header">
							<h2 class="section-title" data-animate="fadeInUp"><?= str_replace(['<p>', '</p>'], '', wpautop($meta['form']['title'])) ?></h2>
						</div>
						<div class="section-body">
							<form id="contact-form" method="POST" data-animate="fadeInUp">
								<fieldset>
									<div class="form-floating">
										<label for="contact_name" class="form-label required"><?= __('Your Name', LANG_STRING) ?></label>
										<input type="text" name="name" id="contact_name" class="form-control" placeholder="" required>
									</div>
									<div class="form-floating">
										<label for="contact_email" class="form-label required"><?= __('Your Email', LANG_STRING) ?></label>
										<input type="email" name="email" id="contact_email" class="form-control" placeholder="" required>
									</div>
									<div class="form-floating">
										<label for="contact_phone" class="form-label required"><?= __('Your Phone', LANG_STRING) ?></label>
										<input type="text" name="phone" id="contact_phone" class="form-control" placeholder="" required>
									</div>
									<div class="form-floating">
										<label for="contact_subject" class="form-label required"><?= __('Subject', LANG_STRING) ?></label>
										<input type="text" name="subject" id="contact_subject" class="form-control" placeholder="" required autocomplete="off">
									</div>
									<div class="form-floating">
										<label for="contact_message" class="form-label required"><?= __('Your Message', LANG_STRING) ?></label>
										<textarea name="message" id="contact_message" rows="6" class="form-control" placeholder="" required></textarea>
									</div>
								</fieldset>
								<div class="form-action">
									<button type="submit" data-loading-text="<?= __('Please Wait…', LANG_STRING) ?>" class="button button-outline-primary"><?= __('Send Message', LANG_STRING) ?></button>
								</div>
							</form>
						</div>
					</div>
					<div class="col-1 col-md-3 col-lg-3 col-xl-3">
						<div class="contact-form-after"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
}
get_footer();
?>