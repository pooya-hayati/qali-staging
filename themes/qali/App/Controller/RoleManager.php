<?php

namespace App\Controller;

class RoleManager
{

    public function __construct()
    {
        // Initialize hooks
        add_action('init', [$this, 'initialize']);
        add_action('pre_get_posts', [$this, 'restrict_product_visibility']);
        add_action('save_post', [$this, 'restrict_product_submission']);
        add_action('show_user_profile', [$this, 'add_product_category_field']);
        add_action('edit_user_profile', [$this, 'add_product_category_field']);
        add_action('personal_options_update', [$this, 'save_product_category_field']);
        add_action('edit_user_profile_update', [$this, 'save_product_category_field']);
        add_action('admin_menu', [$this, 'restrict_admin_menu'], 999);
        add_action('admin_init', [$this, 'restrict_admin_access']);
        add_action('pre_get_posts', [$this, 'restrict_custom_post_types']);
    }

    /**
     * Initialize roles and capabilities
     */
    public function initialize()
    {
        if (!get_role('product_manager')) {
            add_role(
                'product_manager',
                __('Product Manager', LANG_STRING),
                [
                    'read' => true,
                    'upload_files' => true,
                    'edit_products' => true,
                    'edit_published_products' => true,
                    'delete_products' => true,
                    'delete_published_products' => true,
                    'edit_others_products' => true,
                    'delete_others_products' => true,
                    'publish_products' => true,
                    "edit_product_terms" => true,
                    "assign_product_terms" => true,
                ]
            );
        } else {
            $role = get_role('product_manager');
            $role->add_cap('read');
            $role->add_cap('upload_files');
            $role->add_cap('edit_products');
            $role->add_cap('edit_published_products');
            $role->add_cap('delete_products');
            $role->add_cap('delete_published_products');
            $role->add_cap('edit_others_products');
            $role->add_cap('delete_others_products');
            $role->add_cap('publish_products');
            $role->add_cap('edit_product_terms');
            $role->add_cap('assign_product_terms');
        }

        if (!get_role('blog_manager')) {
            add_role(
                'blog_manager',
                __('Blog Manager', LANG_STRING),
                [
                    'read' => true,
                    'upload_files' => true,

                    'edit_posts' => true,
                    'edit_published_posts' => true,
                    'edit_others_posts' => true,
                    'delete_others_posts' => true,
                    'publish_posts' => true,
                    'delete_posts' => true,
                    'edit_categories' => true,
                    'manage_categories' => true,

                    'edit_pages' => true,
                    'edit_published_pages' => true,
                    'edit_others_pages' => true,
                    'publish_pages' => true,
                    'delete_pages' => true,
                    'delete_published_pages' => true,
                ]
            );
        } else {
            $role = get_role('blog_manager');
            $role->add_cap('read');
            $role->add_cap('upload_files');

            $role->add_cap('edit_posts');
            $role->add_cap('edit_published_posts');
            $role->add_cap('edit_others_posts');
            $role->add_cap('delete_others_posts');
            $role->add_cap('publish_posts');
            $role->add_cap('delete_posts');
            $role->add_cap('edit_categories');
            $role->add_cap('manage_categories');

            $role->add_cap('edit_pages');
            $role->add_cap('edit_published_pages');
            $role->add_cap('edit_others_pages');
            $role->add_cap('publish_pages');
            $role->add_cap('delete_pages');
            $role->add_cap('delete_published_pages');
        }
    }

    /**
     * Restrict visibility of products to assigned categories
     */
    public function restrict_product_visibility($query)
    {
        if (is_admin() && $query->is_main_query() && current_user_can('edit_products')) {
            $allowed_category = get_user_meta(get_current_user_id(), 'allowed_product_category', true);
            if ($allowed_category) {
                $query->set('post_type', 'product');
                $query->set('tax_query', [
                    [
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $allowed_category,
                        'include_children' => true,
                    ],
                ]);
            }
        }
    }

    /**
     * Restrict submission of products to allowed categories
     */
    public function restrict_product_submission($post_id)
    {
        if (get_post_type($post_id) === 'product' && !current_user_can('manage_options')) {
            $allowed_category = get_user_meta(get_current_user_id(), 'allowed_product_category', true);
            if ($allowed_category) {
                $product_categories = wp_get_post_terms($post_id, 'product_cat', ['fields' => 'slugs']);
                $allowed_children_slugs = $this->get_allowed_category_slugs($allowed_category);
                foreach ($product_categories as $category) {
                    if (!in_array($category, $allowed_children_slugs)) {
                        wp_die(__('You cannot add a product to this category.', LANG_STRING));
                    }
                }
            }
        }
    }

