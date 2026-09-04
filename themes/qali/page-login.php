<?php

/**
 * Template Name: Login
 */
if (is_user_logged_in()) {
	wp_redirect(home_url('/dashboard'));
    exit;
}

get_header();
$settings = get_option('settings');
?>
<div class="account-wrapper">
    <div class="account-hero">
        <img src="<?= URL_ASSETS ?>/img/pattern-5.svg" alt="<?= SITE_NAME ?>" class="account-hero-bg">
    </div>
    <div class="account-mainbar">
        <div class="account-header">
            <a href="<?= get_bloginfo('url') ?>" class="account-btn">Back</a>
            <a href="<?= get_bloginfo('url') ?>" title="<?= SITE_NAME ?>" class="account-logo">
                <img src="<?= isset($settings['general']['logo']) ? file_link($settings['general']['logo']) : '' ?>" alt="<?= SITE_NAME ?>">
            </a>
        </div>
        <div class="account-card">
            <div class="account-card-header">
                <h1 class="account-card-title">Login to your account</h1>
            </div>
            <div class="account-card-body">
                <form id="form-login" method="post">
                    <fieldset>
                        <div class="form-floating">
                            <label for="account_email" class="form-label required">Email</label>
                            <input name="email" id="account_email" type="email" class="form-control" placeholder="" required>
                        </div>
                        <div class="form-floating">
                            <label for="account_password" class="form-label required">Password</label>
                            <input name="password" id="account_password" type="password" class="form-control" placeholder="" required>
                        </div>
                    </fieldset>
                    <div class="form-action">
                        <button type="submit" data-loading-text="<?= __('Please Wait…', LANG_STRING) ?>" class="button button-outline-primary"><?= __('Login', LANG_STRING) ?></button>
                    </div>
                </form>
            </div>
            <div class="account-card-footer">
                <div class="account-card-switch">Not a member? <a href="<?= home_url('register') ?>">Create an account</a></div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>