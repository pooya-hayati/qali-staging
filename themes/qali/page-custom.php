<?php

/**
 * Template Name: Custom
 */
get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);

	$shape = [
		'Square',
		'Rectangular',
		'Round',
		'Runner',
	];

	$color = [
		'Neutral Tones' => [
			'Beige',
			'Ivory',
			'Gray',
		],
		'Bold Tones' => [
			'Burgundy',
			'Navy',
			'Emerald',
		],
		'Earthy Tones' => [
			'Terracotta',
			'Olive Green',
			'Mustard',
		],
		'Pastel Shades' => [
			'Blush Pink',
			'Sky Blue',
			'Mint Green',
		],
	];

	$design = [
		'Traditional' => [
			'Bijar',
			'Heriz',
			'Medallion',
			'Floral',
			'Garden',
			'Qashqai',
			'Tree of Life',
			'Tabriz',
			'Mehrabi',
			'Herati',
			'All Over',
			'Plain',
			'Ushak',
		],
		'Moroccan' => [
			'Azilal',
			'Beni Ourain',
		],
		'Modern' => [
			'Abstract',
			'Minimalist',
			'Geometric',
			'Striped',
			'Checkerboard',
		],
		'Chinese' => [
			'Dragon Motif',
			'Pictorial',
			'Floral',
		],
		'Other' => [
			'Animal Inspired',
			'Floral',
		],
	];

