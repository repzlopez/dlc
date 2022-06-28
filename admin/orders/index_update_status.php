<?php
if(!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if(!defined('INCLUDE_CHECK')) die('Invalid Operation');

$tbl = 'tbl'. $content;
$x = '';

updateMemberStatus();

// $x.='<ul id="'.$content.'" class="list">'. loadPrefMemberBonus(WEEK).'</ul>';
echo $x;

function updateMemberStatus() {
	$x = '';
	$con = SQLi('distributor');

	$qry = "SELECT d.dsdid,b.bhdid,o.omdid,x.omdid
		FROM ". DB ."distributor.distributors d
		LEFT JOIN (
				SELECT bhppv
				FROM ". DB ."distributor.bohstp
				GROUP BY bhpyr,bhpmo
				HAVING bhppv>=" . MINPV_6pct . "
			) b ON b.bhdid=d.dsdid

		LEFT JOIN (
				SELECT SUM(ompv) pv
				FROM ". DB ."distributor.ormstp
				GROUP BY ompyr,ompmo
				HAVING pv>=" . MINPV_6pct . "
			) o ON o.omdid=d.dsdid

		LEFT JOIN (
				SELECT SUM(ompv) pv
				FROM " . DB . "orders.ormstp
				GROUP BY ompyr,ompmo
				HAVING pv>=" . MINPV_6pct . "
			) x ON x.omdid=d.dsdid

		ORDER BY d.dsdid
	";
echo $qry . '<br>';

// 	$rs = $con->query( $qry);

// 	if( $rs->num_rows > 0 ) {
// 		echo "<h3>Processing PM Bonus...</h3><br>";
// 	}

// 	while ($r = $rs->fetch_assoc()) {
// // find if $r['omdid'] is PM
// 		$dssid  = getDat('distributor', 'distributors', 'dssid', 'dsdid', $r['omdid']);
// 		$refer  = getDat('beta', 'tblolreg', 'referrer', 'dsdid', $r['omdid']);

// 		$bonus_for = ( $refer != '' ? $refer : $dssid );

// 		echo "<h4>Invoice# ". $r['ominv'] .' for '. $r['omdid'] . " | $bonus_for" ."</h4>";

// 		$l1 = explode("~", $r['orders']);
// 		foreach($l1 as $k=>$v) {
// 			$l2 = explode("|", $v);

// 			calcPMBonus($r['ominv'], $r['omdid'], $l2[0], $l2[2]);
// 			$i++;
// 		}

// 		echo '<br>';
// 	}

// 	if ($rs->num_rows > 0) {
// 		echo '<hr style="border:#eee solid 1px"><br><h3 class="rt">' . $i . ' NEW PM Bonus added</h3><br><hr style="border:#eee solid 1px">';
// 	}

}

function calcPMBonus($invoice,$dsdid,$item,$qty) {
	$bonus  = getDat('products', 'tbllist', 'pmbonus', 'id', $item);
	$bonus *= $qty;

	echo "<p> - $item (x $qty): Bonus ". number_format($bonus,2) ." ADDED<p>";

	$qry = "INSERT INTO pm_bonus VALUES ('',$invoice,'$dsdid',$item,$bonus,0)";

	$con = SQLi('orders');
	$con->query($qry);
}

function loadPrefMemberBonus($wk) {
	$con = SQLi('distributor');
	$hdr = '<li class="hdr"><span class="s0"></span><span class="s4">Dist ID</span>
		<span class="s6">Name</span>
		<span class="s2 rt">Invoice</span>
		<span class="s3 rt">Bonus</span>
		<span class="s2 rt">Status</span></li>';

	$x = '<li class="blue"><br><h3>Preferred Member Bonus</h3><br></li>'.$hdr;

	$qry = "
		SELECT b.*,
			CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam,
			CONCAT(SUBSTR(dssetd,1,4),'-',SUBSTR(dssetd,5,2),'-',SUBSTR(dssetd,7,2)) setup
		FROM ".DB. "orders.pm_bonus b
		LEFT JOIN ".DB. "distributor.distributors d
			ON d.dsdid=b.dsdid
	";

	$rs = mysqli_query($con,$qry);
	while($r=$rs->fetch_assoc()) {

		$pv = 0;
		foreach($r as $k=>$v) $$k = $v;

		$x .= '<li><span class="s0"></span><span class="s4">'.$dsdid.'</span>
			<span class="s6" title="'.$nam.'">'.$nam. '</span>
			<span class="s2 rt">'. $invoice . '</span>
			<span class="s3 rt">'. number_format($bonus, 2, '.', ',') .'</span>
			<span class="s2 rt">'. $status .'</span></li>';
	}

	mysqli_close($con);
	return $x;
}
