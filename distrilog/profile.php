<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
defined('INCLUDE_CHECK') or define('INCLUDE_CHECK',1);
require_once('../admin/setup.php');
if(!ISIN_DISTRI||UPDATE_ON){ reloadTo('/');exit; }

$arr=array('dsdid','dslnam','dsfnam','dsmnam','dsbrth','dsmph','dsstrt','dsbarn','dscity','dsprov','dstin','dseadd','dssid');

$con=SQLi('distributor');
$rs=mysqli_query($con,"SELECT * FROM distributors WHERE dsdid='".DIST_ID."'") or die(mysqli_error($con));
while($rw=mysqli_fetch_array($rs)){
	for($i=1;$i<mysqli_num_fields($rs);$i++){
		$s=mysqli_fetch_field_direct($rs,$i)->name;
		$dat=($s=='dsbrth')?sprintf('%08d',$rw[$i]):$rw[$i];
		if(in_array($s,$arr)){
			$s=($s=='dsbrth'?'dsbday':($s=='dsmph'?'dscont':($s=='dsbarn'?'dsbrgy':($s=='dseadd'?'dsemail':$s))));
			$_SESSION['dsmstp_update'][$s]=$dat;
		}
	}
}mysqli_close($con);
if(!ISIN_WP) reloadTo('../reg?i='.DIST_ID);
?>
