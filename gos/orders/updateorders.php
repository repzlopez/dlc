<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

if(isset($_POST['submit']) && $_POST['submit'] == 'pabili') echo buildOrders();
if(isset($_POST['totals']) && $_POST['totals']) echo getTotals();

function buildOrders() {
	$msg = '';
	if(isset($_SESSION['center_orders'])) {
		foreach ($_SESSION['center_orders'] as $k => $v) {
			$pid = $v[0];
			$msg .= '<li rel="' . $pid . '">';
			$msg .= '<span class="s1">' . $pid . '</span>';
			$msg .= '<span class="s6">' . utf8_encode($v[1]) . '</span>';
			$msg .= '<span class="s2 rt">' . number_format((float)$v[3], 2) . '</span>';
			$msg .= '<span class="s2 rt">' . number_format((float)$v[4], 2) . '</span>';
			$msg .= '<span class="s1 rt">' . $v[2] . '</span>';
			$msg .= '<span class="s2 rt">' . number_format($v[2] * (float)$v[3], 2) . '</span>';
			$msg .= '<span class="s2 rt">' . number_format($v[2] * (float)$v[4], 2) . '</span></li>';
		}
	}

	return $msg;
}

function getTotals() {
	$d= $d1= $d2 = 0;

	if(isset($_SESSION['center_orders'])) {
		foreach($_SESSION['center_orders'] as $k => $v) {
			$d1 += ($v[2] * (float)$v[3]);
			$d2 += ($v[2] * (float)$v[4]);
			$d++;
		}
	}

	return "$d1|$d2|$d";
}
?>
