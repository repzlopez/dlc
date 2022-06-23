<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../fetch.php');
testScope("global|distri",DLC_ADMIN);
$adminpage='distriserve';
$_SESSION['lastpage']=$_SERVER['PHP_SELF'];
$_SESSION['lastURI']=$adminpage;
$_SESSION['a_page']=$adminpage;
$title='| '.strtoupper($adminpage);
$content=isset($_GET['p'])?$_GET['p']:'';
$item=isset($_GET['i'])?$_GET['i']:'';
$do=isset($_GET['do'])?$_GET['do']:0;
$page='Distributor Service';

ob_start();
include('../../head.php');
if(!ISIN_ADMIN) {
	echo $mainlogo;
}elseif($content!='') {
	$updating=getStatus('996')?'UPDATE IN PROGRESS. ':'';
	include("index_$content.php");
}else{ echo '<ul class="home">';
	$_SESSION['lastpage']='../';
	echo '<li><a href="?p=step1">STEP 1 UPDATE DATABASE</a></li>';
	echo '<li><a href="?p=step2">STEP 2 UPDATE DISTRIBUTORS</a></li>';
	echo DIV_CLEAR.'</ul>';
	echo $backbtn;
	unset($_SESSION['my']);
}
require('../../foot.php');
ob_end_flush();

function getLastUpdate() {
	$con=SQLi('distributor');
	$rs=mysqli_query($con,"SELECT date_updated FROM updates ORDER BY date_updated DESC LIMIT 1") or die(mysqli_error($con));
	$rw=mysqli_fetch_assoc($rs);
	return 'Last updated <span class="more">'.date('F d, Y h.ia',strtotime($rw['date_updated'])).'</span>';
	mysqli_close($con);
}
?>
