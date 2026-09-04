<?php

namespace App\Controller;

class MailSender
{
    public $theme = 'templates/mail/mail-main.php';

    public function get_email_manager()
    {
        $settings = get_option('settings');
        return isset($settings['contact']['manager']) ? $settings['contact']['manager'] : '';
    }

    public function register()
    {
        add_action('phpmailer_init', array($this, 'send_html'));
        add_filter('wp_mail_content_type', array($this, 'set_content_type'), 100);
        add_filter('wp_mail', array($this, 'email_subject_remove_site_name'), 0);
        add_filter('wp_new_user_notification_email', array($this, 'edit_new_user_notification_email'), 10, 3);
        //add_filter('email_change_email', array($this, 'email_change_email'), 10, 3);

    }

    public function email_change_email($email_change_email, $user, $userdata)
    {
        $email_change_email['to'] = $this->get_email_manager;

        return $email_change_email;
    }

    public function edit_new_user_notification_email($wp_new_user_notification_email, $user, $blogname)
    {

        $pass     = isset($_POST['pass1']) ? $_POST['pass1'] : '';
        $home_url = home_url('/');

        $message                                   = sprintf(
            __('Your Username is : %s and Your password is : %s<br /><a href="%s">Login Page</a>', LANG_STRING),
            $user->data->user_login,
            $pass,
            $home_url
        );
        $wp_new_user_notification_email['message'] = $message;

        return $wp_new_user_notification_email;
    }

    public function email_subject_remove_site_name($email)
    {
        $blogname         = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $email['subject'] = str_replace("[" . $blogname . "] - ", "", $email['subject']);
        $email['subject'] = str_replace("[" . $blogname . "]", "", $email['subject']);
        $email['message'] = str_replace("با احترام\n", "", $email['message']);
        $email['message'] = str_replace("گروه", "", $email['message']);
        $email['message'] = str_replace("همه در ", "", $email['message']);
        $email['message'] = str_replace("###SITEURL###", "", $email['message']);
        if ($email['subject'] == 'آدرس ایمیل جدید' || $email['subject'] == 'New Email Address') {
            $email['to'] = $this->get_email_manager;
        }


        return $email;
    }

    function set_content_type()
    {
        $content_type = 'text/html';

        return $content_type;
    }

    public function send_html($php_mailer)
    {

        $message = $php_mailer->Body;
        //$message = nl2br($message);
        $message = make_clickable($message);

        $data = [
            'title'       => $php_mailer->Subject,
            'description' => $message,
        ];

        $message = $this->theme($data);

        $php_mailer->Body = $message;
    }

    public function send($user_id, $subject, $description, $is_mail = false)
    {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $user    = get_userdata($user_id);

        if ($is_mail === false) {
            if (! isset($user->data->user_email)) {
                return false;
            } else {
                $to = $user->data->user_email;
            }
        } else {
            if (is_email($user_id)) {
                $to = $user_id;
            } else {
                return false;
            }
        }
        $sent = wp_mail($to, $subject, $description, $headers);

        return $sent;
    }

    public function theme($data, $debug = false)
    {
        ob_start();
        $data_html = '';
        get_template_part_var(
            $this->theme,
            $data
        );
        $data_html .= ob_get_clean();
        if ($debug) {
            echo $data_html;
            die;
        }

        return $data_html;
    }
}
