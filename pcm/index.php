<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');
require('func.php');
$_SESSION['pcm_last']=DLC_PCRT;
$title='PCM | '.DLC_PCM;
$content=isset($_GET['p'])?$_GET['p']:'';
unset($_SESSION['pcm_edit_orders']);
unset($_SESSION['center_orders_data']);
unset($_SESSION['center_orders']);
unset($_SESSION['pcm_edit']);
unset($_SESSION['for_edit']);
unset($_SESSION['post']);

if(!ISIN_PCM){
	$_SESSION['catch_login']='pcm';
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=/login">';exit;
}

$y='';
ob_start();
include('head.php');
if($content!=''){
	include("../admin/lookup/index_$content.php");
}else{ $y.='<ul class="home">';
	$y.=IS_PCM?'<li><a href="admin">ADMIN</a></li>':'';
	$y.=!IS_PCM?'<li><a href="orders">ORDERS</a></li>':'';
	$y.='<li><a href="remit">REMIT</a></li>';
	$y.=!IS_PCM?'<li><a href="distri">DISTRI</a></li>':'';
	$y.='<li><a href="'.(IS_PCM?"/admin/logistics/?p=stocks&do=0":'stocks').'">STOCKS</a></li>';
	$y.=!IS_PCM?'<li><a href="setup">SETUP</a></li>':'';
	$y.=IS_PCM?'<li><a href="'.DLC_ADMIN.'">DLC<br>ADMIN</a></li>':'';
	$y.=!IS_PCM?'<li><a href="?p=allowsponsor&do=0">ALLOWED TO SPONSOR</a></li>':'';
	$y.=DIV_CLEAR.'</ul>';
}echo $y;
include('foot.php');
ob_end_flush();
?>
