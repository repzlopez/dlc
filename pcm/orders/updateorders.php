<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(isset($_POST['submit'])&&$_POST['submit']=='pabili') echo buildOrders();
if(isset($_POST['totals'])&&$_POST['totals']) echo getTotals();

function buildOrders() {
	$msg='';
	if(isset($_SESSION['center_orders'])) {
		foreach($_SESSION['center_orders'] as $key=>$val) {
			$pid=$val[0];
			$msg.='<li rel="'.$pid.'">';
			$msg.='<span class="s1">'.$pid.'</span>';
			$msg.='<span class="s6">'.utf8_encode($val[1]).'</span>';
			$msg.='<span class="s2 rt">'.number_format((float)$val[3],2).'</span>';
			$msg.='<span class="s2 rt">'.number_format((float)$val[4],2).'</span>';
			$msg.='<span class="s1 rt">'.$val[2].'</span>';
			$msg.='<span class="s2 rt">'.number_format($val[2]*(float)$val[3],2).'</span>';
			$msg.='<span class="s2 rt">'.number_format($val[2]*(float)$val[4],2).'</span></li>';
		}
	}return $msg;
}

function getTotals() {
	$msg='';$d=0;$d1=0;$d2=0;
	if(isset($_SESSION['center_orders'])) {
		foreach($_SESSION['center_orders'] as $key=>$val) {
			$d1+=($val[2]*(float)$val[3]);
			$d2+=($val[2]*(float)$val[4]);
			$d++;
		}
	}return "$d1|$d2|$d";
}
?>
