<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!isset($_SESSION['isLogged'])) exit;
require_once( '../distrilog/profile.php' );
require_once( '../reg/index.php' );
$x=str_replace('h2','h3',$x);
echo $x;
?>
