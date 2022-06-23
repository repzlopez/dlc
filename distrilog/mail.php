<?php if(!isset($_SESSION)) session_start();
define('INCLUDE_CHECK',1);
include('../func.php');
if(!ISIN_DISTRI&&!isset($_SESSION['u_site'])&&!isset($_POST['submit'])) echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL=../">';
$domn='dlcorp.com';
$recv='phiorders';
$subj='DLC ORDER';
$dist=$_SESSION['u_site'];
$name=$_SESSION['u_name'];
$mess=$_POST['msg'];

ob_start();
if($name!='' And $mess!='') {
	$final="From: $name \n\n$mess";
//	mail("$recv@$domn", 'ORDERS', $final, "From: $addr");
	echo '<div id="head"><img src="src/logo.png" id="logo" alt="" /><span id="nii"></span><span id="sfn" alt=""></span></div>';
	echo '<div id="body" style="width:700px"><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p class="dlink center">THANK YOU FOR YOUR E-MAIL.<br />WE WILL GET BACK WITH YOU SHORTLY.</p>';
	echo '<div style="top:500px;position:absolute"><a href="index.html" class="lastedit blink">HOME</a></div>';
}else{
	include('../head.php');
	echo '<div><p class="c">You must fill-in all the required fields please go<br /><a href="#">BACK</a></p>';
}
ob_end_flush();
?>