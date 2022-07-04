<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
define('SUBSIDY',5);

require('../../admin/setup.php');
require('../func.php');

if (!ISIN_GOS) {
	reloadTo(DLC_GORT);
	exit;
}

$_SESSION['gos_last']  = DLC_GORT;
$_SESSION['editorder'] = '';
$title   = 'GOS | Remittance';
$content = 'remit';

$p0 = "AND paystat='0' ";
$p1 = "AND paystat='1' ";
$s1 = "AND status<9 ";
$s9 = "AND status=9 ";

$tab = array('unpaid' => $p0 . $s1, 'replenish' => '', 'paid' => $p1 . $s1, 'remittance' => '', 'void' => $s9);
$arrRemit = array('Cash', 'Cheque', 'Card', 'Fund Transfer'); //
$hilite = isset($_GET['id']) ? test_input($_GET['id']) : null;
$sum    = isset($_GET['sum']) ? test_input($_GET['sum']) : null;
$monyr  = isset($_GET['monyr']) ? test_input($_GET['monyr']) : date('Ym');

ob_start();
include('../head.php');
loadRemit($monyr);
unset($_GET);
include('../foot.php');
ob_end_flush();

function loadRemit($monyr) {
	global $tab,$sum;$s='';

	include('printorders.php');

	$tabid = isset($_GET['tab']) ? $_GET['tab'] : 'unpaid';
	$do    = isset($_GET['do']) ? $_GET['do'] : '';
	$item  = isset($_GET['i']) ? $_GET['i'] : '';
	$prt   = isset($_GET['prt']) ? $_GET['prt'] : 0;
	$tbl   = 'tblorders';
	$con   = SQLi('gos');

	if( $do==0 ) {
		if( $tabid=='remittance' && isset($sum) ) echo '<div><a href="javascript:window.print()" class="back" id="download">Print</a></div>';

		echo '<ul id="tab" class="list">';

		foreach($tab as $t=>$k) {
			$num = getNumbers($t);
			echo '<a href="?tab=' . $t . '"' . ($t == $tabid ? ' class="current"' : '') . '>' . strtoupper($t) . ($num > 0 ? ' (' . $num . ')' : '') . '</a>';
		}

		echo '</ul>';
		echo '<ul class="list' . ($sum ? ' gcsum' : '') . '"><li class="' . ($sum ? ' gcsum' : 'hdr') . '">';

		switch($tabid) {
			case 'replenish':
				echo '<span class="s3">Item Code</span>';
				echo '<span class="s5">Item Name</span>';
				echo '<span class="s3 rt">Remaining</span>';
				echo '<span class="s3 rt">Requested</span>';
				break;

			default:
				if(isset($sum)) {
				} else {
					echo '<span class="s4"><input type="checkbox" class="chkbox chkall" />Transaction #</span>';
					echo '<span class="s3 ct">' . (IS_GOS ? 'Confirmation' : 'Date Paid') . '</span>';
					echo '<span class="s3 rt">' . ($tabid == 'remittance' ? 'Payment Type' : 'Distributor ID') . '</span>';
					echo '<span class="s3 rt">' . ($tabid == 'remittance' ? 'Paid' : 'Order') . ' Amount</span>';
					echo '<span class="s3 rt">Remittance</span>';
					echo '<span class="s2 rt">Invoice</span>';
				}

				$s = '<li><span class="rt">' . dropDate(substr($monyr, -2), 'mo') . ' ' . dropDate(substr($monyr, 0, 4), 'yr') . ' <input class="monyr download" type="button" value="ALL" /></span></li>';
		}

		echo "</li>$s</ul>";

		switch($tabid) {
			case 'remittance':
				echo listRemit($monyr);
				break;

			case 'replenish':
				echo listReplenish();
				break;

			default:
				echo listTrans($tabid,$monyr);
		}

	} else {
		$_SESSION['gos_last'] = htmlspecialchars($_SERVER['HTTP_REFERER']);

		echo '<div><a href="javascript:window.print()" class="back" id="download">Print</a></div>';

		if( $do==1 ) {
			$con = SQLi('gos');
			$rs  = $con->query("SELECT * FROM $tbl WHERE refno='$item'") or die(mysqli_error($con));

			if($rs->num_rows>0) {
				$rw  = $rs->fetch_array();
				$did = $rw['dsdid'];
				$nam = $rw['dsnam'];
				$tin = $rw['dstin'];
				$cas = $rw['paycash'];
				$chq = $rw['paychek'];
				$car = $rw['paycard'];
				$fnd = $rw['payfund'];
				$unp = (!$rw['paystat'] && $rw['status'] < 9);
				$brn = substr($item, 0, 5);
				$det = $rw['refdate'];
				$det = substr($det, 4, 2) . '/' . substr($det, 6, 2) . '/' . substr($det, 0, 4);

				list($orders, $shpv, $sham) = printOrders($item);

				echo '<ul id="viewremit">';
				echo '<li><span class="s1 lt">Dist#:</span><span class="s5">'.$did.'</span> <span class="s1 lt">Date:</span><span class="s3">'.$det.'</span></li>';
				echo '<li><span class="s1 lt">Name:</span><span class="s5">'.$nam.'</span> <span class="s1 lt">TIN:</span><span class="s3">'.$tin.'</span></li>';
				echo '<li><span class="s1 lt">GOS:</span><span class="s5">'.$brn.'</span> <span class="s1 lt">Trans:</span><span class="s3">'.$item.'</span></li>';
				echo '<li class="ct printonly"><strong>PROVISIONAL RECEIPT</strong></li>';
				echo $orders;
				echo '<br /><li><span class="s1"><strong>TOTAL</strong></span><span class="s4"></span><span class="s1"></span>';
				echo '<span class="s2 rt">'.number_format($shpv,2).'</span>';
				echo '<span class="s2 rt">'.number_format($sham,2).'</span></li>';

				if( IS_GOS ) {
					echo '<li><br /><span class="blue">BREAKDOWN:</span><br />';
					echo '<span class="s1"></span><strong class="s4 rt">Cash:</strong><span class="s1"></span><span class="s2"></span><span class="s2 rt blue">'.number_format((float)$cas,2).'</span><br />';
					echo '<span class="s1"></span><strong class="s4 rt">Check:</strong><span class="s1"></span><span class="s2"></span><span class="s2 rt blue">'.number_format((float)$chq,2).'</span><br />';
					echo '<span class="s1"></span><strong class="s4 rt">Card:</strong><span class="s1"></span><span class="s2"></span><span class="s2 rt blue">'.number_format((float)$car,2).'</span><br />';
					echo '<span class="s1"></span><strong class="s4 rt">Fund Transfer:</strong><span class="s1"></span><span class="s2"></span><span class="s2 rt blue">'.number_format((float)$fnd,2).'</span></li>';
				}

				echo '</ul>'.( IS_GOS && $unp ? '<a href="void.php" class="back voidorder" id="download">VOID ORDER</a>' :'');

				$src = DLC_GORT.'/remit/scan/'.$item.'.jpg';
				$_SESSION['editorder'] = ( !IS_GOS && !$rw['paystat'] && !$rw['replen'] && !$rw['status'] ) ? '<a href="printorders.php?gcref='.$item.'" class="back edit_order" id="download">EDIT ORDER</a>' :'';
			}

			if( $prt ) {
				unset($_SESSION['pcm_edit_orders']);
				unset($_SESSION['center_orders_data']);
				unset($_SESSION['center_orders']);
				unset($_SESSION['pcm_edit']);
				unset($_SESSION['for_edit']);
			}

		}

		if( $do!=0 ) echo '<input type="hidden" name="do" value="'.$do.','.$item.'" />';
	}

	mysqli_close($con);
}

