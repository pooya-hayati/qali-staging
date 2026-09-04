<?php

namespace App\Controller;

use WP_User_Query;

class Profile
{
    private $validation;

    public function __construct()
    {
        $this->validation = new \App\Controller\Validation();

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        add_action('wp_ajax_nopriv_' . 'user_login', [$this, 'user_login']);
        add_action('wp_ajax_nopriv_' . 'user_register', [$this, 'user_register']);
        add_action('wp_ajax_nopriv_' . 'password_forget', [$this, 'password_forget']);
        add_action('wp_ajax_nopriv_' . 'password_reset', [$this, 'password_reset']);

        //add_action('template_redirect', [$this, 'force_edit_profile']);
        add_action('admin_init', [$this, 'admin_dashboard_access']);
    }

    public function enqueue_scripts()
    {
        if (is_page('login') || is_page('register') || is_page('forgot-password')) {
            wp_enqueue_script('account', URL_ASSETS . '/js/account.js?v=' . THEME_VERSION . '', ['main'], null, true);
            wp_localize_script('account', 'account_params', [
                'nonce' => wp_create_nonce('account_nonce'),
            ]);
        }
    }

    public function user_login()
    {
        if (!check_ajax_referer('account_nonce', 'security', false)) {
            wp_send_json_error(['message' => __('Invalid security token.', LANG_STRING)]);
        }

        parse_str($_POST['data'], $_data);

        $data = parse_args(
            [
                'email'    => '',
                'password' => '',
                'remember' => '',
                'ref'      => ''
            ],
            $_data
        );

        $email    = sanitize_email($data['email']);
        $password = $data['password'];
        $remember = isset($data['remember']) ? boolval($data['remember']) : false;
        $ref      = isset($data['ref']) ? esc_url_raw($data['ref']) : home_url();

        $fields = [
            [
                'name'  => 'Email',
                'value' => $email,
                'rules' => [
                    ['type' => 'required'],
                    ['type' => 'email'],
                ],
            ],
            [
                'name'  => 'Password',
                'value' => $password,
                'rules' => [
                    ['type' => 'required'],
                ],
            ],
        ];
        $this->validation->validate($fields);

        $user = wp_signon([
            'user_login'    => $email,
            'user_password' => $password,
            'remember'      => $remember
        ], false);

        if (is_wp_error($user)) {
            $errors        = $user->get_error_codes();
            $error_message = '';

            foreach ($errors as $error) {
                switch ($error) {
                    case 'invalid_username':
                        $error_message = __('The username is invalid.', LANG_STRING);
                        break;
                    case 'incorrect_password':
                        $error_message = __('The password is incorrect.', LANG_STRING);
                        break;
                    case 'empty_username':
                        $error_message = __('Please enter a username.', LANG_STRING);
                        break;
                    case 'empty_password':
                        $error_message = __('Please enter a password.', LANG_STRING);
                        break;
                    default:
                        $error_message = __('An unknown error occurred.', LANG_STRING);
                        break;
                }
            }

            wp_send_json_error(['message' => $error_message]);
        } else {
            wp_send_json_success([
                'message' => __('Successfully logged in. Welcome!', LANG_STRING),
                'forward' => $ref
            ]);
        }
    }

