<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

if ( ISIN_ADMIN&&!ISIN_MIG ) {
	$_SESSION['mig_logged'] = 1;
	$_SESSION['mig_bad']    = 0;
	$_SESSION['a_logged']   = 0;
	reloadTo(DLC_MGRT);
}

function getWHID($user) {
	$con = SQLi('products');
	$id  = null;$wh=null;$ds=null;$bn=null;
	$rs  = mysqli_query($con,"SELECT id,wh,dsdid,bond FROM tblwarehouse WHERE id='$user' AND status=1") or die(mysqli_error());
	if ( mysqli_num_rows($rs)>0 ) { $rw=mysqli_fetch_array($rs);$id=$rw['id'];$wh=$rw['wh'];$ds=$rw['dsdid'];$bn=$rw['bond'];}
	return array($id,$wh,$ds,$bn);
}
?>
