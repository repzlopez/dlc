<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../fetch.php');
$adminpage = 'distriserve';

$_SESSION['lastpage'] = $_SERVER['REQUEST_URI'];
$_SESSION['lastURI']  = $adminpage;
$_SESSION['a_page']   = $adminpage;
$title   = '| '.strtoupper($adminpage);
$content = isset($_GET['p'])?$_GET['p']:'';
$item    = isset($_GET['i'])?$_GET['i']:'';
$do      = isset($_GET['do'])?$_GET['do']:0;
$page    = 'Distributor Service';
$y = '';

ob_start();
include('../head.php');

if( !ISIN_ADMIN ) {
	$y .= $mainlogo;

} elseif($content!='') {
	include("index_$content.php");
	$y .= $backbtn;

} else {
	$y .= '<ul class="home">';
	$y .= '<li><a href="update">UPDATE DATABASE</a></li>';
	$y .= '<li><a href="?p=dsmstp">UPDATE DSMSTP</a></li>';
	$y .= '<li><a href="?p=passreset">PASSWORD RESET</a></li>';
	$y .= '<li><a href="?p=responsor&do=0">RESPONSOR REQUEST</a></li>';
	$y .= DIV_CLEAR.'</ul>';

	$y .= '<ul class="home">';
	$y .= '<li><a href="?p=olreg&do=0">ONLINE REGISTRATION</a></li>';
	$y .= '<li><a href="?p=slots&do=0">SLOTS</a></li>';
	$y .= '<li><a href="?p=newdistri&do=0">NEW DISTRIBUTORS</a></li>';
	// $y .= '<li><a href="?p=responsor&do=0">FOR RESPONSOR</a></li>';
	$y .= DIV_CLEAR.'</ul>';
	unset($_SESSION['my']);
}

echo $y;
require('../foot.php');
ob_end_flush();
?>