function listTrans($id,$monyr) {
	global $arrRemit, $tab;
	$b= $bid= $c= $cid= $old= $new= $fil= $total= '';
	$ttsham = 0;

	$fil = $tab[$id];
	$notglob = ''; //(IS_GOS?"AND remitid<>''":'');

	if($id=='unpaid') { $b='PAYMENT';$bid='paym';$c='CONFIRM PAYMENT';$cid='conf'; }

	$qry = "SELECT * FROM tblorders WHERE refno LIKE '" . LOGIN_BRANCH . "%$monyr%' $fil $notglob ORDER BY refno,refdate";
	$con = SQLi('gos');
	$rs  = $con->query($qry) or die(mysqli_error($con));
	$num = $rs->num_rows;
	$msg = '<ul class="list">';

	while( $rw=$rs->fetch_assoc() ) {
		$dsid = $rw['dsdid'];
		$rfno = $rw['refno'];
		$conf = $rw['payconf'];
		$remt = ($rw['remitid']!='');
		$det  = formatDate($rw['paydate']);

		list($orders, $shpv, $sham) = printOrders($rfno);

		$ttsham += $sham;
		$chck    = '<input type="checkbox" class="chkbox" rel="' . $rfno . '~' . $sham . '" name="gc_batch" />';
		$remitid = '<a href="' . $_SERVER['PHP_SELF'] . '?tab=remittance&id=' . $rw['remitid'] . '">' . substr($rw['remitid'], 5, strlen($rw['remitid'])) . '</a>';
		$total   = '<li><span class="s4"></span><span class="s3"></span><span class="s3 blue ct">TOTAL</span><span class="s3 rt">' . number_format($ttsham, 2) . '</span><span class="s3"></span></li>';
		$new     = substr($rfno, 0, 5);

		if( $old==$new ) {
		} else {
			$msg .= ( $id=='unpaid' && $old!='' ) ? $total :'';
			if( IS_GOS ) {
				$msg .= '<li class="nobg"></li><li class="nobg"><span class="blue s6">GOS # '.$new.'</span></li>';
			}

			if( $old!='' ) { $ttsham = 0; }
		}

		$old  = $new;

		$msg .= '<li rel="' . $rfno . '">';
		$msg .= '<span class="s4">' . (IS_GOS ? ($remt ? $chck : '') : ($remt ? ($id == 'paid' ? $chck : '') : $chck)) . '<a href="?p=remit&do=1&i=' . $rfno . '">' . $rfno . '</a></span>';
		$msg .= '<span class="s3 ct">' . (IS_GOS ? $conf : $det) . '</span>';
		$msg .= '<span class="s3 rt">' . $dsid . '</span>';
		$msg .= '<span class="s3 rt tdue">' . number_format($sham, 2) . '</span>';
		$msg .= '<span class="s3 rt">' . ($rw['status'] < 9 ? $remitid : '--- VOID ---') . '</span>';
		$msg .= '<span class="s2 rt">' . $rw['invoice'] . '</li>';
	}

	$msg .= ($id == 'unpaid') ? $total : '';
	$batbttn = ($id == 'unpaid' ? '<a href="#" class="back batclick" rel="' . (IS_GOS ? $cid : $bid) . '" id="download">BATCH ' . (IS_GOS ? $c : $b) . '</a>' : '');
	$addconf = IS_GOS && $id == 'unpaid' ? '<a href="#" class="back dobatch" id="batconf">CONFIRM</a>' : '';

	$msg .= '<li class="nobor">';
	$msg .= $batbttn . $addconf;
	$msg .= '</li></ul>';

	$paym = '<ul id="batpaym" rel=""><li> <span class="blue s3 lt">Total Due</span> <span id="tam" rel=0 class="s3 rt">0.00</span> <span class="s2"></span><span class="s3">Date Remitted</span> <input type="text" class="txt s2 rt" id="datePicker" name="gc_date" /></li>';

	$paym .= '<li class="multi">';
	$paym .= '<select class="s3">' . generate_options($arrRemit, 'Cash') . '</select>';
	$paym .= '<input type="text" class="txt s3 rt" value="0.00" />';
	$paym .= '<form enctype="multipart/form-data" target="catchframe" id="upfile" method="post" action="upload.php"><input type="text" class="txt s4" name="gc_conf" maxlength=32 /><input type="file" class="s4" name="gc_scan" /></form></li>';

	$paym .= '<li class="nobor"><a href="#" class="back dobatch" id="download">SUBMIT PAYMENT</a></li>';
	$paym .= '</ul>';

	if($id == 'unpaid') $msg .= IS_GOS && $num > 0 ? '' : $paym;
	return $msg;
}

