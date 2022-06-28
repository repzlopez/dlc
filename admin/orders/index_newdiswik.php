<?php
if (!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if (!defined('INCLUDE_CHECK')) die('Invalid Operation');

$x   = '';
$tbl = 'tbl'. $content;

$x .= '<ul id="'. $content .'" class="list">'. loadNewDisWik(WEEK-1) .'</ul>';
$x .= '<ul id="'. $content .'" class="list">'. loadNewDisWik((int)WEEK) .'</ul>';
echo $x;

function loadNewDisWik($wk) {
	$con = SQLi('distributor');
	$hdr = '<li class="hdr"><span class="s0"></span><span class="s4">ID</span>
		<span class="s6">Name</span>
		<span class="s2 rt">Encoded</span>
		<span class="s2 rt">PPV</span></li>';

	$x = '<li class="blue"><br><strong>Week '. $wk .'</strong><br></li>'. $hdr;

	$qry = "
		SELECT SUM(ompv) pv, dsdid,fst,lst,wk,
			CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam,
			CONCAT(SUBSTR(dssetd,1,4),'-',SUBSTR(dssetd,5,2),'-',SUBSTR(dssetd,7,2)) setup
		FROM ".DB."orders.ormstp
		LEFT JOIN ".DB."distributor.distributors
			ON dsdid=omdid
		LEFT JOIN ".DB."beta.tblsched
			ON (wk=$wk AND yr='".WKYR."')
		WHERE ompmo=$wk AND ompyr='".WKYR."'
		GROUP BY omdid
		HAVING setup BETWEEN fst AND lst
	";

	$rs = $con->query($qry);
	while( $r = $rs->fetch_assoc() ) {
		$pv = 0;
		foreach($r as $k=>$v) $$k=$v;

		$x .= '<li><span class="s0"></span><span class="s4">'. $dsdid .'</span>
			<span class="s6" title="'.$nam.'">'. $nam .'</span>
			<span class="s2 rt">'. date('m.d.Y',strtotime($setup)) .'</span>
			<span class="s2 rt">'. number_format($pv,2) .'</span></li>';
	}

	mysqli_close($con);
	return $x;
}
?>
