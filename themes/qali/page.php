<?php
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
                    <h3 class="section-subtitle" data-animate="fadeInDown"><?= isset($hero['subtitle']) ? str_replace(['<p>', '</p>'], '', wpautop_with_shortcodes($hero['subtitle'])) : '' ?></h3>
                </div>
            </div>
        </div>
        <div class="section-divider" data-animate="fadeInUp">
            <img src="<?= URL_ASSETS ?>/img/pattern-6.svg" alt="<?= SITE_NAME ?>">
        </div>
    </section>
    <section class="section section-page-content">
        <div class="section-wrapper">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-11 col-xl-10">
                        <div class="entry">
                            <div class="entry-body">
                                <div class="entry-content"><?php the_content() ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section section-cta">
        <div class="section-wrapper">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-11">
                        <div class="section-cover" data-animate="fadeInUp">
                            <img src="<?= URL_ASSETS ?>/img/pattern-2.svg" alt="<?= SITE_NAME ?>">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="section-header">
                            <div class="section-title" data-animate="fadeInUp"><?= __('Explore Products', LANG_STRING) ?></div>
                            <div class="section-nav" data-animate="fadeInDown">
                                <a href="<?= get_post_type_archive_link('product') ?>" class="section-btn button button-outline-primary"><?= __('All Products', LANG_STRING) ?></a>
                                <a href="<?= get_permalink(get_page_by_path('collections')) ?>" class="section-btn button button-outline-primary"><?= __('All Collections', LANG_STRING) ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
}
get_footer();
?>