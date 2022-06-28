<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

if(!defined('INCLUDE_CHECK')) {
	define('INCLUDE_CHECK',1);
	require('../setup.php');
}

global $rubyMgr;
$rubyMgr = array(
	EDDY,
	RICK,
	GRACE,
	GRACE.'-1',
	GRACE.'-2',
	'630000000003',//DIVINE
	'630000000003-1',
	'630000000003-2',
	'630000000224',//WINNIE
	'630000000224-1',
	'630000000224-2',
	// '630000001334',//FRANCO
	// '630000001334-1',
	// '630000001334-2',

);

getMinPV(WKYR, WEEK);

if(isset($_POST)&&isset($_POST['recalc'])) {
	switch($_POST['recalc']) {
		case 'bomstp':
			recalc(0,WEEK,WKYR);
			break;
		case 'slots':
			recalcSlots();
			break;
	}
}

function updateBomstp($dsid,$pv,$wk,$z,$do,$mgr='') {
	global $rubyMgr;
	$bmtpv= $lev=0;
	$bmsid='';
	$ppv = ($z<1 ? $pv :0);
	$npv = ($z<1 ? 0: $pv);

	$qry = "SELECT dssid,bmsid,bmppv,ROUND(bmppv+bmnpv,2) bmtpv
		FROM ".DB."distributor.distributors
		LEFT JOIN bomstp ON bmdid=dsdid
		WHERE dsdid='$dsid'";

	$con = SQLi('orders');
	$rs  = mysqli_query($con,$qry) or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		$rw = mysqli_fetch_array($rs);
		foreach($rw as $k=>$v) { $$k=$v; }
	}
// echo str_pad($dsid,16,"_")." | $bmtpv+$pv<br>";
	$match = preg_split("/[|]+/",$bmsid,-1,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
	$mline = substr_count($mgr,'|');
	$mgr   = ($mgr=='' ? '': ($bmsid!=''?(in_array($mgr,$match)?$bmsid:"$bmsid$mgr|"):"$mgr|"));
	$isMgr = isMgr($bmtpv+$pv,$mline);
	$lev   = $isMgr && $bmppv>0 || in_array($dsid,$rubyMgr) ? 5: getEndLvl($bmtpv+$pv);

	$u1 = ($z>0 ? '': "bmppv=ROUND(ROUND(bmppv,2)+$pv,2),");
	$u2 = ($z<1 ? '': "bmnpv=ROUND(ROUND(bmnpv,2)+$pv,2),");
	$u3 = 'bmelev='. $lev;
	$u4 = ($mgr=='' && $bmsid!='' ? '': ",bmsid='$mgr'");
// echo "$dsid | $mgr | $isMgr | $lev<br>";

	$qry = "INSERT INTO bomstp VALUES ('$dsid','$mgr','$wk',$lev,$ppv,$npv) ON DUPLICATE KEY UPDATE $u1 $u2 $u3 $u4";
	$con->query($qry);

	if( $dsid==EDDY ) {}
	else {
		$z++;
		updateBomstp($dssid,$pv,$wk,$z,$do,($isMgr?$dsid:''));
	}
}

function updateBreakaway($dsid,$pv,$z,$breakaway=0,$bkawpv=0) {
	$bmtpv = 0;
	$qry = "SELECT dssid,bmsid,bmppv,bmnpv,ROUND(bmppv+bmnpv,2) bmtpv,bmelev
		FROM bomstp
		LEFT JOIN ".DB."distributor.distributors d ON dsdid=bmdid
		WHERE bmdid='$dsid'";

	$con = SQLi('orders');
	$rs  = $con->query($qry);
	if($rs->num_rows>0) {
		$rw = $rs->fetch_array();
		foreach($rw as $k=>$v) { $$k=$v; }
	}

	$mline = substr_count($bmsid,'|');

	if($breakaway) {
		$isMgr = 1;
		global $rubyMgr;

		// $lev = isMgr($bmtpv-$bkawpv,$mline)||in_array($dsid,$rubyMgr)?5:getEndLvl($bmtpv-$bkawpv);
		$lev = $bmppv>MINPV||in_array($dsid,$rubyMgr)?5:getEndLvl($bmtpv-$bkawpv);

		$qry = "UPDATE bomstp SET bmnpv=ROUND(ROUND(bmnpv,2)-$bkawpv,2),bmelev=$lev WHERE bmdid='$dsid'";

		if($bkawpv<=$bmnpv) $con->query($qry);
// echo str_pad($dsid,16,"_")." | $bmppv | ".($bmtpv-$bkawpv)." | $bmelev | $lev <br>";

	} else {
		$isMgr  = isMgr($bmtpv,$mline);
		$bkawpv = $pv;
// echo str_pad($dsid,16,"_")." | $bmtpv | $pv | $isMgr | $bmelev <br>";
	}

	if($dsid==EDDY) {}
	else updateBreakaway($dssid,$pv,$z++,$isMgr,$bkawpv);
}

function testMinForSlot($id,$yr,$mo) {
	$qry = "SELECT
		(SELECT COUNT(*) FROM ormstp WHERE omdid='$id' AND ompyr='$yr' AND ompmo='$mo' AND om40=1)-
		(SELECT COUNT(*) FROM tblslots WHERE ref=CONCAT('$yr','$mo','$id') AND dsdid='$id') m
	";

	$con = SQLi('orders');
	$rs  = $con->query($qry);
	$rw  = $rs->fetch_assoc();
	$m   = $rw['m'];

	while($m>=1) {
		$con->query("INSERT INTO tblslots VALUES ('','$id',NULL,NULL,NULL,NULL,'$yr$mo$id')");
		$m--;
	}
}

function isMgr($pv,$mline) {
	return (
		($pv >= $_SESSION['minpv'][5]) ||
		($mline==2 && $pv>=$_SESSION['minpv'][2]) ||
		($mline>2 && $pv>=MINPV)
	);
}

function recalcSlots() {
	$qry = "
		SELECT omdid,ompyr,ompmo,
			CONCAT(omdid,ompyr,ompmo) cut
		FROM ormstp
		WHERE ompyr>=2017
		AND ompmo>=35
		GROUP BY cut
	";

	$con = SQLi('orders');
	$rs  = $con->query($qry);
	while($r = $rs->fetch_assoc()) {
		testMinForSlot($r['omdid'],$r['ompyr'],$r['ompmo']);
	}

	mysqli_close($con);
}

function recalc($do,$wk,$yr) {
	$con = SQLi('orders');
	$con->query("TRUNCATE TABLE bomstp");

	$qry = "
		SELECT omdid,ROUND(SUM(ompv),2) pv
		FROM ormstp
		WHERE ompmo='$wk' AND ompyr='$yr' AND LOWER(ominv)<>'void'
		GROUP BY omdid

	";//ORDER BY ompv DESC
	$rs = $con->query($qry);
	while($r1 = $rs->fetch_assoc()) {
		// ini_set('max_execution_time',120);
// echo "<br><br>";
		updateBomstp($r1['omdid'],$r1['pv'],$wk,0,$do);
	}

	$rs = $con->query("SELECT bmdid,bmppv FROM bomstp WHERE bmppv>0 ORDER BY bmppv DESC") or die(mysqli_error($con));
	while($r2 = $rs->fetch_assoc()) {
// echo "<br><br>";
		updateBreakaway($r2['bmdid'],$r2['bmppv'],0);
	}
}
?>
