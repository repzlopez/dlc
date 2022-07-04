<?php
if (!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if (!defined('INCLUDE_CHECK')) die('Invalid Operation');

$x   = '';
$tbl = $content;
$con = SQLi('orders');

if( $do==0 ) {
	$rel_ok= $rel= $t= 0;

	$hdr = '<li class="hdr"><span class="s0"></span><span class="s4">Dist ID</span>
		<span class="s6">Name</span>
		<span class="s2 rt">Invoice</span>
		<span class="s3 rt">Bonus</span>
		<span class="s3 ct">Release</span></li>';

	$x = '<li class="blue"><br><h3>Preferred Member Bonus</h3><br></li>' . $hdr;

	$qry = "
		SELECT b.*,
			CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam,
			CONCAT(SUBSTR(dssetd,1,4),'-',SUBSTR(dssetd,5,2),'-',SUBSTR(dssetd,7,2)) setup
		FROM " . DB . "orders.pm_bonus b
		LEFT JOIN " . DB . "distributor.distributors d
			ON d.dsdid=b.dsdid
	";

	$rs = $con->query($qry) or die(mysqli_error($con));
	while( $rw = $rs->fetch_assoc() ) {
		foreach($rw as $k=>$v) { $$k=$v; }

		$pv = 0;

		$x .= '<li rel="' . $id . '"><span class="s4">' . $dsdid . '</span>
		<span class="s6" title="' . $nam . '">' . $nam . '</span>
		<span class="s2 rt">' . $invoice . '</span>
		<span class="s3 rt">' . number_format($bonus, 2, '.', ',') . '</span>
		<span class="s3 ct">' . ( $status ? 'yes' : '<input type="button" class="pm_release" rel="' . $id . '" value="Release" />') . '</span></li>';
	}

	mysqli_close($con);
	$x .= '</ul>';

}

echo $x;
?>
