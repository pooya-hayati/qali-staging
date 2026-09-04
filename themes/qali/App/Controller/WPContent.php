<?php

namespace App\Controller;

class WPContent
{

    public function __construct()
    {
        $this->register();
    }

    // Initialize all the required hooks (actions and filters)
    public function register()
    {
        add_action('admin_menu', [$this, 'register_admin_page']);
        add_action('admin_post_mass_create_items', [$this, 'handle_mass_create_items']);

        $post_types = get_post_types(['public' => true], 'names');
        foreach ($post_types as $post_type) {
            add_filter("post_row_actions", [$this, 'add_duplicate_link'], 10, 2);
            add_filter("page_row_actions", [$this, 'add_duplicate_link'], 10, 2);
            add_action("bulk_actions-edit-{$post_type}", [$this, 'add_bulk_duplicate_action']);
            add_filter("handle_bulk_actions-edit-{$post_type}", [$this, 'handle_bulk_duplicate_action'], 10, 3);
        }
        add_action('admin_post_duplicate_post_with_meta', [$this, 'handle_post_duplicate']);
        add_action('admin_notices', [$this, 'display_admin_notices']);

        add_action('post_submitbox_misc_actions', [$this, 'add_button_new']);
        add_filter('redirect_post_location', [$this, 'redirect_new']);
        add_filter('redirect_post_location', [$this, 'redirect_close']);
        add_action('admin_notices', [$this, 'saved_notice']);
    }


    public function register_admin_page()
    {
        add_submenu_page(
            'tools.php',
            __('Mass Creator', LANG_STRING),
            __('Mass Creator', LANG_STRING),
            'manage_options',
            'mass-creator',
            [$this, 'render_admin_page']
        );
    }

