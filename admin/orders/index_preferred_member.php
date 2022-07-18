<?php
if(!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if(!defined('INCLUDE_CHECK')) die('Invalid Operation');

$tbl = 'tbl'. $content;
$x = '';

prepPrefMemberBonus();

$x.='<ul id="'.$content.'" class="list">'. loadPrefMemberBonus(WEEK).'</ul>';
echo $x;

function prepPrefMemberBonus() {
	$x = '';
	$i = 0;
	$con = SQLi('orders');

	$qry = "SELECT o.omdid, o.ominv, x.refno, x.orders,
			(SELECT DATEDIFF(CONCAT(SUBSTRING(dssetd,1,4),'-',SUBSTRING(dssetd,5,2),'-',SUBSTRING(dssetd,-2)), '". PM_START ."') FROM diamondl_distributor.distributors WHERE dsdid=o.omdid) age 
		FROM ormstp o
		LEFT JOIN (
				SELECT refno,invoice,orders
				FROM " . DB . "gos.tblorders
				UNION
				SELECT refno,invoice,orders
				FROM " . DB . "pcm.tblorders
			) x ON x.invoice=o.ominv
		WHERE o.ompyr = ". WKYR ."
		AND o.ompmo = " . WEEK . "
		AND o.ominv NOT IN (SELECT invoice FROM pm_bonus)
		AND o.omdid NOT IN (SELECT bhdid FROM " . DB . "distributor.bohstp)
		AND x.orders <> ''
		HAVING age >= 0
		ORDER BY o.ominv DESC
	";
// echo $qry . '<br>';

	$rs = $con->query($qry) or die(mysqli_error($con));

	if( $rs->num_rows > 0 ) {
		echo "<h3>Processing PM Bonus...</h3><br>";
	}

	while ($r = $rs->fetch_assoc()) {

		$dssid  = getDat('distributor', 'distributors', 'dssid', 'dsdid', $r['omdid']);
		$refer  = getDat('beta', 'tblolreg', 'referrer', 'dsdid', $r['omdid']);

		$bonus_for = ( $refer != $r['omdid'] &&  $refer != '' ? $refer : $dssid );

		echo "<h4>Invoice# ". $r['ominv'] . " to $bonus_for</h4>";

		$l1 = explode("~", $r['orders']);
		foreach($l1 as $k=>$v) {
			$l2 = explode("|", $v);

			calcPMBonus($r['ominv'], $bonus_for, $l2[0], $l2[2]);
			$i++;
		}

		echo '<br>';
	}

	if ($rs->num_rows > 0) {
		echo '<hr style="border:#eee solid 1px"><br><h3 class="rt">' . $i . ' NEW PM Bonus added</h3><br><hr style="border:#eee solid 1px">';
	}

}

function calcPMBonus($invoice,$dsdid,$item,$qty) {
	$bonus  = getDat('products', 'tbllist', 'pmbonus', 'id', $item);
	$bonus *= $qty;

	echo "<p> - $item (x $qty): Bonus ". number_format($bonus,2) ." ADDED<p>";

	$qry = "INSERT INTO pm_bonus VALUES (0,$invoice,'$dsdid',$item,$bonus,null,0)";
// echo $qry . '<br>';

	$con = SQLi('orders');
	$con->query($qry) or die(mysqli_error($con));
}

function loadPrefMemberBonus($wk) {
	$con = SQLi('distributor');
	$hdr = '<li class="hdr"><span class="s0"></span><span class="s4">Dist ID</span>
		<span class="s6">Name</span>
		<span class="s2 rt">Invoice</span>
		<span class="s3 rt">Bonus</span>
		<span class="s2 ct">Status</span></li>';

	$x = '<li class="blue"><br><h3>Preferred Member Bonus</h3><br></li>'.$hdr;

	$qry = "
		SELECT b.*,
			CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam,
			CONCAT(SUBSTR(dssetd,1,4),'-',SUBSTR(dssetd,5,2),'-',SUBSTR(dssetd,7,2)) setup
		FROM ".DB. "orders.pm_bonus b
		LEFT JOIN ".DB. "distributor.distributors d
			ON d.dsdid=b.dsdid
	";
// echo $qry . '<br>';

	$rs = $con->query($qry) or die(mysqli_error($con));
	while($r=$rs->fetch_assoc()) {

		foreach($r as $k=>$v) $$k = $v;

		$x .= '<li><span class="s0"></span><span class="s4">'.$dsdid.'</span>
			<span class="s6" title="'.$nam.'">'.$nam. '</span>
			<span class="s2 rt">'. $invoice . '</span>
			<span class="s3 rt">'. number_format($bonus, 2, '.', ',') .'</span>
			<span class="s2 ct">'. $status .'</span></li>';
	}

	mysqli_close($con);
	return $x;
}
