<?php
if (!isset($_SESSION)) {
  session_set_cookie_params(0);
  session_start();
}

require('inc/setup.php');
require('inc/head.php');
require('inc/header.php');
require('inc/main.php');
require('inc/footer.php');
require('inc/foot.php');
?>
