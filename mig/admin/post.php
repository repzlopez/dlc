<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
testScope("global|mig",DLC_MGRT);

if ( isset($_POST['submit'])&&$_POST['submit']=='Submit' ) {
     $idata=$udata='';$reqmiss=0;
	$testArr = array('un','pw');
	$_SESSION['post'] = null;
	$_SESSION['post']['bad_distri'] = '';
	$con = SQLi('beta');

	foreach( $_POST as $k=>$v ) {
		if ( in_array($k,$testArr) ) {
			if ( trim($v)=='' ) $reqmiss++;
		}
		$dat = trim_escape($v);
		$$k = $dat;
		$_SESSION['post'][$k] = $dat;
		if ( $k!='do'&&$k!='submit' ) {
			if ( $k=='pw' ) $dat = md5($un.$pw.'@)!)');
			$dat = ( $k=='status' ? "$dat" : "'$dat'" );
			$idata .= "$dat,";
			$udata .= ($k=='un'||($k=='pw'&&$v==''))?'':$k."=$dat,";
		}
	}

	unset($_POST);
	if ( !testExist($un,'products','tblwarehouse','id') ) { $_SESSION['post']['bad_distri']='Unable to continue. Warehouse ID not found. Setup Warehouse ID first.'; }
	elseif ( testExist($un,'beta','tblmig','un')&&$do<2 ) { $_SESSION['post']['bad_distri']='Unable to continue. User exists.'; }
	elseif ( $do==1&&$reqmiss>0 ) { $_SESSION['post']['bad_distri']='Unable to continue. Fields in RED are required.'; }
	else {
		$idata = substr_replace($idata,'',-1);
		$udata = substr_replace($udata,'',-1);
		$qry   = "INSERT INTO tblmig VALUES ($idata) ON DUPLICATE KEY UPDATE $udata";

		unset($_SESSION['post']);
		mysqli_query($con,$qry) or die(mysqli_error($con));
		reloadTo(DLC_MGRT.'/admin');
	} reloadTo($_SERVER['HTTP_REFERER']);
}
?>
