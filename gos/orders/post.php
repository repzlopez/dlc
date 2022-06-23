<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
if(!ISIN_GOS) { reloadTo(DLC_GORT);exit; }
$isedit=isset($_SESSION['gos_edit'])?true:false;
$editrf=$isedit?$_SESSION['gos_edit']:null;
$useref=$isedit?$editrf:LOGIN_BRANCH.setRefNo();
$usedat=setDatePosted();
$action=$isedit?1:0;
$orders='';
$idata="'$useref','$usedat',";
$ilog="null,'$useref','".date(TMDSET,time())."','$action','".LOGIN_ID."'";
$udata="refdate='$usedat',";

$con=SQLi('gos');
if(isset($_POST)) {
	foreach($_POST as $key=>$val) {
		$dat=trim_escape($val);
		$$key=$dat;
		if($key=='paystat') {
			$idata.="$dat,";
			$udata.=$key."=$dat,";
		}elseif($key=='payconf') {
			$idata.=(trim($val)!='')?"'$dat',":"'',";
			$udata.=$key."='[$dat]',";
		}else{
			$idata.="'$dat',";
			$udata.=$key."='$dat',";
		}
	}
	$idata=substr_replace($idata,'',-6);
	$udata=substr_replace($udata,'',-13);
}
if(isset($_SESSION['center_orders'])) {
	foreach($_SESSION['center_orders'] as $v) {
		$v0=$v[0];
		$v1=$v[1];
		$v2=$v[2];
		$v3=$v[3];
		$v4=$v[4];
		$orders.="$v0|$v1|$v2|$v3|$v4~";
		updateStocks($v0,$v2,'-');
	}
}
if(isset($_SESSION['gos_edit_orders'])) {
	foreach($_SESSION['gos_edit_orders'] as $v) {
		updateStocks($v[0],$v[1],'+');
	}
}
if(isset($submit)&&$submit=='_ _') {
	$orders=substr_replace($orders,'',-1);
	$idata.="'','','$orders'";
	$udata.="orders='$orders'";
	$con=SQLi('gos');
	mysqli_query($con,"INSERT INTO tblorders VALUES ($idata) ON DUPLICATE KEY UPDATE $udata") or die(mysqli_error($con));
	mysqli_query($con,"INSERT INTO tbllog VALUES ($ilog)") or die(mysqli_error($con));
	mysqli_close($con);
	echo "$useref";
}
unset($_SESSION['gos_edit_orders']);
unset($_SESSION['center_orders_data']);
unset($_SESSION['center_orders']);
unset($_SESSION['gos_edit']);
unset($_SESSION['for_edit']);
unset($_POST);

function updateStocks($cod,$qty,$o) {
	$con=SQLi('products');
	$brn='w'.LOGIN_BRANCH;
	mysqli_query($con,"UPDATE tblstocks SET $brn=$brn $o $qty WHERE id='$cod'") or die(mysqli_error($con));
}
?>
