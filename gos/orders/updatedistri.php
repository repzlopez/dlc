<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');

global $z;
$tbl  = 'distributors';
$dsid = isset($_SESSION['gos_dsdid']) ? $_SESSION['gos_dsdid'] : null;

$con = SQLi('distributor');
foreach($_POST as $k => $v) {
	$dat = strtoupper(trim_escape($v));
	$$k = $dat;
}

if($submit=='_ _') {
	if(testExist($find, 'distributor', 'distributors', 'dsdid')) {
		getDistList($find);

		if($z) {
			$qry = "SELECT * FROM $tbl WHERE dsdid='$find'";
			$rs = $con->query( $qry) or die(mysqli_error($con));
			$rw = $rs->fetch_array();
			$con = ($rw['dsmph'] != '') ? $rw['dsmph'] : (($rw['dshph'] != '') ? $rw['dshph'] : $rw['dsoph']);
			echo getName($rw['dsdid'], 'lfm') . '|' . $con . '|' . ($rw['dstin'] == '' ? 'TBS' : $rw['dstin']);
		} else echo 'NOT FOUND';

	} else {
		echo 'NOT FOUND';
	}

} elseif($submit=='@@@') {
	$dat = '';
	$qry = "SELECT * FROM $tbl WHERE dslnam LIKE '%$find%' OR dsfnam LIKE '%$find%'";
	$rs  = $con->query( $qry) or die(mysqli_error($con));

	while($rw = $rs->fetch_assoc()) {
		getDistList($rw['dsdid']);

		if ($z) {
			$dat .= '<li><span class="s5 blue">' . $rw['dsdid'] . '</span><span>' . getName($rw['dsdid'], 'lfm') . '</span></li>';
		}
	}

	echo $dat;

} else "NOT FOUND||";

unset($_POST);
unset($_SESSION['gos_down']);

function getDistList($id) {
	$con = SQLi('distributor');
	global $z;

	$rs = $con->query( "SELECT dssid FROM distributors WHERE dsdid='$id'") or die(mysqli_error($con));
	$rw = $rs->fetch_assoc();

	$dsid = $rw['dssid'];
	$gsid = $_SESSION['gos_dsdid'];

	if ($dsid == $gsid || $id == $gsid) {
		$z = 1;

	} else {
		if ($dsid == EDDY) {
			$z = 0;
		} else getDistList($dsid);
	}
}
?>
