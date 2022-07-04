<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

if( ($_POST['id']!='null') && $_POST['submit']=='pabili' ) {
	$id = isset($_POST['id'])?$_POST['id']:null;
	$qy = isset($_POST['qy'])?$_POST['qy']:0;
	$i  = 0;

	require_once('../../admin/setup.php');

	$order_type = isset($_SESSION['order_type']) ? $_SESSION['order_type'] : '';

	switch ($order_type) {
		case '':
		case 'distributor':
			$use_price = "wsp"; break;

		case 'preferred':
			$use_price = "pmp"; break;

		case 'customer':
			$use_price = "srp"; break;
	}

	$con = SQLi('products');
	$qry = "SELECT * FROM tbllist WHERE id=$id";
	$rs  = $con->query($qry);
	$r   = $rs->fetch_assoc();

	$cod = $r['id'];
	$nam = $r['name'];
	$pvv = $r['pv'];
	$prc = $r[$use_price];

	mysqli_close($con);

	if( !isset($_SESSION['center_orders']) ) $_SESSION['center_orders']=array();

	while( $i<count($_SESSION['center_orders']) && $_SESSION['center_orders'][$i][0]!=$id ) $i++;

	if( $i<count($_SESSION['center_orders']) ) {
		if( $_SESSION['center_orders'][$i][2] + $qy >= 0 ) {
			$_SESSION['center_orders'][$i][2] = $qy;
			$_SESSION['center_orders'][$i][3] = $prc;
			$_SESSION['center_orders'][$i][4] = $pvv;
		}

	} else $_SESSION['center_orders'][] = array($cod,$nam,$qy,$prc,$pvv);

	if( $_SESSION['center_orders'][$i][2] + $qy==0 || $_SESSION['center_orders'][$i][2] + $qy=='' ) {
		unset($_SESSION['center_orders'][$i]);
		$_SESSION['center_orders'] = array_values($_SESSION['center_orders']);
	}
}

if(isset($_POST['clear'])&&$_POST['clear']) {
	unset($_SESSION['gos_edit_orders']);
	unset($_SESSION['center_orders_data']);
	unset($_SESSION['center_orders']);
	unset($_SESSION['gos_edit']);
	unset($_SESSION['for_edit']);
}

if( isset($_POST['paydata']) && $_POST['paydata'] )	$_SESSION['gos_paydata'];

include 'updateorders.php';
?>
