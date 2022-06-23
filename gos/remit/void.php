<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
testScope("global|gos",DLC_GORT);
echo 'Please wait while we void this transaction. Thank you.';
$trans=$_SESSION['gos_edit'];
$brn=substr($trans,0,5);
$con=SQLi('gos');
mysqli_query($con,"UPDATE tblorders SET status=9 WHERE refno='$trans'") or die(mysqli_error($con));
mysqli_query($con,"INSERT INTO tbllog VALUES (null,'$trans','".date(TMDSET,time())."',9,'".LOGIN_ID."')") or die(mysqli_error($con));
if(isset($_SESSION['gos_edit_orders'])) {
	foreach($_SESSION['gos_edit_orders'] as $v) {
		updateStocks($brn,$v[0],$v[1],'+');
	}
}
unset($_SESSION['gos_edit_orders']);
unset($_SESSION['center_orders_data']);
unset($_SESSION['center_orders']);
unset($_SESSION['gos_edit']);
unset($_SESSION['for_edit']);
unset($_POST);
reloadTo('/gos/remit/');

function updateStocks($brn,$cod,$qty,$o) {
	$con=SQLi('products');
	mysqli_query($con,"UPDATE tblstocks SET w$brn=(w$brn+$qty) WHERE id='$cod'") or die(mysqli_error($con));
}
?>