function listReplenish() {
	$old = '-';
	$msg = $btn = '';
	$xqry = IS_GOS ? '' : "WHERE code LIKE '" . LOGIN_BRANCH . "%'";
	$qry  = "SELECT * FROM tblreplenish $xqry ORDER BY code";

	$con = SQLi('gos');
	$rs  = $con->query($qry) or die(mysqli_error($con));

	if($rs->num_rows > 0) {
		while($rw = $rs->fetch_assoc()) {
			$brn = substr($rw['code'], 0, 5);
			$cod = substr($rw['code'], 5, 5);
			$qty = $rw['qty'];
			$req = IS_GOS ? $rw['req'] : $qty;

			if($old != $brn && $old != '-') $msg .= getBtn($old);

			if($old != $brn) {
				$msg .= '<ul class="list batrepl" id="' . $brn . '">';
				if(IS_GOS) {
					list($rec, $con, $add, $cor) = getDelivery($brn);
					$msg .= '<li class="hdr"><span class="blue">GOS # ' . $brn . '</span>';
					$msg .= '<a href="#" class="delidet" id="download">DELIVERY DETAILS</a><div id="' . $brn . '" class="clear">';
					$msg .= '<span class="s2 rt">Receiver:</span><span class="s6">' . $rec . '</span><br />';
					$msg .= '<span class="s2 rt">Contact:</span><span class="s6">' . $con . '</span><br />';
					$msg .= '<span class="s2 rt">Address:</span><span class="s6">' . $add . '</span><br />';
					$msg .= '<span class="s2 rt">Courier:</span><span class="s6">' . $cor . '</span><br />';
					$msg .= '</div></li>';
				}
			}

			$old = $brn;

			if($old == $brn) {
				$msg .= '<li><span class="s3">' . $cod . '</span>';
				$msg .= '<span class="s5">' . getPName($cod) . '</span>';
				$msg .= '<span class="s3 rt qty">' . $qty . '</span>';
				$msg .= '<span class="s3 rt"><input type="text" rel="' . $cod . '" class="txt s1 rt" value="' . $req . '" ' . (IS_GOS ? '' : READ_ONLY) . ' /></span></li>';
			}
		}

		return $msg . getBtn($brn);
	}
}

