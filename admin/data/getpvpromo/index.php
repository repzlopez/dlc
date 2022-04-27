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
	$getmo=isset($_GET['mo'])?true:false;		//if whole month
	$getPV=isset($_GET['pv'])?$_GET['pv']:100;	//pv
	$getDate=isset($_GET['do'])?$_GET['do']:date("mY",time());	//mmyyyy REQUIRED
	$get1only=isset($_GET['single'])?$_GET['single']:false;		//if single invoice
	$file=($getmo)?'with'.$getPV.'PVupfor':($get1only?'qualifiedfor':'accumulated'.$getPV);
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=Distributors_'.$file.'_'.$getDate.'.csv');
	$csv=($getmo)?listPVMonth($getPV,$getDate):listPV($getPV,$getDate,$get1only);
	print $csv;
}

function listPV($pv,$date,$single){
	$sum1=$single?'':'SUM';
	$only1=$single?'':"GROUP BY omdid";
	$qry="
		SELECT dsdid,dsfnam,dslnam,ominv,$sum1(ompv) opv
		FROM ormstp
		LEFT JOIN distributors
			ON dsdid=omdid
		WHERE ompyr=".substr($date,2,4)."
		AND ompmo=".substr($date,0,2)."
		$only1
		HAVING opv>=$pv
		ORDER BY omdid,ominv
	";
	$str="Distributors with $pv PV and up [ ".($single?'Single Invoice':'Total PV')." ]\n";
	$str.='"ID#","FIRST NAME","LAST NAME","INVOICE","PPV"'."\n";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$str.='"'.$rw['dsdid'].'",';
		$str.='"'.$rw['dsfnam'].'",';
		$str.='"'.$rw['dslnam'].'",';
		$str.='"'.$rw['ominv'].'",';
		$str.='"'.number_format($rw['opv'],2).'"'."\n";
	}
	$str.="\n\n";
	return $str;
}

function listPVMonth($pv,$date){
	$idate=substr($date,2,4).substr($date,0,2);
	$qry="
		SELECT DISTINCT d.dsdid,dsfnam,dslnam,
			(SELECT SUM(ompv)
			 FROM ormstp
			 WHERE omdid=d.dsdid
			 AND omodat LIKE '%$idate%'
			 ) AS tpv
		FROM distributors AS d,ormstp
		WHERE omdid=dsdid
		AND omodat LIKE '%$idate%'
		AND omcoid='DLCPH'
		ORDER BY omdid,omodat
	";

	$str="Distributors with $pv PV and up for ".date('F Y',strtotime(substr($date,2,4).'-'.substr($date,0,2).'-18'))."\n";
	$str.='"ID#","FIRST NAME","LAST NAME","PPV"'."\n";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		if($rw['tpv']>=$pv){
			$str.='"'.$rw['dsdid'].'",';
			$str.='"'.$rw['dsfnam'].'",';
			$str.='"'.$rw['dslnam'].'",';
			$str.='"'.number_format($rw['tpv'],2).'"'."\n";
		}
	}
	$str.="\n\n";
	return $str;
}
?>
