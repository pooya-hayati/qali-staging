<?php

namespace App\Controller;

class Comment
{

    public function __construct()
    {
        add_action('wp_ajax_' . 'submit_comment', [$this, 'handle_ajax_comment']);
        add_action('wp_ajax_nopriv_' . 'submit_comment', [$this, 'handle_ajax_comment']);
    }

    public function handle_ajax_comment()
    {
        // اعتبارسنجی CSRF
        if (!isset($_POST['csrf_token']) || !wp_verify_nonce($_POST['csrf_token'], 'submit_comment_nonce')) {
            wp_send_json_error(['message' => __('Security error. Please try again.', LANG_STRING)]);
            return;
        }

        // Honeypot field validation for spam protection
        if (!empty($_POST['honeypot'])) {
            wp_send_json_error(['message' => __('Spam detected.', LANG_STRING)]);
            return;
        }

        // چک کردن ورودی‌های لازم
        $comment_post_ID = isset($_POST['comment_post_ID']) ? intval($_POST['comment_post_ID']) : 0;
        $comment_content = isset($_POST['comment']) ? sanitize_text_field($_POST['comment']) : '';
        $comment_author = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
        $comment_author_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

        if (empty($comment_content) || empty($comment_author) || empty($comment_author_email) || $comment_post_ID === 0) {
            wp_send_json_error(['message' => __('Please fill in all required fields.', LANG_STRING)]);
            return;
        }

        // اعتبارسنجی ایمیل
        if (!is_email($comment_author_email)) {
            wp_send_json_error(['message' => __('Invalid email address.', LANG_STRING)]);
            return;
        }

        // اعتبارسنجی امتیاز برای صفحات محصول
        if (get_post_type($comment_post_ID) === 'product' && ($rating < 1 || $rating > 5)) {
            wp_send_json_error(['message' => __('Please provide a valid rating between 1 and 5.', LANG_STRING)]);
            return;
        }

        // ساخت آرایه نظر
        $comment_data = [
            'comment_post_ID'      => $comment_post_ID,
            'comment_author'       => $comment_author,
            'comment_author_email' => $comment_author_email,
            'comment_content'      => $comment_content,
            'comment_type'         => '',
            'comment_parent'       => 0,
            'user_id'              => get_current_user_id(),
        ];

        // درج نظر
        $comment_id = wp_insert_comment($comment_data);

        if ($comment_id) {
            // اگر نظر با موفقیت ثبت شد
            if ($rating > 0) {
                add_comment_meta($comment_id, 'rating', $rating, true);
            }
            wp_send_json_success(['message' => __('Your comment has been successfully submitted.', LANG_STRING)]);
        } else {
            wp_send_json_error(['message' => __('Error submitting comment. Please try again.', LANG_STRING)]);
        }
    }
}