function getBtn($brn) {
	$btn  = '<li class="nobor">';
	$btn .= '<a href="#" rel="' . $brn . '" class="back" id="download">' . (IS_GOS ? 'CONFIRM' : 'REPLENISH') . '</a>';
	$btn .= '</li></ul>';
	return $btn;
}

function listRemit($monyr) {
	global $sum;
	$msg = '';
	$rtpov = 0;
	$txct = 'text-align:center';
	$txlt = 'text-align:left';
	$txrt = 'text-align:right';

	if($sum) { //IS_GOS&&
		$qry = "SELECT * FROM tblorders WHERE remitid='$sum' ORDER BY refno";
		$con = SQLi('gos');
		$rs  = $con->query($qry) or die(mysqli_error($con));

		while($rw=$rs->fetch_assoc()) {
			$msg .= '<table border="0"><tr><th colspan="4"><strong>' . $rw['dsdid'] . ' - ' . getName(str_replace('*', '', $rw['dsdid']), 'lff') . ' [' . formatDate($rw['paydate']) . ']</strong></th><th colspan="2">Invoice#: ' . $rw['invoice'] . '</th><th colspan="2" style="text-align:right">Trans#: ' . $rw['refno'] . '</th></tr>';
			$msg .= '<tr><th style="width:72px;' . $txct . ';">Code</th>';
			$msg .= '<th style="width:40px;' . $txct . ';">Qty</th>';
			$msg .= '<th style="width:360px;' . $txlt . ';">Product Name</th>';
			$msg .= '<th style="width:80px;' . $txrt . ';">POV</th>';
			$msg .= '<th style="width:80px;' . $txrt . ';">PV</th>';
			$msg .= '<th style="width:80px;' . $txrt . ';">Price/Unit</th>';
			$msg .= '<th style="width:80px;' . $txrt . ';">Amount</th>';
			$msg .= '<th style="width:100px;' . $txct . ';">Remarks</th></tr>';

			list($ord, $spv, $sam) = printOrders($rw['refno']);

			$arr  = $_SESSION['center_orders'];
			$arr[]= array('', '', 0, 0, 0);
			$ctr  = count($arr) - 1;
			$tpov = 0;
			$tppv = 0;
			$tamt = 0;

			foreach($arr as $i=>$v) {
				$v1 = $v[0];
				$v2 = utf8_encode($v[1]);
				$v3 = $v[2];
				$v4 = $v[3];
				$v5 = $v[4];

				$pov   = getPOV($v1);
				$opov  = $pov * $v3;
				$tpov += $opov;
				$oppv  = $v5 * $v3;
				$tppv += $oppv;
				$oamt  = $v4 * $v3;
				$tamt += $oamt;

				$msg .= '<tr' . ($i < $ctr ? '' : ' style="border:3px solid #777 !important"') . '><td style="width:72px;' . $txct . ';">' . ($i < $ctr ? $v1 : '') . '</td>';
				$msg .= '<td style="width:40px;' . $txct . ';">' . ($i < $ctr ? $v3 : '') . '</td>';
				$msg .= '<td style="width:100px !important;' . $txlt . ';">' . ($i < $ctr ? $v2 : 'TOTAL') . '</td>';
				$msg .= '<td style="width:80px;' . $txrt . ';">' . number_format(($i < $ctr ? $opov : $tpov), 2) . '</td>';
				$msg .= '<td style="width:80px;' . $txrt . ';">' . number_format(($i < $ctr ? $oppv : $tppv), 2) . '</td>';
				$msg .= '<td style="width:80px;' . $txrt . ';">' . ($i < $ctr ? number_format((float)$v4, 2) : '') . '</td>';
				$msg .= '<td style="width:80px;' . $txrt . ';">' . number_format(($i < $ctr ? $oamt : $tamt), 2) . '</td>';
				$msg .= '<td class=""></td></tr>';
			}

			$rtpov += (float)$tpov;
			$msg .= '</table><br />';
		}
		
		$subsidy=($rtpov*(SUBSIDY/100));$GOS=$subsidy-($subsidy*.1);
		$msg.='<ul class="paysummary"><li class="hdr"><strong>PAYMENT TYPE</strong></li>';

		$pamt = 0;
		$con = SQLi('gos');
		$rs = $con->query("SELECT * FROM tblremit WHERE transact='$sum'") or die(mysqli_error($con));

		if ($rs->num_rows > 0) {
			while ($rw = $rs->fetch_assoc()) {
				$pamt += (float)$rw['payamt'];
				$msg .= '<li><span class="s3">' . $rw['paydate'] . '</span><span class="s5">' . $rw['paytype'] . '</span><span class="s5">' . $rw['paynote'] . '</span><span class="s3 rt">' . number_format($rw['payamt'], 2) . '</span></li>';
			}
			$msg .= '</ul><br /><ul class="paysummary"><li><span class="s3"></span><span class="s5">Total POV / Payment</span><span class="s4 rt">' . number_format($rtpov, 2) . '</span><span class="s4 rt">' . number_format($pamt, 2) . '</span></li>';
			// $msg.='</ul><br /><ul class="paysummary"><li><span class="s3"></span><span class="s5">Subsidy '.SUBSIDY.'% / Less 10% Tax</span><span class="s4 rt">'.number_format($subsidy,2).'</span><span class="s4 rt">'.number_format($GOS,2).'</span></li>';
		}

		$msg .= '</ul>';
		return '' . $msg . '';

	} else {
		global $hilite;
		$old = '';
		$qry = "SELECT * FROM tblremit WHERE transact LIKE '" . LOGIN_BRANCH . "%$monyr%' ORDER BY transact";
		$con = SQLi('gos');
		$rs  = $con->query($qry) or die(mysqli_error($con));

		while($rw=$rs->fetch_assoc()) {
			$reid = $rw['transact'];
			$date = $rw['paydate'];
			$type = $rw['paytype'];
			$pamt = $rw['payamt'];
			$note = $rw['paynote'];
			$scan = ($rw['payscan'] != '' ? 'yes' : 'no');
			$hili = ($hilite == $reid) ? ' current' : '';
			$hide = ($hilite == $reid) ? '' : ' hide';

			$new = $reid;

			if($old != $new) {
				$con = SQLi('gos');
				$get = $con->query("SELECT SUM(payamt) AS pay FROM tblremit WHERE transact='" . $reid . "'") or die(mysqli_error($con));
				$red = $get->fetch_assoc();

				$msg .= '<li class="remittance' . $hili . '" rel="' . $reid . '"><span class="s4"><a href="' . DLC_GORT . '/remit/?tab=remittance&sum=' . $reid . '">' . $reid . '</a></span>'; //(IS_GOS?DLC_GORT.'/remit/?tab=remittance&sum='.$reid:'#')
				$msg .= '<span class="s3 ct">' . $date . '</span>';
				$msg .= '<span class="s3"></span><span class="s3"></span>';
				$msg .= '<span class="s3 rt">' . number_format((float)$red['pay'], 2) . '</span></li>';
			}

			$old = $new;

			$msg .= '<li class="' . $reid . $hide . '"><span class="s4"></span><span class="s3"></span>';
			$msg .= '<span class="s3 rt">' . $type . '</span>';
			$msg .= '<span class="s3 rt">' . number_format((float)$pamt, 2) . '</span>';
			$msg .= '<span class="s3"></span></li>';
		}

		return '<ul class="list' . ($sum ? ' gcsum' : '') . '">' . $msg . '</ul>';
	}
}

