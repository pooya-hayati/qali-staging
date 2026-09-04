<?php

/**
 * Template Name: Art
 */
get_header();
while (have_posts()) {
	the_post();

	$post = get_post();
	$meta = get_post_meta_all($post->ID);
?>
	<div class="art-slideshow swiper">
		<ul class="swiper-control">
			<li class="swiper-control-top"><span>Top</span></li>
			<li class="swiper-control-right"><span>Right</span></li>
			<li class="swiper-control-bottom"><span>Bottom</span></li>
			<li class="swiper-control-left"><span>Left</span></li>
		</ul>
		<div class="swiper-wrapper">
			<div class="swiper-slide">
				<div class="slide" id="slide-1A">
					<div class="slide-inner">
						<h2 class="slide-intro-title">
							<span>Rug</span>
							<div class="swiper inline-swiper inline-swiper-1">
								<div class="swiper-wrapper">
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-01.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-02.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-03.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-04.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-05.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-06.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-07.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-08.jpg" alt="<?= $post->post_title ?>">
									</div>
								</div>
							</div>
							<span>is</span>
							<div class="swiper inline-swiper inline-swiper-2">
								<div class="swiper-wrapper">
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-09.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-10.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-11.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-12.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-13.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-14.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-15.jpg" alt="<?= $post->post_title ?>">
									</div>
									<div class="swiper-slide">
										<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1a-16.jpg" alt="<?= $post->post_title ?>">
									</div>
								</div>
							</div>
							<span>Art</span>
						</h2>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-1B">
					<div class="slide-inner">
						<div class="slide-desc">‘Rug is Art’ is a deep dive into Persian rugs and classical paintings, revealing their mesmerizing parallels.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-1C">
					<div class="slide-inner">
						<div class="slide-desc">It’s about stories in <b>knots</b> and <b>brushstrokes</b>, each <b>weaving a tale</b> as rich as the other.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-1D">
					<div class="slide-inner">
						<div class="slide-desc">This journey celebrates <b>their timeless beauty</b>, <b>artistry</b>, and <b>shared heritage</b>, showing <b>how these masterpieces of design capture hearts across time</b>.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-1E">
					<div class="slide-inner">
						<div class="curve-carousel swiper">
							<div class="swiper-wrapper">
								<div class="swiper-slide">
									<div class="curve-card">
										<div class="curve-card-header">
											<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1e-01.jpg" alt="<?= $post->post_title ?>" class="curve-card-img">
										</div>
										<div class="curve-card-body">
											<h3 class="curve-card-title">The Smile</h3>
											<h4 class="curve-card-subtitle">Mona Lisa, Leonardo da Vinci</h4>
										</div>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="curve-card">
										<div class="curve-card-header">
											<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1e-02.jpg" alt="<?= $post->post_title ?>" class="curve-card-img">
										</div>
										<div class="curve-card-body">
											<h3 class="curve-card-title">The Animal</h3>
											<h4 class="curve-card-subtitle">The Pazyryk Rug</h4>
										</div>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="curve-card">
										<div class="curve-card-header">
											<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1e-03.jpg" alt="<?= $post->post_title ?>" class="curve-card-img">
										</div>
										<div class="curve-card-body">
											<h3 class="curve-card-title">The Hand</h3>
											<h4 class="curve-card-subtitle">Salvador Mundi</h4>
										</div>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="curve-card">
										<div class="curve-card-header">
											<img src="<?= URL_ASSETS ?>/img/art/slide-1/slide-1e-04.jpg" alt="<?= $post->post_title ?>" class="curve-card-img">
										</div>
										<div class="curve-card-body">
											<h3 class="curve-card-title">The Tree</h3>
											<h4 class="curve-card-subtitle">The Tree of Life</h4>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-2A">
					<div class="slide-inner">
						<h4 class="slide-intro-surtitle">Threads of History</h4>
						<h2 class="slide-intro-title">Rug & Painting<br>Through Time</h2>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-2B">
					<div class="slide-inner">
						<div class="slide-desc">Let's take a trip back in time. The inception of Persian rugs goes way back to ancient Persia. It's a tale of art evolving over the centuries. Now, picture this alongside painting, it started from caves and landed in our homes. True art always evolves. Rug is art as it simply evolves. Both art forms have been evolving shoulder to shoulder like siblings for thousands of years. <b>Art changes, grows, and connects us all.</b></div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-2C">
					<div class="slide-inner">
						<div class="map-wrapper">
							<ul class="map-tab-nav">
								<li class="active"><a href="#tab-map-1" data-toggle="tab">Rug Map</a></li>
								<li><a href="#tab-map-2" data-toggle="tab">Painting Map</a></li>
							</ul>
							<div class="tab-content">
								<div id="tab-map-1" class="tab-pane fade in active">
									<div class="row g-3">
										<div class="col-lg-9">
											<div class="map-box">
												<div class="map-inner">
													<div id="map-1" class="map-item"></div>
												</div>
											</div>
										</div>
										<div class="col-lg-3">
											<div id="map-box-1" class="map-box map-box-info">
												<div class="map-box-header">
													<img src="" alt="<?= $post->post_title ?>" class="map-box-img">
												</div>
												<div class="map-box-body">
													<h3 class="map-box-title"></h3>
													<div class="map-box-desc"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="tab-map-2" class="tab-pane fade">
									<div class="row g-3">
										<div class="col-lg-9">
											<div class="map-box">
												<div class="map-inner">
													<div id="map-2" class="map-item"></div>
												</div>
											</div>
										</div>
										<div class="col-lg-3">
											<div id="map-box-2" class="map-box map-box-info">
												<div class="map-box-header">
													<img src="" alt="<?= $post->post_title ?>" class="map-box-img">
												</div>
												<div class="map-box-body">
													<h3 class="map-box-title"></h3>
													<div class="map-box-desc"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-3A">
					<div class="slide-inner">
						<h4 class="slide-intro-surtitle">Tapestries of Meaning</h4>
						<h2 class="slide-intro-title">Symbolism in Rugs<br>and Paintings</h2>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-3B">
					<div class="slide-inner">
						<div class="slide-desc">Persian rugs and paintings <b>are not just art</b>, they're <b>stories woven and painted</b> in shared symbols. <b>Animals</b> or <b>motifs</b>, each <b>with its own tale</b>, are more than just pretty objects, they're <b>languages in knots and strokes</b>, echoing <b>universal themes</b>. </div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-3C">
					<div class="slide-inner">
						<div class="row g-4 justify-content-center">
							<div class="col-12 col-md-7">
								<div class="art-gallery-body">
									<ul class="art-gallery-dots">
										<li><img src="<?= URL_ASSETS ?>/img/icon-art-tab-active.svg" alt="<?= $post->post_title ?>"></li>
										<li><img src="<?= URL_ASSETS ?>/img/icon-art-tab-deactive.svg" alt="<?= $post->post_title ?>"></li>
									</ul>
									<div class="art-gallery-desc">
										<p>In Persian rugs, <b>lions roar with bravery, deer prance with grace, birds soar with spirit</b>.</p>
										<p>Paintings echo the same meaning, each animal adding its own vibe – lions for guts, deer for calm, birds for rising above. It’s not just decor, it’s a tale of symbols.</p>
									</div>
								</div>
							</div>
							<div class="col-8 col-md-5">
								<div class="art-gallery">
									<div class="art-gallery-large swiper">
										<div class="swiper-wrapper">
											<div class="art-gallery-large-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3c-01.jpg" alt="<?= $post->post_title ?>">
											</div>
											<div class="art-gallery-large-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3c-02.jpg" alt="<?= $post->post_title ?>">
											</div>
										</div>
									</div>
									<div class="art-gallery-thumb swiper">
										<div class="swiper-wrapper">
											<div class="art-gallery-thumb-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3c-01.jpg" alt="<?= $post->post_title ?>">
											</div>
											<div class="art-gallery-thumb-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3c-02.jpg" alt="<?= $post->post_title ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-3D">
					<div class="slide-inner">
						<div class="row g-4 justify-content-center">
							<div class="col-12 col-md-7">
								<div class="art-gallery-body">
									<ul class="art-gallery-dots">
										<li><img src="<?= URL_ASSETS ?>/img/icon-art-tab-deactive.svg" alt="<?= $post->post_title ?>"></li>
										<li><img src="<?= URL_ASSETS ?>/img/icon-art-tab-active.svg" alt="<?= $post->post_title ?>"></li>
									</ul>
									<div class="art-gallery-desc">
										<p>In the world of art, eagles and trees reign. Eagles, with their freedom and power, stand as guardians in Persian rugs and as symbols of liberty in paintings.</p>
										<p>Trees link earth and heaven in rugs, while in paintings, they’re all about life and growth, bridging the physical and spiritual.</p>
									</div>
								</div>
							</div>
							<div class="col-8 col-md-5">
								<div class="art-gallery">
									<div class="art-gallery-large swiper">
										<div class="swiper-wrapper">
											<div class="art-gallery-large-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3d-01.jpg" alt="<?= $post->post_title ?>">
											</div>
											<div class="art-gallery-large-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3d-02.jpg" alt="<?= $post->post_title ?>">
											</div>
										</div>
									</div>
									<div class="art-gallery-thumb swiper">
										<div class="swiper-wrapper">
											<div class="art-gallery-thumb-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3d-01.jpg" alt="<?= $post->post_title ?>">
											</div>
											<div class="art-gallery-thumb-img swiper-slide">
												<img src="<?= URL_ASSETS ?>/img/art/slide-3/slide-3d-02.jpg" alt="<?= $post->post_title ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-4A">
					<div class="slide-inner">
						<h4 class="slide-intro-surtitle">Crafting Beauty</h4>
						<h2 class="slide-intro-title">Materials & Techniques<br>in Rugs & Paintings</h2>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-4B">
					<div class="slide-inner">
						<div class="slide-video">
							<video src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4b-01.mp4" poster="<?= URL_ASSETS ?>/img/art/slide-4/slide-4c-01.jpg" autoplay muted playsinline></video>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-4C">
					<div class="slide-inner">
						<div class="slide-cover"><img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4c-01.jpg" alt="<?= $post->post_title ?>"></div>
						<div class="slide-desc"><b>Impressionist painting</b> is created with <b>quick</b>, <b>visible brushstrokes</b> creating <b>movement and life</b>. Pure 19th-century genius.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-4D">
					<div class="slide-inner">
						<div class="slide-video">
							<video src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4d-01.mp4" poster="<?= URL_ASSETS ?>/img/art/slide-4/slide-4e-01.jpg" autoplay muted playsinline></video>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-4E">
					<div class="slide-inner">
						<div class="slide-cover"><img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4e-01.jpg" alt="<?= $post->post_title ?>"></div>
						<div class="slide-desc"><b>Rug weavers</b> use a variety of <b>knotting techniques</b> to create different <b>textures and patterns</b>. <i>They have a technique similar to impressionism</i> - each knot’s a <b>brushstroke</b>, crafting <b>textures and patterns</b> full of <b>life and detail</b>.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-4F">
					<div class="slide-inner">
						<div class="slide-info-grid row g-3 justify-content-center">
							<div class="col-4 col-xl-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4f-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Turkish (symmetric) knot</h3>
									</div>
								</div>
							</div>
							<div class="col-4 col-xl-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4f-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Variants of the “Jufti” Knot woven around four warps</h3>
									</div>
								</div>
							</div>
							<div class="col-4 col-xl-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4f-03.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Spanish knot or single-warp knot</h3>
									</div>
								</div>
							</div>
							<div class="col-4 col-xl-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4f-04.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Diagonal, or offset, knotting</h3>
									</div>
								</div>
							</div>
							<div class="col-4 col-xl-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4f-05.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Weaving with one warp depressed</h3>
									</div>
								</div>
							</div>
							<div class="col-4 col-xl-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4f-06.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Persian (asymmetric) knot, open to the right</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="slide-desc">Painters and rug weavers <b>mix techniques</b> for diverse effects.<br><b>Amazingly, their final works echo each other’s brilliance.</b></div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-4G">
					<div class="slide-inner">
						<div class="slide-info-grid row g-3 justify-content-center align-items-end">
							<div class="col-6 col-md-5 col-xl-4">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4g-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Claude Monet</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-5 col-xl-4">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-4/slide-4g-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ A traditional rug</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="slide-desc">How’d artists, 200 years ago, in different places, nail similar feats in two mediums? Mind-blowing!</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-5A">
					<div class="slide-inner">
						<h4 class="slide-intro-surtitle">Palette of Emotions</h4>
						<h2 class="slide-intro-title">Color Use<br>in Rugs & Paintings</h2>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-5B">
					<div class="slide-inner">
						<div class="slide-desc">Colors in both Persian rugs and paintings, from Renaissance to modern styles, are <b>pivotal in evoking emotions</b> and <b>symbolizing themes</b> like <b>wealth</b>, <b>divinity</b>, <b>calmness</b>, and <b>nature</b>.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-5C">
					<div class="slide-inner">
						<ul class="art-subsection-nav">
							<li class="active"><a href="#color-1" data-toggle="tab" style="background: #4682B4">Grey Blue</a></li>
							<li><a href="#color-2" data-toggle="tab" style="background: #8B4513">Brown</a></li>
							<li><a href="#color-3" data-toggle="tab" style="background: #FF0000">Red</a></li>
							<li><a href="#color-4" data-toggle="tab" style="background: #800080">Purple </a></li>
							<li><a href="#color-5" data-toggle="tab" style="background: #FF4500">Orange </a></li>
							<li><a href="#color-6" data-toggle="tab" style="background: #00008B">Dark Blue</a></li>
						</ul>
						<div class="tab-content">
							<div id="color-1" class="tab-pane fade in active">
								<div class="row g-5 justify-content-between align-items-end">
									<div class="col-lg-4">
										<div class="art-subsection-body">
											<h3 class="art-subsection-title" style="color: #4682B4">Grey Blue</h3>
											<div class="art-subsection-desc">Soft. Balanced. Dreamy.<br>Grey blue is the space between thoughts—a color that breathes.<br>It evokes clarity, peace, and a sense of gentle modernity.</div>
										</div>
									</div>
									<div class="col-lg-8">
										<div class="slide-info-grid row g-2 justify-content-center align-items-end">
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-BlueGrey-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko (ish) Rug</h3>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-BlueGrey-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko Painting</h3>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="color-2" class="tab-pane fade">
								<div class="row g-5 justify-content-between align-items-end">
									<div class="col-lg-4">
										<div class="art-subsection-body">
											<h3 class="art-subsection-title" style="color: #8B4513">Brown</h3>
											<div class="art-subsection-desc">Warm. Grounded. Familiar.<br>Brown evokes the feeling of comfort—like a warm hug from a space you trust.<br>It brings nature indoors and roots your home in timeless calm.</div>
										</div>
									</div>
									<div class="col-lg-8">
										<div class="slide-info-grid row g-2 justify-content-center align-items-end">
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Brown-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko (ish) Rug</h3>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Brown-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko Painting</h3>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="color-3" class="tab-pane fade">
								<div class="row g-5 justify-content-between align-items-end">
									<div class="col-lg-4">
										<div class="art-subsection-body">
											<h3 class="art-subsection-title" style="color: #FF0000">Red</h3>
											<div class="art-subsection-desc">Bold. Passionate. Alive.<br>Red stirs the soul, igniting energy and confidence wherever it lays.<br>It turns your space into a story worth hearing.</div>
										</div>
									</div>
									<div class="col-lg-8">
										<div class="slide-info-grid row g-2 justify-content-center align-items-end">
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Red-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko (ish) Rug</h3>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Red-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko Painting</h3>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="color-4" class="tab-pane fade">
								<div class="row g-5 justify-content-between align-items-end">
									<div class="col-lg-4">
										<div class="art-subsection-body">
											<h3 class="art-subsection-title" style="color: #800080">Purple</h3>
											<div class="art-subsection-desc">Mysterious. Artistic. Regal.<br>Purple evokes introspection and quiet luxury, like a poem woven in silk.<br>It’s for those who want their home to whisper rather than shout.</div>
										</div>
									</div>
									<div class="col-lg-8">
										<div class="slide-info-grid row g-2 justify-content-center align-items-end">
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Purple-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko (ish) Rug</h3>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Purple-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko Painting</h3>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="color-5" class="tab-pane fade">
								<div class="row g-5 justify-content-between align-items-end">
									<div class="col-lg-4">
										<div class="art-subsection-body">
											<h3 class="art-subsection-title" style="color: #FF4500">Orange</h3>
											<div class="art-subsection-desc">Joyful. Playful. Energizing.<br>Orange is a burst of optimism—sunshine for your floors.<br>It radiates warmth and creativity, lifting moods instantly.</div>
										</div>
									</div>
									<div class="col-lg-8">
										<div class="slide-info-grid row g-2 justify-content-center align-items-end">
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Orange-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko (ish) Rug</h3>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-Orange-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko Painting</h3>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="color-6" class="tab-pane fade">
								<div class="row g-5 justify-content-between align-items-end">
									<div class="col-lg-4">
										<div class="art-subsection-body" style="color: #00008B">
											<h3 class="art-subsection-title">Dark Blue</h3>
											<div class="art-subsection-desc">Deep. Reflective. Sophisticated.<br>Dark blue brings emotional depth, like staring into a quiet midnight sky.<br>It’s serenity in color form—elegant, calming, and endlessly cool.</div>
										</div>
									</div>
									<div class="col-lg-8">
										<div class="slide-info-grid row g-2 justify-content-center align-items-end">
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-DarkBlue-01.jpg " alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko (ish) Rug</h3>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="slide-info-card">
													<div class="slide-info-card-header">
														<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5c-DarkBlue-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
													</div>
													<div class="slide-info-card-body">
														<h3 class="slide-info-card-title">→ Marc Rothko Painting</h3>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-5D">
					<div class="slide-inner">
						<div class="slide-desc">Mark Rothko is a legend in modern art. He did not just paint; he tapped into our soul. Those big, bold color blocks of his, they're a journey into something deeper, more emotional. Unsurprisingly, in weaving rugs, colors are utilized to convey a spiritual experience.<br><b>The question arises: Did this color technique, as evident here, inspire Rothko? According to myths: Yes!</b></div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-5E">
					<div class="slide-inner">
						<div class="slide-info-grid row g-3 justify-content-center align-items-end">
							<div class="col-6 col-md-5 col-xl-4">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5e-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Antique Chinese Rug</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-5 col-xl-4">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-5/slide-5e-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Chinese Painting</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="slide-desc">Can you tell if it’s a rug or a painting? Sometimes, the difference is only in the medium.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-5F">
					<div class="slide-inner">
						<div class="slide-desc">
							<p>Before modernism, the line between painting and rug design was often blurred. Without context, a rug’s detail could be mistaken for a classical painting.</p>
							<p>Take Chinese art—both in silk scrolls and hand-knotted rugs, the dragon appears with the same grace and symbolism. Ink or wool, the message remains: power, protection, and beauty.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6A">
					<div class="slide-inner">
						<h4 class="slide-intro-surtitle">Contemporary Reflections</h4>
						<h2 class="slide-intro-title">Modern Art Inspired<br>by Persian Rug</h2>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6B">
					<div class="slide-inner">
						<div class="slide-video">
							<video src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6b-01.mp4" autoplay muted playsinline></video>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6C">
					<div class="slide-inner">
						<div class="slide-desc">In the early 20th century, as the art world began to move away from traditional representations and embrace abstraction and non-representational forms, artists sought new sources of inspiration.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6D">
					<div class="slide-inner">
						<div class="slide-desc">Persian rugs provided a unique departure from conventional artistic references.<br><b>They offered a fascinating blend of tradition and innovation, with their geometric abstractions and harmonious color combinations.</b> </div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6E">
					<div class="slide-inner">
						<div class="row g-5 justify-content-between align-items-end">
							<div class="col-lg-4">
								<div class="art-subsection-body">
									<h3 class="art-subsection-title">Wassily Kandinsky</h3>
									<div class="art-subsection-desc">One of the most notable artists influenced by Persian rugs was Wassily Kandinsky, a pioneer of abstract art. Kandinsky was drawn to the way Persian rugs used color and shape to create a sense of spirituality and emotional resonance. He believed that art could communicate on a deeper, non-representational level, <b>much like the way Persian rugs conveyed meaning through their abstract patterns.</b></div>
								</div>
							</div>
							<div class="col-lg-8">
								<div class="slide-info-grid row g-2 justify-content-center align-items-end">
									<div class="col-7">
										<div class="slide-info-card">
											<div class="slide-info-card-header">
												<img src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6e-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
											</div>
											<div class="slide-info-card-body">
												<h3 class="slide-info-card-title">→ Kandinsky</h3>
											</div>
										</div>
									</div>
									<div class="col-5">
										<div class="slide-info-card">
											<div class="slide-info-card-header">
												<img src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6e-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
											</div>
											<div class="slide-info-card-body">
												<h3 class="slide-info-card-title">→ Kandinsky</h3>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6F">
					<div class="slide-inner">
						<div class="slide-info-grid row g-2 justify-content-center align-items-end">
							<div class="col-6 col-lg-3">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6f-01.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Frank Stella “STAR OF PERSIA I” 1967</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-lg-5">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6f-02.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Frank Stella - Protractor Variation I - 1968</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-lg-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6f-03.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Paul Klee, Revolution of the Viaduct - 1937</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-lg-2">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<img src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6f-04.jpg" alt="<?= $post->post_title ?>" class="slide-info-card-img">
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Paul Klee, Twittering Machine - 1922</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="slide-desc">Other modern artists, including Paul Klee and Frank Stella, also drew inspiration from Persian rugs in their work.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6G">
					<div class="slide-inner">
						<div class="slide-desc">Not only have Persian rugs played a significant role in shaping modern art, but they have also been a source of inspiration in other fields, such as fashion. From Gucci to Hermès, they were inspired by the creative ways in which Persian rugs convey meaning.</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-6H">
					<div class="slide-inner">
						<div class="slide-info-grid row g-2 justify-content-center align-items-end">
							<div class="col-7">
								<div class="slide-info-card">
									<div class="slide-info-card-header">
										<video src="<?= URL_ASSETS ?>/img/art/slide-6/slide-6h-01.mov" autoplay muted loops playsinline class="slide-info-card-img"></video>
									</div>
									<div class="slide-info-card-body">
										<h3 class="slide-info-card-title">→ Hermès Fashion Show - 2013</h3>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-7A">
					<div class="slide-inner">
						<h4 class="slide-intro-surtitle">The Enduring Legacy of Rugs & Paintings</h4>
						<h2 class="slide-intro-title">It’s Your Turn</h2>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-7B">
					<div class="slide-inner">
						<div class="slide-desc">As you know by now, rugs, especially those Persian masterpieces, they’re not just floor decor. They’re muses in their own right, inspiring all sorts of art forms, like painting. Think of the stories, the colors, the life they bring.<br>Now, it’s your turn.</div>
						<ul class="slide-floating">
							<li><img src="<?= URL_ASSETS ?>/img/art/slide-7/slide-7b-01.jpg" alt="<?= $post->post_title ?>"></li>
							<li><img src="<?= URL_ASSETS ?>/img/art/slide-7/slide-7b-02.jpg" alt="<?= $post->post_title ?>"></li>
							<li><img src="<?= URL_ASSETS ?>/img/art/slide-7/slide-7b-03.jpg" alt="<?= $post->post_title ?>"></li>
							<li><img src="<?= URL_ASSETS ?>/img/art/slide-7/slide-7b-04.jpg" alt="<?= $post->post_title ?>"></li>
							<li><img src="<?= URL_ASSETS ?>/img/art/slide-7/slide-7b-05.jpg" alt="<?= $post->post_title ?>"></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div class="slide" id="slide-7C">
					<div class="slide-inner">
						<div class="slide-cta-grid row g-3">
							<div class="col-4">
								<div class="slide-cta-card slide-cta-collection" data-animate="fadeInUp">
									<div class="slide-cta-card-header">
										<?php /*<div class="slide-cta-collector swiper">
											<div class="swiper-wrapper">
												<div class="swiper-slide">
													<div class="collector-card">
														<img src="<?= URL_ASSETS ?>/img/demo/Rectangle 55.jpg" alt="<?= $post->post_title ?>" class="collector-card-img">
														<h3 class="collector-card-title">Luca</h3>
													</div>
												</div>
												<div class="swiper-slide">
													<div class="collector-card">
														<img src="<?= URL_ASSETS ?>/img/demo/Rectangle 4.jpg" alt="<?= $post->post_title ?>" class="collector-card-img">
														<h3 class="collector-card-title">Marek</h3>
													</div>
												</div>
												<div class="swiper-slide">
													<div class="collector-card">
														<img src="<?= URL_ASSETS ?>/img/demo/Rectangle 54.jpg" alt="<?= $post->post_title ?>" class="collector-card-img">
														<h3 class="collector-card-title">Henrik</h3>
													</div>
												</div>
												<div class="swiper-slide">
													<div class="collector-card">
														<img src="<?= URL_ASSETS ?>/img/demo/Rectangle 37.jpg" alt="<?= $post->post_title ?>" class="collector-card-img">
														<h3 class="collector-card-title">Sofia</h3>
													</div>
												</div>
												<div class="swiper-slide">
													<div class="collector-card">
														<img src="<?= URL_ASSETS ?>/img/demo/Rectangle 6.jpg" alt="<?= $post->post_title ?>" class="collector-card-img">
														<h3 class="collector-card-title">Anouk</h3>
													</div>
												</div>
											</div>
										</div>*/ ?>
									</div>
									<div class="slide-cta-card-body">
										<h3 class="slide-cta-card-title">Meet Our Collectors</h3>
										<span class="slide-cta-card-btn">Go to collections</span>
									</div>
									<a href="<?= get_permalink(get_page_by_path('collections')) ?>" class="overlay-link" target="_blank">Collections</a>
								</div>
							</div>
							<div class="col-4">
								<div class="slide-cta-card slide-cta-doc" data-animate="fadeInUp">
									<div class="slide-cta-card-header">
										<div class="slide-cta-icon">
											<img src="<?= URL_ASSETS ?>/img/art/slide-7/icon-cta-2.png" alt="<?= $post->post_title ?>">
										</div>
									</div>
									<div class="slide-cta-card-body">
										<h3 class="slide-cta-card-title">Browse Our Exclusive Products</h3>
										<span class="slide-cta-card-btn">See All</span>
									</div>
									<a href="<?= get_permalink(get_page_by_path('products')) ?>" class="overlay-link" target="_blank">Products</a>
								</div>
							</div>
							<div class="col-4">
								<div class="slide-cta-card slide-cta-contact" data-animate="fadeInUp">
									<div class="slide-cta-card-header">
										<div class="slide-cta-icon">
											<img src="<?= URL_ASSETS ?>/img/art/slide-7/icon-cta-3.png" alt="<?= $post->post_title ?>">
										</div>
									</div>
									<div class="slide-cta-card-body">
										<h3 class="slide-cta-card-title">Connect with a Consultant</h3>
										<span class="slide-cta-card-btn">Get Connected</span>
									</div>
									<a href="<?= get_permalink(get_page_by_path('contact')) ?>" class="overlay-link" target="_blank">Contact</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slide-menu">
			<button class="swiper-pagination" type="button"></button>
			<ul>
				<li><a href="">Opening</a></li>
				<li><a href="">Rug & Painting Through Time</a></li>
				<li><a href="">Symbolism in Rugs and Paintings</a></li>
				<li><a href="">Materials & Techniques in Rugs & Paintings</a></li>
				<li><a href="">Color Use in Rugs & Paintings</a></li>
				<li><a href="">Modern Art Inspired by Persian Rug</a></li>
				<li><a href="">Conclusion: It’s Your Turn!</a></li>
			</ul>
		</div>
	</div>
	<script src="<?= URL_ASSETS ?>/js/leaflet.min.js"></script>
	<script>
		const locationsRug = [{
				title: "Karabakh",
				desc: "Rich, vibrant Caucasian rugs blending floral elegance with tribal soul—each knot echoes Armenian culture and mountain spirit.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Karabakh.jpg",
				coords: [39.8294529, 46.0246961]
			},
			{
				title: "Herekeh (Turkey)",
				desc: "Famous for silk, precision, and intricate designs—Herekeh rugs are luxurious masterpieces of Ottoman craftsmanship and imperial elegance.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Herekeh.jpg",
				coords: [38.9668346, 29.8427665]
			}, {
				title: "Oushak (Anatolia)",
				desc: "Bold medallions and soft palettes—Oushak rugs bring Anatolian legacy to life with warmth, grace, and antique charm.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Oushak.jpg",
				coords: [38.6692481, 29.3649145]
			}, {
				title: "Hotan (China / Eastern Afghanistan)",
				desc: "A fusion of Chinese, Turkic, and Persian influences—Hotan rugs shimmer with ancient motifs, silk elegance, and Buddhist soul.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Hotan.jpg",
				coords: [39.8696328, 84.1808634]
			}, {
				title: "Mongolia",
				desc: "Minimal yet symbolic, Mongolian rugs reflect nomadic culture—blending Buddhist iconography and earthy strength in each hand-woven line.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Mongolia.jpg",
				coords: [46.865094, 103.8329944]
			}, {
				title: "Tibet",
				desc: "Spiritual and bold—Tibetan rugs combine vibrant colors with Buddhist symbols, often crafted from highland wool and ancient tradition.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Tibet.jpg",
				coords: [31.452696, 78.131731]
			}, {
				title: "Pazyryk (Kazakhstan)",
				desc: "World’s oldest known rug—Pazyryk is a frozen time capsule of Scythian art, preserved in ice, rich in mystery.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Pazyryk.jpg",
				coords: [47.5188747, 56.3459844]
			}, {
				title: "Agra (India)",
				desc: "Mughal opulence meets Persian finesse—Agra rugs glow with floral detail, regal symmetry, and India’s imperial artistic grandeur.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Agra.jpg",
				coords: [21.0680074, 82.7525294]
			}, {
				title: "Tabriz (Iran)",
				desc: "Fine knots, ornate designs—Tabriz rugs are refined, intellectual works of art from one of Iran’s oldest weaving centers.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Tabriz.jpg",
				coords: [38.0775666, 46.2196275]
			}, {
				title: "Heriz (Northeast Iran)",
				desc: "Bold, geometric, and rugged—Heriz rugs are architectural stories woven in wool, durable and full of Persian village character.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Heriz.jpg",
				coords: [38.2574284, 47.0892371]
			}, {
				title: "Bidjar (Northwest Iran)",
				desc: "Iron rugs of Iran—Bidjar weavings are dense, resilient, and deeply colored, made to last centuries with understated power.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Bidjar.jpg",
				coords: [34.9469899, 47.3223025]
			}, {
				title: "Sultanabad (Arak, Iran)",
				desc: "Western-favored Persian rugs—Sultanabad blends grandeur and softness, crafted for export with deep blues, gentle reds, and floral mastery.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Sultanabad.jpg",
				coords: [34.0860121, 49.6116053]
			}, {
				title: "Malayer (Iran)",
				desc: "Charming and varied—Malayer rugs are the personal diaries of Persian villages, each filled with intimate patterns and earthy tones.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Malayer.jpg",
				coords: [34.0860121, 49.6116053]
			}, {
				title: "Kashan (Iran)",
				desc: "Classic Persian beauty—Kashan rugs are luxurious, floral, and precise, known for their high-quality wool and royal Persian heritage.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Kashan.jpg",
				coords: [33.976789, 51.3854087]
			}, {
				title: "Isfahan (Iran)",
				desc: "Silk and perfection—Isfahan rugs are masterpieces of elegance, often bearing architectural harmony and motifs inspired by Safavid art.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Isfahan.jpg",
				coords: [32.6621812, 51.5222158]
			}, {
				title: "Bakhtiar (Shahr-e Kord, Iran)",
				desc: "Rural charm meets structure—Bakhtiar rugs showcase garden patterns and pastoral Persian life, crafted by tribal hands with natural dyes.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Bakhtiar.jpg",
				coords: [32.3314558, 50.8255189]
			}, {
				title: "Qashqai (Shiraz, Iran)",
				desc: "Nomadic, colorful, alive—Qashqai rugs are expressive works of art made on the move, bursting with symbols and spirit.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Qashqai.jpg",
				coords: [29.641463, 52.3669615]
			}, {
				title: "Kerman (Iran)",
				desc: "Graceful, romantic, refined—Kerman rugs are storytelling canvases with pastel hues, floral elegance, and masterful Persian craftsmanship.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Kerman.jpg",
				coords: [30.2730252, 56.9838485]
			}, {
				title: "Baluch (Balochistan)",
				desc: "Earth-toned and tribal—Baluch rugs are humble, mystical expressions of nomadic life, often small, portable, and richly symbolic.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Baluch.jpg",
				coords: [28.2473676, 58.4414177]
			}, {
				title: "Turkoman (Gonbad-e Kavus, Ashgabat)",
				desc: "Deep reds and guls—Turkoman rugs speak of Central Asian pride, tradition, and fierce identity in every geometrical motif.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-rug-Turkoman.jpg",
				coords: [37.2490148, 55.1342547]
			}
		];
		const locationsPaint = [{
				title: "Prehistoric (East Africa)",
				desc: "Cave walls became our first canvas—early humans used earth tones and symbols to express survival, spirit, and story.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Prehistoric.jpg",
				coords: [2.5843459, 27.8427556]
			},
			{
				title: "Ancient Civilizations (Egypt & Iraq)",
				desc: "Structured, sacred, symbolic—Egyptian and Mesopotamian art depicted gods, kings, and cosmic order in powerful, linear visual languages.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-AncientCivilizations.jpg",
				coords: [26.8074204, 25.5837403]
			},
			{
				title: "Medieval Period (Byzantine, Turkey, Iran)",
				desc: "Art was divine—gold, icons, and mosaic blended with Islamic geometry and Persian miniatures to mirror heaven on earth.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-MedievalPeriod.jpg",
				coords: [38.402606, 44.338174]
			},
			{
				title: "Renaissance (Rome, Florence)",
				desc: "Rebirth of humanism—proportion, beauty, and classical knowledge returned to canvas, guided by da Vinci, Michelangelo, and Raphael’s genius.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Renaissance.jpg",
				coords: [43.8453421, 11.232013]
			},
			{
				title: "Baroque (Rembrandt, Europe)",
				desc: "Drama, shadow, and light—Baroque art exploded with emotion, realism, and spiritual intensity in every stroke and swirling scene.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Baroque.jpg",
				coords: [43.717798, 10.6850884]
			},
			{
				title: "Rococo (France, Fragonard, Watteau)",
				desc: "Playful, ornate, romantic—Rococo art whispered of aristocratic pleasures, pastel dreams, and delicate escapism before revolution’s roar.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Rococo.jpg",
				coords: [46.1106885, 2.5911028]
			},
			{
				title: "Neoclassicism (Jacques-Louis David)",
				desc: "Order, logic, and virtue—Neoclassicism revived Greco-Roman ideals in art, praising heroism, stoicism, and civic duty through clear forms.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Neoclassicism.jpg",
				coords: [46.1106885, 2.5911028]
			},
			{
				title: "Romanticism (Goya, Turner)",
				desc: "Emotion over reason—Romantic art raged with nature, nightmares, and rebellion, exposing the heart’s storms and beauty.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Romanticism.jpg",
				coords: [35.7927593, -6.9713369]
			},
			{
				title: "Realism (Courbet)",
				desc: "Truth on canvas—Realism painted the working class, ordinary lives, and social struggle with bold, unflinching honesty.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Realism.jpg",
				coords: [46.1106885, 2.5911028]
			},
			{
				title: "Impressionism (Monet, Renoir)",
				desc: "Light, movement, and fleeting moments—Impressionism captured modern life in dappled strokes, as if seen through sunlit eyes.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-Impressionism.jpg",
				coords: [46.1106885, 2.5911028]
			},
			{
				title: "Post-Impressionism (Van Gogh, Cézanne)",
				desc: "Deeper color, deeper feeling—Post-Impressionism took emotion and structure further, paving the way for modern abstraction and personal vision.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-PostImpressionism.jpg",
				coords: [52.1835533, 2.6406062]
			},
			{
				title: "Modern Art (Spain: Picasso, Dali, Pollock)",
				desc: "Rules were broken—Modern art exploded into cubism, surrealism, and abstraction, challenging form, meaning, and how we see the world.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-ModernArt.jpg",
				coords: [35.7927593, -6.9713369]
			},
			{
				title: "Contemporary Art (New York: Warhol)",
				desc: "Art became everything—pop, politics, identity, irony. Warhol turned soup cans into icons and the artist into cultural celebrity.",
				img: "<?= URL_ASSETS ?>/img/art/slide-2/slide-2c-paint-ContemporaryArt.jpg",
				coords: [42.7158983, -78.4098326]
			},
		]
	</script>
<?php
}
get_footer();
?>