<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
if(!isset($_SESSION['isLogged'])) exit;
require_once( '../reg/responsor.php' );
$x=str_replace('h2','h3',$x);
echo $x;
?>
