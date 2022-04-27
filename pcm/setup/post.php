<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
if(!ISIN_PCM){ reloadTo(DLC_PCRT);exit; }

$con=SQLi('pcm');
$idata='';$udata='';
foreach($_POST as $key=>$val){
	$dat=trim_escape($val);
	$$key=$dat;
	if($key!='submit'){
		$idata.="'$dat',";
		$udata.=($key!='id'&&$key!='wh')?$key."='$dat',":'';
	}
}unset($_POST);

if($submit=='_ _'){
	$idata=substr_replace($idata,'',-1);
	$udata=substr_replace($udata,'',-1);
	$qry="INSERT INTO tblsetup VALUES ($idata) ON DUPLICATE KEY UPDATE $udata";
	mysqli_query($con,$qry) or die(mysqli_error($con));
	echo 1;
}elseif($submit=='@ @'){
	$qry=" WHERE un='".LOGIN_BRANCH."'";
	$rs=mysqli_query($con,"SELECT pw FROM tbladmin $qry") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	if(md5(LOGIN_BRANCH.$acpascur.'@)!)')==$rw['pw']){
		if($acpasold==$acpasnew) $dat=md5(LOGIN_BRANCH.$acpasnew.'@)!)');
		mysqli_query($con,"UPDATE tbladmin SET pw='$dat' $qry");
		echo '0Password changed';
	}else echo '1 Current Password mismatch.';
	mysqli_close($con);
}
?>
