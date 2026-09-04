<?php

namespace App\Controller;

class PageTitle
{

    public static function show()
    {
        if (is_singular()) {
            $page_title = get_the_title();
        } elseif (is_page()) {
            $page_title = get_the_title();
        } elseif (is_single()) {
            $page_title = get_the_title();
        } elseif (is_home()) {
            $page_title = get_the_title(get_option('page_for_posts', true));
        } elseif (is_front_page()) {
            $page_title = bloginfo('name');
        } elseif (is_search()) {
            $page_title = sprintf(__('Search Results for: %s', LANG_STRING), get_search_query());
        } elseif (is_tax()) {
            $term       = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $the_tax    = get_taxonomy(get_query_var('taxonomy'));
            $page_title = $term->name;
        } elseif (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                echo $before . get_category_parents($thisCat->parent, true, ' ' . $delimiter . ' ') . $after;
            }
            $page_title = __('Archive', LANG_STRING) . ': ' . single_cat_title('', false);
        } elseif (is_tag()) {
            $thisTag    = get_tag(get_query_var('tag'), false);
            $page_title = __('Tag', LANG_STRING) . ': ' . single_cat_title('', false);
        } elseif (get_post_type()) {
            $page_title = get_post_type_object(get_post_type())->labels->name;
        } elseif (is_404()) {
            $page_title = __('Not Found', LANG_STRING);
        } else {
            $page_title = bloginfo('name');
        }

        return $page_title;
    }
}
