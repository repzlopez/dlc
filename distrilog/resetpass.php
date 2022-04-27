<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');

global $resetstatus;
foreach($_POST as $k=>$v) $$k=$v;
unset($_POST);

if(isset($submit)){
	$con=SQLi('distributor');
	$id=trim_escape($id);
	$rs=mysqli_query($con,"SELECT dsdid FROM accounts WHERE dsdid='$id' AND reset_pass_key='$key'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0){
		mysqli_query($con,"DELETE FROM accounts WHERE dsdid='$id'");
		$_SESSION['resetstatus']='Password successfully reset.';
	}else{
		$_SESSION['resetstatus']='Unable to continue. Invalid credentials.';
	}
	mysqli_close($con);
	reloadTo('/read/'.$shortlink.'at-your-service/forgot-password/');
}
?>
