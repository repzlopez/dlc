<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if((!isset($_SESSION['u_site'])&&!isset($_SESSION['login_id']))){
	echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL=../../../">';exit;
}
$isadmin=isset($_SESSION['a_logged'])?1:0;
$dbsrc=$isadmin?'beta':'distributor';
require('../../admin/infoconfig.php');
$pw=stripslashes(trim($_POST['pw']));
if(isset($_POST['cancel'])&&$_POST['cancel']){
	$user=($isadmin)?$_SESSION['login_id']:$_SESSION['u_site'];
	$pass=($isadmin)?md5($user.$pw.'@)!)'):sha1(stripslashes($pw));
	$quer=($isadmin)?"SELECT pw FROM tbladmin WHERE un = '$user'":"SELECT password FROM accounts WHERE dsdid = '$user'";
	$rs=mysqli_query($con,$quer) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs) or die(mysqli_error($con));
	$psw=($isadmin)?$rw['pw']:$rw['password'];
	$passql=stripslashes($psw);
	echo ($pass==$passql);
}
mysqli_close($con);
unset($_POST);
?>
