<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

if(isset($_POST['submit'])&&$_POST['submit']=='pabili') {
	$id=isset($_POST['id'])?$_POST['id']:null;
	$qy=isset($_POST['qy'])?$_POST['qy']:0;
	$i=0;
	require_once('../../admin/setup.php');
	$con=SQLi('products');
	$qry="SELECT * FROM tbllist WHERE id=$id";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$cod=$rw['id'];
	$nam=$rw['name'];
	$pvv=$rw['pv'];
	$wsp=$rw['wsp'];
	mysqli_close($con);

	if(!isset($_SESSION['center_orders'])) $_SESSION['center_orders']=array();
	while($i<count($_SESSION['center_orders'])&&$_SESSION['center_orders'][$i][0]!=$id) $i++;
	if($i<count($_SESSION['center_orders'])) {
		if($_SESSION['center_orders'][$i][2]+$qy>=0) {
			$_SESSION['center_orders'][$i][2]=$qy;
			$_SESSION['center_orders'][$i][3]=$wsp;
			$_SESSION['center_orders'][$i][4]=$pvv;
		}
	}else $_SESSION['center_orders'][]=array($cod,$nam,$qy,$wsp,$pvv);
	if($_SESSION['center_orders'][$i][2]+$qy==0||$_SESSION['center_orders'][$i][2]+$qy=='') {
		unset($_SESSION['center_orders'][$i]);
		$_SESSION['center_orders']=array_values($_SESSION['center_orders']);
	}
}

if(isset($_POST['clear'])&&$_POST['clear']) {
	unset($_SESSION['pcm_edit_orders']);
	unset($_SESSION['center_orders_data']);
	unset($_SESSION['center_orders']);
	unset($_SESSION['pcm_edit']);
	unset($_SESSION['for_edit']);
}

if(isset($_POST['paydata'])&&$_POST['paydata'])	$_SESSION['pcm_paydata'];
include 'updateorders.php';
?>
