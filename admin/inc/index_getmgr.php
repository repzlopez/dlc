<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
if(!IS_GLOB) {reloadTo(DLC_ADMIN);exit;}
global $pip,$exemgr,$stat,$r;
$msg='<form method="post" id="recap" enctype="multipart/form-data" action="index.php?p='.$content.'"><ul>';
$msg.='<li><span class="blue">'.$arrLinks[$content].'</span>';
$msg.='<li><label>File:</label><input type="file" name="file" /><span class="bad"></span></li>';
$msg.='<li><label>Filter:</label><input type="hidden" name="stat" value=0 /><input type="checkbox" name="stat" value=1 /></li>';
$msg.='<input type="submit" name="submit" class="btn" value="UPLOAD" /></ul></form>';
$msg.=$homebtn;

if(isset($_POST)) {
	global $stat;
	$con=SQLi('distributor');
	foreach($_POST as $key=>$val) {
		$$key=trim_escape($val);
	}unset($_POST);
}

if(isset($submit)&&$submit=='UPLOAD') {
	$file=$_FILES['file'];
	unset($_POST);
	unset($_FILES);
	$id=$file['tmp_name'];
	if(isValid($file['type'])) {
		ob_end_flush();
		ob_end_clean();
		ob_start();
		date_default_timezone_set('Asia/Manila');
		header('Content-type: application/vnd.ms-excel');
		header('Content-disposition: filename=distri_data.csv');
		$str='"ID#","LAST NAME","FIRST NAME","MIDDLE NAME","OFFICE","HOME","MOBILE","CITY","PROV","TIN","SETUP","MGR"'."\n";
		$str.=getData($id);
		print $str;
		exit;
	}
}
ob_start();
echo $msg;
ob_end_flush();

function isValid($ft) {
	if($ft=='text/csv'||$ft=='application/csv'||$ft=='application/vnd.ms-excel') return true;
}

function getData($file) {
	global $pip;
	$pip=loadPIP();
	$dat=fopen($file,'r');$s='';
	while($r=fgets($dat)) {
		$s.=getInfo(trim(str_replace('-','',$r)));
	}return $s;
}

function getInfo($id) {
	$con=SQLi('distributor');
	ini_set('max_execution_time',600);
	$qry="SELECT * FROM distributors WHERE dsdid='$id'";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$m=getMgr($id,0,MAXLOOP);
	$dd=$rw['dssetd'];

	$str ='"'.$id.'",';
	$str.='"'.$rw['dslnam'].'",';
	$str.='"'.$rw['dsfnam'].'",';
	$str.='"'.$rw['dsmnam'].'",';
	$str.='"'.$rw['dsoph'].'",';
	$str.='"'.$rw['dshph'].'",';
	$str.='"'.$rw['dsmph'].'",';
	$str.='"'.$rw['dscity'].'",';
	$str.='"'.$rw['dsprov'].'",';
	$str.='"'.$rw['dstin'].'",';
	$str.='"'.substr($dd,4,2).'/'.substr($dd,-2).'/'.substr($dd,0,4).'",';
	$str.=$m;
	return $str.="\n";
	mysqli_close($con);
}

function getMgr($id,$lvl) {
	global $pip,$exemgr,$stat,$r;$r='';
	$mgr=($stat)?$exemgr:$pip;
	if($lvl>=MAXLOOP) {}
	else{
		if(in_array($id,$mgr)) {
			$r.='"'.($stat?array_search($id,$mgr):$id).'",';
			$r.='"'.getName($id,'fml',1).'",';
		}else{
			$con=SQLi('distributor');
			$qry="SELECT dssid FROM distributors WHERE dsdid='$id'";
			$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
			if(mysqli_num_rows($rs)==0) {
			}else{
				while($rw=mysqli_fetch_assoc($rs)) {
					$d=$rw['dssid'];
					if(in_array($d,$mgr)) {
						$r.='"'.($stat?array_search($d,$mgr):$d).'",';
						$r.='"'.getName($d,'fml',1).'",';
					}else getMgr($d,$lvl+1);
				}
			}
		}
	}return $r;
}

function loadPIP() {
	$con=SQLi('beta');
	$qry="SELECT * FROM tblpip";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		$arr[]=$rw['dsdid'];
	}return $arr;
}
?>