    public function render_admin_page()
    {
        $post_types = get_post_types(['public' => true], 'objects');

        echo '<div class="wrap">';
        echo '<h1>' . __('Mass Creator', LANG_STRING) . '</h1>';

        if (isset($_GET['created'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Items successfully created!', LANG_STRING) . '</p></div>';
        }

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="form-table">';
        wp_nonce_field('mass_create_items_action', 'mass_create_items_nonce');
        echo '<input type="hidden" name="action" value="mass_create_items">';

        echo '<tr><th scope="row"><label for="item_titles">' . __('Item Titles (one per line)', LANG_STRING) . '</label></th>';
        echo '<td><textarea id="item_titles" name="item_titles" rows="10" class="large-text" required></textarea></td></tr>';

        echo '<tr><th scope="row"><label for="post_type">' . __('Post Type', LANG_STRING) . '</label></th>';
        echo '<td><select id="post_type" name="post_type" class="regular-text" required>';
        foreach ($post_types as $post_type) {
            echo '<option value="' . esc_attr($post_type->name) . '">' . esc_html($post_type->label) . '</option>';
        }
        echo '</select></td></tr>';

        echo '<tr><th scope="row"><label for="post_status">' . __('Post Status', LANG_STRING) . '</label></th>';
        echo '<td><select id="post_status" name="post_status" class="regular-text" required>';
        echo '<option value="publish">' . __('Published', LANG_STRING) . '</option>';
        echo '<option value="draft">' . __('Draft', LANG_STRING) . '</option>';
        echo '<option value="pending">' . __('Pending Review', LANG_STRING) . '</option>';
        echo '</select></td></tr>';

        echo '<tr><th scope="row"></th><td><button type="submit" class="button button-primary">' . __('Create Items', LANG_STRING) . '</button></td></tr>';
        echo '</form>';

        $this->render_created_items();
        echo '</div>';
    }

    public function render_created_items()
    {
        echo '<h2>' . __('Created Items', LANG_STRING) . '</h2>';

        $created_items = get_transient('mass_creator_created_items');
        if (!$created_items) {
            echo '<p>' . __('No items created in this session.', LANG_STRING) . '</p>';
            return;
        }

        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>' . __('Title', LANG_STRING) . '</th><th>' . __('Actions', LANG_STRING) . '</th></tr></thead><tbody>';

        foreach ($created_items as $post_id) {
            $post = get_post($post_id);

            if ($post) {
                $actions = sprintf(
                    '<a href="%1$s" target="_blank">%2$s</a>',
                    esc_url(get_permalink($post->ID)),
                    __('View', LANG_STRING)
                );

                if (current_user_can('edit_post', $post->ID)) {
                    $actions .= ' | ' . sprintf(
                        '<a href="%1$s">%2$s</a>',
                        esc_url(get_edit_post_link($post->ID)),
                        __('Edit', LANG_STRING)
                    );
                }

                if (current_user_can('delete_post', $post->ID)) {
                    $actions .= ' | ' . sprintf(
                        '<a href="%1$s" onclick="return confirm(\'%2$s\');">%3$s</a>',
                        esc_url(get_delete_post_link($post->ID)),
                        __('Are you sure you want to delete this item?', LANG_STRING),
                        __('Delete', LANG_STRING)
                    );
                }

                printf(
                    '<tr><td>%1$s</td><td>%2$s</td></tr>',
                    esc_html($post->post_title),
                    $actions
                );
            }
        }

        echo '</tbody></table>';
    }

    public function handle_mass_create_items()
    {
        if (!isset($_POST['mass_create_items_nonce']) || !wp_verify_nonce($_POST['mass_create_items_nonce'], 'mass_create_items_action')) {
            wp_die(__('Invalid nonce. Please refresh the page and try again.', LANG_STRING));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to perform this action.', LANG_STRING));
        }

        $item_titles = explode("\n", sanitize_textarea_field($_POST['item_titles'] ?? ''));
        $post_type = sanitize_text_field($_POST['post_type'] ?? '');
        $post_status = sanitize_text_field($_POST['post_status'] ?? 'draft');

        if (empty($item_titles) || empty($post_type) || empty($post_status)) {
            wp_die(__('All fields are required.', LANG_STRING));
        }

        $created_items = [];

        foreach ($item_titles as $title) {
            $title = trim($title);
            if (empty($title)) {
                continue;
            }

            $post_id = wp_insert_post([
                'post_title'   => $title,
                'post_type'    => $post_type,
                'post_status'  => $post_status,
            ]);

            if (!is_wp_error($post_id)) {
                $created_items[] = $post_id;
            }
        }

        // ذخیره آیتم‌های ساخته‌شده به صورت موقت
        set_transient('mass_creator_created_items', $created_items, 5 * MINUTE_IN_SECONDS);

        wp_redirect(admin_url('admin.php?page=mass-creator&created=1'));
        exit;
    }

    /**
     * Add a "Duplicate" link to post and page actions.
     *
     * @param array $actions Existing actions.
     * @param WP_Post $post The post object.
     * @return array Modified actions with a "Duplicate" link.
     */
    public function add_duplicate_link($actions, $post)
    {
        if (current_user_can('edit_post', $post->ID)) {
            $duplicate_url = admin_url('admin-post.php?action=duplicate_post_with_meta&post_id=' . $post->ID . '&_wpnonce=' . wp_create_nonce('duplicate_post_action'));
            $actions['duplicate'] = '<a href="' . esc_url($duplicate_url) . '">' . __('Duplicate', LANG_STRING) . '</a>';
        }
        return $actions;
    }

    /**
     * Handle copying a post with all meta data.
     */
    public function handle_post_duplicate()
    {
        $this->validate_request('duplicate_post_action');

        $post_id = intval($_GET['post_id'] ?? 0);
        if (!$post_id) {
            wp_die(__('Invalid post ID.', LANG_STRING));
        }

        $this->duplicate_post($post_id);

        // Redirect to the posts list with a success message
        $redirect_url = add_query_arg('duplicated', '1', admin_url('edit.php?post_type=' . get_post_type($post_id)));
        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Add a "Duplicate" option to bulk actions dropdown.
     *
     * @param array $bulk_actions Current bulk actions.
     * @return array Modified bulk actions.
     */
    public function add_bulk_duplicate_action($bulk_actions)
    {
        $bulk_actions['duplicate'] = __('Duplicate', LANG_STRING);
        return $bulk_actions;
    }

    /**
     * Handle bulk duplicate action.
     *
     * @param string $redirect_url URL to redirect to.
     * @param string $action The selected bulk action.
     * @param array $post_ids Selected post IDs.
     * @return string Redirect URL.
     */
    public function handle_bulk_duplicate_action($redirect_url, $action, $post_ids)
    {
        if ($action !== 'duplicate') {
            return $redirect_url;
        }

        foreach ($post_ids as $post_id) {
            $this->duplicate_post($post_id);
        }

        return add_query_arg('bulk_duplicated', count($post_ids), $redirect_url);
    }

    /**
     * Duplicate a post with all its meta data.
     *
     * @param int $post_id Post ID to duplicate.
     */
    private function duplicate_post($post_id)
    {
        $post = get_post($post_id);

        if (!$post || !current_user_can('edit_post', $post_id)) {
            return;
        }

        $new_post_id = wp_insert_post([
            'post_title'   => $post->post_title . ' (Duplicate)',
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_type'    => $post->post_type,
            'post_status'  => 'draft',
            'post_author'  => get_current_user_id(),
            'post_date'    => current_time('mysql'),
            'post_date_gmt' => current_time('mysql', 1),
        ]);

        if (is_wp_error($new_post_id)) {
            return;
        }

        $meta_data = get_post_meta($post_id);
        foreach ($meta_data as $key => $values) {
            foreach ($values as $value) {
                add_post_meta($new_post_id, $key, $value);
            }
        }
    }

    /**
     * Validate the request for nonce and permissions.
     *
     * @param string $action The nonce action.
     */
    private function validate_request($action)
    {
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], $action)) {
            wp_die(__('Invalid nonce. Please refresh the page and try again.', LANG_STRING));
        }

        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to perform this action.', LANG_STRING));
        }
    }

    /**
     * Display admin notices for duplicate actions.
     */
    public function display_admin_notices()
    {
        if (isset($_GET['duplicated'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Item successfully duplicated.', LANG_STRING) . '</p></div>';
        }

        if (isset($_GET['bulk_duplicated'])) {
            $count = intval($_GET['bulk_duplicated']);
            echo '<div class="notice notice-success is-dismissible"><p>' . sprintf(__('%d items successfully duplicated.', LANG_STRING), $count) . '</p></div>';
        }
    }

    public function add_button_new()
    {
        if (isset($_GET['post'])) {
            $status = get_post_status($_GET['post']);
        } else {
            $status = '';
        }

        $button_label_new = ($status == 'publish' || $status == 'private') ? __('Update and New', LANG_STRING) : __('Publish and New', LANG_STRING);
        $button_label_close = ($status == 'publish' || $status == 'private') ? __('Update and Close', LANG_STRING) : __('Publish and Close', LANG_STRING);

?>
        <div id="major-publishing-actions" style="overflow:hidden">
            <div id="publishing-action">
                <input type="submit" class="button button-secondary" name="save_and_new" value="<?= esc_attr($button_label_new); ?>">
                <input type="submit" class="button button-secondary" name="save_and_close" value="<?= esc_attr($button_label_close); ?>">
            </div>
        </div>
<?php
    }

    public function redirect_new($location)
    {
        if (isset($_POST['save_and_new'])) {
            $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
            $location = admin_url('post-new.php?post_type=' . $post_type);
        }
        return $location;
    }


    public function redirect_close($location)
    {
        if (isset($_POST['save_and_close'])) {
            $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
            $location = admin_url('edit.php?post_type=' . $post_type);
        }
        return $location;
    }

    public function saved_notice()
    {
        if (isset($_GET['save_and_new'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Post saved. You can now create a new one.', LANG_STRING) . '</p></div>';
        }
    }
}
