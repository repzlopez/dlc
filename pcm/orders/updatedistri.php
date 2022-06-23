<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
$con=SQLi('distributor');
global $z;
$tbl='distributors';
$dsid=isset($_SESSION['pcm_dsdid'])?$_SESSION['pcm_dsdid']:null;

foreach($_POST as $key=>$val) {
	$dat=strtoupper(trim_escape($val));
	$$key=$dat;
}

if($submit=='_ _') {
	if(testExist($find,'distributor','distributors','dsdid')) {
		getDistList($find);
		if($z) {
			$qry="SELECT * FROM $tbl WHERE dsdid='$find'";
			$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
			$rw=mysqli_fetch_array($rs);
			$con=($rw['dsmph']!='')?$rw['dsmph']:(($rw['dshph']!='')?$rw['dshph']:$rw['dsoph']);
			echo getName($rw['dsdid'],'lfm').'|'.$con.'|'.($rw['dstin']==''?'TBS':$rw['dstin']);
		}else echo 'NOT FOUND';
	}else{ echo 'NOT FOUND';}
}elseif($submit=='@@@') {
	$dat='';
	$qry="SELECT * FROM $tbl WHERE dslnam LIKE '%$find%' OR dsfnam LIKE '%$find%'";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		getDistList($rw['dsdid']);
		if($z) {
			$dat.='<li><span class="s5 blue">'.$rw['dsdid'].'</span><span>'.getName($rw['dsdid'],'lfm').'</span></li>';
		}
	}echo $dat;
}else "NOT FOUND||";

unset($_POST);
unset($_SESSION['pcm_down']);

function getDistList($id) {
	global $z;
	$con=SQLi('distributor');
	$rs=mysqli_query($con,"SELECT dssid FROM distributors WHERE dsdid='$id'") or die(mysqli_error($con));
	$rw=mysqli_fetch_assoc($rs);
	$dsid=$rw['dssid'];
	$gsid=$_SESSION['pcm_dsdid'];
	if($dsid==$gsid||$id==$gsid) {
		$z=1;
	}else{
		if($dsid==EDDY) {$z=0;}
		else getDistList($dsid);
	}
}
?>
