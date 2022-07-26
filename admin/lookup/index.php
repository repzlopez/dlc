<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

require('../fetch.php');

$adminpage = 'lookup';
testScope("global|" . $adminpage, DLC_ADMIN);

$_SESSION['lastpage'] = $_SERVER['REQUEST_URI'];
$_SESSION['lastURI']  = $adminpage;
$_SESSION['a_page']   = $adminpage;

$title = '| ' . strtoupper($adminpage);
$content = isset($_GET['p']) ? $_GET['p'] : '';
$page = "Lookup Service";
$y = '';

ob_start();
include('../head.php');
if(!ISIN_ADMIN) {
	$y .= $mainlogo;

}elseif($content!='') {
	include("index_$content.php");
	$y .= $backbtn;

} else {
	$y .= '<ul class="home">';
	$y .= '<li><a href="?p=distrilookup">DISTRIBUTOR LOOKUP</a></li>';
	$y .= '<li><a href="?p=distripv">DISTRIBUTOR PV</a></li>';
	$y .= '<li><a href="?p=lineage">DISTRIBUTOR LINEAGE</a></li>';
	$y .= '<li><a href="?p=allowsponsor&do=0">ALLOWED TO SPONSOR</a></li>';
	$y .= '<li><a href="?p=preferredmember&do=0">PREF MEMBER</a></li>';
	$y .= DIV_CLEAR . '</ul>';
}

echo $y;
require('../foot.php');
ob_end_flush();
?>
