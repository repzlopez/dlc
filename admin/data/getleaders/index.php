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
	$do=isset($_GET['do'])?$_GET['do']:null;
	$yr=isset($_GET['yr'])?$_GET['yr']:null;
	include_once('../../setup.php');
	$lev=getPercent(substr($do,-1));
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=Distributors_'.$lev.'_asof_'.date('mdY',time()).'.csv');
	$dbsrc='distributor';
	$all18=listLevel($do,$yr);
	$hdr= '"ID#","LAST NAME","FIRST NAME","MIDDLE NAME","LEVEL","1st TIME"'."\n";
	$csv =$lev." Distributors\n";
	$csv.=$hdr;
	$csv.=$all18;
	print $csv;
}

function listLevel($n,$yr){$str='';$add='';
	if(substr($n,-1)>4){
		$add="AND (
			(bhnpv>=".$_SESSION['minpv'][$n]." AND bhqumr<=1) OR
			(bhnpv>=500 AND bhqumr=2) OR
			(bhqumr>2))";
	}else $add=(isset($yr)?"AND bhpyr$yr":'');

	$qry="
		SELECT DISTINCT bhdid,bhelev,bhcoid,bhpmo,bhpyr,dslnam,dsfnam,dsmnam,dsoph,dshph,dsmph
		FROM bohstp,distributors
		WHERE bhcoid='DLCPH'
		AND dsdid=bhdid
		AND bhelev$n
		".$add."
		ORDER BY bhdid,bhpyr,bhpmo";
	$oldid='';
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	ini_set('max_execution_time',300);
	while($rw=mysqli_fetch_assoc($rs)){
		$newid=$rw['bhdid'];
		if($oldid!=$newid){
			$oldid=$rw['bhdid'];
			$monyr=$rw['bhpyr'].'|'.sprintf('%02d',$rw['bhpmo']);
			$str.='"'.$rw['bhdid'].'",';
			$str.='"'.$rw['dslnam'].'",';
			$str.='"'.$rw['dsfnam'].'",';
			$str.='"'.$rw['dsmnam'].'",';
			$str.='"'.getPercent($rw['bhelev'],$rw['bhpyr']).'",';
			$str.='"'.$monyr.'"'."\n";
		}
	}return $str;
}
?>
