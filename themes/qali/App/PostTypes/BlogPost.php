<?php

namespace App\PostTypes;

use Core\PostType;

class BlogPost extends PostType
{


    public $_post_name   = 'post';
    public $_prefix_blog = 'blog';

    public function register()
    {

        parent::register();

        add_action('init', [$this, 'create_new_url_querystring'], 999);
        add_filter('post_link', [$this, 'append_query_string'], 10, 3);
        add_filter('template_redirect', [$this, 'redirect_old_urls']);

        add_filter('manage_post_posts_columns', [$this, 'add_featured_post_column']);
        add_action('manage_post_posts_custom_column', [$this, 'render_featured_post_column'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_featured_post_script']);
        add_action('wp_ajax_toggle_featured_post', [$this, 'toggle_featured_post']);
    }

    public function create_new_url_querystring()
    {
        add_rewrite_rule(
            "{$this->_prefix_blog}/([^/]*)$",
            'index.php?name=$matches[1]',
            'top'
        );
        add_rewrite_tag("%{$this->_prefix_blog}%", '([^/]*)');
    }

    public function append_query_string($url, $post, $leavename)
    {
        if ($post->post_type == 'post') {
            $url = home_url(user_trailingslashit("{$this->_prefix_blog}/$post->post_name"));
        }

        return $url;
    }

    public function redirect_old_urls()
    {
        $current_uri = $_SERVER['REQUEST_URI'];
        $prefix_blog = 'blog';

        if (preg_match('/\/(\d{4})\/(\d{2})\/(\d{2})\/([^\/]+)\/?$/', $current_uri, $matches)) {
            $post_slug = $matches[4];
            $post_id   = url_to_postid($post_slug);

            if ($post_id) {
                $new_url = home_url(user_trailingslashit("{$prefix_blog}/" . get_post_field('post_name', $post_id)));

                wp_redirect($new_url, 301);
                exit();
            }
        }
    }

    public function post_type_meta_args()
    {

        $meta_boxes[] = [
            'id'         => 'standard-' . $this->_post_name . '-options',
            'title'      => __('Options', LANG_STRING),
            'post_types' => 'post',
            'context'    => 'normal',
            'priority'   => 'high',
            'autosave'   => false,
            'fields'     => [
                [
                    'name'    => __('Cover', LANG_STRING),
                    'id'      => 'cover',
                    'type'    => 'single_image',
                    'columns' => 6,
                ],
            ]
        ];

        return $meta_boxes;
    }

    public function add_featured_post_column($columns)
    {
        $columns['featured_post'] = __('Featured', LANG_STRING);
        return $columns;
    }

    public function render_featured_post_column($column, $post_id)
    {
        if ($column === 'featured_post') {
            $is_featured = get_post_meta($post_id, '_is_featured', true);
            $icon        = $is_featured ? '⭐' : '☆';
            echo "<span class='featured-post-icon' data-post-id='{$post_id}' data-featured='{$is_featured}'>{$icon}</span>";
        }
    }

    public function enqueue_featured_post_script($hook)
    {
        //if ($hook === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'post') {
        if ($hook === 'edit.php') {
            // افزودن CSS
            echo '<style>
                .featured-post-icon {
                    cursor   : pointer;
                    font-size: 20px;
                }
            </style>';

            // افزودن JS اینلاین
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    document.querySelectorAll(".featured-post-icon").forEach(function (icon) {
                        icon.addEventListener("click", function () {
                            const postId     = this.getAttribute("data-post-id");
                            const isFeatured = this.getAttribute("data-featured") === "1" ? "0" : "1";

                            fetch(ajaxurl, {
                                method : "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: new URLSearchParams({
                                    action     : "toggle_featured_post",
                                    nonce      : "' . wp_create_nonce('featured_post_nonce') . '",
                                    post_id    : postId,
                                    is_featured: isFeatured
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.setAttribute("data-featured", isFeatured);
                                    this.textContent = isFeatured === "1" ? "⭐" : "☆";
                                } else {
                                    alert("Error!");
                                }
                            });
                        });
                    });
                });
            </script>';
        }
    }

    // هندلر AJAX برای تغییر وضعیت نوشته
    public function toggle_featured_post()
    {
        check_ajax_referer('featured_post_nonce', 'nonce');

        $post_id     = intval($_POST['post_id']);
        $is_featured = intval($_POST['is_featured']);

        if (current_user_can('edit_post', $post_id)) {
            update_post_meta($post_id, '_is_featured', $is_featured);
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Access denied.', LANG_STRING));
        }
    }
}
