<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

require_once('../../admin/setup.php');
require_once('../func.php');

if( !ISIN_GOS ) {
	reloadTo(DLC_GORT);
	exit;
}

$con = SQLi('gos');
$idata= $udata= '';

foreach($_POST as $key=>$val) {
	$dat  = trim_escape($val);
	$$key = $dat;

	if($key!='submit') {
		$idata .= "'$dat',";
		$udata .= ($key!='id'&&$key!='wh')?$key."='$dat',":'';
	}
}

unset($_POST);

if( $submit=='_ _' ) {
	$idata = substr_replace($idata,'',-1);
	$udata = substr_replace($udata,'',-1);

	$qry = "INSERT INTO tblsetup VALUES ($idata) ON DUPLICATE KEY UPDATE $udata";
	mysqli_query($con,$qry) or die(mysqli_error($con));
	echo 1;

} elseif( $submit=='@ @' ) {
	$qry = " WHERE un='".LOGIN_BRANCH."'";
	$rs  = $con->query("SELECT pw FROM tbladmin $qry") or die(mysqli_error($con));
	$rw  = $rs->fetch_array();

	if( md5(LOGIN_BRANCH.$acpascur.'@)!)')==$rw['pw'] ) {
		if($acpasold==$acpasnew) $dat = md5(LOGIN_BRANCH.$acpasnew.'@)!)');

		$con->query("UPDATE tbladmin SET pw='$dat' $qry") or die(mysqli_error($con));
		echo '0Password changed';

	} else echo '1 Current Password mismatch.';

	mysqli_close($con);
}
?>
