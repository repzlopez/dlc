<?php
if (!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if (!defined('INCLUDE_CHECK')) die('Invalid Operation');

$x = '';
$tbl = $content;
$all = isset($_GET['all']) ? $_GET['all'] : 0;

$x .= '<ul id="'.$content.'" class="list clear">';
$x .= '<li class="hdr"><span class="s4">Reference</span>';
$x .= '<span class="s3">Order Date</span>';
$x .= '<span class="s4">Distributor</span>';
$x .= '<span class="s5">Name</span>';
$x .= '<span class="s4">Remittance</span>';
$x .= '<span class="s2">Invoice</span>';

list($wk1,$yr,,$wed) = getDatWk(date('Y-m-d'));
list($wk,,$thu,) = getDatWk( ($wk1>2 ? sprintf("%02d", $wk1-1) : '01') .$yr, 1);

$ad1 = ($all ? '': ",CONCAT(SUBSTRING(refdate,1,4),'-',SUBSTRING(refdate,5,2),'-',SUBSTRING(refdate,-2)) rdate");
$ad2 = ($all ? '': "HAVING rdate BETWEEN '$thu' AND '$wed'");

$qry = "SELECT * $ad1
	FROM tblorders
	WHERE status<9
	$ad2
	ORDER BY refno DESC";
//echo "$qry<br>";

$con = SQLi('gos');
$rs  = $con->query($qry);
while($r = $rs->fetch_assoc()) {
	foreach($r as $k=>$v) { $$k=$v; }

	$x .= '<li><a '.(IS_GLOB||IS_GOS?'href="/gos/remit/?p=remit&do=1&i='.$refno.'"':'').' class="s4" target="_blank">'.$refno.'</a>';
	$x .= '<span class="s3">'.formatDate($refdate,'Ymd').'</span>';
	$x .= '<span class="s4">'.$dsdid.'</span>';
	$x .= '<span class="s5">'.getName(str_replace('*','',$dsdid),'lfm').'</span>';
	$x .= '<a '.(IS_GLOB||IS_GOS?'href="/gos/remit/?tab=remittance&sum='.$remitid.'"':'').' class="s4" target="_blank">'.$remitid.'</a>';
	$x .= '<span class="s2">'.$invoice.'</span>';
	$x .= '</li>';
}

unset($_SESSION['errmsg']);
mysqli_close($con);

$x .= '</ul>';
echo $x;

?>