    public function user_register()
    {
        if (!check_ajax_referer('account_nonce', 'security', false)) {
            wp_send_json_error(['message' => __('Invalid security token.', LANG_STRING)]);
        }

        parse_str($_POST['data'], $_data);

        $data = parse_args(
            [
                'first_name' => '',
                'last_name'  => '',
                'email'      => '',
                'password'   => '',
                'ref'        => ''
            ],
            $_data
        );

        $first_name = sanitize_text_field($data['first_name']);
        $last_name = sanitize_text_field($data['last_name']);
        $email = sanitize_email($data['email']);
        $password = $data['password'];
        $ref = isset($data['ref']) ? esc_url_raw($data['ref']) : home_url();

        $fields = [
            [
                'name' => 'first_name',
                'value' => $first_name,
                'rules' => [
                    ['type' => 'required'],
                    ['type' => 'english'],
                ],
            ],
            [
                'name' => 'last_name',
                'value' => $last_name,
                'rules' => [
                    ['type' => 'required'],
                    ['type' => 'english'],
                ],
            ],
            [
                'name' => 'Email',
                'value' => $email,
                'rules' => [
                    ['type' => 'required'],
                    ['type' => 'email'],
                ],
            ],
            [
                'name' => 'Password',
                'value' => $password,
                'rules' => [
                    ['type' => 'required'],
                ],
            ],
        ];
        $this->validation->validate($fields);

        if (email_exists($email)) {
            wp_send_json_error(['message' => __('This email is already registered.', LANG_STRING)]);
            return;
        }

        $userdata = [
            'user_login' => $email,
            'user_email' => $email,
            'user_pass'  => $password,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'role'       => 'subscriber'
        ];

        $user_id = wp_insert_user($userdata);

        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => __('An error occurred while registering the user.', LANG_STRING)]);
        } else {
            $user = wp_signon([
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => true
            ], false);

            if (is_wp_error($user)) {
                wp_send_json_error(['message' => __('Login failed after registration.', LANG_STRING)]);
            } else {
                wp_send_json_success([
                    'message'     => __('Registration successful. Welcome!', LANG_STRING),
                    'forward' => $ref
                ]);
            }
        }
    }

    public function password_forget()
    {
        if (!check_ajax_referer('account_nonce', 'security', false)) {
            wp_send_json_error(['message' => __('Invalid security token.', LANG_STRING)]);
        }

        parse_str($_POST['data'], $_data);
        $data = parse_args(['email' => ''], $_data);

        $error      = false;
        $show_error = __('A password reset link was sent to your Email.', LANG_STRING);

        if (empty($data['email'])) {
            $error      = true;
            $show_error = __('Please enter a valid email address', LANG_STRING);
        } elseif (!is_email($data['email'])) {
            $error      = true;
            $show_error = __('Invalid email format', LANG_STRING);
        }

        if (!$error) {
            $exist_user = get_user_by('email', $data['email']);
            if (!$exist_user) {
                $error      = true;
                $show_error = __('This email address has not been previously registered in the System!', LANG_STRING);
            } else {
                $user_id_new = $exist_user->ID;
                $hash        = hash('sha256', $exist_user->data->user_email);
                update_user_meta($user_id_new, 'reset_password_hash', $hash);

                $subject     = __('Reset Password', LANG_STRING);
                $active_link = home_url('/forgot-password?code=' . $hash);
                $message     = sprintf(
                    __('You requested to reset your password. If this is correct, please click the following link: <a href="%s">Reset Password</a>', LANG_STRING),
                    $active_link
                );
                wp_mail($data['email'], $subject, $message);
            }
        }

        $data_out = ['message' => $show_error, 'forward' => home_url()];
        if ($error) {
            wp_send_json_error($data_out);
        } else {
            do_action('forgot_user', $user_id_new);
            wp_send_json_success($data_out);
        }

        die();
    }

    public function password_reset()
    {
        if (!check_ajax_referer('account_nonce', 'security', false)) {
            wp_send_json_error(['message' => __('Invalid security token.', LANG_STRING)]);
        }

        parse_str($_POST['data'], $_data);
        $data = parse_args(
            [
                'password'         => '',
                'confirm_password' => '',
                'hash'             => '',
            ],
            $_data
        );

        $error               = false;
        $data_out['forward'] = home_url('/login');
        $show_error          = __('Your password was changed successfully, Redirecting to login page...', LANG_STRING);

        $user_query = new WP_User_Query(
            [
                'meta_key'   => 'reset_password_hash',
                'meta_value' => $data['hash'],
            ]
        );

        if (!$user_query->results) {
            $error      = true;
            $show_error = __('You do not have access to change password', LANG_STRING);
        } else {
            $_data   = $user_query->results[0];
            $user_id = $_data->ID;

            if (empty($data['password'])) {
                $error      = true;
                $show_error = __('Enter the password', LANG_STRING);
            } elseif (strlen($data['password']) < 6) {
                $error      = true;
                $show_error = __('The password must have a minimum of 6 characters!', LANG_STRING);
            } elseif ($data['password'] !== $data['confirm_password']) {
                $error      = true;
                $show_error = __('Confirmation password is incorrect', LANG_STRING);
            }

            if (!$error) {
                $data_user            = new \stdClass();
                $data_user->user_pass = wp_hash_password($data['password']);
                $data_user->ID        = $user_id;
                wp_update_user($data_user);

                delete_user_meta($user_id, 'reset_password_hash');
            }
        }

        $data_out['message'] = $show_error;

        if ($error) {
            wp_send_json_error($data_out);
        } else {
            do_action('reset_password', $user_id);
            wp_send_json_success($data_out);
        }

        die();
    }

    public function admin_dashboard_access()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (
            is_user_logged_in() && is_admin()
            && !current_user_can('administrator')
            && !current_user_can('product_manager')
            && !current_user_can('blog_manager')
            && !current_user_can('shop_manager')
        ) {

            wp_redirect(home_url('/dashboard'));
            exit;
        }
    }

    public static function user_data()
    {
        $user_id = get_current_user_id();
        return get_user_meta_all($user_id);
    }

    public static function get_user_full_name($user_id)
    {
        $meta  = get_user_meta_all($user_id);
        $names = [
            isset($meta['first_name']) ? $meta['first_name'] : null,
            isset($meta['last_name']) ? $meta['last_name']  : null,
        ];
        return implode(' ', $names);
    }

    public static function is_complete_profile($user_id)
    {
        if (is_user_logged_in()) {
            $user_data = get_userdata($user_id);
            $user_meta = _get_all_meta($user_id, 'user');
            if (isset(
                $user_meta['first_name'],
                $user_meta['last_name'],
                $user_meta['mobile'],
            )) {
                return true;
            }
            return false;
        }
    }
    public static function force_edit_profile()
    {
        if (is_user_logged_in()) {
            global $wp_query;
            $user = wp_get_current_user();

            // Check if the user has administrative capabilities
            if (in_array('administrator', $user->roles)) {
                return;
            }

            // Check if the user is a WooCommerce customer or subscriber
            if (!in_array('customer', $user->roles) && !in_array('subscriber', $user->roles)) {
                return;
            }

            $is_complete  = self::is_complete_profile(get_current_user_id());
            $profile_page = isset($wp_query->query['dashboard_page']) && $wp_query->query['pagename'] == 'dashboard' && $wp_query->query['dashboard_page'] == 'profile';

            if ($is_complete != true && !$profile_page && !defined('DOING_AJAX')) {
                wp_redirect(home_url('dashboard/profile/'));
                exit;
            }
        }
    }
}
