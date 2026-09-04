<!DOCTYPE html>
<html <?php language_attributes() ?>>

<head>
	<meta charset="utf-8" />
	<title><?php wp_title('|', true, 'right') ?></title>
	<script>
		const URL_SITE = '<?= home_url() ?>',
			URL_ASSETS = '<?= URL_ASSETS ?>',
			URL_AJAX = '<?= AJAX_URL ?>'
	</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M7RWW8NW');</script>
<!-- End Google Tag Manager -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9Z19TJJLV4"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-9Z19TJJLV4');
</script>
	<?php wp_head() ?>
</head>

<body <?php body_class() ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7RWW8NW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<?php
	if (!is_page_template('page-login.php') && !is_page_template('page-register.php') && !is_page_template('page-forgot.php')) {
		get_template_part('templates/header/header', 'main');
	}
	?>