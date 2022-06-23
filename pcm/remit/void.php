<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
testScope("global|pcm",DLC_PCRT);
echo 'Please wait while we void this transaction. Thank you.';
$con=SQLi('pcm');
$trans=$_SESSION['pcm_edit'];
$brn=substr($trans,0,5);
mysqli_query($con,"UPDATE tblorders SET status=9 WHERE refno='$trans'");
mysqli_query($con,"INSERT INTO tbllog VALUES (null,'$trans','".date(TMDSET,time())."',9,'".LOGIN_ID."')");
if(isset($_SESSION['pcm_edit_orders'])) {
	foreach($_SESSION['pcm_edit_orders'] as $v) {
		updateStocks($brn,$v[0],$v[1],'+');
	}
}
unset($_SESSION['pcm_edit_orders']);
unset($_SESSION['center_orders_data']);
unset($_SESSION['center_orders']);
unset($_SESSION['pcm_edit']);
unset($_SESSION['for_edit']);
unset($_POST);
reloadTo('/pcm/remit/');

function updateStocks($brn,$cod,$qty,$o) {
	mysqli_query(SQLi('products'),"UPDATE tblstocks SET w$brn=(w$brn+$qty) WHERE id='$cod'");
}
?>
