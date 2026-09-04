<?php
$settings = get_option('settings');
use App\Controller\PageTitle;
use App\Controller\BreadCrumbs;
?>
<header id="page-header" class="page-header-4">
	<div class="page-header-inner">
		<h1 class="page-header-title appear-on-scroll"><?= PageTitle::show() ?></h1>
	</div>
</header>