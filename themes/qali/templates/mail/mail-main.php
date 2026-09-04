<?php
$assets_url = assets_url('img/mail/');
$rtl        = is_rtl() ? 'rtl' : 'ltr';
$site_name  = get_bloginfo('name');
$site_desc  = get_bloginfo('description');
$site_url   = get_bloginfo('url');
$settings   = get_option('settings');
$logo       = isset($settings['general']['logo']) ? image_link($settings['general']['logo'], 'full') : '';

$description = isset($this->description) ? $this->description : '';
$copyright   = isset($settings['general']['copyright']) ? $settings['general']['copyright'] : '';
$phone       = isset($settings['contact']['phone']) ? $settings['contact']['phone'] : '';
$address     = isset($settings['contact']['address']) ? $settings['contact']['address'] : '';
$email       = isset($settings['contact']['email']) ? $settings['contact']['email'] : '';
?>

<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;UTF-8"/>
    <style type="text/css">
        body {
            direction: <?= $rtl ?>;
            margin: 0;
            padding: 15px;
            background-color: #F4F3F4;
            font-family: IRANSans, Tahoma, Helvetica, Arial, sans-serif;
            font-size: 14px;

        }

        #table_mail {
            max-width: 700px;
            margin: 0 auto;
            padding: 15px;
            border: solid 1px #d9d9d9;
            font-family: IRANSans, Tahoma, Helvetica, Arial, sans-serif;
            font-size: 14px;
        }

        #header {
            font-family: IRANSans, Tahoma, Helvetica, Arial, sans-serif;
            border: none;
            color: #444;
            width: 100%;
        }

        #content {
            margin-top: 15px;
            padding-top: 15px;
            font-size: 14px;
            color: #444;
            border: none;
            border-top: solid 1px #d9d9d9;
            width: 100%;
        }

        #footer {
            margin-top: 15px;
            padding-top: 15px;
            color: #000;
            font-size: 12px;
            border: none;
            border-top: solid 1px #d9d9d9;
            width: 100%;
        }
    </style>
</head>

<body text="#444444" bgcolor="#F4F3F4" link="#21759B" alink="#21759B" vlink="#21759B">
<table id="table_mail" border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff" style="" dir="<?= $rtl ?>">
    <tbody>
    <tr>
        <td>
            <table id="header" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <a href="<?= $site_url ?>">
                            <img src="<?= $logo ?>" alt="<?= $site_name ?>" style="max-height: 50px; max-width: 100%"/>
                        </a>
                    </td>
                    <td align="<?= is_rtl() ? 'left' : 'right' ?>" valign="bottom" style="font-size: 14px; color: #777777;">
                        <div><?= $site_name ?></div>
                        <div><?= $site_desc ?></div>
                    </td>
                </tr>
                </tbody>
            </table>
            <table id="content" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <?= $description ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <table id="footer" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <div><Strong>©<?= date('Y') ?> :</Strong> <?= $copyright ?></div>
                        <div><strong><?= __('Phone', LANG_STRING) ?>:</strong> <?= $phone ?></div>
                        <div><strong><?= __('Address', LANG_STRING) ?>:</strong> <?= $address ?></div>
                        <div><strong><?= __('Email', LANG_STRING) ?>:</strong> <a href="mailto:<?= $email ?>"><?= $email ?></a></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>

</html>