<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
require 'getbrowser.php';
echo getBrowser();
echo '<br /><br />';
echo $_SERVER['HTTP_USER_AGENT'];
?>
