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
	$_SESSION['distlist']='';
	$_SESSION['datalist']='';
	$getPV=isset($_GET['pv'])?$_GET['pv']:5;		//pv
	$getTPV=isset($_GET['tpv'])?$_GET['tpv']:null;	//tpv
	$getDate=isset($_GET['do'])?$_GET['do']:'';		//mmyyyy REQUIRED
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=Distributors_w_'.(($getTPV!==null)?$getTPV:$getPV).'PV_'.$getDate.'.csv');
	getDistList(GRACE,0,MAXLOOP,$getDate,$getTPV);
	$csv ="Distributors with ".(($getTPV!==null)?$getTPV:$getPV)." PV [ $getDate ]\n";
	$csv.='"ID#","LAST NAME","FIRST NAME","PROV","PPV","GPV","TPV"'."\n";
	$csv.=$_SESSION['distlist'];					//start from eddy
	print $csv;
	unset($_SESSION['distlist']);
	unset($_SESSION['datalist']);
}

function getDistList($sponsor,$lvl,$date,$tpv){
	if($lvl>=MAXLOOP){}
	else{
		$con=SQLi('distributor');
		$qry="SELECT dsdid,dslnam,dsfnam,dsprov FROM distributors WHERE dssid='$sponsor'";
		$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
		if(mysqli_num_rows($rs)==0){
		}else{
			while($rw=mysqli_fetch_assoc($rs)){
				if(hasPV($rw['dsdid'],$date,$tpv)){
					$_SESSION['distlist'].='"'.$rw['dsdid'].'","'.$rw['dslnam'].'","'.$rw['dsfnam'].'","'.$rw['dsprov'].'",'.$_SESSION['datalist'];
				}getDistList($rw['dsdid'],$lvl+1,$date,$tpv);
			}
		}
	}
}

function hasPV($id,$date,$tpv){
	$qry="
		SELECT bhppv ppv,(bhnpv-bhppv) gpv,bhnpv tpv
		FROM bohstp
		WHERE bhdid='$id'
		AND bhpmo='".intval(substr($date,0,2))."'
		AND bhpyr='".substr($date,2,4)."'
		AND bhnpv>=$tpv
		AND bhppv>=5
	";$str='';
	$con=SQLi('distributor');
	$rs1=mysqli_query($qry) or die(mysqli_error($con));
	$rw1=mysqli_fetch_array($rs1);
	$str.='"'.number_format($rw1['ppv'],2).'",';
	$str.='"'.number_format($rw1['gpv'],2).'",';
	$str.='"'.number_format($rw1['tpv'],2).'"'."\n";
	$_SESSION['datalist']=$str;
	return (mysqli_num_rows($rs1)>0)?true:false;
}
?>
