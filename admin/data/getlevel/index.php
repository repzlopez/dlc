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
	$getDate=isset($_GET['do'])?$_GET['do']:'';	/* mmyyyy REQUIRED */
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=Distributors_Levels_in_'.$getDate.'.csv');
	$csv =listLevel(6,$getDate);
	$csv.="\n\n";
	$csv.=listLevel(5,$getDate);
	$csv.="\n\n";
	$csv.=listLevel(4,$getDate);
	$csv.="\n\n";
	$csv.=listLevel(3,$getDate);
	$csv.="\n\n";
	$csv.=listLevel(2,$getDate);
	$csv.="\n\n";
	$csv.=listLevel(1,$getDate);
	print $csv;
}

function listLevel($level,$date){
	$year=substr($date,2,4);
	$useWhere="bhdid=dsdid AND bhpmo='".intval(substr($date,0,2))."' AND bhpyr='".$year."'";
	$useCoID="bhcoid='DLCPH'";
	$usePV="bhelev=$level";
	$dat='';
	$qry="
		SELECT dsfnam,dslnam,dshph,dsmph,dscity,dsprov,bhdid,bhppv,bhnpv,bhelev
		FROM distributors,bohstp
		WHERE $useWhere
		AND $usePV
		AND $useCoID
	";

	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$ctr=mysqli_num_rows($rs);
	if($ctr>0){
		while($rw=mysqli_fetch_assoc($rs)) {
			$dat.='"'.number_format($rw['bhdid'],0).'",';
			$dat.='"'.$rw['dslnam'].'",';
			$dat.='"'.$rw['dsfnam'].'",';
			$dat.='"'.$rw['dshph'].'",';
			$dat.='"'.$rw['dsmph'].'",';
			$dat.='"'.number_format($rw['bhppv'],2).'",';
			$dat.='"'.number_format($rw['bhnpv'],2).'",';
			$dat.='"'.$rw['bhelev'].'",';
			$dat.='"'.$rw['dscity'].' '.$rw['dsprov'].'"'."\n";
		}
	}

//	$lev=array('0%'=>0,'6%'=>1,'9%'=>2,'12%'=>3,'15%'=>4,'18%'=>5,'21%'=>6);
	$str =getPercent($level,$year).' Distributors [ '.$ctr.' Distributor'.($ctr>1?'s':'').' ]'."\n";
	$str.='"ID#","LAST NAME","FIRST NAME","HOME","MOBILE","PPV","TPV","LEVEL","AREA"'."\n";
	$str.=$dat;
	return $str;
}
?>
