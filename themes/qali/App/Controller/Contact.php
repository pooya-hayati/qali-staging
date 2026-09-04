<?php

namespace App\Controller;

class Contact
{
    public function register()
    {
        add_action('wp_ajax_' . 'contact', array($this, 'contact'));
        add_action('wp_ajax_nopriv_' . 'contact', array($this, 'contact'));
    }

    public function get_email_manager()
    {
        $settings = get_option('settings');
        return isset($settings['contact']['manager']) ? $settings['contact']['manager'] : '';
    }

    public function contact()
    {
        parse_str($_POST['data'], $_data);
        $theme = 'templates/mail/mail-contact.php';
        $_data = parse_args([
            'name'    => '',
            'email'   => '',
            'phone'   => '',
            'subject' => '',
            'message' => '',
        ], $_data);

        ob_start();
        $data_html = '';
        get_template_part_var(
            $theme,
            $_data
        );
        $data_html .= ob_get_clean();

        $to        = $this->get_email_manager();
        $subject   = $_data['subject'];
        wp_mail($to, $subject, $data_html);
        wp_send_json_success([
            'forward' => home_url(),
            'message' => __('Your message has been sent', LANG_STRING)
        ]);
        die;
    }
}
