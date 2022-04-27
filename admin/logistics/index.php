<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../fetch.php');
$adminpage='logistics';
if(!ISIN_GOS&&!ISIN_PCM) testScope("global|logis|gos|pcm",DLC_ADMIN);
$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
$_SESSION['lastURI']=$adminpage;
$_SESSION['a_page']=$adminpage;
$_SESSION['dbprod']=true;
$_SESSION['isActualStocks']=false;
$title='| '.strtoupper($adminpage);
$content=isset($_GET['p'])?$_GET['p']:'';
$item=isset($_GET['i'])?$_GET['i']:'';
$do=isset($_GET['do'])?$_GET['do']:'';
$page="Logistics";
$str='';
ob_start();
include('../head.php');
if(!$_SESSION['a_logged']&&!$_SESSION['gos_logged']&&!$_SESSION['pcm_logged']){
	$str.=$mainlogo;
}elseif($content!=''){
	$_SESSION['dbprod']=true;
	include("index_$content.php");
	$str.='<div id="'.$adminpage.'" class="clear"><a href="../logistics" class="back" id="download">BACK</a></div>';
}else{
	// $str.='<ul class="home">';
	// $str.='<li><a href="?p=sales">SALES</a></li>';
	// $str.=DIV_CLEAR.'</ul>';
	$str.='<ul class="home">';
	$str.='<li><a href="?p=list">UPDATE PRODUCT LIST</a></li>';
	$str.='<li><a href="?p=warehouse&do=0">WAREHOUSE LIST</a></li>';
	$str.='<li><a href="?p=assembly">ASSEMBLY LIST</a></li>';
	$str.='<li><a href="?p=fda">FDA LIST</a></li>';
	$str.='<li><a href="?p=packages&do=0">PACKAGES</a></li>';
	$str.=DIV_CLEAR.'</ul>';
	$str.='<ul class="home">';
	$str.='<li><a href="?p=transfer">TRANSFERS</a></li>';
	$str.='<li><a href="?p=stocks&do=0">STOCKS STATUS</a></li>';
	$str.='<li><a href="?p=stocksactual&do=1">STOCKS ACTUAL</a></li>';
	$str.=DIV_CLEAR.'</ul>';
	unset($_SESSION['dbprod']);
}echo $str;
require('../foot.php');
ob_end_flush();
?>