?>
	<main class="custom-wizard">
		<div class="swiper wizard-swiper">
			<div class="swiper-wrapper">
				<div class="swiper-slide" data-step="1">
					<section class="section section-custom section-full" id="custom-wizard-1">
						<div class="section-wrapper">
							<div class="container-fluid">
								<div class="row g-0">
									<div class="col-lg-10 col-xl-9 col-xxl-8 mx-auto">
										<div class="section-header">
											<h1 class="section-title">Customize Your Very Own Rug</h1>
											<div class="section-desc">Take the first step in crafting a one-of-a-kind rug that reflects your style and space. You’ll choose your preferred size, design pattern, and color. Let’s get started on turning your vision into reality!</div>
										</div>
										<div class="section-footer">
											<div class="section-nav">
												<a href="#custom-wizard-2" class="section-btn button button-outline-primary">Get Started</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				<div class="swiper-slide" data-step="2">
					<section class="section section-custom section-full" id="custom-wizard-2">
						<div class="section-wrapper">
							<div class="container-fluid">
								<div class="row g-0">
									<div class="col-lg-10 col-xl-9 col-xxl-8 mx-auto">
										<div class="section-header">
											<h2 class="section-title">Choose a Shape for Your Rug</h2>
										</div>
										<div class="section-body">
											<div class="swiper wizard-select">
												<div class="swiper-wrapper">
													<?php
													$index = 0;
													foreach ($shape as $index => $_shape) {
														$slug = strtolower(str_replace(' ', '', $_shape));
													?>
														<div class="swiper-slide">
															<label class="wizard-select-card">
																<div class="wizard-select-card-header">
																	<div class="custom-shape custom-shape-<?= $slug ?>"></div>
																</div>
																<div class="wizard-select-card-body">
																	<h3 class="wizard-select-card-title"><?= $_shape ?></h3>
																	<input type="radio" name="wizard_shape" value="shape_<?= $slug ?>" data-title="<?= $_shape ?>" <?= $index === 0 ? ' checked' : '' ?>>
																</div>
															</label>
														</div>
													<?php } ?>

												</div>
												<div class="swiper-button-next"></div>
												<div class="swiper-button-prev"></div>
											</div>
										</div>
										<div class="section-footer">
											<div class="section-nav">
												<ul class="section-tab">
													<li><a href="#custom-wizard-2"><?= __('Shape', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-3"><?= __('Size', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-4"><?= __('Design Pattern', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-5"><?= __('Color Palette', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-6"><?= __('Review', LANG_STRING) ?></a></li>
												</ul>
												<a href="#custom-wizard-3" class="wizard-next"><span>Next</span></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				<div class="swiper-slide" data-step="3">
					<section class="section section-custom section-full" id="custom-wizard-3">
						<div class="section-wrapper">
							<div class="container-fluid">
								<div class="row g-0">
									<div class="col-lg-10 col-xl-9 col-xxl-8 mx-auto">
										<div class="section-header">
											<h2 class="section-title">Choose a Size for Your Rug</h2>
											<div class="section-desc">Have something specific in mind? Enter your custom dimensions below:</div>
										</div>
										<div class="section-body">
											<div class="custom-size-group">
												<div>
													<div class="custom-size-shape"></div>
												</div>
												<div>
													<fieldset class="custom-size-field">
														<div class="form-floating">
															<label for="wizard_width" class="form-label required"><?= __('Width', LANG_STRING) ?></label>
															<input type="number" name="wizard_width" id="wizard_width" class="form-control" placeholder="" min="0" required>
														</div>
														<div class="form-floating">
															<label for="wizard_length" class="form-label required"><?= __('Length', LANG_STRING) ?></label>
															<input type="number" name="wizard_length" id="wizard_length" class="form-control" placeholder="" min="0" required>
														</div>
														<div class="form-floating">
															<label for="custom_unit" class="form-label required"><?= __('Unit', LANG_STRING) ?></label>
															<select class="form-control" name="wizard_unit" id="wizard_unit" required>
																<option>feet</option>
																<option>inch</option>
																<option>centimeter</option>
															</select>
														</div>
													</fieldset>
												</div>
											</div>
										</div>
										<div class="section-footer">
											<div class="section-nav">
												<ul class="section-tab">
													<li><a href="#custom-wizard-2"><?= __('Shape', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-3"><?= __('Size', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-4"><?= __('Design Pattern', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-5"><?= __('Color Palette', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-6"><?= __('Review', LANG_STRING) ?></a></li>
												</ul>
												<a href="#custom-wizard-2" class="wizard-prev"><span>Prev</span></a>
												<a href="#custom-wizard-4" class="wizard-next"><span>Next</span></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				<div class="swiper-slide" data-step="4">
					<section class="section section-custom section-full" id="custom-wizard-4">
						<div class="section-wrapper">
							<div class="container-fluid">
								<div class="row g-0">
									<div class="col-lg-10 col-xl-9 col-xxl-8 mx-auto">
										<div class="section-header">
											<h2 class="section-title">Choose a Design Pattern</h2>
										</div>
										<div class="section-body">
											<div class="swiper wizard-select">
												<div class="swiper-wrapper">
													<?php
													$index = 0;
													foreach ($design as $group => $items) {
														echo '<!-- ' . $group . ' -->';
														foreach ($items as $item) {
															$group_slug = strtolower(str_replace(' ', '', $group));
															$item_slug = strtolower(str_replace(' ', '', $item));

															$slug = $group_slug . '-' . $item_slug;
													?>
															<div class="swiper-slide">
																<label class="wizard-select-card">
																	<div class="wizard-select-card-header">
																		<img src="<?= URL_ASSETS ?>/img/wizard/design-<?= $slug ?>.jpg" alt="<?= $group ?> / <?= $item ?>" class="wizard-select-card-img">
																	</div>
																	<div class="wizard-select-card-body">
																		<h3 class="wizard-select-card-title"><?= $group ?></h3>
																		<h4 class="wizard-select-card-subtitle"><?= $item ?></h4>
																		<input type="radio" name="wizard_design" value="<?= $slug ?>" data-title="<?= $group ?> / <?= $item ?>" <?= $index === 0 ? ' checked' : '' ?>>
																	</div>
																</label>
															</div>
													<?php
															$index++;
														}
													}
													?>

												</div>
												<div class="swiper-button-next"></div>
												<div class="swiper-button-prev"></div>
											</div>
										</div>
										<div class="section-footer">
											<div class="section-nav">
												<ul class="section-tab">
													<li><a href="#custom-wizard-2"><?= __('Shape', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-3"><?= __('Size', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-4"><?= __('Design Pattern', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-5"><?= __('Color Palette', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-6"><?= __('Review', LANG_STRING) ?></a></li>
												</ul>
												<a href="#custom-wizard-3" class="wizard-prev"><span>Prev</span></a>
												<a href="#custom-wizard-5" class="wizard-next"><span>Next</span></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				<div class="swiper-slide" data-step="5">
					<section class="section section-custom section-full" id="custom-wizard-5">
						<div class="section-wrapper">
							<div class="container-fluid">
								<div class="row g-0">
									<div class="col-lg-10 col-xl-9 col-xxl-8 mx-auto">
										<div class="section-header">
											<h2 class="section-title">Select Your Color Palette</h2>
										</div>
										<div class="section-body">
											<div class="swiper wizard-select">
												<div class="swiper-wrapper">
													<?php
													$index = 0;
													foreach ($color as $group_title => $items) {
														$slug = strtolower(str_replace(' ', '', $group_title));
														$subtitle = implode(' / ', $items);
													?>
														<div class="swiper-slide">
															<label class="wizard-select-card">
																<div class="wizard-select-card-header">
																	<img src="<?= URL_ASSETS ?>/img/wizard/color-<?= $slug ?>.jpg" alt="<?= $group_title ?>" class="wizard-select-card-img">
																</div>
																<div class="wizard-select-card-body">
																	<h3 class="wizard-select-card-title"><?= $group_title ?></h3>
																	<h3 class="wizard-select-card-subtitle"><?= $subtitle ?></h3>
																	<input type="radio" name="wizard_color" value="<?= $slug ?>" data-title="<?= $group_title ?>" <?= $index === 0 ? ' checked' : '' ?>>
																</div>
															</label>
														</div>
													<?php
														$index++;
													}
													?>

												</div>
												<div class="swiper-button-next"></div>
												<div class="swiper-button-prev"></div>
											</div>
											<div class="section-desc">Describe your desired color or provide popular codes (e.g., HEX, RGB, or Pantone) for precision:</div>
											<fieldset class="custom-color-field">
												<div class="form-floating">
													<label for="wizard_custom_color" class="form-label"><?= __('Custom Color', LANG_STRING) ?></label>
													<textarea name="wizard_color" id="wizard_custom_color" class="form-control" placeholder="" rows="2"></textarea>
												</div>
											</fieldset>
										</div>
										<div class="section-footer">
											<div class="section-nav">
												<ul class="section-tab">
													<li><a href="#custom-wizard-2"><?= __('Shape', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-3"><?= __('Size', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-4"><?= __('Design Pattern', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-5"><?= __('Color Palette', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-6"><?= __('Review', LANG_STRING) ?></a></li>
												</ul>
												<a href="#custom-wizard-4" class="wizard-prev"><span>Prev</span></a>
												<a href="#custom-wizard-6" class="wizard-next"><span>Next</span></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				<div class="swiper-slide" data-step="6">
					<section class="section section-custom section-full" id="custom-wizard-6">
						<div class="section-wrapper">
							<div class="container-fluid">
								<div class="row g-0">
									<div class="col-lg-12 col-xl-12 col-xxl-12 mx-auto">
										<div class="section-header">
											<h2 class="section-title">Fill in your information and submit</h2>
										</div>
										<div class="section-body">
											<form id="wizard-form">
												<fieldset>
													<div class="row g4 flex-md-row-reverse">
														<div class="col-md-4">
															<ul class="custom-summary">
																<li class="summary-shape">
																	<span><?= __('Shape', LANG_STRING) ?></span>
																	<div></div>
																	<input type="hidden" name="custom_shape">
																</li>
																<li class="summary-size">
																	<span><?= __('Size', LANG_STRING) ?></span>
																	<div></div>
																	<input type="hidden" name="custom_size">
																</li>
																<li class="summary-design">
																	<span><?= __('Design Pattern', LANG_STRING) ?></span>
																	<div></div>
																	<input type="hidden" name="custom_design">
																</li>
																<li class="summary-color">
																	<span><?= __('Color Palette', LANG_STRING) ?></span>
																	<div></div>
																	<input type="hidden" name="custom_color">
																</li>
															</ul>
														</div>
														<div class="col-md-3">
															<img src="" alt="" data-base="<?= URL_ASSETS ?>/img/wizard/rug-{NAME}.jpg" class="summary-rug">
														</div>
														<div class="col-md-5">
															<div class="form-floating">
																<label for="custom_name" class="form-label required"><?= __('Your Name', LANG_STRING) ?></label>
																<input type="text" name="name" id="custom_name" class="form-control" placeholder="" required>
															</div>
															<div class="form-floating">
																<label for="custom_email" class="form-label required"><?= __('Your Email', LANG_STRING) ?></label>
																<input type="email" name="email" id="custom_email" class="form-control" placeholder="" required>
															</div>
															<div class="form-floating">
																<label for="custom_phone" class="form-label required"><?= __('Your Phone', LANG_STRING) ?></label>
																<input type="text" name="phone" id="custom_phone" class="form-control" placeholder="" required>
															</div>
															<div class="form-floating">
																<label for="custom_message" class="form-label required"><?= __('Your Message', LANG_STRING) ?></label>
																<textarea name="message" id="custom_message" rows="6" class="form-control" placeholder="" required></textarea>
															</div>
														</div>
													</div>
												</fieldset>
												<div class="form-action">
													<button type="submit" class="button button-outline-primary"><?= __('Submit Request', LANG_STRING) ?></button>
												</div>
											</form>
										</div>
										<div class="section-footer">
											<div class="section-nav">
												<ul class="section-tab">
													<li><a href="#custom-wizard-2"><?= __('Shape', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-3"><?= __('Size', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-4"><?= __('Design Pattern', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-5"><?= __('Color Palette', LANG_STRING) ?></a></li>
													<li><a href="#custom-wizard-6"><?= __('Review', LANG_STRING) ?></a></li>
												</ul>
												<a href="#custom-wizard-5" class="wizard-prev"><span>Prev</span></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</main>
<?php
}
get_footer();
?>