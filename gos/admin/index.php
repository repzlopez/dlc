<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

require('../../admin/setup.php');
require('../func.php');

testScope("global|gos", DLC_GORT);

$title = 'GOS | Admin';
$content = 'admin';

ob_start();
include('../head.php');

$tbl = 'tbladmin';
$con = SQLi('gos');

if( !$do ) {
	$_SESSION['gos_last'] = DLC_GORT;

	echo '<ul class="list">';
	echo '<li class="hdr"><span class="s3">Admin ID</span><span class="s5">Name</span><span class="s2">Scope</span><span class="s2">Enabled</span></li>';

	$rs = $con->query( "SELECT * FROM $tbl ORDER BY un") or die(mysqli_error($con));

	while($rw = $rs->fetch_assoc()) {
		list($whid, $name, $dsdid, $bond) = getWHID($rw['un']);
		echo '<li rel="' . $rw['un'] . '"><span class="s3"><a href="?do=2&i=' . $rw['un'] . '">' . $rw['un'] . '</a></span><span class="s5">' . ($name == '' ? '-- CLOSED --' : $name) . '</span><span class="s2">' . $rw['scop'] . '</span><span class="s2 stat ' . (!$rw['status'] ? 'bad' : '') . '">' . ($rw['status'] ? 'yes' : 'no') . '</span></li>';
	}

	mysqli_close($con);

	echo '<li><a href="?do=1"><span id="addnew"></span>ADD NEW USER</a></li>';
	echo '</ul>';

} else {

	$sespost = null;
	$styleis = $bad = '';
	$_SESSION['gos_last'] = DLC_GORT . '/admin';

	$rs = $con->query( "SELECT * FROM $tbl WHERE un='$item'") or die(mysqli_error($con));
	$x  = ($rs->num_rows > 0) ? 1 : 0;
	$rw = $rs->fetch_array();

	$c_y = $rw['status'] ? C_K : '';
	$c_n = !$rw['status'] ? C_K : '';
	$un  = ($x) ? $rw['un'] : '';
	$rpw = ($x) ? $rw['pw'] : '';
	$ded = ($do > 1) ? $do . ',' . $rw['un'] : $do;

	mysqli_close($con);

	if(isset($_SESSION['post'])) {
		$sespost = $_SESSION['post'];
		$bad = $sespost['bad_distri'];
		$styleis = (strpos($sespost['bad_distri'], 'RED') !== false) ? RED : '';

		foreach ($sespost as $key => $val) {
			$$key = $val;
		}
	}

	echo '<form method="post" action="post.php"><ul>';
	echo '<li><label>GOS ID:</label>'.($do==1?'<select name="un">'.getDrop($un).'</select>':$un.'<input type="hidden" name="un" value="'.$un.'" />').'</li>';
	echo '<li><label>Password:</label><input type="password" '.$styleis.' name="pw" class="txt ex" value="" /><input type="hidden" name="scop" value="GOS" /></li>';
	echo '<li><label>Enabled:</label><label><input type="radio" name="status" value=1 '.$c_y.' class="rdo" />Yes</label> <label><input type="radio" name="status" value=0 '.$c_n.' class="rdo" />No</label></li>';
	echo '<li><span class="bad">'.$bad.'</span><input type="hidden" name="do" value="'.$ded.'" /></li>';
	echo '<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}

include('../foot.php');
ob_end_flush();

function getDrop($id='') {$n='';
	$con = SQLi('products');
	$qry = "
		SELECT DISTINCT w.id,w.wh,a.un
		FROM " . DB . "products.tblwarehouse w
		LEFT JOIN " . DB . "gos.tbladmin a
			ON w.id=a.un
		WHERE a.un IS NULL
		AND w.status=1
		ORDER BY w.id
	";

	$rs = $con->query( $qry) or die(mysqli_error($con));
	while($rw = $rs->fetch_assoc()) {
		$n .= '<option value="' . $rw['id'] . '" ' . ($rw['id'] == $id ? SELECTED : '') . '>' . $rw['id'] . ' ' . $rw['wh'] . '</option>';
	}

	mysqli_close($con);
	return $n;
}
?>