function generate_options($arr,$select) {
	$return_string = array();
	foreach($arr as $val) {
		$return_string[] = '<option value="' . $val . '" ' . (($val == $select) ? SELECTED : '') . '>' . $val . '</option>';
	}

	return join('', $return_string);
}

function getDelivery($wh) {
	$con = SQLi('gos');
	$qry = "SELECT * FROM tblsetup WHERE wh='$wh'";
	$rs = $con->query($qry) or die(mysqli_error($con));
	$rw = $rs->fetch_array();
	return array($rw['dfrec'], $rw['dfcon'], $rw['dfadd'], $rw['dfcor']);
}

function getPOV($id) {
	$con = SQLi('products');
	$qry = "SELECT pov FROM tbllist WHERE id='$id'";
	$rs = $con->query($qry) or die(mysqli_error($con));
	$rw = $rs->fetch_array();
	return $rw['pov'];
}

function getNumbers($id) {
	$con = SQLi('gos');
	global $tab;
	$ret = '';

	if($id == 'unpaid') {
		$fil = $tab[$id];
		$notglob = (IS_GOS ? "AND remitid<>''" : '');
		$qry = "SELECT * FROM tblorders WHERE " . substr($tab[$id], 4, strlen($tab[$id])) . (IS_GOS ? "$fil $notglob " : " AND refno LIKE '" . LOGIN_BRANCH . "%'");
		$rs = $con->query($qry) or die(mysqli_error($con));
		$ret = $rs->num_rows;
	}

	return $ret;
}
?>
