<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('setup.php');
$isadmin=(strpos($_SERVER['PHP_SELF'],'admin')>0)?true:false;
if(!$isadmin&&!isset($_SESSION['maintenance'])) {
	reloadTo(DLC_ROOT.'/errors/404.php');exit;
}
//passreset
if(isset($_POST['submit'])&&$_POST['submit']=='.|.') {
	$con=SQLi('distributor');
	$id=trim_escape($_POST['id']);
	if($id=='') { echo '';exit; }

	$rs=mysqli_query($con,"SELECT dsdid FROM accounts WHERE dsdid='$id'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		echo getName($id,'lfm');
	}else{
		$rs1=mysqli_query($con,"SELECT dsdid,dssid,dsbrth FROM distributors WHERE dsdid='$id'") or die(mysqli_error($con));
		if(mysqli_num_rows($rs1)>0) {
			$rw=mysqli_fetch_array($rs1);
			echo getName($id,'lfm').' ( Distributor not logged-in ) [ Default: '.$rw['dsbrth'].' ]|'.$rw['dssid'];
		}
	}
	mysqli_close($con);
}
?>
