<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../fetch.php');
$adminpage='data';
testScope("global|".$adminpage,DLC_ADMIN);
$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
$_SESSION['lastURI']=$adminpage;
$_SESSION['a_page']=$adminpage;
$title='| '.strtoupper($adminpage);
$page='Data';
$str='';
ob_start();
require('../head.php');
if(isset($_GET['dlog'])) {
	$sort=isset($_GET['sort'])?$_GET['sort']:0;
	$str.=listDownloads($sort);
}else{$x='';
	foreach($arrGets as $key=>$value) {
		$x.='<li><a href="'.$key.'">'.$value.'</a></li>';
	}$m=buildKeys($x);
	$x='';
	foreach($arrSets as $key=>$value) {
		$x.='<li><a href="'.$key.'">'.$value.'</a></li>';
	}$n=buildKeys($x);
	$str.=$m.$n;
}echo $str;
require('../foot.php');
ob_end_flush();

function buildKeys($m) {
	return '<ul class="home">'.$m.DIV_CLEAR.'</ul>';
}

function listDownloads($sort) {
	switch($sort) {
		case 1:$sortorder='dsdid';break;
		case 2:$sortorder='dlid';break;
		default:$sortorder='stamp DESC';
	}
	$str='<ul id="dllog">';
	$con=SQLi('beta');
	$rs=mysqli_query($con,"SELECT * FROM tbllogdl ORDER BY $sortorder") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		$str.='<li><span class="s4">'.$rw['dsdid'].'</span><span class="s4 ct">'.$rw['ip'].'</span><span class="s6">'.$rw['dlid'].'</span><span class="s4 rt">'.$rw['stamp'].'</span></li>';
	}mysqli_close($con);
	return $str.'</ul>';
}
?>
