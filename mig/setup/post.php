<?php

if ( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
if ( !ISIN_MIG ) { reloadTo(DLC_MGRT);exit; }

$con = SQLi('beta');
$idata=$udata= '';

foreach ( $_POST as $k=>$v ) {
	$dat = trim_escape($v);
	$$k  = $dat;
	if ( $k != 'submit' ) {
		$idata .= "'$dat',";
		$udata .= ( $k!='id' && $k!='wh' ) ? $k."='$dat'," :'';
	}
} unset($_POST);

if ( $submit=='@ @' ) {
	$qry = " WHERE un='".LOGIN_BRANCH."'";
	$rs  = mysqli_query($con,"SELECT pw FROM tblmig $qry") or die(mysqli_error($con));
	$rw  = mysqli_fetch_array($rs);
	if ( md5( LOGIN_BRANCH.$acpascur.'@)!)' ) == $rw['pw'] ) {
		if ( $acpasold==$acpasnew ) $dat = md5(LOGIN_BRANCH.$acpasnew.'@)!)');
		mysqli_query($con,"UPDATE tblmig SET pw='$dat' $qry") or die(mysqli_error($con));
          echo '0Password changed';
	} else echo '1 Current Password mismatch.';
	mysqli_close();
}
?>
