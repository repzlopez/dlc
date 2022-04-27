<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../fetch.php');
$adminpage='orders';
testScope("global|".$adminpage,DLC_ADMIN);
$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
$_SESSION['lastURI']='mypage';
$_SESSION['a_page']=$adminpage;

$title='| '.strtoupper($adminpage);
$content=isset($_GET['p'])?$_GET['p']:'';
// $item=isset($_GET['i'])?$_GET['i']:'';
// $do=isset($_GET['do'])?$_GET['do']:0;
$page="Orders";

ob_start();
require('../head.php');
if(!ISIN_ADMIN){
	echo $mainlogo;
}elseif($content!=''){
	include("index_$content.php");
	echo $backbtn;
}else{
	if(!isset($_GET['get'])){
		echo '<ul class="home">';
		echo '<li><a href="?get=1">ONLINE ORDERING</a></li>';
		echo '<li><a href="?p=ormstp&do=0">REALTIME ORMSTP</a></li>';
		echo '<li><a href="?p=bomstp">REALTIME BOMSTP</a></li>';
		echo DIV_CLEAR.'</ul>';
		echo '<ul class="home">';
		echo '<li><a href="?p=newdiswik">NEW<br/>THIS WEEK</a></li>';
		echo '<li><a href="?p=gos">REALTIME<br/>GOS</a></li>';
		echo '<li><a href="?p=pcm">REALTIME<br/>PCM</a></li>';
		echo DIV_CLEAR.'</ul>';
	}else{
		echo '<div id="orderhdr"><ul></ul></div>'.DIV_CLEAR;

		$get=isset($_GET['get'])?$_GET['get']:1;
		switch($get){
			case 0 :getOrders(0);break;
			case 4 :getOrders(4);break;
			case 3 :getOrders(3);break;
			case 2 :getOrders(2);break;
			default:getOrders(1);break;
		}
		echo $backbtn;
	}
}
require('../foot.php');
ob_end_flush();

function getOrders($stat){
	include_once('../../distrilog/cart/values.php');
	$dlv=$dlv2;
	echo '<div class="orders" rel='.$stat.'><div class="blue">'.array_search($stat,$dlv).'</div>';
	echo '<ul>';
	include('../updateorders.php');
	echo '</ul></div>';
}
?>
