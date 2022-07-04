<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

require('../../admin/setup.php');
require('../func.php');

if(!ISIN_PCM) {
	reloadTo(DLC_PCRT);
	exit;
}

$_SESSION['pcm_last'] = DLC_PCRT.'/stocks';
$_SESSION['lastpage'] = '/pcm/stocks';
$title   = 'PCM | Stocks';
$content = '';
$page    = isset($_GET['p'])?$_GET['p']:'';

ob_start();
include('../head.php');

if(LOGIN_TYPE=='pcm') {
	switch ($page) {
		case 'stocks':
			echo loadStocks();
			break;

		case 'transfers':
			echo loadTransfers();
			break;

		default:
			echo '<ul class="home">';
			echo '<li><a href="?p=stocks">STOCKS</a></li>';
			echo '<li><a href="?p=transfers">TRANSFERS</a></li>';
			echo DIV_CLEAR . '</ul>';
	}
}

include('../foot.php');
ob_end_flush();

function loadStocks() {
	$brn = 'w'.LOGIN_BRANCH;
	$con = SQLi('products');
	$qry = "SELECT id,$brn FROM tblstocks WHERE $brn!=0 ORDER BY id";
	$rs  = $con->query($qry) or die(mysqli_error($con));

	$msg  = '<div><br>Found <strong class="blue">'.mysqli_num_rows($rs).'</strong> items</div>';
	$msg .= '<ul id="remit" class="list clear">';
	$msg .= '<li class="hdr"><span class="s1">Item</span><span class="s7">Description</span><span class="s1 rt">Qty</span></li>';

	while( $rw = $rs->fetch_array() ) {
		$id  = $rw['id'];
		$qty = $rw[$brn];
		$nam = getPName($id);

		$msg .= '<li rel="'.$id.'">';
		$msg .= '<span class="s1">'.$id.'</span>';
		$msg .= '<span class="s7">'.$nam.'</span>';
		$msg .= '<span class="s1 rt">'.$qty.'</span></li>';
	}

	mysqli_close($con);
	return $msg .= '</ul>';
}

function loadTransfers() {
	global $ynArr;
	$data = '<li><span class="s4">Transfer ID</span><span class="s2">Requester</span><span class="s2">From</span><span class="s2">To</span><span class="s1">Closed</span>';
	$con  = SQLi('products');
	$rs   = $con->query("SELECT * FROM tbllogtransfer WHERE whto='".LOGIN_BRANCH."' ORDER BY status,reqstamp") or die(mysqli_error($con));

	while ($rw = $rs->fetch_assoc()) {
		$statbad=!$rw['status'] ? 'bad' :'';
		$data .= '<li><a href="\admin\logistics\viewtransfer.php?id='.$rw['id'].'" class="s4">'.$rw['id'].'</a><span class="s2">'.$rw['reqid'].'</span><span class="s2">'.$rw['whfrom'].'</span><span class="s2">'.$rw['whto'].'</span><span class="s1 '.$statbad.'">'.$rw['status'].'</span>';
	}

	return '<form><ul id="transarc"><li class="hdr blue">Transfers</li>'.$data.'</ul></form>';
}
?>
