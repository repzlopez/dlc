<?php
if ( !isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
include '../../func.php';

$lc  = isset($_POST['loc']) ? $_POST['loc'] :null;
$id  = isset($_POST['pid']) ? $_POST['pid'] :null;
$qy  = isset($_POST['qty']) ? $_POST['qty'] :null;
$cod = $id;
$i   = 0;

if (isset($_POST['olg']) ) $_SESSION['olreg_ref'] = $_POST['olg'];

if ( isset($id) && strlen($id)==5 ) {
	$dbsrc = 'products';
	require('../../admin/infoconfig.php');
	$qry = "
		SELECT name,pv,wsp,srp,wt,cat,
			(SELECT k.status FROM tblpackages k WHERE k.id='$id') isPack
		FROM tbllist l
		LEFT JOIN tblproducts p
			ON p.id=l.id
		WHERE l.id='$id'
	";

	$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw = mysqli_fetch_array($rs);
	foreach ( $rw as $k=>$v ) $$k = $v;

	$pvv = ($cat=='productaids') ? 0 : $pv;
	$wsp = ($cat=='productaids'||GUEST||OLREG_REF===false) ? $srp : $wsp;//(isset($isPack)&&$isPack==1)||
	mysqli_close($con);

	if ( !isset($_SESSION['shoplist']) ) $_SESSION['shoplist'] = array();
	while ( $i<count($_SESSION['shoplist']) && $_SESSION['shoplist'][$i][0]!=$id ) $i++;
	if ( $i<count($_SESSION['shoplist']) ) {
		if ( $_SESSION['shoplist'][$i][3]+$qy >= 0 ) {
			$_SESSION['shoplist'][$i][3] += $qy;
			$_SESSION['shoplist'][$i][4]  = $pvv;
			$_SESSION['shoplist'][$i][5]  = $wsp;
			$_SESSION['shoplist'][$i][7]  = $wt;
			$_SESSION['shoplist'][$i][8]  = $srp;
		}
	} else $_SESSION['shoplist'][] = array($id,$cod,$name,$qy,$pvv,$wsp,$lc,$wt,$srp);
	// if(OLREG_REF==OLREG_Choice100) $_SESSION['shoplist'][$i++]=array('','','Processing Fee',0,0,PROC_FEE,'',0,0);
}
if ( isset($_POST['clear'])&&$_POST['clear'] ) unset($_SESSION['shoplist']);
if ( isset($_SESSION['shoplist']) ) {
	// include '../../func.php';
	getShoplist($_SESSION['shoplist']);
}
?>
