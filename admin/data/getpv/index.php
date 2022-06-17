<?php session_set_cookie_params(0);
if(!isset($_SESSION)) session_start();
define('INCLUDE_CHECK',1);
require('../../fetch.php');
if(!ISIN_ADMIN||!testScope("global|data")){
	reloadTo(DLC_ADMIN);exit;
}else{
	date_default_timezone_set('Asia/Manila');
	$getPV=isset($_GET['pv'])?$_GET['pv']:5;		//pv
	$getTPV=isset($_GET['tpv'])?$_GET['tpv']:null;	//tpv
	$getDate=isset($_GET['do'])?$_GET['do']:'';		//mmyyyy REQUIRED
	$getArea=isset($_GET['area'])?$_GET['area']:0;	//area
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=Distributors_w_'.(($getTPV!==null)?$getTPV:$getPV).'PV_'.$getDate.'.csv');
	$dbsrc='distributor';
	include('../../infoconfig.php');
	$csv=listPV($getPV,$getDate,$getDate,$getTPV,$getArea);
	print $csv;
}

function listPV($pv,$date,$monyr,$tpv=null,$area=false){
	if($date!=''){
		$bmh='bh';
		$useTable='bohstp';
		$useWhere="bhdid=dsdid AND bhpmo='".intval(substr($date,0,2))."' AND bhpyr='".substr($date,2,4)."'";
	}else{
		$bmh='bm';
		$useTable='bomstp';
		$useWhere='bmdid=dsdid';
	}
	$useSelect=$bmh.'ppv ppv,'.$bmh.'npv tpv,'.$bmh.'elev elev';
	$useCoID=$bmh."coid='DLCPH'";
	$usePV='AND '.$bmh.'ppv>='.$pv.(($tpv!==null)?' AND tpv>='.$tpv:'');
	$qry="
		SELECT dsdid,dsfnam,dslnam,dscity,dsprov,$useSelect
		FROM distributors,$useTable
		WHERE $useWhere
		AND $useCoID
		$usePV
	";
	$pvhdr=(($tpv!==null)?$tpv:$pv);
	$str="Distributors with $pvhdr PV [ $monyr ]\n";
	$str.='"ID#","LAST NAME","FIRST NAME","PPV","GPV","TPV","LEVEL"'.($area?',"AREA"':'')."\n";
	$rs=mysql_query($qry) or die(mysql_error());
	while($rw=mysql_fetch_assoc($rs)){
		$getGPV=$rw['tpv']-$rw['ppv'];
//		if($getGPV>=$pvhdr){
			$str.='"'.$rw['dsdid'].'",';
			$str.='"'.$rw['dslnam'].'",';
			$str.='"'.$rw['dsfnam'].'",';
			$str.='"'.number_format($rw['ppv'],2).'",';
			$str.='"'.number_format($getGPV,2).'",';
			$str.='"'.number_format($rw['tpv'],2).'",';
			$str.='"'.$rw['elev'].'"';
			$str.=$area?',"'.$rw['dscity'].' '.$rw['dsprov'].'"':'';
			$str.="\n";
//		}
	}
	$str.="\n\n";
	return $str;
}
?>