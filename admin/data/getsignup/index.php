<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../fetch.php');
if(!ISIN_ADMIN||!testScope("global|data")){
	reloadTo(DLC_ADMIN);exit;
}else{
	date_default_timezone_set('Asia/Manila');
	$filter=isset($_GET['do'])?$_GET['do']:'';	//yyyymm REQUIRED
	$date=str_split($filter,2);
	$date=$date[2].'_'.$date[0].$date[1];
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: filename=Distributors_signupon_".$date.".csv");
	print listDistributors($filter);
}

function listDistributors($filter){
	$qry="SELECT * FROM distributors WHERE dscoid='DLCPH' AND dssetd LIKE '%$filter%' ORDER BY dssetd";
//	$qry="SELECT * FROM distributors WHERE dscoid='DLCPH' AND dseadd LIKE '% $filter%'";
	$str='"","ID#","Set Date","First Name","Middle Name","Last Name"'.',"Street","Brgy","City","Province"'."\n";
	$i=1;
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$str.='"'.$i++.'",';
		$str.='"'.$rw['dsdid'].'",';
		$str.='"'.$rw['dssetd'].'",';
//		$str.='"'.substr($rw['dseadd'],strpos($rw['dseadd'],$filter),8).'",';
		$str.='"'.$rw['dsfnam'].'",';
		$str.='"'.$rw['dsmnam'].'",';
		$str.='"'.$rw['dslnam'].'",';
		$str.='"'.$rw['dsstrt'].'",';
		$str.='"'.$rw['dsbarn'].'",';
		$str.='"'.$rw['dscity'].'",';
		$str.='"'.$rw['dsprov'].'"';
//		$str.='=="|'.$rw['dstime'].'|'.$rw['dseadd'].'|'.$rw['dssetd'].'|"==';
		$str.="\n";
	}$str.="\n\n";
	mysqli_close($con);
	return $str;
}
?>
