<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../fetch.php');
$adminpage='accounting';
testScope("global|".$adminpage,DLC_ADMIN);
$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
$_SESSION['lastURI']=$adminpage;
$_SESSION['a_page']=$adminpage;
$title='| '.strtoupper($adminpage);
$content=isset($_GET['p'])?$_GET['p']:'';
$item=isset($_GET['i'])?$_GET['i']:'';
$do=isset($_GET['do'])?$_GET['do']:0;
$page="Accounting";
$str='';
ob_start();
include('../head.php');
if(!ISIN_ADMIN) {
	$str.=$mainlogo;
}elseif($content!='') {
	include("index_$content.php");
	$str.=$backbtn;
}else{
	$str.='<ul class="home">';
	$str.='<li><a href="?p=referral&do=0">BB Bonus</a></li>';
	$str.='<li><a href="?p=smps50&do='.WEEK.WKYR.'">50% SMPS Qualifiers</a></li>';
	$str.=DIV_CLEAR.'</ul>';

	$str.='<ul class="home">';
	$str.='<li><a href="../data/getdistrilist/index.php?tin=1&addr=1">EWT</a></li>';
     $str.='<li><a href="'.DLC_ROOT.'/distrilog/recap.php?w=&u=">Recap</a></li>';
	$str.=DIV_CLEAR.'</ul>';
}echo $str;
require('../foot.php');
ob_end_flush();
?>
