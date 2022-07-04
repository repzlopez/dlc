<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

$search_on = (isset($_POST['find'])) ? $_POST['find'] : null;
$page_on   = (isset($_POST['page'])) ? $_POST['page'] : null;

$find  = isset($search_on) ? "WHERE (d.dsdid LIKE '%$search_on%' OR dsfnam LIKE '%$search_on%' OR dsmnam LIKE '%$search_on%' OR dslnam LIKE '%$search_on%')" : '';
$dbsrc = 'distributor';

require_once('info.config');
require_once('infoconfig.php');

$qry = "
	SELECT d.dsdid,dslnam,dsfnam,dsmnam,
		(SELECT SUM(CASE WHEN dslid='' THEN 1 ELSE 0 END)
		FROM ".DB."orders.tblslots o
		WHERE o.dsdid=d.dsdid) slots
	FROM ".DB."distributor.distributors d
	$find ORDER BY dsdid";

$y = '';
// ini_set('max_execution_time',60);

$rs = $con->query($qry) or die(mysqli_error($con));
$num = $rs->num_rows;

while($rw = $rs->fetch_assoc()) {
	$href = $_SESSION['login_type'] == 'admin' ? '<a href="?p=' . $page_on . '&id=' . $rw['dsdid'] . '">' . $rw['dsdid'] . '</a>' : $rw['dsdid'];

	$y .= '<li rel="' . $rw['dsdid'] . '">';
	$y .= '<span class="s4">'. $href .'</span>';
	$y .= '<span class="s6">' . $rw['dslnam'] . ', ' . $rw['dsfnam'] . ' ' . $rw['dsmnam'] . '</span>';
	// $y .= '<span class="s2 rt">' . (int)$rw['slots'] . '</span>';
	$y .= '</li>';
}

$x  = '<ul id="distri" class="list clear">';
$x .= '<li><br>Found <strong class="s0">' . $num . '</strong> match' . ($num > 1 ? 'es' : '') . '<br><br></li>';
$x .= '<li class="hdr"><span class="s4">Distributor ID</span>';
$x .= '<span class="s6">Distributor Name</span>';
// $y .= '<span class="s2 rt">Slots</span>';
$x .= '</li>';

$x .= $y;
$x .= '</ul>';

echo $x;
?>
