<?php
if (!is_page_template('page-login.php') && !is_page_template('page-register.php') && !is_page_template('page-forgot.php')) {
    get_template_part('templates/footer/footer', 'main');
}
?>
<?php wp_footer() ?>
</body>

</html>