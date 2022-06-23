<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
$tm=$_POST['item'];
$de=$_POST['desc'];
$qy=$_POST['qty'];
$i=0;
if(isset($tm)) {
	if(!isset($_SESSION['transfers'])) $_SESSION['transfers']=array();
	if(checkAssembly($tm,$qy)) {
	}else{
		while($i<count($_SESSION['transfers'])&&$_SESSION['transfers'][$i][0]!=$tm) $i++;
		if($i<count($_SESSION['transfers'])) {
			if($_SESSION['transfers'][$i][2]!=$qy) {
				$_SESSION['transfers'][$i][1]=$de;
				$_SESSION['transfers'][$i][2]=$qy;
			}
		}else $_SESSION['transfers'][]=array($tm,$de,$qy);
	}
}
if(isset($_POST['clear'])&&$_POST['clear']) {
	unset($_SESSION['transfers']);
	unset($_SESSION['csverror']);
}

if(isset($_SESSION['transfers'])) {
	include '../fetch.php';
	echo getTransfers($_SESSION['transfers']);
}

function checkAssembly($id,$qty) {
	if($_SESSION['isAssembly']) { return false;exit;}
	$con=SQLinv('products');
	$rs=mysqli_query($con,"SELECT reqdesc FROM tbllogassembly WHERE id='$id'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)<1) { return false;exit;}
	$rw=mysqli_fetch_array($rs);
	$i=0;
	$req=explode("~",$rw['reqdesc']);
	mysqli_close($con);
	foreach($req as $v) {
		$ireq[]=explode("|",$v);
		$_SESSION['transfers'][]=array($ireq[$i][0],$ireq[$i][1],($ireq[$i][2]*$qty));
		$i++;
	}
	return true;
}

function SQLinv($dbsrc) {
	require('../infoconfig.php');
	return $con;
}
?>
