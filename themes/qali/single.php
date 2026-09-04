<?php

/**
 * Single Post Template
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) :
	the_post();

	$post_id     = get_the_ID();
	$title       = get_the_title();
	$date        = get_the_date('c');
	$date_display = get_the_date('F j, Y');
	$author_id   = get_post_field('post_author', $post_id);
	$author_name = get_the_author_meta('display_name', $author_id);
	$categories  = get_the_terms($post_id, 'category');
	$tags        = get_the_terms($post_id, 'post_tag');
	$excerpt     = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 30, '…');
?>

	<article id="post-<?php echo esc_attr($post_id); ?>" <?php post_class('section section-blog-single'); ?> itemscope itemtype="https://schema.org/BlogPosting">
		<div class="section-wrapper">
			<div class="container-fluid">
				<header class="section-header">
					<div class="row g-3 flex-md-row-reverse justify-content-between align-items-end">
						<div class="col-md-10">
							<h1 class="section-title" data-animate="fadeInUp" itemprop="headline">
								<?php echo esc_html($title); ?>
							</h1>
							<meta itemprop="description" content="<?php echo esc_attr(wp_strip_all_tags($excerpt)); ?>">
						</div>
						<div class="col-md-2">
							<time class="section-subtitle" datetime="<?php echo esc_attr($date); ?>" itemprop="datePublished">
								<?php echo esc_html($date_display); ?>
							</time>
							<meta itemprop="dateModified" content="<?php echo esc_attr(get_the_modified_date('c')); ?>">
						</div>
					</div>

					<div class="entry-meta-header">
						<span class="entry-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
							<meta itemprop="name" content="<?php echo esc_attr($author_name); ?>">
						</span>
						<?php if (has_post_thumbnail()) : ?>
							<meta itemprop="image" content="<?php echo esc_url(get_the_post_thumbnail_url($post_id, 'full')); ?>">
						<?php endif; ?>
					</div>
				</header>

				<div class="section-body">
					<div class="row g-3">
						<div class="col-md-8 offset-md-2">
							<div class="entry">
								<div class="entry-body" itemprop="articleBody">
									<div class="entry-content">
										<?php
										the_content(
											sprintf(
												wp_kses(
													__('Continue reading<span class="screen-reader-text"> "%s"</span>', LANG_STRING),
													['span' => ['class' => []]]
												),
												esc_html($title)
											)
										);

										wp_link_pages([
											'before' => '<div class="page-links">' . esc_html__('Pages:', LANG_STRING),
											'after'  => '</div>',
										]);
										?>
									</div>
								</div>

								<footer class="entry-footer">
									<ul class="entry-meta">
										<?php if (! empty($categories) && ! is_wp_error($categories)) : ?>
											<li>
												<strong><?php echo esc_html__('Category', LANG_STRING); ?>:</strong>
												<?php
												$category_links = array_map(
													function ($term) {
														return sprintf(
															'<a href="%s" rel="category tag">%s</a>',
															esc_url(get_term_link($term)),
															esc_html($term->name)
														);
													},
													$categories
												);
												echo wp_kses_post(implode(', ', $category_links));
												?>
											</li>
										<?php endif; ?>

										<?php if (! empty($tags) && ! is_wp_error($tags)) : ?>
											<li>
												<strong><?php echo esc_html__('Tags', LANG_STRING); ?>:</strong>
												<?php
												$tag_links = array_map(
													function ($term) {
														return sprintf(
															'<a href="%s" rel="tag">%s</a>',
															esc_url(get_term_link($term)),
															esc_html($term->name)
														);
													},
													$tags
												);
												echo wp_kses_post(implode(', ', $tag_links));
												?>
											</li>
										<?php endif; ?>
									</ul>

									<?php
									if (function_exists('the_post_navigation')) {
										the_post_navigation([
											'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', LANG_STRING) . '</span> <span class="nav-title">%title</span>',
											'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', LANG_STRING) . '</span> <span class="nav-title">%title</span>',
										]);
									}
									?>
								</footer>
							</div>

							<?php
							if (comments_open() || get_comments_number()) {
								comments_template();
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<link itemprop="mainEntityOfPage" href="<?php echo esc_url(get_permalink()); ?>">
		<meta itemprop="publisher" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
	</article>

<?php
endwhile;

get_footer();
