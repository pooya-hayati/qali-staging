<?php
$post    = $this->post;
$meta    = get_post_meta_all($post->ID);
$title   = $post->post_title;
$excerpt = $post->post_excerpt;
$image   = post_image($post->ID, 'full');
?>
<li class="collector-card" data-animate="fadeInUp">
	<img src="<?= $image ?>" alt="<?= $title ?>" class="collector-card-img">
	<h3 class="collector-card-title"><?= $title ?></h3>
	<h4 class="collector-card-subtitle"><?= $excerpt ?></h4>
</li>