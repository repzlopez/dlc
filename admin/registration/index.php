<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../fetch.php');
$adminpage = 'registration';
testScope("global|registration", DLC_ADMIN);

$_SESSION['lastpage'] = $_SERVER['REQUEST_URI'];
$_SESSION['lastURI']  = $adminpage;
$_SESSION['a_page']   = $adminpage;
$title   = '| '.strtoupper($adminpage);
$content = isset($_GET['p'])?$_GET['p']:'';
$item    = isset($_GET['i'])?$_GET['i']:'';
$do      = isset($_GET['do'])?$_GET['do']:0;
$page    = 'Registration';
$y = '';

ob_start();
include('../head.php');

if( !ISIN_ADMIN ) {
	$y .= $mainlogo;

} else {
	$y .= '<ul class="home">';
	$y .= '<li><a href="../distriserve?p=olreg&do=0">ONLINE REGISTRATION</a></li>';
	$y .= DIV_CLEAR.'</ul>';
	unset($_SESSION['my']);
}

echo $y;
require('../foot.php');
ob_end_flush();
?>
