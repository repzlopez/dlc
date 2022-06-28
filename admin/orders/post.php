<?php
if(!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
define('INCLUDE_CHECK',1);

include('../setup.php');
date_default_timezone_set('Asia/Manila');

$idata= $udata='';
$int  = "/ompv|ompov/i";
$ex   = "/do|oldpv|refno|submit/i";
// print_r($_POST);
// echo "<br><br>";

foreach($_POST as $k=>$v) {
	$$k = trim_escape("$v");
	$_SESSION['data'][$k] = $$k;

	if( !preg_match($ex,$k) ) {
		if( preg_match($int,$k) ) {
			$udata .= "$k=".$$k.",";
			$idata .= $$k.",";

		} else {
			$udata .= "$k='".$$k."',";
			$idata .= "'".$$k."',";
		}
	}
}

unset($_POST);
// $idata=substr($idata,0,-1);
// $udata=substr($udata,0,-1);

if( isset($submit) && $submit=='SUBMIT' ) {
	unset($_SESSION['data']);
	$con = SQLi('orders');
	include('updateBomstp.php');

	if(strpos(strtolower($ominv),'void')!==false) {
		$con->query("DELETE FROM ormstp WHERE ominv='$refno'");

	} else {
		$stamp = date(TMDSET,time());
		$con = SQLi('orders');
		$con->query("INSERT INTO ormstp VALUES ($idata '$stamp') ON DUPLICATE KEY UPDATE $udata stamp='$stamp'") or die(mysqli_error($con));

		testMinForSlot($omdid,$ompyr,$ompmo);

		if($do==2||($do==1&&strlen($refno)>10)) {
			$con = SQLi('gos');
			$ad  = (strlen($refno)>10 ? 'refno' : 'invoice');
			$con->query("UPDATE tblorders SET invoice='$ominv' WHERE $ad='$refno'");
		}

		if( $do==2 && $refno!=$ominv ) {}
		$_SESSION['errmsg'] = '<span class="good">Successfully submitted</span>';
	}

	if($do) recalc($do,$ompmo,$ompyr);

	reloadTo(DLC_ADMIN.'/orders/?p=ormstp&do=0');
}
?>
