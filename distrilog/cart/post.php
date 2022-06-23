<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../admin/setup.php');
if( !GUEST && !ISIN_DISTRI && !DIST_ID && !isset($_POST['submit']) ) {
	reloadTo(DLC_ROOT);exit;
}

$action	= '';
$newref	= setRefNo();
$idata	= "'".$newref."','".setDatePosted()."','".(GUEST?GUEST:(DIST_ID?DIST_ID:''))."','".(GUEST?GUEST:(DIST_ID?DIST_ID:''))."',";
foreach( $_POST as $k=>$v ) {
	$$k = $v;
	if( $k=='payAmt' ) $v = str_replace(',','',$v);
	if( $k=='payDate' ) $v = str_replace('.','',$v);
	if( $k!='submit' ) $idata .= "'".addslashes(trim_escape($v))."',";
}

$orders  = '';
$shopppv = 0;
$idata   = substr_replace($idata,'',-1);

if ( isset($_SESSION['shoplist']) ) {
	foreach( $_SESSION['shoplist'] as $v ) {
		$v0 = $v[0];
		$v1 = $v[1];
		$v2 = $v[2];
		$v3 = $v[3];
		$v4 = $v[4];
		$v5 = $v[5];
		$v6 = $v[6];
		$v7 = $v[7];
		$orders  .= "$v0|$v1|$v2|$v3|$v4|$v5|$v7~";
		$shopppv += $v4*$v3;
	} $_SESSION['guestpv'] = $shopppv;
}

$orders = substr_replace($orders,'',-1);
$idata .= ",'$orders'";

if( isset($_POST['cancel'])&&$_POST['cancel'] ) {
	$con = SQLi('orders');
	$id  = $_POST['refNo'];

	mysqli_query($con,"UPDATE tblorders SET deliStat=0 WHERE refNo='$id'");
	$user = (isset($_SESSION['login_id'])) ? $_SESSION['login_id'] : $_SESSION['u_site'];

	setLog($id,$user,'0');
	echo 'Order successfully cancelled!';
}

switch($submit) {
     case '|||':
          $con = SQLi('orders');
     	$udata = '';
     	$user  = (isset($_SESSION['login_id']))?$_SESSION['login_id']:$_SESSION['u_site'];

     	foreach( $_POST as $k=>$v ) {
     		$udata .= $k."='".trim_escape($v)."',";
     	}

          $id  = $_POST['refNo'];
     	$udata = substr_replace($udata,'',-28);

		mysqli_query($con,"UPDATE tblorders SET $udata WHERE refNo='$id'");
		setLog($id,$user,'5');
		echo 'Order successfully updated!';
		break;

     case '_ _':
     case 'Checkout':
		$con = SQLi('orders');
		mysqli_query($con,"INSERT INTO tblorders VALUES ($idata)");
		setLog($newref,(GUEST?GUEST:$_SESSION['u_site']),'1');
		echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL=../mycart.php">';
		break;

     default:
          break;
}

//mysqli_close($con);
unset($_SESSION['shoplist']);
unset($_POST);

function setLog($refno,$user,$action) {
	date_default_timezone_set('Asia/Manila');
	$time = date(TMDSET,time());
	$con  = SQLi('orders');
	mysqli_query($con,"INSERT INTO tbllog VALUES ('$refno','$time','$user','$action')");
}

function setRefNo() {
	$con  = SQLi('orders');
	$date = setDatePosted();

	$rs  = mysqli_query($con,"SELECT * FROM tblorders WHERE refDate = $date") or die(mysqli_error($con));
	$num = mysqli_num_rows($rs);
	return '1-'.$date.sprintf("%04d",$num++);
}

function setDatePosted() {
	date_default_timezone_set('Asia/Manila');
	$date = date('Ymd',time());
	return $date;
}

?>
