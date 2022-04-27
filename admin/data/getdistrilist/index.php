<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../fetch.php');
if(!ISIN_ADMIN||!testScope("global|data|accounting")){
	reloadTo(DLC_ADMIN);exit;
}else{
	date_default_timezone_set('Asia/Manila');
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=Distributors_asof_'.date('mdY',time()).'.csv');

	global $getbdy,$getcoid,$gettin,$getcon,$getmail,$getadd,$getset,$getups,$lookup,$rem;
	$do	=isset($_GET['do'])?$_GET['do']:null;
	$getbdy	=isset($_GET['bday'])?$_GET['bday']:0;
	$getcoid=isset($_GET['coid'])?$_GET['coid']:0;
	$gettin	=isset($_GET['tin'])?$_GET['tin']:0;
	$getcon	=isset($_GET['cont'])?$_GET['cont']:0;
	$getmail=isset($_GET['mail'])?$_GET['mail']:0;
	$getadd	=isset($_GET['addr'])?$_GET['addr']:0;
	$getset	=isset($_GET['setd'])?$_GET['setd']:0;
	$getups	=isset($_GET['ups'])?$_GET['ups']:0;
	$rem	=isset($_GET['rem'])?$_GET['rem']:0;

	$str='"","ID#","LNAME","FNAME","MNAME",';
	if($getcoid) $str.='"COID",';
	if($getups) $str.='"SPONSOR",';
	if($getbdy) $str.='"BDAY",';
	if($gettin) $str.='"TIN",';
	if($getcon) $str.='"OFFICE","HOME","MOBILE",';
	if($getmail) $str.='"EMAIL",';
	if($getadd) $str.='"STREET","BRGY","CITY","PROVINCE",';
	if($getset) $str.='"SETUP",';
	$str.='""'."\n";

	if(isset($do)){
		$_SESSION['downlines']='';
		$con=SQLi('distributor');
		$rs=mysqli_query($con,"SELECT * FROM distributors WHERE dsdid='$do' ORDER BY dssetd") or die(mysqli_error($con));
		$rw=mysqli_fetch_assoc($rs);
		$_SESSION['downlines']='"",'.getLine($rw);
		getDistList($do,0,MAXLOOP,1);
		echo $str.$_SESSION['downlines'];
	}else print $str.listDistributors($do);
}

function listDistributors($do){
	global $rem;
	$i=1;$x='';
	$qry="SELECT * FROM distributors ORDER BY dsdid";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		if($rem&&strpos($rw['dsdid'],'-')!==false){}
		else $x.='"'.$i++.'",'.getLine($rw);
	}return $x."\n\n";
}

function getDistList($sponsor,$lvl,$swit){
	global $rem;
	if($lvl>=MAXLOOP){}
	else{
		$except=isset($_GET['ex'])?explode('|',$_GET['ex']):array();
		$qry="SELECT * FROM distributors WHERE dssid='$sponsor' ORDER BY dssetd";
		ini_set('max_execution_time',300);
		$con=SQLi('distributor');
		$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
		if(mysqli_num_rows($rs)==0){
			if($lvl==0) $_SESSION['downlines'].='NO DATA';
		}else{
			// if($lvl==0) $_SESSION['downlines'].="['ID#/Name', 'Upline', 'Tooltip'],"."<br/>"."['$sponsor', null, 'The Upline'],"."<br/>";
			while($rw=mysqli_fetch_assoc($rs)){
				$swit=($swit&&in_array($rw['dsdid'],$except))?0:1;
				if($swit){
					if($rem&&strpos($rw['dsdid'],'-')!==false){}
					else $_SESSION['downlines'].=$swit?'"",'.getLine($rw):'';
					// $id=$rw['dsdid'];$up=$rw['dssid'];
					// $name=ucwords(strtolower($rw['dsfnam'])).' '.ucwords(strtolower($rw['dslnam']));
					// $_SESSION['downlines'].="[{v:'$id',f:'$id $name'},'$up',null],"."<br>";
					getDistList($rw['dsdid'],$lvl+1,$swit);
				}
			}
		}
	}
}

function getLine($rw){
	global $getbdy,$getcoid,$gettin,$getcon,$getmail,$getadd,$getset,$getups;
	$bday=sprintf('%08d',$rw['dsbrth']);
	$str ='"'.$rw['dsdid'].'",';
	$str.='"'.$rw['dslnam'].'",';
	$str.='"'.$rw['dsfnam'].'",';
	$str.='"'.$rw['dsmnam'].'",';

	if($getups) $str.='"'.$rw['dssid'].'",';
	if($getbdy) $str.='"'.($bday=='00000000'?'':formatDate($bday,'mdY')).'",';
	if($getcoid) $str.='"'.$rw['dscoid'].'",';
	if($gettin) $str.='"'.$rw['dstin'].'",';
	if($getcon){
		$str.='"'.$rw['dsoph'].'",';
		$str.='"'.$rw['dshph'].'",';
		$str.='"'.$rw['dsmph'].'",';
	}
	if($getmail) $str.='"'.$rw['dseadd'].'",';
	if($getadd){
		$str.='"'.$rw['dsstrt'].'",';
		$str.='"'.$rw['dsbarn'].'",';
		$str.='"'.$rw['dscity'].'",';
		$str.='"'.$rw['dsprov'].'",';
	}
	if($getset) $str.='"'.formatDate($rw['dssetd'],'Ymd').'",';
	return $str.'""'."\n";
}
?>
