<?php
if (!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if (!defined('INCLUDE_CHECK')) die('Invalid Operation');

$id = $name = $msg = $c = $x = '';

if( isset($_POST['submit']) && $_POST['id'] !='' ) {
	$id  = trim_escape($_POST['id']);
	$qry = "SELECT bhdid,CONCAT(dsfnam,' ',dsmnam,' ',dslnam) nam,
				DATEDIFF(CONCAT(SUBSTRING(dssetd,1,4),'-',SUBSTRING(dssetd,5,2),'-',SUBSTRING(dssetd,-2)), '" . PM_START . "') age 
		FROM " . DB . "distributor.distributors d
		LEFT JOIN " . DB . "distributor.bohstp b ON b.bhdid=d.dsdid
		WHERE dsdid='$id'
	";

	$con = SQLi('distributor');
	$rs = $con->query($qry) or die(mysqli_error($con));

	if( $rs->num_rows > 0) {
		$r = $rs->fetch_array();

		$name = $r['nam'];

		if( $r['bhdid'] != '' ) {
			$c = 'good';
			$msg = 'DISTRIBUTOR';

		} elseif( $r['age'] < 0  ) {
			$msg = "NON DISTRIBUTOR";

		} else {
			$msg = "PREFERRED MEMBER";
			$c = 'bad';
		}

	} else {
		$msg = "NOT FOUND";
		$c = 'bad';
	}

	unset($_POST);
}

$x .= '<form method="post" action="" id="testsponsoring"><ul>';
$x .= '<li><span class="blue">PREFERRED MEMBER</span>';
$x .= '<li><label>Distributor ID:</label><input type="text" name="id" class="txt s5" id="distid" placeholder="Enter Distributor ID" /> <span class="small more">** dist.id</span></li>';
$x .= '<li><h3 class="b ct ' . $c . '">' . $id . '<br>' . strtoupper($name) . '</h3></li>';
$x .= '<li><h1 class="b ct ' . $c . '">' . $msg . '</h1></li>';
$x .= '<input type="submit" name="submit" class="btn" value="Check" /></ul></form>';

ob_start();
echo $x;
ob_end_flush();
?>
