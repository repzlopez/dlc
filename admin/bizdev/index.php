<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../fetch.php');
$adminpage='bizdev';
testScope("global|".$adminpage,DLC_ADMIN);
$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
$_SESSION['lastURI']=$adminpage;
$_SESSION['a_page']=$adminpage;
$title='| '.strtoupper($adminpage);
$content=isset($_GET['p'])?$_GET['p']:'';
$item=isset($_GET['i'])?$_GET['i']:'';
$do=isset($_GET['do'])?$_GET['do']:0;
$page='Business Development';
$str='';
ob_start();
include('../head.php');
if(!ISIN_ADMIN){
	$str.=$mainlogo;
}elseif($content!=''){
	include("index_$content.php");
	$str.=$backbtn;
}else{ $str.='<ul class="home">';
	$str.='<li><a href="?p=activities&do=0">ACTIVITIES</a></li>';
	$str.='<li><a href="?p=downloads&do=0">DOWNLOADS</a></li>';
	$str.='<li><a href="?p=calendar&do=0">CALENDAR</a></li>';
	$str.='<li><a href="?p=locations&do=0">LOCATIONS</a></li>';
	$str.=DIV_CLEAR.'</ul>';
}echo $str;
require('../foot.php');
ob_end_flush();
?>
