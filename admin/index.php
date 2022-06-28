<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK', 1);
require 'fetch.php';
require 'getwebstat.php';

unset($_SESSION['msg1']);
unset($_SESSION['msg2']);
unset($_SESSION['dbprod']);

unset($_SESSION['gos_logged']);
unset($_SESSION['pcm_logged']);
unset($_SESSION['mig_logged']);
unset($_SESSION['gos_bad']);
unset($_SESSION['gos_last']);
unset($_SESSION['pcm_bad']);
unset($_SESSION['pcm_last']);

unset($_SESSION['sortarray']);
unset($_SESSION['prodsearch']);
unset($_SESSION['prodfilter']);
unset($_SESSION['prodsort']);
unset($_SESSION['center_orders']);

$_SESSION['lastpage'] = null;

if( !ISIN_ADMIN && !ISIN_GOS && !ISIN_PCM && !ISIN_MIG ) {
	$_SESSION['catch_login'] = 'admin';
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=/login">';
	exit;
}

$content = isset($_GET['p']) ? $_GET['p'] :'';

if( $content!='' ) $page = $arrLinks[$content];
$title = '| Admin';
$str   = '';

ob_start();
include('head.php');

if( $content!='' ) {
	include("inc/index_$content.php");

} else {
	$str .= '<ul class="home">';
		if( IS_GLOB || testScope("distri") ) $str .= '<li><a href="distriserve">DISTRIBUTOR SERVICE</a></li>';
		if( IS_GLOB || testScope("bizdev") ) $str .= '<li><a href="bizdev">BIZ DEV</a></li>';
		if( IS_GLOB || testScope("proddev") ) $str .= '<li><a href="proddev">PROD DEV</a></li>';
		if( IS_GLOB || testScope("logis") ) $str .= '<li><a href="logistics">LOGISTICS</a></li>';
		if( IS_GLOB || testScope("orders") ) $str .= '<li><a href="orders">ORDERS</a></li>';
		if( IS_GLOB || testScope("data") ) $str .= '<li><a href="data">DATA</a></li>';
		if( IS_GLOB || testScope("accounting") ) $str .= '<li><a href="accounting">ACCOUNTING</a></li>';

		if( testScope("encoding") ) $str .= '<li><a href="distriserve?p=newdistri&do=0">ENCODING</a></li>';
		if( testScope("registration") ) $str .= '<li><a href="distriserve?p=olreg&do=0">REGISTRATION</a></li>';

		if (IS_GLOB || testScope("lookup")) $str .= '<li><a href="lookup">LOOKUP</a></li>';

		$str .= DIV_CLEAR.'</ul>';

	if( IS_GLOB || testScope("gos|pcm") ) {
		$str .= '<ul class="home">';
			if (IS_GLOB || testScope("gos")) $str .= '<li><a href="' . DLC_GORT . '">GOS<br>ADMIN</a></li>';
			if (IS_GLOB || testScope("pcm")) $str .= '<li><a href="' . DLC_PCRT . '">PCM<br>ADMIN</a></li>';
		$str .= DIV_CLEAR . '</ul>';
	}

	if(IS_GLOB) {
		$str .= '<ul class="home">';

		foreach($arrLinks as $k=>$v) {
			$str .= '<li><a href="?p='.$k.'&do=0">'.$v.'</a></li>';
		}

		$str .= DIV_CLEAR.'</ul>';
	}
}

echo $str;
include('foot.php');
ob_end_flush();?>
