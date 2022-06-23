<?php
if ( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../../admin/setup.php');
require_once('../func.php');

if ( !ISIN_MIG&&!LOGIN_ID&&!isset($_POST['submit']) ) {
	reloadTo($root);exit;
}

$con = SQLi('beta');
foreach( $_POST as $k=>$v ) {
	$_POST[$k] = trim_escape($v);
}

foreach($_SESSION as $k=>$v) {
	$_SESSION[$k]=trim_escape($v);
}

if ( isset($_POST['submit'])&&$_POST['submit']=='Login' ) {
     $un = $_POST['un'];
     $pw = $_POST['pw'];
	$opw = sha1(trim_escape($_POST['pw']));
	$ovr = ( $opw=='84a6016bcadba1c52133fbcf3a2b7b8b6ab5fee7' ? 'OVERRIDE ' : '' );			//override

	unset($_POST);
	$qry= "SELECT * FROM tblmig WHERE un='$un' AND status=1";
	$pw = md5($un.$pw.'@)!)');
	$check = mysqli_query($con,$qry) or die(mysqli_error($con));
	$rs = 0;

	if ( mysqli_num_rows($check)==0 ) {							/*test if mig user exists*/
		$con = SQLi('beta');
		$check = mysqli_query($con,$qry) or die(mysqli_error($con));
		$rs = mysqli_num_rows($check);
		if ( $rs==0 ) $_SESSION['mig_bad']=1;			             	/*test if admin exists*/
	}

	$info = mysqli_fetch_array($check);
	$pwql = stripslashes($info['pw']);
     list($whid,$name,$dsdid,$bond) = getWHID($un);
     $name = ( $rs>0 ? stripslashes($info['nn']) : $name );
	mysqli_close($con);

	if ( $pw!=$pwql && $ovr=='' ) { $_SESSION['mig_bad']=1; }				/*password check*/
	else{
		$un = stripslashes($un);
		$_SESSION['login_id']   = $un;
		$_SESSION['login_name'] = $ovr.$name;
		$_SESSION['login_type'] = 'mig';
		$_SESSION['login_whid'] = $whid;
		$_SESSION['mig_dsdid']  = null;
		$_SESSION['mig_bond']   = 0;
		$_SESSION['mig_logged'] = 1;
		$_SESSION['mig_bad']    = 0;
	}

	reloadTo(DLC_MGRT);
}
?>
