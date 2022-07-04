<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if (!defined('INCLUDE_CHECK')) define('INCLUDE_CHECK',1);

if(isset($_GET['gcref'])) {
	require('../../admin/setup.php');
	printOrders($_GET['gcref']);
	unset($_GET);

	if($_SESSION['for_edit']) {
		echo 'Please wait while we prepare your orders.';
		reloadTo('../orders/');

	} else {
		echo 'Nice try but you\'re not going there...';
		reloadTo($_SESSION['gos_last']);
	}
}

function printOrders($ref) {
	$con = SQLi('gos');
	$rs = mysqli_query($con, "SELECT * FROM tblorders WHERE refNo='$ref'") or die(mysqli_error($con));
	$rw = mysqli_fetch_array($rs);
	$data = explode("~", ($rw['orders'] != '' ? $rw['orders'] : '||||'));

	foreach ($data as $val) {
		$idata[] = explode("|", $val);
	}

	$_SESSION['for_edit'] = ($rw['paystat'] == 0) ? true : false;
	$_SESSION['gos_edit_orders'] = array();
	$_SESSION['center_orders'] = array();
	$_SESSION['gos_edit'] = $ref;
	$_SESSION['center_orders_data'] = $rw['dsdid'] . '|' . $rw['dsnam'] . '|' . $rw['dscon'] . '|' . $rw['dstin'] . '|' . $rw['paycash'] . '|' . $rw['paycard'] . '|' . $rw['paychek'] . '|' . $rw['payfund'] . '|' . $rw['paydate'] . '|' . $rw['payconf'];

	$msg = '<li class="hdr"><span class="s1 lt">Code</span><span class="s4">Description</span><span class="s1 rt">Qty</span><span class="s2 rt">PV</span><span class="s2 rt">Amount</span></li>';
	$v = array();
	$shopppv = 0;
	$shopamt = 0;
	$tppv    = 0;
	$tamt    = 0;
	$limit   = 20;

	foreach($idata as $v) {
		$v1 = $v[0];
		$v2 = utf8_encode($v[1]);
		$v3 = $v[2];
		$v4 = $v[3];
		$v5 = $v[4];

		$shopppv = $v5 * $v3;
		$shopamt = $v4 * $v3;
		$tppv += $shopppv;
		$tamt += $shopamt;

		if(strlen($v2) > $limit) $v2 = substr($v2, 0, $limit);

		$_SESSION['center_orders'][] = array($v1, $v[1], $v3, $v4, $v5);
		$_SESSION['gos_edit_orders'][] = array($v1, $v3);

		$msg .= '<li>';
		$msg .= '<span class="s1 lt">' . $v1 . '</span>';
		$msg .= '<span class="s4">' . $v2 . '</span>';
		$msg .= '<span class="s1 rt">' . $v3 . '</span>';
		$msg .= '<span class="s2 rt">' . number_format($shopppv, 2) . '</span>';
		$msg .= '<span class="s2 rt">' . number_format($shopamt, 2) . '</span></li>';
	}

	return array($msg, $tppv, $tamt);
}
?>
