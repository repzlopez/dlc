<?php session_set_cookie_params(0);
if(!isset($_SESSION)) session_start();
define('INCLUDE_CHECK',1);
require('../../fetch.php');
if(!ISIN_ADMIN||!testScope("global|data")) {
	reloadTo(DLC_ADMIN);exit;
}else{
	date_default_timezone_set('Asia/Manila');
	$do=isset($_GET['do'])?$_GET['do']:'';		//aabbyyyy REQUIRED
	$aa=substr($do,0,2);
	$bb=substr($do,2,2);
	$yr=substr($do,-4);
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=All_DP_PV_POV_for_Weeks_'.$aa.'-'.$bb.'.csv');
	$csv=listPV($aa,$bb,$yr);
	print $csv;
}

function listPV($aa,$bb,$yr) {
	$rnd='';$hdr='';$t=1;
	for($n=(int)$aa;$n<=(int)$bb;$n++) {
		$rnd.=",ROUND((SELECT SUM(ompov) FROM ormstp WHERE omdid=ID AND ompmo=".$n." AND ompyr=$yr),2) dp".sprintf("%02d",$n);
		$rnd.=",ROUND((SELECT SUM(bhppv) FROM bohstp WHERE bhdid=ID AND bhpmo='".sprintf("%02d",$n)."' AND bhpyr='$yr'),2) pv".sprintf("%02d",$n);
		$hdr.='"Week '.sprintf("%02d",$n).' DP","Week '.sprintf("%02d",$n).' PPV","Week '.sprintf("%02d",$n).' POV",';
	}
	$qry="
		SELECT DISTINCT bhdid ID,dsdid,dslnam,dsfnam,dsmnam $rnd
		FROM bohstp
		LEFT JOIN distributors
			ON dsdid=bhdid
		JOIN ormstp
			ON omdid=bhdid
		WHERE bhpmo BETWEEN '$aa' AND '$bb'
		AND bhpyr='$yr'
		ORDER BY ID
	";
	$str="All DP PV POV [ $aa-$bb $yr]\n";
	$str.='"ID#","NAME",'.$hdr."\n";
	ini_set('max_execution_time',300);
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		$s='';
		$id=$rw['ID'];
		$nm=$rw['dslnam'].', '.$rw['dsfnam'].' '.substr($rw['dsmnam'],0,1).'.';
		for($n=(int)$aa;$n<=(int)$bb;$n++) {
			$ddp=$rw['dp'.sprintf("%02d",$n)];
			$dat=$rw['pv'.sprintf("%02d",$n)];
			$s.='"","'.($dat).'","'.($dat*25).'",';
		}

		$str.='"'.$id.'","'.$nm.'",';
		$str.=$s."\n";
	}
	$str.="\n\n";
	return $str;
}
?>
