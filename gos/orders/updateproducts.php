<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) define('INCLUDE_CHECK',1);

require_once('../../admin/setup.php');

if(isset($_POST['sortonly'])&&$_POST['sortonly']) {
	echo sortProducts($_POST['sortorder']);
} else echo buildProducts();

function buildProducts() {
	$msg   = '';
	$found = 0;
	$_SESSION['sortarray'] = array();
	$sort_on = 'sort_order,p.id';

	$search_on = (isset($_POST['find'])) ? $_POST['find'] : (isset($_SESSION['prodsearch']) ? $_SESSION['prodsearch'] : '');
	$filter_on = (isset($_POST['filter'])) ? $_POST['filter'] : (isset($_SESSION['prodfilter']) ? $_SESSION['prodfilter'] : '');
	$search_in = (!is_numeric($search_on)) ? 'l.name' : 'l.id';
	$order_type = isset($_SESSION['order_type']) ? $_SESSION['order_type'] :'';

	$_SESSION['prodsearch'] = $search_on;
	$_SESSION['prodfilter'] = $filter_on;
	$_SESSION['prodsort']   = $sort_on;

	$filarr  = buildFilter();
	$sort_by = "ORDER BY $sort_on";

	switch($order_type) {
		case '':
		case 'distributor':
			$use_price = "l.wsp";
			break;

		case 'preferred':
			$use_price = "l.pmp";
			break;

		case 'customer':
			$use_price = "l.srp";
			break;
	}

	$qry = "SELECT p.*,l.name,$use_price use_price,l.pov,l.pv
			FROM tblproducts p
			LEFT JOIN tbllist l
				ON p.id=l.id
			WHERE $search_in LIKE '%$search_on%'
			$filarr
			AND l.status=1
			$sort_by";

	unset($_POST);

	$con = SQLi('products');
	$rs  = $con->query($qry) or die(mysqli_error($con));

	while($rw = $rs->fetch_assoc()) {
		$line = '';
		$pid  = $rw['id'];
		$oqty = getQty($pid);
		$nam  = utf8_encode($rw['name']);

		$line .= '<li rel="' . $pid . '">';
		$line .= '<span class="s1">' . $pid . '</span>';
		$line .= '<span class="s6">' . $nam . '</span>';
		$line .= '<span class="s2 rt" id="ws' . $pid . '">' . number_format($rw['use_price'], 2) . '</span>';
		$line .= '<span class="s2 rt" id="pv' . $pid . '">' . number_format($rw['pv'], 2) . '</span>';
		$line .= '<span class="s1"><input type="text" class="s1 rt oqty txt" rel="' . $pid . '" name="oqty" value=' . $oqty . ' /></span>';
		$line .= '<span class="s2 rt" id="am' . $pid . '">' . number_format($rw['use_price'] * $oqty, 2) . '</span>';
		$line .= '<span class="s2 rt" id="ap' . $pid . '">' . number_format($rw['pv'] * $oqty, 2) . '</span></li>';

		$_SESSION['sortarray']['full'][] = $line;
		$_SESSION['sortarray']['def'][]  = $found;
		$_SESSION['sortarray']['id'][]   = $pid;
		$_SESSION['sortarray']['name'][] = $rw['name'];
		$_SESSION['sortarray']['wsp'][]  = $rw['use_price'];
		$_SESSION['sortarray']['pv'][]   = $rw['pv'];

		$msg .= $line;
		$found++;
	}

	mysqli_close($con);
	return "$msg|$found";
}

function sortProducts($sortorder) {
	$msg  = '';
	$sord = explode('|', $sortorder);
	$sort = ($sord[1] == 'asc') ? SORT_ASC : SORT_DESC;

	$toSortArr = $_SESSION['sortarray']['full'];
	$bySortArr = array_map('strtolower', $_SESSION['sortarray'][$sord[0]]);

	array_multisort($bySortArr, $sort, SORT_NATURAL, $toSortArr);

	foreach($toSortArr as $val) {
		$msg .= $val;
	}

	return $msg;
}

function my_sort($x,$y) {
	if($x == $y) return 0;
	return ($x > $y) ? -1 : 1;
}

function getQty($id) {
	$q = 0;
	if(isset($_SESSION['center_orders'])) {
		foreach ($_SESSION['center_orders'] as $k => $v) {
			if (in_array($id, $v)) {
				$q = $v[2];
			}
		}
	}

	return $q;
}

function buildFilter() {
	$d   = 0;
	$arr = '';
	$con = SQLi('products');
	$rs  = $con->query("SELECT DISTINCT parent_id FROM tblcategories WHERE status=1") or die(mysqli_error($con));
	$fltr = isset($_SESSION['prodfilter']) ? $_SESSION['prodfilter'] : str_pad('', mysqli_num_rows($rs) + 1, '1');

	while ($rw = $rs->fetch_assoc()) {
		$yno = array('null' => 0, "'" . $rw['parent_id'] . "'" => 1);
		$pid = array_search(substr($fltr, $d++, 1) == '1', $yno);
		$arr .= " p.cat=$pid OR";
	}

	mysqli_close($con);
	$arr = substr($arr, 0, strlen($arr) - 3);
	return ($arr != '') ? "AND ($arr)" : '';
}
?>
