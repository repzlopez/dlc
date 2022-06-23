<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
testScope("global|gos",DLC_GORT);
if(isset($_POST['submit'])&&$_POST['submit']=='Submit') {
	$testArr=array('un','pw');
	$idata='';$udata='';$reqmiss=0;
	$_SESSION['post']=null;
	$_SESSION['post']['bad_distri']='';
	$con=SQLi('gos');
	foreach($_POST as $key=>$val) {
		if(in_array($key,$testArr)) {
			if(trim($val)=='') $reqmiss++;
		}
		$dat=trim_escape($val);
		$$key=$dat;
		$_SESSION['post'][$key]=$dat;
		if($key!='do'&&$key!='submit') {
			if($key=='pw') $dat=md5($un.$pw.'@)!)');
			$dat=($key=='status'?"$dat":"'$dat'");
			$idata.="$dat,";
			$udata.=($key=='un'||($key=='pw'&&$val==''))?'':$key."=$dat,";
		}
	}
	unset($_POST);
	if(!testExist($un,'products','tblwarehouse','id')) { $_SESSION['post']['bad_distri']='Unable to continue. Warehouse ID not found. Setup Warehouse ID first.';}
	elseif(testExist($un,'gos','tbladmin','un')&&$do<2) { $_SESSION['post']['bad_distri']='Unable to continue. User exists.';}
	elseif($do==1&&$reqmiss>0) { $_SESSION['post']['bad_distri']='Unable to continue. Fields in RED are required.';}
	else{
		$idata=substr_replace($idata,'',-1);
		$udata=substr_replace($udata,'',-1);
		$qry="INSERT INTO tbladmin VALUES ($idata) ON DUPLICATE KEY UPDATE $udata";
		unset($_SESSION['post']);
		mysqli_query($con,$qry) or die(mysqli_error($con));
		reloadTo(DLC_GORT.'/admin');
	}reloadTo($_SERVER['HTTP_REFERER']);
}
?>
