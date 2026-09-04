<?php

namespace App\Controller;

class BreadCrumbs
{
    public static function show()
    {
        $home_title      = __('Home Page', LANG_STRING);
        $custom_taxonomy = '';
        $prefix          = '';
        global $post, $wp_query;
        if (! is_front_page()) {
            echo '<ol class="breadcrumb">';
            echo '<li class="breadcrumb-item"><a href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
            if (is_archive() && ! is_tax() && ! is_category() && ! is_tag()) {
                echo '<li class="breadcrumb-item active">' . post_type_archive_title($prefix, false) . '</li>';
            } elseif (is_archive() && is_tax() && ! is_category() && ! is_tag()) {
                $post_type = get_post_type();
                if ($post_type != 'post') {
                    $post_type_object  = get_post_type_object($post_type);
                    $post_type_archive = get_post_type_archive_link($post_type);
                    echo '<li class="breadcrumb-item"><a href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                }
                $custom_tax_name = get_queried_object()->name;
                echo '<li class="breadcrumb-item active">' . $custom_tax_name . '</li>';
            } elseif (is_single()) {
                $post_type = get_post_type();
                if ($post_type != 'post') {
                    $post_type_object  = get_post_type_object($post_type);
                    $post_type_archive = get_post_type_archive_link($post_type);
                    echo '<li class="breadcrumb-item"><a href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                }
                $category = get_the_category();
                if (! empty($category)) {


                    $last_category   = array_values($category);
                    $last_category   = end($last_category);
                    $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');
                    $cat_parents     = explode(',', $get_cat_parents);
                    $cat_display     = '';
                    foreach ($cat_parents as $parents) {
                        $cat_display .= '<li class="breadcrumb-item active">' . $parents . '</li>';
                    }
                }
                $taxonomy_exists = taxonomy_exists($custom_taxonomy);
                if (empty($last_category) && ! empty($custom_taxonomy) && $taxonomy_exists) {
                    $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
                    $cat_id         = $taxonomy_terms[0]->term_id;
                    $cat_nicename   = $taxonomy_terms[0]->slug;
                    $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                    $cat_name       = $taxonomy_terms[0]->name;
                }
                if (! empty($last_category)) {
                    echo $cat_display;
                    echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                } elseif (! empty($cat_id)) {
                    echo '<li class="breadcrumb-item"><a href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
                    echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                } else {
                    echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                }
            } elseif (is_category()) {
                echo '<li class="breadcrumb-item active">' . single_cat_title('', false) . '</li>';
            } elseif (is_page()) {
                if ($post->post_parent) {
                    $anc = get_post_ancestors($post->ID);
                    $anc = array_reverse($anc);
                    if (! isset($parents)) {
                        $parents = null;
                    }
                    foreach ($anc as $ancestor) {
                        $parents .= '<li class="breadcrumb-item"><a href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                    }
                    echo $parents;
                    echo '<li class="breadcrumb-item active"> ' . get_the_title() . '</li>';
                } else {
                    echo '<li class="breadcrumb-item active"> ' . get_the_title() . '</li>';
                }
            } elseif (is_tag()) {
                $term_id       = get_query_var('tag_id');
                $taxonomy      = 'post_tag';
                $args          = 'include=' . $term_id;
                $terms         = get_terms($taxonomy, $args);
                $get_term_id   = $terms[0]->term_id;
                $get_term_slug = $terms[0]->slug;
                $get_term_name = $terms[0]->name;
                echo '<li class="breadcrumb-item active">' . $get_term_name . '</li>';
            } elseif (is_day()) {
                echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . '</a></li>';
                echo '<li class="breadcrumb-item"><a href="' . get_month_link(
                    get_the_time('Y'),
                    get_the_time('m')
                ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . '</a></li>';
                echo '<li class="breadcrumb-item active">' . get_the_time('jS') . ' ' . get_the_time('M') . '</li>';
            } elseif (is_month()) {
                echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . '</a></li>';
                echo '<li class="breadcrumb-item active">' . get_the_time('M') . '</li>';
            } elseif (is_year()) {
                echo '<li class="breadcrumb-item active">' . get_the_time('Y') . '</li>';
            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                echo '<li class="breadcrumb-item active">' . __(
                    'Author:',
                    LANG_STRING
                ) . ' ' . $userdata->display_name . '</li>';
            } elseif (get_query_var('paged')) {
                echo '<li class="breadcrumb-item active">' . __(
                    'Page',
                    LANG_STRING
                ) . '  ' . get_query_var('paged') . '</li>';
            } elseif (is_search()) {
                echo '<li class="breadcrumb-item active">' . __(
                    'Search results for:',
                    LANG_STRING
                ) . ' ' . get_search_query() . '</li>';
            } elseif (is_404()) {
                echo '<li class="breadcrumb-item active">' . __('Not Found', LANG_STRING) . '</li>';
            }
            echo '</ol>';
        }
    }
}