    /**
     * Restrict access to other custom post types
     */
    public function restrict_custom_post_types($query)
    {
        if (is_admin() && $query->is_main_query()) {
            $current_post_type = $query->get('post_type');
            if ($this->user_has_role('product_manager') && $current_post_type !== 'product') {
                wp_die(__('You are not allowed to access this post type.', LANG_STRING));
            }
            if ($this->user_has_role('blog_manager') && !in_array($current_post_type, ['post', 'page', 'category', 'tag'])) {
                wp_die(__('You are not allowed to access this post type.', LANG_STRING));
            }
        }
    }

    /**
     * Get all allowed category slugs including children
     */
    private function get_allowed_category_slugs($allowed_category)
    {
        $category_object = get_term_by('slug', $allowed_category, 'product_cat');
        if (!$category_object) return [$allowed_category];

        $children = get_term_children($category_object->term_id, 'product_cat');
        $child_slugs = array_map(function ($term_id) {
            $term = get_term($term_id, 'product_cat');
            return $term ? $term->slug : null;
        }, $children);

        return array_filter(array_merge([$allowed_category], $child_slugs));
    }

    /**
     * Restrict admin menu items
     */
    public function restrict_admin_menu()
    {
        if ($this->user_has_role('product_manager')) {
            $menus_to_remove = [
                'index.php',
                'edit.php',
                'upload.php',
                'edit-comments.php',
                'themes.php',
                'plugins.php',
                'users.php',
                'tools.php',
                'options-general.php',
                'edit.php?post_type=page',
                'edit.php?post_type=job',
                'edit.php?post_type=ticket',
            ];
            foreach ($menus_to_remove as $menu) {
                remove_menu_page($menu);
            }
        }

        if ($this->user_has_role('blog_manager')) {
            $menus_to_remove = [
                'index.php',
                //'edit-comments.php',
                'themes.php',
                'plugins.php',
                'users.php',
                'tools.php',
                'options-general.php',
                //'edit.php?post_type=page',
                'edit.php?post_type=product',
                'edit.php?post_type=job',
                'edit.php?post_type=ticket',
            ];
            foreach ($menus_to_remove as $menu) {
                remove_menu_page($menu);
            }
        }
    }

    /**
     * Restrict access to certain admin pages
     */
    public function restrict_admin_access()
    {
        if ($this->user_has_role('product_manager')) {
            $restricted_pages = [
                //'upload.php',
                'edit-comments.php',
                'themes.php',
                'plugins.php',
                'users.php',
                'tools.php',
                'options-general.php',
                'edit.php?post_type=page',
                'post-new.php?post_type=page',
                'edit.php?post_type=job',
                'post-new.php?post_type=job',
                'edit.php?post_type=ticket',
                'post-new.php?post_type=ticket',
            ];
            $current_page = basename($_SERVER['PHP_SELF']);
            foreach ($restricted_pages as $page) {
                if (strpos($_SERVER['REQUEST_URI'], $page) !== false) {
                    wp_die(__('You are not allowed to access this page.', LANG_STRING));
                }
            }
        }

        if ($this->user_has_role('blog_manager')) {
            $restricted_pages = [
                //'edit-comments.php',
                'themes.php',
                'plugins.php',
                'users.php',
                'tools.php',
                'options-general.php',
                //'edit.php?post_type=page',
                //'post-new.php?post_type=page',
                'edit.php?post_type=job',
                'post-new.php?post_type=job',
                'edit.php?post_type=ticket',
                'post-new.php?post_type=ticket',
                'edit.php?post_type=product',
                'post-new.php?post_type=product',
            ];
            $current_page = basename($_SERVER['PHP_SELF']);
            foreach ($restricted_pages as $page) {
                if (strpos($_SERVER['REQUEST_URI'], $page) !== false) {
                    wp_die(__('You are not allowed to access this page.', LANG_STRING));
                }
            }
        }
    }

    /**
     * Add allowed product category field to user profile
     */
    public function add_product_category_field($user)
    {
        if (!current_user_can('manage_options')) return;

        $categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => 0,
        ]);

        $selected_category = get_user_meta($user->ID, 'allowed_product_category', true);
?>
        <h3><?php _e('Product Management Settings', LANG_STRING); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="allowed_product_category"><?php _e('Allowed Product Category', LANG_STRING); ?></label></th>
                <td>
                    <select name="allowed_product_category" id="allowed_product_category">
                        <option value=""><?php _e('Select a Category', LANG_STRING); ?></option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= esc_attr($category->slug); ?>" <?php selected($selected_category, $category->slug); ?>>
                                <?= esc_html($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
<?php
    }

    /**
     * Save allowed product category field
     */
    public function save_product_category_field($user_id)
    {
        if (!current_user_can('manage_options')) return;

        if (isset($_POST['allowed_product_category'])) {
            update_user_meta($user_id, 'allowed_product_category', sanitize_text_field($_POST['allowed_product_category']));
        }
    }

    private function user_has_role($role)
    {
        $user = wp_get_current_user();
        if (!empty($user->roles) && is_array($user->roles)) {
            return in_array($role, $user->roles);
        }
        return false;
    }
}
