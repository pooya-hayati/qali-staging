<?php

namespace App\Controller;

class Pagination
{

    public static function show()
    {
        global $wp_query;
        $big   = 999999999;
        $pages = paginate_links(array(
            'base'      => str_replace($big, '%#%', get_pagenum_link($big)),
            'format'    => '?page=%#%',
            'current'   => max(1, get_query_var('paged')),
            'total'     => $wp_query->max_num_pages,
            'type'      => 'array',
            'prev_next' => true,
            'prev_text' => __('Prev', LANG_STRING),
            'next_text' => __('Next', LANG_STRING),
        ));
        if (is_array($pages)) {
            $current_page = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');
            echo '<ul class="pagination">';
            foreach ($pages as $i => $page) {
                if ($current_page == 1 && $i == 0) {
                    echo "<li class='active'>$page</li>";
                } else {
                    if ($current_page != 1 && $current_page == $i) {
                        echo "<li class='active'>$page</li>";
                    } else {
                        echo "<li>$page</li>";
                    }
                }
            }
            echo '</ul>';
        }
    }
}
